<?php
class ControllerCheckoutPPXCheckout extends Controller {

	private $error = array();

	public function index() {
		if (!isset($this->session->data['ppx']['token'])) {
			$this->redirect($this->url->link('checkout/checkout'));
		}

		if ((!$this->cart->hasProducts() && !empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
	  		$this->redirect($this->url->link('checkout/cart'));
    	}

		// PO Box check
		if (($this->cart->hasShipping() && $this->config->get('paypal_express_no_po_box'))) {
			if ($this->customer->isLogged() && isset($this->session->data['shipping_address_id'])) {
				$this->load->model('account/address');
				$cust_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
				if  (stripos($cust_address['address_1'], 'PO ') ||
				    stripos($cust_address['address_1'], 'BOX') ||
				    stripos($cust_address['address_1'], 'P.O.') ||
				    stripos($cust_address['address_1'], 'Post Office') ||
				    stripos($cust_address['address_1'], 'POBOX') ||
				    stripos($cust_address['address_1'], 'PO-BOX')) {
						//https://opencartguru.com/index.php?route=account/address/update&address_id=8819
						$this->session->data['error_warning'] = "We cannot ship to PO BOXES. Please change your address";
						$this->redirect($this->url->link('checkout/cart'));
						//$this->redirect($this->url->link('account/address/update', 'address_id=' . $this->session->data['shipping_address_id']));
				}
			} elseif (!$this->customer->isLogged() && isset($this->session->data['guest']['shipping'])) {
				if  (stripos($this->session->data['guest']['shipping']['address_1'], 'PO ') ||
				    stripos($this->session->data['guest']['shipping']['address_1'], 'BOX') ||
				    stripos($this->session->data['guest']['shipping']['address_1'], 'P.O.') ||
				    stripos($this->session->data['guest']['shipping']['address_1'], 'Post Office') ||
				    stripos($this->session->data['guest']['shipping']['address_1'], 'POBOX') ||
				    stripos($this->session->data['guest']['shipping']['address_1'], 'PO-BOX')) {
						$this->session->data['error_warning'] = "We cannot ship to PO BOXES. Please change your address";
						$this->redirect($this->url->link('checkout/cart'));
				}
			}
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
				$this->redirect($this->url->link('checkout/cart'));
			}
		}

		$this->language->load('checkout/checkout');

		// Override the titles of the checkout steps
		$this->language->load('payment/paypal_express');


		$this->document->setTitle($this->language->get('heading_title'));

		$this->data['breadcrumbs'] = array();

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
        	'separator' => false
      	);

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_cart'),
			'href'      => $this->url->link('checkout/cart'),
        	'separator' => $this->language->get('text_separator')
      	);

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('checkout/ppx_checkout', '', 'SSL'),
        	'separator' => $this->language->get('text_separator')
      	);

	    $this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_checkout_option'] = sprintf($this->language->get('text_checkout_option'));
		$this->data['text_checkout_account'] = $this->language->get('text_checkout_account');
		$this->data['text_checkout_payment_address'] = $this->language->get('text_checkout_payment_address');
		$this->data['text_checkout_shipping_address'] = $this->language->get('text_checkout_shipping_address');
		$this->data['text_checkout_shipping_method'] = $this->language->get('text_checkout_shipping_method');
		$this->data['text_checkout_payment_method'] = $this->language->get('text_checkout_payment_method');
		$this->data['text_checkout_confirm'] = $this->language->get('text_checkout_confirm');
		$this->data['text_modify'] = $this->language->get('text_modify');
		$this->data['entry_coupon'] = $this->language->get('entry_coupon');
		$this->data['button_coupon'] = $this->language->get('button_coupon');

		$this->data['logged'] = $this->customer->isLogged();
		$this->data['shipping_required'] = $this->cart->hasShipping();

		// Force PPX Data
		$this->session->data['payment_method'] = array(
			'id'  			=> 'paypal_express', //v14x
			'code'  		=> 'paypal_express', //v15x
			'title' 		=> 'Paypal Express',
			'sort_order' 	=> '1'
		);

		//$this->session->data['payment_address_id'] = $this->customer->getAddressId();
		$this->session->data['comment'] = !empty($this->session->data['comment']) ? $this->session->data['comment'] : '';
		//

		// If paypal login looping, then this may be the cause. But not the underlying reason.
    	if ($this->customer->isLogged() && !isset($this->session->data['payment_address_id'])) {
			$this->redirect($this->url->link('payment/paypal_express/SetExpressCheckout', 'resetppx'));
		} elseif (!$this->customer->isLogged() && !isset($this->session->data['guest']['payment'])) {
			$this->redirect($this->url->link('payment/paypal_express/SetExpressCheckout', 'resetppx'));
		}

		// If shipping already set, then assume they used the normal checkout procedure and don't need to land on ppxcheckout. Just complete the order.
		if (($this->cart->hasShipping() && isset($this->session->data['shipping_method'])) || (!$this->cart->hasShipping())) {
			//$this->redirect($this->url->link('payment/paypal_express/DoExpressCheckoutPayment'));
			// for this to work I have to call checkout/confirm another way first. Currently that is done on the tpl so I still need it to load.
		}

		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/ppx_checkout.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/ppx_checkout.tpl';
		} else {
			$this->template = 'default/template/checkout/ppx_checkout.tpl';
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

	public function coupon() {
	   	if (isset($this->request->post['coupon'])) {

	   	 	$this->language->load('checkout/checkout');

			// Override the titles of the checkout steps
			$this->language->load('payment/paypal_express');

	   	 	$json = array();

			if ($this->request->post['coupon'] && $this->validateCoupon()) {
				$this->session->data['coupon'] = $this->request->post['coupon'];
				$json['success_coupon'] = $this->language->get('text_success_coupon');
			} else {
				// if empty, unset it with no error
				if ($this->request->post['coupon'] === "") {
					unset($this->session->data['coupon']);
					if (!empty($this->session->data['coupon'])) {
						$json['success_coupon'] = $this->language->get('text_coupon_removed');
					} else {
						$json['fail_coupon'] = $this->language->get('error_coupon');
					}
				} elseif ($this->request->post['coupon'] != "") {
					$json['fail_coupon'] = $this->language->get('error_coupon');
				} elseif ($this->error['warning']) {
					$json['fail_coupon'] = $this->error['warning'];
				}
			}

			$this->response->setOutput(json_encode($json));
		}
	}

	protected function validateCoupon() {
		$this->load->model('checkout/coupon');

		$coupon_info = $this->model_checkout_coupon->getCoupon($this->request->post['coupon']);

		if (!$coupon_info) {
			$this->error['warning'] = $this->language->get('error_coupon');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
?>