<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = new SQLite3('./scripts/birds.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
if($db == False){
  echo "Database is busy";
  header("refresh: 0;");
}

#By Date
if(isset($_GET['byfilename'])){
  $statement = $db->prepare('SELECT DISTINCT(Date) FROM detections GROUP BY Date');
  if($statement == False){
    echo "Database is busy";
    header("refresh: 0;");
  }
  $result = $statement->execute();
  $view = "bydate";
  #By Date
}elseif(isset($_GET['bydate'])){
  $statement = $db->prepare('SELECT DISTINCT(Date) FROM detections GROUP BY Date');
  if($statement == False){
    echo "Database is busy";
    header("refresh: 0;");
  }
  $result = $statement->execute();
  $view = "bydate";

  #Specific Date
} elseif(isset($_GET['date'])) {
  $date = $_GET['date'];
  session_start();
  $_SESSION['date'] = $date;
  if(isset($_GET['sort']) && $_GET['sort'] == "occurrences") {
    $statement = $db->prepare("SELECT DISTINCT(Com_Name) FROM detections WHERE Date == \"$date\" GROUP BY Com_Name ORDER BY COUNT(*) DESC");
  } else {
    $statement = $db->prepare("SELECT DISTINCT(Com_Name) FROM detections WHERE Date == \"$date\" ORDER BY Com_Name");
  }
  if($statement == False){
    echo "Database is busy";
    header("refresh: 0;");
  }
  $result = $statement->execute();
  $view = "date";

  #By Species
} elseif(isset($_GET['byspecies'])) {
  if(isset($_GET['sort']) && $_GET['sort'] == "occurrences") {
  $statement = $db->prepare('SELECT DISTINCT(Com_Name) FROM detections GROUP BY Com_Name ORDER BY COUNT(*) DESC');
  } else {
    $statement = $db->prepare('SELECT DISTINCT(Com_Name) FROM detections ORDER BY Com_Name ASC');
  } 
  session_start();
  if($statement == False){
    echo "Database is busy";
    header("refresh: 0;");
  }
  $result = $statement->execute();
  $view = "byspecies";

  #Specific Species
} elseif(isset($_GET['species'])) {
  $species = $_GET['species'];
  session_start();
  $_SESSION['species'] = $species;
  $statement = $db->prepare("SELECT * FROM detections WHERE Com_Name == \"$species\" ORDER BY Com_Name");
  $statement3 = $db->prepare("SELECT Date, Time, Sci_Name, MAX(Confidence), File_Name FROM detections WHERE Com_Name == \"$species\" ORDER BY Com_Name");
  if($statement == False || $statement3 == False){
    echo "Database is busy";
    header("refresh: 0;");
  }
  $result = $statement->execute();
  $result3 = $statement3->execute();
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
<?php } ?>


<table>
  <tr>
    <form action="" method="GET">
    <input type="hidden" name="view" value="Recordings">
<?php
  #By Date
  if($view == "bydate") {
    while($results=$result->fetchArray(SQLITE3_ASSOC)){
      $date = $results['Date'];
      echo "<td>
        <button action=\"submit\" name=\"date\" value=\"$date\">$date</button></td></tr>";}

  #By Species
  } elseif($view == "byspecies") {
    while($results=$result->fetchArray(SQLITE3_ASSOC)){
      $name = $results['Com_Name'];
      echo "<td>
        <button action=\"submit\" name=\"species\" value=\"$name\">$name</button></td></tr>";}

  #Specific Date
  } elseif($view == "date") {
    while($results=$result->fetchArray(SQLITE3_ASSOC)){
      $name = $results['Com_Name'];
      echo "<td>
        <button action=\"submit\" name=\"species\" value=\"$name\">$name</button></td></tr>";}

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
if(isset($_GET['species'])){
  $name = $_GET['species'];
  if(isset($_SESSION['date'])) {
    $date = $_SESSION['date'];
    $statement2 = $db->prepare("SELECT * FROM detections where Com_Name == \"$name\" AND Date == \"$date\" ORDER BY Time DESC");
  } else {
  $statement2 = $db->prepare("SELECT * FROM detections where Com_Name == \"$name\" ORDER BY Date DESC, Time DESC");}
  if($statement2 == False){
    echo "Database is busy";
    header("refresh: 0;");
  }
  $result2 = $statement2->execute();
  echo "<table>
    <tr>
    <th>$name</th>
    </tr>";
    while($results=$result2->fetchArray(SQLITE3_ASSOC))
    {
      $comname = preg_replace('/ /', '_', $results['Com_Name']);
      $comname = preg_replace('/\'/', '', $comname);
      $date = $results['Date'];
      $filename = "/By_Date/".$date."/".$comname."/".$results['File_Name'];
      $sciname = preg_replace('/ /', '_', $results['Sci_Name']);
      $sci_name = $results['Sci_Name'];
      $time = $results['Time'];
      $confidence = $results['Confidence'];
      echo "<tr>
        <td class=\"relative\"><a target=\"_blank\" href=\"index.php?filename=".$results['File_Name']."\"><img class=\"copyimage\" width=25 src=\"images/copy.png\"></a>$date $time<br>$confidence<br>
        <video onplay='setLiveStreamVolume(0)' onended='setLiveStreamVolume(1)' onpause='setLiveStreamVolume(1)' controls poster=\"$filename.png\" preload=\"none\" title=\"$filename\"><source src=\"$filename\"></video></td>
        </tr>";

    }echo "</table>";}

if(isset($_GET['filename'])){
  $name = $_GET['filename'];
  $statement2 = $db->prepare("SELECT * FROM detections where File_name == \"$name\" ORDER BY Date DESC, Time DESC");
  if($statement2 == False){
    echo "Database is busy";
    header("refresh: 0;");
  }
  $result2 = $statement2->execute();
  echo "<table>
    <tr>
    <th>$name</th>
    </tr>";
    while($results=$result2->fetchArray(SQLITE3_ASSOC))
    {
      $comname = preg_replace('/ /', '_', $results['Com_Name']);
      $comname = preg_replace('/\'/', '', $comname);
      $date = $results['Date'];
      $filename = "/By_Date/".$date."/".$comname."/".$results['File_Name'];
      $sciname = preg_replace('/ /', '_', $results['Sci_Name']);
      $sci_name = $results['Sci_Name'];
      $time = $results['Time'];
      $confidence = $results['Confidence'];
      echo "<tr>
        <td>$date $time<br>$confidence<br>
        <video onplay='setLiveStreamVolume(0)' onended='setLiveStreamVolume(1)' onpause='setLiveStreamVolume(1)' controls poster=\"$filename.png\" preload=\"none\" title=\"$filename\"><source src=\"$filename\"></video></td>
        </tr>";

    }echo "</table>";}?>
</div>
</html>
