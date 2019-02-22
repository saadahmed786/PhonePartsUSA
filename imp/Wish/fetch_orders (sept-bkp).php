<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");
date_default_timezone_set("America/Los_Angeles");

include_once("../db.php");
include_once("../config.php");

include_once 'keys.php';
include_once 'Wish-Merchant-API-master/vendor/autoload.php';

use Wish\WishClient;
$client = new WishClient($api_token,'prod');

$last_cron_date = $db->func_query_first_cell("select last_cron_date from wish_credential");
if(!intval($last_cron_date)){
	$last_cron_date = date('Y-m-d', strtotime('-1 day'));
}
else{
	$last_cron_date = date('Y-m-d', (strtotime($last_cron_date) - (24*60*60)));
}

$startDate = $last_cron_date;
$endDate   = date('Y-m-d H:i:s');

print $startDate . " -- " . $endDate;

try {
	$orders = $client->getAllChangedOrdersSince($startDate);
	if($orders){
		foreach($orders as $order){
			ManageOrder($order);
		}
	}

	$db->db_exec("update wish_credential SET last_cron_date = '$endDate'");
}
catch(Wish\Exception\ServiceResponseException $error) {
	echo $error->getCode();
	echo $error->getMessage();
}
catch(Wish\Exception\ConnectionException $error) {
	echo $error->getCode();
	echo $error->getMessage();
}

function ManageOrder($orderObject){
	global $db;

	print_r($orderObject); exit;

	if($orderObject){
		$OrderID  = $orderObject->order_id;
		$OrderID  = "WL".$OrderID;

		$OrderTotal  = $orderObject->order_total;
		$order_date  = $orderObject->order_time;
		if($order_date){
			$order_date = mySqlDate($order_date);

			//PST time
			$order_date = date('Y-m-d H:i:s', (strtotime($order_date) - (8*60*60)));
		}

		$OrderStatus = $orderObject->state;
		$ship_date   = $orderObject->last_updated;
		$ship_date   = mySqlDate($ship_date);

		$paid_date = '';
		if($OrderStatus == 'Pending' || $OrderStatus == 'Awaiting Payment' || $OrderStatus == 'Incomplete'){
			//skip the order
			return 2;
		}

		$shipping_cost = $orderObject->shipping_cost;
		$PaymentMethod = $orderObject->payment_method;

		$Address  =  $orderObject->ShippingDetail->street_address1 . " " . $orderObject->ShippingDetail->street_address2;
		$Email    =  $orderObject->ShippingDetail->email;
		$CustName =  $orderObject->ShippingDetail->name;
		$City  =  $orderObject->ShippingDetail->city;
		$State =  $orderObject->ShippingDetail->state;
		$Country =  $orderObject->ShippingDetail->country;
		$Zip   =  $orderObject->ShippingDetail->zip;
		$Phone =  $orderObject->ShippingDetail->phone_number;

		$orderExist = $db->func_query_first_cell("select id from inv_orders where order_id = '".mysql_real_escape_string($OrderID)."'");
		if(!$orderExist){
			$db->db_exec("insert into inv_orders(order_id,order_date,order_price,order_status,email,store_type,dateofmodification)
			                  values ('$OrderID','".$order_date."','$OrderTotal','$OrderStatus','$Email','wish','".date('Y-m-d H:i:s')."')");

			$db->db_exec("insert into inv_orders_details(order_id,first_name,phone_number,address1,city,state,country,zip,payment_method,shipping_cost,dateofmodification)
			                   values ('$OrderID','".$CustName."','$Phone','$Address','$City','$State','$Country','$Zip','$PaymentMethod','$shipping_cost','".date('Y-m-d H:i:s')."')");

			$product_sku    = mysql_real_escape_string($orderObject->sku);
			$product_price  = mysql_real_escape_string($orderObject->price);
			$product_price  = str_replace(',','',(string)$product_price);

			$orderItemData = array();
			$orderItemData['order_id'] = $OrderID;
			$orderItemData['order_item_id'] = $orderObject->transaction_id;
			$orderItemData['product_sku']   = $product_sku;
			$orderItemData['product_qty']   = (int)$orderObject->quantity;
			$orderItemData['product_price'] = $product_price;
			$orderItemData['dateofmodification'] = date('Y-m-d H:i:s');

			//check if SKU is KIT SKU
			$kit_skus = $db->func_query_first("select * from inv_kit_skus where kit_sku = '$product_sku'");
			if($kit_skus){
				$kit_skus_array = explode(",",$kit_skus['linked_sku']);
				foreach($kit_skus_array as $kit_skus_row){
					$orderItemData['product_sku']  = $kit_skus_row;
					$db->func_array2insert("inv_orders_items",$orderItemData);
				}
			}
			else{
				$db->func_array2insert("inv_orders_items",$orderItemData);
			}
		}
	}
	else{
		$db->db_exec("Update inv_orders SET order_price = '$OrderTotal', store_type = 'wish' , order_status = '$OrderStatus' Where id = '$orderExist'");
	}

	return 1;
}

function mySqlDate($date){
	$date  = str_ireplace(" +0000","",$date);
	$mysql_date = date('Y-m-d H:i:s',strtotime($date));
	return $mysql_date;
}

if($_REQUEST['m'] == 1){
	$_SESSION['message'] = "Order imported successfully";
	header("Location:".$host_path."order.php");
}

echo "done";