<?php
shell_exec("/home/pi/BirdNET-Pi/scripts/stop_core_services.sh");
header('Location: /overview.php');
?>
