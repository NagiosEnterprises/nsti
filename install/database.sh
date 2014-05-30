#!/bin/sh

DB_SCHEMA="${BASEPATH}/nsti/dist/nsti.sql"

mysqladmin CREATE ${DB_NAME}

if ! mysql -u${DB_USER} -p${DB_PASS} -h${DB_HOST} ${DB_NAME} < $DB_SCHEMA;
then
    echo "Unable to add database schema. Does the ${DB_NAME} exist?"
    exit 1
fi
