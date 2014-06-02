Installation
============

NSTI performs well with and is designed to run on any Linux distribution.


Installing NSTI Using Git Clone
-------------------------------
First you will need to clone the Git repo to retrieve NSTI.  Make sure you are in the directory location you want the clone to be placed in.  The main folder will be called 'nsti'.

Github clones:
	* 'HTTPS clone <https://github.com/NagiosEnterprises/nsti.git>'
	* 'SSH Clone <git@github.com:NagiosEnterprises/nsti.git>'
	* 'Subversion Clone <https://github.com/NagiosEnterprises/nsti>'

Now that you have the clone go into the nsti folder,

	cd /nsti

and run,

	sh install.sh

Once you have a clone of NSTI on your local machine and run the install script there are some prerequisits that the installer will run using the yum installer.  Support for the other repos will be added in another version.  If you have another repo that does not use yum just install the following packages manually with the installer of your choice:

- mysql 
- mysql-devel
- mysql-server
- httpd

Then restart mysqld, set mysql user and password and set mysqld and apache to start on startup

	service mysqld start
	/usr/bin/mysqladmin -u root password 'nsti'
	service mysqld restart

	chkconfig mysqld on
	chkconfig httpd on

Now continues with prerequisite installation

- gcc
- wget
- make
- tar
- mod_wsgi
- python-devel

	curl https://raw.githubusercontent.com/pypa/pip/master/contrib/get-pip.py | python


If any of the packages failed to install you will see error outputs such as the following::

	'Cannot continue install until all of these prereqs are met.'

	'mod_wsgi is not installed in Apache. Please use your package manager to install it.'

	'No user `nagios`. Add a user for nagios and make sure it is in the nagcmd group.'
    'Apache must also be in nagcmd.'


Flask and Storm
----------------

.. note ::

	Both of these are inside the requirements.txt that the install script refers to so they should both be installed at this point, but just in case we will go over them and how to install them if, for some reason, they are missing.

NSTI uses Flask as a lightweight web applicatin framework and uses Werkzeug and Jinja2 for templating engines.  Flask is a great microframework that allows extentions to be added granting it access to form validation and database abstraction.  You will need Flask so install it using pip or easy_install.

Storm is a Python programming library for Object-relational mapping (ORM) between one or more SQL databases and Python objects.  This allows NSTI to make queries and demands of the datasbases to be able to populate, collect, filter the traps in the database, but also how the traps are displayed.


MySQL Configuration
--------------------

To run NSTI correctly it is important that you have your mysql set up correctly so NSTI can recieve data from the database.  The most common problem with NSTI not communicating properly with your mysql database is the root user password settings.  It is recommended that you set the password if the install script failed to do so.


The nsti.py file is located here::

	/nsti/nsti/etc/nsti.py


This is where you will set your database type, host, name, user and password.  To test if you succesfully configured your database use the runserver.py in the /nsti directory.  It will output any connection errors and you will be able to adjust settings accordingly.

Another point to be made is that you will need to verify that the database NSTI is writing to was created.  If it wasn't you will recieve similar errors to the following:

	ERROR 1049 (42000): Unknown database 'snmptt'
	Unable to add database schema. Does the snmptt exist?

This means that the installer did not create the database so you will need to run the installer again or manually add the database that will be written to, create the root user and use password 'nsti' to make sure it is working.

After you verify everything is working with the database it is recommended that you change your password and update it in the configuration files.