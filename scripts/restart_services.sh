#!/usr/bin/env bash
# Restarts ALL services and removes ALL unprocessed audio
source /etc/birdnet/birdnet.conf
set -x
my_dir=$HOME/BirdNET-Pi/scripts

sudo systemctl stop birdnet_server.service
sudo pkill server.py
sudo systemctl stop birdnet_recording.service
services=(web_terminal.service
spectrogram_viewer.service
pushed_notifications.service
livestream.service
icecast2.service
extraction.service
chart_viewer.service
birdnet_recording.service
birdnet_log.service)

for i in  "${services[@]}";do
sudo systemctl restart "${i}"
done
until grep 5050 <(netstat -tulpn 2>&1);do
sudo systemctl restart birdnet_server.service
sleep 30
done
sudo systemctl restart birdnet_analysis.service
