<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");
require_once("auth.php");
require_once("inc/functions.php");
include_once("amazon/amazon_config.php");
include_once 'amazon/AmazonAPI.php';

$order = $db->func_query_first("SELECT a.*,b.shipping_cost FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id AND a.order_id='".$_POST['order_id']."'");
switch ($order['prefix']) {
	case 'ZM':
		$amazon_id = 2;	
	break;
	case 'PG':
		$amazon_id = 5;	
	break;

	case 'PGCA':
		$amazon_id = 6;	
	break;

	case 'PGMX':
		$amazon_id = 7;	
	break;
	case 'MX':
		$amazon_id = 3;	
	break;
	case 'MX':
		$amazon_id = 3;	
	break;

	case 'CA':
		$amazon_id = 4;	
	break;

	case '':
		$amazon_id = 1;	
	break;
	
	
}


$merchantInfo = $db->func_query_first("Select id ,merchant_id,market_place_id, last_cron_date from amazon_credential where id='".(int)$amazon_id."'");
if(!@$merchantInfo){
    return;
}
 
$merchant_id = $merchantInfo['merchant_id'];
$market_place_id = $merchantInfo['market_place_id'];
$refundShipping = ($_POST['refundShipping'])? 1 : 0;


$items_array = array();
$postItems = rtrim($_POST['items'], ",");
$shipping_fee = $order['shipping_cost'] / count(explode(",",$postItems));

$query_where = ($postItems)? "`id` IN ('" . str_replace(',', "', '", $postItems) . "') AND": '';
$items = $db->func_query("SELECT * FROM inv_orders_items WHERE $query_where order_id='".$order['order_id']."'");
foreach($items as $item)
{
	
	$items_array[] = array('amazon_item_id' => $item['order_item_id'],'price'=>$item['product_price']);

}
$data = array();
$data = array('order_id' => $order['order_id'],'items'=>$items_array);

if($refundShipping)
{

	$data += array('shipping_fee'=>round($shipping_fee,2));	

}

$AmazonAPI = new AmazonAPI($market_place_id);
$xml = $AmazonAPI->RefundOrderXml($data , $merchant_id);
    //$request = $xml;
   //echo $xml;
$request1 =   $AmazonAPI->SendRequest($xml , $merchant_id,'_POST_PAYMENT_ADJUSTMENT_DATA_');

if (!$postItems) {
// Cancel Order
$AmazonAPI = new AmazonAPI($market_place_id);
$xml = $AmazonAPI->CancelOrderXml($data , $merchant_id);
    //$request = $xml;
   //echo $xml;exit;
$request2 =   $AmazonAPI->SendRequest($xml , $merchant_id,'_POST_PAYMENT_ADJUSTMENT_DATA_');

}
$json = array();

if($request1)
{
	$json['success'] = (($postItems)? 'Item(s) refunded successfully!': 'Order cancled and refunded successfully!');

	$addReport = array(
		'order_id'  =>  $order['order_id'],
		'reason_id' =>  $_POST['reason'],
		'order_amount'    =>  $_POST['amount'],
		'user_id'   =>  $_SESSION['user_id'],
		'date_added'=>  date('Y-m-d H:i:s')
		);
	$cancel_id = $db->func_array2insert("inv_order_cancel_report", $addReport);
	unset($addReport);

	foreach ($items as $xsk) {
		$addReport = array(
			'cancel_id'  =>  $cancel_id,
			'sku'       =>  $xsk['product_sku'],
			'amount'    =>  $xsk['product_price'],
			'action'   =>  (($postItems)? 'Item Removed': 'Order Canceled'),
			'date_added'=>  date('Y-m-d H:i:s')
			);
		$db->func_array2insert("inv_product_cancel_report", $addReport);
		unset($addReport);
	}

	$comment = 'Order #'. linkToOrder($_POST['order_id']) .' canceld and refunded.';
	actionLog($comment);
}
else
{
	$json['error'] = 'There is some error processing the transaction. please try again';	
}

echo json_encode($json);
?>