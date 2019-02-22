<?php
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
include_once '../config.php';
include_once '../inc/functions.php';

// $rows = $db->func_query("SELECT DISTINCT country FROM inv_orders_details where year(dateofmodification)='2017' and country_id=0");
// foreach($rows as $row)
// {
// 	$country_id = $db->func_query_first_cell("SELECT country_id FROM oc_country WHERE LOWER(iso_code_2)='".strtolower(trim($row['country']))."' or lower(name)='".strtolower(trim($row['country']))."'");
// 	if($country_id)
// 	{
// 		$db->db_exec("update inv_orders_details set country_id='".(int)$country_id."' where trim(lower(country))='".strtolower(trim($row['country']))."'");

// 		$db->db_exec("update inv_orders_details set bill_country_id='".(int)$country_id."' where trim(lower(bill_country))='".strtolower(trim($row['country']))."'");


// 	}
// }


// $rows = $db->func_query("SELECT distinct  state FROM inv_customers where zone_id='0' and state<>''");
// foreach($rows as $row)
// {
// 	$zone_id = $db->func_query_first_cell("SELECT zone_id FROM oc_zone WHERE  ( LOWER(code)='".strtolower(trim($row['state']))."' or lower(name)='".strtolower(trim($row['state']))."') and country_id=223 ");
// 	if($zone_id)
// 	{
// 		$db->db_exec("update inv_customers set zone_id='".(int)$zone_id."' where trim(lower(state))='".strtolower(trim($row['state']))."' ");

	


// 	}
// }


$rows = $db->func_query("SELECT distinct state FROM inv_orders_details where zone_id=0 and state<>'' and country_id=38 ");
foreach($rows as $row)
{
	$zone_id = $db->func_query_first_cell("SELECT zone_id FROM oc_zone WHERE  ( LOWER(code)='".strtolower(trim($row['state']))."' or lower(name)='".strtolower(trim($row['state']))."') and country_id=38 ");
	if($zone_id)
	{
		$db->db_exec("update inv_orders_details set zone_id='".(int)$zone_id."' where trim(lower(state))='".strtolower(trim($row['state']))."' and country_id=38 ");

	


	}
}


echo 'success';
?>