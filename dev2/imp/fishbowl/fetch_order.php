<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");
include_once("../config.php");

$order_id = $_REQUEST['order_id'];
$order = $db->func_query_first("select o.* , od.* from inv_orders o inner join inv_orders_details od on o.order_id = od.order_id
							 where o.order_id = '$order_id'");

$order['Items'] = $db->func_query("select * from inv_orders_items where order_id = '".$order_id."'");

print_r(json_encode($order));