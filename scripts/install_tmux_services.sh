#!/usr/bin/env bash
# This script installs a web-based terminal @ http://birdterminal.local
# ONLY run this additional script if you trust everyone on your 
# local network completely as the credentials are sent WITHOUT 
# any SSL/TLS encryption. For a secure remote connection to your 
# BirdNET-Pi command line, consider enabling SSH on the 
# Raspberry Pi and using another Linux machine or an SSH 
# client software, or you can alternately add the 'tls internal'
# directive to the Caddyfile to add a self-signed certificate for TLS/SSL
# encryption. For remote desktop access, NoMachine can be installed.
source /etc/birdnet/birdnet.conf
my_dir=$(realpath $(dirname $0))

# Install tmux from version control
install_tmux() {
  DEPENDS=( 
  automake
  autoconf
  libevent-2*
  libevent-dev 
  ncurses-bin
  ncurses-base
  ncurses-term
  libncurses-dev 
  build-essential 
  bison
  pkg-config 
  gcc
  )

  if which tmux &>/dev/null; then
    echo "tmux is installed"
  else

    sudo apt update && sudo apt -y install "${DEPENDS[@]}"

    cd ${HOME} && git clone https://github.com/tmux/tmux.git
    cd tmux
    sh autogen.sh
    ./configure && make && sudo make install
    cd && rm -drf ./tmux
    sudo ln -sf "$(dirname ${my_dir})/templates/tmux.conf" /etc/tmux.conf
  fi
}

install_web_terminal() {
  cat << EOF | sudo tee /etc/systemd/system/birdterminal.service
[Unit]
Description=A BirdNET-Pi Web Terminal

[Service]
Restart=on-failure
RestartSec=3
Type=simple
User=${BIRDNET_USER}
Environment=TERM=xterm-256color
ExecStart=/usr/local/bin/gotty -w --title-format "Login!" -p 9111 tmux new -A -s Login sudo bash -c login

[Install]
WantedBy=multi-user.target
EOF
  HASHWORD="$(caddy hash-password -plaintext "${CADDY_PWD}")"
  cat << EOF | sudo tee -a /etc/caddy/Caddyfile
http://birdterminal.local {
  reverse_proxy localhost:9111
  basicauth {
    birdnet "${HASHWORD}"
  }
}
EOF
  sudo systemctl enable --now birdterminal.service
  sudo systemctl enable --now avahi-alias@birdterminal.local.service
  sudo systemctl restart caddy
}
install_tmux
install_web_terminal
