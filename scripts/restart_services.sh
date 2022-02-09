#!/usr/bin/env bash
# Restarts ALL services and removes ALL unprocessed audio
source /etc/birdnet/birdnet.conf
set -x
my_dir=/home/pi/BirdNET-Pi/scripts

sudo systemctl stop birdnet_recording.service
sudo rm -rf ${RECS_DIR}/$(date +%B-%Y/%d-%A)/*
services=(web_terminal.service
spectrogram_viewer.service
pushed_notifications.service
livestream.service
icecast2.service
extraction.timer
extraction.service
chart_viewer.service
birdnet_recording.service
birdnet_log.service
birdnet_server.service
birdnet_analysis.service)

sudo pkill server.py
for i in  "${services[@]}";do
sudo systemctl restart "${i}"
done
