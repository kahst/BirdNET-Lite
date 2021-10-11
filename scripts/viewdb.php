<html>
   <head>
      <title>'birds' database 'detections' table</title>
   </head>
   <body>
      <?php
         $dbhost = 'localhost';
         $dbuser = 'birder';
         $dbpass = 'databasepassword';
         $dbname = 'birds';
         $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
         
         if($mysqli->connect_errno ) {
            printf("Connect failed: %s<br />", $mysqli->connect_error);
            exit();
         }
         printf('Connected successfully.<br />');
   
         $sql = 'SELECT * FROM detections';
		 
         $result = $mysqli->query($sql);
           
         if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
               printf("Date: %s || Time: %s ||  Sci_Name: %s ||  Com_Name: %s ||  Confidence: %s ||  Lat: %s ||  Lon: %s ||  Cutoff: %s ||  Week: %s ||  Sens: %s ||  Overlap: %s <br />", 
                  $row["Date"], 
                  $row["Time"], 
                  $row["Sci_Name"],
                  $row["Com_Name"],
                  $row["Confidence"],
                  $row["Lat"],
                  $row["Lon"],
                  $row["Cutoff"],
                  $row["Week"],
                  $row["Sens"],
                  $row["Overlap"]);
            }
         } else {
            printf('No record found.<br />');
         }
         mysqli_free_result($result);
         $mysqli->close();
      ?>
   </body>
</html>
