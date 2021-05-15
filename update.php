<?php
ini_s
flush();
error_reporting(0);
set_time_limit(0);
$scan = scandir("Bots");
$diff = array_diff($scan, [".",".."]);
foreach($diff as $value){
    copy("Source/index.php","Bots/$value/index.php");
    copy("Source/handler.php","Bots/$value/handler.php");
}
unlink("error_log");
?>