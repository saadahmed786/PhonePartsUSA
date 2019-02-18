<?php  
class ControllerCheckoutShipDetailsMethod extends Controller { 
	public function index() {
		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$this->redirect($this->url->link('checkout/cart'));
		}

		$this->language->load('checkout/checkout');
		$this->data['entry_shipping'] = $this->language->get('entry_shipping');
		$this->data['entry_newsletter'] = sprintf($this->language->get('entry_newsletter'), $this->config->get('config_name'));

		if (isset($this->session->data['shipping_country_id'])) {
			$this->data['country_id'] = $this->session->data['shipping_country_id'];		
		} else {	
			$this->data['country_id'] = $this->config->get('config_country_id');
		}

		if (isset($this->session->data['shipping_zone_id'])) {
			$this->data['zone_id'] = $this->session->data['shipping_zone_id'];			
		} else {
			$this->data['zone_id'] = '';
		}
		
		if ($this->config->get('config_account_id')) {
			$this->load->model('catalog/information');
			
			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
			
			if ($information_info) {
				$this->data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/info', 'information_id=' . $this->config->get('config_account_id'), 'SSL'), $information_info['title'], $information_info['title']);
			} else {
				$this->data['text_agree'] = '';
			}
		} else {
			$this->data['text_agree'] = '';
		}
		// Validate minimum quantity requirments.			
		$products = $this->cart->getProducts();
		
		$this->data['logged'] = $this->customer->isLogged();
		$this->data['shipping_required'] = $this->cart->hasShipping();
		if ($this->customer->isLogged()) {
			$this->data['first_name'] = $this->customer->getFirstName();
			$this->data['last_name'] = $this->customer->getLastName();
			$this->data['business_name'] = $this->customer->getBusinessName();
			$this->data['phone'] = $this->customer->getTelephone();
			$this->data['email'] = $this->customer->getEmail();
		} else {
			$this->data['login_link'] = $this->url->link('account/login', 'redirect=checkout/checkout', 'SSL');
			$this->load->model('localisation/country');
			$this->data['countries'] = $this->model_localisation_country->getCountries();
		}

		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/ship_details_method.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/ship_details_method.tpl';
		} else {
			$this->template = 'default/template/checkout/ship_details_method.tpl';
		}

		$this->response->setOutput($this->render());
	}
}
?>