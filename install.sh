#!/bin/sh

PREREQS="mysql mysql-devel mysql-server httpd gcc wget make tar snmptt net-snmp net-snmp-utils mod_wsgi python-devel"

. install/libinstall.sh

# Check to make sure the prereqs are met.
. install/prereqs.sh
# Check to make sure Python is of proper version
. install/pythonmodules.sh
# Adding the database
. install/database.sh
# Edit the SNMPTT defaults
. install/snmptt.sh
# Adding the apache configuration
. install/apacheconfig.sh
# Moving the directory structure
. install/movedirectory.sh
