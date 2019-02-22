<?php
header("HTTP/1.1 200 OK");
set_time_limit(0);
include "../config.php";
include "../inc/functions.php";
require_once("lib/easypost.php");
\EasyPost\EasyPost::setApiKey(EASYPOST_API);
$obj = file_get_contents('php://input');
if($obj)
{
$obj = json_decode($obj,true);
	if($obj['description']=='tracker.updated')
	{
		$result = $obj['result'];
		$db->db_exec("DELETE FROM inv_tracker_status WHERE tracker_id='".$result['id']."'");
		foreach($result['tracking_details'] as $detail)
 	{
 		$_array = array();
 	$_array['tracker_id'] = $result['id'];
 	$_array['message'] = $db->func_escape_string($detail['message']);
 	$_array['status'] = $db->func_escape_string($detail['status']);
 	$_array['datetime'] = $db->func_escape_string($detail['datetime']);
 	$_array['tracking_location'] = $db->func_escape_string(json_encode($detail['tracking_location']));
	$db->func_array2insert("inv_tracker_status", $_array);

 	}
	}
	file_put_contents('logs_live.txt', PHP_EOL."Response:".json_encode($obj),FILE_APPEND);	
}

?>