#!/bin/bash

#List audio devices and find input device name
input_device=$(LANG=C pacmd list-sources | grep name: | grep input | awk -F'<|>' '{print $2}')

#Add echo-cancel module code to the end of the file
echo ".ifexists module-echo-cancel.so
load-module module-echo-cancel source_master=$input_device aec_method=webrtc source_name=echocancel sink_name=echocancel1
set-default-source echocancel
set-default-sink echocancel1
.endif" | sudo tee -a /etc/pulse/default.pa >/dev/null

#Reload pulseaudio
pulseaudio -k

echo "Echo cancellation setup is complete."
