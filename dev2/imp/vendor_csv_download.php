<?php

include_once 'auth.php';
include_once 'inc/functions.php';
include_once 'inc/split_page_results.php';

if(isset($_GET['page'])){
	$page = intval($_GET['page']);
}
if($page < 1){
	$page = 1;
}

$max_page_links = 10;
$num_rows = 50;
$start = ($page - 1)*$num_rows;
$account_id = (int)$_GET['account_id'];
if($_GET['number']){
	//$number = $db->func_escape_string($_GET['number']);	
//	$inv_query  = "select * from inv_shipments where package_number like '%$number%' and vendor='".$account_id."' and status<>'Issued' order by date_issued DESC";
}else{
	$inv_query  = "SELECT 
	id,
	date_issued,
	date_received,
	package_number,
	'' AS comments,
	0 as total,
	ex_rate,
	'' as xdescription,
	shipping_cost,
	0 as line_item
	FROM
	inv_shipments 
	WHERE vendor = '$account_id' 
	
	UNION
	ALL 
	SELECT 
	0 AS id,
	voucher_date AS date_issued,
	'' AS date_received,
	'' AS package_number,
	description as comments,
	credit as total,
	0 as ex_rate,
	comments as xdescription,
	0 as shipping_cost,
	line_item
	FROM
	inv_accounts 
	WHERE account_id = 'V$account_id' 
	ORDER BY date_issued ASC";
	
}

$shipments  = $db->func_query($inv_query);

foreach($shipments as $index => $shipment){
	$SQL = "select sum(qty_shipped * unit_price) as shipped_total ,  sum(qty_received * unit_price) as received_total 
	from inv_shipment_items where shipment_id = '".$shipment['id']."'";
	$shipments[$index]['extra'] = $db->func_query_first($SQL); 
	
	$SQL = "select sum(qty_rejected) as rejects ,  sum(qty_rejected * unit_price) as reject_total 
	from inv_rejected_shipment_items rsi inner join inv_shipment_items si on 
	(rsi.shipment_id = si.shipment_id and rsi.product_sku = si.product_sku)
	where rsi.shipment_id = '".$shipment['id']."'";
	$shipments[$index]['extra2'] = $db->func_query_first($SQL); 
}
$parameters = $_SERVER['QUERY_STRING'];
//print_r($shipments); exit;

$balance = 0.00;
foreach($shipments as $temp)
{

	$balance+=($temp['extra']['received_total'] / $temp['ex_rate'])+($temp['shipping_cost'] / $temp['ex_rate']);

}


$credit = $db->func_query_first_cell("SELECT SUM(credit) FROM inv_accounts WHERE account_id='V".$account_id."'");
$balance = $balance - $credit;

$shipped_total = 0; $received_total = 0; $shipping_cost = 0;
$balance = 0.00;
$csv = array();
$csv[] = array(
	'Invoice Date',
	'Description',
	'Credit',
	'Debit',

	'Balance',
	'Tracking',
	'Comments'
	);
foreach($shipments as $k => $shipment) {
	$key = ($k + 1);

	if($shipment['ex_rate'] == 0) {
		$shipment['ex_rate'] = 1;
	}

	$check_payment = $db->func_query_first("SELECT * FROM inv_account_shipments WHERE shipment_id='".$shipment['id']."'");
	$is_voucher = false;
	if($shipment['id']==0) {

		$is_voucher = true;
	}


	$csv[$key][] = americanDate(($is_voucher?$shipment['date_issued']:$shipment['date_received']));

	$csv[$key][] = $shipment['xdescription'];

	if($is_voucher==false) {
		$credit = ($shipment['extra']['received_total'] / $shipment['ex_rate']) + ($shipment['shipping_cost'] / $shipment['ex_rate']);
		$debit = 0.00;
	} else {
		if($shipment['line_item']==0) {
			$credit = 0.00;
			$debit = $shipment['total'];
		} else {
			if($shipment['total']<0) {
				$credit = 0.00;
				$debit = $shipment['total']*(-1);
			} else {
				$credit = $shipment['total'];
				$debit = 0.00;
			}
		}
	}
	$balance = $balance + ($credit - $debit);

	$csv[$key][] = number_format($credit,2);

	$csv[$key][] = number_format($debit,2);

	$csv[$key][] = number_format($balance,2);

	$csv[$key][] =  $shipment['package_number'];

	if($is_voucher==false) {
		$csv[$key][] = 'Received ' . date('m/d/Y',strtotime($shipment['date_issued']));
	} else {
		$csv[$key][] = $shipment['comments'];
	}

}
$fileName = 'Vendor'. $account_id .'-'.time();
$csvFile = fopen("files/" . $fileName . '.csv', "w");
foreach ($csv as $fields) {
    fputcsv($csvFile, $fields,',');
}
fclose($csvFile);
header("Location: files/".$fileName.'.csv');
?>