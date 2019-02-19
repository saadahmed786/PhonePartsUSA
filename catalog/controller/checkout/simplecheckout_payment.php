<?php  
/*
@author	Dmitriy Kubarev
@link	http://www.simpleopencart.com
@link	http://www.opencart.com/index.php?route=extension/extension/info&extension_id=4811
*/  

class ControllerCheckoutSimpleCheckoutPayment extends Controller {
    public function index() {
        
         $this->language->load('checkout/simplecheckout');

        $this->data['address_empty'] = false;
        
        $address = array(
            'firstname' => isset($this->session->data['guest']['payment']['firstname']) ? $this->session->data['guest']['payment']['firstname'] : '',
            'lastname' => isset($this->session->data['guest']['payment']['lastname']) ? $this->session->data['guest']['payment']['lastname'] : '',
            'company' => isset($this->session->data['guest']['payment']['company']) ? $this->session->data['guest']['payment']['company'] : '',
            'company_id' => isset($this->session->data['guest']['payment']['company_id']) ? $this->session->data['guest']['payment']['company_id'] : '',
            'tax_id' => isset($this->session->data['guest']['payment']['tax_id']) ? $this->session->data['guest']['payment']['tax_id'] : '',
            'address_1' => isset($this->session->data['guest']['payment']['address_1']) ? $this->session->data['guest']['payment']['address_1'] : '',
            'address_2' => isset($this->session->data['guest']['payment']['address_2']) ? $this->session->data['guest']['payment']['address_2'] : '',
            'postcode' => isset($this->session->data['guest']['payment']['postcode']) ? $this->session->data['guest']['payment']['postcode'] : '',
            'city' => isset($this->session->data['guest']['payment']['city']) ? $this->session->data['guest']['payment']['city'] : '',
            'zone_id' => isset($this->session->data['guest']['payment']['zone_id']) ? $this->session->data['guest']['payment']['zone_id'] : '',
            'zone' => isset($this->session->data['guest']['payment']['zone']) ? $this->session->data['guest']['payment']['zone'] : '',
            'zone_code' => isset($this->session->data['guest']['payment']['zone_code']) ? $this->session->data['guest']['payment']['zone_code'] : '',
            'country_id' => isset($this->session->data['guest']['payment']['country_id']) ? $this->session->data['guest']['payment']['country_id'] : '',
            'country' => isset($this->session->data['guest']['payment']['country']) ? $this->session->data['guest']['payment']['country'] : '', 
            'iso_code_2' => isset($this->session->data['guest']['payment']['iso_code_2']) ? $this->session->data['guest']['payment']['iso_code_2'] : '',
            'iso_code_3' => isset($this->session->data['guest']['payment']['iso_code_3']) ? $this->session->data['guest']['payment']['iso_code_3'] : '',
            'address_format' => isset($this->session->data['guest']['payment']['address_format']) ? $this->session->data['guest']['payment']['address_format'] : '',
            'address_id' => isset($this->session->data['guest']['payment']['address_id']) ? $this->session->data['guest']['payment']['address_id'] : ''
        );
      
        if ($address['country_id'] == '' && !empty($this->session->data['guest']['display_payment_address_fields']['payment_country_id'])) {
            $this->data['address_empty'] = true;
        }
        
        if ($address['zone_id'] == '' && !empty($this->session->data['guest']['display_payment_address_fields']['payment_zone_id'])) {
            $this->data['address_empty'] = true;
        }
        
        if ($address['city'] == '' && !empty($this->session->data['guest']['display_payment_address_fields']['payment_city'])) {
            $this->data['address_empty'] = true;
        }
        
        if ($address['postcode'] == '' && !empty($this->session->data['guest']['display_payment_address_fields']['payment_postcode'])) {
            $this->data['address_empty'] = true;
        }
        
        $this->data['text_checkout_payment_method'] = $this->language->get('text_checkout_payment_method');        
            
        $this->data['simple_payment_view_address_empty'] = $this->config->get('simple_payment_view_address_empty');
        $simple_payment_view_address_full = $this->config->get('simple_payment_view_address_full');
        $simple_payment_view_autoselect_first = $this->config->get('simple_payment_view_autoselect_first');
        
        $this->data['payment_methods'] = array();
      
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

        $method_data = array();
        
        $this->load->model('setting/extension');
        
        $results = $this->model_setting_extension->getExtensions('payment');
        
        $simple_links = $this->config->get('simple_links');
        $shipping_method_code = !empty($this->session->data['shipping_method']['code']) ? $this->session->data['shipping_method']['code'] : false;
        $shipping_method_code = $shipping_method_code ? explode('.',$shipping_method_code) : false;
        $shipping_method_code = $shipping_method_code ? $shipping_method_code[0] : false;
        
        foreach ($results as $result) {
            $show_module = true;
            if (!$this->data['address_empty']) {
                $show_module = true;
            } elseif ($this->data['address_empty'] && !empty($simple_payment_view_address_full[$result['code']])) {
                $show_module = false;
            } 
            
            if ($this->config->get($result['code'] . '_status') && $show_module) {
                
                $check_shipping_methods = false;
                if ($shipping_method_code && isset($simple_links) && isset($simple_links[$result['code']]) && $simple_links[$result['code']]) {
                    $check_shipping_methods = explode(",",$simple_links[$result['code']]);
                }
                
                if (!$check_shipping_methods || ($check_shipping_methods && in_array($shipping_method_code, $check_shipping_methods))) {
                    $this->load->model('payment/' . $result['code']);
                    
                    $method = $this->{'model_payment_' . $result['code']}->getMethod($address, $total); 
                    
                    if ($method) {
                        $method_data[$result['code']] = $method;
                    }
                }
            }
        }
                     
        $sort_order = array(); 
      
        foreach ($method_data as $key => $value) {
            $sort_order[$key] = $value['sort_order'];
        }

        array_multisort($sort_order, SORT_ASC, $method_data);            
        
        $this->data['payment_methods'] = $this->session->data['payment_methods'] = $method_data;    
        
        $this->data['text_payment_address'] = $this->language->get('text_payment_address');
        
        $this->data['code'] = '';
        
        if (empty($this->session->data['payment_methods'])) {
            unset($this->session->data['payment_method']);
        }
        
        if (!empty($this->session->data['payment_methods']) && !empty($this->session->data['payment_method'])) {
            if (!empty($this->session->data['payment_methods'][$this->session->data['payment_method']['code']])) {
                $this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->session->data['payment_method']['code']];
                $this->data['code'] = $this->session->data['payment_method']['code'];
            } else {
                unset($this->session->data['payment_method']);
            }
        } 
        
        $this->data['reload_cart'] = false;
        if (!empty($this->session->data['payment_methods']) && empty($this->session->data['payment_method']) && $simple_payment_view_autoselect_first) {
            $this->session->data['payment_method'] = reset($this->session->data['payment_methods']);
            $this->data['code'] = $this->session->data['payment_method']['code'];
            $this->data['reload_cart'] = true;
        }
        
        if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/simplecheckout_payment.tpl')) {
            $this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/simplecheckout_payment.tpl';
        } else {
            $this->template = 'default/template/checkout/simplecheckout_payment.tpl';
        }
                
        $this->response->setOutput($this->render());
    }
    
    public function select() {
        if (!empty($this->request->post['code'])) {
            if (!empty($this->session->data['payment_methods'][$this->request->post['code']])) {
                $this->session->data['payment_method'] = $this->session->data['payment_methods'][$this->request->post['code']];
                
                if (!empty($this->session->data['guest']['payment']['address_id'])) {
                    $this->session->data['payment_address_id'] = $this->session->data['guest']['payment']['address_id'];
                }
                if (!empty($this->session->data['guest']['payment']['country_id'])) {
                    $this->session->data['payment_country_id'] = $this->session->data['guest']['payment']['country_id'];
				}
                if (!empty($this->session->data['guest']['payment']['zone_id'])) {
                    $this->session->data['payment_zone_id'] = $this->session->data['guest']['payment']['zone_id'];
                }
                if (empty($this->session->data['guest']['payment']['country_id']) || empty($this->session->data['guest']['payment']['zone_id'])) {
                    unset($this->session->data['payment_country_id']);
                    unset($this->session->data['payment_zone_id']);
        		}
            }
        }
    }
}
?>