<?php 
/*
@author	Dmitriy Kubarev
@link	http://www.simpleopencart.com
@link	http://www.opencart.com/index.php?route=extension/extension/info&extension_id=4811
*/  

class ControllerCheckoutSimpleCheckoutCart extends Controller { 
    private $error = array();
    
    public function index() {
    
        $this->language->load('checkout/simplecheckout');
        
        $json = array();

        if (!isset($this->session->data['vouchers'])) {
			$this->session->data['vouchers'] = array();
		}
		
        // Update
		if (!empty($this->request->post['quantity'])) {
			foreach ($this->request->post['quantity'] as $key => $value) {
				$this->cart->update($key, $value);
			}		
            $json['reload_simplecheckout'] = true;
		}
        
		// Remove
		if (!empty($this->request->post['remove'])) {
			$this->cart->remove($this->request->post['remove']);
			unset($this->session->data['vouchers'][$this->request->post['remove']]);
            $json['reload_simplecheckout'] = true;
		}
        
		// Coupon    
		if (isset($this->request->post['coupon']) && $this->validateCoupon()) { 
			$this->session->data['coupon'] = $this->request->post['coupon'];
            $json['reload_simplecheckout'] = true;
		}
		
		// Voucher
		if (isset($this->request->post['voucher']) && $this->validateVoucher()) { 
			$this->session->data['voucher'] = $this->request->post['voucher'];
            $json['reload_simplecheckout'] = true;
		}
        
        if (!empty($this->request->post['quantity']) || !empty($this->request->post['remove']) || !empty($this->request->post['voucher'])) {
            unset($this->session->data['shipping_methods']);
            //unset($this->session->data['shipping_method']);
            unset($this->session->data['payment_methods']);
            //unset($this->session->data['payment_method']);    
            unset($this->session->data['reward']);
        }
        
		// Reward
		if (isset($this->request->post['reward']) && $this->validateReward()) { 
			$this->session->data['reward'] = $this->request->post['reward'];
			$json['reload_simplecheckout'] = true;
		}

        if (!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) {
        	$json['redirect'] = $this->url->link('checkout/simplecheckout');				
		}
            
        if (!$json) {
            $this->language->load('checkout/simplecheckout');

            if ($this->config->get('config_customer_price') && !$this->customer->isLogged()) {
    			$this->data['attention'] = sprintf($this->language->get('text_login'), $this->url->link('account/login'), $this->url->link('account/simpleregister'));
                $json['block_order'] = true;
            } else {
    			$this->data['attention'] = '';
    		}
    		
            $this->data['error_warning'] = '';
            
            if (isset($this->error['warning'])) {
				$this->data['error_warning'] = $this->error['warning'];
            } elseif (isset($this->session->data['error'])) {
    			$this->data['error_warning'] = $this->session->data['error'];
    			unset($this->session->data['error']);			
    		}
            
            if (!$this->cart->hasStock()) {
                if ($this->config->get('config_stock_warning')) {
                    $this->data['error_warning'] = $this->language->get('error_stock');
                }
                if (!$this->config->get('config_stock_checkout')) {
                    $this->data['error_warning'] = $this->language->get('error_stock');
                    $json['block_order'] = true;
                }
            }
            
            $this->data['action'] = $this->url->link('checkout/simplecheckout_cart');
            
            $this->load->model('tool/image');
            
            $this->load->library('encryption');
            
            $this->data['column_image'] = $this->language->get('column_image');
            $this->data['column_name'] = $this->language->get('column_name');
            $this->data['column_model'] = $this->language->get('column_model');
            $this->data['column_quantity'] = $this->language->get('column_quantity');
            $this->data['column_price'] = $this->language->get('column_price');
            $this->data['column_total'] = $this->language->get('column_total');
            
            $this->data['button_update'] = $this->language->get('button_update');
            
            $this->data['products'] = array();
            
            $this->data['config_stock_warning'] = $this->config->get('config_stock_warning');
            $this->data['config_stock_checkout'] = $this->config->get('config_stock_checkout');
            
            $this->data['products'] = array();
			
			$products = $this->cart->getProducts();
            
            $points_total = 0;
            
            foreach ($products as $product) {
                
                $product_total = 0;
    				
    			foreach ($products as $product_2) {
    				if ($product_2['product_id'] == $product['product_id']) {
    					$product_total += $product_2['quantity'];
    				}
    			}		
    			
                if ($product['minimum'] > $product_total) {
                    $this->data['error_warning'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
				    $json['block_order'] = true;
                }
                
                $option_data = array();
    
                foreach ($product['option'] as $option) {
					if ($option['type'] != 'file') {
						$value = $option['option_value'];	
					} else {
						$encryption = new Encryption($this->config->get('config_encryption'));
                        $option_value = $encryption->decrypt($option['option_value']);
                        $filename = substr($option_value, 0, strrpos($option_value, '.'));
                        $value = $filename;
					}
					
					$option_data[] = array(
						'name'  => $option['name'],
						'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
					);
        		}
                
                if ($product['image']) {
                    $image = $this->model_tool_image->resize($product['image'], 40, 40);
                } else {
                    $image = '';
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
     
      
                $this->data['products'][] = array(
                    'key'      => $product['key'],
                    'thumb'    => $image,
                    'name'     => $product['name'],
                    'model'    => $product['model'],
                    'option'   => $option_data,
                    'quantity' => $product['quantity'],
                    'stock'    => $product['stock'],
                    'reward'   => ($product['reward'] ? sprintf($this->language->get('text_reward'), $product['reward']) : ''),
                    'price'    => $price,
                    'total'    => $total,
                    'href'     => $this->url->link('product/product', 'product_id=' . $product['product_id'])
                );
                
                if ($product['points']) {
    				$points_total += $product['points'];
    			}
            } 
            
            // Gift Voucher
            $this->data['vouchers'] = array();
            
            if (!empty($this->session->data['vouchers'])) {
                foreach ($this->session->data['vouchers'] as $key => $voucher) {
                    $this->data['vouchers'][] = array(
                        'description' => $voucher['description'],
                        'amount'      => $this->currency->format($voucher['amount'])
                    );
                }
            }  
            
            $total_data = array();
            $total = 0;
            $taxes = $this->cart->getTaxes();
            
            $this->data['modules'] = array();
    		
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
                        
                        $this->data['modules'][$result['code']] = true;
    				}
    			}
    			
    			$sort_order = array(); 
    		  
    			foreach ($total_data as $key => $value) {
    				$sort_order[$key] = $value['sort_order'];
    			}
    
    			array_multisort($sort_order, SORT_ASC, $total_data);
    		}
    		
    		$this->data['totals'] = $total_data;
            
            $this->data['entry_coupon'] = $this->language->get('entry_coupon');
			$this->data['entry_voucher'] = $this->language->get('entry_voucher');
            
            $points = $this->customer->getRewardPoints();
            $points_to_use = $points > $points_total ? $points_total : $points;
            $this->data['points'] = $points_to_use;
            
			$this->data['entry_reward'] = sprintf($this->language->get('entry_reward'), $points_to_use);
            
            $this->data['reward'] = isset($this->session->data['reward']) ? $this->session->data['reward'] : '';
            $this->data['voucher'] = isset($this->session->data['voucher']) ? $this->session->data['voucher'] : '';
            
            $this->data['coupon'] = isset($this->session->data['coupon']) ? $this->session->data['coupon'] : '';
                        
            if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/simplecheckout_cart.tpl')) {
                $this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/simplecheckout_cart.tpl';
                $this->data['template'] = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template'));
            } else {
                $this->template = 'default/template/checkout/simplecheckout_cart.tpl';
                $this->data['template'] = 'default';
            }
            
            $current_theme = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template'));
            
            if ($current_theme == 'shoppica' || $current_theme == 'shoppica2') {
                $json['total'] = $this->currency->format($total);
            } else {
                $json['total'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total));
            }
            
            $json['output'] = $this->render();
        }
        
        $this->response->setOutput(json_encode($json));        
    }
    
    private function validateCoupon() {
		$this->load->model('checkout/coupon');
				
        if (!empty($this->request->post['coupon'])) {
    		$coupon_info = $this->model_checkout_coupon->getCoupon($this->request->post['coupon']);			
    		
    		if (!$coupon_info) {			
    			$this->error['warning'] = $this->language->get('error_coupon');
                $this->session->data['error'] = $this->error['warning'];
    		}
        }
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}		
	}
	
	private function validateVoucher() {
		$this->load->model('checkout/voucher');
			
        if (!empty($this->request->post['voucher'])) {
    		$voucher_info = $this->model_checkout_voucher->getVoucher($this->request->post['voucher']);			
    		
    		if (!$voucher_info) {			
    			$this->error['warning'] = $this->language->get('error_voucher');
                $this->session->data['error'] = $this->error['warning'];
    		}
		}
        
		if (!$this->error) {
			return true;
		} else {
			return false;
		}		
	}
	
	private function validateReward() {
        if (!empty($this->request->post['reward'])) {
    		$points = $this->customer->getRewardPoints();
    		
    		$points_total = 0;
    		
    		foreach ($this->cart->getProducts() as $product) {
    			if ($product['points']) {
    				$points_total += $product['points'];
    			}
    		}	
    		
    		if ($this->request->post['reward'] > $points) {
    			$this->error['warning'] = sprintf($this->language->get('error_points'), $this->request->post['reward']);
                $this->session->data['error'] = $this->error['warning'];
    		}
    		
    		if ($this->request->post['reward'] > $points_total) {
    			$this->error['warning'] = sprintf($this->language->get('error_maximum'), $points_total);
                $this->session->data['error'] = $this->error['warning'];
    		}
        }
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}		
	}
}
?>