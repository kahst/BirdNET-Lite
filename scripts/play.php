<?php
if(file_exists('./scripts/common.php')){
	include_once "./scripts/common.php";
}else{
	include_once "./common.php";
}

if(isset($_GET['deletefile'])) {
  if(isset($_SERVER['PHP_AUTH_USER'])) {
    $submittedpwd = $_SERVER['PHP_AUTH_PW'];
    $submitteduser = $_SERVER['PHP_AUTH_USER'];
    if($submittedpwd == $config['CADDY_PWD'] && $submitteduser == 'birdnet'){

	  $filename_to_delete = $_GET['deletefile'];
      $message = deleteDetection($filename_to_delete)['message'];
      echo $message;
      die();

    } else {
      header('WWW-Authenticate: Basic realm="My Realm"');
      header('HTTP/1.0 401 Unauthorized');
      echo 'You must be authenticated to change the protection of files.';
      exit;
    }
  } else {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'You must be authenticated to change the protection of files.';
    exit;
  }
}

if(isset($_GET['excludefile'])) {
  if(isset($_SERVER['PHP_AUTH_USER'])) {
    $submittedpwd = $_SERVER['PHP_AUTH_PW'];
    $submitteduser = $_SERVER['PHP_AUTH_USER'];
    if($submittedpwd == $config['CADDY_PWD'] && $submitteduser == 'birdnet'){

      if(isset($_GET['exclude_add'])) {
		  $response_data = protectDetectionFromDeletion('protect', $_GET['excludefile']);
		  echo $response_data['message'];
		  die();
      } else {
		  $response_data = protectDetectionFromDeletion('unprotect', $_GET['excludefile']);
		  echo $response_data['message'];
		  die();
      }

    } else {
      header('WWW-Authenticate: Basic realm="My Realm"');
      header('HTTP/1.0 401 Unauthorized');
      echo 'You must be authenticated to change the protection of files.';
      exit;
    }
  } else {
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'You must be authenticated to change the protection of files.';
    exit;
  }
}

$shifted_path = getDirectory('shifted_dir');

if(isset($_GET['shiftfile'])) {

	$filename = $_GET['shiftfile'];
	$doShift = null;
	if (isset($_GET['doshift'])) {
		$doShift = true;
	}

	$response_data = frequencyShiftDetectionAudio($filename, $doShift);
    echo $response_data['message'];
    die();
}

if(isset($_GET['bydate'])){
  $result_data = getDetectionsByDate();
	if($result_data['success'] == False){
		echo $result_data['message'];
		header("refresh: 0;");
	}
	$result = $result_data['data'];
  $view = "bydate";

  #Specific Date
} elseif(isset($_GET['date'])) {
  $date = $_GET['date'];
  session_start();
  $_SESSION['date'] = $date;
  if(isset($_GET['sort']) && $_GET['sort'] == "occurrences") {
    $sort = $_GET['sort'];
  } else {
    $sort = null;
  }
	$result_data = getDetectionsByDate($date, $sort);
	if($result_data['success'] == False){
		echo $result_data['message'];
		header("refresh: 0;");
	}
	$result = $result_data['data'];

  $view = "date";

  #By Species
} elseif(isset($_GET['byspecies'])) {
	if(isset($_GET['sort']) && $_GET['sort'] == "occurrences") {
		$sort = $_GET['sort'];
	} else {
		$sort = null;
	}

  session_start();

  $resultArr_data = getDetectionsBySpecies(null, $sort);
	if($resultArr_data['success'] == False){
		echo $resultArr_data['message'];
		header("refresh: 0;");
	}
	$result = $resultArr_data['data']['species'];

  $view = "byspecies";

  #Specific Species
} elseif(isset($_GET['species'])) {
  $species = $_GET['species'];
  session_start();
  $_SESSION['species'] = $species;

	$resultArr_data = getDetectionsBySpecies($species, null);
	if($resultArr_data['success'] == False){
		echo $resultArr_data['message'];
		header("refresh: 0;");
	}
	$result = $resultArr_data['data']['species'];
    $resul3 = $resultArr_data['data']['species_MaxConf'];
	$view = "species";

} else {
  session_start();
  session_unset();
  $view = "choose";
}
?>

<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
    </style>
  </head>

<script>
function deleteDetection(filename,copylink=false) {
  if (confirm("Are you sure you want to delete this detection from the database?") == true) {
    const xhttp = new XMLHttpRequest();
    xhttp.onload = function() {
      if(this.responseText == "OK"){
        if(copylink == true) {
          window.top.close();
        } else {
          location.reload();
        }
      } else {
        alert("Database busy.")
      }
    }
    xhttp.open("GET", "play.php?deletefile="+filename, true);
    xhttp.send();
  }
}

function toggleLock(filename, type, elem) {
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
    if(this.responseText == "OK"){
      if(type == "add") {
        elem.setAttribute("src","images/lock.svg");
        elem.setAttribute("title", "This file is excluded from being purged.");
        elem.setAttribute("onclick", elem.getAttribute("onclick").replace("add","del"));
      } else {
        elem.setAttribute("src","images/unlock.svg");
        elem.setAttribute("title", "This file will be deleted when disk space needs to be freed.");
        elem.setAttribute("onclick", elem.getAttribute("onclick").replace("del","add"));
      }
    }
  }
  if(type == "add") {
    xhttp.open("GET", "play.php?excludefile="+filename+"&exclude_add=true", true);
  } else {
    xhttp.open("GET", "play.php?excludefile="+filename+"&exclude_del=true", true);  
  }
  xhttp.send();
  elem.setAttribute("src","images/spinner.gif");
}

function toggleShiftFreq(filename, shiftAction, elem) {
  const xhttp = new XMLHttpRequest();
  xhttp.onload = function() {
    if(this.responseText == "OK"){
      if(shiftAction == "shift") {
        elem.setAttribute("src","images/unshift.svg");
        elem.setAttribute("title", "This file has been shifted down in frequency.");
        elem.setAttribute("onclick", elem.getAttribute("onclick").replace("shift","unshift"));
        console.log("shifted freqs of " + filename);
  video=elem.parentNode.getElementsByTagName("video")[0];
  video.setAttribute("title", video.getAttribute("title").replace("/By_Date/","/By_Date/shifted/"));
  source = video.getElementsByTagName("source")[0];
  source.setAttribute("src", source.getAttribute("src").replace("/By_Date/","/By_Date/shifted/"));
  video.load();
      } else {
        elem.setAttribute("src","images/shift.svg");
        elem.setAttribute("title", "This file is not shifted in frequency.");
        elem.setAttribute("onclick", elem.getAttribute("onclick").replace("unshift","shift"));
        console.log("unshifted freqs of " + filename);
  video=elem.parentNode.getElementsByTagName("video")[0];
  video.setAttribute("title", video.getAttribute("title").replace("/By_Date/shifted/","/By_Date/"));
  source = video.getElementsByTagName("source")[0];
  source.setAttribute("src", source.getAttribute("src").replace("/By_Date/shifted/","/By_Date/"));
  video.load();
      }
    }
  }
  if(shiftAction == "shift") {
    console.log("shifting freqs of " + filename);
    xhttp.open("GET", "play.php?shiftfile="+filename+"&doshift=true", true);
  } else {
    console.log("unshifting freqs of " + filename);
    xhttp.open("GET", "play.php?shiftfile="+filename, true);  
  }
  xhttp.send();
  elem.setAttribute("src","images/spinner.gif");
}
</script>

<?php
#If no specific species
if(!isset($_GET['species']) && !isset($_GET['filename'])){
?>
<div class="play">
<?php if($view == "byspecies" || $view == "date") { ?>
<div style="width: auto;
   text-align: center">
   <form action="" method="GET">
      <input type="hidden" name="view" value="Recordings">
      <input type="hidden" name="<?php echo $view; ?>" value="<?php echo $_GET['date']; ?>">
      <button <?php if(!isset($_GET['sort']) || $_GET['sort'] == "alphabetical"){ echo "style='background:#9fe29b !important;'"; }?> class="sortbutton" type="submit" name="sort" value="alphabetical">
         <img src="images/sort_abc.svg" title="Sort by alphabetical" alt="Sort by alphabetical">
      </button>
      <button <?php if(isset($_GET['sort']) && $_GET['sort'] == "occurrences"){ echo "style='background:#9fe29b !important;'"; }?> class="sortbutton" type="submit" name="sort" value="occurrences">
         <img src="images/sort_occ.svg" title="Sort by occurrences" alt="Sort by occurrences">
      </button>
   </form>
</div>
<?php } ?>

<table>
  <tr>
    <form action="" method="GET">
    <input type="hidden" name="view" value="Recordings">
<?php
  #By Date
  if($view == "bydate") {
      foreach ($result as $bd_result){
		  $date = $bd_result['Date'];
		  if(realpath(getDirectory('extracted_by_date') . "/" . $date) !== false){
			  echo "<td>
          <button action=\"submit\" name=\"date\" value=\"$date\">".($date == date('Y-m-d') ? "Today" : $date)."</button></td></tr>";}
	  }

          #By Species
  } elseif($view == "byspecies") {
    $birds = array();
	  foreach ($result as $species_bird_name) {
		  $name = $species_bird_name['Com_Name'];
		  $birds[] = $name;
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
          <td class="spec">
              <button type="submit" name="species" value="<?php echo $birds[$index];?>"><?php echo $birds[$index];?></button>
          </td>
          <?php
        } else {
          echo "<td></td>";
        }
      }

      echo "</tr>";
    }
  } elseif($view == "date") {
    $birds = array();
	  foreach ($result as $species_bird_name) {
		  $name = $species_bird_name['Com_Name'];
		  if (realpath(getDirectory('extracted_by_date') . "/" . $date . "/" . str_replace(" ", "_", $name)) !== false) {
			  $birds[] = $name;
		  }
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
      <td class="spec">
          <button type="submit" name="species" value="<?php echo $birds[$index];?>"><?php echo $birds[$index];?></button>
      </td>
      <?php
    } else {
      echo "<td></td>";
    }
  }

  echo "</tr>";
}

    #Choose
  } else {
    echo "<td>
      <button action=\"submit\" name=\"byspecies\" value=\"byspecies\">By Species</button></td></tr>
      <tr><td><button action=\"submit\" name=\"bydate\" value=\"bydate\">By Date</button></td>";
  } 

  echo "</form>
    </tr>
    </table>";
}

#Specific Species
if(isset($_GET['species'])){ ?>
<div style="width: auto;
   text-align: center">
   <form action="" method="GET">
      <input type="hidden" name="view" value="Recordings">
      <input type="hidden" name="species" value="<?php echo $_GET['species']; ?>">
      <input type="hidden" name="sort" value="<?php echo $_GET['sort']; ?>">
      <button <?php if(!isset($_GET['sort']) || $_GET['sort'] == "" || $_GET['sort'] == "date"){ echo "style='background:#9fe29b !important;'"; }?> class="sortbutton" type="submit" name="sort" value="date">
         <img width=35px src="images/sort_date.svg" title="Sort by date" alt="Sort by date">
      </button>
      <button <?php if(isset($_GET['sort']) && $_GET['sort'] == "confidence"){ echo "style='background:#9fe29b !important;'"; }?> class="sortbutton" type="submit" name="sort" value="confidence">
         <img src="images/sort_occ.svg" title="Sort by confidence" alt="Sort by confidence">
      </button><br>
      <input style="margin-top:10px" <?php if(isset($_GET['only_excluded'])){ echo "checked"; }?> type="checkbox" name="only_excluded" onChange="submit()">
      <label for="onlyverified">Only Show Purge Excluded</label>
   </form>
</div>
<?php
  $disk_check_exclude_path = getFilePath('disk_check_exclude.txt');
  // add disk_check_exclude.txt lines into an array for grepping
  $fp = @fopen($disk_check_exclude_path, 'r');
if ($fp) {
  $disk_check_exclude_arr = explode("\n", fread($fp, filesize($disk_check_exclude_path)));
}

$name = $_GET['species'];
$confidence = null;
if(isset($_SESSION['date'])) {
  $date = $_SESSION['date'];
  if(isset($_GET['sort']) && $_GET['sort'] == "confidence") {
      $confidence = $_GET['sort'];
  }
	$result2_data = getSpeciesDetectionInfo($name, $date, $confidence);
} else {
  if(isset($_GET['sort']) && $_GET['sort'] == "confidence") {
	  $confidence = $_GET['sort'];
  }
	$result2_data = getSpeciesDetectionInfo($name, null, $confidence);
}

	if ($result2_data['success'] == False) {
		echo $result2_data['message'];
		header("refresh: 0;");
	}
	$result2 = $result2_data['data'];

//Count number of records we have
$num_rows = count($result2);

echo "<table>
  <tr>
  <th>$name</th>
  </tr>";
  $iter=0;
  while($iter < count($result2))
  {
    $results = $result2[$iter];
    $comname = preg_replace('/ /', '_', $results['Com_Name']);
    $comname = preg_replace('/\'/', '', $comname);
    $date = $results['Date'];
    $filename = "/By_Date/".$date."/".$comname."/".$results['File_Name'];
    $filename_shifted = "/By_Date/shifted/".$date."/".$comname."/".$results['File_Name'];
    $filename_png = $filename . ".png";
    $sciname = preg_replace('/ /', '_', $results['Sci_Name']);
    $sci_name = $results['Sci_Name'];
    $time = $results['Time'];
    $confidence = round((float)round($results['Confidence'],2) * 100 ) . '%';
    $filename_formatted = $date."/".$comname."/".$results['File_Name'];

	$iter++;
    // file was deleted by disk check, no need to show the detection in recordings
    if(!file_exists(getDirectory('extracted') . "/" . $filename)) {
      continue;
    }
    if(!in_array($filename_formatted, $disk_check_exclude_arr) && isset($_GET['only_excluded'])) {
      continue;
    }

    if($num_rows < 100){
      $imageelem = "<video onplay='setLiveStreamVolume(0)' onended='setLiveStreamVolume(1)' onpause='setLiveStreamVolume(1)' controls poster=\"$filename_png\" preload=\"none\" title=\"$filename\"><source src=\"$filename\"></video>";
    } else {
      $imageelem = "<a target='_blank' href=\"$filename\"><img src=\"$filename_png\"></a>";
    }

    if($config["FULL_DISK"] == "purge") {
      if(!in_array($filename_formatted, $disk_check_exclude_arr)) {
        $imageicon = "images/unlock.svg";
        $title = "This file will be deleted when disk space needs to be freed (>95% usage).";
        $type = "add";
      } else {
        $imageicon = "images/lock.svg";
        $title = "This file is excluded from being purged.";
        $type = "del";
      }

      if(file_exists($shifted_path.$filename_formatted)) {
        $shiftImageIcon = "images/unshift.svg";
        $shiftTitle = "This file has been shifted down in frequency."; 
        $shiftAction = "unshift";
  $filename = $filename_shifted;
      } else {
        $shiftImageIcon = "images/shift.svg";
        $shiftTitle = "This file is not shifted in frequency.";
        $shiftAction = "shift";
      }

      echo "<tr>
  <td class=\"relative\"> 

<img style='cursor:pointer;right:90px' src='images/delete.svg' onclick='deleteDetection(\"".$filename_formatted."\")' class=\"copyimage\" width=25 title='Delete Detection'> 
<img style='cursor:pointer;right:45px' onclick='toggleLock(\"".$filename_formatted."\",\"".$type."\", this)' class=\"copyimage\" width=25 title=\"".$title."\" src=\"".$imageicon."\"> 
<img style='cursor:pointer' onclick='toggleShiftFreq(\"".$filename_formatted."\",\"".$shiftAction."\", this)' class=\"copyimage\" width=25 title=\"".$shiftTitle."\" src=\"".$shiftImageIcon."\"> $date $time<br>$confidence<br>

        ".$imageelem."
        </td>
        </tr>";
    } else {
      echo "<tr>
  <td class=\"relative\">$date $time<br>$confidence
<img style='cursor:pointer' src='images/delete.svg' onclick='deleteDetection(\"".$filename_formatted."\")' class=\"copyimage\" width=25 title='Delete Detection'><br>
        ".$imageelem."
        </td>
        </tr>";
    }

  }if($iter == 0){ echo "<tr><td><b>No recordings were found.</b><br><br><span style='font-size:medium'>They may have been deleted to make space for new recordings. You can prevent this from happening in the future by clicking the <img src='images/unlock.svg' style='width:20px'> icon in the top right of a recording.<br>You can also modify this behavior globally under \"Full Disk Behavior\" <a href='views.php?view=Advanced'>here.</a></span></td></tr>";}echo "</table>";}

  if(isset($_GET['filename'])){
	$disk_check_exclude_path = getFilePath('disk_check_exclude.txt');
	$name = $_GET['filename'];
    $result2_data = getDetectionsByFilename($name);
	  if($result2_data['success'] == False){
		  echo $result2_data['message'];
		  header("refresh: 0;");
	  }
	  $result2 = $result2_data['data'];
    echo "<table>
      <tr>
      <th>$name</th>
      </tr>";
      foreach ($result2 as $results)
      {
        $comname = preg_replace('/ /', '_', $results['Com_Name']);
        $comname = preg_replace('/\'/', '', $comname);
        $date = $results['Date'];
        $filename = "/By_Date/".$date."/".$comname."/".$results['File_Name'];
        $filename_shifted = "/By_Date/shifted/".$date."/".$comname."/".$results['File_Name'];
        $filename_png = $filename . ".png";
        $sciname = preg_replace('/ /', '_', $results['Sci_Name']);
        $sci_name = $results['Sci_Name'];
        $time = $results['Time'];
        $confidence = round((float)round($results['Confidence'],2) * 100 ) . '%';
        $filename_formatted = $date."/".$comname."/".$results['File_Name'];

        // add disk_check_exclude.txt lines into an array for grepping
        $fp = @fopen($disk_check_exclude_path, 'r');
        if ($fp) {
          $disk_check_exclude_arr = explode("\n", fread($fp, filesize($disk_check_exclude_path)));
        }

        if($config["FULL_DISK"] == "purge") {
          if(!in_array($filename_formatted, $disk_check_exclude_arr)) {
            $imageicon = "images/unlock.svg";
            $title = "This file will be deleted when disk space needs to be freed (>95% usage).";
            $type = "add";
          } else {
            $imageicon = "images/lock.svg";
            $title = "This file is excluded from being purged.";
            $type = "del";
          }

      if(file_exists($shifted_path.$filename_formatted)) {
        $shiftImageIcon = "images/unshift.svg";
        $shiftTitle = "This file has been shifted down in frequency."; 
        $shiftAction = "unshift";
  $filename = $filename_shifted;
      } else {
        $shiftImageIcon = "images/shift.svg";
        $shiftTitle = "This file is not shifted in frequency.";
        $shiftAction = "shift";
      }

          echo "<tr>
      <td class=\"relative\"> 

<img style='cursor:pointer;right:90px' src='images/delete.svg' onclick='deleteDetection(\"".$filename_formatted."\", true)' class=\"copyimage\" width=25 title='Delete Detection'> 
<img style='cursor:pointer;right:45px' onclick='toggleLock(\"".$filename_formatted."\",\"".$type."\", this)' class=\"copyimage\" width=25 title=\"".$title."\" src=\"".$imageicon."\"> 
<img style='cursor:pointer' onclick='toggleShiftFreq(\"".$filename_formatted."\",\"".$shiftAction."\", this)' class=\"copyimage\" width=25 title=\"".$shiftTitle."\" src=\"".$shiftImageIcon."\">$date $time<br>$confidence<br>

<video onplay='setLiveStreamVolume(0)' onended='setLiveStreamVolume(1)' onpause='setLiveStreamVolume(1)' controls poster=\"$filename_png\" preload=\"none\" title=\"$filename\"><source src=\"$filename\"></video></td>
            </tr>";
        } else {
          echo "<tr>
      <td class=\"relative\">$date $time<br>$confidence
<img style='cursor:pointer' src='images/delete.svg' onclick='deleteDetection(\"".$filename_formatted."\", true)' class=\"copyimage\" width=25 title='Delete Detection'><br>
            <video onplay='setLiveStreamVolume(0)' onended='setLiveStreamVolume(1)' onpause='setLiveStreamVolume(1)' controls poster=\"$filename_png\" preload=\"none\" title=\"$filename\"><source src=\"$filename\"></video></td>
            </tr>";
        }

      }
      echo "</table>";
  }?>
</div>
<style>
td.spec {
  width: calc(100% / <?php echo $num_cols;?>);
}
tr:first-child td.spec {
  padding-top: 10px;
}
</style>

</html>
