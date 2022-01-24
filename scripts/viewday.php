<?php
header("refresh: 300;");
$myDate = date('d-m-Y');
$chart = "Combo-$myDate.png";
$mysqli = mysqli_connect();
$mysqli->select_db('birds');

if ($mysqli->connect_error) {
	die('Connect Error (' .
		$mysqli->connect_errno . ') '.
		$mysqli->connect_error);
}

// SQL query to select data from database

$sql1 = "SELECT * FROM detections 
        WHERE Date = CURDATE()	
	ORDER BY Date DESC, Time DESC";
$mosttable = $mysqli->query($sql1);

$sql2 = "SELECT * FROM detections 
	WHERE Date = CURDATE()";
$todaystable = $mysqli->query($sql2);
$todayscount=mysqli_num_rows($todaystable);


$sql3 = "SELECT * FROM detections 
	WHERE Date = CURDATE() 
	AND Time >= DATE_SUB(NOW(),INTERVAL 1 HOUR)";
$lasthourtable = $mysqli->query($sql3);
$lasthourcount=mysqli_num_rows($lasthourtable);

$sql4 = "SELECT Com_Name, Date, Time, MAX(Confidence) 
	FROM detections 
	WHERE Date = CURDATE()	
	GROUP BY Com_Name 
	ORDER BY MAX(Confidence) DESC";
$specieslist = $mysqli->query($sql4);
$speciescount=mysqli_num_rows($specieslist);

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
	<title>Today's View</title>
	<!-- CSS FOR STYLING THE PAGE -->
<link rel="stylesheet" href="style.css">


<style>
.center {
  display: block;
  margin-left: auto;
  margin-right: auto;
  width: 100%;
}
table,th,td {
  background-color: rgb(219, 255, 235);
}
</style>
</head>

<?php
if (file_exists('/home/pi/BirdSongs/Extracted/Charts/'.$chart)) {
  echo "<img src=\"/Charts/$chart?nocache=$time()\" style=\"width: 100%;padding: 5px;margin-left: auto;margin-right: auto;display: block;\">";
} else {
    echo "<p style=\"text-align:center;margin-left:-150px;\">No Detections For Today</p>";
}
?>
<body style="background-color: rgb(119, 196, 135);">

	<section>
<div class="row">
 <div class="column2">
		<table>
			<tr>
				<th></th>
				<th>Today</th>
				<th>Last Hour</th>
				<th>Number of Unique Species Today</th>
			</tr>
			<tr>

				<th>Number of Detections</th>
				<td><?php echo $todayscount;?></td>
				<td><?php echo $lasthourcount;?></td>
				<td><?php echo $speciescount;?></td>
			</tr>
		</table>

</div>

</div>
	</section>
</div>

</html>
