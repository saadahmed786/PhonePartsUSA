<?php
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
require_once("../auth.php");
require_once("../inc/functions.php");
// Update the Cost Field of All the Boxes Items

$rows = $db->func_query("SELECT id,product_sku FROM inv_return_shipment_box_items");
foreach($rows as $row)
{
	$true_cost = getTrueCost($row['product_sku']);
	if($true_cost)
	{
		$db->db_exec("UPDATE inv_return_shipment_box_items SET cost='".(float)$true_cost."' WHERE id='".(int)$row['id']."'");	
		
	}
	
}
?>