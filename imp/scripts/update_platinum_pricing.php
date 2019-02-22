<?php
ini_set('max_execution_time', 600); //300 seconds = 5 minutes
include_once '../config.php';
include_once '../inc/functions.php';
$rows = $db->func_query("SELECT * 
FROM  `oc_product_discount` 
WHERE customer_group_id =  '1633'
AND pricing_update =0  order by product_discount_id LIMIT 1000");
foreach($rows as $row)
{
	$sku = $db->func_query_first_cell("SELECT model FROM oc_product where product_id='".$row['product_id']."'");
	$price = $row['price'];
	$price = round($price,2);
	$price = sprintf('%0.2f', $price);
	echo $sku.'--- Old Price: '.$price;
	$new_price = substr($price, 0, -1); // 2.11 -> 2.1, 1.48 -> 1.4, 1002.51 -> 1002.5
	$new_price = (string)$new_price.'9';
	// echo $new_price."<br>";
	$new_price = (float)$new_price;
	$new_price = round($new_price,4);
	echo '---- New Price: '.$new_price."<br>";
	$db->db_exec("UPDATE oc_product_discount SET price='".$new_price."',pricing_update=1 WHERE product_discount_id='".$row['product_discount_id']."' ");
}
echo 'success';
?>