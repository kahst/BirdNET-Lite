#!/usr/bin/env bash
# This installs the services that have been selected
#set -x # Uncomment to enable debugging
trap 'rm -f ${TMPFILE}' EXIT
trap 'exit 1' SIGINT SIGHUP
my_dir=$(realpath $(dirname $0))
TMPFILE=$(mktemp)
nomachine_url="https://download.nomachine.com/download/7.6/Arm/nomachine_7.6.2_3_arm64.deb"
gotty_url="https://github.com/yudai/gotty/releases/download/v1.0.1/gotty_linux_arm.tar.gz"
CONFIG_FILE="$(dirname ${my_dir})/birdnet.conf"

install_scripts() {
  echo "Installing BirdNET-Pi scripts to /usr/local/bin"
  ln -sf ${my_dir}/* /usr/local/bin/
  rm /usr/local/bin/index.html
}

install_mariadb() {
  if ! which mysql &> /dev/null;then
    echo "Installing MariaDB Server"
    apt -qqy update
    apt -qqy install mariadb-server
    echo "MariaDB Installed"
  fi
  echo "Initializing the database"
#  ${my_dir}/createdb.sh
}

install_birdnet_analysis() {
  echo "Installing the birdnet_analysis.service"
  cat << EOF > /etc/systemd/system/birdnet_analysis.service
[Unit]
Description=BirdNET Analysis
[Service]
Restart=always
RuntimeMaxSec=10800
Type=simple
RestartSec=2
User=${USER}
ExecStart=/usr/local/bin/birdnet_analysis.sh
[Install]
WantedBy=multi-user.target
EOF
  systemctl enable birdnet_analysis.service
}

install_extraction_service() {
  echo "Installing the extraction.service and extraction.timer"
  cat << EOF > /etc/systemd/system/extraction.service
[Unit]
Description=BirdNET BirdSound Extraction
[Service]
Restart=on-failure
RestartSec=3
Type=simple
User=${USER}
ExecStart=/usr/local/bin/extract_new_birdsounds.sh
[Install]
WantedBy=multi-user.target
EOF
  cat << EOF > /etc/systemd/system/extraction.timer
[Unit]
Description=BirdNET BirdSound Extraction Timer
Requires=extraction.service

[Timer]
Unit=extraction.service
OnCalendar=*:*:0/10

[Install]
WantedBy=multi-user.target
EOF
  systemctl enable extraction.timer
  systemctl enable extraction.service
  echo "Adding the species_updater.cron"
  if ! crontab -u ${USER} -l &> /dev/null;then
    crontab -u ${USER} $(dirname ${my_dir})/templates/species_updater.cron &> /dev/null
  else
    crontab -u ${USER} -l > ${TMPFILE}
    cat $(dirname ${my_dir})/templates/species_updater.cron >> ${TMPFILE}
    crontab -u ${USER} "${TMPFILE}" &> /dev/null
  fi
}

create_necessary_dirs() {
  echo "Creating necessary directories"
  [ -d ${EXTRACTED} ] || sudo -u ${USER} mkdir -p ${EXTRACTED}
  [ -d ${EXTRACTED}/By_Date ] || sudo -u ${USER} mkdir -p ${EXTRACTED}/By_Date
  [ -d ${EXTRACTED}/By_Common_Name ] || sudo -u ${USER} mkdir -p ${EXTRACTED}/By_Common_Name
  [ -d ${EXTRACTED}/By_Scientific_Name ] || sudo -u ${USER} mkdir -p ${EXTRACTED}/By_Scientific_Name
  [ -d ${PROCESSED} ] || sudo -u ${USER} mkdir -p ${PROCESSED}
  [ -L ${EXTRACTED}/scripts ] || sudo -u ${USER} ln -s $(dirname ${my_dir})/scripts ${EXTRACTED}
  [ -L ${EXTRACTED}/spectrogram.php ] || sudo -u ${USER} ln -s $(dirname ${my_dir})/scripts/spectrogram.* ${EXTRACTED}
  [ -L ${EXTRACTED}/viewdb.php ] || sudo -u ${USER} ln -s $(dirname ${my_dir})/scripts/viewdb.php ${EXTRACTED}
  sudo -u ${USER} ln -fs ${HOME}/phpsysinfo ${EXTRACTED}
  [ -L ${EXTRACTED}/phpsysinfo.ini ] || sudo -u ${USER} cp ${HOME}/phpsysinfo/phpsysinfo.ini.new ${HOME}/phpsysinfo/phpsysinfo.ini
}
 
install_alsa() {
  echo "Checking for alsa-utils and pulseaudio"
  if which arecord &> /dev/null ;then
    echo "alsa-utils installed"
  else
    echo "Installing alsa-utils"
    apt -qqq update 
    apt install -qqy alsa-utils
    echo "alsa-utils installed"
  fi
  if which pulseaudio &> /dev/null;then
    echo "PulseAudio installed"
  else
    echo "Installing pulseaudio"
    apt -qqq update
    apt install -qqy pulseaudio
    echo "PulseAudio installed"
  fi
}

install_recording_service() {
  echo "Installing birdnet_recording.service"
  cat << EOF > /etc/systemd/system/birdnet_recording.service
[Unit]
Description=BirdNET Recording

[Service]
Environment=XDG_RUNTIME_DIR=/run/user/1000
Restart=always
Type=simple
RestartSec=3
User=${USER}
ExecStart=/usr/local/bin/birdnet_recording.sh

[Install]
WantedBy=multi-user.target
EOF
  systemctl enable birdnet_recording.service
}


install_sshfs() {
  echo "Checking for SSHFS to mount remote filesystem"
  if ! which sshfs &> /dev/null ;then
    echo "Installing SSHFS"
    apt -qqq update 
    apt install -qqqy sshfs
  fi
}

setup_sshkeys() {
  echo "Setting up SSH keys for SSHFS"
  echo "Adding remote host key to ${HOME}/.ssh/known_hosts"
  ssh-keyscan -H ${REMOTE_HOST} >> ${HOME}/.ssh/known_hosts
  chown ${USER}:${USER} ${HOME}/.ssh/known_hosts &> /dev/null
  if [ ! -f ${HOME}/.ssh/id_ed25519.pub ];then
    echo "Creating a new key"
    ssh-keygen -t ed25519 -f ${HOME}/.ssh/id_ed25519 -P ""
  fi
  chown -R ${USER}:${USER} ${HOME}/.ssh/ &> /dev/null
  echo "Copying public key to ${REMOTE_HOST}"
  ssh-copy-id ${REMOTE_USER}@${REMOTE_HOST}
}
 
install_systemd_mount() {
  echo "Installing systemd.mount"
  cat << EOF > /etc/systemd/system/${SYSTEMD_MOUNT}
[Unit]
Description=Mount remote fs with sshfs
DefaultDependencies=no
Conflicts=umount.target
After=network-online.target
Before=umount.target
Wants=network-online.target
[Install]
WantedBy=multi-user.target
[Mount]
What=${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_RECS_DIR}
Where=${RECS_DIR}
Type=fuse.sshfs
Options=delay_connect,_netdev,allow_other,IdentityFile=${HOME}/.ssh/id_ed25519,reconnect,ServerAliveInterval=30,ServerAliveCountMax=5,x-systemd.automount,uid=1000,gid=1000
TimeoutSec=60
EOF
}

install_caddy() {
  if ! which caddy &> /dev/null ;then
    echo "Installing Caddy"
    curl -1sLf \
      'https://dl.cloudsmith.io/public/caddy/stable/setup.deb.sh' \
        | sudo -E bash
    apt -qq update
    apt install -qqy caddy
    systemctl enable --now caddy
  else
    echo "Caddy is installed"
    systemctl enable --now caddy
  fi
}

install_Caddyfile() {
  echo "Installing the Caddyfile"
  [ -d /etc/caddy ] || mkdir /etc/caddy
  sudo -u ${USER} ln -sf $(dirname ${my_dir})/templates/index.html ${EXTRACTED}/
  if [ -f /etc/caddy/Caddyfile ];then
    cp /etc/caddy/Caddyfile{,.original}
  fi
  HASHWORD=$(caddy hash-password -plaintext ${CADDY_PWD})
  cat << EOF > /etc/caddy/Caddyfile
${EXTRACTIONS_URL} {
  root * ${EXTRACTED}
  file_server browse
  basicauth /Processed* {
    birdnet ${HASHWORD}
  }
  basicauth /scripts* {
    birdnet ${HASHWORD}
  }
  basicauth /stream {
    birdnet ${HASHWORD}
  }
  reverse_proxy /stream localhost:8000
  php_fastcgi unix//run/php/php7.3-fpm.sock
}

http://birdnetpi.local {
  root * ${EXTRACTED}
  file_server browse
  basicauth /Processed* {
    birdnet ${HASHWORD}
  }
  basicauth /scripts* {
    birdnet ${HASHWORD}
  }
  basicauth /stream {
    birdnet ${HASHWORD}
  }
  reverse_proxy /stream localhost:8000
  php_fastcgi unix//run/php/php7.3-fpm.sock
}

http://birdlog.local {
  reverse_proxy localhost:8080
}

http://extractionlog.local {
  reverse_proxy localhost:8888
}
EOF
  systemctl reload caddy
}

install_avahi_aliases() {
  echo "Installing Avahi Services"
  if ! which avahi-publish &> /dev/null; then
    echo "Installing avahi-utils"
    apt install -y avahi-utils &> /dev/null
  fi
  echo "Installing avahi-alias service"
  cat << 'EOF' > /etc/systemd/system/avahi-alias@.service
[Unit]
Description=Publish %I as alias for %H.local via mdns
After=network.target network-online.target
Requires=network-online.target

[Service]
Restart=always
Type=simple
ExecStart=/bin/bash -c "/usr/bin/avahi-publish -a -R %I $(avahi-resolve -4 -n %H.local | cut -f 2)"

[Install]
WantedBy=multi-user.target
EOF
  systemctl enable --now avahi-alias@birdnetpi.local.service
}

install_spectrogram_service() {
  cat << EOF > /etc/systemd/system/spectrogram_viewer.service
[Unit]
Description=BirdNET-Pi Spectrogram Viewer
[Service]
Restart=always
RestartSec=10
Type=simple
User=${USER}
ExecStart=/usr/local/bin/spectrogram.sh
[Install]
WantedBy=multi-user.target
EOF
   systemctl enable --now spectrogram_viewer.service
}

install_gotty_logs() {
  echo "Installing GoTTY logging"
  if ! which gotty &> /dev/null;then
    echo "Installing GoTTY binary"
    wget -c ${gotty_url} -O - |  tar -xz -C /usr/local/bin/
  fi
  sudo -u ${USER} ln -sf $(dirname ${my_dir})/templates/gotty \
    ${HOME}/.gotty
  echo "Installing the birdnet_log.service"
  cat << EOF > /etc/systemd/system/birdnet_log.service
[Unit]
Description=BirdNET Analysis Log

[Service]
Restart=on-failure
RestartSec=3
Type=simple
User=${USER}
Environment=TERM=xterm-256color
ExecStart=/usr/local/bin/gotty -p 8080 --title-format "BirdNET-Pi Log" journalctl -o cat -fu birdnet_analysis.service

[Install]
WantedBy=multi-user.target
EOF
  systemctl enable --now birdnet_log.service
  echo "Installing the extraction_log.service"
  cat << EOF > /etc/systemd/system/extraction_log.service
[Unit]
Description=BirdNET Extraction Log

[Service]
Restart=on-failure
RestartSec=3
Type=simple
User=${USER}
Environment=TERM=xterm-256color
ExecStart=/usr/local/bin/gotty -p 8888 --title-format "Extractions Log" journalctl -o cat -fu extraction.service

[Install]
WantedBy=multi-user.target
EOF
  systemctl enable --now extraction_log.service
}

install_sox() {
  if which sox &> /dev/null;then
    echo "Sox is installed"
  else
    echo "Installing sox"
    apt -qq update
    apt install -y sox
    echo "Sox installed"
  fi
}

install_php() {
  if ! which pip &> /dev/null || ! which php-fpm7.3;then
    echo "Installing PHP modules"
    apt -qq update
    apt install -qqy php php-fpm php7.3-mysql php-xml
  else
    echo "PHP and PHP-FPM installed"
  fi
    echo "Configuring PHP for Caddy"
    sed -i 's/www-data/caddy/g' /etc/php/7.3/fpm/pool.d/www.conf
    systemctl restart php7.3-fpm.service
    echo "Adding Caddy sudoers rule"
    cat << EOF > /etc/sudoers.d/010_caddy-nopasswd
caddy ALL=(ALL) NOPASSWD: ALL
EOF
    chmod 0440 /etc/sudoers.d/010_caddy-nopasswd
  if [ ! -d ${HOME}/phpsysinfo ];then
    echo "Fetching phpSysInfo"
    sudo -u ${USER} git clone https://github.com/phpsysinfo/phpsysinfo.git \
      ${HOME}/phpsysinfo
  fi 
}

install_edit_birdnet_conf() {
  cat << EOF > /etc/systemd/system/edit_birdnet_conf.service
[Unit]
Description=Edit birdnet.conf

[Service]
Restart=on-failure
RestartSec=3
Type=simple
User=pi
Environment=TERM=xterm-256color
ExecStart=/usr/local/bin/gotty -w -p 9898 --title-format "Edit birdnet.conf" nano /home/pi/BirdNET-Pi/birdnet.conf

[Install]
WantedBy=multi-user.target
EOF
}


install_icecast() {
  if ! which icecast2;then
    echo "Installing IceCast2"
    apt -qq update
    echo "icecast2 icecast2/icecast-setup boolean false" | debconf-set-selections
    apt install -qqy icecast2 
    config_icecast
    systemctl enable --now icecast2
    /etc/init.d/icecast2 start
  else
    echo "Icecast2 is installed"
    config_icecast
    systemctl enable --now icecast2
    /etc/init.d/icecast2 start
  fi
}

config_icecast() {
  if [ -f /etc/icecast2/icecast.xml ];then 
    cp /etc/icecast2/icecast.xml{,.prebirdnetpi}
  fi
  sed -i 's/>admin</>birdnet</g' /etc/icecast2/icecast.xml
  passwords=("source-" "relay-" "admin-" "master-" "")
  for i in "${passwords[@]}";do
  sed -i "s/<${i}password>.*<\/${i}password>/<${i}password>${ICE_PWD}<\/${i}password>/g" /etc/icecast2/icecast.xml
  done
}

install_livestream_service() {
  echo "Installing Live Stream service"
  cat << EOF > /etc/systemd/system/livestream.service
[Unit]
Description=BirdNET-Pi Live Stream

[Service]
Environment=XDG_RUNTIME_DIR=/run/user/1000
Restart=always
Type=simple
RestartSec=3
User=${USER}
ExecStart=/usr/local/bin/livestream.sh

[Install]
WantedBy=multi-user.target
EOF
  systemctl enable --now livestream.service
}

install_nomachine() {
  echo "Installing NoMachine"
  cd ~
  curl -s -O "${nomachine_url}"
  apt install -y ${HOME}/nomachine_7.6.2_3_arm64.deb
  rm -f ${HOME}/nomachine_7.6.2_3_arm64.deb
}

install_systemd_overrides() {
  for i in caddy birdnet_analysis extraction birdnet_recording;do
    if [ -f /etc/systemd/system/${i}.service ];then
      [ -d /etc/systemd/system/${i}.d ] || mkdir /etc/systemd/system/${i}.d
      echo "Installing the systemd overrides.conf for the ${i}.service"
      cat << EOF > /etc/systemd/system/${i}.d/overrides.conf
[Unit]
After=network.target network-online.target ${SYSTEMD_MOUNT}
Requires=network-online.target ${SYSTEMD_MOUNT}
EOF
    fi
  done
}

install_cleanup_cron() {
  echo "Installing the cleanup.cron"
  if ! crontab -u ${USER} -l &> /dev/null;then
    crontab -u ${USER} $(dirname ${my_dir})/templates/cleanup.cron &> /dev/null
  else
    crontab -u ${USER} -l > ${TMPFILE}
    cat $(dirname ${my_dir})/templates/cleanup.cron >> ${TMPFILE}
    crontab -u ${USER} "${TMPFILE}" &> /dev/null
  fi
}

install_selected_services() {
  install_scripts
  install_birdnet_analysis

  if [[ "${DO_EXTRACTIONS}" =~ [Yy] ]];then
    install_extraction_service
  fi

  if [[ "${DO_RECORDING}" =~ [Yy] ]];then
    install_alsa
    install_recording_service
  fi

  if [[ "${REMOTE}" =~ [Yy] ]];then
    install_sshfs
    setup_sshkeys
    install_systemd_mount
  fi

  if [ ! -z "${EXTRACTIONS_URL}" ];then
    install_caddy
    install_Caddyfile
    install_avahi_aliases
    install_gotty_logs
    install_sox
    install_mariadb
    install_php
    install_spectrogram_service
    install_edit_birdnet_conf
  fi

  if [ ! -z "${ICE_PWD}" ];then
    install_icecast
    install_livestream_service
  fi

  if [[ "${INSTALL_NOMACHINE}" =~ [Yy] ]];then
    install_nomachine
  fi

  if [[ "${REMOTE}" =~ [Yy] ]];then
    install_systemd_overrides
  fi

  create_necessary_dirs
  install_cleanup_cron
}

if [ -f ${CONFIG_FILE} ];then 
  source ${CONFIG_FILE}
  USER=${BIRDNET_USER}
  HOME="$(getent passwd ${BIRDNET_USER} | cut -d: -f6)"
  install_selected_services
else
  echo "Unable to find a configuration file. Please make sure that $CONFIG_FILE exists."
fi
