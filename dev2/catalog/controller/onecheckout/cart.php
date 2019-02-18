<?php 
class ControllerOneCheckoutCart extends Controller {
	public function index() {
		$this->language->load('onecheckout/checkout');
		$this->language->load('checkout/cart');
		$this->load->model('onecheckout/checkout');	
		$version_int = $this->model_onecheckout_checkout->versiontoint();
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
      		if (isset($this->request->post['quantity'])) {
				if (!is_array($this->request->post['quantity'])) {
					if (isset($this->request->post['option'])) {
						$option = $this->request->post['option'];
					} else {
						$option = array();	
					}
			
      				$this->cart->add($this->request->post['product_id'], $this->request->post['quantity'], $option);
				} else {
					foreach ($this->request->post['quantity'] as $key => $value) {
	      				$this->cart->update($key, $value);
					}
				}
      		}

      		if (isset($this->request->post['remove'])) {
	    		foreach ($this->request->post['remove'] as $key) {
          			$this->cart->remove($key);
				}
      		}
			
      		if (isset($this->request->post['voucher']) && $this->request->post['voucher']) {
	    		foreach ($this->request->post['voucher'] as $key) {
          			if (isset($this->session->data['vouchers'][$key])) {
						unset($this->session->data['vouchers'][$key]);
					}
				}
      		}
									
			if (isset($this->request->post['redirect'])) {
				$this->session->data['redirect'] = $this->request->post['redirect'];
			}	
			
			if (isset($this->request->post['quantity']) || isset($this->request->post['remove']) || isset($this->request->post['voucher'])) {
				unset($this->session->data['shipping_methods']);
				unset($this->session->data['shipping_method']);
				unset($this->session->data['payment_methods']);
				unset($this->session->data['payment_method']);	
				unset($this->session->data['reward']);	
				
				$this->redirect($this->url->link('checkout/cart'));
			}
    	}

    	$this->document->setTitle($this->language->get('heading_title'));

      	$this->data['breadcrumbs'] = array();

      	$this->data['breadcrumbs'][] = array(
        	'href'      => $this->url->link('common/home'),
        	'text'      => $this->language->get('text_home'),
        	'separator' => false
      	); 

      	$this->data['breadcrumbs'][] = array(
        	'href'      => $this->url->link('checkout/cart'),
        	'text'      => $this->language->get('heading_title'),
        	'separator' => $this->language->get('text_separator')
      	);
			
    	if ($this->cart->hasProducts() || (isset($this->session->data['vouchers']) && $this->session->data['vouchers'])) {
      		$this->data['heading_title'] = $this->language->get('heading_title');
			
			$this->data['text_select'] = $this->language->get('text_select');
			$this->data['text_none'] = $this->language->get('text_none');
			$this->data['text_weight'] = $this->language->get('text_weight');
		
     		$this->data['column_remove'] = $this->language->get('column_remove');
      		$this->data['column_image'] = $this->language->get('column_image');
      		$this->data['column_name'] = $this->language->get('column_name');
      		$this->data['column_model'] = $this->language->get('column_model');
      		$this->data['column_quantity'] = $this->language->get('column_quantity');
			$this->data['column_price'] = $this->language->get('column_price');
      		$this->data['column_total'] = $this->language->get('column_total');
			
      		$this->data['button_update'] = $this->language->get('button_update');
      		$this->data['button_shopping'] = $this->language->get('button_shopping');
      		$this->data['button_checkout'] = $this->language->get('button_checkout');
			
			if ($this->config->get('config_customer_price') && !$this->customer->isLogged()) {
				$this->data['attention'] = sprintf($this->language->get('text_login'), $this->url->link('account/login'), $this->url->link('account/register'));
			} else {
				$this->data['attention'] = '';
			}
			
			if (!$this->cart->hasStock() && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
      			$this->data['error_warning'] = $this->language->get('error_stock');
			} elseif (isset($this->session->data['error'])) {
				$this->data['error_warning'] = $this->session->data['error'];
			
				unset($this->session->data['error']);			
			} else {
				$this->data['error_warning'] = '';
			}
			
			if (isset($this->session->data['success'])) {
				$this->data['success'] = $this->session->data['success'];
			
				unset($this->session->data['success']);
			} else {
				$this->data['success'] = '';
			}
				
			$this->data['action'] = $this->url->link('checkout/cart');
			
			if($this->config->has('config_weight_class_id')){
				$config_weight_class_id = $this->config->get('config_weight_class_id');
			}else{
				$config_weight_class_id = $this->config->get('config_weight_class');
			}
			
			if ($this->config->get('config_cart_weight')) {
				$this->data['weight'] = $this->weight->format($this->cart->getWeight(), $config_weight_class_id, $this->language->get('decimal_point'), $this->language->get('thousand_point'));
			} else {
				$this->data['weight'] = false;
			}
						 
			$this->load->model('tool/image');
			
      		$this->data['products'] = array();
			
			//version			
			if($version_int <= 1513 && $version_int >= 1500){
				$this->data['imgabsmiddle']  = ' align="absmiddle"';
			} elseif($version_int > 1513) {
				$this->data['imgabsmiddle']  = '';
			}

			$products = $this->cart->getProducts();

      		foreach ($products as $product) {
				$product_total = 0;
					
				foreach ($products as $product_2) {
					if ($product_2['product_id'] == $product['product_id']) {
						$product_total += $product_2['quantity'];
					}
				}			
				
				if ($product['minimum'] > $product_total) {
					$this->data['error_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
				}				
					
				if ($product['image']) {
					$image = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
				} else {
					$image = '';
				}

				$option_data = array();

        		foreach ($product['option'] as $option) {
					if ($option['type'] != 'file') {
					  
					  if(function_exists('utf8_strlen') && function_exists('utf8_substr')){
						$option_data[] = array(
							'name'  => $option['name'],
							'value' => (utf8_strlen($option['option_value']) > 20 ? utf8_substr($option['option_value'], 0, 20) . '..' : $option['option_value'])
						);
					  } else {
					  	$option_data[] = array(
							'name'  => $option['name'],
							'value' => (strlen($option['option_value']) > 20 ? substr($option['option_value'], 0, 20) . '..' : $option['option_value'])
						);
					  }
						
					} else {
						$this->load->library('encryption');
						
						$encryption = new Encryption($this->config->get('config_encryption'));
						
						if(function_exists('utf8_strlen') && function_exists('utf8_strrpos') && function_exists('utf8_substr')){
						  $file = utf8_substr($encryption->decrypt($option['option_value']), 0, utf8_strrpos($encryption->decrypt($option['option_value']), '.'));						
						  $option_data[] = array(
							'name'  => $option['name'],
							'value' => (utf8_strlen($file) > 20 ? utf8_substr($file, 0, 20) . '..' : $file)
						  );
						} else {
						  $file = substr($encryption->decrypt($option['option_value']), 0, strrpos($encryption->decrypt($option['option_value']), '.'));						
						  $option_data[] = array(
							'name'  => $option['name'],
							'value' => (strlen($file) > 20 ? substr($file, 0, 20) . '..' : $file)
						  );
						}	
										
					}
        		}
				
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
				
				//version
				if($version_int < 1513 && $version_int >= 1500){
					$points  = ($product['points'] ? sprintf($this->language->get('text_points'), $product['points']) : '');
				} elseif($version_int == 1513) {
					$points  = ($product['reward'] ? sprintf($this->language->get('text_reward'), $product['reward']) : '');
				} elseif($version_int > 1513) {
					$points  = ($product['reward'] ? sprintf($this->language->get('text_points'), $product['reward']) : '');
				}
					
        		$this->data['products'][] = array(
          			'key'      => $product['key'],
          			'thumb'    => $image,
					'name'     => $product['name'],
          			'model'    => $product['model'],
          			'option'   => $option_data,
          			'quantity' => $product['quantity'],
          			'stock'    => $product['stock'],
					'points'   => $points,
					'price'    => $price,
					'total'    => $total,
					'href'     => $this->url->link('product/product', 'product_id=' . $product['product_id'])
        		);
      		}
			
			// Gift Voucher
			$this->data['vouchers'] = array();
			
			if (isset($this->session->data['vouchers']) && $this->session->data['vouchers']) {
				foreach ($this->session->data['vouchers'] as $key => $voucher) {
					$this->data['vouchers'][] = array(
						'key'         => $key,
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount'])
					);
				}
			} 
						
			$total_data = array();
			$total = 0;
			$taxes = $this->cart->getTaxes();
			
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {						 
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
			}
			
			$this->data['totals'] = $total_data;
				
			$this->data['shipping_status'] = !isset($this->session->data['address_id']) && $this->cart->hasShipping() && $this->config->get('shipping_estimator') && $this->config->get('shipping_status');
			
			if (!isset($this->session->data['address_id']) && $this->cart->hasShipping() && $this->config->get('shipping_estimator') && $this->config->get('shipping_status')) {
			
				$this->data['shipping_heading_title'] = $this->language->get('heading_title_shipping');
			
				$this->data['text_shipping'] = $this->language->get('text_shipping');
				$this->data['text_select'] = $this->language->get('text_select');
			
				$this->data['entry_country'] = $this->language->get('entry_country_shipping');
				$this->data['entry_zone'] = $this->language->get('entry_zone_shipping');
				$this->data['entry_postcode'] = $this->language->get('entry_postcode_shipping');
			
				$this->data['button_quote'] = $this->language->get('button_quote');
				$this->data['button_shipping'] = $this->language->get('button_shipping');
			
				if (isset($this->session->data['shipping_country_id'])) {
					$this->data['country_id'] = $this->session->data['shipping_country_id'];			  	
				} elseif (isset($this->session->data['guest']['shipping']['country_id'])) {
					$this->data['country_id'] = $this->session->data['guest']['shipping']['country_id'];			  	
				} else {
					$this->data['country_id'] = $this->config->get('config_country_id');
				}
				
				$this->load->model('localisation/country');
			
				$this->data['countries'] = $this->model_localisation_country->getCountries();
						
				if (isset($this->session->data['shipping_zone_id'])) {
					$this->data['zone_id'] = $this->session->data['shipping_zone_id'];			
				} elseif (isset($this->session->data['guest']['shipping']['zone_id'])) {
					$this->data['zone_id'] = $this->session->data['guest']['shipping']['zone_id'];			
				} else {
					$this->data['zone_id'] = '';
				}
			
				if (isset($this->session->data['shipping_postcode'])) {
					$this->data['postcode'] = $this->session->data['shipping_postcode'];					
				} elseif (isset($this->session->data['guest']['shipping']['postcode'])) {
					$this->data['postcode'] = $this->session->data['guest']['shipping']['postcode'];					
				} else {
					$this->data['postcode'] = '';
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
			}
			//cart module
			$this->data['cartmodule'] = $this->getChild('onecheckout/cartmodule');
			
			if (isset($this->session->data['redirect'])) {
      			$this->data['continue'] = $this->session->data['redirect'];
				
				unset($this->session->data['redirect']);
			} else {
				$this->data['continue'] = $this->url->link('common/home');
			}
						
			$this->data['checkout'] = $this->url->link('checkout/checkout', '', 'SSL');

			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/onecheckout/cart.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/onecheckout/cart.tpl';
			} else {
				$this->template = 'default/template/onecheckout/cart.tpl';
			}
			
			$this->children = array(
				'common/column_left',
				'common/column_right',
				'common/content_top',
				'common/content_bottom',
				'common/footer',
				'common/header'	
			);
						
			$this->response->setOutput($this->render());					
    	} else {
      		$this->data['heading_title'] = $this->language->get('heading_title');

      		$this->data['text_error'] = $this->language->get('text_empty');

      		$this->data['button_continue'] = $this->language->get('button_continue');

      		$this->data['continue'] = $this->url->link('common/home');

			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/error/not_found.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/error/not_found.tpl';
			} else {
				$this->template = 'default/template/error/not_found.tpl';
			}
			
			$this->children = array(
				'common/column_left',
				'common/column_right',
				'common/content_top',
				'common/content_bottom',
				'common/footer',
				'common/header'	
			);
					
			$this->response->setOutput($this->render());			
    	}
  	}
	
	public function remove() {
		$this->language->load('checkout/cart');
		$this->load->model('onecheckout/checkout');	
		if (isset($this->request->post['remove'])) {
			foreach ($this->request->post['remove'] as $key) {
          			$this->cart->remove($key);
			}
		}
		
		if (isset($this->request->post['voucher']) && $this->request->post['voucher']) {
	    	foreach ($this->request->post['voucher'] as $key) {
          		if (isset($this->session->data['vouchers'][$key])) {
					unset($this->session->data['vouchers'][$key]);
				}
			}
      	}
		
		if (isset($this->request->post['quantity']) || isset($this->request->post['remove']) || isset($this->request->post['voucher'])) {
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['payment_method']);	
			unset($this->session->data['reward']);	
		}			

		$total_data = array();
		$total = 0;
		$taxes = $this->cart->getTaxes();	
		
    	if ($this->cart->hasProducts() || (isset($this->session->data['vouchers']) && $this->session->data['vouchers'])) {
			
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {						 
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
			}
				
			foreach($total_data as $key => $data) {
				unset($total_data[$key]['code']);
				unset($total_data[$key]['value']);
				unset($total_data[$key]['sort_order']);
			}	
		} else {
			$json['button_continue'] = $this->language->get('button_continue');			
			$json['continue'] = $this->url->link('common/home');		
			$json['cart_empty'] = '<div class="content" style="display:none;">' . $this->language->get('text_empty') . '</div>';
			
		}
		
		$this->language->load('checkout/cart');
		
		if (!$this->cart->hasStock() && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
			$json['error_warning'] = $this->language->get('error_stock');
		} else {
			$json['error_warning'] = '';
		}
		
		$json['totals'] = $total_data;		
		$json['cart_total'] = ($this->cart->hasProducts() || (isset($this->session->data['vouchers']) && $this->session->data['vouchers'])) ? TRUE : FALSE;
		
		if($this->config->has('config_weight_class_id')){
				$config_weight_class_id = $this->config->get('config_weight_class_id');
			}else{
				$config_weight_class_id = $this->config->get('config_weight_class');
			}
		
		if($this->config->get('config_cart_weight') && $this->cart->countProducts()) {
			$json['cart_heading'] = $this->language->get('heading_title') . '&nbsp;&nbsp;(' . $this->weight->format($this->cart->getWeight(), $config_weight_class_id, $this->language->get('decimal_point'), $this->language->get('thousand_point')) . ')';
		} else {
			$json['cart_heading'] = $this->language->get('heading_title');
		}
		
		$this->language->load('common/header');
			
		$json['header_cart_total'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total));
				
		$this->response->setOutput($this->model_onecheckout_checkout->jsonencode($json));
	}
	
	public function quantity() {
		$this->language->load('checkout/cart');
		$this->load->model('onecheckout/checkout');	
		if (isset($this->request->post['quantity'])) {
			foreach($this->request->post['quantity'] as $key => $value) {
				$this->cart->update($key, $value);
			}
		}
		
		if (isset($this->request->post['quantity'])) {
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['payment_method']);	
			unset($this->session->data['reward']);	
		}

		foreach ($this->cart->getProducts() as $result) {
			foreach($this->request->post['quantity'] as $key => $value) {
				if($result['key'] == $key) {
					if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
					} else {
						$price = false;
					}

					if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
						$total = $this->currency->format($this->tax->calculate($result['total'], $result['tax_class_id'], $this->config->get('config_tax')));
					} else {
						$total = false;
					}
					
					$json['quantity'] = $result['quantity'];
					$json['stock']    = $result['stock'];
					$version_int = $this->model_onecheckout_checkout->versiontoint();
					//version
					if($version_int < 1513 && $version_int >= 1500){
						$json['points']  = ($result['points'] ? sprintf($this->language->get('text_points'), $result['points']) : '');
					} elseif($version_int == 1513) {
						$json['points']  = ($result['reward'] ? sprintf($this->language->get('text_reward'), $result['reward']) : '');
					} elseif($version_int > 1513) {
						$json['points']  = ($result['reward'] ? sprintf($this->language->get('text_points'), $result['reward']) : '');
					}		
					$json['price']    = $price;
					$json['total']    = $total;
				}
			}
		}

		$total_data = array();
		$total = 0;
		$taxes = $this->cart->getTaxes();
		if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {						 
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
		}
			
		foreach($total_data as $key => $data) {
			unset($total_data[$key]['code']);
			unset($total_data[$key]['value']);
			unset($total_data[$key]['sort_order']);
		}

		$json['totals'] = $total_data;
		
		if (!$this->cart->hasStock() && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
			$json['error_warning'] = $this->language->get('error_stock');
		} else {
			$json['error_warning'] = '';
		}
		
		if($this->config->has('config_weight_class_id')){
				$config_weight_class_id = $this->config->get('config_weight_class_id');
			}else{
				$config_weight_class_id = $this->config->get('config_weight_class');
			}
				
		if($this->config->get('config_cart_weight') && $this->cart->countProducts()) {
			$json['cart_heading'] = $this->language->get('heading_title') . '&nbsp;&nbsp;(' . $this->weight->format($this->cart->getWeight(), $config_weight_class_id, $this->language->get('decimal_point'), $this->language->get('thousand_point')) . ')';
		} else {
			$json['cart_heading'] = $this->language->get('heading_title');
		}
		
		$this->language->load('common/header');
		
		$json['header_cart_total'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total));
		
		$this->response->setOutput($this->model_onecheckout_checkout->jsonencode($json));	
	}
}
?>