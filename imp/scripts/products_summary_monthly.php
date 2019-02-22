<?php
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 0); //300 seconds = 5 minutes
include_once '../config.php';
include_once '../inc/functions.php';

function getMonthsInRange($startDate, $endDate) {
$months = array();
// echo $endDate;exit;
while (strtotime($startDate) <= strtotime($endDate)) {
    $months[] = array('year' => date('Y', strtotime($startDate)), 'month' => date('m', strtotime($startDate)), );
    $startDate = date('d M Y', strtotime($startDate.
        '+ 1 month'));
}

return $months;
}

//$month = date('m');
$year = date('Y');


if(isset($_GET['date']))
{
	$dates = explode(",",$_GET['date']);
	$first_date = '01-'.$dates[0];
	$second_date = '30-'.$dates[1];
	// echo 
	// echo $first_date;exit;
	$first = date('Y-m-01',strtotime($first_date));
	$second = date('Y-m-30',strtotime($second_date));

	// echo $second;exit;
	//$first = explode("-", $first_date);
	//$second = explode("-", $second_date);

}
else
{
	echo 'Please provide proper date range';exit;
}
$months = (getMonthsInRange($first_date,$second_date));
$total = 1000;
$page = 1;
if(isset($_GET['page']))
{
	$page = (int)$_GET['page'];
}
$limit_start = ($page-1)*$total;

$filename = 'products-summary-monthly.csv';
$fp = fopen($filename, "w");
$headers = array("SKU","Title");
foreach($months as $month)
{
	$headers[] = ($month['month']).'-'.$month['year'].' Sale Qty';
	$headers[] = ($month['month']).'-'.$month['year'].' Sale Amount';
}
foreach($months as $month)
{
	$headers[] = ($month['month']).'-'.$month['year'].' Return Qty';
	$headers[] = ($month['month']).'-'.$month['year'].' Return Amount';
}


// $headers[] = 'LBB Amount';
// ,"# of Return Items","Return Amount","LBB Amount"
fputcsv($fp, $headers);

$rows = $db->func_query("select distinct a.product_sku,(SELECT c.name from oc_product_description c,oc_product b where a.product_sku=b.sku and b.product_id=c.product_id) as name FROM inv_orders_items a where date(a.dateofmodification) between '".$first."' and '".$second."' order by a.product_sku ASC LIMIT 50");
foreach($rows as $row)
{
	$sku = $row['product_sku'];
	$title = $row['name'];

	$rowData = array();
	$rowData = array($sku,$title);
	
	foreach($months as $month)
	{
		$order_data =  $db->func_query_first("SELECT sum(b.product_qty) as product_qty,sum(b.product_price) as product_price FROM inv_orders a RIGHT JOIN inv_orders_items b   ON(a.order_id=b.order_id) where lower(b.product_sku)='".strtolower($sku)."' and month(a.order_date)='".$month['month']."' and year(a.order_date)='".$month['year']."'   and lower(a.order_status) in ('processed','shipped') and a.store_type not like '%amazon%' ");
		$rowData[] = (int)$order_data['product_qty'];
		$rowData[] = (int)$order_data['product_price'];
	}

	foreach($months as $month)
	{
		$return_data =  $db->func_query_first("SELECT sum(b.quantity) as return_qty,sum(b.price) as return_price FROM inv_returns a,inv_return_items b  where a.id=b.return_id and lower(b.sku)='".strtolower($sku)."' and month(a.date_added)='".$month['month']."' and year(a.date_added)='".$month['year']."'   and lower(a.rma_status) in ('completed','in qc','qc completed') and lower(b.item_condition) in ('item issue - rtv','item issue','not tested','over 60 days') ");
		// $return_data = array();
		$rowData[] = (int)$return_data['return_qty'];
		$rowData[] = (int)$order_data['return_price'];
	}

	
	// print_r($rowData);exit;
	fputcsv($fp, $rowData);

}
fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($filename);
@unlink($filename);

// echo 1;
?>