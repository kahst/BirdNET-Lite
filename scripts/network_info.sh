#!/usr/bin/env bash
#Display network info for phpsysinfo

echo "LAN IP: $(hostname -I|cut -d' ' -f1)"
echo "Public IP: $(curl -s4 ifconfig.co)"
echo
echo "------------------------------"
echo '         $(ip a)         '
ip a
