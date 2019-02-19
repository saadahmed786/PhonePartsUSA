<?php

include_once '../config.php';

$fp = fopen("products.csv","r");
$heading = fgetcsv($fp);

$i = 0;
$products = array();
while(!feof($fp)){
	$row = fgetcsv($fp);
	for($j=0;$j<count($heading);$j++){
		if($row[$j]){
			$products[$i][$heading[$j]] = trim($row[$j]);
		}
	}

	$i++;
}

$db->db_exec("truncate table bigcommerce_mappings");

foreach($products as $product){
	$insert = array();
	$insert['product_id']  = $product['Product ID'];
	$insert['product_sku'] = $product['Code'];
	
	$db->func_array2insert("bigcommerce_mappings",$insert);
}

echo "done";