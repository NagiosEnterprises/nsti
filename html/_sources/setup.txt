SNMP Setup
==========

Once you have gone through the installation and all the dependencies are installed without error you will want to set up SNMP traps on your devices.  This section of the documentation is designed as a low level user guide on how to set up SNMP on some devices so you can recieve traps for NSTI.  This part of the documentation assumes that you installed NSTI with no errors and can access the landing page with no error codes.

.. danger::

    Make sure that your NSTI install is accessible to be sure that all the files and database configurations have compeleted successfully.  This part of the documentation will not be of any use if you cannot access NSTI.


For a more detailed explaination check the very thorough Net-SNMP documentation and Tutorial pages::

	http://www.net-snmp.org/docs/man/

	http://www.net-snmp.org/wiki/index.php/Tutorials


Getting Started with SNMP
-------------------------

Getting SNMP setup can be daunting, but for this documentation we will go through a simple set up to be able to familiarize yourself with setting up more difficult devices.  Once doing it a few time you will be able to do this with no problem.



Setting Up Your MIB Files
*************************

MIB stands for Management Information Base and is simply a collection of information.  OIDs or Object Identifiers uniquely identify objects in a MIB 'tree.'  To be able to recieve SNMP traps you will have to configure your MIBs so that snmptt can read them with a configuration file.

To do this we are going to use a SNMP Trap Translator called SNMPTTCONVERTMIB, located here::

	http://snmptt.sourceforge.net/docs/snmpttconvertmib.shtml

In the words of the creator of this program::

.. code-block:: bash

	SNMPTTCONVERTMIB is a Perl script which will read a MIB file and convert the TRAP-TYPE (v1) or NOTIFICATION-TYPE (v2) definitions into a configuration file readable by SNMPTT.

Exactly what we need to move forward and get our traps to view in NSTI.  We will convert the MIB files for our specific device that we will choose, which will usually be done by going to a website like mibdepot::

    http://www.mibdepot.com/index.shtml


For this example I am going to use a Linux SNMPv2 MIB to monitor a CentOS VM.
