<?php 
class ControllerCheckoutShippingAddress extends Controller {
	//print_r("here");exit;
	public function index() {
		$this->language->load('checkout/checkout');
		
		$this->data['text_address_existing'] = $this->language->get('text_address_existing');
		$this->data['text_address_new'] = $this->language->get('text_address_new');
		$this->data['text_select'] = $this->language->get('text_select');
		$this->data['text_none'] = $this->language->get('text_none');

		$this->data['entry_firstname'] = $this->language->get('entry_firstname');
		$this->data['entry_lastname'] = $this->language->get('entry_lastname');
		$this->data['entry_company'] = $this->language->get('entry_company');
		$this->data['entry_address_1'] = $this->language->get('entry_address_1');
		$this->data['entry_address_2'] = $this->language->get('entry_address_2');
		$this->data['entry_postcode'] = $this->language->get('entry_postcode');
		$this->data['entry_city'] = $this->language->get('entry_city');
		$this->data['entry_country'] = $this->language->get('entry_country');
		$this->data['entry_zone'] = $this->language->get('entry_zone');
	
		$this->data['button_continue'] = $this->language->get('button_continue');
			
		if (isset($this->session->data['shipping_address_id'])) {
			$this->data['address_id'] = $this->session->data['shipping_address_id'];
		} else {
			$this->data['address_id'] = $this->customer->getAddressId();
		}

		$this->load->model('account/address');

		if ($this->session->data['warehouse']) {
			$this->data['addresses'][0] = $this->model_account_address->getAddress($this->customer->getAddressId());
		} else {
			$this->data['addresses'] = $this->model_account_address->getAddresses();
		}

		if (isset($this->session->data['shipping_postcode'])) {
			$this->data['postcode'] = $this->session->data['shipping_postcode'];		
		} else {
			if(isset($this->session->data['guest']))
	{
	$this->data['postcode'] =$this->session->data['guest']['payment']['postcode'];	
	}
	else
	{
		$this->data['postcode']='';	
	}
		}
				
		if (isset($this->session->data['shipping_country_id'])) {
			$this->data['country_id'] = $this->session->data['shipping_country_id'];		
		} else {
			$this->data['country_id'] = $this->config->get('config_country_id');
		}
				
		if (isset($this->session->data['shipping_zone_id'])) {
			$this->data['zone_id'] = $this->session->data['shipping_zone_id'];		
		} else {
			if(isset($this->session->data['guest']))
	{
	$this->data['zone_id'] =$this->session->data['guest']['payment']['zone_id'];	
	}
	else
	{
		$this->data['zone_id']='';	
	}
		}
						
		$this->load->model('localisation/country');
		
		$this->data['countries'] = $this->model_localisation_country->getCountries();

		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/shipping_address.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/shipping_address.tpl';
		} else {
			$this->template = 'default/template/checkout/shipping_address.tpl';
		}
	
	if(isset($this->session->data['logged_in']['firstname']))
	{
		$this->data['firstname'] = $this->session->data['logged_in']['firstname'];
	}
	else
	{
		$this->data['firstname'] = '';
	}

	if(isset($this->session->data['logged_in']['lastname']))
	{
		$this->data['lastname'] = $this->session->data['logged_in']['lastname'];
	}
	else
	{
		$this->data['lastname'] = '';
	}			
				
	if(isset($this->session->data['guest']))
	{
	$this->data['xfirstname'] = $this->session->data['guest']['firstname'];	
	}
	else
	{
		$this->data['xfirstname']='';	
	}
	if(isset($this->session->data['guest']))
	{
	$this->data['xlastname'] = $this->session->data['guest']['lastname'];	
	}
	else
	{
		$this->data['xlastname']='';	
	}
	
	if(isset($this->session->data['guest']))
	{
	$this->data['xaddress_1'] = $this->session->data['guest']['payment']['address_1'];	
	}
	else
	{
		$this->data['xaddress_1']='';	
	}
	
	if(isset($this->session->data['guest']))
	{
	$this->data['xaddress_2'] = $this->session->data['guest']['payment']['address_2'];	
	}
	else
	{
		$this->data['xaddress_2']='';	
	}
	
	if(isset($this->session->data['guest']))
	{
	$this->data['xcity'] = $this->session->data['guest']['payment']['city'];	
	}
	else
	{
		$this->data['xcity']='';	
	}
	if(isset($this->session->data['guest']))
	{
	$this->data['xtelephone'] = $this->session->data['guest']['telephone'];	
	}
	else
	{
		$this->data['xtelephone']='';	
	}
	
	
	
	
	
	
	if(isset($this->session->data['guest']))
	{
	$this->data['xcompany_id'] =$this->session->data['guest']['payment']['company_id'];	
	}
	else
	{
		$this->data['xcompany_id']='';	
	}
	
	if(isset($this->session->data['guest']))
	{
	$this->data['xtax_id'] =$this->session->data['guest']['payment']['tax_id'];	
	}
	else
	{
		$this->data['xtax_id']='';	
	}
	
	
	if(isset($this->session->data['guest']))
	{
	$this->data['xcountry_id'] =$this->session->data['guest']['payment']['country_id'];	
	}
	else
	{
		$this->data['xcountry_id']='';	
	}
				
		$this->response->setOutput($this->render());
  	}	
	
	public function validate() {
		$this->language->load('checkout/checkout');
		
		$json = array();
		
		// Validate if customer is logged in.
		if (!$this->customer->isLogged()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
		}
		
		// Validate if shipping is required. If not the customer should not have reached this page.
		if (!$this->cart->hasShipping()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
		}
		
		// Validate cart has products and has stock.		
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart');
		}	

		// Validate minimum quantity requirments.			
		$products = $this->cart->getProducts();
				
		foreach ($products as $product) {
			$product_total = 0;
				
			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}		
			
			if ($product['minimum'] > $product_total) {
				$json['redirect'] = $this->url->link('checkout/cart');
				
				break;
			}				
		}
								
		if (!$json) {
			// Commented by Zaman reason (when address is changed on logged in step 2, it should not update shipping methods by selected address)
			// if ($this->request->post['shipping_address'] == 'existing') {
			// 	$this->load->model('account/address');
				
			// 	if (empty($this->request->post['address_id'])) {
			// 		$json['error']['warning'] = $this->language->get('error_address');
			// 	} elseif (!in_array($this->request->post['address_id'], array_keys($this->model_account_address->getAddresses()))) {
			// 		$json['error']['warning'] = $this->language->get('error_address');
			// 	}
						
			// 	if (!$json) {			
			// 		$this->session->data['shipping_address_id'] = $this->request->post['address_id'];
					
			// 		// Default Shipping Address
			// 		$this->load->model('account/address');

			// 		$address_info = $this->model_account_address->getAddress($this->request->post['address_id']);
					
			// 		if ($address_info) {
			// 			$this->session->data['shipping_country_id'] = $address_info['country_id'];
			// 			$this->session->data['shipping_zone_id'] = $address_info['zone_id'];
			// 			$this->session->data['shipping_postcode'] = $address_info['postcode'];						
			// 		} else {
			// 			unset($this->session->data['shipping_country_id']);	
			// 			unset($this->session->data['shipping_zone_id']);	
			// 			unset($this->session->data['shipping_postcode']);
			// 		}
					
					
			// 	}
			// } 
			
			// if ($this->request->post['shipping_address'] == 'new') {
				if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32) || !preg_match("/^[a-zA-Z0-9\,\-\.\'\`?: ]*$/", $this->request->post['firstname'])) {
					$json['error']['firstname'] = $this->language->get('error_firstname');
				}
		
				if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32) || !preg_match("/^[a-zA-Z0-9\,\-\.\'\`?: ]*$/", $this->request->post['lastname'])) {
					$json['error']['lastname'] = $this->language->get('error_lastname');
				}
		
				if ((utf8_strlen($this->request->post['address_1']) < 3) || (utf8_strlen($this->request->post['address_1']) > 128)) {
					$json['error']['address_1'] = $this->language->get('error_address_1');
				}

				if ((utf8_strlen($this->request->post['address_2']) != '') && !preg_match("/^[a-zA-Z0-9\,\.\'\`?: ]*$/", $this->request->post['address_2'])) {
					// $json['error']['address_1'] = $this->language->get('error_address_1');
				}
		
				if ((utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 128)) {
					$json['error']['city'] = $this->language->get('error_city');
				}
				if ((utf8_strlen($this->request->post['telephone']) < 5) || (utf8_strlen($this->request->post['telephone']) > 12)) {
					// $json['error']['telephone'] = $this->language->get('error_address_1');
				}
				$this->load->model('localisation/country');
				
				$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);
				
				if ($country_info && $country_info['postcode_required'] && (utf8_strlen($this->request->post['postcode']) < 2) || (utf8_strlen($this->request->post['postcode']) > 10)) {
					$json['error']['postcode'] = $this->language->get('error_postcode');
				}
				
				if ($this->request->post['country_id'] == '') {
					$json['error']['country'] = $this->language->get('error_country');
				}
				
				if ($this->request->post['zone_id'] == '') {
					$json['error']['zone'] = $this->language->get('error_zone');
				}
				
				if (!$json) {						
					// Default Shipping Address
					$this->load->model('account/address');		
					// if($this->request->post['shipping_address']=='new')
					// {
						$this->session->data['shipping_address_id'] = $this->model_account_address->addAddress($this->request->post);
					// }
					// else
					// {

					// $this->session->data['shipping_address_id'] = $this->request->post['address_id'];
					// }
					$this->db->query("UPDATE ".DB_PREFIX."customer SET address_id='".$this->session->data['shipping_address_id']."' WHERE customer_id='".$this->customer->getId()."' ");

					$this->session->data['shipping_country_id'] = $this->request->post['country_id'];
					$this->session->data['shipping_zone_id'] = $this->request->post['zone_id'];
					$this->session->data['shipping_postcode'] = $this->request->post['postcode'];
									
					// unset($this->session->data['shipping_method']);						
					// unset($this->session->data['shipping_methods']);
				}
			// }
		}
		if (isset($this->request->post['comment']))
				$this->session->data['logged_comment'] = strip_tags($this->request->post['comment']);
		$this->response->setOutput(json_encode($json));
	}
	public function validate_logged_in()
	{
		$this->language->load('checkout/checkout');

		$json = array();
		
		// Validate if customer is not logged in.
		if (!$this->customer->isLogged()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
		} 			
		
		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
			$json['redirect'] = $this->url->link('checkout/cart');		
		}
		
		// // Check if guest checkout is avaliable.			
		// if (!$this->config->get('config_guest_checkout') || $this->config->get('config_customer_price') || $this->cart->hasDownload()) {
		// 	$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
		// } 
					
		if (!$json) {
			if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32)) {
				$json['error']['firstname'] = $this->language->get('error_firstname');
			}
	
			if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32)) {
				$json['error']['lastname'] = $this->language->get('error_lastname');
			}
	
			if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
				$json['error']['email'] = $this->language->get('error_email');
			}

			if ($this->request->post['email'] != $this->request->post['confirmEmail'] && isset($this->request->post['confirmEmail'])) {
				// $json['error']['confirmEmail'] = 'Email confirmation does not match email';
			}
			
			if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
				$json['error']['telephone'] = $this->language->get('error_telephone');
			}

			if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
				$json['error']['telephone'] = $this->language->get('error_telephone');
			}

			if (!isset($this->request->post['agree']) || $this->request->post['agree'] == 0) {
				$json['error']['agree'] = 'Please read and agree to our Terms & Conditions to proceed.';
			}

			if(isset($this->request->post['source']) && $this->request->post['source']=='')
			{
				$json['error']['source'] = 'Please let us know about the source of information';	
			}
				if(isset($this->request->post['business_type']) && $this->request->post['business_type']=='')
			{
				$json['error']['business_type'] = 'Please let us know about the type of your business';	
			}

			if(isset($this->request->post['business_type']) && $this->request->post['business_type']!='Not a Business' && utf8_strlen($this->request->post['company']) < 3)
			{
				$json['error']['company'] = 'Please let us know about the name of your company';	
			}


			if (!$json) {

			$this->session->data['logged_in']['firstname'] = $this->request->post['firstname'];
			$this->session->data['logged_in']['lastname'] = $this->request->post['lastname'];
			$this->session->data['logged_in']['email'] = $this->request->post['email'];
			$this->session->data['logged_in']['telephone'] = $this->request->post['telephone'];
			$this->session->data['logged_in']['telephone_2'] = $this->request->post['telephone_2'];
			$this->session->data['logged_in']['fax'] = $this->request->post['fax'];
			$this->session->data['logged_in']['payment']['company'] = $this->request->post['company'];

			$_data = array();
			$_data['firstname'] = $this->request->post['firstname'];
			$_data['lastname'] = $this->request->post['lastname'];
			$_data['company'] = $this->session->data['logged_in']['company'];  
			$_data['address_1'] = $this->request->post['address_1'];
			$_data['address_2'] = $this->request->post['address_2'];
			$_data['postcode'] = $this->request->post['postcode'];
			$_data['city'] = $this->request->post['city'];
			$_data['zone_id'] = $this->request->post['zone_id'];
			$_data['country_id'] = $this->request->post['country_id'];


			// print_r($_data);exit;

					$this->load->model('account/address');	
					$address_id = $this->db->query("SELECT telephone FROM ".DB_PREFIX."customer where customer_id='".(int)$this->customer->getId()."'");


					

					if($address_id->row['telephone']=='')
					{
						$this->db->query("UPDATE ".DB_PREFIX."customer SET telephone='".$this->db->escape($this->request->post['telephone'])."'WHERE customer_id='".$this->customer->getId()."'");
					}
					if(isset($this->request->post['source']))
					{
				$this->db->query("INSERT INTO ".DB_PREFIX."customer_source SET source='".$this->db->escape($this->request->post['source'])."',email='".$this->db->escape($this->request->post['email'])."' ");
					}
					if(isset($this->request->post['business_type']))
					{
					$this->db->query("INSERT INTO ".DB_PREFIX."customer_source SET source='".$this->db->escape($this->request->post['business_type'])."',email='".$this->db->escape($this->request->post['email'])."',`type`='business_type' ");
					}
					// $this->session->data['shipping_address_id'] = $this->model_account_address->addAddress($_data);
					// $this->session->data['shipping_country_id'] = $this->request->post['country_id'];
					// $this->session->data['shipping_zone_id'] = $this->request->post['zone_id'];
					// $this->session->data['shipping_postcode'] = $this->request->post['postcode'];
		}
	}

		$this->response->setOutput(json_encode($json));	
	
	}
	function getAddress()
	{
		$this->load->model('account/address');
		$address_id = $this->request->post['address_id'];

		$address = $this->model_account_address->getAddress($address_id);
		$json = array();
		if(!$address)
		{
			$json['error'] = 'No Address Found!';
		}
		else
		{
			$json=$address;
		}
		$this->response->setOutput(json_encode($json));	
	}
}
?>