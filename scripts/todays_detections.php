<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = new SQLite3('./scripts/birds.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
if($db == False){
  echo "Database is busy";
  header("refresh: 0;");
}

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

$statement5 = $db->prepare('SELECT COUNT(DISTINCT(Com_Name)) FROM detections WHERE Date == Date(\'now\', \'localtime\')');
if($statement5 == False){
  echo "Database is busy";
  header("refresh: 0;");
}
$result5 = $statement5->execute();
$todayspeciestally = $result5->fetchArray(SQLITE3_ASSOC);

$statement6 = $db->prepare('SELECT COUNT(DISTINCT(Com_Name)) FROM detections');
if($statement6 == False){
  echo "Database is busy";
  header("refresh: 0;");
}
$result6 = $statement6->execute();
$totalspeciestally = $result6->fetchArray(SQLITE3_ASSOC);

if(isset($_GET['ajax_detections']) && $_GET['ajax_detections'] == "true"  ) {
  if(isset($_GET['searchterm'])) {
    $searchquery = "AND (Com_name LIKE '%".$_GET['searchterm']."%' OR Sci_name LIKE '%".$_GET['searchterm']."%' OR Confidence LIKE '%".$_GET['searchterm']."%' OR File_Name LIKE '%".$_GET['searchterm']."%' OR Time LIKE '%".$_GET['searchterm']."%')";
  } else {
    $searchquery = "";
  }
  if(isset($_GET['display_limit']) && is_numeric($_GET['display_limit'])){
    $statement0 = $db->prepare('SELECT Time, Com_Name, Sci_Name, Confidence, File_Name FROM detections WHERE Date == Date(\'now\', \'localtime\') '.$searchquery.' ORDER BY Time DESC LIMIT '.(intval($_GET['display_limit'])-40).',40');
  } else {
    // legacy mode
    $statement0 = $db->prepare('SELECT Time, Com_Name, Sci_Name, Confidence, File_Name FROM detections WHERE Date == Date(\'now\', \'localtime\') '.$searchquery.' ORDER BY Time DESC'.$searchquery);
  }
  if($statement0 == False){
    echo "Database is busy";
    header("refresh: 0;");
  }
  $result0 = $statement0->execute();

  ?> <table>
   <?php
  $iterations = 0;
  while($todaytable=$result0->fetchArray(SQLITE3_ASSOC))
  {
    $iterations++;

  $comname = preg_replace('/ /', '_', $todaytable['Com_Name']);
  $comname = preg_replace('/\'/', '_', $comname);
  $filename = "/By_Date/".date('Y-m-d')."/".$comname."/".$todaytable['File_Name'];
  $sciname = preg_replace('/ /', '_', $todaytable['Sci_Name']);
  ?>
        <?php if(isset($_GET['display_limit']) && is_numeric($_GET['display_limit'])){ ?>
          <tr class="relative" id="<?php echo $iterations; ?>">
          <td><a target="_blank" href="index.php?filename=<?php echo $todaytable['File_Name']; ?>"><img class="copyimage" width=25 src="images/copy.png"></a><?php echo $todaytable['Time'];?><br>
          <b><a class="a2" href="https://allaboutbirds.org/guide/<?php echo $comname;?>" target="top"><?php echo $todaytable['Com_Name'];?></a></b><br>
          <a class="a2" href="https://wikipedia.org/wiki/<?php echo $sciname;?>" target="top"><i><?php echo $todaytable['Sci_Name'];?></i></a><br>
          <b>Confidence:</b> <?php echo $todaytable['Confidence'];?><br>
          <video onplay='setLiveStreamVolume(0)' onended='setLiveStreamVolume(1)' onpause='setLiveStreamVolume(1)' controls poster="<?php echo $filename.".png";?>" preload="none" title="<?php echo $filename;?>"><source preload="none" src="<?php echo $filename;?>"></video>
          </td>
        <?php } else { //legacy mode ?>
          <tr class="relative" id="<?php echo $iterations; ?>">
          <td><?php echo $todaytable['Time'];?><br></td><td>
          <b><a class="a2" href="https://allaboutbirds.org/guide/<?php echo $comname;?>" target="top"><?php echo $todaytable['Com_Name'];?></a></b><br>
          <a class="a2" href="https://wikipedia.org/wiki/<?php echo $sciname;?>" target="top"><i><?php echo $todaytable['Sci_Name'];?></i></a><br></td>
          <td><b>Confidence:</b> <?php echo $todaytable['Confidence'];?><br></td>
          <td style="min-width:180px"><audio controls preload="none" title="<?php echo $filename;?>"><source preload="none" src="<?php echo $filename;?>"></video>
          </td>
        <?php } ?>
  <?php }?>
        </tr>
      </table>

  <?php 
  // don't show the button if there's no more detections to be displayed, we're at the end of the list
  if($iterations >= 40 && isset($_GET['display_limit']) && is_numeric($_GET['display_limit'])) { ?>
  <center>
  <button style="margin-top:10px;font-size:x-large;background:#dbffeb;padding:10px;border: 2px solid black;" onclick="loadDetections(<?php echo $_GET['display_limit'] + 40; ?>, this);" value="Today's Detections">Load 40 More...</button>
  </center>
  <?php }

  die();
}

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
<div class="viewdb">
    <h3>Number of Detections</h3>
    <table>
      <tr>
  <th>Total</th>
  <th>Today</th>
  <th>Last Hour</th>
  <th>Unique Species Total</th>
  <th>Unique Species Today</th>
      </tr>
      <tr>
      <td><?php echo $totalcount['COUNT(*)'];?></td>
      <form action="" method="GET">
      <td><input type="hidden" name="view" value="Recordings"><button type="submit" name="date" value="<?php echo date('Y-m-d');?>"><?php echo $todaycount['COUNT(*)'];?></button></td>
      </form>
      <td><?php echo $hourcount['COUNT(*)'];?></td>
      <form action="" method="GET">
      <td><button type="submit" name="view" value="Species Stats"><?php echo $totalspeciestally['COUNT(DISTINCT(Com_Name))'];?></button></td>
      </form>
      <form action="" method="GET">
      <td><input type="hidden" name="view" value="Recordings"><button type="submit" name="date" value="<?php echo date('Y-m-d');?>"><?php echo $todayspeciestally['COUNT(DISTINCT(Com_Name))'];?></button></td>
      </form>
      </tr>
    </table>

    <h3>Today's Detections — <input size="11" type="text" placeholder="Search..." id="searchterm" name="searchterm"></h3>

    <div style="padding-bottom:10px" id="detections_table"></div>

    <button onclick="switchViews(this);" style="color:gray;margin:5px;float:right;z-index:100;position:relative;font-size:small;background:#dbffeb;padding:5px;border: 2px solid black;">Legacy view</button>

</div>

<script>

var timer = '';
searchterm = "";

document.getElementById("searchterm").onkeypress = (function(e) {
  if (e.key === "Enter") {
      clearTimeout(timer);
      searchDetections(document.getElementById("searchterm").value);
  } else {
     clearTimeout(timer);
     timer = setTimeout(function() {
        searchDetections(document.getElementById("searchterm").value);
     }, 1000);
  }
});

function switchViews(element) {
  document.getElementById("detections_table").innerHTML = "";
  if(element.innerHTML == "Legacy view") {
    element.innerHTML = "Normal view";
    loadDetections(undefined);
  } else if(element.innerHTML == "Normal view") {
    element.innerHTML = "Legacy view";
    loadDetections(40);
  }
}
function searchDetections(searchvalue) {
    document.getElementById("detections_table").innerHTML = "";
    searchterm = searchvalue;
    loadDetections(40,undefined);
}
function loadDetections(detections_limit, element=undefined) {
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
    if(typeof element !== "undefined")
    {
     element.remove();
     document.getElementById("detections_table").innerHTML+= this.responseText;
    } else {
     document.getElementById("detections_table").innerHTML= this.responseText;
    }
    
  }
  if(searchterm !== ""){
    xhttp.open("GET", "todays_detections.php?ajax_detections=true&display_limit="+detections_limit+"&searchterm="+searchterm, true);
  } else {
    xhttp.open("GET", "todays_detections.php?ajax_detections=true&display_limit="+detections_limit, true);
  }
  xhttp.send();
}
window.addEventListener("load", function(){
  loadDetections(40);
});
</script>

