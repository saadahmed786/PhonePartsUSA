<?php 
class ControllerCheckoutSimplifiedCheckout extends Controller { 
	private $error = array();

	public function index() {
		

		if (!$this->config->get('simplified_checkout_status')) {
			$this->redirect($this->url->link('checkout/checkout', '', 'SSL'));
		}
		
		// Delete the success message that add coupon adds.
		unset($this->session->data['success']);

		$this->load->model('setting/extension');
		$this->load->model('account/customer');
		$this->language->load('checkout/simplified_checkout');
		
		//if ((!$this->cart->hasProducts() && (!isset($this->session->data['vouchers']) || !$this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
	  		$this->redirect($this->url->link('checkout/cart', '', 'SSL'));
    	}	
		
	   	if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['email']) && isset($this->request->post['password']) && $this->validateLogin()) {
			unset($this->session->data['guest']);
			
			$this->redirect($this->url->link('checkout/simplified_checkout', '', 'SSL'));
		}
		elseif ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['payment_method']) && $this->validateAccount()) {

			// If not guest checkout
			if (
			($this->config->get('config_guest_checkout') && (isset($this->request->post['checkout_type']) && $this->request->post['checkout_type'] == 'account')) || 
			!$this->config->get('config_guest_checkout') || 
			$this->customer->isLogged() ||
			$this->cart->hasDownload()
			) {
				unset($this->session->data['guest']);
				// Create account and login the user.
				if (!$this->customer->isLogged()) {
					// These 2 lines needed due to bug in addCustomer() function in OpenCart <= 1.5.1.2
					$customer = $this->request->post['customer'];
					$customer['email'] = $this->request->post['email'];
					
					if ($this->config->get('simplified_checkout_hide_country') && $this->config->get('simplified_checkout_fixed_country')) {
						$customer['country_id'] = $this->config->get('simplified_checkout_fixed_country');
					}
					
					if ($this->config->get('simplified_checkout_hide_zone')) {
						$customer['zone_id'] = $this->config->get('simplified_checkout_fixed_zone');
					}
					
					$add_customer_data = array_merge($this->request->post['shipping'], $customer);
					
					$this->model_account_customer->addCustomer($add_customer_data);
					$this->customer->login($customer['email'], $customer['password']);
				}
				
				if ($this->customer->isLogged()) {
	
					// Set shipping address
					$this->load->model('account/address');
		
		            // Get the shipping data supplied buy the buyer.
		            if (isset($this->request->post['different_shipping_address'])) {
		            	$shipping_data = $this->request->post['different_shipping'];
		            }
		            else {
		            	$shipping_data = $this->request->post['shipping'];
		            }
		            
		            if ($this->config->get('simplified_checkout_hide_country') && $this->config->get('simplified_checkout_fixed_country')) {
						$shipping_data['country_id'] = $this->config->get('simplified_checkout_fixed_country');
					}
					
					if ($this->config->get('simplified_checkout_hide_zone')) {
						$shipping_data['zone_id'] = $this->config->get('simplified_checkout_fixed_zone');
					}
		            
		            // Chceck if the supplied address exists, else add a new address.
		            $this->session->data['shipping_address_id'] = $this->checkIfAddressExists($shipping_data);
		            
		            if (!$this->session->data['shipping_address_id']) {
		            	$this->session->data['shipping_address_id'] = $this->model_account_address->addAddress($shipping_data);
		            }

		            
		            // Set payment address.
		           	
		           	// Check if shipping and payment address is different.
		           	// If so check if payment address exists else add a new one.
		           	if (isset($this->request->post['different_shipping_address'])) {
		           		$this->session->data['payment_address_id'] = $this->checkIfAddressExists($this->request->post['shipping']);
		           		
		           		if (!$this->session->data['payment_address_id']) {
		           			$this->session->data['payment_address_id'] = $this->model_account_address->addAddress($this->request->post['shipping']);
		           		}
		           		
		           	} else {
		           		$this->session->data['payment_address_id'] = $this->session->data['shipping_address_id'];
		           	}
				}
			}
			else {
				// Guest Account
				$this->session->data['guest']['firstname'] = $this->request->post['shipping']['firstname'];
				$this->session->data['guest']['lastname'] = $this->request->post['shipping']['lastname'];
				$this->session->data['guest']['email'] = $this->request->post['email'];
				$this->session->data['guest']['telephone'] = $this->request->post['customer']['telephone'];
				$this->session->data['guest']['fax'] = $this->request->post['customer']['fax'];
			
				// Payment
				$this->session->data['guest']['payment']['firstname'] = $this->request->post['shipping']['firstname'];
				$this->session->data['guest']['payment']['lastname'] = $this->request->post['shipping']['lastname'];				
				$this->session->data['guest']['payment']['company'] = $this->request->post['shipping']['company'];
				$this->session->data['guest']['payment']['address_1'] = $this->request->post['shipping']['address_1'];
				$this->session->data['guest']['payment']['address_2'] = $this->request->post['shipping']['address_2'];
				$this->session->data['guest']['payment']['postcode'] = $this->request->post['shipping']['postcode'];
				$this->session->data['guest']['payment']['city'] = $this->request->post['shipping']['city'];
				
				// If a fixed country and zone is specified use it
				if ($this->config->get('simplified_checkout_hide_country') && $this->config->get('simplified_checkout_fixed_country')) {
					$this->session->data['guest']['payment']['country_id'] = $this->config->get('simplified_checkout_fixed_country');
				} else {
					$this->session->data['guest']['payment']['country_id'] = $this->request->post['shipping']['country_id'];
	
				}
					
				if ($this->config->get('simplified_checkout_hide_zone')) {
					$this->session->data['guest']['payment']['zone_id'] = $this->config->get('simplified_checkout_fixed_zone');
				} else {
					$this->session->data['guest']['payment']['zone_id'] = $this->request->post['shipping']['zone_id'];
				}

				
				$this->load->model('localisation/country');
				
				$country_info = $this->model_localisation_country->getCountry($this->session->data['guest']['payment']['country_id']);
				
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
	
				$zone_info = $this->model_localisation_zone->getZone($this->session->data['guest']['payment']['zone_id']);
				
				if ($zone_info) {
					$this->session->data['guest']['payment']['zone'] = $zone_info['name'];
					$this->session->data['guest']['payment']['zone_code'] = $zone_info['code'];
				} else {
					$this->session->data['guest']['payment']['zone'] = '';
					$this->session->data['guest']['payment']['zone_code'] = '';
				}

				// Shippin
				if (isset($this->request->post['different_shipping_address'])) {
					$this->session->data['guest']['shipping']['firstname'] = $this->request->post['different_shipping']['firstname'];
					$this->session->data['guest']['shipping']['lastname'] = $this->request->post['different_shipping']['lastname'];
					$this->session->data['guest']['shipping']['company'] = $this->request->post['different_shipping']['company'];
					$this->session->data['guest']['shipping']['address_1'] = $this->request->post['different_shipping']['address_1'];
					$this->session->data['guest']['shipping']['address_2'] = $this->request->post['different_shipping']['address_2'];
					$this->session->data['guest']['shipping']['postcode'] = $this->request->post['different_shipping']['postcode'];
					$this->session->data['guest']['shipping']['city'] = $this->request->post['different_shipping']['city'];
					
					if ($this->config->get('simplified_checkout_hide_country') && $this->config->get('simplified_checkout_fixed_country')) {
						$this->session->data['guest']['shipping']['country_id'] = $this->config->get('simplified_checkout_fixed_country');
					} else {
						$this->session->data['guest']['shipping']['country_id'] = $this->request->post['different_shipping']['country_id'];
					}
						
					if ($this->config->get('simplified_checkout_hide_zone')) {
						$this->session->data['guest']['shipping']['zone_id'] = $this->config->get('simplified_checkout_fixed_zone');
					} else {
						$this->session->data['guest']['shipping']['zone_id'] = $this->request->post['different_shipping']['zone_id'];
					}
					
					
					
					
					$country_info = $this->model_localisation_country->getCountry($this->session->data['guest']['shipping']['country_id']);
					
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
		
					$zone_info = $this->model_localisation_zone->getZone($this->session->data['guest']['shipping']['zone_id']);
		
					if ($zone_info) {
						$this->session->data['guest']['shipping']['zone'] = $zone_info['name'];
						$this->session->data['guest']['shipping']['zone_code'] = $zone_info['code'];
					} else {
						$this->session->data['guest']['shipping']['zone'] = '';
						$this->session->data['guest']['shipping']['zone_code'] = '';
					}
				} else {
					$this->session->data['guest']['shipping'] = $this->session->data['guest']['payment'];
				}				
				
			}
			
			// Set the payment method
			$this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method']];
			
			// Set the shipping method.
		    $shipping = explode('.', $this->request->post['shipping_method']);
			$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];

			// Comment
	        $this->session->data['comment'] = strip_tags($this->request->post['comment']);
	
			// Redirect to confirm page.
			$this->redirect($this->url->link('checkout/simplified_checkout_confirm', '', 'SSL'));
		}

		
    	/* START TOTALS */
    	$total_data = array();
		$total = 0;
		$taxes = $this->cart->getTaxes();
			
    	$sort_order = array(); 
			
		$results = $this->model_setting_extension->getExtensions('total');
		
		foreach ($results as $key => $value) {
			$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
		}
		
		array_multisort($sort_order, SORT_ASC, $results);
		
		foreach ($results as $result) {
			if ($this->config->get($result['code'] . '_status')) {
				$this->load->model('total/' . $result['code']);
	
				$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
			}
		}
		
		$sort_order = array(); 
	  
		foreach ($total_data as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}

		array_multisort($sort_order, SORT_ASC, $total_data);
    	
    	
    	/* END TOTALS */


		$this->language->load('checkout/checkout');
		$data = array();
		
		
		/* */
		if (!$this->customer->isLogged()) {
			$this->data['logged_in'] = false;
		}
		else {
			$this->data['logged_in'] = true;
		}
		/* */
		
		
		/* START PAYMENT METHODS */
		
		
					
			
		// Dummy address to get all payment methods in the same country as the shop.
		
		
		if (isset($this->request->post['shipping'])) {
			$payment_address = $this->request->post['shipping'];
		} elseif (isset($this->session->data['payment_address_id'])) {
			$this->load->model('account/address');
			$payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
		} elseif (isset($this->session->data['guest']['payment'])) {
			$payment_address = $this->session->data['guest']['payment'];
		} elseif ($this->customer->isLogged()) {
			$this->load->model('account/address');
			$address_id = $this->customer->getAddressId();
			
			$payment_address = $this->model_account_address->getAddress($address_id);
		} else {
			$payment_address = array('zone_id' => 0, 'country_id' => $this->config->get('config_country_id'));
		}
		
		// Check for fixed country or zones
		if ($this->config->get('simplified_checkout_hide_country') && $this->config->get('simplified_checkout_fixed_country')) {
			$payment_address['country_id'] = $this->config->get('simplified_checkout_fixed_country');
		}
		
		if ($this->config->get('simplified_checkout_hide_zone')) {
			$payment_address['zone_id'] = $this->config->get('simplified_checkout_fixed_zone');
		}
		
		$this->load->model('localisation/country');
	  	$country_info = $this->model_localisation_country->getCountry($payment_address['country_id']);
	  	$payment_address = array_merge($payment_address, $country_info);
		
		// Get data
		$method_data = array();
		
		$results = $this->model_setting_extension->getExtensions('payment');

		foreach ($results as $result) {
			if ($this->config->get($result['code'] . '_status')) {
				$this->load->model('payment/' . $result['code']);
				
				$method = $this->{'model_payment_' . $result['code']}->getMethod($payment_address, $total); 
				
				if ($method) {
					$method_data[$result['code']] = $method;
				}
			}
		}
					 
		$sort_order = array(); 
	  
		foreach ($method_data as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}

		array_multisort($sort_order, SORT_ASC, $method_data);			
		
		$this->session->data['payment_methods'] = $method_data;
		
		// Assign data to template
		$this->data['text_payment_method'] = $this->language->get('text_payment_method');
		$this->data['text_comments'] = $this->language->get('text_comments');
   
		if (isset($this->session->data['payment_methods']) && !$this->session->data['payment_methods']) {
			$this->data['error_warning'] = sprintf($this->language->get('error_no_payment'), $this->url->link('information/contact'));
		}

		if (isset($this->session->data['payment_methods'])) {
			$this->data['payment_methods'] = $this->session->data['payment_methods']; 
		} else {
			$this->data['payment_methods'] = array();
		}
	  
		if (isset($this->session->data['payment_method']['code'])) {
			$this->data['code'] = $this->session->data['payment_method']['code'];
		} else {
			$this->data['code'] = '';
		}
		
	
		if ($this->config->get('config_checkout_id')) {
			$this->load->model('catalog/information');
			
			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));
			
			if ($information_info) {
				$this->data['text_agree_payment'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/info', 'information_id=' . $this->config->get('config_checkout_id'), 'SSL'), $information_info['title'], $information_info['title']);
			} else {
				$this->data['text_agree_payment'] = '';
			}
		} else {
			$this->data['text_agree_payment'] = '';
		}
		
		if (isset($this->request->post['agree_payment'])) {
			$this->data['post_agree_payment'] = 1;
		} else {
			$this->data['post_agree_payment'] = 0;
		}
		
		/* END PAYMENT METHODS */
	
		
		
		
		/* START SHIPPING METHODS */
		$quote_data = array();
		// Dummy address
		if (!$this->config->get('simplified_checkout_dynamic_shipping') || $this->request->server['REQUEST_METHOD'] == 'POST' || isset($this->session->data['shippging_address_id']) || isset($this->session->data['guest']['shipping']) || $this->customer->isLogged())
		{
			if (isset($this->request->post['different_shipping_address']) && $this->request->post['different_shipping_address']) {
				$shipping_address = $this->request->post['different_shipping'];
			} elseif (isset($this->request->post['shipping'])) {
				$shipping_address = $this->request->post['shipping'];
			} elseif (isset($this->session->data['shipping_address_id'])) {
				$this->load->model('account/address');
				$shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
			} elseif (isset($this->session->data['guest']['shipping'])) {
				$shipping_address = $this->session->data['guest']['shipping'];
			} elseif ($this->customer->isLogged()) {
				$this->load->model('account/address');
				$address_id = $this->customer->getAddressId();
			
				$shipping_address = $this->model_account_address->getAddress($address_id);
			} else {
				$shipping_address = $payment_address;
			}
			
			// Check for fixed country or zones
			if ($this->config->get('simplified_checkout_hide_country') && $this->config->get('simplified_checkout_fixed_country')) {
				$shipping_address['country_id'] = $this->config->get('simplified_checkout_fixed_country');
			}
			
			if ($this->config->get('simplified_checkout_hide_zone')) {
				$shipping_address['zone_id'] = $this->config->get('simplified_checkout_fixed_zone');
			}
			
			
	  		$country_info = $this->model_localisation_country->getCountry($shipping_address['country_id']);
	  		$shipping_address = array_merge($shipping_address, $country_info);
			
			// Get data
			
			
			$results = $this->model_setting_extension->getExtensions('shipping');
			$i = 0;
			foreach ($results as $result) {
				if ($this->config->get($result['code'] . '_status')) {
					$this->load->model('shipping/' . $result['code']);
					
					$quote = $this->{'model_shipping_' . $result['code']}->getQuote($shipping_address); 
		
					if ($quote) {
						$quote_data[$result['code']] = array( 
							'title'      => $quote['title'],
							'quote'      => $quote['quote'], 
							'sort_order' => $quote['sort_order'],
							'error'      => $quote['error']
						);
						
						if ($i == 0) {
							foreach ($quote['quote'] as $q) {
								$this->session->data['shipping_method'] = $q;
								$i++;
								break;
							}
						}
						
						//$i++;
					}
				}
			}
	
			$sort_order = array();
		  
			foreach ($quote_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}
	
			array_multisort($sort_order, SORT_ASC, $quote_data);
		}
		$this->session->data['shipping_methods'] = $quote_data;
		
		
		// Asign data to template
		$this->data['text_shipping_method'] = $this->language->get('text_shipping_method');
		$this->data['text_comments'] = $this->language->get('text_comments');
		
		if (isset($this->session->data['shipping_methods']) && !$this->session->data['shipping_methods']) {
			$this->data['error_warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
		}
					
		if (isset($this->session->data['shipping_methods'])) {
			$this->data['shipping_methods'] = $this->session->data['shipping_methods']; 
		} else {
			$this->data['shipping_methods'] = array();
		}
		
		if (isset($this->session->data['shipping_method']['code'])) {
			$this->data['shipping_code'] = $this->session->data['shipping_method']['code'];
		} else {
			$this->data['shipping_code'] = '';
		}

		/* END SHIPPING METHODS */
		
		// Update totals to include the cost of the default shipping.
		/* START TOTALS */
    	$total_data = array();
		$total = 0;
		$taxes = $this->cart->getTaxes();
			
    	$sort_order = array(); 
			
		$results = $this->model_setting_extension->getExtensions('total');
		
		foreach ($results as $key => $value) {
			$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
		}
		
		array_multisort($sort_order, SORT_ASC, $results);
		
		foreach ($results as $result) {
			if ($this->config->get($result['code'] . '_status')) {
				$this->load->model('total/' . $result['code']);
	
				$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
			}
		}
		
		$sort_order = array(); 
	  
		foreach ($total_data as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}

		array_multisort($sort_order, SORT_ASC, $total_data);
    	
    	
    	/* END TOTALS */
		
		/* START LOGIN */
		
		$this->language->load('account/account');
		$this->language->load('account/login');
		
		$this->data['button_login'] = $this->language->get('button_login');
		$this->data['text_i_am_returning_customer'] = $this->language->get('text_i_am_returning_customer');
		$this->data['text_forgotten'] = $this->language->get('text_forgotten');
		$this->data['forgotten'] = $this->url->link('account/forgotten', '', 'SSL');
		
		if (isset($this->error['login'])) {
			$this->data['error_login'] = $this->error['login'];
		} else {
			$this->data['error_login'] = '';
		}
		
		/* END LOGIN */
		
		/* START USER DETAILS */
		
		if ($this->config->get('config_account_id') && !$this->config->get('simplified_checkout_hide_account_terms')) {
			$this->load->model('catalog/information');
			
			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
			
			if ($information_info) {
				$this->data['text_agree_account'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/info', 'information_id=' . $this->config->get('config_account_id'), 'SSL'), $information_info['title'], $information_info['title']);
			} else {
				$this->data['text_agree_account'] = '';
			}
		} else {
			$this->data['text_agree_account'] = '';
		}
		
		if (isset($this->request->post['agree_account'])) {
			$this->data['post_agree_account'] = 1;
		} else {
			$this->data['post_agree_account'] = 0;
		}

		$this->data['entry_firstname'] = $this->language->get('entry_firstname');
    	$this->data['entry_lastname'] = $this->language->get('entry_lastname');
    	$this->data['entry_email'] = $this->language->get('entry_email');
    	$this->data['entry_telephone'] = $this->language->get('entry_telephone');
    	$this->data['entry_fax'] = $this->language->get('entry_fax');
    	$this->data['entry_company'] = $this->language->get('entry_company');
    	$this->data['entry_address_1'] = $this->language->get('entry_address_1');
    	$this->data['entry_address_2'] = $this->language->get('entry_address_2');
    	$this->data['entry_postcode'] = $this->language->get('entry_postcode');
    	$this->data['entry_city'] = $this->language->get('entry_city');
    	$this->data['entry_country'] = $this->language->get('entry_country');
    	$this->data['entry_zone'] = $this->language->get('entry_zone');
		$this->data['entry_newsletter'] = $this->language->get('entry_newsletter');
    	$this->data['entry_password'] = $this->language->get('entry_password');
    	$this->data['entry_confirm'] = $this->language->get('entry_confirm');
    	
    	$this->data['entry_create_account'] = $this->language->get('entry_create_account');
    	$this->data['text_yes'] = $this->language->get('text_yes');
    	$this->data['text_no'] = $this->language->get('text_no');
		
		$this->data['text_select'] = $this->language->get('text_select');
		
		$this->data['guest_checkout'] = ($this->config->get('config_guest_checkout') && !$this->config->get('config_customer_price') && !$this->cart->hasDownload());
		
		$this->load->model('localisation/country');
		
    	$this->data['countries'] = $this->model_localisation_country->getCountries();
		
		
		if (isset($this->error['firstname'])) {
			$this->data['error_firstname'] = $this->error['firstname'];
		} else {
			$this->data['error_firstname'] = '';
		}	
		
		if (isset($this->error['lastname'])) {
			$this->data['error_lastname'] = $this->error['lastname'];
		} else {
			$this->data['error_lastname'] = '';
		}		
	
		if (isset($this->error['address_1'])) {
			$this->data['error_address_1'] = $this->error['address_1'];
		} else {
			$this->data['error_address_1'] = '';
		}
		
		if (isset($this->error['postcode'])) {
			$this->data['error_postcode'] = $this->error['postcode'];
		} else {
			$this->data['error_postcode'] = '';
		}
		
		if (isset($this->error['city'])) {
			$this->data['error_city'] = $this->error['city'];
		} else {
			$this->data['error_city'] = '';
		}
		
		if (isset($this->error['country'])) {
			$this->data['error_country'] = $this->error['country'];
		} else {
			$this->data['error_country'] = '';
		}
		
		if (isset($this->error['zone'])) {
			$this->data['error_zone'] = $this->error['zone'];
		} else {
			$this->data['error_zone'] = '';
		}
	
		if (isset($this->error['email'])) {
			$this->data['error_email'] = $this->error['email'];
		} else {
			$this->data['error_email'] = '';
		}
		
		if (isset($this->error['telephone'])) {
			$this->data['error_telephone'] = $this->error['telephone'];
		} else {
			$this->data['error_telephone'] = '';
		}
		
		if (isset($this->error['password'])) {
			$this->data['error_password'] = $this->error['password'];
		} else {
			$this->data['error_password'] = '';
		}
		
 		if (isset($this->error['confirm'])) {
			$this->data['error_confirm'] = $this->error['confirm'];
		} else {
			$this->data['error_confirm'] = '';
		}
		
		// Different shipping address
		if (isset($this->error['different_firstname'])) {
			$this->data['error_different_firstname'] = $this->error['different_firstname'];
		} else {
			$this->data['error_different_firstname'] = '';
		}	
		
		if (isset($this->error['different_lastname'])) {
			$this->data['error_different_lastname'] = $this->error['different_lastname'];
		} else {
			$this->data['error_different_lastname'] = '';
		}		
	
		if (isset($this->error['different_address_1'])) {
			$this->data['error_different_address_1'] = $this->error['different_address_1'];
		} else {
			$this->data['error_different_address_1'] = '';
		}
		
		
		if (isset($this->error['different_postcode'])) {
			$this->data['error_different_postcode'] = $this->error['different_postcode'];
		} else {
			$this->data['error_different_postcode'] = '';
		}
		
		if (isset($this->error['different_city'])) {
			$this->data['error_different_city'] = $this->error['different_city'];
		} else {
			$this->data['error_different_city'] = '';
		}
		
		if (isset($this->error['different_country'])) {
			$this->data['error_different_country'] = $this->error['different_country'];
		} else {
			$this->data['error_different_country'] = '';
		}
		
		if (isset($this->error['different_zone'])) {
			$this->data['error_different_zone'] = $this->error['different_zone'];
		} else {
			$this->data['error_different_zone'] = '';
		}
		
		// If the user is logged in prefill the order information
		if (isset($this->session->data['payment_address_id'])) {
			$this->load->model('account/address');
			$shipping_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
			$email = $this->customer->getEmail();
			$telephone = $this->customer->getTelephone();
			$fax = $this->customer->getFax();
			
			$this->data['company'] = $shipping_address['company'];
			$this->data['firstname'] = $shipping_address['firstname'];
			$this->data['lastname'] = $shipping_address['lastname'];
			$this->data['address_1'] = $shipping_address['address_1'];
			$this->data['address_2'] = $shipping_address['address_2'];
			$this->data['postcode'] = $shipping_address['postcode'];
			$this->data['city'] = $shipping_address['city'];
			$this->data['country_id'] = $shipping_address['country_id'];
			$this->data['zone_id'] = $shipping_address['zone_id'];
			$this->data['email'] = $email;
			$this->data['telephone2'] = $telephone;
			$this->data['fax'] = $fax;
		}
		elseif (isset($this->session->data['guest']['firstname'])) {
			$this->data['company'] = $this->session->data['guest']['payment']['company'];
			$this->data['firstname'] = $this->session->data['guest']['firstname'];
			$this->data['lastname'] = $this->session->data['guest']['lastname'];
			$this->data['address_1'] = $this->session->data['guest']['payment']['address_1'];
			$this->data['address_2'] = $this->session->data['guest']['payment']['address_2'];
			$this->data['postcode'] = $this->session->data['guest']['payment']['postcode'];
			$this->data['city'] = $this->session->data['guest']['payment']['city'];
			$this->data['country_id'] = $this->session->data['guest']['payment']['country_id'];
			$this->data['zone_id'] = $this->session->data['guest']['payment']['zone_id'];
			$this->data['email'] = $this->session->data['guest']['email'];
			$this->data['telephone2'] = $this->session->data['guest']['telephone'];
			$this->data['fax'] = $this->session->data['guest']['fax'];
		}
		elseif ($this->customer->isLogged()) {
			$this->load->model('account/address');
			$address_id = $this->customer->getAddressId();
			
			$shipping_address = $this->model_account_address->getAddress($address_id);
			$email = $this->customer->getEmail();
			$telephone = $this->customer->getTelephone();
			$fax = $this->customer->getFax();
			
			$this->data['company'] = $shipping_address['company'];
			$this->data['firstname'] = $shipping_address['firstname'];
			$this->data['lastname'] = $shipping_address['lastname'];
			$this->data['address_1'] = $shipping_address['address_1'];
			$this->data['address_2'] = $shipping_address['address_2'];
			$this->data['postcode'] = $shipping_address['postcode'];
			$this->data['city'] = $shipping_address['city'];
			$this->data['country_id'] = $shipping_address['country_id'];
			$this->data['zone_id'] = $shipping_address['zone_id'];
			$this->data['email'] = $email;
			$this->data['telephone2'] = $telephone;
			$this->data['fax'] = $fax;
	
		}
		else {
			$this->data['company'] = '';
			$this->data['firstname'] = '';
			$this->data['lastname'] = '';
			$this->data['address_1'] = '';
			$this->data['address_2'] = '';
			$this->data['postcode'] = '';
			$this->data['city'] = '';
			$this->data['country_id'] = '';
			$this->data['zone_id'] = '';
			$this->data['email'] = '';
			$this->data['telephone2'] = '';
			$this->data['fax'] = '';
		}
		
		if (isset($this->request->post['checkout_type'])) {
			$this->data['checkout_type'] = $this->request->post['checkout_type'];
		} else {
			$this->data['checkout_type'] = '';
		}
		
		if (isset($this->request->post['shipping']['company'])) {
    		$this->data['company'] = $this->request->post['shipping']['company'];
		}

		if (isset($this->request->post['shipping']['firstname'])) {
    		$this->data['firstname'] = $this->request->post['shipping']['firstname'];
		} 

		if (isset($this->request->post['shipping']['lastname'])) {
    		$this->data['lastname'] = $this->request->post['shipping']['lastname'];
		} 
		
		if (isset($this->request->post['shipping']['address_1'])) {
    		$this->data['address_1'] = $this->request->post['shipping']['address_1'];
		} 
		
		if (isset($this->request->post['shipping']['address_2'])) {
    		$this->data['address_2'] = $this->request->post['shipping']['address_2'];
		} 
		
		if (isset($this->request->post['shipping']['postcode'])) {
    		$this->data['postcode'] = $this->request->post['shipping']['postcode'];
		} 
		
		if (isset($this->request->post['shipping']['city'])) {
    		$this->data['city'] = $this->request->post['shipping']['city'];
		} 
		
		if (isset($this->request->post['shipping']['country_id'])) {
    		$this->data['country_id'] = $this->request->post['shipping']['country_id'];
		} 
		
		if (isset($this->request->post['shipping']['zone_id'])) {
    		$this->data['zone_id'] = $this->request->post['shipping']['zone_id'];
		} 
		
		if (isset($this->request->post['email'])) {
    		$this->data['email'] = $this->request->post['email'];
		} 
		
		if (isset($this->request->post['customer']['telephone'])) {
    		$this->data['telephone2'] = $this->request->post['customer']['telephone'];
		} 	
		
		if (isset($this->request->post['customer']['fax'])) {
    		$this->data['fax'] = $this->request->post['customer']['fax'];
		} 
		
		$this->load->model('localisation/zone');

		

		if ($this->data['country_id']) {
    		$this->data['zones'] = $this->model_localisation_zone->getZonesByCountryId($this->data['country_id']);
		}
		else {
			$this->data['zones'] = array();
		}
		
		if (!$this->config->get('simplified_checkout_hide_zone') && $this->config->get('simplified_checkout_hide_country')) {
			$this->data['zones'] = $this->model_localisation_zone->getZonesByCountryId($this->config->get('simplified_checkout_fixed_country'));
		}
		/* END USER DETAILS */
		
		/* START DIFFERENT SHIPPING ADDRESS */
		
		$this->language->load('checkout/simplified_checkout');
		
		if (isset($this->request->post['different_shipping_address'])) {
			$this->data['different_shipping_address'] = 'checked="checked"';
		} else {
			$this->data['different_shipping_address'] = '';
		}
	
		$this->data['entry_different_shipping_address'] = $this->language->get('entry_different_shipping_address');
		
		
		if (isset($this->request->post['different_shipping']['company'])) {
    		$this->data['different_company'] = $this->request->post['different_shipping']['company'];
		} else {
			$this->data['different_company'] = '';
		}

		if (isset($this->request->post['different_shipping']['firstname'])) {
    		$this->data['different_firstname'] = $this->request->post['different_shipping']['firstname'];
		} else {
			$this->data['different_firstname'] = '';
		}

		if (isset($this->request->post['different_shipping']['lastname'])) {
    		$this->data['different_lastname'] = $this->request->post['different_shipping']['lastname'];
		} else {
			$this->data['different_lastname'] = '';
		}
		
		if (isset($this->request->post['different_shipping']['address_1'])) {
    		$this->data['different_address_1'] = $this->request->post['different_shipping']['address_1'];
		} else {
			$this->data['different_address_1'] = '';
		}
		
		if (isset($this->request->post['different_shipping']['address_2'])) {
    		$this->data['different_address_2'] = $this->request->post['different_shipping']['address_2'];
		} else {
			$this->data['different_address_2'] = '';
		}
		
		if (isset($this->request->post['different_shipping']['postcode'])) {
    		$this->data['different_postcode'] = $this->request->post['different_shipping']['postcode'];
		} else {
			$this->data['different_postcode'] = '';
		}
		
		if (isset($this->request->post['different_shipping']['city'])) {
    		$this->data['different_city'] = $this->request->post['different_shipping']['city'];
		} else {
			$this->data['different_city'] = '';
		}
		
		if (isset($this->request->post['different_shipping']['country_id'])) {
    		$this->data['different_country_id'] = $this->request->post['different_shipping']['country_id'];
		} else {
			$this->data['different_country_id'] = $this->config->get('config_country_id');
		}
		
		/* END DIFFERENT SHIPPING ADDRESS */
		

		/* DISABLED INPUTS */
		if ($this->config->get('simplified_checkout_hide_country') && $this->config->get('simplified_checkout_fixed_country')) {
			$this->data['hide_country'] = true;
		} else {
			$this->data['hide_country'] = false;
		}
		
		if ($this->config->get('simplified_checkout_hide_zone')) {
			$this->data['hide_zone'] = true;
		} else {
			$this->data['hide_zone'] = false;
		}
		
		

		// General vars
		$this->language->load('checkout/simplified_checkout');
		
		$this->load->model('checkout/order');

		$this->data['breadcrumbs'] = array();

      	$this->data['breadcrumbs'][] = array(
        	'href'      => HTTP_SERVER . 'index.php?route=common/home',
        	'text'      => $this->language->get('text_home'),
        	'separator' => FALSE
      	); 

      	$this->data['breadcrumbs'][] = array(
        	'href'      => HTTP_SERVER . 'index.php?route=checkout/cart',
        	'text'      => $this->language->get('text_cart'),
        	'separator' => $this->language->get('text_separator')
      	);
		

      	$this->data['breadcrumbs'][] = array(
       		'href'      => HTTP_SERVER . 'index.php?route=checkout/simplified_checkout',
       		'text'      => $this->language->get('heading_title'),
       		'separator' => $this->language->get('text_separator')
      	);


		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['loading_image'] = HTTPS_SERVER . 'catalog/view/theme/default/image/loading.gif';

    	$this->data['text_shipping_address'] = $this->language->get('text_shipping_address');
    	$this->data['text_shipping_method'] = $this->language->get('text_shipping_method');
    	$this->data['text_payment_address'] = $this->language->get('text_payment_address');
    	$this->data['text_payment_method'] = $this->language->get('text_payment_method');
    	$this->data['text_comment'] = $this->language->get('text_comment');
		$this->data['text_coupon'] = $this->language->get('text_coupon');
    	$this->data['text_change'] = $this->language->get('text_change');
    	
    	$this->data['text_dynamic_shipping'] = $this->language->get('text_dynamic_shipping');

		$this->data['button_order'] = $this->language->get('text_order');
		$this->data['button_continue'] = $this->language->get('button_continue');
		$this->data['button_update_shipping'] = $this->language->get('button_update_shipping');
		
		$this->data['column_name'] = $this->language->get('column_name');
    	$this->data['column_model'] = $this->language->get('column_model');
    	$this->data['column_quantity'] = $this->language->get('column_quantity');
    	$this->data['column_price'] = $this->language->get('column_price');
    	$this->data['column_total'] = $this->language->get('column_total');
    	
    	if ($this->config->get('simplified_checkout_dynamic_shipping')) {
			$this->data['dynamic_shipping'] = 1;
		} else {
			$this->data['dynamic_shipping'] = 0;
		}
		
		if (isset($this->error['warning'])) {
    		$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
		
    		unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
	
	
		// Products
		$this->load->model('tool/image');
		
    	$this->data['products'] = array();

    	foreach ($this->cart->getProducts() as $product) {
			$option_data = array();

			foreach ($product['option'] as $option) {
				if ($option['type'] != 'file') {
					$option_data[] = array(
						'name'  => $option['name'],
						'value' => (strlen($option['option_value']) > 20 ? substr($option['option_value'], 0, 20) . '..' : $option['option_value'])
					);
				} else {
					$this->load->library('encryption');
					
					$encryption = new Encryption($this->config->get('config_encryption'));
					
					$file = substr($encryption->decrypt($option['option_value']), 0, strrpos($encryption->decrypt($option['option_value']), '.'));
					
					$option_data[] = array(
						'name'  => $option['name'],
						'value' => (strlen($file) > 20 ? substr($file, 0, 20) . '..' : $file)
					);												
				}
			}  
			
			
				
			//  Open Cart < 1.5.1.3
			if (method_exists($this->tax, 'getRate')) {
				$price = $this->currency->format($product['price']);
				$total = $this->currency->format($product['total']);
				$tax = $this->tax->getRate($product['tax_class_id']);
			}
			else {
				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$price = false;
				}
	
				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$total = $this->currency->format($this->tax->calculate($product['total'], $product['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$total = false;
				}
				$tax = '';
			}
				
				
			if ($product['image']) {
				$image = $this->model_tool_image->resize($product['image'], 50, 50);
			} else {
				$image = false;
			}
 
			$this->data['products'][] = array(
				'product_id' => $product['product_id'],
				'image'		 => $image,
				'name'       => $product['name'],
				'model'      => $product['model'],
				'option'     => $option_data,
				'quantity'   => $product['quantity'],
				'subtract'   => $product['subtract'],
				'tax'        => $tax,
				'price'      => $price,
				'total'      => $total,
				'href'       => $this->url->link('product/product', 'product_id=' . $product['product_id'])
			); 
		}
    	
		// Gift Voucher
		$this->data['vouchers'] = array();
		
		if (isset($this->session->data['vouchers']) && $this->session->data['vouchers']) {
			foreach ($this->session->data['vouchers'] as $key => $voucher) {
				$this->data['vouchers'][] = array(
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'])
				);
			}
		}  
		
		// Comment
		if (isset($this->request->post['comment'])) {
			$this->data['comment'] = $this->request->post['comment'];
		} else {
			$this->data['comment'] = '';
		}
		
		$this->data['totals'] = $total_data;
		
		$this->data['action'] = $this->url->link('checkout/simplified_checkout', '', 'SSL');
		
		// Coupon
		if ($this->config->get('simplified_checkout_show_coupon')) {
			$this->data['show_coupon'] = true;
		} else {
			$this->data['show_coupon'] = false;
		}
		$this->language->load('total/coupon');
		$this->data['entry_coupon'] = $this->language->get('heading_title');
		$this->data['button_coupon'] = $this->language->get('button_coupon');
		$this->data['coupon_success'] = $this->language->get('text_success');

		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/simplified_checkout_2column.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/simplified_checkout_2column.tpl';
		} else {
			$this->template = 'default/template/checkout/simplified_checkout_2column.tpl';
		}

		/*
		if ($this->config->get('simplified_checkout_template') && $this->config->get('simplified_checkout_template') == '2column') {
			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/simplified_checkout_2column.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/simplified_checkout_2column.tpl';
			} else {
				$this->template = 'default/template/checkout/simplified_checkout_2column.tpl';
			}
		}
		else {
			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/simplified_checkout.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/simplified_checkout.tpl';
			} else {
				$this->template = 'default/template/checkout/simplified_checkout.tpl';
			}
		}
		*/
		
		$this->children = array(
				'common/column_left',
				'common/column_right',
				'common/content_top',
				'common/content_bottom',
				'common/footer',
				'common/header'
			);

		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
  	}
	
  	
  	private function validateLogin() {
  		$this->language->load('account/login');
  		
    	if (!$this->customer->login($this->request->post['email'], $this->request->post['password'])) {
      		$this->error['login'] = $this->language->get('error_login');
    	}
	
    	if (!$this->error) {
      		return TRUE;
    	} else {
      		return FALSE;
    	}  	
  	}
  	
  	private function validateAccount() {
  		
  		$this->language->load('account/register');
  		if ((strlen(utf8_decode($this->request->post['shipping']['firstname'])) < 1) || (strlen(utf8_decode($this->request->post['shipping']['firstname'])) > 32)) {
      		$this->error['firstname'] = $this->language->get('error_firstname');
    	}

    	if ((strlen(utf8_decode($this->request->post['shipping']['lastname'])) < 1) || (strlen(utf8_decode($this->request->post['shipping']['lastname'])) > 32)) {
      		$this->error['lastname'] = $this->language->get('error_lastname');
    	}
    	
    	if ((strlen(utf8_decode($this->request->post['shipping']['address_1'])) < 3) || (strlen(utf8_decode($this->request->post['shipping']['address_1'])) > 128)) {
      		$this->error['address_1'] = $this->language->get('error_address_1');
    	}
    	
    	if ($this->config->get('simplified_checkout_hide_country') && $this->config->get('simplified_checkout_fixed_country')) {
			$country_id = $this->config->get('simplified_checkout_fixed_country');
		} else {
			$country_id = $this->request->post['shipping']['country_id'];
		}
		
    	$this->load->model('localisation/country');
		
		$country_info = $this->model_localisation_country->getCountry($country_id);
    	
    	if ($country_info && $country_info['postcode_required'] && (strlen(utf8_decode($this->request->post['shipping']['postcode'])) < 2) || (strlen(utf8_decode($this->request->post['shipping']['postcode'])) > 10)) {
			$this->error['postcode'] = $this->language->get('error_postcode');
		}
		
		
		if ((strlen(utf8_decode($this->request->post['shipping']['city'])) < 3) || (strlen(utf8_decode($this->request->post['shipping']['city'])) > 128)) {
      		$this->error['city'] = $this->language->get('error_city');
    	}
		
		if (!$this->config->get('simplified_checkout_hide_country') || !$this->config->get('simplified_checkout_fixed_country')) {
			if ($this->request->post['shipping']['country_id'] == '') {
      			$this->error['country'] = $this->language->get('error_country');
    		}
    	}
    	

		if (!$this->customer->isLogged()) {
			if ((strlen(utf8_decode($this->request->post['email'])) > 96) || (!preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email']))) {
	      		$this->error['email'] = $this->language->get('error_email');
	    	}
	    	
	    	if ((strlen(utf8_decode($this->request->post['customer']['telephone'])) < 3) || (strlen(utf8_decode($this->request->post['customer']['telephone'])) > 32)) {
	      		$this->error['telephone'] = $this->language->get('error_telephone');
	    	}
	    	
	    	
    		if ((isset($this->request->post['checkout_type']) && $this->request->post['checkout_type'] == 'account') || !$this->config->get('config_guest_checkout') || $this->cart->hasDownload()) {
    			if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
      				$this->error['warning'] = $this->language->get('error_exists');
    			}
    			
    			if ((strlen(utf8_decode($this->request->post['customer']['password'])) < 4) || (strlen(utf8_decode($this->request->post['customer']['password'])) > 20)) {
	      			$this->error['password'] = $this->language->get('error_password');
	    		}
	
	    		if ($this->request->post['customer']['confirm'] != $this->request->post['customer']['password']) {
	      			$this->error['confirm'] = $this->language->get('error_confirm');
	    		}
    		}
    		
    		if ($this->config->get('config_account_id') && (isset($this->request->post['checkout_type']) && $this->request->post['checkout_type'] == 'account' && !$this->config->get('simplified_checkout_hide_account_terms')) || (!$this->config->get('config_guest_checkout') || $this->cart->hasDownload())) {
				$this->load->model('catalog/information');
					
				$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
					
				if ($information_info && !isset($this->request->post['agree_account'])) {
					$this->error['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
				}
			}
		}
		
		/* DIFFERENT SHIPPING ADDRESS */
		if (isset($this->request->post['different_shipping_address']))
  		{
	  		
	  		
	  		if ((strlen(utf8_decode($this->request->post['different_shipping']['firstname'])) < 1) || (strlen(utf8_decode($this->request->post['different_shipping']['firstname'])) > 32)) {
	      		$this->error['different_firstname'] = $this->language->get('error_firstname');
	    	}
	
	    	if ((strlen(utf8_decode($this->request->post['different_shipping']['lastname'])) < 1) || (strlen(utf8_decode($this->request->post['different_shipping']['lastname'])) > 32)) {
	      		$this->error['different_lastname'] = $this->language->get('error_lastname');
	    	}
	    	
	    	if ((strlen(utf8_decode($this->request->post['different_shipping']['address_1'])) < 3) || (strlen(utf8_decode($this->request->post['different_shipping']['address_1'])) > 128)) {
	      		$this->error['different_address_1'] = $this->language->get('error_address_1');
	    	}
	    	
	    	if ($this->config->get('simplified_checkout_hide_country') && $this->config->get('simplified_checkout_fixed_country')) {
				$different_country_id = $this->config->get('simplified_checkout_fixed_country');
			} else {
				$different_country_id = $this->request->post['different_shipping']['country_id'];
			}
			
	    	$this->load->model('localisation/country');
			
			$different_country_info = $this->model_localisation_country->getCountry($different_country_id);

	    	if ($different_country_info && $different_country_info['postcode_required'] && (strlen(utf8_decode($this->request->post['different_shipping']['postcode'])) < 2) || (strlen(utf8_decode($this->request->post['different_shipping']['postcode'])) > 10)) {
				$this->error['different_postcode'] = $this->language->get('error_postcode');
			}
			
			
			if ((strlen(utf8_decode($this->request->post['different_shipping']['city'])) < 3) || (strlen(utf8_decode($this->request->post['different_shipping']['city'])) > 128)) {
	      		$this->error['different_city'] = $this->language->get('error_city');
	    	}
		}
		
		if ($this->config->get('config_checkout_id')) {
			$this->load->model('catalog/information');
			
			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));
			
			if ($information_info && !isset($this->request->post['agree_payment'])) {
				$this->error['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
			}
		}	

  		if (!$this->error) {
      		return TRUE;
    	} else {
      		return FALSE;
    	}
  	}
  	
  	public function getPaymentMethods() {
  	
  		if (isset($this->request->get['country_id'])) {
  			$country_id = $this->request->get['country_id'];
  		} else {
  			$country_id = $this->config->get('config_country_id');
  		}
  		
  		if (isset($this->request->get['zone_id'])) {
  			$zone_id = $this->request->get['zone_id'];
  		} else {
  			$zone_id = 0;
  		}
  		
  		// Calculate Totals
		$total_data = array();					
		$total = 0;
		$taxes = $this->cart->getTaxes();
		
		$this->load->model('setting/extension');
		
		$sort_order = array(); 
		
		$results = $this->model_setting_extension->getExtensions('total');
		
		foreach ($results as $key => $value) {
			$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
		}
		
		array_multisort($sort_order, SORT_ASC, $results);
		
		foreach ($results as $result) {
			if ($this->config->get($result['code'] . '_status')) {
				$this->load->model('total/' . $result['code']);
	
				$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
			}
		}
  		 
  		$payment_address = array('zone_id' => $zone_id, 'country_id' => $country_id);
  		
  		// Get data
		$method_data = array();
		
		$this->load->model('setting/extension');
		$results = $this->model_setting_extension->getExtensions('payment');

		foreach ($results as $result) {
			if ($this->config->get($result['code'] . '_status')) {
				$this->load->model('payment/' . $result['code']);
				
				$method = $this->{'model_payment_' . $result['code']}->getMethod($payment_address, $total); 
				
				if ($method) {
					$method_data[$result['code']] = $method;
				}
			}
		}
					 
		$sort_order = array(); 
	  
		foreach ($method_data as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}

		array_multisort($sort_order, SORT_ASC, $method_data);			
		
		$data['payment_methods'] = $method_data;
		$this->session->data['payment_methods'] = $data['payment_methods'];
		$this->response->setOutput(json_encode($data['payment_methods']));	
		
		
  	}
  	  	
  	public function getShippingMethods() {
  		if (isset($this->request->get['shipping'])) {
  			$shipping_address = $this->request->get['shipping'];
  		} elseif (isset($this->request->get['different_shipping'])) {
  			$shipping_address = $this->request->get['different_shipping'];
  		}
  
  		if (isset($shipping_address['country_id']) && !empty($shipping_address['country_id'])) {
  			$shipping_address['country_id'] = $shipping_address['country_id'];
  		} else {
  			$shipping_address['country_id'] = $this->config->get('config_country_id');
  		}
  		
  		if (isset($shipping_address['zone_id']) && !empty($shipping_address['zone_id'])) {
  			$shipping_address['zone_id'] = $shipping_address['zone_id'];
  		} else {
  			$shipping_address['zone_id'] = 0;
  		}
  		
  		$this->load->model('localisation/country');
  		$country_info = $this->model_localisation_country->getCountry($shipping_address['country_id']);
  		$shipping_address = array_merge($shipping_address, $country_info);
  		$quote_data = array();

		$this->load->model('setting/extension');
		
		$results = $this->model_setting_extension->getExtensions('shipping');
		
		foreach ($results as $result) {
			if ($this->config->get($result['code'] . '_status')) {
				$this->load->model('shipping/' . $result['code']);
				
				$quote = $this->{'model_shipping_' . $result['code']}->getQuote($shipping_address); 
	
				if ($quote) {
					$quote_data[$result['code']] = array( 
						'title'      => $quote['title'],
						'quote'      => $quote['quote'], // 'quote' => $quote['quote']
						'sort_order' => $quote['sort_order'],
						'error'      => $quote['error']
					);
				}
			}
		}

		$sort_order = array();
	  
		foreach ($quote_data as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}

		array_multisort($sort_order, SORT_ASC, $quote_data);
		
		$data['shipping_methods'] = $quote_data;
		//print_r($data['shipping_methods']);
		$this->session->data['shipping_methods'] = $data['shipping_methods'];
		$this->response->setOutput(json_encode($data['shipping_methods']));
  	}
  	
  	private function setShippingAddress() {
  		
  	}
  	
  	private function getShippingAddress() {
  		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
	  		$data = $this->request->post;
		} elseif ($this->request->server['REQUEST_METHOD'] == 'GET') {
			$data = $this->request->get;
		}
		
		if (isset($data['different_shipping_address']) && $data['different_shipping_address']) {
			$shipping_address = $data['different_shipping'];
		} elseif (isset($data['shipping'])) {
			$shipping_address = $data['shipping'];
		} elseif (isset($this->session->data['shipping_address_id'])) {
			$this->load->model('account/address');
			$shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
		} elseif (isset($this->session->data['guest']['shipping'])) {
			$shipping_address = $this->session->data['guest']['shipping'];
		} elseif ($this->customer->isLogged()) {
			$this->load->model('account/address');
			$address_id = $this->customer->getAddressId();
		
			$shipping_address = $this->model_account_address->getAddress($address_id);
		} else {
			$shipping_address = array('zone_id' => 0, 'country_id' => $this->config->get('config_country_id'));
		}
		
		// Check for fixed country or zones
		if ($this->config->get('simplified_checkout_hide_country') && $this->config->get('simplified_checkout_fixed_country')) {
			$shipping_address['country_id'] = $this->config->get('simplified_checkout_fixed_country');
		}
		
		if ($this->config->get('simplified_checkout_hide_zone')) {
			$shipping_address['zone_id'] = $this->config->get('simplified_checkout_fixed_zone');
		}
		
		$this->load->model('localisation/country');
	  	$country_info = $this->model_localisation_country->getCountry($shipping_address['country_id']);
	  	$shipping_address = array_merge($shipping_address, $country_info);
	  	
	  	return $shipping_address;
  	}
  	
  	public function getTotals() {
  		// Set the shipping method.
  		if (isset($this->request->get['shipping_method'])) {
		    $shipping = explode('.', $this->request->get['shipping_method']);
			$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
		}
		
		if (!isset($this->session->data['guest']['shipping'])) {
			$reset_shipping = true;
			$this->session->data['guest']['shipping'] = $this->getShippingAddress();
		} else {
			$reset_shipping = false;
		}
		
  		$total_data = array();
		$total = 0;
		$taxes = $this->cart->getTaxes();

    	$sort_order = array(); 
		
		$this->load->model('setting/extension');
		$results = $this->model_setting_extension->getExtensions('total');
		
		foreach ($results as $key => $value) {
			$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
		}
		
		array_multisort($sort_order, SORT_ASC, $results);
		
		foreach ($results as $result) {
			if ($this->config->get($result['code'] . '_status')) {
				$this->load->model('total/' . $result['code']);
	
				$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
			}
		}
		
		$sort_order = array(); 
	  
		foreach ($total_data as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}

		array_multisort($sort_order, SORT_ASC, $total_data);
		
		$this->response->setOutput(json_encode($total_data));
		
		if ($reset_shipping) {
			unset($this->session->data['guest']['shipping']);
		}
  	}
  	
  	private function checkIfAddressExists($data) {
  		$this->load->model('account/address');
  		
  		$addresses = $this->model_account_address->getAddresses();
  		
  		$address_id = false;
  		
  		foreach ($addresses as $address) {
  			if ($address_id) {
  				break;
  			}
  			
  			$all_match = true;
  			
			foreach($data as $k => $v) {
				
				if ($address[$k] != $v) {
					$all_match = false;
					break;
				}
			}
			
			if ($all_match) {
				$address_id = $address['address_id'];
			}
  		}
  		
  		if ($address_id) {
  			return $address_id;
  		} else {
  			return false;
  		}
  		
  	}
  	
  	
}
?>