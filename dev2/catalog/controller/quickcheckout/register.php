<?php 
class ControllerQuickcheckoutRegister extends Controller {
  	public function index() {
		$this->language->load('checkout/checkout');
		
		$this->data['text_your_details'] = $this->language->get('text_your_details');
		$this->data['text_your_address'] = $this->language->get('text_your_address');
		$this->data['text_your_password'] = $this->language->get('text_your_password');
		$this->data['text_select'] = $this->language->get('text_select');
		$this->data['text_none'] = $this->language->get('text_none');
						
		$this->data['entry_firstname'] = $this->language->get('entry_firstname');
		$this->data['entry_lastname'] = $this->language->get('entry_lastname');
		$this->data['entry_email'] = $this->language->get('entry_email');
		$this->data['entry_telephone'] = $this->language->get('entry_telephone');
		$this->data['entry_fax'] = $this->language->get('entry_fax');
		$this->data['entry_company'] = $this->language->get('entry_company');
		$this->data['entry_customer_group'] = $this->language->get('entry_customer_group');
		$this->data['entry_company_id'] = $this->language->get('entry_company_id');
		$this->data['entry_tax_id'] = $this->language->get('entry_tax_id');		
		$this->data['entry_address_1'] = $this->language->get('entry_address_1');
		$this->data['entry_address_2'] = $this->language->get('entry_address_2');
		$this->data['entry_postcode'] = $this->language->get('entry_postcode');
		$this->data['entry_city'] = $this->language->get('entry_city');
		$this->data['entry_country'] = $this->language->get('entry_country');
		$this->data['entry_zone'] = $this->language->get('entry_zone');
		$this->data['entry_newsletter'] = sprintf($this->language->get('entry_newsletter'), $this->config->get('config_name'));
		$this->data['entry_password'] = $this->language->get('entry_password');
		$this->data['entry_confirm'] = $this->language->get('entry_confirm');
		$this->data['entry_shipping'] = $this->language->get('entry_shipping');

		$this->data['button_continue'] = $this->language->get('button_continue');

		$this->data['customer_groups'] = array();
		
		if (is_array($this->config->get('config_customer_group_display'))) {
			$this->load->model('account/customer_group');
			
			$customer_groups = $this->model_account_customer_group->getCustomerGroups();
			
			foreach ($customer_groups  as $customer_group) {
				if (in_array($customer_group['customer_group_id'], $this->config->get('config_customer_group_display'))) {
					$this->data['customer_groups'][] = $customer_group;
				}
			}
		}
		
		$this->data['customer_group_id'] = $this->config->get('config_customer_group_id');
		
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
		
		$this->data['shipping_required'] = $this->cart->hasShipping();
			
		
        $config = $this->config->get('quickcheckout');
		if (isset($this->session->data['guest']['payment']['postcode'])) {
			$this->data['postcode'] = $this->session->data['guest']['payment']['postcode'];							
		} elseif (isset($this->session->data['shipping_postcode'])) {
			$this->data['postcode'] = $this->session->data['shipping_postcode'];			
		} else {
			$this->data['postcode'] = '';
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
		  if($config['quickcheckout_display']){$quickcheckout = 'quickcheckout';}else{ $quickcheckout= 'checkout';}
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/'.$quickcheckout.'/register.tpl')) {
          $this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/'.$quickcheckout.'/register.tpl';
		  		  } else {
			$this->template = 'default/template/'.$quickcheckout.'/register.tpl';
      



		}
		
		$this->response->setOutput($this->render());		
  	}
	

        /* DV Code */
          public function check() {
          $this->language->load('checkout/checkout');
          
          $this->load->model('account/customer');
          
          $json = array();
          
          $config = $this->config->get('quickcheckout');
 		      
          
          
          
          if (!$json) {					
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
          
          if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
          /* DV Code */
          if($config['register_email_require']){
          $json['error']['email'] = $this->language->get('error_email');
          }elseif(!$config['quickcheckout_display']){
		  $json['error']['email'] = $this->language->get('error_email');
		  }
          }
          
          if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
          if($config['register_email_require']){
          $json['error']['warning'] = $this->language->get('error_exists');	  
          }elseif(!$config['quickcheckout_display']){
		  $json['error']['warning'] = $this->language->get('error_exists');
		  }
          }
          
          if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
          /* DV Code */
          if($config['register_telephone_require']){
          $json['error']['telephone'] = $this->language->get('error_telephone');
          }elseif(!$config['quickcheckout_display']){
		  $json['error']['telephone'] = $this->language->get('error_telephone');
		  }
          }
          
          // Customer Group
          $this->load->model('account/customer_group');
          
          if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
          $customer_group_id = $this->request->post['customer_group_id'];
          } else {
          $customer_group_id = $this->config->get('config_customer_group_id');
          }
          
          $customer_group = $this->model_account_customer_group->getCustomerGroup($customer_group_id);
          
          if ($customer_group) {	
          // Company ID
          if ($customer_group['company_id_display'] && $customer_group['company_id_required'] && empty($this->request->post['company_id'])) {
          /* DV Code */
          if($config['register_company_id_require']){
          $json['error']['company_id'] = $this->language->get('error_company_id');
          }elseif(!$config['quickcheckout_display']){
		  $json['error']['company_id'] = $this->language->get('error_company_id');
		  }				
          }
          
          // Tax ID
          if ($customer_group['tax_id_display'] && $customer_group['tax_id_required'] && empty($this->request->post['tax_id'])) {
          /* DV Code */
          if($config['register_tax_id_require']){
          $json['error']['tax_id'] = $this->language->get('error_tax_id');
          }elseif(!$config['quickcheckout_display']){
		  $json['error']['tax_id'] = $this->language->get('error_tax_id');
		  }
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
          
          if ((utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 128)) {
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
          
          if ($this->config->get('config_vat') && $this->request->post['tax_id'] && (vat_validation($country_info['iso_code_2'], $this->request->post['tax_id']) == 'invalid')) {
		  $json['error']['tax_id'] = $this->language->get('error_vat');
          /* DV Code should i validate VAT? */
          if($config['register_vat_require']){
          $json['error']['tax_id'] = $this->language->get('error_vat');
          }elseif(!$config['quickcheckout_display']){
		  $json['error']['tax_id'] = $this->language->get('error_vat');
		  }
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
          
          if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
          /* DV Code */
          if($config['register_password_require']){
          $json['error']['password'] = $this->language->get('error_password');
          }elseif(!$config['quickcheckout_display']){
		  $json['error']['password'] = $this->language->get('error_password');
		  }
          
          }
          
          if ($this->request->post['confirm'] != $this->request->post['password']) {
          /* DV Code */
          if($config['register_password_require']){
          $json['error']['confirm'] = $this->language->get('error_confirm');
          }elseif(!$config['quickcheckout_display']){
		   $json['error']['confirm'] = $this->language->get('error_confirm');
		  }
          
          }
          
          if ($this->config->get('config_account_id')) {
          $this->load->model('catalog/information');
          
          $information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
          
          if ($information_info && !isset($this->request->post['agree'])) {
		  /* DV Code */
          if($config['register_privacy_agree_require']){
          $json['error']['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
          }elseif(!$config['quickcheckout_display']){
		  $json['error']['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
		  }
          
          }
          }
          }
          if (!$json) {
			$this->session->data['guest']['customer_group_id'] = $customer_group_id;
			$this->session->data['guest']['firstname'] = $this->request->post['firstname'];
			$this->session->data['guest']['lastname'] = $this->request->post['lastname'];
			$this->session->data['guest']['email'] = $this->request->post['email'];
			$this->session->data['guest']['telephone'] = $this->request->post['telephone'];
			$this->session->data['guest']['fax'] = $this->request->post['fax'];
			
			$this->session->data['guest']['payment']['firstname'] = $this->request->post['firstname'];
			$this->session->data['guest']['payment']['lastname'] = $this->request->post['lastname'];				
			$this->session->data['guest']['payment']['company'] = $this->request->post['company'];
			$this->session->data['guest']['payment']['company_id'] = $this->request->post['company_id'];
			$this->session->data['guest']['payment']['tax_id'] = $this->request->post['tax_id'];
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
			
			// Default Payment Address
			$this->session->data['payment_country_id'] = $this->request->post['country_id'];
			$this->session->data['payment_zone_id'] = $this->request->post['zone_id'];
			
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
		  
		  public function update() {
          $this->language->load('checkout/checkout');
          
          $this->load->model('account/customer');
          
          $json = array();
          
          $config = $this->config->get('quickcheckout');
 	
          
          // Customer Group
          $this->load->model('account/customer_group');
          
          if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
          $customer_group_id = $this->request->post['customer_group_id'];
          } else {
          $customer_group_id = $this->config->get('config_customer_group_id');
          }
          
          $customer_group = $this->model_account_customer_group->getCustomerGroup($customer_group_id);
          
          $this->load->model('localisation/country');
          
          $country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);
          
			$this->session->data['guest']['customer_group_id'] = $customer_group_id;
			$this->session->data['guest']['firstname'] = $this->request->post['firstname'];
			$this->session->data['guest']['lastname'] = $this->request->post['lastname'];
			$this->session->data['guest']['email'] = $this->request->post['email'];
			$this->session->data['guest']['telephone'] = $this->request->post['telephone'];
			$this->session->data['guest']['fax'] = $this->request->post['fax'];
			
			$this->session->data['guest']['payment']['firstname'] = $this->request->post['firstname'];
			$this->session->data['guest']['payment']['lastname'] = $this->request->post['lastname'];				
			$this->session->data['guest']['payment']['company'] = $this->request->post['company'];
			$this->session->data['guest']['payment']['company_id'] = $this->request->post['company_id'];
			$this->session->data['guest']['payment']['tax_id'] = $this->request->post['tax_id'];
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
			
			// Default Payment Address
			$this->session->data['payment_country_id'] = $this->request->post['country_id'];
			$this->session->data['payment_zone_id'] = $this->request->post['zone_id'];
			
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
          $this->response->setOutput(json_encode($json));	
          }
          /* DV Code End */
		  
      
	public function validate() {

        $config = $this->config->get('quickcheckout');
          
      
		$this->language->load('checkout/checkout');
		
		$this->load->model('account/customer');
		
		$json = array();
		
		// Validate if customer is already logged out.
		if ($this->customer->isLogged()) {
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
		
			if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
				
        /* DV Code */
          if($config['register_email_require']){
          $json['error']['email'] = $this->language->get('error_email');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['email'] = $this->language->get('error_email');
		  }
      
			}
	
			if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
				
        /* DV Code */
          if($config['register_email_require']){
          $json['error']['email'] = $this->language->get('error_exists');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['email'] = $this->language->get('error_exists');
		  }else{
			  /* DV Code default email */
			  $this->request->post['email'] = $config['register_email'];
			  }
      
			}
			
			if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
				
        /* DV Code */
          if($config['register_telephone_require']){
          $json['error']['telephone'] = $this->language->get('error_telephone');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['telephone'] = $this->language->get('error_telephone');
		  }
      
			}
	
			// Customer Group
			$this->load->model('account/customer_group');
			
			if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
				$customer_group_id = $this->request->post['customer_group_id'];
			} else {
				$customer_group_id = $this->config->get('config_customer_group_id');
			}
			
			$customer_group = $this->model_account_customer_group->getCustomerGroup($customer_group_id);
				
			if ($customer_group) {	
				// Company ID
				if ($customer_group['company_id_display'] && $customer_group['company_id_required'] && empty($this->request->post['company_id'])) {
					
        /* DV Code */
          if($config['register_company_id_require']){
          $json['error']['company_id'] = $this->language->get('error_company_id');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['company_id'] = $this->language->get('error_company_id');
		  }
      
				}
				
				// Tax ID
				if ($customer_group['tax_id_display'] && $customer_group['tax_id_required'] && empty($this->request->post['tax_id'])) {
					
        /* DV Code */
          if($config['register_tax_id_require']){
          $json['error']['tax_id'] = $this->language->get('error_tax_id');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['tax_id'] = $this->language->get('error_tax_id');
		  }
      
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
	
			if ((utf8_strlen($this->request->post['city']) < 2) || (utf8_strlen($this->request->post['city']) > 128)) {
				
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
				
				if ($this->config->get('config_vat') && $this->request->post['tax_id'] && (vat_validation($country_info['iso_code_2'], $this->request->post['tax_id']) == 'invalid')) {
					
        /* DV Code */
          if($config['register_vat_require']){
          $json['error']['tax_id'] = $this->language->get('error_vat');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['tax_id'] = $this->language->get('error_vat');
		  }
      
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
	
			if ((utf8_strlen($this->request->post['password']) < 4) || (utf8_strlen($this->request->post['password']) > 20)) {
				
        /* DV Code */
          if($config['register_password_require']){
          $json['error']['password'] = $this->language->get('error_password');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['password'] = $this->language->get('error_password');
		  }
      
			}
	
			if ($this->request->post['confirm'] != $this->request->post['password']) {
				
        /* DV Code */
          if($config['register_password_require']){
          $json['error']['confirm'] = $this->language->get('error_confirm');
          }elseif(!$config['quickcheckout_display']){
          $json['error']['confirm'] = $this->language->get('error_confirm');
		  }
      
			}
			
			if ($this->config->get('config_account_id')) {
				$this->load->model('catalog/information');
				
				$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
				
				if ($information_info && !isset($this->request->post['agree'])) {
					$json['error']['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
				}
			}
		}
		
		if (!$json) {

        $this->session->data['guest']['customer_group_id'] = $customer_group_id;
			$this->session->data['guest']['firstname'] = $this->request->post['firstname'];
			$this->session->data['guest']['lastname'] = $this->request->post['lastname'];
			$this->session->data['guest']['email'] = $this->request->post['email'];
			$this->session->data['guest']['telephone'] = $this->request->post['telephone'];
			$this->session->data['guest']['fax'] = $this->request->post['fax'];
			
			$this->session->data['guest']['payment']['firstname'] = $this->request->post['firstname'];
			$this->session->data['guest']['payment']['lastname'] = $this->request->post['lastname'];				
			$this->session->data['guest']['payment']['company'] = $this->request->post['company'];
			$this->session->data['guest']['payment']['company_id'] = $this->request->post['company_id'];
			$this->session->data['guest']['payment']['tax_id'] = $this->request->post['tax_id'];
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
			
			// Default Payment Address
			$this->session->data['payment_country_id'] = $this->request->post['country_id'];
			$this->session->data['payment_zone_id'] = $this->request->post['zone_id'];
			
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
      
			$this->model_account_customer->addCustomer($this->request->post);
			
			$this->session->data['account'] = 'register';
			
			if ($customer_group && !$customer_group['approval']) {
				$this->customer->login($this->request->post['email'], $this->request->post['password']);
				
				$this->session->data['payment_address_id'] = $this->customer->getAddressId();
				$this->session->data['payment_country_id'] = $this->request->post['country_id'];
				$this->session->data['payment_zone_id'] = $this->request->post['zone_id'];
									
				if (!empty($this->request->post['shipping_address'])) {
					$this->session->data['shipping_address_id'] = $this->customer->getAddressId();
					$this->session->data['shipping_country_id'] = $this->request->post['country_id'];
					$this->session->data['shipping_zone_id'] = $this->request->post['zone_id'];
					$this->session->data['shipping_postcode'] = $this->request->post['postcode'];					
				}
			} else {
				$json['redirect'] = $this->url->link('account/success');
			}
			
			
        /* DV Code */
      






		}	
		
		$this->response->setOutput(json_encode($json));	
	} 
}
?>