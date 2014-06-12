#!/bin/sh

DB_SCHEMA="${BASEPATH}/nsti/dist/nsti.sql"
INTERACTIVE="True"


if mysqlshow -u root &>/dev/null; then
	# Set the password to "nagiosxi"
	mysqlpass=nagiosxi
	mysqladmin -u root password "$mysqlpass"
	echo "MySQL root password is now set to: $mysqlpass"
else
	for i in 1 2 3; do
		if [ "$INTERACTIVE" = "True" ]; then
			# Ask for the password
			read -p "Enter MySQL Root Password: " pass
		fi

		# Test the password
		if mysqlshow -u root -p"$pass" &>/dev/null; then
			echo "Password validated."
			mysqlpass="$pass"
			break
		else
			echo "Password failed." >&2
			[ $i -eq 3 ] && exit 1
		fi
	done
fi

mysqladmin -s -uroot -p"$mysqlpass" create ${DB_NAME}
mysql -uroot -p"$mysqlpass" -e 'CREATE USER "'${DB_USER}'"@"'${DB_HOST}'" IDENTIFIED BY "'${DB_PASS}'";'
mysql -uroot -p"$mysqlpass" -e 'GRANT ALL PRIVILEGES ON snmptt.* TO "'${DB_USER}'"@"'${DB_HOST}'";'

if ! mysql -u${DB_USER} -p${DB_PASS} ${DB_NAME} < $DB_SCHEMA;
then
    echo "Unable to add database schema. Does the ${DB_NAME} database exist?"
    exit 1
fi
