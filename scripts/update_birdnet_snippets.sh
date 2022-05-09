#!/usr/bin/env bash
# Update BirdNET-Pi
source /etc/birdnet/birdnet.conf
trap 'exit 1' SIGINT SIGHUP
USER=$(awk -F: '/1000/ {print $1}' /etc/passwd)
HOME=$(awk -F: '/1000/ {print $6}' /etc/passwd)
my_dir=$HOME/BirdNET-Pi/scripts
if ! grep python3 <(head -n1 $my_dir/analyze.py) &>/dev/null;then
  echo "Ensure all python scripts use the virtual environment"
  sudo -u$USER sed -si "1 i\\#\!$HOME/BirdNET-Pi/birdnet/bin/python3" $my_dir/*.py
fi
if ! grep PRIVACY_THRESHOLD /etc/birdnet/birdnet.conf &>/dev/null;then
  sudo -u$USER echo "PRIVACY_THRESHOLD=0" >> /etc/birdnet/birdnet.conf
fi
if grep privacy ~/BirdNET-Pi/templates/birdnet_server.service &>/dev/null;then
  sudo -E sed -i 's/privacy_server.py/server.py/g' \
    ~/BirdNET-Pi/templates/birdnet_server.service
  sudo systemctl daemon-reload
  restart_services.sh
fi
if ! which lsof &>/dev/null;then
  sudo apt update && sudo apt -y install lsof
fi
