<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$startdate = strtotime('last sunday') - (7*86400);
$enddate = strtotime('last sunday') - (1*86400);
?>
<div class="brbanner"> <?php
echo "<h1>Week ".date('W', $enddate)." Report</h1>".date('F jS, Y',$startdate)." — ".date('F jS, Y',$enddate)."<br>";
?></div><?php

$db = new SQLite3('./scripts/birds.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
if($db == False){
  echo "Database is busy";
  header("refresh: 0;");
}


$statement1 = $db->prepare('SELECT DISTINCT(Com_Name), COUNT(*) FROM detections WHERE Date BETWEEN "'.date("Y-m-d",$startdate).'" AND "'.date("Y-m-d",$enddate).'" GROUP By Com_Name ORDER BY COUNT(*) DESC LIMIT 10');
if($statement1 == False){
  echo "Database is busy";
  header("refresh: 0;");
}
$result1 = $statement1->execute();

?>
<br>
<table>
<thead>
	<tr>
		<th><?php echo "Top 10 Species: <br>"; ?></th>
	</tr>
</thead>
<tbody>
<?php

while($detection=$result1->fetchArray(SQLITE3_ASSOC))
{

$statement2 = $db->prepare('SELECT COUNT(*) FROM detections WHERE Com_Name == "'.$detection["Com_Name"].'" AND Date BETWEEN "'.date("Y-m-d",$startdate - (7*86400)).'" AND "'.date("Y-m-d",$enddate - (7*86400)).'"');
if($statement2 == False){
  echo "Database is busy";
  header("refresh: 0;");
}
$result2 = $statement2->execute();
$totalcount = $result2->fetchArray(SQLITE3_ASSOC);
$priorweekcount = $totalcount['COUNT(*)'];

$percentagediff = round((1 - $priorweekcount / $detection["COUNT(*)"]) * 100);

if($percentagediff > 0) {
	$percentagediff = "<span style='color:green;font-size:small'>+".$percentagediff."%</span>";
} else {
	$percentagediff = "<span style='color:red;font-size:small'>-".abs($percentagediff)."%</span>";
}

echo "<tr><td>".$detection["Com_Name"]."<br><small style=\"font-size:small\">".$detection["COUNT(*)"]." (".$percentagediff.")</small><br></td></tr>";
}
?>
</tbody>
</table>

<br>
<div style="text-align:center">
	<small style="font-size:small">* percentages are calculated relative to week <?php echo date('W', $enddate) - 1; ?></small>
</div>