<?php
/*
@author	Dmitriy Kubarev
@link	http://www.simpleopencart.com
@link	http://www.opencart.com/index.php?route=extension/extension/info&extension_id=4811
*/  

class ControllerCheckoutSimpleCheckout extends Controller { 
    public function index() {
    
        $this->language->load('checkout/simplecheckout');
        
        $this->document->setTitle($this->language->get('heading_title')); 
            
        $this->data['breadcrumbs'] = array();

          $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_home'),
            'href'      => $this->url->link('common/home'),
            'separator' => false
          ); 
        
          $this->data['breadcrumbs'][] = array(
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('checkout/simplecheckout', '', 'SSL'),
            'separator' => $this->language->get('text_separator')
          );
        
        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['text_proceed_payment'] = $this->language->get('text_proceed_payment');
        
        $this->data['block_order'] = false;
            
        if ($this->config->get('config_customer_price') && !$this->customer->isLogged()) {
            $this->data['block_order'] = true;
        } 
        
        if (!$this->cart->hasStock() && !$this->config->get('config_stock_checkout')) {
              $this->data['block_order'] = true;
        }
            
        if ($this->cart->hasProducts() || !empty($this->session->data['vouchers'])) {
            $products = $this->cart->getProducts();
                    
            foreach ($products as $product) {
                $product_total = 0;
                    
                foreach ($products as $product_2) {
                    if ($product_2['product_id'] == $product['product_id']) {
                        $product_total += $product_2['quantity'];
                    }
                }        
                
                if ($product['minimum'] > $product_total) {
                    $this->data['block_order'] = true;
                }        
            }
                    
            $this->data['button_order'] = $this->language->get('button_order');
            $this->data['button_back'] = $this->language->get('button_back');
              
            $this->data['simple_common_view_agreement_checkbox'] = false;
            $this->data['simple_common_view_agreement_text'] = false;
            $this->data['simple_common_view_agreement_checkbox_init'] = 0;
            
            if ($this->config->get('simple_common_view_agreement_id')) {
                $this->load->model('catalog/information');
                
                $information_info = $this->model_catalog_information->getInformation($this->config->get('simple_common_view_agreement_id'));
                
                if ($information_info) {
                    $this->data['simple_common_view_agreement_checkbox'] = $this->config->get('simple_common_view_agreement_checkbox');
                    $this->data['simple_common_view_agreement_text'] = $this->config->get('simple_common_view_agreement_text');
                    $this->data['simple_common_view_agreement_checkbox_init'] = $this->config->get('simple_common_view_agreement_checkbox_init');
            
                    $this->data['information_title'] = $information_info['title'];
                    $this->data['information_text'] = html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8');
                    
                    $current_theme = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template'));
                    
                    $id = ($current_theme == 'shoppica' || $current_theme == 'shoppica2') ? 'text_agree_shoppica' : 'text_agree';
                    $this->data['text_agree'] = sprintf($this->language->get($id), $this->url->link('information/information/info', 'information_id=' . $this->config->get('simple_common_view_agreement_id'), 'SSL'), $information_info['title'], $information_info['title']);
                }
            }
            
            $this->data['has_shipping'] = true;
            if (!$this->cart->hasShipping()) {
                $this->data['has_shipping'] = false;
            }
            
            $simple_common_template = $this->config->get('simple_common_template');
            $this->data['simple_common_template'] = $simple_common_template != '' ? $simple_common_template : '{left_column}{cart}{customer}{/left_column}{right_column}{shipping}{payment}{agreement}{/right_column}';
            
            if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/simplecheckout.tpl')) {
                $this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/checkout/simplecheckout.tpl';
                $this->data['template'] = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template'));
            } else {
                $this->template = 'default/template/checkout/simplecheckout.tpl';
                $this->data['template'] = 'default';
            }
            
            $this->children = array(
                'common/column_left',
                'common/column_right',
                'common/content_top',
                'common/content_bottom',
                'common/footer',
                'common/header',
                'checkout/simplecheckout_customer',
                'checkout/simplecheckout_shipping',
                'checkout/simplecheckout_payment',
                'checkout/simplecheckout_cart'        
            );
                   
            $this->response->setOutput($this->render());
            
        } else {
              $this->data['heading_title'] = $this->language->get('heading_title');

              $this->data['text_error'] = $this->language->get('text_empty');

              $this->data['button_continue'] = $this->language->get('button_continue');

              $this->data['continue'] = $this->url->link('common/home');

            if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/error/not_found.tpl')) {
                $this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/error/not_found.tpl';
            } else {
                $this->template = 'default/template/error/not_found.tpl';
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
    }
}
?>