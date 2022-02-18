<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("refresh: 300;");
$myDate = date('Y-m-d');
$chart = "Combo-$myDate.png";

$db = new SQLite3('/home/pi/BirdNET-Pi/scripts/birds.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

$statement = $db->prepare('SELECT COUNT(*) FROM detections');
$result = $statement->execute();
$totalcount = $result->fetchArray(SQLITE3_ASSOC);

$statement2 = $db->prepare('SELECT COUNT(*) FROM detections WHERE Date == DATE(\'now\')');
$result2 = $statement2->execute();
$todaycount = $result2->fetchArray(SQLITE3_ASSOC);

$statement3 = $db->prepare('SELECT COUNT(*) FROM detections WHERE TIME >= TIME(\'now\', \'localtime\', \'-1 hour\')');
$result3 = $statement3->execute();
$hourcount = $result3->fetchArray(SQLITE3_ASSOC);

$statement4 = $db->prepare('SELECT Com_Name, Sci_Name, Time, Confidence FROM detections LIMIT 1');
$result4 = $statement4->execute();
$mostrecent = $result4->fetchArray(SQLITE3_ASSOC);
$comlink = preg_replace('/ /', '_', $mostrecent['Com_Name']);
$scilink = preg_replace('/ /', '_', $mostrecent['Sci_Name']);

$statement5 = $db->prepare('SELECT COUNT(DISTINCT(Com_Name)) FROM detections WHERE Date == Date(\'now\')');
$result5 = $statement5->execute();
$speciestally = $result5->fetchArray(SQLITE3_ASSOC);
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
    <table>
      <tr>
        <th>Most Recent Detection</th>
	<td><a href="/By_Date/<?php echo $myDate."/".$comlink;?>"><?php echo $mostrecent['Com_Name'];?></a></td>
	<td><a href="/By_Date/<?php echo$myDate;?>"/><?php echo $mostrecent['Time'];?></a></td>
	<td><?php echo $mostrecent['Confidence'];?></td>
	<td><a href="https://wikipedia.org/wiki/<?php echo $scilink;?>" target="top"/>More Info</a></td>
      </tr>
    </table>
  </div>
</div>

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
        <td><?php echo $totalcount['COUNT(*)'];?></td>
        <td><?php echo $todaycount['COUNT(*)'];?></td>
        <td><?php echo $hourcount['COUNT(*)'];?></td>
      </tr>
    </table>
  </div>
 <div class="column">
    <table>
      <tr>
        <th>Species Detected Today</th>
	<td><?php echo $speciestally['COUNT(DISTINCT(Com_Name))'];?></td>
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
