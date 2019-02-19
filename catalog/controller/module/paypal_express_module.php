<?php
class ControllerModulePaypalExpressModule extends Controller {
	protected function index($setting = array()) {

		$classname = str_replace('vq2-catalog_controller_module_', '', basename(__FILE__, '.php'));
		$store_url = ($this->config->get('config_ssl') && !is_numeric($this->config->get('config_ssl')) ? $this->config->get('config_ssl') : $this->config->get('config_url'));
	
		$this->language->load('module/' . $classname);

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['href'] = ($store_url . 'index.php?route=payment/paypal_express_new/SetExpressCheckout');
		
		if (empty($setting)) { //v14x
			$this->data['oc_version'] = 'v14x';
			
			$this->data['error'] = (isset($this->session->data['ppx']['error'])) ? ($this->language->get('error_title') . $this->session->data['ppx']['error']) : null;

			$this->data['module'] = $this->config->get($classname . '_module');

			unset($this->session->data['ppx']['error']);

			$this->id       = $classname;
	
		} else { //v151x
			$this->data['oc_version'] = 'v15x';
																		
			$this->data['wrapper'] = false;
			if (isset($setting['wrapper']) && $setting['wrapper']) {
				$this->data['wrapper'] = true;
			}
		}
		
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/' . $classname . '.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/' . $classname . '.tpl';
		} else {
			$this->template = 'default/template/module/' . $classname . '.tpl';
		}

		$this->render();
			
  	}
}
?>