#!/usr/bin/env bash
# This installs the services that have been selected
set -x # Uncomment to enable debugging
trap 'rm -f ${tmpfile}' EXIT
trap 'exit 1' SIGINT SIGHUP
USER=pi
HOME=/home/pi
my_dir=${HOME}/BirdNET-Pi/scripts
tmpfile=$(mktemp)
gotty_url="https://github.com/yudai/gotty/releases/download/v1.0.1/gotty_linux_arm.tar.gz"
config_file="$(dirname ${my_dir})/birdnet.conf"

install_depends() {
  curl -1sLf \
    'https://dl.cloudsmith.io/public/caddy/stable/setup.deb.sh' \
      | sudo -E bash
  apt -qqq update && apt -qqy upgrade
  echo "icecast2 icecast2/icecast-setup boolean false" | debconf-set-selections
  apt install -qqy caddy lynx ftpd sqlite3 php-sqlite3 alsa-utils \
    pulseaudio avahi-utils sox libsox-fmt-mp3 php php-fpm php-mysql php-xml \
    php-zip icecast2 swig ffmpeg wget unzip curl cmake make bc libjpeg-dev \
    zlib1g-dev python3-dev python3-pip python3-venv
  wget -c ${gotty_url} -O - |  tar -xz -C /usr/local/bin/
}


set_hostname() {
  if [ "$(hostname)" == "raspberrypi" ];then
    hostnamectl set-hostname birdnetpi
    sed -i 's/raspberrypi/birdnetpi/g' /etc/hosts
  fi
}

update_etc_hosts() {
  sed -ie s/'$(hostname).local'/"$(hostname).local ${BIRDNETPI_URL//https:\/\/} ${WEBTERMINAL_URL//https:\/\/} ${BIRDNETLOG_URL//https:\/\/}"/g /etc/hosts
}

install_scripts() {
  ln -sf ${my_dir}/* /usr/local/bin/
  rm /usr/local/bin/index.html
}

install_birdnet_analysis() {
  cat << EOF > /etc/systemd/system/birdnet_analysis.service
[Unit]
Description=BirdNET Analysis
After=birdnet_server.service
Requires=birdnet_server.service
[Service]
Restart=always
Type=simple
RestartSec=2
User=${USER}
ExecStart=/usr/local/bin/birdnet_analysis.sh
[Install]
WantedBy=multi-user.target
EOF
  systemctl enable birdnet_analysis.service
}

install_birdnet_server() {
  cat << EOF > /etc/systemd/system/birdnet_server.service
[Unit]
Description=BirdNET Analysis Server
Before=birdnet_analysis.service
[Service]
Restart=always
Type=simple
RestartSec=10
User=${USER}
ExecStart=/usr/local/bin/server.py
[Install]
WantedBy=multi-user.target
EOF
  systemctl enable birdnet_server.service
}

install_extraction_service() {
  cat << EOF > /etc/systemd/system/extraction.service
[Unit]
Description=BirdNET BirdSound Extraction
[Service]
Restart=on-failure
RestartSec=3
Type=simple
User=${USER}
ExecStart=/usr/bin/env bash -c 'while true;do extract_new_birdsounds.sh;sleep ${RECORDING_LENGTH};done'
[Install]
WantedBy=multi-user.target
EOF
  systemctl enable extraction.service
}

install_pushed_notifications() {
  cat << EOF > /etc/systemd/system/pushed_notifications.service
[Unit]
Description=BirdNET-Pi Pushed.co Notifications
[Service]
Restart=on-success
RestartSec=3
Type=simple
User=pi
ExecStart=/usr/local/bin/species_notifier.sh
[Install]
WantedBy=multi-user.target
EOF
  systemctl enable pushed_notifications.service
}

create_necessary_dirs() {
  echo "Creating necessary directories"
  [ -d ${EXTRACTED} ] || sudo -u ${USER} mkdir -p ${EXTRACTED}
  [ -d ${EXTRACTED}/By_Date ] || sudo -u ${USER} mkdir -p ${EXTRACTED}/By_Date
  [ -d ${EXTRACTED}/Charts ] || sudo -u ${USER} mkdir -p ${EXTRACTED}/Charts
  [ -d ${PROCESSED} ] || sudo -u ${USER} mkdir -p ${PROCESSED}

  sudo -u ${USER} ln -fs $(dirname ${my_dir})/homepage/* ${EXTRACTED}  
  if [ ! -z ${BIRDNETLOG_URL} ];then
    BIRDNETLOG_URL="$(echo ${BIRDNETLOG_URL} | sed 's/\/\//\\\/\\\//g')"
    sudo -u${USER} sed -i "s/http:\/\/birdnetpi.local:8080/${BIRDNETLOG_URL}/g" $(dirname ${my_dir})/homepage/*.html
    sudo -u${USER} sed -i "s/http:\/\/birdnetpi.local:8080/${BIRDNETLOG_URL}/g" $(dirname ${my_dir})/scripts/*.html
    sudo -u${USER} sed -i "s/http:\/\/birdnetpi.local:8080/${BIRDNETLOG_URL}/g" $(dirname ${my_dir})/scripts/*.php
    sudo -u${USER} sed -i "s/http:\/\/birdnetpi.local:8080/${BIRDNETLOG_URL}/g" $(dirname ${my_dir})/scripts/*/*.php
  fi
  if [ ! -z ${WEBTERMINAL_URL} ];then
    WEBTERMINAL_URL="$(echo ${WEBTERMINAL_URL} | sed 's/\/\//\\\/\\\//g')"
    sudo -u${USER} sed -i "s/http:\/\/birdnetpi.local:8888/${WEBTERMINAL_URL}/g" $(dirname ${my_dir})/homepage/*.html
    sudo -u${USER} sed -i "s/http:\/\/birdnetpi.local:8888/${WEBTERMINAL_URL}/g" $(dirname ${my_dir})/scripts/*.html
    sudo -u${USER} sed -i "s/http:\/\/birdnetpi.local:8888/${WEBTERMINAL_URL}/g" $(dirname ${my_dir})/scripts/*.php
    sudo -u${USER} sed -i "s/http:\/\/birdnetpi.local:8888/${WEBTERMINAL_URL}/g" $(dirname ${my_dir})/scripts/*/*.php
  fi

  sudo -u ${USER} ln -fs $(dirname ${my_dir})/model/labels.txt ${my_dir}/
  sudo -u ${USER} ln -fs $(dirname ${my_dir})/scripts ${EXTRACTED}
  if [ -z ${BIRDNETPI_URL} ];then
    sudo -u${USER} sed -i "s/birdnetpi.local/$(hostname).local/g" $(dirname ${my_dir})/homepage/*.html
    sudo -u${USER} sed -i "s/birdnetpi.local/$(hostname).local/g" $(dirname ${my_dir})/scripts/*.html
    sudo -u${USER} sed -i "s/birdnetpi.local/$(hostname).local/g" $(dirname ${my_dir})/scripts/*.php
    sudo -u${USER} sed -i "s/birdnetpi.local/$(hostname).local/g" $(dirname ${my_dir})/scripts/*/*.php
  else
    BIRDNETPI_URL="$(echo ${BIRDNETPI_URL} | sed 's/\/\//\\\/\\\//g')"
    sudo -u${USER} sed -i "s/http:\/\/birdnetpi.local/${BIRDNETPI_URL}/g" $(dirname ${my_dir})/homepage/*.html
    sudo -u${USER} sed -i "s/http:\/\/birdnetpi.local/${BIRDNETPI_URL}/g" $(dirname ${my_dir})/scripts/*.html
    sudo -u${USER} sed -i "s/http:\/\/birdnetpi.local/${BIRDNETPI_URL}/g" $(dirname ${my_dir})/scripts/*.php
    sudo -u${USER} sed -i "s/http:\/\/birdnetpi.local/${BIRDNETPI_URL}/g" $(dirname ${my_dir})/scripts/*/*.php
  fi

  sudo -u ${USER} ln -fs $(dirname ${my_dir})/scripts/spectrogram.php ${EXTRACTED}
  sudo -u ${USER} ln -fs $(dirname ${my_dir})/scripts/overview.php ${EXTRACTED}
  sudo -u ${USER} ln -fs $(dirname ${my_dir})/scripts/stats.php ${EXTRACTED}
  sudo -u ${USER} ln -fs $(dirname ${my_dir})/scripts/viewdb.php ${EXTRACTED}
  sudo -u ${USER} ln -fs $(dirname ${my_dir})/scripts/history.php ${EXTRACTED}
  sudo -u ${USER} ln -fs $(dirname ${my_dir})/homepage/images/favicon.ico ${EXTRACTED}
  sudo -u ${USER} ln -fs ${HOME}/phpsysinfo ${EXTRACTED}
  sudo -u ${USER} cp -f $(dirname ${my_dir})/templates/phpsysinfo.ini ${HOME}/phpsysinfo/
  sudo -u ${USER} cp -f $(dirname ${my_dir})/templates/green_bootstrap.css ${HOME}/phpsysinfo/templates/
  sudo -u ${USER} cp -f $(dirname ${my_dir})/templates/index_bootstrap.html ${HOME}/phpsysinfo/templates/html

  echo "Setting Wttr.in URL to "${LATITUDE}", "${LONGITUDE}""
  sudo -u${USER} sed -i "s/https:\/\/v2.wttr.in\//https:\/\/v2.wttr.in\/"${LATITUDE},${LONGITUDE}"/g" $(dirname ${my_dir})/homepage/menu.html
  chmod -R g+rw $(dirname ${my_dir})
  chmod -R g+rw ${RECS_DIR}
}

generate_BirdDB() {
  echo "Generating BirdDB.txt"
  if ! [ -f $(dirname ${my_dir})/BirdDB.txt ];then
    sudo -u ${USER} touch $(dirname ${my_dir})/BirdDB.txt
    echo "Date;Time;Sci_Name;Com_Name;Confidence;Lat;Lon;Cutoff;Week;Sens;Overlap" | sudo -u ${USER} tee -a $(dirname ${my_dir})/BirdDB.txt
  elif ! grep Date $(dirname ${my_dir})/BirdDB.txt;then
    sudo -u ${USER} sed -i '1 i\Date;Time;Sci_Name;Com_Name;Confidence;Lat;Lon;Cutoff;Week;Sens;Overlap' $(dirname ${my_dir})/BirdDB.txt
  fi
  ln -sf $(dirname ${my_dir})/BirdDB.txt ${my_dir}/BirdDB.txt &&
  chown pi:pi ${my_dir}/BirdDB.txt && chmod g+rw ${my_dir}/BirdDB.txt
}

set_login() {
  if ! [ -d /etc/lightdm ];then
    systemctl set-default multi-user.target
    ln -fs /lib/systemd/system/getty@.service /etc/systemd/system/getty.target.wants/getty@tty1.service
    cat > /etc/systemd/system/getty@tty1.service.d/autologin.conf << EOF
[Service]
ExecStart=
ExecStart=-/sbin/agetty --autologin $USER --noclear %I \$TERM
EOF
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

install_Caddyfile() {
  [ -d /etc/caddy ] || mkdir /etc/caddy
  if [ -f /etc/caddy/Caddyfile ];then
    cp /etc/caddy/Caddyfile{,.original}
  fi
  if ! [ -z ${CADDY_PWD} ];then
  HASHWORD=$(caddy hash-password -plaintext ${CADDY_PWD})
  cat << EOF > /etc/caddy/Caddyfile
http://localhost http://$(hostname).local ${BIRDNETPI_URL} {
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
  basicauth /phpsysinfo* {
    birdnet ${HASHWORD}
  }
  reverse_proxy /stream localhost:8000
  php_fastcgi unix//run/php/php7.4-fpm.sock
}
EOF
  else
    cat << EOF > /etc/caddy/Caddyfile
http://localhost http://$(hostname).local ${BIRDNETPI_URL} {
  root * ${EXTRACTED}
  file_server browse
  reverse_proxy /stream localhost:8000
  php_fastcgi unix//run/php/php7.4-fpm.sock
}
EOF
  fi

  if [ ! -z ${WEBTERMINAL_URL} ] && [ ! -z ${HASHWORD} ];then
    cat << EOF >> /etc/caddy/Caddyfile
${WEBTERMINAL_URL} {
  basicauth {
    birdnet ${HASHWORD}
  }
  reverse_proxy localhost:8888
}
EOF
  elif [ ! -z ${WEBTERMINAL_URL} ] && [ -z ${HASHWORD} ];then
    cat << EOF >> /etc/caddy/Caddyfile
${WEBTERMINAL_URL} {
  reverse_proxy localhost:8888
}
EOF
  fi

  if [ ! -z ${BIRDNETLOG_URL} ];then
    cat << EOF >> /etc/caddy/Caddyfile
${BIRDNETLOG_URL} {
  reverse_proxy localhost:8080
}
EOF
  fi
  systemctl enable caddy
  usermod -aG pi caddy
}

install_avahi_aliases() {
  cat << 'EOF' > /etc/systemd/system/avahi-alias@.service
[Unit]
Description=Publish %I as alias for %H.local via mdns
After=network.target network-online.target
Requires=network-online.target
[Service]
Restart=always
RestartSec=3
Type=simple
ExecStart=/bin/bash -c "/usr/bin/avahi-publish -a -R %I $(hostname -I |cut -d' ' -f1)"
[Install]
WantedBy=multi-user.target
EOF
systemctl enable avahi-alias@"$(hostname)".local.service
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
  systemctl enable spectrogram_viewer.service
}

install_chart_viewer_service() {
  echo "Installing the chart_viewer.service"
  cat << EOF > /etc/systemd/system/chart_viewer.service
[Unit]
Description=BirdNET-Pi Chart Viewer Service
[Service]
Restart=always
RestartSec=300
Type=simple
User=pi
ExecStart=/usr/local/bin/daily_plot.py
[Install]
WantedBy=multi-user.target
EOF
  systemctl enable chart_viewer.service
}

install_gotty_logs() {
  sudo -u ${USER} ln -sf $(dirname ${my_dir})/templates/gotty \
    ${HOME}/.gotty
  sudo -u ${USER} ln -sf $(dirname ${my_dir})/templates/bashrc \
    ${HOME}/.bashrc
  cat << EOF > /etc/systemd/system/birdnet_log.service
[Unit]
Description=BirdNET Analysis Log
[Service]
Restart=on-failure
RestartSec=3
Type=simple
User=${USER}
Environment=TERM=xterm-256color
ExecStart=/usr/local/bin/gotty -p 8080 --title-format "BirdNET-Pi Log" birdnet_log.sh
[Install]
WantedBy=multi-user.target
EOF
  systemctl enable birdnet_log.service
  cat << EOF > /etc/systemd/system/web_terminal.service
[Unit]
Description=BirdNET-Pi Web Terminal
[Service]
Restart=on-failure
RestartSec=3
Type=simple
User=${USER}
Environment=TERM=xterm-256color
ExecStart=/usr/local/bin/gotty -w -p 8888 --title-format "BirdNET-Pi Terminal" bash
[Install]
WantedBy=multi-user.target
EOF
  systemctl enable web_terminal.service
}

configure_caddy_php() {
  echo "Configuring PHP for Caddy"
  sed -i 's/www-data/caddy/g' /etc/php/*/fpm/pool.d/www.conf
  systemctl restart php7\*-fpm.service
  echo "Adding Caddy sudoers rule"
  cat << EOF > /etc/sudoers.d/010_caddy-nopasswd
caddy ALL=(ALL) NOPASSWD: ALL
EOF
  chmod 0440 /etc/sudoers.d/010_caddy-nopasswd
}

install_phpsysinfo() {
  sudo -u ${USER} git clone https://github.com/phpsysinfo/phpsysinfo.git \
    ${HOME}/phpsysinfo
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
  systemctl enable icecast2.service
}

install_livestream_service() {
  cat << EOF > /etc/systemd/system/livestream.service
[Unit]
Description=BirdNET-Pi Live Stream
After=network-online.target
Requires=network-online.target
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
  systemctl enable livestream.service
}

install_cleanup_cron() {
  cat $(dirname ${my_dir})/templates/cleanup.cron >> /etc/crontab
}

install_services() {
  set_hostname
  update_etc_hosts
  set_login

  install_depends
  install_scripts
  install_Caddyfile
  install_avahi_aliases
  install_birdnet_analysis
  install_birdnet_server
  install_recording_service
  install_extraction_service
  install_pushed_notifications
  install_spectrogram_service
  install_chart_viewer_service
  install_gotty_logs
  install_phpsysinfo
  install_livestream_service
  install_cleanup_cron

  create_necessary_dirs
  generate_BirdDB
  configure_caddy_php
  config_icecast
  ${my_dir}/createdb.sh
}

if [ -f ${config_file} ];then 
  source ${config_file}
  install_services
else
  echo "Unable to find a configuration file. Please make sure that $config_file exists."
fi
