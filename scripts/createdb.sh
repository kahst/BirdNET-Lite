#!/usr/bin/env bash
# This script performs the mysql_secure_installation
# Creates the birds database
# Creates the detections table
# Creates the birder user and grants them appropriate
# permissions
# If using this script to re-initialize (DROP then CREATE)
# the DB, be sure to run this as root or with sudo
source /etc/birdnet/birdnet.conf
mysql_secure_installation << EOF

y
${DB_ROOT_PWD}
${DB_ROOT_PWD}
y
y
y
EOF

mysql << EOF
drop database birds;
CREATE DATABASE IF NOT EXISTS birds;

USE birds;

CREATE TABLE IF NOT EXISTS detections (
  Date DATE,
  Time TIME,
  Sci_Name VARCHAR(100) NOT NULL,
  Com_Name VARCHAR(100) NOT NULL,
  Confidence FLOAT,
  Lat FLOAT,
  Lon FLOAT,
  Cutoff FLOAT,
  Week INT,
  Sens FLOAT,
  Overlap FLOAT);
GRANT ALL ON birds.* TO 'birder'@'localhost' IDENTIFIED BY '${DB_PWD}' WITH GRANT OPTION;

exit
EOF
sed -i "s/databasepassword/${DB_PWD}/g" /home/pi/Birding-Pi/analyze.py
