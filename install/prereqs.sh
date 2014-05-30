#!/bin/sh

PREREQS='mysql httpd gcc wget make tar'

#install database prereqs- only supporting yum packager FOR NOW
yum install -y mysql mysql-devel mysql-server httpd

#make sure our services are running
service mysqld start

#set mysql user and password
/usr/bin/mysqladmin -u root password 'nsti'
service mysqld restart

#set to start on system startup
chkconfig mysqld on
chkconfig httpd on

#install more prereqs
yum install -y gcc wget make tar mod_wsgi python-devel

#grab pip
curl https://raw.githubusercontent.com/pypa/pip/master/contrib/get-pip.py | python

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
echo "-----------------------"
echo "INSTALLER PREREQS MET"
echo "-----------------------"
echo ""

echo "Making sure WSGI is installed in Apache..."
echo "------------------------------------------"
if ! httpd -M 2> /dev/null | grep wsgi;
then
    echo "mod_wsgi is not installed in Apache. Please use your package manager to install it."
    exit 1
fi
echo "---------------------"
echo "MOD_WSGI INSTALLED"
echo "---------------------"
echo ""

# Add nagios user
add_user nagios
add_group nagios
add_group nagcmd

# Add nagios user to nagios group
add_to_groups nagios nagios nagcmd
add_to_groups apache nagios nagcmd

echo "Creating users and groups..."
echo "----------------------------"
if ! cat /etc/passwd | grep nagios;
then
    echo 'No user `nagios`. Add a user for nagios and make sure it is in the nagcmd group.'
    echo 'Apache must also be in nagcmd.'
    exit 1
fi
echo "------------------"
echo "USERS/GROUPS OK"
echo "------------------"
echo ""