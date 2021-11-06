#!/usr/bin/env -S sudo -E bash
# Setting date and time manually for non-network attached installatoins
most_recent="$(date +"%F %T" | tr '-' '/')"
new_time=$(whiptail --inputbox "Please set the correct time by changing these values. (24h clock format)" 20 60 "$most_recent" 3>&1 1>&2 2>&3)
date -s "$new_time"
