#!/usr/bin/env bash
# Uninstall script to remove everything
# set -x # Uncomment to debug
trap 'rm -f ${TMPFILE}' EXIT
source /etc/birdnet/birdnet.conf &> /dev/null
SCRIPTS=(/usr/local/bin/birdnet_analysis.sh
/usr/local/bin/birdnet_recording.sh
/usr/local/bin/birdnet_stats.sh
/usr/local/bin/cleanup.sh
/usr/local/bin/clear_all_data.sh
/usr/local/bin/disk_usage.sh
/usr/local/bin/dump_logs.sh
/usr/local/bin/extract_new_birdsounds.sh
/usr/local/bin/install_birdnet.sh
/usr/local/bin/install_config.sh
/usr/local/bin/install_services.sh
/usr/local/bin/install_tmux_services.sh
/usr/local/bin/install_zram_service.sh
/usr/local/bin/livestream.sh
/usr/local/bin/pretty_date.sh
/usr/local/bin/reconfigure_birdnet.sh
/usr/local/bin/restart_birdnet.sh
/usr/local/bin/species_notifier.sh
/usr/local/bin/uninstall.sh
/usr/local/bin/update_species.sh
${HOME}/.gotty)


SERVICES=(avahi-alias@birdlog.local.service
avahi-alias@birdnetsystem.local.service
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
extraction_log.service
extraction.d
extraction.service
extraction.timer
livestream.service
${SYSTEMD_MOUNT})

remove_services() {
  for i in "${SERVICES[@]}"; do
    if [ -L /etc/systemd/system/multi-user.target.wants/"${i}" ];then
      sudo systemctl disable --now "${i}"
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
    if [ -L "${i}" ];then
      sudo rm -v "${i}"
    fi
  done
}

remove_services
remove_scripts
if [ -d /etc/birdnet ];then sudo rm -drf /etc/birdnet;fi
if [ -f ${HOME}/BirdNET-Lite/birdnet.conf ];then sudo rm -f ${HOME}/BirdNET-Lite/birdnet.conf;fi
echo "Uninstall finished. Remove this directory with 'rm -drfv' to finish."
