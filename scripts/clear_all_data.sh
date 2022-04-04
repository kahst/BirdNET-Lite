#!/usr/bin/env bash
# This script removes all data that has been collected. It is tantamount to
# starting all data-collection from scratch. Only run this if you are sure
# you are okay will losing all the data that you've collected and processed
# so far.
source /etc/birdnet/birdnet.conf
HOME=/home/pi
USER=pi
my_dir=${HOME}/BirdNET-Pi/scripts
echo "Stopping services"
sudo systemctl stop birdnet_recording.service
sudo systemctl stop birdnet_analysis.service
sudo systemctl stop birdnet_server.service
echo "Removing all data . . . "
sudo rm -drf "${RECS_DIR}"
sudo rm -f "${IDFILE}"
sudo rm -f $(dirname ${my_dir})/BirdDB.txt

echo "Creating necessary directories"
[ -d ${EXTRACTED} ] || mkdir -p ${EXTRACTED}
[ -d ${EXTRACTED}/By_Date ] || mkdir -p ${EXTRACTED}/By_Date
[ -d ${EXTRACTED}/Charts ] || mkdir -p ${EXTRACTED}/Charts
[ -d ${PROCESSED} ] || mkdir -p ${PROCESSED}

ln -fs $(dirname ${my_dir})/homepage/* ${EXTRACTED}
ln -fs $(dirname ${my_dir})/model/labels.txt ${my_dir}/
ln -fs $(dirname ${my_dir})/scripts ${EXTRACTED}
ln -fs $(dirname ${my_dir})/scripts/play.php ${EXTRACTED}
ln -fs $(dirname ${my_dir})/scripts/spectrogram.php ${EXTRACTED}
ln -fs $(dirname ${my_dir})/scripts/overview.php ${EXTRACTED}
ln -fs $(dirname ${my_dir})/scripts/stats.php ${EXTRACTED}
ln -fs $(dirname ${my_dir})/scripts/todays_detections.php ${EXTRACTED}
ln -fs $(dirname ${my_dir})/scripts/history.php ${EXTRACTED}
ln -fs $(dirname ${my_dir})/homepage/images/favicon.ico ${EXTRACTED}
ln -fs ${HOME}/phpsysinfo ${EXTRACTED}
ln -fs $(dirname ${my_dir})/templates/phpsysinfo.ini ${HOME}/phpsysinfo/
ln -fs $(dirname ${my_dir})/templates/green_bootstrap.css ${HOME}/phpsysinfo/templates/
ln -fs $(dirname ${my_dir})/templates/index_bootstrap.html ${HOME}/phpsysinfo/templates/html
sudo chmod -R g+rw $(dirname ${my_dir})
sudo chmod -R g+rw ${EXTRACTED}

echo "Dropping and re-creating database"
createdb.sh
echo "Generating BirdDB.txt"
touch $(dirname ${my_dir})/BirdDB.txt
echo "Date;Time;Sci_Name;Com_Name;Confidence;Lat;Lon;Cutoff;Week;Sens;Overlap" > $(dirname ${my_dir})/BirdDB.txt
ln -sf $(dirname ${my_dir})/BirdDB.txt ${my_dir}/BirdDB.txt
chown pi:pi ${my_dir}/BirdDB.txt && chmod g+rw ${my_dir}/BirdDB.txt
echo "Restarting services"
restart_services.sh
