<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("refresh: 30;");
$mysqli = mysqli_connect();
$mysqli->select_db('birds');

if ($mysqli->connect_error) {
  die('Connect Error (' .
    $mysqli->connect_errno . ') '.
    $mysqli->connect_error);
}

// SQL query to select data from database
$sql = "SELECT COUNT(*) AS 'Total' FROM detections
  ORDER BY Date DESC, Time DESC";
$totalcount = $mysqli->query($sql);

$sql1 = "SELECT Date, Time, Sci_Name, Com_Name, MAX(Confidence) 
  FROM detections 
  WHERE Date = CURDATE() 
  GROUP BY Date, Time, Sci_Name, Com_Name 
  ORDER BY Time DESC";
$mosttable = $mysqli->query($sql1);

$sql2 = "SELECT COUNT(*) AS 'Total' FROM detections 
  WHERE Date = CURDATE()";
$todayscount = $mysqli->query($sql2);

$sql3 = "SELECT COUNT(*) AS 'Total' FROM detections 
  WHERE Date = CURDATE() 
  AND Time >= DATE_SUB(NOW(),INTERVAL 1 HOUR)";
$lasthourcount = $mysqli->query($sql3);

$sql4 = "SELECT Com_Name, Date, Time, MAX(Confidence)
  FROM detections
  WHERE Date = CURDATE()
  GROUP BY Com_Name
  ORDER BY MAX(Confidence) DESC";
$specieslist = $mysqli->query($sql4);
$speciescount = mysqli_num_rows($specieslist);

$sql5 = "SELECT Com_Name,COUNT(*) 
  AS 'Total'
  FROM detections 
  GROUP BY Com_Name
  ORDER BY Total DESC";
$speciestally = $mysqli->query($sql5);

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>BirdNET-Pi DB</title>
  <link rel="stylesheet" href="style.css">
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
        <td><?php while ($row = $totalcount->fetch_assoc()) { echo $row['Total']; };?></td>
        <td><?php while ($row = $todayscount->fetch_assoc()) { echo $row['Total']; };?></td>
        <td><?php while ($row = $lasthourcount->fetch_assoc()) { echo $row['Total']; };?></td>
        <td><?php echo $speciescount;?></td>
      </tr>
    </table>
</div>
</div>
    <h2>Today's Detections</h2>
    <!-- TABLE CONSTRUCTION-->
    <table>
      <tr>
        <th>Time</th>
        <th>Scientific Name</th>
        <th>Common Name</th>
        <th>Confidence</th>
      </tr>
      <!-- PHP CODE TO FETCH DATA FROM ROWS-->
<?php // LOOP TILL END OF DATA
while($rows=$mosttable ->fetch_assoc())
{
  $Confidence = sprintf("%.1f%%", $rows['MAX(Confidence)'] * 100);
?>
      <tr>
        <!--FETCHING DATA FROM EACH
          ROW OF EVERY COLUMN-->
        <td><?php echo $rows['Time'];?></td>
        <td><?php echo $rows['Sci_Name'];?></td>
        <td><?php echo $rows['Com_Name'];?></td>
        <td><?php echo $Confidence;?></td>
      </tr>
<?php
}
?>
    </table>
<div class="row">
  <div class="column">
    <h3>Detected Species by Confidence</h3>
    <table>
      <tr>
        <th>Species</th>
        <th>Date</th>
        <th>Time</th>
        <th>Max Confidence Score</th>
      </tr>
<?php // LOOP TILL END OF DATA
while($rows=$specieslist ->fetch_assoc())
{
  $MAX = sprintf("%.1f%%", $rows['MAX(Confidence)'] * 100)
?>
      <tr>
        <td><?php echo $rows['Com_Name'];?></td>
        <td><?php echo $rows['Date'];?></td>
        <td><?php echo $rows['Time'];?></td>
        <td><?php echo $MAX;?></td>

      </tr>
<?php
}
?>
    </table>
  </div>
  <div class="column">
    <h3>Species List by Detections</h3>
    <table>
      <tr>
        <th>Species</th>
        <th>Detections</th>
      </tr>
<?php // LOOP TILL END OF DATA
while($rows=$speciestally ->fetch_assoc())
{
?>
      <tr>
        <td><?php echo $rows['Com_Name'];?></td>
        <td><?php echo $rows['Total'];?></td>
      </tr>
<?php
}
?>

</div>
  </section>
</html>

