<?php 
class ControllerAccountDashboard extends Controller {
	public function index() {
    	if (!$this->customer->isLogged()) {
	  		$this->session->data['redirect'] = $this->url->link('account/account', '', 'SSL');

	  		$this->redirect($this->url->link('account/login', '', 'SSL')); 
    	}    	
	
		
		$this->session->data['viewvouchers'] = 'child';
		$this->session->data['vieworder'] = 'child';
		$this->session->data['viewlbb'] = 'child';
		$this->session->data['viewreturn'] = 'child';
		$this->session->data['vieworder'] = 'child';
		$this->data['customer_name'] = $this->customer->getFirstName();
		$this->data['customer_email'] = $this->customer->getEmail();
		$this->data['customer_business'] = $this->customer->getBusinessName();
		$this->data['telephone'] = $this->customer->getTelephone();

		$this->data['order'] = $this->url->link('account/order', '', 'SSL');
    	$this->data['lbb'] = $this->url->link('account/lbb', '', 'SSL');
    	$this->data['returns'] = $this->url->link('account/returns', '', 'SSL');
    	$this->data['download'] = $this->url->link('account/download', '', 'SSL');
		$this->data['return'] = $this->url->link('account/return', '', 'SSL');
		$this->data['transaction'] = $this->url->link('account/transaction', '', 'SSL');
		$this->data['newsletter'] = $this->url->link('account/newsletter', '', 'SSL');
		$this->data['viewvouchers'] = $this->url->link('account/viewvouchers', '', 'SSL');


		$this->data['orders'] = $this->getChild('account/order');
		$this->data['vouchers'] = $this->getChild('account/viewvouchers');
		$this->data['buyback'] = $this->getChild('account/lbb');
		$this->data['template_returns'] = $this->getChild('account/returns');

		unset($this->session->data['viewvouchers']);
		unset($this->session->data['vieworder']);
		unset($this->session->data['viewlbb']);
		unset($this->session->data['viewreturn']);
		unset($this->session->data['vieworder']);
		
		
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/dashboard.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/dashboard.tpl';
		} else {
			$this->template = 'default/template/account/dashboard.tpl';
		}
				
		$this->response->setOutput($this->render());		
	}
	
				
		
}
?>