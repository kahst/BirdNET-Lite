<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

# Basic Settings
if(isset($_GET["latitude"])){
$latitude = $_GET["latitude"];
$longitude = $_GET["longitude"];
$birdweather_id = $_GET["birdweather_id"];
$pushed_app_key = $_GET["pushed_app_key"];
$pushed_app_secret = $_GET["pushed_app_secret"];
$apprise_input = $_GET['apprise_input'];

$contents = file_get_contents("/etc/birdnet/birdnet.conf");
$contents = preg_replace("/LATITUDE=.*/", "LATITUDE=$latitude", $contents);
$contents = preg_replace("/LONGITUDE=.*/", "LONGITUDE=$longitude", $contents);
$contents = preg_replace("/BIRDWEATHER_ID=.*/", "BIRDWEATHER_ID=$birdweather_id", $contents);
$contents = preg_replace("/PUSHED_APP_KEY=.*/", "PUSHED_APP_KEY=$pushed_app_key", $contents);
$contents = preg_replace("/PUSHED_APP_SECRET=.*/", "PUSHED_APP_SECRET=$pushed_app_secret", $contents);

$contents2 = file_get_contents("./scripts/thisrun.txt");
$contents2 = preg_replace("/LATITUDE=.*/", "LATITUDE=$latitude", $contents2);
$contents2 = preg_replace("/LONGITUDE=.*/", "LONGITUDE=$longitude", $contents2);
$contents2 = preg_replace("/BIRDWEATHER_ID=.*/", "BIRDWEATHER_ID=$birdweather_id", $contents2);
$contents2 = preg_replace("/PUSHED_APP_KEY=.*/", "PUSHED_APP_KEY=$pushed_app_key", $contents2);
$contents2 = preg_replace("/PUSHED_APP_SECRET=.*/", "PUSHED_APP_SECRET=$pushed_app_secret", $contents2);

$fh = fopen("/etc/birdnet/birdnet.conf", "w");
$fh2 = fopen("./scripts/thisrun.txt", "w");
fwrite($fh, $contents);
fwrite($fh2, $contents2);

if(strlen($apprise_input) > 0){
  $appriseconfig = fopen("".exec("~/").".apprise", "w");
  fwrite($appriseconfig, $apprise_input);
}


$language = $_GET["language"];
if ($language != "none"){
  $user = shell_exec("awk -F: '/1000/{print $1}' /etc/passwd");
  $home = shell_exec("awk -F: '/1000/{print $6}' /etc/passwd");
  $home = trim($home);
  $command = "sudo -u".$user." mv ".$home."/BirdNET-Pi/model/labels.txt ".$home."/BirdNET-Pi/model/labels.txt.old && sudo -u".$user." unzip ".$home."/BirdNET-Pi/model/labels_l18n.zip ".$language." -d ".$home."/BirdNET-Pi/model && sudo -u".$user." mv ".$home."/BirdNET-Pi/model/".$language." ".$home."/BirdNET-Pi/model/labels.txt";
  $command_output = `sudo $command`;
  `sudo restart_services.sh`;
}
}

?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
  </style>
  </head>
<div class="settings">
      <h2>Basic Settings</h2>
    <form action="" method="GET">
<?php 
if (file_exists('./scripts/thisrun.txt')) {
  $config = parse_ini_file('./scripts/thisrun.txt');
} elseif (file_exists('./scripts/firstrun.ini')) {
  $config = parse_ini_file('./scripts/firstrun.ini');
} 
if (file_exists('~/.apprise')) {
  $apprise_config = file_get_contents('~/.apprise');
} else {
  $apprise_config = "";
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
  if($submittedpwd !== $caddypwd || $submitteduser !== 'birdnet'){
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'You cannot edit the settings for this installation';
    exit;
  }
}
?>
      <label for="latitude">Latitude: </label>
      <input name="latitude" type="number" max="90" min="-90" step="0.0001" value="<?php print($config['LATITUDE']);?>" required/><br>
      <label for="longitude">Longitude: </label>
      <input name="longitude" type="number" max="180" min="-180" step="0.0001" value="<?php print($config['LONGITUDE']);?>" required/><br>
      <p>Set your Latitude and Longitude to 4 decimal places. Get your coordinates <a href="https://latlong.net" target="_blank">here</a>.</p>
      <label for="birdweather_id">BirdWeather ID: </label>
      <input name="birdweather_id" type="text" value="<?php print($config['BIRDWEATHER_ID']);?>" /><br>
      <p><a href="https://app.birdweather.com" target="_blank">BirdWeather.com</a> is a weather map for bird sounds. Stations around the world supply audio and video streams to BirdWeather where they are then analyzed by BirdNET and compared to eBird Grid data. BirdWeather catalogues the bird audio and spectrogram visualizations so that you can listen to, view, and read about birds throughout the world. <a href="mailto:tim@birdweather.com?subject=Request%20BirdWeather%20ID&body=<?php include('./scripts/birdweather_request.php'); ?>" target="_blank">Email Tim</a> to request a BirdWeather ID</p>
      <label for="pushed_app_key">Pushed App Key: </label>
      <input name="pushed_app_key" type="text" value="<?php print($config['PUSHED_APP_KEY']);?>" /><br>
      <label for="pushed_app_secret">Pushed App Secret: </label>
      <input name="pushed_app_secret" type="text" value="<?php print($config['PUSHED_APP_SECRET']);?>" /><br>
      <p><a target="_blank" href="https://pushed.co/quick-start-guide">Pushed iOS Notifications</a> can be setup and enabled for New Species notifications. Be sure to "Enable" the "Pushed Notifications" in "Tools" > "Services" if you would like to use this feature. Sorry, Android users, this only works on iOS.</p>

      <label for="apprise_input">Apprise Notifications Configuration: </label>
      <textarea name="apprise_input" type="text" ><?php print($apprise_config);?></textarea>
      <p><a target="_blank" href="https://github.com/caronc/apprise/wiki">Apprise Notifications</a> can be setup and enabled for New Species notifications.</p>

      <label for="language">Database Language: </label>
      <select name="language">
        <option value="none">Select your language</option>
        <option value="labels_af.txt">Afrikaans</option>
        <option value="labels_ca.txt">Catalan</option>
        <option value="labels_cs.txt">Czech</option>
        <option value="labels_zh.txt">Chinese</option>
        <option value="labels_hr.txt">Croatian</option>
        <option value="labels_da.txt">Danish</option>
        <option value="labels_nl.txt">Dutch</option>
        <option value="labels_en.txt">English</option>
        <option value="labels_et.txt">Estonian</option>
        <option value="labels_fi.txt">Finnish</option>
        <option value="labels_fr.txt">French</option>
        <option value="labels_de.txt">German</option>
        <option value="labels_hu.txt">Hungarian</option>
        <option value="labels_is.txt">Icelandic</option>
        <option value="labels_id.txt">Indonesia</option>
        <option value="labels_it.txt">Italian</option>
        <option value="labels_ja.txt">Japanese</option>
        <option value="labels_lv.txt">Latvian</option>
        <option value="labels_lt.txt">Lithuania</option>
        <option value="labels_no.txt">Norwegian</option>
        <option value="labels_pl.txt">Polish</option>
        <option value="labels_pt.txt">Portugues</option>
        <option value="labels_ru.txt">Russian</option>
        <option value="labels_sk.txt">Slovak</option>
        <option value="labels_sl.txt">Slovenian</option>
        <option value="labels_es.txt">Spanish</option>
        <option value="labels_sv.txt">Swedish</option>
        <option value="labels_th.txt">Thai</option>
        <option value="labels_uk.txt">Ukrainian</option>
      </select>
      <br><br>
      <input type="hidden" name="status" value="success">
      <input type="hidden" name="submit" value="settings">
      <button type="submit" name="view" value="Settings">
<?php
if(isset($_GET['status'])){
  echo "Success!";
} else {
  echo "Update Settings";
}
?>
      </button>
      </form>
      <form action="" method="GET">
        <button type="submit" name="view" value="Advanced">Advanced Settings</button>
      </form>
</div>
