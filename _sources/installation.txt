Installation
============

NSTI performs well with and is designed to run on any Linux distribution.


Installing NSTI Using Git Clone
-------------------------------
First you will need to clone the Git repo to retrieve NSTI.  Make sure you are in the directory location you want the clone to be placed in.

Github clones:
	* 'HTTPS clone <https://github.com/NagiosEnterprises/nsti.git>'
	* 'SSH Clone <git@github.com:NagiosEnterprises/nsti.git>'
	* 'Subversion Clone <https://github.com/NagiosEnterprises/nsti>'

Once you have a clone of NSTI on your local machine there are 6 baseline prerequisits to install first:

- mysql 
- httpd
- gcc
- wget
- make
- tar

To verify and check that these are installed use the install.sh script in the main NSTI directory::
	./install.sh

If successfull you should see output like the following::

	Checking installer prereqs...
	-----------------------------
	/usr/bin/mysql
	/usr/sbin/httpd
	/usr/bin/gcc
	/usr/bin/wget
	/usr/bin/make
	/bin/tar
	----------------------------
	INSTALLER PREREQS MET
	----------------------------
	
	Making sure WSGI is installed in Apache...
	------------------------------------------
	 wsgi_module (shared)
	----------------------------
	MOD_WSGI INSTALLED
	----------------------------

If a message from the install script mentions you do not have wsgi installed, install mod_wsgi using yum or whatever your repo uses as an installer.


Flask and Storm
----------------

.. note ::

	Both of these are inside the requirements.txt that the install script refers to so they should both be installed at this point, but just in case we will go over them and how to install them if, for some reason, they are missing.

NSTI uses Flask as a lightweight web applicatin framework and uses Werkzeug and Jinja2 for templating engines.  Flask is a great microframework that allows extentions to be added granting it access to form validation and database abstration.  You will need Flask so install it using pip or easy_install.

Storm is a Python programming library for Object-relational mapping (ORM) between one or more SQL databases and Python objects.  This allows NSTI to make queries and demands off of the datasbases to be able to populate, collect, filter, etc. the traps in the database, but also how the traps are displayed or related.  If you see any errors about Storm missing (you definitely will if it is missing) then simply install


MySQL Configuration
--------------------

To run NSTI correctly it is important that you have your mysql set up correctly so NSTI can recieve data from the database.  The most common problem with NSTI not communicating properly with your mysql database is the root user password settings.  It is recommended that you set the password.

Just like Flask and Storm, MySQL is in the requirements.txt file that the install script uses.  If it is missing be sure to install these packages:

	mysql-server
	python-storm-mysql


The nsti.py file is located here::

	/nsti/nsti/etc/nsti.py


This is where you will set your database type, host, name, user and password.  To test if you succesfully configured your database use the runserver.py in the /nsti directory.  It will output any connection errors and you will be able to adjust settings accordingly.

Another point to be made is that you will need to verify that the database NSTI is writing to was created.  If it wasn't you will recieve similar errors to the following:

	ERROR 1049 (42000): Unknown database 'snmptt'
	Unable to add database schema. Does the snmptt exist?

This means that the installer did not create the database so you will need to run the installer again or manually add the database that will be written to.
