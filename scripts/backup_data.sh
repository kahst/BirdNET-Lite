#!/usr/bin/env bash
# Tar and compress all data
source /etc/birdnet/birdnet.conf

tar -czvf /home/pi/BirdNET-Pi/BirdNET-Pi_Data_Dump_$(date +%F).tar.gz ${EXTRACTED} /home/pi/BirdNET-Pi/BirdDB.txt
