<?php

class Newegg{

	public function readCSV($csv_path){
		$fp = fopen($csv_path,"r");
		if(!$fp){
			return false;
		}

		$heading = fgetcsv($fp);
		$orders  = array();

		$i = 0;
		while(!feof($fp)){
			$row = fgetcsv($fp);
			for($j=0;$j<count($heading);$j++){
				if($row[$j]){
					$heading[$j] = trim($heading[$j]);
					$heading[$j] = preg_replace("/[^a-zA-Z0-9\s\&\#]/is", "", utf8_decode($heading[$j]));
					$orders[$i][trim($heading[$j])] = trim($row[$j]);
				}
			}
			$i++;
		}

		foreach($orders as $order){
			$this->addOrder($order);
		}

		//unlink($csv_path);
		return 1;
	}

	public function addOrder($orderObject){
		global $db;

		$order_id = trim($orderObject['Order Number']);
		$order_status = "Processing";
		$shipping_status = "Not Shipped";

		if ($shipping_status and $shipping_status == 'Shipped') {
			$order_status = 'Shipped';
		}

		$order_id = "NE" . $order_id;
		$order_date = trim($orderObject['Order Date & Time']);
		$order_date = date("Y-m-d H:i:s",strtotime($order_date));

		//convert time GMT to PT = -8:00
		$order_date = date('Y-m-d H:i:s', strtotime($order_date) - (8 * 60 * 60));

		$payment_status = 'Cleared';
		$order_total = $orderObject['Order Total'];
		$email   = $orderObject['Order Customer Email'];
		$carrier = "";
		$carrier_class = $orderObject['Order Shipping Method'];
		$track_number  = $orderObject['Tracking Number'];

		//if order is exist then do not process it again`
		$isExist = $db->func_query_first_cell("select id from inv_orders where order_id = '$order_id'");
		if ($isExist) {
			$orderUpdate = array();
			$orderUpdate['order_status']  = $order_status;
			$orderUpdate['dateofmodification'] = date('Y-m-d H:i:s');
			$db->func_array2update("inv_orders", $orderUpdate, " order_id = '$order_id'");

			return $isExist;
		}

		$order_details = array();
		$order_details['phone_number'] = $orderObject['Ship To Phone Number'];
		$order_details['first_name'] = $orderObject['Ship To First Name'];
		$order_details['last_name']  = $orderObject['Ship To LastName'];
		$order_details['address1'] = $orderObject['Ship To Address Line 1'];
		$order_details['address2'] = $orderObject['Ship To Address Line 2'];
		$order_details['city']    = $orderObject['Ship To City'];
		$order_details['state']   = $orderObject['Ship To State'];
		$order_details['country'] = $orderObject['Ship To Country'];
		$order_details['zip'] = $orderObject['Ship To ZipCode'];
			
		$order_details['payment_method'] = "";
		$order_details['transaction_id'] = "";

		$shipping_cost = $orderObject['Order Shipping Total'];
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
			$order['email']  = $email;
			$order['store_type'] = "newegg";
			$order['sub_store_type'] = "";
			$order['customer_name']  = $customer_name;
			$order['dateofmodification'] = date('Y-m-d H:i:s');

			$InsertID = $db->func_array2insert("inv_orders", $order);

			$order_detail = array();
			$order_detail['shipping_cost']   = $shipping_cost;
			$order_detail['shipping_method'] = $carrier_class;
			$order_detail['payment_method']  = "";
			$order_detail['first_name'] = $db->func_escape_string($order_details['first_name']);
			$order_detail['last_name'] = $db->func_escape_string($order_details['last_name']);
			$order_detail['address1'] = $db->func_escape_string($order_details['address1']);
			$order_detail['address2'] = $db->func_escape_string($order_details['address2']);
			$order_detail['city']  = $order_details['city'];
			$order_detail['state'] = $order_details['state'];
			$order_detail['country'] = $order_details['country'];
			$order_detail['zip'] = $order_details['zip'];
			$order_detail['phone_number'] = $order_details['phone_number'];
			$order_detail['order_id'] = $order_id;
			$order_detail['dateofmodification'] = date('Y-m-d H:i:s');

			$db->func_array2insert("inv_orders_details", $order_detail);

			$orderItem = array();
			$orderItem['order_id'] = $order_id;
			$orderItem['order_item_id'] = $orderObject['Item Newegg #'];
			$orderItem['product_sku'] = $orderObject['Item Seller Part #'];
			$orderItem['product_qty'] = $orderObject['Quantity Ordered'];
			$orderItem['product_price'] = $orderObject['Item Unit Price'];
			$orderItem['product_true_cost']  = $this->getTrueCost($orderObject['Item Seller Part #']);
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

			return $InsertID;
		}
	}

	public function getTrueCost($sku) {
		global $db;
		$true_cost = 0.00;
		$cost = $db->func_query_first("SELECT  cost_date,raw_cost,ex_rate,shipping_fee FROM inv_product_costs WHERE sku='" . $sku . "' ORDER BY id DESC limit 1");
		if ($cost) {
			$true_cost = ($cost['raw_cost'] + $cost['shipping_fee']) / $cost['ex_rate'];
			$true_cost = round($true_cost, 2);
		}
		return $true_cost;
	}

	public function updateInventory($sku , $qty){
		$url = 'https://api.newegg.com/marketplace/contentmgmt/item/inventoryandprice?sellerid=A6MG&version=304';

		$xml = '<ItemInventoryAndPriceInfo><Type>1</Type><Value>'.$sku.'</Value><Inventory>'.$qty.'</Inventory></ItemInventoryAndPriceInfo>';
		$result = $this->sendRequest($url, $xml);

		return $result;
	}

	public function sendRequest($url , $xml){
		$headers = array ("Content-Type: application/xml",
						  "Accept: application/xml",	
						  "Authorization: 8f4d686b7b58906c9efb6d1d07d53561",	
						  "SecretKey: 3d83163f-cedd-4602-94e8-eb1edaf6c9a8");
		try {
			// Get the curl session object
			$session = curl_init($url);
			$putString = stripslashes($xml);
			$putData = tmpfile();
			fwrite($putData, $putString);
			fseek($putData, 0);

			// Set the POST options.
			curl_setopt($session, CURLOPT_HEADER, 1);
			curl_setopt($session, CURLOPT_HTTPHEADER,$headers);
			curl_setopt($session, CURLOPT_PUT, true);
			curl_setopt($session, CURLOPT_HEADER, false);
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($session, CURLOPT_POSTFIELDS, $xml);
			curl_setopt($session, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($session, CURLOPT_INFILE, $putData);
			curl_setopt($session, CURLOPT_INFILESIZE, strlen($putString));

			// Do the POST and then close the session
			$response = curl_exec($session);
			curl_close($session);
			return $response;
		}
		catch (InvalidArgumentException $e) {
			curl_close($session); throw $e;
		}
		catch (Exception $e) {
			curl_close($session); throw $e;
		}
	}
}