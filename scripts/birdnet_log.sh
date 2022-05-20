#!/usr/bin/env bash
journalctl --no-hostname -q -o short -fu birdnet_analysis -ubirdnet_server -uextraction | sed "s/$(date "+%b %d ")//g;s/${HOME//\//\\/}\///g;/Line/d;/systemd/d;s/ .*\[.*\]: /---/"
