<?php
shell_exec("/home/pi/BirdNET-Pi/scripts/clear_all_data.sh");
header('Location: http://birdnetpi.local/scripts/index.html?success=true');
?>
