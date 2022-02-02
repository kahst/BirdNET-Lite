<?php
$caddy_pwd = $_POST["caddy_pwd"];
$db_pwd = $_POST["db_pwd"];
$ice_pwd = $_POST["ice_pwd"];
$birdnetpi_url = $_POST["birdnetpi_url"];
$webterminal_url = $_POST["webterminal_url"];
$birdnetlog_url = $_POST["birdnetlog_url"];
$overlap = $_POST["overlap"];
$confidence = $_POST["confidence"];
$sensitivity = $_POST["sensitivity"];
$full_disk = $_POST["full_disk"];
$rec_card = $_POST["rec_card"];
$channels = $_POST["channels"];
$recording_length = $_POST["recording_length"];
$extraction_length = $_POST["extraction_length"];

$contents = file_get_contents("/home/pi/BirdNET-Pi/birdnet.conf");
$contents = preg_replace("/CADDY_PWD=.*/", "CADDY_PWD=$caddy_pwd", $contents);
$contents = preg_replace("/DB_PWD=.*/", "DB_PWD=$db_pwd", $contents);
$contents = preg_replace("/ICE_PWD=.*/", "ICE_PWD=$ice_pwd", $contents);
$contents = preg_replace("/BIRDNETPI_URL=.*/", "BIRDNETPI_URL=$birdnetpi_url", $contents);
$contents = preg_replace("/WEBTERMINAL_URL=.*/", "WEBTERMINAL_URL=$webterminal_url", $contents);
$contents = preg_replace("/BIRDNETLOG_URL=.*/", "BIRDNETLOG_URL=$birdnetlog_url", $contents);
$contents = preg_replace("/OVERLAP=.*/", "OVERLAP=$overlap", $contents);
$contents = preg_replace("/CONFIDENCE=.*/", "CONFIDENCE=$confidence", $contents);
$contents = preg_replace("/SENSITIVITY=.*/", "SENSITIVITY=$sensitivity", $contents);
$contents = preg_replace("/FULL_DISK=.*/", "FULL_DISK=$full_disk", $contents);
$contents = preg_replace("/REC_CARD=.*/", "REC_CARD=$rec_card", $contents);
$contents = preg_replace("/CHANNELS=.*/", "CHANNELS=$channels", $contents);
$contents = preg_replace("/RECORDING_LENGTH=.*/", "RECORDING_LENGTH=$recording_length", $contents);
$contents = preg_replace("/EXTRACTION_LENGTH=.*/", "EXTRACTION_LENGTH=$extraction_length", $contents);

$contents2 = file_get_contents("/home/pi/BirdNET-Pi/thisrun.txt");
$contents2 = preg_replace("/CADDY_PWD=.*/", "CADDY_PWD=$caddy_pwd", $contents2);
$contents2 = preg_replace("/DB_PWD=.*/", "DB_PWD=$db_pwd", $contents2);
$contents2 = preg_replace("/ICE_PWD=.*/", "ICE_PWD=$ice_pwd", $contents2);
$contents2 = preg_replace("/BIRDNETPI_URL=.*/", "BIRDNETPI_URL=$birdnetpi_url", $contents2);
$contents2 = preg_replace("/WEBTERMINAL_URL=.*/", "WEBTERMINAL_URL=$webterminal_url", $contents2);
$contents2 = preg_replace("/BIRDNETLOG_URL=.*/", "BIRDNETLOG_URL=$birdnetlog_url", $contents2);
$contents2 = preg_replace("/OVERLAP=.*/", "OVERLAP=$overlap", $contents2);
$contents2 = preg_replace("/CONFIDENCE=.*/", "CONFIDENCE=$confidence", $contents2);
$contents2 = preg_replace("/SENSITIVITY=.*/", "SENSITIVITY=$sensitivity", $contents2);
$contents2 = preg_replace("/FULL_DISK=.*/", "FULL_DISK=$full_disk", $contents2);
$contents2 = preg_replace("/REC_CARD=.*/", "REC_CARD=$rec_card", $contents2);
$contents2 = preg_replace("/CHANNELS=.*/", "CHANNELS=$channels", $contents2);
$contents2 = preg_replace("/RECORDING_LENGTH=.*/", "RECORDING_LENGTH=$recording_length", $contents2);
$contents2 = preg_replace("/EXTRACTION_LENGTH=.*/", "EXTRACTION_LENGTH=$extraction_length", $contents2);

$fh = fopen("/home/pi/BirdNET-Pi/birdnet.conf", "w");
$fh2 = fopen("/home/pi/BirdNET-Pi/thisrun.txt", "w");
fwrite($fh, $contents);
fwrite($fh2, $contents2);
@session_start();

if(true){
   $_SESSION['success'] = 1;
   header("Location:advanced.php");
}
?>

