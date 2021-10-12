<?php
shell_exec("/home/pi/BirdNET-Pi/scripts/reboot_system.sh");
header('Location: http://birdnetpi.local/scripts/index.html?success=true');
?>
