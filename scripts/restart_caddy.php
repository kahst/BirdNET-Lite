<?php
shell_exec("/home/pi/BirdNET-Pi/scripts/restart_caddy.sh");
header('Location: /viewdb.php');
?>
