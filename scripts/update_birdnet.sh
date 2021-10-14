#!/usr/bin/env bash
# Update BirdNET-Pi
source /etc/birdnet/birdnet.conf
trap 'rm -f ${TMPFILE}' EXIT
SCRIPTS=(birdnet_analysis.sh
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


SERVICES=(avahi-alias@.service
avahi-alias@birdlog.local.service
avahi-alias@birdnetpi.local.service
avahi-alias@birdstats.local.service
avahi-alias@extractionlog.local.service
avahi-alias@birdterminal.local.service
birdnet_analysis.d
birdnet_analysis.service
birdnet_log.service
birdnet_recording.d
birdnet_recording.service
birdstats.service
birdterminal.service
caddy.d
caddy.service
edit_birdnet_conf.service
extraction_log.service
extraction.d
extraction.service
extraction.timer
livestream.service
spectrogram_viewer.service
${SYSTEMD_MOUNT})

remove_services() {
  for i in "${SERVICES[@]}"; do
    if [ -L /etc/systemd/system/multi-user.target.wants/"${i}" ];then
      sudo systemctl kill "${i}"
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
  TMPFILE=$(mktemp)
  crontab -l | sed -e '/birdnet/,+1d' > "${TMPFILE}"
  crontab "${TMPFILE}"
}

remove_icecast() {
  if [ -f /etc/init.d/icecast2 ];then
    sudo /etc/init.d/icecast2 stop
    sudo systemctl disable --now icecast2
  fi
}

remove_scripts() {
  for i in "${SCRIPTS[@]}";do
    if [ -L "/usr/local/bin/${i}" ];then
      sudo rm -v "/usr/local/bin/${i}"
    fi
  done
}

echo "Updating BirdNET"
remove_services | tee -a /tmp/birdnetupdate.log
remove_scripts | tee -a /tmp/birdnetupdate.log
cd ${HOME}/BirdNET-Pi || exit 1
git pull || exit 1
USER=${USER} sudo ./scripts/update_services.sh | tee -a /tmp/birdnetupdate.log

echo "Restarting services"
for i in "${SERVICES[@]}";do
  if [ -f /etc/systemd/system/"${i}" ];then
    sudo systemctl restart ${i} | tee -a /tmp/birdnetupdate.log
  fi
done
echo "Update Complete"
