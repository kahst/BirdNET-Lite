<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = new SQLite3('./scripts/birds.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
if($db == False) {
  echo "Database busy";
  header("refresh: 0;");
}

if(isset($_GET['sort']) && $_GET['sort'] == "occurrences") {
  
  $statement = $db->prepare('SELECT Date, Time, File_Name, Com_Name, COUNT(*), MAX(Confidence) FROM detections GROUP BY Com_Name ORDER BY COUNT(*) DESC');
  if($statement == False) {
    echo "Database busy";
    header("refresh: 0;");
  }
  $result = $statement->execute();

  $statement2 = $db->prepare('SELECT Date, Time, File_Name, Com_Name, COUNT(*), MAX(Confidence) FROM detections GROUP BY Com_Name ORDER BY COUNT(*) DESC');
  if($statement == False) {
    echo "Database busy";
    header("refresh: 0;");
  }
  $result2 = $statement2->execute();
} else {

  $statement = $db->prepare('SELECT Date, Time, File_Name, Com_Name, COUNT(*), MAX(Confidence) FROM detections GROUP BY Com_Name ORDER BY Com_Name ASC');
  if($statement == False) {
    echo "Database busy";
    header("refresh: 0;");
  }
  $result = $statement->execute();

  $statement2 = $db->prepare('SELECT Date, Time, File_Name, Com_Name, COUNT(*), MAX(Confidence) FROM detections GROUP BY Com_Name ORDER BY Com_Name ASC');
  if($statement == False) {
    echo "Database busy";
    header("refresh: 0;");
  }
  $result2 = $statement2->execute();
}



if(isset($_GET['species'])){
  $selection = $_GET['species'];
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
<div class="stats">
<div class="column">
  <div style="width: auto;
   text-align: center">
   <form action="" method="GET">
      <input type="hidden" name="view" value="Species Stats">
      <button style="margin-top:10px;font-size:x-large;background:#dbffeb;padding:5px;border: 2px solid black;" type="submit" name="sort" value="alphabetical">
         <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
            width="35px" height="35px" viewBox="0 0 381.404 381.404" style="enable-background:new 0 0 35 35;"
            xml:space="preserve">
            <g>
               <path d="M34.249,246.497l63.655,25.155V5.1c0-2.818,2.29-5.094,5.103-5.1h51.022c2.811,0.006,5.096,2.282,5.096,5.094v266.559
                  l63.658-25.155c2.134-0.824,4.569-0.147,5.943,1.675c1.39,1.839,1.378,4.36-0.022,6.178l-96.136,125.057
                  c-0.971,1.261-2.466,1.994-4.053,1.998c-1.573,0-3.074-0.737-4.041-1.998L28.333,254.35c-0.705-0.92-1.058-2.019-1.053-3.106
                  c0-1.063,0.336-2.157,1.021-3.082C29.682,246.35,32.115,245.66,34.249,246.497z M354.125,244.402h-21.907l-12.364-29.736h-37.228
                  l-12.027,29.736h-21.407l41.452-99.561h21.861L354.125,244.402z M314.653,200.095l-9.91-23.779
                  c-1.412-3.374-2.571-6.58-3.599-9.677c-1.144,3.429-2.266,6.528-3.405,9.476l-9.948,23.974h26.862V200.095z M340.509,267.006
                  h-83.718V282.3h56.287l-61.786,73.763v10.518h90.071v-15.289h-62.305l61.438-73.472v-10.813H340.509z"/>
            </g>
         </svg>
      </button>
      <button style="margin-top:10px;font-size:x-large;background:#dbffeb;padding:5px;border: 2px solid black;" type="submit" name="sort" value="occurrences">
         <svg version="1.1" id="Capa_2" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
            width="35px" height="35px" viewBox="0 0 96.55 96.55" style="enable-background:new 0 0 35 35;" xml:space="preserve"
            >
            <g>
               <g>
                  <path d="M73.901,56.376h-26.76c-0.652,0-1.182,0.526-1.182,1.181v14.065c0,0.651,0.529,1.18,1.182,1.18h26.76
                     c0.654,0,1.184-0.526,1.184-1.18V57.555C75.084,56.902,74.555,56.376,73.901,56.376z"/>
                  <path d="M62.262,80.001H47.141c-0.652,0-1.182,0.528-1.182,1.183v14.063c0,0.653,0.529,1.182,1.182,1.182h15.122
                     c0.652,0,1.182-0.526,1.182-1.182V81.182C63.444,80.529,62.916,80.001,62.262,80.001z"/>
                  <path d="M84.122,28.251h-36.98c-0.652,0-1.182,0.527-1.182,1.18v14.063c0,0.652,0.529,1.182,1.182,1.182h36.98
                     c0.651,0,1.181-0.529,1.181-1.182V29.43C85.301,28.778,84.773,28.251,84.122,28.251z"/>
                  <path d="M94.338,0.122H47.141c-0.652,0-1.182,0.529-1.182,1.182v14.063c0,0.654,0.529,1.182,1.182,1.182h47.198
                     c0.652,0,1.181-0.527,1.181-1.182V1.303C95.519,0.651,94.992,0.122,94.338,0.122z"/>
                  <path d="M39.183,65.595h-8.011V2c0-1.105-0.896-2-2-2h-16.13c-1.104,0-2,0.895-2,2v63.595h-8.01c-0.771,0-1.472,0.443-1.804,1.138
                     C0.895,67.427,0.99,68.25,1.472,68.85l18.076,26.954c0.38,0.474,0.953,0.746,1.559,0.746s1.178-0.272,1.558-0.746L40.741,68.85
                     c0.482-0.601,0.578-1.423,0.245-2.117C40.654,66.039,39.954,65.595,39.183,65.595z"/>
               </g>
            </g>
         </svg>
      </button>
   </form>
</div>
<table>
<?php
while($results=$result2->fetchArray(SQLITE3_ASSOC))
{
$comname = preg_replace('/ /', '_', $results['Com_Name']);
$comname = preg_replace('/\'/', '', $comname);
$filename = "/By_Date/".$results['Date']."/".$comname."/".$results['File_Name'];
?>
  <tr>
  <form action="" method="GET">
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
<?php if(!isset($_GET['species'])){
?><p class="centered">Choose a species to load images from Wikimedia Commons.</p>
<?php
};?>
<?php if(isset($_GET['species'])){
  $species = $_GET['species'];
   
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
  <td class=\"relative\"><a target=\"_blank\" href=\"index.php?filename=".$results['File_Name']."\"><img class=\"copyimage\" width=25 src=\"images/copy.png\"></a> <a href=\"https://wikipedia.org/wiki/$dbsciname\" target=\"top\"/><i>$sciname</i></a><br>
  <b>Occurrences: </b>$count<br>
  <b>Max Confidence: </b>$maxconf<br>
  <b>Best Recording: </b>$date $time<br>
  <a href=\"https://allaboutbirds.org/guide/$comname\" target=\"top\"/>All About Birds</a><br>
  <video onplay='setLiveStreamVolume(0)' onended='setLiveStreamVolume(1)' onpause='setLiveStreamVolume(1)' controls poster=\"$filename.png\" title=\"$filename\"><source src=\"$filename\"></video></td>
  </tr>
    </table>
  <p>Loading Images from <a href=\"https://commons.wikimedia.org/w/index.php?search=$linkname&title=Special:MediaSearch&go=Go&type=image\" target=\"_blank\">Wikimedia Commons</a></p>", '6096');
  
  echo "<script>document.getElementsByTagName(\"h3\")[0].scrollIntoView();</script>";
  
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
      <form action="" method="GET">
      <td class="relative"><a target="_blank" href="index.php?filename=<?php echo $results['File_Name']; ?>"><img class="copyimage" width=25 src="images/copy.png"></a><input type="hidden" name="view" value="Species Stats">
        <button type="submit" name="species" value="<?php echo $results['Com_Name'];?>"><?php echo $results['Com_Name'];?></button><br><b>Occurrences:</b> <?php echo $results['COUNT(*)'];?><br>
      <b>Max Confidence:</b> <?php echo $results['MAX(Confidence)'];?><br>
      <b>Best Recording:</b> <?php echo $results['Date']." ".$results['Time'];?><br><video onplay='setLiveStreamVolume(0)' onended='setLiveStreamVolume(1)' onpause='setLiveStreamVolume(1)' controls poster="<?php echo $filename.".png";?>" preload="none" title="<?php echo $filename;?>"><source src="<?php echo $filename;?>" type="audio/mp3"></video></td>
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

