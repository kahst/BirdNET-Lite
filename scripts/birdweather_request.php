<?php
if (file_exists('thisrun.txt')) {
  $config = parse_ini_file('thisrun.txt');
} elseif (file_exists('firstrun.ini')) {
  $config = parse_ini_file('firstrun.ini');
} 
$template = file_get_contents("./scripts/email_template");

foreach($config as $key => $value)
{
    $template = str_replace('{{ '.$key.' }}', $value, $template);
}
echo $template;
?>

