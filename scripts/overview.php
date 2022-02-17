<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("refresh: 300;");
$myDate = date('Y-m-d');
$chart = "Combo-$myDate.png";
$mysqli = mysqli_connect();
$mysqli->select_db('birds');

if ($mysqli->connect_error) {
  die('Connect Error (' .
    $mysqli->connect_errno . ') '.
    $mysqli->connect_error);
}

// SQL query to select data from database
$sql0 = "SELECT COUNT(*) AS 'Total' FROM detections";
$totalcount = $mysqli->query($sql0);

$sql1 = "SELECT Com_Name, Sci_Name, Date, Time FROM detections 
  ORDER BY Date DESC, Time DESC LIMIT 1";
$mostrecent = $mysqli->query($sql1);

$sql2 = "SELECT COUNT(*) AS 'Total' FROM detections 
  WHERE Date = CURDATE()";
$todayscount = $mysqli->query($sql2);

$sql3 = "SELECT COUNT(*) AS 'Total' FROM detections 
  WHERE Date = CURDATE() 
  AND Time >= DATE_SUB(NOW(),INTERVAL 1 HOUR)";
$lasthourcount = $mysqli->query($sql3);

$sql4 = "SELECT Com_Name
  FROM detections
  WHERE Date = CURDATE()
  GROUP BY Com_Name";
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
a {
  text-decoration: none;
  color:black;
}
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
  $dbname = preg_replace('/ /', '_', $rows['Com_Name']);
  $dbname = preg_replace('/\'/', '', $dbname);
  $dbsciname = preg_replace('/ /', '_', $rows['Sci_Name']);
?>
    <table>
      <tr>
        <th>Most Recent Detection</th>
	<td><a href="/By_Common_Name/<?php echo $dbname;?>"><?php echo $rows['Com_Name'];?></a></td>
	<td><a href="/By_Date/<?php echo $rows['Date'];?>"/><?php echo $rows['Date'];?></a></td>
        <td><?php echo $rows['Time'];?></td>
	<td><a href="https://wikipedia.org/wiki/<?php echo $dbsciname;?>" target="top"/>More Info</a></td>
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
        <td><?php while ($row = $totalcount->fetch_assoc()) { echo $row['Total']; };?></td>
        <td><?php while ($row = $todayscount->fetch_assoc()) { echo $row['Total']; };?></td>
        <td><?php while ($row = $lasthourcount->fetch_assoc()) { echo $row['Total']; };?></td>
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
<?php
if (file_exists('/home/pi/BirdSongs/Extracted/Charts/'.$chart)) {
  echo "<img src=\"/Charts/$chart?nocache=time()\" style=\"width: 100%;padding: 5px;margin-left: auto;margin-right: auto;display: block;\">";
} else {
    echo "<p style=\"text-align:center;margin-left:-150px;\">No Detections For Today</p>";
}
?>
    <h2>Currently Analyzing</h2>
<img src='/spectrogram.png?nocache=<?php echo time();?>' style="width: 100%;padding: 5px;">
</html>
