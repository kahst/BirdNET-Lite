<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(isset($_GET['date'])){
$theDate = $_GET['date'];
} else {
$theDate = date('Y-m-d');
}
$chart = "Combo-$theDate.png";
$chart2 = "Combo2-$theDate.png";

$db = new SQLite3('./scripts/birds.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);

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
<div class="history centered">
<form action="" method="GET">
  <input type="date" name="date" value="<?php echo $theDate;?>">
  <button type="submit" name="view" value="Daily Charts">Submit Date</button>
</form>
		<table>
			<tr>
				<th>Total Detections For The Day</th>
				<td><?php echo $totalcount['COUNT(*)'];?></td>
			</tr>
		</table>
		<br><br>
<?php
if (file_exists('./Charts/'.$chart)) {
  echo "<img src=\"/Charts/$chart?nocache=time()\" >";
} else {
  echo "<p>No Charts for $theDate</p>";
}
echo "<hr>";
if (file_exists('./Charts/'.$chart2)) {
  echo "<img src=\"/Charts/$chart2?nocache=time()\">";
} else {
  echo "<p>No Charts For $theDate</p>";
}?>
</div>
</html>
