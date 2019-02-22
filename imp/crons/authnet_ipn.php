<?php

$data = $_POST;
//mail("vipin.garg12@gmail.com","RMA IPN",print_r($data,true));
file_put_contents("logs/authnet_ipn.txt",print_r($data,true),FILE_APPEND);

include_once '../config.php';

if($_POST['x_trans_id']){
	$transactionRow = array();
	$invoice_id = (int)$_POST['x_invoice_num'];
	$transactionRow['auth_code'] = $_POST['x_auth_code'];
	$transactionRow['transaction_id'] = $_POST['x_trans_id'];
	$transactionRow['avs_code'] = $_POST['x_avs_code'];
	$transactionRow['payment_source'] = "Auth.net";
	$transactionRow['dateofmodification'] = date('Y-m-d H:i:s');

	$isExist = $db->func_query_first("select id from inv_orders where order_id = '".$invoice_id."'");
	if(!$isExist){
		//skip
	}
	else{
		if($_POST['x_address']){
			$transactionRow['street_address'] = $db->func_escape_string($_POST['x_address']);
			$transactionRow['zipcode'] = $_POST['x_zip'];
		}

		$db->func_array2update("inv_orders",$transactionRow," order_id = '$invoice_id' ");
	}
}

echo "success";
exit;