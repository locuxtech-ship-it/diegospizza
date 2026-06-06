#!/bin/bash
set -e

BACKUP_DIR="/var/backups/diegospizza"
DB_NAME="diegospizza"
DB_USER="diegospizza"
DB_PASS="${DB_PASSWORD}"
DATE=$(date +%Y%m%d_%H%M%S)
RETENTION_DAYS=7

mkdir -p "$BACKUP_DIR"

mysqldump --single-transaction --routines --triggers \
    -h db -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" \
    | gzip > "$BACKUP_DIR/${DB_NAME}_${DATE}.sql.gz"

find "$BACKUP_DIR" -name "${DB_NAME}_*.sql.gz" -mtime +$RETENTION_DAYS -delete

echo "Backup created: ${BACKUP_DIR}/${DB_NAME}_${DATE}.sql.gz"
