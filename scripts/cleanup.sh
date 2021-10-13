#!/usr/bin/env bash
source /etc/birdnet/birdnet.conf

cd "${PROCESSED}" || exit 1
empties=($(find ${PROCESSED} -size 57c))
for i in "${empties[@]}";do
  rm -f "${i}"
  rm -f "${i/.csv/}"
done

if [[ "$(find ${PROCESSED} | wc -l)" -ge 100 ]];then
  ls -1t . | tail -n +100 | xargs -r rm -vv
fi
