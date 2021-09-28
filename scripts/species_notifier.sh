#!/usr/bin/env bash
# Sends a notification if a new species is detected
# set -x
trap 'rm -f $TMPFILE' SIGINT SIGHUP EXIT

source /etc/birdnet/birdnet.conf

TMPFILE=$(mktemp)

[ -f ${IDFILE} ] || touch ${IDFILE}
cat "${IDFILE}" > "${TMPFILE}"

/usr/local/bin/update_species.sh &> /dev/null

if ! diff "${IDFILE}" "${TMPFILE}"; then 
	SPECIES=("$(diff "${IDFILE}" "${TMPFILE}" \
    | awk '/</ {print $2" "$3}')")

  NOTIFICATION="New Species Detected: ${SPECIES[@]}"
  
  sudo systemctl restart birdnet_analysis && sleep 30
  sudo systemctl start extraction
  if [ ! -z ${PUSHED_APP_KEY} ];then
    curl -X POST -s \
      --form-string "app_key=${PUSHED_APP_KEY}" \
      --form-string "app_secret=${PUSHED_APP_SECRET}" \
      --form-string "target_type=app" \
      --form-string "content=${NOTIFICATION}" \
      https://api.pushed.co/1/push
  fi
fi
