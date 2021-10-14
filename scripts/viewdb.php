<?php

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

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>BirdNET-Pi DB</title>
	<!-- CSS FOR STYLING THE PAGE -->
	<style>
		@media screen and (max-width: 600px) {
		  .column {
		    width: 100%;
		  }
		}
		* {
		  box-sizing: border-box;
		}

		.row {
		  display: flex;
		  margin-left:-5px;
		  margin-right:-5px;
		}

		.column {
		  flex: 50%;
		  padding: 5px;
		}

		table {
                  margin: 0 auto;
		  font-size: large;
		  border-collapse: collapse;
		  border-spacing: 0;
		  width: 100%;
		  border: 1px solid black;
		}

		h1 {
			text-align: center;
			color: black;
			font-size: xx-large;
			font-family: 'Gill Sans', 'Gill Sans MT',
			' Calibri', 'Trebuchet MS', 'sans-serif';
		}

		h2 {
			text-align: center;
			color: black;
			font-size: large;
			font-family: 'Gill Sans', 'Gill Sans MT',
			' Calibri', 'Trebuchet MS', 'sans-serif';
		}

		td {
			background-color: rgb(119, 196, 135);
			border: 1px solid black;
		}

		th,
		td {
			font-weight: bold;
			border: 1px solid black;
			padding: 10px;
			text-align: center;
		}

		td {
			font-weight: lighter;
		}
	</style>
</head>

<body>
	<section>
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
				<td><?php echo $rows['Com_Name'];?></td>
				<td><?php echo $rows['Date'];?></td>
				<td><?php echo $rows['Time'];?></td>
				<td><?php echo $rows['MAX(Confidence)'];?></td>
			</tr>
<?php
}
?>
		</table>

		<h1>BirdsDB Detections Table</h1>
		<!-- TABLE CONSTRUCTION-->
		<table>
			<tr>
				<th>Date</th>
				<th>Time</th>
				<th>Sci_Name</th>
				<th>Com_Name</th>
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
while($rows=$fulltable ->fetch_assoc())
{
?>
			<tr>
				<!--FETCHING DATA FROM EACH
					ROW OF EVERY COLUMN-->
				<td><?php echo $rows['Date'];?></td>
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
</body>

</html>

