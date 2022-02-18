#!/usr/bin/env bash
# Simple new installer
HOME=/home/pi
USER=pi
branch=sqlite
if ! which git &> /dev/null;then
  sudo apt update
  sudo apt -y install git
fi
git clone -b ${branch} https://github.com/mcguirepr89/BirdNET-Pi.git ${HOME}/BirdNET-Pi &&
${HOME}/BirdNET-Pi/scripts/install_birdnet.sh | tee -a installation.log 2>&1
if [ ${PIPESTATUS[0]} -eq 0 ];then
  echo "Installation completed successfully" >> installation.log
  sudo reboot
else
  echo "The installation exited unsuccessfully." >> installation.log
  exit 1
fi
