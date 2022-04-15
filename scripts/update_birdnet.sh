#!/usr/bin/env bash
# Update BirdNET-Pi
source /etc/birdnet/birdnet.conf
trap 'exit 1' SIGINT SIGHUP
my_dir=$(realpath $(dirname $0))

sudo -u${USER} git -C $my_dir stash
sudo -u${USER} git -C $my_dir pull -f
sudo systemctl daemon-reload
sudo -u${USER} git -C $my_dir stash pop
sudo ln -sf $my_dir/* /usr/local/bin/
if ! grep python3 <(head -n1 $my_dir/analyze.py);then
  echo "Ensure all python scripts use the virtual environment"
  sed -si "1 i\\#\!$(realpath $(dirname $my_dir))/BirdNET-Pi/birdnet/bin/python3" $my_dir/*.py
if ! grep PRIVACY_MODE /etc/birdnet/birdnet.conf;then
  sudo -u${USER} echo "PRIVACY_MODE=off" >> /etc/birdnet/birdnet.conf
fi
