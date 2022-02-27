<link rel="stylesheet" href="style.css">
<meta name="viewport" content="width=device-width, initial-scale=1">
<div class="topbar">
<div class="topnav" id="myTopnav">
<form action="" method="POST" id="views">
  <button type="submit" name="view" value="Overview" id="views">Overview</button>
  <button type="submit" name="view" value="Today's Detections" id="views">Today's Detections</button>
  <button type="submit" name="view" value="Species Stats" id="views">Species Stats</button>
  <button type="submit" name="view" value="Daily Charts" id="views">Daily Charts</button>
  <button type="submit" name="view" value="Tools" id="views">Tools</button>
  <button type="submit" name="view" value="Recordings" id="views">Recordings</button>
</form>
<form action="index.php" method="GET" id="log">
  <button type="submit" name="log" value="log" id="Log">View Log</button>
</form>
<form action="index.php" method="GET" id="spectrogram">
  <button style="float:none;"type="submit" name="spectrogram" value="view" id="spectrogram">Spectrogram</button>
</form>
<button href="javascript:void(0);" class="icon" onclick="myFunction()"><img src="images/menu.png"></button>
</div>
</div>
</body>
<?php
if(isset($_POST['view'])){
  if($_POST['view'] == "System"){header('location:phpsysinfo/index.php');}
  if($_POST['view'] == "Spectrogram"){include('spectrogram.php');}
  if($_POST['view'] == "Overview"){include('overview.php');}
  if($_POST['view'] == "Today's Detections"){include('viewdb.php');}
  if($_POST['view'] == "Species Stats"){echo "<br><br>";include('stats.php');}
  if($_POST['view'] == "Daily Charts"){include('history.php');}
  if($_POST['view'] == "Tools"){
    if (file_exists('/home/pi/BirdNET-Pi/thisrun.txt')) {
      $config = parse_ini_file('/home/pi/BirdNET-Pi/thisrun.txt');
    } elseif (file_exists('/home/pi/BirdNET-Pi/firstrun.ini')) {
      $config = parse_ini_file('/home/pi/BirdNET-Pi/firstrun.ini');
    }
    $caddypwd = $config['CADDY_PWD'];
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
      header('WWW-Authenticate: Basic realm="My Realm"');
      header('HTTP/1.0 401 Unauthorized');
      echo 'You cannot edit the settings for this installation';
      exit;
    } else {
      $submittedpwd = $_SERVER['PHP_AUTH_PW'];
      $submitteduser = $_SERVER['PHP_AUTH_USER'];
      if($submittedpwd == $caddypwd && $submitteduser == 'birdnet'){
        echo "<form action=\"\" method=\"POST\">
        <button type=\"submit\" name=\"view\" value=\"Settings\">Settings</button>
        <button type=\"submit\" name=\"view\" value=\"System\">System Info</button>
        <button type=\"submit\" name=\"view\" value=\"Included\">Custom Species List</button>
        <button type=\"submit\" name=\"view\" value=\"Excluded\">Excluded Species List</button>
        </form>";
      } else {
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'You cannot edit the settings for this installation';
        exit;
      }
    }
  }
  if($_POST['view'] == "Recordings"){include('play.php');}
  if($_POST['view'] == "Settings"){include('scripts/config.php');} 
  if($_POST['view'] == "Advanced"){include('scripts/advanced.php');}
  if($_POST['view'] == "Included"){
    if(isset($_POST['species']) && isset($_POST['add'])){
      $file = '/home/pi/BirdNET-Pi/include_species_list.txt';
      $str = file_get_contents("$file");
      $str = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $str);
      file_put_contents("$file", "$str");
      if(isset($_POST['species'])){
        foreach ($_POST['species'] as $selectedOption)
          file_put_contents("/home/pi/BirdNET-Pi/include_species_list.txt", $selectedOption."\n", FILE_APPEND);
      }
    } elseif(isset($_POST['species']) && isset($_POST['del'])){
      $file = '/home/pi/BirdNET-Pi/include_species_list.txt';
      $str = file_get_contents("$file");
      $str = preg_replace('/^\h*\v+/m', '', $str);
      file_put_contents("$file", "$str");
      foreach($_POST['species'] as $selectedOption) {
        $content = file_get_contents("/home/pi/BirdNET-Pi/include_species_list.txt");
        $newcontent = str_replace($selectedOption, "", "$content");
        file_put_contents("/home/pi/BirdNET-Pi/include_species_list.txt", "$newcontent");
      }
      $file = '/home/pi/BirdNET-Pi/include_species_list.txt';
      $str = file_get_contents("$file");
      $str = preg_replace('/^\h*\v+/m', '', $str);
      file_put_contents("$file", "$str");
    }
    include('scripts/include_list.php');
  }
  if($_POST['view'] == "Excluded"){
    if(isset($_POST['species']) && isset($_POST['add'])){
      $file = '/home/pi/BirdNET-Pi/exclude_species_list.txt';
      $str = file_get_contents("$file");
      $str = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $str);
      file_put_contents("$file", "$str");
      foreach ($_POST['species'] as $selectedOption)
        file_put_contents("/home/pi/BirdNET-Pi/exclude_species_list.txt", $selectedOption."\n", FILE_APPEND);
    } elseif (isset($_POST['species']) && isset($_POST['del'])){
      $file = '/home/pi/BirdNET-Pi/exclude_species_list.txt';
      $str = file_get_contents("$file");
      $str = preg_replace('/^\h*\v+/m', '', $str);
      file_put_contents("$file", "$str");
      foreach($_POST['species'] as $selectedOption) {
        $content = file_get_contents("/home/pi/BirdNET-Pi/exclude_species_list.txt");
        $newcontent = str_replace($selectedOption, "", "$content");
        file_put_contents("/home/pi/BirdNET-Pi/exclude_species_list.txt", "$newcontent");
      }
      $file = '/home/pi/BirdNET-Pi/exclude_species_list.txt';
      $str = file_get_contents("$file");
      $str = preg_replace('/^\h*\v+/m', '', $str);
      file_put_contents("$file", "$str");
    }
    include('scripts/exclude_list.php');
  }
}else{
  include('overview.php');}
?>
<script>
function myFunction() {
  var x = document.getElementById("myTopnav");
  if (x.className === "topnav") {
    x.className += " responsive";
  } else {
    x.className = "topnav";
  }
}
</script>
</body>
