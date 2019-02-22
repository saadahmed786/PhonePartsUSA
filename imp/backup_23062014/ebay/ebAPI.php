<?php

class ebAPI{

	public $devID, $appID, $certID, $compatabilityLevel, $userToken, $serverUrl , $ruName;

	public $lastFetchDate = false;

	public function __construct(){
		$this->devID  = devID;
		$this->appID  = appID;
		$this->certID = certID;
		$this->compatabilityLevel  = compatabilityLevel;
		$this->serverUrl  = serverUrl;
		$this->userToken  = userToken;

		global $production;
		if(!$production){
			$this->tokenUrl = 'https://signin.sandbox.ebay.com/ws/eBayISAPI.dll?';
		}
	}

	public static function mySqlDate($ebay_date){
		$ebay_date  = str_ireplace(".000Z","",$ebay_date);
		$ebay_date  = str_ireplace(".768Z","",$ebay_date);
		$ebay_date  = str_ireplace("T"," ",$ebay_date);
		$mysql_date = date('Y-m-d H:i:s',strtotime($ebay_date));
		return $mysql_date;
	}

	public function fetchOrders($startDate,$endDate,$seller_token,$siteId=0,$page=1){
		global $db;

		if(!$seller_token)
		return -1;
		 
		$LastTimeModified = date('Y-m-d H:i:s');
		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8"?>';
		$requestXmlBody .= '<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$seller_token</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<Pagination>';
		$requestXmlBody .= '<EntriesPerPage>50</EntriesPerPage>';
		$requestXmlBody .= "<PageNumber>".$page."</PageNumber>";
		$requestXmlBody .= '</Pagination>';
		$requestXmlBody .= '<ModTimeFrom>'.($startDate).'</ModTimeFrom>';
		$requestXmlBody .= '<ModTimeTo>'.($endDate).'</ModTimeTo>';
		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';
		$requestXmlBody .= "<Version>$this->compatabilityLevel</Version>";
		$requestXmlBody .= '<WarningLevel>High</WarningLevel>';
		$requestXmlBody .= '</GetOrdersRequest>';

		$session = new eBaySession($this->userToken, $this->devID, $this->appID, $this->certID, $this->serverUrl, $this->compatabilityLevel,$siteId,'GetOrders');
		do{
			$responseXml = $session->sendHttpRequest($requestXmlBody);
		}
		while(!$responseXml);

		if(stristr($responseXml, 'HTTP 404') || $responseXml == ''){
			mail("vipin.garg12@gmail.com","ebay error","404 error, sending request error".$responseXml.$requestXmlBody);
			return 0;
		}

		$responseObj  = @simplexml_load_string($responseXml);

		//print_r($responseObj); exit;//

		if(!$responseObj){
			return 0;
		}

		#Get any error nodes
		$errors = $responseObj->Errors;
		if(count($errors) > 0 && $responseObj->Ack == 'Failure'){
			$errorMsg = "<P><B>Errors occured while fetching seller orders</B> <br />";
			for($n=0;$n<sizeof($errors);$n++){
				$code     = $errors[$n]->ErrorCode;
				$shortMsg = $errors[$n]->ShortMessage;
				$longMsg  = $errors[$n]->LongMessage;
				$ErrorParameters = $errors[$n]->ErrorParameters;
				$ErrorParametersValue = $ErrorParameters->Value;
				$errorMsg .= $code . ": $ErrorParametersValue ".$longMsg ." <br />";
			}

			$errorMsg = $errorMsg . "---".$requestXmlBody."---".$responseXml;
			mail("vipin.garg12@gmail.com","eBay error - IMP",$errorMsg);
			return $errorMsg;
		}
		else{
			$last_date = '';

			#Manage all transactions
			for($i=0; $i<sizeof($responseObj->OrderArray->Order); $i++){
				$transaction = $responseObj->OrderArray->Order[$i];
				$this->ManageOrder($transaction);

				$last_date = $transaction->CheckoutStatus->LastModifiedTime;
			}
			 
			if($last_date and strtotime($startDate) < strtotime($last_date)){
				$db->db_exec("update ebay_credential set last_cron_date = '$last_date'");
			}

			#Get total number of pages
			$pages  = $responseObj->PaginationResult->TotalNumberOfPages;
			#Handle the pagignation for all transactions
			if($pages > $page){
				$page = $page+1;
				$response = $this->fetchOrders($startDate,$endDate,$seller_token,$siteId,$page);
				if($response == 0){
					return 0;
				}
			}
		}

		return 1;
	}


	public function ManageOrder($orderObject){
		global $db;

		if($orderObject){
			$OrderID     = (string)$orderObject->OrderID;
			$OrderTotal  = str_replace(',','',(string)$orderObject->Total);
			$CompleteStatus = (string)$orderObject->CheckoutStatus->Status;

			$order_date = (string)$orderObject->CheckoutStatus->LastModifiedTime;
			if($order_date){
				$order_date = $this->mySqlDate($order_date);

				//PST time
				$order_date = date('Y-m-d H:i:s', (strtotime($order_date) - (8*60*60)));
			}

			$OrderStatus = (string)$orderObject->OrderStatus;
			$ship_date = (string)$orderObject->ShippedTime;
			if($ship_date){
				$ship_date = $this->mySqlDate($ship_date);
				//PST time
				$ship_date = date('Y-m-d H:i:s', (strtotime($ship_date) - (8*60*60)));
			}

			if(intval($ship_date) > 0){
				$OrderStatus =  "Shipped";
			}

			$paid_date = (string)$orderObject->PaidTime;
			if($paid_date){
				$paid_date = $this->mySqlDate($paid_date);
			}

			if(intval($paid_date) <= 0){
				//skip the order
				return 2;
			}

			$shipping_additional_cost = (string)$orderObject->ShippingServiceSelected->ShippingServiceAdditionalCost;
			$shipping_cost = (string)$orderObject->ShippingServiceSelected->ShippingServiceCost;
			$shipping_cost = $shipping_cost + $shipping_additional_cost;
			 
			$PaymentMethod = (string)$orderObject->CheckoutStatus->PaymentMethod;

			$Address  =  $orderObject->ShippingAddress->Street1 . " " . $orderObject->ShippingAddress->Street2;
			$Email    =  $orderObject->TransactionArray->Transaction->Buyer->Email;
			$CustName =  $orderObject->BuyerUserID;
			$City  =  $orderObject->ShippingAddress->CityName;
			$State =  $orderObject->ShippingAddress->StateOrProvince;
			$Country =  $orderObject->ShippingAddress->CountryName;
			$Zip   =  $orderObject->ShippingAddress->PostalCode;
			$Phone =  $orderObject->ShippingAddress->Phone;

			$orderExist = $db->func_query_first_cell("select id from inv_orders where order_id = '".mysql_real_escape_string($OrderID)."'");
			if(!$orderExist){
				$db->db_exec("insert into inv_orders(order_id,order_date,order_price,order_status,email,store_type,dateofmodification)
                             values ('$OrderID','".$order_date."','$OrderTotal','$OrderStatus','$Email','ebay','".date('Y-m-d H:i:s')."')");

				$db->db_exec("insert into inv_orders_details(order_id,first_name,phone_number,address1,city,state,country,zip,payment_method,shipping_cost,dateofmodification)
                              values ('$OrderID','".$CustName."','$Phone','$Address','$City','$State','$Country','$Zip','$PaymentMethod','$shipping_cost','".date('Y-m-d H:i:s')."')");

				$SellingManagerSalesRecordNumber =  (string)$orderObject->ShippingDetails->SellingManagerSalesRecordNumber;
				foreach($orderObject->TransactionArray->Transaction as $transaction){
					$qty = (int)$transaction->QuantityPurchased;
					$transaction_id  = (string)$transaction->TransactionID;
					$product_sku    = mysql_real_escape_string($transaction->Item->SKU);

					$product_price   = mysql_real_escape_string($transaction->TransactionPrice);
					$product_price   = str_replace(',','',(string)$product_price);

					$transaction_date = (string)$orderObject->CreatedTime;
					$transaction_date = $this->mySqlDate($transaction_date);
					$db->db_exec("insert into inv_orders_items(order_id,order_item_id,product_sku,product_price,product_qty,dateofmodification)
                                  values ('$OrderID','$transaction_id','$product_sku','$product_price','$qty','".date('Y-m-d H:i:s')."')");
				}
			}
			else{
				$db->db_exec("Update inv_orders SET order_price = '$OrderTotal' , order_status = '$OrderStatus' Where id = '$orderExist'");
			}
		}

		return 1;
	}

	public function geteBayTime($siteId=0){
		$verb = 'GeteBayOfficialTime';
		///Build the request Xml string
		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<GeteBayOfficialTimeRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>".$this->userToken."</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '</GeteBayOfficialTimeRequest>';

		$session = new eBaySession($this->userToken, $this->devID, $this->appID, $this->certID, $this->serverUrl, $this->compatabilityLevel, $siteId, $verb);

		//send the request and get response
		$responseXml = $session->sendHttpRequest($requestXmlBody);
		if(stristr($responseXml, 'HTTP 404') || $responseXml == '')
		die('<P>Error sending request');

		$responseObj = @simplexml_load_string($responseXml) or die(htmlspecialchars($responseXml));
		$Timestamp   = $responseObj->Timestamp;
		return $Timestamp;
	}

	public function eBayReviseItem($token , $ItemID , $qty , $siteId = 0){
		global $db;

		if(!$ItemID || !$qty){
			return 0;
		}

		$verb = 'ReviseItem';
		///Build the request Xml string
		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<ReviseItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>".$token."</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<Item>';
		$requestXmlBody .= '<ItemID>'.$ItemID.'</ItemID>';
		$requestXmlBody .= '<Quantity>'.$qty.'</Quantity>';
		$requestXmlBody .= '</Item>';
		$requestXmlBody .= '</ReviseItemRequest>';

		$session = new eBaySession($this->userToken, $this->devID, $this->appID, $this->certID, $this->serverUrl, $this->compatabilityLevel, $siteId, $verb);

		//send the request and get response
		do{
			$responseXml = $session->sendHttpRequest($requestXmlBody);
		}
		while(!$responseXml);
		$responseObj = @simplexml_load_string($responseXml);

		#Get any error nodes
		$errors = $responseObj->Errors;
		if(count($errors) > 0 && $responseObj->Ack == 'Failure')
		{
			$errorMsg = "<P><B>Errors occured while update item on ebay</B> <br />";
			for($n=0;$n<sizeof($errors);$n++){
				$code     = $errors[$n]->ErrorCode;
				$shortMsg = $errors[$n]->ShortMessage;
				$longMsg  = $errors[$n]->LongMessage;
				$ErrorParameters = $errors[$n]->ErrorParameters;
				$ErrorParametersValue = $ErrorParameters->Value;
				$errorMsg .= $code . ": $ErrorParametersValue ".$longMsg ." <br />";
			}

			$errorMsg = $errorMsg;// . "--"."---".$requestXmlBody."---".$responseXml;
			return $errorMsg;
		}
		else{
			return 1;
		}
	}


	public function eBayRelistItem($token , $ItemID , $siteId = 0){
		global $db;

		if(!$ItemID){
			return 0;
		}

		$verb = 'RelistItem';
		///Build the request Xml string
		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<RelistItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>".$token."</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= '<Item>';
		$requestXmlBody .= '<ItemID>'.$ItemID.'</ItemID>';
		$requestXmlBody .= '</Item>';
		$requestXmlBody .= '</RelistItemRequest>';

		$session = new eBaySession($this->userToken, $this->devID, $this->appID, $this->certID, $this->serverUrl, $this->compatabilityLevel, $siteId, $verb);

		//send the request and get response
		do{
			$responseXml = $session->sendHttpRequest($requestXmlBody);
		}
		while(!$responseXml);
		$responseObj = @simplexml_load_string($responseXml);

		#Get any error nodes
		$errors = $responseObj->Errors;
		if(count($errors) > 0 && $responseObj->Ack == 'Failure')
		{
			$errorMsg = "<P><B>Errors occured while RelistItem on ebay</B> <br />";
			for($n=0;$n<sizeof($errors);$n++){
				$code     = $errors[$n]->ErrorCode;
				$shortMsg = $errors[$n]->ShortMessage;
				$longMsg  = $errors[$n]->LongMessage;
				$ErrorParameters = $errors[$n]->ErrorParameters;
				$ErrorParametersValue = $ErrorParameters->Value;
				$errorMsg .= $code . ": $ErrorParametersValue ".$longMsg ." <br />";
			}

			$errorMsg = $errorMsg;// . "--"."---".$requestXmlBody."---".$responseXml;
			return $errorMsg;
		}
		else{
			$newItemID = $responseObj->ItemID;

			$db->db_exec("Update ebay_mapping SET ebay_item_id = '$newItemID' , item_status = 'listed' , dateofmodification = '".date('Y-m-d H:i:s')."' Where ebay_item_id = '$ItemID'");

			return $newItemID;
		}
	}


	/**
	 * End ebay Item
	 * @param BigInt $itemId
	 * @param String $sellerToken
	 * @param Integer $siteId
	 */
	public function endItem($ItemID , $sellerToken , $siteId = 0){
		global $db;

		$verb = 'EndItem';

		$requestXmlBody  = '<?xml version="1.0" encoding="utf-8" ?>';
		$requestXmlBody .= '<EndItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>".$sellerToken."</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= "<ItemID>".$ItemID."</ItemID><EndingReason>NotAvailable</EndingReason>";
		$requestXmlBody .= "</EndItemRequest>";

		$session = new eBaySession($this->userToken, $this->devID, $this->appID, $this->certID, $this->serverUrl, $this->compatabilityLevel, $siteId, $verb);

		//send the request and get response
		do{
			$responseXml = $session->sendHttpRequest($requestXmlBody);
		}
		while(!$responseXml);

		$responseObj = @simplexml_load_string($responseXml);
		if($responseObj){
			#Get any error nodes
			$errors = $responseObj->Errors;
			if(count($errors) > 0 && $responseObj->Ack == 'Failure'){
				$errorMsg = "<P><B>Errors occured while ending item</B> <br />";
				for($n=0;$n<sizeof($errors);$n++){
					$code     = $errors[$n]->ErrorCode;
					$shortMsg = $errors[$n]->ShortMessage;
					$longMsg  = $errors[$n]->LongMessage;
					$ErrorParameters = $errors[$n]->ErrorParameters;
					$ErrorParametersValue = $ErrorParameters->Value;
					$errorMsg .= $code . ": $ErrorParametersValue ".$longMsg ." <br />";
				}

				$errorMsg = $errorMsg;// ."---".$requestXmlBody."---".$responseXml;
				return $errorMsg;
			}
			else{
				$db->db_exec("Update ebay_mapping SET item_status = 'ended' , dateofmodification = '".date('Y-m-d H:i:s')."' Where ebay_item_id = '$ItemID'");

				return 1;
			}
		}
	}
}