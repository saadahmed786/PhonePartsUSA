<?php

set_time_limit ( 0 );
ini_set ( "memory_limit", "2048M" );

include '../config.php';
$productsCount = $db->func_query_first_cell ( "select count(product_sku) from inv_product_prices" );

include_once ("../Bonanza/bonanza_keys.php");
include_once '../Bonanza/Bonanza.php';

$Bonanza = new Bonanza ();
$Bonanza->setCredential ( $dev_key, $cert_key );

$limit = 100;

// fetch Bonanza
for($i = 0; $i < $productsCount; $i += $limit) {
	$products = $db->func_query ( "select product_sku from inv_product_prices where bonanza_fetchdate is null OR bonanza_fetchdate not like '%".date('Y-m-d')."%' limit $i , $limit", "product_sku" );
	
	foreach ( $products as $product_sku => $product ) {
		try {
			$response = $Bonanza->getProductPrice ( $product_sku );
			$price = $response ['item'] ['buyItNowPrice'];
				
			$db->db_exec ( "update inv_product_prices SET bonanza = '$price' , bonanza_fetchdate = '" . date ( 'Y-m-d H:i:s' ) . "' where product_sku = '$product_sku'" );
		}
		catch ( Exception $e ) {
			print $e->getMessage(). "<br />";
		}
	}
}

echo "success";