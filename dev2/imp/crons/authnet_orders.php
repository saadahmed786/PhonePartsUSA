<?php

set_time_limit(0);
include_once '../config.php';
include_once '../inc/functions.php';
include 'authnet/AuthorizeNet.php';

define("AUTHORIZENET_API_LOGIN_ID", "72JxcBN6r");
define("AUTHORIZENET_TRANSACTION_KEY", "37d46De47QQWHrcW");
define("AUTHORIZENET_SANDBOX", false);

$last_cron = $db->func_query_first("select last_cron_time from inv_cron where store_type = 'AuthNet'");
if(@intval($last_cron['last_cron_time'])){
	$last_cron_date = $last_cron['last_cron_time'];
	$last_cron_date = date('Y-m-d\TH:i:s', strtotime($last_cron_date) - (12*60*60));
}
else{
	$last_cron_date = date('Y-m-d\TH:i:s', time() - (2*24*60*60));
}

// to fix UTC and client local time
$end_cron_date = date('Y-m-d\TH:i:s' , time() + (24*60*60));

if(strtotime($end_cron_date) - strtotime($last_cron_date) > (5*24*60*60)){
	$end_cron_date = date('Y-m-d\TH:i:s', strtotime($last_cron_date) + (5*24*60*60));
}

print $last_cron_date . " -- " . $end_cron_date . "<br />";

// Get Settled Batch List
$request  = new AuthorizeNetTD;
$response = $request->getSettledBatchList(false , $last_cron_date , $end_cron_date);

if($response->xml->messages->resultCode == 'Error'){
	echo $response->xml->messages->message->text;
}
else{
	foreach ($response->xml->batchList->batch as $batch) {
		$batchId = $batch->batchId;
		$transactions = $request->getTransactionList($batchId);

		if($transactions->xml->transactions->transaction){
			foreach($transactions->xml->transactions->transaction as $tranasction){
				$transactionId = (string)$tranasction->transId;
				$response = $request->getTransactionDetails($transactionId);

				if(!$tranasction->invoiceNumber){
					continue;
				}

				$transactionRow = array();
				$invoice_id = (int)$tranasction->invoiceNumber;
				$transactionRow['transaction_id'] = $transactionId;
				$transactionRow['auth_code'] = $response->xml->transaction->authCode;
				$transactionRow['avs_code']  = $response->xml->transaction->AVSResponse;
				$transactionRow['payment_source'] = "Auth.net";
				$transactionRow['street_address'] = $db->func_escape_string($response->xml->transaction->billTo->address);
				$transactionRow['zipcode'] = $response->xml->transaction->billTo->zip;
				$transactionRow['dateofmodification'] = date('Y-m-d H:i:s');

				print $invoice_id . "--". $transactionId. "--". $tranasction->submitTimeLocal. "<br />";

				$isExist = $db->func_query_first("select id  from inv_orders where order_id = '".$invoice_id."'");
				if(!$isExist){
					$missingOrder = array();
					$missingOrder['order_id'] = $invoice_id;
					$missingOrder['transaction_id'] = $transactionId;
					$missingOrder['payment_source'] = "Auth.net";
					$missingOrder['order_date'] = $tranasction->submitTimeLocal;
					$missingOrder['order_status'] = $tranasction->transactionStatus;

					$transaction_status = strtolower($tranasction->transactionStatus);
					if(in_array($transaction_status , array('captured','pending settlement','captured/pending settlement','settled successfully'))){
						//check if this is processed in oc table too
						$oc_exist = $db->func_query_first("select order_id , order_status_id from oc_order where order_id = '$invoice_id'"); //and order_status_id not in (15 , 24 , 3 , 16 , 7 , 21)
						if(!$oc_exist){
							$missingOrder['oc_order_status'] = getOrderStatus($oc_exist['order_status_id']);
							$db->func_array2insert("inv_missing_orders", $missingOrder);
						}
					}
				}
				else{
					$db->func_array2update("inv_orders",$transactionRow," order_id = '".$invoice_id."' ");
				}
			}
		}
	}

	$end_cron_date = date('Y-m-d H:i:s', strtotime($end_cron_date) - (24*60*60));
	$db->db_exec("Update inv_cron SET last_cron_time = '".$end_cron_date."' , status = 0 , last_run = '".date('Y-m-d H:i:s')."' where store_type = 'AuthNet'");
}

echo "success";
exit;