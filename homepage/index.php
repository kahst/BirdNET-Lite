<link rel="stylesheet" href="style.css">
<div class="banner">
<?php
echo "<meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">";
if(isset($_GET['stream'])){
  echo "<h1>BirdNET-Pi</h1><br><audio controls autoplay><source src=\"/stream\"></audio>";
} else {
  echo "<h1>BirdNET-Pi</h1><br><form action=\"\" method=\"GET\"><button type=\"submit\" name=\"stream\" value=\"play\">Live Audio</button></form>";
}
echo "</div>";
if(isset($_GET['log'])){
  if (file_exists('/home/pi/BirdNET-Pi/thisrun.txt')) {
    $config = parse_ini_file('/home/pi/BirdNET-Pi/thisrun.txt');
    $logs = $config['BIRDNETLOG_URL'];
  } elseif (file_exists('/home/pi/BirdNET-Pi/firstrun.ini')) {
    $config = parse_ini_file('/home/pi/BirdNET-Pi/firstrun.ini');
    $logs = $config['BIRDNETLOG_URL'];
  }
  header("Location: $logs");
}elseif(isset($_GET['spectrogram'])){
  header("Location: /spectrogram.php");
} else {
  echo "<iframe src=\"/views.php\" width=\"100%\" height=\"85%\">";
}
