<?php

include_once '../config.php';
include_once '../inc/functions.php';
include 'paypal/paypal.php';

//$api_username = 'phonepartsusa_api1.gmail.com';
//$api_password = 'VVFTLCAWDH22GFHB';
//$api_signature = 'AFcWxV21C7fd0v3bYYYRCpSSRl31ALdaX2OeEjPMSGWnmyzoNurmChOa';

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
		$transactionRow['payment_source'] = "PayPal";
		$transactionRow['transaction_fee'] = (float)$transaction_fee;
		$transactionRow['street_address'] = $db->func_escape_string(urldecode($transactionDetail['SHIPTOSTREET']));
		$transactionRow['zipcode'] = urldecode($transactionDetail['SHIPTOZIP']);
		$transactionRow['is_address_verified'] = $transactionDetail['ADDRESSSTATUS'];
		$transactionRow['dateofmodification'] = date('Y-m-d H:i:s');
		$transactionRow['paid_price'] = urldecode($transactions['L_AMT'.$i]);
		$transactionRow['paypal_updated'] = '1';

		$isExist = $db->func_query_first("select id from inv_orders where order_id = '".$invoice_id."'");
		if(!$isExist){
			$missingOrder = array();
			$missingOrder['order_id'] = $invoice_id;
			$missingOrder['transaction_id'] = $transactionId;
			$missingOrder['payment_source'] = "PayPal";
			$missingOrder['order_status'] = urldecode($transactions['L_STATUS'.$i]);
			$missingOrder['order_date']   = date('Y-m-d H:i:s',strtotime(urldecode($transactions['L_TIMESTAMP'.$i])));
			$missingOrder['paid_price'] = urldecode($transactions['L_AMT'.$i]);
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
			$query = "select o.* , od.* from inv_orders o inner join inv_orders_details od on o.order_id = od.order_id where order_status NOT IN ('Estimate','On Hold') and ss_ignore=0 and o.order_id = '$iorderID' group by o.order_id";
			$iorder = $db->func_query_first($query);

			if ($iorder) {
				whiteList($iorder, 1);
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
		$transactionRow['payment_source'] = "PayPal";
		$transactionRow['street_address'] = $db->func_escape_string(urldecode($transactionDetail['SHIPTOSTREET']));
		$transactionRow['zipcode'] = urldecode($transactionDetail['SHIPTOZIP']);
		$transactionRow['is_address_verified'] = $transactionDetail['ADDRESSSTATUS'];
		$transactionRow['dateofmodification'] = date('Y-m-d H:i:s');
		$transactionRow['paid_price'] = urldecode($transactions['L_AMT'.$i]);
		$transactionRow['paypal_updated'] = '1';

		$isExist = $db->func_query_first("select id from inv_orders where order_id = '".$invoice_id."'");
		if(!$isExist){
			$missingOrder = array();
			$missingOrder['order_id'] = $invoice_id;
			$missingOrder['transaction_id'] = $transactionId;
			$missingOrder['payment_source'] = "PayPal";
			$missingOrder['order_status'] = urldecode($transactions['L_STATUS'.$i]);
			$missingOrder['order_date']   = date('Y-m-d H:i:s',strtotime(urldecode($transactions['L_TIMESTAMP'.$i])));
			$missingOrder['paid_price'] = urldecode($transactions['L_AMT'.$i]);
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
			$query = "select o.* , od.* from inv_orders o inner join inv_orders_details od on o.order_id = od.order_id where order_status NOT IN ('Estimate','On Hold') and ss_ignore=0 and o.order_id = '$iorderID' group by o.order_id";
			$iorder = $db->func_query_first($query);

			if ($iorder) {
				whiteList($iorder, 1);
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
		$transactionRow['payment_source'] = "PayPal";
		$transactionRow['street_address'] = $db->func_escape_string(urldecode($transactionDetail['SHIPTOSTREET']));
		$transactionRow['zipcode'] = urldecode($transactionDetail['SHIPTOZIP']);
		$transactionRow['is_address_verified'] = $transactionDetail['ADDRESSSTATUS'];
		$transactionRow['dateofmodification'] = date('Y-m-d H:i:s');
		$transactionRow['paid_price'] = urldecode($transactions['L_AMT'.$i]);
		$transactionRow['paypal_updated'] = '1';

		$isExist = $db->func_query_first("select id from inv_orders where order_id = '".$invoice_id."'");
		if(!$isExist){
			$missingOrder = array();
			$missingOrder['order_id'] = $invoice_id;
			$missingOrder['transaction_id'] = $transactionId;
			$missingOrder['payment_source'] = "PayPal";
			$missingOrder['order_status'] = urldecode($transactions['L_STATUS'.$i]);
			$missingOrder['order_date']   = date('Y-m-d H:i:s',strtotime(urldecode($transactions['L_TIMESTAMP'.$i])));
			$missingOrder['paid_price'] = urldecode($transactions['L_AMT'.$i]);
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
			$query = "select o.* , od.* from inv_orders o inner join inv_orders_details od on o.order_id = od.order_id where order_status NOT IN ('Estimate','On Hold') and ss_ignore=0 and o.order_id = '$iorderID' group by o.order_id";
			$iorder = $db->func_query_first($query);

			if ($iorder) {
				whiteList($iorder, 1);
			}
		}

		$count--;
		$i++;
	}
}


$db->db_exec("Update inv_cron SET last_cron_time = '".$end_cron_date."' , status = 0 , last_run = '".date('Y-m-d H:i:s')."' where store_type = 'PayPal'");

echo "success";
exit;