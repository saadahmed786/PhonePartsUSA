<?php 
class ControllerCheckoutSimplifiedCheckoutConfirm extends Controller { 
	private $error = array();

	public function index() {
		$this->load->model('account/address');
		$this->language->load('checkout/simplified_checkout');
		
		// Delete the success message that add coupon adds.
		unset($this->session->data['success']);
		
		//if (!$this->cart->hasProducts() || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
	  		$this->redirect($this->url->link('checkout/cart', '', 'SSL'));
    	}		
		
    	if ($this->customer->isLogged()) {
			$payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);		
		} elseif (isset($this->session->data['guest'])) {
			$payment_address = $this->session->data['guest']['payment'];
		}

		if (!isset($payment_address)) {
			$this->session->data['error']['warning'] = 'no_payment_address';
			$this->redirect($this->url->link('checkout/simplified_checkout', '', 'SSL'));
		}
		
		if (!isset($this->session->data['payment_method'])) {
			$this->session->data['error']['warning'] = 'no_payment_method';
	  		$this->redirect($this->url->link('checkout/simplified_checkout', '', 'SSL'));
    	}

    	if ($this->cart->hasShipping()) {
			$this->load->model('account/address');
			
			if ($this->customer->isLogged()) {
				$shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);		
			} elseif (isset($this->session->data['guest'])) {
				$shipping_address = $this->session->data['guest']['shipping'];
			}				

			if (!isset($shipping_address)) {
				$this->session->data['error']['warning'] = 'no_shippping_address';
				$this->redirect($this->url->link('checkout/simplified_checkout', '', 'SSL'));
			}
			
			if (!isset($this->session->data['shipping_method'])) {
				$this->session->data['error']['warning'] = 'no_shippping_method';
	  			$this->redirect($this->url->link('checkout/simplified_checkout', '', 'SSL'));
    		}
		} else {
			unset($this->session->data['guest']['shipping']);
			unset($this->session->data['shipping_address_id']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);			
		}
		
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
		
		$sort_order = array(); 
	  
		foreach ($total_data as $key => $value) {
			$sort_order[$key] = $value['sort_order'];
		}

		array_multisort($sort_order, SORT_ASC, $total_data);

		$this->language->load('checkout/checkout');
		
		$data = array();
		
		$data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
		$data['store_id'] = $this->config->get('config_store_id');
		$data['store_name'] = $this->config->get('config_name');
		
		if ($data['store_id']) {
			$data['store_url'] = $this->config->get('config_url');		
		} else {
			$data['store_url'] = HTTP_SERVER;	
		}
		
		if ($this->customer->isLogged()) {
			$data['customer_id'] = $this->customer->getId();
			$data['customer_group_id'] = $this->customer->getCustomerGroupId();
			$data['firstname'] = $this->customer->getFirstName();
			$data['lastname'] = $this->customer->getLastName();
			$data['email'] = $this->customer->getEmail();
			$data['telephone'] = $this->customer->getTelephone();
			$data['fax'] = $this->customer->getFax();
		
			$this->load->model('account/address');
			
			$payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
		} elseif (isset($this->session->data['guest'])) {
			$data['customer_id'] = 0;
			$data['customer_group_id'] = $this->config->get('config_customer_group_id');
			$data['firstname'] = $this->session->data['guest']['firstname'];
			$data['lastname'] = $this->session->data['guest']['lastname'];
			$data['email'] = $this->session->data['guest']['email'];
			$data['telephone'] = $this->session->data['guest']['telephone'];
			$data['fax'] = $this->session->data['guest']['fax'];
			
			$payment_address = $this->session->data['guest']['payment'];
		}
		
		$this->data['payment_address'] = $payment_address;
		
		$data['payment_firstname'] = $payment_address['firstname'];
		$data['payment_lastname'] = $payment_address['lastname'];	
		$data['payment_company'] = $payment_address['company'];	
		$data['payment_address_1'] = $payment_address['address_1'];
		$data['payment_address_2'] = $payment_address['address_2'];
		$data['payment_city'] = $payment_address['city'];
		$data['payment_postcode'] = $payment_address['postcode'];
		$data['payment_zone'] = $payment_address['zone'];
		$data['payment_zone_id'] = $payment_address['zone_id'];
		$data['payment_country'] = $payment_address['country'];
		$data['payment_country_id'] = $payment_address['country_id'];
		$data['payment_address_format'] = $payment_address['address_format'];
	
		if (isset($this->session->data['payment_method']['title'])) {
			$data['payment_method'] = $this->session->data['payment_method']['title'];
		} else {
			$data['payment_method'] = '';
		}
		
		if (isset($this->session->data['payment_method']['title'])) {
			$this->data['payment_method'] = $this->session->data['payment_method']['title'];
		} else {
			$this->data['payment_method'] = '';
		}
		
		if ($this->cart->hasShipping()) {
			if ($this->customer->isLogged()) {
				$this->load->model('account/address');
				
				$shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);	
			} elseif (isset($this->session->data['guest'])) {
				$shipping_address = $this->session->data['guest']['shipping'];
			}			
			$this->data['shipping_address'] = $shipping_address;
			
			$data['shipping_firstname'] = $shipping_address['firstname'];
			$data['shipping_lastname'] = $shipping_address['lastname'];	
			$data['shipping_company'] = $shipping_address['company'];	
			$data['shipping_address_1'] = $shipping_address['address_1'];
			$data['shipping_address_2'] = $shipping_address['address_2'];
			$data['shipping_city'] = $shipping_address['city'];
			$data['shipping_postcode'] = $shipping_address['postcode'];
			$data['shipping_zone'] = $shipping_address['zone'];
			$data['shipping_zone_id'] = $shipping_address['zone_id'];
			$data['shipping_country'] = $shipping_address['country'];
			$data['shipping_country_id'] = $shipping_address['country_id'];
			$data['shipping_address_format'] = $shipping_address['address_format'];
		
			if (isset($this->session->data['shipping_method']['title'])) {
				$data['shipping_method'] = $this->session->data['shipping_method']['title'];
			} else {
				$data['shipping_method'] = '';
			}
		} else {
			$data['shipping_firstname'] = '';
			$data['shipping_lastname'] = '';	
			$data['shipping_company'] = '';	
			$data['shipping_address_1'] = '';
			$data['shipping_address_2'] = '';
			$data['shipping_city'] = '';
			$data['shipping_postcode'] = '';
			$data['shipping_zone'] = '';
			$data['shipping_zone_id'] = '';
			$data['shipping_country'] = '';
			$data['shipping_country_id'] = '';
			$data['shipping_address_format'] = '';
			$data['shipping_method'] = '';
			
			$this->data['shipping_address'] = '';
		}
		
		// Opencart < 1.5.1.3
		if (method_exists($this->tax, 'setZone')) {
			if ($this->cart->hasShipping()) {
				$this->tax->setZone($shipping_address['country_id'], $shipping_address['zone_id']);
			} else {
				$this->tax->setZone($payment_address['country_id'], $payment_address['zone_id']);
			}			
		}
		if (isset($this->session->data['shipping_method']['title'])) {
			$this->data['shipping_method'] = $this->session->data['shipping_method']['title'];
		} else {
			$this->data['shipping_method'] = '';
		}	
		
		
		// Products
		
		$product_data = array();
	
		foreach ($this->cart->getProducts() as $product) {
			$option_data = array();

			foreach ($product['option'] as $option) {
				if ($option['type'] != 'file') {	
					$option_data[] = array(
						'product_option_id'       => $option['product_option_id'],
						'product_option_value_id' => $option['product_option_value_id'],
						'product_option_id'       => $option['product_option_id'],
						'product_option_value_id' => $option['product_option_value_id'],
						'option_id'               => $option['option_id'],
						'option_value_id'         => $option['option_value_id'],								   
						'name'                    => $option['name'],
						'value'                   => $option['option_value'],
						'type'                    => $option['type']
					);					
				} else {
					$this->load->library('encryption');
					
					$encryption = new Encryption($this->config->get('config_encryption'));
					
					$option_data[] = array(
						'product_option_id'       => $option['product_option_id'],
						'product_option_value_id' => $option['product_option_value_id'],
						'product_option_id'       => $option['product_option_id'],
						'product_option_value_id' => $option['product_option_value_id'],
						'option_id'               => $option['option_id'],
						'option_value_id'         => $option['option_value_id'],								   
						'name'                    => $option['name'],
						'value'                   => $encryption->decrypt($option['option_value']),
						'type'                    => $option['type']
					);								
				}
			}
 
 			// Opencart < 1.5.1.3
 			if (method_exists($this->tax, 'getRate')) {
 				$tax = $this->tax->getRate($product['tax_class_id']);
 			} else {
 				$tax = $this->tax->getTax($product['total'], $product['tax_class_id']);
 			}
 			
 
			$product_data[] = array(
				'product_id' => $product['product_id'],
				'name'       => $product['name'],
				'model'      => $product['model'],
				'option'     => $option_data,
				'download'   => $product['download'],
				'quantity'   => $product['quantity'],
				'subtract'   => $product['subtract'],
				'price'      => $product['price'],
				'total'      => $product['total'],
				'tax'        => $tax
			); 
		}
		
		// Gift Voucher
		if (isset($this->session->data['vouchers']) && $this->session->data['vouchers']) {
			foreach ($this->session->data['vouchers'] as $voucher) {
				$product_data[] = array(
					'product_id' => 0,
					'name'       => $voucher['description'],
					'model'      => '',
					'option'     => array(),
					'download'   => array(),
					'quantity'   => 1,
					'subtract'   => false,
					'price'      => $voucher['amount'],
					'total'      => $voucher['amount'],
					'tax'        => 0
				);
			}
		} 
					
		$data['products'] = $product_data;
		$data['totals'] = $total_data;
		$data['comment'] = $this->session->data['comment'];
		$data['total'] = $total;
		$data['reward'] = $this->cart->getTotalRewardPoints();
		
		$this->data['comment'] = $data['comment'];
		$this->data['text_comment'] = $this->language->get('text_comment');
		
		if (isset($this->request->cookie['tracking'])) {
			$this->load->model('affiliate/affiliate');
			
			$affiliate_info = $this->model_affiliate_affiliate->getAffiliateByCode($this->request->cookie['tracking']);
			
			if ($affiliate_info) {
				$data['affiliate_id'] = $affiliate_info['affiliate_id']; 
				$data['commission'] = ($total / 100) * $affiliate_info['commission']; 
			} else {
				$data['affiliate_id'] = 0;
				$data['commission'] = 0;
			}
		} else {
			$data['affiliate_id'] = 0;
			$data['commission'] = 0;
		}
		
		$data['language_id'] = $this->config->get('config_language_id');
		$data['currency_id'] = $this->currency->getId();
		$data['currency_code'] = $this->currency->getCode();
		$data['currency_value'] = $this->currency->getValue($this->currency->getCode());
		$data['ip'] = $this->request->server['REMOTE_ADDR'];
		
		$this->load->model('checkout/order');
		
		$this->session->data['order_id'] = $this->model_checkout_order->create($data);
		
		// Gift Voucher
		if (isset($this->session->data['vouchers']) && is_array($this->session->data['vouchers'])) {
			$this->load->model('checkout/voucher');

			foreach ($this->session->data['vouchers'] as $voucher) {
				$this->model_checkout_voucher->addVoucher($this->session->data['order_id'], $voucher);
			}
		}
		$this->data['column_name'] = $this->language->get('column_name');
		$this->data['column_model'] = $this->language->get('column_model');
		$this->data['column_quantity'] = $this->language->get('column_quantity');
		$this->data['column_price'] = $this->language->get('column_price');
		$this->data['column_total'] = $this->language->get('column_total');
		
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
			
			// Opencart < 1.5.1.3
			/*
 			if (method_exists($this->tax, 'getRate')) {
 				$tax = $this->tax->getRate($product['tax_class_id']);
 			} else {
 				$tax = $this->tax->getTax($product['total'], $product['tax_class_id']);
 			}
			*/
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
				'name'       => $product['name'],
				'image'		 => $image,
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
					
		// Breadcrumb
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
					
		$this->data['totals'] = $total_data;

		$this->data['payment'] = $this->getChild('payment/' . $this->session->data['payment_method']['code']);

	
		$this->data['text_shipping_address'] = $this->language->get('text_shipping_address');
		$this->data['text_shipping_method_short'] = $this->language->get('text_shipping_method_short');
		$this->data['text_payment_address'] = $this->language->get('text_payment_address');
		$this->data['text_payment_method_short'] = $this->language->get('text_payment_method_short');
	
		$this->data['heading_title'] = $this->language->get('heading_title');

		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/simplified_checkout_confirm.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/simplified_checkout_confirm.tpl';
		} else {
			$this->template = 'default/template/checkout/simplified_checkout_confirm.tpl';
		}
		
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
	
	private function validateCoupon() {

  		$this->load->model('checkout/coupon');

		$this->language->load('checkout/confirm');

		$coupon = $this->model_checkout_coupon->getCoupon($this->request->post['coupon']);

		if (!$coupon) {
			$this->error['warning'] = $this->language->get('error_coupon');
		}

  		if (!$this->error) {
	  		return TRUE;
		} else {
	  		return FALSE;
		}
  	}
}
?>