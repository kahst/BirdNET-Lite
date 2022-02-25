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
  if($_POST['view'] == "Species Stats"){include('stats.php');}
  if($_POST['view'] == "History"){include('history.php');}
  if($_POST['view'] == "Tools"){
    echo "<form action=\"\" method=\"POST\">
      <button type=\"submit\" name=\"view\" value=\"Settings\">Settings</button>
      <button type=\"submit\" name=\"view\" value=\"System\">System Info</button>
      <button type=\"submit\" name=\"view\" value=\"Included\">Custom Species List</button>
      <button type=\"submit\" name=\"view\" value=\"Excluded\">Excluded Species List</button>
      </form>";
  }
  if($_POST['view'] == "Live Stream"){
    echo "<audio controls autoplay><source src=\"/stream\" type=\"audio/mpeg\"></audio>";
  }
  if($_POST['view'] == "Extractions"){include('play.php');}
  if($_POST['view'] == "Settings"){include('scripts/config.php');} 
  if($_POST['view'] == "Advanced"){include('scripts/advanced.php');}
  if($_POST['view'] == "Log"){
    $url = 'http://birdnetpi.local:8080';
    header("location: $url;");
  }
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
}
?>
