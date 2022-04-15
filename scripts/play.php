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
if(isset($_GET['bydate'])){
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
  $statement = $db->prepare("SELECT DISTINCT(Com_Name) FROM detections WHERE Date == \"$date\" ORDER BY Com_Name");
  if($statement == False){
    echo "Database is busy";
    header("refresh: 0;");
  }
  $result = $statement->execute();
  $view = "date";

  #By Species
} elseif(isset($_GET['byspecies'])) {
  $statement = $db->prepare('SELECT DISTINCT(Com_Name) FROM detections ORDER BY Com_Name');
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
if(!isset($_GET['species'])){
?>
<div class="play">
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
        <td>$date $time<br>$confidence<br>
        <video onplay='setLiveStreamVolume(0)' onended='setLiveStreamVolume(1)' onpause='setLiveStreamVolume(1)' controls poster=\"$filename.png\" preload=\"none\" title=\"$filename\"><source src=\"$filename\"></video></td>
        </tr>";

    }echo "</table>";}?>
</div>
</html>
