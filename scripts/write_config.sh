#!/usr/bin/bash
# Writes variables to config file
birdnetpi_dir=/home/pi/BirdNET-Pi
birders_conf=${birdnetpi_dir}/Birders_Guide_Installer_Configuration.txt
sed -i s/'^LATITUDE=$'/"LATITUDE=${new_lat}"/g ${birders_conf}
sed -i s/'^LONGITUDE=$'/"LONGITUDE=${new_lon}"/g ${birders_conf}
sed -i s/'^CADDY_PWD=$'/"CADDY_PWD=${caddy_pwd}"/g ${birders_conf}
sed -i s/'^ICE_PWD=$'/"ICE_PWD=${ice_pwd}"/g ${birders_conf}
sed -i s/'^DB_PWD=$'/"DB_PWD=${db_pwd}"/g ${birders_conf}
sed -i s/'^DB_ROOT_PWD=$'/"DB_ROOT_PWD=${db_root_pwd}"/g ${birders_conf}
