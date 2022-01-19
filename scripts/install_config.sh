#!/usr/bin/env bash
# Creates and installs the /etc/birdnet/birdnet.conf file
#set -x # Uncomment to enable debugging
set -e
trap 'exit 1' SIGINT SIGHUP

my_dir=$(realpath $(dirname $0))
birdnetpi_dir=$(realpath $(dirname $my_dir))
BIRDNET_CONF="$(dirname ${my_dir})/birdnet.conf"

get_RECS_DIR() {
  read -p "What is the full path to your recordings directory (locally)? " RECS_DIR
}

get_LATITUDE() {
  read -p "What is the latitude where the recordings were made? " LATITUDE
}

get_LONGITUDE() {
  read -p "What is the longitude where the recordings were made? " LONGITUDE
}

get_DO_EXTRACTIONS() {
  while true; do
    read -n1 -p "Do you want this device to perform the extractions? " DO_EXTRACTIONS
    echo
    case $DO_EXTRACTIONS in
      [Yy] ) break;;
      [Nn] ) break;;
      * ) echo "You must answer with Yes or No (y or n)";;
    esac
  done
}

get_DO_RECORDING() {
  while true; do
    read -n1 -p "Is this device also doing the recording? " DO_RECORDING
    echo
    case $DO_RECORDING in
      [Yy] ) break;;
      [Nn] ) break;;
      * ) echo "You must answer with Yes or No (y or n)";;
    esac
  done
}

get_BIRDNETPI_URL() {
  while true;do
    read -n1 -p "Would you like to access the extractions via a web browser?

    *Note: It is recommended, (but not required), that you run the web
    server on the same host that does the extractions. If the extraction
    service and web server are on different hosts, the \"By_Species\" and
    \"Processed\" symbolic links won't work. The \"By-Date\" extractions,
    however, will work as expected." CADDY_SERVICE
    echo
    case $CADDY_SERVICE in
      [Yy] ) read -p "What URL would you like to publish the extractions to?
        *Note: Set this to http://localhost if you do not want to make the
        extractions publically available: " BIRDNETPI_URL
        get_CADDY_PWD
        get_ICE_PWD
        break;;
      [Nn] ) BIRDNETPI_URL= CADDY_PWD= ICE_PWD=;break;;
      * ) echo "Please answer Yes or No";;
    esac
  done
}

get_CADDY_PWD() {
  if [ -z ${CADDY_PWD} ]; then
    while true; do
      read -p "Please set a password to protect your data: " CADDY_PWD
      case $CADDY_PWD in
        "" ) echo "The password cannot be empty. Please try again.";;
        * ) break;;
      esac
    done
  fi
}

get_ICE_PWD() {
  if [ ! -z ${CADDY_PWD} ] && [[ ${DO_RECORDING} =~ [Yy] ]];then
    while true; do
      read -n1 -p "Would you like to enable the live audio streaming service?" LIVE_STREAM
      echo
      case $LIVE_STREAM in
        [Yy] )
          read -p "Please set the icecast password. Use only alphanumeric characters." ICE_PWD
          echo
          case ${ICE_PWD} in
            "" ) echo "The password cannot be empty. Please try again.";;
            *) break;;
          esac
          break;;
        [Nn] ) break;;
        * ) echo "You must answer Yes or No (y or n).";;
      esac
    done
  fi
}

get_DB_PWDS() {
  if [ ! -z ${DB_PWD} ];then
    read -p "Please set a password for your database: " DB_PWD
    echo
  fi
}

get_PUSHED() {
  while true; do
    read -n1 -p "Do you have a free App key to receive mobile notifications via Pushed.co?" YN
    echo
    case $YN in
      [Yy] ) read -p "Enter your Pushed.co App Key: " PUSHED_APP_KEY
        read -p "Enter your Pushed.co App Key Secret: " PUSHED_APP_SECRET
        break;;
      [Nn] ) PUSHED_APP_KEY=
        PUSHED_APP_SECRET=
        break;;
      * ) echo "A simple Yea or Nay will do";;
    esac
  done
}

get_INSTALL_NOMACHINE() {
  while true; do
    read -n1 -p "Would you like to also install NoMachine for remote desktop access?" INSTALL_NOMACHINE
    echo
    case $INSTALL_NOMACHINE in
      [Yy] ) break;;
      [Nn] ) break;;
      * ) echo "You must answer with Yes or No (y or n)";;
    esac
  done
}

get_CHANNELS() {
  REC_CARD="$(sudo -u pi aplay -L \
    | grep dsnoop \
    | cut -d, -f1  \
    | grep -ve 'vc4' -e 'Head' -e 'PCH' \
    | uniq)"
    
  [ -f $(dirname ${my_dir})/soundcard_params.txt ] || touch $(dirname ${my_dir})/soundcard_params.txt
  SOUND_PARAMS=$(dirname ${my_dir})/soundcard_params.txt
  SOUND_CARD="$(sudo -u ${USER} aplay -L \
    | awk -F, '/^hw:/ {print $1}' \
    | grep -ve 'vc4' -e 'Head' -e 'PCH' \
    | uniq)"
  script -c "arecord -D ${SOUND_CARD} --dump-hw-params" -a "${SOUND_PARAMS}" &> /dev/null
  CHANNELS=$(awk '/CHANN/ { print $2 }' "${SOUND_PARAMS}" | sed 's/\r$//')
  [ -z REC_CARD ] && REC_CARD=default
  [ -z CHANNELS ] && CHANNELS=2
  echo "REC_CARD variable set to ${REC_CARD}"  
  echo "Number of channels available: ${CHANNELS}"
}


configure() {
  get_RECS_DIR
  get_LATITUDE
  get_LONGITUDE
  get_DB_PWDS
  get_DO_EXTRACTIONS
  get_DO_RECORDING
  get_BIRDNETPI_URL
  get_PUSHED
  get_INSTALL_NOMACHINE
  get_CHANNELS
}

install_birdnet_conf() {
  cat << EOF > $(dirname ${my_dir})/birdnet.conf
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

RECS_DIR=${RECS_DIR}

#-----------------------  Web Interface User Password  ------------------------#
#____________________The variable below sets the 'birdnet'_____________________# 
#___________________user password for the live audio stream,___________________# 
#_________________web tools, system info, and processed files__________________#

## CADDY_PWD is the plaintext password (that will be hashed) and used to access
## certain parts of the web interface

CADDY_PWD=${CADDY_PWD}

#-------------------------  MariaDB User Passwords  ---------------------------#
#_____________The variable below sets the password for the_____________________# 
#_______________________'birder' user on the MariaDB___________________________#

## DB_PWD is for the 'birder' user
DB_PWD=${DB_PWD}

#-------------------------  Live Audio Stream  --------------------------------#
#_____________The variable below configures/enables the live___________________# 
#_____________________________audio stream.____________________________________#


## ICE_PWD is the password that icecast2 will use to authenticate ffmpeg as a
## trusted source for the stream. You will never need to enter this manually
## anywhere other than here.

ICE_PWD=${ICE_PWD}

#-----------------------  Web-hosting/Caddy File-server -----------------------#
#________The four variables below can be set to enable internet access_________#
#____________to your data,(e.g., extractions, raw data, live___________________#
#______________audio stream, BirdNET.selection.txt files)______________________#


## BIRDNETPI_URL is the URL where the extractions, data-set, and live-stream
## will be web-hosted. If you do not own a domain, or would just prefer to keep 
## the BirdNET-Pi on your local network, keep this EMPTY.

BIRDNETPI_URL=${BIRDNETPI_URL}
EXTRACTIONLOG_URL${EXTRACTIONLOG_URL}
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

INSTALL_NOMACHINE=${INSTALL_NOMACHINE}

#
#------------------------------ Extraction Service  ---------------------------#

## DO_EXTRACTIONS is simply a setting for enabling the extraction.service.
## Set this to Y or y to enable extractions.

DO_EXTRACTIONS=${DO_EXTRACTIONS}

#-----------------------------  Recording Service  ----------------------------#
#____________________The variable below can be set to enable __________________#
#________________________the birdnet_recording.service ________________________#

## DO_RECORDING is simply a setting for enabling the 24/7
## birdnet_recording.service.
## Set this to Y or y to enable recording.

DO_RECORDING=${DO_RECORDING}

## REC_CARD is the sound card you would want the birdnet_recording.service to 
## use. This setting is irrelevant if you are not planning on doing data 
## collection via recording on this machine. The command substitution below 
## looks for a USB microphone's dsnoop alsa device. The dsnoop device lets
## birdnet_recording.service and livestream.service share the raw audio stream
## from the microphone. If you would like to use a different microphone than
## what this produces, or if your microphone does not support creating a
## dsnoop device, you can set this explicitly from a list of the available
## devices from the output of running 'aplay -L'

REC_CARD=${REC_CARD}

## PROCESSED is the directory where the formerly 'Analyzed' files are moved 
## after extractions have been made from them. This includes both WAVE and 
## BirdNET.selection.txt files.

PROCESSED=${PROCESSED}

## EXTRACTED is the directory where the extracted audio selections are moved.

EXTRACTED=${EXTRACTED}

## IDFILE is the file that keeps a complete list of every spececies that
## BirdNET has identified from your data-set. It is persistent across
## data-sets, so would need to be whiped clean through deleting or renaming
## it. A backup is automatically made from this variable each time it is 
## updated (structure: ${IDFILE}.bak), and would also need to be removed
## or renamed to start a new file between data-sets. Alternately, you can
## change this variable between data-sets to preserve records of disparate
## data-sets according to name.

IDFILE=${IDFILE}

## OVERLAP is the value in seconds which BirdNET should use when analyzing
## the data. The values must be between 0.0-2.9.

OVERLAP=${OVERLAP}

## CONFIDENCE is the minimum confidence level from 0.0-1.0 BirdNET's analysis 
## should reach before creating an entry in the BirdNET.selection.txt file.
## Don't set this to 1.0 or you won't have any results.

CONFIDENCE=${CONFIDENCE}

## SENSITIVITY is the detection sensitivity from 0.5-1.5.

SENSITIVITY=${SENSITIVITY}

## CHANNELS holds the variabel that corresponds to the number of channels the
## sound card supports.

CHANNELS=${CHANNELS}

## FULL_DISK can be set to configure how the system reacts to a full disk
## 0 = Remove the oldest day's worth of recordings
## 1 = Keep all data and 'stop_core_services.sh'

FULL_DISK=0

## VENV is the virtual environment where the the BirdNET python build is found,
## i.e, VENV is the virtual environment miniforge built for BirdNET.

VENV=/home/pi/BirdNET-Pi/birdnet

## RECORDING_LENGTH sets the length of the recording that BirdNET-Lite will analyze.
RECORDING_LENGTH=${RECORDING_LENGTH}

## EXTRACTION_LENGTH sets the length of the audio extractions that will be made
## from each BirdNET-Lite detection.
EXTRACTION_LENGTH=${EXTRACTION_LENGTH}

## BIRDNET_USER should be the non-root user systemd should use to execute each 
## service.

BIRDNET_USER=pi

## These are just for debugging
LAST_RUN=${LAST_RUN}
THIS_RUN=${THIS_RUN}
EOF
}

# Checks for a birdnet.conf file in the BirdNET-Pi directory for a 
# non-interactive installation. Otherwise,the installation is interactive.
if [ -f ${BIRDNET_CONF} ];then
  source ${BIRDNET_CONF}
  #install_birdnet_conf
  [ -d /etc/birdnet ] || sudo mkdir /etc/birdnet
  sudo ln -sf $(dirname ${my_dir})/birdnet.conf /etc/birdnet/birdnet.conf
  grep -ve '^#' -e '^$' /etc/birdnet/birdnet.conf > ${birdnetpi_dir}/firstrun.ini
else
  configure
  install_birdnet_conf
  [ -d /etc/birdnet ] || sudo mkdir /etc/birdnet
  sudo ln -sf $(dirname ${my_dir})/birdnet.conf /etc/birdnet/birdnet.conf
  grep -ve '^#' -e '^$' /etc/birdnet/birdnet.conf > ${birdnetpi_dir}/firstrun.ini
fi
