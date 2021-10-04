<?php
shell_exec("/home/pi/BirdNET-Lite/scripts/shutdown_system.sh");
header('Location: http://birdnetsystem.local/scripts/index.html?success=true');
?>
