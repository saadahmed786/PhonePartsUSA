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

class ControllerPaymentAuthorizeNetCim extends CimFrontController {	

	
	protected function index() {
$this->language->load('account/authorizenet_cim');
		$this->language->load('payment/authorizenet_cim');
		if ($this->customer->isLogged()) {
			$cim_customerID = $this->model_authorizenet_cim_customer->getCimCustomerCimID($this->customer->getId());
			
			$this->data['cim_customer_profile'] = $this->getCimCustomerProfile($cim_customerID);
			$this->data['default_payment_profile_id'] = $this->model_authorizenet_cim_customer->getDefaultPaymentProfileId($this->customer->getId());
			$this->data['local_payment_profile_list'] = $this->model_authorizenet_cim_customer->getCimPaymentProfiles($this->customer->getId());
			
			if(!$this->data['cim_customer_profile']){		
				$this->data['error'] = $this->language->get('text_error_connecting_cim');
				$this->data['text_error_connecting_cim_body'] = $this->language->get('text_error_connecting_cim_body');
			}
			$this->data['isguest'] = false;
		} else {
			//$this->data['error'] = sprintf($this->language->get('text_cim_requires_account_and_login'), $this->url->link('account/login', '', 'SSL'));
			$this->data['isguest'] = true;
		}
		
		$this->loadVariables();
		if ($this->customer->isLogged() && isset($this->session->data['payment_address_id'])) {
			$payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
			$payment_address['telephone'] = $this->customer->getTelephone();
		} elseif (isset($this->session->data['guest'])) {
			$payment_address = $this->session->data['guest']['payment'];
			$payment_address['telephone'] = $this->session->data['guest']['telephone'];
		}
		$this->data['payment_address'] = $payment_address;
		
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/payment/authorizenet_cim.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/payment/authorizenet_cim.tpl';
		} else {
			$this->template = 'default/template/payment/authorizenet_cim.tpl';
		}
		
		$this->render();

	}
	
	
	private function getCimPaymentCardID(&$cim_customerID,&$json){
		
		// if new cart entered validate cart then add to cim
		if ($this->request->post['select_payment_account'] == 'use_cim_payment_account'){
			if (trim($this->request->post['customer_payment_profile_id'])) {
				return 	$this->request->post['customer_payment_profile_id'];
			}else {
				$json['error'] = $this->language->get('text_error_select_payment_profile');
				return false;
			}
		}else {
			if (!$this->validateForm($json)) {
				return false;
			}
			return $this->addPaymentProfile($json);
		}
	}
	public function send() {
		$this->language->load('account/authorizenet_cim');
		$this->language->load('payment/authorizenet_cim');
		$this->load->model('checkout/order');

		$json = array();
		
		$this->data['text_cim_held_notify_subj'] = $this->language->get('text_cim_held_notify_subj');
		$this->data['text_cim_held_notify_message'] = $this->language->get('text_cim_held_notify_message');
		$this->data['text_cim_held_user_message'] = $this->language->get('text_cim_held_user_message');
		if ($this->customer->isLogged()) {
			// Custome rprofile shuld be already created in index view. we dont expect $cim_customerID be false
			$cim_customerID = $this->model_authorizenet_cim_customer->getCimCustomerCimID($this->customer->getId());
	
			// init finished continue
			if ($cim_customerID){
				if(($cim_paymentID=$this->getCimPaymentCardID($cim_customerID,$json))!=false){
					$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
					
					$this->createCimOrder($cim_customerID, $cim_paymentID, $order_info, $json);
				}
			} else {
				$json['error'] = $this->language->get('text_error_cim_profile_notfound');
			}		
		}else{
			$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
			$this->createAimOrder($order_info, $json);
		}
		$this->response->setOutput(json_encode($json));
	}
	

	public function singleClickOrder() {
		$this->load->model('checkout/order');
		$cim_customerID = $this->model_authorizenet_cim_customer->getCimCustomerCimID($this->customer->getId());
		$cim_default_payment_profile_id = $this->model_authorizenet_cim_customer->getScPaymentProfileId($this->customer->getId());
		$cim_default_payment_adress_id = $this->model_authorizenet_cim_customer->getScShippingAddressId($this->customer->getId());
		
		// check defaults if not exist redirect.
		// load chart sesion variables
		// load default adress
		// load payment id
		// place order
		

		$this->response->setOutput(json_encode($json));
	}
	public function singleClickProduct(){
		// add product to chart
		
		$this->language->load('account/authorizenet_cim');
		$this->language->load('payment/authorizenet_cim');
		$this->singleClickOrder();
	}
}
?>