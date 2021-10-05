#!/usr/bin/env bash
# BirdNET Stats Page
trap 'setterm --cursor on' EXIT
source /etc/birdnet/birdnet.conf
setterm --cursor off

while true;do
cat << "EOF"
 .+"+.+"+.+"+.+"+.+"+.+"+.+"+.+"+.+"+.+"+.+"+.+"+.+"+.+"+.
(   _           ____  __                 _                 )
 ) |_)o.__||\ ||_ |__(_    __|_ _ ._ _  |_) _ ._  _ .__|_ (
(  |_)||(_|| \||_ |  __)\/_> |_(/_| | | | \(/_|_)(_)|  |_  )
 )                      /                     |           (
 "+.+"+.+"+.+"+.+"+.+"+.+"+.+"+.+"+.+"+.+"+.+"+.+"+.+"+.+"
EOF
if [ "$(find ${EXTRACTED} -name '*.wav' | wc -l)" -ge 1 ];then
  a=$( find "${EXTRACTED}" -name '*.wav' \
    | awk -F "/" '{print $NF}' \
    | cut -d'-' -f1 \
    | sort -n \
    | tail -n1 )
else
  a=0
fi
echo
if [ "${a}" -ge "1" ];then
  SOFAR=$(($(wc -l ${IDFILE}| cut -d' ' -f1)/2))
else
  SOFAR=0
fi
if [ $SOFAR = 1 ];then
  verbage=detection
else
  verbage=detections
fi
echo "  -$a $verbage so far"
echo
echo "  -$SOFAR species identified so far"
echo
if [ ${a} -ge 1 ];then
while read -r line;do
  SPECIES="$(echo "${line}" | awk -F: '/Common Name/ {print $2}')"
  SPECIES=${SPECIES// /_}
  SPECIES=${SPECIES/_}
  [ -z ${SPECIES} ] && continue
  DETECTIONS="$(ls -1 ${EXTRACTED}/By_Date/*/${SPECIES}| wc -l)"
  if [ ${DETECTIONS} = 1 ];then
    verbage=detection
  else
    verbage=detections
  fi
  echo -e "${DETECTIONS} $verbage for ${SPECIES//_/ }" | sort
done < ${IDFILE}
fi
echo
echo -n "Listening since "${INSTALL_DATE}""
sleep 20
clear
done
