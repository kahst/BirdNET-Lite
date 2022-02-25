<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
</style>

<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

$filename = 'scripts/labels.txt';
$eachlines = file($filename, FILE_IGNORE_NEW_LINES);

?>

<body>
      <h2>All Species Labels</h2>
      <form action="" method="POST" id="add">
	<select name="species[]" id="species" multiple size="30">
	<option selected value="base">Please Select</option>
<?php   
foreach($eachlines as $lines){
	echo "<option value='".$lines."'>$lines</option>";
}?>
        </select>
      <input type="hidden" name="add" value="add">
      </form>

      <button type="submit" name="view" value="Included" form="add">Add to list</button><br>
      <button type="submit" name="view" value="Included" form="del">Remove from list</button>

        <h2>Custom Species List</h2>
        <p>Warning!<br>If this list contains ANY species, the system will ONLY recognize those species. Keep this list EMPTY unless you are ONLY interested in detecting specific species.<br>You have been warned!</p>
        <form action="" method="POST" id="del">
	<select name="species[]" id="value2" multiple size="30">
	<option selected value="base">Please Select</option>
<?php
	$filename = '/home/pi/BirdNET-Pi/include_species_list.txt';
$eachlines = file($filename, FILE_IGNORE_NEW_LINES);
foreach($eachlines as $lines){
	echo "<option value='".$lines."'>$lines</option>";
}?>
      <input type="hidden" name="del" value="del">
      </form>
</body>
