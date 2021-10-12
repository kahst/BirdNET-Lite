#!/usr/bin/env bash
# Restarts ALL services and removes ALL unprocessed audio
source /etc/birdnet/birdnet.conf

sudo systemctl stop birdnet_recording.service
sudo rm -rf ${RECS_DIR}/$(date +%B-%Y/%d-%A)/*
sudo systemctl start birdnet_recording.service

SERVICES=(avahi-alias@birdlog.local.service
avahi-alias@birdnetpi.local.service
avahi-alias@birdstats.local.service
avahi-alias@extractionlog.local.service
birdnet_analysis.service
birdnet_log.service
birdstats.service
extraction.timer
extractionlog.service
livestream.service)

for i in  "${SERVICES[@]}";do
  sudo systemctl restart ${i}
done

