#!/usr/bin/env bash
# Update BirdNET-Pi
source /etc/birdnet/birdnet.conf
trap 'exit 1' SIGINT SIGHUP
USER=$(awk -F: '/1000/ {print $1}' /etc/passwd)
HOME=$(awk -F: '/1000/ {print $6}' /etc/passwd)
my_dir=$HOME/BirdNET-Pi/scripts

sudo -u${USER} git -C $my_dir stash
sudo -u${USER} git -C $my_dir pull -f
sudo systemctl daemon-reload
sudo -u${USER} git -C $my_dir stash pop
sudo ln -sf $my_dir/* /usr/local/bin/
sudo $my_dir/update_birdnet_snippets.sh
