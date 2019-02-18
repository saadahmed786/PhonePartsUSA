<?php 
class ControllerQuickcheckoutShippingAddress extends Controller {
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

		$this->data['addresses'] = $this->model_account_address->getAddresses();

		if (isset($this->session->data['shipping_postcode'])) {
			$this->data['postcode'] = $this->session->data['shipping_postcode'];		
		} else {
			$this->data['postcode'] = '';
		}
				
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
						
		$this->load->model('localisation/country');
		
		$this->data['countries'] = $this->model_localisation_country->getCountries();

		
        $config = $this->config->get('quickcheckout');
 		    
          
		  if($config['quickcheckout_display']){$quickcheckout = 'quickcheckout';}else{ $quickcheckout= 'checkout';}
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/'.$quickcheckout.'/shipping_address.tpl')) {
          $this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/'.$quickcheckout.'/shipping_address.tpl';
  		  } else {
			$this->template = 'default/template/'.$quickcheckout.'/shipping_address.tpl';
      



		}
				
		$this->response->setOutput($this->render());
  	}	
	

        public function check() {
		  $config = $this->config->get('quickcheckout');
 		    
          
          $this->language->load('checkout/checkout');
          
          $json = array();
          
          if (!isset($this->request->post['shipping_address']) || $this->request->post['shipping_address'] == 'new' ) {
          if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32)) {
          if($config['register_shipping_firstname_require']){
          $json['error']['firstname'] = $this->language->get('error_firstname');
          }elseif(!$config['quickcheckout_display']){
		  $json['error']['firstname'] = $this->language->get('error_firstname');
		  }
          }
          
          if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32)) {
		  if($config['register_shipping_lastname_require']){
          $json['error']['lastname'] = $this->language->get('error_lastname');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['lastname'] = $this->language->get('error_lastname');
		  }
          }
          
          if ((utf8_strlen($this->request->post['address_1']) < 3) || (utf8_strlen($this->request->post['address_1']) > 128)) {
		  if($config['register_shipping_address_1_require']){
          $json['error']['address_1'] = $this->language->get('error_address_1');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['address_1'] = $this->language->get('error_address_1');
		  }
          }
          
          if ((utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 128)) {
		  if($config['register_shipping_city_require']){
          $json['error']['city'] = $this->language->get('error_city');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['city'] = $this->language->get('error_city');
		  }
          }
          
          $this->load->model('localisation/country');
          
          $country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);
          
          if ($country_info && $country_info['postcode_required'] && (utf8_strlen($this->request->post['postcode']) < 2) || (utf8_strlen($this->request->post['postcode']) > 10)) {
          
		  if($config['register_shipping_postcode_require']){
          $json['error']['postcode'] = $this->language->get('error_postcode');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['postcode'] = $this->language->get('error_postcode');
		  }
          }
          
          if ($this->request->post['country_id'] == '') {
          
		  if($config['register_shipping_country_require']){
          $json['error']['country'] = $this->language->get('error_country');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['country'] = $this->language->get('error_country');
		  }
          }
          
          if ($this->request->post['zone_id'] == '') {
          
          
		  if($config['register_shipping_zone_require']){
         $json['error']['zone'] = $this->language->get('error_zone');
          }elseif(!$config['quickcheckout_display']){
         $json['error']['zone'] = $this->language->get('error_zone');
		  }
          }
          
          }
		  if (!$json) {
				$this->session->data['guest']['shipping']['firstname'] = $this->request->post['firstname'];
				$this->session->data['guest']['shipping']['lastname'] = $this->request->post['lastname'];
				$this->session->data['guest']['shipping']['company'] = $this->request->post['company'];
				$this->session->data['guest']['shipping']['address_1'] = $this->request->post['address_1'];
				$this->session->data['guest']['shipping']['address_2'] = $this->request->post['address_2'];
				$this->session->data['guest']['shipping']['postcode'] = $this->request->post['postcode'];
				$this->session->data['guest']['shipping']['city'] = $this->request->post['city'];
				$this->session->data['guest']['shipping']['country_id'] = $this->request->post['country_id'];
				$this->session->data['guest']['shipping']['zone_id'] = $this->request->post['zone_id'];
				
				$this->load->model('localisation/country');
			
				$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);
				
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
				
				$this->load->model('localisation/zone');
								
				$zone_info = $this->model_localisation_zone->getZone($this->request->post['zone_id']);
	
				if ($zone_info) {
					$this->session->data['guest']['shipping']['zone'] = $zone_info['name'];
					$this->session->data['guest']['shipping']['zone_code'] = $zone_info['code'];
				} else {
					$this->session->data['guest']['shipping']['zone'] = '';
					$this->session->data['guest']['shipping']['zone_code'] = '';
				}
				
				// Default Shipping Address	
				if(isset($this->request->post['address_id'])){
				$this->session->data['shipping_address_id'] = $this->request->post['address_id'];	
				}
				$this->session->data['shipping_country_id'] = $this->request->post['country_id'];
				$this->session->data['shipping_zone_id'] = $this->request->post['zone_id'];
				$this->session->data['shipping_postcode'] = $this->request->post['postcode'];
			}

          $this->response->setOutput(json_encode($json));
          }
		 public function country() {
		$this->load->model('localisation/country');
			$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);
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
			$this->session->data['shipping_country_id'] = $this->request->post['country_id'];
			$this->session->data['shipping_zone_id'] = $this->request->post['zone_id'];
			$this->session->data['shipping_postcode'] = $this->request->post['postcode'];
		}
      
	public function validate() {

        $config = $this->config->get('quickcheckout');
 		    
          
      
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
			if (isset($this->request->post['shipping_address']) && $this->request->post['shipping_address'] == 'existing') {
				$this->load->model('account/address');
				
				if (empty($this->request->post['address_id'])) {
					$json['error']['warning'] = $this->language->get('error_address');
				} elseif (!in_array($this->request->post['address_id'], array_keys($this->model_account_address->getAddresses()))) {
					$json['error']['warning'] = $this->language->get('error_address');
				}
						
				if (!$json) {			
					$this->session->data['shipping_address_id'] = $this->request->post['address_id'];
					
					// Default Shipping Address
					$this->load->model('account/address');

					$address_info = $this->model_account_address->getAddress($this->request->post['address_id']);
					
					if ($address_info) {
						$this->session->data['shipping_country_id'] = $address_info['country_id'];
						$this->session->data['shipping_zone_id'] = $address_info['zone_id'];
						$this->session->data['shipping_postcode'] = $address_info['postcode'];						
					} else {
						unset($this->session->data['shipping_country_id']);	
						unset($this->session->data['shipping_zone_id']);	
						unset($this->session->data['shipping_postcode']);
					}
					
					
        /* DV Code */
      							

				}
			} 
			
			if ($this->request->post['shipping_address'] == 'new') {
				if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32)) {
					
        /* DV Code */
          if($config['register_shipping_firstname_require']){
          $json['error']['firstname'] = $this->language->get('error_firstname');
          }elseif(!$config['quickcheckout_display']){
		  $json['error']['firstname'] = $this->language->get('error_firstname');
		  }
      
				}
		
				if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32)) {
					
        /* DV Code */
          if($config['register_shipping_lastname_require']){
          $json['error']['lastname'] = $this->language->get('error_lastname');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['lastname'] = $this->language->get('error_lastname');
		  }
      
				}
		
				if ((utf8_strlen($this->request->post['address_1']) < 3) || (utf8_strlen($this->request->post['address_1']) > 128)) {
					
        /* DV Code */
          if($config['register_shipping_address_1_require']){
          $json['error']['address_1'] = $this->language->get('error_address_1');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['address_1'] = $this->language->get('error_address_1');
		  }
      
				}
		
				if ((utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 128)) {
					
        /* DV Code */
          if($config['register_shipping_city_require']){
          $json['error']['city'] = $this->language->get('error_city');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['city'] = $this->language->get('error_city');
		  }
      
				}
				
				$this->load->model('localisation/country');
				
				$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);
				
				if ($country_info && $country_info['postcode_required'] && (utf8_strlen($this->request->post['postcode']) < 2) || (utf8_strlen($this->request->post['postcode']) > 10)) {
					
        /* DV Code */
          if($config['register_shipping_postcode_require']){
          $json['error']['postcode'] = $this->language->get('error_postcode');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['postcode'] = $this->language->get('error_postcode');
		  }
      
				}
				
				if ($this->request->post['country_id'] == '') {
					
        /* DV Code */
          if($config['register_shipping_country_require']){
          $json['error']['country'] = $this->language->get('error_country');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['country'] = $this->language->get('error_country');
		  }
      
				}
				
				if (!isset($this->request->post['zone_id']) || $this->request->post['zone_id'] == '') {
					
        /* DV Code */
          if($config['register_shipping_zone_require']){
          $json['error']['zone'] = $this->language->get('error_zone');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['zone'] = $this->language->get('error_zone');
		  }
      
				}
				
				if (!$json) {						
					// Default Shipping Address
					$this->load->model('account/address');		
					
					$this->session->data['shipping_address_id'] = $this->model_account_address->addAddress($this->request->post);
					$this->session->data['shipping_country_id'] = $this->request->post['country_id'];
					$this->session->data['shipping_zone_id'] = $this->request->post['zone_id'];
					$this->session->data['shipping_postcode'] = $this->request->post['postcode'];
									
					
        /* DV Code */
      						

				}
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
}
?>