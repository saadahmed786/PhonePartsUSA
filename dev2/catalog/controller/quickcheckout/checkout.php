<?php  
class ControllerQuickcheckoutCheckout extends Controller { 
public function index() {

        $config = $this->config->get('quickcheckout');
      
		// Validate cart has products and has stock.
		if ((!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) || (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout'))) {
	  		$this->redirect($this->url->link('checkout/cart'));
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
				$this->redirect($this->url->link('checkout/cart'));
			}				
		}
				
		$this->language->load('checkout/checkout');
		
		$this->document->setTitle($this->language->get('heading_title')); 
		$this->document->addScript('catalog/view/javascript/jquery/colorbox/jquery.colorbox-min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/colorbox/colorbox.css');
					
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
		
		
        
/* DV code*/
$this->document->addScript('catalog/view/javascript/dreamvention/tinysort/jquery.tinysort.min.js');
if(strpos($this->language->get('text_checkout_option'), '1')){
$this->data['text_checkout_option'] = substr($this->language->get('text_checkout_option'), strpos ( $this->language->get('text_checkout_option') , ' ' ,strpos($this->language->get('text_checkout_option'), '1') ));
}else{$this->data['text_checkout_option'] = $this->language->get('text_checkout_option');}
if(strpos($this->language->get('text_checkout_account'), '2')){
$this->data['text_checkout_account'] = substr($this->language->get('text_checkout_account'), strpos ( $this->language->get('text_checkout_account') , ' ' ,strpos($this->language->get('text_checkout_account'), '2') ));
}else{$this->data['text_checkout_account'] = $this->language->get('text_checkout_account');}

if(strpos($this->language->get('text_checkout_payment_address'), '2')){
$this->data['text_checkout_payment_address'] = substr($this->language->get('text_checkout_payment_address'), strpos ( $this->language->get('text_checkout_payment_address') , ' ' ,strpos($this->language->get('text_checkout_payment_address'), '2') ));
}else{$this->data['text_checkout_payment_address'] = $this->language->get('text_checkout_payment_address');}

if(strpos($this->language->get('text_checkout_shipping_address'), '3')){
$this->data['text_checkout_shipping_address'] = substr($this->language->get('text_checkout_shipping_address'), strpos ( $this->language->get('text_checkout_shipping_address') , ' ' ,strpos($this->language->get('text_checkout_shipping_address'), '3') ));
}else{$this->data['text_checkout_shipping_address'] = $this->language->get('text_checkout_shipping_address');}
if(strpos($this->language->get('text_checkout_shipping_method'), '4')){
$this->data['text_checkout_shipping_method'] = substr($this->language->get('text_checkout_shipping_method'), strpos ( $this->language->get('text_checkout_shipping_method') , ' ' ,strpos($this->language->get('text_checkout_shipping_method'), '4') ));
}else{$this->data['text_checkout_shipping_method'] = $this->language->get('text_checkout_shipping_method');}
if(strpos($this->language->get('text_checkout_payment_method'), '5')){
$this->data['text_checkout_payment_method'] = substr($this->language->get('text_checkout_payment_method'), strpos ( $this->language->get('text_checkout_payment_method') , ' ' ,strpos($this->language->get('text_checkout_payment_method'), '5') ));
}else{$this->data['text_checkout_payment_method'] = $this->language->get('text_checkout_payment_method');}
if(strpos($this->language->get('text_checkout_confirm'), '6')){
$this->data['text_checkout_confirm'] = substr($this->language->get('text_checkout_confirm'), strpos ( $this->language->get('text_checkout_confirm') , ' ' ,strpos($this->language->get('text_checkout_confirm'), '6') ));
}else{$this->data['text_checkout_confirm'] = $this->language->get('text_checkout_confirm');}
$this->data['checkout_min_order'] = $this->currency->format($config['checkout_min_order']);

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
if($config['checkout_min_order'] < $total || $config['checkout_min_order'] == 0){$this->data['checkout_min_order_reached'] = 0; }else{$this->data['checkout_min_order_reached'] = 1;};			
			
/* DV code end*/







		$this->data['text_modify'] = $this->language->get('text_modify');
		
		$this->data['logged'] = $this->customer->isLogged();
		$this->data['shipping_required'] = $this->cart->hasShipping();	
		
		
               
		if($config['quickcheckout_display']){$quickcheckout = 'quickcheckout';}else{ $quickcheckout= 'checkout';}
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/'.$quickcheckout.'/checkout.tpl')) {
          $this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/'.$quickcheckout.'/checkout.tpl';
		  } else {
			$this->template = 'default/template/'.$quickcheckout.'/checkout.tpl';
      



		}
		
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'	
		);

        if(!isset($this->session->data['guest']['payment']) || !isset($this->session->data['guest']['shipping'])){
		if($config['payment_method_agree_display']){$agree= 1;}else{$agree= 0;}
		if(isset($this->session->data['payment_methods'])){
		$payment_method_array = array_values($this->session->data['payment_methods']);
		$payment_method_array = array_shift($payment_method_array);
		}else{
			$payment_method_array = array(
			'code' => '',
            'title' => '',
            'sort_order' => '');
		}
		if(isset($this->session->data['shipping_methods'])){
		$shipping_method_array = array_values($this->session->data['shipping_methods']);
		$shipping_method_array = array_shift($shipping_method_array);
		}else{
			$shipping_method_array = array(
			'code' => '',
            'title' => '',
            'cost' => '',
            'tax_class_id' => '',
            'text' => '');
		}
		
		
          $this->session->data['guest'] = array(
          'shipping_address' => 1,
          'customer_group_id' => 1,
          'firstname' => '',
          'lastname' => '',
          'email' => '',
          'telephone' => '',
          'fax' => '',
          'payment' => array
          (
          'country_id' => $this->config->get('config_country_id'),
		  'firstname' => '',
			'lastname' => '',
			'company' => '',
			'company_id' => '',
			'tax_id' => '',
			'address_1' => '',
			'address_2' => '',
			'postcode' => '',
			'city' => '',
			'zone_id' => $this->config->get('config_zone_id'),
			'country' => '',
			'iso_code_2' => '',
			'iso_code_3' => '',
			'address_format' => '',
			'zone' => '',
			'zone_code' => ''
          ),
          'shipping' => array
          (
          'country_id' => $this->config->get('config_country_id'),
		  'firstname' => '',
			'lastname' => '',
			'company' => '',
			'address_1' => '',
			'address_2' => '',
			'postcode' => '',
			'city' => '',	
			'zone_id' => $this->config->get('config_zone_id'),
			'country' => '',
			'iso_code_2' => '',
			'iso_code_3' => '',
			'address_format' => '',
			'zone' => '',
			'zone_code' => ''
          ),
		  'payment_country_id' => $this->config->get('config_country_id'),
			'payment_zone_id' => $this->config->get('config_zone_id'),
			'shipping_country_id' => $this->config->get('config_country_id'),
			'shipping_zone_id' => $this->config->get('config_zone_id'),
			'shipping_postcode' => '',
			'agree' => $agree,
		'shipping_method' => array
        (
            'code' => (isset($shipping_method_array['code']) ? $shipping_method_array['code'] : ''),
            'title' => (isset($shipping_method_array['title']) ? $shipping_method_array['title'] : ''),
            'cost' => (isset($shipping_method_array['cost']) ? $shipping_method_array['cost'] : ''),
            'tax_class_id' => (isset($shipping_method_array['tax_class_id']) ? $shipping_method_array['tax_class_id'] : ''),
            'text' => (isset($shipping_method_array['text']) ? $shipping_method_array['text'] : ''),
			'date' => ''
        ),
		
		'payment_method' => array
        (
            'code' => (isset($payment_method_array['code']) ? $payment_method_array['code'] : ''),
            'title' => (isset($payment_method_array['title']) ? $payment_method_array['title'] : ''),
            'sort_order' => (isset($payment_method_array['sort_order']) ? $payment_method_array['sort_order'] : '')
        )
		);
          }  
		  
      
				
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
}
?>