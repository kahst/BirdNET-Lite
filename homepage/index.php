<div class="banner">
<h1>BirdNET-Pi</h1>
<form action="" method="POST">
<?php
if (file_exists('/home/pi/BirdNET-Pi/thisrun.txt')) {
  $config = parse_ini_file('/home/pi/BirdNET-Pi/thisrun.txt');
} elseif (file_exists('/home/pi/BirdNET-Pi/firstrun.ini')) {
  $config = parse_ini_file('/home/pi/BirdNET-Pi/firstrun.ini');
}

$pages = array("Overview", "Database", "Species Stats", "History", "Tools", "Live Stream", "Extractions", "Spectrogram","Log");
foreach($pages as $page){
  echo "<button type=\"submit\" name=\"view\" value=\"$page\">$page</button>";
}?>
</form>
</div>

<?php
  function httpPost($url, $data){
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($curl);
    echo $response;
  }
if(isset($_POST['view'])){
  if($_POST['view'] == "System"){header('location:phpsysinfo/index.php');}
  if($_POST['view'] == "Spectrogram"){include('spectrogram.php');}
  if($_POST['view'] == "Overview"){include('overview.php');}
  if($_POST['view'] == "Database"){include('viewdb.php');}
  if($_POST['view'] == "Included"){
    if(isset($_POST['species']) && isset($_POST['add'])){
      $file='/home/pi/BirdNET-Pi/include_species_list.txt';
      $str=file_get_contents("$file");
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
  if($_POST['view'] == "Species Stats"){
    include('stats.php');
    if(isset($_POST['species'])){
      $data = [ 'species' => $_POST['species'] ];
      httpPost('/stats.php', $data);
    }
  }
  if($_POST['view'] == "History"){
    include('history.php');
    $data = [ 'date' => $_POST['date'] ];
    httpPost('/history.php', $data);
  }
  if($_POST['view'] == "Tools"){
    echo "<form action=\"\" method=\"POST\">
      <button type=\"submit\" name=\"view\" value=\"Settings\">Settings</button>
      <button type=\"submit\" name=\"view\" value=\"System\">System Info</button>
      <button type=\"submit\" name=\"view\" value=\"Included\">Custom Species List</button>
      <button type=\"submit\" name=\"view\" value=\"Excluded\">Excluded Species List</button>
      </form>";
  }
  if($_POST['view'] == "Live Stream"){
    echo "<audio controls autoplay><source src=\"/stream\" type=\"audio/mpeg\"></audio>";}
  if($_POST['view'] == "Extractions"){
    include('play.php');
    $data = [];
    httpPost('/play.php', $data);}
    if($_POST['view'] == "Settings"){
      if(isset($_POST['submit'])){
        $data = [
          'latitude' => $_POST["latitude"],
          'longitude' => $_POST["longitude"],
          'birdweather_id' => $_POST["birdweather_id"],
          'pushed_app_key' => $_POST["pushed_app_key"],
          'pushed_app_secret' => $_POST["pushed_app_secret"],
          'language' => $_POST["language"],
          'submit' => $_POST["submit"],
        ];
        $url = $_SERVER['HTTP_REFERER'];
        httpPost("$url/scripts/update_config.php", $data);
      }
      include('scripts/config.php');
    } 
  if($_POST['view'] == "Advanced"){
    if(isset($_POST['submit'])){
      $data = [
        'caddy_pwd' => $_POST["caddy_pwd"],
        'ice_pwd' => $_POST["ice_pwd"],
        'webterminal_url' => $_POST["webterminal_url"],
        'birdnetlog_url' => $_POST["birdnetlog_url"],
        'birdnetpi_url' => $_POST["birdnetpi_url"],
        'overlap' => $_POST["overlap"],
        'confidence' => $_POST["confidence"],
        'sensitivity' => $_POST["sensitivity"],
        'full_disk' => $_POST["full_disk"],
        'rec_card' => $_POST["rec_card"],
        'channels' => $_POST["channels"],
        'recording_length' => $_POST["recording_length"],
        'extraction_length' => $_POST["extraction_length"],
        'audiofmt' => $_POST["audiofmt"],
        'status' => "Success"
      ];
      $url = $_SERVER['HTTP_REFERER'];
      httpPost("$url/scripts/update_config.php", $data);
    }
    include('scripts/advanced.php');
  }
  if($_POST['view'] == "Log"){
    $url = 'http://birdnetpi.local:8080';
    header("location: $url;");}
}
?>
