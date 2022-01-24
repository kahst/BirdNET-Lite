<?php
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
@session_start();

if(true){
   $_SESSION['success'] = 1;
   header("Location:config.php");
}

$language = $_POST["language"];
if ($language != "none"){
  $command = "sudo -upi set -x; sudo -upi mv /home/pi/BirdNET-Pi/model/labels.txt /home/pi/BirdNET-Pi/model/labels.txt.old && sudo -upi unzip /home/pi/BirdNET-Pi/model/labels_l18n.zip $language -d /home/pi/BirdNET-Pi/model && sudo -upi mv /home/pi/BirdNET-Pi/model/$language /home/pi/BirdNET-Pi/model/labels.txt";
  $command_output = `$command`;
  echo $command_output;
  @session_start();
  
  if(true){
     $_SESSION['success'] = 1;
     header("Location:config.php");
  }
}
?>

