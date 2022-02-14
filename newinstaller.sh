#!/usr/bin/env bash
# Simple new installer
HOME=/home/pi
USER=pi
branch=edit
if ! which git &> /dev/null;then
  sudo apt update
  sudo apt -y install git
fi
git clone -b ${branch} https://github.com/mcguirepr89/BirdNET-Pi.git ${HOME}/BirdNET-Pi &&
cp ${HOME}/BirdNET-Pi/birdnet.conf-defaults ${HOME}/BirdNET-Pi/birdnet.conf &&
${HOME}/BirdNET-Pi/scripts/install_birdnet.sh
