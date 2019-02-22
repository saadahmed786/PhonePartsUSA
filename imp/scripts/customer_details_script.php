<?php
ini_set('memory_limit', '3072M');
//echo ini_get('max_execution_time');exit;
set_time_limit(0); //300 seconds = 5 minutes

require_once("../config.php");
//require_once("../inc/functions.php");

$rows = $db->func_query("SELECT
firstname,lastname,email,customer_group,no_of_orders,total_amount,last_order FROM inv_customers  WHERE temp_bit=0 and ( (no_of_orders>=5) OR (email LIKE '%phone%' OR email LIKE '%computer%' OR email LIKE '%repair%' OR email LIKE '%fix%' OR email LIKE '%cpr%' OR email LIKE '%comp%')) LIMIT 30");
// $filename = "customers-" . uniqid() . ".csv";
// $fp = fopen($filename, "w");
// $headers = array('Firstname','Lastname','Telephone','Email','Address 1','Address 2','City','State','Zip Code','Customer Group','No of Orders','Total Amount','Last Order');
// fputcsv($fp, $headers,',');
// $i = 0;

// foreach($rows as $row)
// {
// 	$data = array();
// 	$order_info = $db->func_query_first("SELECT b.phone_number,b.bill_address1,b.bill_address2,b.city,b.state,b.zip FROM inv_orders a, inv_orders_details b where a.order_id=b.order_id AND LOWER(a.email)='".strtolower($row['email'])."' ORDER BY a.id DESC LIMIT 1");
	
// 	$data[] = $db->func_escape_string($row['firstname']);
// 	$data[] = $db->func_escape_string($row['lastname']);
// 	$data[] = $db->func_escape_string($order_info['phone_number']);
	
// 	$data[] = $db->func_escape_string($row['email']);
// 	$data[] = $db->func_escape_string($order_info['bill_address1']);
// 	$data[] = $db->func_escape_string($order_info['bill_address2']);
// 	$data[] = $db->func_escape_string($order_info['city']);
// 	$data[] = $db->func_escape_string($order_info['state']);
// 	$data[] = $db->func_escape_string($order_info['zip']);
// 	$data[] = $db->func_escape_string($row['customer_group']);
// 	$data[] = $db->func_escape_string($row['no_of_orders']);

// 	$data[] = $db->func_escape_string($row['total_amount']);
// 	$data[] = $db->func_escape_string($row['last_order']);
	
// fputcsv($fp, $data,',');
// $i++;
// }

// fclose($fp);

// header('Content-type: application/csv');
// header('Content-Disposition: attachment; filename="' . $filename . '"');
// readfile($filename);
// @unlink($filename);


foreach($rows as $row)
 {
 	$data = array();
 	$order_info = $db->func_query_first("SELECT b.phone_number,b.address1,b.address2,b.city,b.state,b.zip FROM inv_orders a, inv_orders_details b where a.order_id=b.order_id AND TRIM(LOWER(a.email))='".$db->func_escape_string(trim(strtolower($row['email'])))."' ORDER BY a.id DESC LIMIT 1");
	
 	$data['telephone'] = $db->func_escape_string($order_info['phone_number']);
 	$data['address1'] = $db->func_escape_string($order_info['address1']);
 	$data['address2'] = $db->func_escape_string($order_info['address2']);
 	$data['city'] = $db->func_escape_string($order_info['city']);
 	$data['state'] = $db->func_escape_string($order_info['state']);
 	$data['zip'] = $db->func_escape_string($order_info['zip']);
 	$data['temp_bit'] = 1;
$db->func_array2update('inv_customers', $data, 'TRIM(LOWER(email)) = "'. $db->func_escape_string(trim(strtolower($row['email']))) .'"');
	
echo $row['email']."<br>";
	}
?>