#!/bin/bash

# Loop through each entry in birds.db
sqlite3 ~/BirdNET-Pi/scripts/birds.db "SELECT DISTINCT Sci_Name FROM detections" | while read -r row; do
    # Get Sci_Name from current row
sci_name=$(echo "$row" | cut -d '|' -f 3)

    # Look up ComName for Sci_Name
    com_name=$(grep -iE "$sci_name.*" ~/BirdNET-Pi/model/labels.txt | cut -d '_' -f 2-)
	
    if [[ -n "$com_name" ]]; then
    	# Update Com_Name in birds.db
    	sqlite3 ~/BirdNET-Pi/scripts/birds.db "UPDATE detections SET Com_Name='$com_name' WHERE Sci_Name='$sci_name'"
    	echo "SETTING "$com_name" WHERE sciname="$sci_name"\n"
    fi
done
