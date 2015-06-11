#!/bin/bash

wget http://assets.nagios.com/downloads/addons/snmptt_1.4.tgz
tar -zxvf snmptt_1.4.tgz

cd /snmptt_1.4

# move binary files, add execute permissions
cp {snmptt,snmptthandler} /usr/sbin/
chmod +x /usr/sbin/snmptt
chmod +x /usr/sbin/snmptthandler

# add SNMPTT config files
cp snmptt.ini /etc/snmp/
touch /etc/snmp/snmptt.conf
mkdir -p /var/log/snmptt/

# NSTI daemon mode (you must uncomment of add authorization for the daemon
echo -e '#disableAuthorization yes\n#authCommunity    log,execute,net    public\ntraphandle default /usr/sbin/snmptthandler' >> /etc/snmp/snmptrad.conf
mkdir -p /var/spool/snmptt

# startup script
cp snmptt-init.d /etc/rc.d/init.d/snmptt
chkconfig --add snmptt
chkconfig --level 2345 snmptt on
service snmptt start
snmptrapd -On