<?php

include_once '../config.php';
include_once '../inc/functions.php';
include 'paypal/paypal.php';

//$api_username = 'phonepartsusa_api1.gmail.com';
//$api_password = 'VVFTLCAWDH22GFHB';
//$api_signature = 'AFcWxV21C7fd0v3bYYYRCpSSRl31ALdaX2OeEjPMSGWnmyzoNurmChOa';

function sendFraudDetails($order_id)
{
	global $db;
	global $path;
	global $host_path;
	require_once $path . 'phpmailer/class.smtp.php';
	require_once $path . 'phpmailer/class.phpmailer.php';
	$query = "select o.* , od.* from inv_orders o inner join inv_orders_details od on o.order_id = od.order_id where o.order_id = '".$db->func_escape_string($order_id)."' group by o.order_id";
	$order_details = $db->func_query_first($query);

	// sendEmail('Employee PPUSA', ADMIN_EMAIL, 'Test '.$order_id, json_encode($order_details));
	if(trim(strtolower($order_details))=='on hold')
	{


		$post = array(
			"description" => 'http://imp.phonepartsusa.com/viewOrderDetail.php?order='.$order_details['order_id'],
			"subject" => 'Order #'. $order_details['order_id'] .' is set to On Hold',
			"email" => $order_details['email'],
			"name" => $order_details['customer_name'],
			"priority" => 1,
			"status" => 2,
			"action"=>'create'
			);

			$ch = curl_init($host_path . 'freshdesk/create_ticket.php?config=1');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
			$response = curl_exec($ch);
			curl_close($ch);


			$blackListCustomer = (int)$db->func_query_first_cell('SELECT COUNT(email) FROM inv_chargeback WHERE lower(email) = "'. trim(strtolower($order_details['email'])) .'" OR (street_name = "' . $order_details['address1'] . '" AND zipcode = "'. substr($order_details['zip'], 0, 5) .'" AND street_name <> "" AND zipcode <> "")');

			$is_black_list = 0;



			if($blackListCustomer)
			{
				$is_black_list = 1;
			}

			if (stristr(trim(strtolower($order_details['email'])), '@protonmail.com') !== false) {
				$is_black_list = 1;
			}

			// email message
			$message = '';
			$message .= '<strong>Customer Name:</strong> '.$order_details['first_name'].' '.$order_details['last_name'];
			$message .= '<br><strong>Previous Orders:</strong> '.$db->func_query_first_cell("SELECT no_of_orders FROM inv_customers where trim(lower(email))='".trim(strtolower($order_details['email']))."'");
			$message .= '<br><strong>Previous Order Total:</strong> $'.number_format($db->func_query_first_cell("select sub_total+tax+shipping_amount from inv_orders where lower(trim(email))='".trim(strtolower($db->func_escape_string($order_details['email'])))."'"),2);

			$message .= '<br><strong>Order ID:</strong> '.$order_details['prefix'].$order_details['order_id'];
			$message .= '<br><strong>Order Total:</strong> '.(float)$order_details['sub_total']+(float)$order_details['tax']+(float)$order_details['shipping_amount'];
			$message .= '<br><strong>Payment Transaction ID:</strong> '.$order_details['transaction_id'];
			$message .= '<br><strong>Shipping Method:</strong> '.$order_details['shipping_method'];
			$message .= '<br><strong>Previous Blacklist:</strong> '.($is_black_list==0?'No':'Yes');
			$message .= '<br><strong>AVS Verification:</strong> AVSADDR:'.$order_details['is_address_verified'].' AVSPAYMENT: '.$order_details['avs_code'];
			$message .= '<br><strong>Billing Address:</strong> '.$order_details['bill_firstname'].' '.$order_details['bill_lastname'].', '.$order_details['bill_address1'].', '.($order_details['bill_address2']?$order_details['bill_address2'].', ':'').$order_details['bill_city'].', '.$db->func_query_first_cell("SELECT code FROM oc_zone WHERE zone_id='".$order_details['bill_zone_id']."'").', '.$order_details['bill_zip'];
			$message .= '<br><strong>Shipping Address:</strong> '.$order_details['shipping_firstname'].' '.$order_details['shipping_lastname'].', '.$order_details['_address1'].', '.($order_details['address2']?$order_details['address2'].', ':'').$order_details['city'].', '.$db->func_query_first_cell("SELECT code FROM oc_zone WHERE zone_id='".$order_details['zone_id']."'").', '.$order_details['zip'];

			sendEmail('Employee PPUSA', ANALYST_EMAIL, 'Hold On - Order '.$order_id, $message);
			sendEmail('Employee PPUSA', ADMIN_EMAIL, 'Hold On - Order '.$order_id, $message);


	}
}

$api_username  = 'saad_api1.phonepartsusa.com';
$api_password  = '25LZA8T9PK5JUE7D';
$api_signature = 'ADhwoxCQxWQkCgyvRaeKgsjmNM0SA2PC2ETubnk0A6gFN-agImHp4iEV';

$paypal = new PaypalPayment($api_username , $api_password , $api_signature);

$last_cron = $db->func_query_first("select last_cron_time from inv_cron where store_type = 'PayPal'");
if(@intval($last_cron['last_cron_time'])){
	$last_cron_date = $last_cron['last_cron_time'];
	$last_cron_date = date('Y-m-d\TH:i:s', strtotime($last_cron_date));
}
else{
	$last_cron_date = date('Y-m-d\TH:i:s', time() - (1*24*60*60));
}

$end_cron_date = gmdate('Y-m-d\TH:i:s');

if(strtotime($end_cron_date) - strtotime($last_cron_date) > (6*60*60)){
	$end_cron_date = date('Y-m-d\TH:i:s', strtotime($last_cron_date) + (6*60*60));
}

print $last_cron_date . " -- " . $end_cron_date . "<br />";

$transactions = $paypal->getTransactions($last_cron_date , $end_cron_date);
//print_r($transactions);exit;

if($transactions){
	$count = 0;
	//counting no.of  transaction IDs present in NVP response arrray.
	while (isset($transactions["L_TRANSACTIONID".$count])){
		$count++;
	}

	$i = 0;
	while($count > 0){
		$transactionId = urldecode($transactions['L_TRANSACTIONID'.$i]);
		$transaction_fee = urldecode($transactions['L_FEEAMT'.$i]);
		if($transaction_fee<0)
		{
			$transaction_fee = $transaction_fee * -1;
		}
		$transactionDetail = $paypal->getTransctionDetails($transactionId);
		if($transactionDetail['INVNUM']){
			$order_source = 'Web';
		}
		else{
			$order_source = 'eBay';
			$count--;
			$i++;
			continue;
		}

		$invoice_id = (int)$db->func_escape_string(urldecode($transactionDetail['INVNUM']));
		if(!$invoice_id){
			$count--;
			$i++;
			continue;
		}

		print $invoice_id . " -- " . $transactionId. "<br />";

		$transactionRow = array();
		$transactionRow['auth_code'] = 0;
		$transactionRow['transaction_id'] = $transactionId;
		$transactionRow['avs_code']       = urldecode($transactionDetail['PAYMENTSTATUS']);
		// $transactionRow['payment_source'] = "PayPal";
		$transactionRow['transaction_fee'] = (float)$transaction_fee;
		$transactionRow['street_address'] = $db->func_escape_string(urldecode($transactionDetail['SHIPTOSTREET']));
		$transactionRow['zipcode'] = urldecode($transactionDetail['SHIPTOZIP']);
		$transactionRow['is_address_verified'] = $transactionDetail['ADDRESSSTATUS'];
		$transactionRow['dateofmodification'] = date('Y-m-d H:i:s');
		// $transactionRow['paid_price'] = urldecode($transactions['L_AMT'.$i]);
		$transactionRow['paypal_updated'] = '1';

		$isExist = $db->func_query_first("select id from inv_orders where order_id = '".$invoice_id."'");
		if(!$isExist){
			$missingOrder = array();
			$missingOrder['order_id'] = $invoice_id;
			$missingOrder['transaction_id'] = $transactionId;
			$missingOrder['payment_source'] = "PayPal";
			$missingOrder['order_status'] = urldecode($transactions['L_STATUS'.$i]);
			$missingOrder['order_date']   = date('Y-m-d H:i:s',strtotime(urldecode($transactions['L_TIMESTAMP'.$i])));
			// $missingOrder['paid_price'] = urldecode($transactions['L_AMT'.$i]);
			$missingOrder['transaction_fee'] = (float)$transaction_fee;

			if($missingOrder['order_status'] == 'Completed'){
				//check if this is processed in oc table too
				$oc_exist = $db->func_query_first("select order_id , order_status_id from oc_order where order_id = '$invoice_id'"); //and order_status_id not in (15 , 24 , 3 , 16 , 7 , 21)
				if(!$oc_exist){
					$missingOrder['oc_order_status'] = getOrderStatus($oc_exist['order_status_id']);
					$db->func_array2insert("inv_missing_orders", $missingOrder);
				}
			}
		}
		else{
			$db->func_array2update("inv_orders",$transactionRow," order_id = '$invoice_id' ");
			$iorderID = $invoice_id;
			$query = "select o.* , od.* from inv_orders o inner join inv_orders_details od on o.order_id = od.order_id where o.order_id = '$iorderID' group by o.order_id";
			$iorder = $db->func_query_first($query);

			if ($iorder) {
				whiteList($iorder, 1);
				sendFraudDetails($iorderID);
			}
		}

		$count--;
		$i++;
	}
}

$api_username = 'paypal_api1.phonepartsusa.com';
$api_password = 'A3UTLAF89676LVFW';
$api_signature = 'AWEus9lWHhjbjG6qaUICKluU-eFdAZ2ufK7YWkgbrqeiaBiq1y7wOc0j';

$paypal = new PaypalPayment($api_username , $api_password , $api_signature);

$last_cron = $db->func_query_first("select last_cron_time from inv_cron where store_type = 'PayPal'");
if(@intval($last_cron['last_cron_time'])){
	$last_cron_date = $last_cron['last_cron_time'];
	$last_cron_date = date('Y-m-d\TH:i:s', strtotime($last_cron_date));
}
else{
	$last_cron_date = date('Y-m-d\TH:i:s', time() - (1*24*60*60));
}

$end_cron_date = gmdate('Y-m-d\TH:i:s');

if(strtotime($end_cron_date) - strtotime($last_cron_date) > (6*60*60)){
	$end_cron_date = date('Y-m-d\TH:i:s', strtotime($last_cron_date) + (6*60*60));
}

print $last_cron_date . " -- " . $end_cron_date . "<br />";

$transactions = $paypal->getTransactions($last_cron_date , $end_cron_date);
//echo "<pre>";
//print_r($transactions);exit;

if($transactions){
	$count = 0;
	//counting no.of  transaction IDs present in NVP response arrray.
	while (isset($transactions["L_TRANSACTIONID".$count])){
		$count++;
	}

	$i = 0;
	while($count > 0){
		$transactionId = urldecode($transactions['L_TRANSACTIONID'.$i]);
		$transaction_fee = urldecode($transactions['L_FEEAMT'.$i]);
		if($transaction_fee<0)
		{
			$transaction_fee = $transaction_fee * -1;
		}
		$transactionDetail = $paypal->getTransctionDetails($transactionId);
		if($transactionDetail['INVNUM']){
			$order_source = 'Web';
		}
		else{
			$order_source = 'eBay';
			$count--;
			$i++;
			continue;
		}

		$invoice_id =$db->func_escape_string(urldecode($transactionDetail['INVNUM']));
		if(!$invoice_id){
			$count--;
			$i++;
			continue;
		}

		print $invoice_id . " -- " . $transactionId. "<br />";

		$transactionRow = array();
		$transactionRow['auth_code'] = 0;
		$transactionRow['transaction_id'] = $transactionId;
		$transactionRow['transaction_fee'] = $transaction_fee;
		$transactionRow['avs_code']       = urldecode($transactionDetail['PAYMENTSTATUS']);
		// $transactionRow['payment_source'] = "PayPal";
		$transactionRow['street_address'] = $db->func_escape_string(urldecode($transactionDetail['SHIPTOSTREET']));
		$transactionRow['zipcode'] = urldecode($transactionDetail['SHIPTOZIP']);
		$transactionRow['is_address_verified'] = $transactionDetail['ADDRESSSTATUS'];
		$transactionRow['dateofmodification'] = date('Y-m-d H:i:s');
		// $transactionRow['paid_price'] = urldecode($transactions['L_AMT'.$i]);
		$transactionRow['paypal_updated'] = '1';

		$isExist = $db->func_query_first("select id from inv_orders where order_id = '".$invoice_id."'");
		if(!$isExist){
			$missingOrder = array();
			$missingOrder['order_id'] = $invoice_id;
			$missingOrder['transaction_id'] = $transactionId;
			$missingOrder['payment_source'] = "PayPal";
			$missingOrder['order_status'] = urldecode($transactions['L_STATUS'.$i]);
			$missingOrder['order_date']   = date('Y-m-d H:i:s',strtotime(urldecode($transactions['L_TIMESTAMP'.$i])));
			// $missingOrder['paid_price'] = urldecode($transactions['L_AMT'.$i]);
			$missingOrder['transaction_fee'] = (float)$transaction_fee;
			if($missingOrder['order_status'] == 'Completed'){
				//check if this is processed in oc table too
				$oc_exist = $db->func_query_first("select order_id from oc_order where order_id = '$invoice_id' ");
				if(!$oc_exist){
					$db->func_array2insert("inv_missing_orders", $missingOrder);
				}
			}
		}
		else{
			$db->func_array2update("inv_orders",$transactionRow," order_id = '$invoice_id' ");
			$iorderID = $invoice_id;
			$query = "select o.* , od.* from inv_orders o inner join inv_orders_details od on o.order_id = od.order_id where o.order_id = '$iorderID' group by o.order_id";
			$iorder = $db->func_query_first($query);

			if ($iorder) {
				whiteList($iorder, 1);
				sendFraudDetails($iorderID);
			}
		}

		$count--;
		$i++;
	}
}
$last_cron = $db->func_query_first("select last_cron_time from inv_cron where store_type = 'PayPal'");
if(@intval($last_cron['last_cron_time'])){
	$last_cron_date = $last_cron['last_cron_time'];
	$last_cron_date = date('Y-m-d\TH:i:s', strtotime($last_cron_date));
}
else{
	$last_cron_date = date('Y-m-d\TH:i:s', time() - (1*24*60*60));
}

$end_cron_date = gmdate('Y-m-d\TH:i:s');

if(strtotime($end_cron_date) - strtotime($last_cron_date) > (6*60*60)){
	$end_cron_date = date('Y-m-d\TH:i:s', strtotime($last_cron_date) + (6*60*60));
}



$api_username = 'admin_api1.replacementlcds.com';
$api_password = 'RYV6DNWNNLVSY5BP';
$api_signature = 'AKDJMrcfZ1rLAY1K5iKwGm86PLbiABK1CxVKkOQqmclTR72aK8GJDvEW';

$paypal = new PaypalPayment($api_username , $api_password , $api_signature);

$transactions = $paypal->getTransactions($last_cron_date , $end_cron_date);


if($transactions){
	$count = 0;
	//counting no.of  transaction IDs present in NVP response arrray.
	while (isset($transactions["L_TRANSACTIONID".$count])){
		$count++;
	}

	$i = 0;
	while($count > 0){
		$transactionId = urldecode($transactions['L_TRANSACTIONID'.$i]);
		$transaction_fee = urldecode($transactions['L_FEEAMT'.$i]);
		if($transaction_fee<0)
		{
			$transaction_fee = $transaction_fee * -1;
		}
		$transactionDetail = $paypal->getTransctionDetails($transactionId);
		if($transactionDetail['INVNUM']){
			$order_source = 'Web';
		}
		else{
			$order_source = 'eBay';
			$count--;
			$i++;
			continue;
		}

		$invoice_id = urldecode($transactionDetail['INVNUM']);
		$invoice_id = str_replace('#', '', $invoice_id);
		$invoice_id = 'RL'.$invoice_id;
		if(!$invoice_id){
			$count--;
			$i++;
			continue;
		}

		print $invoice_id . " -- " . $transactionId. "<br />";

		$transactionRow = array();
		$transactionRow['auth_code'] = 0;
		$transactionRow['transaction_id'] = $transactionId;
		$transactionRow['transaction_fee'] = $transaction_fee;
		$transactionRow['avs_code']       = urldecode($transactionDetail['PAYMENTSTATUS']);
		// $transactionRow['payment_source'] = "PayPal";
		$transactionRow['street_address'] = $db->func_escape_string(urldecode($transactionDetail['SHIPTOSTREET']));
		$transactionRow['zipcode'] = urldecode($transactionDetail['SHIPTOZIP']);
		$transactionRow['is_address_verified'] = $transactionDetail['ADDRESSSTATUS'];
		$transactionRow['dateofmodification'] = date('Y-m-d H:i:s');
		// $transactionRow['paid_price'] = urldecode($transactions['L_AMT'.$i]);
		$transactionRow['paypal_updated'] = '1';

		$isExist = $db->func_query_first("select id from inv_orders where order_id = '".$invoice_id."'");
		if(!$isExist){
			$missingOrder = array();
			$missingOrder['order_id'] = $invoice_id;
			$missingOrder['transaction_id'] = $transactionId;
			$missingOrder['payment_source'] = "PayPal";
			$missingOrder['order_status'] = urldecode($transactions['L_STATUS'.$i]);
			$missingOrder['order_date']   = date('Y-m-d H:i:s',strtotime(urldecode($transactions['L_TIMESTAMP'.$i])));
			// $missingOrder['paid_price'] = urldecode($transactions['L_AMT'.$i]);
			$missingOrder['transaction_fee'] = (float)$transaction_fee;
			if($missingOrder['order_status'] == 'Completed'){
				//check if this is processed in oc table too
				$oc_exist = $db->func_query_first("select order_id from oc_order where order_id = '$invoice_id' and order_status_id not in (15 , 24 , 3 , 16 , 7 , 21)");
				if(!$oc_exist){
					$db->func_array2insert("inv_missing_orders", $missingOrder);
				}
			}
		}
		else{
			$db->func_array2update("inv_orders",$transactionRow," order_id = '$invoice_id' ");
			$iorderID = $invoice_id;
			$query = "select o.* , od.* from inv_orders o inner join inv_orders_details od on o.order_id = od.order_id where o.order_id = '$iorderID' group by o.order_id";
			$iorder = $db->func_query_first($query);

			if ($iorder) {
				whiteList($iorder, 1);
				sendFraudDetails($iorderID);
			}
		}

		$count--;
		$i++;
	}
}


$db->db_exec("Update inv_cron SET last_cron_time = '".$end_cron_date."' , status = 0 , last_run = '".date('Y-m-d H:i:s')."' where store_type = 'PayPal'");

echo "success";
exit;