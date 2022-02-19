<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("refresh: 30;");

$db = new SQLite3('/home/pi/BirdNET-Pi/scripts/birds.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

$statement0 = $db->prepare('SELECT Time, Com_Name, Sci_Name, Confidence, File_Name FROM detections WHERE Date == Date(\'now\') ORDER BY Time DESC');
$result0 = $statement0->execute();

$statement1 = $db->prepare('SELECT COUNT(*) FROM detections');
$result1 = $statement1->execute();
$totalcount = $result1->fetchArray(SQLITE3_ASSOC);

$statement2 = $db->prepare('SELECT COUNT(*) FROM detections WHERE Date == DATE(\'now\')');
$result2 = $statement2->execute();
$todaycount = $result2->fetchArray(SQLITE3_ASSOC);

$statement3 = $db->prepare('SELECT COUNT(*) FROM detections WHERE TIME >= TIME(\'now\', \'localtime\', \'-1 hour\')');
$result3 = $statement3->execute();
$hourcount = $result3->fetchArray(SQLITE3_ASSOC);

$statement4 = $db->prepare('SELECT Com_Name, Sci_Name, Time, Confidence FROM detections LIMIT 1');
$result4 = $statement4->execute();
$mostrecent = $result4->fetchArray(SQLITE3_ASSOC);

$statement5 = $db->prepare('SELECT COUNT(DISTINCT(Com_Name)) FROM detections');
$result5 = $statement5->execute();
$speciestally = $result5->fetchArray(SQLITE3_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>BirdNET-Pi DB</title>
  <link rel="stylesheet" href="style.css">
  <style>
a {
  text-decoration:none;
  color:black;
}
.a2 { color:blue;}
</style>
</head>
<body style="background-color: rgb(119, 196, 135);">

  <section>
    <h2>Number of Detections</h2>
<div class="row">
 <div class="column2">
    <table>
      <tr>
	<th>Total</th>
	<th>Today</th>
	<th>Last Hour</th>
	<th>Number of Unique Species</th>
      </tr>
      <tr>
      <td><?php echo $totalcount['COUNT(*)'];?></td>
      <td><a href="/By_Date/<?php echo date('Y-m-d');?>"/><?php echo $todaycount['COUNT(*)'];?></a></td>
      <td><?php echo $hourcount['COUNT(*)'];?></td>
      <td><a href="/stats.php"/><?php echo $speciestally['COUNT(DISTINCT(Com_Name))'];?></a></td>
      </tr>
    </table>
</div>
</div>
    <h2>Today's Detections</h2>
    <table>
      <tr>
	<th>Time</th>
	<th>Scientific Name</th>
	<th>Common Name</th>
	<th>Confidence</th>
	<th>Links</th>
      </tr>
<?php
while($todaytable=$result0->fetchArray(SQLITE3_ASSOC))
{
$comname = preg_replace('/ /', '_', $todaytable['Com_Name']);
$comlink = "/By_Date/".date('Y-m-d')."/".$comname."/".$todaytable['File_Name'];
$sciname = preg_replace('/ /', '_', $todaytable['Sci_Name']);
?>
      <tr>
      <td><a href="<?php echo $comlink;?>" target="footer"/><?php echo $todaytable['Time'];?></a></td>
      <td><?php echo $todaytable['Sci_Name'];?></td>
      <td><?php echo $todaytable['Com_Name'];?></td>
      <td><?php echo $todaytable['Confidence'];?></td>
      <td><a class="a2" href="https://allaboutbirds.org/guide/<?php echo $comname;?>" target="top">All About Birds</a>, <a class="a2" href="https://wikipedia.org/wiki/<?php echo $sciname;?>" target="top">Wikipedia</a></td>
<?php }?>
      </tr>
    </table>
