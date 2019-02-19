<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("../config.php");

$successIdsStr  = $_REQUEST['successIdsStr'];
$nomappingIdsStr = $_REQUEST['nomappingIdsStr'];
$errorMessage = json_decode($_REQUEST['errorMessage'],true);

if($successIdsStr){
	$db->db_exec("Update inv_orders SET fb_shipped = 1 , dateofmodification = '".date('Y-m-d H:i:s')."' where order_id IN ($successIdsStr)");
}

if($nomappingIdsStr){
	$db->db_exec("Update inv_orders SET ignored = 1 where order_id IN ($nomappingIdsStr)");
}

if(is_array($errorMessage) and count($errorMessage) > 0){
	foreach($errorMessage as $order_id => $error){
		$message .= "Order ID - $order_id - {$error[1][0]} <br />";

		$fb_error = array();
		$fb_error['error_code']     = $error[0][0];
		$fb_error['error_message']  = $error[1][0];
		$fb_error['request_name']   = 'saveSOOrder';
		$fb_error['order_id']       = $order_id;
		$fb_error['other_details']  = $db->func_escape_string(json_encode($error));
		$fb_error['dateofmodification'] = date('Y-m-d H:i:s');

		$db->func_array2insert("inv_fb_errors",$fb_error);
	}
}

echo "success";