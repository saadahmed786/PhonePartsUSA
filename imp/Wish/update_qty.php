<?php

use Wish;
set_time_limit(0);
ini_set("memory_limit", "20000M");
date_default_timezone_set("America/Los_Angeles");

include_once("../config.php");

include_once 'keys.php';
include_once 'Wish-Merchant-API-master/vendor/autoload.php';

use Wish\WishClient;

function updateWishInventory($skuArray){
	global $db,$api_token;
	if(!is_array($skuArray) || count($skuArray) == 0){
		return false;
	}

	$client = new WishClient($api_token,'prod');

	$result = array();
	foreach($skuArray as $product){
		$result[] = $client->updateInventoryBySKU($product['sku'], $product['qty']);
	}

	return $result;
}