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

  if [ -s $HOME/BirdNET-Pi/apprise.txt ];then
    $HOME/BirdNET-Pi/birdnet/bin/apprise -vv -t 'New Species Detected' -b "${NOTIFICATION}" --config=$HOME/BirdNET-Pi/apprise.txt
  fi
fi

