<?php
class ControllerPaymentAsiaPay extends Controller {
	protected function index() {
    	$this->data['button_confirm'] = $this->language->get('button_confirm');
		
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/payment/asiapay.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/payment/asiapay.tpl';
		} else {
			$this->template = 'default/template/payment/asiapay.tpl';
		}		
		
		$this->render();
	} 	
}
?>