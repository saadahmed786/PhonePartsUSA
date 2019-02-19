<?php 
class ControllerOneCheckoutSuccess extends Controller { 
	public function index() { 
		if (isset($this->session->data['order_id'])) {
			$order_id = $this->session->data['order_id'];
			$this->cart->clear();
			
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['guest']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);	
			unset($this->session->data['coupon']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
		}	
									   
		$this->language->load('checkout/success');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->data['breadcrumbs'] = array(); 

      	$this->data['breadcrumbs'][] = array(
        	'href'      => $this->url->link('common/home'),
        	'text'      => $this->language->get('text_home'),
        	'separator' => false
      	); 
		
      	$this->data['breadcrumbs'][] = array(
        	'href'      => $this->url->link('checkout/cart'),
        	'text'      => $this->language->get('text_basket'),
        	'separator' => $this->language->get('text_separator')
      	);
				
		$this->data['breadcrumbs'][] = array(
			'href'      => $this->url->link('checkout/checkout', '', 'SSL'),
			'text'      => $this->language->get('text_checkout'),
			'separator' => $this->language->get('text_separator')
		);	
					
      	$this->data['breadcrumbs'][] = array(
        	'href'      => $this->url->link('checkout/success'),
        	'text'      => $this->language->get('text_success'),
        	'separator' => $this->language->get('text_separator')
      	);
		
    	$this->data['heading_title'] = $this->language->get('heading_title');
		//version
		$this->load->model('onecheckout/checkout');	
		$version_int = $this->model_onecheckout_checkout->versiontoint();		
		if($version_int < 1504 && $version_int >= 1500){
			$this->data['text_message'] = sprintf($this->language->get('text_message'), $this->url->link('account/account', '', 'SSL'), $this->url->link('account/order', '', 'SSL'), $this->url->link('account/download', '', 'SSL'), $this->url->link('information/contact'));
		} elseif($version_int >= 1504) {
			if ($this->customer->isLogged()) {
    			$this->data['text_message'] = sprintf($this->language->get('text_customer'), $this->url->link('account/account', '', 'SSL'), $this->url->link('account/order', '', 'SSL'), $this->url->link('account/download', '', 'SSL'), $this->url->link('information/contact'));
			} else {
    			$this->data['text_message'] = sprintf($this->language->get('text_guest'), $this->url->link('information/contact'));
			}		
		}
		
		//checkout survey
		$this->language->load('onecheckout/checkout');
		$this->data['onecheckout_survey_status'] = $this->config->get('onecheckout_survey_status');
		$this->data['survey_heading_title'] = $this->language->get('survey_heading_title');
		$this->data['text_survey'] = $this->language->get('text_survey');
		$this->data['order_id'] = isset($order_id) ? $order_id:'' ;
		$this->data['onecheckout_survey_option'] = isset($this->session->data['onecheckout_survey_option']) ? $this->session->data['onecheckout_survey_option']:'';
		if($this->config->get('onecheckout_survey_option')) {
			if(substr(VERSION,0,5) == '1.5.0'){
				$this->data['survey_options'] = unserialize($this->config->get('onecheckout_survey_option'));
			}else{
				$this->data['survey_options'] = $this->config->get('onecheckout_survey_option');
			}	
		} else {
			$this->data['survey_options'] = array();
		}
		
    	$this->data['button_continue'] = $this->language->get('button_continue');

    	$this->data['continue'] = $this->url->link('common/home');

		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/onecheckout/success.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/onecheckout/success.tpl';
		} else {
			$this->template = 'default/template/onecheckout/success.tpl';
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
?>