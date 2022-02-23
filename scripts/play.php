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
  $statement = $db->prepare('SELECT DISTINCT(Date), Com_Name from detections GROUP BY Date');
  if($statement == False){
    echo "Database is busy";
    header("refresh: 0;");
  }
  $result = $statement->execute();
  $view = "bydate";
} elseif(isset($_POST['byspecies'])) {
  $statement = $db->prepare('SELECT DISTINCT(Com_Name) from detections ORDER BY Com_Name');
  if($statement == False){
    echo "Database is busy";
    header("refresh: 0;");
  }
  $result = $statement->execute();
  $view = "byspecies";
} elseif(isset($_POST['date'])) {
  $date = $_POST['date'];
  $statement = $db->prepare("SELECT DISTINCT(Com_Name) from detections WHERE Date == \"$date\" ORDER BY Com_Name");
  if($statement == False){
    echo "Database is busy";
    header("refresh: 0;");
  }
  $result = $statement->execute();
  $view = "date";
} elseif(isset($_POST['species'])) {
  $species = $_POST['species'];
  $statement = $db->prepare("SELECT * from detections WHERE Com_Name == \"$species\" ORDER BY Com_Name");
  $statement3 = $db->prepare("SELECT Date, Time, Com_Name, MAX(Confidence), File_Name from detections WHERE Com_Name == \"$species\" ORDER BY Com_Name");
  if($statement == False || $statement3 == False){
    echo "Database is busy";
    header("refresh: 0;");
  }
  $result = $statement->execute();
  $result3 = $statement3->execute();
  $view = "species";
}

?>

<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="play.css">
    <style>
    </style>
  </head>
<body>
<div class="column left">
<table style="float:top;">
<?php
if(!isset($_POST['species'])){
while($results=$result->fetchArray(SQLITE3_ASSOC))
{
?>
  <tr>
    <form action="" method="POST">
    <td>
<?php
  if($view == "bydate"){
    $date = $results['Date'];
    echo "<button action=\"submit\" name=\"date\" value=\"$date\">$date</button>";
  } elseif($view == "byspecies") {
    $name = $results['Com_Name'];
    echo "<button action=\"submit\" name=\"species\" value=\"$name\">$name</button>";
  } elseif($view == "date") {
    $name = $results['Com_Name'];
    echo "<button action=\"submit\" name=\"species\" value=\"$name\">$name</button>";
  }; };} else {
    while($results=$result3->fetchArray(SQLITE3_ASSOC)){
    $maxconf = $results['MAX(Confidence)'];
    $date = $results['Date'];
    $time = $results['Time'];
    $comname = preg_replace('/ /', '_', $results['Com_Name']);
    $file = $results['File_Name'];
    $filename = "/By_Date/".$date."/".$comname."/".$results['File_Name'];
    echo "<table>
      <tr>
      <th>Max Confidence: $maxconf</th>
      </tr>
      <tr>
      <td>Most confident recording: <a href=\"$filename\" target=\"footer\">$file</a></td>
      </tr></table>";
    };};?>
    </td>
    </form>
  </tr>
</table>
</div>
<div class="column right">
<?php
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
      <th>Date</th>
      <th>Time</th>
      <th>Scientific Name</th>
      <th>Common Name</th>
      <th>Confidence</th>
      </tr>";
      while($results=$result2->fetchArray(SQLITE3_ASSOC))
      {
        $comname = preg_replace('/ /', '_', $results['Com_Name']);
        $date = $results['Date'];
        $comlink = "/By_Date/".$date."/".$comname."/".$results['File_Name'];
        $sciname = preg_replace('/ /', '_', $results['Sci_Name']);
        $sci_name = $results['Sci_Name'];
        $time = $results['Time'];
        $confidence = $results['Confidence'];
        echo "<tr>
          <td>$date</td>
          <td><a href=\"$comlink\" target=\"footer\"/>$time</a></td>
          <td><a href=\"https://wikipedia.org/wiki/$sciname\" target=\"top\">$sci_name</a></td>
          <form action=\"/stats.php\" method=\"POST\"> 
          <td><button type=\"submit\" name=\"species\" value=\"$name\">$name</button>
          </form></td>
          <td>$confidence</td>
          </tr>";

      }echo "</table>";}?>
</div>
</div>
</body>
</html>
