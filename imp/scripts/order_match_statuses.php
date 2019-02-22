<?php
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
require_once("../auth.php");
require_once("../inc/functions.php");

$inv_orders = $db->func_query("SELECT order_id,order_status FROM inv_orders WHERE store_type='web' AND MONTH(order_date)='05'  AND YEAR(order_date)='2015' ");
foreach($inv_orders as $inv_order)
{
		$check_ref_order_id = $db->func_query_first("SELECT order_id,ref_order_id FROM oc_order WHERE ref_order_id='".$inv_order['order_id']."'");
		if(!$check_ref_order_id)
		{
		$order = $db->func_query_first("SELECT order_status_id FROM oc_order WHERE order_id='".(int)$inv_order['order_id']."' ");
		}
		else
		{
			$order = $db->func_query_first("SELECT order_status_id FROM oc_order WHERE order_id='".$check_ref_order_id['order_id']."' ");
		}
		if($order)
		{
		switch($order['order_status_id'])
		{
			case "21":
			$status = "On Hold"; // On Hold
			break;	
			case "3":
			$status = "Shipped"; // Shipped
			break;	
			case "7":
			$status = "Canceled"; // Canceled
			break;	
			case "15":
			$status = "Processed"; // Processed
			break;
			case "24":
			$status = "Processed"; // Store Pickup
			break;	
			case "11":
			$status = "Refunded"; // Refunded
			break;
			case "16":
			$status = "Voided"; // Voided
			break;	
			default:
			$status = 'Completed';
			break;
			
			
			
		}
		
		if($inv_order['order_status']!=$status)
		{
		$db->db_exec("UPDATE inv_orders SET order_status='$status' WHERE order_id='".$inv_order['order_id']."'"); // Update Query
		
		// Log
		echo "Order ID:".$inv_order['order_id']."<br>";
		echo "Status was:".$inv_order['order_status']."<br>";
		echo "Status changed with:".$status."<br>==============================================================================<br>";
		}
		
		}
}
?>