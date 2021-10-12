<?php
shell_exec("/home/pi/BirdSongs/Extracted/spectrogram.sh");
header('Location: http://birdnetpi.local/spectrogram.png');
?>
