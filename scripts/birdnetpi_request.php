<?php
if (file_exists('/home/*/BirdNET-Pi/thisrun.txt')) {
  $config = parse_ini_file('/home/*/BirdNET-Pi/thisrun.txt');
} elseif (file_exists('/home/*/BirdNET-Pi/firstrun.ini')) {
  $config = parse_ini_file('/home/*/BirdNET-Pi/firstrun.ini');
} 
$template = file_get_contents("scripts/email_template2");

foreach($config as $key => $value)
{
    $template = str_replace('{{ '.$key.' }}', $value, $template);
}
echo $template;
?>

