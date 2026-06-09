#!/bin/bash
# Backup script for Alogoto database
# Usage: ./backup.sh [--restore file.sql]

set -e

BACKUP_DIR="$(dirname "$0")/../storage/backups"
DB_NAME="alogoto_backend"
DB_USER="root"
DB_PASS="Adechina@"
DB_HOST="127.0.0.1"
DB_PORT="3306"
RETENTION_DAYS=30
TIMESTAMP=$(date '+%Y%m%d_%H%M%S')

mkdir -p "$BACKUP_DIR"

if [ "$1" = "--restore" ]; then
    if [ -z "$2" ]; then
        echo "Usage: $0 --restore <backup_file.sql>"
        exit 1
    fi
    if [ ! -f "$2" ]; then
        echo "Fichier de backup introuvable : $2"
        exit 1
    fi
    echo "Restauration depuis $2..."
    mysql -u "$DB_USER" -p"$DB_PASS" -h "$DB_HOST" -P "$DB_PORT" "$DB_NAME" < "$2"
    echo "Restauration terminée."
    exit 0
fi

BACKUP_FILE="$BACKUP_DIR/alogoto_backup_${TIMESTAMP}.sql"
echo "Sauvegarde de $DB_NAME vers $BACKUP_FILE..."

mysqldump \
    --user="$DB_USER" \
    --password="$DB_PASS" \
    --host="$DB_HOST" \
    --port="$DB_PORT" \
    --single-transaction \
    --routines \
    --triggers \
    --events \
    "$DB_NAME" \
    | gzip > "${BACKUP_FILE}.gz"

echo "Sauvegarde compressee : ${BACKUP_FILE}.gz"
echo "Nettoyage des backups de plus de ${RETENTION_DAYS} jours..."
find "$BACKUP_DIR" -name "alogoto_backup_*.sql.gz" -mtime "+${RETENTION_DAYS}" -delete

echo "Sauvegarde terminee avec succes."
