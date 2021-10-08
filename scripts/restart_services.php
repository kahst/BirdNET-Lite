<?php
shell_exec("/home/pi/Birding-Pi/scripts/restart_services.sh");
header('Location: http://birdingpi.local/scripts/index.html?success=true');
?>
