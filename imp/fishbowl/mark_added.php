<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("../config.php");

$productIds = $_REQUEST['productIds'];
if($productIds){
	if($_GET['success'] == 1){
		$db->db_exec("Update oc_product SET fb_added = 1 , date_modified = '".date('Y-m-d H:i:s')."' where product_id IN ($productIds)");
	}
	else{
		$db->db_exec("Update oc_product SET ignored = 1 , date_modified = '".date('Y-m-d H:i:s')."' where product_id IN ($productIds)");
	}
}

echo "success";