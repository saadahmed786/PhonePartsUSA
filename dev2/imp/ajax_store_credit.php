<?php
require_once("auth.php");
$order_id = $_POST['order_id'];
$items = rtrim($_POST['items'],",");
$items = explode(",",$items);

$code = $order_id.'R';
$order_info = $db->func_query_first("SELECT a.*,b.first_name FROM inv_orders a,inv_orders_details b WHERE a.order_id=b.order_id and  a.order_id='".$order_id."'");

$data = array();
			$data['code'] = $code;
			$data['voucher_theme_id'] = 8;
			$data['message'] = '';
			$data['amount'] = 0.00;
			$data['status'] = 1;
			
			$data['order_id'] = $order_id;
			$data['date_added'] = date('Y-m-d h:i:s');
			$data['from_name'] = 'PhonePartsUSA.com';
			$data['from_email'] = 'sales@phonepartsusa.com';
			$data['to_name'] = $db->func_escape_string($order_info['first_name']);
			$data['to_email'] = $db->func_escape_string($order_info['email']);
			
		$voucher_id = 	$db->func_array2insert("oc_voucher",$data);


$amount = 0.00;
foreach($items as $item)
{
	 $return_info = $db->func_query_first("SELECT
a.*,
b.sku,b.title,b.`quantity`,b.price,b.`return_code`,b.`reason`,b.`decision`,b.return_id
FROM
    `inv_returns` a
    INNER JOIN `inv_return_items`  b
        ON (a.`id` = b.`return_id`) 
		
		WHERE b.id='".(int)$item."'
		
		");
		
		
		$data = array();
			$data['order_id'] = $return_info['order_id'];
			$data['voucher_id'] = $voucher_id;
			$data['description'] = '$'.number_format($return_info['price'],2)." Gift Certificate for ".$order_info['first_name'];
			$data['code'] = $code;
			$data['from_name'] = 'PhonePartsUSA.com';
			$data['from_email'] = 'sales@phonepartsusa.com';
			$data['to_name'] = $db->func_escape_string($order_info['first_name']);
			$data['to_email'] = $db->func_escape_string($order_info['email']);
			$data['voucher_theme_id'] = 8;
			$data['message'] = 'Store Credit # '.$code.' has been issued';
			$data['amount'] = $return_info['price'];
			
				$db->func_array2insert("oc_order_voucher",$data);
		
		
$amount+=$return_info['price'];
	
	
}

$db->db_exec("UPDATE oc_voucher SET amount='".(float)$amount."' WHERE voucher_id='".$voucher_id."'");


		
		
		
			
			echo 'Store Credit Code: '.$code.' has been generated';
?>
