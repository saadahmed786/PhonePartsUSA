<?php
ini_set("memory_limit",-1);
ini_set('max_execution_time', 600); //300 seconds = 5 minutes
include_once '../config.php';
include_once '../inc/functions.php';
$rows = $db->func_query("SELECT product_id 
FROM  `oc_product_discount` 
WHERE customer_group_id =  '1633'
AND pricing_update =1  group by product_id ");
$fp = fopen('export_zaman.csv', "w");
$headers = array("SKU", "ItemName", "Qty 1","Qty 3","Qty 10");
fputcsv($fp, $headers);
foreach($rows as $row)
{
	 $model = $db->func_query_first_cell("SELECT model FROM oc_product where product_id='".$row['product_id']."'");
	 $item_name = $db->func_query_first_cell("SELECT name FROM oc_product_description where product_id='".$row['product_id']."'");

	$details = $db->func_query("SELECT price 
FROM  `oc_product_discount` 
WHERE customer_group_id =  '1633'
AND pricing_update =1 and product_id='".$row['product_id']."'  order by product_discount_id ");
	$i=0;
	$price_array = array();
	foreach($details as $detail)
	{
		$price_array[] = $detail['price'];
	}
	$rowData = array();
	 $rowData = array($model, $item_name, $price_array[0],$price_array[1],$price_array[2]);
	 fputcsv($fp, $rowData);
	// $price = $row['price'];
	// $price = round($price,2);
	// $price = sprintf('%0.2f', $price);
	// echo $sku.'--- Old Price: '.$price;
	// $new_price = substr($price, 0, -1); // 2.11 -> 2.1, 1.48 -> 1.4, 1002.51 -> 1002.5
	// $new_price = (string)$new_price.'9';
	// // echo $new_price."<br>";
	// $new_price = (float)$new_price;
	// $new_price = round($new_price,4);
	// echo '---- New Price: '.$new_price."<br>";
	// $db->db_exec("UPDATE oc_product_discount SET price='".$new_price."',pricing_update=1 WHERE product_discount_id='".$row['product_discount_id']."' ");
}
fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="export_zaman.csv"');
readfile($filename);
@unlink($filename);

?>