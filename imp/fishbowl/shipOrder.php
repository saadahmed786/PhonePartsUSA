<?php

require_once("applicationTop.php");

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("../config.php");

$order_url = $host_path . "/fishbowl/fetchOrders.php?order_type=SAVESO&shipped=1";
$ch = curl_init();
curl_setopt($ch , CURLOPT_URL , $order_url);
curl_setopt($ch , CURLOPT_TIMEOUT, 10);
curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
$orders = curl_exec($ch);

if($orders == 'NO'){
	echo "no new orders";
	exit;
}

$orders = json_decode($orders , true);

$successIds = array();
$nomappingIds = array();
$errorMessage = array();

foreach($orders as $order){
	$order_id = "S".$order['order_id'];
	
	$result = $fbapi->ShipRequest($order_id);

	$FbiMsgsRsStatus = $result['FbiMsgsRs']['@attributes']['statusCode'];
	$attributes      = $result['FbiMsgsRs'][0]->attributes();
	$SaveSORsStatus  = $attributes['statusCode'];

	print $FbiMsgsRsStatus . " -- " . $SaveSORsStatus . '--'. $order['order_id']. "--". $order['store_type']. "<br />";

	if ($FbiMsgsRsStatus == 1000 &&  $SaveSORsStatus == 1000) {
		$successIds[] = "'" . $order['order_id'] . "'";
	}
	else{
		$SaveSORsMessage = $attributes['statusMessage'];
		$nomappingIds[]  = "'" . $order['order_id'] . "'";
		$errorMessage[$order['order_id']] = array($SaveSORsStatus,$SaveSORsMessage,$FbiMsgsRsStatus,$result);
	}
}

if(($successIds and count($successIds) > 0) || ($nomappingIds and count($nomappingIds) > 0)){
	$successIdsStr = implode(",",$successIds);
	$nomappingIdsStr = implode(",",$nomappingIds);

	$updateUrl = $host_path . "/fishbowl/mark_shipped.php";

	$ch = curl_init();
	curl_setopt($ch , CURLOPT_URL , $updateUrl);
	curl_setopt($ch , CURLOPT_POST , 1);
	curl_setopt($ch , CURLOPT_POSTFIELDS , array('successIdsStr' => $successIdsStr ,
                                                 'nomappingIdsStr' => $nomappingIdsStr,
                                                 'errorMessage' => json_encode($errorMessage)
	));
	curl_setopt($ch , CURLOPT_TIMEOUT, 30);
	curl_exec($ch);
}

echo "success";