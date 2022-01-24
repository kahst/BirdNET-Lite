<?php
if($_POST['Submit']){
$open = fopen("scripts/birdnet.conf","w+");
$text = $_POST['update'];
fwrite($open, $text);
fclose($open);
echo "File updated.<br />"; 
echo "File:<br />";
$file = file("scripts/birdnet.conf");
foreach($file as $text) {
echo $text."<br />";
}
}else{
$file = file("scripts/birdnet.conf");
echo "<form style=\"text-align:center;\" action=\"".$PHP_SELF."\" method=\"post\">";
echo "<textarea Name=\"update\" cols=\"80\" rows=\"100\">";
foreach($file as $text) {
echo $text;
} 
echo "</textarea>";
echo "<input name=\"Submit\" type=\"submit\" value=\"Update\" />\n
</form>";
}
?>
