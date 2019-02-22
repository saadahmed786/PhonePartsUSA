<?php
class Bonanza {

	/**
	 * Dev Name for API request
	 * 
	 * @var String
	 */
	public $dev_name;

	/**
	 * Cert Name for API request
	 * 
	 * @var String
	 */
	public $cert_name;

	/**
	 * Token for API request
	 * 
	 * @var String
	 */
	public $token;

	public $url = "https://api.bonanza.com/api_requests/secure_request";

	public $standard_url = "http://api.bonanza.com/api_requests/standard_request";

	public function setCredential($dev_name, $cert_name) {
		$this->dev_name = $dev_name;
		$this->cert_name = $cert_name;
		
		return $this;
	}

	/**
	 * get seller token for futher API calls
	 * 
	 * @return fetchTokenResponse Array
	 *         [hardExpirationTime] => 2011-12-16T06:20:58.000Z
	 *         [authenticationURL] => https://www.bonanza.com/login?apitoken=GTL0jWeQat
	 *         [authToken] => GTL0jWeQat
	 */
	public function fetchToken() {
		$post_fields = "fetchTokenRequest";
		$response = $this->sendRequest ( $post_fields );
		if ($response ['Ack'] == 'Success') {
			return $response ['Data'] ['fetchTokenResponse'];
		}
		else {
			$message = "Fetch token error -- " . $response ['Message'];
			throw new Exception ( $message, "1001" );
		}
	}

	/**
	 * Send request to bananza and get response , data will be returned as a string
	 * @param
	 *        	$post_fields
	 */
	public function sendRequest($post_fields, $secure_connection = true) {
		if (! $this->dev_name || ! $this->cert_name) {
			return array (
					"Ack" => "Error",
					"Message" => "API credentials can not be empty" 
			);
		}
		
		if ($secure_connection) {
			$connection = curl_init ( $this->url );
			
			curl_setopt ( $connection, CURLOPT_SSL_VERIFYPEER, 0 );
			curl_setopt ( $connection, CURLOPT_SSL_VERIFYHOST, 0 );
			
			$headers = array (
					"X-BONANZLE-API-DEV-NAME: " . $this->dev_name,
					"X-BONANZLE-API-CERT-NAME: " . $this->cert_name 
			);
		}
		else {
			$connection = curl_init ( $this->standard_url );
			$headers = array (
					"X-BONANZLE-API-DEV-NAME: " . $this->dev_name 
			);
		}
		
		$curl_options = array (
				CURLOPT_HTTPHEADER => $headers,
				CURLOPT_POSTFIELDS => $post_fields,
				CURLOPT_POST => 1,
				CURLOPT_RETURNTRANSFER => 1 
		); // data will be returned as a string
		
		curl_setopt_array ( $connection, $curl_options );
		
		$json_response = curl_exec ( $connection );
		$response = json_decode ( $json_response, true );
		
		if (curl_errno ( $connection ) > 0) {
			$response = array (
					"Ack" => "Error",
					"Message" => curl_errno ( $connection ) . ":" . curl_error ( $connection ) 
			);
		}
		elseif ($response ['ack'] != 'Success') {
			$response = array (
					"Ack" => "Error",
					"Message" => $response ['errorMessage'] ['message'] 
			);
		}
		else {
			$response = array (
					"Ack" => "Success",
					"Data" => json_decode ( $json_response, true ) 
			);
		}
		
		curl_close ( $connection );
		return $response;
	}

	/**
	 * fetch bonanza orders and insert into db
	 * 
	 * @param DateTime $modTimeFrom        	
	 * @param DateTime $modTimeTo        	
	 * @param String $token        	
	 * @param Integer $store_id        	
	 * @param Integer $page        	
	 */
	public function fetchStoreOrders($modTimeFrom, $modTimeTo, $token, $page = 1) {
		global $db;
		
		$parms = array (
				'requesterCredentials' => array (
						'bonanzleAuthToken' => $token 
				),
				'modTimeFrom' => $modTimeFrom,
				'modTimeTo' => $modTimeTo,
				'paginationInput' => array (
						'entriesPerPage' => 100,
						'pageNumber' => $page 
				) 
		);
		
		$post_fields = "getOrdersRequest=" . urlencode ( json_encode ( $parms ) );
		$response = $this->sendRequest ( $post_fields );
		
		if ($response ['Ack'] == 'Success') {
			$orders = $response ['Data'] ['getOrdersResponse'] ['orderArray'];
			if (is_array ( $orders ) and count ( $orders ) > 0) {
				foreach ( $orders as $order ) {
					$order = $order ['order'];
					
					$orderTotal = $order ['total'];
					$userName = $order ['buyerUserName'];
					$orderStatus = $order ['orderStatus'];
					$orderId = "BO" . $order ['orderID'];
					
					$orderDate = $order ['createdTime'];
					$orderDate = $this->mySqlDate ( $orderDate, 'Bonanza' );
					$email = $order ['transactionArray'] ['transaction'] ['buyer'] ['email'];
					
					$paidTime = $order ['paidTime'];
					if (! $paidTime) {
						// if payment is pending for order then it will process again in next cron run
						continue;
					}
					else {
						$shipping_cost = $order ['shippingDetails'] ['amount'];
						$paymentMethod = $order ['transactionArray'] ['transaction'] ['providerName'];
						$shippingMethod = $order ['shippingDetails'] ['shippingService'];
						
						// print_r($order); exit;
						
						$Address = $order ['shippingAddress'] ['street1'] . " " . $order ['shippingAddress'] ['street2'];
						$CustName = $order ['shippingAddress'] ['name'];
						$City = $order ['shippingAddress'] ['cityName'];
						$State = $order ['shippingAddress'] ['stateOrProvince'];
						$Country = $order ['shippingAddress'] ['country'];
						$Zip = $order ['shippingAddress'] ['postalCode'];
						$Phone = '';
						
						$customer_name = $db->func_escape_string($CustName);
						
						$orderExist = $db->func_query_first_cell ( "select id from inv_orders where order_id = '" . $db->func_escape_string ( $orderId ) . "'" );
						if (! $orderExist) {
							$db->db_exec ( "insert into inv_orders(order_id,order_date,order_price,paid_price,order_status,email,store_type,customer_name,dateofmodification)
			                  values ('$orderId','" . $orderDate . "','$orderTotal','$orderTotal','$orderStatus','$email','bonanza','$customer_name','" . date ( 'Y-m-d H:i:s' ) . "')" );
							
							$db->db_exec ( "insert into inv_orders_details(order_id,first_name,phone_number,address1,city,state,country,zip,payment_method,shipping_method,shipping_cost,dateofmodification)
			                   values ('$orderId','" . $CustName . "','$Phone','$Address','$City','$State','$Country','$Zip','$paymentMethod','$shippingMethod','$shipping_cost','" . date ( 'Y-m-d H:i:s' ) . "')" );
						}
						else {
							$db->db_exec ( "Update inv_orders SET order_price = '$orderTotal',paid_price='$orderTotal', store_type = 'bonanza' ,
						   				  order_status = '$orderStatus' Where id = '$orderExist'" );
						}
						
						$db->db_exec ( "delete from inv_orders_items where order_id = '$orderId'" );
						
						$itemArray = $order ['itemArray'];
						foreach ( $itemArray as $Item ) {
							$productSku = $Item ['item'] ['sellerInventoryID'];
							$quantity = $Item ['item'] ['quantity'];
							$price = $Item ['item'] ['price'];
							
							$orderItemData = array ();
							$orderItemData ['order_id'] = $orderId;
							$orderItemData ['order_item_id'] = $Item ['item'] ['itemID'];
							$orderItemData ['product_sku'] = $productSku;
							$orderItemData ['product_qty'] = ( int ) $quantity;
							$orderItemData ['product_price'] = $price;
							$orderItemData ['product_true_cost'] = $this->getTrueCost($productSku);
							$orderItemData ['dateofmodification'] = date ( 'Y-m-d H:i:s' );
							
							// check if SKU is KIT SKU
							$kit_skus = $db->func_query_first ( "select * from inv_kit_skus where kit_sku = '$productSku'" );
							if ($kit_skus) {
								$kit_skus_array = explode ( ",", $kit_skus ['linked_sku'] );
								foreach ( $kit_skus_array as $kit_skus_row ) {
									$orderItemData ['product_sku'] = $kit_skus_row;
									$db->func_array2insert ( "inv_orders_items", $orderItemData );
								}
								
								// mark kit sku need_sync on all marketplaces
								$db->db_exec ( "update inv_kit_skus SET need_sync = 1 where kit_sku = '$product_sku'" );
							}
							else {
								$db->func_array2insert ( "inv_orders_items", $orderItemData );
							}
						}
					}
				}
				
				if ($response ['hasMoreOrders']) {
					$page = $page + 1;
					$this->fetchStoreOrders ( $modTimeFrom, $modTimeTo, $token, $page );
				}
			}
			
			$db->db_exec ( "update bonanza_credential set last_cron_date = '$modTimeTo'" );
		}
		else {
			$message = "Fetch orders error -- " . $response ['Message'];
			throw new Exception ( $message, "1001" );
		}
		
		return 1;
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


	/**
	 * Update store inventory
	 * 
	 * @param String $token        	
	 * @param Array $skuArray
	 *        	$skuArray - array(0 => array('sku' => 'test' , 'quantity' => 5) , 1 => array('sku' => 'test2' , 'quantity' => 6));
	 */
	public function updateStoreInventory($token, $skuArray) {
		if (sizeof ( $skuArray ) > 0) {
			foreach ( $skuArray as $sku ) {
				$this->updateInventory ( $itemId, $sku ['qty'], $token );
			}
		}
	}

	/**
	 * Update inventory qty on bonanza server
	 * @param $itemId
	 * @param $qty
	 * @param $token
	 */
	public function updateInventory($itemId, $qty, $token) {
		$item = array ();
		$item ['quantity'] = $qty;
		$args = array (
				"item" => $item,
				"itemId" => $itemId 
		);
		$args ['requesterCredentials'] ['bonanzleAuthToken'] = $token;
		$post_fields = "reviseFixedPriceItem=" . urlencode ( json_encode ( $args ) );
		
		$response = $this->sendRequest ( $post_fields );
		if ($response ['Ack'] == 'Success') {
			return $response ['Data'] ['reviseFixedPriceItemResponse'];
		}
		else {
			$message = "Update Qty error --" . $response ['Message'];
			throw new Exception ( $message, "1001" );
		}
	}

	public function mySqlDate($date, $type = 'eBay') {
		if ($type == 'Amazon') {
			$mysql_date = date ( 'Y-m-d H:i:s', strtotime ( $date ) );
			return $mysql_date;
		}
		elseif ($type == 'CA') {
			$ca_date = str_replace ( "T", " ", $date );
			$ca_date = substr ( $ca_date, 0, 19 );
			
			$ca_date = date ( 'Y-m-d H:i:s', strtotime ( $date ) );
			return $ca_date;
		}
		else {
			$ebay_date = str_replace ( ".000Z", "", $date );
			$ebay_date = str_replace ( "T", " ", $ebay_date );
			$mysql_date = date ( 'Y-m-d H:i:s', strtotime ( $ebay_date ) );
			return $mysql_date;
		}
	}

	public function getProductPrice($product_sku) {
		global $db;
		$itemId = $db->func_query_first_cell("select product_id from bonanza_mappings where product_sku = '$product_sku'");
		if(!$itemId){
			throw new Exception("Product SKU not found", "1002");
		}
		
		$args = array (
			"itemId" => $itemId,
		);
		$post_fields = "getSingleItemRequest=" . urlencode ( json_encode ( $args ) );
		$response = $this->sendRequest ( $post_fields , false);
		
		if ($response ['Ack'] == 'Success') {
			return $response ['Data'] ['getSingleItemResponse'];
		}
		else {
			$message = "Fetch Item error --" . $response ['Message'];
			throw new Exception ( $message, "1001" );
		}
	}

	public function updateProductPrice($product_sku , $product_price , $token) {
		global $db;
		$itemId = $db->func_query_first_cell("select product_id from bonanza_mappings where product_sku = '$product_sku'");
		if(!$itemId){
			throw new Exception("Product SKU not found", "1002");
		}
		
		$item = array ();
		$item ['price'] = $product_price;
		$args = array (
				"item" => $item,
				"itemId" => $itemId
		);
		$args ['requesterCredentials'] ['bonanzleAuthToken'] = $token;
		$post_fields = "reviseFixedPriceItem=" . urlencode ( json_encode ( $args ) );
		
		$response = $this->sendRequest ( $post_fields );
		if ($response ['Ack'] == 'Success') {
			return $response ['Data'] ['reviseFixedPriceItemResponse'];
		}
		else {
			$message = "Update Price error --" . $response ['Message'];
			throw new Exception ( $message, "1001" );
		}
	}

	public function getBooth() {
		$args = array (
				"userId" => "phonedealz" 
		);
		$post_fields = "getBoothRequest=" . urlencode ( json_encode ( $args ) );
		$response = $this->sendRequest ( $post_fields, 0 );
		
		if ($response ['Ack'] == 'Success') {
			return $response ['Data'] ['getBoothResponse'];
		}
		else {
			$message = "Get Booth error --" . $response ['Message'];
			throw new Exception ( $message, "1001" );
		}
	}

	public function getProducts($page = 1) {
		$args = array (
				"userId" => "phonedealz",
				"itemsPerPage" => 25,
				"page" => $page 
		);
		$post_fields = "getBoothItemsRequest=" . urlencode ( json_encode ( $args ) );
		$response = $this->sendRequest ( $post_fields );
		
		if ($response ['Ack'] == 'Success') {
			return $response ['Data'] ['getBoothItemsResponse'];
		}
		else {
			$message = "Fetch Items error --" . $response ['Message'];
			throw new Exception ( $message, "1001" );
		}
	}
}
?>