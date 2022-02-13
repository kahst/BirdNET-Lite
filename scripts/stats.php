<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$mysqli = mysqli_connect();
$mysqli->select_db('birds');

if ($mysqli->connect_error) {
	die('Connect Error (' .
		$mysqli->connect_errno . ') '.
		$mysqli->connect_error);
}

// SQL query to select data from database
$sql = "SELECT COUNT(*) AS 'Total' FROM detections
	ORDER BY Date DESC, Time DESC";
$totalcount = $mysqli->query($sql);

$sql1 = "SELECT Com_Name, COUNT(*), MAX(Confidence)
	FROM detections
	GROUP BY Com_Name
	ORDER BY COUNT(*) DESC";
$stats = $mysqli->query($sql1);

$sql2 = "SELECT COUNT(*) AS 'Total' FROM detections 
	WHERE Date = CURDATE()";
$todayscount = $mysqli->query($sql2);

$sql3 = "SELECT COUNT(*) AS 'Total' FROM detections 
	WHERE Date = CURDATE() 
	AND Time >= DATE_SUB(NOW(),INTERVAL 1 HOUR)";
$lasthourcount = $mysqli->query($sql3);

$sql4 = "SELECT Com_Name, Date, Time, MAX(Confidence)
	FROM detections
	GROUP BY Com_Name
	ORDER BY MAX(Confidence) DESC";
$specieslist = $mysqli->query($sql4);
$speciescount = mysqli_num_rows($specieslist);

$sql5 = "SELECT Com_Name,COUNT(*) 
	AS 'Total'
	FROM detections 
	GROUP BY Com_Name
	ORDER BY Total DESC";
$speciestally = $mysqli->query($sql5);

$getspecies = "SELECT Com_Name from detections
  GROUP BY Com_Name";
$result = $mysqli->query($getspecies);

if(isset($_POST['species'])){
  $selection = $_POST['species'];
  $specificspecies = "SELECT Com_Name, Sci_Name, COUNT(*), MAX(Confidence) from detections
    WHERE Com_Name = \"$selection\"";
  $specificstats = $mysqli->query($specificspecies);}

$mysqli->close();
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
	width: calc(50% - 30px);
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
	width:50%;
	border: none;
	padding: 10px 10px;
	font-size: medium;
	cursor: pointer;
	text-align: center;
}

select {
  font-size:large;
  width: 60%;
}

select option {
  font-size:large;
}

form {
  margin-left:20px;
}

@media screen and (max-width: 800px) {
  select {
    width:100%;
  }
  h3 {
    margin-bottom:0px;
    text-align:center;
  }
  form {
    text-align:left;
    margin-left:0px;
  }
  .column {
    float: none;
    width: 100%;
  }
  input, label  {
    width:100%;
  {
}
  </style>
</head>
<body style="background-color: rgb(119, 196, 135);">

  <section>
<div class="row">
 <div class="column first">
    <h3>Number of Detections</h3>
    <table>
      <tr>
	<th>Total</th>
	<th>Today</th>
	<th>Last Hour</th>
	<th>Number of Unique Species</th>
      </tr>
      <tr>
	<td><?php while ($row = $totalcount->fetch_assoc()) { echo $row['Total']; };?></td>
	<td><?php while ($row = $todayscount->fetch_assoc()) { echo $row['Total']; };?></td>
	<td><?php while ($row = $lasthourcount->fetch_assoc()) { echo $row['Total']; };?></td>
	<td><?php echo $speciescount;?></td>
      </tr>
    </table>
    <h3>Summary</h3>
    <table>
      <tr>
	<th>Common Name</th>
	<th>Occurrences</th>
	<th>Max Confidence Score</th>
      </tr>
<?php // LOOP TILL END OF DATA
while($rows=$stats ->fetch_assoc())
{
	$MAX = sprintf("%.1f%%", $rows['MAX(Confidence)'] * 100)
?>
      <tr>
	<td><a href="../By_Common_Name/<?php echo preg_replace('/ /', '_', $rows['Com_Name']);?>"><?php echo $rows['Com_Name'];?></a></td>
	<td><?php echo $rows['COUNT(*)'];?></td>
	<td><?php echo $MAX;?></td>

      </tr>
<?php
}
?>
    </table>
  </div>  
 <div class="column">
<form action="stats.php" method="POST">
  <h3>Species Stats</h3>
    <select name="species" >
    <option value="<?php if(isset($_POST['species'])){echo $selection;}?>"><?php if(isset($_POST['species'])){echo $selection;}else{echo "--Choose Species--";}?></option>
      <?php
        while($row = $result->fetch_assoc()) {
      ?>
      <option value='<?php echo $row['Com_Name'];?>'><?php echo $row['Com_Name'];?></option>"
<?php
}
?>
    </select>
  </p>
  <button type="submit" class="block"/>Show Species Statistics</button>
</form>
<?php if(isset($_POST['species'])){
  echo "<h3>";echo $_POST['species'];echo "</h3>
    <table>
      <tr>
	<th>Common Name</th>
	<th>Scientific Name</th>
	<th>Occurrences</th>
	<th>Highest Confidence Score</th>
	<th>Wikipedia Link</th>
      </tr>";
while($rows = $specificstats->fetch_assoc()) {
  $dbname = preg_replace('/ /', '_', $rows['Com_Name']);
  $dbsciname = preg_replace('/ /', '_', $rows['Sci_Name']);
  echo "<tr>
  <td>";echo "<a href=\"../By_Common_Name/$dbname\"/>";echo $rows['Com_Name']; echo "</a></td>
  <td>";echo "<a href=\"../By_Scientific_Name/$dbsciname\"/>";echo $rows['Sci_Name']; echo "</a></td>
  <td>";echo $rows['COUNT(*)'];echo "</td>
  <td>";echo $rows['MAX(Confidence)'];echo "</td>
  <td><a href=\"https://wikipedia.org/wiki/$dbname\" target=\"top\"/>link</a></td>
  </tr>";
}}
?>
    </table>
  </div>  
</div>
</div>
  </section>
</html>

