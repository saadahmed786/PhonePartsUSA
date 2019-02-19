<?php
include_once '../config.php';
include_once '../inc/functions.php';
include 'paypal/paypal.php';
set_time_limit(120);
//$api_username = 'phonepartsusa_api1.gmail.com';
//$api_password = 'VVFTLCAWDH22GFHB';
//$api_signature = 'AFcWxV21C7fd0v3bYYYRCpSSRl31ALdaX2OeEjPMSGWnmyzoNurmChOa';

$api_username = 'paypal_api1.phonepartsusa.com';
$api_password = 'A3UTLAF89676LVFW';
$api_signature = 'AWEus9lWHhjbjG6qaUICKluU-eFdAZ2ufK7YWkgbrqeiaBiq1y7wOc0j';

$paypal = new PaypalPayment($api_username , $api_password , $api_signature);

//$last_cron = $db->func_query_first("select last_cron_time from inv_cron where store_type = 'PayPal'");
$last_cron['last_cron_time'] = '2015-10-25T01:00:01';
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
$end_cron_date = date('Y-m-d\TH:i:s', strtotime('2015-10-30T23:00:01') );
print $last_cron_date . " -- " . $end_cron_date . "<br />";

$transactions = $paypal->getTransactions($last_cron_date , $end_cron_date);
echo "<pre>";
print_r($transactions);
echo "</pre>";exit;
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
		echo "<pre>";

		print_r($transactionDetail);exit;
		if($transactionDetail['INVNUM']){
			$order_source = 'Web';
		}
		else{
			$order_source = 'eBay';
			$count--;
			$i++;
			continue;
		}

		//$invoice_id = (int)$db->func_escape_string(urldecode($transactionDetail['INVNUM']));
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

		$isExist = $db->func_query_first("select id from inv_orders where order_id = '".$invoice_id."'");
		if(!$isExist){
			$missingOrder = array();
			$missingOrder['order_id'] = $invoice_id;
			$missingOrder['transaction_id'] = $transactionId;
			$missingOrder['payment_source'] = "PayPal";
			$missingOrder['order_status'] = urldecode($transactions['L_STATUS'.$i]);
			$missingOrder['order_date']   = date('Y-m-d H:i:s',strtotime(urldecode($transactions['L_TIMESTAMP'.$i])));

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
		}

		$count--;
		$i++;
	}
}


?>