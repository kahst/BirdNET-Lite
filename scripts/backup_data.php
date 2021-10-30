<?php
shell_exec("sudo -u pi /home/pi/BirdNET-Pi/scripts/backup_data.sh > /tmp/birdnetbackup.log 2&>1");
header('Location: /backup_inprogress.html');
?>
