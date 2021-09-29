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
SOFAR=$(($(wc -l ${IDFILE}| cut -d' ' -f1)/2))
echo "  -$a detections so far"
echo
echo "  -$SOFAR species identified so far"
echo
while read -r line;do
  echo "    | $line"
done < <(awk -v n=2 '1; NR % n == 0 {print ""}' ${IDFILE})
echo
echo -n "Listening since "${INSTALL_DATE}""
sleep 180
clear
done
