<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("amazon_config.php");
include_once 'AmazonAPI.php';

$merchantInfo = $db->func_query_first("Select id ,merchant_id,market_place_id, last_cron_date from amazon_credential order by dateofmodifications DESC");
if(!@$merchantInfo){
    return;
}

function updateInventory($skuArray , $merchant_id = false , $market_place_id = false){
    global $merchantInfo , $db;

    if(!is_array($skuArray) || count($skuArray) == 0){
        return false;
    }

    if(!$merchant_id || !$market_place_id){
        $merchant_id = $merchantInfo['merchant_id'];
        $market_place_id = $merchantInfo['market_place_id'];
    }

    $AmazonAPI = new AmazonAPI($market_place_id);
    $xml = $AmazonAPI->InventoryXml($skuArray , $merchant_id);
    
    $feed_status = $AmazonAPI->SendRequest($xml , $merchant_id);

    return $feed_status;
}