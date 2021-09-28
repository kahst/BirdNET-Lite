#!/usr/bin/env bash
# Pretty date suffixes
TODAY="$(date +%e)"

if [[ $TODAY == ' 1' ]] || [[ $TODAY == 21 ]] || [[ $TODAY == 31 ]]; then
  SUFFIX="st"
elif [[ $TODAY == ' 2' ]] || [[ $TODAY == 22 ]];then
  SUFFIX="nd"
elif [[ $TODAY == ' 3' ]] || [[ $TODAY == 23 ]];then
  SUFFIX="rd"
else
  SUFFIX="th"
fi
echo "$SUFFIX"
