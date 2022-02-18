<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header("refresh: 300;");
$myDate = date('Y-m-d');
$chart = "Combo-$myDate.png";

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Overview</title>
  <!-- CSS FOR STYLING THE PAGE -->
<link rel="stylesheet" href="style.css">


<style>
a {
  text-decoration: none;
  color:black;
}
.center {
  display: block;
  margin-left: 5px;
  margin-right: 5px;
  width: 90%;
  padding: 5px;
}
.center2 {
  display: block;
  margin-left: 5px;
  margin-right: 5px;
  width: 100%;
  padding: 5px;
}
</style>
</head>
<body style="background-color: rgb(119, 196, 135);">
    <h2>Overview</h2>
<div class="row">
 <div class="column2">
    <table>
      <tr>
        <th>Most Recent Detection</th>
	<td><a href="/By_Common_Name/"></a></td>
	<td><a href="/By_Date/"/></a></td>
        <td></td>
	<td><a href="https://wikipedia.org/wiki/" target="top"/>More Info</a></td>
      </tr>
    </table>
  </div>
</div>

<div class="row">
 <div class="column">
    <table>
      <tr>
        <th></th>
        <th>Total</th>
        <th>Today</th>
        <th>Last Hour</th>
      </tr>
      <tr>
        <th>Number of Detections</th>
        <td></td>
        <td></td>
        <td></td>
      </tr>
    </table>
  </div>
 <div class="column">
    <table>
      <tr>
        <th>Species Detected Today</th>
        <td></td>
      </tr>
    </table>
  </div>
</div>
<?php
if (file_exists('/home/pi/BirdSongs/Extracted/Charts/'.$chart)) {
  echo "<img src=\"/Charts/$chart?nocache=time()\" style=\"width: 100%;padding: 5px;margin-left: auto;margin-right: auto;display: block;\">";
} else {
    echo "<p style=\"text-align:center;margin-left:-150px;\">No Detections For Today</p>";
}
?>
    <h2>Currently Analyzing</h2>
<img src='/spectrogram.png?nocache=<?php echo time();?>' style="width: 100%;padding: 5px;">
</html>
