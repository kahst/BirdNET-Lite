#!/usr/bin/env bash
# Make sox spectrogram
source /etc/birdnet/birdnet.conf
analyzing_now="$(cat /home/pi/BirdNET-Pi/analyzing_now.txt)"
spectrogram_png=${EXTRACTED}/spectrogram.png
#sudo -u pi sox "${analyzing_now}" -n spectrogram -t "Currently Analyzing" -c "${analyzing_now}" -o "${spectrogram_png}"
sudo -u pi sox "${analyzing_now}" -n remix 1 rate 16k spectrogram -t "Currently Analyzing" -c "${analyzing_now}" -o "${spectrogram_png}"
