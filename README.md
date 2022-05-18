<h1 align="center">
  BirdNET-Pi
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
- [NatureStation.net in Johannesburg, South Africa](https://joburg.birdnetpi.com)<br>
- [BirdNET-Pi in Öringe, Tyresö, Sweden](https://tyreso.birdnetpi.com)<br>
- [Private Nature Garden, Grevenbroich, Germany](http://grevenbroich-elsen.birdnetpi.com)<br>
- [Norman, Oklahoma, United States](https://normanok.birdnetpi.com)<br>
- [Nijmegen, the Netherlands](https://nijmegen.birdnetpi.com)<br>
- [Murrysville, Pennsylvania, United States](https://murrysvillepa.birdnetpi.com/)
- [Berowra, New South Wales, Australia](https://berowra.birdnetpi.com/)

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
- Australia
- Canada
- Switzerland

## Features
* 24/7 recording and BirdNET-Lite analysis
* Automatic extraction of detected data (creating audio clips of detected bird sounds)
* Spectrograms available for all extractions
* Live audio stream
* [BirdWeather](https://app.birdweather.com) integration -- you can request a BirdWeather ID from BirdNET-Pi's "Tools" > "Settings" page
* Web interface access to all data and logs provided by [Caddy](https://caddyserver.com)
* Web Terminal
* [Tiny File Manager](https://tinyfilemanager.github.io/)
* FTP server included
* SQLite3 Database
* Adminer database maintenance
* [phpSysInfo](https://github.com/phpsysinfo/phpsysinfo)
* [Apprise Notifications](https://github.com/caronc/apprise) supporting 70+ notification platforms
* Localization supported

## Requirements
* A Raspberry Pi 4B or Raspberry Pi 3B+ (The 3B+ must run on RaspiOS-ARM64-**Lite**)
* An SD Card with the **_64-bit version of RaspiOS_** installed (please use Bullseye) -- Lite is recommended, but the installation works on RaspiOS-ARM64-Full as well. [(Download the latest here)](https://downloads.raspberrypi.org/raspios_lite_arm64/images/)
* A USB Microphone or Sound Card

## Installation
[A comprehensive installation guide is available here](https://github.com/mcguirepr89/BirdNET-Pi/wiki/Installation-Guide).

The system can be installed with:
```
curl -s https://raw.githubusercontent.com/mcguirepr89/BirdNET-Pi/main/newinstaller.sh | bash
```
The installer takes care of any and all necessary updates, so you can run that as the very first command upon the first boot, if you'd like.

The installation creates a log in `$HOME/installation$(date "+%F").log`.
## Access
The BirdNET-Pi can be accessed from any web browser on the same network:
- http://birdnetpi.local
- Default Basic Authentication Username: birdnet
- Password is empty by default. Set this in "Tools" > "Settings" > "Advanced Settings"

Please take a look at the [wiki](https://github.com/mcguirepr89/BirdNET-Pi/wiki) and [discussions](https://github.com/mcguirepr89/BirdNET-Pi/discussions) for information on
- [making your installation public](https://github.com/mcguirepr89/BirdNET-Pi/wiki/Sharing-Your-BirdNET-Pi)
- [backing up and restoring your database](https://github.com/mcguirepr89/BirdNET-Pi/wiki/Backup-and-Restore-the-Database)
- [adjusting your sound card settings](https://github.com/mcguirepr89/BirdNET-Pi/wiki/Adjusting-your-sound-card)
- [suggested USB microphones](https://github.com/mcguirepr89/BirdNET-Pi/discussions/39)
- [building your own microphone](https://github.com/DD4WH/SASS/wiki/Stereo--(Mono)-recording-low-noise-low-cost-system)
- [privacy concerns and options](https://github.com/mcguirepr89/BirdNET-Pi/discussions/166)
- [beta testing](https://github.com/mcguirepr89/BirdNET-Pi/discussions/11)
- [and more!](https://github.com/mcguirepr89/BirdNET-Pi/discussions)


## Updating 

Use the web interface and go to "Tools" > "System Controls" > "Update." If you encounter any issues with that, or suspect that the update did not work for some reason, please save its output and post it in an issue where we can help.

## Uninstallation
```
/usr/local/bin/uninstall.sh && cd ~ && rm -drf BirdNET-Pi
```

## Troubleshooting and Ideas
Submit an issue or discussion.

## Sharing
Please join a Discussion!! and please join [BirdWeather!!](https://app.birdweather.com)
I hope that if you find BirdNET-Pi has been worth your time, you will share your setup, results, customizations, etc. [HERE](https://github.com/mcguirepr89/BirdNET-Pi/discussions/69) and will consider [making your installation public](https://github.com/mcguirepr89/BirdNET-Pi/wiki/Sharing-Your-BirdNET-Pi).

## Project Links

- [Marie Lelouche's <i>Out of Spaces</i>](https://www.lestanneries.fr/exposition/marie-lelouche-out-of-spaces/) using BirdNET-Pi in post-sculpture VR! [Press Kit](https://github.com/mcguirepr89/BirdNET-Pi-assets/blob/main/dp_out_of_spaces_marie_lelouche_digital_05_01_22.pdf)
- [Research on noded BirdNET-Pi networks for farming](https://github.com/mcguirepr89/BirdNET-Pi-assets/blob/main/G23_Report_ModelBasedSysEngineering_FarmMarkBirdDetector_V1__Copy_.pdf)
- <ins>[Core-Electronics](https://core-electronics.com.au/projects/bird-calls-raspberry-pi)</ins> Build Article


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

## :thinking:
Are you a lucky ducky with an extra Raspberry Pi 4B lying around? [Here's an idea!](https://foldingathome.org/alternative-downloads)
