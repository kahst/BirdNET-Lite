#!/usr/bin/env bash
# This script removes all data that has been collected. It is tantamount to
# starting all data-collection from scratch. Only run this if you are sure
# you are okay will losing all the data that you've collected and processed
# so far.
source /etc/birdnet/birdnet.conf

echo "
This script removes all data that has been collected. It is tantamount to
starting all data-collection from scratch. Only run this if you are sure
you are okay with losing all the data that you've collected and processed
so far.

"
read -n1 -p "Are you sure you want to wipe away ALL data?" YN
echo
while true; do
  case $YN in
    [Yy]) break;;
    *) echo "Exiting since you didn't answer with Y or y." && exit;;
  esac
done
echo "Removing all data . . . "
sudo rm -drf "${RECS_DIR}"
rm "${IDFILE}"

echo "Recreating necessary directories"
[ -d ${RECS_DIR} ] || mkdir -p ${RECS_DIR}
[ -d ${EXTRACTED} ] || mkdir -p ${EXTRACTED}
[ -d ${EXTRACTED}/By_Date ] || mkdir -p ${EXTRACTED}/By_Date
[ -d ${EXTRACTED}/By_Common_Name ] || mkdir -p ${EXTRACTED}/By_Common_Name
[ -d ${EXTRACTED}/By_Scientific_Name ] || mkdir -p ${EXTRACTED}/By_Scientific_Name
[ -d ${PROCESSED} ] || mkdir -p ${PROCESSED}

cp ~/BirdNET-Lite/templates/index.html ${EXTRACTED}/
