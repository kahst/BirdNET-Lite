#!/usr/bin/env bash
journalctl --no-hostname -o short -fu birdnet_analysis -u birdnet_server | sed "s/$(date "+%b %d ")//g"
