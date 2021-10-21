<?php
shell_exec("/home/pi/BirdNET-Pi/scripts/restart_birdnet_recording.sh");
header('Location: /viewdb.php');
?>
