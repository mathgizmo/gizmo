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

# Delete files older than 30 days
find ./*.sql.gz -mtime +30 -exec rm {} \;
