<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("keys.php");
include_once 'ChannelAdvisor.php';

global $db,$DEV_KEY,$Password,$AccountID;

$ChannelAdvisor = new ChannelAdvisor($DEV_KEY , $Password , $AccountID);

$sku_array[] = array("sku" => "APL-003-0234" , "qty" => "134");
$result = $ChannelAdvisor->UpdateInventoryItemQuantityAndPriceList($sku_array);
print_r($result);