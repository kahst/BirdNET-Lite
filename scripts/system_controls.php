<?php
if(file_exists('./scripts/common.php')){
	include_once "./scripts/common.php";
}else{
	include_once "./common.php";
}

$_SESSION['behind'] = getGitStatus();
?><html>
<meta name="viewport" content="width=device-width, initial-scale=1">
<br>
<br>
<script>
var seconds = 0;
function update() {
  if(confirm('Are you sure you want to update?')) {
    setInterval(function(){ seconds += 1; document.getElementById('updatebtn').innerHTML = "Updating: <pre id='timer' class='bash'>"+new Date(seconds * 1000).toISOString().substring(14, 19)+"</span>"; }, 1000);
    return true;
  } else {
    return false;
  }
}
</script>
<div class="systemcontrols">
  <form action="" method="GET">
    <button type="submit" name="submit" value="sudo reboot" onclick="return confirm('Are you sure you want to reboot?')">Reboot</button>
  </form>
  <form action="" method="GET">
    <button type="submit" name="submit" id="updatebtn" value="update_birdnet.sh" onclick="update();">Update <?php if(isset($_SESSION['behind']) && $_SESSION['behind'] != "0" && $_SESSION['behind'] != "with"){?><div class="updatenumber"><?php echo $_SESSION['behind']; ?></div><?php } ?></button>
  </form>
  <form action="" method="GET">
    <button type="submit" name="submit" value="sudo shutdown now" onclick="return confirm('Are you sure you want to shutdown?')">Shutdown</button>
  </form>
  <form action="" method="GET">
    <button type="submit" name="submit" value="sudo clear_all_data.sh" onclick="return confirm('Clear ALL Data? Note that this cannot be undone and will take up to 90 seconds.')">Clear ALL data</button>
  </form> 
<?php
  $curr_hash = getGitCurrentHash()
?>
  <p style="font-size:11px;text-align:center"></br></br>Running version: </p>
  <a href="https://github.com/mcguirepr89/BirdNET-Pi/commit/<?php echo $curr_hash; ?>" target="_blank">
    <p style="font-size:11px;text-align:center;box-sizing: border-box"><?php echo $curr_hash; ?></p>
  </a>
</div>
