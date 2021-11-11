<?php
header("refresh: 30;");

$user = 'birder';
$password = 'databasepassword';

$database = 'birds';

$servername='localhost';
$mysqli = new mysqli($servername, $user, $password, $database);

if ($mysqli->connect_error) {
	die('Connect Error (' .
		$mysqli->connect_errno . ') '.
		$mysqli->connect_error);
}

// SQL query to select data from database
$sql = "SELECT * FROM detections
       ORDER BY Date DESC, Time DESC";
$fulltable = $mysqli->query($sql);
$totalcount=mysqli_num_rows($fulltable);

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
	GROUP BY Com_Name 
	ORDER BY MAX(Confidence) DESC";
$specieslist = $mysqli->query($sql4);
$speciescount=mysqli_num_rows($specieslist);

$sql5 = "SELECT Com_Name,COUNT(*) 
	AS Total 
	FROM detections 
	GROUP BY Com_Name
	ORDER BY Total DESC";
$speciestally = $mysqli->query($sql5);

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<!-- <meta name="viewport" content="width=device-width, initial-scale=1"> -->
	<title>BirdNET-Pi DB</title>
	<!-- CSS FOR STYLING THE PAGE -->
<link rel="stylesheet" href="style.css">
<style>
</style>
</head>
<body style="background-color: rgb(119, 196, 135);background-image: linear-gradient(to top, rgb(119, 196, 135),black;">

	<section>
<div class="row">
 <div cladd="column" style="width: 75%;padding-left: 15%;">
		<h2>Number of Detections</h2>
		<table>
			<tr>
				<th>Total</th>
				<th>Today</th>
				<th>Last Hour</th>
				<th>Number of Unique Species</th>
			</tr>
			<tr>
				<td><?php echo $totalcount;?></td>
				<td><?php echo $todayscount;?></td>
				<td><?php echo $lasthourcount;?></td>
				<td><?php echo $speciescount;?></td>
			</tr>
		</table>
</div>
</div>
<div class="row">
  <div class="column">
		<h2>Detected Species</h2>
		<table>
			<tr>
				<th>Species</th>
				<th>Date</th>
				<th>Time</th>
				<th>Max Confidence Score</th>
			</tr>
<?php // LOOP TILL END OF DATA
while($rows=$specieslist ->fetch_assoc())
{
?>
			<tr>
				<td><?php echo $rows['Com_Name'];?></td>
				<td><?php echo $rows['Date'];?></td>
				<td><?php echo $rows['Time'];?></td>
				<td><?php echo $rows['MAX(Confidence)'];?></td>

			</tr>
<?php
}
?>
		</table>
  </div>
  <div class="column">
		<h2>Species Statistics</h2>
		<table>
			<tr>
				<th>Species</th>
				<th>Detections</th>
			</tr>
<?php // LOOP TILL END OF DATA
while($rows=$speciestally ->fetch_assoc())
{
?>
			<tr>
				<td><?php echo $rows['Com_Name'];?></td>
				<td><?php echo $rows['Total'];?></td>
			</tr>
<?php
}
?>
		</table>
  </div>
</div>
		<h2>Today's Detections</h2>
		<!-- TABLE CONSTRUCTION-->
		<table>
			<tr>
				<th>Time</th>
				<th>Scientific Name</th>
				<th>Common Name</th>
				<th>Confidence</th>
				<th>Lat</th>
				<th>Lon</th>
				<th>Cutoff</th>
				<th>Week</th>
				<th>Sens</th>
				<th>Overlap</th>
			</tr>
			<!-- PHP CODE TO FETCH DATA FROM ROWS-->
<?php // LOOP TILL END OF DATA
while($rows=$mosttable ->fetch_assoc())
{
?>
			<tr>
				<!--FETCHING DATA FROM EACH
					ROW OF EVERY COLUMN-->
				<td><?php echo $rows['Time'];?></td>
				<td><?php echo $rows['Sci_Name'];?></td>
				<td><?php echo $rows['Com_Name'];?></td>
				<td><?php echo $rows['Confidence'];?></td>
				<td><?php echo $rows['Lat'];?></td>
				<td><?php echo $rows['Lon'];?></td>
				<td><?php echo $rows['Cutoff'];?></td>
				<td><?php echo $rows['Week'];?></td>
				<td><?php echo $rows['Sens'];?></td>
				<td><?php echo $rows['Overlap'];?></td>
			</tr>
<?php
}
?>
		</table>
	</section>
</div>
</html>

