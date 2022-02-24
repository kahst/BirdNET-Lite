<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors',1);

?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
  </style>
  </head>
      <h2>Basic Settings</h2>
  <body>
  <div class="row">
    <div class="column first">
    <form action="" method="POST">
<?php 
if (file_exists('/home/pi/BirdNET-Pi/thisrun.txt')) {
  $config = parse_ini_file('/home/pi/BirdNET-Pi/thisrun.txt');
} elseif (file_exists('/home/pi/BirdNET-Pi/firstrun.ini')) {
  $config = parse_ini_file('/home/pi/BirdNET-Pi/firstrun.ini');
} ?>
      <label for="latitude">Latitude: </label>
      <input name="latitude" type="number" max="90" min="-90" step="0.0001" value="<?php print($config['LATITUDE']);?>" required/><br>
      <label for="longitude">Longitude: </label>
      <input name="longitude" type="number" max="180" min="-180" step="0.0001" value="<?php print($config['LONGITUDE']);?>" required/><br>
      <p>Set your Latitude and Longitude to 4 decimal places. Get your coordinates <a href="https://latlong.net" target="_blank">here</a>.</p>
      <label for="birdweather_id">BirdWeather ID: </label>
      <input name="birdweather_id" type="text" value="<?php print($config['BIRDWEATHER_ID']);?>" /><br>
      <p><a href="https://app.birdweather.com" target="_blank">BirdWeather.com</a> is a weather map for bird sounds. Stations around the world supply audio and video streams to BirdWeather where they are then analyzed by BirdNET and compared to eBird Grid data. BirdWeather catalogues the bird audio and spectrogram visualizations so that you can listen to, view, and read about birds throughout the world. <a href="mailto:tim@birdweather.com?subject=Request%20BirdWeather%20ID&body=<?php include('birdweather_request.php'); ?>" target="_blank">Email Tim</a> to request a BirdWeather ID</p>
      <label for="pushed_app_key">Pushed App Key: </label>
      <input name="pushed_app_key" type="text" value="<?php print($config['PUSHED_APP_KEY']);?>" /><br>
      <label for="pushed_app_secret">Pushed App Secret: </label>
      <input name="pushed_app_secret" type="text" value="<?php print($config['PUSHED_APP_SECRET']);?>" /><br>
      <p><a href="https://pushed.co/quick-start-guide">Pushed iOS Notifications</a> can be setup and enabled for New Species notifications. Sorry, Android users, this only works on iOS.</p>
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
      <button type="submit" name="view" value="Settings"><?php
if(isset($_POST['status'])){
  echo "Success!";
} else {
  echo "Update Settings";
}
?></button>
    </form>
    <form action="" method="POST">
      <button type="submit" name="view" value="Advanced">Advanced Settings</button>
    </form>
    </div>
  </div>
</body>

