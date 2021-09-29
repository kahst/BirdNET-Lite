#!/usr/bin/env bash
# Runs BirdNET in virtual environment
#set -x
source /etc/birdnet/birdnet.conf
CUSTOM_LIST="/home/pi/BirdNET-Lite/custom_species_list.txt"
DAYS=(
"2 days ago"
"yesterday"
"today"
) 

# Create an array of the day's audio files
# Uses 1st argument: 
#   - {DIRECTORY}
get_files() {
  echo "Starting get_files() for ${1}"
  files=($( find ${1} -maxdepth 1 -name '*wav' \
  | sort \
  | awk -F "/" '{print $NF}' ))
  [ -n "${files[1]}" ] && echo "Files loaded"
}

# Move all files that have been analyzed already into newly created "Analyzed"
# directory
# Uses 1st argument:
#   - {DIRECTORY}
move_analyzed() {
  echo "Starting move_analyzed() for ${1}"
  for i in "${files[@]}";do 
  j="$(echo "${i}" | cut -d'.' -f1-2).csv" 
  if [ -f "${1}/${j}" ];then
    if [ ! -d "${1}-Analyzed" ];then
      mkdir -vvvvvvvp "${1}-Analyzed" && echo "'Analyzed' directory created"
    fi
    echo "Moving analyzed files to new directory"
    mv -vv "${1}/${i}" "${1}-Analyzed/"
    mv -vv "${1}/${j}" "${1}-Analyzed/"
  fi
done
}

# Run BirdNET analysis on the remaining WAVE files for the day
# Uses 1st and 2nd arguments:
#   - {DIRECTORY}
#   - {"today", "yesterday", "2 days ago",...}
run_analysis() {
  echo "Starting run_analysis() for ${1}"
  WEEK=$(date --date="${2}" +"%U")
  cd ${HOME}/BirdNET-Lite || exit 1
  for i in "${files[@]}";do
    if [ -f ${1}/${i} ] && [ ! -f ${CUSTOM_LIST} ];then
      python3 analyze.py \
        --i "${1}/${i}" \
        --o "${1}/${i}.csv" \
        --lat "${LATITUDE}" \
        --lon "${LONGITUDE}" \
        --week "${WEEK}" \
        --overlap "${OVERLAP}" \
        --min_conf "${CONFIDENCE}"
    elif [ -f ${1}/${i} ] && [ -f ${CUSTOM_LIST} ];then
      python3 analyze.py \
        --i "${1}/${i}" \
        --o "${1}/${i}.csv" \
        --lat "${LATITUDE}" \
        --lon "${LONGITUDE}" \
        --week "${WEEK}" \
        --overlap "${OVERLAP}" \
        --min_conf "${CONFIDENCE}" \
	--custom_list "${CUSTOM_LIST}"
   fi
  done
}

# The three main functions
# Requires 2 arguments:
#   - {DIRECTORY}
#   - {"today", "yesterday", "2 days ago",...}
run_birdnet() {
  echo "Starting run_birdnet() in \"${1}\" for \""${2}"\""
  sleep 1
  get_files "${1}"
  sleep 1
  move_analyzed "${1}"
  sleep 1
  run_analysis "${1}" "${2}"
}

if [ $(find ${RECS_DIR} -maxdepth 1 -name '*wav' | wc -l) -gt 0 ];then
  run_birdnet "${RECS_DIR}" "today"
fi

for i in ${!DAYS[@]};do
  DIRECTORY="$RECS_DIR/$(date --date="${DAYS[$i]}" "+%B-%Y/%d-%A")"
  if [ $(find ${DIRECTORY} -name '*wav' | wc -l) -gt 0 ];then
    run_birdnet "${DIRECTORY}" "${DAYS[$i]}"
  fi
done
