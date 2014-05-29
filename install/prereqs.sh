#!/bin/sh

PREREQS='mysql httpd gcc wget make tar'

echo "Checking installer prereqs..."
echo "-----------------------------"
if ! is_installed "$PREREQS";
then
    echo "Baseline prereqs are not installed. You must have these installed:"
    for prereq in "$PREREQS";
    do
        echo " - $prereq"
    done
    echo "Cannot continue install until all of these prereqs are met."
    exit 1
fi
echo "----------------------------"
echo "INSTALLER PREREQS MET"
echo "----------------------------"
echo ""

echo "Making sure WSGI is installed in Apache..."
echo "------------------------------------------"
if ! httpd -M 2> /dev/null | grep wsgi;
then
    echo "mod_wsgi is not install in Apache. Please use your package manager to install it."
    exit 1
fi
echo "----------------------------"
echo "MOD_WSGI INSTALLED"
echo "----------------------------"
echo ""

echo "Creating users and groups..."
echo "----------------------------"
if ! cat /etc/passwd | grep nagios;
then

    # Add nagios user
    add_user nagios
    add_group nagios
    add_group nagcmd
	
    # Add nagios user to nagios group
    add_to_groups nagios nagios nagcmd
    add_to_groups apache nagios nagcmd

fi
echo "---------------------------"
echo "USERS/GROUPS CREATED"
echo "---------------------------"
echo ""
