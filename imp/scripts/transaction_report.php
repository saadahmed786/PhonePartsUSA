<?php
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 0); //300 seconds = 5 minutes
include_once '../config.php';
include_once '../inc/functions.php';
function getOrderTotal($order_id,$shipping_cost)
{
	global $db;
	$order_items = $db->func_query("Select * from inv_orders_items where order_id = '$order_id' ");
	$sub_total = 0;



	foreach ($order_items as $zz => $order_item) {






		$sub_total+=($order_item['product_price']-$order_item['promotion_discount']);



	}

	$order_total = $shipping_cost + $sub_total;



	$_tax = round($db->func_query_first_cell('SELECT SUM(`value`) FROM `oc_order_total` WHERE cast(`order_id` as char(50)) = "'. $order_id .'" AND `code` = "tax"'),2);



	if($_tax<0.00)$_tax=0.00;

	$order_total = $order_total + $_tax;
	return $order_total;
}

$date = $_GET['date'];
if(!$date)
{
	$date = date('Y-m-d');
}

$filename = $date.'_transaction_report.csv';
$fp = fopen($filename, "w");
$headers = array("Order ID", "Email","Order Total", "Total Voucher","Voucher Code(s)","Total CC","Total PayPal","Transaction ID(s)"
	);
fputcsv($fp, $headers,',');

$rows = $db->func_query("SELECT DISTINCT a.order_date, a.prefix,a.order_id, a.email,a.order_price,a.payment_source,b.shipping_cost
	FROM  inv_orders a,inv_orders_details b
	WHERE a.order_id=b.order_id and DATE( a.order_date ) =  '$date'
	AND LOWER( a.email ) NOT 
	IN (
		'fba@amazon.com',  'ecafferty@encompass.com'
	)
	order by a.order_date asc");
foreach($rows as $row)
{
	
	$vouchers = $db->func_query("SELECT * FROM oc_voucher_history WHERE cast(`order_id` as char(50)) ='".$row['order_id']."' or inv_order_id='".$row['order_id']."'");
	$total_voucher = 0.00;
	$voucher_codes ='';
	foreach($vouchers as $voucher)
	{
		$total_voucher  = (float) $total_voucher + (float)$voucher['amount'];
		$voucher_codes = $voucher_codes . $db->func_query_first_cell("SELECT code FROM oc_voucher where voucher_id='".$voucher['voucher_id']."'").',';
	}
	$voucher_codes = rtrim($voucher_codes,',');
	$total_voucher = $total_voucher * (-1);


	$transactions = $db->func_query("SELECT transaction_id,amount FROM inv_transactions WHERE order_id = '".$row['order_id']."'");
	$amount = 0.00;
	$transaction_ids = '';
	foreach($transactions as $transaction)
	{
		$amount = (float)$amount + (float)$transaction['amount'];
		$transaction_ids = $transaction_ids .$transaction['transaction_id'].','; 
	}
	$transaction_ids = rtrim($transaction_ids,',');
	$total_cc = 0.00;
	$total_paypal = 0.00;
	if(strtolower($row['payment_source'])=='paypal')
	{
		$total_paypal = $amount;
	}

	if(strtolower($row['payment_source'])=='payflow')
	{
		$total_cc = $amount;
	}

	$rowData = array();
	$rowData = array($row['prefix'].$row['order_id'],$row['email'],(float)getOrderTotal($row['order_id'],$row['shipping_cost']),(float)$total_voucher,$voucher_codes,round($total_cc,2),round($total_paypal,4),$transaction_ids);

	// print_r($rowData);exit;
	fputcsv($fp, $rowData,',');
}
fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($filename);
@unlink($filename);

// echo 1;
?>