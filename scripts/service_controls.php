<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
* {
  font-family: 'Arial', 'Gill Sans', 'Gill Sans MT',
  ' Calibri', 'Trebuchet MS', 'sans-serif';
  box-sizing: border-box;

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
	color: white;
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
		@media screen and (max-width: 800px) {
			.column {
				float: none;
				width: 100%;
			}
		}
</style>
<body style="background-color: rgb(119, 196, 135);">
	<div class="row">
		<div class="column first">
			<form action="" method="POST" onclick="return confirm('Stop core services?')">
				<input type="hidden" name="submit" value="stop_core_services.sh">
				<button type="submit" class="block">Stop Core Services</button>
			</form>
			<form action="" method="POST" onclick="return confirm('Restart ALL services?')">
				<input type="hidden" name="submit" value="restart_services.sh">
				<button type="submit" class="block">Restart ALL Services</button>
			</form>
			<form action="" method="POST">
				<input type="hidden" name="submit" value="sudo systemctl restart birdnet_analysis.service">
				<button type="submit" class="block">Restart BirdNET Analysis</button>
			</form>
			<form action="" method="POST">
				<input type="hidden" name="submit" value="sudo systemctl restart birdnet_recording.service">
				<button type="submit" class="block">Restart Recording</button>
			</form>
			<form action="" method="POST" onclick="return confirm('Restart Caddy? You will be disconnected for about 20 seconds.')">
				<input type="hidden" name="submit" value="sudo systemctl restart caddy">
				<button type="submit" class="block">Restart Caddy</button>
			</form>
		</div>
		<div class="column second">
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(isset($_POST['submit'])){
$command = $_POST['submit'];
if($command == 'restart_services.sh'){
  $str= "<h3>Restarting Services</h3>
        <p>Please wait 60 seconds</p>";
  echo str_pad($str, 4096);
  ob_flush();
  flush();
}
if(isset($command)){
$results = shell_exec("$command 2>&1");
echo "<pre>$results</pre>";
}
}
ob_end_flush();
?>
		</div>
	</div>
</body>
