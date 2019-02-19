<?php
class ControllerPosCheckout extends Controller {
	public function index() {
		$this->language->load('pos/pos');
		
		$json = array();

		// only when product changes happen, will process the modify order
		if (!isset($this->request->post['order_id'])) {
			$this->response->setOutput(json_encode($json));	
			return;
		}
			
		$this->load->library('user');
		
		$this->user = new User($this->registry);
				
		if ($this->user->isLogged() && $this->user->hasPermission('modify', 'sale/order')) {	
			// Reset everything
			$this->cart->clear();
			$this->customer->logout();
			
//			unset($this->session->data['shipping_method']);
//			unset($this->session->data['shipping_methods']);			
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
			// add for NY tax begin
			unset($this->session->data['shipping_address_id']);
			// add for NY tax end

			// Settings
			$this->load->model('setting/setting');
			
			$settings = $this->model_setting_setting->getSetting('config', $this->request->post['store_id']);
			
			foreach ($settings as $key => $value) {
				$this->config->set($key, $value);
			}
			
    		// Customer
			if ($this->request->post['customer_id']) {
				$this->load->model('account/customer');

				$customer_info = $this->model_account_customer->getCustomer($this->request->post['customer_id']);

				if ($customer_info) {
					$this->customer->login($customer_info['email'], '', true);
					$this->cart->clear();
					// add for NY tax begin
					$this->session->data['shipping_address_id'] = $customer_info['address_id'];
					// add for NY tax end
				} else {
					$json['error']['customer'] = $this->language->get('error_customer');
				}
			} else {
				// Customer Group
				$this->config->set('config_customer_group_id', $this->request->post['customer_group_id']);
			}
	
			// Product
			$order_id = $this->request->post['order_id'];
			$order_product_id = '';
			if (isset($this->request->post['order_product_id'])) {
				$order_product_id = $this->request->post['order_product_id'];
			}
			$product_id = '';
			if (isset($this->request->post['product_id'])) {
				$product_id = $this->request->post['product_id'];
			}

			$this->load->model('catalog/product');
			$action = $this->request->post['action'];
			
			$order_products = array();
			$key = '0';

			$modifyOrder = false;
			$qtyAfter = 0;
			
			$page_price_list = array();

			if (!empty($this->request->post['order_product'])) {
				foreach ($this->request->post['order_product'] as $order_product) {
					$toUse = true;
					$quantity = $order_product['quantity'];
					if ($order_product['order_product_id'] == $order_product_id) {
						// the one it's modifying
						if ($action == 'delete') {
							$toUse = false;
						} elseif ($action == 'modify') {
							// check quantity using the quantity change
							$quantity = $this->request->post['quantity'] - $quantity;
							$modifyOrder = true;
							$qtyAfter = $this->request->post['quantity'];
						}
					}

					if ($toUse) {
						// the front end model catalog/product will make sure the product is in the given store, avoid this check at the current stage
						// $product_info = $this->model_catalog_product->getProduct($order_product['product_id']);
					
						// if ($product_info) {
							$option_data = array();
							
							if (isset($order_product['order_option'])) {
								foreach ($order_product['order_option'] as $option) {
									if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'image') { 
										$option_data[$option['product_option_id']] = $option['product_option_value_id'];
									} elseif ($option['type'] == 'checkbox') {
										$option_data[$option['product_option_id']][] = $option['product_option_value_id'];
									} elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
										$option_data[$option['product_option_id']] = $option['value'];
									}
								}
								
								if (version_compare(VERSION, '1.5.5.1', '>')) {
									$added_key = (int)$order_product['product_id'] . ':' . base64_encode(serialize($option_data)) . ':';
								} else {
									$added_key = (int)$order_product['product_id'] . ':' . base64_encode(serialize($option_data));
								}
								if ($order_product['order_product_id'] == $order_product_id) {
									$key = $added_key;
								}
							} else {
								if (version_compare(VERSION, '1.5.5.1', '>')) {
									$added_key = (int)$order_product['product_id'] . '::';
								} else {
									$added_key = (int)$order_product['product_id'];
								}
								if ($order_product['order_product_id'] == $order_product_id) {
									$key = $added_key;
								}
							}
							
							if (version_compare(VERSION, '1.5.5.1', '>')) {
								$this->cart->add($order_product['product_id'], $quantity, $option_data, 0);
							} else {
								$this->cart->add($order_product['product_id'], $quantity, $option_data);
							}
							$order_products[$added_key] = $order_product;

							$page_price_list[$added_key] = $order_product['price'];
							// add for Weight based price begin
							if (isset($order_product['order_option'])) {
								foreach ($order_product['order_option'] as $option) {
									if ($option['type'] == 'text' && $option['product_option_id'] == '-1') {
										$page_price_list[$added_key] = $order_product['price'] * $option['value'];
										break;
									}
								}
							}
							// add for Weight based price end
						// }
					}
				}
			}

			$this->session->data['page_price_list'] = $page_price_list;
			
			if ($action == 'insert') {
				// the front end model catalog/product will make sure the product is in the given store, avoid this check at the current stage
				// $product_info = $this->model_catalog_product->getProduct($product_id);
				// if ($product_info) {
					// product does exist
					$product_options = $this->model_catalog_product->getProductOptions($product_id);
					$option = array();
					if (isset($this->request->post['option'])) {
						$option = $this->request->post['option'];
					}
					
					foreach ($product_options as $product_option) {
						if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
							$json['error']['product']['option'][$product_option['product_option_id']] = sprintf($this->language->get('error_required'), $product_option['name']);
						}
					}
					if (!isset($json['error'])) {
						if (!isset($this->request->post['option'])) {
							if (version_compare(VERSION, '1.5.5.1', '>')) {
								$key = (int)$product_id . '::';
							} else {
								$key = (int)$product_id;
							}
						} else {
							if (version_compare(VERSION, '1.5.5.1', '>')) {
								$key = (int)$product_id . ':' . base64_encode(serialize($this->request->post['option'])) . ':';
							} else {
								$key = (int)$product_id . ':' . base64_encode(serialize($this->request->post['option']));
							}
						}
						
						if (array_key_exists($key, $order_products)) {
							$modifyOrder = true;
							$this->cart->update($key, $this->request->post['quantity']);
							$qtyAfter = $order_products[$key]['quantity'] + $this->request->post['quantity'];
						} else {
							if (version_compare(VERSION, '1.5.5.1', '>')) {
								$this->cart->add($product_id, $this->request->post['quantity'], $option, 0);
							} else {
								$this->cart->add($product_id, $this->request->post['quantity'], $option);
							}
						}
					}
				// }
			}
			// add for Inplace Pricing begin
			if (isset($this->request->post['inplace_price'])) {
				$this->session->data['inplace_pricing'] = array (
					'key' => $key,
					'price' => $this->request->post['inplace_price']
				);
				// add for Weight based price begin
				if (isset($order_product['order_option'])) {
					foreach ($order_product['order_option'] as $option) {
						if ($option['type'] == 'text' && $option['product_option_id'] == '-1') {
							// for calculating the total
							$this->session->data['inplace_pricing']['weight'] = $option['value'];
							// for calculating the tax
							$this->session->data['weight'] = array (
								'key' => $key,
								'weight' => $option['value']
							);
							break;
						}
					}
				}
				// add for Weight based price end
			}
			// add for Inplace Pricing end
			
			// add for Weight based price begin
			if (!empty($this->request->post['weight']) && !$modifyOrder) {
				$this->session->data['weight'] = array (
					'key' => $key,
					'weight' => $this->request->post['weight']
				);
			}
			// add for Weight based price end
			
			// Tax, important: all the products should be in the cart before the tax is calculated
			if ($this->cart->hasShipping()) {
				$this->tax->setShippingAddress($this->request->post['shipping_country_id'], $this->request->post['shipping_zone_id']);
			} else {
				$this->tax->setShippingAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
			}
			$this->tax->setPaymentAddress($this->request->post['payment_country_id'], $this->request->post['payment_zone_id']);				
			$this->tax->setStoreAddress($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
			
			// add for Quick sale begin
			if (isset($this->request->post['extra_info']) && $this->request->post['extra_info'] == 'quick_sale' && isset($this->request->post['include_tax']) && $this->request->post['include_tax'] == '1') {
				// after set the tax and before calculate the total, update the product price if required
				$unit_price = $this->cart->getPriceFromPriceWithTax((float)$this->request->post['price'], (int)$this->request->post['tax_class_id']);
				$this->db->query("UPDATE " . DB_PREFIX . "product SET price = '" . $unit_price . "' WHERE product_id = '" . (int)$product_id . "'");
				// call update to force the cached data to be refreshed when next time call the getProducts(), which will pick up the price change
				$this->cart->update($key, $this->request->post['quantity']);
			}
			// add for Quick sale end
			
			while (!isset($json['error'])) {
				// Stock
				$cart_products = $this->cart->getProducts();
				foreach ($cart_products as $cart_product) {
					if (!$cart_product['stock'] && $cart_product['key'] == $key) {
						// as the current calculation algorithm is different from opencart default one
						// i.e. opencart add all product, check the stock and then save
						// while we check the stock, save, modify, check the stock again and then save again
						// only the product to be changed will be compared
						if (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')) {
							if (empty($cart_product['option'])) {
								$json['error']['product']['stock'] = sprintf($this->language->get('error_stock'), $cart_product['name']);
							} else {
								$option_desc = '';
								$option_size = count($cart_product['option']);
								$option_index = 0;
								foreach ($cart_product['option'] as $cart_option) {
									$option_desc .= $cart_option['name'] . '=' . $cart_option['option_value'];
									if ($option_index < $option_size-1) {
										$option_desc .= ', ';
									}
									$option_index ++;
								}
								$json['error']['product']['stock'] = sprintf($this->language->get('error_stock_option'), $cart_product['name'], $option_desc);
							}
							break 2;
						} else {
							break;
						}
					}
				}
				
				// recalculate using the real number
				if ($modifyOrder) {
					$this->cart->update($key, $qtyAfter);
					$cart_products = $this->cart->getProducts();
					foreach ($cart_products as $cart_product) {
						if (!$cart_product['stock']) {
							// discard stock error this time as it has been checked when the product was added/updated to cart
							$cart_product['stock'] = true;
						}
					}
				}
				
				if ($action == 'insert') {
					if (!isset($cart_products[$key])) {
						$json['error']['warning'] = $this->language->get('error_status');
						break;
					}
					$order_product = $cart_products[$key];
					if ($modifyOrder) {
						// if it's actually a modification
						$order_product['action'] = 'modify';
						// find the product in order_products and set the order_product_id
						$existing_order_product = $order_products[$key];
						$order_product['order_product_id'] = $existing_order_product['order_product_id'];
					} else {
						// it's an insert
						$order_product['action'] = $action;
					}
					$order_product['tax'] = $this->tax->getTax($order_product['price'], $order_product['tax_class_id']);
					$order_product['price_text'] = $this->currency->format($order_product['price'] + ($this->config->get('config_tax') ? $order_product['tax'] : 0), $this->request->post['currency_code'], $this->request->post['currency_value']);
					$order_product['total_text'] = $this->currency->format($order_product['total'] + ($this->config->get('config_tax') ? ($order_product['tax'] * $order_product['quantity']) : 0), $this->request->post['currency_code'], $this->request->post['currency_value']);
					// add for Weight based price begin
					if (!empty($this->request->post['weight']) && !$modifyOrder) {
						$order_product['total_text'] = $this->currency->format($order_product['total'] + ($this->config->get('config_tax') ? ($order_product['tax'] * $this->request->post['weight']) : 0), $this->request->post['currency_code'], $this->request->post['currency_value']);
						$option_data = array(
							'product_option_id'       => '-1',
							'product_option_value_id' => '-1',
							'option_id'               => '-1',
							'option_value_id'         => '-1',
							'name'                    => $this->request->post['weight_name'],
							'option_value'            => $this->request->post['weight'],
							'type'                    => 'text',
							'quantity'                => '',
							'subtract'                => '',
							'price'                   => '',
							'price_prefix'            => '',
							'points'                  => '',
							'points_prefix'           => '',								
							'weight'                  => '',
							'weight_prefix'           => ''
						);
						if (empty($order_product['option'])) {
							$order_product['option'] = array();
						}
						array_push($order_product['option'], $option_data);
					}
					// add for Weight based price end
					
					$json['order_product'] = $order_product;
				} elseif ($action == 'modify') {
					$modify_product = $cart_products[$key];
					// need the price_text for the modified product
					$modify_product['tax'] = $this->tax->getTax($modify_product['price'], $modify_product['tax_class_id']);
					$json['total_text'] = $this->currency->format($modify_product['total'] + ($this->config->get('config_tax') ? ($modify_product['tax'] * $modify_product['quantity']) : 0), $this->request->post['currency_code'], $this->request->post['currency_value']);
					// add for Weight based price begin
					if (isset($order_product['order_option'])) {
						foreach ($order_product['order_option'] as $option) {
							if ($option['type'] == 'text' && $option['product_option_id'] == '-1') {
								$json['total_text'] = $this->currency->format($modify_product['total'] + ($this->config->get('config_tax') ? ($modify_product['tax'] * $modify_product['quantity'] * $option['value']) : 0), $this->request->post['currency_code'], $this->request->post['currency_value']);
								break;
							}
						}
					}
					// add for Weight based price end
					$json['total'] = $modify_product['total'];
					$json['price'] = $modify_product['price'];
					// add for Inplace Pricing begin
					if (isset($this->request->post['inplace_price'])) {
						$json['tax'] = $modify_product['tax'];
					}
					// add for Inplace Pricing end
				}
				
				// Voucher
				$this->session->data['vouchers'] = array();
				
				if (isset($this->request->post['order_voucher'])) {
					foreach ($this->request->post['order_voucher'] as $voucher) {
						$this->session->data['vouchers'][] = array(
							'voucher_id'       => $voucher['voucher_id'],
							'description'      => $voucher['description'],
							'code'             => substr(md5(mt_rand()), 0, 10),
							'from_name'        => $voucher['from_name'],
							'from_email'       => $voucher['from_email'],
							'to_name'          => $voucher['to_name'],
							'to_email'         => $voucher['to_email'],
							'voucher_theme_id' => $voucher['voucher_theme_id'], 
							'message'          => $voucher['message'],
							'amount'           => $voucher['amount']    
						);
					}
				}

				// Add a new voucher if set
				if (isset($this->request->post['from_name']) && isset($this->request->post['from_email']) && isset($this->request->post['to_name']) && isset($this->request->post['to_email']) && isset($this->request->post['amount'])) {
					if ((utf8_strlen($this->request->post['from_name']) < 1) || (utf8_strlen($this->request->post['from_name']) > 64)) {
						$json['error']['vouchers']['from_name'] = $this->language->get('error_from_name');
					}  
				
					if ((utf8_strlen($this->request->post['from_email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['from_email'])) {
						$json['error']['vouchers']['from_email'] = $this->language->get('error_email');
					}
				
					if ((utf8_strlen($this->request->post['to_name']) < 1) || (utf8_strlen($this->request->post['to_name']) > 64)) {
						$json['error']['vouchers']['to_name'] = $this->language->get('error_to_name');
					}       
				
					if ((utf8_strlen($this->request->post['to_email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['to_email'])) {
						$json['error']['vouchers']['to_email'] = $this->language->get('error_email');
					}
				
					if (($this->request->post['amount'] < 1) || ($this->request->post['amount'] > 1000)) {
						$json['error']['vouchers']['amount'] = sprintf($this->language->get('error_amount'), $this->currency->format(1, false, 1), $this->currency->format(1000, false, 1) . ' ' . $this->config->get('config_currency'));
					}
				
					if (!isset($json['error']['vouchers'])) { 
						$voucher_data = array(
							'order_id'         => 0,
							'code'             => substr(md5(mt_rand()), 0, 10),
							'from_name'        => $this->request->post['from_name'],
							'from_email'       => $this->request->post['from_email'],
							'to_name'          => $this->request->post['to_name'],
							'to_email'         => $this->request->post['to_email'],
							'voucher_theme_id' => $this->request->post['voucher_theme_id'], 
							'message'          => $this->request->post['message'],
							'amount'           => $this->request->post['amount'],
							'status'           => true             
						); 
						
						$this->load->model('checkout/voucher');
						
						$voucher_id = $this->model_checkout_voucher->addVoucher(0, $voucher_data);  
										
						$this->session->data['vouchers'][] = array(
							'voucher_id'       => $voucher_id,
							'description'      => sprintf($this->language->get('text_for'), $this->currency->format($this->request->post['amount'], $this->config->get('config_currency')), $this->request->post['to_name']),
							'code'             => substr(md5(mt_rand()), 0, 10),
							'from_name'        => $this->request->post['from_name'],
							'from_email'       => $this->request->post['from_email'],
							'to_name'          => $this->request->post['to_name'],
							'to_email'         => $this->request->post['to_email'],
							'voucher_theme_id' => $this->request->post['voucher_theme_id'], 
							'message'          => $this->request->post['message'],
							'amount'           => $this->request->post['amount']            
						); 
					}
				}
				
				if (isset($json['error'])) break;
							
				$this->load->model('setting/extension');

				// Coupon
				if (!empty($this->request->post['coupon'])) {
					$this->load->model('checkout/coupon');
				
					$coupon_info = $this->model_checkout_coupon->getCoupon($this->request->post['coupon']);			
				
					if ($coupon_info) {					
						$this->session->data['coupon'] = $this->request->post['coupon'];
					} else {
						$json['error']['coupon'] = $this->language->get('error_coupon');
					}
				}
				
				// Voucher
				if (!empty($this->request->post['voucher'])) {
					$this->load->model('checkout/voucher');
				
					$voucher_info = $this->model_checkout_voucher->getVoucher($this->request->post['voucher']);			
				
					if ($voucher_info) {					
						$this->session->data['voucher'] = $this->request->post['voucher'];
					} else {
						$json['error']['voucher'] = $this->language->get('error_voucher');
					}
				}

				// Reward Points
				if (!empty($this->request->post['reward'])) {
					$points = $this->customer->getRewardPoints();
					
					if ($this->request->post['reward'] > $points) {
						$json['error']['reward'] = sprintf($this->language->get('error_points'), $this->request->post['reward']);
					}
					
					if (!isset($json['error']['reward'])) {
						$points_total = 0;
						
						foreach ($this->cart->getProducts() as $product) {
							if ($product['points']) {
								$points_total += $product['points'];
							}
						}				
						
						if ($this->request->post['reward'] > $points_total) {
							$json['error']['reward'] = sprintf($this->language->get('error_maximum'), $points_total);
						}
						
						if (!isset($json['error']['reward'])) {		
							$this->session->data['reward'] = $this->request->post['reward'];
						}
					}
				}

				// Totals
				$json['order_total'] = array();					
				$total = 0;
				$taxes = $this->cart->getTaxes();
				// error_log(print_r($taxes, true));
				
				$sort_order = array(); 
				
				$results = $this->model_setting_extension->getExtensions('total');
				
				foreach ($results as $key => $value) {
					$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
				}
				
				array_multisort($sort_order, SORT_ASC, $results);
				
				foreach ($results as $result) {
					if ($this->config->get($result['code'] . '_status') && !($result['code'] == 'shipping' && !empty($this->session->data['shipping_method']['code']) && $this->session->data['shipping_method']['code'] == 'instore.instore')) {
						$this->load->model('total/' . $result['code']);
						
						// add for Discount begin
						$update_total = true;
						if ($result['code'] == 'pos_discount') {
							if (isset($this->session->data['pos_discount'])) {
								$discount = $this->session->data['pos_discount'];
								if ($discount['order_id'] != $order_id) {
									$update_total = false;
								}
							} else {
								$update_total = false;
							}
							$json['currency_symbol'] = $this->currency->getSymbolLeft($this->request->post['currency_code']);
							if ($json['currency_symbol'] == '') {
								$json['currency_symbol'] = $this->currency->getSymbolRight($this->request->post['currency_code']);
							}
						}
						if ($update_total) {
						// add for Discount end
						
						$this->{'model_total_' . $result['code']}->getTotal($json['order_total'], $total, $taxes);
						error_log('total: ' . $result['code'] . ' with total ' . $total . ' and tax ');
						// error_log(print_r($taxes, true));
						// error_log(print_r($json['order_total'], true));
						
						// add for Discount begin
						}
						// add for Discount end
					}
					
					$sort_order = array(); 
				  
					foreach ($json['order_total'] as $key => $value) {
						$sort_order[$key] = $value['sort_order'];
						// reformat the total text as it's not right if default currency is not USD, need to be fixed by Opencart, but fix here temporarily
						$json['order_total'][$key]['text'] = $this->currency->format($value['value'], $this->request->post['currency_code'], $this->request->post['currency_value']);
					}
		
					array_multisort($sort_order, SORT_ASC, $json['order_total']);
				}
				
				break;
			}
			
			if (!isset($json['error'])) { 
				$json['success'] = $this->language->get('text_success');
			}
			
			// Reset everything
			$this->cart->clear();
			$this->customer->logout();
			
//			unset($this->session->data['shipping_method']);
//			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
			unset($this->session->data['page_price_list']);
			// add for Inplace Pricing begin
			unset($this->session->data['inplace_pricing']);
			// add for Inplace Pricing end
			// add for NY tax begin
			unset($this->session->data['shipping_address_id']);
			// add for NY tax end
			// add for Weight based price begin
			unset($this->session->data['weight']);
			// add for Weight based price end
		} else {
      		$json['error']['warning'] = $this->language->get('error_permission');
		}
	
		$this->response->setOutput(json_encode($json));	
	}
}
?>