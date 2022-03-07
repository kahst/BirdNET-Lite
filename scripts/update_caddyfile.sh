#!/usr/bin/env bash
source /etc/birdnet/birdnet.conf
USER=pi
HOME=/home/pi
my_dir=/home/pi/BirdNET-Pi/scripts
set -x
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
  handle /By_Date/* {
    file_server browse
  }
  handle /Charts/* {
    file_server browse
  }
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
  handle /By_Date/* {
    file_server browse
  }
  handle /Charts/* {
    file_server browse
  }
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

sudo systemctl reload caddy
