#!/usr/bin/env bash
# Reconfigure the Birding-Pi
source /etc/birdnet/birdnet.conf
uninstall.sh
${HOME}/Birding-Pi/scripts/install_config.sh
sudo ${HOME}/Birding-Pi/scripts/install_services.sh
echo "Birding-Pi has now been reconfigured."
