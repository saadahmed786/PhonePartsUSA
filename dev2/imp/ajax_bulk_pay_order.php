<?php
require_once("auth.php");
require_once("inc/functions.php");

$order_id = $db->func_escape_string($_POST['orders']['order_ids']);
$firstname = $db->func_escape_string($_POST['orders_details']['first_name']);
$lastname = $db->func_escape_string($_POST['orders_details']['last_name']);
$email = $db->func_escape_string($_POST['orders']['email']);
$phone = $db->func_escape_string($_POST['orders_details']['phone_number']);
$address1 = $db->func_escape_string($_POST['orders_details']['address1']);
$city = $db->func_escape_string($_POST['orders_details']['city']);
$state = $db->func_escape_string($_POST['orders_details']['state']);
$zip = $db->func_escape_string($_POST['orders_details']['zip']);
$country = "United States";
$customer_group_id = $_POST['customer_group_id'];
$total = $db->func_escape_string($_POST['total']);

// Create OpenCart Order if Left Blank




$server = $db->func_query_first_cell("SELECT `value` FROM oc_setting WHERE `key`='pp_payflow_pro_server'");
if($server=='L')
{
	$server='live';	
}
else
{
	$server='test';	
}
$partner = $db->func_query_first_cell("SELECT `value` FROM oc_setting WHERE `key`='pp_payflow_pro_partner'");
$vendor = $db->func_query_first_cell("SELECT `value` FROM oc_setting WHERE `key`='pp_payflow_pro_vendor'");
$user = $db->func_query_first_cell("SELECT `value` FROM oc_setting WHERE `key`='pp_payflow_pro_username'");
$password = $db->func_query_first_cell("SELECT `value` FROM oc_setting WHERE `key`='pp_payflow_pro_password'");
$transaction = $db->func_query_first_cell("SELECT `value` FROM oc_setting WHERE `key`='pp_payflow_pro_transaction'");
$prefix = $db->func_query_first_cell("SELECT `value` FROM oc_setting WHERE `key`='pp_payflow_pro_idprefix'");

if ($server == 'test') {
			$api_endpoint = 'https://pilot-payflowpro.paypal.com'; // Test server
			
		} else {
			$api_endpoint = 'https://payflowpro.paypal.com'; // Live server
			
		}

		if ($transaction == 'A') {
			$transaction_type = 'A';  // Authorization only

		} else {
			$transaction_type = 'S';  // Sale

		}
		
		
		
		if (!isset($order_id)) {
			$json = array();
			$json['error'] = 'No order id found';
			echo(json_encode($json));

			return;
		}
		
		$prefix_order_id = $prefix . $order_id;
		
		$payment_data_array = array(
			'PARTNER'   => html_entity_decode($partner, ENT_QUOTES, 'UTF-8'),
			'VENDOR'    => html_entity_decode($vendor, ENT_QUOTES, 'UTF-8'),
			'USER'      => html_entity_decode($user, ENT_QUOTES, 'UTF-8'),
			'PWD'       => html_entity_decode($password, ENT_QUOTES, 'UTF-8'),
			'TRXTYPE'   => $transaction_type,
			'TENDER'    => 'C',  // C = Credit Card
			'ACCT'      => preg_replace('/[^0-9]/', '', $_POST['cc_number']),  // Card number
			'CVV2'      => preg_replace('/[^0-9]/', '', $_POST['cc_cvv2']),  // CVV2 card verification number
			'EXPDATE'   => $_POST['cc_expire_date_month'] . $_POST['cc_expire_date_year'],  // Card expiration date
			'AMT'       => '$'.number_format($total,2),
			'CURRENCY'  => 'USD',
			'FIRSTNAME' => $firstname,  // First name on card
			'LASTNAME'  => $lastname,  // Last name on card
			'STREET'    => $address1,  // Address verification (AVS)
			'ZIP'       => $zip  // Address verification (AVS)

			//Fraud Protection Services
			,'CUSTIP'   => '0.0.0.0'
			,'CITY'     => $city
			,'STATE'    =>$state
			,'EMAIL'    => $email

			,'PHONENUM' => $phone
			,'BILLTOCOUNTRY'  => "USA"
			);


$payment_data_array+=array(
	'SHIPTOSTREET' => $address1
	,'SHIPTOCITY'   => $city
	,'SHIPTOSTATE'  =>$state
	,'SHIPTOZIP'    => $zip
	,'SHIPTOCOUNTRY' 	=> "USA"
	);


$payment_data = array();
foreach ($payment_data_array as $key => $value) {
	$payment_data[] = $key . '[' . strlen($value) . ']=' . $value;
}
$payment_data = implode('&', $payment_data);
if ($server == 'T') {
			// Remove sensitive data
	$payment_data_test = preg_replace('/(USER|VENDOR|PWD|ACCT|CVV2|EXPDATE)(\[[^\]]+\]=)([^&]*)/','$1$2xxxxx',$payment_data);

}


$timeout=120;


$headers = array();
$headers[] = 'Content-Type: text/namevalue';
$headers[] = 'Content-Length: ' . strlen($payment_data);
$headers[] = 'X-VPS-Client-Timeout: '.$timeout; 
$headers[] = 'X-VPS-Request-ID: ' . $prefix_order_id . time();
		// Optional headers.
$headers[] = 'X-VPS-VIT-Integration-Product: OpenCart.com with PPPayflowPro Extension';
if (defined('VERSION'))
	$headers[] = 'X-VPS-VIT-Integration-Version: '.VERSION;

$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $api_endpoint);
curl_setopt($curl, CURLOPT_PORT, 443);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
curl_setopt($curl, CURLOPT_HEADER, 1);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_TIMEOUT, $timeout+10);
curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($curl, CURLOPT_POSTFIELDS, $payment_data);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
curl_setopt($curl, CURLOPT_POST, 1);

$result = curl_exec($curl);

$headers = curl_getinfo($curl);
$json = array();
if ($result === false)  {
	$json['error'] = 'RESULT=-10000&RESPMSG=Unable to connect to the payment gateway to process the transaction.';

} else if ($headers['http_code'] != 200) {
	$json['error'] = 'RESULT=-10001&RESPMSG=HTTP 200 Response expected.  Received ' . $headers['http_code'] . '.';

}

curl_close($curl);

$result = strstr($result, 'RESULT');
$result = explode('&', $result);
$response_data = array();
foreach ($result as $temp) {
	$pos = strpos($temp, '=');
	if ($pos !== false)  $response_data[substr($temp, 0, $pos)] = substr($temp, $pos + 1);
}


        //check for a 1000: Generic processor error: 10001 - Timeout processing request error and route as a 104
if (isset($response_data['RESPMSG']) && $response_data['RESULT']=='1000' && preg_match('/10001.*Timeout/',$response_data['RESPMSG'])) {
	$response_data['RESULT']='104';
}


if (in_array($response_data['RESULT'],array('0','126','104'))) {

	if ($response_data['AVSADDR'] == 'Y') {
		$is_address_verified = 'Confirmed';
	} else {
		$is_address_verified = 0;
	}

	$order_ids = explode(',', $order_id);
	
	foreach ($order_ids as $key => $o_id) {
		// Get order details
		$order = $db->func_query_first_cell("SELECT * from inv_orders where order_id = '$o_id'");

		//Update order details in imp.
		$db->db_exec("UPDATE inv_orders SET paid_price=order_price, auth_code='".$response_data['AUTHCODE']."', transaction_id='".$response_data['PPREF']."', avs_code='" . $response_data['AVSZIP'] . "',is_address_verified='" . $is_address_verified . "', payment_source='Payflow', payment_detail_1 = '". $_POST['cc_number'] ."' where order_id = '$o_id'");

		// Set payment method in IMP
		$db->db_exec("UPDATE inv_orders_details SET payment_method='Credit or Debit Card (Processed securely by PayPal)' where order_id = '$o_id'");

		// Update payment details if order is web

		if ($order['store_type'] == 'web') {
			if ($db->func_query_first_cell("SELECT order_id from oc_payflow_admin_tools where order_id = '" . $o_id . "'")) {
				$db->db_exec("UPDATE oc_payflow_admin_tools SET transaction_id='". $response_data['PPREF'] ."', order_id = '$o_id', amount='". $_POST['amount'] ."'");
			} else {
				$db->db_exec("INSERT INTO oc_payflow_admin_tools SET `order_id` = '" . $o_id . "', `pp_transaction_id`='".$response_data['PPREF']."', `authorization_id`='".$response_data['AUTHCODE']."', `avsaddr`='". $response_data['AVSADDR'] ."', `avszip`='" . $response_data['AVSZIP'] . "', `cvv2match`='" . $response_data['CVV2MATCH'] . "'");
			}

			$db->db_exec("UPDATE oc_order SET payment_code = 'pp_payflow_pro' WHERE order_id='$o_id'");
		}

		// if Order more than one
		$ifexist = $db->func_query_first_cell("SELECT id FROM inv_transactions WHERE transaction_id = '". $response_data['PPREF'] ."'");
		if ($key == 0) {
			if (count($order_ids) == 1) {
				if ($ifexist) {
					$t_q = "UPDATE inv_transactions "
					."SET transaction_id = '". $response_data['PPREF'] ."', "
					."order_id = '$o_id', "
					."is_mapped = '1', "
					."date_added = '". date('Y-m-d H:i:s') ."' "
					."WHERE transaction_id = '". $response_data['PPREF'] ."'";
				} else {
					$t_q = "INSERT INTO inv_transactions "
					."SET transaction_id = '". $response_data['PPREF'] ."', "
					."order_id = '$o_id', "
					."is_mapped = '1', "
					."date_added = '". date('Y-m-d H:i:s') ."'";
				}
				$multi = false;
			} else {
				if ($ifexist) {
					$t_q = "UPDATE inv_transactions "
					."SET transaction_id = '". $response_data['PPREF'] ."', "
					."order_id = '$o_id', "
					."is_mapped = '1', "
					."is_multi = '1', "
					."date_added = '". date('Y-m-d H:i:s') ."' "
					."WHERE transaction_id = '". $response_data['PPREF'] ."'";
				} else {
					$t_q = "INSERT INTO inv_transactions "
					."SET transaction_id = '". $response_data['PPREF'] ."', "
					."order_id = '$o_id', "
					."is_mapped = '1', "
					."is_multi = '1', "
					."date_added = '". date('Y-m-d H:i:s') ."'";
				}

			}
			$multi = true;
		}
		

		$db->db_exec($t_q);
		if ($multi) {
			$db->db_exec("INSERT into inv_transactions_multi SET transaction_id='". $response_data['PPREF'] ."', order_id = '$o_id'");
		}


		// Adding Order payment history

		$hdata = array();
		$hdata['order_id'] = $o_id;
		$hdata['comment'] = 'Payment has been made for the amount of $' . number_format($paid_price, 2) . ' via "' . $payment_method . '" method';
		$hdata['user_id'] = $_SESSION['user_id'];
		$hdata['date_added'] = date('Y-m-d H:i:s');
		$db->func_array2insert("inv_order_history", $hdata);

		$orders[] = linkToOrder($o_id);

	}



            //log transaction results

	$log = 'Amount of <strong>$'. $total .'</strong> was paid for Order # '. implode(', ', $orders) .'<br> Payment Response: "'. $response_data['RESPMSG'] .'"<br> and Authcode: "'. $response_data['AUTHCODE'] .'"';
	actionLog($log);

	switch($response_data['RESULT']){
		case '104':
            //case '1000'://Timeout
		$json['success'] = $response_data['RESPMSG']." login to your payflow manger to verify if this transaction is processed sucessfully or not.";

		break;
            //FPS Review
		case '126':
		$json['success'] = $response_data['RESPMSG']." login to your payflow manger to verify if this transaction (Reports > Fraud Protection > Fraud Transactions)";

		break;
            //success
		default:
		$json['success'] = 'Payment is processed successfully!';
		break;
	}

} else {

	if (in_array($response_data['RESULT'], array('1','3','4','5','6','7','8','9','10','26','27','28'))){
				//Error messages relating more so to merchant not customer
		print_r($response_data);exit;
		$json['error'] = 'Unexpected Error occured!';


	}elseif (in_array($response_data['RESULT'], array('125','128'))){
				//Fraud Protection Services Filter Declines - simply show as declined
		$json['error'] = 'Fraud Detected, Payment declined';
	}else{

				//All other messages
		$json['error'] = $response_data['RESPMSG'];
	}
}
echo json_encode($json);
?>
