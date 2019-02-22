<?php
set_time_limit(200);
include_once '../config.php';
include_once '../inc/functions.php';
include 'paypal/paypal.php';
// exit;
$api_username = 'paypal_api1.phonepartsusa.com';
$api_password = 'A3UTLAF89676LVFW';
$api_signature = 'AWEus9lWHhjbjG6qaUICKluU-eFdAZ2ufK7YWkgbrqeiaBiq1y7wOc0j';

$paypal = new PaypalPayment($api_username , $api_password , $api_signature);

$last_cron = $db->func_query_first("select last_cron_time from inv_cron where store_type = 'PPTransaction'");
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
// echo "<pre>";
// print_r($transactions);exit;

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
			$invoice_id =$db->func_escape_string(urldecode($transactionDetail['INVNUM']));
		}
		else{
			$payflow_check = $db->func_query_first("SELECT order_id,pp_transaction_id as transaction_id FROM oc_payflow_admin_tools WHERE pp_transaction_id='".$transactionId."'");
			if($payflow_check)
			{
				$order_source = 'Payflow';
				$invoice_id = $payflow_check['order_id'];

			}
			else
			{
				$order_source = 'Unknown';
				$invoice_id='';
			}
			//$count--;
			//$i++;
			//continue;
		}

		
		if(!$invoice_id){
		//	$count--;
		//	$i++;
		//	continue;
		}

		print $invoice_id . " -- " . $transactionId. "<br />";
		
		//$map_check = $db->func_query_first("SELECT transaction_id FROM inv_transactions where transaction_id='".$transactionId."' and order_id<>'' and is_mapped=1");
		$map_check = $db->func_query_first("SELECT transaction_id FROM inv_transactions where transaction_id='".$transactionId."' ");
		if($map_check)
		{
			// exit;
			$count--;
			$i++;
			continue;
		}
	//	if($order_source=='Unknown')
	//	{
		//	echo "<pre>";

// 			print_r($transactions);
// 			echo "</pre>";
// echo '=======================';
 		// 	echo "<pre>";

			// print_r($transactionDetail);
			// echo "</pre>";

		//}
		$data = array();
		$data['firstname'] = $db->func_escape_string(urldecode($transactionDetail['FIRSTNAME']));;
		$data['lastname'] = $db->func_escape_string(urldecode($transactionDetail['LASTNAME']));;
		

		$data['email'] = $db->func_escape_string(urldecode($transactionDetail['EMAIL']));;
		if($data['email']=='')
		{
			$data['email'] = $db->func_escape_string(urldecode($transactionDetail['L_EMAIL'.$i]));;
		}

		$data['receiver_email'] = $db->func_escape_string(urldecode($transactionDetail['RECEIVEREMAIL']));;
		$data['transaction_id'] = $transactionId;
		$data['order_id'] = $invoice_id;
		$data['amount']       = urldecode($transactions['L_AMT'.$i]);
		$data['transaction_fee'] = (float)$transaction_fee;
		$data['net_amount'] = urldecode($transactions['L_NETAMT'.$i]);
		$data['order_status'] =  $db->func_escape_string(urldecode($transactions['L_STATUS'.$i]));
		$data['payment_status'] =  $db->func_escape_string(urldecode($transactionDetail['PAYMENTSTATUS']));


		$data['order_date'] =date('Y-m-d H:i:s',strtotime(urldecode($transactions['L_TIMESTAMP'.$i])));
		$data['order_source'] = $order_source;
		$data['date_added'] = date("Y-m-d H:i:s");
		
		$isExist = $db->func_query_first("select id from inv_orders where order_id = '".$invoice_id."'");
		if(!$isExist){
			$data['is_mapped']  = 0;
			if($data['payment_status'] == 'Completed'){
				//check if this is processed in oc table too
				$oc_exist = $db->func_query_first("select order_id from oc_order where cast(order_id as char(50)) = '$invoice_id' and order_status_id>0 ");
				if(!$oc_exist){
					$data['is_mapped']  = 0;
				}
				else
				{
					$data['is_mapped']=1;
				}
			}
		}
		else{
			$data['is_mapped'] = 1;
			
			
		}

		if ($data['is_mapped'] == 0) {
			$invoice_id = $db->func_query_first_cell("SELECT order_id FROM inv_orders WHERE DATE(order_date) = '" . date('Y-m-d',strtotime($data['order_date'])) . "' AND (order_price - paid_price) = '". $data['amount'] ."' AND email = '". $data['email'] ."'");
			if (!$invoice_id) {
				$data['is_mapped'] = 0;
				$invoice_id = $db->func_query_first_cell("SELECT order_id FROM oc_order WHERE DATE(date_added) = '" . date('Y-m-d',strtotime($data['order_date'])) . "' AND cast(total as decimal(14,2)) = '". $data['amount'] ."' AND email = '". $data['email'] ."'");
				if ($invoice_id) {
					$data['order_id'] = $invoice_id;
					$data['is_mapped'] = 1;
				}
			} else {
				$data['order_id'] = $invoice_id;
				$data['is_mapped'] = 1;
				
			}
		}


		$check = $db->func_query_first("SELECT transaction_id FROM inv_transactions WHERE transaction_id='".$transactionId."'");
		if($check)
		{
			$db->func_array2update("inv_transactions",$data," transaction_id = '$transactionId' ");
		}
		else
		{
			$db->func_array2insert("inv_transactions",$data);
			if($data['is_mapped']==1 and $invoice_id)
			{
				$check_for_paid = $db->func_query_first("SELECT paid_price,order_price,order_status FROM inv_orders WHERE order_id='".$invoice_id."'");
			if((float)$check_for_paid['paid_price']==0.00) {
					$db->db_exec("UPDATE inv_orders SET paid_price=".$data['amount']." where order_id='".$invoice_id."'");	
				} else if ( ($check_for_paid['order_price'] - $check_for_paid['paid_price']) == $data['amount']) {
					$db->db_exec("UPDATE inv_orders SET paid_price=paid_price+".$data['amount']." where order_id='".$invoice_id."'");	
				}

			if(strtolower($check_for_paid['order_status'])=='on hold' && round($check_for_paid['order_price'],1)==round($data['amount'],1))
			{
			//	$db->db_exec("UPDATE inv_orders SET order_status = 'Processed' WHERE order_id='".$invoice_id."'");
			//	$db->db_exec("UPDATE oc_order SET order_status_id = '15' WHERE cast(order_id as char(50))='".$invoice_id."' OR ref_order_id='".$invoice_id."'");

			/*	$addcomment = array();
		$addcomment['date_added'] = date('Y-m-d H:i:s');
		$addcomment['user_id'] = '43';
		$addcomment['comment'] = 'Order payment mapped. Order Processed';
		$addcomment['order_id'] = $order['order_id'];
		$order_history_id = $db->func_array2insert("oc_order_history", $addcomment);
		*/


			}
				$payment_source = $db->func_query_first_cell("SELECT lower(payment_source) FROM inv_orders where order_id='".$invoice_id."'");
			
			if(strtolower($order_source)=='payflow')
			{
				
				$payment_source='payflow';
			}
			else
			{
				$payment_source='paypal';
			}

			addVoucher($invoice_id,$payment_source,$data['amount'],$data['transaction_id']);

					$accounts = array();
					$accounts['description'] = 'Payment made #'.$data['transaction_id'].' @ '.$invoice_id;
					if($data['net_amount']>0)
					{
						$accounts['credit'] = 0.00;
						$accounts['debit'] = $data['net_amount'];
						
					}
					else
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $data['net_amount']*(-1);
					}
					$accounts['order_id'] = $invoice_id;
					$accounts['customer_email'] = $data['email'];
					$accounts['type']='paypal';
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = $data['order_date'];

					
					if(trim($data['order_id']) && trim($data['transaction_id']))
					{
						add_accounting_voucher($accounts); // Debit PayPal Account
						
					}


					$accounts = array();
					$accounts['description'] = 'Payment made #'.$data['transaction_id'].' @ '.$invoice_id;
					$accounts['customer_email'] = $data['email'];
					
					if($data['net_amount']>0)
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $data['net_amount'];
						
					}
					else
					{
						$accounts['debit'] = $data['net_amount']*(-1);
						$accounts['credit'] = 0.00;
					}


					$accounts['order_id'] = $invoice_id;
					$accounts['type'] = 'sales';
					$accounts['contra_account_code']='paypal';
					$accounts['date_added'] = $data['order_date'];

					if(trim($data['order_id']) && trim($data['transaction_id']))
					{
						add_accounting_voucher($accounts); // Debit PayPal Account
						
					}


					// PayPal Fee

					$accounts = array();
					$accounts['description'] = 'PayPal Fee @ '.$data['transaction_id'];
					if($data['transaction_fee']>0)
					{
					$accounts['credit'] = 0.00;
					$accounts['debit'] = $data['transaction_fee'];
					}
					else
					{
						$accounts['debit'] = 0.00;
					$accounts['credit'] = $data['transaction_fee']*(-1);
					}
					$accounts['order_id'] = $invoice_id;
					$accounts['customer_email'] = $data['email'];
					$accounts['type']='paypal_fee';
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = $data['order_date'];

					add_accounting_voucher($accounts); 


					$accounts = array();
					$accounts['description'] = 'Payment made @ '.$data['transaction_id'];
					if($data['transaction_fee']>0)
					{
					$accounts['debit'] = 0.00;
					$accounts['credit'] = $data['transaction_fee'];
					}
					else
					{
						$accounts['credit'] = 0.00;
					$accounts['debit'] = $data['transaction_fee']*(-1);
					}
					$accounts['order_id'] = $invoice_id;
					$accounts['customer_email'] = $data['email'];
					$accounts['type']='sales';
					$accounts['contra_account_code'] = 'paypal_fee';
					$accounts['date_added'] = $data['order_date'];

					add_accounting_voucher($accounts); 

					// End PayPal Fee


					$tax_check = $db->func_query_first_cell("SELECT tax from inv_orders where order_id='".$invoice_id."'");

					if($tax_check>0)
					{
						$tax_rate = $db->func_query_first("SELECT * FROM oc_tax_rate WHERE geo_zone_id=10");
						$tax_amount = ($data['amount']*(float)$tax_detail['rate'])/100;

					$accounts = array();
					$accounts['description'] = 'State Tax';
					if($tax_amount>0)
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $tax_amount;
						
					}
					else
					{
						$accounts['debit'] = $tax_amount*(-1);
						$accounts['credit'] = 0.00;
					}
					$accounts['order_id'] = $invoice_id;
					$accounts['customer_email'] = $data['email'];
					$accounts['type']='tax';
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = $data['order_date'];
					if(trim($data['order_id']) && trim($data['transaction_id']))
					{
						add_accounting_voucher($accounts); // Tax Account
						
					}


					$accounts = array();
					$accounts['description'] = 'State Tax';
					if($tax_amount>0)
					{
						$accounts['debit'] = $tax_amount;
						$accounts['credit'] = 0.00;
						
					}
					else
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $tax_amount*(-1);
					}
					$accounts['order_id'] = $invoice_id;
					$accounts['customer_email'] = $data['email'];
					$accounts['type']='sales';
					$accounts['contra_account_code'] = 'tax';
					$accounts['date_added'] = $data['order_date'];

					if(trim($data['order_id']) && trim($data['transaction_id']))
					{
						add_accounting_voucher($accounts); // Tax Account
						
					}

					}


			}
		}

						$is_missing = false;
						if($data['is_mapped']=='0')
						{
							$is_missing = true;
						}
						
						$missing_check = getOrder($data['order_id']);
						if($missing_check['order_id'])
						{
							$is_missing = false;
						}

						if($is_missing)
						{

							$missing_check = getOrder('E'.$data['order_id']);	
							if($missing_check['order_id'])
							{
								$data['order_id'] = 'E'.$data['order_id'];
								$is_missing = false;

							}

						}

						if($is_missing)
						{

							$missing_check = getOrder('RL'.$data['order_id']);
							if($missing_check['order_id'])
							{
								$data['order_id'] = 'RL'.$data['order_id'];
								$is_missing = false;
							}

						}

						if($is_missing==false)
						{
							$db->func_query("UPDATE inv_transactions SET is_mapped=1,order_id='".$data['order_id']."' WHERE transaction_id='".$data['transaction_id']."'");
							
							$payment_source = $db->func_query_first_cell("SELECT lower(payment_source) FROM inv_orders where order_id='".$data['order_id']."'");
			
			if(strtolower($order_source)=='payflow')
			{
				
				$payment_source='payflow';
			}
			else
			{
				$payment_source='paypal';
			}

							addVoucher($data['order_id'],$payment_source,$data['amount'],$data['transaction_id']);

							$accounts = array();
					$accounts['description'] = 'Payment made #'.$data['transaction_id'].' @ '.$invoice_id;
					if($data['net_amount']>0)
					{
						$accounts['credit'] = 0.00;
						$accounts['debit'] = $data['net_amount'];
						
					}
					else
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $data['net_amount']*(-1);
					}
					$accounts['order_id'] = $invoice_id;
					$accounts['customer_email'] = $data['email'];
					$accounts['type']='paypal';
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = $data['order_date'];

					if(trim($data['order_id']) && trim($data['transaction_id']))
					{
						add_accounting_voucher($accounts); // Debit PayPal Account
						
					}


					$accounts = array();
					$accounts['description'] = 'Payment made #'.$data['transaction_id'].' @ '.$invoice_id;
					$accounts['customer_email'] = $data['email'];
					
					if($data['net_amount']>0)
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $data['net_amount'];
						
					}
					else
					{
						$accounts['debit'] = $data['net_amount']*(-1);
						$accounts['credit'] = 0.00;
					}


					$accounts['order_id'] = $invoice_id;
					$accounts['type'] = 'sales';
					$accounts['contra_account_code']='paypal';
					$accounts['date_added'] = $data['order_date'];

					if(trim($data['order_id']) && trim($data['transaction_id']))
					{
						add_accounting_voucher($accounts); // Debit PayPal Account
						
					}


					// PayPal Fee

					$accounts = array();
					$accounts['description'] = 'PayPal Fee @ '.$data['transaction_id'];
					if($data['transaction_fee']>0)
					{
					$accounts['credit'] = 0.00;
					$accounts['debit'] = $data['transaction_fee'];
					}
					else
					{
						$accounts['debit'] = 0.00;
					$accounts['credit'] = $data['transaction_fee']*(-1);
					}
					$accounts['order_id'] = $invoice_id;
					$accounts['customer_email'] = $data['email'];
					$accounts['type']='paypal_fee';
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = $data['order_date'];

					add_accounting_voucher($accounts); 


					$accounts = array();
					$accounts['description'] = 'Payment made @ '.$data['transaction_id'];
					if($data['transaction_fee']>0)
					{
					$accounts['debit'] = 0.00;
					$accounts['credit'] = $data['transaction_fee'];
					}
					else
					{
						$accounts['credit'] = 0.00;
					$accounts['debit'] = $data['transaction_fee']*(-1);
					}
					$accounts['order_id'] = $invoice_id;
					$accounts['customer_email'] = $data['email'];
					$accounts['type']='sales';
					$accounts['contra_account_code'] = 'paypal_fee';
					$accounts['date_added'] = $data['order_date'];

					add_accounting_voucher($accounts); 

					// End PayPal Fee



					$tax_check = $db->func_query_first_cell("SELECT tax from inv_orders where order_id='".$invoice_id."'");

					if($tax_check>0)
					{
						$tax_rate = $db->func_query_first("SELECT * FROM oc_tax_rate WHERE geo_zone_id=10");
						$tax_amount = ($data['amount']*(float)$tax_detail['rate'])/100;

					$accounts = array();
					$accounts['description'] = 'State Tax';
					if($tax_amount>0)
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $tax_amount;
						
					}
					else
					{
						$accounts['debit'] = $tax_amount*(-1);
						$accounts['credit'] = 0.00;
					}
					$accounts['order_id'] = $invoice_id;
					$accounts['customer_email'] = $data['email'];
					$accounts['type']='tax';
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = $data['order_date'];
					if(trim($data['order_id']) && trim($data['transaction_id']))
					{
						add_accounting_voucher($accounts); // Tax Account
						
					}


					$accounts = array();
					$accounts['description'] = 'State Tax';
					if($tax_amount>0)
					{
						$accounts['debit'] = $tax_amount;
						$accounts['credit'] = 0.00;
						
					}
					else
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $tax_amount*(-1);
					}
					$accounts['order_id'] = $invoice_id;
					$accounts['customer_email'] = $data['email'];
					$accounts['type']='sales';
					$accounts['contra_account_code'] = 'tax';
					$accounts['date_added'] = $data['order_date'];

					if(trim($data['order_id']) && trim($data['transaction_id']))
					{
						add_accounting_voucher($accounts); // Tax Account
						
					}

					}



						}
		
		
		

		$count--;
		$i++;
	}
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
			$order_source = 'RLCD';
			
		}
		else{
			$order_source = 'Payflow';
			$count--;
			$i++;
			continue;
		}

		$invoice_id  =$db->func_escape_string(urldecode($transactionDetail['INVNUM']));
		
		if (preg_match('/#/',$invoice_id))
		{
			$invoice_id = str_replace("#", "", $invoice_id);
			$invoice_id = 'RL'.$invoice_id;
		}


		print $invoice_id . " -- " . $transactionId. "<br />";

		$map_check = $db->func_query_first("SELECT transaction_id FROM inv_transactions where transaction_id='".$transactionId."' ");
		if($map_check)
		{
			// exit;
			$count--;
			$i++;
			continue;
		}

		$data = array();
		$data['firstname'] = $db->func_escape_string(urldecode($transactionDetail['FIRSTNAME']));;
		$data['lastname'] = $db->func_escape_string(urldecode($transactionDetail['LASTNAME']));;
		$data['email'] = $db->func_escape_string(urldecode($transactionDetail['EMAIL']));;
		$data['receiver_email'] = $db->func_escape_string(urldecode($transactionDetail['RECEIVEREMAIL']));;
		$data['transaction_id'] = $transactionId;
		$data['order_id'] = $invoice_id;
		$data['amount']       = urldecode($transactions['L_AMT'.$i]);
		$data['transaction_fee'] = (float)$transaction_fee;
		$data['net_amount'] = urldecode($transactions['L_NETAMT'.$i]);
		$data['order_status'] =  $db->func_escape_string(urldecode($transactions['L_STATUS'.$i]));
		$data['payment_status'] =  $db->func_escape_string(urldecode($transactionDetail['PAYMENTSTATUS']));
		$data['order_date'] =date('Y-m-d H:i:s',strtotime(urldecode($transactions['L_TIMESTAMP'.$i])));
		$data['order_source'] = $order_source;
		$data['date_added'] = date("Y-m-d H:i:s");
		
		$isExist = $db->func_query_first("select id from inv_orders where order_id = '".$invoice_id."'");
		if(!$isExist){
			$data['is_mapped']  = 0;
			if($data['payment_status'] == 'Completed'){
				//check if this is processed in oc table too
				$oc_exist = $db->func_query_first("select order_id from oc_order where cast(order_id as char(50)) = '$invoice_id' and order_status_id>0 ");
				if(!$oc_exist){
					$data['is_mapped']  = 0;
				}
				else
				{
					$data['is_mapped']=1;
				}
			}
		}
		else{
			$data['is_mapped'] = 1;
			
			
		}

		if ($data['is_mapped'] == 0) {
			$invoice_id = $db->func_query_first_cell("SELECT order_id FROM inv_orders WHERE DATE(order_date) = '" . date('Y-m-d',strtotime($data['order_date'])) . "' AND order_price = '". $data['amount'] ."' AND email = '". $data['email'] ."'");
			if (!$invoice_id) {
				$data['is_mapped'] = 0;
				$invoice_id = $db->func_query_first_cell("SELECT order_id FROM oc_order WHERE DATE(date_added) = '" . date('Y-m-d',strtotime($data['order_date'])) . "' AND cast(total as decimal(14,2)) = '". $data['amount'] ."' AND email = '". $data['email'] ."'");
				if ($invoice_id) {
					$data['order_id'] = $invoice_id;
					$data['is_mapped'] = 1;
				}
			} else {
				$data['order_id'] = $invoice_id;
				$data['is_mapped'] = 1;
				
			}
		}

		$check = $db->func_query_first("SELECT transaction_id FROM inv_transactions WHERE transaction_id='".$transactionId."'");
		if($check)
		{
			$db->func_array2update("inv_transactions",$data," transaction_id = '$transactionId' ");
		}
		else
		{
			$db->func_array2insert("inv_transactions",$data);
			$check_for_paid = $db->func_query_first("SELECT paid_price,order_price,order_status FROM inv_orders WHERE order_id='".$invoice_id."'");
			// if((float)$check_for_paid['paid_price']==0.00)
			// {
				$db->db_exec("UPDATE inv_orders SET paid_price=paid_price+".$data['amount']." where order_id='".$invoice_id."'");	
			// }



			if(strtolower($check_for_paid['order_status'])=='on hold' && round($check_for_paid['order_price'],1)==round($data['amount'],1))
			{
			/*	$db->db_exec("UPDATE inv_orders SET order_status = 'Processed' WHERE order_id='".$invoice_id."'");

				$addcomment = array();
		$addcomment['date_added'] = date('Y-m-d H:i:s');
		$addcomment['user_id'] = '43';
		$addcomment['comment'] = 'Order payment mapped. Order Processed';
		$addcomment['order_id'] = $order['order_id'];
		$order_history_id = $db->func_array2insert("oc_order_history", $addcomment);*/

		
				// $db->db_exec("UPDATE oc_order SET order_status_id = '15' WHERE order_id='".$invoice_id."'");
			}

			if($data['is_mapped']==1 && $invoice_id)
			{


			addVoucher($invoice_id,'paypal',$data['amount'],$data['transaction_id']);

			$accounts = array();
					$accounts['description'] = 'Payment made #'.$data['transaction_id'].' @ '.$invoice_id;
					if($data['net_amount']>0)
					{
						$accounts['credit'] = 0.00;
						$accounts['debit'] = $data['net_amount'];
						
					}
					else
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $data['net_amount']*(-1);
					}
					$accounts['order_id'] = $invoice_id;
					$accounts['customer_email'] = $data['email'];
					$accounts['type']='paypal';
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = $data['order_date'];

					if(trim($data['order_id']) && trim($data['transaction_id']))
					{
						add_accounting_voucher($accounts); // Debit PayPal Account
						
					}


					$accounts = array();
					$accounts['description'] = 'Payment made #'.$data['transaction_id'].' @ '.$invoice_id;
					$accounts['customer_email'] = $data['email'];
					
					if($data['net_amount']>0)
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $data['net_amount'];
						
					}
					else
					{
						$accounts['debit'] = $data['net_amount']*(-1);
						$accounts['credit'] = 0.00;
					}


					$accounts['order_id'] = $invoice_id;
					$accounts['type'] = 'sales';
					$accounts['contra_account_code']='paypal';
					$accounts['date_added'] = $data['order_date'];

					if(trim($data['order_id']) && trim($data['transaction_id']))
					{
						add_accounting_voucher($accounts); // Debit PayPal Account
						
					}


					// PayPal Fee

					$accounts = array();
					$accounts['description'] = 'PayPal Fee @ '.$data['transaction_id'];
					if($data['transaction_fee']>0)
					{
					$accounts['credit'] = 0.00;
					$accounts['debit'] = $data['transaction_fee'];
					}
					else
					{
						$accounts['debit'] = 0.00;
					$accounts['credit'] = $data['transaction_fee']*(-1);
					}
					$accounts['order_id'] = $invoice_id;
					$accounts['customer_email'] = $data['email'];
					$accounts['type']='paypal_fee';
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = $data['order_date'];

					add_accounting_voucher($accounts); 


					$accounts = array();
					$accounts['description'] = 'Payment made @ '.$data['transaction_id'];
					if($data['transaction_fee']>0)
					{
					$accounts['debit'] = 0.00;
					$accounts['credit'] = $data['transaction_fee'];
					}
					else
					{
						$accounts['credit'] = 0.00;
					$accounts['debit'] = $data['transaction_fee']*(-1);
					}
					$accounts['order_id'] = $invoice_id;
					$accounts['customer_email'] = $data['email'];
					$accounts['type']='sales';
					$accounts['contra_account_code'] = 'paypal_fee';
					$accounts['date_added'] = $data['order_date'];

					add_accounting_voucher($accounts); 

					// End PayPal Fee


					$tax_check = $db->func_query_first_cell("SELECT tax from inv_orders where order_id='".$invoice_id."'");

					if($tax_check>0)
					{
						$tax_rate = $db->func_query_first("SELECT * FROM oc_tax_rate WHERE geo_zone_id=10");
						$tax_amount = ($data['amount']*(float)$tax_detail['rate'])/100;

					$accounts = array();
					$accounts['description'] = 'State Tax';
					if($tax_amount>0)
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $tax_amount;
						
					}
					else
					{
						$accounts['debit'] = $tax_amount*(-1);
						$accounts['credit'] = 0.00;
					}
					$accounts['order_id'] = $invoice_id;
					$accounts['customer_email'] = $data['email'];
					$accounts['type']='tax';
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = $data['order_date'];
					if(trim($data['order_id']) && trim($data['transaction_id']))
					{
						add_accounting_voucher($accounts); // Tax Account
						
					}


					$accounts = array();
					$accounts['description'] = 'State Tax';
					if($tax_amount>0)
					{
						$accounts['debit'] = $tax_amount;
						$accounts['credit'] = 0.00;
						
					}
					else
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $tax_amount*(-1);
					}
					$accounts['order_id'] = $invoice_id;
					$accounts['customer_email'] = $data['email'];
					$accounts['type']='sales';
					$accounts['contra_account_code'] = 'tax';
					$accounts['date_added'] = $data['order_date'];

					if(trim($data['order_id']) && trim($data['transaction_id']))
					{
						add_accounting_voucher($accounts); // Tax Account
						
					}

					}

			}
		}

		$is_missing = false;
						if($data['is_mapped']=='0')
						{
							$is_missing = true;
						}
						
						$missing_check = getOrder($data['order_id']);
						if($missing_check['order_id'])
						{
							$is_missing = false;
						}

						if($is_missing)
						{

							$missing_check = getOrder('E'.$data['order_id']);	
							if($missing_check['order_id'])
							{
								$data['order_id'] = 'E'.$data['order_id'];
								$is_missing = false;

							}

						}

						if($is_missing)
						{

							$missing_check = getOrder('RL'.$data['order_id']);
							if($missing_check['order_id'])
							{
								$data['order_id'] = 'RL'.$data['order_id'];
								$is_missing = false;
							}

						}

						if($is_missing==false)
						{
							$db->func_query("UPDATE inv_transactions SET is_mapped=1,order_id='".$data['order_id']."' WHERE transaction_id='".$data['transaction_id']."'");

							addVoucher($data['order_id'],'paypal',$data['amount'],$data['transaction_id']);

							$accounts = array();
					$accounts['description'] = 'Payment made #'.$data['transaction_id'].' @ '.$invoice_id;
					if($data['net_amount']>0)
					{
						$accounts['credit'] = 0.00;
						$accounts['debit'] = $data['net_amount'];
						
					}
					else
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $data['net_amount']*(-1);
					}
					$accounts['order_id'] = $invoice_id;
					$accounts['customer_email'] = $data['email'];
					$accounts['type']='paypal';
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = $data['order_date'];

					if(trim($data['order_id']) && trim($data['transaction_id']))
					{
						add_accounting_voucher($accounts); // Debit PayPal Account
						
					}



					$accounts = array();
					$accounts['description'] = 'Payment made #'.$data['transaction_id'].' @ '.$invoice_id;
					$accounts['customer_email'] = $data['email'];
					
					if($data['net_amount']>0)
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $data['net_amount'];
						
					}
					else
					{
						$accounts['debit'] = $data['net_amount']*(-1);
						$accounts['credit'] = 0.00;
					}


					$accounts['order_id'] = $invoice_id;
					$accounts['type'] = 'sales';
					$accounts['contra_account_code']='paypal';
					$accounts['date_added'] = $data['order_date'];

					if(trim($data['order_id']) && trim($data['transaction_id']))
					{
						add_accounting_voucher($accounts); // Debit PayPal Account
						
					}


					// PayPal Fee

					$accounts = array();
					$accounts['description'] = 'PayPal Fee @ '.$data['transaction_id'];
					if($data['transaction_fee']>0)
					{
					$accounts['credit'] = 0.00;
					$accounts['debit'] = $data['transaction_fee'];
					}
					else
					{
						$accounts['debit'] = 0.00;
					$accounts['credit'] = $data['transaction_fee']*(-1);
					}
					$accounts['order_id'] = $invoice_id;
					$accounts['customer_email'] = $data['email'];
					$accounts['type']='paypal_fee';
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = $data['order_date'];

					add_accounting_voucher($accounts); 


					$accounts = array();
					$accounts['description'] = 'Payment made @ '.$data['transaction_id'];
					if($data['transaction_fee']>0)
					{
					$accounts['debit'] = 0.00;
					$accounts['credit'] = $data['transaction_fee'];
					}
					else
					{
						$accounts['credit'] = 0.00;
					$accounts['debit'] = $data['transaction_fee']*(-1);
					}
					$accounts['order_id'] = $invoice_id;
					$accounts['customer_email'] = $data['email'];
					$accounts['type']='sales';
					$accounts['contra_account_code'] = 'paypal_fee';
					$accounts['date_added'] = $data['order_date'];

					add_accounting_voucher($accounts); 

					// End PayPal Fee


					$tax_check = $db->func_query_first_cell("SELECT tax from inv_orders where order_id='".$invoice_id."'");

					if($tax_check>0)
					{
						$tax_rate = $db->func_query_first("SELECT * FROM oc_tax_rate WHERE geo_zone_id=10");
						$tax_amount = ($data['amount']*(float)$tax_detail['rate'])/100;

					$accounts = array();
					$accounts['description'] = 'State Tax';
					if($tax_amount>0)
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $tax_amount;
						
					}
					else
					{
						$accounts['debit'] = $tax_amount*(-1);
						$accounts['credit'] = 0.00;
					}
					$accounts['order_id'] = $invoice_id;
					$accounts['customer_email'] = $data['email'];
					$accounts['type']='tax';
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = $data['order_date'];
					if(trim($data['order_id']) && trim($data['transaction_id']))
					{
						add_accounting_voucher($accounts); // Tax Account
						
					}


					$accounts = array();
					$accounts['description'] = 'State Tax';
					if($tax_amount>0)
					{
						$accounts['debit'] = $tax_amount;
						$accounts['credit'] = 0.00;
						
					}
					else
					{
						$accounts['debit'] = 0.00;
						$accounts['credit'] = $tax_amount*(-1);
					}
					$accounts['order_id'] = $invoice_id;
					$accounts['customer_email'] = $data['email'];
					$accounts['type']='sales';
					$accounts['contra_account_code'] = 'tax';
					$accounts['date_added'] = $data['order_date'];

					if(trim($data['order_id']) && trim($data['transaction_id']))
					{
						add_accounting_voucher($accounts); // Tax Account
						
					}

					}


						}



		$count--;
		$i++;
	}
}

$db->db_exec("Update inv_cron SET last_cron_time = '".$end_cron_date."' , status = 0 , last_run = '".date('Y-m-d H:i:s')."' where store_type = 'PPTransaction'");

echo "success";
exit;