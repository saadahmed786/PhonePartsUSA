<?php
include 'auth.php';
include 'config.php';
include 'inc/functions.php';
$order_id = $_GET['order_id'];
$filename = 'csv_order-' . $order_id . '.csv';

$fp = fopen($filename, "w");
fputcsv($fp, array('SKU', 'Product Name', 'Quantity' , 'Price' , 'Sub Total'),',');
$product = $db->func_query("SELECT a.product_sku,c.name,a.product_qty,a.product_unit, product_price as SubTotal FROM inv_orders_items a inner join oc_product b on (a.product_sku = b.sku) inner join oc_product_description c on (b.product_id = c.product_id) where a.order_id = '$order_id'");
foreach ($product as $row) {
     fputcsv($fp, $row,',');
}
fclose($fp);



header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
readfile($filename);
@unlink($filename);



?>