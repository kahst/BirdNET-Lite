<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
</style>

<body>
<div class="customlabels column1">
<form action="" method="GET" id="add">
  <h3>All Species Labels</h3>
  <select name="species[]" id="species" multiple size="auto">
  <option>Choose a species below to add to the Excluded Species List</option>
  <?php
    error_reporting(E_ALL);
    ini_set('display_errors',1);
    
    $filename = './scripts/labels.txt';
    $eachline = file($filename, FILE_IGNORE_NEW_LINES);
    
    foreach($eachline as $lines){echo 
  "<option value=\"".$lines."\">$lines</option>";}
  ?>
  </select>
  <input type="hidden" name="add" value="add">
</form>
<div class="customlabels smaller">
  <button type="submit" name="view" value="Excluded" form="add">>>ADD>></button>
</div>
</div>

<div class="customlabels column2">
  <table><td>
  <button type="submit" name="view" value="Excluded" form="add">>>ADD>></button>
  <br><br>
  <button type="submit" name="view" value="Excluded" form="del">REMOVE</button>
  </td></table>
</div>

<div class="customlabels column3">
<form action="" method="GET" id="del">
  <h3>Excluded Species List</h3>
  <select name="species[]" id="value2" multiple size="auto">
<?php
  $filename = './scripts/exclude_species_list.txt';
  $eachline = file($filename, FILE_IGNORE_NEW_LINES);
  foreach($eachline as $lines){
    echo 
  "<option value=\"".$lines."\">$lines</option>";
}?>
  </select>
  <input type="hidden" name="del" value="del">
</form>
<div class="customlabels smaller">
  <button type="submit" name="view" value="Excluded" form="del">REMOVE</button>
</div>
</div>


</body>
