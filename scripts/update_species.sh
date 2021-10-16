#!/usr/bin/env bash
# Update the species list
#set -x
trap 'rm -f "$TMPFILE"' EXIT
source /etc/birdnet/birdnet.conf
db=birds
dbuser=birder
dbpassword=${DB_PWD}

mysql -u${dbuser} -p${dbpassword} ${db} \
  -e 'SELECT Com_Name 
      FROM detections 
      GROUP BY Com_Name' |\
  tail -n+2 > ${IDFILE}
