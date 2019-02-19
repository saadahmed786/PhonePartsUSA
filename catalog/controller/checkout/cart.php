<?php 
class ControllerCheckoutCart extends Controller {
	private $error = array();
	
	public function index() {
		
		if ($this->request->post['quickOrder']) {
			$products = $this->request->post['sku'];
			$quantity = $this->request->post['qty'];
			$this->load->model('catalog/product');
			 $destination = DIR_IMAGE . time() . uniqid() . '.csv';
			move_uploaded_file($_FILES['quickordercsv']['tmp_name'], $destination);
			if (($handle = fopen($destination, "r")) !== FALSE) {
				$q_r = 0;
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					if ($q_r) {
						$products[] = strtoupper($data[0]);
						$quantity[] = $data[1];
					}
					$q_r++;
				}
				fclose($handle);
			}
			unlink($destination);
			$_message = '';
			foreach ($products as $key => $product) {
				$product = strtoupper($product);
				if ($product) {
					$q_product_id = $this->model_catalog_product->getProductIDbySku($product);
					$q_product_qty = $quantity[$key];
					if ($q_product_id) {
						$var = $this->add2($q_product_id, $q_product_qty);
						// var_dump($var);exit;
						$var = json_decode($var,true);
						if(!$var['success'])
						{
							$_message.= '('.$product.')  Error parsing CSV upload. Please download our sample CSV<br>';
						}

					}
					else
					{
						$_message.= $product.' not found in system, please verify the proper SKU.<br>';
					}
				}
			}
			if($_message)
			{
				$this->error['warning'] = $_message;
			}
			else
			{
				$this->session->data['success'] = 'Hooray! Product(s) added into your cart!';
			}
		}

		$this->language->load('checkout/cart');
		
		if (!isset($this->session->data['vouchers'])) {
			$this->session->data['vouchers'] = array();
		}
		
		// Update
		if (!empty($this->request->post['quantity'])) {
			foreach ($this->request->post['quantity'] as $key => $value) {
				$this->cart->update($key, $value);
			}
			
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']); 
			unset($this->session->data['reward']);
			
			$this->redirect($this->url->link('checkout/cart'));  			
		}
		
		// Remove
		if (isset($this->request->get['remove'])) {
			$this->cart->remove($this->request->get['remove']);
			
			unset($this->session->data['vouchers'][$this->request->get['remove']]);
			unset($this->session->data['voucher'][$this->request->get['remove']]);
			
			$this->session->data['success'] = $this->language->get('text_remove');
			if (!isset($this->request->get['sign'])) {
				unset($this->session->data['shipping_method']);
				unset($this->session->data['shipping_methods']);
				unset($this->session->data['payment_method']);
				unset($this->session->data['payment_methods']); 
				unset($this->session->data['reward']);  
			}
			if(isset($this->request->get['sign']) && $this->request->get['sign']==1)
			{
				echo 1;exit;
			}
			$this->redirect($this->url->link('checkout/cart'));
		}
		
		// Coupon    
		
		if (isset($this->request->post['coupon']) ) { 
			
			if($this->validateCoupon())
			{
				$this->session->data['coupon'] = $this->request->post['coupon'];
				
				$this->session->data['success'] = $this->language->get('text_coupon');
				
				$this->redirect($this->url->link('checkout/cart'));
			}
			elseif($this->validateVoucher($this->request->post['coupon']))
			{
				
				$this->request->post['voucher'] = $this->request->post['coupon'];
				
			}
		}
		
		// Voucher
		if (isset($this->request->post['voucher']) && $this->validateVoucher()) { 
			$this->session->data['voucher'][$this->request->post['voucher']] = $this->request->post['voucher'];
			
			$this->session->data['success'] = $this->language->get('text_voucher');
			
			$this->redirect($this->url->link('checkout/cart'));
		}

		// Reward
		if (isset($this->request->post['reward']) && $this->validateReward()) { 
			$this->session->data['reward'] = $this->request->post['reward'];
			
			$this->session->data['success'] = $this->language->get('text_reward');
			
			$this->redirect($this->url->link('checkout/cart'));
		}
		
		// Shipping
		if (isset($this->request->post['shipping_method']) && $this->validateShipping()) {
			$shipping = explode('.', $this->request->post['shipping_method']);
			
			$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
			// echo $this->session->data['shipping_method']['code'];exit;
			$this->session->data['success'] = $this->language->get('text_shipping');
			
				echo 1;exit;
			
			$this->redirect($this->url->link('checkout/cart'));
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
		
		if ($this->cart->hasProducts() || !empty($this->session->data['vouchers'])) {
			$points = $this->customer->getRewardPoints();
			
			$points_total = 0;
			
			foreach ($this->cart->getProducts() as $product) {
				if ($product['points']) {
					$points_total += $product['points'];
				}
			}		
			
			$this->data['heading_title'] = $this->language->get('heading_title');
			
			$this->data['text_next'] = $this->language->get('text_next');
			$this->data['text_next_choice'] = $this->language->get('text_next_choice');
			$this->data['text_use_coupon'] = $this->language->get('text_use_coupon');
			$this->data['text_use_voucher'] = $this->language->get('text_use_voucher');
			$this->data['text_use_reward'] = sprintf($this->language->get('text_use_reward'), $points);
			$this->data['text_shipping_estimate'] = $this->language->get('text_shipping_estimate');
			$this->data['text_shipping_detail'] = $this->language->get('text_shipping_detail');
			$this->data['text_shipping_method'] = $this->language->get('text_shipping_method');
			$this->data['text_select'] = $this->language->get('text_select');
			$this->data['text_none'] = $this->language->get('text_none');
			
			$this->data['column_image'] = $this->language->get('column_image');
			$this->data['column_name'] = $this->language->get('column_name');
			$this->data['column_model'] = $this->language->get('column_model');
			$this->data['column_quantity'] = $this->language->get('column_quantity');
			$this->data['column_price'] = $this->language->get('column_price');
			$this->data['column_total'] = $this->language->get('column_total');
			
			$this->data['entry_coupon'] = $this->language->get('entry_coupon');
			$this->data['entry_voucher'] = $this->language->get('entry_voucher');
			$this->data['entry_reward'] = sprintf($this->language->get('entry_reward'), $points_total);
			$this->data['entry_country'] = $this->language->get('entry_country');
			$this->data['entry_zone'] = $this->language->get('entry_zone');
			$this->data['entry_postcode'] = $this->language->get('entry_postcode');
			
			$this->data['button_update'] = $this->language->get('button_update');
			$this->data['button_remove'] = $this->language->get('button_remove');
			$this->data['button_coupon'] = $this->language->get('button_coupon');
			$this->data['button_voucher'] = $this->language->get('button_voucher');
			$this->data['button_reward'] = $this->language->get('button_reward');
			$this->data['button_quote'] = $this->language->get('button_quote');
			$this->data['button_shipping'] = $this->language->get('button_shipping');			
			$this->data['button_shopping'] = $this->language->get('button_shopping');
			$this->data['button_checkout'] = $this->language->get('button_checkout');
			
		//	$this->data['checkout'] = $this->url->link('checkout/checkout', '', 'SSL');


			
			
			if ($this->config->get('config_customer_price') && !$this->customer->isLogged()) {
				$this->data['attention'] = sprintf($this->language->get('text_login'), $this->url->link('account/login'), $this->url->link('account/register'));
			} else {
				$this->data['attention'] = '';
			}
			
			if (isset($this->session->data['success'])) {
				$this->data['success'] = $this->session->data['success'];
				
				unset($this->session->data['success']);
			} else {
				$this->data['success'] = '';
			}
			
			$this->data['action'] = $this->url->link('checkout/cart');   
			
			if ($this->config->get('config_cart_weight')) {
				$this->data['weight'] = $this->weight->format($this->cart->getWeight(), $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point'));
			} else {
				$this->data['weight'] = '';
			}
			
			$this->load->model('tool/image');
			
			$this->data['products'] = array();
			
			$products = $this->cart->getProducts();
			$xxProducts = array();
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
					$image = $this->model_tool_image->resize('data/image-coming-soon.jpg', $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
				}

				$option_data = array();

				foreach ($product['option'] as $option) {
					if ($option['type'] != 'file') {
						$value = $option['option_value'];	
					} else {
						$filename = $this->encryption->decrypt($option['option_value']);
						
						$value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
					}
					
					$option_data[] = array(
						'name'  => $option['name'],
						'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
						);
				}
				
				// Display prices
				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')));
					$old_price = $this->currency->format($this->tax->calculate($product['old_price'], $product['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$price = false;
				}
				// echo $old_price;exit;
				
				// Display prices
				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$total = $this->currency->format($this->tax->calculate($product['total'], $product['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$total = false;
				}


				// Load Discounts for PPUSA 2.0
				$this->load->model('catalog/product');
				$discountsData = $this->model_catalog_product->getProductDiscounts($product['product_id']);

				$discounts = array(); 
				$discounts[] = array(
					'quantity' => '1' . (($discountsData[0]) ? '-' . ($discountsData[0]['quantity'] - 1) : ''),
					'price'    => $price
					);
				foreach ($discountsData as $key => $discount) {
					$discounts[] = array(
						'quantity' => $discount['quantity'] . (($discountsData[($key + 1)]) ? '-' . ($discountsData[($key + 1)]['quantity'] - 1) : '+'),
						'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')))
						);
				}
				
				$xxProducts[] = array(
					'key'      => $product['key'],
					'thumb'    => $image,
					'name'     => $product['name'],
					'model'    => $product['model'],
					'option'   => $option_data,
					'quantity' => $product['quantity'],
					'discounts'    => $discounts,
					'stock'    => $product['stock'],
					'stock_available'=>$product['stock_available'],
					'reward'   => ($product['reward'] ? sprintf($this->language->get('text_points'), $product['reward']) : ''),
					'price'    => $price,
					'total'    => $total,
					'href'     => $this->url->link('product/product', 'product_id=' . $product['product_id']),
					'remove'   => $this->url->link('checkout/cart', 'remove=' . $product['key'])
					);
				$this->data['products'][] = array(
					'key'      => $product['key'],
					'thumb'    => $image,
					'name'     => $product['name'],
					'model'    => $product['model'],
					'option'   => $option_data,
					'discounts'    => $discounts,
					'quantity' => $product['quantity'],
					'stock'    => $product['stock'],
					'stock_available'=>$product['stock_available'],
					'reward'   => ($product['reward'] ? sprintf($this->language->get('text_points'), $product['reward']) : ''),
					'price'    => $price,
					'old_price'    => ($old_price),
					'total'    => $total,
					'href'     => $this->url->link('product/product', 'product_id=' . $product['product_id']),
					'remove'   => $this->url->link('checkout/cart', 'remove=' . $product['key'])
					);
			}
			
			if (isset($this->error['warning'])) {
				$this->data['error_warning'] = $this->error['warning'];
			} elseif (!$this->cart->hasStock() && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
      			// $this->data['error_warning'] = $this->language->get('error_stock');		
				
				$stock_message = 'Please update stock:<br><br>';
				$k= 1;
				$this->data['xxProducts'] = array();
				foreach($xxProducts as $xproduct)
				{
					if($xproduct['stock']==false)
					{
						$this->data['xxProducts'][] = $xproduct['model'];
						if($xproduct['stock_available']>0)
						{

						$stock_message.=$k.') '.$xproduct['model'].', only '.$xproduct['stock_available'].' pcs are available<br>';
						}
						else
						{
							$stock_message.=$k.') '.$xproduct['model'].', is out of stock<br>';
						}
						$k++;
					}


				}
				$this->data['error_warning']=$stock_message;
				// $this->data['xxProducts'] = $xxProducts;
			} else {
				$this->data['error_warning'] = '';
				$this->data['xxProducts'] = array();
			}

			// Gift Voucher
			$this->data['vouchers'] = array();

			if (!empty($this->session->data['vouchers'])) {

				foreach ($this->session->data['vouchers'] as $key => $voucher) {
					$this->data['vouchers'][] = array(
						'key'         => $key,
						'description' => $voucher['description'],
						'amount'      => $this->currency->format($voucher['amount']),
						'remove'      => $this->url->link('checkout/cart', 'remove=' . $key)   
						);
				}
			}

			if (isset($this->request->post['next'])) {
				$this->data['next'] = $this->request->post['next'];
			} else {
				$this->data['next'] = '';
			}

			$this->data['coupon_status'] = $this->config->get('coupon_status');

			if (isset($this->request->post['coupon'])) {
				$this->data['coupon'] = $this->request->post['coupon'];			
			} elseif (isset($this->session->data['coupon'])) {
				$this->data['coupon'] = $this->session->data['coupon'];
			} else {
				$this->data['coupon'] = '';
			}

			$this->data['voucher_status'] = $this->config->get('voucher_status');

			if (isset($this->request->post['voucher'])) {
				$this->data['voucher'] = $this->request->post['voucher'];				
			} elseif (isset($this->session->data['voucher'])) {
				$this->data['voucher'] = $this->session->data['voucher'];
			} else {
				$this->data['voucher'] = '';
			}

			$this->data['reward_status'] = ($points && $points_total && $this->config->get('reward_status'));

			if (isset($this->request->post['reward'])) {
				$this->data['reward'] = $this->request->post['reward'];				
			} elseif (isset($this->session->data['reward'])) {
				$this->data['reward'] = $this->session->data['reward'];
			} else {
				$this->data['reward'] = '';
			}

			$this->data['shipping_status'] = $this->config->get('shipping_status') && $this->config->get('shipping_estimator') && $this->cart->hasShipping();	

			if (isset($this->request->post['country_id'])) {
				$this->data['country_id'] = $this->request->post['country_id'];				
			} elseif (isset($this->session->data['shipping_country_id'])) {
				$this->data['country_id'] = $this->session->data['shipping_country_id'];			  	
			} else {
				$this->data['country_id'] = $this->config->get('config_country_id');
			}

			$this->load->model('localisation/country');

			$this->data['countries'] = $this->model_localisation_country->getCountries();

			if (isset($this->request->post['zone_id'])) {
				$this->data['zone_id'] = $this->request->post['zone_id'];				
			} elseif (isset($this->session->data['shipping_zone_id'])) {
				$this->data['zone_id'] = $this->session->data['shipping_zone_id'];			
			} else {
				$this->data['zone_id'] = '';
			}
			// print_r($this->session->data['shipping_postcode']);exit;
			if (isset($this->request->post['postcode'])) {
				$this->data['postcode'] = $this->request->post['postcode'];				
			} elseif (isset($this->session->data['shipping_postcode'])) {
				$this->data['postcode'] = $this->session->data['shipping_postcode'];					
			} else {
				$this->data['postcode'] = '';
			}

			if (isset($this->request->post['shipping_method'])) {
				$this->data['shipping_method'] = $this->request->post['shipping_method'];				
			} elseif (isset($this->session->data['shipping_method'])) {
				$this->data['shipping_method'] = $this->session->data['shipping_method']['code']; 
			} else {
				$this->data['shipping_method'] = '';
			}

			// Totals
			$this->load->model('setting/extension');

			$total_data = array();					
			$total = 0;
			$taxes = $this->cart->getTaxes();

			// Display prices
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
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

					$sort_order = array(); 

					foreach ($total_data as $key => $value) {
						$sort_order[$key] = $value['sort_order'];
					}

					array_multisort($sort_order, SORT_ASC, $total_data);			
				}
			}

			$this->data['totals'] = $total_data;

			$this->data['continue'] = $this->url->link('common/home');

			$this->data['checkout'] = $this->url->link('checkout/checkout', '', 'SSL');
$this->data['aclogin'] = $this->url->link('account/login', '', 'SSL');


			$this->document->addStyle('catalog/view/theme/' . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')). '/stylesheet/tipsy.css');
			$this->document->addScript('catalog/view/javascript/jquery.tipsy.js');

			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/cart.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/cart.tpl';
			} else {
				$this->template = 'default/template/checkout/cart.tpl';
			}

			$this->children = array(
				'common/column_left',
				'common/column_right',
				'common/content_bottom',
				'common/content_top',
				'common/footer',
				'common/header'	
				);
			
				$this->children[] = 'module/cart_right_cart';
			

			$this->response->setOutput($this->render());					
		} else {
			
			$this->data['heading_title'] = 'Shopping Cart';
			$this->data['heading_image'] ='catalog/view/theme/ppusa2.0/images/icons/newcart.png';

			$this->data['text_error'] = 'Shopping cart is empty, try adding items into our interactive cart!';

			$this->data['button_continue'] = $this->language->get('button_continue');

			$this->data['continue'] = $this->url->link('common/home');

			unset($this->session->data['success']);

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

	private function validateCoupon() {
		$this->load->model('checkout/coupon');

		$coupon_info = $this->model_checkout_coupon->getCoupon($this->request->post['coupon']);			

		if (!$coupon_info) {			
		//	$this->error['warning'] = $this->language->get('error_coupon');
			return false;
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}		
	}

	public function addCoupon() {

		$this->load->model('checkout/coupon');
		$this->language->load('checkout/cart');
		$coupon_info = $this->model_checkout_coupon->getCoupon($this->request->post['coupon']);			

		if(isset($this->request->post['payment_code']))
			$this->session->data['payment_method']['code'] = $this->request->post['payment_code'];

		if(isset($this->request->post['comment']))
			$this->session->data['comment'] = $this->request->post['comment'];

		$json = array();

		if (!$coupon_info) {			
			$this->request->post['voucher'] = $this->request->post['coupon'];
				//		$json['warning'] = $this->language->get('error_coupon');

			$this->addVoucher();
		}else{
			$this->session->data['coupon'] = $this->request->post['coupon'];
			$json['success'] = $this->language->get('text_coupon');
			$this->response->setOutput(json_encode($json));		
		}	

	}

	public function addVoucher() {
		$this->load->model('checkout/voucher');
		$this->load->model('checkout/coupon');
		$this->language->load('checkout/cart');
		$voucher_info = $this->model_checkout_voucher->getVoucher($this->request->post['voucher']);			

		if(isset($this->request->post['payment_code']))
			$this->session->data['payment_method']['code'] = $this->request->post['payment_code'];

		if(isset($this->request->post['comment']))
			$this->session->data['comment'] = $this->request->post['comment'];

		$json = array();

		if (!$voucher_info) {			
			
			$voucher_info = $this->model_checkout_coupon->getCoupon($this->request->post['voucher']);	

			if (!$voucher_info) {
				if(strtolower($this->request->post['voucher'])=='bulklv25')
				{
					$json['warning'] = 'You must add atleast 25 pcs of iPhone Eco Plus LCD Screens to activate this promotion';
				}
				else
				{
					$json['warning'] = $this->language->get('error_voucher');
					
				}
			}
			else
			{
				$this->session->data['voucher'][$this->request->post['voucher']] = $this->request->post['voucher'];
				$json['success'] = 'Bulk pricing promotion has been activated!';
				$this->session->data['success'] = $json['success'];
				$json['ib'] = 1;
			}
		}else{
			$this->session->data['voucher'][$this->request->post['voucher']] = $this->request->post['voucher'];
			$json['success'] = $this->language->get('text_voucher');
			$json['ib'] = 0;
		}	
		$this->response->setOutput(json_encode($json));		
	}

	public function removeCoupon() {
		$this->language->load('checkout/cart');
		unset($this->session->data['coupon']);

		if(isset($this->request->post['payment_code']))
			$this->session->data['payment_method']['code'] = $this->request->post['payment_code'];

		if(isset($this->request->post['comment']))
			$this->session->data['comment'] = $this->request->post['comment'];

		$json['success'] = $this->language->get('text_coupon_success_remove');
		if(isset($this->request->post['saveMessageToSession']))
			$this->session->data['success'] = $this->language->get('text_coupon_success_remove');
		$this->response->setOutput(json_encode($json));		
	}
	public function removeVoucher() {
		$this->language->load('checkout/cart');
		unset($this->session->data['voucher']);

		if(isset($this->request->post['payment_code']))
			$this->session->data['payment_method']['code'] = $this->request->post['payment_code'];

		if(isset($this->request->post['comment']))
			$this->session->data['comment'] = $this->request->post['comment'];

		$json['success'] = $this->language->get('text_voucher_success_remove');
		if(isset($this->request->post['saveMessageToSession']))
			$this->session->data['success'] = $this->language->get('text_voucher_success_remove');

		$this->response->setOutput(json_encode($json));		
	}

	private function validateVoucher($voucher_no='') {
		$this->load->model('checkout/voucher');

		if($voucher_no=='')
		{
			$voucher_info = $this->model_checkout_voucher->getVoucher($this->request->post['voucher']);			
		}
		else
		{
			$voucher_info = $this->model_checkout_voucher->getVoucher($voucher_no);			
		}


		if (!$voucher_info) {			
			$this->error['warning'] = $this->language->get('error_voucher');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}		
	}

	private function validateReward() {
		$points = $this->customer->getRewardPoints();

		$points_total = 0;

		foreach ($this->cart->getProducts() as $product) {
			if ($product['points']) {
				$points_total += $product['points'];
			}
		}	

		if (empty($this->request->post['reward'])) {
			$this->error['warning'] = $this->language->get('error_reward');
		}

		if ($this->request->post['reward'] > $points) {
			$this->error['warning'] = sprintf($this->language->get('error_points'), $this->request->post['reward']);
		}

		if ($this->request->post['reward'] > $points_total) {
			$this->error['warning'] = sprintf($this->language->get('error_maximum'), $points_total);
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}		
	}

	private function validateShipping() {
		if (!empty($this->request->post['shipping_method'])) {
			$shipping = explode('.', $this->request->post['shipping_method']);

			if (!isset($shipping[0]) || !isset($shipping[1]) || !isset($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {			
				$this->error['warning'] = $this->language->get('error_shipping');
			}
		} else {
			$this->error['warning'] = $this->language->get('error_shipping');
		}
		// print_r($this->session->data['shipping_methods']);exit;

		if (!$this->error) {
			return true;
		} else {
			return false;
		}		
	}
	public function update_qty()
	{
		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		}
		if (isset($this->request->post['quantity'])) {
				$quantity = $this->request->post['quantity'];
			}
		$this->cart->update($product_id, $quantity);
	}
	public function add($product_id = 0, $quantity = 1) {
		$this->language->load('checkout/cart');

		$json = array();

		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		}

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) {			
			if (isset($this->request->post['quantity'])) {
				$quantity = $this->request->post['quantity'];
			}

			if (isset($this->request->post['option'])) {
				$option = array_filter($this->request->post['option']);
			} else {
				$option = array();	
			}

			$product_options = $this->model_catalog_product->getProductOptions($product_id);

			foreach ($product_options as $product_option) {
				if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
					$json['error']['option'][$product_option['product_option_id']] = sprintf($this->language->get('error_required'), $product_option['name']);
				}
			}
			
			if (!$json) {
				$this->cart->add($product_id, $quantity, $option);

				//$json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . $this->request->post['product_id']), $product_info['name'], $this->url->link('checkout/cart'));

				$json['success'] = sprintf($this->language->get('text_success'), $product_info['name'],$this->url->link('checkout/cart'), 'Checkout');

				unset($this->session->data['shipping_method']);
				unset($this->session->data['shipping_methods']);
				unset($this->session->data['payment_method']);
				unset($this->session->data['payment_methods']);

				// Totals
				$this->load->model('setting/extension');

				$total_data = array();					
				$total = 0;
				$taxes = $this->cart->getTaxes();

				// Display prices
				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
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

						$sort_order = array(); 

						foreach ($total_data as $key => $value) {
							$sort_order[$key] = $value['sort_order'];
						}

						array_multisort($sort_order, SORT_ASC, $total_data);			
					}
				}
				$json['total_items'] = $this->cart->countProducts();
				$json['product_count'] = $this->session->data['cart'][$product_id];
				$json['total'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total));
			} else {
				$json['redirect'] = str_replace('&amp;', '&', $this->url->link('product/product', 'product_id=' . $product_id));
			}
		}

		$this->response->setOutput(json_encode($json));
		//return json_encode($json);		
	}


	// used for quick order setting
	private function add2($product_id = 0, $quantity = 1) {
		$this->language->load('checkout/cart');

		$json = array();

		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		}

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);

		if ($product_info) {			
			if (isset($this->request->post['quantity'])) {
				$quantity = $this->request->post['quantity'];
			}

			if (isset($this->request->post['option'])) {
				$option = array_filter($this->request->post['option']);
			} else {
				$option = array();	
			}

			$product_options = $this->model_catalog_product->getProductOptions($product_id);

			foreach ($product_options as $product_option) {
				if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
					$json['error']['option'][$product_option['product_option_id']] = sprintf($this->language->get('error_required'), $product_option['name']);
				}
			}
			
			if (!$json) {
				$this->cart->add($product_id, $quantity, $option);

				//$json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . $this->request->post['product_id']), $product_info['name'], $this->url->link('checkout/cart'));

				$json['success'] = sprintf($this->language->get('text_success'), $product_info['name'],$this->url->link('checkout/cart'), 'Checkout');

				unset($this->session->data['shipping_method']);
				unset($this->session->data['shipping_methods']);
				unset($this->session->data['payment_method']);
				unset($this->session->data['payment_methods']);

				// Totals
				$this->load->model('setting/extension');

				$total_data = array();					
				$total = 0;
				$taxes = $this->cart->getTaxes();

				// Display prices
				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
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

						$sort_order = array(); 

						foreach ($total_data as $key => $value) {
							$sort_order[$key] = $value['sort_order'];
						}

						array_multisort($sort_order, SORT_ASC, $total_data);			
					}
				}
				$json['total_items'] = $this->cart->countProducts();
				$json['total'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total));
			} else {
				$json['redirect'] = str_replace('&amp;', '&', $this->url->link('product/product', 'product_id=' . $product_id));
			}
		}

		// $this->response->setOutput(json_encode($json));
		return json_encode($json);		
	}

	public function quote() {
		$this->language->load('checkout/cart');

		$json = array();	

		if (!$this->cart->hasProducts()) {
	//	$json['error']['warning'] = $this->language->get('error_product');				
		}				

		if (!$this->cart->hasShipping()) {
		//$json['error']['warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));				
		}				

		if ($this->request->post['country_id'] == '') {
			$json['error']['country'] = $this->language->get('error_country');
		}

		if ($this->request->post['zone_id'] == '') {
			$zone_id = 0;
			if(isset($this->request->post['postcode']))
			{

				$_json = file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.$this->request->post['postcode'].'&sensor=false');
				$obj = json_decode($_json);
//var_dump($obj);exit;
				$state = '';
				foreach($obj->results as $result)
				{

	//var_dump($result);exit;
	//print_r($result['address_component']);exit;
					foreach($result->address_components as $_address)
					{

						if(in_array('administrative_area_level_1',$_address->types))
						{

							$state =  $_address->long_name;
						}




					}
				}

				if($state!='')
				{
	//echo 'SELECT geo_zone_id FROM oc_geo_zone WHERE (`name`) LIKE "%'.($state).'%"';exit;
	//echo 'SELECT geo_zone_id FROM oc_geo_zone WHERE `name`="'.strtolower($state).'"';exit;
	//echo "SELECT geo_zone_id FROM oc_geo_zone WHERE LOWER(name)='".strtolower($state)."'";exit;
	//echo 'SELECT geo_zone_id FROM oc_geo_zone WHERE (`name`) LIKE "%'.($state).'%"';exit;
					$_xx = '`name`';

					$zone_id_query = $this->db->query('SELECT zone_id FROM '.DB_PREFIX.'zone WHERE '.$_xx.' = "'.($state).'"');

					$zone_id = $zone_id_query->row['zone_id'];

					$this->request->post['zone_id'] = $zone_id;
				}
				else
				{
					$json['error']['zone'] = $this->language->get('error_zone');
				}
			}
			else
			{

				$json['error']['zone'] = $this->language->get('error_zone');
			}
		}

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->post['country_id']);

		if ($country_info && $country_info['postcode_required'] && (utf8_strlen($this->request->post['postcode']) < 2) || (utf8_strlen($this->request->post['postcode']) > 10)) {
			$json['error']['postcode'] = $this->language->get('error_postcode');
		}

		if (!$json) {		
			$this->tax->setShippingAddress($this->request->post['country_id'], $this->request->post['zone_id']);

			// Default Shipping Address
			$this->session->data['shipping_country_id'] = $this->request->post['country_id'];
			$this->session->data['shipping_zone_id'] = $this->request->post['zone_id'];
			$this->session->data['shipping_postcode'] = $this->request->post['postcode'];

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
				$zone_code = $zone_info['code'];
			} else {
				$zone = '';
				$zone_code = '';
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
				'zone_code'      => $zone_code,
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
							'error'      => $quote['error'],
							'delivery_time'	=>	$quote['delivery_time']
							);
					}
				}
			}

		

			$today_day = date('l');
			if($today_day!='Friday' and $today_day!='Thursday')
			{
				
				
				unset($quote_data['multiflatrate']['quote']['multiflatrate_5']);	// visible Fedex Next Day Saturday (Ships Fri 4:00 pm PST) on Friday only
			}
			else
			{
				//echo date('d-m-Y h:i a');exit;
				$current_time = date('d-m-Y H:i:s');
				if($today_day=='Thursday')
				{

					
					if(strtotime($current_time) < strtotime(date("d-m-Y 16:30:00")))
					{

						unset($quote_data['multiflatrate']['quote']['multiflatrate_5']);	// visible Fedex Next Day Saturday (Ships Fri 4:00 pm PST) on Friday only
					}
				}
				else
				{
					if(strtotime($current_time) > strtotime(date("d-m-Y 16:00:00")))
					{
						unset($quote_data['multiflatrate']['quote']['multiflatrate_5']);	// visible Fedex Next Day Saturday (Ships Fri 4:00 pm PST) on Friday only
					}
				}
			}
			$sort_order = array();

			foreach ($quote_data as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $quote_data);

			$this->session->data['shipping_methods'] = $quote_data;
			$json['default_shipping_method'] = $this->session->data['shipping_method']['code'];
			if ($this->session->data['shipping_methods']) {
				$json['shipping_method'] = $this->session->data['shipping_methods']; 
			} else {
				$json['error']['warning'] = sprintf($this->language->get('error_no_shipping'), $this->url->link('information/contact'));
			}				
		}	

		$this->response->setOutput(json_encode($json));						
	}

	public function country() {
		$json = array();

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']		
				);
		}

		$this->response->setOutput(json_encode($json));
	}
	public function use_voucher()
	{
		$voucher = $this->request->get['code'];	
		if($voucher && $this->validateVoucher($voucher))
		{
			$this->session->data['voucher'][$voucher] = $voucher;	
			$this->redirect($this->url->link('checkout/cart'));  			
		}
		else
		{
			$this->redirect($this->url->link('account/viewvouchers'));  			

		}

	}

	public function addSignature() {
		$this->language->load('checkout/cart');

		$json = array();

		if (isset($this->request->post['product_id'])) {
			$product_id = $this->request->post['product_id'];
		} else {
			$product_id = 0;
		}

		$this->load->model('catalog/product');

		$product_info = $this->model_catalog_product->getProduct($product_id);
		
		// print_r($product_info);exit;
		if ($product_info) {			
			if (isset($this->request->post['quantity'])) {
				$quantity = $this->request->post['quantity'];
			} else {
				$quantity = 1;
			}

			if (isset($this->request->post['option'])) {
				$option = array_filter($this->request->post['option']);
			} else {
				$option = array();	
			}

			$product_options = $this->model_catalog_product->getProductOptions($this->request->post['product_id']);

			foreach ($product_options as $product_option) {
				if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
					$json['error']['option'][$product_option['product_option_id']] = sprintf($this->language->get('error_required'), $product_option['name']);
				}
			}

			if (!$json) {
				$this->cart->add($this->request->post['product_id'], $quantity, $option);
				// echo 'here';exit;
				//$json['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . $this->request->post['product_id']), $product_info['name'], $this->url->link('checkout/cart'));

				$json['success'] = sprintf($this->language->get('text_success'), $product_info['name'],$this->url->link('checkout/cart'), 'Checkout');


				// Totals
				$this->load->model('setting/extension');

				$total_data = array();					
				$total = 0;
				$taxes = $this->cart->getTaxes();

				// Display prices
				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
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

						$sort_order = array(); 

						foreach ($total_data as $key => $value) {
							$sort_order[$key] = $value['sort_order'];
						}

						array_multisort($sort_order, SORT_ASC, $total_data);			
					}
				}

				$json['total'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($total));
			} else {
				$json['redirect'] = str_replace('&amp;', '&', $this->url->link('product/product', 'product_id=' . $this->request->post['product_id']));
			}
		}

		$this->response->setOutput(json_encode($json));		
	}
}
?>
