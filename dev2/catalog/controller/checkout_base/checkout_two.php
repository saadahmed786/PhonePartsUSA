<?php
//-----------------------------------------
// Author: 	Qphoria@gmail.com
// Web: 	http://www.OpenCartGuru.com/
// Title: 	Uber Checkout 1.5.x
//-----------------------------------------
class ControllerCheckoutCheckoutTwo extends Controller {
	private $error = array();

	public function index() {

		// May be better to clear this each time for easier testing
		unset($this->session->data['payment_methods']);
		//unset($this->session->data['shipping_country_id']);
		//unset($this->session->data['shipping_zone_id']);
		//unset($this->session->data['shipping_postcode']);

		$this->data = array_merge($this->data, $this->language->load('checkout/checkout_two'));

		$this->data['checkout_shipping'] = $this->url->link('checkout/shipping', '', 'SSL');

    	$this->data['checkout_shipping_address'] = $this->url->link('checkout/checkout_one', '', 'SSL');

    	$this->data['add_address'] = $this->url->link('checkout/checkout_two/newAddress', '', 'SSL');
		
		// Buttons
		if (version_compare(VERSION, '1.5.1.3', '>') == true) {
			$this->data['add_address_button_html'] 		= '<input type="button" target="_top" onclick="location.href=\''. $this->data['add_address'] .'\'; parent.$.fn.colorbox.close();" class="button" value="'. $this->data['button_add_address'] .'" />';
			$this->data['update_address_button_html'] 	= '<input type="button" id="updateAddress" onclick="updateAddress();" class="button" value="'. $this->data['button_continue'] .'" />';
			$this->data['comment_button_html'] 			= '<input type="button" onclick="updateComment();" class="button" value="'. $this->data['button_comment'] .'" />';
			$this->data['coupon_button_html'] 			= '<input type="button" onclick="updateCouponPaymentTotals();" class="button" value="'. $this->data['button_coupon'] .'" />';
			$this->data['voucher_button_html'] 			= '<input type="button" onclick="updateVoucherPaymentTotals();" class="button" value="'. $this->data['button_voucher'] .'" />';
			$this->data['agree_button_html'] 			= '<input type="button" onclick="alert(\''. $this->data['text_must_agree'] .'\'); return false;" class="button" value="'. $this->data['button_confirm'] .'" />';
		} elseif (version_compare(VERSION, '1.5.1.3', '=') == true) {
			$this->data['add_address_button_html'] 		= '<a target="_top" onclick="location.href=\''. $this->data['add_address'] .'\'; parent.$.fn.colorbox.close();" class="button151"><span>'. $this->data['button_add_address'] .'</span></a>';
			$this->data['update_address_button_html'] 	= '<a id="updateAddress" onclick="updateAddress();" class="button151"><span>' . $this->data['button_continue'] . '</span></a>';
			$this->data['comment_button_html'] 			= '<a onclick="updateComment();" class="button151"><span>'. $this->data['button_comment'] . '</span></a>';
			$this->data['coupon_button_html'] 			= '<a onclick="updateCouponPaymentTotals();" class="button151"><span>'. $this->data['button_coupon'] .'</span></a>';
			$this->data['voucher_button_html'] 			= '<a onclick="updateVoucherPaymentTotals();" class="button151"><span>'. $this->data['button_voucher'] .'</span></a>';
			$this->data['agree_button_html'] 			= '<a onclick="alert(\''. $this->data['text_must_agree'] .'\'); return false;" class="button151"><span>'. $this->data['button_confirm'] .'</span></a>';
		} else {
			$this->data['add_address_button_html'] 		= '<a target="_top" onclick="location.href=\''. $this->data['add_address'] .'\'; parent.$.fn.colorbox.close();" class="button"><span>'. $this->data['button_add_address'] .'</span></a>';
			$this->data['update_address_button_html'] 	= '<a id="updateAddress" onclick="updateAddress();" class="button"><span>' . $this->data['button_continue'] . '</span></a>';
			$this->data['comment_button_html'] 			= '<a onclick="updateComment();" class="button"><span>'. $this->data['button_comment'] . '</span></a>';
			$this->data['coupon_button_html'] 			= '<a onclick="updateCouponPaymentTotals();" class="button"><span>'. $this->data['button_coupon'] .'</span></a>';
			$this->data['voucher_button_html'] 			= '<a onclick="updateVoucherPaymentTotals();" class="button"><span>'. $this->data['button_voucher'] .'</span></a>';
			$this->data['agree_button_html'] 			= '<a onclick="alert(\''. $this->data['text_must_agree'] .'\'); return false;" class="button"><span>'. $this->data['button_confirm'] .'</span></a>';
		}
		
		//if (!$this->cart->hasProducts() || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
	  		$this->redirect($this->url->link('checkout/cart'));
    	}

    	if (!$this->customer->isLogged() && !isset($this->session->data['guest']['payment'])) {
			$this->session->data['redirect'] = $this->url->link('checkout/checkout_two', '', 'SSL');
	  		$this->redirect($this->url->link('checkout/checkout_one', '', 'SSL'));
		}

		if (isset($this->session->data['uc_address_change'])) {
			unset($this->session->data['uc_address_change']);
			$this->data['showAddressSelect'] = true;
		}

		$this->load->model('setting/extension');

		$this->load->model('account/address');
		if ($this->customer->isLogged()) {
			$this->data['addresses'] = $this->model_account_address->getAddresses();
		} else {
			$this->data['addresses'] = array();
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->breadcrumbs = array();

      	$this->document->breadcrumbs[] = array(
        	'href'      => $this->url->link('common/home'),
        	'text'      => $this->language->get('text_home'),
        	'separator' => FALSE
      	);

      	$this->document->breadcrumbs[] = array(
        	'href'      => $this->url->link('checkout/cart'),
        	'text'      => $this->language->get('text_basket'),
        	'separator' => $this->language->get('text_separator')
      	);

      	if (isset($this->session->data['guest'])) {
      		$this->document->breadcrumbs[] = array(
        		'href'      => $this->url->link('checkout/checkout_one', '', 'SSL'),
        		'text'      => $this->language->get('text_register'),
        		'separator' => $this->language->get('text_separator')
      		);
		}

      	$this->document->breadcrumbs[] = array(
        	'href'      => $this->url->link('checkout/checkout_two', '', 'SSL'),
        	'text'      => $this->language->get('text_confirm'),
        	'separator' => $this->language->get('text_separator')
      	);

		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['breadcrumbs'] = $this->document->breadcrumbs;

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

///////
		# Get Totals
		$total = 0;
		$total_data = array();
		$this->getTotals($total, $total_data);
    	//

    	$this->addOrder();

/////////


		if ($this->cart->hasShipping()) {
			if ($this->customer->isLogged() && !isset($this->session->data['shipping_address_id'])) {
				$this->session->data['shipping_address_id'] = $this->customer->getAddressId();
			}

			if ($this->customer->isLogged() && isset($this->session->data['shipping_address_id'])) {
				$shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
				$this->data['shipping_address_id'] = $this->session->data['shipping_address_id'];
			} elseif (isset($this->session->data['guest']['shipping'])) {
				$shipping_address = $this->session->data['guest']['shipping'];
			} elseif (isset($this->session->data['guest']['payment'])) {
				$shipping_address = $this->session->data['guest']['payment'];
			}

			if (!isset($shipping_address['firstname'])) {
				$shipping_address = reset($this->data['addresses']);
			}

			$this->session->data['shipping_country_id'] = $shipping_address['country_id'];
			$this->session->data['shipping_zone_id'] = $shipping_address['zone_id'];
			$this->session->data['shipping_postcode'] = $shipping_address['postcode'];

			if ($shipping_address['address_format']) {
      			$format = $shipping_address['address_format'];
    		} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
			}

    		$find = array(
	  			'{firstname}',
	  			'{lastname}',
	  			'{company}',
      			'{address_1}',
      			'{address_2}',
     			'{city}',
      			'{postcode}',
      			'{zone}',
				'{zone_code}',
      			'{country}'
			);

			$replace = array(
	  			'firstname' => $shipping_address['firstname'],
	  			'lastname'  => $shipping_address['lastname'],
	  			'company'   => $shipping_address['company'],
      			'address_1' => $shipping_address['address_1'],
      			'address_2' => $shipping_address['address_2'],
      			'city'      => $shipping_address['city'],
      			'postcode'  => $shipping_address['postcode'],
      			'zone'      => $shipping_address['zone'],
				'zone_code' => $shipping_address['zone_code'],
      			'country'   => $shipping_address['country']
			);

			$this->data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
		} else {
			$this->data['shipping_address'] = '';
		}

		if (isset($this->session->data['shipping_methods'])) {
			$this->data['shipping_methods'] = $this->session->data['shipping_methods'];
		} else {
			$this->data['shipping_methods'] = array();
		}

		if (isset($this->session->data['shipping_method'])) {
			$this->data['shipping_method'] = $this->session->data['shipping_method'];
		} else {
			$this->data['shipping_method'] = '';
		}

		if (isset($this->session->data['shipping_method']['code'])) {
			$this->data['shipping_code'] = $this->session->data['shipping_method']['code'];
		} else {
			$this->data['shipping_code'] = '';
		}

    	if ($this->customer->isLogged() && !isset($this->session->data['payment_address_id'])) {
			$this->session->data['payment_address_id'] = $this->customer->getAddressId();
		}

		if ($this->customer->isLogged() && isset($this->session->data['payment_address_id'])) {
			$payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
			$this->data['payment_address_id'] = $this->session->data['payment_address_id'];
		} elseif (isset($this->session->data['guest']['payment'])) {
			$payment_address = $this->session->data['guest']['payment'];
		}

		if (!isset($payment_address['firstname'])) {
			$payment_address = reset($this->data['addresses']);
		}

		$this->session->data['payment_country_id'] = $payment_address['country_id'];
		$this->session->data['payment_zone_id'] = $payment_address['zone_id'];
		$this->session->data['payment_postcode'] = $payment_address['postcode'];

		if ($payment_address) {
			if ($payment_address['address_format']) {
      			$format = $payment_address['address_format'];
    		} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
			}

    		$find = array(
	  			'{firstname}',
	  			'{lastname}',
	  			'{company}',
      			'{address_1}',
      			'{address_2}',
     			'{city}',
      			'{postcode}',
      			'{zone}',
				'{zone_code}',
      			'{country}'
			);

			$replace = array(
	  			'firstname' => $payment_address['firstname'],
	  			'lastname'  => $payment_address['lastname'],
	  			'company'   => $payment_address['company'],
      			'address_1' => $payment_address['address_1'],
      			'address_2' => $payment_address['address_2'],
      			'city'      => $payment_address['city'],
      			'postcode'  => $payment_address['postcode'],
      			'zone'      => $payment_address['zone'],
				'zone_code' => $payment_address['zone_code'],
      			'country'   => $payment_address['country']
			);

			$this->data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
		} else {
			$this->data['payment_address'] = '';
		}

		if (isset($this->session->data['payment_method']['code'])) {
			$this->data['payment_code'] = $this->session->data['payment_method']['code'];
		} else {
			$this->data['payment_code'] = '';
		}

    	$this->data['checkout_payment'] = $this->url->link('checkout/payment', '', 'SSL');

    	$this->data['checkout_payment_address'] = $this->url->link('checkout/checkout_one', '', 'SSL');

    	$this->data['products'] = array();

    	foreach ($this->cart->getProducts() as $product) {
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

			$this->data['products'][] = array(
				'product_id' => $product['product_id'],
				'name'       => $product['name'],
				'model'      => $product['model'],
				'option'     => $option_data,
				'quantity'   => $product['quantity'],
				'subtract'   => $product['subtract'],
				'price'      => $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'))),
				'total'      => $this->currency->format($this->tax->calculate($product['total'], $product['tax_class_id'], $this->config->get('config_tax'))),
				'href'       => $this->url->link('product/product', 'product_id=' . $product['product_id'])
			);
		}

		// Gift Voucher
		$this->data['vouchers'] = array();

		if (!empty($this->session->data['vouchers'])) {
			foreach ($this->session->data['vouchers'] as $voucher) {
				$this->data['vouchers'][] = array(
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'])
				);
			}
		}

		# Get Totals
		$total = 0;
		$total_data = array();
		$this->getTotals($total, $total_data);
    	//

		$this->data['totals'] = $total_data;

		if (isset($this->request->post['comment'])) {
			$this->data['comment'] = $this->request->post['comment'];
			$this->session->data['comment'] = $this->request->post['comment'];
		} elseif (isset($this->session->data['comment'])) {
			$this->data['comment'] = nl2br($this->session->data['comment']);
		} else {
			$this->data['comment'] = '';
		}

		if ($this->config->get('config_checkout_id')) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));

			if ($information_info) {
				$this->data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/info', 'information_id=' . $this->config->get('config_checkout_id'), '', 'SSL'), $information_info['title'], $information_info['title']);
			} else {
				$this->data['text_agree'] = '';
			}
		} else {
			$this->data['text_agree'] = '';
		}

		if (isset($this->session->data['agree'])) {
			$this->data['agree'] = $this->session->data['agree'];
		} else {
			$this->data['agree'] = '';
		}

		$this->data['coupon_status'] = $this->config->get('coupon_status');
		$this->data['voucher_status'] = $this->config->get('voucher_status');

		$this->data['action'] = $this->url->link('checkout/checkout_two', '', 'SSL');

		if (isset($this->request->post['coupon'])) {
			$this->data['coupon'] = $this->request->post['coupon'];
		} elseif (isset($this->session->data['coupon'])) {
			$this->data['coupon'] = $this->session->data['coupon'];
		} else {
			$this->data['coupon'] = '';
		}

		if (isset($this->request->post['voucher'])) {
			$this->data['voucher'] = $this->request->post['voucher'];
		} elseif (isset($this->session->data['voucher'])) {
			$this->data['voucher'] = $this->session->data['voucher'];
		} else {
			$this->data['voucher'] = '';
		}

		$this->data['payment'] = $this->getChild('payment/' . $this->session->data['payment_method']['code']);

		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/checkout_two.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/checkout_two.tpl';
		} else {
			$this->template = 'default/template/checkout/checkout_two.tpl';
		}

		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);

		//$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
		$this->response->setOutput($this->render());
  	}

  	private function addOrder() {
  		$this->load->model('account/address');
  		$this->load->model('setting/extension');
  		$data = array();

  		if ($this->customer->isLogged()) {
			$data['customer_id'] = $this->customer->getId();
			$data['customer_group_id'] = $this->customer->getCustomerGroupId();
			$data['firstname'] = $this->customer->getFirstName();
			$data['lastname'] = $this->customer->getLastName();
			$data['email'] = $this->customer->getEmail();
			$data['telephone'] = $this->customer->getTelephone();
			$data['fax'] = $this->customer->getFax();
			//$this->load->model('account/address');
			//$payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
		} elseif (isset($this->session->data['guest'])) {
			$data['customer_id'] = 0;
			$data['customer_group_id'] = $this->session->data['guest']['customer_group_id'];
			$data['firstname'] = $this->session->data['guest']['firstname'];
			$data['lastname'] = $this->session->data['guest']['lastname'];
			$data['email'] = $this->session->data['guest']['email'];
			$data['telephone'] = $this->session->data['guest']['telephone'];
			$data['fax'] = $this->session->data['guest']['fax'];
			//$payment_address = $this->session->data['guest']['payment'];
		}

		$data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
		$data['store_id'] = $this->config->get('config_store_id');
		$data['store_name'] = $this->config->get('config_name');
		$data['store_url'] = $this->config->get('config_url');

		if ($this->cart->hasShipping()) {

			if ($this->customer->isLogged() && !isset($this->session->data['shipping_address_id'])) {
				$this->session->data['shipping_address_id'] = $this->customer->getAddressId();
			}

			if ($this->customer->isLogged() && isset($this->session->data['shipping_address_id'])) {
				$shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
			} elseif (isset($this->session->data['guest']['shipping'])) {
				$shipping_address = $this->session->data['guest']['shipping'];
			} elseif (isset($this->session->data['guest']['payment'])) {
				$shipping_address = $this->session->data['guest']['payment'];
			}

			$this->load->model('account/address');
			if ($this->customer->isLogged()) {
				$addresses = $this->model_account_address->getAddresses();
			} else {
				$addresses = array();
			}

			// Get first address if default address not found
			if (!isset($shipping_address['firstname'])) {
				$shipping_address = reset($addresses);
			}

			$this->session->data['shipping_country_id'] = $shipping_address['country_id'];
			$this->session->data['shipping_zone_id'] = $shipping_address['zone_id'];
			$this->session->data['shipping_postcode'] = $shipping_address['postcode'];

			# Get All Available Shipping Methods
			//if (!isset($this->session->data['shipping_methods']) || (!$this->config->get('config_shipping_session') && !isset($this->request->post['shipping_refresh']))) {
				$quote_data = array();

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

				$sort_order = array();

				foreach ($quote_data as $key => $value) {
					$sort_order[$key] = $value['sort_order'];
				}

				array_multisort($sort_order, SORT_ASC, $quote_data);

				$this->session->data['shipping_methods'] = $quote_data;
			//}

			// If no shipping methods available, show warning on checkout page
			if ($this->cart->hasShipping() && empty($this->session->data['shipping_methods'])) {
				$this->error['warning'] = $this->language->get('error_shipping_methods');
			}//

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

			# Change Current Shipping - Probably don't need anymore
			if (isset($this->request->post['shipping_method'])) {
				$shipping = explode('.', $this->request->post['shipping_method']);

				$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
			}

			# Set Default Shipping Method
			if (empty($this->session->data['shipping_method'])) {
				foreach($this->session->data['shipping_methods'] as $k => $v){
					$first_rate = array_keys($v['quote']);
					if (isset($first_rate[0]) && isset($v['quote'][$first_rate[0]])) {
						$this->session->data['shipping_method'] = $v['quote'][$first_rate[0]];
						break;
					}
				}
			}

			if (!empty($this->session->data['shipping_method']['title'])) {
				$data['shipping_method'] = $this->session->data['shipping_method']['title'];
			} else {
				$data['shipping_method'] = '';
			}

			if (!empty($this->session->data['shipping_method']['code'])) {
				$data['shipping_code'] = $this->session->data['shipping_method']['code'];
			} else {
				$data['shipping_code'] = '';
			}

			if (method_exists($this->tax, 'setZone')) {
				$this->tax->setZone($shipping_address['country_id'], $shipping_address['zone_id']);
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
			$data['shipping_code'] = '';

			unset($this->session->data['shipping_address_id']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);

			if (method_exists($this->tax, 'setZone')) {
				$this->tax->setZone($this->config->get('config_country_id'), $this->config->get('config_zone_id'));
			}
		}

		# Get Payment Details
		if ($this->customer->isLogged() && !isset($this->session->data['payment_address_id'])) {
			$this->session->data['payment_address_id'] = $this->customer->getAddressId();
		}

		if ($this->customer->isLogged() && isset($this->session->data['payment_address_id'])) {
			$payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);
		} elseif (isset($this->session->data['guest']['payment'])) {
			$payment_address = $this->session->data['guest']['payment'];
		}

		$this->load->model('account/address');
		if ($this->customer->isLogged()) {
			$addresses = $this->model_account_address->getAddresses();
		} else {
			$addresses = array();
		}

		// Get first address if default address not found
		if (!isset($payment_address['firstname'])) {
			$payment_address = reset($addresses);
		}

		$data['payment_firstname'] = $payment_address['firstname'];
		$data['payment_lastname'] = $payment_address['lastname'];
		$data['payment_company'] = $payment_address['company'];
		if (isset($payment_address['company_id'])) {
			$data['payment_company_id'] = $payment_address['company_id']; // v153
			$data['payment_tax_id'] = $payment_address['tax_id']; // v153
		}
		$data['payment_address_1'] = $payment_address['address_1'];
		$data['payment_address_2'] = $payment_address['address_2'];
		$data['payment_city'] = $payment_address['city'];
		$data['payment_postcode'] = $payment_address['postcode'];
		$data['payment_zone'] = $payment_address['zone'];
		$data['payment_zone_id'] = $payment_address['zone_id'];
		$data['payment_country'] = $payment_address['country'];
		$data['payment_country_id'] = $payment_address['country_id'];
		$data['payment_address_format'] = $payment_address['address_format'];

		# Get Totals
		$total = 0;
		$total_data = array();
		$this->getTotals($total, $total_data);
    	//

		# Get All Available Payment Methods
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

		// If working from site.com/store1 to site.com/store2 and have different payments, prevent error
		if (!isset($this->session->data['payment_method']['code']) || !isset($this->session->data['payment_methods'][$this->session->data['payment_method']['code']])) {
			$this->session->data['payment_method'] = reset($this->session->data['payment_methods']);
		}

		// If no payment methods available, show warning on checkout page.
		if (empty($this->session->data['payment_methods'])) {
			$this->error['warning'] = $this->language->get('error_payment_methods');
		}//

		$this->data['payment_methods'] = $this->session->data['payment_methods'];

		# Change Current Payment - Probably dont need anymore
		if(isset($this->request->post['payment_method'])){
			$this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method']];
		}

		# Set Default Payment Method
		if (empty($this->session->data['payment_method'])) {
			foreach($this->session->data['payment_methods'] as $payment_method){
				$this->session->data['payment_method'] = $payment_method;
				break;
			}
		}

		if (isset($this->session->data['payment_method'])) {
			$data['payment_method'] = $this->session->data['payment_method']['title'];
		} else {
			$data['payment_method'] = '';
		}

		if (isset($this->session->data['payment_method']['code'])) {
			$data['payment_code'] = $this->session->data['payment_method']['code'];
		} else {
			$data['payment_code'] = '';
		}

		if (method_exists($this->tax, 'setZone')) {
			$this->tax->setZone($payment_address['country_id'], $payment_address['zone_id']);
		}

		if (isset($this->error['warning'])) {
    		$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}


		# Get Products
		$product_data = array();

		foreach ($this->cart->getProducts() as $product) {
      		$option_data = array();

			foreach ($product['option'] as $option) {
				if ($option['type'] != 'file') {
					$value = $option['option_value'];
				} else {
					$value = $this->encryption->decrypt($option['option_value']);
				}

				$option_data[] = array(
					'product_option_id'       => $option['product_option_id'],
					'product_option_value_id' => $option['product_option_value_id'],
					'option_id'               => $option['option_id'],
					'option_value_id'         => $option['option_value_id'],
					'name'                    => $option['name'],
					'value'                   => $value,
					'type'                    => $option['type']
				);
			}

			if (method_exists($this->tax, 'getTax')) { // v1513 or later
				$xtax = $this->tax->getTax($product['price'], $product['tax_class_id']);
			} elseif (method_exists($this->tax, 'getRates')) {// v151 to v1512
				$xtaxes = $this->tax->getRates($product['total'], $product['tax_class_id']);
				$xtax = 0;
				foreach ($xtaxes as $x) {
					$xtax += $x['amount'];
				}
			} else {// v150
				$xtax = $this->tax->getRate($product['tax_class_id']);
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
				//'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
				'tax'        => $xtax,
				'reward'     => $product['reward']
			);
    	}


		// Gift Voucher
		$voucher_data = array();

		if (!empty($this->session->data['vouchers'])) {
			foreach ($this->session->data['vouchers'] as $voucher) {
				$voucher_data[] = array(
					'description'      => $voucher['description'],
					'code'             => substr(md5(mt_rand()), 0, 10),
					'to_name'          => $voucher['to_name'],
					'to_email'         => $voucher['to_email'],
					'from_name'        => $voucher['from_name'],
					'from_email'       => $voucher['from_email'],
					'voucher_theme_id' => $voucher['voucher_theme_id'],
					'message'          => $voucher['message'],
					'amount'           => $voucher['amount']
				);
			}
		}

		# Get Totals
		$total = 0;
		$total_data = array();
		$this->getTotals($total, $total_data);
    	//


		$data['products'] = $product_data;
		$data['vouchers'] = $voucher_data;
		$data['totals'] = $total_data;
		$data['total'] = $total;
		if (method_exists($this->cart, 'getTotalRewardPoints')) {
			$data['reward'] = $this->cart->getTotalRewardPoints();
		} else {
			$data['reward'] = 0;
		}

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


		$data['comment'] = (!empty($this->session->data['comment'])) ? $this->session->data['comment'] : '';
		$data['language_id'] = $this->config->get('config_language_id');
		$data['currency_id'] = $this->currency->getId();
		$data['currency_code'] = $this->currency->getCode();
		$data['currency_value'] = $this->currency->getValue($this->currency->getCode());

		if (isset($this->session->data['coupon'])) {
			$this->load->model('checkout/coupon');

			$coupon = $this->model_checkout_coupon->getCoupon($this->session->data['coupon']);

			if ($coupon) {
				$data['coupon_id'] = $coupon['coupon_id'];
			} else {
				$data['coupon_id'] = 0;
			}
		} else {
			$data['coupon_id'] = 0;
		}

		if (isset($this->session->data['voucher'])) {
			$this->load->model('checkout/voucher');

			$voucher = $this->model_checkout_voucher->getVoucher($this->session->data['voucher']);

			if ($voucher) {
				$data['voucher_id'] = $voucher['voucher_id'];
			} else {
				$data['voucher_id'] = 0;
			}
		} else {
			$data['voucher_id'] = 0;
		}

		$data['ip'] = $this->request->server['REMOTE_ADDR'];

		if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
			$data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
		} elseif(!empty($this->request->server['HTTP_CLIENT_IP'])) {
			$data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
		} else {
			$data['forwarded_ip'] = '';
		}

		if (isset($this->request->server['HTTP_USER_AGENT'])) {
			$data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
		} else {
			$data['user_agent'] = '';
		}

		if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
			$data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
		} else {
			$data['accept_language'] = '';
		}

		$this->load->model('checkout/order');

		if (method_exists($this->model_checkout_order, 'addOrder')) {
			$this->session->data['order_id'] = $this->model_checkout_order->addOrder($data);
		} else {
			$this->session->data['order_id'] = $this->model_checkout_order->create($data);
		}
	}

	private function validateCoupon() {

  		$this->load->model('checkout/coupon');

		$this->language->load('checkout/checkout_two');

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

  	private function validateVoucher() {

  		$this->load->model('checkout/voucher');

		$this->language->load('checkout/checkout_two');

		$voucher = $this->model_checkout_voucher->getVoucher($this->request->post['voucher']);

		if (!$voucher) {
			$this->error['warning'] = $this->language->get('error_voucher');
		}

  		if (!$this->error) {
	  		return TRUE;
		} else {
	  		return FALSE;
		}
  	}

  	public function newAddress() {
		$this->session->data['uc_address_change'] = true;

		$this->redirect($this->url->link('account/address/insert', '', 'SSL'));
	}

	public function payment() {
		if (!empty($this->request->post['payment_method'])) {

			$this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method']];

			$this->addOrder(); // Always generate new order when totals update

			$payment = $this->getChild('payment/' . $this->session->data['payment_method']['code']);

			echo $payment;
		}
	}

	public function totals() {
		$total = 0;
		$total_data = array();
		$this->getTotals($total, $total_data);

		$html  = '<table style="float: right; display: inline-block; padding-right:5px;">';
        foreach ($total_data as $total) {
        	$html .= '<tr>';
    		$html .= '  <td align="right"><b>'.$total['title'].':</b></td>';
        	$html .= '  <td align="right">'.$total['text'].'</td>';
        	$html .= '</tr>';
		}
		$html .= '</table>';

		echo $html;
	}


	// Combined single callback function to avoid multiple ajax callbacks for shipping changes
	public function shippingPaymentTotals() {

		$json = array();

		// Shipping
		if (!empty($this->request->post['shipping_method'])) {
			$shipping = explode('.', $this->request->post['shipping_method']);
			$this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
		}

		if (!$this->config->get('uber_checkout_shipping_update_payment')) {
				
			
			// Payment
			if (!empty($this->request->post['payment_method'])) {
				$this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method']];
			}


			// Totals
			$total = 0;
			$total_data = array();
			$this->getTotals($total, $total_data);

			$html  = '<table style="float: right; display: inline-block; padding-right:5px;">';
			foreach ($total_data as $total) {
				$html .= '<tr>';
				$html .= '  <td align="right"><b>'.$total['title'].':</b></td>';
				$html .= '  <td align="right">'.$total['text'].'</td>';
				$html .= '</tr>';
			}
			$html .= '</table>';
			$json['totals'] = $html;

			// Refresh the temp order
			$this->addOrder();
		}
		
		if ($this->config->get('uber_checkout_shipping_update_payment')) {
			$json['reload'] = '1';
		} else {
			$json['payment'] = $this->getChild('payment/' . $this->session->data['payment_method']['code']);
		}

		$this->response->setOutput(json_encode($json));
	}

	public function couponPaymentTotals() {
	   	if (isset($this->request->post['coupon'])) {

	   	 	$this->language->load('checkout/checkout_two');

	   	 	$json = array();

	   	 	if (isset($this->request->post['coupon'])) {
	   	 		if ($this->request->post['coupon'] && $this->validateCoupon()) {
					$this->session->data['coupon'] = $this->request->post['coupon'];
					$json['success_coupon'] = $this->language->get('text_success_coupon');
					$this->addOrder(); // Always generate new order when totals update
				} else {
					// if empty, unset it with no error
					if ($this->request->post['coupon'] == "" && !empty($this->session->data['coupon']) && $this->session->data['coupon'] != $this->request->post['coupon']) {
						unset($this->session->data['coupon']);
						$json['success_coupon'] = $this->language->get('text_coupon_removed');
					} elseif ($this->request->post['coupon'] != "") {
						$json['fail_coupon'] = $this->language->get('error_coupon');
					}
				}
			}

			// Payment
			if (!empty($this->request->post['payment_method'])) {
				$this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method']];
			}
			$json['payment'] = $this->getChild('payment/' . $this->session->data['payment_method']['code']);

			// Totals
			$total = 0;
			$total_data = array();
			$this->getTotals($total, $total_data);

			$html  = '<table style="float: right; display: inline-block; padding-right:5px;">';
			foreach ($total_data as $total) {
				$html .= '<tr>';
				$html .= '  <td align="right"><b>'.$total['title'].':</b></td>';
				$html .= '  <td align="right">'.$total['text'].'</td>';
				$html .= '</tr>';
			}
			$html .= '</table>';
			$json['totals'] = $html;

			// Refresh the temp order
			$this->addOrder();

			$this->response->setOutput(json_encode($json));
		}
	}

	public function voucherPaymentTotals() {
	   	if (isset($this->request->post['voucher'])) {

	   	 	$this->language->load('checkout/checkout_two');

	   	 	$json = array();

			if (!empty($this->request->post['voucher'])) {
				if ($this->request->post['voucher'] && $this->validateVoucher()) {
					$this->session->data['voucher'] = $this->request->post['voucher'];
					$json['success_voucher'] = $this->language->get('text_success_voucher');
					$this->addOrder(); // Always generate new order when totals update
				} else {
					// if empty, unset it with no error
					//if ($this->request->post['voucher'] == "" && !empty($this->session->data['voucher'])) {
					if ($this->request->post['voucher'] == "" && !empty($this->session->data['voucher']) && $this->session->data['voucher'] != $this->request->post['voucher']) {
						unset($this->session->data['voucher']);
						$json['success_voucher'] = $this->language->get('text_voucher_removed');
					} elseif ($this->request->post['voucher'] != "") {
						$json['fail_voucher'] = $this->language->get('error_voucher');
					}
				}
			}

			// Payment
			if (!empty($this->request->post['payment_method'])) {
				$this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method']];
			}
			$json['payment'] = $this->getChild('payment/' . $this->session->data['payment_method']['code']);

			// Totals
			$total = 0;
			$total_data = array();
			$this->getTotals($total, $total_data);

			$html  = '<table style="float: right; display: inline-block; padding-right:5px;">';
			foreach ($total_data as $total) {
				$html .= '<tr>';
				$html .= '  <td align="right"><b>'.$total['title'].':</b></td>';
				$html .= '  <td align="right">'.$total['text'].'</td>';
				$html .= '</tr>';
			}
			$html .= '</table>';
			$json['totals'] = $html;

			// Refresh the temp order
			$this->addOrder();

			$this->response->setOutput(json_encode($json));
		}
	}

	public function comment() {
	   	if (isset($this->request->post['comment'])) {
	   		$this->language->load('checkout/checkout_two');

			$this->session->data['comment'] = $this->request->post['comment'];

			$json = array();

			$json['success'] = $this->language->get('text_success_comment');

			$this->addOrder(); // Always generate new order when totals update

			$this->response->setOutput(json_encode($json));
		}
	}

	public function address() {
		$json = array();

		unset($this->session->data['shipping_methods']);
		unset($this->session->data['shipping_method']);
		unset($this->session->data['payment_methods']);
		unset($this->session->data['payment_method']);

		if (isset($this->request->post['payment_address_id'])) {
	   		$this->load->model('account/address');

	   		$payment_address = $this->model_account_address->getAddress((int)$this->request->post['payment_address_id']);

	   		if ($payment_address) {

	   			$this->session->data['payment_address_id'] = (int)$this->request->post['payment_address_id'];
				$this->session->data['payment_country_id'] = $payment_address['country_id'];
				$this->session->data['payment_zone_id'] = $payment_address['zone_id'];
				$this->session->data['payment_postcode'] = $payment_address['postcode'];

				if ($payment_address['address_format']) {
      				$format = $payment_address['address_format'];
    			} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
				}

    			$find = array(
	  				'{firstname}',
	  				'{lastname}',
	  				'{company}',
      				'{address_1}',
      				'{address_2}',
     				'{city}',
      				'{postcode}',
      				'{zone}',
					'{zone_code}',
      				'{country}'
				);

				$replace = array(
	  				'firstname' => $payment_address['firstname'],
	  				'lastname'  => $payment_address['lastname'],
	  				'company'   => $payment_address['company'],
      				'address_1' => $payment_address['address_1'],
      				'address_2' => $payment_address['address_2'],
      				'city'      => $payment_address['city'],
      				'postcode'  => $payment_address['postcode'],
      				'zone'      => $payment_address['zone'],
					'zone_code' => $payment_address['zone_code'],
      				'country'   => $payment_address['country']
				);

				$json['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
			} else {
				$json['payment_address'] = 'N/A';
			}
		}

		if (isset($this->request->post['shipping_address_id'])) {
	   		$this->load->model('account/address');

	   		$shipping_address = $this->model_account_address->getAddress((int)$this->request->post['shipping_address_id']);

	   		if ($shipping_address) {

	   			$this->session->data['shipping_address_id'] = (int)$this->request->post['shipping_address_id'];
	   			$this->session->data['shipping_country_id'] = $shipping_address['country_id'];
				$this->session->data['shipping_zone_id'] = $shipping_address['zone_id'];
				$this->session->data['shipping_postcode'] = $shipping_address['postcode'];

				if ($shipping_address['address_format']) {
      				$format = $shipping_address['address_format'];
    			} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
				}

    			$find = array(
	  				'{firstname}',
	  				'{lastname}',
	  				'{company}',
      				'{address_1}',
      				'{address_2}',
     				'{city}',
      				'{postcode}',
      				'{zone}',
					'{zone_code}',
      				'{country}'
				);

				$replace = array(
	  				'firstname' => $shipping_address['firstname'],
	  				'lastname'  => $shipping_address['lastname'],
	  				'company'   => $shipping_address['company'],
      				'address_1' => $shipping_address['address_1'],
      				'address_2' => $shipping_address['address_2'],
      				'city'      => $shipping_address['city'],
      				'postcode'  => $shipping_address['postcode'],
      				'zone'      => $shipping_address['zone'],
					'zone_code' => $shipping_address['zone_code'],
      				'country'   => $shipping_address['country']
				);

				$json['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
			} else {
				$json['shipping_address'] = 'N/A';
			}
		} elseif ($this->config->get('uber_checkout_no_ship_address')) { // override shipping address with payment address if no ship flag is set
			$this->session->data['shipping_address_id'] = $this->session->data['payment_address_id'];
			$json['shipping_address'] = $json['payment_address'];
		}
		$this->response->setOutput(json_encode($json));
	}

	private function getTotals(&$total, &$total_data) {
		# Get Totals
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
    	//
	}
}
?>