#!/usr/bin/env bash
# Make sox spectrogram
source /etc/birdnet/birdnet.conf
analyzing_now="$(cat /home/pi/BirdNET-Lite/analyzing_now.txt)"
spectrogram_png=${EXTRACTED}/spectrogram.png
sudo -u pi sox "${analyzing_now}" -n spectrogram -o "${spectrogram_png}"
