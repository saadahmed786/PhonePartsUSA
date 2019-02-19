<?php
class ControllerModuleIOM extends Controller {
	private $error = array(); 
	
	private function loadSetting($setting, $default = 0) {
		return isset($this->request->post[$setting]) ? $this->request->post[$setting] : (($this->config->get($setting)) ? $this->config->get($setting) : $default);
	}
	
	public function index() {   
		$this->load->language('module/iom');
			
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('iom', $this->request->post);		
					 
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}
				
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data = $this->language->load('module/iom');

		$this->data['text_yes'] = $this->language->get('text_yes');
		$this->data['text_no'] = $this->language->get('text_no');
		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array('text' => $this->language->get('text_home'), 'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'), 'separator' => false);
   		$this->data['breadcrumbs'][] = array('text' => $this->language->get('text_module'), 'href' => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'), 'separator' => ' :: ');
   		$this->data['breadcrumbs'][] = array('text' => $this->language->get('heading_title'), 'href' => $this->url->link('module/iom', 'token=' . $this->session->data['token'], 'SSL'), 'separator' => ' :: ');
		$this->data['action'] = $this->url->link('module/iom', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		$this->load->model('localisation/order_status');
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
 
		$this->data['iom_inventory_auto_return'] = $this->loadSetting('iom_inventory_auto_return', array());
		$this->data['iom_inventory_auto_reserve'] = $this->loadSetting('iom_inventory_auto_reserve', array());
			
		$this->load->model('localisation/order_product_status');
		$this->data['order_product_statuses'] = $this->model_localisation_order_product_status->getOrderProductStatuses();

		//ORDER PRODUCT STATUS
		$this->data['iom_inventory_ops_pending']     = $this->loadSetting('iom_inventory_ops_pending', 0);
		$this->data['iom_inventory_ops_backordered'] = $this->loadSetting('iom_inventory_ops_backordered', 0);
		$this->data['iom_inventory_ops_partialship'] = $this->loadSetting('iom_inventory_ops_partailship', 0);
		$this->data['iom_inventory_ops_reserved']    = $this->loadSetting('iom_inventory_ops_reserved', 0);
		$this->data['iom_inventory_ops_ordered']     = $this->loadSetting('iom_inventory_ops_ordered', 0);
		$this->data['iom_inventory_ops_cancelled']   = $this->loadSetting('iom_inventory_ops_cancelled', 0);
		$this->data['iom_inventory_ops_shipped']     = $this->loadSetting('iom_inventory_ops_shipped', 0);
		
		//ADDITIONAL ORDER STATUSES
		$this->data['iom_inventory_os_shipready']            = $this->loadSetting('iom_inventory_os_shipready', 0);
		$this->data['iom_inventory_os_inventoryrequired']    = $this->loadSetting('iom_inventory_os_inventoryrequired', 0);
		
		
		$this->template = 'module/iom.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/iom')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>