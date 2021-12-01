#!/usr/bin/env bash
set -x
used="$(df -h / | tail -n1 | awk '{print $5}')"

if [ "${used//%}" -gt 95 ]; then
  echo "Stopping Core Services"
  /usr/local/bin/stop_core_services.sh
fi
