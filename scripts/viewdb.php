<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("refresh: 30;");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>BirdNET-Pi DB</title>
  <link rel="stylesheet" href="style.css">
  <style>
a {
  text-decoration:none;
  color:black;
}
.a2 { color:blue;}
</style>
</head>
<body style="background-color: rgb(119, 196, 135);">

  <section>
    <h2>Number of Detections</h2>
<div class="row">
 <div class="column2">
    <table>
      <tr>
	<th>Total</th>
	<th>Today</th>
	<th>Last Hour</th>
	<th>Number of Unique Species</th>
      </tr>
      <tr>
	<td></td>
	<td></td>
	<td></td>
	<td></td>
      </tr>
    </table>
</div>
</div>
    <h2>Today's Detections</h2>
    <table>
      <tr>
	<th>Time</th>
	<th>Scientific Name</th>
	<th>Common Name</th>
	<th>Confidence</th>
	<th>Links</th>
      </tr>
      <tr>
	<td></td>
	<td><a href="/By_Scientific_Name/"/></a></td>
	<td><a href="/By_Common_Name/"/></a></td>
	<td></td>
	<td><a class="a2" href="https://allaboutbirds.org/guide/" target="top">All About Birds</a>, <a class="a2" href="https://wikipedia.org/wiki/" target="top">Wikipedia</a></td>
      </tr>
    </table>
