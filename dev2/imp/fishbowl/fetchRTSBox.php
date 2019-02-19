<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");
include_once("../config.php");

$limit = 25;
//No GFS transfer into fishbowl Gohar
/*$orders = $db->func_query("select id,box_number as package_number,date_added,0.00 as shipping_cost from inv_return_shipment_boxes where status = 'Completed' and fb_added = 0 and box_type='GFSBox' order by date_completed DESC limit $limit");*/
if(count($orders) == 0){
	//echo "NO";
	//exit;
}

foreach($orders as $index => $order){
	$shipment_id = $order['id'];


	$orders[$index]['order_type']  = 'shipment';
	$orders[$index]['date_issued']  = date('Y-m-d H:i:s');
$shipment_items = $db->func_query("SELECT * FROM inv_return_shipment_box_items WHERE return_shipment_box_id='".(int)$shipment_id."' ");
		foreach($shipment_items as $shipment_item)
		{
		$orders[$index]['Items'][] = array(
				'product_sku' => $shipment_item['product_sku'] ,
				'qty_received' => $shipment_item['quantity'] ,
				'unit_price' => $shipment_item['price'] ,
		);
	}
	}


print_r(json_encode($orders));
// print_r(serialize($orders));