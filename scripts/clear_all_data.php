<?php
shell_exec("/home/pi/Birding-Pi/scripts/clear_all_data.sh");
header('Location: http://birdingpi.local/scripts/index.html?success=true');
?>
