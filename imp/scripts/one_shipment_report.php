<?php
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 0); //300 seconds = 5 minutes
include_once '../config.php';
include_once '../inc/functions.php';

$date = $_GET['date'];
if(!$date)
{
	$date = date('Y-m-d');
}

$filename = $date.'_one_shipment_report.csv';
$fp = fopen($filename, "w");
$headers = array("Order Date","Order ID","Order Status","Store Type", "Email","Shipping Method", "Service Code","Tracking #","Weight","Order Total","Shipping Cost","Shipping Paid","TXN Fee","Last Tracking Status","Voided","Transaction ID"
	);
fputcsv($fp, $headers,',');

$rows = $db->func_query("SELECT DISTINCT a.transaction_id,a.transaction_fee, a.store_type,a.order_date, a.prefix,a.order_id, a.email,b.shipping_method,b.shipping_cost,a.order_status,a.order_price
	FROM  inv_orders a,inv_orders_details b
	WHERE a.order_id=b.order_id and DATE( a.order_date ) =  '$date'
	AND LOWER( a.email ) NOT 
	IN (
		'fba@amazon.com',  'ecafferty@encompass.com'
	)
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

	$transactions = '';
	$transaction_dets = $db->func_query("SELECT transaction_id,amount FROM inv_transactions WHERE order_id LIKE '%".$row['order_id']."'");
	if(!$transaction_dets)
	{
		$transaction_dets = $db->func_query("SELECT transaction_id,amount FROM oc_paypal_admin_tools WHERE cast(order_id as char(50))='".$row['order_id']."'");
	}

	else if(!$transaction_dets)
	{
		$transaction_dets = $db->func_query("SELECT pp_transaction_id,transaction_id,amount FROM oc_payflow_admin_tools WHERE cast(order_id as char(50))='".$row['order_id']."'");
	}
	if($transaction_dets)
	{
		
		foreach($transaction_dets as $transaction_det)
		{
			if($transaction_det['pp_transaction_id'])
			{
				$transaction_det['transaction_id'] = $transaction_det['pp_transaction_id'];
			}
			$transactions .= $transaction_det['transaction_id'].',';
		}
	}
	if(!$transactions) {
		$transactions = $order['transaction_id'];
	}

	if($row['store_type']=='web')
	{
		$_voucher_query = 'cast(a.order_id as char(50)) = "'. $row['order_id'] .'"';
	}
	else
	{
		$_voucher_query = 'cast(a.inv_order_id as char(50)) = "'. $row['order_id'] .'"';
	}

	$vouchers = $db->func_query('SELECT *, `a`.`amount` as `used`, `b`.`amount` as `remain` FROM `oc_voucher_history` as a, `oc_voucher` as b WHERE a.`voucher_id` = b.`voucher_id` AND '.$_voucher_query.' ');
	foreach ($vouchers as $voucher){
		if ($voucher['code']) {
			$transactions .= $voucher['code'].',';
		}

	}
	$rowData = array();
	$rowData = array($row['order_date'],$row['prefix'].$row['order_id'],$row['order_status'],$row['store_type'],$row['email'],$row['shipping_method'],$service_code,' '.$shipstation_query['tracking_number'],$weight,round($order_price,2),round($shipping_cost,4),round($shipping_paid,2),round($_txn_fee,2),$last_tracking_status,(int)$is_voided,$transactions);
	
	// print_r($rowData);exit;
	fputcsv($fp, $rowData,',');
}
}
fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($filename);
@unlink($filename);

// echo 1;
?>