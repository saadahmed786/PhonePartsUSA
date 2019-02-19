<?php
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
//require_once("auth.php");
include_once 'config.php';
require_once("inc/functions.php");

$orders = $db->func_query("SELECT order_id,TRIM(LOWER(email)) as email,order_price,paid_price,order_status,order_date FROM inv_orders WHERE customer_synced =0 and store_type<>'amazon'
AND (
LOWER( order_status ) 
IN (
 'processed',  'shipped',  'completed',  'unshipped' , 'on hold'
)
)
and DATE(order_date) = DATE(NOW())
 ORDER BY id desc LIMIT 200");


foreach($orders as $order)
{
	$detail = $db->func_query_first("SELECT first_name,last_name,phone_number,address1,city,state,zip,zone_id,company FROM inv_orders_details WHERE order_id='".$order['order_id']."'");
	
	$customer_detail = $db->func_query_first("SELECT a.customer_id,b.name, a.date_added FROM oc_customer a, oc_customer_group_description b WHERE a.`customer_group_id`=b.`customer_group_id` AND a.`email`='".$order['email']."'");
	$data['date_added'] = $customer_detail['date_added'];	
	if(!$customer_detail)
	{
		$customer_detail['customer_id'] = 0;	
		$customer_detail['name'] = 'Default';	
		$data['date_added'] = $order['order_date'];
	}
	$data = array();
	$data['firstname'] = $db->func_escape_string($detail['first_name']);
	$data['lastname'] = $db->func_escape_string($detail['last_name']);
	$data['email'] = $db->func_escape_string(trim($order['email']));
	$data['city'] = $db->func_escape_string($detail['city']);
	$data['state'] = $db->func_escape_string($detail['state']);
	$data['zone_id'] = $db->func_escape_string($detail['zone_id']);
	$data['customer_group'] = $customer_group;
	$data['customer_id'] = $customer_detail['customer_id'];	
	$data['customer_group'] = $customer_detail['name'];	
	$data['address1']=$db->func_escape_string($detail['address1']);
	$data['zip'] = $db->func_escape_string($detail['zip']);
	$data['company'] = $db->func_escape_string($detail['company']);
	$check_query = $db->func_query_first("SELECT * FROM inv_customers WHERE TRIM(LOWER(email))='".trim(strtolower($order['email']))."'");

	if($check_query)
	{
		//$data['no_of_orders'] = $check_query['no_of_orders']+1;
		//$data['total_amount'] = $check_query['total_amount']+$order_price;
		
		$db->func_array2update("inv_customers", $data,"TRIM(LOWER(email))='".trim(strtolower($data['email']))."'");
	}
	else
	{
		$data['date_added'] = $order['order_date'];
			//$data['total_amount'] = $order_price;
		$db->func_array2insert("inv_customers", $data);
	}
	$db->db_exec("UPDATE inv_orders SET customer_synced=1 WHERE order_id='".$order['order_id']."'");
	echo $order['order_id']."<br>";
}


?>