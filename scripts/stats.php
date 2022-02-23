<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$db = new SQLite3('/home/pi/BirdNET-Pi/scripts/birds.db', SQLITE3_OPEN_CREATE | SQLITE3_OPEN_READWRITE);
if($db == False) {
	echo "Database busy";
	header("refresh: 0;");
}
$statement = $db->prepare('SELECT Com_Name, COUNT(*), MAX(Confidence), Sci_Name FROM detections GROUP BY Com_Name ORDER BY COUNT(*) DESC');
if($statement == False) {
	echo "Database busy";
	header("refresh: 0;");
}
$result = $statement->execute();


$statement2 = $db->prepare('SELECT Com_Name FROM detections GROUP BY Com_Name ORDER BY Com_Name ASC');
if($statement2 == False) {
	echo "Database busy";
	header("refresh: 0;");
}
$result2 = $statement2->execute();

if(isset($_POST['species'])){
  $selection = $_POST['species'];
  $statement3 = $db->prepare("SELECT Com_Name, Sci_Name, COUNT(*), MAX(Confidence) from detections
    WHERE Com_Name = \"$selection\"");
  if($statement3 == False) {
  	echo "Database busy";
  	header("refresh: 0;");
  }
  $result3 = $statement3->execute();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>BirdNET-Pi DB</title>
  <link rel="stylesheet" href="style.css">
<style>
/* Chrome, Safari, Edge, Opera */
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Firefox */
input[type=number] {
  -moz-appearance: textfield;
}
* {
  font-family: 'Arial', 'Gill Sans', 'Gill Sans MT',
  ' Calibri', 'Trebuchet MS', 'sans-serif';
	box-sizing: border-box;
}
/* Create two unequal columns that floats next to each other */
.column {
	float: left;
  padding: 10px;
}
.first {
	width: calc(50% - 70px);
}
.second {
	width: calc(50% + 70px);
}
.
/* Clear floats after the columns */
.row:after {
	content: "";
	display: table;
	clear: both;
}
body {
	background-color: rgb(119, 196, 135);
}
a {
        color:black;
	text-decoration: none;
}
.block {
	display: block;
	width:40%;
	margin-left:auto;
	border: none;
	padding: 5px 5px;
	font-size: medium;
	cursor: pointer;
	text-align: center;
}

button {
  background-color: rgb(219, 295, 235);
  border:none;
  font-size:large;
  cursor:pointer;
}

img {
	width:75%;
}

.center {
  display: block;
  margin-left: auto;
  margin-right: auto;
}

select option {
  font-size:large;
}

select {
  float:left;
  display:block;
  margin-right:auto;
  font-size:large;
  width: 50%;
}

@media screen and (max-width: 800px) {
  h3 {
    margin-bottom:0px;
    text-align:center;
  }
  form {
    text-align:left;
    margin-left:0px;
    width:100%;
  }
  .column {
    float: none;
    width: 100%;
  }
  input, label, img  {
    width:100%;
  {
}
  </style>
</head>
<body style="background-color: rgb(119, 196, 135);">

  <section>
<div class="row">
 <div class="column first">
<?php if(!isset($_POST['species'])){
    echo "<p style=\"text-align:center;font-size:large;\">Choose a species below to show statistics.</p>";
};?>
    <table>
      <tr>
	<th>Common Name</th>
	<th>Occurrences</th>
	<th>Max Confidence Score</th>
      </tr>
<?php
while($results=$result->fetchArray(SQLITE3_ASSOC))
{
$comname = preg_replace('/ /', '_', $results['Com_Name']);
$comlink = "/By_Date/".date('Y-m-d')."/".$comname;
$sciname = preg_replace('/ /', '_', $results['Sci_Name']);
?>
      <tr>
      <form action="stats.php" method="POST">
      <td><button type="submit" name="species" value="<?php echo $results['Com_Name'];?>"><?php echo $results['Com_Name'];?></button></td></form>
      <td><?php echo $results['COUNT(*)'];?></td>
      <td><?php echo $results['MAX(Confidence)'];?></td>
      </tr>
<?php
}
?>
    </table>
  </div>  
<?php if(isset($_POST['species'])){
  $species = $_POST['species'];
  $str = "<div class=\"column second\">
   <h3>$species</h3>
   <h3>Species Stats</h3>
    <table>
      <tr>
	<th>Scientific Name</th>
	<th>Occurrences</th>
	<th>Highest Confidence Score</th>
	<th>Links</th>
      </tr>";
  echo str_pad($str, 4096);
  ob_flush();
  flush();
   
while($results=$result3->fetchArray(SQLITE3_ASSOC)){
  $count = $results['COUNT(*)'];
  $maxconf = $results['MAX(Confidence)'];
  $name = $results['Com_Name'];
  $sciname = $results['Sci_Name'];
  $dbname = preg_replace('/ /', '_', $results['Com_Name']);
  $dbname = preg_replace('/\'/', '', $dbname);
  $dbsciname = preg_replace('/ /', '_', $results['Sci_Name']);
  $imagelink = shell_exec("/home/pi/BirdNET-Pi/scripts/get_image.sh $dbsciname");
  $imagecitation = shell_exec("/home/pi/BirdNET-Pi/scripts/get_citation.sh $dbsciname");
  $str= "<tr>
  <td><a href=\"https://wikipedia.org/wiki/$dbsciname\" target=\"top\"/>$sciname</a></td>
  <td>$count</td>
  <td>$maxconf</td>
  <td><a href=\"https://allaboutbirds.org/guide/$dbname\" target=\"top\"/>All About Birds</a>
  </tr>
    </table>";
  echo str_pad($str, 4096);
  ob_flush();
  flush();
  echo "<img class=\"center\" src=\"$imagelink\">
  <pre>$imagecitation</pre></td>
  </div>  
</div>
</div>";
}}
?>

  </section>
</html>

