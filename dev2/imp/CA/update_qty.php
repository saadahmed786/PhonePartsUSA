<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("ca_keys.php");
include_once 'ChannelAdvisor.php';

function updateCAInventory($skuArray){
	global $db,$DEV_KEY,$Password,$AccountID,$accounts;

	if(!is_array($skuArray) || count($skuArray) == 0){
		return false;
	}

	foreach($accounts as $account){
		$ChannelAdvisor = new ChannelAdvisor($DEV_KEY , $Password , $account['AccountID'] , $account['Prefix']);
		$result[] = $ChannelAdvisor->UpdateInventoryItemQuantityAndPriceList($skuArray);
	}
	
	return $result;
}