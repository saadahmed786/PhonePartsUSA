<?php
set_time_limit ( 0 );
ini_set ( "memory_limit", "2048M" );

use Bigcommerce\Api\Client as Bigcommerce;
use Wish\WishClient;

include '../config.php';
$productsCount = $db->func_query_first_cell ( "select count(product_id) from oc_product where status = 1" );

$limit = 100;
include_once ("../CA/ca_keys.php");
include_once '../CA/ChannelAdvisor.php';

// fetch all channel advisor prices
for($i = 0; $i < $productsCount; $i += $limit) {
	$products = $db->func_query ( "select sku from oc_product where status = 1 limit $i , $limit", "sku" );
	
	foreach ( $products as $product_sku => $product ) {
		$isExist = $db->func_query_first_cell ( "select id from inv_product_prices where product_sku = '$product_sku'" );
		if (! $isExist) {
			$db->db_exec ( "insert into inv_product_prices set product_sku = '$product_sku'" );
		}
		
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

include '../Bigcommerce/big_keys.php';
include '../Bigcommerce/bigcommerce-api-php-master/vendor/autoload.php';

Bigcommerce::configure ( array (
		'store_url' => 'https://www.replacementlcds.com',
		'username' => $username,
		'api_key' => $api_token 
) );

Bigcommerce::setCipher ( 'RC4-SHA' );
Bigcommerce::verifyPeer ( false );
Bigcommerce::failOnError ();

// fetch bigcommerce
for($i = 0; $i < $productsCount; $i += $limit) {
	$products = $db->func_query ( "select sku from oc_product where status = 1 limit $i , $limit", "sku" );
	
	foreach ( $products as $product_sku => $product ) {
		$product_id = $db->func_query_first_cell ( "select product_id from bigcommerce_mappings where product_sku = '$product_sku'" );
		try {
			$response = Bigcommerce::getProduct ( $product_id );
			$retail_price = $response->retail_price;
			$sale_price = $response->sale_price;
			
			$db->db_exec ( "update inv_product_prices SET bigcommerce_retail = '$price' , bigcommerce_retail_fetchdate = '" . date ( 'Y-m-d H:i:s' ) . "' where product_sku = '$product_sku'" );
			$db->db_exec ( "update inv_product_prices SET bigcommerce = '$price' , bigcommerce_fetchdate = '" . date ( 'Y-m-d H:i:s' ) . "' where product_sku = '$product_sku'" );
		}
		catch ( Exception $e ) {
		}
	}
}

include_once ("../Bonanza/bonanza_keys.php");
include_once '../Bonanza/Bonanza.php';

$Bonanza = new Bonanza ();
$Bonanza->setCredential ( $dev_key, $cert_key );

// fetch Bonanza
for($i = 0; $i < $productsCount; $i += $limit) {
	$products = $db->func_query ( "select sku from oc_product where status = 1 limit $i , $limit", "sku" );
	
	foreach ( $products as $product_sku => $product ) {
		try {
			$response = $Bonanza->getProductPrice ( $product_sku );
			$price = $response ['item'] ['buyItNowPrice'];
			
			$db->db_exec ( "update inv_product_prices SET bonanza = '$price' , bonanza_fetchdate = '" . date ( 'Y-m-d H:i:s' ) . "' where product_sku = '$product_sku'" );
		}
		catch ( Exception $e ) {
		}
	}
}

include_once '../Wish/keys.php';
include_once '../Wish/Wish-Merchant-API-master/vendor/autoload.php';

// fetch Bonanza
for($i = 0; $i < $productsCount; $i += $limit) {
	$products = $db->func_query ( "select sku from oc_product where status = 1 limit $i , $limit", "sku" );
	
	foreach ( $products as $product_sku => $product ) {
		$client = new WishClient ( $api_token, 'prod' );
		try {
			$response = $client->getProductBySKU ( $product_sku );
			$price = $response->variants [0]->price;
			
			$db->db_exec ( "update inv_product_prices SET wish = '$price' , wish_fetchdate = '" . date ( 'Y-m-d H:i:s' ) . "' where product_sku = '$product_sku'" );
		}
		catch ( Exception $e ) {
		}
	}
}

echo "success";