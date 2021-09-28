#!/usr/bin/env bash
source /etc/birdnet/birdnet.conf

cd "${PROCESSED}" || exit 1
FIND_DATE=*$(date --date="2 days ago" "+%F")*
find . -name "${FIND_DATE}" -exec rm -rfv {} +
