<?php

class ShipStation{
	
	public $api_key = '0d50ba42240844269473de9ba065873e';

	public $api_secret = '771f86ef07aa47b29e275175d00e6481';
	
	public $endpoint = "https://ssapi.shipstation.com";
	
	public function addOrder($order_data){
		global $db;
		$order = array();
		
		$billTo = array();
		$billTo['name'] = $order_data['first_name']. " ".$order_data['last_name'];
		$billTo['street1'] = $order_data['address1'];
		$billTo['street2'] = $order_data['address2'];
		$billTo['city']    = $order_data['city'];
		$billTo['state']   = $order_data['state'];
		$billTo['postalCode']  = $order_data['zip'];
		$billTo['country']     = substr($order_data['country'],0,2);
		$billTo['phone']       = $order_data['phone_number'];
		$billTo['residential'] = true;
		
		$shipTo = array();
		$shipTo['name'] = $order_data['first_name']. " ".$order_data['last_name'];
		$shipTo['street1'] = $order_data['address1'];
		$shipTo['street2'] = $order_data['address2'];
		$shipTo['city']    = $order_data['city'];
		$shipTo['state']   = $order_data['state'];
		$shipTo['postalCode']  = $order_data['zip'];
		$shipTo['country']     = substr($order_data['country'],0,2);
		$shipTo['phone']       = $order_data['phone_number'];
		$shipTo['residential'] = true;
		
		$items = array();
		$i = 0;
		foreach($order_data['Items'] as $item){
			$items[$i]['orderItemId'] = time();
			$items[$i]['sku']       = $item['product_sku'];
			$items[$i]['name']      = replaceSpecial(getItemName($item['product_sku']));
			$items[$i]['quantity']  = $item['product_qty'];
			$items[$i]['unitPrice'] = $item['product_price'];
			
			$i++;
		}
		
		$order['orderNumber'] = $order_data['order_id'];
		$order['orderKey']    = $order_data['order_id'];
		$order['orderDate']   = $order_data['order_date'];
		$order['paymentDate'] = $order_data['order_date'];
		$order['orderStatus'] = 'shipped'; //$order_data['order_status'];
		$order['customerEmail'] = $order_data['email'];
		$order['amountPaid'] = number_format($order_data['order_price'],2);
		$order['taxAmount']  = 0;
		$order['shippingAmount'] = number_format($order_data['shipping_cost'],2);
		$order['paymentMethod']  = $order_data['payment_method'];
		$order['requestedShippingService'] = $order_data['shipping_method'];
		
		$order['billTo'] = $billTo;
		$order['shipTo'] = $shipTo;
		$order['items']  = $items;
		
		print "<pre>";
		print_R($order); //exit;
		
		$response = $this->sendRequest($order);
		return $response;
	}
	
	public function sendRequest($order , $action = "/orders/createorder"){
		$ch  = curl_init();
		
		curl_setopt($ch, CURLOPT_URL,$this->endpoint.$action);
		curl_setopt($ch, CURLOPT_USERPWD, "$this->api_key:$this->api_secret");

		curl_setopt($ch, CURLOPT_POST,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($order));
		curl_setopt($ch, CURLOPT_HTTPHEADER, array ("Content-Type: application/json"));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_TIMEOUT,30);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,0);

		$result = curl_exec($ch);
		$error  = curl_exec($ch);

		return $result;
	}
}