<?php
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 0); //300 seconds = 5 minutes
include_once '../config.php';
include_once '../inc/functions.php';

$year = date('Y');
if(isset($_GET['year']))
{
	$year=(int)$_GET['year'];
}
if(!isset($_GET['email']))
{
	echo 'Please provide a email address to generate report';exit;
}
else
{
	$email = $db->func_escape_string($_GET['email']);
}


$filename = $year.'_customer_order_report.csv';
$fp = fopen($filename, "w");
$headers = array("Order Date","Order ID","Order Status","Store Type", "Email","Shipping Address","City","State","Zip","Shipping Method", "Service Code","Tracking #","Weight","Order Total"
	);
fputcsv($fp, $headers);

$rows = $db->func_query("SELECT DISTINCT a.transaction_fee, a.store_type,a.order_date, a.prefix,a.order_id, a.email,b.shipping_method,b.shipping_cost,a.order_status,a.order_price,b.address1,b.city,b.state,b.zip
	FROM  inv_orders a,inv_orders_details b
	WHERE a.order_id=b.order_id and YEAR( a.order_date ) =  '$year'
	AND LOWER( a.email ) NOT 
	IN (
		'fba@amazon.com',  'ecafferty@encompass.com'
	)
	and LOWER(a.email)='".strtolower($email)."'
	order by a.order_date asc");
foreach($rows as $row)
{

	$order_fees = $db->func_query_first_cell("select sum(fee) from inv_order_fees where order_id = '".$row['order_id']."'");
	$txn_fee = 0.00;
	if($order_fees)
	{
		$txn_fee = $order_fees * (-1);

	}
	else
	{
		if($row['store_type']=='ebay')
	{
		$txn_fee  = $row['transaction_fee'];
	}
	else
	{
		$txn_fee = $db->func_query_first_cell("SELECT transaction_fee FROM inv_transactions WHERE order_id='".$row['order_id']."' ");
	}
	}


	
	
	$shipstation_querys = $db->func_query("select * from inv_shipstation_transactions where cast(`order_id` as char(50)) = '".$row['order_id']."' ORDER BY voided DESC");
	
	foreach($shipstation_querys as $shipstation_query)
	{
	$service_code = stripDashes($shipstation_query['service_code']);
	$tracking_no = (string)$shipstation_query['tracking_number'];
	$weight = $shipstation_query['weight']. " ". $shipstation_query['units'];
	$shipping_paid = $shipstation_query['shipping_cost'] + $shipstation_query['insurance_cost'];
	$is_voided = $shipstation_query['voided'];

	$tracker = $db->func_query_first("SELECT * FROM inv_tracker WHERE tracking_code='".$shipstation_query['tracking_number']."'");
	$last_tracking_status = '';

	$item_txn_fee = ((float)$row['product_unit']/(float)$row['order_price']) * $txn_fee;
	if($tracker)
	{
		$last_tracking_status = $db->func_query_first_cell("SELECT `status` FROM inv_tracker_status WHERE tracker_id='".$tracker['tracker_id']."' order by id desc limit 1");
	}
	if($last_tracking_status=='cancelled')
	{
		$order_price = 0.00;
		$shipping_paid = 0.00;
		$shipping_cost = 0.00;
	}
	else
	{
		$order_price = $row['order_price'];
		$shipping_cost = $row['shipping_cost'];
	}
	$_txn_fee = ($shipping_cost/$order_price ) * $txn_fee;

	$rowData = array();
	$rowData = array($row['order_date'],$row['prefix'].$row['order_id'],$row['order_status'],$row['store_type'],$row['email'],$row['address1'],$row['city'],$row['state'],$row['zip'],$row['shipping_method'],$service_code,' '.$shipstation_query['tracking_number'],$weight,round($order_price,2));
	
	// print_r($rowData);exit;
	fputcsv($fp, $rowData);
}
}
fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($filename);
@unlink($filename);

// echo 1;
?>