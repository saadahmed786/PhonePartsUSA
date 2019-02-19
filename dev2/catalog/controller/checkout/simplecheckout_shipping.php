<?php 
/*
@author	Dmitriy Kubarev
@link	http://www.simpleopencart.com
@link	http://www.opencart.com/index.php?route=extension/extension/info&extension_id=4811
*/  

class ControllerCheckoutSimpleCheckoutShipping extends Controller {
    public function index() {

        $this->language->load('checkout/simplecheckout');

        $this->data['address_empty'] = false;
        
        if (!$this->cart->hasShipping()) {
            return;
        }
        
        $address = array(
            'firstname' => isset($this->session->data['guest']['shipping']['firstname']) ? $this->session->data['guest']['shipping']['firstname'] : '',
            'lastname' => isset($this->session->data['guest']['shipping']['lastname']) ? $this->session->data['guest']['shipping']['lastname'] : '',
            'company' => isset($this->session->data['guest']['shipping']['company']) ? $this->session->data['guest']['shipping']['company'] : '',
            'company_id' => isset($this->session->data['guest']['shipping']['company_id']) ? $this->session->data['guest']['shipping']['company_id'] : '',
            'tax_id' => isset($this->session->data['guest']['shipping']['tax_id']) ? $this->session->data['guest']['shipping']['tax_id'] : '',
            'address_1' => isset($this->session->data['guest']['shipping']['address_1']) ? $this->session->data['guest']['shipping']['address_1'] : '',
            'address_2' => isset($this->session->data['guest']['shipping']['address_2']) ? $this->session->data['guest']['shipping']['address_2'] : '',
            'postcode' => isset($this->session->data['guest']['shipping']['postcode']) ? $this->session->data['guest']['shipping']['postcode'] : '',
            'city' => isset($this->session->data['guest']['shipping']['city']) ? $this->session->data['guest']['shipping']['city'] : '',
            'zone_id' => isset($this->session->data['guest']['shipping']['zone_id']) ? $this->session->data['guest']['shipping']['zone_id'] : '',
            'zone' => isset($this->session->data['guest']['shipping']['zone']) ? $this->session->data['guest']['shipping']['zone'] : '',
            'zone_code' => isset($this->session->data['guest']['shipping']['zone_code']) ? $this->session->data['guest']['shipping']['zone_code'] : '',
            'country_id' => isset($this->session->data['guest']['shipping']['country_id']) ? $this->session->data['guest']['shipping']['country_id'] : '',
            'country' => isset($this->session->data['guest']['shipping']['country']) ? $this->session->data['guest']['shipping']['country'] : '', 
            'iso_code_2' => isset($this->session->data['guest']['shipping']['iso_code_2']) ? $this->session->data['guest']['shipping']['iso_code_2'] : '',
            'iso_code_3' => isset($this->session->data['guest']['shipping']['iso_code_3']) ? $this->session->data['guest']['shipping']['iso_code_3'] : '',
            'address_format' => isset($this->session->data['guest']['shipping']['address_format']) ? $this->session->data['guest']['shipping']['address_format'] : '',
            'address_id' => isset($this->session->data['guest']['shipping']['address_id']) ? $this->session->data['guest']['shipping']['address_id'] : ''
        );
       
        if ($address['country_id'] == '' && !empty($this->session->data['guest']['display_customer_fields']['main_country_id'])) {
            $this->data['address_empty'] = true;
        }
        
        if ($address['zone_id'] == '' && !empty($this->session->data['guest']['display_customer_fields']['main_zone_id'])) {
            $this->data['address_empty'] = true;
        }
        
        if ($address['city'] == '' && !empty($this->session->data['guest']['display_customer_fields']['main_city'])) {
            $this->data['address_empty'] = true;
        }
        
        if ($address['postcode'] == '' && !empty($this->session->data['guest']['display_customer_fields']['main_postcode'])) {
            $this->data['address_empty'] = true;
        }
        
        $this->data['text_checkout_shipping_method'] = $this->language->get('text_checkout_shipping_method');
        
        $this->data['simple_shipping_view_title'] = $this->config->get('simple_shipping_view_title');
        $this->data['simple_shipping_view_address_empty'] = $this->config->get('simple_shipping_view_address_empty');
        $simple_shipping_view_address_full = $this->config->get('simple_shipping_view_address_full');
        $simple_shipping_view_autoselect_first  = $this->config->get('simple_shipping_view_autoselect_first');
        $this->data['shipping_methods'] = array();
    
        $quote_data = array();
        
        $this->load->model('setting/extension');
        
        $results = $this->model_setting_extension->getExtensions('shipping');
        
        foreach ($results as $result) {
            $show_module = true;
            if (!$this->data['address_empty']) {
                $show_module = true;
            } elseif ($this->data['address_empty'] && !empty($simple_shipping_view_address_full[$result['code']])) {
                $show_module = false;
            } 
            
            if ($this->config->get($result['code'] . '_status') && $show_module) {
                $this->load->model('shipping/' . $result['code']);
                
                $quote = $this->{'model_shipping_' . $result['code']}->getQuote($address); 
    
                if ($quote && !empty($quote['quote'])) {
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
        
        $this->data['shipping_methods'] = $this->session->data['shipping_methods'] = $quote_data;

        $this->data['text_shipping_address'] = $this->language->get('text_shipping_address');
                   
        $this->data['code'] = '';
                                 
        if (empty($this->session->data['shipping_methods'])) {
            unset($this->session->data['shipping_method']);
        }
        
        if (!empty($this->session->data['shipping_methods']) && !empty($this->session->data['shipping_method'])) {
            $shipping = explode('.', $this->session->data['shipping_method']['code']);
            if (!empty($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]]) && $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]]['code'] == $this->session->data['shipping_method']['code']) {
                $this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
                $this->data['code'] = $this->session->data['shipping_method']['code'];
            } else {
                unset($this->session->data['shipping_method']);
            }
        }
        
        $this->data['reload_customer_only'] = false;
        if (!empty($this->session->data['shipping_methods']) && empty($this->session->data['shipping_method']) && $simple_shipping_view_autoselect_first) {
            $first = reset($this->session->data['shipping_methods']);
            if (!empty($first['quote'])) {
                $this->session->data['shipping_method'] = reset($first['quote']);
                $this->data['code'] = $this->session->data['shipping_method']['code'];
                $this->data['reload_customer_only'] = true;
            }
        }
                        
        if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/simplecheckout_shipping.tpl')) {
            $this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/simplecheckout_shipping.tpl';
        } else {
            $this->template = 'default/template/checkout/simplecheckout_shipping.tpl';
        }

        $this->response->setOutput($this->render());        
    }
    
    public function select() {
        if (!empty($this->request->post['code'])) {
            $shipping = explode('.', $this->request->post['code']);
            if (!empty($this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]])) {
                $this->session->data['shipping_method'] = $this->session->data['shipping_methods'][$shipping[0]]['quote'][$shipping[1]];
                
                if (!empty($this->session->data['guest']['shipping']['address_id'])) {
                    $this->session->data['shipping_address_id'] = $this->session->data['guest']['shipping']['address_id'];
                }
                if (!empty($this->session->data['guest']['shipping']['country_id'])) {
                    $this->session->data['shipping_country_id'] = $this->session->data['guest']['shipping']['country_id'];
				}
                if (!empty($this->session->data['guest']['shipping']['zone_id'])) {
                    $this->session->data['shipping_zone_id'] = $this->session->data['guest']['shipping']['zone_id'];
                }
                if (empty($this->session->data['guest']['shipping']['country_id']) || empty($this->session->data['guest']['shipping']['zone_id'])) {
                    unset($this->session->data['shipping_country_id']);
                    unset($this->session->data['shipping_zone_id']);
        		}
                if (!empty($this->session->data['guest']['shipping']['postcode'])) {
                    $this->session->data['shipping_postcode'] = $this->session->data['guest']['shipping']['postcode'];
                }
            }
        }
    }
}
?>