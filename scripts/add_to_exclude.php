<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
$file='/home/pi/BirdNET-Pi/exclude_species_list.txt';
$str=file_get_contents("$file");
$str = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $str);
file_put_contents("$file", "$str");

if (isset($_POST['species'])) 
	foreach ($_POST['species'] as $selectedOption)
  file_put_contents("/home/pi/BirdNET-Pi/exclude_species_list.txt", $selectedOption."\n", FILE_APPEND);
header("Location: {$_SERVER['HTTP_REFERER']}");
exit;
?>
