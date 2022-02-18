#!/usr/bin/env bash
# Simple new installer
exec >  >(tee -ia installation.log)
exec 2> >(tee -ia installation.log >&2)
HOME=/home/pi
USER=pi
branch=sqlite
if ! which git &> /dev/null;then
  sudo apt update
  sudo apt -y install git
fi
git clone -b ${branch} https://github.com/mcguirepr89/BirdNET-Pi.git ${HOME}/BirdNET-Pi 2&>1 &&
${HOME}/BirdNET-Pi/scripts/install_birdnet.sh
if [ ${PIPESTATUS[0]} -eq 0 ];then
  echo "Installation completed successfully"
  sudo reboot
else
  echo "The installation exited unsuccessfully."
  exit 1
fi
