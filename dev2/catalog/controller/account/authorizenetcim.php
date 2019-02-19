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

class ControllerAccountAuthorizenetCim extends CimFrontController {
	
	public function __construct($registry){
		parent::__construct($registry);
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/authorizenetcim', '', 'SSL');		
			$this->redirect($this->url->link('account/login', '', 'SSL'));
		}		

		$this->language->load('payment/authorizenet_cim');
		$this->language->load('account/authorizenet_cim');
	}	   
	
  	public function index() {
  		//$request = new AuthorizeNetCIM();
  			
		$cim_customerID = $this->model_authorizenet_cim_customer->getCimCustomerCimID($this->customer->getId());
		$this->data['cim_customer_profile'] = $this->getCimCustomerProfile($cim_customerID);
		
		$this->data['cim_payment_address_list']= $this->model_authorizenet_cim_customer->getCustomerAddresses($this->customer->getId());
		$this->data['default_payment_profile_id'] = $this->model_authorizenet_cim_customer->getDefaultPaymentProfileId($this->customer->getId());
		$this->data['local_payment_profile_list'] = $this->model_authorizenet_cim_customer->getCimPaymentProfiles($this->customer->getId());
		list($this->data['sc_payment_adress_id'], $tmp ) = $this->model_authorizenet_cim_customer->getScShippingAddressId($this->customer->getId());
		$this->data['sc_adress_list'] = $this->model_authorizenet_cim_customer->getCustomerAddresses($this->customer->getId());
		
		if(!$this->data['cim_customer_profile']){
			$this->error['error'] = $this->language->get('text_error_connecting_cim');
			$this->data['text_error_connecting_cim_body'] = $this->language->get('text_error_connecting_cim_body');
	  		$this->data['cim_customer_profile'] = new stdClass();
	  		$this->data['cim_customer_profile']->paymentProfiles = array();
		}

		
		// load breadcrumb
		$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home'),
				'separator' => false
		);
			
		$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_account'),
				'href'      => $this->url->link('account/account', '', 'SSL'),
				'separator' => $this->language->get('text_separator')
		);
			
		$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('heading_title'),
				'href'      => $this->url->link('account/authorizenetcim', '', 'SSL'),
				'separator' => $this->language->get('text_separator')
		);		

		$this->loadVariables();
		
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/authorizenet_cim_list.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/authorizenet_cim_list.tpl';
		} else {
			$this->template = 'default/template/account/authorizenet_cim_list.tpl';
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

  	public function createPaymentProfile() {
  		$json = array();

  		if ($this->validateForm($json)) {
	  		$payment_profile_id = $this->addPaymentProfile($json);
	  		if (!$payment_profile_id && !isset($json['error'])) {
	  			$json['error'] = $this->language->get('text_error_create_pament_account');
	  		}
  		}
  		$this->response->setOutput(json_encode($json));
  	}
  	
  	public function deletePaymentProfile() {
    	$json=array();
    	$request = new AuthorizeNetCIM();
    	$cim_customerID = $this->model_authorizenet_cim_customer->getCimCustomerCimID($this->customer->getId());
    	$cimpaymetaccountid=$this->request->post['cimpaymentid'];
    	$response=$request->deleteCustomerPaymentProfile($cim_customerID, $cimpaymetaccountid);
    	if ($response->xml->messages->message->code == "I00001"){
    		$this->model_authorizenet_cim_customer->deletePaymentProfile($this->customer->getId(),$cimpaymetaccountid);
			$json['success'] = $this->language->get('text_success_delete_payment_profile');
			$json['success_url']=$this->url->link('account/authorizenetcim', '', 'SSL');
    		$this->authorizenet_cim_log->write('CUSTOMER DELETED PAYMENT ACCOUNT: CustomerID: '.$this->customer->getId().' CustomerCimPaymentAccountId: '.$cimpaymetaccountid);
    	}else {
    		$json['error'] = $this->language->get('text_error_delete_payment_profile');
    		$this->authorizenet_cim_log->write('AUTHNET DELETE PAYMENT ACCOUNT ERROR: customerProfileId: '.$cim_customerID.' customerPaymentProfileId: '.$cim_paymentID.' Response Code:'.$transactionResponse->response_code.' Error Message: '.$transactionResponse->error_message);
    	}
		$this->response->setOutput(json_encode($json));
  	} 

  	public function setDefaultPaymentProfile() {
  		$json=array();
  		$new_default_paymentid=$this->request->post['default_paymentid'];
  		if ($new_default_paymentid) {
  			$ress = $this->model_authorizenet_cim_customer->setDefaultPaymentProfile($this->customer->getId(),$new_default_paymentid);
  			//$this->authorizenet_cim_log->write("$new_default_paymentid default patment adress changed $ress");
  			$json['success'] = $this->language->get('text_success_set_default_payment');
    		$json['success_url']=$this->url->link('account/authorizenetcim', '', 'SSL');
  		}else {
  			$json['error'] = $this->language->get('text_error_set_default_payment');
  		}
  		$this->response->setOutput(json_encode($json));
  	}
  	
  	public function setDefaultPaymentAddress() {
  		$json=array();
  		$new_default_addressid=$this->request->post['default_payment_addressid'];
  		$new_default_addressid=$this->request->post['default_payment_addressid'];
  		if ($new_default_addressid) {
  			$this->model_authorizenet_cim_customer->setScShippingAddress($this->customer->getId(),$new_default_addressid, "????" );
  			$json['success'] = $this->language->get('text_success_set_default_address');
  			$json['success_url']=$this->url->link('account/authorizenetcim', '', 'SSL');
  		}else {
  			$json['error'] = $this->language->get('text_error_set_default_address');
  		}
  		
  		$this->response->setOutput(json_encode($json));  		 
  	}
  	
  	/*
  	private function loadIndexData() {
  		$this->data['breadcrumbs'][] = array(
  				'text'      => $this->language->get('text_home'),
  				'href'      => $this->url->link('common/home'),
  				'separator' => false
  		);
  			
  		$this->data['breadcrumbs'][] = array(
  				'text'      => $this->language->get('text_account'),
  				'href'      => $this->url->link('account/account', '', 'SSL'),
  				'separator' => $this->language->get('text_separator')
  		);
  			
  		$this->data['breadcrumbs'][] = array(
  				'text'      => $this->language->get('heading_title'),
  				'href'      => $this->url->link('account/authorizenetcim', '', 'SSL'),
  				'separator' => $this->language->get('text_separator')
  		);
  			
  		$this->document->setTitle($this->language->get('heading_title'));
  	
  		$this->data['heading_title'] = $this->language->get('heading_title');
  		$this->data['text_account'] = $this->language->get('text_account');
  		$this->data['text_credit_card_entries'] = $this->language->get('text_credit_card_entries');
  		$this->data['text_bank_accont_entries'] = $this->language->get('text_bank_accont_entries');
  	
  		$this->data['button_new_pamet_account'] = $this->language->get('button_new_pamet_account');
  		$this->data['button_edit'] = $this->language->get('button_edit');
  		$this->data['button_delete'] = $this->language->get('button_delete');
  		$this->data['button_back'] = $this->language->get('button_back');
  		$this->data['text_close'] = $this->language->get('text_close');
  		$this->data['button_set_default'] = $this->language->get('button_set_default');  		
  	
  		// form
  		$this->data['text_select_cimcard'] = $this->language->get('text_select_cimcard');
  		$this->data['text_select_cimadress'] = $this->language->get('text_select_cimadress');
  		$this->data['text_select_wanttouse_differentaccount'] = $this->language->get('text_select_wanttouse_differentaccount');
  		$this->data['text_select_wanttouse_cim'] = $this->language->get('text_select_wanttouse_cim');
  		$this->data['text_select_select_cimcard'] = $this->language->get('text_select_select_cimcard');
  		$this->data['text_select_select_adress'] = $this->language->get('text_select_select_adress');
  		$this->data['text_select_paymentaccount'] = $this->language->get('text_select_paymentaccount');
  		$this->data['text_create_newcredit_card'] = $this->language->get('text_create_newcredit_card');
  		$this->data['text_create_bank_account'] = $this->language->get('text_create_bank_account');
  		$this->data['button_cancel'] = $this->language->get('button_cancel');
  		$this->data['button_save'] = $this->language->get('button_save');
  		$this->data['entry_customer_type'] = $this->language->get('entry_customer_type');
  		$this->data['text_business'] = $this->language->get('text_business');
  		$this->data['text_individual'] = $this->language->get('text_individual');
  		$this->data['text_adress_entries'] = $this->language->get('text_adress_entries'); 
  		$this->data['text_cim_payment_accounts'] = $this->language->get('text_cim_payment_accounts');
  		
  		$this->data['text_single_click_setup']  = $this->language->get('text_single_click_setup');
  		$this->data['text_sc_billing_address']  = $this->language->get('text_sc_billing_address');
  		$this->data['text_sc_shiping_address']  = $this->language->get('text_sc_shiping_address');
  		$this->data['text_sc_shiping_method']  = $this->language->get('text_sc_shiping_method');
  		$this->data['text_sc_payment_card']  = $this->language->get('text_sc_payment_card');
  		$this->data['button_sc_save']  = $this->language->get('button_sc_save');
  		$this->data['button_sc_select_shipping_address']  = $this->language->get('button_sc_select_shipping_address'); 		
  		
  	
  		$this->data['text_credit_card'] = $this->language->get('text_credit_card');
  		$this->data['text_wait'] = $this->language->get('text_wait');
  		$this->data['text_select'] = $this->language->get('text_select');
  		$this->data['text_select_prfx_bank_account'] = $this->language->get('text_select_prfx_bank_account');
  		$this->data['text_select_prfx_credit_card'] = $this->language->get('text_select_prfx_credit_card');
  	
  		$this->data['entry_cc_owner'] = $this->language->get('entry_cc_owner');
  		$this->data['entry_cc_number'] = $this->language->get('entry_cc_number');
  		$this->data['entry_cc_expire_date'] = $this->language->get('entry_cc_expire_date');
  		$this->data['entry_cc_cvv2'] = $this->language->get('entry_cc_cvv2');
  		$this->data['entry_ba_bankname'] = $this->language->get('entry_ba_bankname');
  		$this->data['entry_ba_echecktype'] = $this->language->get('entry_ba_echecktype');
  		$this->data['entry_ba_nameonaccount'] = $this->language->get('entry_ba_nameonaccount');
  		$this->data['entry_ba_accountnumber'] = $this->language->get('entry_ba_accountnumber');
  		$this->data['entry_ba_routingnumber'] = $this->language->get('entry_ba_routingnumber');
  		$this->data['entry_ba_accounttype'] = $this->language->get('entry_ba_accounttype');
  		$this->data['entry_savings'] = $this->language->get('entry_savings');
  		$this->data['entry_businesschecking'] = $this->language->get('entry_businesschecking');
  		$this->data['entry_checking'] = $this->language->get('entry_checking');
  		$this->data['text_select'] = $this->language->get('text_select');
  		
  		$this->data['authorizenet_cim_use_jquerydialog'] = $this->config->get('authorizenet_cim_use_jquerydialog');
  		$this->data['authorizenet_cim_require_billing_adress'] = $this->config->get('authorizenet_cim_require_billing_adress');
  		
  	
  	
  		$this->data['not_supported'] = $this->language->get('not_supported');
  		$this->data['button_confirm'] = $this->language->get('button_confirm');
  	
  		$this->data['months'] = array();
  	
  		for ($i = 1; $i <= 12; $i++) {
  			$this->data['months'][] = array(
  					'text'  => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)),
  					'value' => sprintf('%02d', $i)
  			);
  		}
  	
  		$today = getdate();
  	
  		$this->data['year_expire'] = array();
  	
  		for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
  			$this->data['year_expire'][] = array(
  					'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
  					'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i))
  			);
  		}
  	
  		if(isset($this->error['warning'])) {
  			$this->data['error_warning'] = $this->error['warning'];
  		} else {
  			$this->data['error_warning'] = '';
  		}
  		if (isset($this->error['error'])) {
  			$this->data['error'] = $this->error['error'];
  		} else {
  			$this->data['error'] = '';
  		}
  	
  		if (isset($this->session->data['success'])) {
  			$this->data['success'] = $this->session->data['success'];
  	
  			unset($this->session->data['success']);
  		} else {
  			$this->data['success'] = '';
  		}

  		$this->getShippingMethods();
  	
  		$this->data['insert'] = $this->url->link('account/authorizenetcim/createpaymentprofile', '', 'SSL');
  		$this->data['delete'] = $this->url->link('account/authorizenetcim/deletepaymentprofile', '', 'SSL');
  		$this->data['setdefaultpayment'] = $this->url->link('account/authorizenetcim/setdefaultpaymentprofile', '', 'SSL');
  		$this->data['setdefaultaddress'] = $this->url->link('account/authorizenetcim/setdefaultpaymentaddress', '', 'SSL');
  		$this->data['back'] = $this->url->link('account/account', '', 'SSL');
  		
  		$this->loadAdressVariables();
  	}
  	*/
  	
  	private function getShippingMethods(){

  		$this->load->model('account/address');
  		
  		$shipping_address = 0;  		
  		if ($this->customer->isLogged() && isset($this->session->data['shipping_address_id'])) {
  			$shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
  		} elseif (isset($this->session->data['guest'])) {
  			$shipping_address = $this->session->data['guest']['shipping'];
  		}
  		  		
  		$this->language->load('checkout/checkout');
  		$this->data['text_shipping_method'] = $this->language->get('text_shipping_method');
  		$this->data['text_comments'] = $this->language->get('text_comments');
  		
  		// Shipping Methods
  		$quote_data = array();
  			
  		$this->load->model('setting/extension');
  			
  		$results = $this->model_setting_extension->getExtensions('shipping');
  			
  		foreach ($results as $result) {
  			if ($this->config->get($result['code'] . '_status')) {
  				$this->load->model('shipping/' . $result['code']);
  					
  				$quote = $this->{'model_shipping_' . $result['code']}->getQuote($shipping_address);
  		
  				if ($quote) {
  					$quote_data[$result['code']] = array(
  							'title'      => $quote['title'],
  							'quote'      => $quote['quote'],
  							'sort_order' => $quote['sort_order'],
  							'error'      => $quote['error']
  					);
  				}
  			}
  		}
  		
  		//die(print_r($quote_data));
  		
  		$sort_order = array();
  		
  		foreach ($quote_data as $key => $value) {
  			$sort_order[$key] = $value['sort_order'];
  		}
  		
  		array_multisort($sort_order, SORT_ASC, $quote_data);
  			
  		$this->session->data['shipping_methods'] = $quote_data;
  		
  		if (empty($this->session->data['shipping_methods'])) {
  			$this->data['error_warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
  		} else {
  			$this->data['error_warning'] = '';
  		}
  		
  		if (isset($this->session->data['shipping_methods'])) {
  			$this->data['shipping_methods'] = $this->session->data['shipping_methods'];
  		} else {
  			$this->data['shipping_methods'] = array();
  		}  		
  	}

}
?>