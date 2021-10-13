#!/usr/bin/env bash
# Runs BirdNET-Lite
#set -x
source /etc/birdnet/birdnet.conf
# Document this run's birdnet.conf settings
# Make a temporary file to compare the current birdnet.conf with
# the birdnet.conf as it was the last time this script was called
my_dir=$(realpath $(dirname $0))
make_thisrun() {
  sleep .4
  awk '!/#/ && !/^$/ {print}' /etc/birdnet/birdnet.conf \
    > >(tee "${THIS_RUN}")
  sleep .5
}
make_thisrun &> /dev/null
if ! diff ${LAST_RUN} ${THIS_RUN};then
  echo "The birdnet.conf file has changed"
  echo "Reloading services"
  cat ${THIS_RUN} > ${LAST_RUN}
  until restart_services.sh;do
    sleep 1
  done
fi

CUSTOM_LIST="/home/pi/BirdNET-Pi/custom_species_list.txt"

# Create an array of the audio files
# Takes one argument:
#   - {DIRECTORY}
get_files() {
  echo "get_files() for ${1:19}"
  files=($( find ${1} -maxdepth 1 -name '*wav' \
  | sort \
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
  sleep .5
  echo "Starting run_analysis() for ${1:19}"


  ### TESTING NEW WEEK CALCULATION
  WEEK_OF_YEAR="$(echo "($(date +%m)-1) * 4" | bc -l)"
  DAY_OF_MONTH="$(date +%d)"
  if [ ${DAY_OF_MONTH} -le 7 ];then
    WEEK="$(echo "${WEEK_OF_YEAR} + 1" |bc -l)"
  elif [ ${DAY_OF_MONTH} -le 14 ];then
    WEEK="$(echo "${WEEK_OF_YEAR} + 2" |bc -l)"
  elif [ ${DAY_OF_MONTH} -le 21 ];then
    WEEK="$(echo "${WEEK_OF_YEAR} + 3" |bc -l)"
  elif [ ${DAY_OF_MONTH} -ge 22 ];then
    WEEK="$(echo "${WEEK_OF_YEAR} + 4" |bc -l)"
  fi

  cd ${HOME}/BirdNET-Pi || exit 1
  for i in "${files[@]}";do
    echo "${1}/${i}" > ./analyzing_now.txt
    [ -z ${RECORDING_LENGTH} ] && RECORDING_LENGTH=15
    [ ${RECORDING_LENGTH} == "60" ] && RECORDING_LENGTH=01:00
    FILE_LENGTH="$(ffmpeg -i ${1}/${i} 2>&1 | awk -F. '/Duration/ {print $1}' | cut -d':' -f3-4)"
    [ -z $FILE_LENGTH ] && sleep 3 && continue
    echo "RECORDING_LENGTH set to ${RECORDING_LENGTH}"
    a=1
    if [ "${RECORDING_LENGTH}" == "01:00" ];then
      until [ "$(ffmpeg -i ${1}/${i} 2>&1 | awk -F. '/Duration/ {print $1}' | cut -d':' -f3-4)" == "${RECORDING_LENGTH}" ];do
        sleep 1
	[ $a -ge 60 ] && sudo rm -f ${1}/${i} && break
	a=$((a+1))
      done	
    else 
      until [ "$(ffmpeg -i ${1}/${i} 2>&1 | awk -F. '/Duration/ {print $1}' | cut -d':' -f3-4)" == "00:${RECORDING_LENGTH}" ];do
        sleep 1
	[ $a -ge ${RECORDING_LENGTH} ] && sudo rm -f ${1}/${i} && break
	a=$((a+1))
      done
    fi

    if [ -f ${1}/${i} ] && [ ! -f ${CUSTOM_LIST} ];then
      echo "python3 analyze.py \
--i "${1}/${i}" \
--o "${1}/${i}.csv" \
--lat "${LATITUDE}" \
--lon "${LONGITUDE}" \
--week "${WEEK}" \
--overlap "${OVERLAP}" \
--sensitivity "${SENSITIVITY}" \
--min_conf "${CONFIDENCE}""
      "${VENV}"/bin/python analyze.py \
        --i "${1}/${i}" \
        --o "${1}/${i}.csv" \
        --lat "${LATITUDE}" \
        --lon "${LONGITUDE}" \
        --week "${WEEK}" \
        --overlap "${OVERLAP}" \
	--sensitivity "${SENSITIVITY}" \
        --min_conf "${CONFIDENCE}"
    elif [ -f ${1}/${i} ] && [ -f ${CUSTOM_LIST} ];then
      echo "python3 analyze.py \
--i "${1}/${i}" \
--o "${1}/${i}.csv" \
--lat "${LATITUDE}" \
--lon "${LONGITUDE}" \
--week "${WEEK}" \
--overlap "${OVERLAP}" \
--sensitivity "${SENSITIVITY}" \
--min_conf "${CONFIDENCE}" \
--custom_list "${CUSTOM_LIST}""
      "${VENV}"/bin/python analyze.py \
        --i "${1}/${i}" \
        --o "${1}/${i}.csv" \
        --lat "${LATITUDE}" \
        --lon "${LONGITUDE}" \
        --week "${WEEK}" \
        --overlap "${OVERLAP}" \
	--sensitivity "${SENSITIVITY}" \
        --min_conf "${CONFIDENCE}" \
	--custom_list "${CUSTOM_LIST}"
   fi
  done
}

# The three main functions
# Takes one argument:
#   - {DIRECTORY}
run_birdnet() {
  echo "Starting run_birdnet() for ${1:19}"
  get_files "${1}"
  move_analyzed "${1}"
  run_analysis "${1}"
}

date

if [ $(find ${RECS_DIR} -maxdepth 1 -name '*wav' | wc -l) -gt 0 ];then
  run_birdnet "${RECS_DIR}"
fi

DIRECTORY="$RECS_DIR/$(date "+%B-%Y/%d-%A")"
if [ $(find ${DIRECTORY} -name '*wav' | wc -l) -gt 0 ];then
  run_birdnet "${DIRECTORY}"
fi
