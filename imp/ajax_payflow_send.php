<?php
require_once("auth.php");
require_once("inc/functions.php");

$order_id = $db->func_escape_string($_POST['orders']['order_id']);
$firstname = $db->func_escape_string($_POST['orders_details']['first_name']);
$lastname = $db->func_escape_string($_POST['orders_details']['last_name']);
$email = $db->func_escape_string(strtolower($_POST['orders']['email']));
$phone = $db->func_escape_string($_POST['orders_details']['phone_number']);
$address1 = $db->func_escape_string($_POST['orders_details']['address1']);
$city = $db->func_escape_string($_POST['orders_details']['city']);
$state = $db->func_escape_string($_POST['orders_details']['state']);
$zip = $db->func_escape_string($_POST['orders_details']['zip']);
$country = "United States";
$customer_group_id = $_POST['customer_group_id'];
$total = 0.00;
if(!(float)$_POST['total'])
{
	 echo json_encode(array('error'=>'Please provide a valid paying amount'));exit;
}
foreach($_POST['orders_items'] as $item)
{
	if($item['product_sku'])
	{
		//$unit_price = $db->func_query_first_cell("SELECT price FROM oc_product WHERE sku='".$db->func_escape_string($item['product_sku'])."'");
	//$total+= (float)$unit_price * (int) $item['product_qty'];
		$total+=(float)$item['product_price'];
	}
	
}
$total = ((float)$_POST['total']?$_POST['total']:$total);
if($_POST['orders_details']['shipping_cost'])
{
	if($zone_id=='3651')
	{
		$tax_detail = $db->func_query_first("SELECT * FROM oc_tax_rate WHERE geo_zone_id=10");
		$tax_amount = ($total*(float)$tax_detail['rate'])/100;
		$total = $total+(float)$tax_amount;
	}
	
	//$total = $total+(float)$_POST['orders_details']['shipping_cost']; // zaman disable
	$zone_id = $db->func_query_first_cell("SELECT zone_id FROM oc_zone WHERE name = '".$state."'");
	
	
}

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


if($_SESSION['user_id']=='87')
{
	$server='test'; // for sqa
}
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
			'AMT'       => (float)$total,
			'CURRENCY'  => 'USD',
			'FIRSTNAME' => $firstname,  // First name on card
			'LASTNAME'  => $lastname,  // Last name on card
			'STREET'    => $address1,  // Address verification (AVS)
			'INVNUM'    => $prefix_order_id,  // Address verification (AVS)
			'ZIP'       => $zip  // Address verification (AVS)

			//Fraud Protection Services
			,'CUSTIP'   => '0.0.0.0'
			,'CITY'     => $city
			,'STATE'    =>$state
			,'EMAIL'    => $email

			,'PHONENUM' => $phone
			,'BILLTOCOUNTRY'  => "US"
			);


$payment_data_array+=array(
	'SHIPTOSTREET' => $address1
	,'SHIPTOCITY'   => $city
	,'SHIPTOSTATE'  =>$state
	,'SHIPTOZIP'    => $zip
	,'SHIPTOCOUNTRY' 	=> "US"
	);

$_products = $db->func_query("SELECT * FROM inv_orders_items where order_id='".$_GET['order_id']."'");
$products_summary = array();
$total_products=0;
$index=0;
foreach($products as $_product)
{
	$products_summary[]= $product['product_qty'].'x'.$product['product_sku'];
	$total_products += $product['product_qty'];
			//Addtional FPS
	if ($product['product_price']>0) {
		$payment_data_array+=array(
			'L_COST'.$index => '$'.number_format($product['product_price'],2)
			,'L_QTY'.$index  => $product['product_qty']
			,'L_DESC'.$index  => $product['product_sku']
			);
		if (!empty($product['product_sku'])) {
			$payment_data_array['L_SKU'.$index ] = $product['sku'];
		}

	}
	$index++;



}
$products_summary = implode(',',$products_summary);


$payment_data = array();
foreach ($payment_data_array as $key => $value) {
	$payment_data[] = $key . '[' . strlen($value) . ']=' . $value;
}
$payment_data = implode('&', $payment_data);
if ($server == 'T') {
			// Remove sensitive data
	$payment_data_test = preg_replace('/(USER|VENDOR|PWD|ACCT|CVV2|EXPDATE)(\[[^\]]+\]=)([^&]*)/','$1$2xxxxx',$payment_data);

}
// echo $payment_data;exit;

        //$timeout added 1.5.2j

            $timeout=120;//default


            $headers = array();
            $headers[] = 'Content-Type: text/namevalue';
            $headers[] = 'Content-Length: ' . strlen($payment_data);
		$headers[] = 'X-VPS-Client-Timeout: '.$timeout;  // Should be less than cURL timeout.
		$headers[] = 'X-VPS-Request-ID: ' . $prefix_order_id . time();  // Unique ID to prevent duplicate requests. Append time to separate between multiple errors/requests on same shopping cart checkout attempt
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

		file_put_contents("cclog.log",json_encode($result)."====".$payment_data."\n");
		
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



			$db->db_exec("update inv_orders_details SET payment_method='Credit or Debit Card (Processed securely by PayPal)' where order_id = '$order_id'");
			$paid_price = $total;
			if ($paid_price) {
				$checkOld = $db->func_query_first_cell("SELECT paid_price FROM inv_orders WHERE order_id='$order_id'");

				if ($checkOld != '0.00') {
					$db->db_exec("UPDATE inv_orders SET paid_price=paid_price+$paid_price, payment_detail_2='". $_POST['cc_number'] ."' WHERE order_id='$order_id'");

					$hdata = array();
					$hdata['order_id'] = $order_id;
					$hdata['comment'] = 'Payment has been made for the amount of $' . number_format($paid_price, 2) . ' via "' . $payment_method . '" method';
					$hdata['user_id'] = $_SESSION['user_id'];
					$hdata['date_added'] = date('Y-m-d H:i:s');
					$db->func_array2insert("inv_order_history", $hdata);
				} else {
					$db->db_exec("UPDATE inv_orders SET paid_price=$paid_price, payment_detail_1='". $_POST['cc_number'] ."' WHERE order_id='$order_id'");

					$hdata = array();
					$hdata['order_id'] = $order_id;
					$hdata['comment'] = 'Payment has been made for the amount of $' . number_format($paid_price, 2) . ' via "' . $payment_method . '" method';
					$hdata['user_id'] = $_SESSION['user_id'];
					$hdata['date_added'] = date('Y-m-d H:i:s');
					$db->func_array2insert("inv_order_history", $hdata);
				}
				if ($response_data['AVSADDR'] == 'Y') {
					$is_address_verified = 'Confirmed';
				} else {
					$is_address_verified = 0;
				}
				
				$db->db_exec("UPDATE inv_orders SET auth_code='".$response_data['AUTHCODE']."', transaction_id='".$response_data['PPREF']."', avs_code='" . $response_data['AVSZIP'] . "',is_address_verified='" . $is_address_verified . "', payment_source='Payflow' WHERE order_id='$order_id'");


				addVoucher($order_id,'payflow',$paid_price,$response_data['PPREF']);


					$accounts = array();
					$accounts['description'] = 'Payment made @ '.$response_data['PPREF'];
					$accounts['credit'] = 0.00;
					$accounts['debit'] = $paid_price;
					$accounts['order_id'] = $order_id;
					$accounts['customer_email'] = $email;
					$accounts['type']='payflow';
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = date('Y-m-d H:i:s');

					add_accounting_voucher($accounts); // Debit PayPal Account


					$accounts = array();
					$accounts['description'] = 'Payment made @ '.$response_data['PPREF'];
					$accounts['customer_email'] = $email;
					$accounts['debit'] = 0.00;
					$accounts['credit'] = $paid_price;

					$accounts['order_id'] = $order_id;
					$accounts['type'] = 'sales';
					$accounts['contra_account_code']='payflow';
					$accounts['date_added'] = date('Y-m-d H:i:s');

					add_accounting_voucher($accounts); // Credit Sales Account

					$tax_check = $db->func_query_first_cell("SELECT tax from inv_orders where order_id='".$order_id."'");

					if($tax_check>0)
					{
						$tax_rate = $db->func_query_first("SELECT * FROM oc_tax_rate WHERE geo_zone_id=10");
						$tax_amount = ($paid_price*(float)$tax_detail['rate'])/100;

					$accounts = array();
					$accounts['description'] = 'State Tax';
					$accounts['debit'] = 0.00;
					$accounts['credit'] = $tax_amount;
					$accounts['order_id'] = $order_id;
					$accounts['customer_email'] = $email;
					$accounts['type']='tax';
					$accounts['contra_account_code'] = 'sales';
					$accounts['date_added'] = date('Y-m-d H:i:s');

					add_accounting_voucher($accounts); // Tax Account


					$accounts = array();
					$accounts['description'] = 'State Tax';
					$accounts['credit'] = 0.00;
					$accounts['debit'] = $tax_amount;
					$accounts['order_id'] = $order_id;
					$accounts['customer_email'] = $email;
					$accounts['type']='sales';
					$accounts['contra_account_code'] = 'tax';
					$accounts['date_added'] = date('Y-m-d H:i:s');

					add_accounting_voucher($accounts); // Tax Account

					}


					
			}

			

			switch($response_data['RESULT']){
				case '104':
            //case '1000'://Timeout
				$json['success'] = $response_data['RESPMSG']." login to your payflow manger to verify if this transaction is processed sucessfully or not.";
				
				break;
            case '126'://FPS Review
            $json['success'] = $response_data['RESPMSG']." login to your payflow manger to verify if this transaction (Reports > Fraud Protection > Fraud Transactions)";

            break;
            default://success
            $json['success'] = 'Payment is processed successfully!';
            break;
        }
            //log transaction results


        $ppat_order_query = $db->func_query_first_cell("SELECT order_id from oc_payflow_admin_tools where order_id = '" . $order_id . "'");
        if (!$ppat_order_query) {
        	$db->db_exec("INSERT INTO oc_payflow_admin_tools SET `order_id` = '" . $order_id . "',transaction_id = '" . $response_data['PNREF'] . "', `pp_transaction_id`='".$response_data['PPREF']."', `authorization_id`='".$response_data['AUTHCODE']."', `avsaddr`='". $response_data['AVSADDR'] ."', `avszip`='" . $response_data['AVSZIP'] . "', `cvv2match`='" . $response_data['CVV2MATCH'] . "',amount='".(float)$paid_price."'");
        	$db->db_exec("UPDATE oc_order SET payment_code = 'pp_payflow_pro' WHERE order_id='$order_id'");
        	$db->db_exec("UPDATE inv_transactions SET order_id='".$order_id."',is_mapped=1 WHERE transaction_id='".$response_data['PPREF']."'");
        } else {
        	$db->db_exec("UPDATE oc_payflow_admin_tools SET `order_id` = '" . $order_id . "',transaction_id = '" . $response_data['PNREF'] . "', `transaction_id`='".$response_data['PPREF']."', `authorization_id`='".$response_data['AUTHCODE']."', `avsaddr`='". $response_data['AVSADDR'] ."', `avszip`='" . $response_data['AVSZIP'] . "', `cvv2match`='" . $response_data['CVV2MATCH'] . "',amount='".(float)$paid_price."' WHERE order_id='".$order_id."'");
        	$db->db_exec("UPDATE oc_order SET payment_code = 'pp_payflow_pro' WHERE order_id='$order_id'");
        	$db->db_exec("UPDATE inv_transactions SET order_id='".$order_id."',is_mapped=1 WHERE transaction_id='".$response_data['PPREF']."'");
        }

        $log = 'Amount of <strong>$'. $total .'</strong> was paid for Order # '. linkToOrder($order_id) .'<br> Payment Response: '. $response_data['RESPMSG'] .'"<br> and Authcode: "'. $response_data['AUTHCODE'] .'"';
        actionLog($log);

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
