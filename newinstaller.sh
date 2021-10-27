#!/usr/bin/env bash
# Simple new installer
HOME=/home/pi
USER=pi
branch=newinstaller
sudo apt update
if ! which git &> /dev/null;then
  sudo apt -y install git
fi
git clone --depth 1 -b ${branch} https://github.com/mcguirepr89/BirdNET-Pi.git ${HOME}/BirdNET-Pi
echo 'Now, run the following script

    sudo -E ${HOME}/BirdNET-Pi/scripts/birdnet-pi-config
    
'
