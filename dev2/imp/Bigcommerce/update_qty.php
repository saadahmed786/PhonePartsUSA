<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once 'big_keys.php';
include_once 'bigcommerce-api-php-master/vendor/autoload.php';

use Bigcommerce\Api\Client as Bigcommerce;

Bigcommerce::configure(array(
	'store_url' => 'https://www.replacementlcds.com',
	'username'	=> $username,
	'api_key'	=> $api_token
));

Bigcommerce::setCipher('TLSv1');
Bigcommerce::verifyPeer(false);
Bigcommerce::failOnError();

function updateBigCommerceInventory($skuArray){
	global $db;
	if(!is_array($skuArray) || count($skuArray) == 0){
		return false;
	}

	$result = array();
	foreach($skuArray as $product){
		$product_id = $db->func_query_first_cell("select order_item_id from inv_orders_items where order_id like 'RL%' AND product_sku = '{$product['sku']}'");
		if(!$product_id){
			$product_id = $db->func_query_first_cell("select product_id from bigcommerce_mappings where product_sku = '{$product['sku']}'");
		}

		if($product_id){
			try{
				$result[] = Bigcommerce::updateProduct($product_id , $product['object']);
			}
			catch(Exception $e){
				$result[] = $e->getMessage();
			}
		}
		else{
			$result[] = "{$product['sku']} not found";
		}
	}

	return $result;
}