<h1 align="center">
  BirdNET-Pi <img src="https://img.shields.io/badge/Version-0.13-pink" />
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
BirdNET-Pi is built on the [TFLite version of BirdNET](https://github.com/kahst/BirdNET-Lite) by [**@kahst**](https://github.com/kahst) <a href="https://creativecommons.org/licenses/by-nc-sa/4.0/"><img src="https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-lightgrey.svg"></a> using [pre-built TFLite binaries](https://github.com/PINTO0309/TensorflowLite-bin) by [**@PINTO0309**](https://github.com/PINTO0309) . It is able to recognize bird sounds from a USB sound card in realtime and share its data with the rest of the world.

Check out birds from around the world
- [BirdWeather](https://app.birdweather.com)<br>
- [My test system in Virginia, United States](https://virginia.birdnetpi.com)<br>
- [NatureStation.net in Johannesburg, South Africa](https://joburg.birdnetpi.com)<br>
- [BirdNET-Pi in Öringe, Tyresö, Sweden](https://tyreso.birdnetpi.com)<br>
- [Private Nature Garden, Grevenbroich, Germany](http://birdnetgv.ddnss.de)<br>

[Share your installation!!](https://github.com/mcguirepr89/BirdNET-Pi/wiki/Sharing-Your-BirdNET-Pi)

Currently listening in these countries . . . that I know of . . .
- The United States
- Germany
- South Africa
- France
- Austria
- Sweden
- Scotland
- Norway
- England
- Italy
- Finland

If your installation isn't in one of the countries listed above, please let me know so that I can add your country to the list! Let me know either in a GitHub issue, or [email me](mailto:mcguirepr89@gmail.com) and let me know where your BirdNET-Pi is listening.

## Features
* 24/7 recording and BirdNET-Lite analysis
* [BirdWeather](https://app.birdweather.com) integration (you will need to be issued a BirdWeather ID -- for now, request that from [@timsterc here](https://github.com/mcguirepr89/BirdNET-Pi/discussions/82))
* Web interface access to all data and logs
* Web Terminal
* [Tiny File Manager](https://tinyfilemanager.github.io/)
* FTP server included
* Automatic extraction of detected data (creating audio clips of detected bird sounds)
* Spectrograms available for all extractions
* SQLite3 Database
* Live audio stream
* Adminer database maintenance
* [phpSysInfo](https://github.com/phpsysinfo/phpsysinfo)
* New species mobile notifications from [Pushed.co](https://pushed.co/quick-start-guide) (for iOS users only)
* Localization supported

## Requirements
* A Raspberry Pi 4B or Raspberry Pi 3B+ (The 3B+ must run on RaspiOS-ARM64-**Lite**)
* An SD Card with the **_64-bit version of RaspiOS_** installed (please use Bullseye) -- Lite is recommended, but the installation works on RaspiOS-ARM64-Full as well. [(Download the latest here)](https://downloads.raspberrypi.org/raspios_lite_arm64/images/)
* A USB Microphone or Sound Card

## Installation
[An installation guide is available here](https://github.com/mcguirepr89/BirdNET-Pi/wiki/Installation-Guide).

The system can be installed with:
```
curl -s https://raw.githubusercontent.com/mcguirepr89/BirdNET-Pi/main/newinstaller.sh | bash
```
The installer takes care of any and all necessary updates, so you can run that as the very first command upon the first boot, if you'd like.

The installation creates a log in `/home/pi/installation.log` that you can [email me](mailto:mcguirepr89@gmail.com) if you encounter any issues during installation.

## Access
The BirdNET-Pi system can be accessed from any web browser on the same network:
- http://birdnetpi.local
- Default Basic Authentication Username: birdnet
- Password is empty by default. Set this in "Tools" > "Settings" > "Advanced Settings"

## Uninstallation
```
/usr/local/bin/uninstall.sh && cd ~ && rm -drf BirdNET-Pi
```

## Troubleshooting and Ideas
I want this to work for you! If you have any trouble, or if my documentation is wrong, I'd like to get things right.

If you encounter any issues at any point, or have questions, comments, concerns, ideas, or want to share something, please take a look through the [open and closed issues](https://github.com/mcguirepr89/BirdNET-Pi/issues?q=is%3A+issue) and [the community discussions](https://github.com/mcguirepr89/BirdNET-Pi/discussions). PLEASE feel invited to [open a new issue](https://github.com/mcguirepr89/BirdNET-Pi/issues/new/choose) if you don't find the help you need. Likewise, please accept my invitation to [start a new discussion](https://github.com/mcguirepr89/BirdNET-Pi/discussions/new) to get a conversation started around your topic.

If you are not a GitHub user and need help, you can [email me](mailto:mcguirepr89@gmail.com), but I hope you will consider making a GitHub account so that your questions can be answered here for others as well. I expect this project will attract more bird-enthusiasts than Linux-enthusiasts, so please don't feel like any question is too _novice_ or, pardon the phrase, _stupid_ to ask. I want to help!

## Sharing
Please join a Discussion!! and please join [BirdWeather!!](https://app.birdweather.com)
I hope that if you find BirdNET-Pi has been worth your time, you will share your setup, results, customizations, etc. [HERE](https://github.com/mcguirepr89/BirdNET-Pi/discussions/69) and will consider [making your installation public](https://github.com/mcguirepr89/BirdNET-Pi/wiki/Sharing-Your-BirdNET-Pi).

## ToDo, Notes, and Coming Soon 

### Internationalization:
The bird names are in English by default, but other localized versions are available thanks to the wonderful efforts of [@patlevin](https://github.com/patlevin). Use the web interface's "Tools" > "Settings" and select your "Database Language" to have the detections in your language.

Current database languages include the list below:
| Language | Missing Species out of 6,362 | Missing labels (%) |
| -------- | ------- | ------ |
| Afrikaans | 5774 | 90.76% |
| Catalan | 544 | 8.55% |
| Chinese | 264 | 4.15% |
| Croatian | 370 | 5.82% |
| Czech | 683 | 10.74% |
| Danish | 460 | 7.23% |
| Dutch | 264 | 4.15% |
| Estonian | 3171 | 49.84% |
| Finnish | 518 | 8.14% |
| French | 264 | 4.15% |
| German | 264 | 4.15% |
| Hungarian | 2688 | 42.25% |
| Icelandic | 5588 | 87.83% |
| Indonesian | 5550 | 87.24% |
| Italian | 524 | 8.24% |
| Japanese | 640 | 10.06% |
| Latvian | 4821 | 75.78% |
| Lithuanian | 597 | 9.38% |
| Norwegian | 325 | 5.11% |
| Polish | 265 | 4.17% |
| Portuguese | 2742 | 43.10% |
| Russian | 808 | 12.70% |
| Slovak | 264 | 4.15% |
| Slovenian | 5532 | 86.95% |
| Spanish | 348 | 5.47% |
| Swedish | 264 | 4.15% |
| Thai | 5580 | 87.71% |
| Ukrainian | 646 | 10.15% |

### Tips and Coming Soon:
For some reason, the system seems to run more efficiently and the birds sound better when you [![Star on GitHub](https://img.shields.io/github/stars/mcguirepr89/BirdNET-Pi.svg?style=social)](https://github.com/mcguirepr89/BirdNET-Pi/stargazers) the project :)

Expect FULL internationalization options post-installation for the following languages:
- German
- Swedish
- French
- Spanish
