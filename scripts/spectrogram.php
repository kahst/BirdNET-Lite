<?php
if (file_exists('./scripts/thisrun.txt')) {
  $config = parse_ini_file('./scripts/thisrun.txt');
} elseif (file_exists('./scripts/firstrun.ini')) {
  $config = parse_ini_file('./scripts/firstrun.ini');
}
$refresh = $config['RECORDING_LENGTH'];
$time = time();
echo "<img id=\"spectrogramimage\" style=\"width:100%;height:100%\" src=\"/spectrogram.png?nocache=$time\">";
?>
<script>
// every $refresh seconds, this loop will run and refresh the spectrogram image
window.setInterval(function(){
  document.getElementById("spectrogramimage").src = "/spectrogram.png?nocache="+Date.now();
}, <?php echo $refresh; ?>*1000);
</script>
