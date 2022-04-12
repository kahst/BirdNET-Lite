<?php
if (file_exists('thisrun.txt')) {
  $config = parse_ini_file('thisrun.txt');
} elseif (file_exists('firstrun.ini')) {
  $config = parse_ini_file('firstrun.ini');
}
$refresh = $config['RECORDING_LENGTH'];
$time = time();
echo "<img style=\"width:100%;height:100%\" src=\"/spectrogram.png?nocache=$time\">";
header("Refresh: $refresh;");
?>
