Installation
===============

NSTI performs well with and is designed to run on any Linux distribution.


Installing NSTI Using Git Clones
--------------------------------
First you will need to clone the Git repo to retrieve NSTI.  Make sure you are in the directory location you want the clone to be placed in.

* Github clones:
	* 'HTTPS clone <https://github.com/NagiosEnterprises/nsti.git>'
	* 'SSH Clone <git@github.com:NagiosEnterprises/nsti.git>'
	* 'Subversion Clone <https://github.com/NagiosEnterprises/nsti>'

Once you have a clone of NSTI on your local machine there are 6 baseline prerequisits to install first:
	* mysql 
	* httpd
	* gcc
	* wget
	* make
	* tar

To verify and check that these are installed use the install.sh script in the main NSTI directory::
	./install.sh

If successfull you should see output like the following:

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

If a message from the install script mentions you do not have wsgi installed, use yum to install it::
	yum install mod_wsgi
