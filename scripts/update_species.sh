#!/usr/bin/env bash
# Update the species list
#set -x
trap 'rm -f "$TMPFILE"' SIGINT SIGTERM EXIT

source /etc/birdnet/birdnet.conf

TMPFILE=$(mktemp) || exit 1

[ -f ${IDFILE} ] || touch ${IDFILE}

if [ $(find ${PROCESSED} -name '*csv' | wc -l) -ge 1 ];then
  sort $(find ${PROCESSED} ${ANALYZED} ${EXTRACTED} -name '*csv') \
    | awk -F\; '!/Scientific/ {print"Common Name: " $4 "\nScientific Name: " $3""}' \
    | uniq > "$TMPFILE"
  cat "$TMPFILE" | awk '!visited[$0]++' > "$IDFILE"
  cat "$IDFILE"
else
  cat "$IDFILE"
fi
