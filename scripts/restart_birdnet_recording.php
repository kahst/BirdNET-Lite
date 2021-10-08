<?php
shell_exec("/home/pi/Birding-Pi/scripts/restart_birdnet_recording.sh");
header('Location: http://birdingpi.local/scripts/index.html?success=true');
?>
