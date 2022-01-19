#!/usr/bin/env bash
set -x
used="$(df -h / | tail -n1 | awk '{print $5}')"

if [ "${used//%}" -ge 95 ]; then
  source /etc/birdnet/birdnet.conf

  case $FULL_DISK in
    0) echo "Removing oldest data"
       rm -drfv "$(find ${EXTRACTED}/By_Date/* -maxdepth 1 -type d -prune \
         | sort -r | tail -n1)";;
    *) echo "Stopping Core Services"
       /usr/local/bin/stop_core_services.sh;;
  esac
fi
