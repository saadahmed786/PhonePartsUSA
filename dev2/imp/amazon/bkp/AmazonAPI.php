<?php
class AmazonAPI {

	public $serviceUrl = "https://mws.amazonservices.com";

	public $service;

	public $marketplaceIdArray = array ();

	public $market_place_id;

	public function __construct($market_place_id) {
		$config = array (
				'ServiceURL' => $this->serviceUrl,
				'ProxyHost' => null,
				'ProxyPort' => - 1,
				'MaxErrorRetry' => 3 
		);
		
		$this->service = new MarketplaceWebService_Client ( AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, $config, APPLICATION_NAME, APPLICATION_VERSION );
		
		$this->market_place_id = $market_place_id;
		$this->marketplaceIdArray = array (
				"Id" => array (
						$market_place_id 
				) 
		);
	}

	public function SendRequest($feed, $merchant_id, $FeedType = '_POST_INVENTORY_AVAILABILITY_DATA_') {
		$feedHandle = @fopen ( 'php://temp', 'rw+' );
		fwrite ( $feedHandle, $feed );
		rewind ( $feedHandle );
		$parameters = array (
				'Merchant' => $merchant_id,
				'MarketplaceIdList' => $this->marketplaceIdArray,
				'FeedType' => $FeedType,
				'FeedContent' => $feedHandle,
				'PurgeAndReplace' => false,
				'ContentMd5' => base64_encode ( md5 ( stream_get_contents ( $feedHandle ), true ) ) 
		);
		
		rewind ( $feedHandle );
		
		$request = new MarketplaceWebService_Model_SubmitFeedRequest ( $parameters );
		// $request->setMarketplace($this->market_place_id);
		
		try {
			$response = $this->service->submitFeed ( $request );
			
			if ($response->isSetSubmitFeedResult ()) {
				$submitFeedResult = $response->getSubmitFeedResult ();
				
				if ($submitFeedResult->isSetFeedSubmissionInfo ()) {
					$feedSubmissionInfo = $submitFeedResult->getFeedSubmissionInfo ();
					if ($feedSubmissionInfo->isSetFeedSubmissionId ()) {
						$feed_submission_id = $feedSubmissionInfo->getFeedSubmissionId ();
					}
					
					if ($feedSubmissionInfo->isSetFeedType ()) {
						$feed_type = $feedSubmissionInfo->getFeedType ();
					}
					
					if ($feedSubmissionInfo->isSetSubmittedDate ()) {
						$submitted_date = $feedSubmissionInfo->getSubmittedDate ()->format ( DATE_FORMAT );
					}
					
					if ($feedSubmissionInfo->isSetFeedProcessingStatus ()) {
						$feed_status = $feedSubmissionInfo->getFeedProcessingStatus ();
					}
					
					if ($feedSubmissionInfo->isSetStartedProcessingDate ()) {
						$start_date = $feedSubmissionInfo->getStartedProcessingDate ()->format ( DATE_FORMAT );
					}
					
					if ($feedSubmissionInfo->isSetCompletedProcessingDate ()) {
						$complete_date = $feedSubmissionInfo->getCompletedProcessingDate ()->format ( DATE_FORMAT );
					}
					
					global $db;
					if ($db) {
						$tableData = array ();
						$tableData ['feed_submission_id'] = $feed_submission_id;
						$tableData ['feed_type'] = $feed_type;
						$tableData ['request_type'] = $OperationType;
						$tableData ['submitted_date'] = $submitted_date;
						$tableData ['feed_status'] = $feed_status;
						$tableData ['start_date'] = $start_date;
						$tableData ['complete_date'] = $complete_date;
						$tableData ['feed'] = $db->func_escape_string ( $feed );
						$tableData ['dateofmodification'] = date ( 'Y-m-d H:i:s' );
						$db->func_array2insert ( "amazon_requests", $tableData );
					}
				}
			}
			
			$output = "1";
		}
		catch ( MarketplaceWebService_Exception $ex ) {
			$output = $ex->getStatusCode () . " : " . $ex->getMessage ();
			print_r ( $output );
		}
		
		return $output;
	}

	public function InventoryXml($products, $merchant_id, $amazon_order_id) {
		$xml = '<?xml version="1.0" encoding="utf-8" ?>
				<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
					<Header>
						<DocumentVersion>1.01</DocumentVersion>
						<MerchantIdentifier>' . $merchant_id . '</MerchantIdentifier>
					</Header>
					<MessageType>Inventory</MessageType>';
		
		foreach ( $products as $id => $product ) {
			$xml .= '<Message>
						<MessageID>' . ($id + 1) . '</MessageID>';
			
			if ($OperationType == 'Update') {
				$xml .= '<OperationType>' . $OperationType . '</OperationType>';
			}
			
			// <Available>'.$product['Available'].'</Available>
			$xml .= '<Inventory>
						<SKU>' . $product ['sku'] . '</SKU>
						<Quantity>' . $product ['qty'] . '</Quantity>
						<FulfillmentLatency>1</FulfillmentLatency>
					</Inventory>
				</Message>';
		}
		
		$xml .= '</AmazonEnvelope>';
		return $xml;
	}

	public function CancelOrderXml($data, $merchant_id) {
		$xml = '<?xml version="1.0" encoding="utf-8" ?>
				<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
					<Header>
						<DocumentVersion>1.01</DocumentVersion>
						<MerchantIdentifier>' . $merchant_id . '</MerchantIdentifier>
					</Header>
					<MessageType>OrderAdjustment</MessageType>';
		$xml .= '<Message>
		
						<MessageID>1</MessageID>
						<OrderAdjustment>
						<AmazonOrderID>'.$data['order_id'].'</AmazonOrderID>
						
						
						';
		foreach ( $data['items'] as $id => $product ) {
			
			// <Available>'.$product['Available'].'</Available>
			$xml .= '<AdjustedItem>
						<AmazonOrderItemCode>'.$product['amazon_item_id'].'</AmazonOrderItemCode>
						<AdjustmentReason>CustomerReturn</AdjustmentReason>
						<ItemPriceAdjustments>
<Component>
<Type>Principal</Type>
<Amount currency="USD">1.00</Amount>
</Component>
<Component>
<Type>Shipping</Type>
<Amount currency="USD">'.$data['shipping_fee'].'</Amount>
</Component>

</ItemPriceAdjustments>
					</AdjustedItem>
				';
		}
		$xml.='
		
		</OrderAdjustment>';
		$xml.='</Message>';
		$xml .= '</AmazonEnvelope>';
		return $xml;
	}

	public function PriceXml($products, $merchant_id, $OperationType = 'Update') {
		$xml = '<?xml version="1.0" encoding="utf-8" ?>
				<AmazonEnvelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="amzn-envelope.xsd">
					<Header>
						<DocumentVersion>1.01</DocumentVersion>
						<MerchantIdentifier>' . $merchant_id . '</MerchantIdentifier>
					</Header>
					<MessageType>Price</MessageType>';
		
		foreach ( $products as $id => $product ) {
			$xml .= '<Message>
						<MessageID>' . ($id + 1) . '</MessageID>';
			
			if ($OperationType == 'Update') {
				$xml .= '<OperationType>' . $OperationType . '</OperationType>';
			}
			
			$xml .= '<Price>
						<SKU>' . $product ['sku'] . '</SKU>
						<StandardPrice currency="USD">' . $product ['Price'] . '</StandardPrice>
					</Price>
				</Message>';
		}
		
		$xml .= '</AmazonEnvelope>';
		return $xml;
	}

	public function getProductPrice($merchant_id, $product_sku) {
		$serviceUrl = "https://mws.amazonservices.com/Products/2011-10-01";
		
		$config = array (
				'ServiceURL' => $serviceUrl,
				'ProxyHost' => null,
				'ProxyPort' => - 1,
				'ProxyUsername' => null,
				'ProxyPassword' => null,
				'MaxErrorRetry' => 3 
		);
		
		$service = new MarketplaceWebServiceProducts_Client ( AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, APPLICATION_NAME, APPLICATION_VERSION, $config );
		
		$request = new MarketplaceWebServiceProducts_Model_GetMyPriceForSKURequest ();
		$request->setSellerId ( $merchant_id );
		$request->setMarketplaceId ( $this->market_place_id );
		
		$skuType = new MarketplaceWebServiceProducts_Model_SellerSKUListType ();
		$skuType->setSellerSKU ( $product_sku );
		$request->setSellerSKUList ( $skuType );
		
		try {
			$response = $service->GetMyPriceForSKU ( $request );
			$xml = $response->toXML();
			
			print_r($xml);
			
		}
		catch ( MarketplaceWebServiceProducts_Exception $ex ) {
			$response = $ex->getStatusCode () . " : " . $ex->getMessage ();
		}
		
		return $response;
	}

	public function GetFeedSubmissionResult($submission_id, $merchant_id) {
		$request = new MarketplaceWebService_Model_GetFeedSubmissionResultRequest ();
		$request->setMerchant ( $merchant_id );
		$request->setFeedSubmissionId ( $submission_id );
		$request->setFeedSubmissionResult ( @fopen ( 'php://memory', 'rw+' ) );
		
		try {
			$response = $this->service->getFeedSubmissionResult ( $request );
		}
		catch ( MarketplaceWebService_Exception $ex ) {
			$response = $ex->getStatusCode () . " : " . $ex->getMessage ();
		}
		
		return $response;
	}
}