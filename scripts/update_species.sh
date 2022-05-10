#!/usr/bin/env bash
# Update the species list
#set -x
source /etc/birdnet/birdnet.conf
if [ -f $HOME/BirdNET-Pi/scripts/birds.db ];then
sqlite3 $HOME/BirdNET-Pi/scripts/birds.db "SELECT DISTINCT(Com_Name) FROM detections" | sort >  ${IDFILE}
fi
