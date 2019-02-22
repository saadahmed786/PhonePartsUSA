<?php
require_once("../config.php");
require_once("../inc/functions.php");
require_once("class.php");

$_settings = oc_config('imp_inventory_setting');
$setting = unserialize($_settings);
$method = (isset($_POST['method'])?$_POST['method']:'unknown');
// var_dump($_POST);exit;
// echo $method;exit;
if($method=='shipped')
{
	$order_id = $_POST['order_id'];
	$check = $db->func_query_first_cell("SELECT order_id FROM inv_product_ledger where order_id='".$db->func_escape_string($order_id)."' and action='shipped' limit 1");
	if(!$check)
	{
	
	$inventory->markShipped($order_id);		
	$inventory->updateInventoryShipped($order_id,'shipped');
	}

	echo 'success';	
}
exit;

?>