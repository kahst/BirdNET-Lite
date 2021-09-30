# BirdNET-Lite for arm64/aarch64 (Raspberry Pi 4)
### Built on https://github.com/kahst/BirdNET -- checkout the Wiki at [BirdNETWiki.pmcgui.xyz](https://birdnetwiki.pmcgui.xyz)

This project offers an installation script for BirdNET-Lite as a systemd service on arm64 (aarch64) Debian-based operating systems, namely RaspiOS. The installation script offers to walk the user through setting up the '*birdnet.conf*' main configuration file interactively, or can read from an existing '*birdnet.conf*'. A variety of configurations can be attained through this installation script.

BirdNET-Lite can be configured with the following optional services:
- A 24/7 recording script that can be easily configured to use any available sound card
- An extraction service that extracts the audio selections identified by BirdNET by date and species
- A Caddy instance that serves the extracted files and live audio stream (icecast2) (requires dsnoop capable mic)
- A species list updating and notification script supporting mobile notifications via Pushed.co (sorry, Android users, Pushed.co doesn't seem to work for you)
- NoMachine remote desktop software (for personal use only)

An installation one-liner is available [HERE](https://birdnetwiki.pmcgui.xyz/wiki/Birder%27s_Guide_to_BirdNET-Lite#Install_BirdNET-Lite) for RaspiOS-ARM64 meeting the prequisites below. It installs all services listed above.
- Prerequisites:
  - An updated RaspiOS for AArch64 that has locale, WiFi, time-zone, and pi user password set. A guide is available [here](https://birdnetwiki.pmcgui.xyz/wiki/Birder%27s_Guide_to_BirdNET-Lite#Install_the_base_operating_system_.28OS.29). 64GB SD card for best performance.
  - A USB microphone (dsnoop capable to enable live audio stream).
  - Running the installer from within the Raspberry Pi's desktop environment (i.e., not over SSH -- for SSH installations, see installation options 2 & 3)


## What the installation does
1. Looks for a *'birdnet.conf'* file in the *BirdNET-Lite* main directory
1. If a *'birdnet.conf'* file exists and is filled out properly, the installation is nearly
   non-interactive and builds the system based off of the services configured in the *'birdnet.conf'* file
1. If the installer cannot find a *'birdnet.conf'* file,  the installation is interactive and will
   walk the user through creating the '*birdnet.conf'* file interactively.
1. Installs the following system dependencies:
	- ffmpeg
	- libblas-dev
	- liblapack-dev
	- caddy (for web access to extractions)
	- icecast2 (live audio stream)
	- alsa-utils (for recording)
	- sshfs (to mount remote sound file directories)
1. Installs BirdNET-Lite scripts in */usr/local/bin*
1. Installs all selected services based on '*birdnet.conf*'
1. Installs *miniforge* for the aarch64 architecture using the current release from https://github.com/conda-forge/miniforge
1. Builds BirdNET in miniforge's *'birdnet'* virtual environment
1. Enables (but does not start) the services

## What you should know before any installation
1. The licensing information for the software that is used (see [LICENSE](https://raw.githubusercontent.com/mcguirepr89/BirdNET-Lite/BirdNET-Lite-for-raspi4/LICENSE)).
1. The **latitude** and **longitude** where the bird recordings take place. Google maps is an easy way to find these (right-clicking the location).
1. In order for the live audio stream to work at the same time as the birdnet_recording.service, the microphone needs to be dsnoop capable. If you are wondering whether your mic supports creating the dsnoop device, you can use `aplay -L | awk -F, '/dsn/ {print $1}' | grep -ve 'vc4' -e 'Head' -e 'PCH' | uniq` to check. (No output means your microphone does not support creating a dsnoop device and therefore cannot also provide an audio stream while recording. The birdnet_recording.service, however, should not be affected by this and the installation one-liner can still be used. The live stream link simply will not work.)

## What you should know for a manual installation
1. The **local directory** where the recordings should be found on your local computer. BirdNET-Lite supports setting up a systemd.mount for automounting remote directories. So for instance, if the actual recordings live on RemoteHost's `/home/user/recordings` directory, but you would like them to be found on your device at `/home/me/BirdNET-recordings`, then `/home/me/BirdNET-recordings` will be your answer to that question.
1. If mounting the recordings directory from a remote host, you need to know the **remote hostname, username, and password** to connect to it via SSH, as well as the **absolute path of the recordings on the remote host**.
1. If you are using a special microphone or have multiple sound cards and would like to specify which to use for recording, you can edit the `~/BirdNET-Lite/birdnet.conf` file before the installation and set the **REC_CARD** variable to the sound card of your choice. Copy your desired sound card line from the output of `aplay -L | awk -F, '/^dsn:/ { print $1 }'`(prefered), or `aplay -L | awk -F, '/^hw:/ { print $1 }'`(if prefered is not available). 
1. If you would like to take advantage of Caddy's automatic handling of SSL certificates to be able to host a public website where your friends can hear your bird sounds, forward ports 80 and 443 to the host you want to serve the files. You may also want to purchase a domain name.
   - *Note: If you're just keeping this on your local network, **be sure to set your extraction URL to something 'http://'** (on RaspiOS, I recommend 'http://raspberrypi.local') to disable Caddy's automatic HTTPS. Alternatively, you may edit the `/etc/caddy/Caddyfile` after installation and add the `tls internal` directive to the site block to have Caddy issue a self-signed certificate for an HTTPS connection.*
1. If you would like to take advantage of BirdNET-Lite's ability to send New Species mobile notifications, you can easily setup a Pushed.co notification app (see the #TODOs at the bottom for more info). After setting up your application, make note of your **App Key** and **App Secret** -- you will need these to enable mobile notifications for new species. 
   - *Note for Android users: it seems that the Pushed.co Mobile App does not work for Android devices, which is a huge bummer. If anyone knows of an Android alternative, or if anyone might be able to come up with a home-spun notification system, please let me know.*

## How to install
#### Option 1 (Recommended) -- Install All Services
1. In the terminal run: `curl -s https://raw.githubusercontent.com/mcguirepr89/BirdNET-Lite/rpi4/Birders_Guide_Installer.sh | bash`

##### Options 2 & 3 require you setup 4GB of swapping. That step is included in the directions below.
#### Option 2 -- Pre-fill birdnet.conf
1. In the terminal run `git clone https://github.com/mcguirepr89/BirdNET-Lite.git ~/BirdNET-Lite`
1. You can copy the included *'birdnet.conf-defaults'* template to create and configure the BirdNET-Lite
   to your needs before running the installer. Issue `cp ~/BirdNET-Lite/birdnet.conf-defaults ~/BirdNET-Lite/birdnet.conf`.
   Edit the new *'birdnet.conf'* file to suit your needs and save it.
   If you choose this method, the installation will be (nearly) non-interactive.
1. Setup zRAM swapping. Run `~/BirdNET-Lite/scripts/install_zram_service.sh && sudo reboot`   
1. After the reboot, run `~/BirdNET-Lite/scripts/install_birdnet.sh`
#### Option 3 -- Interactive Installation
1. In the terminal run `git clone https://github.com/mcguirepr89/BirdNET-Lite.git ~/BirdNET-Lite`
1. Setup zRAM swapping. Run `~/BirdNET-Lite/scripts/install_zram_service.sh && sudo reboot`   
1. After the reboot, run `~/BirdNET-Lite/scripts/install_birdnet.sh`
1. Follow the installation prompts to configure the BirdNET-Lite to your needs.
- Note: The installation should be run as a regular user. If run on an OS other than RaspiOS, be sure the regular user is in the sudoers file or the sudo group.

## Access your BirdNET-Lite
If you configured BirdNET-Lite with the Caddy webserver, you can access the extractions locally at

- http://birdnetsystem.local

You can also view the log output for the <code>birdnet_analysis.service</code> and <code>extraction.service</code> at

- http://birdlog.local
- http://extractionlog.local

and the BirdNET-Lite Statistics Report at
- http://birdstats.local

If you opt to also install NoMachine alongside the BirdNET-Lite, you can also access BirdNET-Lite
remotely following the address information that can be found on the NoMachine's server information page.

## Examples
These are examples of my personal instance of the BirdNET-Lite on a Raspberry Pi 4B.
 - https://birdsounds.pmcgui.xyz  -- My BirdNET-Lite Extractions page
 - https://birdlog.pmcgui.xyz  --  My 'birdlog' birdnet_analysis.service log
 - https://extraction.pmcgui.xyz  --  My 'extractionlog' extraction.service log
 - https://birdstats.pmcgui.xyz  -- My 'birdstats' BirdNET-Lite Report

## How to reconfigure the system
At any time, you can completely reconfigure the system to select or remove features. To reconfigure the system, simply run the included "reconfigure_birdnet.sh" script (as the regular user) and follow the prompts to create a new birdnet.conf file and install new services: `~/BirdNET-Lite/scripts/reconfigure_birdnet.sh`

## How to uninstall BirdNET-Lite
To remove BirdNET-Lite, run the included '*uninstall.sh*' script as the regular user.
1. Issue `/usr/local/bin/uninstall.sh && cd ~ && rm -drf BirdNET-Lite`

## Troubleshooting
**General** -- At anytime, you can run the included `~/BirdNET-Lite/dump_logs.sh` script to create a compressed tar ball of system logs that may provide a helpful overview of the system services. In addition, you can upload it in a new issue along with a description of what you are experiencing. dump_logs.sh scrubs password information, but does retain LATITUDE and LONGITUDE information. If at all concerned with privacy, you're welcome to send them to me via email at mailto:mcguirepr89@gmail.com.

**Audio** -- If you have problems with the _bridnet_recording.service_ or _livestream.service_, try setting the REC_CARD setting in the _birdnet.conf_ file to `REC_CARD=default` and the CHANNELS variable to `CHANNELS=2`. This works for two very different microphones I have, so it may work for you. If it does, please let me know, as I may change the code as a result. Also, during installation, a file is created called `~/BirdNET-Lite/soundcard_params.txt` that may provide helpful information for customized settings.

**Installation** -- The installer _should_ always create a compressed set of system logs whether it succeeds or fails. Its location is `~/BirdNET-Lite/logs.tar.gz`. Take a look through there or feel free to create a new issue and upload it along with a description of what you are experiencing.

### TODO & Notes:
1. I ought to add the steps to setup a Pushed.co application for the mobile notifications feature. Here is a link for now https://pushed.co/quick-start-guide
