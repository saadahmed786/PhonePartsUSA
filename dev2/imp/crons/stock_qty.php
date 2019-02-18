<?php
require_once("../config.php");
require_once("../inc/functions.php");
$time_start = microtime(true); 



$products = $db->func_query("SELECT model,quantity,prefill FROM oc_product WHERE stock_update=0 and status=1 limit 10");
foreach($products as $product)
{
	$on_hand = $product['quantity'];
		$prefill = $product['prefill'];
		$sku = $product['model'];

		
		
		$reserved = $db->func_query_first_cell("SELECT sum(b.product_qty)  FROM inv_orders_items b inner join inv_orders a on (a.order_id=b.order_id) and lower(a.order_status)='on hold' and b.is_picked=0 and b.is_packed=0  and trim(lower(b.product_sku))='".strtolower(trim($sku))."'");
		$not_picked = $db->func_query_first_cell("SELECT sum(b.product_qty) - sum(b.picked_quantity) FROM inv_orders_items b inner join inv_orders a on (a.order_id=b.order_id) and lower(a.order_status) in ('processed','on hold')  and trim(lower(b.product_sku))='".strtolower(trim($sku))."'");
		$picked = $db->func_query_first_cell("SELECT sum(b.picked_quantity) - sum(b.packed_quantity) FROM inv_orders_items b inner join inv_orders a on (a.order_id=b.order_id) and lower(a.order_status) in ('processed','unshipped','on hold') and b.is_picked=1 and a.is_packed=0 and trim(lower(b.product_sku))='".strtolower(trim($sku))."'");
		$packed = $db->func_query_first_cell("SELECT sum(b.packed_quantity) FROM inv_orders_items b inner join inv_orders a on (a.order_id=b.order_id) and lower(a.order_status) in ('processed','unshipped','on hold') and b.is_packed=1 and trim(lower(b.product_sku))='".strtolower(trim($sku))."'");

			$adjustment = $db->func_query_first_cell("SELECT sum(SUBSTRING_INDEX(b.item_sku, '*', -1)) FROM inv_removed_order_items  b inner join inv_orders a on (a.order_id=b.order_id) and lower(a.order_status) in ('processed','unshipped','shipped') and a.is_adjusted=1 and trim(lower(SUBSTRING_INDEX(b.item_sku, '*', 1)))='".strtolower(trim($sku))."' and reason<>'Out of Stock' and trim(lower(SUBSTRING_INDEX(b.item_sku, '*', 1)))<>'sign' and b.is_adjusted=0");
		$allocated_qty =  (int)$not_picked + (int)$picked + (int)$packed ;
		$allocated_qty = (int)$allocated_qty - (int)$reserved;

		$available = (int)$on_hand+(int)$prefill+(int)$adjustment - ((int)$allocated_qty+(int)$reserved);

		echo $sku.'--'.$product['quantity'].'--'.$available."<br>";
}

$time_end = microtime(true);

//dividing with 60 will give the execution time in minutes otherwise seconds
$execution_time = ($time_end - $time_start)/60;

//execution time of the script
echo '<br><br><b>Total Execution Time:</b> '.$execution_time.' Mins';


?>