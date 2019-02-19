<?php
//-----------------------------------------
// Author: Qphoria@gmail.com
// Web: http://www.unbannable.com/ocstore
// 14x and 15x version
//-----------------------------------------
class ControllerPaymentPaypalExpressNew extends Controller {

	var $errors = array();

	protected function index() {

		$this->load->language('payment/paypal_express_new');

    	$this->data['button_continue'] = $this->language->get('button_continue');
		$this->data['button_confirm'] = $this->language->get('button_confirm');
		$this->data['button_back'] = $this->language->get('button_back');
		$store_url = ($this->config->get('config_ssl') ? (is_numeric($this->config->get('config_ssl'))) ? str_replace('http', 'https', $this->config->get('config_url')) : $this->config->get('config_ssl') : $this->config->get('config_url'));
		$this->data['error_agree'] = '';

		if ($this->config->get('config_checkout_id')) {

			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));

			if ($information_info) {
				$this->data['error_agree'] = sprintf($this->language->get('error_agree'), $information_info['title']);
			}

			if ($information_info) {
				if (method_exists($this->document, 'addBreadcrumb')) { //1.4.x
					//$this->data['text_ppx_agree'] = sprintf($this->language->get('text_ppx_agree'), ($store_url . 'index.php?route=information/information&information_id=' . $this->config->get('config_checkout_id')), $information_info['title'], $information_info['title']);
					$this->data['text_ppx_agree'] = '';
				} else {
					$this->data['text_ppx_agree'] = sprintf($this->language->get('text_ppx_agree'), ($store_url . 'index.php?route=information/information/info&information_id=' . $this->config->get('config_checkout_id')), $information_info['title'], $information_info['title']);
				}
			} else {
				$this->data['text_ppx_agree'] = '';
			}
		} else {
			$this->data['text_ppx_agree'] = '';
		}

		if (isset($this->session->data['agree'])) {
			$this->data['agree'] = $this->session->data['agree'];
		} else {
			$this->data['agree'] = '';
		}

		// form action processes on this page
		$this->data['action'] = ($store_url . 'index.php?route=payment/paypal_express_new/DoExpressCheckoutPayment');

		$this->data['fields'] = array();

		$this->data['error'] = (isset($this->session->data['error'])) ? $this->session->data['error'] : NULL;
		unset($this->session->data['error']);

		$this->id       = 'payment';

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/paypal_express_new.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/paypal_express_new.tpl';
        } else {
            $this->template = 'default/template/payment/paypal_express_new.tpl';
        }

		$this->render();
	}

	public function confirm() {
		
		return;
	}

	public function SetExpressCheckout() {
		$store_url = ($this->config->get('config_ssl') ? (is_numeric($this->config->get('config_ssl'))) ? str_replace('http', 'https', $this->config->get('config_url')) : $this->config->get('config_ssl') : $this->config->get('config_url'));
		// Force reset option. Mainly for testing or if process gets screwed up somehow
		
		// Redirect User if capable of free checout
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
		$voucher_total = 0.00;
		foreach ($total_data as $total) {
			if($total['code']=='total' && $total['value']<=0)
			{
			
				$this->redirect($store_url . 'index.php?route=checkout/checkout&nc=1');
				exit;
			}
			


			


		}


		// End Function


		/* This piece of code proceeds the paypal express checkout onn checkout page */
		unset($this->session->data['is_pp_checkout']);
		if($this->request->get['is_pp_checkout']==1)
		{
			$this->session->data['is_pp_checkout'] = 1;	
		}
		//echo $this->session->data['is_pp_checkout'];exit;
		// End piece of code here
		
		
		if (isset($this->request->get['resetppx'])) {
			unset($this->session->data['ppx']);
		}

		$amt = $this->cart->getTotal();

		//If total = 0 then just return to cart
		if (!$amt) {
            $this->redirect($store_url . 'index.php?route=checkout/cart');
		}

		// Cart check for new shipping request
		if (isset($this->session->data['cart'])) {
			if (!isset($this->session->data['ppx']['cart'])) {
				$this->session->data['ppx']['cart'] = $this->session->data['cart'];
			}
		}
		if (array_diff($this->session->data['ppx']['cart'], $this->session->data['cart'])) {
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['ppx']['payerid']);
		}
		$this->session->data['ppx']['cart'] = $this->session->data['cart'];
	
		//Check if some steps have already been completed and go to the next step
		if (isset($this->session->data['ppx']['payerid'])) {

			// Force PPX Data
			$this->session->data['payment_method'] = array(
				'id'  			=> 'paypal_express_new', //v14x
				'code'  		=> 'paypal_express_new', //v15x
				'title' 		=> 'Paypal Express',
				'sort_order' 	=> '1'
			);

			if (method_exists($this->document, 'addBreadcrumb')) { //1.4.x
				$this->redirect((isset($this->session->data['guest'])) ? (($store_url) . 'index.php?route=checkout/guest_step_3') : (($store_url) . 'index.php?route=checkout/confirm'));
			} else {
				$this->redirect($store_url . 'index.php?route=checkout/ppx_checkout_new');
			}

        } elseif (isset($this->session->data['ppx']['token'])) {
			
        	$this->redirect($store_url . 'index.php?route=payment/paypal_express_new/GetExpressCheckoutDetails');
		}

	    // Check for supported currency, otherwise convert to USD.
        $supported_currencies = array('AUD','CAD','EUR','GBP','JPY','USD','NZD','CHF','HKD','SGD','SEK','DKK','PLN','NOK','HUF','CZK','ILS','MXN');
        if (in_array($this->session->data['currency'], $supported_currencies)) {
            $currency = $this->session->data['currency'];
        } else {
            $currency = 'USD';
        }

	    $this->session->data['ppx']['amt'] = $amt;
	    $this->session->data['ppx']['currency'] = $currency;

	    $data = array ();
		$data['METHOD'] 		= 'SetExpressCheckout';
		$data['USER'] 			= trim($this->config->get('paypal_express_new_apiuser'));
		$data['PWD'] 			= trim($this->config->get('paypal_express_new_apipass'));
		$data['SIGNATURE'] 		= trim($this->config->get('paypal_express_new_apisig'));
		$data['VERSION'] 		= '60.0';
		$data['PAYMENTACTION'] 	= ($this->config->get('paypal_express_new_payment_action')) ? $this->config->get('paypal_express_new_payment_action') : 'Sale';
		$data['CURRENCYCODE']	= $currency;
		$data['RETURNURL'] 		= ($store_url . 'index.php?route=payment/paypal_express_new/GetExpressCheckoutDetails');
		$data['CANCELURL'] 		= ($store_url . 'index.php?route=checkout/cart');
		// $data['NOTIFYURL'] 		= ($store_url . 'index.php?route=payment/paypal_express_new/callback');
		// $data['NOTIFYURL'] 		= 'https://imp.phonepartsusa.com/crons/paypal_ipn_callback.php';

		if ($this->config->get('paypal_express_new_survey')) {
			$data['SURVEYENABLE'] 		= '1';
			$data['SURVEYQUESTION'] 	= 'How did you hear about us?';
			$data['L_SURVEYCHOICE0'] 	= 'Through a friend';
			$data['L_SURVEYCHOICE1'] 	= 'In a newspaper ad';
		}

		if ($this->config->get('paypal_express_new_logo')) {
			$data['HDRIMG'] = $this->config->get('paypal_express_new_logo');
			$data['HDRIMG'] = str_replace('*', $this->config->get('config_store_id'), $data['HDRIMG']); // Dynamic store logo check
		} elseif (strpos($store_url, 'https')) {
			$data['HDRIMG'] = ($store_url . 'image/' . $this->config->get('config_logo'));
		}
		
		$store_name = ($this->config->get('config_name')) ? $this->config->get('config_name') : $this->config->get('config_store');
		$data['BRANDNAME'] 	= $store_name;
		
		$data['SOLUTIONTYPE'] 	= 'Sole';
		$data['LANDINGPAGE'] 	= ($this->config->get('paypal_express_new_landing') ? 'Billing' : 'Login'); // Billing or Login
		
		
		// Use OpenCart shipping address if already chosen while logged in. Also handles autofilling the cc form for non-paypal users
		if (isset($this->session->data['shipping_address_id'])) {
			//$data['NOSHIPPING']		= '1';
			$data['NOSHIPPING']		= '0';
			$this->load->model('account/address');
			$shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);

			$data['SHIPTOSTREET'] 		= $shipping_address['address_1'];
			$data['SHIPTOSTREET2'] 		= $shipping_address['address_2'];
			$data['SHIPTOCITY'] 		= $shipping_address['city'];
			$data['SHIPTOSTATE'] 		= $shipping_address['zone_code'];
			$data['SHIPTOCOUNTRYCODE'] 	= $shipping_address['iso_code_2'];
			$data['SHIPTOZIP'] 			= $shipping_address['postcode'];
			$data['SHIPTOPHONENUM'] 	= $this->customer->getTelephone();
		}

		if ($this->customer->isLogged()) {
			$data['EMAIL'] = $this->customer->getEmail();
		}
		
		// Paypal doesn't believe in standards so this half-ass kluge of country and language is their method
		$locales = array(
			'AU' => 'AU',
			'AT' => 'AT',
			'BE' => 'BE',
			'BR' => 'BR',
			'CA' => 'CA',
			'CH' => 'CH',
			'CN' => 'CN',
			'DE' => 'DE',
			'ES' => 'ES',
			'GB' => 'GB',
			'FR' => 'FR',
			'IT' => 'IT',
			'NL' => 'NL',
			'PL' => 'PL',
			'PT' => 'PT',
			'RU' => 'RU',
			'US' => 'US',
			'DK' => 'da_DK',
			'IL' => 'he_IL',
			'ID' => 'id_ID',
			'JP' => 'jp_JP',
			'NO' => 'no_NO',
			'LT' => 'ru_RU',
			'LV' => 'ru_RU',
			'SE' => 'sv_SE',
			'TH' => 'th_TH',
			'TR' => 'tr_TR',
			'UA' => 'ru_RU'
		);
		
		if (isset($this->session->data['shipping_address_id']) && in_array($data['SHIPTOCOUNTRYCODE'], $locales)) {
			$data['LOCALECODE'] = $locales[$data['SHIPTOCOUNTRYCODE']];
		}
		

		$data = array_merge($data, $this->buildItems($data));
		//$data = implode("&", $data);
		$data = http_build_query($data);
//echo $data;exit;
		$nvp = $this->process_curl(__FUNCTION__, $data);

		if (isset($nvp['ACK']) && ($nvp['ACK'] == 'Success' || $nvp['ACK'] == 'SuccessWithWarning')) {
			if ($this->config->get('paypal_express_new_test')) {
				$ppurl = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
			} else {
				$ppurl = 'https://www.paypal.com/cgi-bin/webscr';
			}
			$ppurl = ($ppurl . '?cmd=_express-checkout&token=' . $nvp['TOKEN']);
			header("HTTP/1.1 302 Object Moved");
			header("Location: " . $ppurl);
			exit;
		}

	}

	public function GetExpressCheckoutDetails() {
		$store_url = ($this->config->get('config_ssl') ? (is_numeric($this->config->get('config_ssl'))) ? str_replace('http', 'https', $this->config->get('config_url')) : $this->config->get('config_ssl') : $this->config->get('config_url'));
		if (isset($this->request->get['token'])) {
			$token = $this->request->get['token'];
		} elseif (isset($this->session->data['ppx']['token'])) {
			$token = $this->session->data['ppx']['token'];
		} else {
			$this->redirect($store_url . 'index.php?route=payment/paypal_express_new/SetExpressCheckout');
		}

		$this->session->data['ppx']['token'] = $token;

		$data = array();
		$data['METHOD'] 		= 'GetExpressCheckoutDetails';
		$data['USER'] 			= trim($this->config->get('paypal_express_new_apiuser'));
		$data['PWD'] 			= trim($this->config->get('paypal_express_new_apipass'));
		$data['SIGNATURE'] 		= trim($this->config->get('paypal_express_new_apisig'));
		$data['VERSION'] 		= 60.0;
		$data['TOKEN'] 			= $token;
		$data['NOTETEXT'] 		= ((isset($this->request->get['NOTETEXT'])) ? $this->request->get['NOTETEXT'] : '');



		//$data = implode("&", $data);
		$data = http_build_query($data);

		$this->session->data['ppx']['cart'] = $this->session->data['cart'];

		$nvp = $this->process_curl(__FUNCTION__, $data);


		$this->session->data['ppx']['payerid'] 	= $nvp['PAYERID'];

		$this->prepareOrder($nvp);
	}

	public function DoExpressCheckoutPayment() {
		$store_url = ($this->config->get('config_ssl') ? (is_numeric($this->config->get('config_ssl'))) ? str_replace('http', 'https', $this->config->get('config_url')) : $this->config->get('config_ssl') : $this->config->get('config_url'));
		$this->load->language('payment/paypal_express_new');

		if (!isset($this->session->data['ppx']['token'])) {
			unset($this->session->data['ppx']);
			$this->redirect($store_url . 'index.php?route=payment/paypal_express_new/SetExpressCheckout');
		}

		if (!isset($this->session->data['ppx']['payerid'])) {
			$this->redirect($store_url . 'index.php?route=payment/paypal_express_new/GetExpressCheckoutDetails');
		}

		if (!isset($this->session->data['order_id'])) {
			$this->log->write("PP EXPRESS ERROR (A): No session order id found");
			$this->fail("No session order id found");
		}

		// If cart changed during this step via the quick cart or other method, update the ppx cart and refresh checkout with notice to rechoose shipping
		if (array_diff($this->session->data['ppx']['cart'], $this->session->data['cart'])) {
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['ppx']['payerid']);
			$this->session->data['ppx']['cart'] = $this->session->data['cart'];
			$this->session->data['error'] = $this->language->get('text_cart_contents');
			$this->log->write("PP EXPRESS NOTICE (B): Cart session changed");
			$this->fail();
		}

		// Load the temp order
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		$order_id = $this->session->data['order_id'];
		//$amount = $this->currency->format($order_info['total'], $this->session->data['ppx']['currency'], FALSE, FALSE);

		// Check for supported currency, otherwise convert to USD.
        $supported_currencies = array('AUD','CAD','EUR','GBP','JPY','USD','NZD','CHF','HKD','SGD','SEK','DKK','PLN','NOK','HUF','CZK','ILS','MXN');
        if (in_array($this->session->data['currency'], $supported_currencies)) {
            $currency = $this->session->data['currency'];
        } else {
            $currency = 'USD';
        }

	    $this->session->data['ppx']['currency'] = $currency;

		$data = array ();
		$data['METHOD'] 		= 'DoExpressCheckoutPayment';
		$data['USER'] 			= trim($this->config->get('paypal_express_new_apiuser'));
		$data['PWD'] 			= trim($this->config->get('paypal_express_new_apipass'));
		$data['SIGNATURE'] 		= trim($this->config->get('paypal_express_new_apisig'));
		$data['VERSION'] 		= '60.0';
		$data['PAYMENTACTION'] 	= ($this->config->get('paypal_express_new_payment_action')) ? $this->config->get('paypal_express_new_payment_action') : 'Sale';
		$data['CURRENCYCODE'] 	= $currency;
		$data['TOKEN'] 			= $this->session->data['ppx']['token'];
		$data['PAYERID'] 		= $this->session->data['ppx']['payerid'];
		// $data['NOTIFYURL'] 		= 'https://imp.phonepartsusa.com/crons/paypal_ipn_callback.php';
		$data['INVNUM']			= $this->session->data['order_id'];

		$data = array_merge($data, $this->buildItems($data));

		//$data = implode("&", $data);
		$data = http_build_query($data);

	   	$nvp = $this->process_curl(__FUNCTION__, $data);

		if (!isset($nvp['PAYERSTATUS'])) {
			$nvp['PAYERSTATUS'] = 'unverified';
		}

		if (isset($nvp['ACK']) && $nvp['ACK'] == 'Success') {
			// Verify status. Send email if status is not completed
			if ($nvp['PAYMENTSTATUS'] == 'Completed' && $nvp['PAYERSTATUS'] == 'verified') { // verified status
				$this->model_checkout_order->confirm($order_info['order_id'], $this->config->get('paypal_express_new_order_status_id'), $order_info['comment']);
				$this->model_checkout_order->update($order_info['order_id'], $this->config->get('paypal_express_new_order_status_id'), "PP Transaction ID: " . $nvp['TRANSACTIONID'] . "\r\n" . $nvp['PAYERSTATUS'], FALSE);
			} elseif ($nvp['PAYMENTSTATUS'] == 'Completed') { // unverified status
				if (!$this->config->get('paypal_express_new_order_status_id')) { $this->config->set('paypal_express_new_unverified_order_status_id', $this->config->get('paypal_express_new_order_status_id')); }
				$this->model_checkout_order->confirm($order_info['order_id'], $this->config->get('paypal_express_new_unverified_order_status_id'), $order_info['comment']);
				$this->model_checkout_order->update($order_info['order_id'], $this->config->get('paypal_express_new_unverified_order_status_id'), "PP Transaction ID: " . $nvp['TRANSACTIONID'], FALSE);
			} else { // not Completed
				$this->model_checkout_order->confirm($order_info['order_id'], $this->config->get('config_order_status_id'), $order_info['comment']);
				$this->model_checkout_order->update($order_info['order_id'], $this->config->get('config_order_status_id'), "PP Transaction ID: " . $nvp['TRANSACTIONID'] . "\r\n" . $nvp['PAYMENTSTATUS'], FALSE);
				mail($this->config->get('config_email'), 'ATTN: Unverified PP Express Order', "Order ID: $order_id status set to $nvp[PAYMENTSTATUS] needs manual review");
			}

			unset($this->session->data['ppx']);
			$this->redirect($store_url . 'index.php?route=checkout/success');
		}
	}

	// This function gets called by Set and Do
	private function buildItems($data) {

		// Check for supported currency, otherwise convert to USD.
        $supported_currencies = array('AUD','CAD','EUR','GBP','JPY','USD','NZD','CHF','HKD','SGD','SEK','DKK','PLN','NOK','HUF','CZK','ILS','MXN');
        if (in_array($this->session->data['currency'], $supported_currencies)) {
            $currency = $this->session->data['currency'];
        } else {
            $currency = 'USD';
        }

	    $this->session->data['ppx']['currency'] = $currency;

		# Default pp product total to oc subtotal
		$oc_product_total = $this->cart->getSubTotal();

		#
		# Itemized Products
		#
		$pp_itemized_total = 0;
		$pp_product_total = 0;
		$i = 0;
		
		foreach ($this->cart->getProducts() as $product) {
			// Show product options for description if available
			if ($product['option']) {
				$values = array();
				$options = false;
				//exit(nl2br(print_r($product['option'],1)));
				foreach ($product['option'] as $option) {
					if (isset($option['option_value'])) {
					$values[] = $option['option_value'];
					} elseif (isset($option['value'])) {
						$values[] = $option['value'];
					}
				}
				if ($values) {
					$description = implode(',', $values);
				} else {
					$description = '';
				}
			} else {
				$description = $product['name'];
			}

			$price = $this->currency->format($product['price'], $currency, FALSE, FALSE);
			$data['L_NAME' . $i] 	= html_entity_decode($product['name'], ENT_QUOTES, "ISO-8859-1");
			$data['L_NUMBER' . $i] 	= $product['model'];
			$data['L_DESC' . $i] 	= (substr(html_entity_decode($description, ENT_QUOTES, "ISO-8859-1"), 0, 110) . '...');
			$data['L_AMT' . $i ] 	= $price;
			$data['L_QTY' . $i ] 	= $product['quantity'];
			$i++;
			$pp_product_total += $price * $product['quantity'];
		}
		$pp_itemized_total = $pp_product_total;

		#
		# Itemized Totals
		#
		$total_data = array();
		$total = 0;
		$taxes = $this->cart->getTaxes();

		if (method_exists($this->document, 'addBreadcrumb')) { //1.4.x
			$this->load->model('checkout/extension');
			$results = $this->model_checkout_extension->getExtensions('total');
		} else {
			$this->load->model('setting/extension');
			$results = $this->model_setting_extension->getExtensions('total');
		}



		// Sort Order Totals
		$sort_order = array();

		foreach ($results as $key => $value) {
			if (!isset($value['code'])) { $value['code'] = $value['key']; } //v14x compatibility
			if (!$this->config->get($value['code'] . '_status')) { unset($results[$key]); continue; } // filter out disabled totals
			$sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
		}

		array_multisort($sort_order, SORT_ASC, $results);

		$oc_discount_total = 0;
		foreach ($results as $result) {
			if (!isset($result['code'])) { $result['code'] = $result['key']; } //v14x compatibility

			$old_total = $total;

			$last_total = $total;
			$last_count = count($total_data);

			$this->load->model('total/' . $result['code']);
			$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);

			if ($result['code'] == 'sub_total') { $total_data[count($total_data)-1]['code'] = $result['code']; continue; }
			if ($result['code'] == 'total') { $total_data[count($total_data)-1]['code'] = $result['code']; continue; }
			//if ($result['code'] == 'shipping') { continue; }
			//if ($result['code'] == 'tax') { continue; }

			if ($total < $old_total) {
				$oc_discount_total += $old_total - $total;
			}

			if (count($total_data) > $last_count) {
				$total_data[count($total_data)-1]['filename'] = $result['code']; // v14x add base filename to the array since 14x didn't have code
				$total_data[count($total_data)-1]['code'] = $result['code'];
				if ($result['code'] == 'shipping') {
					if (isset($this->session->data['shipping_method']['code'])) { //v15x
						$shipping_method_id = $this->session->data['shipping_method']['code'];
					} else { //v14x
						$shipping_method_id = $this->session->data['shipping_method']['id'];
					}
					$tmp = explode('.', $shipping_method_id);
					$shipping_method_id = $tmp[0];
					$total_data[count($total_data)-1]['key'] = $shipping_method_id;

					$total_data[count($total_data)-1]['prefix'] = '+';
				} elseif ($total < $last_total && $last_total != 0) {
					$total_data[count($total_data)-1]['key'] = $result['code'];
					$total_data[count($total_data)-1]['prefix'] = '-';
				} else {
					$total_data[count($total_data)-1]['key'] = $result['code'];
					$total_data[count($total_data)-1]['prefix'] = '+';
				}
			}
		}

		$oc_discount_total = round($oc_discount_total, 2);

		$pp_discount_total = $this->currency->format($oc_discount_total, $currency, FALSE, FALSE);

		$sort_order = array();

		foreach ($total_data as $key => $value) {
      		$sort_order[$key] = $value['sort_order'];
    	}

    	array_multisort($sort_order, SORT_ASC, $total_data);

		foreach ($total_data as $key => $value) {
			//if (!isset($value['code'])) { $value['code'] = $value['code']; } //v14x compatibility
			// Don't add subtotal and total as line totals
			if ($value['code'] == 'sub_total') { continue; }
			if ($value['code'] == 'total') { continue; }

			// Use paypal's shipping and tax fields if discount less than product total
			// Otherwise, shipping and tax are line items to satisfy paypal's non-zero subtotal
			if ($oc_discount_total < $oc_product_total) {
				if ($value['code'] == 'tax') { continue; }
				if ($value['code'] == 'shipping') { continue; }
			}

      		$order_total_price = (float)($value['prefix'] . $this->currency->format(str_replace('-','', $value['value']), $currency, FALSE, FALSE));
			if($value['code']=='voucher' and $order_total_price>0.00)
			{
				$order_total_price = $order_total_price*-1;	
			}
			$data['L_NAME' . $i] 		= html_entity_decode($value['title'], ENT_QUOTES, "ISO-8859-1");
			$data['L_NUMBER' . $i] 		= '';
			$data['L_DESC' . $i] 		= '';
			$data['L_AMT' . $i] 		= round($order_total_price, 2);
			$data['L_QTY' . $i] 		= '1';
			$i++;
			$pp_itemized_total += $order_total_price;
		}


		// Use paypal's shipping and tax fields if discount less than product total
		$pp_shipping_total = '0.00';
		$pp_tax_total = '0.00';
		if ($oc_discount_total < $oc_product_total) {
			# Get Shipping Total
			$oc_shipping_total = 0;
			if ($this->cart->hasShipping() && isset($this->session->data['shipping_method'])) {
				$oc_shipping_total = round($this->session->data['shipping_method']['cost'], 2);
			}
			$pp_shipping_total = $this->currency->format($oc_shipping_total, $currency, FALSE, FALSE);

			# Get Tax Total
			$oc_tax_total = 0;
			foreach ($taxes as $key => $value) {
				$oc_tax_total += $value;
			}

				if($this->customer->getId())
			{
				$dis_tax = $this->db->query("SELECT dis_tax FROM " . DB_PREFIX . "customer WHERE customer_id = '" . $this->customer->getId() . "'")->row['dis_tax'];
				if($dis_tax)
				{
					$oc_tax_total = 0.00;
				}	
			}
			$pp_tax_total = $this->currency->format($oc_tax_total, $currency, FALSE, FALSE);

			$data['SHIPPINGAMT'] 	= $pp_shipping_total;
			$data['TAXAMT'] 		= $pp_tax_total;
		} else {
			$data['SHIPPINGAMT'] 	= '0.00';
			$data['TAXAMT'] 		= '0.00';
		}

		

		$pp_grand_total = round($pp_itemized_total + $pp_shipping_total + $pp_tax_total, 2);

		$data['ITEMAMT'] = number_format($pp_itemized_total, 2, '.', '');
		$data['AMT'] = number_format($pp_grand_total, 2, '.', '');

		return $data;
	}

	private function fail($msg = false) {
		$store_url = ($this->config->get('config_ssl') ? (is_numeric($this->config->get('config_ssl'))) ? str_replace('http', 'https', $this->config->get('config_url')) : $this->config->get('config_ssl') : $this->config->get('config_url'));
		if (!$msg) { $msg = (!empty($this->session->data['error']) ? $this->session->data['error'] : 'Unknown Error'); }
		if (method_exists($this->document, 'addBreadcrumb')) { //1.4.x
			$this->redirect((isset($this->session->data['guest'])) ? ($store_url . 'index.php?route=checkout/guest_step_3') : ($store_url . 'index.php?route=checkout/confirm'));
		} else {
			echo '<html><head><script type="text/javascript">';
			echo 'alert("'.$msg.'");';
			echo 'window.location="' . ($store_url  . 'index.php?route=checkout/ppx_checkout_new') . '";';
			echo '</script></head></html>';
		}
		exit;
	}

	private function process_curl($method, $data = '') {
		$store_url = ($this->config->get('config_ssl') ? (is_numeric($this->config->get('config_ssl'))) ? str_replace('http', 'https', $this->config->get('config_url')) : $this->config->get('config_ssl') : $this->config->get('config_url'));
		if ($this->config->get('paypal_express_new_test')) {
			$this->ppurl = 'https://api-3t.sandbox.paypal.com/nvp';
		} else {
			$this->ppurl = 'https://api-3t.paypal.com/nvp';
		}
		
		
		$headers[] = 'Content-Type: text/namevalue';
    	$headers[] = 'X-VPS-Timeout: 15';
   		$headers[] = "X-VPS-VIT-Client-Type: PHP/cURL";
	    $headers[] = 'X-VPS-VIT-Integration-Product: PHP::OpenCart - PayPal/NVP';
	    $headers[] = 'X-VPS-VIT-Integration-Version: 1.3.9';
	    $headers[] = 'Access-Control-Allow-Origin: *';
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $this->ppurl);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_SSLVERSION, 6);

        curl_setopt($ch, CURLOPT_FORBID_REUSE, TRUE);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);

        $response = curl_exec($ch);
        $commError = curl_error($ch);
	    $commErrNo = curl_errno($ch);
	    $commInfo = @curl_getinfo($ch);
	    curl_close($ch);
		
		//print_r($response);exit;
        // Redirect any errors to debug or warn customer
        if (!$response) {
        	$this->load->language('payment/paypal_express_new');
			$err = '';
			if ($commError) {
				$err .= "$commErrNo :: $commError";
			}
        	$this->session->data['error'] = $this->language->get('error_no_curl_response') . " -- $err";
            $this->log->write("PP EXPRESS FAIL (C): Curl Error" . $this->session->data['error']);
			$this->fail();
		}

        $pairs = explode('&', $response);

        $nvp = array();
        foreach ($pairs as $pair) {
			list($k, $v) = explode('=', urldecode($pair), 2);
      		$nvp[$k] = $v;
		}

		// Debug
        if ($this->config->get('paypal_express_new_debug')) {
			$dataparse = str_replace('&', "\r\n", $data);
        	$s_msg = "DEBUG SEND DATA (" . $method . "):\r\n" . $dataparse . "\r\n\r\n";
        	$r_msg = "DEBUG RCV DATA (" . $method . "):\r\n"; foreach($nvp as $k=>$v) { $r_msg .= $k."=".$v."\r\n"; }
        	$msg = ("\r\n" . $s_msg . "\r\n" . $r_msg . "\r\n");
			if ($method == 'SetExpressCheckout') {
				// file_put_contents(DIR_LOGS . 'ppx_debug.txt', "------------------------------ \r\n$msg", FILE_APPEND);
			} else {
				// file_put_contents(DIR_LOGS . 'ppx_debug.txt', $msg, FILE_APPEND);
			}
		}//

		if (isset($nvp['ACK']) && $nvp['ACK'] != 'Success') {

            //Error code handling
            switch ($nvp['L_ERRORCODE0']) {
            	// Token & Payer id errors. Just reset the process
            	//case '10001':
            	case '10408':
            	case '10410':
            	case '10411':
            	case '10415':
            	case '10416':
            	case '10419':
            	case '10421':
            	case '11502':
            	case '11585':
            	case '81117':
            	case '81118':
            	unset($this->session->data['ppx']);
            	$this->redirect($store_url . 'index.php?route=payment/paypal_express_new/SetExpressCheckout');
            	break;
			}
			if ($method == 'DoExpressCheckoutPayment') {
				$this->session->data['error'] = $nvp['L_ERRORCODE0'] . '::' . $nvp['L_LONGMESSAGE0'];
				$this->log->write("PP EXPRESS FAIL (D): Gateway Response: " . $this->session->data['error']);
            	$this->fail();
			} else {
				$this->session->data['ppx']['error'] = $nvp['L_ERRORCODE0'] . '::' . $nvp['L_LONGMESSAGE0'];
				$this->session->data['error'] = $nvp['L_ERRORCODE0'] . '::' . $nvp['L_LONGMESSAGE0'];
				$this->session->data['success'] = $nvp['L_ERRORCODE0'] . '::' . $nvp['L_LONGMESSAGE0'];
				$this->redirect($store_url . 'index.php?route=checkout/cart');
			}
		}

		return $nvp;

	}

	public function prepareOrder($data = array()) {
		//file_put_contents(DIR_LOGS . 'ppx_prepareOrder_data_variable.txt', print_r($data,1) . "\r\n-----\r\n", FILE_APPEND);
		$store_url = ($this->config->get('config_ssl') ? (is_numeric($this->config->get('config_ssl'))) ? str_replace('http', 'https', $this->config->get('config_url')) : $this->config->get('config_ssl') : $this->config->get('config_url'));
		// Force the payment method to fool the payment & confirmation page
		$this->language->load('payment/paypal_express_new');
		$this->session->data['payment_method']['id'] = 'paypal_express_new'; //v14x
		$this->session->data['payment_method']['code'] = 'paypal_express_new'; //v15x
		$this->session->data['payment_method']['title'] = $this->language->get('text_title');
		$this->session->data['payment_method']['sort_order'] = '1';
	
		// Get the zone & country value ids
		$country_query = $this->db->query("SELECT country_id FROM " . DB_PREFIX . "country WHERE iso_code_2 = '" . $data['SHIPTOCOUNTRYCODE'] . "'");
		if ($country_query->num_rows) {
			$data['country_id'] = $country_query->row['country_id'];
		} else {
			$data['country_id'] = 0;
		}

		if (!isset($data['SHIPTOSTATE'])) {
			$data['SHIPTOSTATE'] = '0';
		}

		// USA returns SHIPTOSTATE as 2-letter ISO code. Int'l returns full territory name
		if ($data['SHIPTOCOUNTRYCODE'] == 'US') {
			$zone_query = $this->db->query("SELECT zone_id FROM " . DB_PREFIX . "zone WHERE code = '" . $data['SHIPTOSTATE'] . "' AND country_id = '" . $data['country_id'] . "'");
		} else {
			$zone_query = $this->db->query("SELECT zone_id FROM " . DB_PREFIX . "zone WHERE name = '" . $data['SHIPTOSTATE'] . "' AND country_id = '" . $data['country_id'] . "'");
			if (!$zone_query->num_rows) { //if still no match, try partial match
				$zone_query = $this->db->query("SELECT zone_id FROM " . DB_PREFIX . "zone WHERE name LIKE '%" . substr($data['SHIPTOSTATE'], 0, 6) . "%' AND country_id = '" . $data['country_id'] . "'");
			}
		}

		if ($zone_query->num_rows) {
			$data['zone_id'] = $zone_query->row['zone_id'];
		} else {
			$data['zone_id'] = 0;
		}
		$data['postcode'] = $data['SHIPTOZIP'];
		$data['company_id'] = 0;
		$data['tax_id'] = 0;
		
		// Tax change for 1.5.x
		$this->session->data['shipping_country_id'] = $data['country_id'];
		$this->session->data['shipping_zone_id'] = $data['zone_id'];
		$this->session->data['shipping_postcode'] = $data['postcode'];
		
		//file_put_contents(DIR_LOGS . 'ppx_debug.txt', "\r\n Calculated Country/Zone/Postcode: \r\n CountryID: " . $data['country_id'] . " | ZoneID: " . $data['zone_id'] . " | PostCode: " . $data['postcode'] . "\r\n", FILE_APPEND);
		
		
		// They return only a single name, so explode it into 2 pieces. 
		// Override the existing FIRSTNAME and LASTNAME because you may not want the account name
		$tmpname = explode(" ", $data['SHIPTONAME'], 2);
		if (count($tmpname) > 1) {
			$data['FIRSTNAME'] = $tmpname[0];
			$data['LASTNAME']  = $tmpname[1];
		} else {
			$data['FIRSTNAME'] = $data['SHIPTONAME'];
			$data['LASTNAME']  = "";
		}
		
		
		$paypal_address = array();
		$paypal_address['firstname'] = $data['FIRSTNAME'];
		$paypal_address['lastname'] = $data['LASTNAME'];
		$paypal_address['email'] = $data['EMAIL'];
		if (isset($data['SHIPTOPHONENUM'])) {
			$paypal_address['telephone'] = $data['SHIPTOPHONENUM'];
		} elseif (isset($data['PHONENUM'])) {
			$paypal_address['telephone'] = $data['PHONENUM'];
		} else {
			$paypal_address['telephone'] = 'n/a';
		}
		$paypal_address['fax'] = '';
		$paypal_address['company'] = $data['SHIPTONAME'];
		$paypal_address['address_1'] = $data['SHIPTOSTREET'];
		$paypal_address['address_2'] = (isset($data['SHIPTOSTREET2'])) ? $data['SHIPTOSTREET2'] : '';
		$paypal_address['postcode'] = (isset($data['SHIPTOZIP'])) ? $data['SHIPTOZIP'] : '';
		$paypal_address['city'] = (isset($data['SHIPTOCITY'])) ? $data['SHIPTOCITY'] : '';
		$paypal_address['country_id'] = $data['country_id'];
		$paypal_address['zone_id'] = $data['zone_id'];
		$paypal_address['company_id'] = 0;
		$paypal_address['tax_id'] = 0;
		//$paypal_address['default']=1;
		//print_r($paypal_address);exit;
		
		$this->load->model('account/address');

		// Create checkout under customer id
		if ($this->customer->isLogged()) {
			

			// Search existing addresses for paypal address and don't re-add it if exists
			$address_query = $this->db->query("SELECT address_id from " . DB_PREFIX . "address WHERE customer_id = '" . $this->customer->getId() . "' and firstname = '" . $data['FIRSTNAME'] . "' AND address_1 = '" . $data['SHIPTOSTREET'] . "'");
			if ($address_query->num_rows) {
				$this->session->data['ppx']['address_id'] = $address_query->row['address_id'];
			}

			// If the address doesn't exist, create temporary paypal address in the address table to satisfy the confirm page. Delete after order process.
			if (!isset($this->session->data['ppx']['address_id'])) {
				$this->session->data['ppx']['address_id'] = $this->model_account_address->addAddress($paypal_address);
			}

			// Set to payment address. But don't override checkout payment addresses if already set
			//if (!isset($this->session->data['payment_address_id'])){
				$this->session->data['payment_address_id'] = $this->session->data['ppx']['address_id'];
			//}

			if ($this->cart->hasShipping()) {
				// Set to shipping address. But don't override checkout shipping addresses if already set
				//if (!isset($this->session->data['shipping_address_id'])){
					$this->session->data['shipping_address_id'] = $this->session->data['ppx']['address_id'];
				//}
			}

		} else { // Create Guest Order
			$this->load->model('checkout/order');

			// Guest Name/Phone
			if (empty($this->session->data['guest']['email'])) {
				$this->session->data['guest']['email'] = $data['EMAIL'];
			}
			
			// If still empty, then something very bad is happening
			if (empty($this->session->data['guest']['email'])) {
				//file_put_contents(DIR_LOGS . 'ppx_errors.txt', "\r\nGuest Email still empty!", FILE_APPEND);
			}
			
			// Override guest address from opencart with Paypal Express selected address.. Is this the best idea?
			$this->session->data['guest']['firstname'] = $data['FIRSTNAME'];
			$this->session->data['guest']['lastname'] = $data['LASTNAME'];
			if (isset($data['SHIPTOPHONENUM'])) {
				$this->session->data['guest']['telephone'] = $data['SHIPTOPHONENUM'];
			} elseif (isset($data['PHONENUM'])) {
				$this->session->data['guest']['telephone'] = $data['PHONENUM'];
			} else {
				$this->session->data['guest']['telephone'] = '000-000-0000';
			}
			$this->session->data['guest']['fax'] = '';
			

			// Guest Payment Address
			//if (!isset($this->session->data['guest']['payment'])) {
				$this->session->data['guest']['payment']['firstname'] = $data['FIRSTNAME'];
				$this->session->data['guest']['payment']['lastname'] = $data['LASTNAME'];
				$this->session->data['guest']['payment']['company'] = (($data['FIRSTNAME'] . ' ' . $data['LASTNAME']) == $data['SHIPTONAME']) ? '' : $data['SHIPTONAME'];
				$this->session->data['guest']['payment']['address_1'] = $data['SHIPTOSTREET'];
				$this->session->data['guest']['payment']['address_2'] = (isset($data['SHIPTOSTREET2'])) ? $data['SHIPTOSTREET2'] : '';
				$this->session->data['guest']['payment']['postcode'] = (isset($data['SHIPTOZIP'])) ? $data['SHIPTOZIP'] : '';
				$this->session->data['guest']['payment']['city'] = (isset($data['SHIPTOCITY'])) ? $data['SHIPTOCITY'] : '';
				$this->session->data['guest']['payment']['country_id'] = $data['country_id'];
				$this->session->data['guest']['payment']['zone_id'] = $data['zone_id'];
				$this->session->data['guest']['payment']['company_id'] = 0;
				$this->session->data['guest']['payment']['customer_group_id'] = 0;
				$this->session->data['guest']['payment']['tax_id'] = 0;

				$this->load->model('localisation/country');
				$country_info = $this->model_localisation_country->getCountry($data['country_id']);

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
				$zone_info = $this->model_localisation_zone->getZone($data['zone_id']);

				if ($zone_info) {
					$this->session->data['guest']['payment']['zone'] = $zone_info['name'];
					$this->session->data['guest']['payment']['zone_code'] = $zone_info['code'];
				} else {
					$this->session->data['guest']['payment']['zone'] = '';
					$this->session->data['guest']['payment']['zone_code'] = '';
				}
			//}

			// v14x
			foreach ($this->session->data['guest']['payment'] as $k => $v) {
				$this->session->data['guest'][$k] = $this->session->data['guest']['payment'][$k];
			}
			//


			// Guest Shipping Address
			if (empty($this->session->data['guest']['shipping']['firstname'])) {
				// Same as Billing
				$this->session->data['guest']['shipping'] = $this->session->data['guest']['payment'];
			}


			// Create an checkout if set and delete the guest session
			if ($this->config->get('paypal_express_new_account')) {
				if ($this->createAccount($this->session->data['guest'])) {
					unset($this->session->data['guest']);
					
					// Add the current paypal address
					$address_id = $this->model_account_address->addAddress($paypal_address);

					// Set the payment and shipping address ids
					//$address_id = $this->customer->getAddressId();
					$this->session->data['shipping_address_id'] = $address_id;
					$this->session->data['payment_address_id'] = $address_id;
				}
			}
		}

		// Force the comment to avoid error on confirm/guest_step_3 when no shipping.
		if (isset($data['NOTETEXT'])) {
			$this->session->data['comment'] = (isset($this->session->data['comment'])) ? $this->session->data['comment'] . ' - ' . $data['NOTETEXT'] : $data['NOTETEXT'];
		} else {
			$this->session->data['comment'] = (isset($this->session->data['comment'])) ? $this->session->data['comment'] : '';
		}

		if (method_exists($this->document, 'addBreadcrumb')) { //1.4.x
			$this->redirect((isset($this->session->data['guest'])) ? (($store_url) . 'index.php?route=checkout/guest_step_3') : (($store_url) . 'index.php?route=checkout/confirm'));
		} else {
			if(isset($this->session->data['is_pp_checkout']))
			{
				
				
			$this->redirect($store_url . 'index.php?route=payment/paypal_express_new/DoExpressCheckoutPayment');	
			exit;
			}
			else
			{
			
			$this->redirect($store_url . 'index.php?route=checkout/ppx_checkout_new');
			}
		}

	}

	function createAccount($pp_address = array()) {
		$store_url = ($this->config->get('config_ssl') ? (is_numeric($this->config->get('config_ssl'))) ? str_replace('http', 'https', $this->config->get('config_url')) : $this->config->get('config_ssl') : $this->config->get('config_url'));
		if ($this->config->get('paypal_express_new_debug')) {
			//file_put_contents(DIR_LOGS . 'ppx_debug_create.txt', print_r($pp_address, 1) . "\r\n\r\n----------------\r\n\r\n", FILE_APPEND);
		}
		$this->load->model('account/customer');
		$login_email = urldecode($pp_address['email']);

		// If customer already exists, swap temp password to force login, then restore original
		if ($this->model_account_customer->getTotalCustomersByEmail($login_email)) {
			$pass_query = $this->db->query("SELECT `password` FROM " . DB_PREFIX . "customer WHERE `email` = '" . $login_email . "'");
			$original_passwd = $pass_query->row['password'];
			$temp_string_passwd = 'paypal_express_new';
			$temp_hashed_passwd = md5($temp_string_passwd);
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET `password` = '" . $temp_hashed_passwd . "' WHERE `email` = '" . $login_email . "'");
			if ($this->customer->login($login_email, $temp_string_passwd)) {
				$this->session->data['checkout'] = 1;
				$this->db->query("UPDATE " . DB_PREFIX . "customer SET `password` = '" . $original_passwd . "' WHERE `email` = '" . $login_email . "'");
				// TODO: Add the pp_address to the customer address table here
				return true;
			} else {
				return false; // could not login for some reason so checkout as guest
			}
		}

		$login_passwd = $this->generatePassword();

		$this->customer_data = array(
			//'firstname' 		=> $pp_address['payment']['firstname'],
			'firstname' 		=> $pp_address['firstname'],
			'lastname' 			=> $pp_address['lastname'],
			'email' 			=> $login_email,
			'telephone' 		=> $pp_address['telephone'],
			'fax' 				=> '',
			'password' 			=> $login_passwd,
			'newsletter' 		=> '1',
			'customer_group_id' => $this->config->get('config_customer_group_id'),
			'status' 			=> '1',
			'ip' 				=> $this->request->server['REMOTE_ADDR'],
			'company' 			=> $pp_address['company'],
			'address_1' 		=> $pp_address['address_1'],
			'address_2' 		=> $pp_address['address_2'],
			'city' 				=> $pp_address['city'],
			'postcode' 			=> $pp_address['postcode'],
			'zone_id' 			=> $pp_address['zone_id'],
			'country_id' 		=> $pp_address['country_id'],
			'default' 			=> '1',
			'company_id' 		=> '0',
			'tax_id' 			=> '0'
		);

		$this->model_account_customer->addCustomer($this->customer_data);

		// Force approved if using newer versions
		$approve_query = $this->db->query("DESC `" . DB_PREFIX . "customer` `approved`");
		if ($approve_query->num_rows) {
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET `approved` = '1' WHERE `email` = '" . $login_email . "'");
		}

		$this->session->data['ppx']['generated'] = true;
		$this->customer->login($login_email, $login_passwd);
		$this->session->data['checkout'] = 1;

		// Send welcome email with email for login and autogenerated password for password
		$store_name = ($this->config->get('config_name')) ? $this->config->get('config_name') : $this->config->get('config_store');
		$subject = sprintf($this->language->get('mail_subject'), $store_name);
		$message  = $store_name . "\n\n";
		$message .= $this->language->get('mail_line_2') . "\n";
		$message .= ($store_url . 'index.php?route=checkout/login') . "\n\n";
		$message .= sprintf($this->language->get('mail_email'), $login_email) . "\n";
		$message .= sprintf($this->language->get('mail_password'), $login_passwd) . "\n\n";
		$message .= $this->language->get('mail_line_3') . "\n\n";
		$message .= $this->language->get('mail_line_4') . "\n";
		$message .= $store_name;

		$mail = new Mail();	
		$mail->protocol = $this->config->get('config_mail_protocol');
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->hostname = $this->config->get('config_smtp_host');
		$mail->username = $this->config->get('config_smtp_username');
		$mail->password = $this->config->get('config_smtp_password');
		$mail->port = $this->config->get('config_smtp_port');
		$mail->timeout = $this->config->get('config_smtp_timeout');		
		$mail->setTo($login_email);
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender($store_name);
		$mail->setSubject($subject);
		$mail->setText($message);
		$mail->send();

		return true;
	}
	
	public function callback() {
		
		// Debug
        if ($this->config->get('paypal_express_new_debug')) {
			// file_put_contents(DIR_LOGS . 'ppx_ipn.txt', "\r\n----------\r\n" . "GET: " . print_r($_GET, 1) . "\r\nPOST: " . print_r($_POST, 1) . "\r\n", FILE_APPEND);
		}
	/*
		if (isset($this->request->post['custom'])) {
			$order_id = $this->request->post['custom'];
		} else {
			$order_id = 0;
		}		
		
		$this->load->model('checkout/order');
				
		$order_info = $this->model_checkout_order->getOrder($order_id);
		
		if ($order_info) {
			$request = 'cmd=_notify-validate';
		
			foreach ($this->request->post as $key => $value) {
				$request .= '&' . $key . '=' . urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));
			}
			
			if (!$this->config->get('pp_standard_test')) {
				$curl = curl_init('https://www.paypal.com/cgi-bin/webscr');
			} else {
				$curl = curl_init('https://www.sandbox.paypal.com/cgi-bin/webscr');
			}

			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
					
			$response = curl_exec($curl);
			
			if (!$response) {
				$this->log->write('PP_STANDARD :: CURL failed ' . curl_error($curl) . '(' . curl_errno($curl) . ')');
			}
					
			if ($this->config->get('pp_standard_debug')) {
				$this->log->write('PP_STANDARD :: IPN REQUEST: ' . $request);
				$this->log->write('PP_STANDARD :: IPN RESPONSE: ' . $response);
			}
						
			if ((strcmp($response, 'VERIFIED') == 0 || strcmp($response, 'UNVERIFIED') == 0) && isset($this->request->post['payment_status'])) {
				$order_status_id = $this->config->get('config_order_status_id');
				
				switch($this->request->post['payment_status']) {
					case 'Canceled_Reversal':
						$order_status_id = $this->config->get('pp_standard_canceled_reversal_status_id');
						break;
					case 'Completed':
						if ((strtolower($this->request->post['receiver_email']) == strtolower($this->config->get('pp_standard_email'))) && ((float)$this->request->post['mc_gross'] == $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false))) {
							$order_status_id = $this->config->get('pp_standard_completed_status_id');
						} else {
							$this->log->write('PP_STANDARD :: RECEIVER EMAIL MISMATCH! ' . strtolower($this->request->post['receiver_email']));
						}
						break;
					case 'Denied':
						$order_status_id = $this->config->get('pp_standard_denied_status_id');
						break;
					case 'Expired':
						$order_status_id = $this->config->get('pp_standard_expired_status_id');
						break;
					case 'Failed':
						$order_status_id = $this->config->get('pp_standard_failed_status_id');
						break;
					case 'Pending':
						$order_status_id = $this->config->get('pp_standard_pending_status_id');
						break;
					case 'Processed':
						$order_status_id = $this->config->get('pp_standard_processed_status_id');
						break;
					case 'Refunded':
						$order_status_id = $this->config->get('pp_standard_refunded_status_id');
						break;
					case 'Reversed':
						$order_status_id = $this->config->get('pp_standard_reversed_status_id');
						break;	 
					case 'Voided':
						$order_status_id = $this->config->get('pp_standard_voided_status_id');
						break;								
				}
				
				if (!$order_info['order_status_id']) {
					$this->model_checkout_order->confirm($order_id, $order_status_id);
				} else {
					$this->model_checkout_order->update($order_id, $order_status_id);
				}
			} else {
				$this->model_checkout_order->confirm($order_id, $this->config->get('config_order_status_id'));
			}
			
			curl_close($curl);
		}	
		*/
	}

	function generatePassword ($length = 8) {
		$password = "";
		$possible = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!#";
		$i = 0;
		while ($i < $length) {
			// pick a random character from the possible ones
			$char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
			// we don't want this character if it's already in the password
			if (!strstr($password, $char)) {
				$password .= $char;
				$i++;
			}
		}
		return $password;
	}
}
?>