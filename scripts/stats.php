<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = new SQLite3('/home/pi/BirdNET-Pi/scripts/birds.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
if($db == False) {
	echo "Database busy";
	header("refresh: 0;");
}
$statement = $db->prepare('SELECT Date, Time, File_Name, Com_Name, COUNT(*), MAX(Confidence) FROM detections GROUP BY Com_Name ORDER BY COUNT(*) DESC');
if($statement == False) {
	echo "Database busy";
	header("refresh: 0;");
}
$result = $statement->execute();

$statement2 = $db->prepare('SELECT Date, Time, File_Name, Com_Name, COUNT(*), MAX(Confidence) FROM detections GROUP BY Com_Name ORDER BY Com_Name');
if($statement == False) {
	echo "Database busy";
	header("refresh: 0;");
}
$result2 = $statement2->execute();



if(isset($_POST['species'])){
  $selection = $_POST['species'];
  $statement3 = $db->prepare("SELECT Com_Name, Sci_Name, COUNT(*), MAX(Confidence), File_Name, Date, Time from detections WHERE Com_Name = \"$selection\"");
  if($statement3 == False) {
  	echo "Database busy";
  	header("refresh: 0;");
  }
  $result3 = $statement3->execute();
}
?>

<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>BirdNET-Pi DB</title>
  <link rel="stylesheet" href="../style.css">

<style>
</style>

</head>
<body>
<div class="stats">
<div class="column left">
<table>
<?php
while($results=$result2->fetchArray(SQLITE3_ASSOC))
{
$comname = preg_replace('/ /', '_', $results['Com_Name']);
$comname = preg_replace('/\'/', '', $comname);
$filename = "/By_Date/".$results['Date']."/".$comname."/".$results['File_Name'];
?>
  <tr>
  <form action="" method="POST">
  <td><input type="hidden" name="view" value="Species Stats">
    <button type="submit" name="species" value="<?php echo $results['Com_Name'];?>"><?php echo $results['Com_Name'];?></button>
  </td>
<?php
}
?>
  </form>
  </tr>
</table>
</div>
<div class="column center">
<?php if(!isset($_POST['species'])){
?><p class="centered">Choose a species to load images from Wikimedia Commons.</p>
<?php
};?>
<?php if(isset($_POST['species'])){
  $species = $_POST['species'];
   
while($results=$result3->fetchArray(SQLITE3_ASSOC)){
  $count = $results['COUNT(*)'];
  $maxconf = $results['MAX(Confidence)'];
  $date = $results['Date'];
  $time = $results['Time'];
  $name = $results['Com_Name'];
  $sciname = $results['Sci_Name'];
  $dbsciname = preg_replace('/ /', '_', $sciname);
  $comname = preg_replace('/ /', '_', $results['Com_Name']);
  $comname = preg_replace('/\'/', '', $comname);
  $linkname = preg_replace('/_/', '+', $dbsciname);
  $filename = "/By_Date/".$date."/".$comname."/".$results['File_Name'];
  echo str_pad("<h3>$species</h3>
    <table><tr>
  <td><a href=\"https://wikipedia.org/wiki/$dbsciname\" target=\"top\"/><i>$sciname</i></a><br>
  <b>Occurrences: </b>$count<br>
  <b>Max Confidence: </b>$maxconf<br>
  <b>Best Recording: </b>$date $time<br>
  <a href=\"https://allaboutbirds.org/guide/$comname\" target=\"top\"/>All About Birds</a><br>
  <video controls poster=\"$filename.png\"><source src=\"$filename\"></video></td>
  </tr>
    </table>
  <p>Loading Images from <a href=\"https://commons.wikimedia.org/w/index.php?search=$linkname&title=Special:MediaSearch&go=Go&type=image\" target=\"_blank\">Wikimedia Commons</a></p>", '6096');
  
  ob_flush();
  flush();
  $imagelink = "https://commons.wikimedia.org/w/index.php?search=$linkname&title=Special:MediaSearch&go=Go&type=image";
  $homepage = file_get_contents($imagelink);
  preg_match_all("{<img\\s*(.*?)src=('.*?'|\".*?\"|[^\\s]+)(.*?)\\s*/?>}ims", $homepage, $matches, PREG_SET_ORDER);
  foreach ($matches as $val) {
      $pos = strpos($val[2],"/");
      $link = substr($val[2],1,-1);
      if($pos !== 1 && strpos($link, "upload") == true && strpos($link, "CentralAutoLogin") == false)
          echo "<img src=\"$link\">";
  }
}}
?>
<br><br><br>
    <table>
<?php
while($results=$result->fetchArray(SQLITE3_ASSOC))
{
$comname = preg_replace('/ /', '_', $results['Com_Name']);
$comname = preg_replace('/\'/', '', $comname);
$filename = "/By_Date/".$results['Date']."/".$comname."/".$results['File_Name'];
?>
      <tr>
      <form action="" method="POST">
      <td><input type="hidden" name="view" value="Species Stats">
        <button type="submit" name="species" value="<?php echo $results['Com_Name'];?>"><?php echo $results['Com_Name'];?></button><br><b>Occurrences:</b> <?php echo $results['COUNT(*)'];?><br>
      <b>Max Confidence:</b> <?php echo $results['MAX(Confidence)'];?><br>
      <b>Best Recording:</b> <?php echo $results['Date']." ".$results['Time'];?><br><video controls poster="<?php echo $filename.".png";?>"><source src="<?php echo $filename;?>" type="audio/mp3"></video></td>
      </tr>
<?php
}
?>
    </table>
      </form>
</div>
</div>
</body>
</html>

