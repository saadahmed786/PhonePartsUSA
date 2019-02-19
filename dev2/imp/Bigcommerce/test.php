<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("../config.php");
include_once("update_qty.php");

$object = new stdClass();
$object->inventory_level = 5;
$sku_array[] = array("sku" => "TAB-SRN-048" , "object" => $object);

$ca_response = updateBigCommerceInventory($sku_array);
print_r($ca_response);