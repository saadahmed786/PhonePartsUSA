<?php  

class ControllerCheckoutPaymentMethod extends Controller {

	public function index() {

		error_reporting(1);

		$this->language->load('checkout/checkout');

		

		$this->load->model('account/address');

		

		// print_r($this->session->data);exit;



		// if ($this->customer->isLogged() && isset($this->session->data['payment_address_id'])) {

		// 	$payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);		

		// } elseif (isset($this->session->data['guest'])) {

		// 	$payment_address = $this->session->data['guest']['payment'];

		// }	

		if ($this->customer->isLogged() && isset($this->session->data['shipping_address_id'])) {

			$payment_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);		

		} elseif (isset($this->session->data['guest'])) {

			$payment_address = $this->session->data['guest']['shipping'];

		}	

		// print_r($payment_address);exit;

		$this->data['is_po'] = $this->customer->getPO();

		$this->data['po_no'] = '';

		

		if (!empty($payment_address)) {

			// Totals

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

			

			// Payment Methods

			$method_data = array();

			

			$this->load->model('setting/extension');

			

			$results = $this->model_setting_extension->getExtensions('payment');

			

			foreach ($results as $result) {

				// if (!isset($this->session->data['guest']['email']) || $this->session->data['guest']['email']!='zamantest@mailinator.com' ) {

				// 	if($result['code']=='behalf')

				// 	{

						

				// 	continue;

				// 	}

				// }

				if ($this->config->get($result['code'] . '_status')) {

					

					$this->load->model('payment/' . $result['code']);

					

						

					

					$method = $this->{'model_payment_' . $result['code']}->getMethod($payment_address, $total); 



					if ($method) {

						// print_r( $method )."<br>";

						$method_data[$result['code']] = $method;

					}

				}

			}

			// print_r($method_data);

			

			if(array_key_exists('free_checkout',$method_data))

			{

				// echo 'here';

				

				unset($method_data['pp_standard']);

				unset($method_data['paypal_express_new']);

				unset($method_data['pp_standard_new']);

				unset($method_data['pp_standard']);

				unset($method_data['pp_payflow_pro']);

			}

			// exit;



			if( $this->session->data['shipping_method']['id']!='multiflatrate.multiflatrate_0' )

			{

				unset($method_data['cod']);

			}

			else

			{

				foreach ($this->session->data['voucher'] as $_voucher ) {



				$coupon_query =  $this->db->query("SELECT product_limit_qty FROM ".DB_PREFIX."coupon WHERE code='".$this->db->escape($_voucher)."' AND has_product_limit=1 ");

						if($coupon_query->num_rows)

						{

							unset($method_data['pp_standard']);

				unset($method_data['paypal_express_new']);

				unset($method_data['pp_standard_new']);

				unset($method_data['pp_standard']);

				unset($method_data['pp_payflow_pro']);

						}

					}

			}

			



			$sort_order = array(); 



			foreach ($method_data as $key => $value) {

				$sort_order[$key] = $value['sort_order'];

			}



			array_multisort($sort_order, SORT_ASC, $method_data);			

			

			$this->session->data['payment_methods'] = $method_data;			

		}			

		

		$this->data['text_payment_method'] = $this->language->get('text_payment_method');

				$this->data['text_payment_coupon'] = $this->language->get('text_payment_coupon');
				$this->data['text_payment_couponEntered'] = $this->language->get('text_payment_couponEntered');
				$this->data['button_coupon'] = $this->language->get('button_coupon');
				$this->data['button_coupon_remove'] = $this->language->get('button_coupon_remove');
				$this->data['coupon_status'] = $this->config->get('coupon_status');
				$this->data['text_payment_voucher'] = $this->language->get('text_payment_voucher');
				$this->data['text_payment_voucherEntered'] = $this->language->get('text_payment_voucherEntered');
				$this->data['button_voucher'] = $this->language->get('button_voucher');
				$this->data['button_voucher_remove'] = $this->language->get('button_voucher_remove');
				$this->data['voucher_status'] = $this->config->get('voucher_status');
			

		$this->data['text_comments'] = $this->language->get('text_comments');



		$this->data['button_continue'] = $this->language->get('button_continue');



		if (empty($this->session->data['payment_methods'])) {

			$this->data['error_warning'] = sprintf($this->language->get('error_no_payment'), $this->url->link('information/contact'));

		} else {

			$this->data['error_warning'] = '';

		}	



		if (isset($this->session->data['payment_methods'])) {

			$this->data['payment_methods'] = $this->session->data['payment_methods']; 

		} else {

			$this->data['payment_methods'] = array();

		}



		unset($this->session->data['payment_method']['code']);



		// if (isset($this->session->data['payment_method']['code'])) {

		// 	$this->data['code'] = $this->session->data['payment_method']['code'];

		// } else {

		// 	$this->data['code'] = '';

		// }

		unset($this->session->data['payment_method']['code']);



		if(isset($this->session->data['ppx']['token']))

		{

			$this->data['code'] = 'paypal_express_new';



		}



		

		if (isset($this->session->data['comment'])) {

			$this->data['comment'] = $this->session->data['comment'];

		} else {

			$this->data['comment'] = '';

		}

		

		if ($this->config->get('config_checkout_id')) {

			$this->load->model('catalog/information');

			

			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));

			

			if ($information_info) {

				$this->data['text_agree'] = sprintf($this->language->get('text_agree'), $this->url->link('information/information/info', 'information_id=' . $this->config->get('config_checkout_id'), 'SSL'), $information_info['title'], $information_info['title']);

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



		$this->data['data'] = $this->data;




                        if(!isset($this->session->data['tmp_order_id'])){
                                $this->load->model('tool/combat_cart_loss');

                                $this->model_tool_combat_cart_loss->addUnconfirmedOrder();
                                //send email notification to store owner about new uncofirmed order
                                //if (!$this->customer->isLogged()) {
                                        $this->model_tool_combat_cart_loss->sendUnconfirmedOrderAlert($this->session->data['order_id']);
                                //}
                                //end admin emial alert
                        }
                        
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/payment_method.tpl')) {

			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/payment_method.tpl';

		} else {

			$this->template = 'default/template/checkout/payment_method.tpl';

		}

		

		$this->response->setOutput($this->render());

	}



	public function paymentType()

	{

		$this->data['months'] = array();







		for ($i = 1; $i <= 12; $i++) {



			$language_month = $this->language->get('entry_cc_start_date_month'.$i);



			if ($language_month == 'entry_cc_start_date_month'.$i || empty($language_month) ){



				$language_month = strftime('%B', mktime(0, 0, 0, $i, 1, 2000));//generate names



				//$language_month = sprintf('%02d', $i);//generate two digit number



			}







			$this->data['months'][] = array(



				'text'  => sprintf('%02d', $i).' - '.$language_month[0].''.$language_month[1].''.$language_month[2],



				'value' => sprintf('%02d', $i)



			);



		}

		$today = getdate();



		$this->data['year_expire'] = array();







		for ($i = $today['year']; $i < $today['year'] + 11; $i++) {



			$this->data['year_expire'][] = array(



				'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),



				'value' => strftime('%y', mktime(0, 0, 0, 1, 1, $i))



			);



		}



		$this->load->model('account/address');

		

		// print_r($this->session->data);exit;



		// if ($this->customer->isLogged() && isset($this->session->data['payment_address_id'])) {

		// 	$payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);		

		// } elseif (isset($this->session->data['guest'])) {

		// 	$payment_address = $this->session->data['guest']['payment'];

		// }	

		if ($this->customer->isLogged() && isset($this->session->data['shipping_address_id'])) {

			$payment_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);		

		} elseif (isset($this->session->data['guest'])) {

			$payment_address = $this->session->data['guest']['shipping'];

		}





		if (isset($this->session->data['guest']['email'])) {

			$this->data['email'] = $this->session->data['guest']['email'];

		}

		elseif(isset($this->session->data['first_step_email']))

		{

			$this->data['email'] = $this->session->data['first_step_email'];

		}

		 else {

			$this->data['email'] = '';

		}	



		if ($this->customer->isLogged()) {

			

			$this->data['email'] = $this->customer->getEmail();

		}

		// print_r($this->session->data);exit;

		// echo $this->data['email'];exit;



		if (isset($this->session->data['guest']['telephone'])) {

			$this->data['telephone'] = $this->session->data['guest']['telephone'];		

		}

		elseif(isset($this->session->data['logged_in']))

		{

			$this->data['telephone'] = $this->session->data['logged_in']['telephone'];

		}

		elseif($this->customer->isLogged())

		{

			$this->data['telephone'] = $this->customer->getTelephone();

		}

		else {

			$this->data['telephone'] = '';

		}

		



		if ($this->config->get('behalf_status')) {

			

			$this->data['behalf_client_token'] = $this->config->get('behalf_client_token');



			if($this->config->get('behalf_account')=='mockup')

			{

				$this->data['behalf_sdk_uri'] = 'https://sdk.demo.behalf.com/sdk/v4/mock/behalf_payment_sdk.js';

				$this->data['behalf_client_token'] = $this->config->get('behalf_client_token_mockup');

			}

			elseif($this->config->get('behalf_account')=='sandbox')

			{

				$this->data['behalf_sdk_uri'] = 'https://sdk.demo.behalf.com/sdk/v4/behalf_payment_sdk.js';

				$this->data['behalf_client_token'] = $this->config->get('behalf_client_token_sandbox');

			}

			else

			{

				$this->data['behalf_sdk_uri'] = 'https://sdk.behalf.com/sdk/v4/behalf_payment_sdk.js';	

				$this->data['behalf_client_token'] = $this->config->get('behalf_client_token');

			}

		}



		$this->data['payment_address'] = $payment_address;

		$payment_method = $this->request->post['payment_type'];



		$this->load->model('localisation/country');

			$this->data['countries'] = $this->model_localisation_country->getCountries();

			$this->load->model('localisation/zone');

			$this->data['zones'] = $this->model_localisation_zone->getZonesByCountryId($payment_address['country_id']);





			$this->load->model('setting/extension');

		$this->load->model('account/address');



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



		foreach ($total_data as $total) {

			if($total['code']=='sub_total')

			{

				$this->data['sub_total'] = $total['value'];

			}



			if($total['code']=='shipping')

			{

				$shipping_title = $total['title'];

				$this->data['shipping_cost'] = $total['value'];

			}



			elseif($total['code']=='total')

			{

				$this->data['total'] = $total['value'];

			}



			elseif($total['code']=='tax')

			{

				$this->data['tax'] = $total['value'];

			}



			elseif($total['code']=='voucher')

			{

				// $this->data['total'] = $total['text'];

				$voucher_total+=$total['value'];

			}

			





		}



		$this->data['behalf_buyer_id']=$this->db->query("SELECT behalf_buyer_id FROM ".DB_PREFIX."behalf_buyer WHERE lower(email)='".$this->db->escape($this->data['email'])."'")->row;



		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/misc/'.$payment_method.'.tpl')) {

			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/misc/'.$payment_method.'.tpl';

		} else {

			$this->template = 'default/template/misc/'.$payment_method.'.tpl';

		}

		// echo $this->template;exit;

		

		$this->response->setOutput($this->render());	

	}

	

	public function validate() {

		$json = array();

		

		// Validate if payment address has been set.

		$this->load->model('account/address');

		$this->load->model('checkout/order');

		

		if ($this->customer->isLogged() && isset($this->session->data['payment_address_id'])) {

			$payment_address = $this->model_account_address->getAddress($this->session->data['payment_address_id']);

		} elseif (isset($this->session->data['guest'])) {

			$payment_address = $this->session->data['guest']['payment'];

		}



		if (empty($payment_address)) {

			$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');

		}		

		

		// Validate cart has products and has stock.			

		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {

			$json['redirect'] = $this->url->link('checkout/cart');				

		}	

		

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

				$json['redirect'] = $this->url->link('checkout/cart');

				

				break;

			}				

		}



		if (!$json) {

			if (!isset($this->request->post['payment_method'])) {

				$json['error']['warning'] = $this->language->get('error_payment');

			} else {

				if (!isset($this->session->data['payment_methods'][$this->request->post['payment_method']])) {

					$json['error']['warning'] = $this->language->get('error_payment');

				}

			}	



			if ($this->config->get('config_checkout_id')) {

				$this->load->model('catalog/information');

				

				$information_info = $this->model_catalog_information->getInformation($this->config->get('config_checkout_id'));

				

				if ($information_info && !isset($this->request->post['agree'])) {

					$json['error']['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);

				}

			}

			

			if (!$json) {

				$this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['payment_method']];



				$this->session->data['comment'] = strip_tags($this->request->post['comment']);



				$this->session->data['po_no'] = $this->request->post['po_no'];							

			

			}

		}





		$this->response->setOutput(json_encode($json));

	}



	public function validate_new() {

		$json = array();

		

		// Validate if payment address has been set.

		$this->load->model('account/address');

		$this->load->model('checkout/order');

		

		if ($this->customer->isLogged() && isset($this->session->data['shipping_address_id'])) {

			$payment_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);

		} elseif (isset($this->session->data['guest'])) {

			$payment_address = $this->session->data['guest']['shipping'];

		}



		if (empty($payment_address)) {

			$json['redirect'] = $this->url->link('checkout/checkout', '', 'SSL');

		}		

		

		// Validate cart has products and has stock.			

		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {

			$json['redirect'] = $this->url->link('checkout/cart');				

		}	

		

		// Validate minimum quantity requirments.			

		$products = $this->cart->getProducts();

		// Validate customer data.

		data_validation($this->request->post);



		foreach ($products as $product) {

			$product_total = 0;



			foreach ($products as $product_2) {

				if ($product_2['product_id'] == $product['product_id']) {

					$product_total += $product_2['quantity'];

				}

			}		

			

			if ($product['minimum'] > $product_total) {

				$json['redirect'] = $this->url->link('checkout/cart');

				

				break;

			}				

		}



		if (!$json) {
			$is_debug = ($this->customer->getEmail()=='phonepartusa@gmail.com');
			$is_debug = true;
			if($this->request->post['payment_method']=='pp_payflow_pro')

			{

			if ( (utf8_strlen($this->request->post['inputFirstName']) < 1) || (utf8_strlen($this->request->post['inputFirstName']) > 100)) {

				$json['error']['firstname'] = 'error';

			}



			if ( (utf8_strlen($this->request->post['inputLastName']) < 1) || (utf8_strlen($this->request->post['inputLastName']) > 100)) {

				$json['error']['lastname'] = 'error';

			}



			if ( (utf8_strlen($this->request->post['inputStreet']) < 1) || (utf8_strlen($this->request->post['inputStreet']) > 100)) {

				$json['error']['address_1'] = 'error';

			}



			if ( (utf8_strlen($this->request->post['inputCity']) < 2) || (utf8_strlen($this->request->post['inputCity']) > 32)) {

				$json['error']['city'] = 'error';

			}

			if ( $this->request->post['inputState'] == '') {

				$json['error']['state'] = 'error';

			}



			if ( (utf8_strlen($this->request->post['inputZip']) < 3) || (utf8_strlen($this->request->post['inputZip']) > 6)) {

				$json['error']['zip'] = 'error';

			}



			if( !$this->luhn_check($this->request->post['cc_number']))

			{
				if(!$is_debug)
				{
				$json['error']['cc_number'] = 'error';
					
				}

			}



			if ( (utf8_strlen($this->request->post['cc_name']) < 2) || (utf8_strlen($this->request->post['inputZip']) > 50)) {

				if(!$is_debug)
				{

				$json['error']['cc_name'] = 'error';
					
				}

			}



			if ( (utf8_strlen($this->request->post['cc_cvv2']) < 2) || (utf8_strlen($this->request->post['cc_cvv2']) > 4)) {
				if(!$is_debug)
				{
				$json['error']['cvv2'] = 'error';
					
				}

			}

		}

		}

		

		if(!$json)

		{



			if (!isset($this->request->post['payment_method'])) {

				$json['error']['warning'] = $this->language->get('error_payment');

			} else {

				if (!isset($this->session->data['payment_methods'][$this->request->post['payment_method']])) {

					$json['error']['warning'] = $this->language->get('error_payment');

				}

			}	

			

			unset($this->session->data['newcheckout']);

			$this->session->data['newcheckout']['payment_method'] = $this->request->post['payment_method'];

			$this->session->data['payment_method']['code'] = $this->request->post['payment_method'];

			// $this->session->data['newcheckout']['payment_method'] = $this->request->post['payment_method'];

			

			if (isset($this->request->post['inputFirstName'])) {

				$this->session->data['newcheckout']['firstname'] = $this->request->post['inputFirstName'];

			}



			if (isset($this->request->post['inputLastName'])) {

				$this->session->data['newcheckout']['lastname'] = $this->request->post['inputLastName'];

			}



			if (isset($this->request->post['inputCompany'])) {

				$this->session->data['newcheckout']['company'] = $this->request->post['inputCompany'];

			}



			if (isset($this->request->post['inputStreet'])) {

				$this->session->data['newcheckout']['address_1'] = $this->request->post['inputStreet'];

			}

			if (isset($this->request->post['inputCity'])) {

				$this->session->data['newcheckout']['city'] = $this->request->post['inputCity'];

			}



			if (isset($this->request->post['inputSuite'])) {

				$this->session->data['newcheckout']['address_2'] = $this->request->post['inputSuite'];

			}

			if (isset($this->request->post['inputZip'])) {

				$this->session->data['newcheckout']['zip'] = $this->request->post['inputZip'];

				$this->session->data['newcheckout']['postcode'] = $this->request->post['inputZip'];

			}



			if (isset($this->request->post['inputState'])) {

				$this->session->data['newcheckout']['zone_id'] = $this->request->post['inputState'];

			}



			if (isset($this->request->post['inputCountry'])) {

				$this->session->data['newcheckout']['country_id'] = $this->request->post['inputCountry'];

			}



			if (isset($this->request->post['cc_number'])) {

				$this->session->data['newcheckout']['cc_name'] = $this->request->post['cc_name'];

				$this->session->data['newcheckout']['cc_number'] = $this->request->post['cc_number'];

				$this->session->data['newcheckout']['cc_expire_date_month'] = $this->request->post['cc_expire_date_month'];

				$this->session->data['newcheckout']['cc_expire_date_year'] = $this->request->post['cc_expire_date_year'];

				$this->session->data['newcheckout']['cc_cvv2'] = $this->request->post['cc_cvv2'];

			}



			if($this->request->post['payment_method']=='paypal_express_new' and !$this->customer->isLogged())

			{

				$this->session->data['newcheckout'] = $this->session->data['guest']['payment'];

				$this->session->data['newcheckout']['zip'] = $this->session->data['guest']['payment']['postcode'];

			}



		}

		if(!$json && $is_debug && $this->request->post['payment_method']=='pp_payflow_pro')
		{
			$json['redirect'] = $this->url->link('payment/pp_payflow_pro/DoExpressCheckoutPayment');
		}



		$this->response->setOutput(json_encode($json));

	}



	private function luhn_check($cc, $extra_check = false){

    $cards = array(

        "visa" => "(4\d{12}(?:\d{3})?)",

        "amex" => "(3[47]\d{13})",

        "jcb" => "(35[2-8][89]\d\d\d{10})",

        "maestro" => "((?:5020|5038|6304|6579|6761)\d{12}(?:\d\d)?)",

        "solo" => "((?:6334|6767)\d{12}(?:\d\d)?\d?)",

        "mastercard" => "(5[1-5]\d{14})",

        "switch" => "(?:(?:(?:4903|4905|4911|4936|6333|6759)\d{12})|(?:(?:564182|633110)\d{10})(\d\d)?\d?)",

        "discover"=> "([6011]{4})([0-9]{12})"

    );

    $names = array("Visa", "American Express", "JCB", "Maestro", "Solo", "Mastercard", "Switch","Discover");

    $matches = array();

    $pattern = "#^(?:".implode("|", $cards).")$#";

    $result = preg_match($pattern, str_replace(" ", "", $cc), $matches);

    if($extra_check && $result > 0){

        $result = (validatecard($cc))?1:0;

    }

    // echo $result;

    // print_r($matches);exit;

    // echo $names[sizeof($matches)-2];exit;

    return ($result>0)?true:false;

}

}

?>