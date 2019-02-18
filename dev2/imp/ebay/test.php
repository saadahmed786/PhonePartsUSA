<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("../config.php");
include_once("eb_updateQty.php");

$sku_array[0] = array('sku' => 'SRN-SAM-496' , 'qty' => '8');

$response = updateEbayQty($sku_array);

print_r($response);