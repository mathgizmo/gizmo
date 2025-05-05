#! /bin/bash
#cd to script folder
cd "${0%/*}"

#define constants
DB_HOST=localhost
DB_PORT=3306
. ../../laravel/.env
#use Los Angeles time zone to make sure that we have back up
date=$(TZ=":America/Los_Angeles" date +"%d-%b-%Y")

credentialsFile=./.mysql-credentials.cnf
echo "[client]" > $credentialsFile
echo "user=$DB_USERNAME" >> $credentialsFile
echo "password=$DB_PASSWORD" >> $credentialsFile
echo "host=$DB_HOST" >> $credentialsFile

# reload DB from dumps

mysql --defaults-extra-file=$credentialsFile -e "DROP DATABASE $DB_DATABASE"
mysql --defaults-extra-file=$credentialsFile -e "CREATE DATABASE $DB_DATABASE"

echo "Uploading dump..."
mysql --defaults-extra-file=$credentialsFile $DB_DATABASE < ./testDB.sql
mysql --defaults-extra-file=$credentialsFile $DB_DATABASE < ./testDBdata.sql
echo "Done uploading dump."

