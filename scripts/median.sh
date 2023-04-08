#!/bin/bash

# Set variables
db_path=~/BirdNET-Pi/scripts/birds.db
dir_path=~/BirdSongs/Extracted/By_Date

# Calculate the average count of detections for each species
avg_counts=$(sqlite3 $db_path "SELECT Com_Name || '|' || AVG(count) FROM (SELECT Com_Name, COUNT(*) as count FROM detections GROUP BY Com_Name) GROUP BY Com_Name ORDER BY AVG(count)")

# Get the species that are in the bottom 10% of this distribution by count
bottom_10=$(echo "$avg_counts" | awk -F'|' -v n="$(echo "$avg_counts" | wc -l)" 'NR <= n * 0.1 {print $1}')

# Remove all directories from $dir_path that aren't in the bottom 10% rarest
find $dir_path -type d | while read dir; do
    dir_name=$(basename "$dir")
    if ! echo "$bottom_10" | grep -q "$dir_name"; then
        #rm -r "$dir"
        echo "REMOVING: $dir"
    fi
done
