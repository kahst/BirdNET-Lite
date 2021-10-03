<?php
shell_exec("/home/pi/BirdNET-Lite/scripts/restart_caddy.sh");
header('Location: http://birdnetsystem.local/scripts/index.html?success=true');
?>
