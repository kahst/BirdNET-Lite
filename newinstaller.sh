#!/usr/bin/env bash
# Simple new installer
HOME=$HOME
USER=$USER

export HOME=$HOME
export USER=$USER

branch=newbaseos
if ! which git &> /dev/null;then
  sudo apt update
  sudo apt -y install git
fi
git clone -b $branch https://github.com/mcguirepr89/BirdNET-Pi.git ${HOME}/BirdNET-Pi &&

$HOME/BirdNET-Pi/scripts/install_birdnet.sh
if [ ${PIPESTATUS[0]} -eq 0 ];then
  echo "Installation completed successfully"
  sudo reboot
else
  echo "The installation exited unsuccessfully."
  exit 1
fi
