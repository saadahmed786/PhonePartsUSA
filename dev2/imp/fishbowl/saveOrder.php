<?php

require_once("applicationTop.php");

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("../config.php");

$order_url = $host_path . "/fishbowl/fetchOrders.php?order_type=SAVESO";
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
$OrderSoNumbers = array();
$nomappingIds = array();
$errorMessage = array();

foreach($orders as $order){
	$first_name  = $fbapi->replaceSpecial($order['first_name']);
	$first_name  = preg_replace("/[^a-zA-Z0-9]/is","",$first_name);
	
	$last_name   = $fbapi->replaceSpecial($order['last_name']);
	$last_name   = preg_replace("/[^a-zA-Z0-9]/is","",$last_name);
	
	$name = $first_name." ".$last_name;
		
	$getCustomer = $fbapi->getCustomer("Get",$name);
	$customer    = @$getCustomer['FbiMsgsRs']['CustomerGetRs']['Customer'];
		
	//print_r($cusotmer); exit;
	if(!$customer){
		$customer_result = $fbapi->addCustomer($order);

		$attributes = $customer_result['FbiMsgsRs'][0]->attributes();
		//print_r($attributes); exit;

		if($attributes['statusCode'] == 3001){
			$first_name = $first_name."1";
			$name = $first_name." ".$last_name;
				
			$order['first_name'] = $first_name;
			$customer_result = $fbapi->addCustomer($order);
		}
	}
	else{
		//customer exist
	}
		
	// add order
	$order_result = $fbapi->saveSOOrder($order);
	$result = $order_result['result'];

	$FbiMsgsRsStatus = $result['FbiMsgsRs']['@attributes']['statusCode'];
	$SaveSORsStatus  = $result['FbiMsgsRs']['SaveSORs']['@attributes']['statusCode'];

	$SaveSORsMessage = false;
	if(!$SaveSORsStatus and $result['FbiMsgsRs'][0]){
		$attributes = $result['FbiMsgsRs'][0]->attributes();
		$SaveSORsStatus  = $attributes['statusCode'];
		$SaveSORsMessage = $attributes['statusMessage'];
	}

	print $FbiMsgsRsStatus . " -- " . $SaveSORsStatus . '--'. $order['order_id']. "--". $order['store_type']. "<br />";

	if ($FbiMsgsRsStatus == 1000 &&  $SaveSORsStatus == 1000) {
		$successIds[] = "'" . $order['order_id'] . "'";
		if($result['FbiMsgsRs']['SaveSORs']['SalesOrder']['Number']){
			$OrderSoNumbers[$order['order_id']] = array(
                    'SoNumber' => $result['FbiMsgsRs']['SaveSORs']['SalesOrder']['Number'],
                    'Items'    => $order_result['items']
			);
		}
	}
	elseif($FbiMsgsRsStatus == 1000 &&  $SaveSORsStatus == 2100){
		//model not exist in fishbowl software
		$nomappingIds[] = "'" . $order['order_id'] . "'";
		$errorMessage[$order['order_id']] = array($SaveSORsStatus,$SaveSORsMessage,$FbiMsgsRsStatus,$order_result,$customer_result);
	}
	else{
		$nomappingIds[] = "'" . $order['order_id'] . "'";
		$errorMessage[$order['order_id']] = array($SaveSORsStatus,$SaveSORsMessage,$FbiMsgsRsStatus,$order_result,$customer_result);
		file_put_contents("APILOG.txt" , "SAVE SO Response {$order['order_id']} - ". print_r($order_result,true) . "\n\n" , FILE_APPEND);
	}
}

if(($successIds and count($successIds) > 0) || ($nomappingIds and count($nomappingIds) > 0)){
	$successIdsStr = implode(",",$successIds);
	$nomappingIdsStr = implode(",",$nomappingIds);

	$updateUrl = $host_path . "/fishbowl/updateOrders.php";

	$ch = curl_init();
	curl_setopt($ch , CURLOPT_URL , $updateUrl);
	curl_setopt($ch , CURLOPT_POST , 1);
	curl_setopt($ch , CURLOPT_POSTFIELDS , array('successIdsStr' => $successIdsStr ,
                                                 'OrderSoNumbers' => json_encode($OrderSoNumbers) , 
                                                 'nomappingIdsStr' => $nomappingIdsStr,
                                                 'errorMessage' => json_encode($errorMessage)
	));
	curl_setopt($ch , CURLOPT_TIMEOUT, 30);
	curl_exec($ch);
}

echo "success";