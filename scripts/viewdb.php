<?php

// Username is root
$user = 'birder';
$password = 'databasepassword';

// Database name is gfg
$database = 'birds';

// Server is localhost with
// port number 3308
$servername='localhost';
$mysqli = new mysqli($servername, $user, $password, $database);

// Checking for connections
if ($mysqli->connect_error) {
	die('Connect Error (' .
		$mysqli->connect_errno . ') '.
		$mysqli->connect_error);
}

// SQL query to select data from database
$sql = "SELECT * FROM detections";
$result = $mysqli->query($sql);
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Birds Database, Detections Table</title>
	<!-- CSS FOR STYLING THE PAGE -->
	<style>
		table {
			margin: 0 auto;
			font-size: large;
			border: 1px solid black;
		}

		h1 {
			text-align: center;
			color: #006600;
			font-size: xx-large;
			font-family: 'Gill Sans', 'Gill Sans MT',
			' Calibri', 'Trebuchet MS', 'sans-serif';
		}

		td {
			background-color: #E4F5D4;
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
while($rows=$result->fetch_assoc())
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

