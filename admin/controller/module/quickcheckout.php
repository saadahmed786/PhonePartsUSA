<?php
class ControllerModuleQuickcheckout extends Controller {
	private $error = array(); 
	 
	public function index() {   
		$this->load->language('module/quickcheckout');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			
			
			$this->session->data['success'] = $this->language->get('text_success');
			if(isset($this->request->post['quickcheckout']['checkout_settings_checkbox'])){
				
				$this->request->post['quickcheckout']['checkout_settings'] = str_replace("amp;", "", urldecode($this->request->post['quickcheckout']['checkout_settings']));
				parse_str($this->request->post['quickcheckout']['checkout_settings'], $this->request->post );	
			}
			
			if($this->request->post['quickcheckout']['checkout_enable'] == 1){
				if (file_exists(DIR_CATALOG.'../vqmod/xml/vqmod_checkout.xml_')) {
					rename(DIR_CATALOG.'../vqmod/xml/vqmod_checkout.xml_', DIR_CATALOG.'../vqmod/xml/vqmod_checkout.xml');
				 }
			}else{
				if (file_exists(DIR_CATALOG.'../vqmod/xml/vqmod_checkout.xml')) {
					rename(DIR_CATALOG.'../vqmod/xml/vqmod_checkout.xml', DIR_CATALOG.'../vqmod/xml/vqmod_checkout.xml_');
				 }	
			}
			if($this->request->post['quickcheckout']['checkout_compatibility'] == 1){
				if (file_exists(DIR_CATALOG.'../vqmod/xml/vqmod_checkout_compatibility.xml_')) {
					rename(DIR_CATALOG.'../vqmod/xml/vqmod_checkout_compatibility.xml_', DIR_CATALOG.'../vqmod/xml/vqmod_checkout_compatibility.xml');
				 }
			}else{
				if (file_exists(DIR_CATALOG.'../vqmod/xml/vqmod_checkout_compatibility.xml')) {
					rename(DIR_CATALOG.'../vqmod/xml/vqmod_checkout_compatibility.xml', DIR_CATALOG.'../vqmod/xml/vqmod_checkout_compatibility.xml_');
				 }	
			}
			

			$this->model_setting_setting->editSetting('quickcheckout', $this->request->post);		
		
			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}
		$this->document->addScript('view/javascript/dreamvention/tinysort/jquery.tinysort.min.js');	
		$this->document->addScript('view/javascript/dreamvention/gridster/jquery.gridster.min.js');
		$this->document->addScript('view/javascript/dreamvention/jquery/jquery.autosize.min.js');	
		
		
		$this->data['heading_title'] = $this->language->get('heading_title');

		// Words
		$this->data['version'] = 'v3.5';
		$this->data['settings_yes'] = $this->language->get('settings_yes');
		$this->data['settings_no'] = $this->language->get('settings_no');
		$this->data['settings_display'] = $this->language->get('settings_display');
		$this->data['settings_require'] = $this->language->get('settings_require');	
		$this->data['settings_enable'] = $this->language->get('settings_enable');
		$this->data['settings_select'] = $this->language->get('settings_select');
		$this->data['settings_image'] = $this->language->get('settings_image');
		$this->data['settings_second_step'] = $this->language->get('settings_second_step');
		
	
		// Checkout
		$this->data['checkout_heading'] = $this->language->get('checkout_heading');
		$this->data['checkout_intro'] = $this->language->get('checkout_intro');
		$this->data['checkout_intro_display'] = $this->language->get('checkout_intro_display');
		$this->data['checkout_quickcheckout'] = $this->language->get('checkout_quickcheckout');
		$this->data['checkout_debug'] = $this->language->get('checkout_debug');
		$this->data['checkout_compatibility'] = $this->language->get('checkout_compatibility');
		$this->data['checkout_disable'] = $this->language->get('checkout_disable');
		$this->data['checkout_defalt_option'] = $this->language->get('checkout_defalt_option');
		$this->data['checkout_defalt_option_register'] = $this->language->get('checkout_defalt_option_register');
		$this->data['checkout_defalt_option_guest'] = $this->language->get('checkout_defalt_option_guest');
		$this->data['checkout_display_options'] = $this->language->get('checkout_display_options');
		$this->data['checkout_display_login_text'] = $this->language->get('checkout_display_login_text');
		$this->data['checkout_min_order'] = $this->language->get('checkout_min_order');
		$this->data['checkout_min_order_tag'] = $this->language->get('checkout_min_order_tag');
		$this->data['checkout_display_only_register_options'] = $this->language->get('checkout_display_only_register_options');
		$this->data['checkout_display_only_register_text'] = $this->language->get('checkout_display_only_register_text');
		

		$this->data['checkout_guest_step_1'] = $this->language->get('checkout_guest_step_1');
		$this->data['checkout_register_step_1'] = $this->language->get('checkout_register_step_1');
		$this->data['checkout_firstname'] = $this->language->get('checkout_firstname');
		$this->data['checkout_lastname'] = $this->language->get('checkout_lastname');
		$this->data['checkout_email'] = $this->language->get('checkout_email');
		$this->data['checkout_telephone'] = $this->language->get('checkout_telephone');	
		$this->data['checkout_fax'] = $this->language->get('checkout_fax');
		$this->data['checkout_password'] = $this->language->get('checkout_password');	
		
		$this->data['checkout_guest_step_2'] = $this->language->get('checkout_guest_step_2');
		$this->data['checkout_register_step_2'] = $this->language->get('checkout_register_step_2');
		$this->data['checkout_payment_address'] = $this->language->get('checkout_payment_address');	
		$this->data['checkout_company'] = $this->language->get('checkout_company');
		$this->data['checkout_customer_group'] = sprintf($this->language->get('checkout_customer_group'),$this->url->link('sale/customer_group', 'token=' . $this->session->data['token'], 'SSL') );
		$this->data['checkout_company_id'] = $this->language->get('checkout_company_id');
		$this->data['checkout_tax_id'] = $this->language->get('checkout_tax_id');
		$this->data['checkout_address_1'] = $this->language->get('checkout_address_1');
		$this->data['checkout_address_2'] = $this->language->get('checkout_address_2');		
		$this->data['checkout_city'] = $this->language->get('checkout_city');	
		$this->data['checkout_postcode'] = sprintf($this->language->get('checkout_postcode'), $this->url->link('localisation/country', 'token=' . $this->session->data['token'], 'SSL') );			
		$this->data['checkout_country'] = $this->language->get('checkout_country');	
		$this->data['checkout_zone'] = $this->language->get('checkout_zone');
		$this->data['checkout_newsletter'] = $this->language->get('checkout_newsletter');
		$this->data['checkout_privacy_agree'] = $this->language->get('checkout_privacy_agree');	
		

		$this->data['checkout_guest_step_3'] = $this->language->get('checkout_guest_step_3');
		$this->data['checkout_register_step_3'] = $this->language->get('checkout_register_step_3');
		$this->data['checkout_shipping_address'] = $this->language->get('checkout_shipping_address');
		$this->data['checkout_shipping_address_enable'] = $this->language->get('checkout_shipping_address_enable');		
		$this->data['checkout_shipping_agree'] = $this->language->get('checkout_shipping_agree');
				

		$this->data['checkout_step_4'] = $this->language->get('checkout_step_4');	
		$this->data['checkout_shipping_method'] = $this->language->get('checkout_shippint_method');
		$this->data['checkout_shipping_method_methods'] = $this->language->get('checkout_shipping_method_methods');
		$this->data['checkout_shipping_method_title'] = $this->language->get('checkout_shipping_method_title');
		$this->data['checkout_shipping_method_date'] = $this->language->get('checkout_shipping_method_date');
		$this->data['shipping_method_date_picker'] = $this->language->get('shipping_method_date_picker');
		$this->data['checkout_shipping_method_comment'] = $this->language->get('checkout_shipping_method_comment');
		
		$this->data['checkout_step_5'] = $this->language->get('checkout_step_5');	
		$this->data['checkout_payment_method'] = $this->language->get('checkout_payment_method');
		$this->data['checkout_payment_method_methods'] = $this->language->get('checkout_payment_method_methods');
		$this->data['checkout_payment_method_comment'] = $this->language->get('checkout_payment_method_comment');
		$this->data['checkout_payment_method_agree'] = $this->language->get('checkout_payment_method_agree');
		$this->data['checkout_payment_method_methods_steps'] = $this->language->get('checkout_payment_method_methods_steps');
		
		
		
		$this->data['checkout_step_6'] = $this->language->get('checkout_step_6');	
		$this->data['checkout_confirm_images'] = $this->language->get('checkout_confirm_images');	
		$this->data['checkout_confirm_name'] = $this->language->get('checkout_confirm_name');
		$this->data['checkout_confirm_model'] = $this->language->get('checkout_confirm_model');
		$this->data['checkout_confirm_quantity'] = $this->language->get('checkout_confirm_quantity');
		$this->data['checkout_confirm_price'] = $this->language->get('checkout_confirm_price');
		$this->data['checkout_confirm_total'] = $this->language->get('checkout_confirm_total');
		$this->data['confirm_coupon_display'] = $this->language->get('confirm_coupon_display');
		$this->data['confirm_voucher_display'] = $this->language->get('confirm_voucher_display');
		$this->data['confirm_2_step_cart_display'] = $this->language->get('confirm_2_step_cart_display');

		$this->data['checkout_design'] = $this->language->get('checkout_design');
		$this->data['checkout_design_cutomer_info'] = $this->language->get('checkout_design_cutomer_info');
		$this->data['checkout_design_shipping_address'] = $this->language->get('checkout_design_shipping_address');
		$this->data['checkout_design_shipping_method'] = $this->language->get('checkout_design_shipping_method');
		$this->data['checkout_design_payment_method'] = $this->language->get('checkout_design_payment_method');
		$this->data['checkout_design_confirm'] = $this->language->get('checkout_design_confirm');
		$this->data['checkout_design_extra1'] = $this->language->get('checkout_design_extra1');
		$this->data['checkout_design_extra2'] = $this->language->get('checkout_design_extra2');
		$this->data['checkout_design_extra3'] = $this->language->get('checkout_design_extra3');

		$this->data['checkout_labels_float'] = $this->language->get('checkout_labels_float');
		$this->data['checkout_labels_float_left'] = $this->language->get('checkout_labels_float_left');
		$this->data['checkout_labels_float_clear'] = $this->language->get('checkout_labels_float_clear');
		$this->data['checkout_force_default_style'] = $this->language->get('checkout_force_default_style');		
		$this->data['checkout_style'] = $this->language->get('checkout_style');	
		$this->data['checkout_style_css'] = $this->language->get('checkout_style_css');	
		
		$this->data['checkout_settings'] = $this->language->get('checkout_settings');
		$this->data['checkout_settings_checkbox'] = $this->language->get('checkout_settings_checkbox');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_add_module'] = $this->language->get('button_add_module');
		$this->data['button_remove'] = $this->language->get('button_remove');
		
		$this->data['tab_module'] = $this->language->get('tab_module');
		
		if (!file_exists(DIR_CATALOG.'../vqmod/xml/vqmod_extra_positions.xml')) {
            $this->data['positions_needed'] = $this->language->get('positions_needed');
         }

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/quickcheckout', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/quickcheckout', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['token'] = $this->session->data['token'];

		$this->data['modules'] = array();
		
		if (isset($this->request->post['quickcheckout'])) {
			$this->data['quickcheckout'] = $this->request->post['quickcheckout'];
		} elseif ($this->config->get('quickcheckout')) { 
			$this->data['quickcheckout'] = $this->config->get('quickcheckout');
		}	
		
		//Get Payment methods
		$this->load->model('setting/extension');
		$payment_methods = glob(DIR_APPLICATION . 'controller/payment/*.php');
		$this->data['payment_methods'] = array();
		foreach ($payment_methods as $payment){
			$payment = basename($payment, '.php');
			$this->load->language('payment/' . $payment);
			$this->data['payment_methods'][] = array(
				'code' => $payment,
				'title' => $this->language->get('heading_title')
			);
		}
				
		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		$this->template = 'module/quickcheckout.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/quickcheckout')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
	public function install() {
		$this->load->model('setting/setting');
/*      if (file_exists(DIR_CATALOG.'../vqmod/xml/vqmod_dreamtheme.xml')) {
            rename(DIR_CATALOG.'../vqmod/xml/vqmod_dreamtheme.xml', DIR_CATALOG.'../vqmod/xml/vqmod_dreamtheme.xml_');
         }*/
		 $config = $this->config->get('dreamtheme');
		 if($config){
			 if(isset($config['checkout'])){
				 $config = $config['checkout'];
			 }
		 }
		 if(!is_array($config)){ $config= array();}
		 
		 $default_config = array(
    'checkout_enable' => 1,
    'quickcheckout_display' => 1,
    'checkout_debug' => 0,
    'checkout_compatibility' => 0,
	'checkout_display_login' => 1,
	'checkout_display_register' => 1,
	'checkout_display_guest' => 1,
	'checkout_min_order' => 0,
    'sort_firstname_input' => 0,
    'guest_firstname_display' => 1,
    'guest_firstname_require' => 0,
    'register_firstname_display' => 1,
    'register_firstname_require' => 0,
    'sort_lastname_input' => 1,
    'guest_lastname_display' => 0,
    'guest_lastname_require' => 0,
    'register_lastname_display' => 0,
    'register_lastname_require' => 0,
    'register_email' => '',
    'sort_email_input' => 2,
    'guest_email_display' => 1,
    'guest_email_require' => 0,
    'register_email_display' => 1,
    'register_email_require' => 1,
	'sort_telephone_input' => 3,
    'guest_telephone_display' => 1,
    'guest_telephone_require' => 0,
    'register_telephone_display' => 1,
    'register_telephone_require' => 0,
    'sort_fax_input' => 4,
    'guest_fax_display' => 0,
    'register_fax_display' => 0,
    'sort_password_group_input' => 5,
    'register_password_display' => 1,
    'register_password_require' => 1,	
    'guest_payment_address_display' => 1,
    'register_payment_address_display' => 1,
    'sort_company_input' => 1,
    'guest_company_display' => 1,
    'register_company_display' => 1,
    'sort_company_id_input' => 2,
    'guest_company_id_display' => 0,
    'guest_company_id_require' => 0,
    'register_company_id_display' => 0,
    'register_company_id_require' => 0,
    'sort_customer_group_input' => 3,
    'guest_customer_group_display' => 1,
    'register_customer_group_display' => 1,
    'sort_tax_id_input' => 4,
    'guest_tax_id_display' => 0,
    'guest_tax_id_require' => 0,
    'register_tax_id_display' => 0,
    'register_tax_id_require' => 0,
    'sort_address_2_input' => 6,
    'guest_address_2_display' => 0,
    'register_address_2_display' => 0,
    'sort_address_1_input' => 5,
    'guest_address_1_display' => 1,
    'guest_address_1_require' => 0,
    'register_address_1_display' => 1,
    'register_address_1_require' => 0,
    'sort_city_input' => 7,
    'guest_city_display' => 0,
    'guest_city_require' => 0,
    'register_city_display' => 0,
    'register_city_require' => 0,
    'sort_postcode_input' => 8,
    'guest_postcode_display' => 1,
    'guest_postcode_require' => 0,
    'register_postcode_display' => 1,
    'register_postcode_require' => 0,
    'sort_country_input' => 9,
    'guest_country_display' => 1,
    'guest_country_require' => 1,
    'register_country_display' => 1,
    'register_country_require' => 1,
    'sort_zone_input' => 10,
    'guest_zone_display' => 1,
    'guest_zone_require' => 1,
    'register_zone_display' => 1,
    'register_zone_require' => 1,
    'register_newsletter_display' => 0,
		
		'sort_shipping_address_1_input' => 4,
		'sort_shipping_company_input' => 3,
		'sort_shipping_firstname_input' => 1,
		'sort_shipping_address_2_input' => 5,
		'sort_shipping_city_input' => 6,
		'sort_shipping_lastname_input' => 2,
		'sort_shipping_postcode_input' => 7,
		'sort_shipping_country_input' => 8,
		'sort_shipping_zone_input' => 9,
		
		'guest_shipping_address_display' => 0,
		'guest_shipping_address_enable' => 1,
		'guest_shipping_address_1_display' => 1,
		'guest_shipping_address_1_require' => 0,
		'guest_shipping_company_display' => 0,
		'guest_shipping_firstname_display' => 1,
		'guest_shipping_firstname_require' => 0,
		'guest_shipping_address_2_display' => 0,
		'guest_shipping_city_display' => 1,
		'guest_shipping_city_require' => 0,
		'guest_shipping_lastname_display' => 0,
		'guest_shipping_lastname_require' => 0,
		'guest_shipping_postcode_display' => 1,
		'guest_shipping_postcode_require' => 1,
		'guest_shipping_country_display' => 1,
		'guest_shipping_country_require' => 1,
		'guest_shipping_zone_display' => 1,
		'guest_shipping_zone_require' => 1,
		
		'register_shipping_address_display' => 0,
		'register_shipping_address_enable' => 1,
		'register_shipping_address_1_display' => 1,
		'register_shipping_address_1_require' => 1,
		'register_shipping_company_display' => 0,
		'register_shipping_firstname_display' => 1,
		'register_shipping_firstname_require' => 1,
		'register_shipping_address_2_display' => 0,
		'register_shipping_city_display' => 0,
		'register_shipping_city_require' => 0,
		'register_shipping_lastname_display' => 0,
		'register_shipping_lastname_require' => 0,
		'register_shipping_postcode_display' => 1,
		'register_shipping_postcode_require' => 1,
		'register_shipping_country_display' => 1,
		'register_shipping_country_require' => 1,
		'register_shipping_zone_display' => 1,
		'register_shipping_zone_require' => 1,
    	'register_privacy_agree_display' => 1,
		
    'shipping_method_display' => 1,
    'shipping_method_methods_display' => 1,
    'shipping_method_methods_select' => 1,
    'shipping_method_date_display' => 0,
	'shipping_method_date_picker' => 1, 
    'shipping_method_title_display' => 0,
    'shipping_method_comment_display' => 0,
    'payment_method_display' => 1,
    'payment_method_methods_display' => 1,
    'payment_method_methods_select' => 0,
    'payment_method_comment_display' => 1,
    'payment_method_agree_display' => 1,
	'payment_method_methods_image' => 1,
		
		'payment_method_second_step' => array(
			'authorizenet_aim' => 0,
			'bank_transfer' => 0,
			'cheque' => 0,
			'cod' => 0,
			'free_checkout' => 0,
			'klarna_account' => 0,
			'klarna_invoice' => 0,
			'liqpay' => 1,
			'moneybookers' => 1,
			'nochex' => 1,
			'paymate' => 1,
			'paypoint' => 1,
			'payza' => 1,
			'perpetual_payments' => 0,
			'pp_pro' => 0,
			'pp_pro_uk' => 1,
			'pp_standard' => 1,
			'sagepay' => 1,
			'sagepay_direct' => 0,
			'sagepay_us' => 0,
			'twocheckout' => 1,
			'web_payment_software' => 0,
			'worldpay' => 1
		), 
		
    'confirm_images_display' => 1,
    'confirm_name_display' => 1,
    'confirm_model_display' => 0,
    'confirm_quantity_display' => 1,
    'confirm_price_display' => 0,
    'confirm_total_display' => 1,
	'confirm_coupon_display' => 1,
	'confirm_voucher_display' => 1,
	'confirm_2_step_cart_display' => 1,
    'checkout_labels_float' => 0,
    'column_width' => array
        (
            'column-1' => 50,
            'column-2' => 50,
            'column-3' => 0
        ),
    'portlets' => array
		(
            '0' => array
                (
                    'col' => 1,
                    'row' => 0,
                ),
            '1' => array
                (
                    'col' => 1,
                    'row' => 1,
                ),
            '2' => array
                (
                    'col' => 1,
                    'row' => 2,
                ),
            '3' => array
                (
                    'col' => 2,
                    'row' => 0,
                ),
            '4' => array
                (
                    'col' => 2,
                    'row' => 1,
                ),
            '6' => array
                (
                    'col' => 3,
                    'row' => 0,
                ),
            '5' => array
                (
                    'col' => 3,
                    'row' => 1,
                ),
			'7' => array
                (
                    'col' => 3,
                    'row' => 2,
                )
        ),
    'checkout_style' => '',
	'checkout_force_default_style' => ''
);
	  $config = array('quickcheckout' => array_merge($default_config, $config));
	  $this->model_setting_setting->editSetting('quickcheckout', $config);		
	  
        // $this->redirect($this->url->link('module/quickcheckout', 'token=' . $this->session->data['token'], 'SSL'));
   }
}
?>