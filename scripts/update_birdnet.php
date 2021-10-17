<?php
shell_exec("sudo -u pi /home/pi/BirdNET-Pi/scripts/update_birdnet.sh > /tmp/phpupdate.log 2&>1");
header('Location: http://birdnetpi.local/index.html?success=true');
?>
