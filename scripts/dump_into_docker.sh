#!/bin/bash

DB_USER=$DB_USER
DB_PASSWORD=$DB_PASSWORD
DB_HOST=$DB_HOST
DB_SCHEMA=$DB_USER # yes this is correct!

apt-get update
apt-get install -y mysql-client

# dump all the tables ctest tool is writing to
for table in import_data
do
  mysqldump -u$DB_USER -p$DB_PASSWORD -h$DB_HOST --column-statistics=0 --no-tablespaces --opt --where="1" --lock-tables=false $DB_SCHEMA $table > /mysqldumps/$table.sql
done