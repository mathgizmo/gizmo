#! /bin/bash
#!/bin/bash
# Resolve script directory so cron (or other CWD) won't break paths
SCRIPT_DIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)

#define constants
DB_HOST=localhost
DB_PORT=3306
. "$SCRIPT_DIR/../laravel/.env"
. "$SCRIPT_DIR/.dropbox_uploader"
date=$(date +"%d-%b-%Y")

credentialsFile="$SCRIPT_DIR/.mysql-credentials.cnf"
# create credentials file
echo "[client]" > "$credentialsFile"
echo "user=$DB_USERNAME" >> "$credentialsFile"
echo "password=$DB_PASSWORD" >> "$credentialsFile"
echo "host=$DB_HOST" >> "$credentialsFile"
# Set default file permissions
umask 177
# Ensure a local `shasum` is available (hosting may not allow installing packages).
# We create a small shim in this folder and prepend it to PATH so dropbox_uploader
# can use it for chunked uploads.
shasum_local="$SCRIPT_DIR/shasum"
if [ -x "$shasum_local" ]; then
	PATH="$SCRIPT_DIR:$PATH"
else
	if [ -f "$shasum_local" ]; then
		chmod +x "$shasum_local" || true
		PATH="$SCRIPT_DIR:$PATH"
	fi
fi

# Run DB-only backup (separate script)
"$SCRIPT_DIR/do_db_backup.sh" || true

# upload uploads folder
"$SCRIPT_DIR/dropbox_uploader.sh" -f "$SCRIPT_DIR/.dropbox_uploader" upload "$SCRIPT_DIR/../laravel/storage/app/public/uploads/"* "/$DROP_BOX_FOLDER/uploads/"
