#!/usr/bin/env bash
# BirdNET Stats Page
trap 'setterm --cursor on && exit' EXIT
trap 'rm -f "${TMP_FILE}" && exit' EXIT
source /etc/birdnet/birdnet.conf
setterm --cursor off
TMP_FILE="$(mktemp)"

while true;do
  cat << 'EOF'                                                                
 ____                  __  __  __  _____  ______      ____
/\  _`\    __         /\ \/\ \/\ \/\  __\/\__  _\    /\  _`\   __
\ \ \_\ \ /\_\  _ __  \_\ \ \ `\\ \ \ \_/\/_/\ \/    \ \ \_\ \/\_\
 \ \  _ <'\/\ \/\`'__\/'_` \ \ , ` \ \  _\  \ \ \     \ \ ,__/\/\ \
  \ \ \_\ \\ \ \ \ \//\ \_\ \ \ \`\ \ \ \/__ \ \ \     \ \ \/  \ \ \
   \ \____/ \ \_\ \_\\ \___,_\ \_\ \_\ \____/ \ \_\     \ \_\   \ \_\
    \/___/   \/_/\/_/ \/__,_ /\/_/\/_/\/___/   \/_/      \/_/    \/_/

EOF
  if [ "$(find ${EXTRACTED} -name '*.wav' | wc -l)" -ge 1 ] &> /dev/null;then
    a=$(find "${EXTRACTED}/By_Date" -type f -name '*.wav' | wc -l)
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
  if [ "${SOFAR}" -ge "1" ];then
    MOST_RECENT="$(find ${EXTRACTED}/By_Date/$(date +%Y-%m-%d) \
      | sort -t"%" -rk2 \
      | head -n1 \
      | cut -d'/' -f8)"
    AT_TIME="$(find ${EXTRACTED}/By_Date/$(date +%Y-%m-%d) \
      | sort -t"%" -rk2 \
      | head -n1 \
      | rev \
      | cut -d'-' -f1 \
      | rev \
      | cut -d'.' -f1)"
    echo "  -Most recent species detection: ${MOST_RECENT//_/ } at ${AT_TIME}" 
    echo
  fi
  if [ ${a} -ge 1 ];then
    while read -r line;do
      # Get species name
      SPECIES="$(echo "${line}" | awk -F: '/Common Name/ {print $2}')"
      SPECIES="${SPECIES// /_}"
      SPECIES="$(echo ${SPECIES/_} | tr -d "'")"
      [ -z ${SPECIES} ] && continue
    
      # Get all detection files
      ALL_DETECTION_FILES="$(find ${EXTRACTED}/By_Date/*/${SPECIES} -name '*.wav')"
      ALL_DETECTION_FILES="$(echo ${ALL_DETECTION_FILES[@]} | tr ' ' '\n')"
    
      # Parse highest confidence score
      MAX_SCORE="$(echo "${ALL_DETECTION_FILES}"| awk -F% '{print $1}')"
      MAX_SCORE="$(echo "${MAX_SCORE[@]}" | rev |cut -d"-" -f1|rev | sort -r | head -n1)"
    
      # Set noun-plurality agreement for grammar
      DETECTIONS="$(($(ls -1 ${EXTRACTED}/By_Date/*/${SPECIES} | wc -l)/2))"
      if [ ${DETECTIONS} = 1 ];then
        verbage=detection
      else
        verbage=detections
      fi
    
      # Write results to temporary file
      echo "${DETECTIONS} $verbage for ${SPECIES//_/ } | max conf ${MAX_SCORE}%"
    done < "${IDFILE}" > ${TMP_FILE}
    
    # Print temporary file sorted by # of detections
    sort -rk1 -h "${TMP_FILE}"
  fi
  echo
  echo -n "Listening since "${INSTALL_DATE}""
  sleep 20
  clear
done
