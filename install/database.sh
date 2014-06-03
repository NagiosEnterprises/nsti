#!/bin/sh

DB_SCHEMA="${BASEPATH}/nsti/dist/nsti.sql"

mysqladmin -s -uroot -p"${DB_ROOT_PASS}" create ${DB_NAME}
mysql -uroot -p"${DB_ROOT_PASS}" -e 'CREATE USER "'${DB_USER}'"@"'${DB_HOST}'" IDENTIFIED BY "'${DB_PASS}'";'
mysql -uroot -p"${DB_ROOT_PASS}" -e 'GRANT ALL PRIVILEGES ON snmptt.* TO "'${DB_USER}'"@"'${DB_HOST}'";'

if ! mysql -u${DB_USER} -p${DB_PASS} ${DB_NAME} < $DB_SCHEMA;
then
    echo "Unable to add database schema. Does the ${DB_NAME} database exist?"
    exit 1
fi
