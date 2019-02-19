<?php 
/**
 * Contains part of the Opencart Authorize.Net CIM Payment Module code.
 *
 * PHP version 5
 *
 * LICENSE: This source file is subject to memiiso license.
 * Please see the LICENSE.txt file for more information.
 * All other rights reserved.
 *
 * @author     memiiso <gel.yine.gel@hotmail.com>
 * @copyright  2013-~ memiiso
 * @license    Commercial License. Please see the LICENSE.txt file
 */

class ControllerPaymentAuthorizenetCim extends Controller {
	private $error = array(); 

	public function index() {
		$this->language->load('payment/authorizenet_cim');
		$this->language->load('payment/authorizenet_cim_license');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {	
			$this->model_setting_setting->editSetting('authorizenet_cim', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		$this->data['text_license'] = $this->language->get('text_license');		
		$this->data['text_license_agreement'] = $this->language->get('text_license_agreement');	
		$this->data['text_license_text'] = $this->language->get('text_license_text');		
		$this->data['text_test'] = $this->language->get('text_test');
		$this->data['text_live'] = $this->language->get('text_live');
		$this->data['text_sandbox'] = $this->language->get('text_sandbox');
		$this->data['text_authorization'] = $this->language->get('text_authorization');
		$this->data['text_capture'] = $this->language->get('text_capture');		
		$this->data['text_authorizeandcapture'] = $this->language->get('text_authorizeandcapture');		
		$this->data['entry_validation_mode'] = $this->language->get('entry_validation_mode');
		$this->data['validation_mode_test'] = $this->language->get('validation_mode_test');
		$this->data['validation_mode_live'] = $this->language->get('validation_mode_live');
		$this->data['validation_mode_none'] = $this->language->get('validation_mode_none');
		$this->data['text_disable_bank_payment'] = $this->language->get('text_disable_bank_payment');
		
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
		$this->data['entry_order_status_info'] = $this->language->get('entry_order_status_info');
		$this->data['entry_daily_log'] = $this->language->get('entry_daily_log');
		$this->data['entry_delete_notfound_cimid'] = $this->language->get('entry_delete_notfound_cimid');
		$this->data['entry_log_responses'] = $this->language->get('entry_log_responses');
		$this->data['entry_save_shiping_address'] = $this->language->get('entry_save_shiping_address');	
		$this->data['entry_save_use_jquerydialog'] = $this->language->get('entry_save_use_jquerydialog');
		$this->data['entry_send_email_onerror'] = $this->language->get('entry_send_email_onerror');		
		$this->data['entry_enable_shipping_adress'] = $this->language->get('entry_enable_shipping_adress');	
		$this->data['entry_cim_fill_line_items'] = $this->language->get('entry_cim_fill_line_items');
		

		$this->data['entry_cim_require_billing_adress'] 		= $this->language->get('entry_cim_require_billing_adress');	
		$this->data['entry_cim_held_notificatin_emails'] 		= $this->language->get('entry_cim_held_notificatin_emails');
		$this->data['entry_cim_held_notify_customer'] 			= $this->language->get('entry_cim_held_notify_customer');	
		$this->data['entry_cim_held_rule_list'] 				= $this->language->get('entry_cim_held_rule_list');

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
		if (isset($this->error['held_rule_list_error'])) {
			$this->data['held_rule_list_error'] = $this->error['held_rule_list_error'];
		} else {
			$this->data['held_rule_list_error'] = '';
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
			'href'      => $this->url->link('payment/authorizenet_cim', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
				
		$this->data['action'] = $this->url->link('payment/authorizenet_cim', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
		
		
		//  post variables
		$this->setSettingVariable('authorizenet_cim_live_login');
		$this->setSettingVariable('authorizenet_cim_live_key');
		$this->setSettingVariable('authorizenet_cim_sandbox_login');
		$this->setSettingVariable('authorizenet_cim_sandbox_key');
		$this->setSettingVariable('authorizenet_cim_hash');
		$this->setSettingVariable('authorizenet_cim_server');
		$this->setSettingVariable('authorizenet_cim_method');		
		$this->setSettingVariable('authorizenet_cim_total');
		$this->setSettingVariable('authorizenet_cim_order_status_id');
		$this->setSettingVariable('authorizenet_cim_geo_zone_id');	
		$this->setSettingVariable('authorizenet_cim_status');
		$this->setSettingVariable('authorizenet_cim_sort_order');		
		$this->setSettingVariable('authorizenet_cim_daily_log');
		$this->setSettingVariable('authorizenet_cim_delete_notfound');
		$this->setSettingVariable('authorizenet_cim_debug_log');
		$this->setSettingVariable('authorizenet_cim_use_jquerydialog');	
		$this->setSettingVariable('authorizenet_cim_validation_mode');
		$this->setSettingVariable('authorizenet_cim_email_error');
		$this->setSettingVariable('authorizenet_cim_use_shipping_address');
		$this->setSettingVariable('authorizenet_cim_fill_line_items');
		
		$this->setSettingVariable('authorizenet_cim_require_billing_adress');
		$this->setSettingVariable('authorizenet_cim_held_notificatin_emails');
		$this->setSettingVariable('authorizenet_cim_held_notify_customer');
		$this->setSettingVariable('authorizenet_cim_held_rule_list');
				
		$this->setSettingVariable('authorizenet_cim_disable_bank_payment');

		$this->load->model('localisation/order_status');
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();		
		$this->load->model('localisation/geo_zone');	
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();		

		$this->template = 'payment/authorizenet_cim.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	public function license(){
		$json=array();
		$this->language->load('payment/authorizenet_cim');
		
		$json['title']='title';
		$json['text']='aaa';
		
		$this->response->setOutput(json_encode($json));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payment/authorizenet_cim')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->request->post['authorizenet_cim_live_login']) {
			$this->error['login'] = $this->language->get('error_login');
		}

		if (!$this->request->post['authorizenet_cim_live_key']) {
			$this->error['key'] = $this->language->get('error_key');
		}		

		
		

		$this->request->post['authorizenet_cim_held_rule_list'] = preg_replace('/\s+/','',$this->request->post['authorizenet_cim_held_rule_list']);
		$this->request->post['authorizenet_cim_held_rule_list'] = trim(strtoupper($this->request->post['authorizenet_cim_held_rule_list']),'|');
		
		if ($this->request->post['authorizenet_cim_held_rule_list']) {				
			$this->load->model('localisation/order_status');
			$order_statuses = $this->model_localisation_order_status->getOrderStatuses();
			$order_status_id_list = array();
			foreach ($order_statuses as $order_status){
				$order_status_id_list[] = $order_status['order_status_id'];
			}
			$held_rules = explode('|', trim($this->request->post['authorizenet_cim_held_rule_list'] ));
			foreach ($held_rules as $held_rule){
				$tmp = explode(';', trim($held_rule));
				if (count($tmp) != 4 || !in_array($tmp[3], $order_status_id_list) || !is_numeric($tmp[3])) {
					$this->error['held_rule_list_error'] = $this->language->get('text_held_rule_list_error');
					break;
				}
			}			
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
	
	private function setSettingVariable($variable){
		if (isset($this->request->post[$variable])) {
			$this->data[$variable] = $this->request->post[$variable];
		} else {
			$this->data[$variable] = $this->config->get($variable);
		}
	}
	
	public function install(){
		try {		
		
		$this->db->query('CREATE TABLE IF NOT EXISTS `'.DB_PREFIX.'authorizenet_cim` (
		  `customer_id` int(11) NOT NULL,
		  `cim_id` int(11) NOT NULL,
		  `insert_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
		  `update_date` datetime DEFAULT NULL,
		  PRIMARY KEY (`customer_id`)
		) DEFAULT CHARSET=utf8;');
		
		$this->db->query("
CREATE TABLE IF NOT EXISTS `".DB_PREFIX."authorizenet_cim_payment_profiles` (
  `customer_id` int(11) NOT NULL,
  `payment_profile_id` int(11) NOT NULL,
  `account_type` varchar(60) NOT NULL,
  `customer_type` varchar(60) NOT NULL,
  `name_on_account` varchar(120) NOT NULL,
  `account_number` varchar(60) NOT NULL,
  `routing_number` varchar(60) NOT NULL,
  `bank_name` varchar(120) NOT NULL,
  `cc_type` varchar(60) NOT NULL,
  `default` tinyint(1) NOT NULL DEFAULT '0',
  `cim_sc_default` tinyint(1) NOT NULL DEFAULT '0',
  `insert_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`customer_id`,`payment_profile_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
				");
		
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `".DB_PREFIX."authorizenet_cim_order_response` (
			  `order_id` int(11) NOT NULL,
			  `cim_transaction_id` int(16) NOT NULL DEFAULT '0',
			  `response_code` int(11) NOT NULL DEFAULT '0',
			  `response_reason_code` int(11) NOT NULL DEFAULT '0',
			  `response_reason_text` varchar(255) DEFAULT NULL,
			  `response_text` varchar(255) DEFAULT NULL,
			  `response_json` text,
			  `insert_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			  `update_date` datetime DEFAULT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
		");
		
		try {		
				$this->db->query("ALTER TABLE `".DB_PREFIX."address`
		  							  ADD `cim_shipping_address_id` int(11) NOT NULL DEFAULT '0',
									  ADD `cim_sc_billing_default` tinyint(1) NOT NULL DEFAULT '0',
									  ADD `cim_sc_shipping_default` tinyint(1) NOT NULL DEFAULT '0',
									  ADD `cim_sc_shipping_settings` text NOT NULL,
									  ADD `insert_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
									  ADD `update_date` datetime DEFAULT NULL
						;");
			} catch (Exception $e) {
			}
		
		} catch (Exception $e) {
			// 
		}
	
	}
}
?>