#!/usr/bin/env bash
# Second stage of update
birdnet_conf=/home/pi/BirdNET-Pi/birdnet.conf
my_dir=${HOME}/BirdNET-Pi/scripts

# Stage 1 updates the services
sudo ${my_dir}/update_services.sh

# Stage 1.5: adding new birdnet.conf entries
if ! grep FULL_DISK ${birdnet_conf};then
 cat << EOF >> ${birdnet_conf}

## FULL_DISK can be set to configure how the system reacts to a full disk
## 0 = Remove the oldest day's worth of recordings
## 1 = Keep all data and `stop_core_services.sh`

FULL_DISK=0
EOF
fi

# Stage 2 restarts the services
newservices=($(awk '/systemctl/ && !/php/ && !/caddy/ && !/target/ {print $3}' <(sed -e 's/--now//g' ${my_dir}/update_services.sh) | sort | uniq ))
for i in ${newservices[@]};do
  sudo systemctl restart ${i}
done
sudo systemctl restart extraction.timer
