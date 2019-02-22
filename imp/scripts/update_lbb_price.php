<?php
ini_set("memory_limit",-1);
ini_set('max_execution_time', 600); //300 seconds = 5 minutes
include_once '../config.php';
include_once '../inc/functions.php';
$rows = $db->func_query("SELECT buyback_id,shipment_number from oc_buyback where `status`<>'Completed'");
foreach($rows as $row)
{
	$subs = $db->func_query("SELECT * from oc_buyback_products where buyback_id='".$row['buyback_id']."'  ");

	foreach($subs as $sub)
	{
		$get_latest = $db->func_query_first("SELECT * FROM inv_buy_back WHERE sku='".$sub['sku']."'");
		if($get_latest)
		{
			$query = $db->db_exec("UPDATE oc_buyback_products SET 
				oem_a_price='".(float)$get_latest['oem_a']."',
				oem_b_price='".(float)$get_latest['oem_b']."',
				oem_c_price='".(float)$get_latest['oem_c']."',
				oem_d_price='".(float)$get_latest['oem_d']."',

				non_oem_a_price='".(float)$get_latest['non_oem_a']."',
				non_oem_b_price='".(float)$get_latest['non_oem_b']."',
				non_oem_c_price='".(float)$get_latest['non_oem_c']."',
				non_oem_d_price='".(float)$get_latest['non_oem_d']."',
				
				salvage_price = '".(float)$get_latest['salvage_price']."'

				where buyback_product_id='".(float)$sub['buyback_product_id']."'
				");
			// echo $query;exit;

		}
	}
	echo $row['shipment_number']."<br>";
}
	echo 'ho gya';exit;
?>