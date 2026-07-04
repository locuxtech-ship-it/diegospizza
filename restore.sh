#!/bin/bash
set -e

# ==============================================
# Diego's Pizza — Restauración completa
# Recupera desde un backup en un servidor nuevo
# ==============================================

if [ $# -ne 1 ]; then
    echo "Uso: $0 <archivo_backup.tar.gz>"
    echo "Ej:  $0 /var/backups/diegospizza/diegospizza_20260703_120000.tar.gz"
    exit 1
fi

BACKUP_FILE="$1"
PROJECT_DIR="/home/oliver/diegospizza"
TEMP_DIR=$(mktemp -d)

cleanup() {
    rm -rf "$TEMP_DIR"
}
trap cleanup EXIT

echo "=========================================="
echo "Restauración Diego's Pizza"
echo "=========================================="

# 1. Verificar prerequisitos
echo ""
echo "[1/7] Verificando prerequisitos..."
for cmd in docker git; do
    if ! which "$cmd" &>/dev/null; then
        echo "ERROR: $cmd no está instalado"
        exit 1
    fi
done
echo "  OK"

# 2. Extraer backup
echo ""
echo "[2/7] Extrayendo backup..."
tar xzf "$BACKUP_FILE" -C "$TEMP_DIR"
echo "  OK"

# 3. Clonar repositorio
echo ""
echo "[3/7] Clonando repositorio..."
if [ -d "$PROJECT_DIR" ]; then
    echo "  Directorio $PROJECT_DIR ya existe, haciendo pull..."
    cd "$PROJECT_DIR" && git pull
else
    mkdir -p "$PROJECT_DIR"
    git clone https://github.com/locuxtech-ship-it/diegospizza.git "$PROJECT_DIR"
    cd "$PROJECT_DIR"
fi

# Checkout al tag o commit del backup
if [ -f "$TEMP_DIR/.version" ]; then
    TAG=$(cat "$TEMP_DIR/.version")
    echo "  Haciendo checkout a: $TAG"
    git checkout "$TAG" 2>/dev/null || echo "  Tag no encontrado, usando master"
fi
echo "  OK"

# 4. Restaurar .env
echo ""
echo "[4/7] Restaurando .env..."
if [ -f "$TEMP_DIR/.env" ]; then
    cp "$TEMP_DIR/.env" "$PROJECT_DIR/.env"
    echo "  OK"
else
    echo "  ATENCION: No hay .env en el backup"
    echo "  Debes crearlo manualmente desde .env.example"
    echo "  Las credenciales DB y API keys deben ser las mismas"
fi

# 5. Restaurar volumenes docker
echo ""
echo "[5/7] Restaurando volumenes docker..."
for vol_file in "$TEMP_DIR"/vol_*.tar.gz; do
    [ -f "$vol_file" ] || continue
    name=$(basename "$vol_file" .tar.gz | sed 's/vol_//')
    echo "  Restaurando volumen: $name"
    docker volume inspect "diegospizza_${name}" &>/dev/null || docker volume create "diegospizza_${name}"
    # Usar un contenedor temporal para copiar datos al volume (no requiere sudo)
    docker run --rm -v "diegospizza_${name}:/vol" -v "$vol_file:/backup.tar.gz" alpine sh -c "tar xzf /backup.tar.gz -C /vol" 2>/dev/null || {
        echo "  Error restaurando volume $name"
    }
done
echo "  OK"

# 6. Restaurar base de datos
echo ""
echo "[6/7] Restaurando base de datos..."
if [ -f "$TEMP_DIR/database.sql.gz" ]; then
    # Iniciar solo db si no está corriendo
    cd "$PROJECT_DIR"
    docker compose up -d db
    echo "  Esperando que DB esté lista..."
    sleep 10
    docker compose exec -T db sh -c "
        mysql -u root -p\"\${MYSQL_ROOT_PASSWORD}\" -e '
            CREATE DATABASE IF NOT EXISTS diegospizza;
            GRANT ALL PRIVILEGES ON diegospizza.* TO \"diegospizza\"@\"%\";
            FLUSH PRIVILEGES;
        ' 2>/dev/null
    " || echo "  DB ya existe, continuando..."
    
    gunzip -c "$TEMP_DIR/database.sql.gz" | docker compose exec -T db mysql -u diegospizza -p"${DB_PASSWORD}" diegospizza
    echo "  OK"
else
    echo "  ERROR: No hay database.sql.gz en el backup"
    exit 1
fi

# 7. Iniciar todos los servicios
echo ""
echo "[7/7] Iniciando servicios..."
cd "$PROJECT_DIR"
docker compose up -d --build app
docker compose restart nginx
echo "  OK"

echo ""
echo "=========================================="
echo "Restauración completada!"
echo ""
echo "Tu app debería estar accesible en:"
[ -f "$PROJECT_DIR/.env" ] && grep APP_URL "$PROJECT_DIR/.env"
echo ""
echo "Para verificar el estado:"
echo "  cd $PROJECT_DIR && docker compose ps"
echo "=========================================="
