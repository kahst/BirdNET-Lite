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
body {
	width: 50%;
	margin-left: auto;
	margin-right: auto;
	background-color: rgb(119, 196, 135);
}
a {
	text-decoration: none;
}
.block {
	display: block;
	width:100%;
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
	padding:10px;
}

h1 {
  margin-bottom:0px;
}

h3 {
	margin-left: -10px;
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
.center {
  display: block;
  margin-left: auto;
  margin-right: auto;
}
  @media screen and (max-width: 800px) {
	  h1, h2 {
		  text-align:center;
	  }
	  form {
		  text-align:left;
		  margin-left:0px;
	  }	
	  select, body, button, input, label {
		  width:100%;
		  {
		  }
</style>
</head>
<?php
if (file_exists('/home/pi/BirdNET-Pi/thisrun.txt')) {
  $config = parse_ini_file('/home/pi/BirdNET-Pi/thisrun.txt');
} elseif (file_exists('/home/pi/BirdNET-Pi/firstrun.ini')) {
  $config = parse_ini_file('/home/pi/BirdNET-Pi/firstrun.ini');
} ?>

<h1 style="float:top;text-align:center;">Welcome!</h1>
<img class="center" alt="Can't Load Logo" src="../images/red-cardinal.png"/>
<body style="background-color: rgb(119, 196, 135);">
	<form action="write_config.php" method="POST" name="firstboot">
		<p style="font-size:large">Thank you for installing BirdNET-Pi!
		to get started, fill out the Required sections below.</p>
		<h2>Required</h2>
		<label for="latitude">Latitude: </label>
		<input name="latitude" type="number" min="-90" max="90" step="0.0001" value="<?php print($config['LATITUDE']);?>" required/><br>
		<label for="longitude">Longitude: </label>
		<input name="longitude" type="number" min="-180" max="180" step="0.0001" value="<?php print($config['LONGITUDE']);?>" required/><br>
		<h2>Optional Services</h2>
		<p>The services below are not required, but they are pretty cool.</p>
		<label for="birdweather_id">BirdWeather ID: </label>
		<input name="birdweather_id" type="text" value="<?php print($config['BIRDWEATHER_ID']);?>" /><br>
		<p>app.BirdWeather.com is a weather map for bird sounds. Stations around the world supply audio and video streams to BirdWeather where they are then analyzed by BirdNET and compared to eBird Grid data. BirdWeather catalogues the detections and visualizations so that you can listen to, view, and read about the birds in various regions across the globe! Pretty cool! <a href="mailto:tim@birdweather.com?subject=Request%20BirdWeather%20ID&body=<?php include('birdweather_request.php'); ?>" target="top">Email Tim</a> to request a BirdWeather ID</p>

		<label for="pushed_app_key">Pushed App Key: </label>
		<input name="pushed_app_key" type="text" value="" /><br>
		<label for="pushed_app_secret">Pushed App Secret: </label>
		<input name="pushed_app_secret" type="text" value="" /><br>
		<p>Pushed.co is used to offer New Species notifications. Sorry Android users, but the Pushed.co Application is only for iOS.</p>
		<label for="language">Database Language: </label>
		<select name="language">
			<option value="none">English</option>
			<option value="labels_af.txt">Afrikaans</option>
			<option value="labels_ca.txt">Catalan</option>
			<option value="labels_cs.txt">Czech</option>
			<option value="labels_zh.txt">Chinese</option>
			<option value="labels_hr.txt">Croatian</option>
			<option value="labels_da.txt">Danish</option>
			<option value="labels_nl.txt">Dutch</option>
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
</body>
<footer>
	<button type="submit" name"firstboot" class="block">I Am Finished!</button>
</footer>
	</form>
