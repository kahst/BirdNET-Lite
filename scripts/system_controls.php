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
			<form action="" method="POST" onclick="return confirm('Are you sure you want to reboot?')">
				<input type="hidden" name="submit" value="sudo reboot">
				<button type="submit" class="block">Reboot</button>
			</form>
			<form action="" method="POST" onclick="return confirm('BE SURE TO STASH ANY LOCAL CHANGES YOU HAVE MADE TO THE SYSTEM BEFORE UPDATING!!!')">
				<input type="hidden" name="submit" value="update_birdnet.sh">
				<button style="color:blue;" type="submit" class="block">Update</button>
			</form>
			<form action="" method="POST" onclick="return confirm('Are you sure you want to shutdown?')">
				<input type="hidden" name="submit" value="sudo shutdown now">
				<button style="color: red;" type="submit" class="block">Shutdown</button>
			</form>
			<form action="" method="POST" onclick="return confirm('Clear ALL Data? This cannot be undone.')">
				<input type="hidden" name="submit" value="clear_all_data.sh">
				<button style="color: red;" type="submit" class="block">Clear ALL data</button>
			</form>	
		</div>
		<div class="column second">
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(isset($_POST['submit'])){
$command = $_POST['submit'];
if($command == 'update_birdnet.sh'){
  $str= "<h3>Updating . . . </h3>
        <p>Please wait 60 seconds</p>";
  echo str_pad($str, 4096);
  ob_flush();
  flush();
}
if(isset($command)){
$results = shell_exec("$command 2>&1");
echo "</div>
	</div>
<pre>$results</pre>";
}
}
ob_end_flush();
?>
</body>
