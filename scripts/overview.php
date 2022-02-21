<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("refresh: 300;");
$myDate = date('Y-m-d');
$chart = "Combo-$myDate.png";

$db = new SQLite3('/home/pi/BirdNET-Pi/scripts/birds.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
if($db == False) {
  echo "Database is busy";
  header("refresh: 0;");
}

$statement = $db->prepare('SELECT COUNT(*) FROM detections');
if($statement == False) {
  echo "Database is busy";
  header("refresh: 0;");
}
$result = $statement->execute();
$totalcount = $result->fetchArray(SQLITE3_ASSOC);

$statement2 = $db->prepare('SELECT COUNT(*) FROM detections WHERE Date == DATE(\'now\')');
if($statement2 == False) {
  echo "Database is busy";
  header("refresh: 0;");
}
$result2 = $statement2->execute();
$todaycount = $result2->fetchArray(SQLITE3_ASSOC);

$statement3 = $db->prepare('SELECT COUNT(*) FROM detections WHERE TIME >= TIME(\'now\', \'localtime\', \'-1 hour\')');
if($statement3 == False) {
  echo "Database is busy";
  header("refresh: 0;");
}
$result3 = $statement3->execute();
$hourcount = $result3->fetchArray(SQLITE3_ASSOC);

$statement4 = $db->prepare('SELECT Com_Name, Sci_Name, Date, Time, Confidence, File_Name FROM detections ORDER BY Date DESC, Time DESC LIMIT 1');
if($statement4 == False) {
  echo "Database is busy";
  header("refresh: 0;");
}
$result4 = $statement4->execute();
$mostrecent = $result4->fetchArray(SQLITE3_ASSOC);
$comname = preg_replace('/ /', '_', $mostrecent['Com_Name']);
$scilink = preg_replace('/ /', '_', $mostrecent['Sci_Name']);

$statement5 = $db->prepare('SELECT COUNT(DISTINCT(Com_Name)) FROM detections WHERE Date == Date(\'now\')');
if($statement5 == False) {
  echo "Database is busy";
  header("refresh: 0;");
}
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
table, th {
  background-color: rgb(119, 196, 135);
  border:none;
}
th {
  padding: 0 5px;
}
button {
  background-color: rgb(219, 295, 235);
  border:none;
  font-size:large;
  cursor:pointer;
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
 <div>
  </div>
    <table style="padding-bottom:3%;display:block;width:50%;margin-left:auto;margin-right:auto;">
      <tr>
        <th>Total</th>
        <th>Today</th>
        <th>Last Hour</th>
        <th>Species Detected Today</th>
      </tr>
      <tr>
        <td><?php echo $totalcount['COUNT(*)'];?></td>
	<td><a href="/By_Date/<?php echo date('Y-m-d');?>"/><?php echo $todaycount['COUNT(*)'];?></a></td>
        <td><?php echo $hourcount['COUNT(*)'];?></td>
	<td><a href="/stats.php"/><?php echo $speciestally['COUNT(DISTINCT(Com_Name))'];?></a></td>
      </tr>
    </table>
</div>
 <div>

    <table style="padding-bottom:3%;display:block;width:90%;margin-left:auto;margin-right:auto;">
      <tr>
	<th style="border:none;background-color: rgb(119, 196, 135);"></th>
	<th style="border:none;">Scientific Name</th>
	<th style="border:none;">Common Name</th>
	<th style="border:none;">Listen</th>
	<th style="border:none;">Confidence</th>
      </tr>
      <tr>
        <th>Most Recent Detection</th>
	<td><a href="https://wikipedia.org/wiki/<?php echo $scilink;?>" target="top"/><?php echo $mostrecent['Sci_Name'];?></a></td>
        <form action="/stats.php" name="species" method="POST">
	<td><button type="submit" name="species" value="<?php echo $mostrecent['Com_Name'];?>"><?php echo $mostrecent['Com_Name'];?></button></td></form>
	<td><a href="/By_Date/<?php echo$myDate."/".$comname."/".$mostrecent['File_Name'];?>" target="footer"/><?php echo $mostrecent['Date']." ".$mostrecent['Time'];?></a></td>
	<td><?php echo $mostrecent['Confidence'];?></td>
      </tr>
    </table>
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
