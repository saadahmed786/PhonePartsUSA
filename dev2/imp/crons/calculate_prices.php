<?php

include_once '../config.php';

$price_markups = $db->func_query_first("select * from inv_price_markups");

$products = $db->func_query("select product_id , sku from oc_product");
foreach($products as $product){
	$product_id = $product['product_id'];
	$first_price = $db->func_query_first_cell("select price from oc_product_discount where product_id = '".$product_id."' and customer_group_id = 6 and quantity = 1");
	if($first_price > 0){
		$x = $first_price;
	}
	else{
		$x = $db->func_query_first_cell("select price from oc_product_discount where product_id = '$product_id' AND customer_group_id = '8' and quantity = 1");
	}
		
	if($x <= 0){
		continue;
	}
	
	$product_prices = array();
	$product_prices['channel_advisor_new']  = eval("return ".$price_markups['channel_advisor']);
	$product_prices['channel_advisor1_new'] = eval("return ".$price_markups['channel_advisor1']);
	$product_prices['channel_advisor2_new'] = eval("return ".$price_markups['channel_advisor2']);
	$product_prices['bigcommerce_new'] = eval("return ".$price_markups['bigcommerce']);
	$product_prices['bigcommerce_retail_new'] = eval("return ".$price_markups['bigcommerce_retail']);
	$product_prices['bonanza_new'] = eval("return ".$price_markups['bonanza']);
	$product_prices['wish_new'] = eval("return ".$price_markups['wish']);
	$product_prices['ebay_new'] = eval("return ".$price_markups['ebay']);
	$product_prices['amazon_new'] = eval("return ".$price_markups['amazon']);
	$product_prices['open_sky_new'] = eval("return ".$price_markups['open_sky']);
	$product_prices['date_modified'] = date('Y-m-d H:i:s');
	
	$checkExist = $db->func_query_first("select id from inv_product_prices where product_sku = '".$product['sku']."'");
	if(!$checkExist){
		$product_prices['product_sku'] = $product['sku'];
		$db->func_array2insert("inv_product_prices", $product_prices);
	}
	else{
		$db->func_array2update("inv_product_prices", $product_prices,"product_sku = '".$product['sku']."'");
	}
}

echo "Prices calculated successfully.";