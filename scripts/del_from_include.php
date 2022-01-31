<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
$file='/home/pi/BirdNET-Pi/include_species_list.txt';
$str=file_get_contents("$file");
$str = preg_replace('/^\h*\v+/m', '', $str);
file_put_contents("$file", "$str");

if (isset($_POST['species'])) 
	foreach($_POST['species'] as $selectedOption) {
$content = file_get_contents("/home/pi/BirdNET-Pi/include_species_list.txt");
$newcontent = str_replace($selectedOption, "", "$content");
file_put_contents("/home/pi/BirdNET-Pi/include_species_list.txt", "$newcontent");
	}
$file='/home/pi/BirdNET-Pi/include_species_list.txt';
$str=file_get_contents("$file");
//$str = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $str);
$str = preg_replace('/^\h*\v+/m', '', $str);
file_put_contents("$file", "$str");
header("Location: {$_SERVER['HTTP_REFERER']}");
exit;
?>
