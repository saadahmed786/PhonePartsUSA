<?php
class ControllerModuleSimplifiedCheckout extends Controller {
	private $error = array(); 
	 
	public function index() {   
		$this->load->language('module/simplified_checkout');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('simplified_checkout', $this->request->post);		
			
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}
				
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_yes'] = $this->language->get('text_yes');
		$this->data['text_no'] = $this->language->get('text_no');
		$this->data['text_select'] = $this->language->get('text_select');
		$this->data['text_no_zone'] = $this->language->get('text_no_zone');
		$this->data['text_help'] = $this->language->get('text_help');
		$this->data['text_standard'] = $this->language->get('text_standard');
		$this->data['text_2column'] = $this->language->get('text_2column');
		
		$this->data['entry_dynamic_shipping'] = $this->language->get('entry_dynamic_shipping');
		$this->data['entry_show_coupon'] = $this->language->get('entry_show_coupon');
		$this->data['entry_hide_country'] = $this->language->get('entry_hide_country');
		$this->data['entry_fixed_country'] = $this->language->get('entry_fixed_country');
		$this->data['entry_hide_zone'] = $this->language->get('entry_hide_zone');
		$this->data['entry_fixed_zone'] = $this->language->get('entry_fixed_zone');
		$this->data['entry_hide_account_terms'] = $this->language->get('entry_hide_account_terms');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_template'] = $this->language->get('entry_template');

		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		
		$this->data['token'] = $this->session->data['token'];

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/simplified_checkout', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/simplified_checkout', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['simplified_checkout_status'])) {
			$this->data['simplified_checkout_status'] = $this->request->post['simplified_checkout_status'];
		} else {
			$this->data['simplified_checkout_status'] = $this->config->get('simplified_checkout_status');
		}
		
		if (isset($this->request->post['simplified_checkout_dynamic_shipping'])) {
			$this->data['simplified_checkout_dynamic_shipping'] = $this->request->post['simplified_checkout_dynamic_shipping'];
		} else {
			$this->data['simplified_checkout_dynamic_shipping'] = $this->config->get('simplified_checkout_dynamic_shipping');
		}
		
		if (isset($this->request->post['simplified_checkout_show_coupon'])) {
			$this->data['simplified_checkout_show_coupon'] = $this->request->post['simplified_checkout_show_coupon'];
		} else {
			$this->data['simplified_checkout_show_coupon'] = $this->config->get('simplified_checkout_show_coupon');
		}
		
		if (isset($this->request->post['simplified_checkout_hide_country'])) {
			$this->data['simplified_checkout_hide_country'] = $this->request->post['simplified_checkout_hide_country'];
		} else {
			$this->data['simplified_checkout_hide_country'] = $this->config->get('simplified_checkout_hide_country'); 
		} 

		if (isset($this->request->post['simplified_checkout_fixed_country'])) {
			$this->data['simplified_checkout_fixed_country'] = $this->request->post['simplified_checkout_fixed_country'];
		} else {
			$this->data['simplified_checkout_fixed_country'] = $this->config->get('simplified_checkout_fixed_country'); 
		}
		
		if (isset($this->request->post['simplified_checkout_hide_zone'])) {
			$this->data['simplified_checkout_hide_zone'] = $this->request->post['simplified_checkout_hide_zone'];
		} else {
			$this->data['simplified_checkout_hide_zone'] = $this->config->get('simplified_checkout_hide_zone'); 
		} 

		if (isset($this->request->post['simplified_checkout_fixed_zone'])) {
			$this->data['simplified_checkout_fixed_zone'] = $this->request->post['simplified_checkout_fixed_zone'];
		} else {
			$this->data['simplified_checkout_fixed_zone'] = $this->config->get('simplified_checkout_fixed_zone'); 
		}
		
		if (isset($this->request->post['simplified_checkout_hide_account_terms'])) {
			$this->data['simplified_checkout_hide_account_terms'] = $this->request->post['simplified_checkout_hide_account_terms'];
		} else {
			$this->data['simplified_checkout_hide_account_terms'] = $this->config->get('simplified_checkout_hide_account_terms'); 
		} 
		
		if (isset($this->error['fixed_country'])) {
			$this->data['error_fixed_country'] = $this->error['fixed_country'];
		} else {
			$this->data['error_fixed_country'] = '';
		}
		
		$this->data['countries'] = array();
		
		$this->load->model('localisation/country');
		$this->data['countries'] = $this->model_localisation_country->getCountries();
		
		$this->data['zones'] = array();
		$this->load->model('localisation/zone');
		
		if (isset($this->request->post['simplified_checkout_fixed_country'])) {
			$this->data['zones'] = $this->model_localisation_zone->getZonesByCountryId($this->request->post['simplified_checkout_fixed_country']);
		} elseif ($this->config->get('simplified_checkout_fixed_country')) {
			$this->data['zones'] = $this->model_localisation_zone->getZonesByCountryId($this->config->get('simplified_checkout_fixed_country'));
		}
		
		if (isset($this->request->post['simplified_checkout_template'])) {
			$this->data['simplified_checkout_template'] = $this->request->post['simplified_checkout_template'];
		} else {
			$this->data['simplified_checkout_template'] = $this->config->get('simplified_checkout_template'); 
		}
		

				
		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
		
		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		$this->template = 'module/simplified_checkout.tpl';
		$this->children = array(
			'common/header',
			'common/footer',
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/welcome')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if ($this->request->post['simplified_checkout_hide_country'] && !$this->request->post['simplified_checkout_fixed_country']) {
			$this->error['fixed_country'] = $this->language->get('error_fixed_country');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>