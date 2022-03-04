<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(isset($_POST['submit'])){
  $command = $_POST['submit'];
  if($command == 'update_birdnet.sh'){
    $str= "<h3>Updating . . . </h3>
      <p>Please wait 60 seconds</p>";
    echo str_pad($str, 4096);
    ob_flush();
    flush();
  }
  if(isset($command)){
    $results = shell_exec("$command 2>&1");
    echo "</div>
      </div>
      <pre>$results</pre>";
  }
}
ob_end_flush();
?>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="../style.css">
<style>
</style>
<form action="" method="POST" id="reboot">
<input type="hidden" name="submit" value="sudo reboot" form="reboot">
<button type="submit" class="block" onclick="return confirm('Are you sure you want to reboot?')" form="reboot">Reboot</button>
<form action="" method="POST" onclick="return confirm('BE SURE TO STASH ANY LOCAL CHANGES YOU HAVE MADE TO THE SYSTEM BEFORE UPDATING!!!')">
<input type="hidden" name="submit" value="update_birdnet.sh">
<button style="color:blue;" type="submit" class="block">Update</button>
</form>
<form action="" method="POST" onclick="return confirm('Are you sure you want to shutdown?')">
<input type="hidden" name="submit" value="sudo shutdown now">
<button style="color: red;" type="submit" class="block">Shutdown</button>
</form>
<form action="" method="POST" onclick="return confirm('Clear ALL Data? This cannot be undone.')">
<input type="hidden" name="submit" value="clear_all_data.sh">
<button style="color: red;" type="submit" class="block">Clear ALL data</button>
</form>	
