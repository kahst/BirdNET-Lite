#!/usr/bin/env bash
set -e
USER=pi
HOME=/home/pi
my_dir=${HOME}/BirdNET-Pi
branch=main
trap '${my_dir}/scripts/dump_logs.sh && exit' EXIT SIGHUP SIGINT

if [ "$(uname -m)" != "aarch64" ];then
  echo "BirdNET-Pi requires a 64-bit OS.
It looks like your operating system is using $(uname -m), 
but would need to be aarch64.
Please take a look at https://birdnetwiki.pmcgui.xyz for more
information"
  exit 1
fi

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
  touch ${HOME}/stage_1_complete
  echo "Stage 1 complete"
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
    git clone -b ${branch} https://github.com/mcguirepr89/BirdNET-Pi.git ${HOME}/BirdNET-Pi
  else
    cd ${my_dir} && git checkout ${branch}
  fi

  source ${my_dir}/Birders_Guide_Installer_Configuration.txt
  if [ -z ${LATITUDE} ] || [ -z ${LONGITUDE} ] || [ -z ${CADDY_PWD} ] || [ -z ${ICE_PWD} ] || [ -z ${DB_PWD} ];then
    echo
    echo "Follow the instructions to fill out the LATITUDE and LONGITUDE variables
and set the passwords for the live audio stream. Save the file after editing
and then close the Mouse Pad editing window to continue."
    echo
    if [ -z "$SSH_CONNECTION" ];then
      EDITOR=mousepad
    else
      EDITOR=nano
    fi
    $EDITOR ${my_dir}/Birders_Guide_Installer_Configuration.txt
    while pgrep $EDITOR &> /dev/null;do
      sleep 1
    done
    source ${my_dir}/Birders_Guide_Installer_Configuration.txt || exit 1
  fi
  echo "Installing the BirdNET-Pi configuration file."
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
#                    Configuration settings for BirdNET-Pi                     #
################################################################################

############ CHANGE THE LATITUDE AND LONGITUDE TO STATIC VALUES ################


## LATITUDE and LONGITUDE are self-explanatroy. Find them easily at
## maps.google.com.
##  Example: these coordinates would indicate the Eiffel Tower in Paris, France.
##  LATITUDE=48.858
##  LONGITUDE=2.294

## These are input as shell substitutions so that this will work out of the box.
## It guesses your latitude and longitude based off of your network information.
## THESE SHOULD BE CHANGED TO STATIC NUMBERS!!!!
LATITUDE=${LATITUDE}
LONGITUDE=${LONGITUDE}

## RECS_DIR is the location birdnet_analysis.service will look for the data-set
## it needs to analyze. Be sure this directory is readable and writable for
## the BIRDNET_USER. If you are going to be accessing a remote data-set, you
## still need to set this, as this will be where the remote directory gets
## mounted locally.

RECS_DIR=/home/pi/BirdSongs

#-----------------------  Web Interface User Password  ------------------------#
#____________________The variable below sets the 'birdnet'_____________________#
#___________________user password for the live audio stream,___________________#
#_________________web tools, system info, and processed files__________________#

## CADDY_PWD is the plaintext password (that will be hashed) and used to access
## certain parts of the web interface

CADDY_PWD=changeme

#-------------------------  MariaDB User Passwords  ---------------------------#
#_____________The variable below sets the password for the_____________________#
#_______________________'birder' user on the MariaDB___________________________#

## DB_PWD is for the 'birder' user
DB_PWD=changeme

#-------------------------  Live Audio Stream  --------------------------------#
#_____________The variable below configures/enables the live___________________#
#_____________________________audio stream.____________________________________#


## ICE_PWD is the password that icecast2 will use to authenticate ffmpeg as a
## trusted source for the stream. You will never need to enter this manually
## anywhere other than here.

ICE_PWD=changeme

#-----------------------  Web-hosting/Caddy File-server -----------------------#
#________The four variables below can be set to enable internet access_________#
#____________to your data,(e.g., extractions, raw data, live___________________#
#______________audio stream, BirdNET.selection.txt files)______________________#


## BIRDNETPI_URL is the URL where the extractions, data-set, and live-stream
## will be web-hosted. If you do not own a domain, or would just prefer to keep
## the BirdNET-Pi on your local network, keep this EMPTY.

BIRDNETPI_URL=${BIRDNETPI_URL}
EXTRACTIONLOG_URL=${EXTRACTIONLOG_URL}
BIRDNETLOG_URL=${BIRDNETLOG_URL}


#-------------------  Mobile Notifications via Pushed.co  ---------------------#
#____________The two variables below enable mobile notifications_______________#
#_____________See https://pushed.co/quick-start-guide to get___________________#
#_________________________these values for your app.___________________________#

#            Keep these EMPTY if haven't setup a Pushed.co App yet.            #

## Pushed.co App Key and App Secret

PUSHED_APP_KEY=${PUSHED_APP_KEY}
PUSHED_APP_SECRET=${PUSHED_APP_SECRET}

################################################################################
#--------------------------------  Defaults  ----------------------------------#
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

#
#------------------------------ Extraction Service  ---------------------------#

## DO_EXTRACTIONS is simply a setting for enabling the extraction.service.
## Set this to Y or y to enable extractions.

DO_EXTRACTIONS=y

#-----------------------------  Recording Service  ----------------------------#
#____________________The variable below can be set to enable __________________#
#________________________the birdnet_recording.service ________________________#

## DO_RECORDING is simply a setting for enabling the 24/7
## birdnet_recording.service.
## Set this to Y or y to enable recording.

DO_RECORDING=y

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

PROCESSED=/home/pi/BirdSongs/Processed

## EXTRACTED is the directory where the extracted audio selections are moved.

EXTRACTED=/home/pi/BirdSongs/Extracted

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

## CHANNELS holds the variabel that corresponds to the number of channels the
## sound card supports.

CHANNELS=2

## VENV is the virtual environment where the the BirdNET python build is found,
## i.e, VENV is the virtual environment miniforge built for BirdNET.

VENV=/home/pi/BirdNET-Pi/birdnet

## RECORDING_LENGTH sets the length of the recording that BirdNET-Lite will analyze.
RECORDING_LENGTH=

## EXTRACTION_LENGTH sets the length of the audio extractions that will be made
## from each BirdNET-Lite detection.
EXTRACTION_LENGTH=

## BIRDNET_USER should be the non-root user systemd should use to execute each
## service.

BIRDNET_USER=pi


## These are just for debugging
LAST_RUN=
THIS_RUN=
EOF
  [ -d /etc/birdnet ] || sudo mkdir /etc/birdnet
  sudo ln -sf ${my_dir}/birdnet.conf /etc/birdnet/birdnet.conf
}
if [ ! -f ${HOME}/stage_1_complete ] ;then
  stage_1
  stage_2
else
  stage_2
fi  

