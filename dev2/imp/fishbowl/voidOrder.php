<?php

require_once("applicationTop.php");

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("../config.php");

$order_url = $host_path . "/fishbowl/fetchOrders.php?order_type=VOIDSO";
$orders = file_get_contents($order_url);
if($orders == 'NO'){
	exit;
}

$orders = json_decode($orders , true);

$successIds = array();
foreach($orders as $order){
	// Get sales order list
	$fbapi->voidSOOrder($order['orderid']);

	// Check for error messages
	if ($fbapi->statusCode != 1000) {
		// Display error messages if it's not blank
		if (!empty($fbapi->statusMsg)) {
			echo $fbapi->statusMsg;
		}
	}
	else{
		$successIds[] = "'" . $order['orderid'] . "'";
	}
	
	file_put_contents("APILOG.txt" , "VOID SO Response {$order['orderid']} - ". print_r($fbapi,true) . "\n\n" , FILE_APPEND);
}


if($successIds and count($successIds) > 0 ){
	$successIdsStr = implode(",",$successIds);
	
	$updateUrl = $host_path . "/fishbowl/updateOrders.php";

	$ch = curl_init();
	curl_setopt($ch , CURLOPT_URL , $updateUrl);
	curl_setopt($ch , CURLOPT_POST , 1);
	curl_setopt($ch , CURLOPT_POSTFIELDS , array('successIdsStr' => $successIdsStr,'order_type' => 'return'));
	curl_setopt($ch , CURLOPT_TIMEOUT, 30);
	curl_exec($ch);
}

echo "succeess";