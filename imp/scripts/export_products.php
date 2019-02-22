<?php
ini_set("memory_limit",-1);
ini_set('max_execution_time', 600); //300 seconds = 5 minutes
include_once '../config.php';
include_once '../inc/functions.php';
$rows = $db->func_query("SELECT a.product_id, a.sku,a.ignore_up,a.price 
FROM  `oc_product` a
WHERE status=1 limit 8519,2000 ");
$fp = fopen('export_products6.csv', "w");
$headers = array("SKU", "ItemName","Raw Cost", "True Cost","Default","D1","D3","D10","L1","L3","L10"
	,"B1","B3","B10"
	,"S1","S3","S10"
	,"G1","G3","G10"
	,"P1","P3","P10"
	,"Dm1","Dm3","Dm10","is ignored?"
	);
fputcsv($fp, $headers,',');
foreach($rows as $row)
{

	$_query = "Select pc.user_id , u.name , pc.current_cost, pc.raw_cost , pc.ex_rate, pc.cost_date , 

	pc.shipping_fee, pc.vendor_code from 

	oc_product p left join inv_product_costs pc on (p.sku = pc.sku) 

	left join inv_users u on (u.id = pc.user_id) where p.sku = '".$row['sku']."' order by pc.id DESC limit 1";

	$raw_cost = $db->func_query_first($_query);

	$item_name = $db->func_query_first_cell("SELECT name FROM oc_product_description where product_id='".$row['product_id']."'");
	 // $item_name = 'ok';
	$default_price = $db->func_query("SELECT price,quantity FROM oc_product_discount WHERE customer_group_id=8 and product_id='".$row['product_id']."' "); // default
	$default = array();
	foreach($default_price as $price)
	{
		$default[] = $price['price'];
	}

	$default_price = $db->func_query("SELECT price,quantity FROM oc_product_discount WHERE customer_group_id=10 and product_id='".$row['product_id']."'  "); //local
	$local = array();
	foreach($default_price as $price)
	{
		$local[] = $price['price'];
	}

	$default_price = $db->func_query("SELECT price,quantity FROM oc_product_discount WHERE customer_group_id=6 and product_id='".$row['product_id']."' "); //bronze
	$bronze = array();
	foreach($bronze_price as $price)
	{
		$bronze[] = $price['price'];
	}
	 
	$default_price = $db->func_query("SELECT price,quantity FROM oc_product_discount WHERE customer_group_id=1631 and product_id='".$row['product_id']."'"); // silver
	$silver = array();
	foreach($default_price as $price)
	{
		$silver[] = $price['price'];
	}

	$default_price = $db->func_query("SELECT price,quantity FROM oc_product_discount WHERE customer_group_id=1632 and product_id='".$row['product_id']."'");
	$gold = array();
	foreach($default_price as $price)
	{
		$gold[] = $price['price'];
	}

	$default_price = $db->func_query("SELECT price,quantity FROM oc_product_discount WHERE customer_group_id=1633 and product_id='".$row['product_id']."'");
	$platinum = array();
	foreach($default_price as $price)
	{
		$platinum[] = $price['price'];
	}

	$default_price = $db->func_query("SELECT price,quantity FROM oc_product_discount WHERE customer_group_id=1634 and product_id='".$row['product_id']."'");
	$diamond = array();
	foreach($default_price as $price)
	{
		$diamond[] = $price['price'];
	}

	$rowData = array();
	$rowData = array($row['sku'],$db->func_escape_string($item_name),(float)$raw_cost['raw_cost'],getTrueCost($row['sku']),$row['price']
		,(float) $default[0],(float)$default[1],(float)$default[3]
		,(float) $local[0],(float)$local[1],(float)$local[3]
		,(float) $bronze[0],(float)$bronze[1],(float)$bronze[3]
		,(float) $silver[0],(float)$silver[1],(float)$silver[3]
		,(float) $gold[0],(float)$gold[1],(float)$gold[3]
		,(float) $platinum[0],(float)$platinum[1],(float)$platinum[3]
		,(float) $diamond[0],(float)$diamond[1],(float)$diamond[3]
		, $row['ignore_up']
		);
	// print_r($rowData);exit;
	 fputcsv($fp, $rowData,',');
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

// header('Content-type: application/csv');
// header('Content-Disposition: attachment; filename="export_zaman.csv"');
readfile($filename);
@unlink($filename);

?>