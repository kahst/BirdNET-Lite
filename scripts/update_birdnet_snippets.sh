#!/usr/bin/env bash
# Update BirdNET-Pi
source /etc/birdnet/birdnet.conf
trap 'exit 1' SIGINT SIGHUP
USER=$(awk -F: '/1000/ {print $1}' /etc/passwd)
HOME=$(awk -F: '/1000/ {print $6}' /etc/passwd)
my_dir=$HOME/BirdNET-Pi/scripts
if ! grep python3 <(head -n1 $my_dir/analyze.py);then
  echo "Ensure all python scripts use the virtual environment"
  sed -si "1 i\\#\!$HOME/BirdNET-Pi/birdnet/bin/python3" $my_dir/*.py
fi
if ! grep PRIVACY_MODE /etc/birdnet/birdnet.conf;then
  sudo -u${USER} echo "PRIVACY_MODE=off" >> /etc/birdnet/birdnet.conf
fi
if ! which lsof &>/dev/null;then
  sudo apt update && sudo apt -y install lsof
fi
