<?php
require_once("auth.php");
require_once("inc/functions.php");

$order_id = $db->func_escape_string($_POST['orders']['order_id']);
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
$total = 0.00;

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
	
	$total = $total+(float)$_POST['orders_details']['shipping_cost'];
	$zone_id = $db->func_query_first_cell("SELECT zone_id FROM oc_zone WHERE name = '".$state."'");
	
	
}

// Create OpenCart Order if Left Blank




$authorizenet_aim_server = $db->func_query_first_cell("SELECT `value` FROM oc_setting WHERE `key`='authorizenet_aim_server'");
$authorizenet_aim_login = $db->func_query_first_cell("SELECT `value` FROM oc_setting WHERE `key`='authorizenet_aim_login'");
$authorizenet_aim_key = $db->func_query_first_cell("SELECT `value` FROM oc_setting WHERE `key`='authorizenet_aim_key'");
$authorizenet_aim_method = $db->func_query_first_cell("SELECT `value` FROM oc_setting WHERE `key`='authorizenet_aim_method'");
$authorizenet_aim_mode = $db->func_query_first_cell("SELECT `value` FROM oc_setting WHERE `key`='authorizenet_aim_mode'");
$authorizenet_aim_hash = $db->func_query_first_cell("SELECT `value` FROM oc_setting WHERE `key`='authorizenet_aim_hash'");

if ($authorizenet_aim_server == 'live') {
    		$url = 'https://secure.authorize.net/gateway/transact.dll';
		} elseif ($authorizenet_aim_server == 'test') {
			$url = 'https://test.authorize.net/gateway/transact.dll';		
		}	
		
		//$url = 'https://secure.networkmerchants.com/gateway/transact.dll';	
		
		
		
		
		
        $data = array();

		$data['x_login'] = $authorizenet_aim_login;
		$data['x_tran_key'] = $authorizenet_aim_key;
		$data['x_version'] = '3.1';
		$data['x_delim_data'] = 'true';
		$data['x_delim_char'] = ',';
		$data['x_encap_char'] = '"';
		$data['x_relay_response'] = 'false';
		$data['x_first_name'] = html_entity_decode($firstname, ENT_QUOTES, 'UTF-8');
		$data['x_last_name'] = html_entity_decode($lastname, ENT_QUOTES, 'UTF-8');
		$data['x_company'] = html_entity_decode('', ENT_QUOTES, 'UTF-8');
		$data['x_address'] = html_entity_decode($address1, ENT_QUOTES, 'UTF-8');
		$data['x_city'] = html_entity_decode($city, ENT_QUOTES, 'UTF-8');
		$data['x_state'] = html_entity_decode($state, ENT_QUOTES, 'UTF-8');
		$data['x_zip'] = html_entity_decode($zip, ENT_QUOTES, 'UTF-8');
		$data['x_country'] = html_entity_decode($country, ENT_QUOTES, 'UTF-8');
		$data['x_phone'] = $phone;
		$data['x_customer_ip'] = $_SERVER['REMOTE_ADDR'];
		$data['x_email'] = $email;
		$data['x_description'] = html_entity_decode('PhonePartsUSA.com', ENT_QUOTES, 'UTF-8');
		$data['x_amount'] = '$'.number_format($total,2);
		$data['x_currency_code'] = 'USD';
		$data['x_method'] = 'CC';
		$data['x_type'] = ($authorizenet_aim_method == 'capture') ? 'AUTH_CAPTURE' : 'AUTH_ONLY';
		$data['x_card_num'] = str_replace(' ', '', $_POST['cc_number']);
		$data['x_exp_date'] = $_POST['cc_expire_date_month'] . $_POST['cc_expire_date_year'];
		$data['x_card_code'] = $_POST['cc_cvv2'];
		$data['x_invoice_num'] = $order_id;
	
		if ($authorizenet_aim_mode == 'test') {
			$data['x_test_request'] = 'true';
		}	
				
		$curl = curl_init($url);

		curl_setopt($curl, CURLOPT_PORT, 443);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curl, CURLOPT_TIMEOUT, 10);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data, '', '&'));
 
		$response = curl_exec($curl);
		
		$json = array();
		
		if (curl_error($curl)) {
			$json['error'] = 'CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl);
			
			//$this->log->write('AUTHNET AIM CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl));	
		} elseif ($response) {
			$i = 1;
			
			$response_data = array();
			
			$results = explode(',', $response);
			
			foreach ($results as $result) {
				$response_data[$i] = trim($result, '"');
				
				$i++;
			}
		
			if ($response_data[1] == '1') {
				if (strtoupper($response_data[38]) != strtoupper(md5($authorizenet_aim_hash . $authorizenet_aim_login . $response_data[6] . '$'.number_format($total,2)))) {
					//$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('config_order_status_id'));
					
					$message = '';
					
					if (isset($response_data['5'])) {
						$message .= 'Authorization Code: ' . $response_data['5'] . "\n";
					}
					
					if (isset($response_data['6'])) {
						$message .= 'AVS Response: ' . $response_data['6'] . "\n";
					}
			
					if (isset($response_data['7'])) {
						$message .= 'Transaction ID: ' . $response_data['7'] . "\n";
					}
	
					if (isset($response_data['39'])) {
						$message .= 'Card Code Response: ' . $response_data['39'] . "\n";
					}
					
					if (isset($response_data['40'])) {
						$message .= 'Cardholder Authentication Verification Response: ' . $response_data['40'] . "\n";
					}				
	
					//$this->model_checkout_order->update($this->session->data['order_id'], $this->config->get('authorizenet_aim_order_status_id'), $message, false);				
				}
				$_SESSION['message'] = "Payment has made and order status is changed.";
				
				$_SESSION['paid_order'] = 1;
				$db->db_exec("UPDATE inv_orders SET order_status='Paid' WHERE order_id='".$order_id."'");
				
				
				$json['success'] = 'The card has successfully charged for the amount of '.'$'.number_format($total,2);
			} else {
				$json['error'] = $response_data[4];
			}
		} else {
			$json['error'] = 'Empty Gateway Response';
			
		//	$this->log->write('AUTHNET AIM CURL ERROR: Empty Gateway Response');
		}
		
		curl_close($curl);
		
	echo json_encode($json);

?>
