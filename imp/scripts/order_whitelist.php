<?php
include_once("../config.php");
include_once '../inc/functions.php';
$orders = $db->func_query('select o.* , od.* from inv_orders o inner join inv_orders_details od on o.order_id = od.order_id where o.whitelist = "" order by o.order_date desc limit 100');
if ($orders) {
	foreach ($orders as $order) {
		$whitelistArr = whiteList($order, 0, 1);
		$whiteListSerialize = serialize($whitelistArr);
		$db->db_exec("UPDATE inv_orders SET whitelist='".$whiteListSerialize."' WHERE order_id='".$order['order_id']."'");
	}
}
if ($orders) {
	echo '100 Orders passed through Verification';	
}else{
	echo 'No Orders Pending to Verify';
}
//testObject($orders);
?>