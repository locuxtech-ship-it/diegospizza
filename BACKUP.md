# Backup y Recuperacion — Diego's Pizza

## Que se respalda

| Componente | Backup | Donde esta |
|---|---|---|
| Codigo fuente | GitHub (git tag v1.0) | github.com/locuxtech-ship-it/diegospizza |
| Base de datos (MariaDB) | backup.sh local + Google Drive | ~/backups/diegospizza/ |
| Volumenes Docker (storage, db, waha) | backup.sh local + Google Drive | ~/backups/diegospizza/ |
| .env (credenciales) | backup.sh local + Google Drive | ~/backups/diegospizza/ |
| Version exacta del codigo | backup.sh (guardada en el backup) | dentro del .tar.gz |

## Frecuencia

- **Diario a las 3:00 AM** via cron del servidor
- Se puede ejecutar manualmente en cualquier momento

## Archivos incluidos

### backup.sh
Script que se ejecuta en el servidor. Hace:
1. Dump de la base de datos MariaDB
2. Backup de los 3 volumenes Docker (dbdata, storage_data, waha_sessions)
3. Copia el .env con credenciales
4. Guarda el commit/tag actual del codigo
5. Empaqueta todo en un .tar.gz
6. Mantiene 14 dias de backups locales
7. Sube a Google Drive (si configurado)

Uso manual:
```bash
cd /home/oliver/diegospizza
bash backup.sh                                    # solo local
RCLONE_REMOTE=gdrive bash backup.sh               # local + Google Drive
```

### restore.sh
Script para restaurar TODO en un servidor nuevo desde cero.

Uso:
```bash
bash restore.sh /ruta/al/backup.tar.gz
```

Pasos automaticos:
1. Verifica prerequisitos (Docker, git)
2. Extrae el backup
3. Clona el repositorio y hace checkout a la version exacta
4. Restaura el .env
5. Restaura los volumenes Docker
6. Restaura la base de datos
7. Inicia todos los servicios (docker compose up -d)

## Google Drive

### Configuracion inicial (una vez)
1. En el servidor, ejecutar `rclone authorize "drive"` en una maquina CON navegador
2. Copiar el token resultante
3. En el servidor: editar `~/.config/rclone/rclone.conf`
4. Agregar:
   ```
   [gdrive]
   type = drive
   token = {"access_token":"...","token_type":"Bearer","refresh_token":"...","expiry":"..."}
   ```
5. Probar: `rclone ls gdrive:diegospizza-backups`

### Verificar que funciona
```bash
# Ver archivos en Google Drive
rclone ls gdrive:diegospizza-backups

# Backup de prueba
RCLONE_REMOTE=gdrive bash /home/oliver/diegospizza/backup.sh
```

## Recuperacion total por pasos (siempre funciona)

### En el mismo servidor (fallo parcial)
```bash
cd /home/oliver/diegospizza
bash restore.sh ~/backups/diegospizza/diegospizza_20260704_171855.tar.gz
```

### En un servidor nuevo (desastre total)

1. Instalar Ubuntu 24.04 + Docker
2. Tener el archivo .tar.gz del backup (de Google Drive o copia local)
3. Ejecutar:
```bash
# Clonar el repo
git clone https://github.com/locuxtech-ship-it/diegospizza.git
cd diegospizza

# Restaurar
bash restore.sh /ruta/al/backup.tar.gz
```

## Cron

El backup automatico esta configurado en el crontab del usuario oliver:
```
0 3 * * * RCLONE_REMOTE=gdrive /home/oliver/diegospizza/backup.sh >> /home/oliver/backups/diegospizza/cron.log 2>&1
```

Para verlo: `crontab -l`
Para editarlo: `crontab -e`

## Sincronizar produccion a entorno local

Para trabajar con datos reales de produccion en tu maquina local:

### Windows (PowerShell)
```powershell
.\scripts\sync-prod-data.ps1
```

Esto descarga el ultimo backup del servidor y lo importa a tu SQLite local.
Requiere: SSH key configurada, PHP 8.3+ con extensiones pdo_mysql y pdo_sqlite.

### Linux/Mac
```bash
# Descargar backup del servidor
scp "oliver@100.122.80.102:$(ssh oliver@100.122.80.102 'ls -t ~/backups/diegospizza/diegospizza_*.tar.gz | head -1')" /tmp/diegospizza_prod.tar.gz

# Importar
php artisan db:import-prod /tmp/diegospizza_prod.tar.gz
```

## Notas importantes

- El .env contiene contrasenas reales (DB, API keys). El backup incluye una copia.
- rclone esta instalado en `~/.local/bin/rclone`
- Los backups locales ocupan ~33MB por dia
- Google Drive: carpeta `diegospizza-backups`
- Tag en GitHub: `v1.0` (actualizado con cada cambio significativo)
- El restore.sh necesita que el servidor tenga Docker y git instalados
