<?php
$timer=60;
header( "refresh:$timer;url=/viewdb.php" );
$handle = popen("tail -f /tmp/phpupdate.log", 'r');
while(!feof($handle)) {
    $buffer = fgets($handle);
    echo "$buffer<br/>\n";
    ob_flush();
    flush();
}
pclose($handle);
