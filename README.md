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
BirdNET-Pi is built on the [TFLite version of BirdNET](https://github.com/kahst/BirdNET-Lite) by [**@kahst**](https://github.com/kahst) <a href="https://creativecommons.org/licenses/by-nc-sa/4.0/"><img src="https://img.shields.io/badge/License-CC%20BY--NC--SA%204.0-lightgrey.svg"></a> using [pre-built TFLite binaries](https://github.com/PINTO0309/TensorflowLite-bin) by [**@PINTO0309**](https://github.com/PINTO0309) . It is able to recognize bird sounds from a USB sound card in realtime and share its data with the rest of the world.

Check out birds from around the world
- [BirdWeather](https://app.birdweather.com)<br>
- [My system in North Carolina, United States](https://birdnetpi.pmcgui.xyz)<br>
- [NatureStation.net in Johannesburg, South Africa](https://birds.naturestation.net)<br>
- [BirdNET-Pi in Öringe, Tyresö, Sweden](https://birdnet.svardsten.se)<br>
- [Private Nature Garden, Grevenbroich, Germany](http://birdnetgv.ddnss.de)<br>

Currently listening in these countries . . . that I know of . . .
- The United States
- Germany
- South Africa
- France
- Austria
- Sweden
- Scotland
- Norway

If your installation isn't in one of the countries listed above, please let me know so that I can add your country to the list! Let me know either in a GitHub issue, or [email me](mailto:mcguirepr89@gmail.com) and let me know where your BirdNET-Pi is listening.

## Features
* 24/7 recording and BirdNET-Lite analysis
* [BirdWeather](https://app.birdweather.com) integration (you will need to be issued a BirdWeather ID -- for now, request that from [@timsterc here](https://github.com/mcguirepr89/BirdNET-Pi/discussions/82))
* Web interface access to all data and logs
* Automatic extraction of detected data (creating audio clips of detected bird sounds)
* Spectrograms available for all extractions
* MariaDB integration
* NoMachine remote desktop (for personal use only)
* Live audio stream
* Integrated phpSysInfo
* New species mobile notifications from Pushed.co (for iOS users only)
* Localization supported

## Requirements
* A Raspberry Pi 4B
* An SD Card with the 64-bit version of RaspiOS installed (please use Bullseye) [(download the latest here)](https://downloads.raspberrypi.org/raspios_arm64/images/)
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
I want this to work for you! If you have any trouble, or if my documentation is wrong, I'd like to get things right.

If you encounter any issues at any point, or have questions, comments, concerns, ideas, or want to share something, please take a look through the [open and closed issues](https://github.com/mcguirepr89/BirdNET-Pi/issues?q=is%3A+issue) and [the community discussions](https://github.com/mcguirepr89/BirdNET-Pi/discussions). PLEASE feel invited to [open a new issue](https://github.com/mcguirepr89/BirdNET-Pi/issues/new/choose) if you don't find the help you need. Likewise, please accept my invitation to [start a new discussion](https://github.com/mcguirepr89/BirdNET-Pi/discussions/new) to get a conversation started around your topic.

If you are not a GitHub user and need help, you can [email me](mailto:mcguirepr89@gmail.com), but I hope you will consider making a GitHub account so that your questions can be answered here for others as well. I expect this project will attract more bird-enthusiasts than Linux-enthusiasts, so please don't feel like any question is too _novice_ or, pardon the phrase, _stupid_ to ask. I want to help!

## Sharing
Please join a Discussion!! and please join [BirdWeather!!](https://app.birdweather.com)
I hope that if you find BirdNET-Pi has been worth your time, you will share your setup, results, customizations, etc. [HERE](https://github.com/mcguirepr89/BirdNET-Pi/discussions/69) and will consider [making your installation public](https://github.com/mcguirepr89/BirdNET-Pi/wiki/Sharing-Your-BirdNET-Pi).

## ToDo, Notes, and Coming Soon 

### Internationalization:
The bird names are in English by default, but other localized versions are available thanks to the wonderful efforts of [@patlevin](https://github.com/patlevin). Please [download the labels_l18n.zip file](https://github.com/kahst/BirdNET-Lite/files/7288803/labels_l18n.zip) and replace the `model/labels.txt` with your corresponding language. [Click here](https://github.com/kahst/BirdNET-Lite/issues/4#issuecomment-886724712) for more information.

### Realtime Analysis Predictions View
The pre-built TFLite binaries for this project also support [the BirdNET-Demo](https://github.com/kahst/BirdNET-Demo), which I am currently testing for integration into the BirdNET-Pi. If you know anything about JavaScript and are willing to help, please let me know in the [Live Analysis discussion](https://github.com/mcguirepr89/BirdNET-Pi/discussions/24).

### Tips and Coming Soon:
For some reason, the system seems to run more efficiently and the birds sound better when you [![Star on GitHub](https://img.shields.io/github/stars/mcguirepr89/BirdNET-Pi.svg?style=social)](https://github.com/mcguirepr89/BirdNET-Pi/stargazers) the project :)

Expect FULL internationalization options during installation (and available post-installation) for the following languages:
- German
- Swedish
- Frensh
- Spanish

and detection/database localization for the following languages:
| Language | Missing | labels (%) |
| -------- | ------- | ------ |
| Afrikaans | 5774 | 90.76% |
| Catalan | 544 | 8.55% |
| Chinese | 264 | 4.15% |
| Chinese (Traditional) | 295 | 4.64% |
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
| Northern Sami | 5605 | 88.10% |
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
