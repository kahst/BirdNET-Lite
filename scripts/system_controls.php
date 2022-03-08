<html>
<meta name="viewport" content="width=device-width, initial-scale=1">
<br>
<br>
<div class="systemcontrols">
  <form action="" method="POST">
    <button type="submit" name="submit" value="sudo reboot" onclick="return confirm('Are you sure you want to reboot?')">Reboot</button>
  </form>
  <form action="" method="POST">
    <button type="submit" name="submit" value="update_birdnet.sh" onclick="return confirm('BE SURE TO STASH ANY LOCAL CHANGES YOU HAVE MADE TO THE SYSTEM BEFORE UPDATING!!!')" >Update</button>
  </form>
  <form action="" method="POST">
    <button type="submit" name="submit" value="sudo shutdown now" onclick="return confirm('Are you sure you want to shutdown?')">Shutdown</button>
  </form>
  <form action="" method="POST">
    <button type="submit" name="submit" value="sudo -upi clear_all_data.sh" onclick="return confirm('Clear ALL Data? This cannot be undone.')">Clear ALL data</button>
  </form>	
</div>
