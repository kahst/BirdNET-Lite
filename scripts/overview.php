<?php
$myDate = date('Y-m-d');
$chart = "Combo-$myDate.png";

$db = new SQLite3('./scripts/birds.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
if($db == False) {
  echo "Database is busy";
  header("refresh: 0;");
}

if(isset($_GET['ajax_detections']) && $_GET['ajax_detections'] == "true" && isset($_GET['previous_detection_identifier'])) {

  $statement4 = $db->prepare('SELECT Com_Name, Sci_Name, Date, Time, Confidence, File_Name FROM detections ORDER BY Date DESC, Time DESC LIMIT 5');
  if($statement4 == False) {
    echo "Database is busy";
    header("refresh: 0;");
  }
  $result4 = $statement4->execute();
  // hopefully one of the 5 most recent detections has an image that is valid, we'll use that one as the most recent detection until the newer ones get their images created
  while($mostrecent = $result4->fetchArray(SQLITE3_ASSOC)) {
    $comname = preg_replace('/ /', '_', $mostrecent['Com_Name']);
    $sciname = preg_replace('/ /', '_', $mostrecent['Sci_Name']);
    $comname = preg_replace('/\'/', '', $comname);
    $filename = "/By_Date/".$mostrecent['Date']."/".$comname."/".$mostrecent['File_Name'];

      // check to make sure the image actually exists, sometimes it takes a minute to be created
      if (isset($_SERVER['HTTPS']) &&
          ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
          isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
          $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
        $protocol = 'https://';
      }
      else {
        $protocol = 'http://';
      }
      $headers = @get_headers($protocol.$_SERVER['HTTP_HOST'].$filename.".png");
      // we've found our valid detection! ignore everything else from the database loop
      if(strpos($headers[0],'200')) {
          if($_GET['previous_detection_identifier'] == $filename) { die(); }
      ?>
        <style>
        .fade-in {
          opacity: 1;
          animation-name: fadeInOpacity;
          animation-iteration-count: 1;
          animation-timing-function: ease-in;
          animation-duration: 1s;
        }

        @keyframes fadeInOpacity {
          0% {
            opacity: 0;
          }
          100% {
            opacity: 1;
          }
        }
        </style>
        <table class="<?php echo ($_GET['previous_detection_identifier'] == 'undefined') ? '' : 'fade-in';  ?>">
          <h3>Most Recent Detection: <span style="font-weight: normal;"><?php echo $mostrecent['Date']." ".$mostrecent['Time'];?></span></h3>
          <tr>
            <td class="relative"><a target="_blank" href="index.php?filename=<?php echo $mostrecent['File_Name']; ?>"><img class="copyimage" width="25" height="25" src="images/copy.png"></a>
            <form action="" method="GET">
                <input type="hidden" name="view" value="Species Stats">
                <button type="submit" name="species" value="<?php echo $mostrecent['Com_Name'];?>"><?php echo $mostrecent['Com_Name'];?></button></br>
                <a href="https://wikipedia.org/wiki/<?php echo $sciname;?>" target="_blank"/><i><?php echo $mostrecent['Sci_Name'];?></i></a>
                <br>Confidence: <?php echo $mostrecent['Confidence'];?><br>
                <video style="margin-top:10px" onplay='setLiveStreamVolume(0)' onended='setLiveStreamVolume(1)' onpause='setLiveStreamVolume(1)' controls poster="<?php echo $filename.".png";?>" preload="none" title="<?php echo $filename;?>"><source src="<?php echo $filename;?>"></video></td>
            </form>
          </tr>
        </table> <?php break;
      }
  }
  die();
}

if(isset($_GET['ajax_left_chart']) && $_GET['ajax_left_chart'] == "true") {

$totalcount = 0;
$todaycount = 0;
$hourcount = 0;
$statement = $db->prepare('SELECT * FROM detections ORDER BY Date DESC, Time DESC');
if($statement == False) {
  echo "Database is busy";
  header("refresh: 0;");
}
$result = $statement->execute();
while($detection=$result->fetchArray(SQLITE3_ASSOC))
{
  $totalcount++;
  if(strtotime($detection["Date"]." ".$detection["Time"]) > (time() - 3600)){ 
    $hourcount++;
  } if(strtotime($detection["Date"]." ".$detection["Time"]) > (time() - (time() - strtotime("today"))) ){ 
    $todaycount++;
  } 
}

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
<table>
  <tr>
    <th>Total</th>
    <td><?php echo $totalcount;?></td>
  </tr>
  <tr>
    <th>Today</th>
    
    <td><form action="" method="GET"><button type="submit" name="view" value="Today's Detections"><?php echo $todaycount;?></button></td>
    </form>
  </tr>
  <tr>
    <th>Last Hour</th>
    <td><?php echo $hourcount;?></td>
  </tr>
  <tr>
    <th>Species Detected Today</th>
    <td><form action="" method="GET"><input type="hidden" name="view" value="Recordings"><button type="submit" name="date" value="<?php echo date('Y-m-d');?>"><?php echo $speciestally['COUNT(DISTINCT(Com_Name))'];?></button></td>
    </form>
  </tr>
  <tr>
    <th>Total Number of Species</th>
    <td><form action="" method="GET"><button type="submit" name="view" value="Species Stats"><?php echo $totalspeciestally['COUNT(DISTINCT(Com_Name))'];?></button></td>
    </form>
  </tr>
</table>
<?php
die();
}
?>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Overview</title>
<style>
body::-webkit-scrollbar {
  display:none
}
</style>
</head>
<div class="overview">
<div class="overview-stats">
<div class="left-column">
</div>
<div class="right-column">
<div class="chart">
<?php
if (file_exists('./scripts/thisrun.txt')) {
  $config = parse_ini_file('./scripts/thisrun.txt');
} elseif (file_exists('./scripts/firstrun.ini')) {
  $config = parse_ini_file('./scripts/firstrun.ini');
}
$refresh = $config['RECORDING_LENGTH'];
$time = time();
if (file_exists('./Charts/'.$chart)) {
  echo "<img id='chart' src=\"/Charts/$chart?nocache=$time\">";
} else {
  echo "<p>No Detections For Today</p>";
}
?>
</div>

<div id="most_recent_detection"></div>

<h3>Currently Analyzing</h3>
<?php
$refresh = $config['RECORDING_LENGTH'];
$time = time();
echo "<img id=\"spectrogramimage\" src=\"/spectrogram.png?nocache=$time\">";
?>

</div>
</div>

<script>
// we're passing a unique ID of the currently displayed detection to our script, which checks the database to see if the newest detection entry is that ID, or not. If the IDs don't match, it must mean we have a new detection and it's loaded onto the page
function loadDetectionIfNewExists(previous_detection_identifier=undefined) {
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
    // if there's a new detection that needs to be updated to the page
    if(this.responseText.length > 0 && !this.responseText.includes("Database is busy.")) {
      document.getElementById("most_recent_detection").innerHTML = this.responseText;

      // only going to load left chart if there's a new detection
      loadLeftChart();
    }
  }
  xhttp.open("GET", "overview.php?ajax_detections=true&previous_detection_identifier="+previous_detection_identifier, true);
  xhttp.send();
}
function loadLeftChart() {
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
    document.getElementsByClassName("left-column")[0].innerHTML = this.responseText;
  }
  xhttp.open("GET", "overview.php?ajax_left_chart=true", true);
  xhttp.send();
}
window.setInterval(function(){
  var videoelement = document.getElementsByTagName("video")[0];
  if(typeof videoelement !== "undefined") {
    // don't refresh the detection if the user is playing the previous one's audio, wait until they're finished
    if(!!(videoelement.currentTime > 0 && !videoelement.paused && !videoelement.ended && videoelement.readyState > 2) == false) {
      loadDetectionIfNewExists(videoelement.title);
    }
  } else{
    // image or audio didn't load for some reason, force a refresh in 5 seconds
    loadDetectionIfNewExists();
  }
}, <?php echo intval($refresh/4); ?>*1000);
window.addEventListener("load", function(){
  loadDetectionIfNewExists();
  loadLeftChart();
});
// every $refresh seconds, this loop will run and refresh the spectrogram image
window.setInterval(function(){
  document.getElementById("chart").src = "/Charts/<?php echo $chart;?>?nocache="+Date.now();
  document.getElementById("spectrogramimage").src = "/spectrogram.png?nocache="+Date.now();
}, <?php echo $refresh; ?>*1000);
</script>

