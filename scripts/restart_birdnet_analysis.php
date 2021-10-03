<?php
shell_exec("/home/pi/BirdNET-Lite/scripts/restart_birdnet_analysis.sh");
header('Location: http://birdnetsystem.local/scripts/index.html?success=true');
?>
