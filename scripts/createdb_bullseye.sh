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
n
y
y
y
y
EOF

mysql << EOF
DROP DATABASE IF EXISTS birds;
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
FLUSH PRIVILEGES;
exit
EOF
sudo -u${USER} sed -i "s/databasepassword/${DB_PWD}/g" /home/pi/BirdNET-Pi/analyze.py
sed -i "s/mysqli.default_host =.*/mysqli.default_host = localhost/g" /etc/php/7.4/fpm/php.ini
sed -i "s/mysqli.default_user =.*/mysqli.default_user = birder/g" /etc/php/7.4/fpm/php.ini
sed -i "s/mysqli.default_pw =.*/mysqli.default_pw = ${DB_PWD}/g" /etc/php/7.4/fpm/php.ini

