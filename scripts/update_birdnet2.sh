#!/usr/bin/env bash
# Second stage of update


# Stage 1 updates the services
my_dir=${HOME}/BirdNET-Pi/scripts
sudo ${my_dir}/update_services.sh

# Stage 2 restarts the services
services=(avahi-alias@birdnetpi.local.service
birdnet_analysis.service
birdnet_log.service
birdnet_recording.service
edit_birdnet_conf.service
extraction_log.service
extraction.service
extraction.timer
livestream.service
pushed_notifications.service
spectrogram_viewer.service)

restart_services() {
  for i in ${services[@]};do
    sudo systemctl restart ${i}
  done
}

restart_services
