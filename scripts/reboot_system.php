<?php
shell_exec("/home/pi/Birding-Pi/scripts/reboot_system.sh");
header('Location: http://birdingpi.local/scripts/index.html?success=true');
?>
