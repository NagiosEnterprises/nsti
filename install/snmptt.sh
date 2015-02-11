SNMPTTINI="/etc/snmp/snmptt.ini"
SNMPTRAPD="/etc/snmp/snmptrapd.conf"

sed -i'.bkp' 's/^mode[ \t]*=[ \t]*standalone/mode = daemon/g' "$SNMPTTINI"
sed -i'.bkp' 's/^dns_enable[ \t]*=[ \t]*0/dns_enable = 1/g' "$SNMPTTINI"
sed -i'.bkp' 's/^mysql_dbi_enable[ \t]*=[ \t]*0/mysql_dbi_enable = 1/g' "$SNMPTTINI"
sed -i'.bkp' 's/^net_snmp_perl_enable[ \t]*=[ \t]*0/net_snmp_perl_enable = 1/g' "$SNMPTTINI"

# set snmptrapd authCommunity and traphandle
sed -i'.bkp' '$a\ #disableAuthorization yes\authCommunity    log,execute,net    public\traphandle default /usr/sbin/snmptthandler\' "$SNMPTRAPD"