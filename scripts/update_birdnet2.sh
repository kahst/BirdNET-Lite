#!/usr/bin/env bash
# Second stage of update
USER=pi
birdnet_conf=/home/pi/BirdNET-Pi/birdnet.conf
my_dir=${HOME}/BirdNET-Pi/scripts

# Stage 1 updates the services
sudo ${my_dir}/update_services.sh

# Stage 1.5: adding new birdnet.conf entries
if ! grep FULL_DISK ${birdnet_conf} &> /dev/null;then
 cat << EOF >> ${birdnet_conf}

## FULL_DISK can be set to configure how the system reacts to a full disk
## purge = Remove the oldest day's worth of recordings
## keep = Keep all data and `stop_core_services.sh`

FULL_DISK=purge
EOF
fi

sudo -u${USER} sed -i 's/EXTRACTIONLOG_URL/WEBTERMINAL_URL/g' ${birdnetconf}

# Replace Backup labels.txt
sudo -u${USER} cp -f ~/BirdNET-Pi/model/labels.txt.bak ~/BirdNET-Pi/model/labels.txt

# Stage 2 restarts the services
sudo systemctl daemon-reload
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
birdnet_log.service)

for i in  "${services[@]}";do
sudo systemctl restart "${i}"
done

