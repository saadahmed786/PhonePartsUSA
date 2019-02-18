<?php

include_once("../config.php");
date_default_timezone_set("America/Los_Angeles");

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("amazon_config.php");

$merchantInfo = $db->func_query_first("Select id ,merchant_id,market_place_id, last_cron_date from amazon_credential order by dateofmodifications DESC");
if(!@$merchantInfo){
	echo "No merchant exist";
	exit;
}

$amazon_credential_id = $merchantInfo['id'];
$merchant_id = $merchantInfo['merchant_id'];
$market_place_id = $merchantInfo['market_place_id'];

$serviceUrl = "https://mws.amazonservices.com/Finances/2015-05-01";
$config = array (
   'ServiceURL' => $serviceUrl,
   'ProxyHost' => null,
   'ProxyPort' => -1,
   'ProxyUsername' => null,
   'ProxyPassword' => null,
   'MaxErrorRetry' => 3,
);

$service = new MWSFinancesService_Client(
AWS_ACCESS_KEY_ID,
AWS_SECRET_ACCESS_KEY,
APPLICATION_NAME,
APPLICATION_VERSION,
$config);

$orders = $db->func_query("select order_id , order_date from inv_orders where fee_fetched = 0 and ( store_type = 'amazon' || store_type = 'amazon_fba') and order_status = 'Shipped' and order_date > '2015-06-23 00:00:00' order by order_date desc");
if(!$orders){
	echo "no orders";
	exit;
}

foreach($orders as $order){
	$request = new MWSFinancesService_Model_ListFinancialEventsRequest();
	$request->setSellerId($merchant_id);
	$request->setAmazonOrderId($order['order_id']);

	try {
		$response = $service->ListFinancialEvents($request);

		$responseXML = $response->toXML();
		$responseObject = simplexml_load_string($responseXML);
		
		if($responseObject){
			$ShipmentEvent = $responseObject->ListFinancialEventsResult->FinancialEvents->ShipmentEventList->ShipmentEvent;
			$ShipmentFeeList = $ShipmentEvent->ShipmentFeeList;
			
			$db->db_exec("delete from inv_order_fees where order_id = '".$order['order_id']."'");
			
			if($ShipmentFeeList){
				foreach($ShipmentFeeList->ShipmentFee as $ItemFee){
					$order_fee = array();
					$order_fee['fee_type'] = $ItemFee->FeeType;
					$order_fee['fee'] = $ItemFee->FeeAmount->CurrencyAmount;
					$order_fee['order_id'] = $order['order_id'];
					
					$db->func_array2insert("inv_order_fees", $order_fee);
				}
			}
			
			$ItemFeeList = $ShipmentEvent->ShipmentItemList->ShipmentItem->ItemFeeList;
			if($ItemFeeList){
				foreach($ItemFeeList->FeeComponent as $ItemFee){
					$order_fee = array();
					$order_fee['fee_type'] = $ItemFee->FeeType;
					$order_fee['fee'] = $ItemFee->FeeAmount->CurrencyAmount;
					$order_fee['order_id'] = $order['order_id'];
					
					$db->func_array2insert("inv_order_fees", $order_fee);
				}
			}

			$db->db_exec("update inv_orders SET fee_fetched = 1 where order_id = '".$order['order_id']."'");
		}
	}
	catch (MWSFinancesService_Exception $ex) {
		echo("Caught Exception: " . $ex->getMessage() . "\n");
		echo("Response Status Code: " . $ex->getStatusCode() . "\n");
		echo("Error Code: " . $ex->getErrorCode() . "\n");
	}
}

echo "success";