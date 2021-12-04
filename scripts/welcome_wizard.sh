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

3) Connect to WiFi (if applicable) / Set Date and Time (if applicable)

4) Set BirdNET-Pi's latitude and longitude

5) Set the web interface and database passwords

6) Updating the system to use your new birdnet.conf file" --no-wrap --icon-name=red-cardinal

 if [ $? -eq 0 ];then
   exit 0
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
    sync_clock
  else
    change_password
  fi
}

sync_clock() {
  zenity --title="Connecting to WiFi/Setting the Date and Time" --window-icon=/usr/share/pixmaps/red-cardinal32.png \
    --question --ok-label="Automatically" --cancel-label="Manually" --icon-name=red-cardinal --text="Should the installation update the date and time Automatically (using the internet), 
or will you do this Manually?" --no-wrap
  if [ $? -eq 1 ];then
    get_date_and_time
  else
    setup_wifi
  fi
}

get_date_and_time() {
  full_date="$(zenity --forms --title="Set the current time"  --window-icon=/usr/share/pixmaps/red-cardinal32.png --add-calendar=Calendar --add-combo="Current Hour (24h)" --combo-values="01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20|21|22|23" --add-combo="Current Minute" --combo-values="00|01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27|28|29|30|31|32|33|34|35|36|37|38|39|40|41|42|43|44|45|46|47|48|49|50|51|52|53|54|55|56|57|58|59" --add-combo="Current Second" --combo-values="00|01|02|03|04|05|06|07|08|09|10|11|12|13|14|15|16|17|18|19|20|21|22|23|24|25|26|27|28|29|30|31|32|33|34|35|36|37|38|39|40|41|42|43|44|45|46|47|48|49|50|51|52|53|54|55|56|57|58|59")"
  current_date="$(echo ${full_date} | awk -F\| '{print $1}')"
  if ! [ -z $current_date ];then
    current_time="$(echo ${full_date} | awk -F\| '{print $1" "$2":"$3":"$4}')"
    sudo date -s "${current_time}"
    get_latitude
  else
    sync_clock
  fi
}

setup_wifi() {
  sudo systemctl start dhcpcd
  wifi_list=""
  until [[ "${wifi_list}" == "Continue" ]];do
  wifi_list="$(zenity --title="Connect to WiFi" --extra-button="Refresh WiFi List" --window-icon=/usr/share/pixmaps/red-cardinal32.png --info --text="Use the Networking Icon in the bottom right corner of
the screen to select your WiFi network. Enter the credentials to connect when prompted.

If you are currently connected to BirdNET-Pi via its Access Point and VNC/NoMachine,
this connection will be severed as soon as the new connection is made.
Simply connect your VNC/NoMachine client to the network which you are 
currently configuring BirdNET-Pi to use, and you will be able to resume the setup.

Press \"Continue\" AFTER you have connected." --ok-label="Continue" --no-wrap)"
  [ -z ${wifi_list} ] && break
  if [[ "${wifi_list}" == "Refresh WiFi List" ]];then
    sudo systemctl restart dhcpcd
  fi
done

  get_latitude
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
