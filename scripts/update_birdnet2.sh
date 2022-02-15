#!/usr/bin/env bash
# Second stage of update
USER=pi
birdnet_conf=/home/pi/BirdNET-Pi/birdnet.conf
my_dir=${HOME}/BirdNET-Pi/scripts

# Stage 1 updates the services
sudo ${my_dir}/update_services.sh

# Stage 1.5: adding new birdnet.conf entries
if ! grep FULL_DISK ${birdnet_conf} &> /dev/null;then
 cat << EOF >> ${birdnet_conf}

## FULL_DISK can be set to configure how the system reacts to a full disk
## purge = Remove the oldest day's worth of recordings
## keep = Keep all data and `stop_core_services.sh`

FULL_DISK=purge
EOF
fi

if ! grep AUDIOFMT ${birdnet_conf} &> /dev/null;then
  cat<< EOF >> ${birdnet_conf}
## AUDIOFMT set the audio format that sox should use for the extractions.
## The default is mp3. Available formats are: 8svx aif aifc aiff aiffc al amb 
## amr-nb amr-wb anb au avr awb caf cdda cdr cvs cvsd cvu dat dvms f32 f4 f64 f8
## fap flac fssd gsm gsrt hcom htk ima ircam la lpc lpc10 lu mat mat4 mat5 maud 
## mp2 mp3 nist ogg paf prc pvf raw s1 s16 s2 s24 s3 s32 s4 s8 sb sd2 sds sf sl 
## sln smp snd sndfile sndr sndt sou sox sph sw txw u1 u16 u2 u24 u3 u32 u4 u8 
## ub ul uw vms voc vorbis vox w64 wav wavpcm wv wve xa xi
## Note: Most have not been tested.

AUDIOFMT=mp3
EOF
fi

sudo -u${USER} sed -i 's/EXTRACTIONLOG_URL/WEBTERMINAL_URL/g' ${birdnetconf}

# Replace Backup labels.txt
sudo -u${USER} cp -f ~/BirdNET-Pi/model/labels.txt.bak ~/BirdNET-Pi/model/labels.txt

# Stage 2 restarts the services
sudo systemctl daemon-reload
sudo systemctl stop birdnet_recording.service
sudo rm -rf ${RECS_DIR}/$(date +%B-%Y/%d-%A)/*
services=(web_terminal.service
spectrogram_viewer.service
pushed_notifications.service
livestream.service
icecast2.service
extraction.timer
extraction.service
chart_viewer.service
birdnet_recording.service
birdnet_log.service)

for i in  "${services[@]}";do
sudo systemctl restart "${i}"
done
sudo systemctl reload caddy
sudo systemctl restart php7.4-fpm.service
