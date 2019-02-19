<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("amazon_config.php");
include_once 'AmazonAPI.php';

$merchantInfo = $db->func_query("Select * from amazon_credential order by dateofmodifications DESC");
if(!@$merchantInfo){
	echo "No merchant exist";
	exit;
}

function updateInventory($skuArray , $merchant_id = false , $market_place_id = false){
	global $merchantInfo , $db;

	if(!is_array($skuArray) || count($skuArray) == 0){
		return false;
	}

	if(!$merchant_id || !$market_place_id){
		foreach($merchantInfo as $amazonAccount){
			$amazon_credential_id = $amazonAccount['id'];
			$merchant_id     = $amazonAccount['merchant_id'];
			$market_place_id = $amazonAccount['market_place_id'];
			$aws_access_key  = $amazonAccount['aws_access_key'];
			$aws_secret_key  = $amazonAccount['aws_secret_key'];

			$AmazonAPI = new AmazonAPI($market_place_id , $aws_access_key , $aws_secret_key);
			$xml = $AmazonAPI->InventoryXml($skuArray , $merchant_id);

			$feed_status[$merchant_id] = $AmazonAPI->SendRequest($xml , $merchant_id);
		}
	}
	else{
		$AmazonAPI = new AmazonAPI($market_place_id);
		$xml = $AmazonAPI->InventoryXml($skuArray , $merchant_id);

		$feed_status[$merchant_id] = $AmazonAPI->SendRequest($xml , $merchant_id);
	}

	return $feed_status;
}