#!/usr/bin/env bash
# Update database password
source /etc/birdnet/birdnet.conf

sudo mysql -e "
  UPDATE mysql.user 
  SET Password=PASSWORD('${DB_PWD}') 
  WHERE USER='birder' 
  AND Host='localhost';
  FLUSH PRIVILEGES";
git -C /home/pi/BirdNET-Pi checkout -f analyze.py
git -C /home/pi/BirdNET-Pi checkout -f scripts/viewdb.php 
sed -i "s/databasepassword/${DB_PWD}/g" /home/pi/BirdNET-Pi/analyze.py
sed -i "s/databasepassword/${DB_PWD}/g" /home/pi/BirdNET-Pi/scripts/viewdb.php
