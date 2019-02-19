<?php  
class ControllerModuleUpsellOffer extends Controller {
	protected function index($setting) {
		$this->language->load('module/upsell_offer');
		
		$this->data['heading_title'] = $this->language->get('heading_title');
    	
		$this->data['text_register'] = $this->language->get('text_register');
		
		$array = array('1.5.1', '1.5.1.1', '1.5.1.2', '1.5.1.3');
		
		if (in_array(VERSION, $array)) {
			$this->document->addScript('catalog/view/javascript/jquery/colorbox/jquery.colorbox.js');
			$this->document->addStyle('catalog/view/javascript/jquery/colorbox/colorbox.css');
			$this->document->addStyle('catalog/view/theme/' . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/stylesheet/upsell_offer.css');
		}
		
		$array_155x = array('1.5.5', '1.5.5.1', '1.5.6', '1.5.6.1', '1.5.6.2', '1.5.6.3', '1.5.6.4');
		
		if (in_array(VERSION, $array_155x)) {
			$this->document->addScript('catalog/view/javascript/jquery/colorbox/jquery.colorbox-min.js');
			$this->document->addStyle('catalog/view/javascript/jquery/colorbox/colorbox.css');
		}
		
		if ((($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) == 'shoppica2') {
			$this->document->addScript('catalog/view/javascript/jquery/colorbox/jquery.colorbox.js');
			$this->document->addStyle('catalog/view/javascript/jquery/colorbox/colorbox.css');
		}
		
		if ($setting['selector']) {
			$this->data['selector'] = $setting['selector'];
		} else {	
			$this->data['selector'] = 'checkout/checkout';
		}
		
		$this->data['checkout'] = $this->url->link('checkout/checkout', '', 'SSL');
		
		$this->data['show_offer'] = false;
		
		if ((($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) == 'shoppica2') {
			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/upsell_offer_shoppica.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/upsell_offer_shoppica.tpl';
			} else {
				$this->template = 'default/template/module/upsell_offer_shoppica.tpl';
			}
		} else {
			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/upsell_offer.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/upsell_offer.tpl';
			} else {
				$this->template = 'default/template/module/upsell_offer.tpl';
			}
		}
		
		$this->render();
	}
	
	public function get() {
		$this->load->language('module/upsell_offer');

		$this->load->model('upsell/offer');
		
		$data = array(
			'cart_total' => $this->cart->getTotal()
		);

		$upsell_offers_nr = 0;
		
		$results = $this->model_upsell_offer->getUpsellOffers($data);
		
		$live_cart_products = array();
		
		if ($results) {
			foreach ($this->cart->getProducts() as $product) {
				$live_cart_products[] = $product['product_id'];
			}
		}
		
		$upsell_products = array();
		
		foreach ($results as $key=>$result) {
			$cart_products_condition = true; // is true when we don't have a condition set or the condition will be true below after we will check it out 
			
			if ($result['cart_products']) {
				$cart_products_condition = false;
				$result['cart_products'] = explode(',', $result['cart_products']);
				
				// show the offer only if any of this products are in the customer's cart 
				foreach ($result['cart_products'] as $cart_product) {
					if ( in_array($cart_product, $live_cart_products) ) {
						$cart_products_condition = true;
						break;
					}
				}
			}
			
			// it's ok to show the offer
			if ($cart_products_condition) {
				// verify if any upsell product is already in the cart
				$result['upsell_products'] = explode(',', $result['upsell_products']);
				
				foreach ($result['upsell_products'] as $key2=>$upsell_product) {
					if ( in_array($upsell_product, $live_cart_products) ) {
						unset($result['upsell_products'][$key2]);
					}
				}
				
				// if we have still products in this offer that it isn't already in the cart
				if ($result['upsell_products']) {
					$upsell_offers_nr++;
					$upsell_products = array_unique(array_merge($upsell_products, $result['upsell_products']));
				} else {
					unset($results[$key]);
				}	
			} else {
				unset($results[$key]);
			}	
		}
		
		if ($upsell_offers_nr > 1) {
			$upsell_offer_description = $this->config->get('upsell_offer_description');
			$this->data['title'] = $upsell_offer_description['title'][(int)$this->config->get('config_language_id')];
			$this->data['description'] = $upsell_offer_description['description'][(int)$this->config->get('config_language_id')];
		} else {
			foreach ($results as $result) {
				$this->data['title'] = $result['title'];
				$this->data['description'] = $result['description'];
			}
		}
		
		$product_nr = count($upsell_products);
		
		$this->data['products'] = array();
		
		if ($upsell_offers_nr && $product_nr) {
			$this->load->model('catalog/product');
					
			$this->load->model('tool/image');
				
			foreach ($upsell_products as $product_id) {
				$product_info = $this->model_catalog_product->getProduct($product_id);
				
				if ($product_info) {
					if ($product_info['image']) {
						if ($product_nr == 1) {
							if ( $this->config->get('upsell_offer_product_image_width') ) {
								$width = $this->config->get('upsell_offer_product_image_width');
							} else {
								$width = $this->config->get('config_image_thumb_width');
							}
							if ( $this->config->get('upsell_offer_product_image_height') ) {
								$height = $this->config->get('upsell_offer_product_image_height');
							} else {
								$height = $this->config->get('config_image_thumb_height');
							}
						} else {
							if ( $this->config->get('upsell_offer_product_list_image_width') ) {
								$width = $this->config->get('upsell_offer_product_list_image_width');
							} else {
								$width = $this->config->get('config_image_product_width');
							}
							if ( $this->config->get('upsell_offer_product_list_image_height') ) {
								$height = $this->config->get('upsell_offer_product_list_image_height');
							} else {
								$height = $this->config->get('config_image_product_height');
							}
						}
						
						$image = $this->model_tool_image->resize($product_info['image'], $width, $height);
					} else {
						$image = false;
					}

					if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
					} else {
						$price = false;
					}
							
					if ((float)$product_info['special']) {
						$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')));
					} else {
						$special = false;
					}
						
					if ($this->config->get('config_tax')) {
						$tax = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price']);
					} else {
						$tax = false;
					}	
					
					if ($product_info['minimum']) {
						$minimum = $product_info['minimum'];
					} else {
						$minimum = 1;
					}
					
					$this->data['products'][] = array(
						'product_id'     => $product_info['product_id'],
						'thumb'   	 => $image,
						'name'    	 => $product_info['name'],
						'price'   	 => $price,
						'special' 	 => $special,
						'tax'            => $tax,
						'minimum'	 => $minimum,
						'text_minimum'   => sprintf($this->language->get('text_minimum'), $minimum)
					);
				}
			}
			
			$this->data['product_nr'] = $product_nr;
			
			$this->data['text_qty'] = $this->language->get('text_qty');
			$this->data['text_price'] = $this->language->get('text_price');
			$this->data['text_tax'] = $this->language->get('text_tax');
			$this->data['text_added'] = $this->language->get('text_added');
					
			$this->data['button_cart'] = $this->language->get('button_cart');
			$this->data['button_to_cart'] = $this->language->get('button_to_cart');
			$this->data['button_checkout'] = $this->language->get('button_checkout');
			
			$this->data['checkout'] = $this->url->link('checkout/checkout', '', 'SSL');
			$this->data['cart'] = $this->url->link('checkout/cart', '', 'SSL');
		}
		
		$this->data['show_offer'] = true;
		
		if ((($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) == 'shoppica2') {
			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/upsell_offer_shoppica.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/upsell_offer_shoppica.tpl';
			} else {
				$this->template = 'default/template/module/upsell_offer_shoppica.tpl';
			}
		} else {
			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/upsell_offer.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/upsell_offer.tpl';
			} else {
				$this->template = 'default/template/module/upsell_offer.tpl';
			}
		}
		
		if ($product_nr) {
			$output = $this->render();
		} else {
			return 0;
		}
		
		$this->response->setOutput($output);
	}
}
?>