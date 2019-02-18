<?php
require_once("config.php");
require_once("inc/functions.php");
$rma_number = getRMANumber('web');

$order_id=$_POST['order_id'];
$products = rtrim($_POST['products'],',');
$products = explode(",",$products);

$array = array();

$array['date_added'] = date('Y-m-d H:i:s');
$array['rma_number'] = $rma_number;
$array['order_id'] = (int)$order_id;
$array['store_type'] = 'web';
$array['email'] = urldecode($_POST['email']);
$array['rma_status'] = 'Awaiting';

$return_id = $db->func_array2insert("inv_returns",$array);

foreach($products as $product)
{
	$product_det = $db->func_query_first("SELECT
	a.price,
	b.name
FROM
    `oc_order_product` a
    INNER JOIN `oc_product_description` b
        ON (a.`product_id` = b.`product_id`)
		where a.order_id=".(int)$order_id." AND a.model='".$product."'
		
		
		");
$array = array();
$array['sku'] = $product;
$array['title'] = $product_det['name'];
$array['quantity'] = 1;
$array['price'] = $product_det['price'];
$array['return_code'] = urldecode($_POST['return_code']);
$array['return_id'] = $return_id;
$array['how_to_process'] = urldecode($_POST['how_to_process']);


$return_id = $db->func_array2insert("inv_return_items",$array);
}
?>

