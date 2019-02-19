<?php

include '../config.php';

$last_date = date('Y-m-d');//'2015-04-05';

$order_items = $db->func_query("select order_id,product_sku,product_qty,dateofmodification from inv_orders_items where dateofmodification >= '$last_date'");
if(!$order_items){
	exit;
}

foreach($order_items as $order_item){
	$product_sku = $order_item['product_sku'];
	$order_date  = date("Y-m-d", strtotime($order_item['dateofmodification']));
	
	$checkExist = $db->func_query_first("select id from inv_product_sale_history where product_sku = '$product_sku' and sale_date = '$order_date'");
	if(!$checkExist){
		$product_sale_history = array();
		$product_sale_history['product_sku'] = $product_sku;
		$product_sale_history['sale_date'] = $order_date;
		$product_sale_history['order_id']  = $order_item['order_id'];
		$product_sale_history['quantity']  = $order_item['product_qty'];
		
		$db->func_array2insert("inv_product_sale_history", $product_sale_history);
	}
}

echo "success";