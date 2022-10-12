<?php
error_reporting(E_ERROR);
ini_set('display_errors',1);
ini_set('session.gc_maxlifetime', 7200);
session_set_cookie_params(7200);
session_start();
$myDate = date('Y-m-d');
$chart = "Combo-$myDate.png";

$db = new SQLite3('./scripts/birds.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
if($db == False) {
  echo "Database is busy";
  header("refresh: 0;");
}

if (file_exists('./scripts/thisrun.txt')) {
  $config = parse_ini_file('./scripts/thisrun.txt');
} elseif (file_exists('./scripts/firstrun.ini')) {
  $config = parse_ini_file('./scripts/firstrun.ini');
}

$user = shell_exec("awk -F: '/1000/{print $1}' /etc/passwd");
$home = shell_exec("awk -F: '/1000/{print $6}' /etc/passwd");
$home = trim($home);

if(isset($_GET['fetch_chart_string']) && $_GET['fetch_chart_string'] == "true") {
  $myDate = date('Y-m-d');
  $chart = "Combo-$myDate.png";
  echo $chart;
  die();
}

if(isset($_GET['ajax_detections']) && $_GET['ajax_detections'] == "true" && isset($_GET['previous_detection_identifier'])) {

  $statement4 = $db->prepare('SELECT Com_Name, Sci_Name, Date, Time, Confidence, File_Name FROM detections ORDER BY Date DESC, Time DESC LIMIT 5');
  if($statement4 == False) {
    echo "Database is busy";
    header("refresh: 0;");
  }
  $result4 = $statement4->execute();
  if(!isset($_SESSION['images'])) {
    $_SESSION['images'] = [];
  }
  $iterations = 0;
  $lines;
  // hopefully one of the 5 most recent detections has an image that is valid, we'll use that one as the most recent detection until the newer ones get their images created
  while($mostrecent = $result4->fetchArray(SQLITE3_ASSOC)) {
    $comname = preg_replace('/ /', '_', $mostrecent['Com_Name']);
    $sciname = preg_replace('/ /', '_', $mostrecent['Sci_Name']);
    $comname = preg_replace('/\'/', '', $comname);
    $filename = "/By_Date/".$mostrecent['Date']."/".$comname."/".$mostrecent['File_Name'];
    $args = "&license=2%2C3%2C4%2C5%2C6%2C9&orientation=square,portrait";
    $comnameprefix = "%20bird";
    
      // check to make sure the image actually exists, sometimes it takes a minute to be created\
      if(file_exists($home."/BirdSongs/Extracted".$filename.".png")){
          if($_GET['previous_detection_identifier'] == $filename) { die(); }
          if($_GET['only_name'] == "true") { echo $comname.",".$filename;die(); }

          $iterations++;

      if (!empty($config["FLICKR_API_KEY"])) {

        if(!empty($config["FLICKR_FILTER_EMAIL"])) {
          if(!isset($_SESSION["FLICKR_FILTER_EMAIL"])) {
            unset($_SESSION['images']);
            $_SESSION['FLICKR_FILTER_EMAIL'] = json_decode(file_get_contents("https://www.flickr.com/services/rest/?method=flickr.people.findByEmail&api_key=".$config["FLICKR_API_KEY"]."&find_email=".$config["FLICKR_FILTER_EMAIL"]."&format=json&nojsoncallback=1"), true)["user"]["nsid"];
          }
          $args = "&user_id=".$_SESSION['FLICKR_FILTER_EMAIL'];
          $comnameprefix = "";
        } else {
          if(isset($_SESSION["FLICKR_FILTER_EMAIL"])) {
            unset($_SESSION["FLICKR_FILTER_EMAIL"]);
            unset($_SESSION['images']);
          }
        }
   

        // if we already searched flickr for this species before, use the previous image rather than doing an unneccesary api call
        $key = array_search($comname, array_column($_SESSION['images'], 0));
        if($key !== false) {
          $image = $_SESSION['images'][$key];
        } else {
          // only open the file once per script execution
          if(!isset($lines)) {
            $lines = file($home."/BirdNET-Pi/model/labels_flickr.txt");
          }
          // convert sci name to English name
          foreach($lines as $line){ 
            if(strpos($line, $mostrecent['Sci_Name']) !== false){
              $engname = trim(explode("_", $line)[1]);
              break;
            }
          }

         $flickrjson = json_decode(file_get_contents("https://www.flickr.com/services/rest/?method=flickr.photos.search&api_key=".$config["FLICKR_API_KEY"]."&text=".str_replace(" ", "%20", $engname).$comnameprefix."&sort=relevance".$args."&per_page=5&media=photos&format=json&nojsoncallback=1"), true)["photos"]["photo"][0];
          $modaltext = "https://flickr.com/photos/".$flickrjson["owner"]."/".$flickrjson["id"];
          $authorlink = "https://flickr.com/people/".$flickrjson["owner"];
          $imageurl = 'https://farm' .$flickrjson["farm"]. '.static.flickr.com/' .$flickrjson["server"]. '/' .$flickrjson["id"]. '_'  .$flickrjson["secret"].  '.jpg';
          array_push($_SESSION['images'], array($comname,$imageurl,$flickrjson["title"], $modaltext, $authorlink));
          $image = $_SESSION['images'][count($_SESSION['images'])-1];
        }
      }

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
            <td class="relative"><a target="_blank" href="index.php?filename=<?php echo $mostrecent['File_Name']; ?>"><img class="copyimage" title="Open in new tab" width="25" height="25" src="images/copy.png"></a>
            <div class="centered_image_container" style="margin-bottom: 0px !important;">
              <?php if(!empty($config["FLICKR_API_KEY"]) && strlen($image[2]) > 0) { ?>
                <img onclick='setModalText(<?php echo $iterations; ?>,"<?php echo urlencode($image[2]); ?>",  "<?php echo $image[3]; ?>", "<?php echo $image[4]; ?>", "<?php echo $image[1]; ?>")' src="<?php echo $image[1]; ?>" class="img1">
              <?php } ?>
              <form action="" method="GET">
                  <input type="hidden" name="view" value="Species Stats">
                  <button type="submit" name="species" value="<?php echo $mostrecent['Com_Name'];?>"><?php echo $mostrecent['Com_Name'];?></button></br>
                  <a href="https://wikipedia.org/wiki/<?php echo $sciname;?>" target="_blank"/><i><?php echo $mostrecent['Sci_Name'];?></i></a>
                  <br>Confidence: <?php echo $percent = round((float)round($mostrecent['Confidence'],2) * 100 ) . '%';?><br></div><br>
                  <video style="margin-top:10px" onplay='setLiveStreamVolume(0)' onended='setLiveStreamVolume(1)' onpause='setLiveStreamVolume(1)' controls poster="<?php echo $filename.".png";?>" preload="none" title="<?php echo $filename;?>"><source src="<?php echo $filename;?>"></video></td>
              </form>
          </tr>
        </table> <?php break;
      }
  }
  if($iterations == 0) {
    echo "<h3>No Detections For Today.</h3>";
  }
  die();
}

if(isset($_GET['ajax_left_chart']) && $_GET['ajax_left_chart'] == "true") {

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
    <td><?php echo $totalcount['COUNT(*)'];?></td>
  </tr>
  <tr>
    <th>Today</th>
    
    <td><form action="" method="GET"><button type="submit" name="view" value="Today's Detections"><?php echo $todaycount['COUNT(*)'];?></button></td>
    </form>
  </tr>
  <tr>
    <th>Last Hour</th>
    <td><?php echo $hourcount['COUNT(*)'];?></td>
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
  <dialog id="attribution-dialog">
    <h1 id="modalHeading"></h1>
    <p id="modalText"></p>
    <button onclick="hideDialog()">Close</button>
  </dialog>
  <script src="static/dialog-polyfill.js"></script>
  <script>
  var dialog = document.querySelector('dialog');
  dialogPolyfill.registerDialog(dialog);

  function showDialog() {
    document.getElementById('attribution-dialog').showModal();
  }

  function hideDialog() {
    document.getElementById('attribution-dialog').close();
  }

  function setModalText(iter, title, text, authorlink, photolink) {
    document.getElementById('modalHeading').innerHTML = "Photo: \""+decodeURIComponent(title.replaceAll("+"," "))+"\" Attribution";
    document.getElementById('modalText').innerHTML = "<div><img style='border-radius:5px' src='"+photolink+"'></div><br><div>Image link: <a target='_blank' href="+text+">"+text+"</a><br>Author link: <a target='_blank' href="+authorlink+">"+authorlink+"</a></div>";
    showDialog();
  }
  </script>  
<div class="overview-stats">
<div class="left-column">
</div>
<div class="right-column">
<div class="chart">
<?php
$refresh = $config['RECORDING_LENGTH'];
$time = time();
if (file_exists('./Charts/'.$chart)) {
  echo "<img id='chart' src=\"/Charts/$chart?nocache=$time\">";
} 
?>
</div>

<div id="most_recent_detection"></div>
<br>
<h3>5 Most Recent Detections</h3>
<div style="padding-bottom:10px;" id="detections_table"><h3>Loading...</h3></div>

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
    if(this.responseText.length > 0 && !this.responseText.includes("Database is busy") && !this.responseText.includes("No Detections") || previous_detection_identifier == undefined) {
      document.getElementById("most_recent_detection").innerHTML = this.responseText;

      // only going to load left chart & 5 most recents if there's a new detection
      loadLeftChart();
      loadFiveMostRecentDetections();
      refreshTopTen();
    }
  }
  xhttp.open("GET", "overview.php?ajax_detections=true&previous_detection_identifier="+previous_detection_identifier, true);
  xhttp.send();
}
function loadLeftChart() {
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
    if(this.responseText.length > 0 && !this.responseText.includes("Database is busy")) {
      document.getElementsByClassName("left-column")[0].innerHTML = this.responseText;
    }
  }
  xhttp.open("GET", "overview.php?ajax_left_chart=true", true);
  xhttp.send();
}
function refreshTopTen() {
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
  if(this.responseText.length > 0 && !this.responseText.includes("Database is busy") && !this.responseText.includes("No Detections") || previous_detection_identifier == undefined) {
    document.getElementById("chart").src = "/Charts/"+this.responseText+"?nocache="+Date.now();
  }
  }
  xhttp.open("GET", "overview.php?fetch_chart_string=true", true);
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

function loadFiveMostRecentDetections() {
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
    if(this.responseText.length > 0 && !this.responseText.includes("Database is busy")) {
      document.getElementById("detections_table").innerHTML= this.responseText;
    }
  }
  if (window.innerWidth > 500) {
    xhttp.open("GET", "todays_detections.php?ajax_detections=true&display_limit=undefined&hard_limit=5", true);
  } else {
    xhttp.open("GET", "todays_detections.php?ajax_detections=true&display_limit=undefined&hard_limit=5&mobile=true", true);
  }
  xhttp.send();
}
window.addEventListener("load", function(){
  loadDetectionIfNewExists();
});

// every $refresh seconds, this loop will run and refresh the spectrogram image
window.setInterval(function(){
  document.getElementById("spectrogramimage").src = "/spectrogram.png?nocache="+Date.now();
}, <?php echo $refresh; ?>*1000);
</script>

