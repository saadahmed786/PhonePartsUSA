<?php

set_time_limit(0);
include "../config.php";
include "../inc/functions.php";

$last_cron_time = $db->func_query_first_cell("select config_value from configuration where config_key = 'SHIPSTATION_LASTORDER_TIME'");

$stores = array("newegg"=>74747,"rakuten"=>74818,"opensky"=>53186,"newsears"=>74503,"bonanza"=>53158);
foreach($stores as $store_name => $store_id){
	getOrders($last_cron_time , $store_name , $store_id);
}

$currentDate = date('Y-m-d H:i:s');
$db->db_exec("update configuration SET config_value = '$currentDate' where config_key = 'SHIPSTATION_LASTORDER_TIME'");

function getOrders($start_date , $store_name , $store_id , $page = 1, $page_size = 100){
	global $db;

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://ssapi.shipstation.com/orders?modifyDateStart=".urlencode($start_date)."&page=$page&storeId=$store_id");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

	$authtoken = base64_encode("0d50ba42240844269473de9ba065873e:771f86ef07aa47b29e275175d00e6481");
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  		"Authorization:Basic $authtoken"
	));

	$response = curl_exec($ch);
	if($response){
		$response = json_decode($response,true);
		foreach($response['orders'] as $order){
			$prefix = getPrefix($store_name);
			addOrder($order , $store_name , $prefix);
		}

		if($response['pages'] > $page){
			getOrders($start_date , $store_name , $store_id , $page + 1);
		}

		return;
	}
	else{
		$error = curl_error($ch);
		print_r(curl_getinfo($ch));
	}
	curl_close($ch);
}

function addOrder($orderObject , $store_name , $prefix = 'NE') {
	global $db;

	$order_id = trim($orderObject['orderNumber']);
	$order_status = trim($orderObject['orderStatus']);

	$order_id = $prefix . $order_id;

	$order_date = trim($orderObject['orderDate']);
	$order_date = substr($order_date,0,19);

	//convert time GMT to PT = -8:00
	$order_date = date('Y-m-d H:i:s', strtotime($order_date) - (8 * 60 * 60));

	$payment_status = 'Cleared';
	$order_total = $orderObject['orderTotal'];
	$email   = $orderObject['customerEmail'];
	$carrier = $orderObject['carrierCode'];
	$carrier_class = $orderObject['serviceCode'];

	$products = $orderObject['items'];

	//if order is exist then do not process it again`
	$isExist = $db->func_query_first_cell("select id from inv_orders where order_id = '$order_id'");
	if ($isExist) {
		$orderUpdate = array();
		$orderUpdate['order_status']  = $order_status;
		$orderUpdate['dateofmodification'] = date('Y-m-d H:i:s');
		$db->func_array2update("inv_orders", $orderUpdate, " order_id = '$order_id'");

		if ($order_status == 'Cancelled') {
			$isReturnExist = $db->func_query_first_cell("select id from inv_return_orders where order_id = '$order_id'");
			if (!$isReturnExist) {
				$returnDate = trim($orderObject['modifyDate']);
				$returnDate = substr($returnDate,0,19);

				//convert time GMT to PT = -8:00
				$returnDate = date('Y-m-d H:i:s', strtotime($returnDate) - (8 * 60 * 60));
				$db->db_exec("insert into inv_return_orders (order_id,email,order_price,order_date,return_date,status,store_type,dateofmodification)
							values ('$order_id','$email','$order_total','$order_date','$returnDate',$store_name,'web','" . date('Y-m-d H:i:s') . "')");
			} else {
				$db->db_exec("update inv_return_orders SET is_updated = 1 , ignored = 0 , dateofmodification = '" . date('Y-m-d H:i:s') . "' where order_id = '$order_id'");
			}
		}

		return $isExist;
	}

	$order_details = array();
	if ($orderObject['shipTo']['phone']) {
		$order_details['phone_number'] = $orderObject['shipTo']['phone'];
	} elseif ($orderObject['billTo']['phone']) {
		$order_details['phone_number'] = $orderObject['billTo']['phone'];
	} else {
		$order_details['phone_number'] = false;
	}

	//firstname
	if ($orderObject['shipTo']['name'] == '') {
		$order_details['first_name'] = $orderObject['billTo']['name'];
	} else {
		$order_details['first_name'] = $orderObject['shipTo']['name'];
	}

	//lastname
	if ($orderObject['shipTo']['name'] == '') {
		$order_details['last_name'] = '';
	} else {
		$order_details['last_name'] = '';
	}

	//address 1 && address 2
	if ($orderObject['shipTo']['street1'] == '' && $orderObject['shipTo']['street2'] == '') {
		$order_details['address1'] = $orderObject['billTo']['street1'];
		$order_details['address2'] = $orderObject['billTo']['street2'];
	} else {
		$order_details['address1'] = $orderObject['shipTo']['street1'];
		$order_details['address2'] = $orderObject['shipTo']['street2'];
	}

	//city
	if ($orderObject['shipTo']['city'] == '') {
		$order_details['city'] = $orderObject['billTo']['city'];
	} else {
		$order_details['city'] = $orderObject['shipTo']['city'];
	}

	//state
	if ($orderObject['shipTo']['state'] == '') {
		$order_details['state'] = $orderObject['billTo']['state'];
	} else {
		$order_details['state'] = $orderObject['shipTo']['state'];
	}

	//country
	if ($orderObject['shipTo']['country'] == '') {
		$order_details['country'] = $orderObject['billTo']['country'];
	} else {
		$order_details['country'] = $orderObject['shipTo']['country'];
	}

	//zip
	if ($orderObject['shipTo']['postalCode'] == '') {
		$order_details['zip'] = $orderObject['billTo']['postalCode'];
	} else {
		$order_details['zip'] = $orderObject['shipTo']['postalCode'];
	}

	$order_details['payment_method'] = $orderObject['paymentMethod'];
	if ($payment_status != 'Cleared') {
		//if payment is pending for order then it will process again in next cron run
		return false;
	} else {
		$customer_name = $order_details['first_name'] . " " . $order_details['last_name'];
		$customer_name = $db->func_escape_string($customer_name);

		$order = array();
		$order['order_id']   = $order_id;
		$order['order_date'] = $order_date;
		$order['order_status'] = $order_status;
		$order['order_price']  = $order_total;
		$order['status'] = 'open';
		$order['email'] = $email;
		$order['store_type'] = $store_name;
		$order['customer_name'] = $customer_name;
		$order['dateofmodification'] = date('Y-m-d H:i:s');

		$InsertID = $db->func_array2insert("inv_orders", $order);

		$order_detail = array();
		$order_detail['shipping_cost']   = $orderObject['shippingAmount'];
		$order_detail['shipping_method'] = $carrier_class;
		$order_detail['payment_method']  = $orderObject['paymentMethod'];
		$order_detail['first_name'] = $db->func_escape_string($order_details['first_name']);
		$order_detail['last_name']  = $db->func_escape_string($order_details['last_name']);
		$order_detail['address1']   = $db->func_escape_string($order_details['address1']);
		$order_detail['address2']   = $db->func_escape_string($order_details['address2']);
		$order_detail['city']  = $order_details['city'];
		$order_detail['state'] = $order_details['state'];
		$order_detail['country'] = $order_details['country'];
		$order_detail['zip'] = $order_details['zip'];
		$order_detail['phone_number'] = $order_details['phone_number'];
		$order_detail['order_id'] = $order_id;
		$order_detail['dateofmodification'] = date('Y-m-d H:i:s');

		$db->func_array2insert("inv_orders_details", $order_detail);

		if (is_array($products) and sizeof($products) > 0) {
			foreach ($products as $product) {
				$orderItem = array();
				$orderItem['order_id'] = $order_id;
				$orderItem['order_item_id'] = $product['orderItemId'];
				$orderItem['product_sku']   = $product['sku'];
				$orderItem['product_qty']   = $product['quantity'];
				$orderItem['product_price'] = $product['unitPrice'];
				$orderItem['product_true_cost']  = getTrueCost($product['sku']);
				$orderItem['dateofmodification'] = date('Y-m-d H:i:s');

				//check if SKU is KIT SKU
				$item_sku = $db->func_escape_string($orderItem['product_sku']);
				$kit_skus = $db->func_query_first("select * from inv_kit_skus where kit_sku = '$item_sku'");
				if ($kit_skus) {
					$kit_skus_array = explode(",", $kit_skus['linked_sku']);
					foreach ($kit_skus_array as $kit_skus_row) {
						$orderItem['product_sku'] = $kit_skus_row;
						$db->func_array2insert("inv_orders_items", $orderItem);
					}

					//mark kit sku need_sync on all marketplaces
					$db->db_exec("update inv_kit_skus SET need_sync = 1 where kit_sku = '$item_sku'");
				} else {
					$db->func_array2insert("inv_orders_items", $orderItem);
				}

				$shipping_cost += $product['ShippingCost'] + $product['ShippingTaxCost'];
			}
		}

		return $InsertID;
	}
}

function getPrefix($store_name){
	if($store_name == 'newegg'){
		return "NE";
	}
	elseif($store_name == 'rakuten'){
		return "RK";
	}
	elseif($store_name == 'opensky'){
		return "OS";
	}
	elseif($store_name == 'newsears'){
		return "NS";
	}
	elseif($store_name == 'bonanza'){
		return "BO";
	}
}

echo "success";