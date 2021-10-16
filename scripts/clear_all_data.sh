#!/usr/bin/env bash
# This script removes all data that has been collected. It is tantamount to
# starting all data-collection from scratch. Only run this if you are sure
# you are okay will losing all the data that you've collected and processed
# so far.
source /etc/birdnet/birdnet.conf
echo "Stopping services"
sudo systemctl stop birdnet_recording.service
echo "Removing all data . . . "
sudo rm -drf "${RECS_DIR}"
sudo rm -f "${IDFILE}"
echo "Recreating necessary directories"
[ -d ${RECS_DIR} ] || sudo -u ${BIRDNET_USER} mkdir -p ${RECS_DIR}
[ -d ${EXTRACTED} ] || sudo -u ${BIRDNET_USER} mkdir -p ${EXTRACTED}
[ -d ${EXTRACTED}/By_Date ] || sudo -u ${BIRDNET_USER} mkdir -p ${EXTRACTED}/By_Date
[ -d ${EXTRACTED}/By_Common_Name ] || sudo -u ${BIRDNET_USER} mkdir -p ${EXTRACTED}/By_Common_Name
[ -d ${EXTRACTED}/By_Scientific_Name ] || sudo -u ${BIRDNET_USER} mkdir -p ${EXTRACTED}/By_Scientific_Name
sudo -u ${BIRDNET_USER} ln -s /home/pi/BirdNET-Pi/templates/index.html ${EXTRACTED}
[ -d ${PROCESSED} ] || sudo -u ${BIRDNET_USER} mkdir -p ${PROCESSED}
[ -L ${EXTRACTED}/scripts ] || sudo -u ${BIRDNET_USER} ln -s /home/pi/BirdNET-Pi/scripts ${EXTRACTED}/
[ -L ${EXTRACTED}/spectrogram.php ] || sudo -u ${BIRDNET_USER} ln -s /home/pi/BirdNET-Pi/scripts/spectrogram.php ${EXTRACTED}
[ -L ${EXTRACTED}/spectrogram.sh ] || sudo -u ${BIRDNET_USER} ln -s /home/pi/BirdNET-Pi/scripts/spectrogram.sh ${EXTRACTED}
[ -L ${EXTRACTED}/viewdb.php ] || sudo -u ${BIRDNET_USER} ln -s /home/pi/BirdNET-Pi/scripts/viewdb.php ${EXTRACTED}
sudo -u ${USER} ln -fs ${HOME}/phpsysinfo ${EXTRACTED}
[ -L ${EXTRACTED}/phpsysinfo.ini ] || sudo -u ${USER} cp ${HOME}/phpsysinfo/phpsysinfo.ini.new ${HOME}/phpsysinfo/phpsysinfo.ini


sudo -u ${BIRDNET_USER} cp ~/BirdNET-Pi/templates/index.html ${EXTRACTED}/
echo "Dropping and re-creating database"
sudo /home/pi/BirdNET-Pi/scripts/createdb.sh
echo "Restarting services"
sudo systemctl restart birdnet_recording.service
