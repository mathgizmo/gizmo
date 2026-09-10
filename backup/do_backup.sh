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

# Dump database into SQL file
mysqldump --defaults-extra-file="$credentialsFile" "$DB_DATABASE" | gzip > "$SCRIPT_DIR/$DB_DATABASE-$date.sql.gz"

#upload to dropbox
"$SCRIPT_DIR/dropbox_uploader.sh" -f "$SCRIPT_DIR/.dropbox_uploader" upload "$SCRIPT_DIR/$DB_DATABASE-$date.sql.gz" "/$DROP_BOX_FOLDER/"
"$SCRIPT_DIR/dropbox_uploader.sh" -f "$SCRIPT_DIR/.dropbox_uploader" upload "$SCRIPT_DIR/../laravel/storage/app/public/uploads/"* "/$DROP_BOX_FOLDER/uploads/"

# Cleanup old DB backups on DropBox:
# - Backups created on day 01 or 15: remove if older than 4 months
# - All other DB backups: remove if older than 1 month

now_epoch=$(date +%s)

to_epoch() {
	local d="$1"
	# Try GNU date first, fall back to BSD/macOS date
	if date --version >/dev/null 2>&1; then
		date -d "$d" +%s 2>/dev/null || return 1
	else
		date -j -f "%d-%b-%Y" "$d" +%s 2>/dev/null || return 1
	fi
}

# Delete remote file via dropbox_uploader.sh (debug mode) and treat
# path_lookup/not_found as non-fatal (file already absent).
db_delete_remote() {
	local remote_path="$1"
	# run delete in debug so /tmp/du_resp_debug contains JSON response
	"$SCRIPT_DIR/dropbox_uploader.sh" -d -f "$SCRIPT_DIR/.dropbox_uploader" delete "$remote_path" > /dev/null 2>/dev/null || true
	# success if HTTP/2 200
	if grep -q '^HTTP/2 200' /tmp/du_resp_debug 2>/dev/null; then
		return 0
	fi
	# treat not_found as success (already deleted)
	if grep -q 'path_lookup/not_found' /tmp/du_resp_debug 2>/dev/null; then
		return 0
	fi
	return 1
}

# list files in remote dropbox folder and iterate (robust JSON parsing)
# Run dropbox_uploader in debug mode so the raw Dropbox JSON response
# is written to /tmp/du_resp_debug. We copy that file to a safe temp,
# clean control characters, then extract `path_display` values.
"$SCRIPT_DIR/dropbox_uploader.sh" -d -f "$SCRIPT_DIR/.dropbox_uploader" list "/$DROP_BOX_FOLDER" > /dev/null 2>/dev/null || true

# Copy uploader debug JSON to a local temp file (if present)
DBG_FILE="/tmp/du_resp_debug"
LIST_OUT=$(mktemp)
if [ -f "$DBG_FILE" ]; then
		cp "$DBG_FILE" "$LIST_OUT" || true
else
		# fallback: attempt to capture stdout listing if debug file missing
		"$SCRIPT_DIR/dropbox_uploader.sh" -f "$SCRIPT_DIR/.dropbox_uploader" list "/$DROP_BOX_FOLDER" > "$LIST_OUT" 2>/dev/null || true
fi

# Clean and extract path_display fields (strip CR/ANSI/non-printables)
CLEAN_LIST=$(mktemp)
sed -n 's/.*"path_display": *"\([^\"]*\)".*/\1/p' "$LIST_OUT" \
	| tr -d '\r' \
	| perl -pe 's/\e\[?.*?[@-~]//g' \
	| sed 's/[^[:print:]\t]//g' > "$CLEAN_LIST"

while read -r path_display; do
	fname=$(basename "$path_display")
	# only consider .sql.gz DB backups
	if [[ "$fname" != *.sql.gz ]]; then
		continue
	fi

	if [[ "$fname" =~ ([0-9]{2}-[A-Za-z]{3}-[0-9]{4}) ]]; then
		datestr="${BASH_REMATCH[1]}"
	else
		continue
	fi

	day=${datestr%%-*}
	file_epoch=$(to_epoch "$datestr")
	if [ -z "$file_epoch" ]; then
		continue
	fi
	# approximate months difference by 30-day months
	age_months=$(( (now_epoch - file_epoch) / (30*24*3600) ))

	if [ "$day" = "01" ] || [ "$day" = "15" ]; then
		if [ "$age_months" -gt 4 ]; then
			db_delete_remote "/$DROP_BOX_FOLDER/$fname" || echo "delete failed: $fname" >&2
		fi
	else
		if [ "$age_months" -gt 1 ]; then
			db_delete_remote "/$DROP_BOX_FOLDER/$fname" || echo "delete failed: $fname" >&2
		fi
	fi
done < "$CLEAN_LIST"

# cleanup temp files
rm -f "$LIST_OUT" "$CLEAN_LIST"

# Delete local files older than 30 days (approx. 1 month)
find "$SCRIPT_DIR" -maxdepth 1 -name "*.sql.gz" -mtime +30 -exec rm {} \;
