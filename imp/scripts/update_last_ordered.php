<?php
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
include_once '../config.php';
include_once '../inc/functions.php';
$items = $db->func_query('SELECT distinct
sku
FROM
oc_product
where
sku != ""
AND sku NOT IN (SELECT 
	kit_sku
	FROM
	inv_kit_skus) AND last_ordered is null order by sku LIMIT 500');
	foreach($items as $item)
	{
		$last_ordered = $db->func_query_first("SELECT order_id,dateofmodification FROM inv_orders_items WHERE product_sku='".$item['sku']."' ORDER BY id DESC LIMIT 1");	
		
		
		
		if(!$last_ordered)
		{
			$dom = '2001-01-01 00:00:00';
		}
		else
		{
			if($last_ordered['dateofmodification']=='0000-00-00 00:00:00')
		{
			$dom = $db->func_query_first_cell("SELECT order_date FROM inv_orders WHERE order_id='".$last_ordered['order_id']."'");
		}
		else
		{
			$dom = $last_ordered['dateofmodification'];	
		}
		
		
			
		}
		$db->db_exec("UPDATE oc_product SET last_ordered='".$dom."' WHERE sku='".$item['sku']."'");
			echo $item['sku']." - ".$dom."<Br>";

	}
?>