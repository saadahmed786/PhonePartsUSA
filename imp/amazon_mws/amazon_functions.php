<?php
include_once("../../config.php");
include_once("../../inc/functions.php");
include_once("../../inventory/class.php");
require('../includes/classes.php'); //autoload classes, not needed if composer is being used

function ProcessAmazonOrder($db_id,$include='amazon-config.php',$config_store='myStore',$store_type='amazon')
{
	global $db;
	switch($store_type)
	{
		case 'amazon_ca':
		$prefix = 'CA';
		break;
		case 'amazon_mx':
		$prefix = 'MX';
		break;
		case 'amazon_pg':
		$prefix = 'PG';
		break;
		case 'amazon_pgca':
		$prefix = 'PGCA';
		break;
		case 'amazon_pgmx':
		$prefix = 'PGMX';
		break;
		default:
		$prefix = '';
		break;
	}
	$_store_type = $store_type;
	$merchantInfo = $db->func_query_first("Select * from amazon_credential where id='".(int)$db_id."'");
	if(!@$merchantInfo){
		echo "No merchant exist";
		exit;
	}
	$startDate = $merchantInfo['last_cron_date'];
	if($merchantInfo['prefix'])
	{
		$prefix = $merchantInfo['prefix'];
	}
	$amazon_credential_id = $merchantInfo['id'];
	$majorLastDate = '2016-01-01 20:32:00';
	if(strtotime($startDate) < strtotime($majorLastDate)){
		$startDate = $majorLastDate;
	}
	//$startDate = $majorLastDate;
	if(!intval($startDate))
	{
		$startDate = date('Y-m-d H:i:s', ( time() - (24*60*60) ));
	}
	
	else{
		$startDate = date('Y-m-d H:i:s', ( strtotime($startDate) - (6*60*60) ));
	}
  //  echo $startDate;exit;
	require_once $include;

/*
 * This script retrieves a list of orders from the store "myStore" and displays various bits of their info.
 */

$list=getAmazonOrders($config_store,$include,$startDate);

if ($list) {

	echo 'My Store Orders<hr>';
	// foreach ($list as $order) {
	//     //these are AmazonOrder objects
	//     echo '<b>Order Number:</b> '.$order->getAmazonOrderId();
	//     echo '<br><b>Purchase Date:</b> '.$order->getPurchaseDate();
	//     echo '<br><b>Status:</b> '.$order->getOrderStatus();
	//      echo '<br><b>FulFillment:</b> '.$order->getFulfillmentChannel();
	//     echo '<br><b>Customer:</b> '.$order->getBuyerName();
	//     $address=$order->getShippingAddress(); //address is an array
	//     echo '<br><b>City:</b> '.$address['City'];
	//     echo '<br><br>';
	// }
	foreach ($list as $order){
	   // $order_id = $prefix . $order->getAmazonOrderId();
		$order_id = $order->getAmazonOrderId();
		print $order_id . "<br />";
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
		//print_r( $order->getShippingAddress());exit;
		//if ($order->isSetOrderTotal()){
		$orderTotal = $order->getOrderTotal();

		   // if ($orderTotal->isSetAmount()){
		if($orderTotal)
		{
			$amount = $orderTotal['Amount'];
		}
	   // }

		$shipping_address = $order->getShippingAddress();
		$CustName = $db->func_escape_string($order->getBuyerName());
		$Phone   = $shipping_address['Phone'];
		$Address = $db->func_escape_string($shipping_address['AddressLine1']);
		$City    = $db->func_escape_string($shipping_address['City']);
		$State   = $db->func_escape_string($shipping_address['StateOrRegion']);
		$Country = $shipping_address['CountryCode'];
		$Zip     = $shipping_address['PostalCode'];
		$Email   = $order->getBuyerEmail();


		$_country = $db->func_query_first("SELECT country_id,name FROM oc_country WHERE LOWER(iso_code_2)='".strtolower($Country)."'");
		$_zone = $db->func_query_first("SELECT zone_id FROM oc_zone WHERE LOWER(name)='".strtolower($State)."' and country_id='".(int)$country_id."'");
		$country_id = $_country['country_id'];
		$zone_id = $_zone['zone_id'];

		$store_type = $_store_type;
		if($fullfill_type == 'ByAmazon'){
			$store_type = 'amazon_fba';
		}

		$orderExist = $db->func_query_first_cell("select id from inv_orders where order_id = '".$db->func_escape_string($order_id)."'");
		if(!$orderExist){
			echo  "insert into inv_orders(prefix,order_id,order_date,order_price,paid_price,order_status,email,store_type,fullfill_type,customer_name,dateofmodification)
			values ('$prefix','$order_id','".$order_date."','$amount','$amount','$order_status','$Email','$store_type','$fullfill_type','".$CustName."','".date('Y-m-d H:i:s')."')<br>";
			$db->db_exec("insert into inv_orders(prefix,order_id,order_date,order_price,paid_price,order_status,email,store_type,fullfill_type,customer_name,dateofmodification)
				values ('$prefix','$order_id','".$order_date."','$amount','$amount','$order_status','$Email','$store_type','$fullfill_type','".$CustName."','".date('Y-m-d H:i:s')."')");

			$db->db_exec("insert into inv_orders_details(order_id,first_name,phone_number,address1,city,state,country,zip,payment_method,dateofmodification,country_id,zone_id)
				values ('$order_id','".$CustName."','$Phone','$Address','$City','$State','$Country','$Zip','$PaymentMethod','".date('Y-m-d H:i:s')."','".(int)$country_id."','".(int)$zone_id."')");

			$shipping_amount = 0;
			$orderItems = getOrderLineItems($order_id,$config_store,$include);
			$total_promotion_price = 0.00;
			$sub_total = 0.00;
			$items_true_cost = 0.00;
			foreach($orderItems as $orderItem){

				$order_item_id = $orderItem['OrderItemId'];
				$itemname = addslashes($orderItem['Title']);
				$sku  = $orderItem['SellerSKU'];
				$asin = $orderItem['ASIN'];
				$qty  = $orderItem['QuantityOrdered'];
				$order_price = $orderItem['ItemPrice']['Amount'];
				$true_cost = getTrueCost($sku);
				$product_unit = 0.00;
				$kit_product_unit = 0.00;
				$promotion_discount = 0.00;



				if($qty>0)
				{
					$product_unit = (float)($order_price/$qty);
					$kit_product_unit = (float)($order_price/$qty);
				}

				$sub_total = (float)$sub_total + (float)$order_price;

				if($orderItem['ShippingPrice']){
					$shipping_amount += $orderItem['ShippingPrice']['Amount'];
				}
				if($orderItem['PromotionDiscount']){
					$promotion_discount = $orderItem['PromotionDiscount']['Amount'];
				}
				$temp_promotion_discount = (float)($promotion_discount/$qty);
				$temp_order_price = $order_price;
				//$order_price = $order_price - $promotion_discount;

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
							$promotion_discount = $temp_promotion_discount;
							$product_unit = $kit_product_unit;
							$order_price = $kit_product_unit;

							


							if($zz > 0){
								$true_cost = getTrueCost($kit_skus_row);
								$_qty =  1;
								$product_unit = 0.00;
								$order_price = 0.00;
								$promotion_discount = 0.00;

								
							}
							$items_true_cost = (float)$items_true_cost + ((float)$true_cost * (int)$_qty);
							saveInventory($kit_skus_row, $_qty);
							$db->db_exec("insert into inv_orders_items(order_id,order_item_id,product_sku,product_name,product_price,product_qty,product_true_cost,dateofmodification,product_unit,promotion_discount,ostatus)
								values ('$order_id','$order_item_id','$kit_skus_row','$itemname','$order_price','$_qty','$true_cost','".date('Y-m-d H:i:s')."','".$product_unit."','".(float)$promotion_discount."','".strtolower($order_status)."')");

							$zz++;
						}
					}

					//mark kit sku need_sync on all marketplaces
					$db->db_exec("update inv_kit_skus SET need_sync = 1 where kit_sku = '$item_sku'");
				}
				else{
					saveInventory($sku, $qty);
					$db->db_exec("insert into inv_orders_items(order_id,order_item_id,product_sku,product_name,product_price,product_qty,product_true_cost,dateofmodification,product_unit,promotion_discount,ostatus)
						values ('$order_id','$order_item_id','$sku','$itemname','$order_price','$qty','".getTrueCost($sku)."','".date('Y-m-d H:i:s')."','".$product_unit."','".(float)$promotion_discount."','".strtolower($order_status)."')");


					$items_true_cost = (float)$items_true_cost + ((float)getTrueCost($sku) * (int)$qty);

				}
				$total_promotion_price = $total_promotion_price + $promotion_discount;
			}
			$total_promotion_price = $amount - $total_promotion_price;
			$db->db_exec("update inv_orders_details SET shipping_cost = '$shipping_amount' Where order_id = '$order_id'");
			$db->db_exec("update inv_orders SET sub_total='".(float)$sub_total."',shipping_amount = '$shipping_amount',items_true_cost='".(float)$items_true_cost."' Where order_id = '$order_id'");
			$db->db_exec("UPDATE inv_orders SET paid_price='".(float)$total_promotion_price."' WHERE order_id='".$order_id."'");
		}
		elseif($orderExist){
			$shipping_amount = 0;

			$db->db_exec("Update inv_orders SET customer_name = '".$CustName."' , order_status = '$order_status' Where id = '$orderExist'");
			$db->db_exec("Update inv_orders_items SET ostatus = '".strtolower($order_status)."' Where order_id = '".$order_id."'");

			if(strtolower($order_status)=='shipped')
			{
				$inventory->updateInventoryShipped($order_id,'shipped');
			}

			$orderItems = $db->func_query_first_cell("select id from inv_orders_items where order_id = '$order_id'");
			if(!$orderItems){
				$orderItems = getOrderLineItems($order_id,$config_store,$include);
				$total_promotion_price = 0.00;
				foreach($orderItems as $orderItem){

					$order_item_id = $orderItem['OrderItemId'];
					$itemname = addslashes($orderItem['Title']);
					$sku  = $orderItem['SellerSKU'];
					$asin = $orderItem['ASIN'];
					$qty  = $orderItem['QuantityOrdered'];
					$order_price = $orderItem['ItemPrice']['Amount'];
					$true_cost = getTrueCost($sku);
					$product_unit = 0.00;
					$kit_product_unit = 0.00;
					$promotion_discount = 0.00;

					if($qty>0)
					{
						$product_unit = (float)($order_price/$qty);
						$kit_product_unit = (float)($order_price/$qty);
					}


					if($orderItem['ShippingPrice']){
						$shipping_amount += $orderItem['ShippingPrice']['Amount'];
					}
					if($orderItem['PromotionDiscount']){
						$promotion_discount = $orderItem['PromotionDiscount']['Amount'];
					}
					$temp_promotion_discount = (float)($promotion_discount/$qty);
					$temp_order_price = $order_price;
				//$order_price = $order_price - $promotion_discount;
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
								$promotion_discount = $temp_promotion_discount;
								$product_unit = $kit_product_unit;
								$order_price = $kit_product_unit;
								if($zz > 0){
									$true_cost = 0.00;
									$_qty =  1;
									$product_unit = 0.00;
									$order_price = 0.00;
									$promotion_discount = 0.00;
								}
								saveInventory($kit_skus_row, $_qty);
								$db->db_exec("insert into inv_orders_items(order_id,order_item_id,product_sku,product_name,product_price,product_qty,product_true_cost,dateofmodification,product_unit,promotion_discount,ostatus)
									values ('$order_id','$order_item_id','$kit_skus_row','$itemname','$order_price','$_qty','$true_cost','".date('Y-m-d H:i:s')."','".$product_unit."','".(float)$promotion_discount."','".strtolower($order_status)."')");

								$zz++;
							}
						}

					//mark kit sku need_sync on all marketplaces
						$db->db_exec("update inv_kit_skus SET need_sync = 1 where kit_sku = '$item_sku'");
					}
					else{
						saveInventory($sku, $qty);
						$db->db_exec("insert into inv_orders_items(order_id,order_item_id,product_sku,product_name,product_price,product_qty,product_true_cost,dateofmodification,product_unit,promotion_discount,ostatus)
							values ('$order_id','$order_item_id','$sku','$itemname','$order_price','$qty','".getTrueCost($sku)."','".date('Y-m-d H:i:s')."','".$product_unit."','".(float)$promotion_discount."','".strtolower($order_status)."')");
					}
					$total_promotion_price = $total_promotion_price + $promotion_discount;
				}

				$total_promotion_price = $amount - $total_promotion_price;
				$db->db_exec("update inv_orders_details SET shipping_cost = '$shipping_amount' Where order_id = '$order_id'");
				$db->db_exec("update inv_orders SET shipping_amount = '$shipping_amount' Where order_id = '$order_id'");
				//$db->db_exec("UPDATE inv_orders SET paid_price='".(float)$total_promotion_price."' WHERE order_id='".$order_id."'");
				$db->db_exec("update inv_orders set fullfill_type = '$fullfill_type' where order_id = '$order_id'");
			}
		}

		$iorderID = $order_id;
		$query = "select o.* , od.* from inv_orders o inner join inv_orders_details od on o.order_id = od.order_id where order_status NOT IN ('Estimate','On Hold') and ss_ignore=0 and o.order_id = '$iorderID' group by o.order_id";
		$iorder = $db->func_query_first($query);

		if ($iorder) {
			whiteList($iorder, 1);
		}

	}
	if(@$last_date){
		//PST time
		$last_date = date('Y-m-d H:i:s', (strtotime($last_date) - (7*60*60)));
		$db->db_exec("update amazon_credential set last_cron_date = '$last_date' where id = '$amazon_credential_id'");
	}
}


}

/**
 * This function will retrieve a list of all unshipped MFN orders made within the past 24 hours.
 * The entire list of orders is returned, with each order contained in an AmazonOrder object.
 * Note that the items in the order are not included in the data.
 * To get the order's items, the "fetchItems" method must be used by the specific order object.
 */
function getAmazonOrders($config_store,$include,$startDate) {

	global $db;
   // global $startDate;

	try {
		$amz = new AmazonOrderList($config_store,null,null,$include); //store name matches the array key in the config file
		$amz->setLimits('Modified', $startDate); //accepts either specific timestamps or relative times 
	   // $amz->setFulfillmentChannelFilter("MFN"); //no Amazon-fulfilled orders
		$amz->setOrderStatusFilter(
			array('Unshipped','PartiallyShipped','Shipped','InvoiceUnconfirmed')
			); //no shipped or pending orders
		$amz->setUseToken(); //tells the object to automatically use tokens right away
		$amz->fetchOrders(); //this is what actually sends the request
		return $amz->getList();
	} catch (Exception $ex) {
		echo 'There was a problem with the Amazon library. Error: '.$ex->getMessage();
	}
}
function getOrderLineItems($order_id,$config_store,$include)
{
   // require('../includes/classes.php');
	global $db;
	try {
		$amz = new AmazonOrderItemList($config_store,$order_id,null,null,$include); //store name matches the array key in the config file
	   // $amz->setLimits('Modified', $startDate); //accepts either specific timestamps or relative times 
	   // $amz->setFulfillmentChannelFilter("MFN"); //no Amazon-fulfilled orders
	//  $amz->setOrderId($order_id);
		$amz->setUseToken(); //tells the object to automatically use tokens right away
		$amz->fetchItems(); //this is what actually sends the request
		return $amz->getItems();
	} catch (Exception $ex) {
		echo 'There was a problem with the Amazon library. Error: '.$ex->getMessage();
	}
}

?>