<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = new SQLite3('/home/pi/BirdNET-Pi/scripts/birds.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
if($db == False){
  echo "Database is busy";
  header("refresh: 0;");
}

$statement = $db->prepare('SELECT DISTINCT(Com_Name) from detections ORDER BY Com_Name');
if($statement == False){
  echo "Database is busy";
  header("refresh: 0;");
}
$result = $statement->execute();

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
<?php
while($results=$result->fetchArray(SQLITE3_ASSOC))
{
	$comname = preg_replace('/ /', '_', $results['Com_Name']);
	$comlink = "/By_Date/".date('Y-m-d')."/".$comname;
?>
<table>
  <tr>
    <form action="" method="POST">
    <td>
      <button action="submit" name="species" value="<?php echo $results['Com_Name'];?>"><?php echo $results['Com_Name'];?></button>
    </td>
    </form>
  </tr>
</table>
<?php
}?>
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
