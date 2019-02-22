<?php
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
include_once '../config.php';
function getCost($sku)
{
	global $db;
	$cost = 0.00;
	$main_sku = $db->func_query_first_cell("SELECT main_sku FROM oc_product WHERE model='".$sku."'");
	if($main_sku)
	{
		$sku =$main_sku;
	}
	$cost = $db->func_query_first("SELECT  cost_date,raw_cost,ex_rate,shipping_fee FROM inv_product_costs WHERE sku='" . $sku . "' ORDER BY id DESC limit 1");
	
	if($cost['raw_cost']>0 and $cost['ex_rate']>0)
	{
		
		$cost = ($cost['raw_cost']) / $cost['ex_rate'];
		$cost = round($cost, 2);
	}
	return $cost;
}
$_query = "SELECT sku 
FROM inv_product_costs
WHERE sku NOT 
IN (

SELECT sku
FROM inv_avg_cost
)
GROUP BY sku";
$products = $db->func_query($_query);
foreach($products as $product)
{
	$sku=$product['sku'];
	$cost = getCost($sku);
	//$true_cost = getTrueCost($sku);
	$db->db_exec("INSERT INTO inv_avg_cost SET sku='$sku',cost='".(float)$cost."',date_added=NOW()");

	echo $sku." updated <br>";

}
echo 'success';
?>