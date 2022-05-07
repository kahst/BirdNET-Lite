#!/usr/bin/env bash
# Sends a notification if a new species is detected
trap 'rm -f $lastcheck' EXIT
source /etc/birdnet/birdnet.conf

lastcheck="$(mktemp)"

[ -f ${IDFILE} ] || touch ${IDFILE}

cp ${IDFILE} ${lastcheck}

$HOME/BirdNET-Pi/scripts/update_species.sh

if ! diff ${IDFILE} ${lastcheck} &> /dev/null;then
  SPECIES=$(diff ${IDFILE} ${lastcheck} \
    | tail -n+2 |\
    awk '{for(i=2;i<=NF;++i)printf $i""FS ; print ""}' )

  NOTIFICATION="New Species Detection: "${SPECIES[@]}""
  echo "Sending the following notification:
${NOTIFICATION}"

  if [ ! -z ${NOTIFY_RUN_CHANNEL_ID} ];then
    curl https://notify.run/${NOTIFY_RUN_CHANNEL_ID} -d ${NOTIFICATION}
  fi

  if [ ! -z ${PUSHED_APP_KEY} ];then
    curl -X POST \
      --form-string "app_key=${PUSHED_APP_KEY}" \
      --form-string "app_secret=${PUSHED_APP_SECRET}" \
      --form-string "target_type=app" \
      --form-string "content=${NOTIFICATION}" \
      https://api.pushed.co/1/push
  fi

  if [ ! -s $HOME/BirdNET-Pi/apprise.txt ];then
    $HOME/BirdNET-Pi/birdnet/bin/apprise -vv -t 'New Species Detected' -b "${NOTIFICATION}" --config=$HOME/BirdNET-Pi/apprise.txt
  fi
fi

