#!/usr/bin/env bash
# Restart services
source /etc/birdnet/birdnet.conf

sudo systemctl stop birdnet_recording.service
sudo rm -rf ${RECS_DIR}/$(date +%B-%Y/%d-%A)/*
sudo systemctl start birdnet_recording.service
sudo systemctl restart extraction.timer
sudo systemctl restart birdnet_analysis.service
