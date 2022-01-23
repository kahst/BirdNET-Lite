<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
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
	width: calc(50% - 30px);
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
	text-decoration: none;
}
.block {
	display: block;
	font-weight: bold;
	width:100%;
	border: none;
	background-color: #04AA6D;
	padding: 20px 20px;
	color: white;
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
  font-weight:bold;
}
input {
  text-align:center;
  font-size:large;
}
@media screen and (max-width: 800px) {
  h2 {
    margin-bottom:0px;
    text-align:center;
  }
  form {
    text-align:left;
    margin-left:0px;
  }	
  .column {
		float: none;
		width: 100%;
	}
}
  </style>
  </head>
      <h2>Basic Settings</h2>
  <body style="background-color: rgb(119, 196, 135);">
  <div class="row">
    <div class="column first">
    <form action="write_config.php" method="POST">
<?php 
if (file_exists('/home/pi/BirdNET-Pi/thisrun.txt')) {
  $config = parse_ini_file('/home/pi/BirdNET-Pi/thisrun.txt');
} elseif (file_exists('/home/pi/BirdNET-Pi/firstrun.ini')) {
  $config = parse_ini_file('/home/pi/BirdNET-Pi/firstrun.ini');
} ?>
      <h3>Required</h3>
      <label for="latitude">Latitude: </label>
      <input name="latitude" type="text" value="<?php print($config['LATITUDE']);?>" /><br>
      <label for="longitude">Longitude: </label>
      <input name="longitude" type="text" value="<?php print($config['LONGITUDE']);?>" />
      <h3>Optional</h3>
      <label for="birdweather_id">BirdWeather ID: </label>
      <input name="birdweather_id" type="text" value="<?php print($config['BIRDWEATHER_ID']);?>" /><br>
      <label for="pushed_app_key">Pushed App Key: </label>
      <input name="pushed_app_key" type="text" value="<?php print($config['PUSHED_APP_KEY']);?>" /><br>
      <label for="pushed_app_secret">Pushed App Secret: </label>
      <input name="pushed_app_secret" type="text" value="<?php print($config['PUSHED_APP_SECRET']);?>" /><br>
      <br>
      <input type="submit" value="<?php
  @session_start();

if(isset($_SESSION['success'])){
  echo "Success!";
  unset($_SESSION['success']);
} else {
  echo "Update Settings";
}
?>">
      <br>
      <br>
      <button type="text"><a href="advanced.php">Advanced Settings</a></button>
    </form>
    </div>
  </div>
</body>
