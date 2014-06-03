iptables -A INPUT -p tcp --dport 80 -j ACCEPT
iptables-save
