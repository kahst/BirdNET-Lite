#!/usr/bin/env bash
# BirdNET-Pi Welcome Wiz!
#trap 'systemctl --user disable birdnet-pi-config-gui.service' EXIT 0
set -x

birdnet_config=/home/pi/BirdNET-Pi/birdnet.conf

zenity --title="Welcome!" --ok-label="Next" --window-icon=/usr/share/pixmaps/red-cardinal32.png \
  --info --text="
Thank for you installing BirdNET-Pi\!" --no-wrap --icon-name=red-cardinal

 [ $? -eq 1 ] && exit 1

overview() {
  zenity --title="Configuration Wizard" --window-icon=/usr/share/pixmaps/red-cardinal32.png --width=300 --ok-label="Exit" \
 --extra-button="Next" --info --text="This will walk you through the following steps:

1) Change the 'pi' user password

2) Setting your Language, Country, Timezone, Keyboard, and WiFi Country (if applicable)

3) Connect to WiFi (if applicable)

4) Changing the birdnet.conf configuration file to your needs

5) Updating the system to use your new birdnet.conf file" --no-wrap --icon-name=red-cardinal

 if [ $? -eq 0 ];then
   exit 1
 elif [ $? -eq 1 ];then
   open_rpi_configuration && change_password
 fi
}

change_password() {
  zenity --title="Change the 'pi' user password" \
    --window-icon=/usr/share/pixmaps/red-cardinal32.png  --ok-label="Back" \
    --extra-button="Continue" --info --text="Click the button labeled \"Change Password...\"
and enter your new password in each box." --no-wrap --icon-name=red-cardinal

  if [ $? -eq 1 ];then
    change_locale
  else
    overview
  fi
}

change_locale() {
  zenity --title="Setting Language, Country, Timezone, Keyboard, and WiFI Country" \
    --window-icon=/usr/share/pixmaps/red-cardinal32.png  --ok-label="Back" \
    --extra-button="Continue" --info --text="Use the right-most tab labeled \"Localisation\"

1) Locale: Set your Language and Country -- leave Character Set as \"UTF-8\"

2) Timezone: Set your Area and Location

3) Keyboard: Set your keyboard

4) WiFi Country: Choose your country from the list" --no-wrap --icon-name=red-cardinal

  if [ $? -eq 1 ];then
    get_latitude
  else
    change_password
  fi
}

open_rpi_configuration() {
  if ! pgrep rc_gui;then
  env SUDO_ASKPASS=/usr/lib/rc-gui/pwdrcg.sh sudo -AE rc_gui &
  sleep 1.5
  fi
}

get_latitude() {
  new_lat="$(zenity --cancel-label="Back" --ok-label="Set" --title="Enter Your Latitude" --window-icon=/usr/share/pixmaps/red-cardinal32.png --width=300 --entry --text="Enter your Latitude. 

  A network guess: $(curl -s4 ifconfig.co/json | awk '/lat/ {print $2}' | tr -d ',')")"

  if [ -n "$new_lat" ];then
    get_longitude
  else
    change_locale
  fi
}

get_longitude() {
  new_lon="$(zenity --cancel-label="Back" --ok-label="Set" --title="Enter Your Longitude" --window-icon=/usr/share/pixmaps/red-cardinal32.png --width=300 --entry --text="Enter your Longitude. 

  A network guess: $(curl -s4 ifconfig.co/json | awk '/lon/ {print $2}' | tr -d ',')")"

  if [ -n "$new_lon" ];then
    set_caddy_pwd
  else
    get_latitude
  fi
}

set_caddy_pwd() {
  caddy_pwd="$(zenity --cancel-label="Back" --ok-label="Set" --title="Set Web Password" --window-icon=/usr/share/pixmaps/red-cardinal32.png --width=300 --entry --text="Set a new password for the web interface")"

  if [ -n "${caddy_pwd}" ];then
    set_db_pwd
  else
    get_longitude
  fi
}

set_db_pwd() {
  db_pwd="$(zenity --cancel-label="Back" --ok-label="Set" --title="Set The Database Password" --window-icon=/usr/share/pixmaps/red-cardinal32.png --width=300 --entry --text="Set a new password for the databse.")"

  if [ -n "${db_pwd}" ];then
    rearview
  else
    set_caddy_pwd
  fi
}


overview
