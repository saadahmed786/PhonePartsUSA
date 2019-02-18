<?php

class ControllerPaymentPPPayflowPro extends Controller {

	protected function index() {



		//$this->language->load('payment/pp_payflow_pro');

		if (version_compare('1.5.5',VERSION,'>')) {

			//Opencart version less than 1.5.5.0

			$this->load->language('payment/pp_payflow_pro');

		}else {

			$this->language->load('payment/pp_payflow_pro');

		}



		$this->data['text_credit_card'] = $this->language->get('text_credit_card');

		$this->data['text_wait'] = $this->language->get('text_wait');



		$this->data['entry_cc_type'] = $this->language->get('entry_cc_type');

		$this->data['entry_cc_number'] = $this->language->get('entry_cc_number');

		$this->data['entry_cc_number_error'] = $this->language->get('entry_cc_number_error');

		$this->data['entry_cc_start_date'] = $this->language->get('entry_cc_start_date');

		$this->data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');

		$this->data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');

		$this->data['entry_cc_cvv2_error'] = $this->language->get('entry_cc_cvv2_error');

		$this->data['entry_cc_issue'] = $this->language->get('entry_cc_issue');



		$this->data['button_confirm'] = $this->language->get('button_confirm');



		$this->data['months'] = array();



		for ($i = 1; $i <= 12; $i++) {

			$language_month = $this->language->get('entry_cc_start_date_month'.$i);

			if ($language_month == 'entry_cc_start_date_month'.$i || empty($language_month) ){

				$language_month = strftime('%B', mktime(0, 0, 0, $i, 1, 2000));//generate names

				//$language_month = sprintf('%02d', $i);//generate two digit number

			}



			$this->data['months'][] = array(

				'text'  => sprintf('%02d', $i).' - '.$language_month[0].''.$language_month[1].''.$language_month[2],

				'value' => sprintf('%02d', $i)

			);

		}



		$today = getdate();



		$this->data['year_expire'] = array();



		for ($i = $today['year']; $i < $today['year'] + 11; $i++) {

			$this->data['year_expire'][] = array(

				'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),

				'value' => strftime('%y', mktime(0, 0, 0, 1, 1, $i))

			);

		}



/*

		if ($this->request->get['route'] != 'checkout/guest_step_3') {

			$this->data['back'] = $this->url->https('checkout/payment');

		} else {

			$this->data['back'] = $this->url->https('checkout/guest_step_2');

		}



		$this->id = 'payment';

*/

$customer_profiling = array();

if($this->customer->getId())

{

$q = $this->db->query("SELECT * FROM ".DB_PREFIX."payflow_customer_profiling WHERE customer_id='".$this->customer->getId()."'");

$rows = $q->rows;

foreach ($rows as $row)

{

	$customer_profiling[] = array('PNREF'=>$row['PNREF'],'ACCT'=>$row['ACCT']);	

	

	

}

}

$this->data['customer_profiling'] = $customer_profiling;



		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/payment/pp_payflow_pro.tpl')) {

			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/payment/pp_payflow_pro.tpl';

		} else {

			$this->template = 'default/template/payment/pp_payflow_pro.tpl';

		}



		$this->render();

	}



	public function send() {

        ob_start();//error text catch all - avoid json response parsing problems
		if (version_compare('1.5.5',VERSION,'>')) {

			//Opencart version less than 1.5.5.0

			$this->load->language('payment/pp_payflow_pro');

		}else {

			$this->language->load('payment/pp_payflow_pro');

		}



		$log_prefix = 'PPPayflowPro';

		if ($this->config->get('pp_payflow_pro_server') == 'T') {

			$api_endpoint = 'https://pilot-payflowpro.paypal.com'; // Test server

			$log_prefix .= '(Test';

		} else {

			$api_endpoint = 'https://payflowpro.paypal.com'; // Live server

			$log_prefix .= '(Live';

		}



		if ($this->config->get('pp_payflow_pro_transaction') == 'A') {

			$transaction_type = 'A';  // Authorization only

			$log_prefix .= ' Auth) - ';

		} else {

			$transaction_type = 'S';  // Sale

			$log_prefix .= ' Sale) - ';

		}




		// if session expired or never set notify user

		if (!isset($this->session->data['order_id'])) {

			$json = array();

			$json['error'] = $this->language->get('text_session_expired');

			//$json['success'] = $this->url->link('checkout/cart');

			$this->response->setOutput(json_encode($json));

            $buffer = ob_get_clean();

            if ($buffer && $this->config->get('pp_payflow_pro_server') == 'T') {

                $this->log->write($log_prefix . 'Stray text when sending: ' . $buffer);

            }

			return;

		}



		$this->load->model('checkout/order');

		$this->load->model('setting/extension');
		$this->load->model('account/address');

		// Mismatch Fix
			$total_data = array();					
		$total = 0;
		$taxes = $this->cart->getTaxes();

			// Display prices
		if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
			$sort_order = array(); 
			// print_r($taxes);exit;

			$results = $this->model_setting_extension->getExtensions('total');

			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
			}

			array_multisort($sort_order, SORT_ASC, $results);
			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
					$this->load->model('total/' . $result['code']);

					$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
				}

				$sort_order = array(); 

				foreach ($total_data as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $total_data);			
			}
		}
			$total_amount = 0.00;
		foreach ($total_data as $total) {
			

			if($total['code']=='total')
			{
				$total_amount = $total['value'];
			}

			
			


		}
		// echo $total_amount;exit;

		// End mismatch fix



		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		if(!$total_amount || $total_amount<=0.00)
		{
			$total_amount = $order_info['total'];
		}

        // if currency not supported

		if (!in_array(strtoupper($order_info['currency_code']),array('USD','GBP','EUR','CAD','AUD','JPY'))) {

			$json = array();

			$json['error'] = $this->language->get('text_currency_not_supported_error');

			$this->response->setOutput(json_encode($json));

            $buffer = ob_get_clean();

            if ($buffer && $this->config->get('pp_payflow_pro_server') == 'T') {

                $this->log->write($log_prefix . 'Stray text when sending: ' . $buffer);

            }

			return;

		}



		$prefix_order_id = $this->config->get('pp_payflow_pro_idprefix') . $order_info['order_id'];



        //add urlencode to order_info

		
		$this->load->model('localisation/zone');
		if($_POST['action']=='saved')

		{

		$payment_data_array = array(

			'PARTNER'   => html_entity_decode($this->config->get('pp_payflow_pro_partner'), ENT_QUOTES, 'UTF-8'),

			'VENDOR'    => html_entity_decode($this->config->get('pp_payflow_pro_vendor'), ENT_QUOTES, 'UTF-8'),

			'USER'      => html_entity_decode($this->config->get('pp_payflow_pro_username'), ENT_QUOTES, 'UTF-8'),

			'PWD'       => html_entity_decode($this->config->get('pp_payflow_pro_password'), ENT_QUOTES, 'UTF-8'),

			'TRXTYPE'   => $transaction_type,

			'TENDER'    => 'C',  // C = Credit Card

			'ORIGID'	=> $_POST['PNREF'],

			'AMT'       => $this->currency->format($total_amount, $order_info['currency_code'], 1.00, FALSE),  // Amount owed

            

		);	

		}

		else

		{

		$payment_data_array = array(

			'PARTNER'   => html_entity_decode($this->config->get('pp_payflow_pro_partner'), ENT_QUOTES, 'UTF-8'),

			'VENDOR'    => html_entity_decode($this->config->get('pp_payflow_pro_vendor'), ENT_QUOTES, 'UTF-8'),

			'USER'      => html_entity_decode($this->config->get('pp_payflow_pro_username'), ENT_QUOTES, 'UTF-8'),

			'PWD'       => html_entity_decode($this->config->get('pp_payflow_pro_password'), ENT_QUOTES, 'UTF-8'),

			'TRXTYPE'   => $transaction_type,

			'TENDER'    => 'C',  // C = Credit Card

			'ACCT'      => preg_replace('/[^0-9]/', '', $this->request->post['cc_number']),  // Card number

			'CVV2'      => preg_replace('/[^0-9]/', '', $this->request->post['cc_cvv2']),  // CVV2 card verification number

			'EXPDATE'   => $this->request->post['cc_expire_date_month'] . $this->request->post['cc_expire_date_year'],  // Card expiration date

			'AMT'       => $this->currency->format($total_amount, $order_info['currency_code'], 1.00, FALSE),  // Amount owed

            'CURRENCY'  => $order_info['currency_code'],

			'FIRSTNAME' => $order_info['payment_firstname'],  // First name on card

			'LASTNAME'  => $order_info['payment_lastname'],  // Last name on card

			'STREET'    => $this->session->data['newcheckout']['address_1'],  // Address verification (AVS)

			'ZIP'       => $this->session->data['newcheckout']['postcode']  // Address verification (AVS)



			//Fraud Protection Services

			,'CUSTIP'   => $order_info['ip']

			,'CITY'     => $this->session->data['newcheckout']['city']

                	,'STATE'    =>($order_info['payment_iso_code_2'] != 'US') ? $this->model_localisation_zone->getZone($this->session->data['newcheckout']['zone_id'])['name'] : $this->model_localisation_zone->getZone($this->session->data['newcheckout']['zone_id'])['code']

                	,'EMAIL'    => $order_info['email']

                	,'BILLTOCOUNTRY'  => $order_info['payment_iso_code_2']

                	,'PHONENUM' => $order_info['telephone']

		);

		

		}

		//Addtional FPS

		if ($this->cart->hasShipping()) {

			$payment_data_array+=array(

				 'SHIPTOSTREET' => $order_info['shipping_address_1']

				,'SHIPTOCITY'   => $order_info['shipping_city']

				,'SHIPTOSTATE'  =>($order_info['shipping_iso_code_2'] != 'US') ? $order_info['shipping_zone'] : $order_info['shipping_zone_code']

				,'SHIPTOZIP'    => $order_info['shipping_postcode']

				,'SHIPTOCOUNTRY' 	=> $order_info['shipping_iso_code_2']

			);

		} else {

			$payment_data_array+=array(

				 'SHIPTOSTREET' => $order_info['payment_address_1']

				,'SHIPTOCITY'   => $order_info['payment_city']

				,'SHIPTOSTATE'  =>($order_info['payment_iso_code_2'] != 'US') ? $order_info['payment_zone'] : $order_info['payment_zone_code']

				,'SHIPTOZIP'    => $order_info['payment_postcode']

				,'SHIPTOCOUNTRY' 	=> $order_info['payment_iso_code_2']

			);

		}





		// Comment Variables

		$products = $this->cart->getProducts();

		$products_summary = array();

		$total_products=0;

		$index=0;

		foreach ($products as $product){

			$products_summary[]= $product['quantity'].'x'.$product['model'];

			$total_products += $product['quantity'];



			//Addtional FPS

			if ($product['price']>0) {

				$payment_data_array+=array(

					 'L_COST'.$index => $this->currency->format($product['price'], $order_info['currency_code'], 1.00000, FALSE)

					,'L_QTY'.$index  => $product['quantity']

					,'L_DESC'.$index  => $product['name']

				);

				if (!empty($product['model'])) {

					$payment_data_array['L_SKU'.$index ] = $product['model'];

				}

				if (!empty($product['upc'])) {

					$payment_data_array['L_UPC'.$index ] = $product['upc'];

				}

			}

			$index++;

		}

		$products_summary = implode(',',$products_summary);

		$comment_search = array(

			'{id}'

			,'{ip}'

			,'{total_models}'

			,'{total_products}'

			,'{cart}'

		);

		$comment_replace = array(

			$order_info['order_id']

			,$order_info['ip']

			,count($products)

			,$total_products

			,$products_summary

		);

		// COMMENT1

		$comment1 = $this->config->get('pp_payflow_pro_comment1');

		if (!empty($comment1)){

			$payment_data_array['COMMENT1'] = html_entity_decode(substr(str_replace($comment_search,$comment_replace,$comment1),0,128));

		}

		// COMMENT2

		$comment2 = $this->config->get('pp_payflow_pro_comment2');

		if (!empty($comment2)){

			$payment_data_array['COMMENT2'] = html_entity_decode(substr(str_replace($comment_search,$comment_replace,$comment2),0,128));

		}



		// What appears on the bank statement.

		$invnum = html_entity_decode($this->config->get('pp_payflow_pro_invnum'), ENT_QUOTES, 'UTF-8');

		if ($invnum != '')  $payment_data_array['INVNUM'] = $invnum;



		$payment_data = array();

		foreach ($payment_data_array as $key => $value) {

			$payment_data[] = $key . '[' . strlen($value) . ']=' . $value;

		}

		$payment_data = implode('&', $payment_data);


		// echo $payment_data;exit;
		// In test mode log the transaction string sent to the test server

		if ($this->config->get('pp_payflow_pro_server') == 'T') {

			// Remove sensitive data

			$payment_data_test = preg_replace('/(USER|VENDOR|PWD|ACCT|CVV2|EXPDATE)(\[[^\]]+\]=)([^&]*)/','$1$2xxxxx',$payment_data);

			$this->log->write($log_prefix . 'Parameter String: ' . $payment_data_test);

		}



        //$timeout added 1.5.2j

        $timeout = $this->config->get('pp_payflow_pro_timeout');

        if (empty($timeout)) {

            $timeout=120;//default

        }



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

		

		$headers = curl_getinfo($curl);



		if ($result === false)  {

			$result = 'RESULT=-10000&RESPMSG=Unable to connect to the payment gateway to process the transaction.';

			$this->log->write($log_prefix . 'Send Error: ' . curl_error($curl) . '(' . curl_errno($curl) . ')');

		} else if ($headers['http_code'] != 200) {

			$result = 'RESULT=-10001&RESPMSG=HTTP 200 Response expected.  Received ' . $headers['http_code'] . '.';

			$this->log->write($log_prefix . 'Recieve Error: ' . curl_error($curl) . '(' . curl_errno($curl) . ')');

		}



		curl_close($curl);



		$result = strstr($result, 'RESULT');

		$result = explode('&', $result);

		$response_data = array();

		foreach ($result as $temp) {

			$pos = strpos($temp, '=');

			if ($pos !== false)  $response_data[substr($temp, 0, $pos)] = substr($temp, $pos + 1);

		}



		$json = array();

        //check for a 1000: Generic processor error: 10001 - Timeout processing request error and route as a 104

        if (isset($response_data['RESPMSG']) && $response_data['RESULT']=='1000' && preg_match('/10001.*Timeout/',$response_data['RESPMSG'])) {

            $response_data['RESULT']='104';

        }



		if (in_array($response_data['RESULT'],array('0','126','104'))) {



            $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('config_order_status_id'));



			$message = '';



			if ($this->config->get('pp_payflow_pro_server') == 'T') {

				$message .= '<u>NOTICE</u>: This was a Test transaction' . "\n";

			}



            //success/default

			$update_order_status_id=$this->config->get('pp_payflow_pro_order_status_id');

            $redirect=$this->url->link('checkout/success');



            switch($response_data['RESULT']){

            case '104':

            //case '1000'://Timeout

				$message .= '<u>NOTICE</u>: <b>'.$response_data['RESPMSG'] .'</b> - Login to <a target="_blank" href="https://manager.paypal.com/">manager.paypal.com</a> to verify if this transaction processed successfully or not. Current timeout setting is '.$timeout."\n\n";

				$update_order_status_id=$this->config->get('pp_payflow_pro_timeout_order_status_id');

				if (is_null($update_order_status_id)) {

					$update_order_status_id='1';//pending

				}

                $redirect=$this->url->link('payment/pp_payflow_pro/pending');

            break;

            case '126'://FPS Review

				$message .= '<u>NOTICE</u>: <b>'.$response_data['RESPMSG'] .'</b> - Login to <a target="_blank" href="https://manager.paypal.com/">manager.paypal.com</a> to review this transaction (Reports > Fraud Protection > Fraud Transactions)'."\n\n";

				//Reflect that transaction is still pending in opencart backend

				$update_order_status_id=$this->config->get('pp_payflow_pro_fps_order_status_id');

				if (is_null($update_order_status_id)) {

					$update_order_status_id='10';

				}

                $redirect=$this->url->link('payment/pp_payflow_pro/pending');



				//notify admin of transaction awaiting review

				$mail = new Mail();

				$subject='PP Payflow Pro, PayPal FPS-Transaction needs review.';

				$text='Login to manager.paypal.com to review recent transation (Reports > Fraud Protection > Fraud Transactions). If you reject it you must cancel the corresponding transaction in opencart to notify the customer. If you do not want to review these types of Fraud Filters change your filters action from Review to Reject or turn off the filter completely (Service Settings > Fraud Protection > Edit Standard Filters - after making changes click "Deploy")';

                                $mail->protocol = $this->config->get('config_mail_protocol');

                                $mail->parameter = $this->config->get('config_mail_parameter');

                                $mail->hostname = $this->config->get('config_smtp_host');

                                $mail->username = $this->config->get('config_smtp_username');

                                $mail->password = $this->config->get('config_smtp_password');

                                $mail->port = $this->config->get('config_smtp_port');

                                $mail->timeout = $this->config->get('config_smtp_timeout');

                                $mail->setTo($this->config->get('config_email'));

                                $mail->setFrom($this->config->get('config_email'));

                                $mail->setSender($this->config->get('config_name'));

                                $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));

                                $mail->setText(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));

                                $mail->send();



            break;

            default://success

            break;

            }

            //log transaction results

            if (isset($response_data['PNREF'])) {

                $message .= 'Transaction ID (PNREF): ' . $response_data['PNREF'] . "\n";

            }
            if (isset($response_data['PNREF'])) {

                $message .= 'Transaction ID (PayPal): ' . $response_data['PPREF'] . "\n";

            }



            if (isset($response_data['DUPLICATE']) && $response_data['DUPLICATE'] != 0) {

                $message .= 'DUPLICATE: ' . $response_data['DUPLICATE'] . "\n";

            }



            if (isset($response_data['AVSADDR'])) {

                $message .= 'AVSADDR: ' . $response_data['AVSADDR'] . "\n";

            }



            if (isset($response_data['AVSZIP'])) {

                $message .= 'AVSZIP: ' . $response_data['AVSZIP'] . "\n";

            }



            if (isset($response_data['IAVS'])) {

                $message .= 'IAVS: ' . $response_data['IAVS'] . "\n";

            }



            if (isset($response_data['CVV2MATCH'])) {

                $message .= 'CVV2MATCH: ' . $response_data['CVV2MATCH'] . "\n";

            }

			

			$ppat_order_query = $this->db->query("SELECT order_id from " . DB_PREFIX . "payflow_admin_tools where order_id = '" . $this->session->data['order_id'] . "'");

			if (!$ppat_order_query->num_rows) {

				$this->db->query("INSERT INTO " . DB_PREFIX . "payflow_admin_tools SET `order_id` = '" . $this->session->data['order_id'] . "', transaction_id = '" . $response_data['PNREF'] . "',pp_transaction_id='".$response_data['PPREF']."', amount = '" .$total_amount. "', authorization_id = '" .$this->db->escape($response_data['AUTHCODE']). "',avsaddr='".$response_data['AVSADDR']."',avszip='".$response_data['AVSZIP']."',cvv2match='".$response_data['CVV2MATCH']."'");

			} else {

				$this->db->query("UPDATE " . DB_PREFIX . "payflow_admin_tools SET `order_id` = '" . $this->session->data['order_id'] . "', transaction_id = '" . $response_data['PNREF'] . "',pp_transaction_id='".$response_data['PPREF']."', amount = '" .$total_amount. "', authorization_id = '" .$this->db->escape($response_data['AUTHCODE']). "',avsaddr='".$response_data['AVSADDR']."',avszip='".$response_data['AVSZIP']."',cvv2match='".$response_data['CVV2MATCH']."' WHERE order_id='".$order_info['order_id']."'");

				

				

				

				

			}

			

			

			

		if($this->request->post['cc_save_data']=='true')

		{

			unset($payment_data_array['AMT']);

			$payment_data_array['TRXTYPE']='L';

		$payment_data = array();	

			foreach ($payment_data_array as $key => $value) {

			$payment_data[] = $key . '[' . strlen($value) . ']=' . $value;

		}

		

		$payment_data = implode('&', $payment_data);

			

			

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

		

			

			

			curl_close($curl);

				

			$params = array(

  'USER' => $this->config->get('pp_payflow_pro_username'),

  'VENDOR' => $this->config->get('pp_payflow_pro_vendor'),

  'PARTNER' => $this->config->get('pp_payflow_pro_partner'),

  'PWD' => $this->config->get('pp_payflow_pro_password'),

  'TENDER' => 'C', // C = credit card, P = PayPal

  'TRXTYPE' => 'I', //  S=Sale, A= Auth, C=Credit, D=Delayed Capture, V=Void                        

  'ORIGID' => $response_data['PNREF'],

  'CURRENCY' => 'USD',

  'VERBOSITY'=>'HIGH'

);



$data2 = '';

$i = 0;

foreach ($params as $n=>$v) {

    $data2 .= ($i++ > 0 ? '&' : '') . "$n=" . urlencode($v);

}



$headers = array();

$headers[] = 'Content-Type: application/x-www-form-urlencoded';

$headers[] = 'Content-Length: ' . strlen($data2);



$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $api_endpoint);

curl_setopt($ch, CURLOPT_POST, 1);

curl_setopt($ch, CURLOPT_POSTFIELDS, $data2);

curl_setopt($ch, CURLOPT_HEADER, $headers);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);



$result2 = curl_exec($ch);

curl_close($ch);

print_r($result2);exit;

// Parse results

$response2 = array();

$result2 = strstr($result2, 'RESULT');    

$valArray2 = explode('&', $result2);

foreach ($valArray2 as $val) {

  $valArray22 = explode('=', $val);

  $response2[$valArray22[0]] = $valArray22[1];

}



			if (in_array($response2['RESULT'],array('0','126','104'))) {

					

				$_check = $this->db->query("SELECT PNREF from " . DB_PREFIX . "payflow_customer_profiling where ACCT = '" . $response2['ACCT'] . "' and customer_id='".$this->customer->getId()."'");

				

				

				

				if (!$_check->num_rows) {

				$this->db->query("INSERT INTO " . DB_PREFIX . "payflow_customer_profiling SET customer_id='".$this->customer->getId()."',`PNREF` = '" . $response2['ORIGPNREF'] . "', TRANSSTATE = '" . $response2['TRANSSTATE'] . "', AVSADDR = '" .$response2['AVSADDR']. "', AVSZIP = '" .$this->db->escape($response2['AVSZIP']). "', CVV2MATCH = '" .$this->db->escape($response2['CVV2MATCH']). "', SETTLE_DATE = '" .$this->db->escape($response2['SETTLE_DATE']). "', TRANSTIME = '" .$this->db->escape($response2['TRANSTIME']). "', FIRSTNAME = '" .$this->db->escape($response2['FIRSTNAME']). "', LASTNAME = '" .$this->db->escape($response2['LASTNAME']). "', ACCT = '" .$this->db->escape($response2['ACCT']). "', EXPDATE = '" .$this->db->escape($response2['EXPDATE']). "', CARDTYPE = '" .$this->db->escape($response2['CARDTYPE']). "', IAVS = '" .$this->db->escape($response2['IAVS']). "'");

			} else {

				$this->db->query("UPDATE " . DB_PREFIX . "payflow_customer_profiling SET customer_id='".$this->customer->getId()."', `PNREF` = '" . $response2['ORIGPNREF'] . "', TRANSSTATE = '" . $response2['TRANSSTATE'] . "', AVSADDR = '" .$response2['AVSADDR']. "', AVSZIP = '" .$this->db->escape($response2['AVSZIP']). "', CVV2MATCH = '" .$this->db->escape($response2['CVV2MATCH']). "', SETTLE_DATE = '" .$this->db->escape($response2['SETTLE_DATE']). "', TRANSTIME = '" .$this->db->escape($response2['TRANSTIME']). "', FIRSTNAME = '" .$this->db->escape($response2['FIRSTNAME']). "', LASTNAME = '" .$this->db->escape($response2['LASTNAME']). "',  ACCT = '" .$this->db->escape($response2['ACCT']). "', EXPDATE = '" .$this->db->escape($response2['EXPDATE']). "', CARDTYPE = '" .$this->db->escape($response2['CARDTYPE']). "', IAVS = '" .$this->db->escape($response2['IAVS']). "' WHERE ACCT='".$response2['ACCT']."' and customer_id='".$this->customer->getId()."'");

				

				

				

				

			}

				

			}

			

		}

			



			$this->model_checkout_order->update($this->session->data['order_id'], $update_order_status_id, $message, FALSE);



			$json['success'] = $redirect;



		} else {



			if (in_array($response_data['RESULT'], array('1','3','4','5','6','7','8','9','10','26','27','28'))){

				//Error messages relating more so to merchant not customer

				$this->log->write($log_prefix . 'Payflow Error: '.$response_data['RESULT'].': '.$response_data['RESPMSG']);

				$json['error'] = $this->language->get('text_unexpected_error');



				$mail = new Mail();

				$subject='PP Payflow Pro plugin needs attention.';

				$text='Please address this recent error: '.$response_data['RESULT'].'-'.$response_data['RESPMSG']."\n".'See documentation at http://www.paypal.com/en_US/pdf/PayflowPro_HTTPS_Interface_Guide.pdf';

                                $mail->protocol = $this->config->get('config_mail_protocol');

                                $mail->parameter = $this->config->get('config_mail_parameter');

                                $mail->hostname = $this->config->get('config_smtp_host');

                                $mail->username = $this->config->get('config_smtp_username');

                                $mail->password = $this->config->get('config_smtp_password');

                                $mail->port = $this->config->get('config_smtp_port');

                                $mail->timeout = $this->config->get('config_smtp_timeout');

                                $mail->setTo($this->config->get('config_email'));

                                $mail->setFrom($this->config->get('config_email'));

                                $mail->setSender($this->config->get('config_name'));

                                $mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));

                                $mail->setText(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));

                                $mail->send();

/*

                                // Send to additional alert emails

                                $emails = explode(',', $this->config->get('config_alert_emails'));



                                foreach ($emails as $email) {

                                        if ($email && preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $email)) {

                                                $mail->setTo($email);

                                                $mail->send();

                                        }

                                }

*/



			}elseif (in_array($response_data['RESULT'], array('125','128'))){

				//Fraud Protection Services Filter Declines - simply show as declined

				$json['error'] = $this->language->get('text_declined_error');

			}else{

                if ($this->config->get('pp_payflow_pro_server') == 'T') {

                    $this->log->write($log_prefix . 'PayPal responded with error: '.$response_data['RESPMSG']);

                }

				//All other messages

				$json['error'] = $response_data['RESPMSG'];

			}

		}



        //grab any stray text

        $buffer = ob_get_clean();

        if ($buffer && $this->config->get('pp_payflow_pro_server') == 'T') {

            $this->log->write($log_prefix . 'Stray text when sending: ' . $buffer);

        }

		$this->response->setOutput(json_encode($json));

	}



	public function pending() {

		if (isset($this->session->data['order_id'])) {

			$this->cart->clear();



			unset($this->session->data['shipping_method']);

			unset($this->session->data['shipping_methods']);

			unset($this->session->data['payment_method']);

			unset($this->session->data['payment_methods']);

			unset($this->session->data['guest']);

			unset($this->session->data['comment']);

			unset($this->session->data['order_id']);

			unset($this->session->data['coupon']);

			unset($this->session->data['reward']);

			unset($this->session->data['voucher']);

			unset($this->session->data['vouchers']);

			unset($this->session->data['totals']);

		}

		//$this->language->load('payment/pp_payflow_pro');

		if (version_compare('1.5.5',VERSION,'>')) {

			//Opencart version less than 1.5.5.0

			$this->load->language('payment/pp_payflow_pro');

		}else {

			$this->language->load('payment/pp_payflow_pro');

		}



		$this->document->setTitle($this->language->get('heading_title'));



		$this->data['breadcrumbs'] = array();



		$this->data['breadcrumbs'][] = array(

			'href'      => $this->url->link('common/home'),

			'text'      => $this->language->get('text_home'),

			'separator' => false

		);



		$this->data['breadcrumbs'][] = array(

			'href'      => $this->url->link('checkout/cart'),

			'text'      => $this->language->get('text_basket'),

			'separator' => $this->language->get('text_separator')

		);



		$this->data['breadcrumbs'][] = array(

			'href'      => $this->url->link('checkout/checkout', '', 'SSL'),

			'text'      => $this->language->get('text_checkout'),

			'separator' => $this->language->get('text_separator')

		);



		$this->data['breadcrumbs'][] = array(

			'href'      => $this->url->link('payment/pp_payflow_pro/pending'),

			'text'      => $this->language->get('text_success'),

			'separator' => $this->language->get('text_separator')

		);



		$this->data['heading_title'] = $this->language->get('heading_title');



		if ($this->customer->isLogged()) {

			$this->data['text_message'] = sprintf($this->language->get('text_customer'), $this->url->link('account/account', '', 'SSL'), $this->url->link('account/order', '', 'SSL'), $this->url->link('account/download', '', 'SSL'), $this->url->link('information/contact'));

		} else {

			$this->data['text_message'] = sprintf($this->language->get('text_guest'), $this->url->link('information/contact'));

		}



		$this->data['button_continue'] = $this->language->get('button_continue');



		$this->data['continue'] = $this->url->link('common/home');



		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/common/success.tpl')) {

			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/common/success.tpl';

		} else {

			$this->template = 'default/template/common/success.tpl';

		}



		$this->children = array(

			'common/column_left',

			'common/column_right',

			'common/content_top',

			'common/content_bottom',

			'common/footer',

			'common/header'

		);



		$this->response->setOutput($this->render());

    }



}

?>