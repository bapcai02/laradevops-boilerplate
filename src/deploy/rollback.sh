#!/bin/bash
set -e

BACKUP_DIR="/var/www/html/storage/backups"
DB_HOST=${DB_HOST:-mysql}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE:-laradevops}
DB_USERNAME=${DB_USERNAME:-root}
DB_PASSWORD=${DB_PASSWORD:-}

# Load DB creds from Laravel .env if present
if [ -f "/var/www/html/.env" ]; then
  export $(grep -E '^(DB_HOST|DB_PORT|DB_DATABASE|DB_USERNAME|DB_PASSWORD)=' /var/www/html/.env | xargs -r)
fi

print() { echo -e "[rollback] $1"; }

TARGET_FILE="$1"
if [ -z "$TARGET_FILE" ]; then
  # pick latest
  TARGET_FILE=$(ls -t ${BACKUP_DIR}/laravel_*.sql.gz 2>/dev/null | head -n 1)
fi

if [ -z "$TARGET_FILE" ] || [ ! -f "$TARGET_FILE" ]; then
  print "No backup file found to restore."
  exit 1
fi

print "Restoring from $TARGET_FILE ..."

TMP_SQL="/tmp/restore.sql"
if [[ "$TARGET_FILE" == *.gz ]]; then
  gunzip -c "$TARGET_FILE" > "$TMP_SQL"
else
  cp "$TARGET_FILE" "$TMP_SQL"
fi

# Build password option only if provided, and disable SSL to work with local dev
PASS_OPT=""
if [ -n "$DB_PASSWORD" ]; then
  PASS_OPT="-p$DB_PASSWORD"
fi
mysql --skip-ssl -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USERNAME" $PASS_OPT "$DB_DATABASE" < "$TMP_SQL"
rm -f "$TMP_SQL"

print "Restore completed."


