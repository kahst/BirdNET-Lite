#!/usr/bin/env bash
# Second stage of update
birdnet_conf=/home/pi/BirdNET-Pi/birdnet.conf

# Stage 1 updates the birdnet.conf
sudo -upi sed -i 's/EXTRACTIONS_URL/BIRDNETPI_URL/g' 
if ! grep EXTRACTIONLOG ${birdnet_conf} &> /dev/null;then
  echo "EXTRACTIONLOG_URL=" >> ${birdnet_conf}
  echo "BIRDNETLOG_URL=" >> ${birdnet_conf}
fi
# Stage 2 updates the services
my_dir=${HOME}/BirdNET-Pi/scripts
sudo ${my_dir}/update_services.sh

# Stage 2 restarts the services
newservices=$(awk '/service/ && /systemctl/ && !/php/ {print $3}' ${my_dir}/install_services.sh | sort)

restart_newservices() {
  for i in ${newservices[@]};do
    sudo systemctl restart ${i}
  done
}

restart_newservices
