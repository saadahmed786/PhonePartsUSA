<?php
include("../config.php");
include("../inc/functions.php");
$rows = $db->func_query("SELECT * FROM inv_shipment_items WHERE shipment_id='816'");
$total_qty = $db->func_query_first_cell("SELECT SUM(qty_received) FROM inv_shipment_items where shipment_id='816' ");
	$shipping_detail = $db->func_query_first("SELECT shipping_cost,ex_rate FROM inv_shipments WHERE id=816");
	

	$shipping_cost = $shipping_detail['shipping_cost'];
	$ex_rate  = $db->func_escape_string($shipping_detail['ex_rate']);
	$item_shipping_cost = round($shipping_cost / $total_qty,4);

//echo $shipping_cost.'-'.$total_qty;exit;

	
foreach($rows as $row)
{


	

	$product_sku   = $db->func_escape_string($row['product_sku']);
			$product_price = $db->func_escape_string($row['unit_price']);
			
			echo $product_sku.' - '.$product_price.' - '.$ex_rate.' - '.$item_shipping_cost."<br>";

			addUpdateProductCost($product_sku , $product_price , $ex_rate , $item_shipping_cost);
}

?>