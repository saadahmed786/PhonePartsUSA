<?php

include_once("../inc/functions.php");
// include_once("../inventory/class.php");

class ebAPI {



	public $devID, $appID, $certID, $compatabilityLevel, $userToken, $serverUrl, $ruName;

	public $lastFetchDate = false;



	public function __construct() {

		$this->devID = devID;

		$this->appID = appID;

		$this->certID = certID;

		$this->compatabilityLevel = compatabilityLevel;

		$this->serverUrl = serverUrl;

		$this->userToken = userToken;



		global $production;

		if (!$production) {

			$this->tokenUrl = 'https://signin.sandbox.ebay.com/ws/eBayISAPI.dll?';

		}

	}



	public static function mySqlDate($ebay_date) {

		$ebay_date = str_ireplace(".000Z", "", $ebay_date);

		$ebay_date = str_ireplace(".768Z", "", $ebay_date);

		$ebay_date = str_ireplace("T", " ", $ebay_date);

		$mysql_date = date('Y-m-d H:i:s', strtotime($ebay_date));

		return $mysql_date;

	}



	public function fetchOrders($startDate, $endDate, $seller_token, $siteId = 0, $page = 1) {

		global $db;

		// echo $seller_token;exit;

		if (!$seller_token)

			return -1;



		$LastTimeModified = date('Y-m-d H:i:s');

		$requestXmlBody = '<?xml version="1.0" encoding="utf-8"?>';

		$requestXmlBody .= '<GetOrdersRequest xmlns="urn:ebay:apis:eBLBaseComponents">';

		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>$seller_token</eBayAuthToken></RequesterCredentials>";
		$requestXmlBody .= " <IncludeFinalValueFee>true</IncludeFinalValueFee>";

		$requestXmlBody .= '<Pagination>';

		$requestXmlBody .= '<EntriesPerPage>50</EntriesPerPage>';

		$requestXmlBody .= "<PageNumber>" . $page . "</PageNumber>";

		$requestXmlBody .= '</Pagination>';

		$requestXmlBody .= '<ModTimeFrom>' . ($startDate) . '</ModTimeFrom>';

		$requestXmlBody .= '<ModTimeTo>' . ($endDate) . '</ModTimeTo>';

		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';

		$requestXmlBody .= "<Version>$this->compatabilityLevel</Version>";

		$requestXmlBody .= '<WarningLevel>High</WarningLevel>';

		$requestXmlBody .= '</GetOrdersRequest>';



		$session = new eBaySession($this->userToken, $this->devID, $this->appID, $this->certID, $this->serverUrl, $this->compatabilityLevel, $siteId, 'GetOrders');

		do {

			$responseXml = $session->sendHttpRequest($requestXmlBody);
			// echo $responseXml;exit;
		} while (!$responseXml);



		if (stristr($responseXml, 'HTTP 404') || $responseXml == '') {

			mail("xaman.riaz@gmail.com", "ebay error", "404 error, sending request error" . $responseXml . $requestXmlBody);

			return 0;

		}



		$responseObj = @simplexml_load_string($responseXml);



		// print_r($responseObj); exit;//



		if (!$responseObj) {

			return 0;

		}



		#Get any error nodes

		$errors = $responseObj->Errors;

		if (count($errors) > 0 && $responseObj->Ack == 'Failure') {

			$errorMsg = "<P><B>Errors occured while fetching seller orders</B> <br />";

			for ($n = 0; $n < sizeof($errors); $n++) {

				$code = $errors[$n]->ErrorCode;

				$shortMsg = $errors[$n]->ShortMessage;

				$longMsg = $errors[$n]->LongMessage;

				$ErrorParameters = $errors[$n]->ErrorParameters;

				$ErrorParametersValue = $ErrorParameters->Value;

				$errorMsg .= $code . ": $ErrorParametersValue " . $longMsg . " <br />";

			}



			$errorMsg = $errorMsg . "---" . $requestXmlBody . "---" . $responseXml;

			mail("xaman.riaz@gmail.com", "eBay error - IMP", $errorMsg);

			return $errorMsg;

		} else {

			$last_date = '';



			#Manage all transactions

			for ($i = 0; $i < sizeof($responseObj->OrderArray->Order); $i++) {

				$transaction = $responseObj->OrderArray->Order[$i];

				$this->ManageOrder($transaction);



				$last_date = $transaction->CheckoutStatus->LastModifiedTime;

			}



			if ($last_date and strtotime($startDate) < strtotime($last_date)) {

				$db->db_exec("update ebay_credential set last_cron_date = '$last_date'");

			}



			#Get total number of pages

			$pages = $responseObj->PaginationResult->TotalNumberOfPages;

			#Handle the pagignation for all transactions

			if ($pages > $page) {

				$page = $page + 1;

				$response = $this->fetchOrders($startDate, $endDate, $seller_token, $siteId, $page);

				if ($response == 0) {

					return 0;

				}

			}

		}



		return 1;

	}



	public function ManageOrder($orderObject) {

		global $db;



		if ($orderObject) {

			$OrderID = (string) $orderObject->OrderID;

			$OrderTotal = str_replace(',', '', (string) $orderObject->Total);

			$CompleteStatus = (string) $orderObject->CheckoutStatus->Status;



			$order_date = (string) $orderObject->CheckoutStatus->LastModifiedTime;

			if ($order_date) {

				$order_date = $this->mySqlDate($order_date);



				//PST time

				$order_date = date('Y-m-d H:i:s', (strtotime($order_date) - (8 * 60 * 60)));

			}



			$OrderStatus = (string) $orderObject->OrderStatus;

			$ship_date = (string) $orderObject->ShippedTime;

			if ($ship_date) {

				$ship_date = $this->mySqlDate($ship_date);

				//PST time

				$ship_date = date('Y-m-d H:i:s', (strtotime($ship_date) - (8 * 60 * 60)));

			}


			$OrderStatus = 'Processed';
			if (intval($ship_date) > 0) {

				$OrderStatus = "Shipped";

			}



			$paid_date = (string) $orderObject->PaidTime;

			if ($paid_date) {

				$paid_date = $this->mySqlDate($paid_date);

			}



			if (intval($paid_date) <= 0) {

				//skip the order

				return 2;

			}



			$shipping_additional_cost = (string) $orderObject->ShippingServiceSelected->ShippingServiceAdditionalCost;

			$shipping_method = (string) $orderObject->ShippingServiceSelected->ShippingService;

			$shipping_cost = (string) $orderObject->ShippingServiceSelected->ShippingServiceCost;

			$shipping_cost = $shipping_cost + $shipping_additional_cost;



			$PaymentMethod = (string) $orderObject->CheckoutStatus->PaymentMethod;



			$Address = $db->func_escape_string($orderObject->ShippingAddress->Street1 . " " . $orderObject->ShippingAddress->Street2);

			$Email = $orderObject->TransactionArray->Transaction->Buyer->Email;

			$CustName = $db->func_escape_string($orderObject->BuyerUserID);

			$City = $db->func_escape_string($orderObject->ShippingAddress->CityName);

			$State = $db->func_escape_string($orderObject->ShippingAddress->StateOrProvince);

			$Country = $orderObject->ShippingAddress->CountryName;

			$Zip = $orderObject->ShippingAddress->PostalCode;

			$Phone = $orderObject->ShippingAddress->Phone;

			$TransactionFee = (float)$orderObject->TransactionArray->Transaction->FinalValueFee;
			// $TransactionFee = 0.00;

			$SellingManagerSalesRecordNumber = (string) $orderObject->ShippingDetails->SellingManagerSalesRecordNumber;

			$OrderID = "E" . $SellingManagerSalesRecordNumber;


			$_country = $db->func_query_first("SELECT country_id,name FROM oc_country WHERE LOWER(iso_code_2)='".strtolower($Country)."' or LOWER(name)='".strtolower($Country)."'");
		$country_id = $_country['country_id'];
		$_zone = $db->func_query_first("SELECT zone_id FROM oc_zone WHERE LOWER(code)='".strtolower($State)."' and country_id='".(int)$country_id."'");
		$zone_id = $_zone['zone_id'];




			$orderExist = $db->func_query_first_cell("select id from inv_orders where order_id = '" . $db->func_escape_string($OrderID) . "'");

			if (!$orderExist) {

				$db->db_exec("insert into inv_orders(order_id,transaction_fee,order_date,order_price,paid_price,order_status,email,store_type,record_number,customer_name,dateofmodification,shipping_amount)

					values ('$OrderID','".$TransactionFee."','" . $order_date . "','$OrderTotal','$OrderTotal','$OrderStatus','$Email','ebay','$SellingManagerSalesRecordNumber','" . $CustName . "','" . date('Y-m-d H:i:s') . "','".(float)$shipping_cost."')");



				$db->db_exec("insert into inv_orders_details(order_id,first_name,phone_number,address1,city,state,country,zip,payment_method,shipping_cost,dateofmodification,zone_id,country_id)

					values ('$OrderID','" . $CustName . "','$Phone','$Address','$City','$State','$Country','$Zip','$PaymentMethod','$shipping_cost','" . date('Y-m-d H:i:s') . "','".(int)$zone_id."','".(int)$country_id."')");


				



				$sub_total = 0.00;
				$items_true_cost = 0.00;
				foreach ($orderObject->TransactionArray->Transaction as $transaction) {

					$qty = (int) $transaction->QuantityPurchased;

					$transaction_id = (string) $transaction->TransactionID;

					$product_sku = $db->func_escape_string($transaction->Item->SKU);



					$product_unit = $db->func_escape_string($transaction->TransactionPrice);

					$product_unit = str_replace(',', '', (string) $product_unit);

					$product_price = $qty * (float)$product_unit;

					$true_cost     = $this->getTrueCost($product_sku);

					$sub_total = (float)$sub_total+(float)$product_price;


					$transaction_date = (string) $orderObject->CreatedTime;

					$transaction_date = $this->mySqlDate($transaction_date);


					$items_true_cost = (float)$items_true_cost + ((float)$true_cost * (int)$_qty);


					saveInventory($product_sku, $qty);
					$db->db_exec("insert into inv_orders_items(order_id,order_item_id,product_sku,product_unit,product_price,product_qty,product_true_cost,dateofmodification,ostatus)

						values ('$OrderID','$transaction_id','$product_sku','".$product_unit."','$product_price','$qty','$true_cost','" . date('Y-m-d H:i:s') . "','".strtolower($OrderStatus)."')");

				}
				$db->db_exec("UPDATE inv_orders SET sub_total='".(float)$sub_total."',items_true_cost='".(float)$items_true_cost."' WHERE order_id='".$OrderID."'");

			} else {

				$old_status = $db->func_query_first_cell("SELECT order_status FROM inv_orders WHERE id='".$orderExist."'");

				$db->db_exec("Update inv_orders SET   order_price = '$OrderTotal',paid_price='".$OrderTotal."' , customer_name = '" . $CustName . "' , record_number = '$SellingManagerSalesRecordNumber' , order_status = '$OrderStatus' Where id = '$orderExist'");
				$db->db_exec("Update inv_orders_items SET    ostatus = '".strtolower($OrderStatus)."' Where order_id = '".$OrderID."'");

				if(strtolower($OrderStatus)=='shipped' && strtolower($old_status)!='shipped')
				{
				
						$this->rest_api('shipped',$OrderID);
					$inventory->updateInventoryShipped($OrderID,'shipped');
				}

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

	  private function rest_api($method='shipped',$order_id)
        {
            // echo $order_id;exit;
            $ch = curl_init (); // Initialising cURL
    $options = Array(
    CURLOPT_RETURNTRANSFER => TRUE, // Setting cURL's option to return the webpage data
    CURLOPT_FOLLOWLOCATION => TRUE, // Setting cURL to follow 'location' HTTP headers
    CURLOPT_AUTOREFERER => TRUE, // Automatically set the referer where following 'location' HTTP headers
    CURLOPT_CONNECTTIMEOUT => 120, // Setting the amount of time (in seconds) before the request times out
    CURLOPT_TIMEOUT => 120, // Setting the maximum amount of time for cURL to execute queries
    CURLOPT_MAXREDIRS => 10, // Setting the maximum number of redirections to follow
    CURLOPT_HTTPHEADER=> array(
    'Content-type: application/x-www-form-urlencoded'),
    CURLOPT_POST=> 1,
    CURLOPT_POSTFIELDS=>'order_id='.$order_id.'&method='.$method,
    CURLOPT_URL => 'http://imp.phonepartsusa.com/inventory/rest.php' ); // Setting cURL's URL option with the $url variable passed into the function
    // $options2 = array();
    
    curl_setopt_array ( $ch, $options ); // Setting cURL's options using the previously assigned array data in $options
    $data = curl_exec ( $ch ); // Executing the cURL request and assigning the returned data to the $data variable
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    // echo $httpCode;exit;
    curl_close ( $ch ); // Closing cURL
    // echo $data;exit;
    return $data; // Returning the data from the function
        }



	public function getTrueCost($sku) {

		global $db;

		$true_cost = 0.00;

		$main_sku = $db->func_query_first_cell("SELECT main_sku FROM oc_product WHERE model='".$sku."'");

		if($main_sku)

		{

			$sku =$main_sku;

		}

		$cost = $db->func_query_first("SELECT  cost_date,raw_cost,ex_rate,shipping_fee FROM inv_product_costs WHERE sku='" . $sku . "' ORDER BY id DESC limit 1");

		if($cost)

		{

			$true_cost = ($cost['raw_cost'] + $cost['shipping_fee']) / $cost['ex_rate'];

			$true_cost = round($true_cost, 2);

		}

		return $true_cost;

	}



	public function geteBayTime($siteId = 0) {

		$verb = 'GeteBayOfficialTime';

		///Build the request Xml string

		$requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';

		$requestXmlBody .= '<GeteBayOfficialTimeRequest xmlns="urn:ebay:apis:eBLBaseComponents">';

		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>" . $this->userToken . "</eBayAuthToken></RequesterCredentials>";

		$requestXmlBody .= '</GeteBayOfficialTimeRequest>';



		$session = new eBaySession($this->userToken, $this->devID, $this->appID, $this->certID, $this->serverUrl, $this->compatabilityLevel, $siteId, $verb);



		//send the request and get response

		$responseXml = $session->sendHttpRequest($requestXmlBody);

		if (stristr($responseXml, 'HTTP 404') || $responseXml == '')

			die('<P>Error sending request');



		$responseObj = @simplexml_load_string($responseXml) or die(htmlspecialchars($responseXml));

		$Timestamp = $responseObj->Timestamp;

		return $Timestamp;

	}



	public function reviseInventoryStatus($token, $ItemID, $qty, $siteId = 0) {

		global $db;



		if (!$ItemID) {

			return -2;

		}



		$verb = 'ReviseInventoryStatus';

		///Build the request Xml string

		$requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';

		$requestXmlBody .= '<ReviseInventoryStatusRequest xmlns="urn:ebay:apis:eBLBaseComponents">';

		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>" . $token . "</eBayAuthToken></RequesterCredentials>";

		$requestXmlBody .= '<InventoryStatus>';

		$requestXmlBody .= '<ItemID>' . $ItemID . '</ItemID>';

		$requestXmlBody .= '<Quantity>' . $qty . '</Quantity>';

		$requestXmlBody .= '</InventoryStatus>';

		$requestXmlBody .= '</ReviseInventoryStatusRequest>';



		$session = new eBaySession($this->userToken, $this->devID, $this->appID, $this->certID, $this->serverUrl, $this->compatabilityLevel, $siteId, $verb);



		//send the request and get response

		do {

			$responseXml = $session->sendHttpRequest($requestXmlBody);

		} while (!$responseXml);

		$responseObj = @simplexml_load_string($responseXml);



		#Get any error nodes

		$errors = $responseObj->Errors;

		if (count($errors) > 0 && $responseObj->Ack == 'Failure') {

			$errorMsg = "<P><B>Errors occured while update item on ebay</B> <br />";

			for ($n = 0; $n < sizeof($errors); $n++) {

				$code = $errors[$n]->ErrorCode;

				$shortMsg = $errors[$n]->ShortMessage;

				$longMsg = $errors[$n]->LongMessage;

				$ErrorParameters = $errors[$n]->ErrorParameters;

				$ErrorParametersValue = $ErrorParameters->Value;

				$errorMsg .= $code . ": $ErrorParametersValue " . $longMsg . " <br />";

			}



			$errorMsg = $errorMsg; // . "--"."---".$requestXmlBody."---".$responseXml;

			return $errorMsg;

		} else {

			return 1;

		}

	}



	public function eBayReviseItem($token, $ItemID, $qty, $siteId = 0) {

		global $db;



		if (!$ItemID || !$qty) {

			return 0;

		}



		$verb = 'ReviseItem';

		///Build the request Xml string

		$requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';

		$requestXmlBody .= '<ReviseItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';

		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>" . $token . "</eBayAuthToken></RequesterCredentials>";

		$requestXmlBody .= '<Item>';

		$requestXmlBody .= '<ItemID>' . $ItemID . '</ItemID>';

		$requestXmlBody .= '<Quantity>' . $qty . '</Quantity>';

		$requestXmlBody .= '</Item>';

		$requestXmlBody .= '</ReviseItemRequest>';



		$session = new eBaySession($this->userToken, $this->devID, $this->appID, $this->certID, $this->serverUrl, $this->compatabilityLevel, $siteId, $verb);



		//send the request and get response

		do {

			$responseXml = $session->sendHttpRequest($requestXmlBody);

		} while (!$responseXml);

		$responseObj = @simplexml_load_string($responseXml);



		#Get any error nodes

		$errors = $responseObj->Errors;

		if (count($errors) > 0 && $responseObj->Ack == 'Failure') {

			$errorMsg = "<P><B>Errors occured while update item on ebay</B> <br />";

			for ($n = 0; $n < sizeof($errors); $n++) {

				$code = $errors[$n]->ErrorCode;

				$shortMsg = $errors[$n]->ShortMessage;

				$longMsg = $errors[$n]->LongMessage;

				$ErrorParameters = $errors[$n]->ErrorParameters;

				$ErrorParametersValue = $ErrorParameters->Value;

				$errorMsg .= $code . ": $ErrorParametersValue " . $longMsg . " <br />";

			}



			$errorMsg = $errorMsg; // . "--"."---".$requestXmlBody."---".$responseXml;

			return $errorMsg;

		} else {

			return 1;

		}

	}



	public function eBayRelistItem($token, $ItemID, $siteId = 0) {

		global $db;



		if (!$ItemID) {

			return 0;

		}



		$verb = 'RelistItem';

		///Build the request Xml string

		$requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';

		$requestXmlBody .= '<RelistItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';

		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>" . $token . "</eBayAuthToken></RequesterCredentials>";

		$requestXmlBody .= '<Item>';

		$requestXmlBody .= '<ItemID>' . $ItemID . '</ItemID>';

		$requestXmlBody .= '</Item>';

		$requestXmlBody .= '</RelistItemRequest>';



		$session = new eBaySession($this->userToken, $this->devID, $this->appID, $this->certID, $this->serverUrl, $this->compatabilityLevel, $siteId, $verb);



		//send the request and get response

		do {

			$responseXml = $session->sendHttpRequest($requestXmlBody);

		} while (!$responseXml);

		$responseObj = @simplexml_load_string($responseXml);



		#Get any error nodes

		$errors = $responseObj->Errors;

		if (count($errors) > 0 && $responseObj->Ack == 'Failure') {

			$errorMsg = "<P><B>Errors occured while RelistItem on ebay</B> <br />";

			for ($n = 0; $n < sizeof($errors); $n++) {

				$code = $errors[$n]->ErrorCode;

				$shortMsg = $errors[$n]->ShortMessage;

				$longMsg = $errors[$n]->LongMessage;

				$ErrorParameters = $errors[$n]->ErrorParameters;

				$ErrorParametersValue = $ErrorParameters->Value;

				$errorMsg .= $code . ": $ErrorParametersValue " . $longMsg . " <br />";

			}



			$errorMsg = $errorMsg; // . "--"."---".$requestXmlBody."---".$responseXml;

			return $errorMsg;

		} else {

			$newItemID = $responseObj->ItemID;



			$db->db_exec("Update ebay_mapping SET ebay_item_id = '$newItemID' , item_status = 'listed' , dateofmodification = '" . date('Y-m-d H:i:s') . "' Where ebay_item_id = '$ItemID'");



			return $newItemID;

		}

	}



	/**

	 * End ebay Item

	 * @param BigInt $itemId

	 * @param String $sellerToken

	 * @param Integer $siteId

	 */

	public function endItem($ItemID, $sellerToken, $siteId = 0) {

		global $db;



		$verb = 'EndItem';



		$requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';

		$requestXmlBody .= '<EndItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';

		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>" . $sellerToken . "</eBayAuthToken></RequesterCredentials>";

		$requestXmlBody .= "<ItemID>" . $ItemID . "</ItemID><EndingReason>NotAvailable</EndingReason>";

		$requestXmlBody .= "</EndItemRequest>";



		$session = new eBaySession($this->userToken, $this->devID, $this->appID, $this->certID, $this->serverUrl, $this->compatabilityLevel, $siteId, $verb);



		//send the request and get response

		do {

			$responseXml = $session->sendHttpRequest($requestXmlBody);

		} while (!$responseXml);



		$responseObj = @simplexml_load_string($responseXml);

		if ($responseObj) {

			#Get any error nodes

			$errors = $responseObj->Errors;

			if (count($errors) > 0 && $responseObj->Ack == 'Failure') {

				$errorMsg = "<P><B>Errors occured while ending item</B> <br />";

				for ($n = 0; $n < sizeof($errors); $n++) {

					$code = $errors[$n]->ErrorCode;

					$shortMsg = $errors[$n]->ShortMessage;

					$longMsg = $errors[$n]->LongMessage;

					$ErrorParameters = $errors[$n]->ErrorParameters;

					$ErrorParametersValue = $ErrorParameters->Value;

					$errorMsg .= $code . ": $ErrorParametersValue " . $longMsg . " <br />";

				}



				$errorMsg = $errorMsg; // ."---".$requestXmlBody."---".$responseXml;

				return $errorMsg;

			} else {

				$db->db_exec("Update ebay_mapping SET item_status = 'ended' , dateofmodification = '" . date('Y-m-d H:i:s') . "' Where ebay_item_id = '$ItemID'");



				return 1;

			}

		}

	}



	/**

	 * Fetch ebay item list

	 * @param $usertoken

	 * @param $store_id

	 * @param $page

	 * @param $siteId

	 */

	public function getItem($usertoken, $product_sku, $siteId = 0) {

		global $db;

		$verb = 'GetItem';



		$requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';

		$requestXmlBody .= '<GetItemRequest xmlns="urn:ebay:apis:eBLBaseComponents">';

		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>" . $usertoken . "</eBayAuthToken></RequesterCredentials>";

		$requestXmlBody .= "<Version>" . $this->compatabilityLevel . "</Version>";

		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';

		$requestXmlBody .= '<WarningLevel>High</WarningLevel>';

		$requestXmlBody .= "<SKU>" . $product_sku . "</SKU>";

		$requestXmlBody .= "</GetItemRequest>";



		$session = new eBaySession($this->userToken, $this->devID, $this->appID, $this->certID, $this->serverUrl, $this->compatabilityLevel, $siteId, $verb);



		//send the request and get response

		do {

			$responseXml = $session->sendHttpRequest($requestXmlBody);

		} while (!$responseXml);



		$responseObj = @simplexml_load_string($responseXml);

		if ($responseObj) {

			#Get any error nodes

			$errors = $responseObj->Errors;

			if (count($errors) > 0) {

				//$errorMsg = "<P><B>Errors occured while fetching seller active listing</B> <br />";



				for ($n = 0; $n < sizeof($errors); $n++) {

					$code = $errors[$n]->ErrorCode;

					$shortMsg = $errors[$n]->ShortMessage;

					$longMsg = $errors[$n]->LongMessage;

					$ErrorParameters = $errors[$n]->ErrorParameters;

					$ErrorParametersValue = $ErrorParameters->Value;

					$errorMsg .= $code . ": $ErrorParametersValue " . $longMsg . " <br />";

				}



				return $errorMsg;

			} else {

				return $responseObj->Item;

			}

		}

	}



	/**

	 * Fetch ebay store listing and insert into ebay mapping table

	 * @param $usertoken

	 * @param $store_id

	 * @param $page

	 * @param $siteId

	 */

	public function getMyeBaySelling($usertoken, $page = 1, $siteId = 0) {

		global $db;

		$verb = 'GetMyeBaySelling';



		$requestXmlBody = '<?xml version="1.0" encoding="utf-8" ?>';

		$requestXmlBody .= '<GetMyeBaySellingRequest xmlns="urn:ebay:apis:eBLBaseComponents">';

		$requestXmlBody .= "<RequesterCredentials><eBayAuthToken>" . $usertoken . "</eBayAuthToken></RequesterCredentials>";

		$requestXmlBody .= "<Version>" . $this->compatabilityLevel . "</Version>";

		$requestXmlBody .= '<ErrorLanguage>en_US</ErrorLanguage>';

		$requestXmlBody .= '<WarningLevel>High</WarningLevel>';

		$requestXmlBody .= "<ActiveList>

		<Sort>TimeLeft</Sort>

		<Pagination><EntriesPerPage>100</EntriesPerPage>

			<PageNumber>" . $page . "</PageNumber>

		</Pagination>

	</ActiveList>

</GetMyeBaySellingRequest>";



$session = new eBaySession($this->userToken, $this->devID, $this->appID, $this->certID, $this->serverUrl, $this->compatabilityLevel, $siteId, $verb);



		//send the request and get response

do {

	$responseXml = $session->sendHttpRequest($requestXmlBody);

} while (!$responseXml);

$responseObj = @simplexml_load_string($responseXml);



if ($responseObj) {

			#Get any error nodes

	$errors = $responseObj->Errors;

	if (count($errors) > 0) {

		$errorMsg = "<P><B>Errors occured while fetching seller active listing</B> <br />";



		for ($n = 0; $n < sizeof($errors); $n++) {

			$code = $errors[$n]->ErrorCode;

			$shortMsg = $errors[$n]->ShortMessage;

			$longMsg = $errors[$n]->LongMessage;

			$ErrorParameters = $errors[$n]->ErrorParameters;

			$ErrorParametersValue = $ErrorParameters->Value;

			$errorMsg .= $code . ": $ErrorParametersValue " . $longMsg . " <br />";

		}



		$errorMsg = $errorMsg . "--" . $ebay_credential_id . "--" . "---" . $requestXmlBody . "---" . $responseXml;

		mail("xaman.riaz@gmail.com", "IMP error", $errorMsg);

		return $errorMsg;

	} else {

		$Items = $responseObj->ActiveList->ItemArray->Item;

		foreach ($Items as $item) {

			$this->insertItem($item, 1);

		}



				#Get total number of pages

		$pages = $responseObj->ActiveList->PaginationResult->TotalNumberOfPages;

				#Handle the pagignation for all transactions

		if ($pages > $page) {

			$page = $page + 1;

			$this->getMyeBaySelling($usertoken, $page, $siteId);

		}

	}



	return 1;

}

}



public function insertItem($item, $status = 1) {

	global $db;



	$itemId = (string) $item->ItemID;

	if (!$itemId) {

		return false;

	}



	$sku = $item->SKU;

	if (!$sku) {

		$sku = $item->ProductListingDetails->BrandMPN->MPN;

	}



	$sku = $db->func_escape_string($sku);

	$dateofmodification = date('Y-m-d H:i:s');



	if($sku and $itemId){

			//insert here in db

		$ebay_listing_id = $db->func_query_first_cell("select id from ebay_mapping where ebay_item_id = '" . $db->func_escape_string($itemId) . "'");

		if (!$ebay_listing_id) {

			$db->db_exec("insert into ebay_mapping(ebay_item_id,product_sku,item_status,dateofmodification) values ('$itemId','$sku','listed','$dateofmodification')");

			$ebay_listing_id = $db->db_insert_id();

		} else {

			$db->db_exec("update ebay_mapping set ebay_item_id = '" . $itemId . "', dateofmodification='" . $dateofmodification . "' where product_sku = '" . $sku . "'");

		}

	}



	return l;

}

}