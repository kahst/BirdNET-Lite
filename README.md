<h1 align="center">
  BirdNET-Pi <img src="https://img.shields.io/badge/version-0.10-blue" />
</h1>
<p align="center">
A realtime acoustic bird classification system for the Raspberry Pi 4B
</p>
<p align="center">
  <img src="https://user-images.githubusercontent.com/60325264/140656397-bf76bad4-f110-467c-897d-992ff0f96476.png" />
</p>
<p align="center">
Icon made by <a href="https://www.freepik.com" title="Freepik">Freepik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a>
</p>

## Introduction
The BirdNET-Pi project is built on the [TFLite version of BirdNET](https://github.com/kahst/BirdNET-Lite) by [**@kahst**](https://github.com/kahst) <a href="https://creativecommons.org/licenses/by-nc-sa/4.0/"><img src="https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-lightgrey.svg"></a> using [pre-built TFLite binaries](https://github.com/PINTO0309/TensorflowLite-bin) by [**@PINTO0309**](https://github.com/PINTO0309) . It is able to recognize bird sounds from a USB sound card in realtime.

Check out birds from around the world
- [My system in North Carolina, United States](https://birdnetpi.pmcgui.xyz)<br>
- [NatureStation.net in Johannesburg, South Africa](https://birds.naturestation.net)<br>

Currently listening in these countries . . . that I know of . . .
- The United States
- Germany
- South Africa
- France
- Austria
- Sweden

If your installation isn't in one of the countries listed above, please let me know so that I can add your country to the list! Let me know either in a GitHub issue, or [email me](mailto:mcguirepr89@gmail.com) and let me know where your BirdNET-Pi is listening.

## Features
* 24/7 recording and BirdNET-Lite analysis
* Web interface access to all data and logs
* Automatic extraction of detected data (creating audio clips of detected bird sounds)
* Spectrograms available for all extractions
* [BirdWeather](https://app.birdweather.com) integration (you will need to be issued a BirdWeather ID -- for now, request that from [@timsterc here](https://github.com/mcguirepr89/BirdNET-Pi/discussions/82))
* MariaDB integration
* NoMachine remote desktop (for personal use only)
* Live audio stream
* Integrated phpSysInfo
* New species mobile notifications from Pushed.co (for iOS users only)
* Localisation supported

## Requirements
* A Raspberry Pi 4B
* An SD Card with the 64-bit version of RaspiOS installed (Buster and Bullseye compatible) [(download the latest here)](https://downloads.raspberrypi.org/raspios_arm64/images/)
* A USB Microphone or Sound Card

## Installation
Headless installation guide available [HERE](https://github.com/mcguirepr89/BirdNET-Pi/wiki/%22Headless%22-installation-using-VNC)<br>
Pre-installeld beta image available for testing [HERE](https://github.com/mcguirepr89/BirdNET-Pi/discussions/11#discussioncomment-1751201)

The system can be installed with:
```
curl -s https://raw.githubusercontent.com/mcguirepr89/BirdNET-Pi/main/newinstaller.sh | bash
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

## Troubleshooting and Ideas
If you encounter any issues at any point, or have questions, comments, concerns, ideas, or want to share something, please take a look through the [open and closed issues](https://github.com/mcguirepr89/BirdNET-Pi/issues?q=is%3A+issue) and [the community discussions](https://github.com/mcguirepr89/BirdNET-Pi/discussions). PLEASE feel invited to [open a new issue](https://github.com/mcguirepr89/BirdNET-Pi/issues/new/choose) if you don't find the help you need. Likewise, please accept my invitation to [start a new discussion](https://github.com/mcguirepr89/BirdNET-Pi/discussions/new) to get a conversation started around your topic.

If you are not a GitHub user and need help, you can [email me](mailto:mcguirepr89@gmail.com), but I hope you will consider making a GitHub account so that your questions can be answered here for others as well. I expect this project will attract more bird-enthusiasts than Linux-enthusiasts, so please don't feel like any question is too _novice_ or, pardon the phrase, _stupid_ to ask. I want to help!

## Sharing
I hope that if you find BirdNET-Pi has been worth your time, you will share your setup, results, customizations, etc. [HERE](https://github.com/mcguirepr89/BirdNET-Pi/discussions/69) and will consider [making your installation public](https://github.com/mcguirepr89/BirdNET-Pi/wiki/Sharing-Your-BirdNET-Pi).

## ToDo, Notes, and Coming Soon 

### Internationalization:
The bird names are in English by default, but other localized versions are available. Please download the labels_l18n.zip file and replace the `model/labels.txt` with the corresponding language.

### Realtime Analysis Predictions View
The pre-built TFLite binaries for this project also support [the BirdNET-Demo](https://github.com/kahst/BirdNET-Demo), which I am currently testing for integration into the BirdNET-Pi. If you know anything about JavaScript and are willing to help, please let me know in the [Live Analysis discussion](https://github.com/mcguirepr89/BirdNET-Pi/discussions/24).

### Tips:
For some reason, the system seems to run more efficiently and the birds sound better when you [![Star on GitHub](https://img.shields.io/github/stars/mcguirepr89/BirdNET-Pi.svg?style=social)](https://github.com/mcguirepr89/BirdNET-Pi/stargazers) the project :)
