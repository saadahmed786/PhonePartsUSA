<?php
include_once("../config.php");
include_once("../inc/functions.php");

//date_default_timezone_set("America/Los_Angeles");
date_default_timezone_set("UTC");
set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("amazon_config.php");

$merchantInfo = $db->func_query("Select * from amazon_credential order by dateofmodifications DESC");
if(!@$merchantInfo){
	echo "No merchant exist";
	exit;
}

foreach($merchantInfo as $amazonAccount){
	$amazon_credential_id = $amazonAccount['id'];
	$merchant_id     = $amazonAccount['merchant_id'];
	$market_place_id = $amazonAccount['market_place_id'];
	$aws_access_key  = $amazonAccount['aws_access_key'];
	$aws_secret_key  = $amazonAccount['aws_secret_key'];
	$startDate = $amazonAccount['last_cron_date'];
	$prefix = $amazonAccount['prefix'];

	$majorLastDate = '2013-09-14 00:00:00';
	if(strtotime($startDate) < strtotime($majorLastDate)){
		$startDate = $majorLastDate;
	}

	if(!intval($startDate)){
		$startDate = date('Y-m-d H:i:s', ( time() - (24*60*60) ));
	}
	else{
		$startDate = date('Y-m-d H:i:s', ( strtotime($startDate) - (6*60*60) ));
	}

	print $market_place_id ." -- " . $merchant_id . " $startDate <br />";

	$orderDate = date('Y-m-d',strtotime($startDate));
	$serviceUrl = "https://mws.amazonservices.com/Orders/".$orderDate;

	$config = array (
	   'ServiceURL' => $serviceUrl,
	   'ProxyHost' => null,
	   'ProxyPort' => -1,
	   'MaxErrorRetry' => 3,
	);

	$service = new MarketplaceWebServiceOrders_Client(
	$aws_access_key,
	$aws_secret_key,
	APPLICATION_NAME,
	APPLICATION_VERSION,
	$config);

	$request = new MarketplaceWebServiceOrders_Model_ListOrdersRequest();
	$request->setSellerId($merchant_id);
	// List all orders udpated after a certain date
	$request->setLastUpdatedAfter(new DateTime($startDate, new DateTimeZone('UTC')));

	// Set the marketplaces queried in this ListOrdersRequest
	$marketplaceIdList = new MarketplaceWebServiceOrders_Model_MarketplaceIdList();
	$marketplaceIdList->setId(array($market_place_id));
	$request->setMarketplaceId($marketplaceIdList);

	// Set the order statuses for this ListOrdersRequest (optional)
	$orderStatuses = new MarketplaceWebServiceOrders_Model_OrderStatusList();
	$orderStatuses->setStatus(array('Unshipped','PartiallyShipped','Shipped','InvoiceUnconfirmed'));
	$request->setOrderStatus($orderStatuses);

	// Set the Fulfillment Channel for this ListOrdersRequest (optional)
	//$fulfillmentChannels = new MarketplaceWebServiceOrders_Model_FulfillmentChannelList();
	//$fulfillmentChannels->setChannel(array('MFN'));
	//$request->setFulfillmentChannel($fulfillmentChannels);

	try {
		$response = $service->listOrders($request);
		if($response->isSetListOrdersResult()){
			$listOrdersResult = $response->getListOrdersResult();

			if ($listOrdersResult->isSetNextToken()){
				$listToken = $listOrdersResult->getNextToken();
			}

			if($listOrdersResult->isSetOrders()){
				$orders = $listOrdersResult->getOrders();
				$orderList = $orders->getOrder();
				insertOrders($orderList , $amazon_credential_id);
			}

			//get more order if nexttoken set
			if(isset($listToken)){
				$request = new MarketplaceWebServiceOrders_Model_ListOrdersByNextTokenRequest();
				$request->setSellerId($merchant_id);
				$request->setNextToken($listToken);

				$response = $service->listOrdersByNextToken($request);
				if($response->isSetListOrdersByNextTokenResult()){
					$listOrdersByNextTokenResult = $response->getListOrdersByNextTokenResult();

					if ($listOrdersByNextTokenResult->isSetOrders()) {
						$orders = $listOrdersByNextTokenResult->getOrders();
						$orderList = $orders->getOrder();
						insertOrders($orderList , $amazon_credential_id);
					}

					while($listOrdersByNextTokenResult->isSetNextToken()){
						$request->setNextToken($listOrdersByNextTokenResult->getNextToken());
						$response = $service->listOrdersByNextToken($request);

						$listOrdersByNextTokenResult = $response->getListOrdersByNextTokenResult();
						if ($listOrdersByNextTokenResult->isSetOrders()) {
							$orders = $listOrdersByNextTokenResult->getOrders();
							$orderList = $orders->getOrder();
							insertOrders($orderList , $amazon_credential_id);
						}
						else{
							break;
						}
					}
				}
			}

			$end_time = date('Y-m-d H:i:s');
			$db->db_exec("update amazon_credential set last_cron_date = '$end_time' where id = '$amazon_credential_id'");
		}
	}
	catch (MarketplaceWebServiceOrders_Exception $ex) {
		echo $ex->getTraceAsString();
		echo("Caught Exception: " . $ex->getMessage() . "<br />");
		echo("Response Status Code: " . $ex->getStatusCode() . "<br />");
		echo("Error Code: " . $ex->getErrorCode() . "<br />");
		echo("Error Type: " . $ex->getErrorType() . "<br />");
		echo("Request ID: " . $ex->getRequestId() . "<br />");
		echo("XML: " . $ex->getXML() . "<br />");
		echo("ResponseHeaderMetadata: " . $ex->getResponseHeaderMetadata() . "<br />");
	}
}

function insertOrders($orderList , $amazon_credential_id){
	global $db;

	foreach ($orderList as $order){
		$order_id = $prefix . $order->getAmazonOrderId();
		//print $order_id . "<br />";
		$order_date = $order->getPurchaseDate();
		$order_date = trim(str_replace(array("Z","T"), " ", $order_date));

		//PST time
		$order_date = date('Y-m-d H:i:s', (strtotime($order_date) - (7*60*60)));

		$last_date = $order->getLastUpdateDate();
		$last_date = trim(str_replace(array("Z","T"), " ", $last_date));

		$order_status = $order->getOrderStatus();
		$PaymentMethod = $order->getPaymentMethod();

		$report_type = $order->getFulfillmentChannel();
		if($report_type=="MFN"){
			$fullfill_type="ByMerchant";
		}
		else{
			$fullfill_type="ByAmazon";
		}

		if ($order->isSetOrderTotal()){
			$orderTotal = $order->getOrderTotal();
			if ($orderTotal->isSetAmount()){
				$amount = $orderTotal->getAmount();
			}
		}

		$shipping_address = $order->getShippingAddress();
		$CustName = $db->func_escape_string($order->getBuyerName());
		$Phone   = $shipping_address->getPhone();
		$Address = $db->func_escape_string($shipping_address->getAddressLine1());
		$City    = $db->func_escape_string($shipping_address->getCity());
		$State   = $db->func_escape_string($shipping_address->getStateOrRegion());
		$Country = $shipping_address->getCountryCode();
		$Zip     = $shipping_address->getPostalCode();
		$Email   = $order->getBuyerEmail();

		$store_type = 'amazon';
		if($fullfill_type == 'ByAmazon'){
			$store_type = 'amazon_fba';
		}

		$orderExist = $db->func_query_first_cell("select id from inv_orders where order_id = '".$db->func_escape_string($order_id)."'");
		if(!$orderExist){
			$db->db_exec("insert into inv_orders(order_id,order_date,order_price,paid_price,order_status,email,store_type,fullfill_type,customer_name,dateofmodification)
                         values ('$order_id','".$order_date."','$amount','$amount','$order_status','$Email','$store_type','$fullfill_type','".$CustName."','".date('Y-m-d H:i:s')."')");

			$db->db_exec("insert into inv_orders_details(order_id,first_name,phone_number,address1,city,state,country,zip,payment_method,dateofmodification)
                         values ('$order_id','".$CustName."','$Phone','$Address','$City','$State','$Country','$Zip','$PaymentMethod','".date('Y-m-d H:i:s')."')");

			$shipping_amount = 0;
			$orderItems = getOrderLineItems($order_id);
			foreach($orderItems as $orderItem){
				$order_item_id = $orderItem->getOrderItemId();
				$itemname = addslashes($orderItem->getTitle());
				$sku  = $orderItem->getSellerSKU();
				$asin = $orderItem->getASIN();
				$qty  = $orderItem->getQuantityOrdered();
				$order_price = $orderItem->getItemPrice()->getAmount();
				$true_cost = getTrueCost($sku);
				$product_unit = 0.00;
				if($qty>0)
				{
				$product_unit = (float)($order_price/$qty);
				}

				if($orderItem->getShippingPrice()){
					$shipping_amount += $orderItem->getShippingPrice()->getAmount();
				}

				//check if SKU is KIT SKU
				$item_sku = $db->func_escape_string($sku);
				$kit_skus = $db->func_query_first("select * from inv_kit_skus where kit_sku = '$item_sku'");
				if($kit_skus){
					$kit_skus_array = explode(",",$kit_skus['linked_sku']);
					for($_i=1;$_i<=$qty;$_i++)
                        {
						$zz = 0;
					
						foreach($kit_skus_array as $kit_skus_row){
						$kit_skus_row = $kit_skus_row;
                      	$true_cost = getTrueCost($kit_skus_row);
                        $_qty =  1;
                        $product_unit = $product_unit;
                        $order_price = $product_unit;
						if($zz > 0){
							$true_cost = 0.00;
                        $_qty =  1;
                        $product_unit = 0.00;
                        $order_price = 0.00;
						}
						
						$db->db_exec("insert into inv_orders_items(order_id,order_item_id,product_sku,product_name,product_price,product_qty,product_true_cost,dateofmodification,product_unit)
                              values ('$order_id','$order_item_id','$kit_skus_row','$itemname','$order_price','$_qty','$true_cost','".date('Y-m-d H:i:s')."','".$product_unit."')");
					
						$zz++;
					}
				}

					//mark kit sku need_sync on all marketplaces
					$db->db_exec("update inv_kit_skus SET need_sync = 1 where kit_sku = '$item_sku'");
				}
				else{
					$db->db_exec("insert into inv_orders_items(order_id,order_item_id,product_sku,product_name,product_price,product_qty,product_true_cost,dateofmodification)
                              values ('$order_id','$order_item_id','$sku','$itemname','$order_price','$qty','".getTrueCost($sku)."','".date('Y-m-d H:i:s')."')");
				}
			}

			$db->db_exec("update inv_orders_details SET shipping_cost = '$shipping_amount' Where order_id = '$order_id'");
		}
		elseif($orderExist){
			$shipping_amount = 0;
				
			$db->db_exec("Update inv_orders SET customer_name = '".$CustName."' , order_status = '$order_status' Where id = '$orderExist'");

			$orderItems = $db->func_query_first_cell("select id from inv_orders_items where order_id = '$order_id'");
			if(!$orderItems){
				$orderItems = getOrderLineItems($order_id);
				foreach($orderItems as $orderItem){
					$order_item_id = $orderItem->getOrderItemId();
					$itemname = addslashes($orderItem->getTitle());
					$sku  = $orderItem->getSellerSKU();
					$asin = $orderItem->getASIN();
					$qty  = $orderItem->getQuantityOrdered();
					$order_price = $orderItem->getItemPrice()->getAmount();
					$true_cost = getTrueCost($sku);
						
					if($orderItem->getShippingPrice()){
						$shipping_amount += $orderItem->getShippingPrice()->getAmount();
					}

					//check if SKU is KIT SKU
					$item_sku = $db->func_escape_string($sku);
					$kit_skus = $db->func_query_first("select * from inv_kit_skus where kit_sku = '$item_sku'");
					if($kit_skus){
						$kit_skus_array = explode(",",$kit_skus['linked_sku']);
						$zz = 0;
						
						foreach($kit_skus_array as $kit_skus_row){
							if($zz > 0){
								$order_price = 0.00;
								$true_cost = 0.00;
							}
						
							$db->db_exec("insert into inv_orders_items(order_id,order_item_id,product_sku,product_name,product_price,product_qty,product_true_cost,dateofmodification)
                              values ('$order_id','$order_item_id','$kit_skus_row','$itemname','$order_price','$qty','$true_cost','".date('Y-m-d H:i:s')."')");
						
							$zz++;
						}

						//mark kit sku need_sync on all marketplaces
						$db->db_exec("update inv_kit_skus SET need_sync = 1 where kit_sku = '$item_sku'");
					}
					else{
						$db->db_exec("insert into inv_orders_items(order_id,order_item_id,product_sku,product_name,product_price,product_qty,product_true_cost,dateofmodification)
                              values ('$order_id','$order_item_id','$sku','$itemname','$order_price','$qty','$true_cost','".date('Y-m-d H:i:s')."')");
					}
				}

				$db->db_exec("update inv_orders_details SET shipping_cost = '$shipping_amount' Where order_id = '$order_id'");
				$db->db_exec("update inv_orders set fullfill_type = '$fullfill_type' where order_id = '$order_id'");
			}
		}
	}

	if(@$last_date){
		$db->db_exec("update amazon_credential set last_cron_date = '$last_date' where id = '$amazon_credential_id'");
	}

	return true;
}


function getOrderLineItems($amazonOrderId){
	global $merchant_id , $service;

	$request = new MarketplaceWebServiceOrders_Model_ListOrderItemsRequest();
	$request->setSellerId($merchant_id);
	$request->setAmazonOrderId($amazonOrderId);

	try {
		$response = $service->listOrderItems($request);
		if($response->isSetListOrderItemsResult()){
			$listOrderItemsResult = $response->getListOrderItemsResult();
			if ($listOrderItemsResult->isSetOrderItems()) {
				$orderItems = $listOrderItemsResult->getOrderItems();
				$orderItemList = $orderItems->getOrderItem();

				return $orderItemList;
			}
		}
	}
	catch (MarketplaceWebServiceOrders_Exception $ex) {
		return false;
	}
}

if($_REQUEST['m'] == 1){
	$_SESSION['message'] = "Order imported successfully";
	header("Location:$host_path/order.php");
	exit;
}

echo "success";
?>