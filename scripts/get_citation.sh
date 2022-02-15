#!/usr/bin/env bash
# testing getting images dynamically
first_call=$(lynx -nonumbers -dump -listonly "https://commons.wikimedia.org/w/index.php?search=${1//_/+}&title=Special:MediaSearch&go=Go&type=image" | sed '11q;d')

lynx -dump -nonumbers "$(lynx -dump $first_call \
  | awk '/CiteThisPage/ {print $2}')" \
  | grep -A10 'Chicago style' \
  | grep -A10 -e '^$' \
  | grep -B10 'CBE' \
  | grep -B10 -e '^$'
