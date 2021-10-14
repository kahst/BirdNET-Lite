#!/usr/bin/env bash
# Reconfigure the BirdNET-Pi
source /etc/birdnet/birdnet.conf
uninstall.sh
${HOME}/BirdNET-Pi/scripts/install_config.sh
USER=${USER} HOME=${HOME} sudo ${HOME}/BirdNET-Pi/scripts/install_services.sh
echo "BirdNET-Pi has now been reconfigured."
