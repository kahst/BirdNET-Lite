<?php 
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$startdate = strtotime('last sunday') - (7*86400);
$enddate = strtotime('last sunday') - (1*86400);

$debug = false;

if(isset($_GET['ascii'])) {

	$db = new SQLite3('./scripts/birds.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
	if($db == False){
	  echo "Database is busy";
	  header("refresh: 0;");
	}

	$statement1 = $db->prepare('SELECT DISTINCT(Com_Name), COUNT(*) FROM detections WHERE Date BETWEEN "'.date("Y-m-d",$startdate).'" AND "'.date("Y-m-d",$enddate).'" GROUP By Com_Name ORDER BY COUNT(*) DESC');
	if($statement1 == False){
	  echo "Database is busy";
	  header("refresh: 0;");
	}
	$result1 = $statement1->execute();

	$detections = [];
	$i = 0;
	while($detection=$result1->fetchArray(SQLITE3_ASSOC))
	{
		array_push($detections, $detection);
	}

	echo "# Week ".date('W', $enddate)." Report (".date('F jS, Y',$startdate)." — ".date('F jS, Y',$enddate).")\n";

	echo "= Top 10 Species =\n";

	$i = 0;
	foreach($detections as $detection)
	{
		$i++;

		if($i <= 10) {
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
				$percentagediff = "+".$percentagediff."%";
			} else {
				$percentagediff = "-".abs($percentagediff)."%";
			}

			echo $detection["Com_Name"]." - ".$detection["COUNT(*)"]." (".$percentagediff.")\n";
		}
	}

	die();
}

?>
<div class="brbanner"> <?php
echo "<h1>Week ".date('W', $enddate)." Report</h1>".date('F jS, Y',$startdate)." — ".date('F jS, Y',$enddate)."<br>";
?></div><?php

$db = new SQLite3('./scripts/birds.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
if($db == False){
  echo "Database is busy";
  header("refresh: 0;");
}

if($debug == false){
$statement1 = $db->prepare('SELECT DISTINCT(Com_Name), COUNT(*) FROM detections WHERE Date BETWEEN "'.date("Y-m-d",$startdate).'" AND "'.date("Y-m-d",$enddate).'" GROUP By Com_Name ORDER BY COUNT(*) DESC');
} else {
	$statement1 = $db->prepare('SELECT DISTINCT(Com_Name), COUNT(*) FROM detections WHERE Date BETWEEN "'.date("Y-m-d",$startdate).'" AND "'.date("Y-m-d",$enddate).'" GROUP By Com_Name ORDER BY COUNT(*) ASC');
}
if($statement1 == False){
  echo "Database is busy";
  header("refresh: 0;");
}
$result1 = $statement1->execute();

$detections = [];
$i = 0;
while($detection=$result1->fetchArray(SQLITE3_ASSOC))
{
	if($debug == true){
		if($i > 10) { 
			break;
		}
	}
	$i++;
	array_push($detections, $detection);
	
}
?>
<br>
<?php // TODO: fix the box shadows, maybe make them a bit smaller on the tr ?>
<table align="center" style="box-shadow:unset"><tr><td style="background-color:#77c487">
	<table>
	<thead>
		<tr>
			<th><?php echo "Top 10 Species: <br>"; ?></th>
		</tr>
	</thead>
	<tbody>
	<?php

	$i = 0;
	foreach($detections as $detection)
	{
		$i++;

		if($i <= 10) {
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
	}
	?>
	</tbody>
	</table>
	</td><td style="background-color:#77c487">

	<table >
	<thead>
		<tr>
			<th><?php echo "Species Seen for the First Time: <br>"; ?></th>
		</tr>
	</thead>
	<tbody>
	<?php 
	function searchForId($id, $array) {
	   foreach ($array as $key => $val) {
	       if ($val['Com_Name'] === $id) {
	           return $key;
	       }
	   }
	   return null;
	}

	foreach($detections as $detection)
	{
		$statement3 = $db->prepare('SELECT COUNT(*) FROM detections WHERE Com_Name == "'.$detection["Com_Name"].'" AND Date NOT BETWEEN "'.date("Y-m-d",$startdate).'" AND "'.date("Y-m-d",$enddate).'"');
		if($statement3 == False){
		  echo "Database is busy";
		  header("refresh: 0;");
		}
		$result3 = $statement3->execute();
		$totalcount = $result3->fetchArray(SQLITE3_ASSOC);
		$nonthisweekcount = $totalcount['COUNT(*)'];

	    // TODO: add a "no species were seen for the first time this week", if applicable

		if($nonthisweekcount == 0) {
			$key = array_search($detection["Com_Name"], array_column($detections, 'Com_Name'));
			echo "<tr><td>".$detection["Com_Name"]."<br><small style=\"font-size:small\">".$detections[$key]["COUNT(*)"]."</small><br></td></tr>";
		}
	}
	?>
	</tbody>
	</table>
	</td></tr></table>


<br>
<div style="text-align:center">
	<small style="font-size:small">* percentages are calculated relative to week <?php echo date('W', $enddate) - 1; ?></small>
</div>