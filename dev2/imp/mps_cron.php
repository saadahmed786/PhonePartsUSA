<?php

include_once 'config.php';
include_once 'inc/functions.php';

//insert into `inv_product_outofstock_days` ( `product_sku` , `outofstock_days` , `dateofmodification`) select model , 0 , now() from oc_product where location != 1 and is_kit = 0 and status = 1 group by model
//$db->db_exec("update `oc_product` set is_kit = 1 where model in ( select kit_sku from inv_kit_skus )");

if($_GET['update_days'] == 1){
	//delete old records > 30 days
	$db->db_exec("delete from inv_product_outofstock_days where datediff(now(),dateofmodification) > 30");
	
	$products = $db->func_query("select model from oc_product where quantity = 0 and status = 1 and location != 1 and is_kit = 0");
	$today = date('Y-m-d 00:00:00');
	$db->db_exec("delete from inv_product_outofstock_days where dateofmodification = '".$today."'");

	foreach($products as $product){
		$db->db_exec("insert inv_product_outofstock_days SET outofstock_days = 1 , dateofmodification = '".$today."' , product_sku = '".$product['model']."'");
	}

	echo "success";
	exit;
}

echo "Start:" .date('H:i:s') . "<br />";

$mps_updated = date('Y-m-d 00:00:00', strtotime("-1 Day"));

$limit = (int)@$_GET['limit'];
if($limit){
	$inv_query  = "select p.model from oc_product p where location != 1 and (mps_updated is null OR mps_updated <= '$mps_updated')
				   and status = 1 and is_kit = 0  group by model limit $limit";
}
else{
	$inv_query  = "select p.model from oc_product p where location != 1 and (mps_updated is null OR mps_updated <= '$mps_updated')
				   and status = 1 and is_kit = 0  group by model";
}

$products   = $db->func_query($inv_query);

foreach($products as $product){
	$mps = getDPSBySku($product['model']);

	$db->db_exec("update oc_product SET mps = '$mps' , mps_updated = '".date('Y-m-d 00:00:00')."' where model = '".$product['model']."'");
}

echo "End:" .date('H:i:s') . "<br />";

echo "success";