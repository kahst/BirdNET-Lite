<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
</style>

<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

$filename = './scripts/labels.txt';
$eachlines = file($filename, FILE_IGNORE_NEW_LINES);

?>

<div class="customlabels smaller">
<br>
</div>
<body style="height: 90%;">
  <p>Warning!<br>If this list contains ANY species, the system will ONLY recognize those species. Keep this list EMPTY unless you are ONLY interested in detecting specific species.</p>
<div class="customlabels2 column1">
<form action="" method="GET" id="add">
  <h2>All Species Labels</h2>
  <select name="species[]" id="species" multiple size="30">
    <option selected value="base">Please Select</option>
      <?php   
        foreach($eachlines as $lines){echo 
    "<option value=\"".$lines."\">$lines</option>";}
       ?>
  </select>
  <input type="hidden" name="add" value="add">
</form>
<div class="customlabels2 smaller">
  <button type="submit" name="view" value="Included" form="add">>>ADD>></button>
</div>
</div>

<div class="customlabels2 column4">
  <table><td>
  <button type="submit" name="view" value="Included" form="add">>>ADD>></button>
  <br><br>
  <button type="submit" name="view" value="Included" form="del">REMOVE</button>
  </td></table>
</div>

<div class="customlabels2 column3">
<form action="" method="GET" id="del">
  <h2>Custom Species List</h2>
  <select name="species[]" id="value2" multiple size="30">
    <option selected value="base">Please Select</option>
      <?php
        $filename = './scripts/include_species_list.txt';
        $eachlines = file($filename, FILE_IGNORE_NEW_LINES);
        foreach($eachlines as $lines){echo 
    "<option value=\"".$lines."\">$lines</option>";}
      ?>
  </select>
  <input type="hidden" name="del" value="del">
</form>
<div class="customlabels2 smaller">
  <button type="submit" name="view" value="Included" form="del">REMOVE</button>
</div>
</div>
</body>
