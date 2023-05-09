<?php
if(file_exists('./scripts/common.php')){
    include_once "./scripts/common.php";
}else{
    include_once "./common.php";
}
$template = file_get_contents(getFilePath('email_template2'));

foreach($config as $key => $value)
{
    $template = str_replace('{{ '.$key.' }}', $value, $template);
}
echo $template;
?>

