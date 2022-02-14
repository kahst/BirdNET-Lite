#!/usr/bin/env bash
# testing getting images dynamically
first_call=$(lynx -dump -listonly "https://commons.wikimedia.org/w/index.php?search=${1//_/+}&title=Special:MediaSearch&go=Go&type=image")
lynx -dump -nonumbers $(lynx -dump \
  $(echo "$first_call" \
    | sed '11q;d' \
    | awk '{print $2}' ) \
    | awk -F'. ' '/Cite/ {print $2}' \
    | xargs echo -n) 2>&1 | grep -A10 'Chicago style' | grep -A10 -e '^$' | grep -B10 'CBE' | grep -B10 -e '^$'
