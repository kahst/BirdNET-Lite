<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = new SQLite3('/home/pi/BirdNET-Pi/scripts/birds.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
if($db == False){
  echo "Database is busy";
  header("refresh: 0;");
}

$statement0 = $db->prepare('SELECT Time, Com_Name, Sci_Name, Confidence, File_Name FROM detections WHERE Date == Date(\'now\', \'localtime\') ORDER BY Time DESC');
if($statement0 == False){
  echo "Database is busy";
  header("refresh: 0;");
}
$result0 = $statement0->execute();

$statement1 = $db->prepare('SELECT COUNT(*) FROM detections');
if($statement1 == False){
  echo "Database is busy";
  header("refresh: 0;");
}
$result1 = $statement1->execute();
$totalcount = $result1->fetchArray(SQLITE3_ASSOC);

$statement2 = $db->prepare('SELECT COUNT(*) FROM detections WHERE Date == DATE(\'now\', \'localtime\')');
if($statement2 == False){
  echo "Database is busy";
  header("refresh: 0;");
}
$result2 = $statement2->execute();
$todaycount = $result2->fetchArray(SQLITE3_ASSOC);

$statement3 = $db->prepare('SELECT COUNT(*) FROM detections WHERE Date == Date(\'now\', \'localtime\') AND TIME >= TIME(\'now\', \'localtime\', \'-1 hour\')');
if($statement3 == False){
  echo "Database is busy";
  header("refresh: 0;");
}
$result3 = $statement3->execute();
$hourcount = $result3->fetchArray(SQLITE3_ASSOC);

$statement4 = $db->prepare('SELECT Com_Name, Sci_Name, Time, Confidence FROM detections LIMIT 1');
if($statement4 == False){
  echo "Database is busy";
  header("refresh: 0;");
}
$result4 = $statement4->execute();
$mostrecent = $result4->fetchArray(SQLITE3_ASSOC);

$statement5 = $db->prepare('SELECT COUNT(DISTINCT(Com_Name)) FROM detections');
if($statement4 == False){
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
  <title>BirdNET-Pi DB</title>
  <style>
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
      <td><?php echo $totalcount['COUNT(*)'];?></td>
      <form action="" method="POST">
      <td><input type="hidden" name="view" value="Extractions"><button type="submit" name="date" value="<?php echo date('Y-m-d');?>"><?php echo $todaycount['COUNT(*)'];?></button></td>
      </form>
      <td><?php echo $hourcount['COUNT(*)'];?></td>
      <form action="" method="POST">
      <td><button type="submit" name="view" value="Species Stats"><?php echo $speciestally['COUNT(DISTINCT(Com_Name))'];?></button></td>
      </form>
      </tr>
    </table>
    <h2>Today's Detections</h2>
    <table>
      <tr>
	<th>Time</th>
	<th>Listen</th>
	<th>Scientific Name</th>
	<th>Common Name</th>
	<th>Confidence</th>
      </tr>
<?php
while($todaytable=$result0->fetchArray(SQLITE3_ASSOC))
{
$comname = preg_replace('/ /', '_', $todaytable['Com_Name']);
$comname = preg_replace('/\'/', '_', $comname);
$filename = "/By_Date/".date('Y-m-d')."/".$comname."/".$todaytable['File_Name'];
$sciname = preg_replace('/ /', '_', $todaytable['Sci_Name']);
?>
      <tr>
      <td><?php echo $todaytable['Time'];?></td>
      <td><audio controls><source src="<?php echo $filename;?>"></audio></td>
      <td><a class="a2" href="https://wikipedia.org/wiki/<?php echo $sciname;?>" target="top"><?php echo $todaytable['Sci_Name'];?></a></td>
      <td><a class="a2" href="https://allaboutbirds.org/guide/<?php echo $comname;?>" target="top"><?php echo $todaytable['Com_Name'];?></a></td>
      <td><?php echo $todaytable['Confidence'];?></td>
<?php }?>
      </tr>
    </table>
