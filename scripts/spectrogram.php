<?php
if (file_exists('./scripts/thisrun.txt')) {
  $config = parse_ini_file('./scripts/thisrun.txt');
} elseif (file_exists('./scripts/firstrun.ini')) {
  $config = parse_ini_file('./scripts/firstrun.ini');
}
$refresh = $config['RECORDING_LENGTH'];
header("Refresh: $refresh");
$time = time();
echo "<img style=\"width:100%;height:89%\" src=\"/spectrogram.png?nocache=$time\">";
$_GET['view'] = "Spectrogram";
?>
