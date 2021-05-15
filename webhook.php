<?php
ini_set('max_execution_time', 300);
$scan = scandir("Bots");
$diff = array_diff($scan, ['.','..']);

foreach($diff as $value){
    $config = file_get_contents("Bots/".$value."/config.php");
    preg_match_all('/\$Token\s=\s"(.*?)";/', $config, $match);
    $token = $match[1][0];
    file_get_contents("http://api.telegram.org/bot".$token."/setWebHook?url=Https://tech-rashidi.ir/wp-admin/user/pv/".$value."/index.php");
}

Echo "End";
?>