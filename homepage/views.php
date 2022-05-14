<link rel="stylesheet" href="style.css">
<style>
body::-webkit-scrollbar {
  display:none
}
</style>
<meta name="viewport" content="width=device-width, initial-scale=1">
<div class="topnav" id="myTopnav">
<form action="" method="GET" id="views">
  <button type="submit" name="view" value="Overview" form="views">Overview</button>
</form>
<form action="" method="GET" id="views">
  <button type="submit" name="view" value="Today's Detections" form="views">Today's Detections</button>
</form>
<form action="" method="GET" id="views">
  <button type="submit" name="view" value="Species Stats" form="views">Best Recordings</button>
</form>
<form action="" method="GET" id="views">
  <button type="submit" name="view" value="Streamlit" form="views">Species Stats</button>
</form>
<form action="" method="GET" id="views">
  <button type="submit" name="view" value="Daily Charts" form="views">Daily Charts</button>
</form>
<form action="" method="GET" id="views">
  <button type="submit" name="view" value="Recordings" form="views">Recordings</button>
</form>
<form action="" method="GET" id="views">
  <button type="submit" name="view" value="Spectrogram" form="views">Spectrogram</button>
</form>
<form action="" method="GET" id="views">
  <button type="submit" name="view" value="View Log" form="views">View Log</button>
</form>
<form action="" method="GET" id="views">
  <button type="submit" name="view" value="Tools" form="views">Tools</button>
</form>
<button href="javascript:void(0);" class="icon" onclick="myFunction()"><img src="images/menu.png"></button>
</div>

<script>
  var topbuttons = document.querySelectorAll("button[form='views']");
  if(window.location.search.substr(1) != '') {
    for (var i = 0; i < topbuttons.length; i++) {
       if(topbuttons[i].value == decodeURIComponent(window.location.search.substr(1)).replace(/\+/g,' ').split('=').pop()) {
            topbuttons[i].classList.add("button-hover");
       }
     }
  } else {
    topbuttons[0].classList.add("button-hover");
  }
  function copyOutput(elem) {
    elem.innerHTML = 'Copied!';
    const copyText = document.getElementsByTagName("pre")[0].textContent;
    const textArea = document.createElement('textarea');
    textArea.style.position = 'absolute';
    textArea.style.left = '-100%';
    textArea.textContent = copyText;
    document.body.append(textArea);
    textArea.select();
    document.execCommand("copy");
  }
</script>

<div class="views">
<?php
if(isset($_GET['view'])){
  if($_GET['view'] == "System Info"){echo "<iframe src='phpsysinfo/index.php'></iframe>";}
  if($_GET['view'] == "System Controls"){include('scripts/system_controls.php');}
  if($_GET['view'] == "Services"){include('scripts/service_controls.php');}
  if($_GET['view'] == "Spectrogram"){include('spectrogram.php');}
  if($_GET['view'] == "View Log"){echo "<body style=\"scroll:no;overflow-x:hidden;\"><iframe style=\"width:calc( 100% + 1em);\" src=\"/log\"></iframe></body>";}
  if($_GET['view'] == "Overview"){include('overview.php');}
  if($_GET['view'] == "Today's Detections"){include('todays_detections.php');}
  if($_GET['view'] == "Species Stats"){echo "<br><br>";include('stats.php');}
  if($_GET['view'] == "Streamlit"){echo "<iframe src=\"/stats\"></iframe>";}
  if($_GET['view'] == "Daily Charts"){include('history.php');}
  if($_GET['view'] == "Tools"){
    if (file_exists('./scripts/thisrun.txt')) {
      $config = parse_ini_file('./scripts/thisrun.txt');
    } elseif (file_exists('./scripts/firstrun.ini')) {
      $config = parse_ini_file('./scripts/firstrun.ini');
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
        $url = $_SERVER['SERVER_NAME']."/scripts/adminer.php";
        echo "<div class=\"centered\">
  <form action=\"\" method=\"GET\" id=\"views\">
        <button type=\"submit\" name=\"view\" value=\"Settings\" form=\"views\">Settings</button>
        <button type=\"submit\" name=\"view\" value=\"System Info\" form=\"views\">System Info</button>
        <button type=\"submit\" name=\"view\" value=\"System Controls\" form=\"views\">System Controls</button>
        <button type=\"submit\" name=\"view\" value=\"Services\" form=\"views\">Services</button>
        <button type=\"submit\" name=\"view\" value=\"File\" form=\"views\">File Manager</button>
        <a href=\"scripts/adminer.php\" target=\"_top\"><button type=\"submit\" form=\"\">Database Maintenanace</button></a>
        <button type=\"submit\" name=\"view\" value=\"Webterm\" form=\"views\">Web Terminal</button>
        <button type=\"submit\" name=\"view\" value=\"Included\" form=\"views\">Custom Species List</button>
        <button type=\"submit\" name=\"view\" value=\"Excluded\" form=\"views\">Excluded Species List</button>
  </form>
  </div>";
      } else {
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'You cannot edit the settings for this installation';
        exit;
      }
    }
  }
  if($_GET['view'] == "Recordings"){include('play.php');}
  if($_GET['view'] == "Settings"){include('scripts/config.php');} 
  if($_GET['view'] == "Advanced"){include('scripts/advanced.php');}
  if($_GET['view'] == "Included"){
    if(isset($_GET['species']) && isset($_GET['add'])){
      $file = './scripts/include_species_list.txt';
      $str = file_get_contents("$file");
      $str = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $str);
      file_put_contents("$file", "$str");
      if(isset($_GET['species'])){
        foreach ($_GET['species'] as $selectedOption)
          file_put_contents("./scripts/include_species_list.txt", $selectedOption."\n", FILE_APPEND);
      }
    } elseif(isset($_GET['species']) && isset($_GET['del'])){
      $file = './scripts/include_species_list.txt';
      $str = file_get_contents("$file");
      $str = preg_replace('/^\h*\v+/m', '', $str);
      file_put_contents("$file", "$str");
      foreach($_GET['species'] as $selectedOption) {
        $content = file_get_contents("../BirdNET-Pi/include_species_list.txt");
        $newcontent = str_replace($selectedOption, "", "$content");
        file_put_contents("./scripts/include_species_list.txt", "$newcontent");
      }
      $file = './scripts/include_species_list.txt';
      $str = file_get_contents("$file");
      $str = preg_replace('/^\h*\v+/m', '', $str);
      file_put_contents("$file", "$str");
    }
    include('./scripts/include_list.php');
  }
  if($_GET['view'] == "Excluded"){
    if(isset($_GET['species']) && isset($_GET['add'])){
      $file = './scripts/exclude_species_list.txt';
      $str = file_get_contents("$file");
      $str = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $str);
      file_put_contents("$file", "$str");
      foreach ($_GET['species'] as $selectedOption)
        file_put_contents("./scripts/exclude_species_list.txt", $selectedOption."\n", FILE_APPEND);
    } elseif (isset($_GET['species']) && isset($_GET['del'])){
      $file = './scripts/exclude_species_list.txt';
      $str = file_get_contents("$file");
      $str = preg_replace('/^\h*\v+/m', '', $str);
      file_put_contents("$file", "$str");
      foreach($_GET['species'] as $selectedOption) {
        $content = file_get_contents("./scripts/exclude_species_list.txt");
        $newcontent = str_replace($selectedOption, "", "$content");
        file_put_contents("./scripts/exclude_species_list.txt", "$newcontent");
      }
      $file = './scripts/exclude_species_list.txt';
      $str = file_get_contents("$file");
      $str = preg_replace('/^\h*\v+/m', '', $str);
      file_put_contents("$file", "$str");
    }
    include('./scripts/exclude_list.php');
  }
  if($_GET['view'] == "File"){
    echo "<iframe src='scripts/filemanager/filemanager.php'></iframe>";
  }
  if($_GET['view'] == "Webterm"){
    if (file_exists('./scripts/thisrun.txt')) {
      $config = parse_ini_file('./scripts/thisrun.txt');
    } elseif (file_exists('./scripts/firstrun.ini')) {
      $config = parse_ini_file('./scripts/firstrun.ini');
    }
    $caddypwd = $config['CADDY_PWD'];
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
      header('WWW-Authenticate: Basic realm="My Realm"');
      header('HTTP/1.0 401 Unauthorized');
      echo 'You cannot access the web terminal';
      exit;
    } else {
      $submittedpwd = $_SERVER['PHP_AUTH_PW'];
      $submitteduser = $_SERVER['PHP_AUTH_USER'];
      if($submittedpwd == $caddypwd && $submitteduser == 'birdnet'){
        #ACCESS THE WEB TERMINAL
        echo "<iframe src='/terminal'></iframe>";
      } else {
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'You cannot access the web terminal';
        exit;
      }
    }
  }
} elseif(isset($_GET['submit'])) {
    if (file_exists('./scripts/thisrun.txt')) {
      $config = parse_ini_file('./scripts/thisrun.txt');
    } elseif (file_exists('./scripts/firstrun.ini')) {
      $config = parse_ini_file('./scripts/firstrun.ini');
    }
    $caddypwd = $config['CADDY_PWD'];
    if (!isset($_SERVER['PHP_AUTH_USER'])) {
      header('WWW-Authenticate: Basic realm="My Realm"');
      header('HTTP/1.0 401 Unauthorized');
      echo 'You cannot access the web terminal';
      exit;
    } else {
      $submittedpwd = $_SERVER['PHP_AUTH_PW'];
      $submitteduser = $_SERVER['PHP_AUTH_USER'];
      if($submittedpwd == $caddypwd && $submitteduser == 'birdnet'){
        $command = $_GET['submit'];
  if(isset($command)){
        $initcommand = $command;
         if (strpos($command, "systemctl") !== false) {
            $tmp = explode(" ",trim($command));
            $command .= "& sleep 3;sudo systemctl status ".end($tmp);
         }
         $results = shell_exec("$command 2>&1");
         $results = str_replace("FAILURE", "<span style='color:red'>FAILURE</span>", $results);
         $results = str_replace("failed", "<span style='color:red'>failed</span>",$results);
         $results = str_replace("active (running)", "<span style='color:green'><b>active (running)</b></span>",$results);
         if(strlen($results) == 0) {
          $results = "This command has no output.";
         }
         echo "<table style='min-width:70%;'><tr class='relative'><th>Output of command:`".$initcommand."`<button class='copyimage' style='right:40px' onclick='copyOutput(this);'>Copy</button></th></tr><tr><td><pre style='text-align:left'>$results</pre></td></tr></table>"; 
      } else {
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');
        echo 'You cannot access the web terminal';
        exit;
      }
    }
  }
  ob_end_flush();
} else {include('overview.php');}
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
function setLiveStreamVolume(vol) {
  var audioelement =  window.parent.document.getElementsByTagName("audio")[0];
  if (typeof(audioelement) != 'undefined' && audioelement != null)
  {
    audioelement.volume = vol
  }
}
window.onbeforeunload = function(event) {
  // if the user is playing a video and then navigates away mid-play, the live stream audio should be unmuted again
  var audioelement =  window.parent.document.getElementsByTagName("audio")[0];
  if (typeof(audioelement) != 'undefined' && audioelement != null)
  {
    audioelement.volume = 1
  }
}
</script>
</div>
</body>
