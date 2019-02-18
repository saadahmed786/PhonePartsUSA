<?php

$data = $_POST;
mail("xaman.riaz@gmail.com","RMA IPN",print_r($data,true));
file_put_contents("logs/paypal_ipn.txt",print_r($data,true),FILE_APPEND);

include_once '../config.php';

if($_POST['txn_id']){
	if($transactionDetail['INVNUM']){
		$order_source = 'Web';
	}
	else{
		$order_source = 'eBay';
	}

	if($order_source == 'eBay'){
		exit;
	}

	$transactionRow = array();
	$invoice_id = $_POST['invnum'];
	$transactionRow['payment_source'] = 'PayPal';
	$transactionRow['auth_code'] = 0;
	$transactionRow['transaction_id'] = $_POST['txn_id'];
	$transactionRow['avs_code']   = $_POST['payment_status'];
	$transactionRow['street_address'] = $db->func_escape_string($_POST['address_street']);
	$transactionRow['zipcode'] = $_POST['address_zip'];
	$transactionRow['dateofmodification'] = date('Y-m-d H:i:s');

	$isExist = $db->func_query_first("select id from inv_orders where order_id = '$invoice_id'");
	if(!$isExist){
		//skip
	}
	else{
		$db->func_array2update("inv_orders",$transactionRow," order_id = '$invoice_id' ");
	}
}

echo "success";
exit;