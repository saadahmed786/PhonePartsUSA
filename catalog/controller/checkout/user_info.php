<?php  
class ControllerCheckoutUserInfo extends Controller { 
	public function index() {
		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$this->redirect($this->url->link('checkout/cart'));
		}

		// print_r($this->session->data['logged_in']);exit;

		if (isset($this->session->data['guest']['firstname'])) {
			$this->data['firstname'] = $this->session->data['guest']['firstname'];
		}
		elseif(isset($this->session->data['logged_in']))
		{

			$this->data['firstname'] = $this->session->data['logged_in']['firstname'];
		}
		elseif($this->customer->isLogged())
		{
			$this->data['firstname'] = $this->customer->getFirstName();
		}
		else {
			$this->data['firstname'] = '';
		}


		if (isset($this->session->data['guest']['lastname'])) {
			$this->data['lastname'] = $this->session->data['guest']['lastname'];
		}
		elseif(isset($this->session->data['logged_in']))
		{
			$this->data['lastname'] = $this->session->data['logged_in']['lastname'];
		}
		elseif($this->customer->isLogged())
		{
			$this->data['lastname'] = $this->customer->getLastName();
		}
		else {
			$this->data['lastname'] = '';
		}
		if($this->customer->getSource() or $this->customer->getTotalOrders()>=5)
		{
			$this->data['show_source']= false;
		}
		else
		{
			$this->data['show_source']= true;
		}


		if($this->customer->getBusinessType())
		{
			$this->data['show_business_type']= false;
		}
		else
		{
			$this->data['show_business_type']= true;
		}

		
		if (isset($this->session->data['guest']['email'])) {
			$this->data['email'] = $this->session->data['guest']['email'];
		}
		elseif(isset($this->session->data['first_step_email']))
		{
			$this->data['email'] = $this->session->data['first_step_email'];
		}
		 else {
			$this->data['email'] = '';
		}
		
		if (isset($this->session->data['guest']['telephone'])) {
			$this->data['telephone'] = $this->session->data['guest']['telephone'];		
		}
		elseif(isset($this->session->data['logged_in']))
		{
			$this->data['telephone'] = $this->session->data['logged_in']['telephone'];
		}
		elseif($this->customer->isLogged())
		{
			$this->data['telephone'] = $this->customer->getTelephone();
		}
		else {
			$this->data['telephone'] = '';
		}

		if (isset($this->session->data['guest']['fax'])) {
			$this->data['fax'] = $this->session->data['guest']['fax'];				
		} else {
			$this->data['fax'] = '';
		}

		if (isset($this->session->data['guest']['telephone_2'])) {
			$this->data['telephone_2'] = $this->session->data['guest']['telephone_2'];				
		}
		elseif(isset($this->session->data['logged_in']))
		{
			$this->data['telephone_2'] = $this->session->data['logged_in']['telephone_2'];
		}
		else {
			$this->data['telephone_2'] = '';
		}

		if (isset($this->session->data['guest']['payment']['company'])) {
			$this->data['company'] = $this->session->data['guest']['payment']['company'];			
		}
		elseif(isset($this->session->data['logged_in']))
		{
			$this->data['company'] = $this->session->data['logged_in']['payment']['company'];
		}
		elseif($this->customer->isLogged())
		{
			$this->data['company'] = $this->customer->getBusinessName();
		}
		else {
			$this->data['company'] = '';
		}

		$this->load->model('account/customer_group');

		$this->data['customer_groups'] = array();
		
		if (is_array($this->config->get('config_customer_group_display'))) {
			$customer_groups = $this->model_account_customer_group->getCustomerGroups();
			
			foreach ($customer_groups as $customer_group) {
				if (in_array($customer_group['customer_group_id'], $this->config->get('config_customer_group_display'))) {
					$this->data['customer_groups'][] = $customer_group;
				}
			}
		}
		
		if (isset($this->session->data['guest']['customer_group_id'])) {
    		$this->data['customer_group_id'] = $this->session->data['guest']['customer_group_id'];
		} else {
			$this->data['customer_group_id'] = $this->config->get('config_customer_group_id');
		}
		
		// Company ID
		if (isset($this->session->data['guest']['payment']['company_id'])) {
			$this->data['company_id'] = $this->session->data['guest']['payment']['company_id'];			
		} else {
			$this->data['company_id'] = '';
		}
		
		// Tax ID
		if (isset($this->session->data['guest']['payment']['tax_id'])) {
			$this->data['tax_id'] = $this->session->data['guest']['payment']['tax_id'];			
		} else {
			$this->data['tax_id'] = '';
		}
								
		if (isset($this->session->data['guest']['payment']['address_1'])) {
			$this->data['address_1'] = $this->session->data['guest']['payment']['address_1'];			
		} else {
			$this->data['address_1'] = '';
		}

		if (isset($this->session->data['guest']['payment']['address_2'])) {
			$this->data['address_2'] = $this->session->data['guest']['payment']['address_2'];			
		} else {
			$this->data['address_2'] = '';
		}

		if (isset($this->session->data['guest']['payment']['postcode'])) {
			$this->data['postcode'] = $this->session->data['guest']['payment']['postcode'];							
		} elseif (isset($this->session->data['shipping_postcode'])) {
			$this->data['postcode'] = $this->session->data['shipping_postcode'];			
		} else {
			$this->data['postcode'] = '';
		}
		
		if (isset($this->session->data['guest']['payment']['city'])) {
			$this->data['city'] = $this->session->data['guest']['payment']['city'];			
		} else {
			$this->data['city'] = '';
		}

		if (isset($this->session->data['guest']['payment']['country_id'])) {
			$this->data['country_id'] = $this->session->data['guest']['payment']['country_id'];			  	
		} elseif (isset($this->session->data['shipping_country_id'])) {
			$this->data['country_id'] = $this->session->data['shipping_country_id'];		
		} else {
			$this->data['country_id'] = $this->config->get('config_country_id');
		}

		if (isset($this->session->data['guest']['payment']['zone_id'])) {
			$this->data['zone_id'] = $this->session->data['guest']['payment']['zone_id'];	
		} elseif (isset($this->session->data['shipping_zone_id'])) {
			$this->data['zone_id'] = $this->session->data['shipping_zone_id'];						
		} else {
			$this->data['zone_id'] = '';
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
			
			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));
			
			if ($information_info) {
				$this->data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information', 'information_id=' . $this->config->get('config_checkout_id'), 'SSL'), $information_info['title'], $information_info['title']);
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
			// $this->data['first_name'] = $this->customer->getFirstName();
			// $this->data['last_name'] = $this->customer->getLastName();
			// $this->data['business_name'] = $this->customer->getBusinessName();
			// $this->data['phone'] = $this->customer->getTelephone();
			$this->data['email'] = $this->customer->getEmail();
		} else {
			$this->data['login_link'] = $this->url->link('account/login', 'redirect=checkout/checkout', 'SSL');
			$this->load->model('localisation/country');
			$this->data['countries'] = $this->model_localisation_country->getCountries();
			$this->load->model('localisation/zone');
			$this->data['zones'] = $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']);
		}
		$infos =array();
		if($this->customer->isLogged())
		{
			$this->load->model('account/address');
			$infos = $this->model_account_address->getContactInformations();

		}
		$this->data['infos'] = $infos;
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/user_info.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/user_info.tpl';
		} else {
			$this->template = 'default/template/checkout/user_info.tpl';
		}

		$this->response->setOutput($this->render());
	}
}
?>