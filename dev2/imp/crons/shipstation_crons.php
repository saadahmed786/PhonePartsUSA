<?php

include_once '../config.php';
include_once 'shipstation.php';
include_once '../inc/functions.php';

$shipstation = new ShipStation ();

$limit = 10;

$_query = "select o.* , od.* from inv_orders o inner join inv_orders_details od on o.order_id = od.order_id 
		   where (shipstation_added = 0 OR shipstation_added is null) and store_type = 'po_business' group by o.order_id order by order_date DESC limit $limit";
$orders = $db->func_query ( $_query );

if (count ( $orders ) == 0) {
	echo "NO";
	exit ();
}
else {
	foreach ( $orders as $index => $order ) {
		$orders [$index] ['Items'] = $db->func_query ( "select * from inv_orders_items where order_id = '" . $order ['order_id'] . "'" );
	}
}

$response = array();
foreach ( $orders as $order ) {
	$result = json_decode($shipstation->addOrder ( $order ));
	if($result->orderId)
	{
		$db->db_exec("update inv_orders SET shipstation_added = 1 where order_id = '".$order['order_id']."'");
	}
	
	$response[] = $result;
}

print_r ( $response );
exit ();