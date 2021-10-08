<?php
shell_exec("/home/pi/BirdSongs/Extracted/spectrogram.sh");
header('Location: http://birdingpi.local/spectrogram.png');
?>
