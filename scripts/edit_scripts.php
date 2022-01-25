<?php

// configuration
$url = 'list_scripts.php';
$file = $_POST["script"];

// check if form has been submitted
if (isset($_POST['text']))
{
    // save the text contents
    file_put_contents($file, $_POST['text']);

    // redirect to form again
    header(sprintf('Location: %s', $url));
    printf('<a href="%s">Moved</a>.', htmlspecialchars($url));
    exit();
}

// read the textfile
$text = file_get_contents($file);

?>
<!-- HTML form -->
<head>
<style>
* {
  font-family: 'monospace';
}

a {
  text-decoration: none;
  color: black;
}

form {
  text-align: center;
}
</style>
<body style="background-color:rgb(119, 196, 135);">
<form style="height:95%" action="" method="post">
<input style="margin-left:-150px;" type="submit" value="Update" />
<input type="reset" value="Discard Changes" />
<button type="text"><a href="advanced.php">Back</a></button><br>
<textarea name="text" style="font-size:large;width:100%;height:95%;"><?php echo htmlspecialchars($text) ?></textarea>
</form>
</body>
