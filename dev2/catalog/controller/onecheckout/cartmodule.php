<?php  
class ControllerOneCheckoutCartModule extends Controller {

  	public function index() {
		$this->language->load('onecheckout/checkout');
		
		//coupon
			$this->data['coupon_status'] = $this->config->get('coupon_status');
			if($this->config->get('coupon_status')){
				$this->data['coupon_heading_title'] = $this->language->get('heading_title_coupon');
				$this->data['entry_coupon'] = $this->language->get('entry_coupon');
				$this->data['button_coupon'] = $this->language->get('button_coupon');				

				if (isset($this->session->data['coupon'])) {
					$this->data['coupon'] = $this->session->data['coupon'];
				} else {
					$this->data['coupon'] = '';
				}
			}
			//reward
			if($this->config->get('reward_status')){
				$points = $this->customer->getRewardPoints();
				$points_total = 0;
				foreach ($this->cart->getProducts() as $product) {
					if ($product['points']) {
						$points_total += $product['points'];
					}
				}						

				if ($points && $points_total && $this->config->get('reward_status')) {
					$this->data['reward_status'] = true;
					$this->data['reward_heading_title'] = sprintf($this->language->get('heading_title_reward'), $points);
					$this->data['entry_reward'] = sprintf($this->language->get('entry_reward'), $points_total);
					$this->data['button_reward'] = $this->language->get('button_reward');				

					if (isset($this->session->data['reward'])) {
						$this->data['reward'] = $this->session->data['reward'];
					} else {
						$this->data['reward'] = '';
					}
				} else {
					$this->data['reward_status'] = false;
				}
			} else {
				$this->data['reward_status'] = false;
			}
			//voucher
			$this->data['voucher_status'] = $this->config->get('voucher_status');
			if($this->config->get('voucher_status')){
				$this->data['voucher_heading_title'] = $this->language->get('heading_title_voucher');
				$this->data['entry_voucher'] = $this->language->get('entry_voucher');
				$this->data['button_voucher'] = $this->language->get('button_voucher');		

				if (isset($this->session->data['voucher'])) {
					$this->data['voucher'] = $this->session->data['voucher'];
				} else {
					$this->data['voucher'] = '';
				}
			}	
			
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/onecheckout/cartmodule.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/onecheckout/cartmodule.tpl';
		} else {
			$this->template = 'default/template/onecheckout/cartmodule.tpl';
		}
					
		$this->render();
  	}
	
	public function validateCoupon() {
		$this->language->load('onecheckout/checkout');
		$this->load->model('onecheckout/checkout');	
		$json = array();		
		if (!$this->cart->hasProducts()) {
			$json['redirect'] = $this->url->link('checkout/cart');				
		}	
				
		if (isset($this->request->post['coupon'])) {
			$this->load->model('checkout/coupon');	
			$coupon_info = $this->model_checkout_coupon->getCoupon($this->request->post['coupon']);	
			
			if ($coupon_info) {			
				$this->session->data['coupon'] = $this->request->post['coupon'];				
				$this->session->data['success'] = $this->language->get('text_success_coupon');				
				$json['redirect'] = $this->url->link('checkout/cart', '', 'SSL');
			} else {
				$json['error'] = $this->language->get('error_coupon');
			}
		}
		
		$this->response->setOutput($this->model_onecheckout_checkout->jsonencode($json));		
	}
	
	public function validateVoucher() {
		$this->load->model('onecheckout/checkout');
		$this->language->load('onecheckout/checkout');
		
		$json = array();
		
		if (!$this->cart->hasProducts()) {
			$json['redirect'] = $this->url->link('checkout/cart');				
		}	
				
		if (isset($this->request->post['voucher'])) {
			$this->load->model('checkout/voucher');
	
			$voucher_info = $this->model_checkout_voucher->getVoucher($this->request->post['voucher']);			
			
			if ($voucher_info) {			
				$this->session->data['voucher'] = $this->request->post['voucher'];
				
				$this->session->data['success'] = $this->language->get('text_success_voucher');
				
				$json['redirect'] = $this->url->link('checkout/cart', '', 'SSL');
			} else {
				$json['error'] = $this->language->get('error_voucher');
			}
		}
		
		$this->response->setOutput($this->model_onecheckout_checkout->jsonencode($json));		
	}
	
	public function validateReward() {
		$this->load->model('onecheckout/checkout');
		$this->language->load('onecheckout/checkout');
		
		$json = array();
		
		if (isset($this->request->post['reward'])) {
			if (!$this->request->post['reward']) {
				$json['error'] = $this->language->get('error_empty_reward');
			}
			
			$points = $this->customer->getRewardPoints();
			
			if ($this->request->post['reward'] > $points) {
				$json['error'] = sprintf($this->language->get('error_points_reward'), $this->request->post['reward']);
			}
			
			$points_total = 0;
			
			foreach ($this->cart->getProducts() as $product) {
				if ($product['points']) {
					$points_total += $product['points'];
				}
			}				
			
			if ($this->request->post['reward'] > $points_total) {
				$json['error'] = sprintf($this->language->get('error_maximum_reward'), $points_total);
			}
			
			if (!isset($json['error'])) {
				$this->session->data['reward'] = $this->request->post['reward'];
				
				$this->session->data['success'] = $this->language->get('text_success_reward');
				
				$json['redirect'] = $this->url->link('checkout/cart', '', 'SSL');
			}
		}
		
		$this->response->setOutput($this->model_onecheckout_checkout->jsonencode($json));		
	}
	
	public function validateShipping() {
		$this->load->model('onecheckout/checkout');
		$this->language->load('onecheckout/checkout');
		
		$json = array();
		
		if (isset($this->request->post['shipping_method']) && $this->request->post['shipping_method']) {
			$shipping = explode('.', $this->request->post['shipping_method']);
					
			if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {			
				$json['error'] = $this->language->get('error_shipping_shipping');
			}
		} else {
			$json['error'] = $this->language->get('error_shipping_shipping');
		}
		
		if (!isset($json['error'])) {
			$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
				
			$this->session->data['success'] = $this->language->get('text_success_shipping');
				
			$json['redirect'] = $this->url->link('checkout/cart', '', 'SSL');			
		}
		
		$this->response->setOutput($this->model_onecheckout_checkout->jsonencode($json));			
	}
	
	public function quote() {
		$this->load->model('onecheckout/checkout');
		$this->language->load('onecheckout/checkout');
		
		$json = array();	
		
		if (!$this->cart->hasProducts()) {
			$json['redirect'] = $this->url->link('checkout/cart');				
		}	
		
		if (isset($this->request->post['country_id']) && isset($this->request->post['zone_id']) && isset($this->request->post['postcode'])) {
			if ($this->request->post['country_id'] == '') {
				$json['error']['country'] = $this->language->get('error_country_shipping');
			}
			
			if ($this->request->post['zone_id'] == '') {
				$json['error']['zone'] = $this->language->get('error_zone_shipping');
			}	
				
			$this->load->model('localisation/country');
			
			$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);
			
			if ($country_info && $country_info['postcode_required'] && (strlen(utf8_decode($this->request->post['postcode'])) < 2) || (strlen(utf8_decode($this->request->post['postcode'])) > 10)) {
				$json['error']['postcode'] = $this->language->get('error_postcode_shipping');
			}
							
			if (!isset($json['error'])) {		
				
				$version_int = $this->model_onecheckout_checkout->versiontoint();
				//version
				if($version_int < 1513 && $version_int >= 1500){
					$this->tax->setZone($this->request->post['country_id'], $this->request->post['zone_id']);
				} elseif($version_int >= 1513) {
					$this->tax->setShippingAddress($this->request->post['country_id'], $this->request->post['zone_id']);
					
				}
			
				$this->session->data['guest']['shipping']['country_id'] = $this->request->post['country_id'];
				$this->session->data['guest']['shipping']['zone_id'] = $this->request->post['zone_id'];
				$this->session->data['guest']['shipping']['postcode'] = $this->request->post['postcode'];
				// Default Shipping Address
				if ($this->config->get('config_tax_customer') == 'shipping') {
					$this->session->data['shipping_country_id'] = $this->request->post['country_id'];
					$this->session->data['shipping_zone_id'] = $this->request->post['zone_id'];
					$this->session->data['shipping_postcode'] = $this->request->post['postcode'];
				}
			
				if ($country_info) {
					$country = $country_info['name'];
					$iso_code_2 = $country_info['iso_code_2'];
					$iso_code_3 = $country_info['iso_code_3'];
					$address_format = $country_info['address_format'];
				} else {
					$country = '';
					$iso_code_2 = '';
					$iso_code_3 = '';	
					$address_format = '';
				}
				
				$this->load->model('localisation/zone');
			
				$zone_info = $this->model_localisation_zone->getZone($this->request->post['zone_id']);
				
				if ($zone_info) {
					$zone = $zone_info['name'];
					$code = $zone_info['code'];
				} else {
					$zone = '';
					$code = '';
				}
			 
				$address_data = array(
					'firstname'      => '',
					'lastname'       => '',
					'company'        => '',
					'address_1'      => '',
					'address_2'      => '',
					'postcode'       => $this->request->post['postcode'],
					'city'           => '',
					'zone_id'        => $this->request->post['zone_id'],
					'zone'           => $zone,
					'zone_code'      => $code,
					'country_id'     => $this->request->post['country_id'],
					'country'        => $country,	
					'iso_code_2'     => $iso_code_2,
					'iso_code_3'     => $iso_code_3,
					'address_format' => $address_format
				);
			
				$quote_data = array();
				
				$this->load->model('setting/extension');
				
				$results = $this->model_setting_extension->getExtensions('shipping');
				
				foreach ($results as $result) {
					if ($this->config->get($result['code'] . '_status')) {
						$this->load->model('shipping/' . $result['code']);
						
						$quote = $this->{'model_shipping_' . $result['code']}->getQuote($address_data); 
			
						if ($quote) {
							$quote_data[$result['code']] = array( 
								'title'      => $quote['title'],
								'quote'      => $quote['quote'], 
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
				
				$this->session->data['shipping_methods'] = $quote_data;
				
				if (isset($this->session->data['shipping_methods'])) {
					$json['shipping_methods'] = $this->session->data['shipping_methods']; 
				}
				
				if (!isset($this->session->data['shipping_methods']) || !$this->session->data['shipping_methods']) {
					$json['error']['warning'] = sprintf($this->language->get('error_no_shipping_shipping'), $this->url->link('information/contact'));
				}				
			}	
		}
		
		$this->response->setOutput($this->model_onecheckout_checkout->jsonencode($json));						
	}
}

?>