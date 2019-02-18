<?php
ini_set('max_execution_time', 600); //300 seconds = 5 minutes
//require_once("auth.php");
include_once '../config.php';
require_once("../inc/functions.php");
$today = $db->func_query_first_cell("SELECT cron_date FROM temp_inout WHERE completed=0 order by id desc");
if(!$today)
{
	echo 'Date Range Full';
	exit;
}
$products = $db->func_query("SELECT product_id,model as sku FROM oc_product WHERE is_temp_updated=0 and status=1 limit 300");
if(empty($products))
{
	$db->db_exec("UPDATE oc_product SET is_temp_updated=0");
	$db->db_exec("UPDATE temp_inout SET completed=1 WHERE cron_date='$today'");
	echo 'Reset';
	exit;
}

foreach($products as $_product)
{

	$sku = $_product['sku'];

	$kit_skus = $db->func_query_first("select * from inv_kit_skus where kit_sku = '$sku'");
	if ($kit_skus) {
		$is_kit_sku = true;
		$kit_skus_array = explode(",", $kit_skus['linked_sku']);
	}
	else
	{
		$kit_skus_array = array($_product['sku']);
		$is_kit_sku = false;
	}
     foreach($kit_skus_array as $product)
     {
       $sku = $product;         
		$current_qty = (int)$db->func_query_first_cell("SELECT quantity FROM oc_product WHERE model='".$sku."'");

		if($current_qty<0) $current_qty = 0;

		$qty_received = (int)$db->func_query_first_cell("SELECT SUM(b.qty_received) FROM inv_shipments a,inv_shipment_items b WHERE a.id=b.shipment_id and b.product_sku='".$sku."' AND DATE(a.date_completed)='$today' AND a.fb_added=1 ");
		$qty_received+=(int)$db->func_query_first_cell("SELECT SUM(b.quantity) FROM inv_return_shipment_boxes a,inv_return_shipment_box_items b WHERE a.id=b.return_shipment_box_id and b.product_sku='".$sku."' AND DATE(a.date_completed)='$today' AND a.fb_added=1 and a.box_type='GFSBox' ");

		$_temp =  $db->func_query_first("SELECT SUM(b.product_qty) as qty_sold,AVG(b.product_true_cost) as avg_cost,SUM(b.product_price) as total_sold FROM inv_orders a,inv_orders_items b WHERE a.order_id=b.order_id AND b.product_sku='".$sku."'  and date(a.order_date)='$today' AND LOWER(a.order_status) IN ( 'processed',  'shipped',  'completed',  'unshipped')");

		// $qty_sold = (int)$db->func_query_first_cell("SELECT SUM(b.product_qty) FROM inv_orders a,inv_orders_items b WHERE a.order_id=b.order_id AND b.product_sku='".$product['sku']."'  and date(a.order_date)='$today' AND LOWER(a.order_status) IN ( 'processed',  'shipped',  'completed',  'unshipped')");
		// $avg_cost = (float)$db->func_query_first_cell("SELECT AVG(b.product_true_cost) FROM inv_orders a,inv_orders_items b WHERE a.order_id=b.order_id AND b.product_sku='".$product['sku']."'  and date(a.order_date)='$today' AND LOWER(a.order_status) IN ( 'processed',  'shipped',  'completed',  'unshipped')");
		// $avg_price = (float)$db->func_query_first_cell("SELECT AVG(b.product_price) FROM inv_orders a,inv_orders_items b WHERE a.order_id=b.order_id AND b.product_sku='".$product['sku']."'  and date(a.order_date)='$today' AND LOWER(a.order_status) IN ( 'processed',  'shipped',  'completed',  'unshipped')");
		// $total_sold = (float)$db->func_query_first_cell("SELECT SUM(b.product_price) FROM inv_orders a,inv_orders_items b WHERE a.order_id=b.order_id AND b.product_sku='".$product['sku']."'  and date(a.order_date)='$today' AND LOWER(a.order_status) IN ( 'processed',  'shipped',  'completed',  'unshipped')");

		$qty_sold = (int)$_temp['qty_sold'];
		$avg_cost = (float)$_temp['avg_cost'];
		//$avg_price = (float)$_temp['avg_price'];
		$total_sold = (float)$_temp['total_sold'];
		$avg_price = $total_sold / $qty_sold;
		$date_added = date('Y-m-d H:i:s');

		if($qty_received || $qty_sold || $avg_cost || $avg_price || $total_sold)
		{
			echo $sku."<br>";
			$db->db_exec("DELETE FROM inv_inout_report WHERE sku='$sku' and date(cron_date)='$today'");
			$db->db_exec("INSERT INTO inv_inout_report SET sku='$sku',
			current_qty='$current_qty',
			qty_received='$qty_received',
			qty_sold='$qty_sold',
			avg_cost='$avg_cost',
			avg_price='$avg_price',
			total_sold='$total_sold',
			date_added='$date_added',
			cron_date ='$today'
			");

			$db->db_exec("UPDATE oc_product SET is_temp_updated=1 WHERE model='".$_product['sku']."'");
		}
		else
		{
			$db->db_exec("UPDATE oc_product SET is_temp_updated=1 WHERE model='".$_product['sku']."'");
			continue;
		}
	}
}

?>