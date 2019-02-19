<?php

include_once("../config.php");

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("bonanza_keys.php");
include_once 'Bonanza.php';

$Bonanza = new Bonanza();
$Bonanza->setCredential($dev_key , $cert_key);

function updateBonanzaInventory($skuArray){
	global $db , $Bonanza , $auth_token;
	if(!is_array($skuArray) || count($skuArray) == 0){
		return false;
	}

	$result = array();
	foreach($skuArray as $product){
		$product_id = $db->func_query_first_cell("select product_id from bonanza_mappings where product_sku = '{$product['sku']}'");
		
		if($product_id){
			try{
				$result[] = $Bonanza->updateInventory($product_id , $product['qty'] , $auth_token);
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