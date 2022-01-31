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
	width:50%;
	border: none;
	padding: 10px 10px;
	font-size: medium;
	cursor: pointer;
	text-align: center;
}

select {
  font-size:large;
  width: 60%;
}

select option {
  font-size:large;
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
  width:40%;
  font-weight:bold;
}
input {
  width:60%;
  text-align:center;
  font-size:large;
}
@media screen and (max-width: 800px) {
  select {
    width:100%;
  }
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
  input, label  {
    width:100%;
  {
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
      <label for="latitude">Latitude: </label>
      <input name="latitude" type="number" max="90" min="-90" step="0.0001" value="<?php print($config['LATITUDE']);?>" required/><br>
      <label for="longitude">Longitude: </label>
      <input name="longitude" type="number" max="180" min="-180" step="0.0001" value="<?php print($config['LONGITUDE']);?>" required/><br>
      <label for="birdweather_id">BirdWeather ID: </label>
      <input name="birdweather_id" type="text" value="<?php print($config['BIRDWEATHER_ID']);?>" /><br>
      <label for="pushed_app_key">Pushed App Key: </label>
      <input name="pushed_app_key" type="text" value="<?php print($config['PUSHED_APP_KEY']);?>" /><br>
      <label for="pushed_app_secret">Pushed App Secret: </label>
      <input name="pushed_app_secret" type="text" value="<?php print($config['PUSHED_APP_SECRET']);?>" /><br>
      <label for="language">Database Language: </label>
      <select name="language">
        <option value="none">Select your language</option>
        <option value="labels_af.txt">Afrikaans</option>
        <option value="labels_ca.txt">Catalan</option>
        <option value="labels_cs.txt">Czech</option>
        <option value="labels_zh.txt">Chinese</option>
        <option value="labels_hr.txt">Croatian</option>
        <option value="labels_da.txt">Danish</option>
        <option value="labels_nl.txt">Dutch</option>
        <option value="labels_en.txt">English</option>
        <option value="labels_et.txt">Estonian</option>
        <option value="labels_fi.txt">Finnish</option>
        <option value="labels_fr.txt">French</option>
        <option value="labels_de.txt">German</option>
        <option value="labels_hu.txt">Hungarian</option>
        <option value="labels_is.txt">Icelandic</option>
        <option value="labels_id.txt">Indonesia</option>
        <option value="labels_it.txt">Italian</option>
        <option value="labels_ja.txt">Japanese</option>
        <option value="labels_lv.txt">Latvian</option>
        <option value="labels_lt.txt">Lithuania</option>
        <option value="labels_no.txt">Norwegian</option>
        <option value="labels_pl.txt">Polish</option>
        <option value="labels_pt.txt">Portugues</option>
        <option value="labels_ru.txt">Russian</option>
        <option value="labels_sk.txt">Slovak</option>
        <option value="labels_sl.txt">Slovenian</option>
        <option value="labels_es.txt">Spanish</option>
        <option value="labels_sv.txt">Swedish</option>
        <option value="labels_th.txt">Thai</option>
        <option value="labels_uk.txt">Ukrainian</option>
      </select>
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
    </form>
    <form action="advanced.php" class="form2">
      <button type="submit" class="block">Advanced Settings</button>
    </form>
    <form action="index.html" class="form2">
      <button type="submit" class="block">Tools</button>
    </form>
    </div>
  </div>
</body>
<input type="reset" form=">
