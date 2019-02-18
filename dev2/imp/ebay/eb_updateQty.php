<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("session.php");
include_once("eb_config.php");
include_once("ebAPI.php");

function updateEbayQty($eBayArray = null){
	global $db;

	$token = $db->func_query_first_cell("Select config_value from configuration where config_key = 'USER_TOKEN' ");
	if(!$eBayArray){
		return false;
	}
	
	$max_qty = $db->func_query_first_cell("Select config_value from configuration where config_key = 'EBAY_MAX_QTY' ");

	$updateQty = new ebAPI();
	$response  = array();

	foreach($eBayArray as $ebay){
		$sku = $ebay['sku'];
		$qty = $ebay['qty'];

		// we dont need to show more than 10 quantity on ebay
		// if($qty > $max_qty){
		// 	$qty = $max_qty;
		// }

		$productDetail = $db->func_query_first("Select quantity from oc_product where model = '".$sku."' and is_ebay=1");
		$mappingDetail = $db->func_query_first("Select * from ebay_mapping where product_sku = '".$sku."'");

		if($mappingDetail and $productDetail){
			$itemID = $mappingDetail['ebay_item_id'];
			
			if($qty <= 0){
				$qty = 0;
			}
			
			$response[] = $updateQty->reviseInventoryStatus($token , $itemID , $qty);
		}
		else{
			$response[$sku] = "No ebay mapping found for model $sku";
		}
	}

	return $response;
}
?>