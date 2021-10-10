#!/usr/bin/env bash
# test to create a database from bash
mysql << 'EOF'
drop birds;
CREATE DATABASE IF NOT EXISTS birds;

USE birds;

CREATE TABLE IF NOT EXISTS detections (
  Date DATE,
  Time TIME,
  Sci_Name VARCHAR(100) NOT NULL,
  Com_Name VARCHAR(100) NOT NULL,
  Confidence FLOAT,
  Lat FLOAT,
  Lon FLOAT,
  Cutoff FLOAT,
  Week INT,
  Sens FLOAT,
  Overlap FLOAT);

LOAD DATA LOCAL INFILE '/home/pi/Birding-Pi/BirdDB.txt'
INTO TABLE detections
FIELDS TERMINATED BY ';';
SELECT * FROM detections;
exit
EOF

