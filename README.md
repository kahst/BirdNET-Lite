# BirdNET-Pi
![version](https://img.shields.io/badge/version-0.4-blue)

A realtime acoustic bird classification system for the Raspberry Pi 4B

## Introduction
The BirdNET-Pi project is built on the [TFLite version of BirdNET](https://github.com/kahst/BirdNET-Lite). It is able to recognize bird sounds from a USB sound card in realtime. 

[Check out my live system](https://birdnetpi.pmcgui.xyz)<br>
[and NatureStation.net](https://birds.naturestation.net)

## Features
* 24/7 recording and BirdNET-Lite analysis
* Web interface access to all data and logs
* Automatic extraction of detected data (creating audio clips of detected bird sounds)
* Spectrograms available for all extractions
* MariaDB integration
* NoMachine remote desktop (for personal use only)
* Live audio stream
* Integrated phpSysInfo
* New species mobile notifications from Pushed.co (for iOS users only)
* Localisation supported

## Requirements
* A Raspberry Pi 4B
* An SD Card with the 64-bit version of RaspiOS installed [(download the latest here)](https://downloads.raspberrypi.org/raspios_arm64/images/)
* A USB Microphone or Sound Card

## Installation
[Headless installation guide available here](https://github.com/mcguirepr89/BirdNET-Pi/wiki/%22Headless%22-installation-using-VNC)

The system can be installed with:
```
curl -s https://raw.githubusercontent.com/mcguirepr89/BirdNET-Pi/newinstaller/scripts/birdnet-pi-config | sudo -E bash
```

The script first enables and configures the zRAM kernel module for swapping, and reboots. 
After the reboot, the configuration file is opened for editing. Here, you will input your latitude and longitude and will set a few passwords to protect your Pi. When the installation has finished, the BirdNET-Pi is ready to start collecting and analyzing data on the next boot. 

If you have trouble with the installation script, you can cancel and rerun the installer:
```
~/BirdNET-Pi/Birders_Guide_Installer.sh
```

## Access
The BirdNET-Pi system can be accessed from any web browser on the same network:
- http://birdnetpi.local

#### Access Credentials:
- Username:`birdnet`
- Password: The "CADDY_PWD" password set during installation 

## Uninstallation
```
/usr/local/bin/uninstall.sh && cd ~ && rm -drf BirdNET-Pi
```

## ToDo, Notes, and Comming Soon 

### Internationalization:
The bird names are in English by default, but other localized versions are available. Please download the labels_l18n.zip file and replace the `model/labels.txt` with the corresponding language.

### Realtime Analysis Predictions View
The pre-built TFLite binaries for this project also support [the BirdNET-Demo](https://github.com/kahst/BirdNET-Demo), which I am currently testing for integration into the BirdNET-Pi. If you know anything about JavaScript and are willing to help, please let me know in the [Live Analysis discussion](https://github.com/mcguirepr89/BirdNET-Pi/discussions/24).

### Tips:
You can try to overclock your Pi by placing the following in your `/boot/config.txt` file:

```
over_voltage=6
arm_freq=1750
```
Be sure the Pi is adequately cooled and powered.
