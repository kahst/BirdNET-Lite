<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(isset($_POST['date'])){
$theDate = $_POST['date'];
} else {
$theDate = date('Y-m-d');
}
$chart = "Combo-$theDate.png";
$chart2 = "Combo2-$theDate.png";
$mysqli = mysqli_connect();
$mysqli->select_db('birds');

if ($mysqli->connect_error) {
	die('Connect Error (' .
		$mysqli->connect_errno . ') '.
		$mysqli->connect_error);
}

// SQL query to select data from database

$sql1 = "SELECT COUNT(*)
	FROM detections
	WHERE DATE = \"$theDate\"";
$dayscount = $mysqli->query($sql1);

$mysqli->close();
?>

<head>
<link rel="stylesheet" href="style.css">

<style>
input {
  width:auto;
}
center {
  display: block;
  margin-left: auto;
  margin-right: auto;
  width: 100%;
}
body {
  background-color: rgb(119, 196, 135);
}
button,input {
  font-size: medium;
}
table,th,td {
  background-color: rgb(219, 255, 235);
}
table {
  width:30%;
}
hr {
  border: 1px solid green;
  width:80%;
}
</style>
</head>
<body>
<form style="margin-left: -150px;text-align:center;" action="" name="submit" method="POST">
  <input type="date" name="date" value="<?php echo $theDate;?>">
  <button type="submit" class="block">Submit Date</button>
</form>
<div style="margin-left: -150px;">
		<table>
			<tr>
				<th>Total Detections For The Day</th>
<?php
while($row=$dayscount->fetch_assoc()){
?>
				<td><?php echo $row['COUNT(*)'];?></td>
<?php
}?>
			</tr>
		</table>
</div>

<?php
if (file_exists('/home/pi/BirdSongs/Extracted/Charts/'.$chart)) {
  echo "<img src=\"/Charts/$chart?nocache=time()\" style=\"height:auto;width: 100%;padding: 5px;margin-left: auto;margin-right: auto;display: block;\">";
} else {
  echo "<p style=\"text-align:center;margin-left:-150px;\">No Charts for $theDate</p>";
}
echo "<hr>";
if (file_exists('/home/pi/BirdSongs/Extracted/Charts/'.$chart2)) {
  echo "<img src=\"/Charts/$chart2?nocache=time()\" style=\"height:auto;width: 100%;padding: 5px;margin-left: auto;margin-right: auto;display: block;\">";
} else {
  echo "<p style=\"text-align:center;margin-left:-150px;\">No Charts For $theDate</p>";
}?>
</html>
