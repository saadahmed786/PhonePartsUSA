<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");
date_default_timezone_set("America/Los_Angeles");

include_once("../db.php");
include_once("../config.php");

include_once 'keys.php';
include_once 'Wish-Merchant-API-master/vendor/autoload.php';

use Wish\WishClient;
$client = new WishClient($api_token,'prod');

//print "RESULT: ".print_r($client->getAllProducts()); exit;

include_once 'update_qty.php';

$skuArray[] = array('sku' => 'ACC-APL-2030' , 'qty' => 10);
$result = updateWishInventory($skuArray);
print_r($result);