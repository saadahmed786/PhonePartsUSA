<?php
set_time_limit(500);
include_once '../config.php';
include_once '../inc/functions.php';

$data = array();
$data['firstname'] = $db->func_escape_string($_POST['first_name']);
$data['lastname'] = $db->func_escape_string($_POST['last_name']);
$data['email'] = $db->func_escape_string($_POST['payer_email']);
$data['receiver_email'] = $db->func_escape_string($_POST['receiver_email']);
$data['transaction_id'] = $db->func_escape_string($_POST['txn_id']);
$data['order_id'] = $db->func_escape_string($_POST['invoice']);
$data['amount'] = (float)$_POST['payment_gross'];
$data['transaction_fee'] = (float)$_POST['payment_fee'];
$data['net_amount'] = (float)$_POST['payment_gross']-(float)$_POST['payment_fee'];
$data['order_status'] = 'Completed';
$data['payment_status'] = $db->func_escape_string($_POST['payment_status']);
$data['order_date'] = date('Y-m-d H:i:s',strtotime(str_replace(" PDT", "", $_POST['payment_date'])));
$data['order_source'] = 'PayPal';

if(!$data['transaction_id'])
{
	echo 'No Record Found';exit;
}

$map_check = $db->func_query_first("SELECT transaction_id FROM inv_transactions where transaction_id='".$data['transaction_id']."' and order_id<>'' and is_mapped=1");

if($map_check)
		{
				file_put_contents("paypal_ipn.txt","Transaction ID Already Mapped: ".$map_check['transaction_id']."\n",FILE_APPEND);
				exit;
		}

		$isExist = $db->func_query_first("select id from inv_orders where order_id = '".$data['order_id']."'");
		if(!$isExist){
			$data['is_mapped']  = 0;
			if($data['payment_status'] == 'Completed'){
				//check if this is processed in oc table too
				$oc_exist = $db->func_query_first("select order_id from oc_order where cast(order_id as char(50)) = '".$data['order_id']."' and order_status_id>0 ");
				if($oc_exist){
					$data['is_mapped']  = 1;
				}
				else
				{
					$data['is_mapped']=0;
				}
			}
		}
		else{
			$data['is_mapped'] = 1;
		}

		if ($data['is_mapped'] == 0) {
			$data['order_id'] = $db->func_query_first_cell("SELECT order_id FROM inv_orders WHERE DATE(order_date) = '" . date('Y-m-d',strtotime($order_date)) . "' AND (order_price - paid_price) = '". $data['amount'] ."' AND email = '". $data['email'] ."'");
			if (!$data['order_id']) {
				$data['is_mapped'] = 0;
				$data['order_id'] = $db->func_query_first_cell("SELECT order_id FROM oc_order WHERE DATE(date_added) = '" . date('Y-m-d',strtotime($data['order_date'])) . "' AND cast(total as decimal(14,2)) = '". $data['amount'] ."' AND email = '". $data['email'] ."'");
				if ($data['order_id']) {
					
					$data['is_mapped'] = 1;
				}
			} else {
				
				$data['is_mapped'] = 1;
				
			}
		}

		$check = $db->func_query_first("SELECT transaction_id FROM inv_transactions WHERE transaction_id='".$data['transaction_id']."'");
		$data['response_data'] = $db->func_escape_string(urlencode('protection_eligibility='.$_POST['protection_eligibility'].'&address_status='.$_POST['address_status'].'&first_name='.$_POST['first_name'].'&last_name='.$_POST['last_name'].'&payer_business_name='.$_POST['payer_business_name'].'&address_name='.$_POST['address_name'].'&address_street='.$_POST['address_street'].'&address_city='.$_POST['address_city'].'&address_state='.$_POST['address_state'].'&address_country='.$_POST['address_country'].'&address_zip='.$_POST['address_zip']));

		if($check)
		{
			$db->func_array2update("inv_transactions",$data," transaction_id = '$transactionId' ");
		}
		else
		{
			$data['date_added'] = date("Y-m-d H:i:s");
			$db->func_array2insert("inv_transactions",$data);
			if($data['is_mapped']==1 and $data['order_id'])
			{
				$check_for_paid = $db->func_query_first("SELECT paid_price,order_price,order_status FROM inv_orders WHERE order_id='".$data['order_id']."'");
			if((float)$check_for_paid['paid_price']==0.00) {
					$db->db_exec("UPDATE inv_orders SET paid_price=".$data['amount']." where order_id='".$data['order_id']."'");	
				} else if ( ($check_for_paid['order_price'] - $check_for_paid['paid_price']) == $data['amount']) {
					$db->db_exec("UPDATE inv_orders SET paid_price=paid_price+".$data['amount']." where order_id='".$data['order_id']."'");	
				}

				$_paid_price = $db->func_query_first_cell("SELECT paid_price FROM inv_orders WHERE order_id='".$data['order_id']."'");

				if((strtolower($check_for_paid['order_status'])=='on hold' || strtolower($check_for_paid['order_status'])=='estimate') && round($_paid_price,1)==round($data['amount'],1))
			{
				//$db->db_exec("UPDATE inv_orders SET order_status = 'Processed' WHERE order_id='".$data['order_id']."'");
				//$db->db_exec("UPDATE oc_order SET order_status_id = '15' WHERE cast(order_id as char(50))='".$data['order_id']."' OR ref_order_id='".$data['order_id']."'");

				$addcomment = array();
		// $addcomment['date_added'] = date('Y-m-d H:i:s');
		// $addcomment['user_id'] = '43';
		// $addcomment['comment'] = 'Order payment mapped. Order Processed';
		// $addcomment['order_id'] = $order['order_id'];
		// $order_history_id = $db->func_array2insert("oc_order_history", $addcomment);
		


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
						}

						file_put_contents("paypal_ipn.txt","Transaction ID Done Mapping: ".$data['transaction_id'].", Order ID: ".$data['order_id']."\n",FILE_APPEND);
		

?>