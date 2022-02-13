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
echo "Removing all data . . . "
sudo rm -drf "${RECS_DIR}"
sudo rm -f "${IDFILE}"
sudo rm -f $(dirname ${my_dir})/BirdDB.txt
echo "Creating necessary directories"
[ -d ${EXTRACTED} ] || sudo -u ${USER} mkdir -p ${EXTRACTED}
[ -d ${EXTRACTED}/By_Date ] || sudo -u ${USER} mkdir -p ${EXTRACTED}/By_Date
[ -d ${EXTRACTED}/By_Common_Name ] || sudo -u ${USER} mkdir -p ${EXTRACTED}/By_Common_Name
[ -d ${EXTRACTED}/By_Scientific_Name ] || sudo -u ${USER} mkdir -p ${EXTRACTED}/By_Scientific_Name
[ -d ${EXTRACTED}/Charts ] || sudo -u ${USER} mkdir -p ${EXTRACTED}/Charts
[ -d ${PROCESSED} ] || sudo -u ${USER} mkdir -p ${PROCESSED}

sudo -u ${USER} ln -fs $(dirname ${my_dir})/homepage/* ${EXTRACTED}
if [ ! -z ${BIRDNETLOG_URL} ];then
  BIRDNETLOG_URL="$(echo ${BIRDNETLOG_URL} | sed 's/\/\//\\\/\\\//g')"
  sudo -u${USER} sed -i "s/http:\/\/$(hostname).local:8080/"${BIRDNETLOG_URL}"/g" $(dirname ${my_dir})/homepage/*.html
  phpfiles="$(grep -l "$(hostname).local:8080" ${my_dir}/*.php)"
  for i in "${phpfiles[@]}";do
    sudo -u${USER} sed -i "s/http:\/\/$(hostname).local:8080/"${BIRDNETLOG_URL}"/g" ${i}
  done
fi
if [ ! -z ${WEBTERMINAL_URL} ];then
  WEBTERMINAL_URL="$(echo ${WEBTERMINAL_URL} | sed 's/\/\//\\\/\\\//g')"
  sudo -u${USER} sed -i "s/http:\/\/$(hostname).local:8888/"${WEBTERMINAL_URL}"/g" $(dirname ${my_dir})/homepage/*.html
  phpfiles="$(grep -l "$(hostname).local:8888" ${my_dir}/*.php)"
  for i in "${phpfiles[@]}";do
    sudo -u${USER} sed -i "s/http:\/\/$(hostname).local:8888/"${WEBTERMINAL_URL}"/g" ${i}
  done
fi

sudo -u ${USER} ln -fs $(dirname ${my_dir})/model/labels.txt ${my_dir}/
sudo -u ${USER} ln -fs $(dirname ${my_dir})/scripts ${EXTRACTED}
if [ -z ${BIRDNETPI_URL} ];then
  sudo -u${USER} sed -i "s/birdnetpi.local/$(hostname).local/g" $(dirname ${my_dir})/homepage/*.html
  sudo -u${USER} sed -i "s/birdnetpi.local/$(hostname).local/g" $(dirname ${my_dir})/scripts/*.html
  sudo -u${USER} sed -i "s/birdnetpi.local/$(hostname).local/g" $(dirname ${my_dir})/scripts/*.html
  sudo -u${USER} sed -i "s/birdnetpi.local/$(hostname).local/g" $(dirname ${my_dir})/scripts/*.php
  sudo -u${USER} sed -i "s/birdnetpi.local/$(hostname).local/g" $(dirname ${my_dir})/scripts/*/*.php
else
  BIRDNETPI_URL="$(echo ${BIRDNETPI_URL} | sed 's/\/\//\\\/\\\//g')"
  sudo -u${USER} sed -i "s/http:\/\/birdnetpi.local/${BIRDNETPI_URL}/g" $(dirname ${my_dir})/homepage/*.html
  sudo -u${USER} sed -i "s/http:\/\/birdnetpi.local/${BIRDNETPI_URL}/g" $(dirname ${my_dir})/scripts/*.html
  sudo -u${USER} sed -i "s/http:\/\/birdnetpi.local/${BIRDNETPI_URL}/g" $(dirname ${my_dir})/scripts/*.html
  sudo -u${USER} sed -i "s/http:\/\/birdnetpi.local/${BIRDNETPI_URL}/g" $(dirname ${my_dir})/scripts/*.php
  sudo -u${USER} sed -i "s/http:\/\/birdnetpi.local/${BIRDNETPI_URL}/g" $(dirname ${my_dir})/scripts/*/*.php
fi

sudo -u ${USER} ln -fs $(dirname ${my_dir})/scripts/spectrogram.php ${EXTRACTED}
sudo -u ${USER} ln -fs $(dirname ${my_dir})/scripts/viewday.php ${EXTRACTED}
sudo -u ${USER} ln -fs $(dirname ${my_dir})/scripts/overview.php ${EXTRACTED}
sudo -u ${USER} ln -fs $(dirname ${my_dir})/scripts/stats.php ${EXTRACTED}
sudo -u ${USER} ln -fs $(dirname ${my_dir})/scripts/viewdb.php ${EXTRACTED}
sudo -u ${USER} ln -fs $(dirname ${my_dir})/homepage/images/favicon.ico ${EXTRACTED}
sudo -u ${USER} ln -fs ${HOME}/phpsysinfo ${EXTRACTED}
sudo -u ${USER} cp -f $(dirname ${my_dir})/templates/phpsysinfo.ini ${HOME}/phpsysinfo/
sudo -u ${USER} cp -f $(dirname ${my_dir})/templates/green_bootstrap.css ${HOME}/phpsysinfo/templates/
sudo -u ${USER} cp -f $(dirname ${my_dir})/templates/index_bootstrap.html ${HOME}/phpsysinfo/templates/html

echo "Setting Wttr.in URL to "${LATITUDE}", "${LONGITUDE}""
sudo -u${USER} sed -i "s/https:\/\/v2.wttr.in\//https:\/\/v2.wttr.in\/"${LATITUDE},${LONGITUDE}"/g" $(dirname ${my_dir})/homepage/menu.html
chmod -R g+rw $(dirname ${my_dir})
chmod -R g+rw ${RECS_DIR}

echo "Generating BirdDB.txt"
if ! [ -f $(dirname ${my_dir})/BirdDB.txt ];then
  sudo -u ${USER} touch $(dirname ${my_dir})/BirdDB.txt
  echo "Date;Time;Sci_Name;Com_Name;Confidence;Lat;Lon;Cutoff;Week;Sens;Overlap" | sudo -u ${USER} tee -a $(dirname ${my_dir})/BirdDB.txt
elif ! grep Date $(dirname ${my_dir})/BirdDB.txt;then
  sudo -u ${USER} sed -i '1 i\Date;Time;Sci_Name;Com_Name;Confidence;Lat;Lon;Cutoff;Week;Sens;Overlap' $(dirname ${my_dir})/BirdDB.txt
fi
ln -sf $(dirname ${my_dir})/BirdDB.txt ${my_dir}/BirdDB.txt &&
  chown pi:pi ${my_dir}/BirdDB.txt && chmod g+rw ${my_dir}/BirdDB.txt


echo "Dropping and re-creating database"
sudo /home/pi/BirdNET-Pi/scripts/createdb_bullseye.sh
echo "Restarting services"
sudo systemctl start birdnet_recording.service
