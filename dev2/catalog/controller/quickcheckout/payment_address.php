<?php 
class ControllerQuickcheckoutPaymentAddress extends Controller {
	public function index() {
		$this->language->load('checkout/checkout');
		
		$this->data['text_address_existing'] = $this->language->get('text_address_existing');
		$this->data['text_address_new'] = $this->language->get('text_address_new');
		$this->data['text_select'] = $this->language->get('text_select');
		$this->data['text_none'] = $this->language->get('text_none');

		$this->data['entry_firstname'] = $this->language->get('entry_firstname');
		$this->data['entry_lastname'] = $this->language->get('entry_lastname');
		$this->data['entry_company'] = $this->language->get('entry_company');
		$this->data['entry_company_id'] = $this->language->get('entry_company_id');
		$this->data['entry_tax_id'] = $this->language->get('entry_tax_id');			
		$this->data['entry_address_1'] = $this->language->get('entry_address_1');
		$this->data['entry_address_2'] = $this->language->get('entry_address_2');
		$this->data['entry_postcode'] = $this->language->get('entry_postcode');
		$this->data['entry_city'] = $this->language->get('entry_city');
		$this->data['entry_country'] = $this->language->get('entry_country');
		$this->data['entry_zone'] = $this->language->get('entry_zone');
	
		$this->data['button_continue'] = $this->language->get('button_continue');

		if (isset($this->session->data['payment_address_id'])) {
			$this->data['address_id'] = $this->session->data['payment_address_id'];
		} else {
			$this->data['address_id'] = $this->customer->getAddressId();

        $this->session->data['payment_address_id'] = $this->customer->getAddressId();
      
		}
		
		$this->data['addresses'] = array();
		
		$this->load->model('account/address');
		
		$this->data['addresses'] = $this->model_account_address->getAddresses();
		
		$this->load->model('account/customer_group');
		
		$customer_group_info = $this->model_account_customer_group->getCustomerGroup($this->customer->getCustomerGroupId());
		
		if ($customer_group_info) {
			$this->data['company_id_display'] = $customer_group_info['company_id_display'];
		} else {
			$this->data['company_id_display'] = '';
		}
		
		if ($customer_group_info) {
			$this->data['company_id_required'] = $customer_group_info['company_id_required'];
		} else {
			$this->data['company_id_required'] = '';
		}
				
		if ($customer_group_info) {
			$this->data['tax_id_display'] = $customer_group_info['tax_id_display'];
		} else {
			$this->data['tax_id_display'] = '';
		}
		
		if ($customer_group_info) {
			$this->data['tax_id_required'] = $customer_group_info['tax_id_required'];
		} else {
			$this->data['tax_id_required'] = '';
		}
										
		if (isset($this->session->data['payment_country_id'])) {
			$this->data['country_id'] = $this->session->data['payment_country_id'];		
		} else {
			$this->data['country_id'] = $this->config->get('config_country_id');
		}
				
		if (isset($this->session->data['payment_zone_id'])) {
			$this->data['zone_id'] = $this->session->data['payment_zone_id'];		
		} else {
			$this->data['zone_id'] = '';
		}
		
		$this->load->model('localisation/country');
		
		$this->data['countries'] = $this->model_localisation_country->getCountries();
	
		
        $config = $this->config->get('quickcheckout');
 		
          
		  if($config['quickcheckout_display']){$quickcheckout = 'quickcheckout';}else{ $quickcheckout= 'checkout';}
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/'.$quickcheckout.'/payment_address.tpl')) {
          $this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/'.$quickcheckout.'/payment_address.tpl';
		  		  } else {
			$this->template = 'default/template/'.$quickcheckout.'/payment_address.tpl';
      



		}
	
		$this->response->setOutput($this->render());			
  	}
	

        public function check() {
		  $config = $this->config->get('quickcheckout');  
 		  if(isset($this->session->data['payment_address_id']) &&  isset($this->request->post['payment_address']) && $config['register_shipping_address_enable'] == 0 ){
		  	$this->session->data['payment_address'] = $this->request->post['payment_address'];
		  }
          
          $this->language->load('checkout/checkout');
          
          $json = array();
          
          
          if (!$json) {
          
          if ($this->request->post['payment_address'] == 'new') {
          if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32)) {
		   /* DV Code */
          if($config['register_firstname_require']){
          $json['error']['firstname'] = $this->language->get('error_firstname');
          }elseif(!$config['quickcheckout_display']){
		  $json['error']['firstname'] = $this->language->get('error_firstname');
		  }
          
          }
          
          if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32)) {
          
		  /* DV Code */
          if($config['register_lastname_require']){
          $json['error']['lastname'] = $this->language->get('error_lastname');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['lastname'] = $this->language->get('error_lastname');
		  }
          }
          
          // Customer Group
          $this->load->model('account/customer_group');
          
          $customer_group_info = $this->model_account_customer_group->getCustomerGroup($this->customer->getCustomerGroupId());
          
          if ($customer_group_info) {	
          // Company ID
          if ($customer_group_info['company_id_display'] && $customer_group_info['company_id_required'] && empty($this->request->post['company_id'])) {
		  /* DV Code */
          if($config['register_company_require']){
          $json['error']['company_id'] = $this->language->get('error_company_id');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['company_id'] = $this->language->get('error_company_id');
		  }
          
          }
          
          // Tax ID
          if ($customer_group_info['tax_id_display'] && $customer_group_info['tax_id_required'] && empty($this->request->post['tax_id'])) {
          $json['error']['tax_id'] = $this->language->get('error_tax_id');
          }						
          }
          
          if ((utf8_strlen($this->request->post['address_1']) < 3) || (utf8_strlen($this->request->post['address_1']) > 128)) {
		  /* DV Code */
          if($config['register_address_1_require']){
          $json['error']['address_1'] = $this->language->get('error_address_1');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['address_1'] = $this->language->get('error_address_1');
		  }
          }
          
          if ((utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 32)) {
		  /* DV Code */
          if($config['register_city_require']){
          $json['error']['city'] = $this->language->get('error_city');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['city'] = $this->language->get('error_city');
		  }
          }
          
          $this->load->model('localisation/country');
          
          $country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);
          
          if ($country_info) {
          if ($country_info['postcode_required'] && (utf8_strlen($this->request->post['postcode']) < 2) || (utf8_strlen($this->request->post['postcode']) > 10)) {
		  /* DV Code */
          if($config['register_postcode_require']){
          $json['error']['postcode'] = $this->language->get('error_postcode');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['postcode'] = $this->language->get('error_postcode');
		  }
          }
          
          // VAT Validation
          $this->load->helper('vat');
          
          if ($this->config->get('config_vat') && !empty($this->request->post['tax_id']) && (vat_validation($country_info['iso_code_2'], $this->request->post['tax_id']) == 'invalid')) {
          $json['error']['tax_id'] = $this->language->get('error_vat');
          }						
          }
          
          if ($this->request->post['country_id'] == '') {
		  /* DV Code */
          if($config['register_country_require']){
          $json['error']['country'] = $this->language->get('error_country');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['country'] = $this->language->get('error_country');
		  }
          }
          
          if ($this->request->post['zone_id'] == '') {
		  /* DV Code */
          if($config['register_zone_require']){
          $json['error']['zone'] = $this->language->get('error_zone');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['zone'] = $this->language->get('error_zone');
		  }
          
          }          
          }		
          }
          if (!$json) {
		  			// Default Payment Address
			$this->session->data['payment_address_id'] = $this->request->post['address_id'];
			$this->session->data['payment_country_id'] = $this->request->post['country_id'];
			$this->session->data['payment_zone_id'] = $this->request->post['zone_id'];
		  if($this->request->post['payment_address'] != 'existing'){
			if($this->request->post['payment_address'] != 'new'){
			$this->session->data['guest']['customer_group_id'] = $customer_group_id;
			$this->session->data['guest']['firstname'] = $this->request->post['firstname'];
			$this->session->data['guest']['lastname'] = $this->request->post['lastname'];
			$this->session->data['guest']['email'] = $this->request->post['email'];
			$this->session->data['guest']['telephone'] = $this->request->post['telephone'];
			$this->session->data['guest']['fax'] = $this->request->post['fax'];
			$this->session->data['guest']['payment']['company_id'] = $this->request->post['company_id'];
			$this->session->data['guest']['payment']['tax_id'] = $this->request->post['tax_id'];
			}
			$this->session->data['guest']['payment']['firstname'] = $this->request->post['firstname'];
			$this->session->data['guest']['payment']['lastname'] = $this->request->post['lastname'];				
			$this->session->data['guest']['payment']['company'] = $this->request->post['company'];

			
			$this->session->data['guest']['payment']['address_1'] = $this->request->post['address_1'];
			$this->session->data['guest']['payment']['address_2'] = $this->request->post['address_2'];
			$this->session->data['guest']['payment']['postcode'] = $this->request->post['postcode'];
			$this->session->data['guest']['payment']['city'] = $this->request->post['city'];
			$this->session->data['guest']['payment']['country_id'] = $this->request->post['country_id'];
			$this->session->data['guest']['payment']['zone_id'] = $this->request->post['zone_id'];
							
			$this->load->model('localisation/country');
			
			$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);
			
			if ($country_info) {
				$this->session->data['guest']['payment']['country'] = $country_info['name'];	
				$this->session->data['guest']['payment']['iso_code_2'] = $country_info['iso_code_2'];
				$this->session->data['guest']['payment']['iso_code_3'] = $country_info['iso_code_3'];
				$this->session->data['guest']['payment']['address_format'] = $country_info['address_format'];
			} else {
				$this->session->data['guest']['payment']['country'] = '';	
				$this->session->data['guest']['payment']['iso_code_2'] = '';
				$this->session->data['guest']['payment']['iso_code_3'] = '';
				$this->session->data['guest']['payment']['address_format'] = '';
			}
						
			$this->load->model('localisation/zone');

			$zone_info = $this->model_localisation_zone->getZone($this->request->post['zone_id']);
			
			if ($zone_info) {
				$this->session->data['guest']['payment']['zone'] = $zone_info['name'];
				$this->session->data['guest']['payment']['zone_code'] = $zone_info['code'];
			} else {
				$this->session->data['guest']['payment']['zone'] = '';
				$this->session->data['guest']['payment']['zone_code'] = '';
			}
			
			if (!empty($this->request->post['shipping_address'])) {
				$this->session->data['guest']['shipping_address'] = true;
			} else {
				$this->session->data['guest']['shipping_address'] = false;
			}
			

			
			if ($this->session->data['guest']['shipping_address']) {
				$this->session->data['guest']['shipping']['firstname'] = $this->request->post['firstname'];
				$this->session->data['guest']['shipping']['lastname'] = $this->request->post['lastname'];
				$this->session->data['guest']['shipping']['company'] = $this->request->post['company'];
				$this->session->data['guest']['shipping']['address_1'] = $this->request->post['address_1'];
				$this->session->data['guest']['shipping']['address_2'] = $this->request->post['address_2'];
				$this->session->data['guest']['shipping']['postcode'] = $this->request->post['postcode'];
				$this->session->data['guest']['shipping']['city'] = $this->request->post['city'];
				$this->session->data['guest']['shipping']['country_id'] = $this->request->post['country_id'];
				$this->session->data['guest']['shipping']['zone_id'] = $this->request->post['zone_id'];
				
				if ($country_info) {
					$this->session->data['guest']['shipping']['country'] = $country_info['name'];	
					$this->session->data['guest']['shipping']['iso_code_2'] = $country_info['iso_code_2'];
					$this->session->data['guest']['shipping']['iso_code_3'] = $country_info['iso_code_3'];
					$this->session->data['guest']['shipping']['address_format'] = $country_info['address_format'];
				} else {
					$this->session->data['guest']['shipping']['country'] = '';	
					$this->session->data['guest']['shipping']['iso_code_2'] = '';
					$this->session->data['guest']['shipping']['iso_code_3'] = '';
					$this->session->data['guest']['shipping']['address_format'] = '';
				}
	
				if ($zone_info) {
					$this->session->data['guest']['shipping']['zone'] = $zone_info['name'];
					$this->session->data['guest']['shipping']['zone_code'] = $zone_info['code'];

				} else {
					$this->session->data['guest']['shipping']['zone'] = '';
					$this->session->data['guest']['shipping']['zone_code'] = '';
				}
				
				// Default Shipping Address
				$this->session->data['shipping_country_id'] = $this->request->post['country_id'];
				$this->session->data['shipping_zone_id'] = $this->request->post['zone_id'];
				$this->session->data['shipping_postcode'] = $this->request->post['postcode'];
			}	
		}	
			
	}
          $this->response->setOutput(json_encode($json));
          }
		public function country() {
		$this->load->model('localisation/country');
			$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);
			$this->session->data['guest']['payment']['postcode'] = $this->request->post['postcode'];
			$this->session->data['guest']['payment']['country_id'] = $this->request->post['country_id'];
			$this->session->data['guest']['payment']['zone_id'] = $this->request->post['zone_id'];		
			if ($country_info) {
				$this->session->data['guest']['payment']['country'] = $country_info['name'];	
				$this->session->data['guest']['payment']['iso_code_2'] = $country_info['iso_code_2'];
				$this->session->data['guest']['payment']['iso_code_3'] = $country_info['iso_code_3'];
				$this->session->data['guest']['payment']['address_format'] = $country_info['address_format'];
			} else {
				$this->session->data['guest']['payment']['country'] = '';	
				$this->session->data['guest']['payment']['iso_code_2'] = '';
				$this->session->data['guest']['payment']['iso_code_3'] = '';
				$this->session->data['guest']['payment']['address_format'] = '';
			}
			
			if ($this->session->data['guest']['shipping_address']) {
				$this->session->data['guest']['shipping']['postcode'] = $this->request->post['postcode'];
				$this->session->data['guest']['shipping']['country_id'] = $this->request->post['country_id'];
				$this->session->data['guest']['shipping']['zone_id'] = $this->request->post['zone_id'];
				
				if ($country_info) {
					$this->session->data['guest']['shipping']['country'] = $country_info['name'];	
					$this->session->data['guest']['shipping']['iso_code_2'] = $country_info['iso_code_2'];
					$this->session->data['guest']['shipping']['iso_code_3'] = $country_info['iso_code_3'];
					$this->session->data['guest']['shipping']['address_format'] = $country_info['address_format'];
				} else {
					$this->session->data['guest']['shipping']['country'] = '';	
					$this->session->data['guest']['shipping']['iso_code_2'] = '';
					$this->session->data['guest']['shipping']['iso_code_3'] = '';
					$this->session->data['guest']['shipping']['address_format'] = '';
				}
			}
			
			$this->session->data['payment_country_id'] = $this->request->post['country_id'];
			$this->session->data['payment_zone_id'] = $this->request->post['zone_id'];
		 	 if(!$this->customer->isLogged() && isset($this->session->data['payment_address_id']) && $config['register_shipping_address_enable'] == 0){
                $this->session->data['shipping_address_id'] = $this->session->data['payment_address_id'];
            }
 		    
		}
		public function update() {
		  $config = $this->config->get('quickcheckout');  
 		  if(isset($this->session->data['payment_address_id']) &&  isset($this->request->post['payment_address']) && $config['register_shipping_address_enable'] == 0 ){
		  	$this->session->data['payment_address'] = $this->request->post['payment_address'];
		  }
          
          $this->language->load('checkout/checkout');
          
          $json = array();
          
                   
          // Customer Group
          $this->load->model('account/customer_group');
          
          $customer_group_info = $this->model_account_customer_group->getCustomerGroup($this->customer->getCustomerGroupId());
          
          
		  			// Default Payment Address
			$this->session->data['payment_address'] = $this->request->post['payment_address'];
			$this->session->data['payment_address_id'] = $this->request->post['address_id'];
			$this->session->data['payment_country_id'] = $this->request->post['country_id'];
			$this->session->data['payment_zone_id'] = $this->request->post['zone_id'];
		  if($this->request->post['payment_address'] != 'existing'){
			if($this->request->post['payment_address'] != 'new'){
			$this->session->data['guest']['customer_group_id'] = $customer_group_id;
			$this->session->data['guest']['firstname'] = $this->request->post['firstname'];
			$this->session->data['guest']['lastname'] = $this->request->post['lastname'];
			$this->session->data['guest']['email'] = $this->request->post['email'];
			$this->session->data['guest']['telephone'] = $this->request->post['telephone'];
			$this->session->data['guest']['fax'] = $this->request->post['fax'];
			$this->session->data['guest']['payment']['company_id'] = $this->request->post['company_id'];
			$this->session->data['guest']['payment']['tax_id'] = $this->request->post['tax_id'];
			}
			$this->session->data['guest']['payment']['firstname'] = $this->request->post['firstname'];
			$this->session->data['guest']['payment']['lastname'] = $this->request->post['lastname'];				
			$this->session->data['guest']['payment']['company'] = $this->request->post['company'];

			
			$this->session->data['guest']['payment']['address_1'] = $this->request->post['address_1'];
			$this->session->data['guest']['payment']['address_2'] = $this->request->post['address_2'];
			$this->session->data['guest']['payment']['postcode'] = $this->request->post['postcode'];
			$this->session->data['guest']['payment']['city'] = $this->request->post['city'];
			$this->session->data['guest']['payment']['country_id'] = $this->request->post['country_id'];
			$this->session->data['guest']['payment']['zone_id'] = $this->request->post['zone_id'];
							
			$this->load->model('localisation/country');
			
			$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);
			
			if ($country_info) {
				$this->session->data['guest']['payment']['country'] = $country_info['name'];	
				$this->session->data['guest']['payment']['iso_code_2'] = $country_info['iso_code_2'];
				$this->session->data['guest']['payment']['iso_code_3'] = $country_info['iso_code_3'];
				$this->session->data['guest']['payment']['address_format'] = $country_info['address_format'];
			} else {
				$this->session->data['guest']['payment']['country'] = '';	
				$this->session->data['guest']['payment']['iso_code_2'] = '';
				$this->session->data['guest']['payment']['iso_code_3'] = '';
				$this->session->data['guest']['payment']['address_format'] = '';
			}
						
			$this->load->model('localisation/zone');

			$zone_info = $this->model_localisation_zone->getZone($this->request->post['zone_id']);
			
			if ($zone_info) {
				$this->session->data['guest']['payment']['zone'] = $zone_info['name'];
				$this->session->data['guest']['payment']['zone_code'] = $zone_info['code'];
			} else {
				$this->session->data['guest']['payment']['zone'] = '';
				$this->session->data['guest']['payment']['zone_code'] = '';
			}
			
			if (!empty($this->request->post['shipping_address'])) {
				$this->session->data['guest']['shipping_address'] = true;
			} else {
				$this->session->data['guest']['shipping_address'] = false;
			}
			

			
			if ($this->session->data['guest']['shipping_address']) {
				$this->session->data['guest']['shipping']['firstname'] = $this->request->post['firstname'];
				$this->session->data['guest']['shipping']['lastname'] = $this->request->post['lastname'];
				$this->session->data['guest']['shipping']['company'] = $this->request->post['company'];
				$this->session->data['guest']['shipping']['address_1'] = $this->request->post['address_1'];
				$this->session->data['guest']['shipping']['address_2'] = $this->request->post['address_2'];
				$this->session->data['guest']['shipping']['postcode'] = $this->request->post['postcode'];
				$this->session->data['guest']['shipping']['city'] = $this->request->post['city'];
				$this->session->data['guest']['shipping']['country_id'] = $this->request->post['country_id'];
				$this->session->data['guest']['shipping']['zone_id'] = $this->request->post['zone_id'];
				
				if ($country_info) {
					$this->session->data['guest']['shipping']['country'] = $country_info['name'];	
					$this->session->data['guest']['shipping']['iso_code_2'] = $country_info['iso_code_2'];
					$this->session->data['guest']['shipping']['iso_code_3'] = $country_info['iso_code_3'];
					$this->session->data['guest']['shipping']['address_format'] = $country_info['address_format'];
				} else {
					$this->session->data['guest']['shipping']['country'] = '';	
					$this->session->data['guest']['shipping']['iso_code_2'] = '';
					$this->session->data['guest']['shipping']['iso_code_3'] = '';
					$this->session->data['guest']['shipping']['address_format'] = '';
				}
	
				if ($zone_info) {
					$this->session->data['guest']['shipping']['zone'] = $zone_info['name'];
					$this->session->data['guest']['shipping']['zone_code'] = $zone_info['code'];

				} else {
					$this->session->data['guest']['shipping']['zone'] = '';
					$this->session->data['guest']['shipping']['zone_code'] = '';
				}
				
				// Default Shipping Address
				$this->session->data['shipping_country_id'] = $this->request->post['country_id'];
				$this->session->data['shipping_zone_id'] = $this->request->post['zone_id'];
				$this->session->data['shipping_postcode'] = $this->request->post['postcode'];
			}	
		}	
          $this->response->setOutput(json_encode($json));
          }
		
      
	public function validate() {

        $config = $this->config->get('quickcheckout');
          
      
		$this->language->load('checkout/checkout');
		
		$json = array();
		
		// Validate if customer is logged in.
		if (!$this->customer->isLogged()) {
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
			if (isset($this->request->post['payment_address']) && $this->request->post['payment_address'] == 'existing') {
				$this->load->model('account/address');
				
				if (empty($this->request->post['address_id'])) {
					$json['error']['warning'] = $this->language->get('error_address');
				} elseif (!in_array($this->request->post['address_id'], array_keys($this->model_account_address->getAddresses()))) {
					$json['error']['warning'] = $this->language->get('error_address');
				} else {
					// Default Payment Address
					$this->load->model('account/address');
	
					$address_info = $this->model_account_address->getAddress($this->request->post['address_id']);
										
					if ($address_info) {				
						$this->load->model('account/customer_group');
				
						$customer_group_info = $this->model_account_customer_group->getCustomerGroup($this->customer->getCustomerGroupId());
					
						// Company ID
						if ($customer_group_info['company_id_display'] && $customer_group_info['company_id_required'] && !$address_info['company_id']) {
							$json['error']['warning'] = $this->language->get('error_company_id');
						}					
						
						// Tax ID
						if ($customer_group_info['tax_id_display'] && $customer_group_info['tax_id_required'] && !$address_info['tax_id']) {
							$json['error']['warning'] = $this->language->get('error_tax_id');
						}						
					}					
				}
					
				if (!$json) {			
					$this->session->data['payment_address_id'] = $this->request->post['address_id'];
					
					if ($address_info) {
						$this->session->data['payment_country_id'] = $address_info['country_id'];
						$this->session->data['payment_zone_id'] = $address_info['zone_id'];
					} else {
						unset($this->session->data['payment_country_id']);	
						unset($this->session->data['payment_zone_id']);	
					}
										
					
        /* DV Code */
      	

				}
			} else {
				if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32)) {
					
        /* DV Code */
          if($config['register_firstname_require']){
          $json['error']['firstname'] = $this->language->get('error_firstname');
          }elseif(!$config['quickcheckout_display']){
		  $json['error']['firstname'] = $this->language->get('error_firstname');
		  }
      
				}
		
				if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32)) {
					
        /* DV Code */
          if($config['register_lastname_require']){
          $json['error']['lastname'] = $this->language->get('error_lastname');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['lastname'] = $this->language->get('error_lastname');
		  }
      
				}
		
				// Customer Group
				$this->load->model('account/customer_group');
				
				$customer_group_info = $this->model_account_customer_group->getCustomerGroup($this->customer->getCustomerGroupId());
					
				if ($customer_group_info) {	
					// Company ID
					if ($customer_group_info['company_id_display'] && $customer_group_info['company_id_required'] && empty($this->request->post['company_id'])) {
						
        /* DV Code */
          if($config['register_company_id_require']){
          $json['error']['company_id'] = $this->language->get('error_company_id');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['company_id'] = $this->language->get('error_company_id');
		  }
      
					}
					
					// Tax ID
					if ($customer_group_info['tax_id_display'] && $customer_group_info['tax_id_required'] && empty($this->request->post['tax_id'])) {
						$json['error']['tax_id'] = $this->language->get('error_tax_id');
					}						
				}
					
				if ((utf8_strlen($this->request->post['address_1']) < 3) || (utf8_strlen($this->request->post['address_1']) > 128)) {
					
        /* DV Code */
          if($config['register_address_1_require']){
          $json['error']['address_1'] = $this->language->get('error_address_1');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['address_1'] = $this->language->get('error_address_1');
		  }
      
				}
		
				if ((utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 32)) {
					
        /* DV Code */
          if($config['register_city_require']){
          $json['error']['city'] = $this->language->get('error_city');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['city'] = $this->language->get('error_city');
		  }
      
				}
				
				$this->load->model('localisation/country');
				
				$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);
				
				if ($country_info) {
					if ($country_info['postcode_required'] && (utf8_strlen($this->request->post['postcode']) < 2) || (utf8_strlen($this->request->post['postcode']) > 10)) {
						
        /* DV Code */
          if($config['register_postcode_require']){
          $json['error']['postcode'] = $this->language->get('error_postcode');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['postcode'] = $this->language->get('error_postcode');
		  }
      
					}
					 
					// VAT Validation
					$this->load->helper('vat');
					
					if ($this->config->get('config_vat') && !empty($this->request->post['tax_id']) && (vat_validation($country_info['iso_code_2'], $this->request->post['tax_id']) == 'invalid')) {
						$json['error']['tax_id'] = $this->language->get('error_vat');
					}						
				}
				
				if ($this->request->post['country_id'] == '') {
					
        /* DV Code */
          if($config['register_country_require']){
          $json['error']['country'] = $this->language->get('error_country');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['country'] = $this->language->get('error_country');
		  }
      
				}
				
				if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '') {
					
        /* DV Code */
          if($config['register_zone_require']){
          $json['error']['zone'] = $this->language->get('error_zone');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['zone'] = $this->language->get('error_zone');
		  }
      
				}
				
				if (!$json) {
					// Default Payment Address
					$this->load->model('account/address');
					
					$this->session->data['payment_address_id'] = $this->model_account_address->addAddress($this->request->post);
					$this->session->data['payment_country_id'] = $this->request->post['country_id'];
					$this->session->data['payment_zone_id'] = $this->request->post['zone_id'];
															
					
        /* DV Code */
      	

				}		
			}		
		}
		
		$this->response->setOutput(json_encode($json));
	}
}
?>