<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");
date_default_timezone_set("America/Los_Angeles");

include_once("../config.php");
include_once("../inc/functions.php");

include_once 'big_keys.php';
include_once 'bigcommerce-api-php-master/vendor/autoload.php';

use Bigcommerce\Api\Client as Bigcommerce;

Bigcommerce::configure(array(
	'store_url' => 'https://www.replacementlcds.com',
	'username'	=> $username,
	'api_key'	=> $api_token
));

//Bigcommerce::setCipher('RC4-SHA');
Bigcommerce::setCipher('TLSv1');
Bigcommerce::verifyPeer(false);
Bigcommerce::failOnError();

$last_cron_date = $db->func_query_first_cell("select last_cron_date from bigcommerce_credential");
if(!intval($last_cron_date)){
	$last_cron_date = date('Y-m-d H:i:s', strtotime('-1 day'));
}
else{
	$last_cron_date = date('Y-m-d H:i:s', (strtotime($last_cron_date) - (24*60*60)));
}

$startDate = $last_cron_date;
$endDate   = date('Y-m-d H:i:s', strtotime('+12 Hours'));

print $startDate . " -- " . $endDate;

try {
	$page = 1;
	do{
		$filter = array("limit"=>100,"page"=>$page,"min_date_modified"=>$startDate,"max_date_modified"=>$endDate);
		$orders = Bigcommerce::getOrders($filter);
		if($orders){
			foreach($orders as $order){
				ManageOrder($order->getCreateFields());
			}
		}

		$page++;
	}
	while($orders);

	$db->db_exec("update bigcommerce_credential SET last_cron_date = '$endDate'");
}
catch(Bigcommerce\Api\Error $error) {
	echo $error->getCode();
	echo $error->getMessage();
}

function ManageOrder($orderObject){
	global $db;

	//print_r($orderObject); exit;

	if($orderObject){
		$OrderID  = $orderObject->id;
		$OrderID  = "RL".$OrderID;

		$OrderTotal  = $orderObject->total_inc_tax;
		$CompleteStatus = (string)$orderObject->status;

		$order_date = $orderObject->date_created;
		if($order_date){
			$order_date = mySqlDate($order_date);

			//PST time
			$order_date = date('Y-m-d H:i:s', (strtotime($order_date) - (8*60*60)));
		}
		
		$date_modified = $orderObject->date_modified;
		if($date_modified){
			$date_modified = mySqlDate($date_modified);
		
			//PST time
			$date_modified = date('Y-m-d H:i:s', (strtotime($date_modified) - (8*60*60)));
		}

		$OrderStatus = $orderObject->status;
		$ship_date   = $orderObject->date_shipped;
		$ship_date   = mySqlDate($ship_date);

		$paid_date = '';
		if($OrderStatus == 'Pending' || $OrderStatus == 'Awaiting Payment' || $OrderStatus == 'Incomplete'){
			//skip the order
			return 2;
		}

		$shipping_additional_cost = $orderObject->handling_cost_inc_tax;
		$shipping_cost = $orderObject->shipping_cost_inc_tax;
		$shipping_cost = $shipping_cost + $shipping_additional_cost;
		$PaymentMethod = $orderObject->payment_method;

		$Address  =  $orderObject->billing_address->street_1 . " " . $orderObject->billing_address->street_2;
		$Email    =  $orderObject->billing_address->email;
		$CustName =  $orderObject->billing_address->first_name . " ". $orderObject->billing_address->last_name;
		$City  =  $orderObject->billing_address->city;
		$State =  $orderObject->billing_address->state;
		$Country =  $orderObject->billing_address->country;
		$Zip   =  $orderObject->billing_address->zip;
		$Phone =  $orderObject->billing_address->phone;
		
		$customer_name = $db->func_escape_string($CustName);

		$orderExist = $db->func_query_first_cell("select id from inv_orders where order_id = '".$db->func_escape_string($OrderID)."'");
		if(!$orderExist){
			$db->db_exec("insert into inv_orders(order_id,order_date,order_price,paid_price,order_status,email,store_type,customer_name,dateofmodification)
			                  values ('$OrderID','".$order_date."','$OrderTotal','$OrderTotal','$OrderStatus','$Email','bigcommerce','$customer_name','".date('Y-m-d H:i:s')."')");

			$db->db_exec("insert into inv_orders_details(order_id,first_name,phone_number,address1,city,state,country,zip,payment_method,shipping_cost,dateofmodification)
			                   values ('$OrderID','".$CustName."','$Phone','$Address','$City','$State','$Country','$Zip','$PaymentMethod','$shipping_cost','".date('Y-m-d H:i:s')."')");

			$order_items = array();
			$order_items = Bigcommerce::getCollection($orderObject->products->resource);
			if($order_items){
				foreach($order_items as $order_item){
					$order_item = $order_item->getCreateFields();
					$product_sku    = $db->func_escape_string($order_item->sku);
					$product_price  = $db->func_escape_string($order_item->total_inc_tax);
					$product_price  = str_replace(',','',(string)$product_price);
						
					$orderItemData = array();
					$orderItemData['order_id'] = $OrderID;
					$orderItemData['order_item_id'] = $order_item->product_id;
					$orderItemData['product_sku']   = $product_sku;
					$orderItemData['product_qty']   = (int)$order_item->quantity;
					$orderItemData['product_price'] = $product_price;
					$orderItemData['product_true_cost'] = getTrueCost($product_sku);
					$orderItemData['dateofmodification'] = date('Y-m-d H:i:s');

					//check if SKU is KIT SKU
					$kit_skus = $db->func_query_first("select * from inv_kit_skus where kit_sku = '$product_sku'");
					if($kit_skus){
						$kit_skus_array = explode(",",$kit_skus['linked_sku']);
						$z=0;
						foreach($kit_skus_array as $kit_skus_row){
							$orderItemData['product_sku']  = $kit_skus_row;
							$orderItemData['product_true_cost'] = getTrueCost($kit_skus_row);
							if($z>0){
								$orderItemData['product_price'] = 0.00;
								$orderItemData['product_true_cost'] = 0.00;
							}
							$db->func_array2insert("inv_orders_items",$orderItemData);
							$z++;
						}
						
						//mark kit sku need_sync on all marketplaces
						$db->db_exec("update inv_kit_skus SET need_sync = 1 where kit_sku = '$product_sku'");
					}
					else{
						$db->func_array2insert("inv_orders_items",$orderItemData);
					}
				}
			}
		}
		else{
			if($OrderStatus == 'Cancelled'){
				$isReturnExist = $db->func_query_first_cell("select id from inv_return_orders where order_id = '$OrderID'");
				if(!$isReturnExist){
					$returnDate = $date_modified;
					$db->db_exec("insert into inv_return_orders (order_id,email,order_price,order_date,return_date,status,store_type,dateofmodification)
							values ('$OrderID','$Email','$OrderTotal','$order_date','$returnDate','open','web','".date('Y-m-d H:i:s')."')");
				}
				else{
					$db->db_exec("update inv_return_orders SET is_updated = 1 , ignored = 0 , dateofmodification = '".date('Y-m-d H:i:s')."' where order_id = '$OrderID'");
				}
			}
			
			//fishbowl_uploaded = 0 ,
			$db->db_exec("Update inv_orders SET order_price = '$OrderTotal',paid_price='$OrderTotal', store_type = 'bigcommerce' , order_status = '$OrderStatus' Where id = '$orderExist'");
		}
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