<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
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
input {
  width: 60%;
  text-align:center;
  font-size:large;
}
@media screen and (max-width: 800px) {
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
      <h2>Advanced Settings</h2>
  <body style="background-color: rgb(119, 196, 135);">
  <div class="row">
    <div class="column first">
    <form action="write_advanced.php" method="POST">
<?php 

if (file_exists('/home/pi/BirdNET-Pi/thisrun.txt')) {
	$config = parse_ini_file('/home/pi/BirdNET-Pi/thisrun.txt');
} elseif (file_exists('/home/pi/BirdNET-Pi/firstrun.ini')) {
	$config = parse_ini_file('/home/pi/BirdNET-Pi/firstrun.ini');
} ?>
      <h3>Defaults</h3>
      <label>Full Disk Behavior: </label>
      <label style="width:30%;" for="purge">
      <input style="width:15%;" name="full_disk" type="radio" id="purge" value="purge" 
<?php
if (strcmp($config['FULL_DISK'], "purge") == 0) {
	echo "checked";
}?>>Purge</label>
      <label style="width:30%;" for="keep">
      <input style="width:15%" name="full_disk" type="radio" id="keep" value="keep" 
<?php
if (strcmp($config['FULL_DISK'], "keep") == 0) {
	echo "checked";
}?>>Keep</label>
      <label for="rec_card">Audio Card: </label>
      <input name="rec_card" type="text" value="<?php print($config['REC_CARD']);?>" required/><br>
      <label for="channels">Audio Channels: </label>
      <input name="channels" type="number" min="1" max="32" step="1" value="<?php print($config['CHANNELS']);?>" required/><br>
      <label for="recording_length">Recording Length: </label>
      <input name="recording_length" type="number" min="12" max="60" step="1" value="<?php print($config['RECORDING_LENGTH']);?>" /><br>
      <label for="extraction_length">Extraction Length: </label>
      <input name="extraction_length" type="number" min="3" max="<?php print($config['RECORDING_LENGTH']);?>" value="<?php print($config['EXTRACTION_LENGTH']);?>" /><br>
      <h3>Passwords</h3>
      <label for="caddy_pwd">Webpage: </label>
      <input name="caddy_pwd" type="text" value="<?php print($config['CADDY_PWD']);?>" /><br>
      <label for="db_pwd">Database: </label>
      <input name="db_pwd" type="text" value="<?php print($config['DB_PWD']);?>" required/><br>
      <label for="ice_pwd">Live Audio Stream: </label>
      <input name="ice_pwd" type="text" value="<?php print($config['ICE_PWD']);?>" required/><br>
    </div>
    <div class="column second">
      <h3>Custom URLs</h3>
      <label for="birdnetpi_url">BirdNET-Pi URL: </label>
      <input name="birdnetpi_url" type="text" value="<?php print($config['BIRDNETPI_URL']);?>" /><br>
      <label for="birdnetlog_url">BirdNET-Lite Log URL: </label>
      <input name="birdnetlog_url" type="text" value="<?php print($config['BIRDNETLOG_URL']);?>" /><br>
      <h3>BirdNET-Lite Settings</h3>
      <label for="overlap">Overlap: </label>
      <input name="overlap" type="number" min="0.0" max="2.9" step="0.1" value="<?php print($config['OVERLAP']);?>" required/><br>
      <label for="confidence">Minimum Confidence: </label>
      <input name="confidence" type="number" min="0.01" max="0.99" step="0.01" value="<?php print($config['CONFIDENCE']);?>" required/><br>
      <label for="sensitivity">Sigmoid Sensitivity: </label>
      <input name="sensitivity" type="number" min="0.5" max="1.5" step="0.01" value="<?php print($config['SENSITIVITY']);?>" required/><br>
      <br><br>
      <button type="submit" class="block"><?php
	@session_start();

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

