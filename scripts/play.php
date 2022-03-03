<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = new SQLite3('/home/pi/BirdNET-Pi/scripts/birds.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
if($db == False){
  echo "Database is busy";
  header("refresh: 0;");
}

if(isset($_POST['bydate'])){
  $statement = $db->prepare('SELECT DISTINCT(Date), Com_Name FROM detections GROUP BY Date');
  if($statement == False){
    echo "Database is busy";
    header("refresh: 0;");
  }
  $result = $statement->execute();
  $view = "bydate";
} elseif(isset($_POST['date'])) {
  $date = $_POST['date'];
  $statement = $db->prepare("SELECT DISTINCT(Com_Name) FROM detections WHERE Date == \"$date\" ORDER BY Com_Name");
  if($statement == False){
    echo "Database is busy";
    header("refresh: 0;");
  }
  $result = $statement->execute();
  $view = "date";
} elseif(isset($_POST['species'])) {
  $species = $_POST['species'];
  $statement = $db->prepare("SELECT * FROM detections WHERE Com_Name == \"$species\" ORDER BY Com_Name");
  $statement3 = $db->prepare("SELECT Date, Time, Sci_Name, MAX(Confidence), File_Name FROM detections WHERE Com_Name == \"$species\" ORDER BY Com_Name");
  if($statement == False || $statement3 == False){
    echo "Database is busy";
    header("refresh: 0;");
  }
  $result = $statement->execute();
  $result3 = $statement3->execute();
  $view = "species";
} elseif(isset($_POST['byspecies'])) {
  $statement = $db->prepare('SELECT DISTINCT(Com_Name) FROM detections ORDER BY Com_Name');
  if($statement == False){
    echo "Database is busy";
    header("refresh: 0;");
  }
  $result = $statement->execute();
  $view = "byspecies";
} else {
  $statement = $db->prepare('SELECT DISTINCT(Date), Com_Name FROM detections GROUP BY Date');
  $result = $statement->execute();
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
if(!isset($_POST['species'])){
?>
<div class="play">
<table>
  <tr>
    <form action="" method="POST">
    <input type="hidden" name="view" value="Recordings">
<?php
  if($view == "bydate") {
    while($results=$result->fetchArray(SQLITE3_ASSOC)){
    $date = $results['Date'];
    echo "<td>
    <button action=\"submit\" name=\"date\" value=\"$date\">$date</button></td></tr>";}
  } elseif($view == "byspecies") {
    while($results=$result->fetchArray(SQLITE3_ASSOC)){
    $name = $results['Com_Name'];
    echo "<td>
    <button action=\"submit\" name=\"species\" value=\"$name\">$name</button></td></tr>";}
  } elseif($view == "date") {
    while($results=$result->fetchArray(SQLITE3_ASSOC)){
    $name = $results['Com_Name'];
    echo "<td>
    <button action=\"submit\" name=\"species\" value=\"$name\">$name</button></td></tr>";}
  } elseif($view == "choose") {
    $date = "By Date";
    $species = "By Species";
    echo "<td>
    <button action=\"submit\" name=\"byspecies\" value=\"byspecies\">$species</button></td></tr>
    <tr><td><button action=\"submit\" name=\"bydate\" value=\"bydate\">$date</button></td>";
  } else {
    while($results=$result->fetchArray(SQLITE3_ASSOC)){
    $maxconf = $results['MAX(Confidence)'];
    $date = $results['Date'];
    $time = $results['Time'];
    $sciname = $results['Sci_Name'];
    $sci_name = preg_replace('/ /', '_', $sciname);
    $sci_name = preg_replace('/ /', '_', $sci_name);
    $comname = preg_replace('/ /', '_', $species);
    $comname = preg_replace('/\'/', '', $comname);
    $file = $results['File_Name'];
    $filename = "/By_Date/".$date."/".$comname."/".$results['File_Name'];
    echo "<th>$species</th>
      <td style=\"vertical-align:middle;\"><a href=\"https://wikipedia.org/wiki/$sci_name\" target=\"top\"><i>$sciname</i></a></td>
      <td class=\"spectrogram\">Best Recording<br>$date $time<br>$maxconf<br><video controls poster=\"$filename.png\"><source src=\"$filename\"></video></td>
      </tr></table>";
    }}
     echo "</form>
  </tr>
</table>";
}
  if(isset($_POST['species'])){
    $name = $_POST['species'];
    $statement2 = $db->prepare("SELECT * FROM detections where Com_Name == \"$name\" ORDER BY Date DESC, Time DESC");
    if($statement2 == False){
      echo "Database is busy";
      header("refresh: 0;");
    }
    $result2 = $statement2->execute();
    echo "<table>
      <tr>
        <th>Listen</th>
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
          <video controls poster=\"$filename.png\"><source src=\"$filename\"></video></td>
          </tr>";

      }echo "</table>";}?>
</div>
</html>
