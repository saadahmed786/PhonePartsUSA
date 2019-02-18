<?php

set_time_limit ( 0 );
ini_set ( "memory_limit", "2048M" );

include '../config.php';
$productsCount = $db->func_query_first_cell ( "select count(product_sku) from inv_product_prices" );

$limit = 100;
include_once ("../CA/ca_keys.php");
include_once '../CA/ChannelAdvisor.php';

// fetch all channel advisor prices
for($i = 0; $i < $productsCount; $i += $limit) {
	$products = $db->func_query ( "select product_sku from inv_product_prices where channel_advisor_fetchdate not like '%".date('Y-m-d')."%' limit $i , $limit", "product_sku" );

	foreach ( $products as $product_sku => $product ) {
		foreach ( $accounts as $account ) {
			$ChannelAdvisor = new ChannelAdvisor ( $DEV_KEY, $Password, $account ['AccountID'], $account ['Prefix'] );
			$response = $ChannelAdvisor->GetInventoryItemList ( $product_sku );
				
			if ($response ['GetInventoryItemListResult'] ['Status'] == 'Success') {
				if($account ['Prefix'] == 'MM'){
					$price = $response ['GetInventoryItemListResult'] ['ResultData'] ['InventoryItemResponse'] ['PriceInfo'] ['RetailPrice'];
				}
				else{
					$price = $response ['GetInventoryItemListResult'] ['ResultData'] ['InventoryItemResponse'] ['PriceInfo'] ['TakeItPrice'];
				}

				$db->db_exec ( "update inv_product_prices SET channel_advisor = '$price' , channel_advisor_fetchdate = '" . date ( 'Y-m-d H:i:s' ) . "' where product_sku = '$product_sku'" );
			}
		}
	}
}

echo "success";