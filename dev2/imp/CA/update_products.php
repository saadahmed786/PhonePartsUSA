<?php

include_once '../config.php';

$last_run_time = date('Y-m-d H:i:s' , (time() - (24*60*60)));

$products = $db->func_query("select product_id , sku , price from oc_product where ca_updated < '$next_run_time'");
if(count($products) == 0){
	echo "no products";
	exit;
}

include_once("ca_keys.php");
include_once 'ChannelAdvisor.php';

global $db,$DEV_KEY,$Password,$AccountID,$accounts;

foreach($accounts as $account){
	$ChannelAdvisor = new ChannelAdvisor($DEV_KEY , $Password , $account['AccountID'] , $account['Prefix']);
	$ca_credential = $ca_credentials[$account['Prefix']];
	
	//make the batches if more than 10 items
	for($i=0; $i < count($products); $i += 10){
		$skuBatchArray = array_slice($products , $i , 10);
		$response[] = $ChannelAdvisor->UpdateInventoryPriceList($skuBatchArray , $account['Prefix'] , $ca_credential['formula']);
	}
}

print_r($response);