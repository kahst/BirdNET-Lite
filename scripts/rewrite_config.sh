#!/usr/bin/bash
# Writes variables to config file
set -x
birdnetpi_dir=/home/pi/BirdNET-Pi
birdnet_conf=${birdnetpi_dir}/birdnet.conf

if ! [ -z ${new_lat} ];then
sed -i s/'^LATITUDE=.*'/"LATITUDE=${new_lat}"/g ${birdnet_conf}
fi

if ! [ -z ${new_lon} ];then
sed -i s/'^LONGITUDE=.*'/"LONGITUDE=${new_lon}"/g ${birdnet_conf}
fi

if ! [ -z ${caddy_pwd} ];then
sed -i s/'^CADDY_PWD=.*'/"CADDY_PWD=${caddy_pwd}"/g ${birdnet_conf}
hash_pwd=$(caddy hash-password -plaintext ${caddy_pwd})
sudo sed -i s/'birdnet\ .*'/"birdnet ${hash_pwd}"/g /etc/caddy/Caddyfile
sudo systemctl reload caddy
fi

if ! [ -z ${db_pwd} ];then
sed -i s/'^DB_PWD=.*'/"DB_PWD=${db_pwd}"/g ${birdnet_conf}
${birdnetpi_dir}/scripts/update_db_pwd_bullseye.sh
fi

if ! [ -z ${birdweather_id} ];then
sed -i s/'^BIRDWEATHER_ID=.*'/"BIRDWEATHER_ID=${birdweather_id}"/g ${birdnet_conf}
fi

if ! [ -z ${birdnetpi_url} ];then
sed -i s/'^BIRDNETPI_URL=.*'/"BIRDNETPI_URL=${birdnetpi_url/\/\//\\\/\\\/}"/g ${birdnet_conf}
sed -i s/'^EXTRACTIONLOG_URL=.*'/"EXTRACTIONLOG_URL=${extractionlog_url/\/\//\\\/\\\/}"/g ${birdnet_conf}
sed -i s/'^BIRDNETLOG_URL=.*'/"BIRDNETLOG_URL=${birdnetlog_url/\/\//\\\/\\\/}"/g ${birdnet_conf}
fi

if ! [ -z ${new_sensitivity} ];then
sed -i s/'^SENSITIVITY=.*'/"SENSITIVITY=${new_sensitivity}"/g ${birdnet_conf}
fi

if ! [ -z ${new_confidence} ];then
sed -i s/'^CONFIDENCE=.*'/"CONFIDENCE=${new_confidence}"/g ${birdnet_conf}
fi
