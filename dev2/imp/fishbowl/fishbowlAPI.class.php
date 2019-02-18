<?php
/**
 * @package : FishbowlPI
 * @author : dnewsom <dave.newsom@fishbowlinventory.com>
 * @author : kbatchelor <kevin.batchelor@fishbowlinventory.com>
 * @version : 1.2
 * @date : 2010-04-29
 *
 * Utility routines for Fishbowls API
 */

class FishbowlAPI {
	public $result;
	public $statusCode;
	public $statusMsg;
	public $loggedIn;
	public $userRights;
	private $xmlRequest;
	private $xmlResponse;
	private $id;
	private $key;
	private $fbErrorCodes;

	/**
	 * Create the connection to Fishbowl
	 * @param string $host - Fishbowl host
	 * @param string $port - Fishbowl port
	 */
	public function __construct($host, $port) {
		$this->host = $host;
		$this->port = $port;

		$this->id = fsockopen($this->host, $this->port);
		$this->fbErrorCodes = new FBErrorCodes();
	}

	/**
	 * Close the connection
	 */
	public function closeConnection() {
		fclose($this->id);
	}

	/**
	 * Login to Fishbowl
	 * @param string $user - Pass in the username on login
	 * @param string $pass - Pass in the password on login
	 */
	public function login($user = null, $pass = null) {
		if (!is_null($user)) {
			$this->user = $user;
		}
		if (!is_null($pass)) {
			$this->pass = base64_encode(md5($pass, true));
		}
		// Parse XML
		$this->xmlRequest = "<FbiXml>\n".
                            "    <Ticket/>\n" .
                            "    <FbiMsgsRq>\n" .
                            "        <LoginRq>\n" .
                            "            <IAID>" . APP_KEY . "</IAID>\n" .
                            "            <IAName>" . APP_NAME . "</IAName>\n" .
                            "            <IADescription>" . APP_DESCRIPTION . "</IADescription>\n" .
                            "            <UserName>" . $this->user . "</UserName>\n" .
                            "            <UserPassword>" . $this->pass . "</UserPassword>\n" .
                            "        </LoginRq>\n" .
                            "    </FbiMsgsRq>\n" .
                            "</FbiXml>";

		// Pack for sending
		$len = strlen($this->xmlRequest);
		$packed = pack("N", $len);

		// Send and get the response
		fwrite($this->id, $packed, 4);
		fwrite($this->id, $this->xmlRequest);
		$this->getResponse();

		// Set the result
		$this->setResult($this->parseXML($this->xmlResponse));
		$this->setStatus('LoginRs');

		if ($this->statusCode == 1000) {
			// Set the key
			$this->key = $this->result['Ticket']['Key'];
			$this->loggedIn = true;
			$this->userRights = $this->result['FbiMsgsRs']['LoginRs']['ModuleAccess']['Module'];
		} else {
			$this->loggedIn = false;
		}
	}

	/**
	 * Get customer information
	 * @param string $type - What type of call are you running. Default is NameList
	 * @param string $name - If your getting a specific customer you must pass in a name
	 */
	public function getCustomer($type = 'NameList', $name = null) {
		// Setup XML
		if ($type == "Get") {
			$xml = "<CustomerGetRq>\n<Name>{$name}</Name>\n</CustomerGetRq>\n";
			$status = 'CustomerGetRs';
		} elseif ($type == "List") {
			$xml = "<CustomerListRq></CustomerListRq>\n";
			$status = 'CustomerListRs';
		} else {
			$xml = "<CustomerNameListRq></CustomerNameListRq>\n";
			$status = 'CustomerNameListRs';
		}

		// Create request and pack
		$this->createRequest($xml);
		$len = strlen($this->xmlRequest);
		$packed = pack("N", $len);

		// Send and get the response
		fwrite($this->id, $packed, 4);
		fwrite($this->id, $this->xmlRequest);
		$this->getResponse();

		return $this->parseXML($this->xmlResponse);

		// Set the result
		//$this->setResult($this->parseXML($this->xmlResponse));
		//$this->setStatus($status);
	}

	/**
	 * Get vendor information
	 * @param string $type - What type of call are you running. Default is NameList
	 * @param string $name - If your getting a specific vendor you must pass in a name
	 */
	function getVendor($type = 'NameList', $name = null) {
		if ($type == "Get") {
			$xml = "<VendorGetRq>\n<Name>{$name}</Name>\n</VendorGetRq>\n";
			$status = "VendorGetRs";
		} elseif ($type == "List") {
			$xml = "<VendorListRq></VendorListRq>\n";
			$status = "VendorListRs";
		} else {
			$xml = "<VendorNameListRq></VendorNameListRq>\n";
			$status = "VendorNameListRs";
		}

		// Create request and pack
		$this->createRequest($xml);
		$len = strlen($this->xmlRequest);
		$packed = pack("N", $len);

		// Send and get the response
		fwrite($this->id, $packed, 4);
		fwrite($this->id, $this->xmlRequest);
		$this->getResponse();

		// Set the result
		$this->setResult($this->parseXML($this->xmlResponse));
		$this->setStatus($status);
	}

	/**
	 * Get product information
	 * @param string $type
	 * @param string $productNum
	 * @param integer $getImage
	 * @param string $upc
	 */
	public function getProducts($type = 'Get', $productNum = 'B201', $getImage = 0, $upc = null) {
		// Setup XML
		if ($type == "Get") {
			$xml = "<ProductGetRq>\n" .
                   "    <Number>{$productNum}</Number>\n" .
                   "    <GetImage>{$getImage}</GetImage>\n" .
                   "</ProductGetRq>\n";
		} elseif ($type == "Query") {
			$xml = "<ProductQueryRq>\n";
			if ($upc != null) {
				$xml .= "    <UPC>{$upc}</UPC>\n";
			} else {
				$xml .= "    <ProductNum>{$productNum}</ProductNum>\n";
			}
			$xml .= "    <GetImage>{$getImage}</GetImage>\n" .
                    "</ProductQueryRq>\n";
		}

		// Create request and pack
		$this->createRequest($xml);
		$len = strlen($this->xmlRequest);
		$packed = pack("N", $len);

		// Send and get the response
		fwrite($this->id, $packed, 4);
		fwrite($this->id, $this->xmlRequest);
		$this->getResponse();

		// Set the result
		$this->setResult($this->parseXML($this->xmlResponse));
		$this->setStatus('ProductQueryRs');
	}

	/**
	 * Get list of SO's by location group
	 * @param string $LocationGroup
	 */
	public function getSOList($LocationGroup = 'SLC') {
		// Parse XML
		$xml = "<GetSOListRq>\n<LocationGroup>{$LocationGroup}</LocationGroup>\n</GetSOListRq>\n";

		// Create request and pack
		$this->createRequest($xml);
		$len = strlen($this->xmlRequest);
		$packed = pack("N", $len);

		// Send and get the response
		fwrite($this->id, $packed, 4);
		fwrite($this->id, $this->xmlRequest);
		$this->getResponse();

		// Set the result
		$this->setResult($this->parseXML($this->xmlResponse));
		$this->setStatus('GetSOListRs');
	}

	/**
	 * Loads SO for a given number
	 * @param string $number
	 */
	public function getSO($number = '50032') {
		// Parse XML
		$xml = "<LoadSORq>\n<Number>{$number}</Number>\n</LoadSORq>\n";

		// Create request and pack
		$this->createRequest($xml);
		$len = strlen($this->xmlRequest);
		$packed = pack("N", $len);

		// Send and get the response
		fwrite($this->id, $packed, 4);
		fwrite($this->id, $this->xmlRequest);
		$this->getResponse();

		// Set the result
		$this->setResult($this->parseXML($this->xmlResponse));
		$this->setStatus('LoadSORs');
	}

	/**
	 * Get part information. Can be search by either PartNum or UPC
	 * @param string $partNum - Pass in if you're searching for PartNum or pass in null
	 * @param string $upc - Pass in if you're searching for UPC or pass in null
	 */
	public function getPart($partNum = null, $upc = null) {
		// Setup xml
		$xml = "<PartGetRq>\n";
		if (!is_null($partNum)) {
			$xml .= "<Number>{$partNum}</Number>\n";
		} else {
			$xml .= "<Number>{$upc}</Number>\n";
		}
		$xml .= "</PartGetRq>\n";

		// Create request and pack
		$this->createRequest($xml);
		$len = strlen($this->xmlRequest);
		$packed = pack("N", $len);

		// Send and get the response
		fwrite($this->id, $packed, 4);
		fwrite($this->id, $this->xmlRequest);
		$this->getResponse();

		// Set the result
		$this->setResult($this->parseXML($this->xmlResponse));
		$this->setStatus('PartGetRs');
	}

	/**
	 * Get inventory quantity information for a part
	 * $param string $partNum
	 */
	public function getInvQty($partNum) {
		// Setup xml
		$xml = "<InvQtyRq>\n<PartNum>{$partNum}</PartNum>\n</InvQtyRq>\n";

		// Create request and pack
		$this->createRequest($xml);
		$len = strlen($this->xmlRequest);
		$packed = pack("N", $len);

		// Send and get the response
		fwrite($this->id, $packed, 4);
		fwrite($this->id, $this->xmlRequest);
		$this->getResponse();

		// Set the result
		$this->setResult($this->parseXML($this->xmlResponse));
		$this->setStatus('InvQtyRs');
	}

	/**
	 * Parse xml data and store the results
	 */
	private function parseXML($xml, $recursive = false, $cust = false) {
		if (!$recursive) {
			$array = simplexml_load_string($xml);
		} else {
			$array = $xml;
		}

		$newArray = array();
		$array = (array) $array;

		foreach ($array as $key=>$value) {
			$value = (array) $value;
			if (isset($value[0])) {
				if (count($value) > 1) {
					$newArray[$key] = (array) $value;
				} else {
					$newArray[$key] = trim($value[0]);
				}
			} else {
				$newArray[$key] = $this->parseXML($value, true);
			}
		}
		return $newArray;
	}

	/**
	 * Set the XML Request
	 * @param string $xmlData
	 */
	private function createRequest($xmlData) {
		$this->xmlRequest = $this->xmlHeader() . $xmlData . $this->xmlFooter();
	}

	/**
	 * Create XML header
	 */
	private function xmlHeader() {
		$xml = "<FbiXml>\n<Ticket>\n<UserID>1</UserID>\n<Key>{$this->key}</Key>\n</Ticket>\n<FbiMsgsRq>\n";
		return $xml;
	}

	/**
	 * Create XML foorter
	 */
	private function xmlFooter() {
		$xml = "</FbiMsgsRq>\n</FbiXml>\n";
		return $xml;
	}

	/**
	 * Determine the length (in bytes) of our reponse and stream it.
	 */
	private function getResponse() {
		$packed_len = stream_get_contents($this->id, 4); //The first 4 bytes contain our N-packed length
		$hdr = unpack('Nlen', $packed_len);
		$len = $hdr['len'];
		$this->xmlResponse = stream_get_contents($this->id, $len);
	}

	/**
	 * Set the results from a response
	 * @param array $res - This should be the parsed response from the server
	 */
	private function setResult($res) {
		$this->result = $res;
	}

	/**
	 * Set the status code and message for the responses
	 * @param string $response - This should be the response name to get the code and message from
	 */
	private function setStatus($response) {
		if (isset($this->result[$response])) {
			$this->statusCode = $this->result[$response]['@attributes']['statusCode'];
			$this->statusMsg = $this->result[$response]['@attributes']['statusMessage'];
		}
		else{
			$this->statusCode = $this->result['FbiMsgsRs']['@attributes']['statusCode'];
			$this->statusMsg  = @$this->result['FbiMsgsRs']['@attributes']['statusMessage'];
		}

		if ($this->statusCode == 1000) {
			$this->statusMsg = 'Success';
		}
	}

	/**
	 * Generate the request to send to Fishbowl from an object
	 * @param string $name
	 * @param array $array
	 */
	private function generateRequest($array, $name, $subname = null) {
		//star and end the XML document
		$this->xmlRequest = "<{$name}>\n";
		if (!is_null($subname)) {
			$this->xmlRequest .= "\t<{$subname}>\n";
		}
		$this->generateXML($array);
		if (!is_null($subname)) {
			$this->xmlRequest .= "\t</{$subname}>\n";
		}
		$this->xmlRequest .= "</{$name}>";
		return $this->xmlRequest;
	}

	/**
	 * Generate XML from an array
	 * @param array $array
	 */
	private function generateXML($array) {
		static $Depth = 0;
		$Tabs = "";

		// Check if this is the top value
		if (isset($array->data)) {
			$array = $array->data;
		}

		foreach($array as $key => $value){
			unset($Tabs);

			// We want to have arrays, if we find an object we need to convert it
			if (is_object($value)) {
				$value = (array) $value;
			}

			// Check if the node is an array or object
			if (!is_array($value)) {
				// Add tabs so it's readable
				for ($i=1; $i<=$Depth+1; $i++) {
					$Tabs .= "\t";
				}
				if (preg_match("/^[0-9]\$/",$key)) {
					$key = "n{$key}";
				}

				// Add to the XML request
				$this->xmlRequest .= "{$Tabs}<{$key}>{$value}</{$key}>\n";
			} else {
				// Add tabs so it's readable
				$Depth += 1;
				for ($i=1; $i<=$Depth; $i++) {
					$Tabs .= "\t";
				}

				// Add to the XML request and send it to the next level
				$this->xmlRequest .= "{$Tabs}<{$key}>\n";
				$this->generateXML($value);
				$this->xmlRequest .= "{$Tabs}</{$key}>\n";
				$Depth -= 1;
			}
		}
		return true;
	}

	/**
	 * Check if the user has rights to functions
	 * @param string $module
	 * @param string $right
	 */
	public function checkAccessRights($module, $right) {
		// Check if the user is admin
		if ($this->user == 'admin') {
			return true;
		}
			
		// Check if the user has an rights
		if (!is_array($this->userRights)) {
			return false;
		}
			
		// Create the access right
		$accessRight = $module . "-" . $right;
		if (in_array($accessRight, $this->userRights)) {
			return true;
		} else {
			return false;
		}
	}



	/**
	 * Insert new SO order
	 * @param Array $orderDetail
	 */
	public function saveSOOrder($orderDetail){
		//$orderDetail['order_id'] = str_ireplace("-","",$orderDetail['order_id']);
		$orderDetail['first_name'] = $this->replaceSpecial($orderDetail['first_name']);
		$orderDetail['last_name'] = $this->replaceSpecial($orderDetail['last_name']);
		$orderDetail['address1'] = $this->replaceSpecial($orderDetail['address1']);
		$orderDetail['city'] = $this->replaceSpecial($orderDetail['city']);
		$orderDetail['state'] = $this->replaceSpecial($orderDetail['state']);

		if($orderDetail['store_type'] == 'ebay' && strlen($orderDetail['record_number']) > 2){
			$orderDetail['order_id'] = $orderDetail['record_number'];
		}

		$order_status_id = 20; // default as issued
		//$orderDetail['order_id'] == '264162' &&
		if($orderDetail['store_type'] == 'web' and  $orderDetail['shipping_method'] == 'Local Las Vegas Store Pickup - 9:30am-4:30pm - Call Us'){
			print $orderDetail['order_id'] . "---". $orderDetail['order_status'] . "<br />";
				
			if(in_array($orderDetail['order_status'],array('Processed'))){
				$order_status_id = 20; // In Progress
			}
			elseif(in_array($orderDetail['order_status'],array('Shipped'))){
				$order_status_id = 60; // Fulfilled
				//check here if order exist
				$orderExist = $this->loadSOOrder($orderDetail['order_id']);
				if(isset($orderExist['FbiMsgsRs']['LoadSORs']) AND isset($orderExist['FbiMsgsRs']['LoadSORs']['SalesOrder'])){
					if($orderExist['FbiMsgsRs']['LoadSORs']['SalesOrder']['Status'] != 60){
						$this->IssueSOOrder($orderDetail['order_id']);
						$response =  $this->quickShipOrder($orderDetail['order_id']);
						//print "<pre>";print_r($response);
						return array("result"=>$response);
					}
					else{
						$response = array();
						$response['FbiMsgsRs']['@attributes'] = array('statusCode'=>1000);
						$response['FbiMsgsRs']['SaveSORs'] = array('@attributes'=>array('statusCode'=>1000));

						return array("result"=>$response);
					}
				}
				else{
					//order is not added but direct shipped
				}
			}
			elseif(in_array($orderDetail['order_status'],array('Cancelled','Voided'))){
				$order_status_id = 80; // Voided
				//check here if order exist
				$orderExist = $this->loadSOOrder($orderDetail['order_id']);

				if(isset($orderExist['FbiMsgsRs']['LoadSORs']) AND isset($orderExist['FbiMsgsRs']['LoadSORs']['SalesOrder'])){
					if($orderExist['FbiMsgsRs']['LoadSORs']['SalesOrder']['Status'] != 60){
						$response = $this->voidSOOrder($orderDetail['order_id']);
						return array("result"=>$response);
					}
					else{
						return array("result"=>array($orderDetail['order_id'],"Order is already shipped, now can not voided."));
					}
				}
				else{
					//order is not added but direct voided
				}
			}
		}
		else{
			//check order exist or not
			$orderExist = $this->loadSOOrder($orderDetail['order_id']);
			if(isset($orderExist['FbiMsgsRs']['LoadSORs']) AND isset($orderExist['FbiMsgsRs']['LoadSORs']['SalesOrder'])){
				$order_status_id = $orderExist['FbiMsgsRs']['LoadSORs']['SalesOrder']['Status'];
				//60 Shipped
				//80 Voided
				//25 In Progress
				if($order_status_id == 60 || $order_status_id == 80 || $order_status_id == 25){
					$response = array();
					$response['FbiMsgsRs']['@attributes'] = array('statusCode'=>1000);
					$response['FbiMsgsRs']['SaveSORs'] = array('@attributes'=>array('statusCode'=>1000));
						
					return array("result"=>$response);
				}
			}
		}

		$xml = '<SOSaveRq>'.
                    '<SalesOrder>';
		$xml .= '<Number>'.$orderDetail['order_id'].'</Number>';

		$sales_order_items = '';
		foreach($orderDetail['Items'] as $index => $item){
			$sales_order_items .= '<SalesOrderItem>'.
                                    '<ID>-1</ID>'.
                                    '<ProductNumber>'.$item['product_sku'].'</ProductNumber>'.
                                    '<Description>'.$item['product_sku'].'</Description>'.
                                    '<Taxable>true</Taxable>'.  
                                    '<Quantity>'.$item['product_qty'].'</Quantity>'.
                                    '<ProductPrice>'.$item['product_price'].'</ProductPrice>'.
                                    '<TotalPrice>'.($item['product_price'] * $item['product_qty']).'</TotalPrice>'.
                                    '<UOMCode>ea</UOMCode>'.
                                    '<ItemType>10</ItemType>'.
                                    '<Status>10</Status>'.
                                    '<NewItemFlag>false</NewItemFlag>'.
                                    '<LineNumber>'.($index+1).'</LineNumber>'.
                                 '</SalesOrderItem>';
		}

		$xml    .= '<Salesman>admin</Salesman>'.
                    '<Status>'.$order_status_id.'</Status>'.
                    '<Carrier>Will Call</Carrier>'.
		//'<FirstShipDate>'.date('Y-m-d\TH:i:s',strtotime($orderDetail['order_date'])).'</FirstShipDate>'.
                    '<CreatedDate>'.date('Y-m-d\TH:i:s',strtotime($orderDetail['order_date'])).'</CreatedDate>'.
                    '<IssuedDate>'.date('Y-m-d\TH:i:s',strtotime($orderDetail['order_date'])).'</IssuedDate>'.
                    '<TaxRatePercentage>0.0625</TaxRatePercentage>'.
                    '<TaxRateName>Utah</TaxRateName>'.
                    '<ShippingTerms>Prepaid &amp; Billed</ShippingTerms>'.
                    '<PaymentTerms>'.$orderDetail['payment_method'].'</PaymentTerms>'.
                    '<CustomerContact>'.$orderDetail['first_name'] . ' ' . $orderDetail['last_name'].'</CustomerContact>'.
                    '<CustomerName>'.$orderDetail['first_name'] . ' ' . $orderDetail['last_name'].'</CustomerName>'.
                    '<FOB>Origin</FOB>'.
                    '<BillTo>'.
                        '<Name><![CDATA['.$orderDetail['first_name'] . ' ' . $orderDetail['last_name'].']]></Name>'.
                        '<AddressField><![CDATA['.$orderDetail['address1'].']]></AddressField>'.
                        '<City><![CDATA['.$orderDetail['city'].']]></City>'.
                        '<Zip><![CDATA['.$orderDetail['zip'].']]></Zip>'.
						'<Country><![CDATA['.$orderDetail['country'].']]></Country>'.
                        '<State><![CDATA['.$orderDetail['state'].']]></State>'.
                    '</BillTo>'.
                    '<Ship>'.
                        '<Name><![CDATA['.$orderDetail['first_name'] . ' ' . $orderDetail['last_name'].']]></Name>'.
                        '<AddressField><![CDATA['.$orderDetail['address1'].']]></AddressField>'.
                        '<City><![CDATA['.$orderDetail['city'].']]></City>'.
                        '<Zip><![CDATA['.$orderDetail['zip'].']]></Zip>'.
						'<Country><![CDATA['.$orderDetail['country'].']]></Country>'.
                        '<State><![CDATA['.$orderDetail['state'].']]></State>'.
                    '</Ship>'.
                    '<Items>'.
		$sales_order_items.
                    '</Items>'.
               '</SalesOrder>'.
               '<IssueFlag>false</IssueFlag>'.
         '</SOSaveRq>';

		// Create request and pack
		$this->createRequest($xml);
		$len = strlen($this->xmlRequest);
		$packed = pack("N", $len);

		// Send and get the response
		fwrite($this->id, $packed, 4);
		fwrite($this->id, $this->xmlRequest);
		$this->getResponse();

		$result = $this->parseXML($this->xmlResponse);
		$FbiMsgsRsStatus = $result['FbiMsgsRs']['@attributes']['statusCode'];
		$SaveSORsStatus  = $result['FbiMsgsRs']['SaveSORs']['@attributes']['statusCode'];

		if(!$SaveSORsStatus and $result['FbiMsgsRs'][0]){
			$attributes = $result['FbiMsgsRs'][0]->attributes();
			$SaveSORsStatus = $attributes['statusCode'];
			$SaveSORsMessage = $attributes['statusMessage'];
		}

		//order is added successfully. now need to update qty
		if ($FbiMsgsRsStatus == 1000 &&  $SaveSORsStatus == 1000) {
			$item_result = array();
			foreach($orderDetail['Items'] as $item){
				$qtyInHand  = $this->getPartQty($item['product_sku']);
				$qtyforSale = $this->getItemQty($item['product_sku']);
				$qtySold = (int)$item['product_qty'];

				if($orderDetail['is_updated'] == 0 || $orderDetail['status'] == 'open'){
					$qtyAvailable =  $qtyInHand - $qtySold;
					//$this->updateQty($orderDetail['product_name'] , $qtyAvailable);
				}
				else{
					$qtyAvailable = $qtyInHand;
				}

				$availQty = $qtyforSale - $qtySold;
				$item_result[] = array('sku' => $item['product_sku'] , 'qty' => $qtyforSale);
			}

			return array('result' => $result , 'items' => $item_result , 'Ack' => 'Success' , 'xml' => $this->xmlRequest);
		}
		else{
			return array('result' => $result , 'items' => '' , 'Ack' => 'Error' , 'xml' => $this->xmlRequest);
		}
	}

	public function getPartQty($PartNum){
		$xml = '<InvQtyRq>'.
                  '<PartNum>'.$PartNum.'</PartNum>'.
               '</InvQtyRq>';

		// Create request and pack
		$this->createRequest($xml);
		$len = strlen($this->xmlRequest);
		$packed = pack("N", $len);

		// Send and get the response
		fwrite($this->id, $packed, 4);
		fwrite($this->id, $this->xmlRequest);
		$this->getResponse();

		//Set the result
		$result = $this->parseXML($this->xmlResponse);
		$FbiMsgsRsStatus = $result['FbiMsgsRs']['@attributes']['statusCode'];
		$InvQtyRsStatus  = $result['FbiMsgsRs']['InvQtyRs']['@attributes']['statusCode'];
		if(!$InvQtyRsStatus && $result['FbiMsgsRs'][0]){
			$attributes = $result['FbiMsgsRs'][0]->attributes();
			$InvQtyRsStatus = $attributes['statusCode'];
		}

		if ($FbiMsgsRsStatus == 1000 &&  $InvQtyRsStatus == 1000) {
			$qty = $result['FbiMsgsRs']['InvQtyRs']['InvQty']['QtyAvailable'];
		}
		elseif ($FbiMsgsRsStatus == 1000 &&  $InvQtyRsStatus == 1012) {
			return 'notExist';
		}
		else{
			return 'FBStopped - ' . $FbiMsgsRsStatus . ' - '. $InvQtyRsStatus;
		}

		return $qty;
	}

	public function getItemQty($PartNum){
		$xml = '<GetTotalInventoryRq>'.
                  '<PartNumber>'.$PartNum.'</PartNumber>'.
                  '<LocationGroup>Main</LocationGroup>'.
               '</GetTotalInventoryRq>';

		// Create request and pack
		$this->createRequest($xml);
		$len = strlen($this->xmlRequest);
		$packed = pack("N", $len);

		// Send and get the response
		fwrite($this->id, $packed, 4);
		fwrite($this->id, $this->xmlRequest);
		$this->getResponse();

		//Set the result
		$result = $this->parseXML($this->xmlResponse);
		$FbiMsgsRsStatus = $result['FbiMsgsRs']['@attributes']['statusCode'];
		$InvQtyRsStatus  = $result['FbiMsgsRs']['GetTotalInventoryRs']['@attributes']['statusCode'];
		if(!$InvQtyRsStatus && $result['FbiMsgsRs'][0]){
			$attributes = $result['FbiMsgsRs'][0]->attributes();
			$InvQtyRsStatus = $attributes['statusCode'];
		}

		if ($FbiMsgsRsStatus == 1000 &&  $InvQtyRsStatus == 1000) {
			$qty = $result['FbiMsgsRs']['GetTotalInventoryRs']['Available'];
		}
		elseif ($FbiMsgsRsStatus == 1000 &&  $InvQtyRsStatus == 1012) {
			return 'notExist';
		}
		else{
			return 'FBStopped';
		}

		return $qty;
	}

	public function replaceSpecial($str){
		$str = trim($str);
		$chunked = str_split($str,1);
		$str = "";
		foreach($chunked as $chunk){
			$num = ord($chunk);
			// Remove non-ascii & non html characters
			if ($num >= 32 && $num <= 123){
				$str.=$chunk;
			}
		}

		return $str;
	}

	public function updateQty($PartNum , $qty){
		$xml = '<CycleCountRq>'.
                  '<PartNum>'.$PartNum.'</PartNum>'.
                  '<Quantity>'.$qty.'</Quantity>'.
                  '<LocationID>1</LocationID>'.
               '</CycleCountRq>';

		// Create request and pack
		$this->createRequest($xml);
		$len = strlen($this->xmlRequest);
		$packed = pack("N", $len);

		// Send and get the response
		fwrite($this->id, $packed, 4);
		fwrite($this->id, $this->xmlRequest);
		$this->getResponse();

		//Set the result
		$result = $this->parseXML($this->xmlResponse);

		$FbiMsgsRsStatus = $result['FbiMsgsRs']['@attributes']['statusCode'];
		$CycleCountRsStatus  = $result['FbiMsgsRs']['CycleCountRs']['@attributes']['statusCode'];
		if(!$CycleCountRsStatus && $result['FbiMsgsRs'][0]){
			$attributes = $result['FbiMsgsRs'][0]->attributes();
			$CycleCountRsStatus = $attributes['statusCode'];
		}

		if ($FbiMsgsRsStatus == 1000 &&  $CycleCountRsStatus == 1000) {
			return 1;
		}
		else{
			return 'FBStopped';
		}
	}

	public function voidSOOrder($SONumber){
		$xml = '<VoidSORq>'.
                  '<SONumber>'.$SONumber.'</SONumber>'.
               '</VoidSORq>';

		// Create request and pack
		$this->createRequest($xml);
		$len = strlen($this->xmlRequest);
		$packed = pack("N", $len);

		// Send and get the response
		fwrite($this->id, $packed, 4);
		fwrite($this->id, $this->xmlRequest);
		$this->getResponse();

		// Set the result
		return $this->parseXML($this->xmlResponse);
		//$this->setResult($this->parseXML($this->xmlResponse));
	}

	public function loadSOOrder($SONumber){
		$xml = '<LoadSORq>'.
                  '<Number>'.$SONumber.'</Number>'.
               '</LoadSORq>';

		// Create request and pack
		$this->createRequest($xml);
		$len = strlen($this->xmlRequest);
		$packed = pack("N", $len);

		// Send and get the response
		fwrite($this->id, $packed, 4);
		fwrite($this->id, $this->xmlRequest);
		$this->getResponse();

		// Set the result
		return $this->parseXML($this->xmlResponse);
	}

	public function IssueSOOrder($SONumber){
		$xml = '<IssueSORq>'.
                  '<SONumber>'.$SONumber.'</SONumber>'.
               '</IssueSORq>';

		// Create request and pack
		$this->createRequest($xml);
		$len = strlen($this->xmlRequest);
		$packed = pack("N", $len);

		// Send and get the response
		fwrite($this->id, $packed, 4);
		fwrite($this->id, $this->xmlRequest);
		$this->getResponse();

		// Set the result
		return $this->parseXML($this->xmlResponse);
	}

	public function quickShipOrder($SONumber){
		$xml = '<QuickShipRq>'.
                  '<SONumber>'.$SONumber.'</SONumber>'.
               '</QuickShipRq>';

		// Create request and pack
		$this->createRequest($xml);
		$len = strlen($this->xmlRequest);
		$packed = pack("N", $len);

		// Send and get the response
		fwrite($this->id, $packed, 4);
		fwrite($this->id, $this->xmlRequest);
		$this->getResponse();

		// Set the result
		return $this->parseXML($this->xmlResponse);
	}

	public function addCustomer($customer_details){
		$customer_details['first_name'] = $this->replaceSpecial($customer_details['first_name']);
		$customer_details['last_name']  = $this->replaceSpecial($customer_details['last_name']);
		$customer_details['address1'] = $this->replaceSpecial($customer_details['address1']);
		$customer_details['city'] = $this->replaceSpecial($customer_details['city']);
		$customer_details['state'] = $this->replaceSpecial($customer_details['state']);

		$xml = '<CustomerSaveRq>
                  <Customer>    
                    <Status>Normal</Status>
                    <DefPaymentTerms>COD</DefPaymentTerms>
                    <DefShipTerms>Prepaid</DefShipTerms>
                    <Name><![CDATA['.$customer_details['first_name'] . " " . $customer_details['last_name'].']]></Name>
                    <ActiveFlag>true</ActiveFlag>
                    <JobDepth>1</JobDepth>
                    <Addresses>
                        <Address>
                            <Name>Main Office</Name>
                            <Attn>Attention</Attn>
                            <Street><![CDATA['.$customer_details['address1'].']]></Street>
                            <City><![CDATA['.$customer_details['city'].']]></City>
                            <Zip><![CDATA['.$customer_details['zip'].']]></Zip>
                            <Default>true</Default>
                            <Residential>false</Residential>
                            <Type>Main Office</Type>
                            <State>
                                <Name><![CDATA['.$customer_details['state'].']]></Name>
                                <Code><![CDATA['.$customer_details['state'].']]></Code>
                            </State>
                            <Country>
                                <Name><![CDATA['.$customer_details['country'].']]></Name>
                                <Code><![CDATA['.$customer_details['country'].']]></Code>
                            </Country>
                        </Address>
                    </Addresses>
                 </Customer>    
               </CustomerSaveRq>';

		// Create request and pack
		$this->createRequest($xml);
		$len = strlen($this->xmlRequest);
		$packed = pack("N", $len);

		// Send and get the response
		fwrite($this->id, $packed, 4);
		fwrite($this->id, $this->xmlRequest);
		$this->getResponse();

		// Set the result
		return $this->parseXML($this->xmlResponse);
	}

	public function ShipRequest($order_number){
		$ship_date = date('Y-m-d H:i:s');
		$xml = '<ShipRq>
				  <ShipDate>'.$ship_date.'</ShipDate>
				  <FulfillService>true</FulfillService>
				  <AllowEntered>true</AllowEntered> 
				  <OrderNums>
				     <OrderNum>'.$order_number.'</OrderNum>
				  </OrderNums>
				</ShipRq>';

		// Create request and pack
		$this->createRequest($xml);
		$len = strlen($this->xmlRequest);
		$packed = pack("N", $len);

		// Send and get the response
		fwrite($this->id, $packed, 4);
		fwrite($this->id, $this->xmlRequest);
		$this->getResponse();

		// Set the result
		return $this->parseXML($this->xmlResponse);
	}
}
?>