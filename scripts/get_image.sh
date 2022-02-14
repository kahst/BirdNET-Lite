#!/usr/bin/env bash
# testing getting images dynamically
first_call=$(lynx -dump -listonly "https://commons.wikimedia.org/w/index.php?search=${1//_/+}&title=Special:MediaSearch&go=Go&type=image")
lynx -dump -listonly \
  $(echo "$first_call" \
    | sed '11q;d' \
    | awk '{print $2}' ) \
    | awk '/thumb/ {print $2}' \
    | head -1 \
    | xargs echo -n
