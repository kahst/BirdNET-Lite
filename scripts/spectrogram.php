<?php
if (file_exists('/home/pi/BirdNET-Pi/thisrun.txt')) {
  $config = parse_ini_file('/home/pi/BirdNET-Pi/thisrun.txt');
} elseif (file_exists('/home/pi/BirdNET-Pi/firstrun.ini')) {
  $config = parse_ini_file('/home/pi/BirdNET-Pi/firstrun.ini');
}
$refreshtime = $config['RECORDING_LENGTH'];
header("refresh:$refreshtime");
?>
<body>
<img src='/spectrogram.png?nocache=<?php echo time();?>'> 
