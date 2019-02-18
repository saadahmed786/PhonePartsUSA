<?php
class ControllerModuleSocialDiscount extends Controller {
	protected function index($setting) {
		$this->language->load('module/social_discount'); 

      	$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['tweet'] = $this->language->get('tweet');
		$this->data['twitter_message'] = $this->language->get('twitter_message');
		
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/social_discount.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/social_discount.tpl';
		} else {
			$this->template = 'default/template/module/social_discount.tpl';
		}

		$this->render();
	}
	
	public function addSocialDiscount() {
		$this->language->load('module/social_discount');
		if(isset($_POST['social_network'])) {
		if($_POST['social_network'] == 'facebook') {
		$social_discount_network = $this->language->get('facebook_discount');	
		}
		elseif($_POST['social_network'] == 'twitter') {
		$social_discount_network = $this->language->get('twitter_discount');	
		}
		}
		$this->language->load('module/cart');
		$json['total'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($this->cart->getTotal()));
		
		$json['success'] = $this->language->get('text_success_message');	
		$this->session->data['social_discount'] = $social_discount_network;				
		$this->response->setOutput(json_encode($json));		
	}
	
	public function removeSocialDiscount() {
		$this->language->load('module/social_discount');
		$this->language->load('module/cart');
		$json['total'] = sprintf($this->language->get('text_items'), $this->cart->countProducts() + (isset($this->session->data['vouchers']) ? count($this->session->data['vouchers']) : 0), $this->currency->format($this->cart->getTotal()));
		$json['remove'] = $this->language->get('text_remove_message');
		unset($this->session->data['social_discount']);	
		$this->response->setOutput(json_encode($json));		
	}
}
?>