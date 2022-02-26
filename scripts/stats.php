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
<style>
  </style>
</head>
<body>

  <section>
<div class="row">
 <div class="column first">
<?php if(!isset($_POST['species'])){
    echo "<p>Choose a species below to show statistics.</p>";
};?>
    <table>
      <tr>
	<th>Common Name</th>
	<th>Occurrences</th>
	<th>Max Confidence Score</th>
	<th>Best Recording</th>
      </tr>
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
        <button type="submit" name="species" value="<?php echo $results['Com_Name'];?>"><?php echo $results['Com_Name'];?></button>
      </td>
      </form>
      <td><?php echo $results['COUNT(*)'];?></td>
      <td><?php echo $results['MAX(Confidence)'];?></td>
      <td><audio controls><source src="<?php echo $filename;?>"></audio></td>
      </tr>
<?php
}
?>
    </table>
  </div>  
<?php if(isset($_POST['species'])){
  $species = $_POST['species'];
  $str = "<div class=\"column second\">
   <h3>$species</h3>
    <table>
      <tr>
	<th>Scientific Name</th>
	<th>Occurrences</th>
	<th>Highest Confidence Score</th>
	<th>Best Recording</th>
	<th>Links</th>
      </tr>";
  echo str_pad($str, 4096);
  ob_flush();
  flush();
   
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
  echo str_pad("<tr>
  <td><a href=\"https://wikipedia.org/wiki/$dbsciname\" target=\"top\"/>$sciname</a></td>
  <td>$count</td>
  <td>$maxconf</td>
  <td>$date $time<br><audio controls><source src=\"$filename\"></audio></td>
  <td><a href=\"https://allaboutbirds.org/guide/$comname\" target=\"top\"/>All About Birds</a></td>
  </tr>
    </table>
  <p>Loading Images from https://commons.wikimedia.org/w/index.php?search=$linkname&title=Special:MediaSearch&go=Go&type=image</p>", '6096');
  
  ob_flush();
  flush();
  $imagelink = "https://commons.wikimedia.org/w/index.php?search=$linkname&title=Special:MediaSearch&go=Go&type=image";
  $homepage = file_get_contents($imagelink);
  preg_match_all("{<img\\s*(.*?)src=('.*?'|\".*?\"|[^\\s]+)(.*?)\\s*/?>}ims", $homepage, $matches, PREG_SET_ORDER);
  foreach ($matches as $val) {
      $pos = strpos($val[2],"/");
      $link = substr($val[2],1,-1);
      if($pos == 1)
          echo "http://domain.com" . $link;
      elseif(strpos($link, "upload") == true)
          echo "<img src=\"$link\">";
  }
}}
?>

  </section>
</html>

