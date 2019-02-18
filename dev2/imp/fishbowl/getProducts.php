<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");
include_once("../config.php");

$start = (int)$_REQUEST['start'];
if(!$start){
	$start = 0;
}

$product_str = false;

$productIDs = $db->func_query("select product_id from oc_product_to_field where additional_product_id = 5 and name = 'New Product'");
if($productIDs){
	$product_str = array();
	foreach($productIDs as $product){
		$product_str[] = $product['product_id'];
	}

	$product_str = implode(",",$product_str);
}

if($product_str){
	$count = (int)$_REQUEST['count'];
	if($count){
		$total = $db->func_query_first_cell("select count(model) from oc_product where fb_added = 0 and product_id in ($product_str)");
		echo $total;
	}
	else{
		$products = $db->func_query("select p.sku, p.product_id , pd.name from
								 oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id)
								 where fb_added = 0 and p.product_id in ($product_str) limit $start , 5");
		if(count($products) == 0){
			echo "NO";
			exit;
		}

		print_r(serialize($products));
	}
}
else{
	echo "NO";
	exit;
}