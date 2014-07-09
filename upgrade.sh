#!/bin/sh

PREREQS="mysql mysql-devel mysql-server httpd gcc wget make tar snmptt net-snmp net-snmp-utils mod_wsgi python-devel"
DB_ROOT_PASS='nsti'
LOG_FILE="upgrade-`date +%s`.log"
BASEPATH=$(dirname `readlink -f $0`)
RELEASE = 0

touch "$LOG_FILE"

# Check release - latest.php (sinkhole/downloads)

. install/libinstall.sh
. "$BASEPATH/nsti/etc/nsti.py"

# Read databases that exist and compare to ones that are need for 3.0.3
# make a sanity check for databases, users and passwords. need to preserve
# the existing snmptt database (put them in archive or normal table?)

database_upgrade.sh | tee --append "$LOG_FILE"

# Assume the install.sh section to check for all prerequisites (more than likely remove database.sh)

# Check to make sure the prereqs are met.
. install/prereqs.sh | tee --append "$LOG_FILE"
# Check to make sure Python is of proper version
. install/pythonmodules.sh | tee --append "$LOG_FILE"
# Edit the SNMPTT defaults
. install/snmptt.sh | tee --append "$LOG_FILE"
# Adding the apache configuration
. install/apacheconfig.sh | tee --append "$LOG_FILE"
# Adding rule to the firewall
. install/firewall.sh | tee --append "$LOG_FILE"
# Moving the directory structure
. install/movedirectory.sh | tee --append "$LOG_FILE"


# cleanup process

echo "
============================
*NSTI Upgraded Successfully*
============================

Access NSTI here:
	
	<IP address>:8080
	
============================
"