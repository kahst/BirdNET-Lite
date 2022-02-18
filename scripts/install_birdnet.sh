#!/usr/bin/env bash
# Install BirdNET script
set -x # debugging
set -e # exit installation if anything fails
my_dir=$(realpath $(dirname $0))
trap '${my_dir}/dump_logs.sh && echo -e "\n\nExiting the installation. Goodbye!" && exit 1' SIGINT
cd $my_dir || exit 1

if [ "$(uname -m)" != "aarch64" ];then
  echo "BirdNET-Pi requires a 64-bit OS.
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

apt_deps=(swig ffmpeg wget unzip curl cmake make bc)
libs_modules=(libjpeg-dev zlib1g-dev python3-dev python3-pip python3-venv)

install_deps() {
  echo "Checking dependencies"
  for i in "${libs_modules[@]}";do
    if [ $(apt list --installed 2>/dev/null | grep "$i" | wc -l) -le 0 ];then
      echo "	Installing $i"
      sudo apt -y install ${i} &> /dev/null
    else
      echo "$i is installed!"
    fi
  done

  for i in "${apt_deps[@]}";do
    if ! which $i &>/dev/null ;then
      echo "Installing $i"
      sudo apt -y install ${i} &> /dev/null
    else
      echo "$i is installed!"
    fi
  done
}

install_birdnet() {
  cd ~/BirdNET-Pi || exit 1
  echo "Establishing a python virtual environment"
  python3 -m venv birdnet
  source ./birdnet/bin/activate
  echo "Upgrading pip, wheel, and setuptools"
  pip3 install --upgrade pip wheel setuptools
  # TFLite Pre-built binaires from https://github.com/PINTO0309/TensorflowLite-bin
  echo "Installing the TFLite bin wheel"
  pip3 install --upgrade tflite_runtime-2.6.0-cp39-none-linux_aarch64.whl
  echo "Making sure everything else is installed"
  pip3 install -U -r /home/pi/BirdNET-Pi/requirements.txt
}

[ -d ${RECS_DIR} ] || mkdir -p ${RECS_DIR} &> /dev/null

install_deps
install_birdnet 
exit 0
