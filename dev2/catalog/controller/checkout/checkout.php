<?php  
class ControllerCheckoutCheckout extends Controller { 
	public function index() {
		// $this->redirect('http://dev2.phonepartsusa.com/new_checkout');
		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
	  		
	  		$this->redirect($this->url->link('checkout/cart'));
    	}
    	if(isset($this->request->get['nc']) && $this->request->get['nc']==1)
    	{
    		unset($this->session->data['ppx']);
    	}
    	$this->document->addScript('catalog/view/javascript/ppusa2.0/labelholder.js');
		$this->document->addStyle('catalog/view/theme/ppusa2.0/stylesheet/labelholder.css');
	
		
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
				$this->redirect($this->url->link('checkout/cart'));
			}				
		}
				
		$this->language->load('checkout/checkout');
		
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
			'href'      => $this->url->link('checkout/checkout', '', 'SSL'),
        	'separator' => $this->language->get('text_separator')
      	);
					
	    $this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['text_checkout_option'] = $this->language->get('text_checkout_option');
		$this->data['text_checkout_account'] = $this->language->get('text_checkout_account');
		$this->data['text_checkout_payment_address'] = $this->language->get('text_checkout_payment_address');
		$this->data['text_checkout_shipping_address'] = $this->language->get('text_checkout_shipping_address');
		$this->data['text_checkout_shipping_method'] = $this->language->get('text_checkout_shipping_method');
		$this->data['text_checkout_payment_method'] = $this->language->get('text_checkout_payment_method');		
		$this->data['text_checkout_confirm'] = $this->language->get('text_checkout_confirm');
		$this->data['text_modify'] = $this->language->get('text_modify');
		
		$this->data['logged'] = $this->customer->isLogged();
		$this->data['shipping_required'] = $this->cart->hasShipping();	
		
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/checkout.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/checkout.tpl';
		} else {
			$this->template = 'default/template/checkout/checkout.tpl';
		}
		
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'	,
			'module/checkout_right_cart'
		);

		
			
				
		
		$this->load->model('localisation/country');

		if (isset($this->request->post['country_id'])) {
				$this->data['country_id'] = $this->request->post['country_id'];				
			} elseif (isset($this->session->data['shipping_country_id'])) {
				$this->data['country_id'] = $this->session->data['shipping_country_id'];			  	
			} else {
				$this->data['country_id'] = $this->config->get('config_country_id');
			}
			
		$this->data['countries'] = $this->model_localisation_country->getCountries();
		$this->response->setOutput($this->render());
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
	public function city() {
		$state = $this->request->get['state'];
		$this->load->model('localisation/city');
		$json  = $this->model_localisation_city->getCitiesByZoneId($state);	
		echo json_encode($json);
	}
	public function order_confirm()
	{
		$this->load->model('account/address');
		$this->load->model('localisation/zone');
		
		if ($this->customer->isLogged() && isset($this->session->data['shipping_address_id'])) {
			$shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);		
		} elseif (isset($this->session->data['guest'])) {
			$shipping_address = $this->session->data['guest']['shipping'];
		}
		// print_r($this->session->data);exit;

		if ($this->customer->isLogged() && isset($this->session->data['payment_address_id'])) {
			$payment_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);		
		} elseif (isset($this->session->data['guest'])) {
			$payment_address = $this->session->data['newcheckout'];
		}

		// $this->data['contact_firstname'] = $shipping_address['firstname'];
		// $this->data['contact_lastname'] = $shipping_address['lastname'];
		// $this->data['contact_email'] = $shipping_address['email'];
		if($this->customer->isLogged())
		{
		$this->data['contact_firstname'] = $this->session->data['logged_in']['firstname'];
		$this->data['contact_lastname'] = $this->session->data['logged_in']['lastname'];
		$this->data['contact_email'] = $this->session->data['logged_in']['email'];
		$this->data['contact_phone'] = $this->session->data['logged_in']['phone'];

		}
		else
		{
		$this->data['contact_firstname'] = $this->session->data['guest']['firstname'];
		$this->data['contact_lastname'] = $this->session->data['guest']['lastname'];
		$this->data['contact_email'] = $this->session->data['guest']['email'];
		$this->data['contact_phone'] = $this->session->data['guest']['telephone'];

		}
		$this->data['shipping_firstname'] = $shipping_address['firstname'];
		$this->data['shipping_lastname'] = $shipping_address['lastname'];
		$this->data['shipping_company'] = $shipping_address['company'];
		$this->data['shipping_address_1'] = $shipping_address['address_1'];
		$this->data['shipping_address_2'] = $shipping_address['address_2'];
		$this->data['shipping_zip'] = $shipping_address['postcode'];
		$this->data['shipping_city'] = $shipping_address['city'];
		$this->data['shipping_state'] = $this->model_localisation_zone->getZone($shipping_address['zone_id'])['name'];


		$this->data['payment_firstname'] = $payment_address['firstname'];
		$this->data['payment_lastname'] = $payment_address['lastname'];
		$this->data['payment_company'] = $payment_address['company'];
		if($this->session->data['newcheckout']['address_1'])
		{
			$this->data['payment_address_1'] = $this->session->data['newcheckout']['address_1'];
		$this->data['payment_address_2'] = $this->session->data['newcheckout']['address_2'];
		$this->data['payment_zip'] = $this->session->data['newcheckout']['postcode'];
		$this->data['payment_city'] = $this->session->data['newcheckout']['city'];
		$this->data['payment_state'] = $this->model_localisation_zone->getZone($this->session->data['newcheckout']['zone_id'])['name'];

		}
		else
		{
		$this->data['payment_address_1'] = $payment_address['address_1'];
		$this->data['payment_address_2'] = $payment_address['address_2'];
		$this->data['payment_zip'] = $payment_address['postcode'];
		$this->data['payment_city'] = $payment_address['city'];
		$this->data['payment_state'] = $this->model_localisation_zone->getZone($payment_address['zone_id'])['name'];
			
		}
		$this->data['shipping_method'] = $this->session->data['shipping_method']['title'];

		$this->data['cart_items'] = array();

			$products = $this->cart->getProducts();
				$this->load->model('tool/image');
				$this->load->model('catalog/product');
		foreach ($products as $product) {

				$discountsData = $this->model_catalog_product->getProductDiscounts($product['product_id']);

				$discounts = array(); 
				$discounts[] = array(
					'quantity' => '1' . (($discountsData[0]) ? '-' . ($discountsData[0]['quantity'] - 1) : ''),
					'price'    => $product['price']
					);
				foreach ($discountsData as $key => $discount) {
					$discounts[] = array(
						'quantity' => $discount['quantity'] . (($discountsData[($key + 1)]) ? '-' . ($discountsData[($key + 1)]['quantity'] - 1) : '+'),
						'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')))
						);
				}
			
			if ($product['image']) {
					$image = $this->model_tool_image->resize($product['image'], $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
				} else {
					$image = $this->model_tool_image->resize('data/image-coming-soon.jpg', $this->config->get('config_image_cart_width'), $this->config->get('config_image_cart_height'));
				}


			$this->data['cart_items'][] = array(
				'image'=>$image,
				'name'=>$product['name'],
				'quantity'=>$product['quantity'],
				'price'=>$this->currency->format($product['price']),
				'total'=>$this->currency->format($product['total']),
				'model'=>$product['model'],
				'discounts'=>$discounts
				);	
		}
		// echo ""
		// print_r($this->session->data);exit;


		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/order_confirm.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/order_confirm.tpl';
		} else {
			$this->template = 'default/template/checkout/order_confirm.tpl';
		}

		$this->response->setOutput($this->render());
	}
	public function guest_first_step()
	{
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/guest_first_step.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/guest_first_step.tpl';
		} else {
			$this->template = 'default/template/checkout/guest_first_step.tpl';
		}		
		$this->response->setOutput($this->render());
	}
	public function validate_guest_first_step()
	{
		// $email = $this->request->post['email'];
		$this->load->model('account/customer');
		$json=array();
		if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
				$json['error'] = $this->language->get('error_email');
			}
			elseif($this->model_account_customer->getCustomerByEmail($this->request->post['email']))
			{
				$json['error2'] = 'exist';
			}
			else{
				$this->session->data['first_step_email'] = $this->request->post['email'];
			}
			echo json_encode($json);
	}
}
?>