<?php
if (file_exists('/home/pi/BirdNET-Pi/thisrun.txt')) {
  $config = parse_ini_file('/home/pi/BirdNET-Pi/thisrun.txt');
} elseif (file_exists('/home/pi/BirdNET-Pi/firstrun.ini')) {
  $config = parse_ini_file('/home/pi/BirdNET-Pi/firstrun.ini');
}
$refresh = $config['RECORDING_LENGTH'];
$time = time();
echo "<img style=\"width:100%;height:100%\" src=\"/spectrogram.png?nocache=$time\">";
header("Refresh: $refresh;");
?>
