<?php 
class ControllerTotalOneCheckoutPaymentFee extends Controller { 
	private $error = array(); 
	 
	public function index() { 
		$this->redirect($this->url->link('module/onecheckout', 'token=' . $this->session->data['token'], 'SSL'));
		
		$this->load->language('total/onecheckout_payment_fee');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			if (isset($this->request->post['onecheckout_payment_fee_types']) && (substr(VERSION,0,5) == '1.5.0')) {
				$this->request->post['onecheckout_payment_fee_types'] = serialize($this->request->post['onecheckout_payment_fee_types']);
			}
			
			$this->model_setting_setting->editSetting('onecheckout_payment_fee', $this->request->post);
		
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL'));
		}
		
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_none'] = $this->language->get('text_none');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		$this->data['text_select'] = $this->language->get('text_select');
		
		$this->data['entry_onecheckout_payment_fee_total'] = $this->language->get('entry_onecheckout_payment_fee_total');
		$this->data['entry_onecheckout_payment_fee_percentage'] = $this->language->get('entry_onecheckout_payment_fee_percentage');
		$this->data['entry_onecheckout_payment_fee_fixed_fee'] = $this->language->get('entry_onecheckout_payment_fee_fixed_fee');
		$this->data['entry_onecheckout_payment_fee_payment_type'] = $this->language->get('entry_onecheckout_payment_fee_payment_type');
		$this->data['entry_onecheckout_payment_fee_geo_zone'] = $this->language->get('entry_onecheckout_payment_fee_geo_zone');
		
		$this->data['entry_onecheckout_payment_fee_tax'] = $this->language->get('entry_onecheckout_payment_fee_tax');
		$this->data['entry_onecheckout_payment_fee_status'] = $this->language->get('entry_onecheckout_payment_fee_status');
		$this->data['entry_onecheckout_payment_fee_sort_order'] = $this->language->get('entry_onecheckout_payment_fee_sort_order');
					
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
 
		$this->data['tab_general'] = $this->language->get('tab_general');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

   		$this->data['breadcrumbs'] = array();
		
   		$this->data['breadcrumbs'][] = array(
       		'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);
		
		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_total'),
			'href'      => $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('total/onecheckout_payment_fee', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('total/onecheckout_payment_fee', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL');
		
  		if (isset($this->request->post['onecheckout_payment_fee_total'])) {
			$this->data['onecheckout_payment_fee_total'] = $this->request->post['onecheckout_payment_fee_total'];
		} else {
			$this->data['onecheckout_payment_fee_total'] = $this->config->get('onecheckout_payment_fee_total');
		}
		
		if (isset($this->request->post['onecheckout_payment_fee_percentage'])) {
			$this->data['onecheckout_payment_fee_percentage'] = $this->request->post['onecheckout_payment_fee_percentage'];
		} else {
			$this->data['onecheckout_payment_fee_percentage'] = $this->config->get('onecheckout_payment_fee_percentage');
		}
		
		if (isset($this->request->post['onecheckout_payment_fee_fixed_fee'])) {
			$this->data['onecheckout_payment_fee_fixed_fee'] = $this->request->post['onecheckout_payment_fee_fixed_fee'];
		} else {
			$this->data['onecheckout_payment_fee_fixed_fee'] = $this->config->get('onecheckout_payment_fee_fixed_fee');
		}
		
		if (isset($this->request->post['onecheckout_payment_fee_types'])) {
			$this->data['onecheckout_payment_fee_types'] = $this->request->post['onecheckout_payment_fee_types'];
		} elseif ($this->config->has('onecheckout_payment_fee_types')) {
			if(substr(VERSION,0,5) == '1.5.0'){
				$this->data['onecheckout_payment_fee_types'] = unserialize($this->config->get('onecheckout_payment_fee_types'));
			} else {
				$this->data['onecheckout_payment_fee_types'] = $this->config->get('onecheckout_payment_fee_types');
			}
		} else { 	
			$this->data['onecheckout_payment_fee_types'] = array();
		}
					
		$this->load->model('localisation/geo_zone');
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['onecheckout_payment_fee_geo_zone_id'])) {
			$this->data['onecheckout_payment_fee_geo_zone_id'] = $this->request->post['onecheckout_payment_fee_geo_zone_id'];
		} else {
			$this->data['onecheckout_payment_fee_geo_zone_id'] = $this->config->get('onecheckout_payment_fee_geo_zone_id');
		}
		
		$this->load->model('localisation/tax_class');
		$this->data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
		
		if (isset($this->request->post['onecheckout_payment_fee_tax_class_id'])) {
			$this->data['onecheckout_payment_fee_tax_class_id'] = $this->request->post['onecheckout_payment_fee_tax_class_id'];
		} else {
			$this->data['onecheckout_payment_fee_tax_class_id'] = $this->config->get('onecheckout_payment_fee_tax_class_id');
		}
		
		if (isset($this->request->post['onecheckout_payment_fee_status'])) {
			$this->data['onecheckout_payment_fee_status'] = $this->request->post['onecheckout_payment_fee_status'];
		} else {
			$this->data['onecheckout_payment_fee_status'] = $this->config->get('onecheckout_payment_fee_status');
		}
		
		if (isset($this->request->post['onecheckout_payment_fee_sort_order'])) {
			$this->data['onecheckout_payment_fee_sort_order'] = $this->request->post['onecheckout_payment_fee_sort_order'];
		} else {
			$this->data['onecheckout_payment_fee_sort_order'] = $this->config->get('onecheckout_payment_fee_sort_order');
		}
		
		$this->load->model('setting/extension');
		$payment_types = $this->model_setting_extension->getInstalled('payment');

		foreach ($payment_types as $key => $value) {
			$this->load->language('payment/' . $value);
				$this->data['payment_types'][] = array(
				'name'       => $this->language->get('heading_title'),
				'paymentkey' => $value
				);
		}
		
		$this->template = 'total/onecheckout_payment_fee.tpl';
		$this->children = array(
			'common/header',	
			'common/footer'	
		);
		
		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'total/onecheckout_payment_fee')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
}
?>