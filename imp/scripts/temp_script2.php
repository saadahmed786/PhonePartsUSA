<?php
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 0); //300 seconds = 5 minutes
include_once '../config.php';
include_once '../inc/functions.php';
$_sku = $_GET['sku'];
$len = strlen(($_sku));
if(!$_sku)
{
	echo 'please provide the sku';exit;
}
$month = $_GET['month'];
$year = $_GET['year'];
if(!$year)
{
	$year = '2016';
}
if(!$month)
{
	$month = '12';
}
$filename = $_sku.'.csv';
$fp = fopen($filename, "w");
$headers = array("Order ID", "Email","Item Name", "SKU","Qty","True Cost","Total Sold Price"
	);
fputcsv($fp, $headers);

$rows = $db->func_query("SELECT DISTINCT a.order_id, a.email, b.product_sku,b.product_true_cost,  sum(b.product_qty) as product_qty ,sum(b.product_unit) as product_unit
FROM inv_orders_items b, inv_orders a
WHERE b.order_id = a.order_id
AND LOWER( a.order_status ) 
IN (
 'processed',  'shipped',  'completed',  'issued',  'unshipped'
)
AND LEFT( b.product_sku, ".$len." ) =  '".$_sku."'
AND MONTH( a.order_date ) =  '$month'
AND YEAR( a.order_date ) =  '$year'
AND LOWER( a.email ) NOT 
IN (
 'fba@amazon.com',  'ecafferty@encompass.com'
)
GROUP BY a.order_id,b.product_sku");
foreach($rows as $row)
{
	

	$rowData = array();
	$rowData = array($row['order_id'],$row['email'],getItemName($row['product_sku']),$row['product_sku'],(int)$row['product_qty'],round($row['product_true_cost'],2),round($row['product_unit'],4));
		
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