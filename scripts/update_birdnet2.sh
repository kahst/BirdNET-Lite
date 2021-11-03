#!/usr/bin/env bash
# Second stage of update
birdnet_conf=/home/pi/BirdNET-Pi/birdnet.conf
my_dir=${HOME}/BirdNET-Pi/scripts

# Stage 1 updates the services
sudo ${my_dir}/update_services.sh

# Stage 2 restarts the services
newservices=$(awk '/service/ && /systemctl/ && !/php/ {print $3}' ${my_dir}/install_services.sh | sort)
for i in ${newservices[@]};do
  sudo systemctl restart ${i}
done
sudo systemctl restart extraction.timer
