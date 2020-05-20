#! /bin/bash
#cd to script folder
cd "${0%/*}"

#define constants
DB_HOST=localhost
DB_PORT=3306
. ../laravel/.env
. .dropbox_uploader
#use Los Angeles time zone to make sure that we have back up
date=$(TZ=":America/Los_Angeles" date +"%d-%b-%Y")

credentialsFile=./.mysql-credentials.cnf
echo "[client]" > $credentialsFile
echo "user=$DB_USERNAME" >> $credentialsFile
echo "password=$DB_PASSWORD" >> $credentialsFile
echo "host=$DB_HOST" >> $credentialsFile
# Set default file permissions
umask 177

#upload to dropbox
./dropbox_uploader.sh -f .dropbox_uploader download /$DROP_BOX_FOLDER/$DB_DATABASE-$date.sql.gz ./$DB_DATABASE-$date.sql.gz
if [ ! -d "../laravel/storage/app" ]; then
  mkdir ../laravel/storage/app
  chmod 755 ../laravel/storage/app
fi
if [ ! -d "../laravel/storage/app/public" ]; then
  mkdir ../laravel/storage/app/public
  chmod 755 ../laravel/storage/app/public
fi
if [ ! -d "../laravel/storage/app/public/uploads" ]; then
  mkdir ../laravel/storage/app/public/uploads
  chmod 755 ../laravel/storage/app/public/uploads
fi
UPLOADS=$(./dropbox_uploader.sh -f .dropbox_uploader list /$DROP_BOX_FOLDER/uploads/ | awk '{ print $3}' | grep -v '\.\.\.' )
for u in $UPLOADS
do
    echo "Copping $u ..."
    ./dropbox_uploader.sh -f .dropbox_uploader download /$DROP_BOX_FOLDER/uploads/$u ../laravel/storage/app/public/uploads/$u
done

# Dump database into SQL file
#mysqldump --defaults-extra-file=$credentialsFile $DB_DATABASE | gzip> $DB_DATABASE-$date.sql.gz

mysql --defaults-extra-file=$credentialsFile -e "DROP DATABASE $DB_DATABASE"
mysql --defaults-extra-file=$credentialsFile -e "CREATE DATABASE $DB_DATABASE"

echo "Uploading dump..."
zcat ./$DB_DATABASE-$date.sql.gz | mysql --defaults-extra-file=$credentialsFile $DB_DATABASE
