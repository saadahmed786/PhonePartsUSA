<?php

error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING ^ E_DEPRECATED);

include_once "nusoap.php";

class ChannelAdvisor {

	public $headers;

	public $dev_key;

	public $password;

	public $accountId;

	public $prefix;

	public function __construct($dev_key , $password , $account_id , $prefix = 'MM') {
		$this->dev_key   = $dev_key;
		$this->password  = $password;
		$this->accountId = $account_id;
		$this->prefix    = $prefix;
	}

	/**
	 * This method will send permission to CA account
	 * @param $profileId
	 */
	public function requestPermission($profileId){
		if(!$profileId)
		return -1;

		$xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:web="http://api.channeladvisor.com/webservices/">
                           <soapenv:Header>
                              <web:APICredentials>
                                 <web:DeveloperKey>'.$this->dev_key.'</web:DeveloperKey>
                                 <web:Password>'.$this->password.'</web:Password>
                              </web:APICredentials>
                           </soapenv:Header>
                           <soapenv:Body>
                              <web:RequestAccess>
                                 <web:localID>'.$profileId.'</web:localID>
                              </web:RequestAccess>
                           </soapenv:Body>
                        </soapenv:Envelope>';

		$soapaction = "http://api.channeladvisor.com/webservices/RequestAccess";
		$wsdl = "https://api.channeladvisor.com/ChannelAdvisorAPI/v7/AdminService.asmx";
		$namespace = "http://api.channeladvisor.com/datacontracts/";

		$client = new nusoap_client($wsdl);
		$client->soap_defencoding = 'UTF-8';
		$client->operation = 'RequestAccess';

		$result = $client->send($xml,$soapaction);
		if($result['RequestAccessResult']['Status']=='Success'){
			return 1;
		}
		else{
			return $result['RequestAccessResult']['MessageCode'] . " : " . $result['RequestAccessResult']['Message'];
		}
	}

	/**
	 * Send request on server and get response
	 * @param Soap $requestXml
	 * @param String $api
	 */
	public function processRequest($requestXml,$api){
		$soapaction = "http://api.channeladvisor.com/webservices/".$api;
		$wsdl = "https://api.channeladvisor.com/ChannelAdvisorAPI/v7/InventoryService.asmx?WSDL";

		$namespace = "http://api.channeladvisor.com/datacontracts/";
		$client = new nusoap_client($wsdl);
		$client->soap_defencoding = 'UTF-8';
		$client->operation = $api;
		$result = $client->send($requestXml, $soapaction);
		return $result;
	}

	/**
	 * Enter description here ...
	 * @param String $sku
	 */
	public function UpdateInventoryItemQuantityAndPriceList($skuArray){
		$XML = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:web="http://api.channeladvisor.com/webservices/">
                       <soapenv:Header>
                          <web:APICredentials>
                             <web:DeveloperKey>' . $this->dev_key . '</web:DeveloperKey>
                             <web:Password>' . $this->password . '</web:Password>
                          </web:APICredentials>
                       </soapenv:Header>
                       <soapenv:Body>
                          <web:UpdateInventoryItemQuantityAndPriceList>
                             <web:accountID>' . $this->accountId . '</web:accountID>
                             <web:itemQuantityAndPriceList>';
		for($i=0; $i<sizeof($skuArray); $i++){
			$XML  .='<web:InventoryItemQuantityAndPrice>
                                     <web:Sku>'.$skuArray[$i]['sku'].'</web:Sku>
                                     <web:QuantityInfo>
                                        <web:UpdateType>Available</web:UpdateType>
                                        <web:Total>'.$skuArray[$i]['qty'].'</web:Total>
                                     </web:QuantityInfo>                                     
                                    </web:InventoryItemQuantityAndPrice>
                                    ';
		}
		$XML .='        </web:itemQuantityAndPriceList>
                          </web:UpdateInventoryItemQuantityAndPriceList>
                       </soapenv:Body>
                    </soapenv:Envelope>';
		return $this->processRequest($XML,'UpdateInventoryItemQuantityAndPriceList');
	}

	/**
	 * Get inventory details of given sku
	 * @param String $sku
	 */
	public function GetInventoryItemQuantityInfo($sku){
		$XML = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:web="http://api.channeladvisor.com/webservices/">
                       <soapenv:Header>
                          <web:APICredentials>
                             <web:DeveloperKey>' . $this->dev_key . '</web:DeveloperKey>
                             <web:Password>' . $this->password . '</web:Password>
                          </web:APICredentials>
                       </soapenv:Header>
                       <soapenv:Body>
                          <web:GetInventoryItemQuantityInfo>
                             <web:accountID>' . $this->accountId . '</web:accountID>
                             <web:sku>'.$sku.'</web:sku>
                          </web:GetInventoryItemQuantityInfo>
                       </soapenv:Body>
                    </soapenv:Envelope>';
		return $this->processRequest($XML,'GetInventoryItemQuantityInfo');
	}

	/**
	 * @param DATETIME $start_date
	 * @param Integer $orderid
	 * @param Integer $cnt
	 */
	private function requestOrderXml($start_date='',$orderid='',$cnt=1) {
		$xml  = '<?xml version="1.0" encoding="ISO-8859-1" ?'.'>';
		$xml .= '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:web="http://api.channeladvisor.com/webservices/" xmlns:ord="http://api.channeladvisor.com/datacontracts/orders">';
		$xml .= '<soapenv:Header>';
		$xml .= '<web:APICredentials><web:DeveloperKey>' . $this->dev_key . '</web:DeveloperKey><web:Password>' . $this->password . '</web:Password></web:APICredentials>';
		$xml .= '</soapenv:Header>';
		$xml .= '<soapenv:Body>';
		$xml .= '<web:GetOrderList><web:accountID>' . $this->accountId . '</web:accountID>';
		$xml .= '<web:orderCriteria><ord:DetailLevel>Complete</ord:DetailLevel>';

		if($start_date!='') {
			$end_order_date = date('Y-m-d\TH:i:s', (time() + (24*60*60)));

			$xml .= '<ord:StatusUpdateFilterBeginTimeGMT>'.$start_date.'</ord:StatusUpdateFilterBeginTimeGMT>';
			$xml .= '<ord:StatusUpdateFilterEndTimeGMT>'.$end_order_date.'</ord:StatusUpdateFilterEndTimeGMT>';
		}

		if($orderid!=''){
			$xml.='<ord:OrderIDList><ord:int>'.$orderid.'</ord:int></ord:OrderIDList>';
		}

		$xml .= '<ord:OrderStateFilter>Active</ord:OrderStateFilter><ord:PageNumberFilter>'.$cnt.'</ord:PageNumberFilter><ord:PageSize>50</ord:PageSize>';
		$xml .= '</web:orderCriteria>';
		$xml .= '</web:GetOrderList></soapenv:Body></soapenv:Envelope>';

		return $xml;
	}

	/**
	 * Get CA orders
	 * @param DATETIME $start_date
	 * @param Integer $orderid
	 * @param Integer $cnt
	 */
	public function GetOrders($start_date='',$orderid='',$cnt=1){
		$soapaction = "http://api.channeladvisor.com/webservices/GetOrderList";
		$wsdl = "https://api.channeladvisor.com/ChannelAdvisorAPI/v7/OrderService.asmx";
		$namespace = "http://api.channeladvisor.com/datacontracts/";

		$client = new nusoap_client($wsdl);
		$client->soap_defencoding = 'UTF-8';
		$client->operation = 'GetOrderList';
		$requestXml = $this->requestOrderXml($start_date,$orderid,$cnt);

		$result = $client->send($requestXml, $soapaction);
		return $result;
	}

	/**
	 * Insert CA Orders
	 * @param String $accountId
	 * @param DATETIME $start_date
	 * @param Integer $page
	 */
	public function fetchStoreOrders($start_date , $page = 1){
		global $db;
		$result = $this->GetOrders($start_date , false , $page);

		if(isset($result['GetOrderListResult']) AND $result['GetOrderListResult']['Status'] != 'Success'){
			throw new Exception("fetchStoreOrders error : ".$this->accountId." , ".print_r($result,true));
			return false;
		}
		else{
			if(isset($result['GetOrderListResult']['ResultData']['OrderResponseItem'])){
				$OrderResponseItem = $result['GetOrderListResult']['ResultData']['OrderResponseItem'];
				if(isset($OrderResponseItem[0]) && is_array($OrderResponseItem[0])){
					$orders = $OrderResponseItem;
				}
				else{
					$orders = array(0 => $OrderResponseItem);
				}

				foreach($orders as $order){
					$order_date  = trim($order['LastUpdateDate']);
					$this->addCAOrder($order);
				}

				$last_date = str_replace("T"," ",$order_date);
				$start_date1 = str_replace("T"," ",$start_date);

				if($last_date  and strtotime($last_date) > strtotime($start_date1)){
					$db->db_exec("update ca_credential SET last_cron_date = '$order_date' where account_id = '".$this->accountId."'");
				}

				$page++;
				$this->fetchStoreOrders($start_date , $page);
			}

			return 1;
		}
	}

	/**
	 * Add new order for CA
	 * @param $order
	 */
	public function addCAOrder($orderObject){
		global $db;

		//$order_id  = trim($orderObject['ClientOrderIdentifier']);
		$order_id = trim($orderObject['OrderID']);
		$order_status    = trim($orderObject['OrderStatus']['CheckoutStatus']);
		$shipping_status = trim($orderObject['OrderStatus']['ShippingStatus']);

		if($shipping_status and $shipping_status == 'Shipped'){
			$order_status = 'Shipped';
		}

		$order_id = $this->prefix.$order_id;

		$order_date     = trim($orderObject['OrderTimeGMT']);
		$order_date     = $this->mySqlDate($order_date , 'CA');

		//convert time GMT to PT = -8:00
		$order_date	= date('Y-m-d H:i:s', strtotime($order_date) - (8*60*60));

		$payment_status = $orderObject['OrderStatus']['PaymentStatus'];
		$order_total    = $orderObject['TotalOrderAmount'];
		$email          = $orderObject['BuyerEmailAddress'];
		$carrier        = $orderObject['ShippingInfo']['ShipmentList']['Shipment']['ShippingCarrier'];
		$carrier_class  = $orderObject['ShippingInfo']['ShipmentList']['Shipment']['ShippingClass'];
		$track_number   = $orderObject['ShippingInfo']['ShipmentList']['Shipment']['TrackingNumber'];

		if(isset($orderObject['ShoppingCart']['LineItemSKUList']['OrderLineItemItem'][0])){
			$products = $orderObject['ShoppingCart']['LineItemSKUList']['OrderLineItemItem'];
		}
		else{
			$products = array(0 => $orderObject['ShoppingCart']['LineItemSKUList']['OrderLineItemItem']);
		}

		//if order is exist then do not process it again`
		$isExist = $db->func_query_first_cell("select id from inv_orders where order_id = '$order_id'");
		if($isExist){
			$orderUpdate = array();
			//$orderUpdate['fishbowl_uploaded'] = 0;
			$orderUpdate['order_status']   = $order_status;
			$orderUpdate['fullfill_type']  = $products[0]['DistributionCenterCode'];
			$orderUpdate['dateofmodification'] = date('Y-m-d H:i:s');
			$db->func_array2update("inv_orders",$orderUpdate," order_id = '$order_id'");

			if($order_status == 'Cancelled'){
				$isReturnExist = $db->func_query_first_cell("select id from inv_return_orders where order_id = '$order_id'");
				if(!$isReturnExist){
					$returnDate     = trim($orderObject['DateCancelledGMT']);
					$returnDate     = $this->mySqlDate($returnDate , 'CA');

					//convert time GMT to PT = -8:00
					$returnDate	= date('Y-m-d H:i:s', strtotime($returnDate) - (8*60*60));
					$db->db_exec("insert into inv_return_orders (order_id,email,order_price,paid_price,order_date,return_date,status,store_type,dateofmodification)
							values ('$order_id','$email','$order_total','$order_total','$order_date','$returnDate','open','web','".date('Y-m-d H:i:s')."')");
				}
				else{
					$db->db_exec("update inv_return_orders SET is_updated = 1 , ignored = 0 , dateofmodification = '".date('Y-m-d H:i:s')."' where order_id = '$order_id'");
				}
			}

			return $isExist;
		}

		$order_details = array();
		if($orderObject['ShippingInfo']['PhoneNumberDay']){
			$order_details['phone_number'] = $orderObject['ShippingInfo']['PhoneNumberDay'];
		}
		elseif($orderObject['ShippingInfo']['PhoneNumberEvening']){
			$order_details['phone_number'] = $orderObject['ShippingInfo']['PhoneNumberEvening'];
		}
		elseif($orderObject['BillingInfo']['PhoneNumberDay']){
			$order_details['phone_number'] = $orderObject['BillingInfo']['PhoneNumberDay'];
		}
		elseif($orderObject['BillingInfo']['PhoneNumberEvening']){
			$order_details['phone_number'] = $orderObject['BillingInfo']['PhoneNumberEvening'];
		}
		else{
			$order_details['phone_number'] = false;
		}

		//firstname
		if($orderObject['ShippingInfo']['FirstName'] == ''){
			$order_details['first_name'] = $orderObject['BillingInfo']['FirstName'];
		}
		else{
			$order_details['first_name'] = $orderObject['ShippingInfo']['FirstName'];
		}

		//lastname
		if($orderObject['ShippingInfo']['LastName'] == ''){
			$order_details['last_name'] = $orderObject['BillingInfo']['LastName'];
		}
		else{
			$order_details['last_name'] = $orderObject['ShippingInfo']['LastName'];
		}

		//address 1 && address 2
		if($orderObject['ShippingInfo']['AddressLine1'] == '' && $orderObject['ShippingInfo']['AddressLine2'] == ''){
			$order_details['address1'] = $orderObject['BillingInfo']['AddressLine1'];
			$order_details['address2'] = $orderObject['BillingInfo']['AddressLine2'];
		}
		else{
			$order_details['address1'] = $orderObject['ShippingInfo']['AddressLine1'];
			$order_details['address2'] = $orderObject['ShippingInfo']['AddressLine2'];
		}

		//city
		if($orderObject['ShippingInfo']['City'] == ''){
			$order_details['city']  = $orderObject['BillingInfo']['City'];
		}
		else{
			$order_details['city']  = $orderObject['ShippingInfo']['City'];
		}

		//state
		if($orderObject['ShippingInfo']['Region'] == ''){
			$order_details['state']  = $orderObject['BillingInfo']['Region'];
		}
		else{
			$order_details['state']  = $orderObject['ShippingInfo']['Region'];
		}

		//country
		if($orderObject['ShippingInfo']['CountryCode'] == ''){
			$order_details['country']  = $orderObject['BillingInfo']['CountryCode'];
		}
		else{
			$order_details['country']  = $orderObject['ShippingInfo']['CountryCode'];
		}

		//zip
		if($orderObject['ShippingInfo']['PostalCode'] == ''){
			$order_details['zip']  = $orderObject['BillingInfo']['PostalCode'];
		}
		else{
			$order_details['zip']  = $orderObject['ShippingInfo']['PostalCode'];
		}

		$order_details['payment_method'] = $orderObject['PaymentInfo']['PaymentType'];
		$order_details['transaction_id'] = $orderObject['PaymentInfo']['PaymentTransactionID'];

		$shipping_cost = 0;
		if($payment_status!='Cleared'){
			//if payment is pending for order then it will process again in next cron run
			return false;
		}
		else{
			$store_name = @$orderObject['ShoppingCart']['LineItemSKUList']['OrderLineItemItem'][0]['ItemSaleSource'];
			if(!$store_name){
				$store_name = @$orderObject['ShoppingCart']['LineItemSKUList']['OrderLineItemItem']['ItemSaleSource'];
			}

			$store_name_match  = substr($store_name,0,strpos($store_name,"_"));
			if(preg_match('/AMAZON.*?/is',$store_name_match)){
				$sub_store = "Amazon";
			}
			elseif($store_name_match == 'EBAY_STORES' || $store_name_match == 'EBAY'){
				$sub_store = "eBay";
			}
			else{
				$sub_store = $store_name;
			}

			$customer_name = $order_details['first_name'] ." ". $order_details['last_name'];
			$customer_name = $db->func_escape_string($customer_name);

			$order = array();
			$order['order_id']   = $order_id;
			$order['order_date'] = $order_date;
			$order['order_status'] = $order_status;
			$order['order_price']  = $order_total;
			$order['status']  = 'open';
			$order['email']   = $email;
			$order['store_type'] = "channel_advisor";
			$order['sub_store_type'] = $sub_store;
			$order['fullfill_type']  = $products[0]['DistributionCenterCode'];
			$order['customer_name']  = $customer_name;
			$order['dateofmodification'] = date('Y-m-d H:i:s');

			$InsertID = $db->func_array2insert("inv_orders",$order);

			$order_detail = array();
			$order_detail['shipping_cost']   = $shipping_cost;
			$order_detail['shipping_method'] = $orderObject['ShippingInfo']['ShipmentList']['Shipment']['ShippingCarrier'];
			$order_detail['payment_method']  = $orderObject['PaymentInfo']['PaymentType'];
			$order_detail['first_name'] = $db->func_escape_string($order_details['first_name']);
			$order_detail['last_name']  = $db->func_escape_string($order_details['last_name']);
			$order_detail['address1'] = $db->func_escape_string($order_details['address1']);
			$order_detail['address2'] = $db->func_escape_string($order_details['address2']);
			$order_detail['city']  = $order_details['city'];
			$order_detail['state'] = $order_details['state'];
			$order_detail['country'] = $order_details['country'];
			$order_detail['zip']     = $order_details['zip'];
			$order_detail['phone_number'] = $order_details['phone_number'];
			$order_detail['order_id']     = $order_id;
			$order_detail['dateofmodification'] = date('Y-m-d H:i:s');

			$db->func_array2insert("inv_orders_details", $order_detail);

			if(is_array($products) and sizeof($products) > 0){
				foreach($products as $product){
					$orderItem = array();
					$orderItem['order_id'] = $order_id;
					if($product['LineItemID']){
						$orderItem['order_item_id'] = $product['LineItemID'];
					}
					else{
						$orderItem['order_item_id'] = $product['SalesSourceID'];
					}

					$orderItem['product_sku']   = $product['SKU'];
					$orderItem['product_qty']   = $product['Quantity'];
					$orderItem['product_price'] = $product['UnitPrice'];
					$orderItem['dateofmodification'] = date('Y-m-d H:i:s');

					//check if SKU is KIT SKU
					$item_sku = $db->func_escape_string($orderItem['product_sku']);
					$kit_skus = $db->func_query_first("select * from inv_kit_skus where kit_sku = '$item_sku'");
					if($kit_skus){
						$kit_skus_array = explode(",",$kit_skus['linked_sku']);
						foreach($kit_skus_array as $kit_skus_row){
							$orderItem['product_sku']  = $kit_skus_row;
							$db->func_array2insert("inv_orders_items",$orderItem);
						}

						//mark kit sku need_sync on all marketplaces
						$db->db_exec("update inv_kit_skus SET need_sync = 1 where kit_sku = '$item_sku'");
					}
					else{
						$db->func_array2insert("inv_orders_items",$orderItem);
					}

					$shipping_cost += $product['ShippingCost'] + $product['ShippingTaxCost'];
				}

				$db->db_exec("update inv_orders_details SET shipping_cost = '$shipping_cost' Where order_id = '$order_id'");
			}

			return $InsertID;
		}
	}

	/**
	 * @param Ingeger $orderId
	 */
	public function processOrder($orderId){
		$result = $this->GetOrders(false , $orderId);
		if(isset($result['GetOrderListResult']['ResultData']['OrderResponseItem'])){
			$order = $result['GetOrderListResult']['ResultData']['OrderResponseItem'];
			return $this->addCAOrder($order);
		}

		return false;
	}

	public function mySqlDate($date , $type = 'eBay'){
		if($type == 'Amazon'){
			$mysql_date  = date('Y-m-d H:i:s',strtotime($date));
			return $mysql_date;
		}
		elseif($type == 'CA'){
			$ca_date = str_replace("T"," ",$date);
			$ca_date = substr($ca_date, 0 , 19);

			$ca_date  = date('Y-m-d H:i:s',strtotime($date));
			return $ca_date;
		}
		else{
			$ebay_date  = str_replace(".000Z","",$date);
			$ebay_date  = str_replace("T"," ",$ebay_date);
			$mysql_date = date('Y-m-d H:i:s',strtotime($ebay_date));
			return $mysql_date;
		}
	}

	/**
	 * Get inventory details of given sku
	 * @param String $sku
	 */
	public function GetInventoryItemList($skus){
		$sku_str = '';
		if(is_array($skus)){
			foreach($skus as $sku){
				$sku_str .= '<web:string>'.$sku.'</web:string>';
			}
		}
		else{
			$sku_str = '<web:string>'.$skus.'</web:string>';
		}

		$XML = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:web="http://api.channeladvisor.com/webservices/">
                       <soapenv:Header>
                          <web:APICredentials>
                             <web:DeveloperKey>' . $this->dev_key . '</web:DeveloperKey>
                             <web:Password>' . $this->password . '</web:Password>
                          </web:APICredentials>
                       </soapenv:Header>
                       <soapenv:Body>
                          <web:GetInventoryItemList>
                             <web:accountID>' . $this->accountId . '</web:accountID>
                             <web:skuList>
                             	 '.$sku_str.'
                             </web:skuList>
                          </web:GetInventoryItemList>
                       </soapenv:Body>
                    </soapenv:Envelope>';
		return $this->processRequest($XML,'GetInventoryItemList');
	}

	/**
	 * Enter description here ...
	 * @param String $sku
	 */
	public function UpdateInventoryPriceList($skuArray , $prefix = 'MM' , $formula = false){
		$XML = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:web="http://api.channeladvisor.com/webservices/">
                       <soapenv:Header>
                          <web:APICredentials>
                             <web:DeveloperKey>' . $this->dev_key . '</web:DeveloperKey>
                             <web:Password>' . $this->password . '</web:Password>
                          </web:APICredentials>
                       </soapenv:Header>
                       <soapenv:Body>
                          <web:UpdateInventoryItemQuantityAndPriceList>
                             <web:accountID>' . $this->accountId . '</web:accountID>
                             <web:itemQuantityAndPriceList>';
		for($i=0; $i<sizeof($skuArray); $i++){
			if($formula){
				$product_id  = $item['product_id'];
				$first_price = $db->func_query_first_cell("select price from oc_product_discount where product_id = '".$product_id."' and customer_group_id = 6 and quantity = 1");
				if($first_price > 0){
					$x = $first_price;
				}
				else{
					$x = $db->func_query_first_cell("select price from oc_product_discount where product_id = '$product_id' AND customer_group_id = '8' and quantity = 1");
				}
				
				if($x <= 0){
					continue;
				}
				
				$price = eval("return ".$formula);
			}
			else{
				$price = $skuArray[$i]['price'];
			}
			
			$XML  .='<web:InventoryItemQuantityAndPrice>
                                     <web:Sku>'.$skuArray[$i]['sku'].'</web:Sku>
                                     <web:PriceInfo>
                                     	<web:RetailPrice>'.$price.'</web:RetailPrice>
                                        <web:StorePrice>'.$price.'</web:StorePrice>
                                        <web:TakeItPrice>'.$price.'</web:TakeItPrice>
                                     </web:PriceInfo>
                                    </web:InventoryItemQuantityAndPrice>';
		}
		$XML .='        </web:itemQuantityAndPriceList>
                          </web:UpdateInventoryItemQuantityAndPriceList>
                       </soapenv:Body>
                    </soapenv:Envelope>';
		return $this->processRequest($XML,'UpdateInventoryItemQuantityAndPriceList');
	}

	public function addInventoryItem($items , $distribution_centercode , $formula){
		global $db;
		
		if(!$items){
			return false;
		}

		$items_xml = '';
		foreach($items as $item){
			$product_id  = $item['product_id'];
			$first_price = $db->func_query_first_cell("select price from oc_product_discount where product_id = '".$product_id."' and customer_group_id = 6 and quantity = 1");
			if($first_price > 0){
				$x = $first_price;
			}
			else{
				$x = $db->func_query_first_cell("select price from oc_product_discount where product_id = '$product_id' AND customer_group_id = '8' and quantity = 1");
			}
			
			if($x <= 0){
				continue;
			}
			$price = eval("return ".$formula);
			
			$product_images = $db->func_query("select * from oc_product_image where product_id = '$product_id'");
			$product_image_xml = '';
			$placement = 'Image';
			foreach($product_images as $i => $product_image){
				$image = "http://phonepartsusa.com/image/".$product_image['image'];
				$product_image_xml .= '<web:PlacementName>'.$placement.$i.'</web:PlacementName>
			                          <web:FilenameOrUrl>'.$image.'</web:FilenameOrUrl>';
			}
			
			$item['description'] = html_entity_decode($item['description']);
			$items_xml .= '<web:InventoryItemSubmit>
			               <web:Sku>'.$item['sku'].'</web:Sku>
			               <web:Title><![CDATA['.$item['name'].']]></web:Title>
			               <web:Description><![CDATA['.$item['description'].']]></web:Description>
			               <web:Weight>'.$item['weight'].'</web:Weight>
			               <web:WarehouseLocation>'.$item['location'].'</web:WarehouseLocation>
			               <web:UPC>'.$item['upc'].'</web:UPC>
			               <web:MPN>'.$item['mpn'].'</web:MPN>
			               <web:Manufacturer>'.$item['brand'].'</web:Manufacturer>
			               <web:Brand>'.$item['brand'].'</web:Brand>
			               <web:Condition>New</web:Condition>
			               <web:SupplierPO>'.$item['upc'].'</web:SupplierPO>
			               <web:Height>'.$item['height'].'</web:Height>
			               <web:Length>'.$item['length'].'</web:Length>
			               <web:Width>'.$item['width'].'</web:Width>
			               <web:Classification>'.$item['classification'].'</web:Classification>
			               <web:DistributionCenterList>
			                  <web:DistributionCenterInfoSubmit>
			                     <web:DistributionCenterCode>'.$distribution_centercode.'</web:DistributionCenterCode>
			                     <web:Quantity>'.$item['quantity'].'</web:Quantity>
			                     <web:QuantityUpdateType>InStock</web:QuantityUpdateType>
			                  </web:DistributionCenterInfoSubmit>
			               </web:DistributionCenterList>
			               <web:PriceInfo>
			                  <web:RetailPrice>'.$price.'</web:RetailPrice>
			                  <web:TakeItPrice>'.$price.'</web:TakeItPrice>
			                  <web:StorePrice>'.$price.'</web:StorePrice>
			               </web:PriceInfo>
			               <web:ImageList>
			                  <web:ImageInfoSubmit>
			                     '.$product_image_xml.'
			                  </web:ImageInfoSubmit>
			               </web:ImageList>
			            </web:InventoryItemSubmit>';
		}
		
		if(!$items_xml){
			return false;
		}

		$xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:web="http://api.channeladvisor.com/webservices/">
			    <soapenv:Header>
                    <web:APICredentials>
                       <web:DeveloperKey>' . $this->dev_key . '</web:DeveloperKey>
                       <web:Password>' . $this->password . '</web:Password>
                    </web:APICredentials>
                </soapenv:Header>
			    <soapenv:Body>
			      <web:SynchInventoryItemList>
			         <web:accountID>' . $this->accountId . '</web:accountID>
			         <web:itemList>'.$items_xml.'</web:itemList>
			      </web:SynchInventoryItemList>
			   </soapenv:Body>
			</soapenv:Envelope>';
		
		$result = $this->processRequest($xml, "SynchInventoryItemList");
		//print_r($result); exit;
		
		if(isset($result['SynchInventoryItemListResult']) AND $result['SynchInventoryItemListResult']['Status'] != 'Success'){
			$response = "add/update item error : ".$this->accountId." , ".print_r($result,true);
			return $response;
		}
		else{
			$SynchInventoryItemResponse = $result['SynchInventoryItemListResult']['ResultData']['SynchInventoryItemResponse'];
			if(!isset($SynchInventoryItemResponse[0])){
				$SynchInventoryItemResponse[] = $SynchInventoryItemResponse;
			}
			
			foreach($SynchInventoryItemResponse as $SynchInventoryItem){
				$sku = $SynchInventoryItem['Sku'];
				$db->db_exec("update oc_product set ca_added = 1 where sku = '$sku'");
			}
			
			return $result['SynchInventoryItemListResult']['ResultData']['SynchInventoryItemResponse'];
		}
	}
}
?>