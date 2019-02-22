<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once 'Newegg.php';
$Newegg = new Newegg();

function updateNeweggInventory($skuArray){
	global $db , $Newegg;
	if(!is_array($skuArray) || count($skuArray) == 0){
		return false;
	}

	$result = array();
	foreach($skuArray as $product){
		try{
			$result[] = $Newegg->updateInventory($product['sku'] , $product['qty']);
		}
		catch(Exception $e){
			$result[] = $e->getMessage();
		}
	}

	return $result;
}