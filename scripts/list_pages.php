<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
* {
  font-family: 'Arial', 'Gill Sans', 'Gill Sans MT',
    ' Calibri', 'Trebuchet MS', 'sans-serif';
  box-sizing: border-box;
}
/* Create two unequal columns that floats next to each other */
.column {
float: left;
padding: 10px;
}
.first {
width: calc(50% - 70px);
}
.second {
width: calc(50% - 30px);
}
.
/* Clear floats after the columns */
.row:after {
content: "";
display: table;
clear: both;
}
body {
  background-color: rgb(119, 196, 135);
}
a {
  font-size:large;
  text-decoration: none;
}
.block {
display: block;
         font-weight: bold;
width:100%;
border: none;
        background-color: #04AA6D;
padding: 20px 20px;
color: white;
       font-size: medium;
cursor: pointer;
        text-align: center;
}

form {
  text-align:left;
  margin-left:20px;
}
h2 {
  margin-bottom:0px;
}
h3 {
  margin-left: -10px;
  text-align:left;
}
label {
float:left;
width: 40%;
       font-weight:bold;
}
input {
width: 60%;
       text-align:center;
       font-size:large;
}
@media screen and (max-width: 800px) {
  h2 {
    margin-bottom:0px;
    text-align:center;
  }  form {
    text-align:left;
    margin-left:0px;
  }
  .column {
float: none;
width: 100%;
  }
}
</style>
<div class="row">
<div class="column first">
<form action="edit_scripts.php" method="POST">
<?php
function printFoldersRecursive($dir) {
    $allfiles = array();

    // Open a directory, and read its contents
    if (is_dir($dir)){
        if ($dh = opendir($dir)){
            while (($file = readdir($dh)) !== false){
                if($file != '.' && $file != '..' && $file != 'By_Date' && $file != 'By_Common_Name' && $file != 'By_Scientific_Name' && $file != 'Charts' && $file != 'Processed' && $file != '.git' && $file != '.github' && $file != 'phpsysinfo' && $file != '.DS_Store'){
                    $allfiles[] = $file;
                }
            }
            closedir($dh);
        }
    }

    sort($allfiles);

    foreach($allfiles as $file) {
      echo "<input name=\"script\" type=\"submit\" value=\"$file\">";
        if(is_dir($dir.'/'.$file)){
            printFoldersRecursive($dir.'/'.$file);
        }
    }
}

printFoldersRecursive('../../BirdSongs/Extracted');
?>
</form>
</div>
</div>
</body>
