<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("config.php");
include_once("amazon/update_qty.php");
include_once("CA/update_qty.php");
include_once("ebay/eb_updateQty.php");

$products = $db->func_query("select product_id , sku , quantity from oc_product where need_sync = 1  limit 250");
if(!$products){
	print "no products";
	exit;
}

$amazoneArray = array();
$BigcommerceArray = array();
$productUpdateArray = array();

$pids = array();
foreach($products as $product){
	$SKU = $product['sku'];
	$Qty = $product['quantity'];

	$object = new stdClass();
	$object->inventory_level = $Qty;
	$BigcommerceArray[] = array('sku' => $SKU , 'object' => $object);

	$productUpdateArray[] = array('sku' => $SKU , 'qty' => $Qty);
	$amazoneArray[] = array('sku' => $SKU , 'qty' => $Qty);
	$eBayArray[] = array('sku' => $SKU , 'qty' => $Qty);

	$pids[] = $product['product_id'];
}

//update amazon qty
if(count($amazoneArray) > 0){
	for($i=0;$i<sizeof($amazoneArray);$i+=50){
		$updateArray   = array_slice($amazoneArray,$i,50);
		$amazon_response[] = updateInventory($updateArray);
	}
}

//update channel advisor
for($i=0;$i<sizeof($productUpdateArray);$i+=50){
	$updateArray   = array_slice($productUpdateArray,$i,50);
	//$ca_response[] = updateCAInventory($updateArray);
}

if($eBayArray){
	$ebay_response[] = updateEbayQty($eBayArray);
	//print_r($ebay_response);
}

if($BigcommerceArray){
	//update bigcommerce
	include_once("Bigcommerce/update_qty.php");
	$big_response = updateBigCommerceInventory($BigcommerceArray);
}

if($pids){
	$pidsStr = implode(",",$pids);
	$db->db_exec("Update oc_product SET need_sync = 0 where product_id in ($pidsStr)");
}

$data .= "CA Response- ".print_r($ca_response,true) . "\n";
$data .= "Bigcommerce Response- ".print_r($big_response,true) . "\n <br />";
$data .= "Amazon Response- ".print_r($amazon_response,true) . "\n <br />";
$data .= "eBay Response- ".print_r($ebay_response,true) . "\n <br />";

if($data){
	$message = "Hi Admin , <br />";
	$message .= "Update All Sync Report <br />";
	$message .= $data;
	$message .= "<br /><br /> Thanks, <br /> Phonepartsusa Team";

	$headers = "From:no-reply@phonepartsusa.com\r\nFromName:phonepartsusa\r\nContent-type:text/html;charset=utf-8;";
	mail("vipin.garg12@gmail.com","Update All Sync Report",$message,$headers);
	mail("saadahmed786@gmail.com","Update All Sync Report",$message,$headers);
}

error_log($data , 3 , "log/inventory.log");

echo "success";