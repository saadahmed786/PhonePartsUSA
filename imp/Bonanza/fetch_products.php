<?php

include_once("../config.php");
set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("bonanza_keys.php");
include_once 'Bonanza.php';

$Bonanza = new Bonanza();
$Bonanza->setCredential($dev_key , $cert_key);

$db->db_exec("truncate table bonanza_mappings");

try {
	$page = 1;
	do{
		$products = $Bonanza->getProducts($page);
		if($products['items']){
			foreach($products['items'] as $product){
				$product_id  = $product['itemID'];
				$product_sku = $product['sku'];
				
				$checkExist = $db->func_query_first_cell("select product_id from bonanza_mappings where product_id = '$product_id'");
				if(!$checkExist){
					$insert = array();
					$insert['product_id']  = $product_id;
					$insert['product_sku'] = $product_sku;

					$db->func_array2insert("bonanza_mappings",$insert);
				}
			}
		}

		$total_page = ceil($products['totalEntries'] / $products['size']);
		$page++;
	}
	while($total_page >= $page);
}
catch(Exception $error) {
	echo $error->getCode();
	echo $error->getMessage();
}

echo "done";