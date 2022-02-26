#!/usr/bin/env bash
# testing getting images dynamically
set -x
first_call=$(lynx -accept-all-cookies -dump -listonly "https://commons.wikimedia.org/w/index.php?search=${1//_/+}&title=Special:MediaSearch&go=Go&type=image")
lynx -dump -listonly -accept-all-cookies\
  $(echo "$first_call" \
    | sed '11q;d' \
    | awk '{print $2}' ) \
    | awk '/thumb/ {print $2}' \
    | head -1 \
    | xargs echo -n
