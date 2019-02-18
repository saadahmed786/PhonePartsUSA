<?php

include_once '../config.php';

$products = $db->func_query("select p.* , pd.*, c.name as classification from oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id) 
							 left join inv_classification c on (p.classification_id = c.id)
							 where p.ca_added = 0");
if(count($products) == 0){
	echo "no products";
	exit;
}

include_once("ca_keys.php");
include_once 'ChannelAdvisor.php';
global $db,$DEV_KEY,$Password,$AccountID,$accounts;

$ca_credentials = $db->func_query("select * from ca_credential","prefix");

foreach($accounts as $account){
	$ChannelAdvisor = new ChannelAdvisor($DEV_KEY , $Password , $account['AccountID'] , $account['Prefix']);
	if($account['Prefix'] != 'MM'){
		$distribution_centercode = 'Las Vegas';
	}
	else{
		$distribution_centercode = 'Amazon FBA US';
	}
	
	$ca_credential = $ca_credentials[$account['Prefix']];

	//make the batches if more than 10 items
	for($i=0; $i < count($products); $i += 10){
		$skuBatchArray = array_slice($products , $i , 10);
		$response[] = $ChannelAdvisor->addInventoryItem($skuBatchArray , $distribution_centercode , $ca_credential['formula']);
	}
}

print_r($response);