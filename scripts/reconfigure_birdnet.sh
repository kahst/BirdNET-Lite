#!/usr/bin/env bash
# Reconfigure the BirdNET-Lite
source /etc/birdnet/birdnet.conf
uninstall.sh
${HOME}/BirdNET-Lite/scripts/install_config.sh
sudo ${HOME}/BirdNET-Lite/scripts/install_services.sh
echo "BirdNET-Lite has now been reconfigured."
