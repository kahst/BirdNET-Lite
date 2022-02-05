#!/usr/bin/env bash
# Restarts ALL services and removes ALL unprocessed audio
source /etc/birdnet/birdnet.conf
my_dir=/home/pi/BirdNET-Pi/scripts

sudo systemctl stop birdnet_recording.service
sudo rm -rf ${RECS_DIR}/$(date +%B-%Y/%d-%A)/*
services=($(awk '/systemctl/ && !/php/ && !/caddy/ && !/target/ && !/avahi/ {print $3}' <(sed -e 's/--now//g' ${my_dir}/update_services.sh) | sort | uniq ))

for i in  "${services[@]}";do
sudo systemctl restart "${i}"
done
sudo systemctl restart extraction.timer
sudo systemctl start birdnet_recording.service

