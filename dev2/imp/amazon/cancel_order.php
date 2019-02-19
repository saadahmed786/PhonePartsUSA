<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("../config.php");
include_once("amazon_config.php");
include_once 'AmazonAPI.php';

$merchantInfo = $db->func_query("Select * from amazon_credential order by dateofmodifications DESC");
if(!@$merchantInfo){
	echo "No merchant exist";
	exit;
}

foreach($merchantInfo as $amazonAccount){
	$amazon_credential_id = $amazonAccount['id'];
	$merchant_id     = $amazonAccount['merchant_id'];
	$market_place_id = $amazonAccount['market_place_id'];
	$aws_access_key  = $amazonAccount['aws_access_key'];
	$aws_secret_key  = $amazonAccount['aws_secret_key'];
	$prefix = $amazonAccount['prefix'];

	if($prefix == 'ZM'){
		$orders = $db->func_query("SELECT * FROM inv_orders WHERE order_status = 'Canceled' and amazon_cancel_order = 1 and store_type = 'amazon' and order_id like 'ZM%'");
	}
	else{
		$orders = $db->func_query("SELECT * FROM inv_orders WHERE order_status = 'Canceled' and amazon_cancel_order = 1 and store_type = 'amazon' and order_id not like 'ZM%'");
	}

	foreach($orders as $order){
		$order_items = $db->func_query("SELECT * FROM inv_orders_items WHERE order_id='".$order['order_id']."'");
		$items_array = array();
		foreach($order_items as $item){
			$items_array[] = array('amazon_item_id' => $item['order_item_id']);
		}

		$AmazonAPI = new AmazonAPI($market_place_id , $aws_access_key , $aws_secret_key);
		$xml = $AmazonAPI->CancelOrderXml($items_array , $merchant_id);

		$AmazonAPI->SendRequest($xml , $merchant_id,'_POST_ORDER_ACKNOWLEDGEMENT_DATA_');
		$db->db_exec("update inv_orders set amazon_cancel_order = 0 WHERE order_id='".$order['order_id']."'");
	}
}
?>