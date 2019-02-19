<?php
class ControllerCheckoutShippingMethod extends Controller {
  	public function index() {
  	// echo '<pre>'; print_r($this->session->data['guest']); die('</pre>');
		$this->language->load('checkout/checkout');

		$this->load->model('account/address');
		$this->load->model('setting/extension');
		
		if ($this->customer->isLogged() && isset($this->session->data['shipping_address_id'])) {
			$shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
		} else{
			if (isset($this->session->data['shipping_temp']["active"])){
					if (isset($this->session->data['shipping_temp'][$this->session->data['shipping_temp']["active"]]["data"]))
					{
						$shipping_address = $this->session->data['shipping_temp'][$this->session->data['shipping_temp']["active"]]["data"];
					}
			} else {
					if (isset($this->session->data['guest'])){
					$shipping_address = $this->session->data['guest']['shipping'];
				}
			}
		}
		// print_r($shipping_address);exit;

		

		$today_day = date('l');
		$first_shipp_code = "";
		
		if (!empty($shipping_address) and $shipping_address['zone_id']!=0) {
			// echo date('Y-m-d h:iA');exit;
			// Shipping Methods
			$quote_data = array();

			// $this->load->model('setting/extension');

			$results = $this->model_setting_extension->getExtensions('shipping');

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

					}
				}
			}

			


			//echo $today_day;exit;
			if($today_day!='Friday' and $today_day!='Thursday')
			{
				
				
				unset($quote_data['multiflatrate']['quote']['multiflatrate_4']);	// visible Fedex Next Day Saturday (Ships Fri 4:00 pm PST) on Friday only
			}
			else
			{
				//echo date('d-m-Y h:i a');exit;
				$current_time = date('d-m-Y H:i:s');
				if($today_day=='Thursday')
				{

					
					if(strtotime($current_time) < strtotime(date("d-m-Y 16:30:00")))
					{

						unset($quote_data['multiflatrate']['quote']['multiflatrate_4']);	// visible Fedex Next Day Saturday (Ships Fri 4:00 pm PST) on Friday only
					}
				}
				else
				{
					if(strtotime($current_time) > strtotime(date("d-m-Y 16:00:00")))
					{
						unset($quote_data['multiflatrate']['quote']['multiflatrate_4']);	// visible Fedex Next Day Saturday (Ships Fri 4:00 pm PST) on Friday only
					}
				}
			}
			$sort_order = array();

			foreach ($quote_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $quote_data);
                          
                        
			if (empty($first_shipp_code)&&!empty($quote_data)){
				reset($quote_data);
				$first_key = key($quote_data);
				$first_shipp_code =$quote_data[$first_key]["quote"][key($quote_data[$first_key]["quote"])]['code'];
			}

			$this->session->data['shipping_methods'] = $quote_data;
		}

		$this->data['text_shipping_method'] = $this->language->get('text_shipping_method');
		$this->data['text_comments'] = $this->language->get('text_comments');

		$this->data['button_continue'] = $this->language->get('button_continue');

		if (empty($this->session->data['shipping_methods'])) {
			$this->data['error_warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
		} else {
			$this->data['error_warning'] = '';
		}
		if (isset($this->session->data['shipping_methods'])) {
			$this->data['shipping_methods'] = $this->session->data['shipping_methods'];
		} else {
			$this->data['shipping_methods'] = array();
		}

		if (isset($this->session->data['shipping_method']['code'])) {
			$this->data['code'] = $this->session->data['shipping_method']['code'];
		} else {
			$this->data['code'] = '';
		}

		// Check if the code is already listed in shipping method 
		if($this->data['code']!='')
		{
			$is_found = false;
			foreach($this->data['shipping_methods'] as $s_method)
			{
				if (!$s_method['error']) {
					foreach ($s_method['quote'] as $q) {
						if ($q['code'] == $this->data['code'])
						{

							$is_found = true;
							break;
						}
					}
				}
			}

			if(!$is_found)
			{
					$this->data['code'] = '';
			}
		}

		// end check

		if ($this->data['code']==''){
			if( ! isset($this->session->data['shipping_method']) && !empty($this->data['shipping_methods'])){
				if (!empty($first_shipp_code)){
					$shipping = explode('.', $first_shipp_code);
					$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
                                   
					$this->data['code']=$first_shipp_code;
				}

			}

		}


		if (isset($this->session->data['comment'])) {
			$this->data['comment'] = $this->session->data['comment'];
		} else {
			$this->data['comment'] = '';
		}

	
		// echo $this->session->data['shipping_address_id'];
		
		
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/shipping_method.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/shipping_method.tpl';
		} else {
			$this->template = 'default/template/checkout/shipping_method.tpl';
		}


		$this->response->setOutput($this->render());
  	}

	public function validate() {
		$this->language->load('checkout/checkout');

		$json = array();

		// Validate if shipping is required. If not the customer should not have reached this page.
		if (!$this->cart->hasShipping()) {
			$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');
		}

		// Validate if shipping address has been set.
		$this->load->model('account/address');

		if ($this->customer->isLogged() && isset($this->session->data['shipping_address_id'])) {
			$shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
		} elseif (isset($this->session->data['guest'])) {
			$shipping_address = $this->session->data['guest']['shipping'];
		}

		if (empty($shipping_address)) {
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

			if (!isset($this->request->post['shipping_method'])) {
				$json['error']['warning'] = $this->language->get('error_shipping');
			} else {
				$shipping = explode('.', $this->request->post['shipping_method']);
				if (!isset($shipping[0]) || !isset($shipping[1]) ) {
					$json['error']['warning'] = $this->language->get('error_shipping');
				}
			}

			if (!$json) {
				$shipping = explode('.', $this->request->post['shipping_method']);

				if($this->request->post['shipping_method']!='multiflatrate.multiflatrate_0')
				{
					foreach ($this->session->data['voucher'] as $_voucher ) {

				$coupon_query =  $this->db->query("SELECT product_limit_qty FROM ".DB_PREFIX."coupon WHERE code='".$this->db->escape($_voucher)."' AND has_product_limit=1 ");
						if($coupon_query->num_rows)
						{
							$json['error']['warning'] = 'Our bulk promotion orders can only be picked up from our location.';
							break;	
						}
					}
				}

				$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
				// print_r($this->session->data['shipping_method']);exit;
                                if (isset($this->request->post['comment']))
				$this->session->data['comment'] = strip_tags($this->request->post['comment']);
			}
		}

		$this->response->setOutput(json_encode($json));
	}
}
?>