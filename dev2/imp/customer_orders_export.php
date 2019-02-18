<?php
include 'config.php';
include 'inc/functions.php';

$email = $db->func_escape_string(base64_decode($_GET['email']));

$products = $db->func_query("SELECT b.product_sku,SUM(b.product_qty) as product_qty,a.order_date from inv_orders_items b,inv_orders a where a.order_id=b.order_id and a.email='".$email."' group by b.product_sku order by a.order_date desc");

            

$filename = "Orders Details-".$email."-" . date("Y-m-d") . ".csv";
$fp = fopen($filename, "w");


   $headers = array("SKU", "Item Name", "Qty Ordered", "Last Ordered");


fputcsv($fp, $headers,',');

foreach($products as $sku) { 
	$last_ordered = $db->func_query_first_cell('SELECT o.order_date from inv_orders o inner join inv_orders_items oi on (o.order_id = oi.order_id) where o.email = "'.$email.'" AND oi.product_sku = "'.$sku['product_sku'].'" order by o.order_date desc');
        
    $last_ordered = date("m/d/Y", strtotime($last_ordered));
        $rowData = array($sku['product_sku'] , getItemName($sku['product_sku']),$sku['product_qty'],$last_ordered);
                fputcsv($fp, $rowData,',');
            }



fclose($fp);

    header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
readfile($filename);
@unlink($filename);


