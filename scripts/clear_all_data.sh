#!/usr/bin/env bash
# This script removes all data that has been collected. It is tantamount to
# starting all data-collection from scratch. Only run this if you are sure
# you are okay will losing all the data that you've collected and processed
# so far.
source /etc/birdnet/birdnet.conf
HOME=/home/pi
my_dir=${HOME}/BirdNET-Pi/scripts
echo "Stopping services"
sudo systemctl stop birdnet_recording.service
echo "Removing all data . . . "
sudo rm -drf "${RECS_DIR}"
sudo rm -f "${IDFILE}"
echo "Recreating necessary directories"
echo "Creating necessary directories"
[ -d ${EXTRACTED} ] || sudo -u ${USER} mkdir -p ${EXTRACTED}
[ -d ${EXTRACTED}/By_Date ] || sudo -u ${USER} mkdir -p ${EXTRACTED}/By_Date
[ -d ${EXTRACTED}/By_Common_Name ] || sudo -u ${USER} mkdir -p ${EXTRACTED}/By_Common_Name
[ -d ${EXTRACTED}/By_Scientific_Name ] || sudo -u ${USER} mkdir -p ${EXTRACTED}/By_Scientific_Name
[ -d ${PROCESSED} ] || sudo -u ${USER} mkdir -p ${PROCESSED}

sudo -u ${USER} ln -fs $(dirname ${my_dir})/homepage/* ${EXTRACTED}
if [ ! -z ${BIRDNETLOG_URL} ];then
  BIRDNETLOG_URL="$(echo ${BIRDNETLOG_URL} | sed 's/\/\//\\\/\\\//g')"
  sudo -u${USER} sed -i "s/http:\/\/birdnetpi.local:8080/"${BIRDNETLOG_URL}"/g" $(dirname ${my_dir})/homepage/*.html
  phpfiles="$(grep -l "birdnetpi.local:8080" ${my_dir}/*.php)"
  for i in "${phpfiles[@]}";do
    sudo -u${USER} sed -i "s/http:\/\/birdnetpi.local:8080/"${BIRDNETLOG_URL}"/g" ${i}
  done
fi
if [ ! -z ${EXTRACTIONLOG_URL} ];then
  EXTRACTIONLOG_URL="$(echo ${EXTRACTIONLOG_URL} | sed 's/\/\//\\\/\\\//g')"
  sudo -u${USER} sed -i "s/http:\/\/birdnetpi.local:8888/"${EXTRACTIONLOG_URL}"/g" $(dirname ${my_dir})/homepage/*.html
  phpfiles="$(grep -l "birdnetpi.local:8888" ${my_dir}/*.php)"
  for i in "${phpfiles[@]}";do
    sudo -u${USER} sed -i "s/http:\/\/birdnetpi.local:8888/"${EXTRACTIONLOG_URL}"/g" ${i}
  done
fi

sudo -u ${USER} ln -fs $(dirname ${my_dir})/scripts ${EXTRACTED}
if [ ! -z ${BIRDNETPI_URL} ];then
  BIRDNETPI_URL="$(echo ${BIRDNETPI_URL} | sed 's/\/\//\\\/\\\//g')"
  sudo -u${USER} sed -i "s/http:\/\/birdnetpi.local/"${BIRDNETPI_URL}"/g" $(dirname ${my_dir})/homepage/*.html
  phpfiles="$(grep -l birdnetpi.local ${my_dir}/*.php)"
  for i in "${phpfiles[@]}";do
    sudo -u${USER} sed -i "s/http:\/\/birdnetpi.local/"${BIRDNETPI_URL}"/g" ${i}
  done
fi

sudo -u ${USER} ln -fs $(dirname ${my_dir})/scripts/spectrogram.php ${EXTRACTED}
sudo -u ${USER} ln -fs $(dirname ${my_dir})/scripts/viewdb.php ${EXTRACTED}
sudo -u ${USER} ln -fs ${HOME}/phpsysinfo ${EXTRACTED}
sudo -u ${USER} cp -f $(dirname ${my_dir})/templates/phpsysinfo.ini ${HOME}/phpsysinfo/
sudo -u ${USER} cp -f $(dirname ${my_dir})/templates/green_bootstrap.css ${HOME}/phpsysinfo/templates/
sudo -u ${USER} cp -f $(dirname ${my_dir})/templates/index_bootstrap.html ${HOME}/phpsysinfo/templates/html



sudo -u ${BIRDNET_USER} cp ~/BirdNET-Pi/homepage/index.html ${EXTRACTED}/
echo "Dropping and re-creating database"
sudo /home/pi/BirdNET-Pi/scripts/createdb.sh
echo "Restarting services"
sudo systemctl restart birdnet_recording.service
