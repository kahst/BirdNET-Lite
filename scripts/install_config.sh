#!/usr/bin/env bash
# Creates and installs the /etc/birdnet/birdnet.conf file
#set -x # Uncomment to enable debugging
set -e
trap 'exit 1' SIGINT SIGHUP

my_dir=$(realpath $(dirname $0))
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

get_TIMESTAMP_FORMAT() {
  read -n2 -p "Would you like recordings to be time stamped in 12-hour AM/PM
or 24-hour format? " TIMESTAMP_FORMAT
  echo
  case $TIMESTAMP_FORMAT in
    12 ) ;;
    24 ) ;;
    * ) TIMESTAMP_FORMAT=24;;
  esac
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

get_REMOTE() {
  while true; do
    read -n1 -p "Are the recordings mounted on a remote file system?" REMOTE
    echo
    case $REMOTE in
      [Yy] ) 
        read -p "What is the remote hostname or IP address for the recorder? " REMOTE_HOST
        read -p "Who is the remote user? " REMOTE_USER
        read -p "What is the absolute path of the recordings directory on the remote host? " REMOTE_RECS_DIR
        break;;
      [Nn] ) break;;
      * ) echo "Please answer Yes or No (y or n)";;
    esac
  done
}

get_EXTRACTIONS_URL() {
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
        extractions publically available: " EXTRACTIONS_URL
        get_CADDY_PWD
        get_ICE_PWD
        break;;
      [Nn] ) EXTRACTIONS_URL= CADDY_PWD= ICE_PWD=;break;;
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
  [ -z REC_CARD ] || REC_CARD=default
  [ -z CHANNELS ] || CHANNELS=2
  echo "REC_CARD variable set to ${REC_CARD}"  
  echo "Number of channels available: ${CHANNELS}"
}


configure() {
  get_RECS_DIR
  get_LATITUDE
  get_LONGITUDE
  get_DO_EXTRACTIONS
  get_DO_RECORDING
  get_TIMESTAMP_FORMAT
  get_REMOTE
  get_EXTRACTIONS_URL
  get_PUSHED
  get_INSTALL_NOMACHINE
  get_CHANNELS
}

install_birdnet_conf() {
  cat << EOF > $(dirname ${my_dir})/birdnet.conf
################################################################################
#                 Configuration settings for BirdNET as a service              #
################################################################################
INSTALL_DATE="$(date "+%D")"
#___________The four variables below are the only that are required.___________#

## BIRDNET_USER should be the non-root user systemd should use to execute each 
## service.

BIRDNET_USER=${USER}

## RECS_DIR is the location birdnet_analysis.service will look for the data-set
## it needs to analyze. Be sure this directory is readable and writable for
## the BIRDNET_USER. If you are going to be accessing a remote data-set, you
## still need to set this, as this will be where the remote directory gets
## mounted locally. See REMOTE_RECS_DIR below for mounting remote data-sets.

RECS_DIR=${RECS_DIR}

## LATITUDE and LONGITUDE are self-explanatroy. Find them easily at
## maps.google.com. Only go to the thousanths place for these variables
##  Example: these coordinates would indicate the Eiffel Tower in Paris, France.
##  LATITUDE=48.858
##  LONGITUDE=2.294

LATITUDE="${LATITUDE}"
LONGITUDE="${LONGITUDE}"

################################################################################
#------------------------------ Extraction Service  ---------------------------#

#   Keep this EMPTY if you do not want this device to perform the extractions  #

## DO_EXTRACTIONS is simply a setting for enabling the extraction.service.
## Set this to Y or y to enable extractions.

DO_EXTRACTIONS=${DO_EXTRACTIONS}

################################################################################
#-----------------------------  Recording Service  ----------------------------#
#_______________The two variables below can be set to enable __________________#
#________________________the birdnet_recording.service ________________________#

#   Keep this EMPTY if you do not want this device to perform the recording.   #

## DO_RECORDING is simply a setting for enabling the 24/7
## birdnet_recording.service.
## Set this to Y or y to enable recording.

DO_RECORDING=${DO_RECORDING}

## TIMESTAMP_FORMAT is the format the recording service will use to name its
## files. Setting this variable to "12" will name the recorded (and extracted)
## files using the 12-hour AM/PM time format. Setting this variable to "24"
## will name the files using the 24-hour time format. See examples below:
#
## TIMESTAMP_FORMAT=12
## example filename: 236-Northern_Cardinal-86%2021-09-30-birdnet-01:00:19pm.wav
#
## TIMESTAMP_FORMAT=24
## example filename: 236-Northern_Cardinal-86%2021-09-30-birdnet-13:00:19.wav


TIMESTAMP_FORMAT=${TIMESTAMP_FORMAT}


################################################################################
#-----------------  Mounting a remote directory with systemd  -----------------#
#_______________The four variables below can be set to enable a_______________#
#___________________systemd.mount for analysis, extraction,____________________#
#______________________________or file-serving_________________________________#

#            Leave these settings EMPTY if your data-set is local.             #

## REMOTE is simply a setting for enabling the systemd.mount to use a remote 
## filesystem for the data storage and service.
## Set this to Y or y to enable the systemd.mount. 

REMOTE=${REMOTE}

## REMOTE_HOST is the IP address, hostname, or domain name SSH should use to 
## connect for FUSE to mount its remote directories locally.

REMOTE_HOST=${REMOTE_HOST}

## REMOTE_USER is the user SSH will use to connect to the REMOTE_HOST.

REMOTE_USER=${REMOTE_USER}

## REMOTE_RECS_DIR is the directory on the REMOTE_HOST which contains the
## data-set SSHFS should mount to this system for local access. This is NOT the
## directory where you will access the data on this machine. See RECS_DIR for
## that.

REMOTE_RECS_DIR=${REMOTE_RECS_DIR}

################################################################################
#-----------------------  Web-hosting/Caddy File-server -----------------------#
#__________The two variables below can be set to enable web access_____________#
#____________to your data,(e.g., extractions, raw data, live___________________#
#______________audio stream, BirdNET.selection.txt files)______________________#

#         Leave these EMPTY if you do not want to enable web access            #

## EXTRACTIONS_URL is the URL where the extractions, data-set, and live-stream
## will be web-hosted. If you do not own a domain, or would just prefer to keep 
## BirdNET-Lite on your local network, you can set this to http://localhost.
## Setting this (even to http://localhost) will also allow you to enable the   
## GoTTY web logging features below.

EXTRACTIONS_URL=${EXTRACTIONS_URL}

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
## NoMachine alongside the BirdNET-Lite for remote desktop access. This in-
## staller assumes personal use. Please reference the LICENSE file included
## in this repository for more information.
## Set this to Y or y to install NoMachine alongside the BirdNET-Lite

INSTALL_NOMACHINE=${INSTALL_NOMACHINE}

################################################################################
#--------------------------------  Defaults  ----------------------------------#
#________The seven variables below are default settings that you (probably)____#
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

REC_CARD=${REC_CARD}
    
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

IDFILE=${HOME}/BirdNET-Lite/IdentifiedSoFar.txt

## OVERLAP is the value in seconds which BirdNET should use when analyzing
## the data. The values must be between 0.0-2.9.

OVERLAP="0.0"

## CONFIDENCE is the minimum confidence level from 0.0-1.0 BirdNET's analysis 
## should reach before creating an entry in the BirdNET.selection.txt file.
## Don't set this to 1.0 or you won't have any results.

CONFIDENCE="0.7"

## SENSITIVITY is the detection sensitivity from 0.5-1.5.

SENSITIVITY="1.25"

################################################################################
#------------------------------  Auto-Generated  ------------------------------#
#_____________________The variables below are auto-generated___________________#
#______________________________during installation_____________________________#

## CHANNELS holds the variabel that corresponds to the number of channels the
## sound card supports.

CHANNELS=${CHANNELS}

# Don't touch the variables below

## SYSTEMD_MOUNT is created from the RECS_DIR variable to comply with systemd 
## mount naming requirements.

SYSTEMD_MOUNT=$(echo ${RECS_DIR#/} | tr / -).mount

## VENV is the virtual environment where the the BirdNET python build is found,
## i.e, VENV is the virtual environment miniforge built for BirdNET.

VENV=$(dirname ${my_dir})/miniforge/envs/birdnet
EOF
  [ -d /etc/birdnet ] || sudo mkdir /etc/birdnet
  sudo ln -sf $(dirname ${my_dir})/birdnet.conf /etc/birdnet/birdnet.conf
}

# Checks for a birdnet.conf file in the BirdNET-Lite directory for a 
# non-interactive installation. Otherwise,the installation is interactive.
if [ -f ${BIRDNET_CONF} ];then
  source ${BIRDNET_CONF}
  install_birdnet_conf
else
  configure
  install_birdnet_conf
fi
