<?php
if (file_exists('/home/pi/BirdNET-Pi/thisrun.txt')) {
  $config = parse_ini_file('/home/pi/BirdNET-Pi/thisrun.txt');
} elseif (file_exists('/home/pi/BirdNET-Pi/firstrun.ini')) {
  $config = parse_ini_file('/home/pi/BirdNET-Pi/firstrun.ini');
} 
$template = file_get_contents("email_template.html");

foreach($config as $key => $value)
{
    $template = str_replace('{{ '.$key.' }}', $value, $template);
}
echo $template;
?>

