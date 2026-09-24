#!/bin/bash
# DB-only backup script extracted from do_backup.sh
SCRIPT_DIR=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)

# load env and uploader config
. "$SCRIPT_DIR/../laravel/.env"
. "$SCRIPT_DIR/.dropbox_uploader"

date=$(date +"%d-%b-%Y")

# Make cron environment deterministic and pick up local shasum shim
LANG=C
PATH="$SCRIPT_DIR:$PATH"

# Simple lock to avoid concurrent runs
LOCKDIR="$SCRIPT_DIR/.do_db_backup.lock"
if ! mkdir "$LOCKDIR" 2>/dev/null; then
    echo "Another do_db_backup.sh is running; exiting." >&2
    exit 0
fi
trap 'rm -rf "$LOCKDIR"' EXIT

# DRY_RUN=1 will only print actions without performing deletes
DRY_RUN=${DRY_RUN:-0}

# Verbose mode: when VERBOSE=1 print full logs, otherwise print concise messages
VERBOSE=${VERBOSE:-0}
info() { if [ "$VERBOSE" = "1" ]; then echo "$@"; fi }
short() { if [ "$VERBOSE" != "1" ]; then echo "$@"; fi }

# Choose dropbox_uploader invocation based on verbosity. Use -d (debug) only in verbose mode,
# otherwise request quiet output from the uploader to avoid noisy traces.
if [ "$VERBOSE" = "1" ]; then
    DU_CMD=("$SCRIPT_DIR/dropbox_uploader.sh" -d -f "$SCRIPT_DIR/.dropbox_uploader")
else
    DU_CMD=("$SCRIPT_DIR/dropbox_uploader.sh" -f "$SCRIPT_DIR/.dropbox_uploader" -q)
fi

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
    # Accept date like 01-May-2026 and return epoch seconds (UTC)
    python3 - <<PY
import sys,datetime
try:
    d=sys.argv[1]
    dt=datetime.datetime.strptime(d, "%d-%b-%Y")
    print(int(dt.replace(tzinfo=datetime.timezone.utc).timestamp()))
except Exception:
    sys.exit(1)
PY
}

months_diff() {
    # months difference between given date (DD-Mon-YYYY) and today
    python3 - <<PY
import sys,datetime
try:
    d=sys.argv[1]
    dt=datetime.datetime.strptime(d, "%d-%b-%Y")
    now=datetime.datetime.utcnow()
    months=(now.year*12+now.month)-(dt.year*12+dt.month)
    print(months)
except Exception:
    sys.exit(1)
PY
}

# Delete remote file via dropbox_uploader.sh (debug mode) and treat
# path_lookup/not_found as non-fatal (file already absent).
db_delete_remote() {
    local remote_path="$1"
    local tries=0
    local max=3
    while [ $tries -lt $max ]; do
        tries=$((tries+1))
        "${DU_CMD[@]}" delete "$remote_path" > /tmp/du_resp_debug 2>&1 || true
        # extract JSON payload if present
        if [ -s /tmp/du_resp_debug ]; then
            python3 - <<PY
import sys
b=open('/tmp/du_resp_debug','rb').read()
s=b.find(b'{')
if s!=-1:
    cnt=0
    for i in range(s,len(b)):
        if b[i]==123: cnt+=1
        elif b[i]==125: cnt-=1
        if cnt==0:
            open('/tmp/du_json','wb').write(b[s:i+1])
            break
PY
        fi

        # Treat HTTP 200 or path_lookup/not_found as success
        if grep -q '^HTTP/2 200' /tmp/du_resp_debug 2>/dev/null; then
            # if JSON contains an error of type path_lookup/not_found treat as success
            if command -v jq >/dev/null 2>&1 && [ -f /tmp/du_json ]; then
                if jq -e '.error?.path_lookup? | .".tag" == "not_found"' /tmp/du_json >/dev/null 2>&1; then
                    return 0
                fi
            else
                # no jq; still accept HTTP 200
                return 0
            fi
            return 0
        fi

        # If JSON shows a not_found error, treat as success
        if command -v jq >/dev/null 2>&1 && [ -f /tmp/du_json ]; then
            if jq -e '.error?.path_lookup? | .".tag" == "not_found"' /tmp/du_json >/dev/null 2>&1; then
                return 0
            fi
        fi

        # If this was the last try, fail
        if [ $tries -ge $max ]; then
            return 1
        fi
        sleep $((tries*2))
    done
}

# Dump database into SQL file
info "> Dumping database $DB_DATABASE to $SCRIPT_DIR/$DB_DATABASE-$date.sql.gz"
short "Preparing backup: $DB_DATABASE-$date.sql.gz"
mysqldump --defaults-extra-file="$credentialsFile" "$DB_DATABASE" | gzip > "$SCRIPT_DIR/$DB_DATABASE-$date.sql.gz"

# upload DB backup
info "> Uploading DB backup to Dropbox"
"${DU_CMD[@]}" upload "$SCRIPT_DIR/$DB_DATABASE-$date.sql.gz" "/$DROP_BOX_FOLDER/"
short "Backup uploaded: $DB_DATABASE-$date.sql.gz"

# Cleanup old DB backups on DropBox:
# Retention policy (day-based):
# - Backups created on day 01 or 15: remove if older than 120 days
# - All other DB backups: remove if older than 30 days

DBG_FILE="/tmp/du_resp_debug"
# get listing (debug) and extract JSON payload to /tmp/du_json
"${DU_CMD[@]}" list "/$DROP_BOX_FOLDER" > "$DBG_FILE" 2>&1 || true
if [ -s "$DBG_FILE" ]; then
    python3 - <<PY
import sys
b=open('$DBG_FILE','rb').read()
s=b.find(b'{')
if s==-1:
    sys.exit(0)
cnt=0
for i in range(s,len(b)):
    if b[i]==123: cnt+=1
    elif b[i]==125: cnt-=1
    if cnt==0:
        open('/tmp/du_json','wb').write(b[s:i+1])
        print('wrote',i+1-s)
        break
PY
fi

if ! command -v jq >/dev/null 2>&1; then
    echo "Warning: jq not found. Listing will try to fall back to text parsing." >&2
fi

# Build list of .sql.gz path_display entries using jq where available
if command -v jq >/dev/null 2>&1 && [ -f /tmp/du_json ]; then
    TMP_PATHS=$(mktemp)
    jq -r '.entries[]? | .path_display // empty | select(test("\\.sql\\.gz$"))' /tmp/du_json > "$TMP_PATHS" 2>/dev/null || true
    mapfile -t sql_paths < "$TMP_PATHS"
    rm -f "$TMP_PATHS"
else
    # fallback to previous awk/perl/sed pipeline on raw debug output
    LIST_OUT=$(mktemp)
    TMP_PATHS=$(mktemp)
    cp "$DBG_FILE" "$LIST_OUT" || true
    awk -F'"path_display"' '{ for(i=2;i<=NF;i++){ if(match($i,/"([^\\\"]+)"/,m)) print m[1] } }' "$LIST_OUT" \
        | tr -d '\r' \
        | perl -pe 's/\e\[?.*?[@-~]//g' \
        | sed 's/[^[:print:]\t]//g' | grep -E '\.sql\.gz$' > "$TMP_PATHS" 2>/dev/null || true
    mapfile -t sql_paths < "$TMP_PATHS"
    rm -f "$LIST_OUT" "$TMP_PATHS"
fi

# Summary of found files
num_paths=${#sql_paths[@]}
short "Found $num_paths files on Dropbox"

found_count=0

for path_display in "${sql_paths[@]}"; do
    fname=$(basename "$path_display")
    if [[ "$fname" != *.sql.gz ]]; then
        continue
    fi
    if [[ "$fname" =~ ([0-9]{2}-[A-Za-z]{3}-[0-9]{4}) ]]; then
        datestr="${BASH_REMATCH[1]}"
    else
        # try to extract from server_modified via jq if available
        if command -v jq >/dev/null 2>&1 && [ -f /tmp/du_json ]; then
            # find matching entry and extract server_modified
            sv=$(jq -r --arg p "$path_display" '.entries[]? | select(.path_display == $p) | .server_modified // empty' /tmp/du_json)
            if [ -n "$sv" ]; then
                # server_modified is ISO8601, convert to DD-Mon-YYYY
                datestr=$(date -u -d "$sv" +"%d-%b-%Y" 2>/dev/null || true)
            fi
        fi
        if [ -z "$datestr" ]; then
            continue
        fi
    fi
    day=${datestr%%-*}
    # compute age in days (difference between now and datestr)
    age_days=0
    # compute age in days; pass the datestr as argv to Python to avoid heredoc argv issues
    age_days=$(python3 - "$datestr" <<PY
import sys,datetime
try:
    d=sys.argv[1]
    dt=datetime.datetime.strptime(d, "%d-%b-%Y")
    now=datetime.datetime.utcnow()
    delta=(now - dt).days
    print(int(delta))
except Exception:
    sys.exit(1)
PY
    ) || continue

        # Decide retention policy (days)
        delete_candidate=0
        if [ "$day" = "01" ] || [ "$day" = "15" ]; then
            if [ "$age_days" -gt 120 ]; then
                delete_candidate=1
            fi
        else
            if [ "$age_days" -gt 30 ]; then
                delete_candidate=1
            fi
        fi

        if [ "$delete_candidate" -eq 1 ]; then
            info "> Candidate for delete: $path_display  (age_days=$age_days, day=$day)"
            found_count=$((found_count+1))
            if [ "$DRY_RUN" = "1" ]; then
                short "> DRY_RUN: would delete $fname"
            else
                short "Deleting $fname"
                if db_delete_remote "/$DROP_BOX_FOLDER/$fname"; then
                    info "> OK deleted or already absent: $fname"
                    short "Deleted: $fname"
                else
                    info "> FAIL deleting: $fname -- see /tmp/du_resp_debug for response"
                    short "Failed to delete: $fname"
                    sed -n '1,200p' /tmp/du_resp_debug 2>/dev/null || true
                fi
            fi
        fi

done

rm -f /tmp/du_json 2>/dev/null || true

if [ "$VERBOSE" != "1" ]; then
    if [ "$DRY_RUN" = "1" ]; then
        short "DRY_RUN: would delete $found_count files"
    else
        short "Done: deleted $found_count files"
    fi
fi

# Delete local DB dumps older than 30 days
find "$SCRIPT_DIR" -maxdepth 1 -name "*.sql.gz" -mtime +30 -print -exec rm {} \;

echo "> DB backup finished"

exit 0
