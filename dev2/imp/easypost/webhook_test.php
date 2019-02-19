<?php
header("HTTP/1.1 200 OK");
set_time_limit(0);
//include "../config.php";
//include "../inc/functions.php";
require_once("lib/easypost.php");
\EasyPost\EasyPost::setApiKey('ZMn4BcLGpdi3qSZzYbyxyw');
$obj = file_get_contents('php://input');
if($obj)
{
//$obj = json_decode($obj,true);
	
	file_put_contents('logs.txt', PHP_EOL."Response:".($obj),FILE_APPEND);	
}
?>