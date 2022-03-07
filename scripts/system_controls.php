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
<div class="systemcontrols">
  <form action="" method="POST">
    <button type="submit" name="submit" value="sudo reboot" onclick="return confirm('Are you sure you want to reboot?')">Reboot</button>
  </form>
  <form action="" method="POST">
    <button type="submit" name="submit" value="update_birdnet.sh" onclick="return confirm('BE SURE TO STASH ANY LOCAL CHANGES YOU HAVE MADE TO THE SYSTEM BEFORE UPDATING!!!')" >Update</button>
  </form>
  <form action="" method="POST">
    <button type="submit" name="submit" value="sudo shutdown now" onclick="return confirm('Are you sure you want to shutdown?')">Shutdown</button>
  </form>
  <form action="" method="POST">
    <button type="submit" name="submit" value="clear_all_data.sh" onclick="return confirm('Clear ALL Data? This cannot be undone.')">Clear ALL data</button>
  </form>	
  <form action="" method="POST">
    <button type="submit" name="submit" value="">Test</button>
  </form>
</div>
