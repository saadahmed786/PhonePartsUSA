<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("config.php");
include_once("CA/update_qty.php");
include_once("amazon/update_qty.php");
include_once("newegg/update_qty.php");

$products = $db->func_query("select * from inv_kit_skus where need_sync = 1 limit 250");
if(!$products){
	print "no products";
	exit;
}

$BigcommerceArray = array();
$productUpdateArray = array();
$amazoneArray = array();
$neweggArray = array();

$pids = array();
foreach($products as $product){
	$SKU = $product['kit_sku'];

	$minQty = array();
	$linked_skus = $product['linked_sku'];
	$linked_skus = "'".str_ireplace(",","','",$linked_skus)."'";

	//check order status is open or not
	$_query  = "select  o.order_id , o.order_status from inv_orders o inner join inv_orders_items ot on (o.order_id = ot.order_id)
				where ot.product_sku in ($linked_skus) and o.order_date > '2013-09-14 00:00:00' and o.status = 'open' and ignored = 0 AND ( fullfill_type != 'Amazon FBA US' AND fullfill_type is null) AND ( o.order_status != 'On Hold' AND o.order_status != 'Estimate')";
	$isExist = $db->func_query_first($_query);
	if($isExist){
		echo "skipping - $SKU - ".$isExist['order_id']. " ". $isExist['order_status'] . "<br />";
		continue;
		//skip until all child sku processed
	}

	$linked_skus_array = explode(",",$product['linked_sku']);
	foreach($linked_skus_array as $linked_sku){
		$existSku = $db->func_query_first("select product_id , quantity from oc_product where sku = '$linked_sku' OR model = '$linked_sku'");
		if($existSku['product_id']){
			$minQty[] = $existSku['quantity'];
		}
	}

	//print $_query; print_r($minQty); exit;
	if($minQty){
		$minQty = min($minQty);

		$object = new stdClass();
		$object->inventory_level = $minQty;
		$BigcommerceArray[] = array('sku' => $SKU , 'object' => $object);

		$productUpdateArray[] = array('sku' => $SKU , 'qty' => $minQty);
		$pids[] = "'".$SKU."'";

		$amazoneArray[] = array('sku' => $SKU , 'qty' => $minQty);
		
		$neweggArray[] = array('sku' => $SKU , 'qty' => $minQty);

		$db->db_exec("Update oc_product SET quantity = '$minQty' , date_modified = '".date('Y-m-d H:i:s')."' where model = '$SKU' OR sku = '$SKU'");
		
		$db->db_exec("Update inv_kit_skus SET qty = '$minQty' where kit_sku = '$SKU'");
	}
}

//update amazon qty
if(count($amazoneArray) > 0){
	$amazon_response = updateInventory($amazoneArray);
}

//update channel advisor
for($i=0;$i<sizeof($productUpdateArray);$i+=50){
	$updateArray   = array_slice($productUpdateArray,$i,50);
	//$ca_response[] = updateCAInventory($updateArray);
}

if($neweggArray){
	$newegg_response[] = updateNeweggInventory($neweggArray);
}

if($BigcommerceArray){
	//update bigcommerce
	include_once("Bigcommerce/update_qty.php");
	$big_response = updateBigCommerceInventory($BigcommerceArray);
}

if($pids){
	$pidsStr = implode(",",$pids);
	$db->db_exec("Update inv_kit_skus SET need_sync = 0 , dateofmodifcation = '".date('Y-m-d H:i:s')."' where kit_sku in ($pidsStr)");
}

$data .= "CA Response- ".print_r($ca_response,true) . "\n";
$data .= "Bigcommerce Response- ".print_r($big_response,true) . "\n <br />";
$data .= "Amazon Response- ".print_r($amazon_response,true) . "\n <br />";
$data .= "Newegg Response- ".print_r($newegg_response,true) . "\n <br />";

if($data){
	$message = "Hi Admin , <br />";
	$message .= "Update All KIT Sync Report <br />";
	$message .= $data;
	$message .= "<br /><br /> Thanks, <br /> Phonepartsusa Team";

	$headers = "From:no-reply@phonepartsusa.com\r\nFromName:phonepartsusa\r\nContent-type:text/html;charset=utf-8;";
	// mail("vipin.garg12@gmail.com","Update KIT All Sync Report",$message,$headers);
	// mail("saadahmed786@gmail.com","Update KIT All Sync Report",$message,$headers);
}

error_log($data , 3 , "log/inventory.log");

echo "success";