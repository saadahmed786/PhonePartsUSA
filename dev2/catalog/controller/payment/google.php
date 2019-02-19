<?php
//Initial Version 1.0 
//Module: Google Payment for OpenCart
class ControllerPaymentGoogle extends Controller {
		protected function index() {
		
		$merchant_id =  $this->config->get('google_merchant_id');
		$merchant_key = $this->config->get('google_merchant_key');
		$httpMethod = "POST";

		if ($this->config->get('google_production')) {
			$serviceEndPoint = 'https://checkout.google.com/api/checkout/v2/checkout/Merchant/' . $merchant_id;	
		} else {
			$serviceEndPoint= 'https://sandbox.google.com/checkout/api/checkout/v2/checkout/Merchant/' . $merchant_id;
		}

		$this->data['button_confirm'] = "<img src=\"https://checkout.google.com/buttons/checkout.gif?merchant_id=907054239305601&w=180&h=46&style=white&variant=text&loc=en_US\" border=\"0\">\n";
		//$this->data['button_confirm'] = $this->language->get('button_confirm');
		
		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		if ($order_info) {

			$currencies = array(

				'AUD',

				'CAD',

				'EUR',

				'GBP',

				'JPY',

				'USD',

				'NZD',

				'CHF',

				'HKD',

				'SGD',

				'SEK',

				'DKK',

				'PLN',

				'NOK',

				'HUF',

				'CZK',

				'ILS',

				'MXN',

				'MYR',

				'BRL',

				'PHP',

				'TWD',

				'THB',

				'TRY'

			);

			

			if (in_array($order_info['currency_code'], $currencies)) {

				$currency = $order_info['currency_code'];

			} else {

				$currency = 'USD';

			}		

		//////////////////////////////////////////////////////
		
		// Get all totals
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
		$discount_total = 0;	
		foreach ($results as $result) {
			if ($this->config->get($result['code'] . '_status')) {
				$this->load->model('total/' . $result['code']);
				$old_total = $total;
				$this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
				if ($total < $old_total) {
				$discount_total += $old_total - $total;
				}
			}
		}
		$tax_total = 0;
		$total = $this->currency->format($total, $currency, FALSE, FALSE);
		foreach ($taxes as $key => $value) {
				$tax_total += $this->currency->format($value, $currency, FALSE, FALSE);
		}		
		/////////////////////////////////////////////////////
		$shipping_total = 0;
		$shipping_title = "Shipping";
		
		//The Google Cart	
		$googlecart = array();

		$product_total = 0;
		
		//Calc Shipping
		if ($this->cart->hasShipping()) {
			$shipping_total = $this->currency->format($this->session->data['shipping_method']['cost'], $currency, FALSE, FALSE);
			$shipping_title = $this->session->data['shipping_method']['title'];

		}

		//XML Cart
		$gXML = '<?xml version="1.0" encoding="UTF-8"?>';
		$gXML .= '<checkout-shopping-cart xmlns="http://checkout.google.com/schema/2">';
		$gXML .=  '<shopping-cart>';
		$gXML .= '<merchant-private-data><order_id>' . $this->session->data['order_id'] . '</order_id></merchant-private-data>'; 
		$gXML .=  '<items>';
		
		foreach ($this->cart->getProducts() as $product) {
		$price = $this->currency->format($product['price'], $currency, FALSE, FALSE);
		$gXML .=  '<item>';
		$gXML .= '<item-name>' . $product['name'] . '</item-name>' ;
		$gXML .= '<item-description>' . $product['name'] . '</item-description>' ;
		$gXML .= '<unit-price currency="' .$currency . '">' . $price . '</unit-price>' ;
		$gXML .= '<quantity>' . $product['quantity'] . '</quantity>';
		$gXML .= '</item>';
		
		$product_total += ($price * $product['quantity']);
		}
		
		$remaining_total = $total - $product_total - $tax_total - $shipping_total + $discount_total;
		$handling_fee = number_format(abs($remaining_total), 2, '.', '');
		if ($handling_fee > 0) {
			
			$gXML .=  '<item>';
			$gXML .= '<item-name>Handling Charges</item-name>' ;
			$gXML .= '<item-description>Handling Charges</item-description>' ;
			$gXML .= '<unit-price currency="' .$currency . '">' . $handling_fee . '</unit-price>' ;
			$gXML .= '<quantity>1</quantity>';
			$gXML .= '</item>';
		}

		if ($tax_total > 0) {
			
			$gXML .=  '<item>';
			$gXML .= '<item-name>Taxes</item-name>' ;
			$gXML .= '<item-description>Taxes Charged</item-description>' ;
			$gXML .= '<unit-price currency="' .$currency . '">' . $tax_total . '</unit-price>' ;
			$gXML .= '<quantity>1</quantity>';
			$gXML .= '</item>';
		}

		//Is there a Coupon
		if($discount_total > 0 ){
		$discount_price = $this->currency->format($discount_total, $currency, FALSE, FALSE);
		$gXML .=  '<item>';
		$gXML .= '<item-name>Coupon Discount</item-name>' ;
		$gXML .= '<item-description>Coupon Discount</item-description>' ;
		$gXML .= '<unit-price currency="' .$currency . '">-' . $discount_price . '</unit-price>' ;
		$gXML .= '<quantity>1</quantity>';
		$gXML .= '</item>';
		}

		$gXML .= '</items>';
		$gXML .= '</shopping-cart>';
		$gXML .= '<checkout-flow-support>';
		$gXML .= '<merchant-checkout-flow-support>';
		$gXML .= '<shipping-methods>';
		$gXML .= '<flat-rate-shipping name="' .  $shipping_title . '">';
		$gXML .= '<price currency="' . $currency . '">' .  $shipping_total . '</price>';
		$gXML .= '<shipping-restrictions>'; 
        $gXML .= '<allowed-areas>';
        $gXML .= '<world-area/>';
        $gXML .= '</allowed-areas>';    
        $gXML .= '<allow-us-po-box>true</allow-us-po-box>';		
        $gXML .= '</shipping-restrictions>'; 
		$gXML .= '</flat-rate-shipping>';
		$gXML .= '</shipping-methods>';
		$gXML .= '</merchant-checkout-flow-support>';
		$gXML .= '</checkout-flow-support>';
		$gXML .= '</checkout-shopping-cart>';

		//Security - Encoded Cart XML and Generated Signature
		$googlecart['cart'] = base64_encode($gXML);
		$googlecart['signature'] = $this->calculateRFC2104HMAC($gXML, $merchant_key);
		
		$this->data['google_form'] = $this->getPayNowWidgetForm($httpMethod,$serviceEndPoint,$googlecart);

		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/payment/google.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/payment/google.tpl';
		} else {
			$this->template = 'default/template/payment/google.tpl';
		}			
			


	

			$this->render();

		}

	}

	

		public function callback() {
		//Call Amazon Api
		$this->language->load('payment/google');
		
		$this->data['charset'] = $this->language->get('charset');
		$this->data['language'] = $this->language->get('code');
		$this->data['direction'] = $this->language->get('direction');
		
		$this->data['title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));	
		$this->data['heading_title'] = sprintf($this->language->get('heading_title'), $this->config->get('config_name'));
		$this->data['text_response'] = $this->language->get('text_response');
		$this->data['text_success'] = $this->language->get('text_success');
		$this->data['text_success_wait'] = sprintf($this->language->get('text_success_wait'), HTTPS_SERVER . 'index.php?route=checkout/success');

		$this->data['base'] = HTTPS_SERVER;
		
		$xml_data= isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA : file_get_contents("php://input");

		if (get_magic_quotes_gpc()) { 
		$xml_data = stripslashes($xml_data); 
		} 
		
		$NotificationXML=simplexml_load_string($xml_data);

		$serial = (string) $NotificationXML->attributes()->{'serial-number'};
		$google_order_number = (string) $NotificationXML->{'google-order-number'};
		$message_recognizer = (string)$NotificationXML->getName() ;
		$order_id = (string)$NotificationXML->{'order-summary'}->{'shopping-cart'}->{'merchant-private-data'}->{'order_id'};
		
		$message = "";
		//Time to commit order
		if ($message_recognizer=="new-order-notification"){
		$this->load->model('checkout/order');
		$message = "Google Order:" . $google_order_number ;
		$this->model_checkout_order->confirm($order_id, $this->config->get('google_order_status_id') ,$message);
		}
		
		//Send Ack All-IS-WELL 
		$schema_url = "http://checkout.google.com/schema/2";
		header('HTTP/1.0 200 OK');
		$acknowledgment = '<?xml version="1.0" encoding="UTF-8"?>' . '<notification-acknowledgment xmlns="' . $schema_url . '"';
 		if(isset($serial)) {
			$acknowledgment .=" serial-number=\"" . $serial."\"";
		}                  
		$acknowledgment .= " />";
		echo $acknowledgment;

	}
 
   public function confirm(){
 		//Clear Cart before sending to Google Checkout
		if (isset($this->session->data['order_id'])) {
			$this->cart->clear();
			
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['guest']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);	
			unset($this->session->data['coupon']);
		}		
		//Submit Form
 
   }
   
   public function calculateRFC2104HMAC($data, $key) {
    // compute the hmac on input data bytes, make sure to set returning raw hmac to be true
    $rawHmac = hash_hmac("sha1", $data, $key, true);

    // base64-encode the raw hmac
    return base64_encode($rawHmac);
  }

	public  function getPayNowWidgetForm($httpMethod,$serviceEndPoint,array $formHiddenInputs) {
	
		$form = "";
		$form .=  "<form id=\"payment\" action=\""; 
		$form .= $serviceEndPoint;
		$form .= "\" method=\"";
		$form .= $httpMethod . "\">\n";
		
		foreach ($formHiddenInputs  as $name => $value) {
			$form .= "<input type=\"hidden\" name=\"$name";  
			$form .= "\" value=\"$value";
			$form .= "\" >\n";
		}
		$form .= "</form>\n";
		return $form;
	}
	
}
?>