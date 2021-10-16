#!/usr/bin/env bash
# Sends a notification if a new species is detected
#set -x
trap 'rm -f $lastcheck' EXIT
source /etc/birdnet/birdnet.conf

lastcheck="$(mktemp)"

cp ${IDFILE} ${lastcheck}

/home/pi/BirdNET-Pi/scripts/new_update_species.sh

if ! diff ${IDFILE} ${lastcheck} &> /dev/null;then
  SPECIES=$(diff ${IDFILE} ${lastcheck} \
    | tail -n+2 |\
    awk '{for(i=2;i<=NF;++i)printf $i""FS ; print ""}' )

  NOTIFICATION="New Species Detection: "${SPECIES[@]}""
  echo "Sending the following notification:
${NOTIFICATION}"

  if [ ! -z ${PUSHED_APP_KEY} ];then
    curl -X POST \
      --form-string "app_key=${PUSHED_APP_KEY}" \
      --form-string "app_secret=${PUSHED_APP_SECRET}" \
      --form-string "target_type=app" \
      --form-string "content=${NOTIFICATION}" \
      https://api.pushed.co/1/push
  fi
fi

