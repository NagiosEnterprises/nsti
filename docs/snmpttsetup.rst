Setting Up Watchguard Device to Report to NSTI 3.0.2, Guided Instructions:
==========================================================================

This section is a good starting place for getting different types of devices to send trap data to NSTI.  More coming in the future.

1. Configure SNMPTT
-------------------

Open /etc/snmp/snmptt.ini and change the following:

.. code-block:: bash

		unknown_trap_log_enable = 1

		description_mode = 1 

		unknown_trap_exec = /etc/snmp/traphandle.sh

		snmptt_conf_files = <<END
		/etc/snmp/watchguard.conf
		/etc/snmp/snmptt.conf
		END

Now we can create the script we refer to above to handle the MIBS we want to translate.  Here's the list we will use for this example:

* IPSEC-ISAKMP-IKE-DOI-TC
* WATCHGUARD-CLIENT-MIB
* WATCHGUARD-INFO-SYSTEM-MIB
* WATCHGUARD-IPSEC-ENDPOINT-PAIR-MIB
* WATCHGUARD-IPSEC-SA-MON-MIB-EXT
* WATCHGUARD-IPSEC-TUNNEL-MIB
* WATCHGUARD-POLICY-MIB
* WATCHGUARD-PRODUCTS-MIB
* WATCHGUARD-SMI
* WATCHGUARD-SYSTEM-CONFIG-MIB
* WATCHGUARD-SYSTEM-STATISTICS-MIB

Remember to do your research to be sure that you have all of the dependancy MIBS, some of them may not actually be in your mibs directory by deafult and will cause errors.  For our example I already added my WatchGuard dependancy MIBs to the  /usr/share/snmp/mibs  directory (some of the are in the directory by default):

* IF-MIB
* IP-MIB
* RFC1155 SMI-MIB
* RFC1213-MIB
* SNMPv2-MIB
* SNMPv2-SMI
* TCP-MIB
* UDP-MIB

Here is our custom script that will read our watchguard specific files, or you can use a wildcard to select all MIBS in the directory,

.. code-block:: bash
		
		#!/bin/bash

		for i in WATCHGUARD* IPSEC*; do
			snmpttconvertmib --in=/usr/share/snmp/mibs/$i --out=/etc/snmp/watchguard.conf --exec='/etc/snmp/traphandle.sh $r $s "$D"'
		done


.. note ::

		Here we create a new .conf file so we can differentiate between our different types of MIBs.  You have to add these files to be read by snmptt.ini like we have set in the first part of this section.  Also, the * will read EVERY mib file in the given directory so make sure that you want to execute and create an in and out of the MIB files you have stored there.  We use a glob for WATCHGUARD* and IPSEC* for this example since the names aren't all the same.


2. Make Sure SNMPTT is set to Always Start on Boot Up
-----------------------------------------------------

* chkconfig --list

Run this command to ensure snmptt exists, if  use 

* not chkconfig --add snmptt)

.. code-block:: bash

		chkconfig --level 2345 snmptt on

		service snmptt start



3. Update snmptrapd and snmptrapd.conf to handle traps and execute known OIDs
-----------------------------------------------------------------------------

a)  In /etc/init.d/snmptrapd:

.. code-block:: bash

	OPTIONS="-On -s -u /var/run/snmptrapd.pid"


b)  Open snmptrapd.conf and add:

.. code-block:: bash

	disableAuthorization yes  (use this in your initial setup to ensure everything is working.  Remove this later for security)
	traphandle default /usr/local/sbin/snmptt