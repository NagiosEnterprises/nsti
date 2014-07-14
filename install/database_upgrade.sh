#!/bin/sh

DB_SCHEMA="${BASEPATH}/nsti/dist/nsti.sql"
INTERACTIVE="True"

# echo the database we are working on
echo "Database being upgraded....

	 ****************************** !WARNING! ********************************
	 It is very important that you backup your databases before running this 
	 upgrade script.  The origional trap data will be kept in the new database
	 and the old filters will be kept in the new snmptt table filters-1.4 for
	 backup.
	 *************************************************************************
	 "

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

# add timewritten column to existing snmptt tables
mysql -uroot -p"$mysqlpass" -e 'ALTER TABLE snmptt ADD `timewritten` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP after `trapread`'
mysql -uroot -p"$mysqlpass" -e 'ALTER TABLE snmptt_archive ADD `timewritten` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP after `trapread`'
mysql -uroot -p"$mysqlpass" -e 'ALTER TABLE snmptt_unknown ADD `timewritten` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP after `trapread`'

# rename and preserve old filter table - snmptt.filters_1_4
mysql -uroot -p"$mysqlpass" -e 'RENAME TABLE `snmptt.filters` TO `snmptt.filters_1_4`'

# get data from filters_1_4 table to include in new filter tables
mysqldump -uroot -p"$mysqlpass" snmptt filters_1_4 --compact > filters_1_4.sql

# create new snmptt.filter and snmptt.filter_atom tables and import old filters
mysql -uroot -p"$mysqlpass" < table_upgrade.sql


