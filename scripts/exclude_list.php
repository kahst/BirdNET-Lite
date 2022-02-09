<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
* {
  font-family: 'Arial', 'Gill Sans', 'Gill Sans MT',
  ' Calibri', 'Trebuchet MS', 'sans-serif';
  box-sizing: border-box;

	box-sizing: border-box;
}

/* Create two unequal columns that floats next to each other */
.column {
	float: left;
	padding: 10px;
}

.first {
	width: 45%;
}

.second {
	width: 10%;
        display: flex;
        justify-content: center;
        flex-direction: column;
        height: 100%;
}

.third {
	width: 45%;
}

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
	text-decoration: none;
	color: white;
}

.block {
	display: block;
	font-weight: bold;
	width:100%;
	border: none;
	background-color: #04AA6D;
	padding: 20px 20px;
	color: white;
	font-size: medium;
	cursor: pointer;
	text-align: center;
}
h2 {
  text-align: center;
}
select {
  width:100%
}
@media screen and (max-width: 800px) {
	.column {
		float: none;
		width: 100%;
	}
}
</style>

<?php
//remove <script></script> and add php start and close tag
//comment these two lines when code started working fine
error_reporting(E_ALL);
ini_set('display_errors',1);

$filename = 'labels.txt';
$eachlines = file($filename, FILE_IGNORE_NEW_LINES);

?>

<body style="background-color: rgb(119, 196, 135);">
  <div class="row">
    <div class="column first">
      <h2>All Species Labels</h2>
      <form action="add_to_exclude.php" method="POST" id="add">
	<select name="species[]" id="species" multiple size="30">
	<option selected value="base">Please Select</option>
<?php   
foreach($eachlines as $lines){
	echo "<option value='".$lines."'>$lines</option>";
}?>
        </select>
      </form>
      </div>

      <div class="column second">
      <button type="submit" form="add" class="block">Add to list</button><br>
      <button type="submit" form="del" class="block">Remove from list</button>
      </div>

      <div class="column third">
        <h2>Excluded Species List</h2>
        <form action="del_from_exclude.php" method="POST" id="del">
	<select name="species[]" id="value2" multiple size="30">
	<option selected value="base">Please Select</option>
<?php
	$filename = '/home/pi/BirdNET-Pi/exclude_species_list.txt';
$eachlines = file($filename, FILE_IGNORE_NEW_LINES);
foreach($eachlines as $lines){
	echo "<option value='".$lines."'>$lines</option>";
}?>
      </form>
    </div>
  </div>
</body>
