<?php
include_once "./scripts/common.php";

//$db = new SQLite3('./scripts/birds.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
//if($db == False) {
//  echo "Database busy";
//  header("refresh: 0;");
//}


if(isset($_GET['sort']) && $_GET['sort'] == "occurrences") {

//  $statement = $db->prepare('SELECT Date, Time, File_Name, Com_Name, COUNT(*), MAX(Confidence) FROM detections GROUP BY Com_Name ORDER BY COUNT(*) DESC');
//  if($statement == False) {
//    echo "Database busy";
//    header("refresh: 0;");
//  }
//  $result = $statement->execute();
//
//  $statement2 = $db->prepare('SELECT Date, Time, File_Name, Com_Name, COUNT(*), MAX(Confidence) FROM detections GROUP BY Com_Name ORDER BY COUNT(*) DESC');
//  if($statement == False) {
//    echo "Database busy";
//    header("refresh: 0;");
//  }
//
//  $result2 = $statement2->execute();

	$sort = "occurrences";
	$result2_data = getSpeciesBestRecordingList($sort);

	if ($result2_data['success'] == False) {
		echo $result2_data['message'];
		header("refresh: 0;");
	}
	$result2 = $result2_data['data'];
} else {

//  $statement = $db->prepare('SELECT Date, Time, File_Name, Com_Name, COUNT(*), MAX(Confidence) FROM detections GROUP BY Com_Name ORDER BY Com_Name ASC');
//  if($statement == False) {
//    echo "Database busy";
//    header("refresh: 0;");
//  }
//  $result = $statement->execute();
//
//  $statement2 = $db->prepare('SELECT Date, Time, File_Name, Com_Name, COUNT(*), MAX(Confidence) FROM detections GROUP BY Com_Name ORDER BY Com_Name ASC');
//  if($statement == False) {
//    echo "Database busy";
//    header("refresh: 0;");
//  }
//  $result2 = $statement2->execute();

	$result2_data = getSpeciesBestRecordingList();

	if ($result2_data['success'] == False) {
		echo $result2_data['message'];
		header("refresh: 0;");
	}
	$result2 = $result2_data['data'];
}



if(isset($_GET['species'])){
  $selection = $_GET['species'];
//  $statement3 = $db->prepare("SELECT Com_Name, Sci_Name, COUNT(*), MAX(Confidence), File_Name, Date, Time from detections WHERE Com_Name = \"$selection\"");
//  if($statement3 == False) {
//    echo "Database busy";
//    header("refresh: 0;");
//  }
//  $result3 = $statement3->execute();
	$result3_data = getBestRecordingsForSpecies($selection);
	if($result3_data['success'] == False){
		echo $result3_data['message'];
		header("refresh: 0;");
	}
	$result3 = $result3_data['data'];
}

//$user = shell_exec("awk -F: '/1000/{print $1}' /etc/passwd");
//$home = shell_exec("awk -F: '/1000/{print $6}' /etc/passwd");
//$home = trim($home);
if(!file_exists($home."/BirdNET-Pi/scripts/disk_check_exclude.txt") || strpos(file_get_contents($home."/BirdNET-Pi/scripts/disk_check_exclude.txt"),"##start") === false) {
  file_put_contents($home."/BirdNET-Pi/scripts/disk_check_exclude.txt", "");
  file_put_contents($home."/BirdNET-Pi/scripts/disk_check_exclude.txt", "##start\n##end\n");
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
    <input type="hidden" name="sort" value="<?php if(isset($_GET['sort'])){echo $_GET['sort'];}?>">
      <input type="hidden" name="view" value="Species Stats">
      <button <?php if(!isset($_GET['sort']) || $_GET['sort'] == "alphabetical"){ echo "style='background:#9fe29b !important;'"; }?> class="sortbutton" type="submit" name="sort" value="alphabetical">
         <img src="images/sort_abc.svg" title="Sort by alphabetical" alt="Sort by alphabetical">
      </button>
      <button <?php if(isset($_GET['sort']) && $_GET['sort'] == "occurrences"){ echo "style='background:#9fe29b !important;'"; }?> class="sortbutton" type="submit" name="sort" value="occurrences">
         <img src="images/sort_occ.svg" title="Sort by occurrences" alt="Sort by occurrences">
      </button>
   </form>
</div>
<table style="padding-top:10px">
  <?php
  $birds = array();
  foreach($result2 as $results)
  {
    $comname = preg_replace('/ /', '_', $results['Com_Name']);
    $comname = preg_replace('/\'/', '', $comname);
    $filename = "/By_Date/".$results['Date']."/".$comname."/".$results['File_Name'];
    $birds[] = $results['Com_Name'];
  }

  if(count($birds) > 45) {
    $num_cols = 3;
  } else {
    $num_cols = 1;
  }
  $num_rows = ceil(count($birds) / $num_cols);

  for ($row = 0; $row < $num_rows; $row++) {
    echo "<tr>";

    for ($col = 0; $col < $num_cols; $col++) {
      $index = $row + $col * $num_rows;

      if ($index < count($birds)) {
        ?>
        <td>
          <form action="" method="GET">
            <input type="hidden" name="sort" value="<?php if(isset($_GET['sort'])){echo $_GET['sort'];}?>">
            <input type="hidden" name="view" value="Species Stats">
            <button type="submit" name="species" value="<?php echo $birds[$index];?>"><?php echo $birds[$index];?></button>
          </form>
        </td>
        <?php
      } else {
        echo "<td></td>";
      }
    }

    echo "</tr>";
  }
  ?>
</table>
<style>
td {
  padding: 0px;
  width: calc(100% / <?php echo $num_cols;?>);
}
tr:first-child td {
  padding-top: 10px;
}
</style>

</div>
<dialog style="margin-top: 5px;max-height: 95vh;
  overflow-y: auto;overscroll-behavior:contain" id="attribution-dialog">
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

function setModalText(iter, title, text, authorlink) {
  document.getElementById('modalHeading').innerHTML = "Photo "+iter+": \""+title+"\" Attribution";
  document.getElementById('modalText').innerHTML = "<div style='white-space:nowrap'>Image link: <a target='_blank' href="+text+">"+text+"</a><br>Author link: <a target='_blank' href="+authorlink+">"+authorlink+"</a></div>";
  showDialog();
}
</script>  
<div class="column center">
<?php if(!isset($_GET['species'])){
?><p class="centered">Choose a species to load images from Flickr.</p>
<?php
};?>
<?php if(isset($_GET['species'])){
  $species = $_GET['species'];
  $iter=0;
  $lines;
foreach ($result3 as $results){
  $count = $results['COUNT(*)'];
  $maxconf = round((float)round($results['MAX(Confidence)'],2) * 100 ) . '%';
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
  <td class=\"relative\"><a target=\"_blank\" href=\"index.php?filename=".$results['File_Name']."\"><img title=\"Open in new tab\" class=\"copyimage\" width=25 src=\"images/copy.png\"></a> <a href=\"https://wikipedia.org/wiki/$dbsciname\" target=\"top\"/><i>$sciname</i></a><br>
  <b>Occurrences: </b>$count<br>
  <b>Max Confidence: </b>$maxconf<br>
  <b>Best Recording: </b>$date $time<br>
  <a href=\"https://allaboutbirds.org/guide/$comname\" target=\"top\"/>All About Birds</a><br>
  <video onplay='setLiveStreamVolume(0)' onended='setLiveStreamVolume(1)' onpause='setLiveStreamVolume(1)' controls poster=\"$filename.png\" title=\"$filename\"><source src=\"$filename\"></video></td>
  </tr>
    </table>
  <p>Loading Images from Flickr</p>", '6096');
  
  echo "<script>document.getElementsByTagName(\"h3\")[0].scrollIntoView();</script>";
  
  ob_flush();
  flush();
	$flickr_data = getFlickrImage($results, true);

	//Loop over the photos data
	if ($flickr_data['image_found']) {
		$flickr_image_data = $flickr_data['data']['photos'];
		$iter = 0;
		foreach ($flickr_image_data as $key => $img_data) {
			$imageurl = $img_data['image_url'];
			$title = $img_data['photo_title'];
			$modaltext = $img_data['modal_text'];
			$authorlink = $img_data['author_link'];

			echo "<span style='cursor:pointer;' onclick='setModalText(" . $iter . ",\"" . $title . "\",\"" . $modaltext . "\", \"" . $authorlink . "\")'><img style='vertical-align:top' src=\"$imageurl\"></span>";
			$iter++;
		}
	}
}
}
?>
<?php if(isset($_GET['species'])){?>
<br><br>
<div class="brbanner">Best Recordings for Other Species:</div><br>
<?php } else {?>
<hr><br>
<?php } ?>

    <table>
<?php
$excludelines = [];
foreach($result2 as $results)
{
$comname = preg_replace('/ /', '_', $results['Com_Name']);
$comname = preg_replace('/\'/', '', $comname);
$filename = "/By_Date/".$results['Date']."/".$comname."/".$results['File_Name'];

array_push($excludelines, $results['Date']."/".$comname."/".$results['File_Name']);
array_push($excludelines, $results['Date']."/".$comname."/".$results['File_Name'].".png");
?>
      <tr>
      <form action="" method="GET">
        <input type="hidden" name="sort" value="<?php if(isset($_GET['sort'])){echo $_GET['sort'];}?>">
      <td class="relative"><a target="_blank" href="index.php?filename=<?php echo $results['File_Name']; ?>"><img title="Open in new tab" class="copyimage" width=25 src="images/copy.png"></a><input type="hidden" name="view" value="Species Stats">
        <button type="submit" name="species" value="<?php echo $results['Com_Name'];?>"><?php echo $results['Com_Name'];?></button><br><b>Occurrences:</b> <?php echo $results['COUNT(*)'];?><br>
      <b>Max Confidence:</b> <?php echo $percent = round((float)round($results['MAX(Confidence)'],2) * 100 ) . '%';?><br>
      <b>Best Recording:</b> <?php echo $results['Date']." ".$results['Time'];?><br><video onplay='setLiveStreamVolume(0)' onended='setLiveStreamVolume(1)' onpause='setLiveStreamVolume(1)' controls poster="<?php echo $filename.".png";?>" preload="none" title="<?php echo $filename;?>"><source src="<?php echo $filename;?>" type="audio/mp3"></video></td>
      </tr>
<?php
}

$file = file_get_contents(getDirectory('home')."/BirdNET-Pi/scripts/disk_check_exclude.txt");
file_put_contents(getDirectory('home')."/BirdNET-Pi/scripts/disk_check_exclude.txt", "##start"."\n".implode("\n",$excludelines)."\n".substr($file, strpos($file, "##end")));
?>
    </table>
      </form>
</div>
</div>
</body>
</html>
