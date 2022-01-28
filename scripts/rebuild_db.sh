#!/usr/bin/env bash
# Rebuild DB from BirdDB.txt
source /etc/birdnet/birdnet.conf
BIRDNET_DIR=/home/pi/BirdNET-Pi
sudo ${BIRDNET_DIR}/scripts/createdb_bullseye.sh
sudo mysql birds -e "
  LOAD DATA LOCAL INFILE '/home/pi/BirdNET-Pi/BirdDB.txt'
  INTO TABLE detections
  FIELDS TERMINATED BY ';'"
