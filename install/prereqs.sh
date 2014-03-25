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

echo "Checking users and groups..."
echo "----------------------------"
if ! cat /etc/passwd | grep nagios;
then
    echo 'No user `nagios`. Add a user for nagios and make sure it is in the nagcmd group.'
    echo 'Apache must also be in nagcmd.'
    exit 1
fi
echo "---------------------------"
echo "USERS/GROUPS OK"
echo "---------------------------"
echo ""
