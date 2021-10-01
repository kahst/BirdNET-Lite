#!/usr/bin/env bash
# Runs BirdNET-Lite
#set -x
source /etc/birdnet/birdnet.conf
CUSTOM_LIST="/home/pi/BirdNET-Lite/custom_species_list.txt"

# Create an array of the audio files
# Takes one argument:
#   - {DIRECTORY}
get_files() {
  echo "get_files() for ${1:19}"
  files=($( find ${1} -maxdepth 1 -name '*wav' \
  | sort -r \
  | awk -F "/" '{print $NF}' ))
  [ -n "${files[1]}" ] && echo "Files loaded"
}

# Move all files that have been analyzed already into newly created "Analyzed"
# directory
# Takes one argument:
#   - {DIRECTORY}
move_analyzed() {
  echo "Starting move_analyzed() for ${1:19}"
  for i in "${files[@]}";do 
    j="${i}.csv" 
    if [ -f "${1}/${j}" ];then
      if [ ! -d "${1}-Analyzed" ];then
        mkdir -p "${1}-Analyzed" && echo "'Analyzed' directory created"
      fi
      echo "Moving analyzed files to new directory"
      mv "${1}/${i}" "${1}-Analyzed/"
      mv "${1}/${j}" "${1}-Analyzed/"
    fi
  done
}

# Run BirdNET-Lite on the WAVE files from get_files()
# Uses one argument:
#   - {DIRECTORY}
run_analysis() {
  echo "Starting run_analysis() for ${1:19}"
  WEEK=$(date +"%U")
  cd ${HOME}/BirdNET-Lite || exit 1
  for i in "${files[@]}";do

    set -x
    FILE_LENGTH="$(ffmpeg -i ${1}/${i} 2>&1 \
      | awk -F. '/Duration/ {print $1}' \
      | cut -d':' -f3-4)"
    [ -z ${RECORDING_LENGTH} ] && RECORDING_LENGTH=12
    [ ${RECORDING_LENGTH} == "60" ] && RECORDING_LENGTH=01:00
    [ "${FILE_LENGTH}" == "00:${RECORDING_LENGTH}" ] || continue

    if [ -f ${1}/${i} ] && [ ! -f ${CUSTOM_LIST} ];then
      python3 analyze.py \
        --i "${1}/${i}" \
        --o "${1}/${i}.csv" \
        --lat "${LATITUDE}" \
        --lon "${LONGITUDE}" \
        --week "${WEEK}" \
        --overlap "${OVERLAP}" \
	--sensitivity "${SENSITIVITY}" \
        --min_conf "${CONFIDENCE}"
      set +x
    elif [ -f ${1}/${i} ] && [ -f ${CUSTOM_LIST} ];then
      set -x
      python3 analyze.py \
        --i "${1}/${i}" \
        --o "${1}/${i}.csv" \
        --lat "${LATITUDE}" \
        --lon "${LONGITUDE}" \
        --week "${WEEK}" \
        --overlap "${OVERLAP}" \
	--sensitivity "${SENSITIVITY}" \
        --min_conf "${CONFIDENCE}" \
	--custom_list "${CUSTOM_LIST}"
      set +x
   fi
  done
}

# The three main functions
# Takes one argument:
#   - {DIRECTORY}
run_birdnet() {
  echo "Starting run_birdnet() for \"${1:19}\""
  get_files "${1}"
  move_analyzed "${1}"
  run_analysis "${1}"
}

if [ $(find ${RECS_DIR} -maxdepth 1 -name '*wav' | wc -l) -gt 0 ];then
  run_birdnet "${RECS_DIR}"
fi

DIRECTORY="$RECS_DIR/$(date "+%B-%Y/%d-%A")"
if [ $(find ${DIRECTORY} -name '*wav' | wc -l) -gt 0 ];then
  run_birdnet "${DIRECTORY}"
fi
