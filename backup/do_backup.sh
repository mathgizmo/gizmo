#! /bin/bash
#cd to script folder
cd "${0%/*}"

#define constants
DB_HOST=localhost
DB_PORT=3306
. ../laravel/.env
. .dropbox_uploader
date=$(date +"%d-%b-%Y")

credentialsFile=./.mysql-credentials.cnf
echo "[client]" > $credentialsFile
echo "user=$DB_USERNAME" >> $credentialsFile
echo "password=$DB_PASSWORD" >> $credentialsFile
echo "host=$DB_HOST" >> $credentialsFile
# Set default file permissions
umask 177
# Dump database into SQL file
mysqldump --defaults-extra-file=$credentialsFile $DB_DATABASE | gzip> $DB_DATABASE-$date.sql.gz

#upload to dropbox
./dropbox_uploader.sh -f .dropbox_uploader upload ./$DB_DATABASE-$date.sql.gz /$DROP_BOX_FOLDER/
./dropbox_uploader.sh -f .dropbox_uploader upload ../laravel/storage/app/public/uploads/* /$DROP_BOX_FOLDER/uploads/

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

# list files in remote dropbox folder and iterate
./dropbox_uploader.sh -f .dropbox_uploader list /$DROP_BOX_FOLDER | while read -r line; do
	# take the last whitespace-separated token as filename (our backups have no spaces)
	fname=$(echo "$line" | awk '{print $NF}')
	# only target db sql.gz files that include a date like 01-Sep-2026
	if [[ "$fname" =~ [0-9]{2}-[A-Za-z]{3}-[0-9]{4} ]]; then
		datestr=${BASH_REMATCH[0]}
		day=${datestr%%-*}
		file_epoch=$(to_epoch "$datestr")
		if [ -z "$file_epoch" ]; then
			continue
		fi
		# approximate months difference by 30-day months
		age_months=$(( (now_epoch - file_epoch) / (30*24*3600) ))

		if [ "$day" = "01" ] || [ "$day" = "15" ]; then
			if [ "$age_months" -gt 4 ]; then
				./dropbox_uploader.sh -f .dropbox_uploader delete /$DROP_BOX_FOLDER/"$fname"
			fi
		else
			if [ "$age_months" -gt 1 ]; then
				./dropbox_uploader.sh -f .dropbox_uploader delete /$DROP_BOX_FOLDER/"$fname"
			fi
		fi
	fi
done

# Delete local files older than 30 days (approx. 1 month)
find ./*.sql.gz -mtime +30 -exec rm {} \;
