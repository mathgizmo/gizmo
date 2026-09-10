#!/bin/bash
#!/bin/bash
# DB-only backup script extracted from do_backup.sh
SCRIPT_DIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)

# load env and uploader config
. "$SCRIPT_DIR/../laravel/.env"
. "$SCRIPT_DIR/.dropbox_uploader"

date=$(date +"%d-%b-%Y")

credentialsFile="$SCRIPT_DIR/.mysql-credentials.cnf"
# create credentials file
echo "[client]" > "$credentialsFile"
echo "user=$DB_USERNAME" >> "$credentialsFile"
echo "password=$DB_PASSWORD" >> "$credentialsFile"
echo "host=localhost" >> "$credentialsFile"

# Set default file permissions
umask 177

# Ensure local shasum shim is available for constrained hosts
shasum_local="$SCRIPT_DIR/shasum"
if [ -x "$shasum_local" ]; then
    PATH="$SCRIPT_DIR:$PATH"
else
    if [ -f "$shasum_local" ]; then
        chmod +x "$shasum_local" || true
        PATH="$SCRIPT_DIR:$PATH"
    fi
fi

now_epoch=$(date +%s)

to_epoch() {
    local d="$1"
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
    "$SCRIPT_DIR/dropbox_uploader.sh" -d -f "$SCRIPT_DIR/.dropbox_uploader" delete "$remote_path" > /dev/null 2>/dev/null || true
    if grep -q '^HTTP/2 200' /tmp/du_resp_debug 2>/dev/null; then
        return 0
    fi
    if grep -q 'path_lookup/not_found' /tmp/du_resp_debug 2>/dev/null; then
        return 0
    fi
    return 1
}

# Dump database into SQL file
echo "> Dumping database $DB_DATABASE to $SCRIPT_DIR/$DB_DATABASE-$date.sql.gz"
mysqldump --defaults-extra-file="$credentialsFile" "$DB_DATABASE" | gzip > "$SCRIPT_DIR/$DB_DATABASE-$date.sql.gz"

# upload DB backup
echo "> Uploading DB backup to Dropbox"
"$SCRIPT_DIR/dropbox_uploader.sh" -f "$SCRIPT_DIR/.dropbox_uploader" upload "$SCRIPT_DIR/$DB_DATABASE-$date.sql.gz" "/$DROP_BOX_FOLDER/"

# Cleanup old DB backups on DropBox:
# - Backups created on day 01 or 15: remove if older than 4 months
# - All other DB backups: remove if older than 1 month

# get listing (debug JSON)
"$SCRIPT_DIR/dropbox_uploader.sh" -d -f "$SCRIPT_DIR/.dropbox_uploader" list "/$DROP_BOX_FOLDER" > /dev/null 2>/dev/null || true
DBG_FILE="/tmp/du_resp_debug"
LIST_OUT=$(mktemp)
if [ -f "$DBG_FILE" ]; then
    cp "$DBG_FILE" "$LIST_OUT" || true
else
    "$SCRIPT_DIR/dropbox_uploader.sh" -f "$SCRIPT_DIR/.dropbox_uploader" list "/$DROP_BOX_FOLDER" > "$LIST_OUT" 2>/dev/null || true
fi

CLEAN_LIST=$(mktemp)
# Use awk to extract every "path_display" occurrence (handles single-line JSON)
awk -F'"path_display"' '{ for(i=2;i<=NF;i++){ if(match($i,/"([^"]+)"/,m)) print m[1] } }' "$LIST_OUT" \
    | tr -d '\r' \
    | perl -pe 's/\e\[?.*?[@-~]//g' \
    | sed 's/[^[:print:]\t]//g' > "$CLEAN_LIST"

while read -r path_display; do
    fname=$(basename "$path_display")
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
    age_months=$(( (now_epoch - file_epoch) / (30*24*3600) ))
        # Decide retention policy
        delete_candidate=0
        if [ "$day" = "01" ] || [ "$day" = "15" ]; then
            if [ "$age_months" -gt 4 ]; then
                delete_candidate=1
            fi
        else
            if [ "$age_months" -gt 1 ]; then
                delete_candidate=1
            fi
        fi

        if [ "$delete_candidate" -eq 1 ]; then
            echo "> Candidate for delete: $path_display  (age_months=$age_months, day=$day)"
            # attempt delete and report result
            if db_delete_remote "/$DROP_BOX_FOLDER/$fname"; then
                echo "> OK deleted or already absent: $fname"
            else
                echo "> FAIL deleting: $fname -- see /tmp/du_resp_debug for response" >&2
                # show a short snippet of the response for debugging
                sed -n '1,200p' /tmp/du_resp_debug 2>/dev/null || true
            fi
        fi
done < "$CLEAN_LIST"

rm -f "$LIST_OUT" "$CLEAN_LIST"

# Delete local DB dumps older than 30 days
find "$SCRIPT_DIR" -maxdepth 1 -name "*.sql.gz" -mtime +30 -exec rm {} \;

echo "> DB backup finished"

exit 0
