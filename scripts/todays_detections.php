<?php
ini_set('session.gc_maxlifetime', 7200);
session_set_cookie_params(7200);
session_start();
error_reporting(E_ERROR);
ini_set('display_errors',1);

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

$user = shell_exec("awk -F: '/1000/{print $1}' /etc/passwd");
$home = shell_exec("awk -F: '/1000/{print $6}' /etc/passwd");
$home = trim($home);

if(isset($_GET['ajax_detections']) && $_GET['ajax_detections'] == "true"  ) {
  if(isset($_GET['searchterm'])) {
    if(strtolower(explode(" ", $_GET['searchterm'])[0]) == "not") {
      $not = "NOT ";
      $operator = "AND";
      $_GET['searchterm'] =  str_replace("not ", "", $_GET['searchterm']);
      $_GET['searchterm'] =  str_replace("NOT ", "", $_GET['searchterm']);
    } else {
      $not = "";
      $operator = "OR";
    }
    $searchquery = "AND (Com_name ".$not."LIKE '%".$_GET['searchterm']."%' ".$operator." Sci_name ".$not."LIKE '%".$_GET['searchterm']."%' ".$operator." Confidence ".$not."LIKE '%".$_GET['searchterm']."%' ".$operator." File_Name ".$not."LIKE '%".$_GET['searchterm']."%' ".$operator." Time ".$not."LIKE '%".$_GET['searchterm']."%')";
  } else {
    $searchquery = "";
  }
  if(isset($_GET['display_limit']) && is_numeric($_GET['display_limit'])){
    $statement0 = $db->prepare('SELECT Time, Com_Name, Sci_Name, Confidence, File_Name FROM detections WHERE Date == Date(\'now\', \'localtime\') '.$searchquery.' ORDER BY Time DESC LIMIT '.(intval($_GET['display_limit'])-40).',40');
  } else {
    // legacy mode
    if(isset($_GET['hard_limit']) && is_numeric($_GET['hard_limit'])) {
      $statement0 = $db->prepare('SELECT Time, Com_Name, Sci_Name, Confidence, File_Name FROM detections WHERE Date == Date(\'now\', \'localtime\') '.$searchquery.' ORDER BY Time DESC LIMIT '.$_GET['hard_limit']);
    } else {
      $statement0 = $db->prepare('SELECT Time, Com_Name, Sci_Name, Confidence, File_Name FROM detections WHERE Date == Date(\'now\', \'localtime\') '.$searchquery.' ORDER BY Time DESC');
    }
    
  }
  if($statement0 == False){
    echo "Database is busy";
    header("refresh: 0;");
  }
  $result0 = $statement0->execute();

  ?> <table>
   <?php

  if(!isset($_SESSION['images'])) {
    $_SESSION['images'] = [];
  }
  $iterations = 0;
  $lines;

  if (file_exists('./scripts/thisrun.txt')) {
    $config = parse_ini_file('./scripts/thisrun.txt');
  } elseif (file_exists('./scripts/firstrun.ini')) {
  $config = parse_ini_file('./scripts/firstrun.ini');
  } 

  while($todaytable=$result0->fetchArray(SQLITE3_ASSOC))
  {
    $iterations++;

  $comname = preg_replace('/ /', '_', $todaytable['Com_Name']);
  $comname = preg_replace('/\'/', '_', $comname);
  $filename = "/By_Date/".date('Y-m-d')."/".$comname."/".$todaytable['File_Name'];
  $sciname = preg_replace('/ /', '_', $todaytable['Sci_Name']);
  $args = "&license=2%2C3%2C4%2C5%2C6%2C9&orientation=square,portrait";
  $comnameprefix = "%20bird";

  if (!empty($config["FLICKR_API_KEY"]) && (isset($_GET['display_limit']) || isset($_GET['hard_limit']))) {

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
        if(strpos($line, $todaytable['Sci_Name']) !== false){
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
        <?php if(isset($_GET['display_limit']) && is_numeric($_GET['display_limit'])){ ?>
          <tr class="relative" id="<?php echo $iterations; ?>">
          <td class="relative"><a target="_blank" href="index.php?filename=<?php echo $todaytable['File_Name']; ?>"><img class="copyimage" title="Open in new tab" width=25 src="images/copy.png"></a>
            
          <div class="centered_image_container">
            <?php if(!empty($config["FLICKR_API_KEY"]) && strlen($image[2]) > 0) { ?>
              <img onclick='setModalText(<?php echo $iterations; ?>,"<?php echo urlencode($image[2]); ?>",  "<?php echo $image[3]; ?>", "<?php echo $image[4]; ?>", "<?php echo $image[1]; ?>")' src="<?php echo $image[1]; ?>" class="img1">
            <?php } ?>

            <?php echo $todaytable['Time'];?><br> 
          <b><a class="a2" href="https://allaboutbirds.org/guide/<?php echo $comname;?>" target="top"><?php echo $todaytable['Com_Name'];?></a></b><br>
          <a class="a2" href="https://wikipedia.org/wiki/<?php echo $sciname;?>" target="top"><i><?php echo $todaytable['Sci_Name'];?></i></a><br>
          <b>Confidence:</b> <?php echo round((float)round($todaytable['Confidence'],2) * 100 ) . '%';?><br></div><br>
          <video onplay='setLiveStreamVolume(0)' onended='setLiveStreamVolume(1)' onpause='setLiveStreamVolume(1)' controls poster="<?php echo $filename.".png";?>" preload="none" title="<?php echo $filename;?>"><source preload="none" src="<?php echo $filename;?>"></video>
          </td>
        <?php } else { //legacy mode ?>
          <tr class="relative" id="<?php echo $iterations; ?>">
          <td><?php echo $todaytable['Time'];?><br></td><td id="recent_detection_middle_td">
          <div>
            <div>
            <?php if(!empty($config["FLICKR_API_KEY"]) && isset($_GET['hard_limit']) && strlen($image[2]) > 0) { ?>
              <img style="float:left;height:75px;" onclick='setModalText(<?php echo $iterations; ?>,"<?php echo urlencode($image[2]); ?>",  "<?php echo $image[3]; ?>", "<?php echo $image[4]; ?>", "<?php echo $image[1]; ?>")' src="<?php echo $image[1]; ?>" id="birdimage" class="img1">
            <?php } ?>
          </div>
            <div>
            <b><a class="a2" href="https://allaboutbirds.org/guide/<?php echo $comname;?>" target="top"><?php echo $todaytable['Com_Name'];?></a></b><br>
            <a class="a2" href="https://wikipedia.org/wiki/<?php echo $sciname;?>" target="top"><i><?php echo $todaytable['Sci_Name'];?></i></a><br></td>
        </div></div>
          <td><b>Confidence:</b> <?php echo round((float)round($todaytable['Confidence'],2) * 100 ) . '%';?><br></td>
          <?php if(!isset($_GET['mobile'])) { ?>
          <td style="min-width:180px"><audio controls preload="none" title="<?php echo $filename;?>"><source preload="none" src="<?php echo $filename;?>"></video>
          <?php } ?>
          </td>
        <?php } ?>
  <?php }?>
        </tr>
      </table>

  <?php 
  if($iterations == 0) {
    echo "<h3>No Detections For Today.</h3>";
  }
  
  // don't show the button if there's no more detections to be displayed, we're at the end of the list
  if($iterations >= 40 && isset($_GET['display_limit']) && is_numeric($_GET['display_limit'])) { ?>
  <center>
  <button class="loadmore" onclick="loadDetections(<?php echo $_GET['display_limit'] + 40; ?>, this);" value="Today's Detections">Load 40 More...</button>
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

    <h3>Today's Detections — <input autocomplete="off" size="11" type="text" placeholder="Search..." id="searchterm" name="searchterm"></h3>

    <div style="padding-bottom:10px" id="detections_table"><h3>Loading...</h3></div>

    <button onclick="switchViews(this);" class="legacyview">Legacy view</button>

</div>

<script>

var timer = '';
searchterm = "";

document.getElementById("searchterm").onkeydown = (function(e) {
  if (e.key === "Enter") {
      clearTimeout(timer);
      searchDetections(document.getElementById("searchterm").value);
      document.getElementById("searchterm").blur();
  } else {
     clearTimeout(timer);
     timer = setTimeout(function() {
        searchDetections(document.getElementById("searchterm").value);

        setTimeout(function() {
            // search auto submitted and now the user is probably scrolling, get the keyboard out of the way & prevent browser from jumping to the top when a video is played
            document.getElementById("searchterm").blur();
        }, 2000);
     }, 1000);
  }
});

function switchViews(element) {
  if(searchterm == ""){
    document.getElementById("detections_table").innerHTML = "<h3>Loading <?php echo $todaycount['COUNT(*)']; ?> detections...</h3>";
  } else {
    document.getElementById("detections_table").innerHTML = "<h3>Loading...</h3>";
  }
  if(element.innerHTML == "Legacy view") {
    element.innerHTML = "Normal view";
    loadDetections(undefined);
  } else if(element.innerHTML == "Normal view") {
    element.innerHTML = "Legacy view";
    loadDetections(40);
  }
}
function searchDetections(searchvalue) {
    document.getElementById("detections_table").innerHTML = "<h3>Loading...</h3>";
    searchterm = searchvalue;
    if(document.getElementsByClassName('legacyview')[0].innerHTML == "Normal view") {
      loadDetections(undefined,undefined);  
    } else {
      loadDetections(40,undefined);
    }
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
  if(searchterm != ""){
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

