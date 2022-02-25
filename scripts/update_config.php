<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

# Basic Settings
if(isset($_POST["latitude"])){
$latitude = $_POST["latitude"];
$longitude = $_POST["longitude"];
$birdweather_id = $_POST["birdweather_id"];
$pushed_app_key = $_POST["pushed_app_key"];
$pushed_app_secret = $_POST["pushed_app_secret"];

$contents = file_get_contents("/home/pi/BirdNET-Pi/birdnet.conf");
$contents = preg_replace("/LATITUDE=.*/", "LATITUDE=$latitude", $contents);
$contents = preg_replace("/LONGITUDE=.*/", "LONGITUDE=$longitude", $contents);
$contents = preg_replace("/BIRDWEATHER_ID=.*/", "BIRDWEATHER_ID=$birdweather_id", $contents);
$contents = preg_replace("/PUSHED_APP_KEY=.*/", "PUSHED_APP_KEY=$pushed_app_key", $contents);
$contents = preg_replace("/PUSHED_APP_SECRET=.*/", "PUSHED_APP_SECRET=$pushed_app_secret", $contents);

$contents2 = file_get_contents("/home/pi/BirdNET-Pi/thisrun.txt");
$contents2 = preg_replace("/LATITUDE=.*/", "LATITUDE=$latitude", $contents2);
$contents2 = preg_replace("/LONGITUDE=.*/", "LONGITUDE=$longitude", $contents2);
$contents2 = preg_replace("/BIRDWEATHER_ID=.*/", "BIRDWEATHER_ID=$birdweather_id", $contents2);
$contents2 = preg_replace("/PUSHED_APP_KEY=.*/", "PUSHED_APP_KEY=$pushed_app_key", $contents2);
$contents2 = preg_replace("/PUSHED_APP_SECRET=.*/", "PUSHED_APP_SECRET=$pushed_app_secret", $contents2);

$fh = fopen("/home/pi/BirdNET-Pi/birdnet.conf", "w");
$fh2 = fopen("/home/pi/BirdNET-Pi/thisrun.txt", "w");
fwrite($fh, $contents);
fwrite($fh2, $contents2);

$language = $_POST["language"];
if ($language != "none"){
  $command = "sudo -upi mv /home/pi/BirdNET-Pi/model/labels.txt /home/pi/BirdNET-Pi/model/labels.txt.old && sudo -upi unzip /home/pi/BirdNET-Pi/model/labels_l18n.zip $language -d /home/pi/BirdNET-Pi/model && sudo -upi mv /home/pi/BirdNET-Pi/model/$language /home/pi/BirdNET-Pi/model/labels.txt";
  $command_output = `$command`;
}
}
# Advanced Settings
if(isset($_POST['submit'])) {
  $contents = file_get_contents("/home/pi/BirdNET-Pi/birdnet.conf");
  $contents2 = file_get_contents("/home/pi/BirdNET-Pi/thisrun.txt");

  if(isset($_POST["caddy_pwd"])) {
    $caddy_pwd = $_POST["caddy_pwd"];
    if(strcmp($caddy_pwd,$config['CADDY_PWD']) !== 0) {
      $contents = preg_replace("/CADDY_PWD=.*/", "CADDY_PWD=$caddy_pwd", $contents);
      $contents2 = preg_replace("/CADDY_PWD=.*/", "CADDY_PWD=$caddy_pwd", $contents2);
      $fh = fopen("/home/pi/BirdNET-Pi/birdnet.conf", "w");
      $fh2 = fopen("/home/pi/BirdNET-Pi/thisrun.txt", "w");
      fwrite($fh, $contents);
      fwrite($fh2, $contents2);
      exec('sudo /usr/local/bin/update_caddyfile.sh > /dev/null 2>&1 &');
    }
  }

  if(isset($_POST["ice_pwd"])) {
    $ice_pwd = $_POST["ice_pwd"];
    if(strcmp($ice_pwd,$config['ICE_PWD']) !== 0) {
      $contents = preg_replace("/ICE_PWD=.*/", "ICE_PWD=$ice_pwd", $contents);
      $contents2 = preg_replace("/ICE_PWD=.*/", "ICE_PWD=$ice_pwd", $contents2);
    }
  }

  if(isset($_POST["webterminal_url"])) {
    $webterminal_url = $_POST["webterminal_url"];
    if(strcmp($webterminal_url,$config['WEBTERMINAL_URL']) !== 0) {
      $contents = preg_replace("/WEBTERMINAL_URL=.*/", "WEBTERMINAL_URL=$webterminal_url", $contents);
      $contents2 = preg_replace("/WEBTERMINAL_URL=.*/", "WEBTERMINAL_URL=$webterminal_url", $contents2);
      $fh = fopen("/home/pi/BirdNET-Pi/birdnet.conf", "w");
      $fh2 = fopen("/home/pi/BirdNET-Pi/thisrun.txt", "w");
      fwrite($fh, $contents);
      fwrite($fh2, $contents2);
    }
  }

  if(isset($_POST["birdnetlog_url"])) {
    $birdnetlog_url = $_POST["birdnetlog_url"];
    if(strcmp($birdnetlog_url,$config['BIRDNETLOG_URL']) !== 0) {
      $contents = preg_replace("/BIRDNETLOG_URL=.*/", "BIRDNETLOG_URL=$birdnetlog_url", $contents);
      $contents2 = preg_replace("/BIRDNETLOG_URL=.*/", "BIRDNETLOG_URL=$birdnetlog_url", $contents2);
      $fh = fopen("/home/pi/BirdNET-Pi/birdnet.conf", "w");
      $fh2 = fopen("/home/pi/BirdNET-Pi/thisrun.txt", "w");
      fwrite($fh, $contents);
      fwrite($fh2, $contents2);
    }
  }

  if(isset($_POST["birdnetpi_url"])) {
    $birdnetpi_url = $_POST["birdnetpi_url"];
    if(strcmp($birdnetpi_url,$config['BIRDNETPI_URL']) !== 0) {
      $contents = preg_replace("/BIRDNETPI_URL=.*/", "BIRDNETPI_URL=$birdnetpi_url", $contents);
      $contents2 = preg_replace("/BIRDNETPI_URL=.*/", "BIRDNETPI_URL=$birdnetpi_url", $contents2);
      $fh = fopen("/home/pi/BirdNET-Pi/birdnet.conf", "w");
      $fh2 = fopen("/home/pi/BirdNET-Pi/thisrun.txt", "w");
      fwrite($fh, $contents);
      fwrite($fh2, $contents2);
      exec('sudo /usr/local/bin/update_caddyfile.sh > /dev/null 2>&1 &');
    }
  }

  if(isset($_POST["overlap"])) {
    $overlap = $_POST["overlap"];
    if(strcmp($overlap,$config['OVERLAP']) !== 0) {
      $contents = preg_replace("/OVERLAP=.*/", "OVERLAP=$overlap", $contents);
      $contents2 = preg_replace("/OVERLAP=.*/", "OVERLAP=$overlap", $contents2);
    }
  }

  if(isset($_POST["confidence"])) {
    $confidence = $_POST["confidence"];
    if(strcmp($confidence,$config['CONFIDENCE']) !== 0) {
      $contents = preg_replace("/CONFIDENCE=.*/", "CONFIDENCE=$confidence", $contents);
      $contents2 = preg_replace("/CONFIDENCE=.*/", "CONFIDENCE=$confidence", $contents2);
    }
  }

  if(isset($_POST["sensitivity"])) {
    $sensitivity = $_POST["sensitivity"];
    if(strcmp($sensitivity,$config['SENSITIVITY']) !== 0) {
      $contents = preg_replace("/SENSITIVITY=.*/", "SENSITIVITY=$sensitivity", $contents);
      $contents2 = preg_replace("/SENSITIVITY=.*/", "SENSITIVITY=$sensitivity", $contents2);
    }
  }

  if(isset($_POST["full_disk"])) {
    $full_disk = $_POST["full_disk"];
    if(strcmp($full_disk,$config['FULL_DISK']) !== 0) {
      $contents = preg_replace("/FULL_DISK=.*/", "FULL_DISK=$full_disk", $contents);
      $contents2 = preg_replace("/FULL_DISK=.*/", "FULL_DISK=$full_disk", $contents2);
    }
  }

  if(isset($_POST["rec_card"])) {
    $rec_card = $_POST["rec_card"];
    if(strcmp($rec_card,$config['REC_CARD']) !== 0) {
      $contents = preg_replace("/REC_CARD=.*/", "REC_CARD=$rec_card", $contents);
      $contents2 = preg_replace("/REC_CARD=.*/", "REC_CARD=$rec_card", $contents2);
    }
  }

  if(isset($_POST["channels"])) {
    $channels = $_POST["channels"];
    if(strcmp($channels,$config['CHANNELS']) !== 0) {
      $contents = preg_replace("/CHANNELS=.*/", "CHANNELS=$channels", $contents);
      $contents2 = preg_replace("/CHANNELS=.*/", "CHANNELS=$channels", $contents2);
    }
  }

  if(isset($_POST["recording_length"])) {
    $recording_length = $_POST["recording_length"];
    if(strcmp($recording_length,$config['RECORDING_LENGTH']) !== 0) {
      $contents = preg_replace("/RECORDING_LENGTH=.*/", "RECORDING_LENGTH=$recording_length", $contents);
      $contents2 = preg_replace("/RECORDING_LENGTH=.*/", "RECORDING_LENGTH=$recording_length", $contents2);
    }
  }

  if(isset($_POST["extraction_length"])) {
    $extraction_length = $_POST["extraction_length"];
    if(strcmp($extraction_length,$config['EXTRACTION_LENGTH']) !== 0) {
      $contents = preg_replace("/EXTRACTION_LENGTH=.*/", "EXTRACTION_LENGTH=$extraction_length", $contents);
      $contents2 = preg_replace("/EXTRACTION_LENGTH=.*/", "EXTRACTION_LENGTH=$extraction_length", $contents2);
    }
  }

  if(isset($_POST["audiofmt"])) {
    $audiofmt = $_POST["audiofmt"];
    if(strcmp($audiofmt,$config['AUDIOFMT']) !== 0) {
      $contents = preg_replace("/AUDIOFMT=.*/", "AUDIOFMT=$audiofmt", $contents);
      $contents2 = preg_replace("/AUDIOFMT=.*/", "AUDIOFMT=$audiofmt", $contents2);
    }
  }

  $fh = fopen("/home/pi/BirdNET-Pi/birdnet.conf", "w");
  $fh2 = fopen("/home/pi/BirdNET-Pi/thisrun.txt", "w");
  fwrite($fh, $contents);
  fwrite($fh2, $contents2);
}
?>

