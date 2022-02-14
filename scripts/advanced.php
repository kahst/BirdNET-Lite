<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (file_exists('/home/pi/BirdNET-Pi/thisrun.txt')) {
  $config = parse_ini_file('/home/pi/BirdNET-Pi/thisrun.txt');
} elseif (file_exists('/home/pi/BirdNET-Pi/firstrun.ini')) {
  $config = parse_ini_file('/home/pi/BirdNET-Pi/firstrun.ini');
}

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

  if(isset($_POST["db_pwd"])) {
    $db_pwd = $_POST["db_pwd"];
    if(strcmp($db_pwd,$config['DB_PWD']) !== 0) {
      shell_exec('sudo /usr/local/bin/update_db_pwd_bullseye.sh');
      $contents = preg_replace("/DB_PWD=.*/", "DB_PWD=$db_pwd", $contents);
      $contents2 = preg_replace("/DB_PWD=.*/", "DB_PWD=$db_pwd", $contents2);
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
  @session_start();
  if(true){
    $_SESSION['success'] = 1;
  }
}
?>

<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type=number] {
  -moz-appearance: textfield;
}
* {
  font-family: 'Arial', 'Gill Sans', 'Gill Sans MT',
  ' Calibri', 'Trebuchet MS', 'sans-serif';
  box-sizing: border-box;
}
/* Create two unequal columns that floats next to each other */
.column {
  float: left;
  padding: 10px;
}
.first {
  width: calc(50% - 70px);
}
.second {
  width: calc(50% - 70px);
}
.
/* Clear floats after the columns */
.row:after {
  content: "";
  display: table;
  clear: both;
}
body {
  background-color: rgb(119, 196, 135);
}
a {
  font-size:large;
  text-decoration: none;
}
.block {
  display: block;
  width:50%;
  border: none;
  padding: 10px 10px;
  font-size: medium;
  cursor: pointer;
  text-align: center;
}

form {
  text-align:left;
  margin-left:20px;
}
h2 {
  margin-bottom:0px;
}
h3 {
  margin-left: -10px;
  text-align:left;
}
label {
  float:left;
  width: 40%;
  font-weight:bold;
}
input,select {
  width: 60%;
  text-align:center;
  font-size:large;
}
@media screen and (max-width: 1000px) {
  h2,h3 {
    text-align:center;
  }  
  form {
    margin:0;
  }
  .column {
    float: none;
    width: 100%;
  }
  input, label {
    width: 100%;
  {
}
  </style>
  </head>
<?php
if (file_exists('/home/pi/BirdNET-Pi/thisrun.txt')) {
  $newconfig = parse_ini_file('/home/pi/BirdNET-Pi/thisrun.txt');
} elseif (file_exists('/home/pi/BirdNET-Pi/firstrun.ini')) {
  $newconfig = parse_ini_file('/home/pi/BirdNET-Pi/firstrun.ini');
}
?>
      <h2>Advanced Settings</h2>
  <body style="background-color: rgb(119, 196, 135);">
  <div class="row">
    <div class="column first">
    <form action="advanced.php" method="POST">
      <h3>Defaults</h3>
      <label>Full Disk Behavior: </label>
      <label style="width:30%;" for="purge">
      <input style="width:15%;" name="full_disk" type="radio" id="purge" value="purge" 
<?php
if (strcmp($newconfig['FULL_DISK'], "purge") == 0) {
  echo "checked";
}?>>Purge</label>
      <label style="width:30%;" for="keep">
      <input style="width:15%" name="full_disk" type="radio" id="keep" value="keep" 
<?php
  if (strcmp($newconfig['FULL_DISK'], "keep") == 0) {
    echo "checked";
  }?>>Keep</label>
      <p>When the disk becomes full, you can choose to 'purge' old files to make room for new ones or 'keep' your data and stop all services instead.</p>
      <label for="rec_card">Audio Card: </label>
      <input name="rec_card" type="text" value="<?php print($newconfig['REC_CARD']);?>" required/><br>
      <p>Set Audio Card to 'default' to use PulseAudio (always recommended), or an ALSA recognized sound card device from the output of `aplay -L`.</p>
      <label for="channels">Audio Channels: </label>
      <input name="channels" type="number" min="1" max="32" step="1" value="<?php print($newconfig['CHANNELS']);?>" required/><br>
      <p>Set Channels to the number of channels supported by your sound card. 32 max.</p>
      <label for="recording_length">Recording Length: </label>
      <input name="recording_length" type="number" min="3" max="60" step="1" value="<?php print($newconfig['RECORDING_LENGTH']);?>" required/><br>
  <p>Set Recording Length in seconds between 6 and 60. Multiples of 3 are recommended, as BirdNET analyzes in 3-second chunks.</p> 
      <label for="extraction_length">Extraction Length: </label>
      <input name="extraction_length" type="number" min="3" max="<?php print($newconfig['RECORDING_LENGTH']);?>" value="<?php print($newconfig['EXTRACTION_LENGTH']);?>" /><br>
      <p>Set Extraction Length to something less than your Recording Length. Min=3 Max=Recording Length</p>
      <label for="audiofmt">Extractions Audio Format</label>
      <select name="audiofmt">
      <option selected="<?php print($newconfig['AUDIOFMT']);?>"><?php print($newconfig['AUDIOFMT']);?></option>
<?php
  $formats = array("8svx", "aif", "aifc", "aiff", "aiffc", "al", "amb", "amr-nb", "amr-wb", "anb", "au", "avr", "awb", "caf", "cdda", "cdr", "cvs", "cvsd", "cvu", "dat", "dvms", "f32", "f4", "f64", "f8", "fap", "flac", "fssd", "gsm", "gsrt", "hcom", "htk", "ima", "ircam", "la", "lpc", "lpc10", "lu", "mat", "mat4", "mat5", "maud", "mp2", "mp3", "nist", "ogg", "paf", "prc", "pvf", "raw", "s1", "s16", "s2", "s24", "s3", "s32", "s4", "s8", "sb", "sd2", "sds", "sf", "sl", "sln", "smp", "snd", "sndfile", "sndr", "sndt", "sou", "sox", "sph", "sw", "txw", "u1", "u16", "u2", "u24", "u3", "u32", "u4", "u8", "ub", "ul", "uw", "vms", "voc", "vorbis", "vox", "w64", "wav", "wavpcm", "wv", "wve", "xa", "xi");
foreach($formats as $format){
  echo "<option value='$format'>$format</option>";
}
?>
      </select>
      <h3>Passwords</h3>
      <label for="caddy_pwd">Webpage: </label>
      <input name="caddy_pwd" type="text" value="<?php print($newconfig['CADDY_PWD']);?>" /><br>
      <p>This password protects the Live Audio Stream, the Processed extractions, phpSysInfo, your Tools, and WebTerminal. When you update this value, the web server will reload, so wait about 30 seconds and then reload the page.</p>
      <label for="db_pwd">Database: </label>
      <input name="db_pwd" type="text" value="<?php print($newconfig['DB_PWD']);?>" required/><br>
      <p>This password protects the database. When you update this value, it will be updated automatically.</p>
      <label for="ice_pwd">Live Audio Stream: </label>
      <input name="ice_pwd" type="text" value="<?php print($newconfig['ICE_PWD']);?>" required/><br>
    </div>
    <div class="column second">
      <h3>Custom URLs</h3>
      <p>When you update any of the URL settings below, the web server will reload, so be sure to wait at least 30 seconds and then reload the page.</p>
      <label for="birdnetpi_url">BirdNET-Pi URL: </label>
      <input name="birdnetpi_url" type="url" value="<?php print($newconfig['BIRDNETPI_URL']);?>" /><br>
      <p>This URL is how the main page will be reached. If you want your installation to respond to an IP address, place that here, but be sure to indicate `http://`.<br>Example for IP:http://192.168.0.109<br>Example if you own your own domain:https://birdnetpi.pmcgui.xyz</p>
      <label for="birdnetlog_url">BirdNET-Lite Log URL: </label>
      <input name="birdnetlog_url" type="url" value="<?php print($newconfig['BIRDNETLOG_URL']);?>" /><br>
      <p>This URL is how the log will be reached. Only use this variable if you own your own domain.</p>
      <label for="webterminal_url">Web Terminal URL: </label>
      <input name="webterminal_url" type="url" value="<?php print($newconfig['WEBTERMINAL_URL']);?>" /><br>
      <p>This URL is how the Web browser terminal will be reached. Only use this variable if you own your own domain.</p>
      <h3>BirdNET-Lite Settings</h3>
      <label for="overlap">Overlap: </label>
      <input name="overlap" type="number" min="0.0" max="2.9" step="0.1" value="<?php print($newconfig['OVERLAP']);?>" required/><br>
      <p>Min=0.0, Max=2.9</p>
      <label for="confidence">Minimum Confidence: </label>
      <input name="confidence" type="number" min="0.01" max="0.99" step="0.01" value="<?php print($newconfig['CONFIDENCE']);?>" required/><br>
      <p>Min=0.01, Max=0.99</p>
      <label for="sensitivity">Sigmoid Sensitivity: </label>
      <input name="sensitivity" type="number" min="0.5" max="1.5" step="0.01" value="<?php print($newconfig['SENSITIVITY']);?>" required/><br>
      <p>Min=0.5, Max=1.5</p>
      <br><br>
      <button type="submit" name="submit" class="block"><?php

if(isset($_SESSION['success'])){
  echo "Success!";
  unset($_SESSION['success']);
} else {
  echo "Update Settings";
}
?></button>
      <br>
    </form>
    <form action="config.php" style="margin:0;">
      <button type="submit" class="block">Basic Settings</button>
    </form>
      <br>
    <form action="index.html" style="margin:0;">
      <button type="submit" class="block">Tools</button>
    </form>
</div>
</div>
</body>
