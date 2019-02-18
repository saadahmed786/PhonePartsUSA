<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");
date_default_timezone_set("America/Los_Angeles");


include_once("../config.php");
include_once("../inc/functions.php");
include '../crons/paypal/paypal.php';

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
		$oldOrderID = $OrderID;
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
		$CustName =  $db->func_escape_string($orderObject->billing_address->first_name) . " ". $db->func_escape_string($orderObject->billing_address->last_name);
		$City  =  $orderObject->billing_address->city;
		$State =  $orderObject->billing_address->state;
		$Country =  $orderObject->billing_address->country;
		$Zip   =  $orderObject->billing_address->zip;
		$Phone =  $orderObject->billing_address->phone;
		
		$customer_name = $db->func_escape_string($CustName);
		$transaction_id = '';

		$orderExist = $db->func_query_first_cell("select id from inv_orders where order_id = '".$db->func_escape_string($OrderID)."'");
		if(!$orderExist){
			$db->db_exec("insert into inv_orders(order_id,order_date,order_price,paid_price,order_status,email,store_type,customer_name,dateofmodification,shipping_amount)
				values ('$OrderID','".$order_date."','$OrderTotal','$OrderTotal','$OrderStatus','$Email','bigcommerce','$customer_name','".date('Y-m-d H:i:s')."','".(float)$shipping_cost."')");

			$db->db_exec("insert into inv_orders_details(order_id,first_name,phone_number,address1,city,state,country,zip,payment_method,shipping_cost,dateofmodification,bill_address1,bill_city,bill_state,bill_country,bill_zip)
				values ('$OrderID','".$CustName."','$Phone','$Address','$City','$State','$Country','$Zip','$PaymentMethod','$shipping_cost','".date('Y-m-d H:i:s')."','$Address','$City','$State','$Country','$Zip')");

			if(strtolower($PaymentMethod)=='paypal') {

				$paypal_last_cron = $db->func_query_first("select last_cron_time from inv_cron where store_type = 'PayPal'");

				if(@intval($paypal_last_cron['last_cron_time'])){
					$paypal_last_cron_date = $paypal_last_cron['last_cron_time'];
					$paypal_last_cron_date = date('Y-m-d\TH:i:s', strtotime($paypal_last_cron_date));
				} else {
					$paypal_last_cron_date = date('Y-m-d\TH:i:s', time() - (1*24*60*60));
				}

				$paypal_end_cron_date = gmdate('Y-m-d\TH:i:s');

				$api_username  = 'admin_api1.replacementlcds.com';
				$api_password  = 'RYV6DNWNNLVSY5BP';
				$api_signature = 'AKDJMrcfZ1rLAY1K5iKwGm86PLbiABK1CxVKkOQqmclTR72aK8GJDvEW';

				$paypal = new PaypalPayment($api_username , $api_password , $api_signature);


				$transaction = $paypal->getTransactionByInvoice($paypal_last_cron_date , '#'.$oldOrderID);
				if( $transaction['L_TRANSACTIONID0']) {
					$transaction_id = $transaction['L_TRANSACTIONID0'];


					$transactionDetail = $paypal->getTransctionDetails($transaction_id);

					$transactionRow = array();
					$transactionRow['auth_code'] = 0;
					$transactionRow['transaction_id'] = $transaction_id;
					$transactionRow['transaction_fee'] = $transaction['L_FEEAMT0'];
					$transactionRow['avs_code']       = urldecode($transactionDetail['PAYMENTSTATUS']);
					$transactionRow['payment_source'] = "PayPal";
					$transactionRow['street_address'] = $db->func_escape_string(urldecode($transactionDetail['SHIPTOSTREET']));
					$transactionRow['zipcode'] = urldecode($transactionDetail['SHIPTOZIP']);
					$transactionRow['is_address_verified'] = $transactionDetail['ADDRESSSTATUS'];
					$transactionRow['dateofmodification'] = date('Y-m-d H:i:s');

					$db->func_array2update("inv_orders",$transactionRow," order_id = '$OrderID' ");
				}

			} else {
				$paypal_last_cron = $db->func_query_first("select last_cron_time from inv_cron where store_type = 'PayPal'");

				if(@intval($paypal_last_cron['last_cron_time'])){
					$paypal_last_cron_date = $paypal_last_cron['last_cron_time'];
					$paypal_last_cron_date = date('Y-m-d\TH:i:s', strtotime($paypal_last_cron_date));
				}
				else{
					$paypal_last_cron_date = date('Y-m-d\TH:i:s', time() - (1*24*60*60));
				}

				$paypal_end_cron_date = gmdate('Y-m-d\TH:i:s');

				$api_username = 'paypal_api1.phonepartsusa.com';
				$api_password = 'A3UTLAF89676LVFW';
				$api_signature = 'AWEus9lWHhjbjG6qaUICKluU-eFdAZ2ufK7YWkgbrqeiaBiq1y7wOc0j';

				$paypal = new PaypalPayment($api_username , $api_password , $api_signature);


				$transaction = $paypal->getTransactionByInvoice($paypal_last_cron_date , '#'.$oldOrderID);
				if( $transaction['L_TRANSACTIONID0'])
				{
					$transaction_id = $transaction['L_TRANSACTIONID0'];


					$transactionDetail = $paypal->getTransctionDetails($transaction_id);

					$transactionRow = array();
					$transactionRow['auth_code'] = 0;
					$transactionRow['transaction_id'] = $transaction_id;
					$transactionRow['transaction_fee'] = $transaction['L_FEEAMT0'];
					$transactionRow['avs_code']       = urldecode($transactionDetail['PAYMENTSTATUS']);
					$transactionRow['payment_source'] = "Payflow";
					$transactionRow['street_address'] = $db->func_escape_string(urldecode($transactionDetail['SHIPTOSTREET']));
					$transactionRow['zipcode'] = urldecode($transactionDetail['SHIPTOZIP']);
					$transactionRow['is_address_verified'] = $transactionDetail['ADDRESSSTATUS'];
					$transactionRow['dateofmodification'] = date('Y-m-d H:i:s');

					$db->func_array2update("inv_orders",$transactionRow," order_id = '$OrderID' ");
				}
			}



			$order_items = array();
			$order_items = Bigcommerce::getCollection($orderObject->products->resource);

			if($order_items){
				$sub_total=0.00;
				$items_true_cost = 0.00;
				foreach($order_items as $order_item){
					

					$order_item = $order_item->getCreateFields();
					$product_sku    = $db->func_escape_string($order_item->sku);
					$product_price  = $db->func_escape_string($order_item->total_inc_tax);
					$product_price  = str_replace(',','',(string)$product_price);

					$sub_total = (float)$sub_total + (float)$product_price;
					
					$product_unit  = $db->func_escape_string($order_item->price_inc_tax);
					$product_unit  = str_replace(',','',(string)$product_unit);


					$orderItemData = array();
					$orderItemData['order_id'] = $OrderID;
					$orderItemData['order_item_id'] = $order_item->product_id;
					$orderItemData['product_sku']   = $product_sku;
					$orderItemData['product_qty']   = (int)$order_item->quantity;
					$orderItemData['product_unit'] = $product_unit;
					$orderItemData['product_price'] = $product_price;
					$orderItemData['product_true_cost'] = getTrueCost($product_sku);
					$orderItemData['dateofmodification'] = date('Y-m-d H:i:s');

					//check if SKU is KIT SKU
					$kit_skus = $db->func_query_first("select * from inv_kit_skus where kit_sku = '$product_sku'");
					if($kit_skus){
						$kit_skus_array = explode(",",$kit_skus['linked_sku']);
						for($_i=1;$_i<=$order_item->quantity;$_i++)
						{
							$z=0;
							foreach($kit_skus_array as $kit_skus_row){
								$orderItemData['product_sku']  = $kit_skus_row;
								$orderItemData['product_true_cost'] = getTrueCost($kit_skus_row);
								$orderItemData['product_qty'] =  1;
								$orderItemData['product_unit'] = $product_unit;
								$orderItemData['product_price'] = $product_unit;

								


								if($z>0){
									$orderItemData['product_unit'] = 0.00;
									$orderItemData['product_price'] = 0.00;
									$orderItemData['product_true_cost'] = 0.00;
									$orderItemData['product_qty'] =  1;
								}

								$items_true_cost = (float)$items_true_cost + ((float)$orderItemData['product_true_cost']*(int)$orderItemData['product_qty']);


								$db->func_array2insert("inv_orders_items",$orderItemData);
								saveInventory($orderItemData['product_sku'], $orderItemData['product_qty']);
								$z++;
							}
						}
						
						//mark kit sku need_sync on all marketplaces
						$db->db_exec("update inv_kit_skus SET need_sync = 1 where kit_sku = '$product_sku'");
					} else{
						$items_true_cost = (float)$items_true_cost + ((float)$orderItemData['product_true_cost']*(int)$orderItemData['product_qty']);

						saveInventory($orderItemData['product_sku'], $orderItemData['product_qty']);
						$db->func_array2insert("inv_orders_items",$orderItemData);
					}
				}
				$db->db_exec("UPDATE inv_orders SET sub_total='".(float)$sub_total."',items_true_cost='".(float)$items_true_cost."' WHERE order_id='".$OrderID."'");
			}
		} else{
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
		$iorderID = $OrderID;
		$query = "select o.* , od.* from inv_orders o inner join inv_orders_details od on o.order_id = od.order_id where order_status NOT IN ('Estimate','On Hold') and ss_ignore=0 and o.order_id = '$iorderID' group by o.order_id";
		$iorder = $db->func_query_first($query);

		if ($iorder) {
			whiteList($iorder, 1);
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