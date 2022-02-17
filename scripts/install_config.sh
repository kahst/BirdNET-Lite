#!/usr/bin/env bash
# Creates and installs the /etc/birdnet/birdnet.conf file
#set -x # Uncomment to enable debugging
set -e
trap 'exit 1' SIGINT SIGHUP

my_dir=$(realpath $(dirname $0))
birdnetpi_dir=$(realpath $(dirname $my_dir))
BIRDNET_CONF="$(dirname ${my_dir})/birdnet.conf"

install_birdnet_conf() {
  cat << EOF > $(dirname ${my_dir})/birdnet.conf
EOF
}

# Checks for a birdnet.conf file 
if [ -f ${BIRDNET_CONF} ];then
  source ${BIRDNET_CONF}
  chmod g+w ${BIRDNET_CONF}
  [ -d /etc/birdnet ] || sudo mkdir /etc/birdnet
  sudo ln -sf $(dirname ${my_dir})/birdnet.conf /etc/birdnet/birdnet.conf
  grep -ve '^#' -e '^$' /etc/birdnet/birdnet.conf > ${birdnetpi_dir}/firstrun.ini
else
  echo "No birdnet.conf"; exit 1
fi
