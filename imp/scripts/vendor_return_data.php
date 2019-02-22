<?php
require_once("../config.php");
require_once("../inc/functions.php");

if(!isset($_GET['month']))
{
	$month = date('m');
}
else
{
	$month = $_GET['month'];
}

if(!isset($_GET['year']))
{
	$year = date('Y');
}
else
{
	$year = $_GET['year'];
}
if(!isset($_GET['vendor']))
{
	echo 'Please provide a &vendor parameter in url';
}
else
{
	$vendor_id = $db->func_query_first_cell("SELECT id from inv_users where lower(name)='".strtolower($_GET['vendor'])."'");
}
if(!$vendor_id)
{
	echo 'Invalid vendor, cannot find vendor into the system';exit;
}


$filename = 'vendor_return_data_'.strtolower($_GET['vendor']).'_'.$month.$year.'.csv';
$fp = fopen($filename, "w");
$headers = array("Date","Vendor","Items Received","Total Sold","Sold Price","Return Date","Email","RMA","Source","SKU","Product Name","Item Condition","Return Reason","Issue","Cost");
fputcsv($fp, $headers,',');


$rows = $db->func_query("select a.date_qc as date_received,a.rma_number,a.email,b.rtv_vendor_id as vendor_id,b.sku,b.title,b.price,b.item_condition,b.source,b.item_issue,b.return_code from inv_return_items b,inv_returns a where a.id=b.return_id and b.rtv_vendor_id=$vendor_id and lower(a.rma_status) in ('in qc','qc completed','completed') and b.sku<>'' and month(a.date_qc)='$month' and year(a.date_qc)='$year'");

$check_email = array();
$i=0;
$skus = array();
foreach($rows as $row)
{
	$total_sold_count = 0;
	$total_sold_price = 0.00;
	if(!in_array($row['sku'], $skus))
	{
		$skus[] = $row['sku'];

		$total = $db->func_query_first("SELECT SUM(b.product_qty) as product_qty,SUM(b.product_price) as product_price from inv_orders_items b,inv_orders a where a.order_id=b.order_id and lower(a.order_status) in ('processed',  'shipped',  'completed',  'unshipped') and month(a.order_date)='$month' and year(a.order_date)='$year' and trim(lower(b.product_sku))='".trim(strtolower($row['sku']))."' and a.store_type in ('web','po_business')");

		// $total_sold_count = $db->func_query_first_cell("SELECT SUM(b.product_qty) from inv_orders_items b,inv_orders a where a.order_id=b.order_id and lower(a.order_status) in ('processed',  'shipped',  'completed',  'unshipped') and month(a.order_date)='$month' and year(a.order_date)='$year' and trim(lower(b.product_sku))='".trim(strtolower($row['sku']))."' and a.store_type in ('web','po_business')");

		// $total_sold_price = $db->func_query_first_cell("SELECT SUM(b.product_price) from inv_orders_items b,inv_orders a where a.order_id=b.order_id and lower(a.order_status) in ('processed',  'shipped',  'completed',  'unshipped') and month(a.order_date)='$month' and year(a.order_date)='$year' and trim(lower(b.product_sku))='".trim(strtolower($row['sku']))."' and a.store_type in ('web','po_business')");
		$total_sold_count = $total['product_qty'];
		$total_sold_price = $total['product_price'];
	}
	$vendor='';
	$items_received ='';
	if($i==0)
	{
		$vendor = get_username($row['vendor_id']);
		$items_received = (int)$db->func_query_first_cell("select sum(b.qty_received) from inv_shipments a,inv_shipment_items b where a.id=b.shipment_id and month(a.date_received)='$month' and year(a.date_received)='$year' and a.vendor='".(int)$row['vendor_id']."'");
	}
	
	$rowData = array();
	$rowData = array($month.'-'.$year,$vendor,$items_received,(int)$total_sold_count,(float)$total_sold_price,americanDate($row['date_received']),$row['email'],$row['rma_number'],$row['source'],$row['sku'],getItemName($row['sku']),$row['item_condition'],$row['return_code'],$row['item_issue'],(float)$row['price']);
	fputcsv($fp, $rowData,',');		
	
	$i++;

}

fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($filename);
@unlink($filename);


?>