<?php 
class ControllerAccountLogout extends Controller {
	public function index() {
    	if ($this->customer->isLogged()) {
    		$redirect = $this->request->get['redirect'];
    		$redirect = base64_decode($redirect);
    		$redirect = str_replace("&amp;", "&", $redirect);
    		// echo $redirect;exit;
      		$this->customer->logout();
	  		$this->cart->clear();
			
			unset($this->session->data['wishlist']);
			unset($this->session->data['logged_in']);
			unset($this->session->data['shipping_address_id']);
			unset($this->session->data['shipping_country_id']);
			unset($this->session->data['shipping_zone_id']);
			unset($this->session->data['shipping_postcode']);
			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_address_id']);
			unset($this->session->data['payment_country_id']);
			unset($this->session->data['payment_zone_id']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
			unset($this->session->data['coupon']);

			unset($this->session->data['social_discount']);
			
			// OneCheckOut fix tax.php bug
			unset($this->session->data['guest']);
			unset($this->session->data['reward']);			
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);

                        unset($this->session->data['tmp_order_id']);
                        unset($this->session->data['unconfirmed_alert_sent']);
                        
			unset($this->session->data['newcheckout']);
			unset($this->session->data['logged_in']);
			if(!$redirect)
			{

      		$this->redirect($this->url->link('common/home', '', 'SSL'));
			}
			else
			{
				if($redirect=='account/account')
				{
					// $redirect = 'common/home';
				}
				// $this->redirect($this->url->link($redirect, '', 'SSL'));	
				$this->redirect(HTTPS_SERVER.'index.php?'.$redirect);
			}
    	}
    	$this->redirect($this->url->link('common/home', '', 'SSL'));
 
    	$this->language->load('account/logout');
		
		$this->document->setTitle($this->language->get('heading_title'));
      	
		$this->data['breadcrumbs'] = array();

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),        	
        	'separator' => false
      	);
      	
		$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_account'),
			'href'      => $this->url->link('account/account', '', 'SSL'),       	
        	'separator' => $this->language->get('text_separator')
      	);
		
      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_logout'),
			'href'      => $this->url->link('account/logout', '', 'SSL'),
        	'separator' => $this->language->get('text_separator')
      	);	
		
    	$this->data['heading_title'] = $this->language->get('heading_title');

    	$this->data['text_message'] = $this->language->get('text_message');

    	$this->data['button_continue'] = $this->language->get('button_continue');

    	$this->data['continue'] = $this->url->link('common/home');
		
		
            if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/common/logoutsuccess.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/common/logoutsuccess.tpl';
		} else {
			$this->template = 'megastore/template/common/logoutsuccess.tpl';
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