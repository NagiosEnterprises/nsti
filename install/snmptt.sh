SNMPTTINI="/etc/snmp/snmptt.ini"

sed -i'.bkp' 's/^mode[ \t]*=[ \t]*standalone/mode = daemon/g' "$SNMPTTINI"
sed -i'.bkp' 's/^dns_enable[ \t]*=[ \t]*0/dns_enable = 1/g' "$SNMPTTINI"
sed -i'.bkp' 's/^mysql_dbi_enable[ \t]*=[ \t]*0/mysql_dbi_enable = 1/g' "$SNMPTTINI"
