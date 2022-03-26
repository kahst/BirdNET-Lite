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

$statement2 = $db->prepare('SELECT COUNT(*) FROM detections WHERE Date == DATE(\'now\', \'localtime\')');
if($statement2 == False) {
  echo "Database is busy";
  header("refresh: 0;");
}
$result2 = $statement2->execute();
$todaycount = $result2->fetchArray(SQLITE3_ASSOC);

$statement3 = $db->prepare('SELECT COUNT(*) FROM detections WHERE Date == Date(\'now\', \'localtime\') AND TIME >= TIME(\'now\', \'localtime\', \'-1 hour\')');
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
$sciname = preg_replace('/ /', '_', $mostrecent['Sci_Name']);
$comname = preg_replace('/\'/', '', $comname);
$filename = "/By_Date/".$mostrecent['Date']."/".$comname."/".$mostrecent['File_Name'];

$statement5 = $db->prepare('SELECT COUNT(DISTINCT(Com_Name)) FROM detections WHERE Date == Date(\'now\',\'localtime\')');
if($statement5 == False) {
  echo "Database is busy";
  header("refresh: 0;");
}
$result5 = $statement5->execute();
$speciestally = $result5->fetchArray(SQLITE3_ASSOC);

$statement6 = $db->prepare('SELECT COUNT(DISTINCT(Com_Name)) FROM detections');
if($statement6 == False) {
  echo "Database is busy";
  header("refresh: 0;");
}
$result6 = $statement6->execute();
$totalspeciestally = $result6->fetchArray(SQLITE3_ASSOC);
?>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Overview</title>
</head>
<div class="overview">
<div class="overview-stats">
<span>
<div class="left-column">
<table>
  <tr>
    <th>Total</th>
    <td><?php echo $totalcount['COUNT(*)'];?></td>
  </tr>
  <tr>
    <th>Today</th>
    <form action="" method="POST">
    <td><input type="hidden" name="view" value="Recordings"><button type="submit" name="date" value="<?php echo date('Y-m-d');?>"><?php echo $todaycount['COUNT(*)'];?></button></td>
    </form>
  </tr>
  <tr>
    <th>Last Hour</th>
    <td><?php echo $hourcount['COUNT(*)'];?></td>
  </tr>
  <tr>
    <th>Species Detected Today</th>
    <form action="" method="POST">
    <td><button type="submit" name="view" value="Species Stats"><?php echo $speciestally['COUNT(DISTINCT(Com_Name))'];?></button></td>
    </form>
  </tr>
  <tr>
    <th>Total Number of Species</th>
    <form action="" method="POST">
    <td><button type="submit" name="view" value="Species Stats"><?php echo $totalspeciestally['COUNT(DISTINCT(Com_Name))'];?></button></td>
    </form>
  </tr>
</table>
</div>
</span>
<div class="right-column">
<?php
if (file_exists('/home/pi/BirdSongs/Extracted/Charts/'.$chart)) {
  echo "<img src=\"/Charts/$chart?nocache=time()\">";
} else {
  echo "<p>No Detections For Today</p>";
}
?>
<table>
  <h3>Most Recent Detection: <span style="font-weight: normal;"><?php echo $mostrecent['Date']." ".$mostrecent['Time'];?></span></h3>
  <tr>
    <td>
    <form action="" method="POST">
        <input type="hidden" name="view" value="Species Stats">
        <button type="submit" name="species" value="<?php echo $mostrecent['Com_Name'];?>"><?php echo $mostrecent['Com_Name'];?>: </button>
        <a href="https://wikipedia.org/wiki/<?php echo $sciname;?>" target="_blank"/><i><?php echo $mostrecent['Sci_Name'];?></i></a>
        <br>Confidence: <?php echo $mostrecent['Confidence'];?><br>
        <video controls poster="<?php echo $filename.".png";?>" preload="none" title="<?php echo $filename;?>"><source src="<?php echo $filename;?>"></video></td>
    </form>
  </tr>
</table>
<h3>Currently Analyzing</h3>
<img src='/spectrogram.png?nocache=<?php echo time();?>' >
</div>
</div>
