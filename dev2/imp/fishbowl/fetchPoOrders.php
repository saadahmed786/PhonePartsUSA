<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");
include_once("../config.php");

$limit = 25;

$orders = $db->func_query("select * from inv_shipments where status = 'Completed' and fb_added = 0 and ignored = 0 order by date_completed DESC limit $limit");
// print_r($orders);exit;
if(count($orders) == 0){
	//echo "NO";
	//exit;
}

foreach($orders as $index => $order){
	$shipment_id = $order['id'];

	$shipment_query  = "select sq.* , si.product_sku , si.qty_received, si.unit_price from inv_shipment_items si left join inv_shipment_qc sq on
					   (si.product_sku = sq.product_sku and si.shipment_id = sq.shipment_id) where si.shipment_id = '$shipment_id' and si.product_sku != ''";
	$shipment_result = $db->func_query($shipment_query);

	$orders[$index]['order_type']  = 'shipment';

	foreach($shipment_result as $shipment_item){
		$qty_received = $shipment_item['qty_received'];

		if($shipment_item['grade_a'] AND $shipment_item['grade_a_qty']){
			$orders[$index]['Items'][] = array(
				'product_sku' => $shipment_item['grade_a'] ,
				'qty_received' => $shipment_item['grade_a_qty'] ,
				'unit_price' => $shipment_item['unit_price'] ,
			);

			$qty_received -= $shipment_item['grade_a_qty'];
		}

		if($shipment_item['grade_b'] AND $shipment_item['grade_b_qty']){
			$orders[$index]['Items'][] = array(
				'product_sku' => $shipment_item['grade_b'] ,
				'qty_received' => $shipment_item['grade_b_qty'] ,
				'unit_price' => $shipment_item['unit_price'] ,
			);

			$qty_received -= $shipment_item['grade_b_qty'];
		}

		if($shipment_item['grade_c'] AND $shipment_item['grade_c_qty']){
			$orders[$index]['Items'][] = array(
				'product_sku' => $shipment_item['grade_c'] ,
				'qty_received' => $shipment_item['grade_c_qty'] ,
				'unit_price' => $shipment_item['unit_price'] ,
			);

			$qty_received -= $shipment_item['grade_c_qty'];
		}

		$qty_received -= $shipment_item['rejected'];
		$qty_received = $qty_received - $shipment_item['ntr']; // substract the ntr from the quantity received
		$orders[$index]['Items'][] = array(
				'product_sku' => $shipment_item['product_sku'] ,
				'qty_received' => $qty_received ,
				'unit_price' => $shipment_item['unit_price'] ,
		);
	}
}

//get returns orders
$return_orders = $db->func_query("select * from inv_returns_po where fb_added = 0 and ignored = 0 order by date_added DESC limit $limit");
if(count($return_orders) > 0){
	foreach($return_orders as $order)
	{
		$index++;
		$orders[$index] = array();
		$orders[$index]['package_number'] = $order['box_number'];
		$orders[$index]['shipping_cost']  = 0;
		$orders[$index]['order_type']  = 'return';
		$orders[$index]['id']  = $order['id'];
		$orders[$index]['date_added']   = $order['date_added'];
		$orders[$index]['date_issued']  = $order['date_added'];

		$return_po_items = $db->func_query("select * from inv_returns_po_items where returns_po_id = '".$order['id']."'");
		foreach($return_po_items as $return_po_item){
			//check if SKU is KIT SKU
			$item_sku = $db->func_escape_string($return_po_item['product_sku']);
			$kit_skus = $db->func_query_first("select * from inv_kit_skus where kit_sku = '$item_sku'");
			if($kit_skus){
				$kit_skus_array = explode(",",$kit_skus['linked_sku']);
				foreach($kit_skus_array as $kit_skus_row){
					$orders[$index]['Items'][] = array(
						'product_sku' => $kit_skus_row,
						'qty_received' => $return_po_item['quantity'] ,
						'unit_price' => $return_po_item['price'] 
					);
				}
			}
			else{
				$orders[$index]['Items'][] = array(
					'product_sku' => $return_po_item['product_sku'] ,
					'qty_received' => $return_po_item['quantity'] ,
					'unit_price' => $return_po_item['price'] 
				);
			}
		}
	}
}

//print_r(json_encode($orders));
print_r(serialize($orders));