<?php

include_once '../config.php';
include_once '../inc/functions.php';

$shipments = $db->func_query("select * from inv_shipments where status = 'Completed'");

foreach($shipments as $shipment){
	$shipment_id = $shipment['id'];

	$shipment_query  = "select sq.* , si.product_sku , si.qty_received , si.unit_price
					   from inv_shipment_items si left join inv_shipment_qc sq on 
					   (si.product_sku = sq.product_sku and si.shipment_id = sq.shipment_id) 
					   where si.shipment_id = '$shipment_id' and si.product_sku != ''";
	$shipment_items  = $db->func_query($shipment_query);

	$total_qty = 0;
	foreach($shipment_items as $shipment_item){
		$total_qty += $shipment_item['qty_received'];
	}

	$ex_rate  = $db->func_escape_string($shipment['ex_rate']);
	if($total_qty <= 0){
		$total_qty = 1;
	}

	$item_shipping_cost = round($shipment['shipping_cost'] / $total_qty,4);
	$date_completed = date('Y-m-d', strtotime($shipment['date_completed']));

	foreach($shipment_items as $shipment_item){
		$product_sku   = $db->func_escape_string($shipment_item['product_sku']);
		$product_price = $db->func_escape_string($shipment_item['unit_price']);
			
		addUpdateProductCost($product_sku , $product_price , $ex_rate , $item_shipping_cost , $date_completed);
	}
}