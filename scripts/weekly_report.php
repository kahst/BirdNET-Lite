<?php
if(file_exists('./scripts/common.php')){
	include_once "./scripts/common.php";
}else{
	include_once "./common.php";
}

$startdate = strtotime('last sunday') - (7*86400);
$enddate = strtotime('last sunday') - (1*86400);

$debug = false;

if(isset($_GET['ascii'])) {
	$weekly_species_counts = getWeeklyReportSpeciesDetectionCounts();

	if($weekly_species_counts['detections']['success'] == False){
		echo $weekly_species_counts['detections']['message'];
		header("refresh: 0;");
	}
	$result1 = $weekly_species_counts['detections']['data'];

	if($weekly_species_counts['totalcount']['success'] == False){
		echo $weekly_species_counts['totalcount']['message'];
		header("refresh: 0;");
	}
	$totalcount = $weekly_species_counts['totalcount']['data']['COUNT(*)'];

	if($weekly_species_counts['priortotalcount']['success'] == False){
		echo $weekly_species_counts['priortotalcount']['message'];
		header("refresh: 0;");
	}
	$priortotalcount = $weekly_species_counts['priortotalcount']['data']['COUNT(*)'];

    $weekly_species_talley = getWeeklyReportSpeciesTalley();
	if($weekly_species_talley['totalspeciestally']['success'] == False){
		echo $weekly_species_talley['totalspeciestally']['message'];
		header("refresh: 0;");
	}
	$totalspeciestally = $weekly_species_talley['totalspeciestally']['data']['COUNT(DISTINCT(Com_Name))'];

	if($weekly_species_talley['priortotalspeciestally']['success'] == False){
		echo $weekly_species_talley['priortotalspeciestally']['message'];
		header("refresh: 0;");
	}
	$priortotalspeciestally = $weekly_species_talley['priortotalspeciestally']['data']['COUNT(DISTINCT(Com_Name))'];

	$percentagedifftotal = round( (($totalcount - $priortotalcount) / $priortotalcount) * 100  );

	if($percentagedifftotal > 0) {
		$percentagedifftotal = "<span style='color:green;font-size:small'>+".$percentagedifftotal."%</span>";
	} else {
		$percentagedifftotal = "<span style='color:red;font-size:small'>-".abs($percentagedifftotal)."%</span>";
	}

	$percentagedifftotaldistinctspecies = round( (($totalspeciestally - $priortotalspeciestally) / $priortotalspeciestally) * 100  );
	if($percentagedifftotaldistinctspecies > 0) {
		$percentagedifftotaldistinctspecies = "<span style='color:green;font-size:small'>+".$percentagedifftotaldistinctspecies."%</span>";
	} else {
		$percentagedifftotaldistinctspecies = "<span style='color:red;font-size:small'>-".abs($percentagedifftotaldistinctspecies)."%</span>";
	}

	$detections = [];
	$i = 0;
    foreach ($result1 as $detection)
	{
		$detections[$detection["Com_Name"]] = $detection["COUNT(*)"];
	}
	echo "# BirdNET-Pi: Week ".date('W', $enddate)." Report\n";

	echo "Total Detections: <b>".$totalcount."</b> (".$percentagedifftotal.")<br>";
	echo "Unique Species Detected: <b>".$totalspeciestally."</b> (".$percentagedifftotaldistinctspecies.")<br><br>";

	echo "= <b>Top 10 Species</b> =<br>";

	$i = 0;
	foreach($detections as $com_name=>$scount)
	{
		$i++;

		if($i <= 10) {
            $statement2 = getWeeklyReportSpeciesDetection($com_name);
			if($statement2['success'] == False){
				echo $statement2['message'];
				header("refresh: 0;");
			}
			$priorweekcount = $statement2['data']['COUNT(*)'];

      // really percent changed
			if($priorweekcount > 0){
                                $percentagediff = round( (($scount - $priorweekcount) / $priorweekcount) * 100  );

                                if($percentagediff > 0) {
                                        $percentagediff = "<span style='color:green;font-size:small'>+".$percentagediff."%</span>";
                                } else {
                                        $percentagediff = "<span style='color:red;font-size:small'>-".abs($percentagediff)."%</span>";
                                }

                                echo $com_name." - ".$scount." (".$percentagediff.")<br>";
                        } else {
                                echo $com_name." - ".$scount ."<br>";
                        }
		}
	}

	echo "<br>= <b>Species Detected for the First Time</b> =<br>";

    $newspeciescount=0;
	foreach($detections as $com_name=>$scount)
	{
		$statement3 = getWeeklyReportSpeciesDetection($com_name,false);
		if($statement3['success'] == False){
			echo $statement3['message'];
			header("refresh: 0;");
		}
		$nonthisweekcount = $statement3['data']['COUNT(*)'];

		if($nonthisweekcount == 0) {
			$newspeciescount++;
			echo $com_name." - ".$scount."<br>";
		}
	}
	if($newspeciescount == 0) {
		echo "No new species were seen this week.";
	}

        $prevweek = date('W', $enddate) - 1;
        if($prevweek < 1) { $prevweek = 52; } 

	echo "<hr><small>* data from ".date('Y-m-d', $startdate)." — ".date('Y-m-d',$enddate).".</small><br>";
	echo '<small>* percentages are calculated relative to week '.($prevweek).'.</small>';

	die();
}

?>
<div class="brbanner"> <?php
echo "<h1>Week ".date('W', $enddate)." Report</h1>".date('F jS, Y',$startdate)." — ".date('F jS, Y',$enddate)."<br>";
?></div><?php

if($debug == false){
	$weekly_species_counts = getWeeklyReportSpeciesDetectionCounts();

	if($weekly_species_counts['detections']['success'] == False){
		echo $weekly_species_counts['detections']['message'];
		header("refresh: 0;");
	}
	$result1 = $weekly_species_counts['detections']['data'];
} else {
	$weekly_species_counts = getWeeklyReportSpeciesDetectionCounts(false);

	if($weekly_species_counts['detections']['success'] == False){
		echo $weekly_species_counts['detections']['message'];
		header("refresh: 0;");
	}
	$result1 = $weekly_species_counts['detections']['data'];
}

$detections = [];
$i = 0;
foreach ($result1 as $detection)
{
	if($debug == true){
		if($i > 10) { 
			break;
		}
	}
	$i++;
	$detections[$detection["Com_Name"]] = $detection["COUNT(*)"];
	
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
	foreach($detections as $com_name=>$scount)
	{
		$i++;
		if($i <= 10) {
			$statement2 = getWeeklyReportSpeciesDetection($com_name);
			if($statement2['success'] == False){
				echo $statement2['message'];
				header("refresh: 0;");
			}
			$priorweekcount = $statement2['data']['COUNT(*)'];

			if ($priorweekcount > 0) {
				$percentagediff = round( (($scount - $priorweekcount) / $priorweekcount) * 100  );
			} else {
				$percentagediff = 0;
			}

			if($percentagediff > 0) {
				$percentagediff = "<span style='color:green;font-size:small'>+".$percentagediff."%</span>";
			} else {
				$percentagediff = "<span style='color:red;font-size:small'>-".abs($percentagediff)."%</span>";
			}

			echo "<tr><td>".$com_name."<br><small style=\"font-size:small\">".$scount." (".$percentagediff.")</small><br></td></tr>";
		}
	}
	?>
	</tbody>
	</table>
	</td><td style="background-color:#77c487">

	<table >
	<thead>
		<tr>
			<th><?php echo "Species Detected for the First Time: <br>"; ?></th>
		</tr>
	</thead>
	<tbody>
	<?php 

    $newspeciescount=0;
	foreach($detections as $com_name=>$scount)
	{
		$statement3 = getWeeklyReportSpeciesDetection($com_name,false);
		if($statement3['success'] == False){
			echo $statement3['message'];
			header("refresh: 0;");
		}
		$nonthisweekcount = $statement3['data']['COUNT(*)'];

		if($nonthisweekcount == 0) {
			$newspeciescount++;
			echo "<tr><td>".$com_name."<br><small style=\"font-size:small\">".$scount."</small><br></td></tr>";
		}
	}
	if($newspeciescount == 0) {
		echo "<tr><td>No new species were seen this week.</td></tr>";
	}
	?>
	</tbody>
	</table>
	</td></tr></table>


<br>
<div style="text-align:center">
	<hr><small style="font-size:small">* percentages are calculated relative to week <?php echo date('W', $enddate) - 1; ?></small>
</div>
