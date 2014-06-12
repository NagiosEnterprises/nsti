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

First thing we are going to need to do is set up an SNMP handler.  For this documentation we are going to use the Multi Router Traffic Grapher (MRTG) made by Tobias Oetiker to do all the heavy lifting for us::

	http://oss.oetiker.ch/mrtg/pub/mrtg-2.17.4.tar.gz

Here is the Linux setup guide for MRTG (there are also Windows tutorials)::

	http://oss.oetiker.ch/mrtg/doc/mrtg-unix-guide.en.html


Setting Up Your MIB Files
-------------------------

MIB stands for Management Information Base and is simply a collection of information.  OIDs or Object Identifiers uniquely identify objects in a MIB 'tree.'  To be able to recieve SNMP traps you will have to configure your MIBs so that Net-SNMP, or another similar program, can read them with a configuration file.

To do this we are going to use a SNMP Trap Translator called SNMPTTCONVERTMIB, located here::

	http://snmptt.sourceforge.net/docs/snmpttconvertmib.shtml

In the words of the creator of this program::

.. code-block:: bash

	SNMPTTCONVERTMIB is a Perl script which will read a MIB file and convert the TRAP-TYPE (v1) or NOTIFICATION-TYPE (v2) definitions into a configuration file readable by SNMPTT.

Exactly what we need to move forward and get our traps to view in NSTI.

