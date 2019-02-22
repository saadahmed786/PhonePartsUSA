<?php

include("phpmailer/class.smtp.php");
include("phpmailer/class.phpmailer.php");

require_once("auth.php");
require_once("inc/functions.php");

$paypal_check = $db->func_query_first("SELECT payment_code FROM oc_order WHERE order_id='" . $_POST['order_id'] . "' AND LOWER(payment_method) IN('paypal express','paypal')");

$auth_check = $db->func_query_first("SELECT payment_code FROM oc_order WHERE order_id='" . $_POST['order_id'] . "' AND payment_code IN('authorizenet_aim','authorizenet_cim')");

$payflow_check = $db->func_query_first("SELECT payment_code FROM oc_order WHERE order_id='" . $_POST['order_id'] . "' AND payment_code IN('pp_payflow_pro')");
$check = $db->func_query_first("SELECT o.*,od.* FROM inv_orders o,inv_orders_details od WHERE o.order_id=od.order_id AND  o.order_id='".$db->func_escape_string($_POST['order_id'])."'");
$_SESSION['email_info'][$_POST['order_id']]['total_formatted'] = $_POST['amount'];
// print_r( $payflow_check );exit;
if ($paypal_check) {
	paypal_refund();
}
if ($check['store_type'] == 'bigcommerce') {
	if ($check['payment_source'] == 'PayPal') {
		paypal_refund();
	}
}
// if ($auth_check) {
// 	authnet_refund();
// }

if($payflow_check['payment_code']=='pp_payflow_pro')
{
	payflow_refund();	
	
}
if(!$paypal_check && !$payflow_refund and $check['store_type']!='bigcommerce')
{
	$transaction_check = $db->func_query_first("SELECT * from inv_transactions where order_id='".$_POST['order_id']."' and order_status='Completed' and payment_status='Completed' and is_mapped=1");

	if(!$transaction_check)
	{
		$json = array();
		$json['error'] = 'Order not mapped properly, please try again or contact Web Administrator';
		echo json_encode($json);exit;
	}
	else
	{
		if(strtolower($check['payment_method'])=='paypal')
		{
			paypal_refund($transaction_check['transaction_id']);
		}

		if(strtolower($check['payment_method'])=='card')
		{
			payflow_refund($transaction_check['transaction_id']);
		}
	}

}

function payflow_refund($_transaction_id=''){
	global $db;

	if(isset($_REQUEST['remove_tax']) && $_REQUEST['remove_tax']=1)
{
    $remove_tax = true;
}
else
{
    $remove_tax = false;
}

	$json = array();
	$json['error'] = false;


	$user = $db->func_query_first_cell("SELECT value FROM oc_setting WHERE `key`='pp_payflow_pro_username'");
	$password = $db->func_query_first_cell("SELECT value FROM oc_setting WHERE `key`='pp_payflow_pro_password'");
	$vendor = $db->func_query_first_cell("SELECT value FROM oc_setting WHERE `key`='pp_payflow_pro_vendor'");
	$server = $db->func_query_first_cell("SELECT value FROM oc_setting WHERE `key`='pp_payflow_pro_server'");
// Reseller who registered you for Payflow or 'PayPal' if you registered
// directly with PayPal
	$partner = $db->func_query_first_cell("SELECT value FROM oc_setting WHERE `key`='pp_payflow_pro_partner'");
	if($server=='L')
	{
		$sandbox = false;
	}
	else
	{
		$sandbox = true;
	}

	$order_info = $db->func_query_first("SELECT * FROM oc_payflow_admin_tools at LEFT JOIN `oc_order` o ON (at.order_id = o.order_id) WHERE at.order_id = '" . $_POST['order_id'] . "'");
	if (!$order_info) {
		$order_info = $db->func_query_first("SELECT * FROM inv_orders WHERE order_id = '" . $_POST['order_id'] . "'");
	}

	if (!$order_info) {
		$json['error'] = 'Error: Order data not found';
	} else {
		if($_transaction_id=='')
		{
		$transactionID = $order_info['transaction_id'];
			
		}
		else
		{
			$transactionID = $_transaction_id;
		}
		$currency = 'USD';
		$amount = $_POST['amount'];
	}
	// echo $transactionID;exit;
	// print_r($order_info);exit;

	$url = 'https://payflowpro.paypal.com';

	$params = array(
		'USER' => $user,
		'VENDOR' => $vendor,
		'INVNUM' => $_POST['order_id'],
		'PARTNER' => $partner,
		'PWD' => $password,
		'TENDER' => 'C', // C = credit card, P = PayPal
		'TRXTYPE' => 'C', //  S=Sale, A= Auth, C=Credit, D=Delayed Capture, V=Void                        
		'ORIGID' => $transactionID,
		'AMT' => $amount,
		'CURRENCY' => $currency
		);

	$data = '';
	$i = 0;
	foreach ($params as $n=>$v) {
		$data .= ($i++ > 0 ? '&' : '') . "$n=" . urlencode($v);
	}

	$headers = array();
	$headers[] = 'Content-Type: application/x-www-form-urlencoded';
	$headers[] = 'Content-Length: ' . strlen($data);

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_HEADER, $headers);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	$result = curl_exec($ch);

	curl_close($ch);

// Parse results
	$response = array();
	$result = strstr($result, 'RESULT');    
	$valArray = explode('&', $result);
	foreach ($valArray as $val) {
		$valArray2 = explode('=', $val);
		$response[$valArray2[0]] = $valArray2[1];
	}

	// print_r($response);exit;

	if (isset($response['RESULT']) && $response['RESULT'] == 0) {
		//sendEmail($_POST['order_id'], $_POST['return_id'], $_POST['items']);
		$json['success'] = ('$'. $_POST['amount'] .' Refund Issued via Pay Flow.');
		
		//$json['response'] = $response;

		if(!$remove_tax)
		{
		if (!$_POST['items']) {
			$comment = 'Order #'. linkToOrder($_POST['order_id']) .' canceled and refunded.';

			$addReport = array(
				'order_id'  =>  $_POST['order_id'],
				'reason_id' =>  $_POST['reason'],
				'order_amount'    =>  $_POST['amount'],
				'user_id'   =>  $_SESSION['user_id'],
				'date_added'=>  date('Y-m-d H:i:s')
				);
			$cancel_id = $db->func_array2insert("inv_order_cancel_report", $addReport);
			unset($addReport);

			$skus = $db->func_query("SELECT * FROM `inv_orders_items` WHERE `order_id` = '". $_POST['order_id'] ."'");
			foreach ($skus as $xsk) {
				$addReport = array(
					'cancel_id'  =>  $cancel_id,
					'sku'       =>  $xsk['product_sku'],
					'amount'    =>  $xsk['product_price'],
					'action'   =>  'Order Canceled',
					'date_added'=>  date('Y-m-d H:i:s')
					);
				$db->func_array2insert("inv_product_cancel_report", $addReport);
				unset($addReport);
			}
		} else {

			$addReport = array(
				'order_id'  =>  $_POST['order_id'],
				'reason_id' =>  $_POST['reason'],
				'order_amount'    =>  $_POST['amount'],
				'user_id'   =>  $_SESSION['user_id'],
				'date_added'=>  date('Y-m-d H:i:s')
				);
			$cancel_id = $db->func_array2insert("inv_order_cancel_report", $addReport);
			unset($addReport);

			$skus = $db->func_query("SELECT * FROM `inv_orders_items` WHERE `id` IN ('" . str_replace(',', "', '", $_POST['items']) . "')");
			foreach ($skus as $xsk) {
				$sku .= ' ' . linkToProduct($xsk['product_sku']);

				$addReport = array(
					'cancel_id'  =>  $cancel_id,
					'sku'       =>  $xsk['product_sku'],
					'amount'    =>  $xsk['product_price'],
					'action'   =>  'Item Removed',
					'date_added'=>  date('Y-m-d H:i:s')
					);
				$order_history_id = $db->func_array2insert("inv_product_cancel_report", $addReport);
				unset($addReport);
			}
			$comment = 'Product(s) '. $sku .' removed from Order #'. linkToOrder($_POST['order_id']) .' and refunded.';
		}
	}

	if(!$remove_tax)
	{

		$db->func_query('UPDATE `inv_orders` SET `paid_price` = (`paid_price` - "'. $_POST['amount'] .'"),refunded_amount= "'. (float)$_POST['amount'] .'" WHERE order_id = "'. $_POST['order_id'] .'"');

		// addVoucher($_POST['order_id'],'payflow',$_POST['amount']*(-1));
	}
	else
	{
		$db->func_query('UPDATE `inv_orders` SET `paid_price` = (`paid_price` - "'. $_POST['amount'] .'"),refund_tax= "'. (float)$_POST['amount'] .'",refund_tax_type="payflow",refund_tax_date="'.date('Y-m-d H:i:s').'" WHERE order_id = "'. $_POST['order_id'] .'"');	
		$comment = 'Tax Removed and Refunded';

		// addVoucher($_POST['order_id'],'tax',$_POST['amount']*(-1));
	}
		sendEmail1($_POST['order_id'], $_POST['return_id'], $_POST['items']);
		$addcomment = array();
		$addcomment['date_added'] = date('Y-m-d H:i:s');
		$addcomment['user_id'] = $_SESSION['user_id'];
		$addcomment['comment'] = $db->func_escape_string($comment);
		$addcomment['order_id'] = $_POST['order_id'];
		$order_history_id = $db->func_array2insert("inv_order_history", $addcomment);
		actionLog($comment);

		$i = 0;
	} else {
		$json['error'] = 'Unable to refund, please try again';

	}	
	echo json_encode($json);
	exit;
}


function paypal_refund($_transaction_id='') {
	global $db;

	$json = array();
	$json['error'] = false;


    //$this->load->language('module/' . $classname);



	$ppat_api_user = $db->func_query_first("SELECT value FROM oc_setting WHERE `key`='paypal_admin_tools_ppat_api_user'");
	$ppat_api_user = $ppat_api_user['value'];

	$ppat_api_pass = $db->func_query_first("SELECT value FROM oc_setting WHERE `key`='paypal_admin_tools_ppat_api_pass'");
	$ppat_api_pass = $ppat_api_pass['value'];

	$ppat_api_sig = $db->func_query_first("SELECT value FROM oc_setting WHERE `key`='paypal_admin_tools_ppat_api_sig'");
	$ppat_api_sig = $ppat_api_sig['value'];


	$ppat_api_env = $db->func_query_first("SELECT value FROM oc_setting WHERE `key`='paypal_admin_tools_ppat_env'");
	$ppat_api_env = $ppat_api_env['value'];

	if (!$json['error']) {
        // Save API details to settings db
        /* $savefields = array('ppat_api_user', 'ppat_api_pass', 'ppat_api_sig', 'ppat_env');
          $savearr = array();
          foreach ($this->request->post as $key => $value) {
          if (in_array($key, $savefields)) {
          $savearr['paypal_admin_tools_' . $key] = $value;
          }
          }
          $this->load->model('setting/setting');
          $this->model_setting_setting->editSetting($classname, $savearr); */
        //
          if(isset($_REQUEST['remove_tax']) && $_REQUEST['remove_tax']=1)
{
    $remove_tax = true;
}
else
{
    $remove_tax = false;
}
          $query = $db->func_query_first("SELECT * FROM oc_paypal_admin_tools at LEFT JOIN `oc_order` o ON (at.order_id = o.order_id) WHERE at.order_id = '" . $_POST['order_id'] . "'");
          if (!$query) {
          	$query = $db->func_query_first("SELECT * FROM inv_orders WHERE order_id = '" . $_POST['order_id'] . "'");
          }
          if($query['payment_code']=='paypal_express_new' or $query['payment_code']=='pp_standard_new' or strtolower($query['store_type']) == strtolower('bigcommerce') or $_transaction_id!='')
          {

          	$ppat_api_user = $db->func_query_first("SELECT value FROM oc_setting WHERE `key`='paypal_express_new_apiuser'");
          	$ppat_api_user = $ppat_api_user['value'];

          	$ppat_api_pass = $db->func_query_first("SELECT value FROM oc_setting WHERE `key`='paypal_express_new_apipass'");
          	$ppat_api_pass = $ppat_api_pass['value'];

          	$ppat_api_sig = $db->func_query_first("SELECT value FROM oc_setting WHERE `key`='paypal_express_new_apisig'");
          	$ppat_api_sig = $ppat_api_sig['value'];


          	$ppat_api_env = $db->func_query_first("SELECT value FROM oc_setting WHERE `key`='paypal_express_new_test'");
          	$ppat_api_env = $ppat_api_env['value'];	
          	$ppat_api_env = ($ppat_api_env == 0 ? 'live' : 'sandbox');

          	if ($ppat_api_env == 'live' && strtolower($query['store_type']) == strtolower('bigcommerce')) {
          		$ppat_api_user = 'admin_api1.replacementlcds.com';
          		$ppat_api_pass = 'RYV6DNWNNLVSY5BP';
          		$ppat_api_sig = 'AKDJMrcfZ1rLAY1K5iKwGm86PLbiABK1CxVKkOQqmclTR72aK8GJDvEW';
          	}
          }

          if($_transaction_id=='')
          {



          if (!$query) {
          	$json['error'] = 'Error: Order data not found';
          } else {
          	$transactionID = urlencode($query['transaction_id']);
          	$currencyID = urlencode($query['currency_code']);
          }
      }
      else
      {
      	$transactionID = urlencode($_transaction_id);
          	$currencyID = urlencode('USD');
      }

          if (!$json['error']) {

            // Set request-specific fields.
          	$api_user = ($ppat_api_user);
          	$api_pass = ($ppat_api_pass);
          	$api_sig = ($ppat_api_sig);
          	$env = ($ppat_api_env);
            $type = urlencode('Partial');   // 'Full' or 'Partial'
            $amount = $_POST['amount'] ? $_POST['amount'] : ''; // required if Partial.
            $o_id = $_POST['order_id'];
            $memo = $type . ' ' . $amount;

            if ($type == 'Partial' || $type == 'Full') { //Refund types
            	$method = 'RefundTransaction';
                // Add request-specific fields to the request string.
            	$nvpStr = "&TRANSACTIONID=$transactionID&REFUNDTYPE=$type&CURRENCYCODE=$currencyID&NOTE=$memo";
            }

            if (strcasecmp($type, 'Partial') == 0) {
            	if (!isset($amount)) {
            		$json['error'] = ('Error: You must specify amount!');
            	} else {
            		$nvpStr = $nvpStr . "&AMT=$amount";
            	}
            } elseif ($type == 'NotComplete') {
            	$method = 'DoCapture';
            	$amount = urlencode(number_format($query['amount'], 2, '.', ''));
            	$currencyID = urlencode($query['currency']);
            	$authorizationID = urlencode($query['authorization_id']);
            	$memo = empty($memo) ? 'Capture' : $memo;
            	$nvpStr = "&AUTHORIZATIONID=$authorizationID&AMT=$amount&COMPLETETYPE=$type&CURRENCYCODE=$currencyID&NOTE=$memo";
            } elseif ($type == 'Void') {
            	$method = 'DoVoid';
            	$authorizationID = urlencode($query['authorization_id']);
            	$memo = empty($memo) ? 'Void' : $memo;
            	$nvpStr = "&AUTHORIZATIONID=$authorizationID&NOTE=$memo";
            }

            if (!$json['error']) {
                // Execute the API operation; see the PPHttpPost function above.
                //Order Id mapping for refund Gohar
                $nvpStr = $nvpStr . "&INVNUM=$o_id";
            	$httpParsedResponseAr = PPHttpPost($method, $nvpStr, $api_user, $api_pass, $api_sig, $env);

            	if ("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
            		sendEmail1($_POST['order_id'], $_POST['return_id'], $_POST['items']);
            		$json['success'] = ('$'. $_POST['amount'] .' Refund Issued via PayPal.');

            		



            		$i = 0;

            		if (!$_POST['items']) {
            			

            			if(!$remove_tax)
        {

        	$comment = 'Order #'. linkToOrder($_POST['order_id']) .' canceled and refunded.';

            			$addReport = array(
            				'order_id'  =>  $_POST['order_id'],
            				'reason_id' =>  $_POST['reason'],
            				'order_amount'    =>  $_POST['amount'],
            				'user_id'   =>  $_SESSION['user_id'],
            				'date_added'=>  date('Y-m-d H:i:s')
            				);
            			$cancel_id = $db->func_array2insert("inv_order_cancel_report", $addReport);
            			unset($addReport);

            			$skus = $db->func_query("SELECT * FROM `inv_orders_items` WHERE `order_id` = '". $_POST['order_id'] ."'");
            			foreach ($skus as $xsk) {
            				$addReport = array(
            					'cancel_id'  =>  $cancel_id,
            					'sku'       =>  $xsk['product_sku'],
            					'amount'    =>  $xsk['product_price'],
            					'action'   =>  'Order Canceled',
            					'date_added'=>  date('Y-m-d H:i:s')
            					);
            				$db->func_array2insert("inv_product_cancel_report", $addReport);
            				unset($addReport);
            			}
            		}

            		} else {

            			if(!$remove_tax)
        {

            			$addReport = array(
            				'order_id'  =>  $_POST['order_id'],
            				'reason_id' =>  $_POST['reason'],
            				'order_amount'    =>  $_POST['amount'],
            				'user_id'   =>  $_SESSION['user_id'],
            				'date_added'=>  date('Y-m-d H:i:s')
            				);
            			$cancel_id = $db->func_array2insert("inv_order_cancel_report", $addReport);
            			unset($addReport);

            			$skus = $db->func_query("SELECT * FROM `inv_orders_items` WHERE `id` IN ('" . str_replace(',', "', '", $_POST['items']) . "')");
            			foreach ($skus as $xsk) {
            				$sku .= ' ' . linkToProduct($xsk['product_sku']);

            				$addReport = array(
            					'cancel_id'  =>  $cancel_id,
            					'sku'       =>  $xsk['product_sku'],
            					'amount'    =>  $xsk['product_price'],
            					'action'   =>  'Item Removed',
            					'date_added'=>  date('Y-m-d H:i:s')
            					);
            				$order_history_id = $db->func_array2insert("inv_product_cancel_report", $addReport);
            				unset($addReport);
            			}
            			$comment = 'Product(s) '. $sku .' removed from Order #'. linkToOrder($_POST['order_id']) .' and refunded.';
            		}
            		}
            		// $db->func_query('UPDATE `inv_orders` SET `paid_price` = (`paid_price` - "'. $_POST['amount'] .'") WHERE order_id = "'. $_POST['order_id'] .'"');

            		if(!$remove_tax)
        {

            		$db->func_query('UPDATE `inv_orders` SET `paid_price` = (`paid_price` - "'. $_POST['amount'] .'"),refunded_amount= "'. (float)$_POST['amount'] .'" WHERE order_id = "'. $_POST['order_id'] .'"');

            		// addVoucher($_POST['order_id'],'payflow',$_POST['amount']*(-1));

            	}
            	else
            	{
            		$db->func_query('UPDATE `inv_orders` SET `paid_price` = (`paid_price` - "'. $_POST['amount'] .'"),refund_tax= "'. (float)$_POST['amount'] .'",refund_tax_type="paypal",refund_tax_date="'.date('Y-m-d H:i:s').'" WHERE order_id = "'. $_POST['order_id'] .'"');
            		$comment = 'Tax Removed and Refunded';

            		// addVoucher($_POST['order_id'],'tax',$_POST['amount']*(-1));
            	}
            		
            		$addcomment = array();
            		$addcomment['date_added'] = date('Y-m-d H:i:s');
            		$addcomment['user_id'] = $_SESSION['user_id'];
            		$addcomment['comment'] = $db->func_escape_string($comment);
            		$addcomment['order_id'] = $_POST['order_id'];
            		$order_history_id = $db->func_array2insert("inv_order_history", $addcomment);
            		actionLog($comment);
            	} else {
            		$json['error'] = ($httpParsedResponseAr['ACK'] . ': ' . urldecode($httpParsedResponseAr['L_LONGMESSAGE0']));
            	}

            	$json['sent'] = print_r($nvpStr, 1);
            	$json['rcvd'] = urldecode(print_r($httpParsedResponseAr, 1));
            }
        }
    }

    echo json_encode($json);
}

function PPHttpPost($methodName_, $nvpStr_, $API_UserName, $API_Password, $API_Signature, $environment) {

    // Set up your API credentials, PayPal end point, and API version.
	$API_UserName = urlencode($API_UserName);
	$API_Password = urlencode($API_Password);
	$API_Signature = urlencode($API_Signature);
	$API_Endpoint = "https://api-3t.paypal.com/nvp";
	if ("sandbox" === $environment || "beta-sandbox" === $environment) {
		$API_Endpoint = "https://api-3t.$environment.paypal.com/nvp";
	}
	$version = urlencode('51.0');

    // Set the curl parameters.
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
	curl_setopt($ch, CURLOPT_VERBOSE, 1);

    // Turn off the server and peer verification (TrustManager Concept).
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);

    // Set the API operation, version, and API signature in the request.
	$nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";

    // Set the request as a POST FIELD for curl.
	curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

    // Get response from the server.
	$httpResponse = curl_exec($ch);

	if (!$httpResponse) {
		exit("$methodName_ failed: " . curl_error($ch) . '(' . curl_errno($ch) . ')');
	}

    // Extract the response details.
	$httpResponseAr = explode("&", $httpResponse);

	$httpParsedResponseAr = array();
	foreach ($httpResponseAr as $i => $value) {
		$tmpAr = explode("=", $value);
		if (sizeof($tmpAr) > 1) {
			$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
		}
	}

	if ((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
		exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
	}

	return $httpParsedResponseAr;
}

function authnet_refund() {
	global $db;
	$json = array();
	$json['error'] = false;





    // Save API details to settings db
    //


	$query = $db->func_query_first("SELECT * FROM `oc_authnetaim_admin` ana LEFT JOIN `oc_order` o ON (ana.order_id = o.order_id) WHERE ana.order_id = '" . $_POST['order_id'] . "'");


	if (!$query) {
		$json['error'] = 'Necessary transaction details missing. This order will need to be adjusted manually from your Authorize.net Account!';
	}

	if (!$json['error']) {


		$login = $db->func_query_first("SELECT value FROM oc_setting WHERE `key`='authorizenet_aim_login'");
		$login = $login['value'];

		$key = $db->func_query_first("SELECT value FROM oc_setting WHERE `key`='authorizenet_aim_key'");
		$key = $key['value'];

		$server = $db->func_query_first("SELECT value FROM oc_setting WHERE `key`='authorizenet_aim_server'");
		$server = $server['value'];

        // Common Data setup:
		$data['x_login'] = $login;
		$data['x_tran_key'] = $key;
		$data['x_version'] = '3.1';
		$data['x_delim_data'] = 'true';
		$data['x_delim_char'] = ',';
		$data['x_encap_char'] = '"';
		$data['x_relay_response'] = 'false';
		$data['x_invoice_num'] = $_POST['order_id'];
		$data['x_type'] = 'CREDIT';

        // Specific Data setup:
		Switch ($data['x_type']) {
			Case "CREDIT":
			if (empty($_POST['amount'])) {
				$json['error'] = 'Amount Required!';
			}
			if (empty($query['last_four'])) {
				$json['error'] = 'Last 4 digits not found on original order!';
			}
			if (!$json['error']) {
				$data['x_amount'] = $_POST['amount'];
				$data['x_card_num'] = $query['last_four'];
				$data['x_trans_id'] = $query['trans_id'];
                    //$data['x_ref_trans_id'] = $query->row['trans_id'];
			}
			break;
			Case "PRIOR_AUTH_CAPTURE":
			if (!isset($query['auth_code'])) {
				$json['error'] = 'Auth Code not found on original order!';
			}
			if (!$json['error']) {
				if (!empty($_POST['amount'])) {
					$data['x_amount'] = $_POST['amount'];
				}
				$data['x_auth_code'] = $query['auth_code'];
				$data['x_trans_id'] = $query['trans_id'];
			}
			break;
			Case "VOID":
			if (!isset($query['auth_code'])) {
				$json['error'] = 'Auth Code not found on original order!';
			}
			if (!$json['error']) {
				$data['x_trans_id'] = $query['trans_id'];
			}
			break;
		}

		if (!$json['error']) {
			if ($server == 'live') {
                $url = 'https://secure.authorize.net/gateway/transact.dll'; // PROD
            } else {
                $url = 'https://test.authorize.net/gateway/transact.dll'; // DEV
            }

            $response = curl_post($url, $data);

            $results = explode(',', $response['data']);

            foreach ($results as $i => $result) {
            	if (trim($result, '"') != "") {
            		$response_info[$i + 1] = trim($result, '"');
            	}
            }

            $json['sent'] = print_r($data, 1);
            $json['rcvd'] = print_r($response_info, 1);

            if ($response_info[1] == 1) {
            	if ($data['x_type'] == "PRIOR_AUTH_CAPTURE") {
            		$auth_code = (isset($response_info['5'])) ? $response_info['5'] : 0;
            		$db->db_exec("UPDATE oc_authnetaim_admin SET auth_code = '" . ($auth_code) . "' WHERE `order_id` = '" . $_POST['order_id'] . "'");
            	}
            	$xComment = ("Action: " . $data['x_type'] . "\r\nResult: " . $response_info[4] . "\r\nRAW: " . print_r($response_info, 1));
            	$order_status_info = $db->func_query_first("SELECT order_status_id FROM `oc_order` WHERE order_id = '" . $_POST['order_id'] . "'");
            	$db->db_exec("INSERT INTO oc_order_history SET order_id = '" . $_POST['order_id'] . "', order_status_id = '" . (int) $order_status_info['order_status_id'] . "', notify = '0', comment = '" . ($xComment) . "', date_added = NOW()");



            	$i = 0;
            	foreach (explode(",", $_POST['items']) as $item) {

            		$return_info = $db->func_query_first("SELECT * FROM inv_return_items WHERE id='" . $item . "'");

            		$data = array();
            		$data['return_id'] = $_POST['return_id'];
            		$data['order_id'] = $_POST['order_id'];
            		$data['sku'] = $return_info['sku'];
            		$data['price'] = $return_info['price'];
            		$data['action'] = 'Issue Refund';
            		$data['date_added'] = date('Y-m-d h:i:s');

            		$db->func_array2insert("inv_return_decision", $data);


            		$data = array();

            		$data['decision'] = 'Issue Refund';


            		$db->func_array2update("inv_return_items", $data, 'id="' . $item . '"');

            		$i++;
            	}

            	sendEmail1($_POST['order_id'], $_POST['return_id'], $_POST['items']);


            	$json['success'] = $response_info[4];
            } else {
            	$json['error'] = $response_info[4];
            }
        }
    }

    echo (json_encode($json));
}

function curl_post($url, $data) {
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_PORT, 443);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
	curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
	$response = array();

	if (curl_error($ch)) {
		$response['error'] = curl_error($ch) . '(' . curl_errno($ch) . ')';
	} else {
		$response['data'] = curl_exec($ch);
	}
	curl_close($ch);

	return $response;
}

function sendEmail1($order_id, $return_id, $items = array(), $host_path) {

	global $db;
	$emailInfo = $_SESSION['email_info'][$order_id];
	if ($_POST['canned_id']) {

		$email = array();

		$src = $path .'files/canned_' . $_POST['canned_id'] . ".png";

		if (file_exists($src)) {
			$email['image'] = $host_path .'files/canned_' . $_POST['canned_id'] . ".png?" . time();
		}

		$email['title'] = $_POST['title'];
		$email['subject'] = $_POST['subject'];
		$email['message'] = shortCodeReplace($emailInfo, $_POST['comment']);

		sendEmailDetails($emailInfo, $email);

	} else {
		$_SESSION['message'] = 'Email not sent';
	}

}

?>
