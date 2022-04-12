#!/usr/bin/env bash
# Update BirdNET-Pi
source /etc/birdnet/birdnet.conf
trap 'exit 1' SIGINT SIGHUP
my_dir=${HOME}/BirdNET-Pi/scripts

sudo -u${USER} git -C $my_dir/BirdNET-Pi stash
sudo -u${USER} git -C $my_dir/BirdNET-Pi pull -f
sudo systemctl daemon-reload
sudo -u${USER} git -C $my_dir/BirdNET-Pi stash pop
sudo ln -sf $my_dir/* /usr/local/bin/
if ! grep PRIVACY_MODE /etc/birdnet/birdnet.conf;then
  sudo -u${USER} echo "PRIVACY_MODE=off" >> /etc/birdnet/birdnet.conf
fi
