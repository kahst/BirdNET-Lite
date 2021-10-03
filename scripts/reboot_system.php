<?php
shell_exec("/home/pi/BirdNET-Lite/scripts/reboot_system.sh");
header('Location: http://birdnetsystem.local/scripts/index.html?success=true');
?>
