<?php
header("refresh: 300;");
$myDate = date('d-m-Y');
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
<body style="background-color: rgb(119, 196, 135);background-image: linear-gradient(to top, rgb(119, 196, 135),black;">

	<section>
<div class="row">
 <div cladd="column" style="width: 100%;padding-left: 15%;padding-right: 15%;padding-bottom: 10px;">
		<h2>Number of Detections</h2>
		<table>
			<tr>

				<th>Today</th>
				<th>Last Hour</th>
				<th>Number of Unique Species Today</th>
			</tr>
			<tr>

				<td><?php echo $todayscount;?></td>
				<td><?php echo $lasthourcount;?></td>
				<td><?php echo $speciescount;?></td>
			</tr>
		</table>

</div>

</div>
	</section>
</div>

<img src='/Charts/Combo-<?php echo $myDate;?>.png?nocache=<?php echo time();?>' class="center">
</html>
