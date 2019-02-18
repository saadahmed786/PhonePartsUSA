<?php
include_once("config.php");
include_once("inc/functions.php");

$manual_users =  $db->func_query('SELECT * FROM inv_order_history h INNER JOIN inv_orders o on (h.order_id = o.order_id) WHERE h.comment LIKE "%has been created%" AND YEAR (h.date_added) > "2015" AND o.order_user = "" limit 500');
//testObject($manual_users);
foreach ($manual_users as $u) {
	$db->db_exec("UPDATE inv_orders SET order_user='" . (int)$u['user_id'] . "' WHERE order_id='" . $u['order_id'] . "'");
}

?>