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
$sql0 = "SELECT * FROM detections";
$fulltable = $mysqli->query($sql0);
$totalcount = mysqli_num_rows($fulltable);

$sql1 = "SELECT Com_Name, Date, Time FROM detections 
  ORDER BY Date DESC, Time DESC LIMIT 1";
$mostrecent = $mysqli->query($sql1);

$sql2 = "SELECT * FROM detections 
  WHERE Date = CURDATE()";
$todaystable = $mysqli->query($sql2);
$todayscount = mysqli_num_rows($todaystable);


$sql3 = "SELECT * FROM detections 
  WHERE Date = CURDATE() 
  AND Time >= DATE_SUB(NOW(),INTERVAL 1 HOUR)";
$lasthourtable = $mysqli->query($sql3);
$lasthourcount = mysqli_num_rows($lasthourtable);

$sql4 = "SELECT Com_Name, Date, Time, MAX(Confidence) 
  FROM detections 
  WHERE Date = CURDATE()	
  GROUP BY Com_Name 
  ORDER BY MAX(Confidence) DESC";
$specieslist = $mysqli->query($sql4);
$speciescount = mysqli_num_rows($specieslist);

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Overview</title>
  <!-- CSS FOR STYLING THE PAGE -->
<link rel="stylesheet" href="style.css">


<style>
.center {
  display: block;
  margin-left: 5px;
  margin-right: 5px;
  width: 90%;
  padding: 5px;
}
.center2 {
  display: block;
  margin-left: 5px;
  margin-right: 5px;
  width: 100%;
  padding: 5px;
}
</style>
</head>
<body style="background-color: rgb(119, 196, 135);">
    <h2>Overview</h2>
<div class="row">
 <div class="column2">
<?php // LOOP TILL END OF DATA
while($rows=$mostrecent ->fetch_assoc())
{
?>
    <table>
      <tr>
        <th>Most Recent Detection</th>
        <td><?php echo $rows['Com_Name'];?></td>
        <td><?php echo $rows['Date'];?></td>
        <td><?php echo $rows['Time'];?></td>
      </tr>
    </table>
  </div>
</div>
<?php
}
?>

<div class="row">
 <div class="column">
    <table>
      <tr>
        <th></th>
        <th>Total</th>
        <th>Today</th>
        <th>Last Hour</th>
      </tr>
      <tr>
        <th>Number of Detections</th>
        <td><?php echo $totalcount;?></td>
        <td><?php echo $todayscount;?></td>
        <td><?php echo $lasthourcount;?></td>
      </tr>
    </table>
  </div>
 <div class="column">
    <table>
      <tr>
        <th>Species Detected Today</th>
        <td><?php echo $speciescount;?></td>
      </tr>
    </table>
  </div>
</div>
    <h2>Today's Top 10 Species</h2>
<?php
if (file_exists('/home/pi/BirdSongs/Extracted/Charts/'.$chart)) {
  echo "<img src=\"/Charts/$chart?nocache=$time()\" style=\"width: 100%;padding: 5px;margin-left: auto;margin-right: auto;display: block;\">";
} else {
    echo "<p style=\"text-align:center;margin-left:-150px;\">No Detections For Today</p>";
}
?>
    <h2>Currently Analyzing</h2>
<img src='/spectrogram.png?nocache=<?php echo time();?>' style="width: 100%;padding: 5px;">
</html>
