<?php
ini_set('max_execution_time', 600); //300 seconds = 5 minutes
require_once("../auth.php");
require_once("../inc/functions.php");

$_orders = $db->func_query("SELECT order_id FROM inv_orders WHERE store_type='bigcommerce' AND MONTH(order_date)='09' AND YEAR(order_date)='2015'");
foreach($_orders as $_x)
{
$orders = $db->func_query("SELECT id,order_id,product_sku FROM inv_orders_items WHERE order_id='".$_x['order_id']."'");
foreach($orders as $order)
{
	$TrueCost = getTrueCost($order['product_sku']);
	
	//if($TrueCost)
	//{
		$db->db_exec("UPDATE inv_orders_items SET product_true_cost='".(float)$TrueCost."' WHERE id='".$order['id']."'");	
		
	//}
	
}
}

?>