<?php 
class ControllerPaymentgoogle extends Controller {
	private $error = array(); 
	 
	public function index() { 
		$this->load->language('payment/google');
		
		$this->document->setTitle($this->language->get('heading_title'));

		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->model_setting_setting->editSetting('google', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}
		
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		$this->data['text_yes'] = $this->language->get('text_yes');
		$this->data['text_no'] = $this->language->get('text_no');
				
		$this->data['entry_order_status'] = $this->language->get('entry_order_status');		
		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$this->data['entry_production'] = $this->language->get('entry_production');
		$this->data['entry_merchant_id'] = $this->language->get('entry_merchant_id');
		$this->data['entry_merchant_key'] = $this->language->get('entry_merchant_key');			
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		$this->data['tab_general'] = $this->language->get('tab_general');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

  		$this->document->breadcrumbs = array();

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

			'href'      => $this->url->link('payment/google', 'token=' . $this->session->data['token'], 'SSL'),

      		'separator' => ' :: '

   		);		
		
		$this->data['action'] = $this->url->link('payment/google', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
		
		
		if (isset($this->request->post['google_order_status_id'])) {
			$this->data['google_order_status_id'] = $this->request->post['google_order_status_id'];
		} else {
			$this->data['google_order_status_id'] = $this->config->get('google_order_status_id'); 
		} 
		
		$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['google_geo_zone_id'])) {
			$this->data['google_geo_zone_id'] = $this->request->post['google_geo_zone_id'];
		} else {
			$this->data['google_geo_zone_id'] = $this->config->get('google_geo_zone_id'); 
		} 
		
		$this->load->model('localisation/geo_zone');						
		
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['google_status'])) {
			$this->data['google_status'] = $this->request->post['google_status'];
		} else {
			$this->data['google_status'] = $this->config->get('google_status');
		}
		
		if (isset($this->request->post['google_sort_order'])) {
			$this->data['google_sort_order'] = $this->request->post['google_sort_order'];
		} else {
			$this->data['google_sort_order'] = $this->config->get('google_sort_order');
		}
		
		if (isset($this->request->post['google_production'])) {
			$this->data['google_production'] = $this->request->post['google_production'];
		} else {
			$this->data['google_production'] = $this->config->get('google_production');
		}
		
		if (isset($this->request->post['google_merchant_id'])) {
			$this->data['google_merchant_id'] = $this->request->post['google_merchant_id'];
		} else {
			$this->data['google_merchant_id'] = $this->config->get('google_merchant_id');
		}	
		
		if (isset($this->request->post['google_merchant_key'])) {
			$this->data['google_merchant_key'] = $this->request->post['google_merchant_key'];
		} else {
			$this->data['google_merchant_key'] = $this->config->get('google_merchant_key');
		}	
		
		$this->template = 'payment/google.tpl';
		$this->children = array(
			'common/header',	
			'common/footer'	
		);
		
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/google')) {
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