#!/usr/bin/env bash
# Update BirdNET-Pi
source /etc/birdnet/birdnet.conf
trap 'exit 1' SIGINT SIGHUP
USER=pi
HOME=/home/pi
my_dir=${HOME}/BirdNET-Pi/scripts

sudo -u${USER} git -C /home/pi/BirdNET-Pi stash
sudo -u${USER} git -C /home/pi/BirdNET-Pi pull -f
sudo systemctl daemon-reload
sudo -u${USER} git -C /home/pi/BirdNET-Pi stash pop
sudo ln -sf $my_dir/* /usr/local/bin/
