<?php 
class ControllerTotalInsuranceFee extends Controller { 
	private $error = array(); 
	 
	public function index() { 
		
		$this->redirect($this->url->link('module/onecheckout', 'token=' . $this->session->data['token'], 'SSL'));
		
		$this->load->language('total/insurance_fee');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			
			$this->model_setting_setting->editSetting('insurance_fee', $this->request->post);
		
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL'));
		}
		
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_none'] = $this->language->get('text_none');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		$this->data['text_select'] = $this->language->get('text_select');
		$this->data['text_insurance_fee_offer'] = $this->language->get('text_insurance_fee_offer');
		$this->data['text_insurance_fee_opt'] = $this->language->get('text_insurance_fee_opt');
		$this->data['text_insurance_fee_required'] = $this->language->get('text_insurance_fee_required');
		
		$this->data['entry_insurance_fee_percentage'] = $this->language->get('entry_insurance_fee_percentage');
		$this->data['entry_insurance_fee_fixed_fee'] = $this->language->get('entry_insurance_fee_fixed_fee');
		$this->data['entry_insurance_fee_selection'] = $this->language->get('entry_insurance_fee_selection');
		$this->data['entry_insurance_fee_geo_zone'] = $this->language->get('entry_insurance_fee_geo_zone');
		
		$this->data['entry_insurance_fee_status'] = $this->language->get('entry_insurance_fee_status');
		$this->data['entry_insurance_fee_sort_order'] = $this->language->get('entry_insurance_fee_sort_order');
					
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
			'href'      => $this->url->link('total/insurance_fee', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('total/insurance_fee', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/total', 'token=' . $this->session->data['token'], 'SSL');		

		if (isset($this->request->post['insurance_fee_percentage'])) {
			$this->data['insurance_fee_percentage'] = $this->request->post['insurance_fee_percentage'];
		} else {
			$this->data['insurance_fee_percentage'] = $this->config->get('insurance_fee_percentage');
		}
		
		if (isset($this->request->post['insurance_fee_fixed_fee'])) {
			$this->data['insurance_fee_fixed_fee'] = $this->request->post['insurance_fee_fixed_fee'];
		} else {
			$this->data['insurance_fee_fixed_fee'] = $this->config->get('insurance_fee_fixed_fee');
		}
		
		if (isset($this->request->post['insurance_fee_selection'])) {
			$this->data['insurance_fee_selection'] = $this->request->post['insurance_fee_selection'];
		} else {
			$this->data['insurance_fee_selection'] = $this->config->get('insurance_fee_selection');
		}
							
		$this->load->model('localisation/geo_zone');
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['insurance_fee_geo_zone_id'])) {
			$this->data['insurance_fee_geo_zone_id'] = $this->request->post['insurance_fee_geo_zone_id'];
		} else {
			$this->data['insurance_fee_geo_zone_id'] = $this->config->get('insurance_fee_geo_zone_id');
		}
		
		
		if (isset($this->request->post['insurance_fee_status'])) {
			$this->data['insurance_fee_status'] = $this->request->post['insurance_fee_status'];
		} else {
			$this->data['insurance_fee_status'] = $this->config->get('insurance_fee_status');
		}
		
		if (isset($this->request->post['insurance_fee_sort_order'])) {
			$this->data['insurance_fee_sort_order'] = $this->request->post['insurance_fee_sort_order'];
		} else {
			$this->data['insurance_fee_sort_order'] = $this->config->get('insurance_fee_sort_order');
		}
		
		$this->template = 'total/insurance_fee.tpl';
		$this->children = array(
			'common/header',	
			'common/footer'	
		);
		
		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'total/insurance_fee')) {
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