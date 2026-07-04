#!/bin/bash
set -e

# ==============================================
# Diego's Pizza — Backup Completo
# Respaldos: DB, volumes docker, .env
# Almacenamiento: local + opcional Google Drive (rclone)
# ==============================================

BACKUP_DIR="$HOME/backups/diegospizza"
PROJECT_DIR="/home/oliver/diegospizza"
DATE=$(date +%Y%m%d_%H%M%S)

# Cargar variables del .env (DB_PASSWORD, etc)
if [ -f "$PROJECT_DIR/.env" ]; then
    set -a
    source "$PROJECT_DIR/.env"
    set +a
fi
BACKUP_FILE="${BACKUP_DIR}/diegospizza_${DATE}.tar.gz"
RETENTION_DAYS=14
LOG_FILE="${BACKUP_DIR}/backup.log"

# Rclone config (opcional) — activar con RCLONE_REMOTE="" en el comando
RCLONE_REMOTE="${RCLONE_REMOTE:-}"
RCLONE_DEST="diegospizza-backups"

mkdir -p "$BACKUP_DIR"

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

cleanup() {
    rm -rf "$TEMP_DIR"
}
TEMP_DIR=$(mktemp -d)
trap cleanup EXIT

log "=== Iniciando backup: $DATE ==="

# 1. Dump de base de datos
log "Respaldando base de datos..."
cd "$PROJECT_DIR"
docker compose exec -T db mysqldump \
    --single-transaction --routines --triggers \
    -u diegospizza -p"${DB_PASSWORD}" diegospizza \
    | gzip > "$TEMP_DIR/database.sql.gz"
log "  DB dump: $(du -h "$TEMP_DIR/database.sql.gz" | cut -f1)"

# 2. Volumenes docker (storage, waha sessions)
log "Respaldando volumenes docker..."
for vol in diegospizza_dbdata diegospizza_storage_data diegospizza_waha_sessions; do
    mount=$(docker volume inspect "$vol" 2>/dev/null | grep '"Mountpoint"' | awk -F'"' '{print $4}')
    if [ -n "$mount" ] && [ -d "$mount" ]; then
        name="${vol#diegospizza_}"
        sudo tar czf "$TEMP_DIR/vol_${name}.tar.gz" -C "$(dirname "$mount")" "$(basename "$mount")" 2>/dev/null
        log "  Volume $name: $(du -h "$TEMP_DIR/vol_${name}.tar.gz" | cut -f1)"
    else
        log "  Volume $vol: no encontrado, saltando"
    fi
done

# 3. Version del codigo
log "Guardando version del codigo..."
cd "$PROJECT_DIR"
git describe --tags --exact-match 2>/dev/null > "$TEMP_DIR/.version" || \
    git rev-parse HEAD > "$TEMP_DIR/.version"
log "  Version: $(cat "$TEMP_DIR/.version")"

# 4. .env
log "Respaldando .env..."
if [ -f "$PROJECT_DIR/.env" ]; then
    cp "$PROJECT_DIR/.env" "$TEMP_DIR/.env"
    log "  .env copiado"
else
    log "  .env NO ENCONTRADO — importante incluirlo manualmente"
fi

# 5. Empaquetar todo
log "Creando archivo comprimido..."
tar czf "$BACKUP_FILE" -C "$TEMP_DIR" . 2>/dev/null
log "Backup creado: $BACKUP_FILE ($(du -h "$BACKUP_FILE" | cut -f1))"

# 6. Limpiar backups locales antiguos
find "$BACKUP_DIR" -name 'diegospizza_*.tar.gz' -mtime +$RETENTION_DAYS -delete 2>/dev/null
log "Backups locales antiguos (>${RETENTION_DAYS}d) eliminados"

# 7. Subir a Google Drive (si configurado)
if [ -n "$RCLONE_REMOTE" ]; then
    log "Subiendo a Google Drive..."
    rclone copy "$BACKUP_FILE" "${RCLONE_REMOTE}:${RCLONE_DEST}/" 2>>"$LOG_FILE"
    if [ $? -eq 0 ]; then
        log "  Subida exitosa a Google Drive: ${RCLONE_DEST}/"
    else
        log "  ERROR subiendo a Google Drive"
    fi
else
    log "Rclone no configurado — backup solo local"
    log "Para activar: instalar rclone, configurar remote y ejecutar:"
    log "  RCLONE_REMOTE=nombre_remote $0"
fi

log "=== Backup completado: $(date '+%Y-%m-%d %H:%M:%S') ==="
echo ""
echo "Archivos disponibles en $BACKUP_DIR:"
ls -lh "$BACKUP_DIR"/*.tar.gz 2>/dev/null | tail -5
