#!/usr/bin/env bash
set -e
USER=pi
HOME=/home/pi
my_dir=${HOME}/BirdNET-Pi
branch=newinstaller
trap '${my_dir}/scripts/dump_logs.sh && exit' EXIT SIGHUP SIGINT

if [ "$(uname -m)" != "aarch64" ];then
  echo "BirdNET-Pi requires a 64-bit OS.
It looks like your operating system is using $(uname -m), 
but would need to be aarch64.
Please take a look at https://birdnetwiki.pmcgui.xyz for more
information"
  exit 1
fi

install_zram_swap() {
  echo
  echo "Configuring zram.service"
  sudo touch /etc/modules-load.d/zram.conf
  echo 'zram' | sudo tee /etc/modules-load.d/zram.conf
  sudo touch /etc/modprobe.d/zram.conf
  echo 'options zram num_devices=1' | sudo tee /etc/modprobe.d/zram.conf
  sudo touch /etc/udev/rules.d/99-zram.rules
  echo 'KERNEL=="zram0", ATTR{disksize}="4G",TAG+="systemd"' \
    | sudo tee /etc/udev/rules.d/99-zram.rules
  sudo touch /etc/systemd/system/zram.service
  echo "Installing zram.service"
  cat << EOF | sudo tee /etc/systemd/system/zram.service &> /dev/null
[Unit]
Description=Swap with zram
After=multi-user.target

[Service]
Type=oneshot 
RemainAfterExit=true
ExecStartPre=/sbin/mkswap /dev/zram0
ExecStart=/sbin/swapon /dev/zram0
ExecStop=/sbin/swapoff /dev/zram0

[Install]
WantedBy=multi-user.target
EOF
  sudo systemctl enable zram
  echo "Rebooting the system in 5 seconds"
  sleep 5
  sudo reboot
}

stage_1() {
  echo
  echo "Beginning Stage 1"
  echo
  echo "Ensuring the system is up-to-date."
  sudo apt -qq update
  sudo apt -qqy full-upgrade
  sudo apt -y autoremove --purge
  echo "System Updated!"
  if ! which git &> /dev/null; then
    echo "Installing git"
    sudo apt install -qqy git
  fi
  ZRAM="$(swapon --show=SIZE,NAME | awk -FG '!/SIZE/ && /zram/ {print $1}')"
  [ ! -z ${ZRAM} ] || ZRAM=0
  if [ ${ZRAM} -lt 4 ];then
    install_zram_swap
  else
    touch ${HOME}/stage_1_complete
    echo "Stage 1 complete"
    stage_2
    exit
  fi
}

stage_2() {
  echo
  echo "Beginning stage 2"
  echo
  echo "Checking for an internet connection to continue . . ."
  until ping -c 1 8.8.8.8 &> /dev/null; do
    sleep 1
  done
  echo "Connected!"
  echo
  if [ ! -d ${my_dir} ];then
    cd ${HOME} || exit 1
    echo "Cloning the BirdNET-Pi repository $branch branch into your home directory"
    git clone --depth 1 -b ${branch} https://github.com/mcguirepr89/BirdNET-Pi.git ${HOME}/BirdNET-Pi
  else
    cd ${my_dir} && git checkout ${branch}
  fi

  if [ -f ${my_dir}/Birders_Guide_Installer_Configuration.txt ];then
    echo
    echo
    echo "Follow the instructions to fill out the LATITUDE and LONGITUDE variables
and set the passwords for the live audio stream. Save the file after editing
and then close the Mouse Pad editing window to continue."
    set -x
    if [ -z "$SSH_CONNECTION" ];then
      EDITOR=mousepad
    else
      EDITOR=nano
    fi
    env && exit
    $EDITOR ${my_dir}/Birders_Guide_Installer_Configuration.txt
    while pgrep $EDITOR &> /dev/null;do
      sleep 1
    done
    source ${my_dir}/Birders_Guide_Installer_Configuration.txt || exit 1
  else
    echo "Something went wrong. I can't find the configuration file."
    exit 1
  fi

  if [ -z ${LATITUDE} ] || [ -z ${LONGITUDE} ] || [ -z ${CADDY_PWD} ] || [ -z ${ICE_PWD} ] || [ -z ${DB_PWD} ] || [ -z ${DB_ROOT_PWD} ];then
    echo
    echo
    echo "It looks like you haven't filled out the Birders_Guide_Installer_Configuration.txt file
completely.

Open that file to edit it. (Go to the folder icon in the top left and look for the \"BirdNET-Pi\"
folder and double-click the file called \"Birders_Guide_Installer_Configuration.txt\"
Enter the latitude and longitude of where the BirdNET-Pi will be. 
You can find this information at https://maps.google.com

Find your location on the map and right click to find your coordinates.
After you have filled out the configuration file, you can re-run this script. Just do the exact
same things you did to start this (copying and pasting from the Wiki) to try again.
Press Enter to close this window.
Good luck!"
    read
    exit 1
  fi
  echo "Installing the BirdNET-Pi configuration file."
  [ -f ${my_dir}/soundcard_params.txt ] || touch ${my_dir}/soundcard_params.txt
  SOUND_PARAMS="${HOME}/BirdNET-Pi/soundcard_params.txt"
  SOUND_CARD="$(sudo -u pi aplay -L \
   | grep -e '^hw' \
   | cut -d, -f1  \
   | grep -ve 'vc4' -e 'Head' -e 'PCH' \
   | uniq)"
  script -c "arecord -D ${SOUND_CARD} --dump-hw-params" -a ${SOUND_PARAMS} &> /dev/null
  install_birdnet_config || exit 1
  echo "Installing BirdNET-Lite"
  if ${my_dir}/scripts/install_birdnet.sh << EOF ; then

n
EOF
echo "The next time you power on the raspberry pi, all of the services will start up automatically. 

The installation has finished. Press Enter to close this window."
    read
  else
    echo "Something went wrong during installation. Open a github issue or email mcguirepr89@gmail.com"
  fi
}

install_birdnet_config() {
  cat << EOF > ${my_dir}/birdnet.conf
################################################################################
#                 Configuration settings for the BirdNET-Pi                    #
################################################################################

#___________The four variables below are the only that are required.___________#

## BIRDNET_USER should be the non-root user systemd should use to execute each 
## service.

BIRDNET_USER=pi

## RECS_DIR is the location birdnet_analysis.service will look for the data-set
## it needs to analyze. Be sure this directory is readable and writable for
## the BIRDNET_USER. If you are going to be accessing a remote data-set, you
## still need to set this, as this will be where the remote directory gets
## mounted locally. 

RECS_DIR=${HOME}/BirdSongs

## LATITUDE and LONGITUDE are self-explanatroy. Find them easily at
## maps.google.com. Only go to the thousanths place for these variables
##  Example: these coordinates would indicate the Eiffel Tower in Paris, France.
##  LATITUDE=48.858
##  LONGITUDE=2.294

LATITUDE=${LATITUDE}
LONGITUDE=${LONGITUDE}

################################################################################
#------------------------------ Extraction Service  ---------------------------#

#   Keep this EMPTY if you do not want this device to perform the extractions  #

## DO_EXTRACTIONS is simply a setting for enabling the extraction.service.
## Set this to Y or y to enable extractions.

DO_EXTRACTIONS=y

################################################################################
#-----------------------------  Recording Service  ----------------------------#
#____________________The variable below can be set to enable __________________#
#________________________the birdnet_recording.service ________________________#

#   Keep this EMPTY if you do not want this device to perform the recording.   #

## DO_RECORDING is simply a setting for enabling the 24/7
## birdnet_recording.service.
## Set this to Y or y to enable recording.

DO_RECORDING=y

################################################################################
#-----------------------  Web-hosting/Caddy File-server -----------------------#
#________The four variables below can be set to enable internet access_________#
#____________to your data,(e.g., extractions, raw data, live___________________#
#______________audio stream, BirdNET.selection.txt files)______________________#

#         Leave these EMPTY if you do not want to enable web access            #

## BIRDNETPI_URL is the URL where the extractions, data-set, and live-stream
## will be web-hosted. If you do not own a domain, or would just prefer to keep 
## the BirdNET-Pi on your local network, keep this EMPTY.

BIRDNETPI_URL=
EXTRACTIONLOG_URL=
BIRDNETLOG_URL=

## CADDY_PWD is the plaintext password (that will be hashed) and used to access
## the "Processed" directory and live audio stream. This MUST be set if you
## choose to enable this feature.

CADDY_PWD=${CADDY_PWD}

################################################################################
#-------------------------  Live Audio Stream  --------------------------------#
#_____________The variable below configures/enables the live___________________# 
#_____________________________audio stream.____________________________________#

#         Keep this EMPTY if you do not wish to enable the live stream         #
#                or if this device is not doing the recording                  #

## ICE_PWD is the password that icecast2 will use to authenticate ffmpeg as a
## trusted source for the stream. You will never need to enter this manually
## anywhere other than here.

ICE_PWD=${ICE_PWD}

################################################################################
#-------------------  Mobile Notifications via Pushed.co  ---------------------#
#____________The two variables below enable mobile notifications_______________#
#_____________See https://pushed.co/quick-start-guide to get___________________#
#_________________________these values for your app.___________________________#

#            Keep these EMPTY if haven't setup a Pushed.co App yet.            #

## Pushed.co App Key and App Secret

PUSHED_APP_KEY=${PUSHED_APP_KEY}
PUSHED_APP_SECRET=${PUSHED_APP_SECRET}

################################################################################
#-------------------------------  NoMachine  ----------------------------------#
#_____________The variable below can be set include NoMachine__________________#
#_________________remote desktop software to be installed._____________________#

#            Keep this EMPTY if you do not want to install NoMachine.          #

## INSTALL_NOMACHINE is simply a setting that can be enabled to install
## NoMachine alongside the BirdNET-Pi for remote desktop access. This in-
## staller assumes personal use. Please reference the LICENSE file included
## in this repository for more information.
## Set this to Y or y to install NoMachine alongside the BirdNET-Lite

INSTALL_NOMACHINE=y

################################################################################
#--------------------------------  Defaults  ----------------------------------#
#______The seven variables below are default settings that you (probably)______#
#__________________don't need to change at all, but can._______________________# 

## REC_CARD is the sound card you would want the birdnet_recording.service to 
## use. This setting is irrelevant if you are not planning on doing data 
## collection via recording on this machine. The command substitution below 
## looks for a USB microphone's dsnoop alsa device. The dsnoop device lets
## birdnet_recording.service and livestream.service share the raw audio stream
## from the microphone. If you would like to use a different microphone than
## what this produces, or if your microphone does not support creating a
## dsnoop device, you can set this explicitly from a list of the available
## devices from the output of running 'aplay -L'

REC_CARD=default

## PROCESSED is the directory where the formerly 'Analyzed' files are moved 
## after extractions have been made from them. This includes both WAVE and 
## BirdNET.selection.txt files.

PROCESSED=${RECS_DIR}/Processed

## EXTRACTED is the directory where the extracted audio selections are moved.

EXTRACTED=${RECS_DIR}/Extracted

## IDFILE is the file that keeps a complete list of every spececies that
## BirdNET has identified from your data-set. It is persistent across
## data-sets, so would need to be whiped clean through deleting or renaming
## it. A backup is automatically made from this variable each time it is 
## updated (structure: ${IDFILE}.bak), and would also need to be removed
## or renamed to start a new file between data-sets. Alternately, you can
## change this variable between data-sets to preserve records of disparate
## data-sets according to name.

IDFILE=${HOME}/BirdNET-Pi/IdentifiedSoFar.txt

## OVERLAP is the value in seconds which BirdNET should use when analyzing
## the data. The values must be between 0.0-2.9.

OVERLAP=0.0

## CONFIDENCE is the minimum confidence level from 0.0-1.0 BirdNET's analysis 
## should reach before creating an entry in the BirdNET.selection.txt file.
## Don't set this to 1.0 or you won't have any results.

CONFIDENCE=0.7

## SENSITIVITY is the detection sensitivity from 0.5-1.5.

SENSITIVITY=1.25

################################################################################
#------------------------------  Auto-Generated  ------------------------------#
#_____________________The variables below are auto-generated___________________#
#______________________________during installation_____________________________#

## CHANNELS holds the variabel that corresponds to the number of channels the
## sound card supports.

CHANNELS=2

# Don't touch the variables below

## SYSTEMD_MOUNT is created from the RECS_DIR variable to comply with systemd 
## mount naming requirements.

SYSTEMD_MOUNT=$(echo ${RECS_DIR#/} | tr / -).mount

## VENV is the virtual environment where the the BirdNET python build is found,
## i.e, VENV is the virtual environment miniforge built for BirdNET.

VENV=${my_dir}/birdnet

################################################################################
#---------------------------------- Testing -----------------------------------#
#_____________These variables are for testing. Please do not touch_____________#
#_______________them if you are not testing these features.____________________#

RECORDING_LENGTH=

EXTRACTION_LENGTH=

LAST_RUN=
THIS_RUN=

DB_PWD=${DB_PWD}
DB_ROOT_PWD=${DB_ROOT_PWD}
EOF
  [ -d /etc/birdnet ] || sudo mkdir /etc/birdnet
  sudo ln -sf ${my_dir}/birdnet.conf /etc/birdnet/birdnet.conf
}
echo "
Welcome to the Birders Guide Installer script!

The installer runs in two stages:
Stage 1 configures and enables the zRAM kernel module and allocates 4G
        to its swapping size if needed. This will trigger a reboot.
Stage 1 also ensures the system is up to date.
Stage 2 guides you through configuring the essentials and installs the full BirdNET-Pi system."


if [ ! -f ${HOME}/stage_1_complete ] ;then
  stage_1
else
  stage_2
  if [ -f ${HOME}/Birders_Guide_Installer.sh ];then
  rm ${HOME}/Birders_Guide_Installer.sh
  fi
  rm ${HOME}/stage_1_complete
  ${my_dir}/scripts/dump_logs.sh
  systemctl --user disable --now birdnet-system-installer.service
  sudo rm -f /etc/systemd/user/birdnet-system-installer.service
fi  

