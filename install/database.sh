#!/bin/sh

DB_SCHEMA="${BASEPATH}/nsti/dist/nsti.sql"

mysqladmin -s -u${DB_USER} -p${DB_PASS} create ${DB_NAME}

if ! mysql -u${DB_USER} -p${DB_PASS} ${DB_NAME} < $DB_SCHEMA;
then
    echo "Unable to add database schema. Does the ${DB_NAME} exist?"
    exit 1
fi
