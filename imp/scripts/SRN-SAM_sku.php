<?php
include_once("../config.php");
include_once '../inc/functions.php';
$sku = $db->func_escape_string($_GET['sku']);
if(!$sku)
{
	$sku = 'SRN-SAM';
}
$orders = $db->func_query('select product_id,sale_price,sku,quantity FROM oc_product where status=1 and quantity>0 and left(sku,7)="'.$sku.'" ');
//testObject($orders);
$filename = 'SRN-SAM SKU.csv';
$fp = fopen($filename, "w");
$headers = array("SKU","Title","Qty","True Cost", "P1 Price","Sale Price");
fputcsv($fp, $headers);

foreach($orders as $row)
{
	$title = $db->func_query_first_cell("SELECT name from oc_product_description where product_id='".$row['product_id']."'");
	$true_cost = getTrueCost($row['sku']);
	$p1_price = $db->func_query_first_cell("SELECT price FROM oc_product_discount WHERE product_id='".$row['product_id']."' and customer_group_id=1633 and quantity=1");
	$rowData = array();
	$rowData = array($row['sku'],$title,$row['quantity'],$true_cost,$p1_price,$row['sale_price']);
	
	// print_r($rowData);exit;
	fputcsv($fp, $rowData);
}

fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($filename);
@unlink($filename);


?>