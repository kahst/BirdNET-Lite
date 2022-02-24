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
  $statement3 = $db->prepare("SELECT Date, Time, Com_Name, MAX(Confidence), File_Name FROM detections WHERE Com_Name == \"$species\" ORDER BY Com_Name");
  if($statement == False || $statement3 == False){
    echo "Database is busy";
    header("refresh: 0;");
  }
  $result = $statement->execute();
  $result3 = $statement3->execute();
  $view = "species";
} else {
  $statement = $db->prepare('SELECT DISTINCT(Com_Name) FROM detections ORDER BY Com_Name');
  if($statement == False){
    echo "Database is busy";
    header("refresh: 0;");
  }
  $result = $statement->execute();
  $view = "byspecies";
}
?>

<html>
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
    </style>
  </head>
<body>
<table>
<?php
if(!isset($_POST['species'])){
while($results=$result->fetchArray(SQLITE3_ASSOC))
{
?>
  <tr>
    <form action="" method="POST">
    <td>
    <input type="hidden" name="view" value="Extractions">
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
    $name = $results['Com_Name'];
    $comname = preg_replace('/ /', '_', $name);
    $comname = preg_replace('/\'/', '', $comname);
    $file = $results['File_Name'];
    $filename = "/By_Date/".$date."/".$comname."/".$results['File_Name'];
    echo "<table>
      <tr>
      <th>$name</th>
      <th>Max Confidence: $maxconf</th>
      </tr>
      <tr>
      <th>Most confident recording: </th>
      <td><audio controls><source src=\"$filename\"></audio></td>
      </tr></table>";
    };};?>
    </td>
    </form>
  </tr>
</table>
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
      <th>When</th>
      <th>Listen</th>
      <th>Scientific Name</th>
      <th>Common Name</th>
      <th>Confidence</th>
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
          <td>$date $time</td>
          <td><audio controls><source src=\"$filename\"></audio></td>
          <td><a href=\"https://wikipedia.org/wiki/$sciname\" target=\"top\">$sci_name</a></td>
          <form action=\"\" method=\"POST\"> 
          <td><input type=\"hidden\" name=\"view\" value=\"Species Stats\"><button type=\"submit\" name=\"species\" value=\"$name\">$name</button>
          </form></td>
          <td>$confidence</td>
          </tr>";

      }echo "</table>";}?>
</body>
</html>
