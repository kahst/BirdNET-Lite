#!/usr/bin/env bash
# Install BirdNET script
#set -x # debugging
set -e # exit installation if anything fails
my_dir=$(realpath $(dirname $0))
trap '${my_dir}/dump_logs.sh && echo -e "\n\nExiting the installation. Goodbye!" && exit 1' SIGINT
cd $my_dir || exit 1

if [ "$(uname -m)" != "aarch64" ];then
  echo "BirdNET-Lite requires a 64-bit OS.
It looks like your operating system is using $(uname -m), 
but would need to be aarch64.
Please take a look at https://birdnetwiki.pmcgui.xyz for more
information"
  exit 1
fi

#Install/Configure /etc/birdnet/birdnet.conf
./install_config.sh || exit 1
sudo ./install_services.sh || exit 1
source /etc/birdnet/birdnet.conf

APT_DEPS=(swig ffmpeg wget unzip curl cmake make)
LIBS_MODULES=(libjpeg-dev zlib1g-dev python3-dev python3-pip)

install_deps() {
  echo "	Checking dependencies"
  sudo apt update &> /dev/null
  for i in "${LIBS_MODULES[@]}";do
    if [ $(apt list --installed 2>/dev/null | grep "$i" | wc -l) -le 0 ];then
      echo "	Installing $i"
      sudo apt -y install ${i} &> /dev/null
    else
      echo "	$i is installed!"
    fi
  done

  for i in "${APT_DEPS[@]}";do
    if ! which $i &>/dev/null ;then
      echo "	Installing $i"
      sudo apt -y install ${i} &> /dev/null
    else
      echo "	$i is installed!"
    fi
  done
}

install_birdnet() {
  set -xe
  cd ~/BirdNET-Lite || exit 1
  echo "Upgrading pip, wheel, and setuptools"
  sudo pip3 install --upgrade pip wheel setuptools
  echo "Fetching the TFLite pre-built binaries"
  TFLITE_URL="https://drive.google.com/uc?export=download&id=1dlEbugFDJXs-YDBCUC6WjADVtIttWxZA"
  curl -sc /tmp/cookie ${TFLITE_URL}
  CODE="$(awk '/_warning_/ {print $NF}' /tmp/cookie)"
  TF_COOKIE="https://drive.google.com/uc?export=download&confirm=${CODE}&id=1dlEbugFDJXs-YDBCUC6WjADVtIttWxZA"
  curl -Lb /tmp/cookie ${TF_COOKIE} -o tflite_runtime-2.6.0-cp37-none-linux_aarch64.whl
  echo "Installing the new TFLite bin wheel"
  sudo pip3 install --upgrade tflite_runtime-2.6.0-cp37-none-linux_aarch64.whl
  echo "Installing colorama==0.4.4"
  sudo pip3 install colorama==0.4.4
  echo "Installing librosa"
  sudo pip3 install librosa
  set +x
}

echo "
This script will do the following:
#1: Install the following BirdNET system dependencies:
- ffmpeg
- swig
- libjpeg-dev
- zlib1g-dev
- python3-dev
- curl
- cmake
- make
- wget
#2: Copies the systemd .service and .mount files and enables those chosen
#3: Adds cron environments and jobs chosen"

echo
read -sp "\
Be sure you have read the software license before installing. This is
available in the BirdNET-Lite directory as "LICENSE"
If you DO NOT want to install BirdNET and the birdnet_analysis.service, 
press Ctrl+C to cancel. If you DO wish to install BirdNET and the 
birdnet_analysis.service, press ENTER to continue with the installation."
echo
echo

[ -d ${RECS_DIR} ] || mkdir -p ${RECS_DIR} &> /dev/null

install_deps
if [ ! -d ${VENV} ];then
  install_birdnet 
fi

echo "	BirdNet is installed!!

  To start the service manually, issue:
     'sudo systemctl start birdnet_analysis'
  To monitor the service logs, issue: 
     'journalctl -fu birdnet_analysis'
  To stop the service manually, issue: 
     'sudo systemctl stop birdnet_analysis'
  To stop and disable the service, issue: 
     'sudo systemctl disable --now birdnet_analysis.service'

  Visit
  http://birdnetsystem.local to see your extractions,
  http://birdlog.local to see the log output of the birdnet_analysis.service,
  http://extractionlog.local to see the log output of the extraction.service, and
  http://birdstats.local to see the BirdNET-Lite Report"
echo
read -n1 -p "  Would you like to run the birdnet_analysis.service now?" YN
echo
case $YN in
  [Yy] ) sudo systemctl start birdnet_analysis.service \
    && journalctl -fu birdnet_analysis;;
* ) echo "  Thanks for installing BirdNET-Lite!!
  I hope it was helpful!";;
esac
