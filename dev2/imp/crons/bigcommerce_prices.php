<?php

set_time_limit ( 0 );
ini_set ( "memory_limit", "2048M" );

use Bigcommerce\Api\Client as Bigcommerce;

include '../config.php';
$productsCount = $db->func_query_first_cell ( "select count(product_sku) from inv_product_prices" );

include '../Bigcommerce/big_keys.php';
include '../Bigcommerce/bigcommerce-api-php-master/vendor/autoload.php';

Bigcommerce::configure ( array (
	'store_url' => 'https://www.replacementlcds.com',
	'username' => $username,
	'api_key' => $api_token
));

Bigcommerce::setCipher ( 'RC4-SHA' );
Bigcommerce::verifyPeer ( false );
Bigcommerce::failOnError ();

$limit = 100;

// fetch bigcommerce
for($i = 0; $i < $productsCount; $i += $limit) {
	$products = $db->func_query ( "select product_sku from inv_product_prices where bigcommerce_fetchdate not like '%".date('Y-m-d')."%' limit $i , $limit", "product_sku" );

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

echo "success";