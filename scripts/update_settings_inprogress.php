<?php
$timer=30;
header( "refresh:$timer;url=/advanced.php" );
?>
<html lang="en">
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<head></head>
	<body style="background-color: rgb(119, 196, 135)">
<script>

function countDown(secs,elem) {

	var element = document.getElementById(elem);

	element.innerHTML = "Updating settings... Please allow another "+secs+" seconds for it to complete.";

	if(secs < 1) {

		clearTimeout(timer);

		element.innerHTML = '<h4>Let\'s see</h4>';

		element.innerHTML += '<a href="https://birdnetpi.pmcgui.xyz"></a>';

	}

	secs--;

	var timer = setTimeout('countDown('+secs+',"'+elem+'")',1000);

}

</script>

<div id="status"style="font-size:30px;"></div>

<script>countDown(<?php echo $timer;?>,"status");</script>
	</body>
</html>
