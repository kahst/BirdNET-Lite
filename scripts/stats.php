<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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


img {
	width:75%;
}

.center {
  display: block;
  margin-left: auto;
  margin-right: auto;
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
    <h3>Number of Detections</h3>
    <table>
      <tr>
	<th>Total</th>
	<th>Today</th>
	<th>Last Hour</th>
	<th>Number of Unique Species</th>
      </tr>
      <tr>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
      </tr>
    </table>
    <h3>Summary</h3>
    <table>
      <tr>
	<th>Common Name</th>
	<th>Occurrences</th>
	<th>Max Confidence Score</th>
      </tr>
      <tr>
	<td><a href="../By_Common_Name/"></a></td>
	<td></td>
	<td></td>

      </tr>
    </table>
  </div>  
 <div class="column">
<form action="stats.php" method="POST">
  <h3>Species Stats</h3>
    <select name="species" >
    <option value=""></option>
      <option value=""></option>"
    </select>
  </p>
  <button type="submit" class="block"/>Show Species Statistics</button>
</form>
<?php if(isset($_POST['species'])){
  $species = $_POST['species'];
  $str = "<h3>$species</h3>
    <table>
      <tr>
	<th>Common Name</th>
	<th>Scientific Name</th>
	<th>Occurrences</th>
	<th>Highest Confidence Score</th>
	<th>Links</th>
      </tr>";
  echo str_pad($str, 4096);
  ob_flush();
  flush();
   
while($rows = $specificstats->fetch_assoc()) {
  $count = $rows['COUNT(*)'];
  $maxconf = $rows['MAX(Confidence)'];
  $name = $rows['Com_Name'];
  $sciname = $rows['Sci_Name'];
  $dbname = preg_replace('/ /', '_', $rows['Com_Name']);
  $dbname = preg_replace('/\'/', '', $dbname);
  $dbsciname = preg_replace('/ /', '_', $rows['Sci_Name']);
  $imagelink = shell_exec("/home/pi/BirdNET-Pi/scripts/get_image.sh $dbsciname");
  $imagecitation = shell_exec("/home/pi/BirdNET-Pi/scripts/get_citation.sh $dbsciname");
  $str= "<tr>
  <td><a href=\"../By_Common_Name/$dbname\"/>$name</a></td>
  <td><a href=\"../By_Scientific_Name/$dbsciname\"/>$sciname</a></td>
  <td>$count</td>
  <td>$maxconf</td>
  <td><a href=\"https://wikipedia.org/wiki/$dbsciname\" target=\"top\"/>Wikipedia</a>, <a href=\"https://allaboutbirds.org/guide/$dbname\" target=\"top\"/>All About Birds</a>
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

