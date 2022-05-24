#!/usr/bin/env bash
# Install BirdNET script
set -x # Debugging
exec > >(tee -i installation-$(date +%F).txt) 2>&1 # Make log
set -e # exit installation if anything fails

my_dir=$HOME/BirdNET-Pi
export my_dir=$my_dir

cd $my_dir/scripts || exit 1

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
sudo -E HOME=$HOME USER=$USER ./install_services.sh || exit 1
source /etc/birdnet/birdnet.conf

install_birdnet() {
  cd ~/BirdNET-Pi || exit 1
  echo "Establishing a python virtual environment"
  python3 -m venv birdnet
  source ./birdnet/bin/activate
  pip3 install -U -r $HOME/BirdNET-Pi/requirements.txt
}

unpack_labels() {
  unzip $HOME/BirdNET-Pi/model/labels_l18n.zip labels_en.txt \
    -d $HOME/BirdNET-Pi/model
  mv $HOME/BirdNET-Pi/model/labels_en.txt $HOME/BirdNET-Pi/model/labels.txt
}

[ -d ${RECS_DIR} ] || mkdir -p ${RECS_DIR} &> /dev/null

install_birdnet 
unpack_labels
exit 0
