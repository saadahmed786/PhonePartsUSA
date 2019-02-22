<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");
date_default_timezone_set("America/Los_Angeles");

include_once("../config.php");

include_once 'big_keys.php';
include_once 'bigcommerce-api-php-master/vendor/autoload.php';

use Bigcommerce\Api\Client as Bigcommerce;

Bigcommerce::configure(array(
	'store_url' => 'https://www.replacementlcds.com',
	'username'	=> $username,
	'api_key'	=> $api_token
));

Bigcommerce::setCipher('RC4-SHA');
Bigcommerce::verifyPeer(false);
Bigcommerce::failOnError();

try {
	$page = 1;
	do{
		$filter = array("limit"=>100,"page"=>$page);
		$products = Bigcommerce::getProducts($filter);
		if($products){
			foreach($products as $product){
				$product_id  = $product->id;
				$product_sku = $product->sku;

				$checkExist = $db->func_query_first_cell("select product_id from bigcommerce_mappings where product_sku = '$product_sku'");
				if(!$checkExist){
					$insert = array();
					$insert['product_id']  = $product->id;
					$insert['product_sku'] = $product->sku;

					$db->func_array2insert("bigcommerce_mappings",$insert);
				}
			}
		}

		$page++;
	}
	while($products);
}
catch(Bigcommerce\Api\Error $error) {
	echo $error->getCode();
	echo $error->getMessage();
}

echo "done";