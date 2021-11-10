#!/usr/bin/bash
# Writes variables to config file
birdnetpi_dir=/home/pi/BirdNET-Pi
birders_conf=${birdnetpi_dir}/Birders_Guide_Installer_Configuration.txt
sed -i s/'^LATITUDE=$'/"LATITUDE=${new_lat}"/g ${birders_conf}
sed -i s/'^LONGITUDE=$'/"LONGITUDE=${new_lon}"/g ${birders_conf}
sed -i s/'^CADDY_PWD=$'/"CADDY_PWD=${caddy_pwd}"/g ${birders_conf}
sed -i s/'^ICE_PWD=$'/"ICE_PWD=${ice_pwd}"/g ${birders_conf}
sed -i s/'^DB_PWD=$'/"DB_PWD=${db_pwd}"/g ${birders_conf}
sed -i s/'^BIRDNETPI_URL=$'/"BIRDNETPI_URL=${birdnetpi_url/\/\//\\\/\\\/}"/g ${birders_conf}
sed -i s/'^EXTRACTIONLOG_URL=$'/"EXTRACTIONLOG_URL=${extractionlog_url/\/\//\\\/\\\/}"/g ${birders_conf}
sed -i s/'^BIRDNETLOG_URL=$'/"BIRDNETLOG_URL=${birdnetlog_url/\/\//\\\/\\\/}"/g ${birders_conf}
