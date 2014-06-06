#!/bin/sh

PREREQS="mysql mysql-devel mysql-server httpd gcc wget make tar snmptt net-snmp net-snmp-utils mod_wsgi python-devel"
DB_ROOT_PASS='nsti'
LOG_FILE="install-`date +%s`.log"
BASEPATH=$(dirname `readlink -f $0`)

touch "$LOG_FILE"

. install/libinstall.sh
. "$BASEPATH/nsti/etc/nsti.py"

# Check to make sure the prereqs are met.
. install/prereqs.sh | tee --append "$LOG_FILE"
# Check to make sure Python is of proper version
. install/pythonmodules.sh | tee --append "$LOG_FILE"
# Adding the database
. install/database.sh | tee --append "$LOG_FILE"
# Edit the SNMPTT defaults
. install/snmptt.sh | tee --append "$LOG_FILE"
# Adding the apache configuration
. install/apacheconfig.sh | tee --append "$LOG_FILE"
# Adding rule to the firewall
. install/firewall.sh | tee --append "$LOG_FILE"
# Moving the directory structure
. install/movedirectory.sh | tee --append "$LOG_FILE"
