#!/bin/sh

PREREQS='mysql httpd gcc wget make pip python'

. install/libinstall.sh

# Check to make sure the prereqs are met.
. install/prereqs.sh
# Check to make sure Python is of proper version
#. install/pythonmodules.sh
# Adding the database
. install/database.sh
# Adding the apache configuration
. install/apacheconfig.sh
# Moving the directory structure
. install/movedirectory.sh
