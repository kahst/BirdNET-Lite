#!/usr/bin/env bash
# Update BirdNET-Pi
source /etc/birdnet/birdnet.conf
trap 'rm -f ${tmpfile}' EXIT
trap 'exit 1' SIGINT SIGHUP
USER=pi
HOME=/home/pi
my_dir=${HOME}/BirdNET-Pi/scripts
tmpfile=$(mktemp)

scripts=(birdnet_analysis.sh
birdnet_recording.sh
birdnet_stats.sh
cleanup.sh
clear_all_data.php 
clear_all_data.sh
createdb.sh
disk_usage.sh
dump_logs.sh
edit_birdnet.conf.php
edit_birdnet.conf.sh
extract_new_birdsounds.sh
install_birdnet.sh
install_config.sh
install_services.sh
install_tmux_services.sh
install_zram_service.sh
livestream.sh
pretty_date.sh
reboot_system.php
reboot_system.sh
reconfigure_birdnet.sh
restart_birdnet_analysis.php
restart_birdnet_analysis.sh
restart_birdnet_recording.php
restart_birdnet_recording.sh
restart_caddy.php
restart_caddy.sh
restart_extraction.php
restart_extraction.sh
restart_services.php
restart_services.sh
shutdown_system.php
shutdown_system.sh
species_notifier.sh
spectrogram.php
spectrogram.sh
tmux
uninstall.sh
update_species.sh
${HOME}/.gotty)


# Change this to sourcing from current uninstall.sh
# Create a pre-update services array for disabling
# Create a post-update services array for restarting
services=(avahi-alias@birdlog.local.service
avahi-alias@birdnetpi.local.service
avahi-alias@birdstats.local.service
avahi-alias@extractionlog.local.service
avahi-alias@birdterminal.local.service
birdnet_analysis.service
birdnet_log.service
birdnet_recording.service
birdstats.service
birdterminal.service
edit_birdnet_conf.service
extraction_log.service
extraction.service
extraction.timer
livestream.service
spectrogram_viewer.service)

remove_services() {
  for i in "${services[@]}"; do
    if [ -L /etc/systemd/system/multi-user.target.wants/"${i}" ];then
      sudo systemctl kill "${i}"
      sudo systemctl disable "${i}"
    fi
    if [ -f /etc/systemd/system/"${i}" ];then
      sudo rm /etc/systemd/system/"${i}"
    fi
    if [ -d /etc/systemd/system/"${i}" ];then
      sudo rm -drf /etc/systemd/system/"${i}"
    fi
  done
  remove_icecast
  remove_crons
}

remove_crons() {
  crontab -u${USER} -l | sed -e '/birdnet/,+1d' > "${tmpfile}"
  crontab -u${USER} "${tmpfile}"
}

remove_icecast() {
  if [ -f /etc/init.d/icecast2 ];then
    sudo /etc/init.d/icecast2 stop
    sudo systemctl disable --now icecast2
  fi
}

remove_scripts() {
  for i in "${scripts[@]}";do
    if [ -L "/usr/local/bin/${i}" ];then
      sudo rm -v "/usr/local/bin/${i}"
    fi
  done
}

restart_services() {
  for i in ${services[@]};do
    sudo systemctl restart ${i}
  done
}

# Stage 1 removes old stuff
remove_services
remove_scripts

# Stage 2 does a git pull to fetch new things
sudo -u${USER} git -C ${HOME}/BirdNET-Pi pull || exit 1

# Stage 3 updates the services
sudo ${my_dir}/update_services.sh

# Stage 4 restarts the services
services=(avahi-alias@birdnetpi.local.service
birdnet_analysis.service
birdnet_log.service
birdnet_recording.service
edit_birdnet_conf.service
extraction_log.service
extraction.service
extraction.timer
livestream.service
spectrogram_viewer.service)

restart_services

