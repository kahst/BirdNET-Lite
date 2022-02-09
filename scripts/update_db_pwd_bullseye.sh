#!/usr/bin/env bash
# Update database password
source /etc/birdnet/birdnet.conf

mysql -e "
  SET PASSWORD FOR 'birder'@'localhost' = PASSWORD('${DB_PWD}');
  FLUSH PRIVILEGES";
sudo -u ${USER} git -C /home/pi/BirdNET-Pi checkout -f scripts/server.py
sudo -u ${USER} git -C /home/pi/BirdNET-Pi checkout -f scripts/viewdb.php 
sudo -u ${USER} sed -i "s/databasepassword/${DB_PWD}/g" /home/pi/BirdNET-Pi/scripts/server.py
sed -i "s/mysqli.default_host =.*/mysqli.default_host = localhost/g" /etc/php/7.4/fpm/php.ini
sed -i "s/mysqli.default_user =.*/mysqli.default_user = birder/g" /etc/php/7.4/fpm/php.ini
sed -i "s/mysqli.default_pw =.*/mysqli.default_pw = ${DB_PWD}/g" /etc/php/7.4/fpm/php.ini
