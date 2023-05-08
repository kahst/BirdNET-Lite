<?php
if(file_exists('./scripts/common.php')){
	include_once "./scripts/common.php";
}else{
	include_once "./common.php";
}

function service_status($name) {
  if($name == "birdnet_server.service") {
    $filesinproc=trim(shell_exec("ls ".getDirectory('home')."/BirdSongs/Processed | wc -l"));
    if($filesinproc > 200) {
       echo "<span style='color:#fc6603'>(stalled - backlog of ".$filesinproc." files in ~/BirdSongs/Processed/)</span>";
       return;
    }
  }
  $op = getServiceStatus($name)['status'];
  if(strlen($op) > 0) {
    echo "<span style='color:green'>(active)</span>";
  } else {
    echo "<span style='color:#fc6603'>(inactive)</span>";
  }
}
?>
<html>
<meta name="viewport" content="width=device-width, initial-scale=1">
<br>
<div class="servicecontrols">
  <form action="" method="GET">
    <h3>Live Audio Stream <?php echo service_status("livestream.service");?></h3>
    <button type="submit" name="submit" value="service stop livestream.service && service stop icecast2.service">Stop</button>
    <button type="submit" name="submit" value="service restart livestream.service && service restart icecast2.service">Restart </button>
    <button type="submit" name="submit" value="service disable livestream.service && service disable icecast2 && service stop icecast2.service">Disable</button>
    <button type="submit" name="submit" value="service enable icecast2 && service start icecast2.service && service enable livestream.service">Enable</button>
  </form>
  <form action="" method="GET">
    <h3>Web Terminal <?php echo service_status("web_terminal.service");?></h3>
    <button type="submit" name="submit" value="service stop web_terminal.service">Stop</button>
    <button type="submit" name="submit" value="service restart web_terminal.service">Restart </button>
    <button type="submit" name="submit" value="service disable web_terminal.service">Disable</button>
    <button type="submit" name="submit" value="service enable web_terminal.service">Enable</button>
  </form>
  <form action="" method="GET">
    <h3>BirdNET Log <?php echo service_status("birdnet_log.service");?></h3>
    <button type="submit" name="submit" value="service stop birdnet_log.service">Stop</button>
    <button type="submit" name="submit" value="service restart birdnet_log.service">Restart </button>
    <button type="submit" name="submit" value="service disable birdnet_log.service">Disable</button>
    <button type="submit" name="submit" value="service enable birdnet_log.service">Enable</button>
  </form>
  <form action="" method="GET">
    <h3>Extraction Service <?php echo service_status("extraction.service");?></h3>
    <button type="submit" name="submit" value="service stop extraction.service">Stop</button>
    <button type="submit" name="submit" value="service restart extraction.service">Restart </button>
    <button type="submit" name="submit" value="service disable extraction.service">Disable</button>
    <button type="submit" name="submit" value="service enable extraction.service">Enable</button>
  </form>
  <form action="" method="GET">
    <h3>BirdNET Analysis Server <?php echo service_status("birdnet_server.service");?></h3>
    <button type="submit" name="submit" value="service stop birdnet_server.service">Stop</button>
    <button type="submit" name="submit" value="service restart birdnet_server.service">Restart</button>
    <button type="submit" name="submit" value="service disable birdnet_server.service">Disable</button>
    <button type="submit" name="submit" value="service enable birdnet_server.service">Enable</button>
  </form>
  <form action="" method="GET">
    <h3>BirdNET Analysis Client <?php echo service_status("birdnet_analysis.service");?></h3>
    <button type="submit" name="submit" value="service stop birdnet_analysis.service">Stop</button>
    <button type="submit" name="submit" value="service restart birdnet_analysis.service">Restart</button>
    <button type="submit" name="submit" value="service disable birdnet_analysis.service">Disable</button>
    <button type="submit" name="submit" value="service enable birdnet_analysis.service">Enable</button>
  </form>
  <form action="" method="GET">
    <h3>Streamlit Statistics <?php echo service_status("birdnet_stats.service");?></h3>
    <button type="submit" name="submit" value="service stop birdnet_stats.service">Stop</button>
    <button type="submit" name="submit" value="service restart birdnet_stats.service">Restart</button>
    <button type="submit" name="submit" value="service disable birdnet_stats.service">Disable</button>
    <button type="submit" name="submit" value="service enable birdnet_stats.service">Enable</button>
  </form>
  <form action="" method="GET">
    <h3>Recording Service <?php echo service_status("birdnet_recording.service");?></h3>
    <button type="submit" name="submit" value="service stop birdnet_recording.service">Stop</button>
    <button type="submit" name="submit" value="service restart birdnet_recording.service">Restart</button>
    <button type="submit" name="submit" value="service disable birdnet_recording.service">Disable</button>
    <button type="submit" name="submit" value="service enable birdnet_recording.service">Enable</button>
  </form>
  <form action="" method="GET">
    <h3>Chart Viewer <?php echo service_status("chart_viewer.service");?></h3>
    <button type="submit" name="submit" value="service stop chart_viewer.service">Stop</button>
    <button type="submit" name="submit" value="service restart chart_viewer.service">Restart</button>
    <button type="submit" name="submit" value="service disable chart_viewer.service">Disable</button>
    <button type="submit" name="submit" value="service enable chart_viewer.service">Enable</button>
  </form>
  <form action="" method="GET">
    <h3>Spectrogram Viewer <?php echo service_status("spectrogram_viewer.service");?></h3>
    <button type="submit" name="submit" value="service stop spectrogram_viewer.service">Stop</button>
    <button type="submit" name="submit" value="service restart spectrogram_viewer.service">Restart</button>
    <button type="submit" name="submit" value="service disable spectrogram_viewer.service">Disable</button>
    <button type="submit" name="submit" value="service enable spectrogram_viewer.service">Enable</button>
  </form>
  <form action="" method="GET">
    <button type="submit" name="submit" value="system stop core.services">Stop Core Services</button>
  </form> 
  <form action="" method="GET">
    <button type="submit" name="submit" value="system restart core.services">Restart Core Services</button>
  </form> 
</div>
