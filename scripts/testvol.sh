#!/bin/bash

# Connect to the database and extract the data
sqlite3 birds.db <<EOF | while IFS='|' read -r id mean_volume recorded_at; do
.mode csv
.headers off
SELECT id, mean_volume, strftime('%Y-%m-%d %H:00:00', recorded_at) AS hour
FROM volume;
done | {

  # Initialize variables for the first hour
  current_hour=""
  total_volume=0
  count=0

  # Loop through each row of data
  while IFS='|' read -r id mean_volume hour; do
    if [[ "$hour" != "$current_hour" ]]; then
      # If we've moved on to a new hour, output the previous hour's data
      if [[ "$current_hour" != "" ]]; then
        echo "$id|$((total_volume/count))|$current_hour"
      fi
      # Reset the variables for the new hour
      current_hour="$hour"
      total_volume="$mean_volume"
      count=1
    else
      # If we're still in the same hour, add to the total and count
      total_volume="$((total_volume + mean_volume))"
      count="$((count + 1))"
    fi
  done

  # Output the final hour's data
  echo "$id|$((total_volume/count))|$current_hour"
}

EOF
