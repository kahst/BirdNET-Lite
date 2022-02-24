<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(isset($_POST['date'])){
$theDate = $_POST['date'];
} else {
$theDate = date('Y-m-d');
}
$chart = "Combo-$theDate.png";
$chart2 = "Combo2-$theDate.png";

$db = new SQLite3('/home/pi/BirdNET-Pi/scripts/birds.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

$statement1 = $db->prepare("SELECT COUNT(*) FROM detections
	WHERE Date == \"$theDate\"");
$result1 = $statement1->execute();
$totalcount = $result1->fetchArray(SQLITE3_ASSOC);

?>

<head>

<style>
</style>
</head>
<body>
<form action="" method="POST">
  <input type="date" name="date" value="<?php echo $theDate;?>">
  <button type="submit" name="view" value="History">Submit Date</button>
</form>
<div>
		<table>
			<tr>
				<th>Total Detections For The Day</th>
				<td><?php echo $totalcount['COUNT(*)'];?></td>
			</tr>
		</table>
</div>

<?php
if (file_exists('/home/pi/BirdSongs/Extracted/Charts/'.$chart)) {
  echo "<img src=\"/Charts/$chart?nocache=time()\" >";
} else {
  echo "<p>No Charts for $theDate</p>";
}
echo "<hr>";
if (file_exists('/home/pi/BirdSongs/Extracted/Charts/'.$chart2)) {
  echo "<img src=\"/Charts/$chart2?nocache=time()\">";
} else {
  echo "<p>No Charts For $theDate</p>";
}?>
</html>
