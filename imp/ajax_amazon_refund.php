<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");
require_once("auth.php");
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
$refundShipping = $_POST['refundShipping'];

	
	$items_array = array();
	$shipping_fee = $order['shipping_cost'] / count(explode(",",$_POST['items']));
	foreach(explode(",",$_POST['items']) as $_item)
	{
		
	$return_detail  = $db->func_query_first("SELECT sku,price FROM inv_return_items WHERE return_id='".$_POST['return_id']."' and id='".$_item."'");
	$item = $db->func_query_first("SELECT order_item_id FROM inv_orders_items WHERE order_id='".$order['order_id']."' AND product_sku='".$return_detail['sku']."'");
	
	
	
		$items_array[] = array('amazon_item_id' => $item['order_item_id'],'price'=>$return_detail['price']);
		
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
   // echo $xml;exit;
  $request =   $AmazonAPI->SendRequest($xml , $merchant_id,'_POST_PAYMENT_ADJUSTMENT_DATA_');
  
  $json = array();
if($request)
{
	$json['success'] = 'Item(s) Refunded Successfully!';	
}
else
{
	$json['error'] = 'There is some error processing the transaction. please try again';	
}

echo json_encode($json);
?>