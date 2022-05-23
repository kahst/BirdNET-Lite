<html>
<meta name="viewport" content="width=device-width, initial-scale=1">
<br>
<br>
<div class="servicecontrols">
  <form action="" method="GET">
    <h3>Live Audio Stream</h3>
    <button type="submit" name="submit" value="sudo systemctl stop livestream.service && sudo /etc/init.d/icecast2 stop">Stop</button>
    <button type="submit" name="submit" value="sudo systemctl restart livestream.service && sudo /etc/init.d/icecast2 restart">Restart </button>
    <button type="submit" name="submit" value="sudo systemctl disable --now livestream.service && sudo systemctl disable icecast2 && sudo /etc/init.d/icecast2 stop">Disable</button>
    <button type="submit" name="submit" value="sudo systemctl enable icecast2 && sudo /etc/init.d/icecast2 start && sudo systemctl enable --now livestream.service">Enable</button>
  </form>
  <form action="" method="GET">
    <h3>Web Terminal</h3>
    <button type="submit" name="submit" value="sudo systemctl stop web_terminal.service">Stop</button>
    <button type="submit" name="submit" value="sudo systemctl restart web_terminal.service">Restart </button>
    <button type="submit" name="submit" value="sudo systemctl disable --now web_terminal.service">Disable</button>
    <button type="submit" name="submit" value="sudo systemctl enable --now web_terminal.service">Enable</button>
  </form>
  <form action="" method="GET">
    <h3>BirdNET Log</h3>
    <button type="submit" name="submit" value="sudo systemctl stop birdnet_log.service">Stop</button>
    <button type="submit" name="submit" value="sudo systemctl restart birdnet_log.service">Restart </button>
    <button type="submit" name="submit" value="sudo systemctl disable --now birdnet_log.service">Disable</button>
    <button type="submit" name="submit" value="sudo systemctl enable --now birdnet_log.service">Enable</button>
  </form>
  <form action="" method="GET">
    <h3>Extraction Service</h3>
    <button type="submit" name="submit" value="sudo systemctl stop extraction.service">Stop</button>
    <button type="submit" name="submit" value="sudo systemctl restart extraction.service">Restart </button>
    <button type="submit" name="submit" value="sudo systemctl disable --now extraction.service">Disable</button>
    <button type="submit" name="submit" value="sudo systemctl enable --now extraction.service">Enable</button>
  </form>
  <form action="" method="GET">
    <h3>BirdNET Analysis Server</h3>
    <button type="submit" name="submit" value="sudo systemctl stop birdnet_server.service">Stop</button>
    <button type="submit" name="submit" value="sudo systemctl restart birdnet_server.service">Restart</button>
    <button type="submit" name="submit" value="sudo systemctl disable --now birdnet_server.service">Disable</button>
    <button type="submit" name="submit" value="sudo systemctl enable --now birdnet_server.service">Enable</button>
  </form>
  <form action="" method="GET">
    <h3>BirdNET Analysis Client</h3>
    <button type="submit" name="submit" value="sudo systemctl stop birdnet_analysis.service">Stop</button>
    <button type="submit" name="submit" value="sudo systemctl restart birdnet_analysis.service">Restart</button>
    <button type="submit" name="submit" value="sudo systemctl disable --now birdnet_analysis.service">Disable</button>
    <button type="submit" name="submit" value="sudo systemctl enable --now birdnet_analysis.service">Enable</button>
  </form>
  <form action="" method="GET">
    <h3>Streamlit Statistics</h3>
    <button type="submit" name="submit" value="sudo systemctl stop birdnet_stats.service">Stop</button>
    <button type="submit" name="submit" value="sudo systemctl restart birdnet_stats.service">Restart</button>
    <button type="submit" name="submit" value="sudo systemctl disable --now birdnet_stats.service">Disable</button>
    <button type="submit" name="submit" value="sudo systemctl enable --now birdnet_stats.service">Enable</button>
  </form>
  <form action="" method="GET">
    <h3>Recording Service</h3>
    <button type="submit" name="submit" value="sudo systemctl stop birdnet_recording.service">Stop</button>
    <button type="submit" name="submit" value="sudo systemctl restart birdnet_recording.service">Restart</button>
    <button type="submit" name="submit" value="sudo systemctl disable --now birdnet_recording.service">Disable</button>
    <button type="submit" name="submit" value="sudo systemctl enable --now birdnet_recording.service">Enable</button>
  </form>
  <form action="" method="GET">
    <h3>Chart Viewer</h3>
    <button type="submit" name="submit" value="sudo systemctl stop chart_viewer.service">Stop</button>
    <button type="submit" name="submit" value="sudo systemctl restart chart_viewer.service">Restart</button>
    <button type="submit" name="submit" value="sudo systemctl disable --now chart_viewer.service">Disable</button>
    <button type="submit" name="submit" value="sudo systemctl enable --now chart_viewer.service">Enable</button>
  </form>
  <form action="" method="GET">
    <h3>Spectrogram Viewer</h3>
    <button type="submit" name="submit" value="sudo systemctl stop spectrogram_viewer.service">Stop</button>
    <button type="submit" name="submit" value="sudo systemctl restart spectrogram_viewer.service">Restart</button>
    <button type="submit" name="submit" value="sudo systemctl disable --now spectrogram_viewer.service">Disable</button>
    <button type="submit" name="submit" value="sudo systemctl enable --now spectrogram_viewer.service">Enable</button>
  </form>
  <form action="" method="GET">
    <button type="submit" name="submit" value="stop_core_services.sh">Stop Core Services</button>
  </form>	
  <form action="" method="GET">
    <button type="submit" name="submit" value="restart_services.sh" onclick="return confirm('This will take about 90 seconds.')">Restart All Services</button>
  </form>	
</div>
