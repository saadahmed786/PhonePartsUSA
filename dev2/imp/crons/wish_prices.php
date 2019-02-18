<?php

set_time_limit ( 0 );
ini_set ( "memory_limit", "2048M" );

use Wish\WishClient;

include '../config.php';
$productsCount = $db->func_query_first_cell ( "select count(product_sku) from inv_product_prices" );

include_once '../Wish/keys.php';
include_once '../Wish/Wish-Merchant-API-master/vendor/autoload.php';

$limit = 100;

// fetch Bonanza
for($i = 0; $i < $productsCount; $i += $limit) {
	$products = $db->func_query ( "select product_sku from inv_product_prices where wish_fetchdate is null OR wish_fetchdate not like '%".date('Y-m-d')."%' limit $i , $limit", "product_sku" );

	foreach ( $products as $product_sku => $product ) {
		$client = new WishClient ( $api_token, 'prod' );
		try {
			$response = $client->getProductBySKU ( $product_sku );
			$price = $response->variants [0]->price;
				
			$db->db_exec ( "update inv_product_prices SET wish = '$price' , wish_fetchdate = '" . date ( 'Y-m-d H:i:s' ) . "' where product_sku = '$product_sku'" );
		}
		catch ( Exception $e ) {
			print $e->getMessage(). "<br />";
		}
	}
}

echo "success";