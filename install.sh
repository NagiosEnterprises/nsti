#!/bin/sh

PREREQS='mysql httpd gcc wget make'

. install/libinstall.sh

# Check to make sure the prereqs are met.
. install/prereqs.sh
# Check to make sure Python is of proper version
. install/checkpython.sh

