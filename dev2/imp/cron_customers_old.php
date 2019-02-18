<?php
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
//require_once("auth.php");
include_once 'config.php';
require_once("inc/functions.php");
$datetime_start = date("Y-m-d 23:00:00",strtotime("-1 day"));
$datetime_end = date("Y-m-d 22:59:59");
$emails = $db->func_query("SELECT distinct email
FROM inv_customers
WHERE year( last_order ) = '2015' and month(last_order)='07'");
foreach($emails as $email)
{
    $order_info = $db->func_query_first("SELECT COUNT(order_id) AS total_orders, SUM(order_price) as total FROM inv_orders WHERE email='".$email['email']."' AND `order_status` IN (
		'Shipped',
		'On Hold',
		'Processed',
		'Store Pick Up'
		) ");
    
    $db->db_exec("UPDATE inv_customers SET no_of_orders='".$order_info['total_orders']."' AND total_amount='".$order_info['total']."' WHERE email='".$email."'");
    
//    echo $email['email']."<pre>";
//    print_r($order_info);
//    echo "</pre>==========================================";
    
}

?>