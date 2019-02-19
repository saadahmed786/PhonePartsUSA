<?php

define ('POS_VERSION', '4.6.2.B1404302');

class ControllerModulePos extends Controller {
	private $error = array(); 
	
	public function index() {   
		$this->language->load('module/pos');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		$this->load->model('pos/pos');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('POS', $this->request->post);	
					
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}
				
		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['tab_settings_payment_type'] = $this->language->get('tab_settings_payment_type');
		$this->data['tab_settings_options'] = $this->language->get('tab_settings_options');
		$this->data['tab_settings_order'] = $this->language->get('tab_settings_order');
		$this->data['tab_settings_authorizenet'] = $this->language->get('tab_settings_authorizenet');
		$this->data['tab_settings_receipt'] = $this->language->get('tab_settings_receipt');
		$this->data['tab_settings_customer'] = $this->language->get('tab_settings_customer');
		$this->data['tab_settings_discount'] = $this->language->get('tab_settings_discount');
		$this->data['tab_settings_affiliate'] = $this->language->get('tab_settings_affiliate');
		$this->data['tab_settings_quote'] = $this->language->get('tab_settings_quote');
		$this->data['tab_settings_location'] = $this->language->get('tab_settings_location');
		$this->data['tab_settings_table_management'] = $this->language->get('tab_settings_table_management');
		$this->data['tab_settings_product_sn'] = $this->language->get('tab_settings_product_sn');
		$this->data['tab_settings_commission'] = $this->language->get('tab_settings_commission');
		
		$this->data['text_order_payment_type'] = $this->language->get('text_order_payment_type');
		$this->data['text_action'] = $this->language->get('text_action');
		$this->data['text_type_already_exist'] = $this->language->get('text_type_already_exist');
		$this->data['text_payment_type_setting'] = $this->language->get('text_payment_type_setting');
		// add for Openbay integration begin
		$this->data['text_openbay_setting'] = $this->language->get('text_openbay_setting');
		$this->data['text_openbay_enable'] = $this->language->get('text_openbay_enable');
		// add for Openbay integration end
		$this->data['text_display_setting'] = $this->language->get('text_display_setting');
		$this->data['text_display_once_login'] = $this->language->get('text_display_once_login');
		$this->data['column_exclude'] = $this->language->get('column_exclude');
		$this->data['text_select_all'] = $this->language->get('text_select_all');
		$this->data['text_unselect_all'] = $this->language->get('text_unselect_all');
		$this->data['button_delete'] = $this->language->get('button_delete');
		
		// add for Print begin
		$this->data['text_print_setting'] = $this->language->get('text_print_setting');
		$this->data['entry_print_log'] = $this->language->get('entry_print_log');
		$this->data['entry_print_width'] = $this->language->get('entry_print_width');
		$this->data['text_print_browse'] = $this->language->get('text_print_browse');
		$this->data['text_print_image_manager'] = $this->language->get('text_print_image_manager');
		$this->data['text_p_complete'] = $this->language->get('text_p_complete');
		$this->data['text_p_payment'] = $this->language->get('text_p_payment');
		$this->data['entry_term_n_cond'] = $this->language->get('entry_term_n_cond');
		// add for Print end
		// add for Inplace Pricing begin
		$this->data['text_inplace_pricing_setting'] = $this->language->get('text_inplace_pricing_setting');
		$this->data['text_inplace_pricing_enable'] = $this->language->get('text_inplace_pricing_enable');
		// add for Inplace Pricing end
		// add for Hiding Delete begin
		$this->data['text_hide_delete_setting'] = $this->language->get('text_hide_delete_setting');
		$this->data['text_hide_delete_enable'] = $this->language->get('text_hide_delete_enable');
		// add for Hiding Delete end
		// add for Hiding Order Status begin
		$this->data['text_hide_order_status_setting'] = $this->language->get('text_hide_order_status_setting');
		$this->data['text_hide_order_status_message'] = $this->language->get('text_hide_order_status_message');
		// add for Hiding Order Status end
		// add for User as Affiliate begin
		$this->data['text_user_affi_setting'] = $this->language->get('text_user_affi_setting');
		$this->data['column_ua_user'] = $this->language->get('column_ua_user');
		$this->data['column_ua_affiliate'] = $this->language->get('column_ua_affiliate');
		$this->data['column_ua_action'] = $this->language->get('column_ua_action');
		// add for User as Affiliate end
		// add for Default Customer begin
		$this->data['text_customer_setting'] = $this->language->get('text_customer_setting');
		$this->data['text_customer_system'] = $this->language->get('text_customer_system');
		$this->data['text_customer_custom'] = $this->language->get('text_customer_custom');
		$this->data['text_customer_existing'] = $this->language->get('text_customer_existing');
		$this->data['text_customer_info'] = $this->language->get('text_customer_info');
		$this->data['text_address_info'] = $this->language->get('text_address_info');
		$this->data['text_customer'] = $this->language->get('text_customer');
		$this->language->load('sale/order');
    	$this->data['entry_firstname'] = $this->language->get('entry_firstname');
    	$this->data['entry_lastname'] = $this->language->get('entry_lastname');
    	$this->data['entry_email'] = $this->language->get('entry_email');
    	$this->data['entry_telephone'] = $this->language->get('entry_telephone');
    	$this->data['entry_fax'] = $this->language->get('entry_fax');
		$this->data['entry_address_1'] = $this->language->get('entry_address_1');
		$this->data['entry_address_2'] = $this->language->get('entry_address_2');
		$this->data['entry_city'] = $this->language->get('entry_city');
		$this->data['entry_postcode'] = $this->language->get('entry_postcode');
		$this->data['entry_zone'] = $this->language->get('entry_zone');
		$this->data['entry_country'] = $this->language->get('entry_country');
		$this->data['text_select'] = $this->language->get('text_select');
		$this->data['text_none'] = $this->language->get('text_none');
		$this->data['text_autocomplete'] = $this->language->get('text_autocomplete');
		$this->data['text_customer_group'] = $this->language->get('text_customer_group');
		// add for Default Customer end
		// add for Maximum Discount begin
		$this->data['text_max_discount_setting'] = $this->language->get('text_max_discount_setting');
		$this->data['column_group'] = $this->language->get('column_group');
		$this->data['column_discount_limit'] = $this->language->get('column_discount_limit');
		$this->data['entry_max_discount_fixed'] = $this->language->get('entry_max_discount_fixed');
		$this->data['entry_max_discount_percentage'] = $this->language->get('entry_max_discount_percentage');
		// add for Maximum Discount end
		// add for Quotation begin
		$this->data['text_quote_status_setting'] = $this->language->get('text_quote_status_setting');
		$this->data['column_quote_status_name'] = $this->language->get('column_quote_status_name');
		$this->data['column_quote_status_action'] = $this->language->get('column_quote_status_action');
		$this->data['button_rename'] = $this->language->get('button_rename');
		$this->data['text_rename'] = $this->language->get('text_rename');
		$this->data['text_quote_status_already_exist'] = $this->language->get('text_quote_status_already_exist');
		// add for Quotation end
		// add for Empty order control begin
		$this->data['text_status_initial'] = $this->language->get('text_status_initial');
		$this->data['text_status_deleted'] = $this->language->get('text_status_deleted');
		$this->data['text_empty_order_control_setting'] = $this->language->get('text_empty_order_control_setting');
		$this->data['text_empty_order_control_delete_setting'] = $this->language->get('text_empty_order_control_delete_setting');
		$this->data['text_delete_order_with_no_products'] = $this->language->get('text_delete_order_with_no_products');
		$this->data['text_delete_order_with_inital_status'] = $this->language->get('text_delete_order_with_inital_status');
		$this->data['text_delete_order_with_deleted_status'] = $this->language->get('text_delete_order_with_deleted_status');
		$this->data['text_empty_order_control_action'] = $this->language->get('text_empty_order_control_action');
		// add for Empty order control end
		// add for Cash type begin
		$this->data['text_cash_type_setting'] = $this->language->get('text_cash_type_setting');
		$this->data['column_cash_type'] = $this->language->get('column_cash_type');
		$this->data['column_cash_image'] = $this->language->get('column_cash_image');
		$currency_symbol = $this->currency->getSymbolLeft($this->config->get('config_currency'));
		if ($currency_symbol == '') {
			$currency_symbol = $this->currency->getSymbolRight($this->config->get('config_currency'));
		}
		$this->data['column_cash_value'] = $this->language->get('column_cash_value') . ' (' . $currency_symbol . ')';
		$this->data['column_cash_action'] = $this->language->get('column_cash_action');
		$this->data['text_cash_type_note'] = $this->language->get('text_cash_type_note');
		$this->data['text_cash_type_coin'] = $this->language->get('text_cash_type_coin');
		// add for Cash type end
		// add for UPC/SKU/MPN begin
		$this->data['text_scan_type_setting'] = $this->language->get('text_scan_type_setting');
		$this->data['text_scan_type_upc'] = $this->language->get('text_scan_type_upc');
		$this->data['text_scan_type_sku'] = $this->language->get('text_scan_type_sku');
		$this->data['text_scan_type_mpn'] = $this->language->get('text_scan_type_mpn');
		$this->data['config_scan_type'] = $this->config->get('config_scan_type');
		// add for UPC/SKU/MPN end
		// add for location based stock begin
		$this->data['text_location_setting'] = $this->language->get('text_location_setting');
		$this->data['column_location_code'] = $this->language->get('column_location_code');
		$this->data['column_location_name'] = $this->language->get('column_location_name');
		$this->data['column_location_desc'] = $this->language->get('column_location_desc');
		$this->data['column_location_action'] = $this->language->get('column_location_action');
		$this->data['text_location_stock_enable'] = $this->language->get('text_location_stock_enable');
		$this->data['text_location_already_exist'] = $this->language->get('text_location_already_exist');
		$this->data['enable_location_stock'] = 0;
		if (isset($this->request->post['enable_location_stock'])) {
			$this->data['enable_location_stock'] = $this->request->post['enable_location_stock'];
		} else {
			$this->data['enable_location_stock'] = $this->config->get('enable_location_stock');
		}
		$this->data['locations'] = $this->model_pos_pos->getLocations();
		// add for location based stock end
		// add for table management begin
		$this->data['text_table_management_setting'] = $this->language->get('text_table_management_setting');
		$this->data['text_table_management_enable'] = $this->language->get('text_table_management_enable');
		$this->data['entry_table_layout'] = $this->language->get('entry_table_layout');
		$this->data['text_table_layout'] = $this->language->get('text_table_layout');
		$this->data['entry_table_name'] = $this->language->get('entry_table_name');
		$this->data['entry_table_desc'] = $this->language->get('entry_table_desc');
		$this->data['button_set_table'] = $this->language->get('button_set_table');
		$this->data['button_delete_table'] = $this->language->get('button_delete_table');
		$this->data['text_table_name_empty'] = $this->language->get('text_table_name_empty');
		$this->data['text_table_name_exists'] = $this->language->get('text_table_name_exists');
		$this->data['entry_table_number'] = $this->language->get('entry_table_number');
		$this->data['button_table_set_number'] = $this->language->get('button_table_set_number');
		$this->data['column_table_id'] = $this->language->get('column_table_id');
		$this->data['column_table_desc'] = $this->language->get('column_table_desc');
		$this->data['column_table_action'] = $this->language->get('column_table_action');
		$this->data['button_table_modify'] = $this->language->get('button_table_modify');
		$this->data['button_table_remove'] = $this->language->get('button_table_remove');
		$this->data['enable_table_management'] = 0;
		if (isset($this->request->post['enable_table_management'])) {
			$this->data['enable_table_management'] = $this->request->post['enable_table_management'];
		} else {
			$this->data['enable_table_management'] = $this->config->get('enable_table_management');
		}
		$this->data['img_table_layout'] = $this->config->get('img_table_layout');
		$this->data['tables'] = $this->model_pos_pos->getTables(0);
		$this->data['table_number'] = sizeof($this->data['tables']);
		// add for table management end
		// add for Complete Status begin
		$this->data['text_complete_status_setting'] = $this->language->get('text_complete_status_setting');
		$this->data['entry_complete_status'] = $this->language->get('entry_complete_status');
		$this->data['complete_status'] = '';
		if (isset($this->request->post['complete_status'])) {
			$this->data['complete_status'] = $this->request->post['complete_status'];
		} else {
			$this->data['complete_status'] = $this->config->get('complete_status');
		}
		// add for Complete Status end
		// add for Rounding begin
		$this->data['text_rounding_setting'] = $this->language->get('text_rounding_setting');
		$this->data['text_rounding_enable'] = $this->language->get('text_rounding_enable');
		$this->data['text_rounding_5c'] = $this->language->get('text_rounding_5c');
		$this->data['text_rounding_10c'] = $this->language->get('text_rounding_10c');
		$this->data['text_rounding_50c'] = $this->language->get('text_rounding_50c');
		$this->data['enable_rounding'] = 0;
		if (isset($this->request->post['enable_rounding'])) {
			$this->data['enable_rounding'] = $this->request->post['enable_rounding'];
		} else {
			$this->data['enable_rounding'] = $this->config->get('enable_rounding');
		}
		$this->data['config_rounding'] = '';
		if (isset($this->request->post['config_rounding'])) {
			$this->data['config_rounding'] = $this->request->post['config_rounding'];
		} else {
			$this->data['config_rounding'] = $this->config->get('config_rounding');
		}
		// add for Rounding end
		// add for till control begin
		$this->data['text_till_control_setting'] = $this->language->get('text_till_control_setting');
		$this->data['text_till_control_enable'] = $this->language->get('text_till_control_enable');
		$this->data['enable_till_control'] = $this->config->get('enable_till_control');
		$this->data['entry_till_control_key'] = $this->language->get('entry_till_control_key');
		$this->data['button_test'] = $this->language->get('button_test');
		$this->data['text_till_full_payment_enable'] = $this->language->get('text_till_full_payment_enable');
		$this->data['till_control_key'] = '';
		if (isset($this->request->post['till_control_key'])) {
			$this->data['till_control_key'] = $this->request->post['till_control_key'];
		} else {
			$this->data['till_control_key'] = $this->config->get('till_control_key');
		}
		$this->data['enable_till_full_payment'] = 0;
		if (isset($this->request->post['enable_till_full_payment'])) {
			$this->data['enable_till_full_payment'] = $this->request->post['enable_till_full_payment'];
		} else {
			$this->data['enable_till_full_payment'] = $this->config->get('enable_till_full_payment');
		}
		// add for till control end
		// add for serial no begin
		$this->data['text_add_serial_no_setting'] = $this->language->get('text_add_serial_no_setting');
		$this->data['entry_product'] = $this->language->get('entry_product');
		$this->data['entry_sn'] = $this->language->get('entry_sn');
		$this->data['button_sn_save'] = $this->language->get('button_sn_save');
		$this->data['text_list_serial_no_setting'] = $this->language->get('text_list_serial_no_setting');
		$this->data['column_sn_product_name'] = $this->language->get('column_sn_product_name');
		$this->data['column_sn_product_sn'] = $this->language->get('column_sn_product_sn');
		$this->data['column_sn_product_status'] = $this->language->get('column_sn_product_status');
		$this->data['column_action'] = $this->language->get('column_action');
		$this->data['text_sn_sold'] = $this->language->get('text_sn_sold');
		$this->data['text_sn_in_store'] = $this->language->get('text_sn_in_store');
		$this->data['button_search'] = $this->language->get('button_search');
		// add for serial no end
		// add for Status Change Notification begin
		$this->data['text_notification_setting'] = $this->language->get('text_notification_setting');
		$this->data['text_notification_enable'] = $this->language->get('text_notification_enable');
		$this->data['enable_notification'] = 0;
		if (isset($this->request->post['enable_notification'])) {
			$this->data['enable_notification'] = $this->request->post['enable_notification'];
		} else {
			$this->data['enable_notification'] = $this->config->get('enable_notification');
		}
		// add for Status Change Notification end
		// add for commission begin
		$this->data['text_commission_setting'] = $this->language->get('text_commission_setting');
		$this->data['text_commission_enable'] = $this->language->get('text_commission_enable');
		$this->data['text_set_commission'] = $this->language->get('text_set_commission');
		$this->data['entry_product'] = $this->language->get('entry_product');
		$this->data['entry_commission_fixed'] = $this->language->get('entry_commission_fixed');
		$this->data['entry_commission_percentage'] = $this->language->get('entry_commission_percentage');
		$this->data['text_commission_percentage_base'] = $this->language->get('text_commission_percentage_base');
		$this->data['button_commission_save'] = $this->language->get('button_commission_save');
		$this->data['enable_commission'] = 0;
		if (isset($this->request->post['enable_commission'])) {
			$this->data['enable_commission'] = $this->request->post['enable_commission'];
		} else {
			$this->data['enable_commission'] = $this->config->get('enable_commission');
		}
		$this->data['text_list_commission_setting'] = $this->language->get('text_list_commission_setting');
		$this->data['column_commission_product_name'] = $this->language->get('column_commission_product_name');
		$this->data['column_commission_commission'] = $this->language->get('column_commission_commission');
		$this->data['column_commission_action'] = $this->language->get('column_commission_action');
		$this->data['button_commission_search'] = $this->language->get('button_commission_search');
		// add for commission end
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_add_type'] = $this->language->get('button_add_type');
		$this->data['button_remove'] = $this->language->get('button_remove');
		
		$this->data['token'] = $this->session->data['token'];
		
		$this->load->model('user/user');
		$this->data['users'] = $this->model_user_user->getUsers();
		$this->load->model('user/user_group');
		$this->data['user_groups'] = $this->model_user_user_group->getUserGroups();

		$excluded_groups = array();
		if ($this->config->get('excluded_groups')) {
			$excluded_groups = $this->config->get('excluded_groups');
		}
		$this->data['excluded_groups'] = $excluded_groups;
		
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
			'href'      => $this->url->link('module/pos', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/pos', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['payment_types'] = array();
		
		if (isset($this->request->post['POS_payment_types'])) {
			$this->data['payment_types'] = $this->request->post['POS_payment_types'];
		} elseif ($this->config->get('POS_payment_types')) {
			$this->data['payment_types'] = $this->config->get('POS_payment_types');
		}
		// add for Openbay integration begin
		if (isset($this->request->post['enable_openbay'])) {
			$this->data['enable_openbay'] = $this->request->post['enable_openbay'];
		} else {
			$this->data['enable_openbay'] = $this->config->get('enable_openbay');
		}
		// add for Openbay integration end

		if (isset($this->request->post['display_once_login'])) {
			$this->data['display_once_login'] = $this->request->post['display_once_login'];
		} else {
			$this->data['display_once_login'] = $this->config->get('display_once_login');
		}
		
		// add for Print being
		if (isset($this->request->post['p_logo'])) {
			$this->data['p_logo'] = $this->request->post['p_logo'];
		} else {
			$this->data['p_logo'] = $this->config->get('p_logo') ? $this->config->get('p_logo') : 'view/image/pos/no_image.png';
		}
		if (isset($this->request->post['p_width'])) {
			$this->data['p_width'] = $this->request->post['p_width'];
		} else {
			$this->data['p_width'] = $this->config->get('p_width') ? $this->config->get('p_width') : '200';
		}
		if (isset($this->request->post['p_complete'])) {
			$this->data['p_complete'] = $this->request->post['p_complete'];
		} else {
			$this->data['p_complete'] = $this->config->get('p_complete');
		}
		if (isset($this->request->post['p_payment'])) {
			$this->data['p_payment'] = $this->request->post['p_payment'];
		} else {
			$this->data['p_payment'] = $this->config->get('p_payment');
		}
		if (isset($this->request->post['p_term_n_cond'])) {
			$this->data['p_term_n_cond'] = $this->request->post['p_term_n_cond'];
		} else {
			$this->data['p_term_n_cond'] = $this->config->get('p_term_n_cond');
			if (!$this->data['p_term_n_cond']) {
				$this->data['p_term_n_cond'] = $this->language->get('term_n_cond_default');
			}
		}
		// add for Print end
		// add for Inplace Pricing begin
		if (isset($this->request->post['enable_inplace_pricing'])) {
			$this->data['enable_inplace_pricing'] = $this->request->post['enable_inplace_pricing'];
		} else {
			$this->data['enable_inplace_pricing'] = $this->config->get('enable_inplace_pricing');
		}
		// add for Inplace Pricing end
		// add for Hiding Delete begin
		$this->load->model('user/user_group');
		$this->data['user_groups'] = $this->model_user_user_group->getUserGroups();
		if (isset($this->request->post['enable_hide_delete'])) {
			$this->data['enable_hide_delete'] = $this->request->post['enable_hide_delete'];
		} else {
			$this->data['enable_hide_delete'] = $this->config->get('enable_hide_delete');
		}
		$delete_excluded_groups = array();
		if ($this->config->get('delete_excluded_groups')) {
			$delete_excluded_groups = $this->config->get('delete_excluded_groups');
		}
		$this->data['delete_excluded_groups'] = $delete_excluded_groups;
		// add for Hiding Delete end
		// add for Hiding Order Status begin
		$order_hiding_status = array();
		if ($this->config->get('order_hiding_status')) {
			$order_hiding_status = $this->config->get('order_hiding_status');
		}
		$this->data['order_hiding_status'] = $order_hiding_status;
		$this->load->model('localisation/order_status');
    	$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		// add for Hiding Order Status end
		// add for Empty order control begin
		if (isset($this->request->post['initial_status_id'])) {
			$this->data['initial_status_id'] = $this->request->post['initial_status_id'];
		} else {
			$this->data['initial_status_id'] = $this->config->get('initial_status_id') ? $this->config->get('initial_status_id') : '0';
		}
		if (isset($this->request->post['delete_order_with_no_products'])) {
			$this->data['delete_order_with_no_products'] = $this->request->post['delete_order_with_no_products'];
		} else {
			$this->data['delete_order_with_no_products'] = $this->config->get('delete_order_with_no_products');
		}
		if (isset($this->request->post['delete_order_with_inital_status'])) {
			$this->data['delete_order_with_inital_status'] = $this->request->post['delete_order_with_inital_status'];
		} else {
			$this->data['delete_order_with_inital_status'] = $this->config->get('delete_order_with_inital_status');
		}
		if (isset($this->request->post['delete_order_with_deleted_status'])) {
			$this->data['delete_order_with_deleted_status'] = $this->request->post['delete_order_with_deleted_status'];
		} else {
			$this->data['delete_order_with_deleted_status'] = $this->config->get('delete_order_with_deleted_status');
		}
		// add for Empty order control end
		// add for User as Affiliate begin
		// get affiliate list
		$this->load->model('sale/affiliate');
		$affiliates = $this->model_sale_affiliate->getAffiliates();
		$this->data['user_affis'] = $this->model_pos_pos->getUAs();
		$this->data['ua_users'] = array();
		$this->data['ua_affiliates'] = array();
		foreach ($this->data['users'] as $user) {
			$inlist = false;
			foreach ($this->data['user_affis'] as $user_affi) {
				if ($user['user_id'] == $user_affi['user_id']) {
					$inlist = true;
					break;
				}
			}
			if (!$inlist) {
				// not associated yet
				array_push($this->data['ua_users'], $user);
			}
		}
		foreach ($affiliates as $affiliate) {
			$inlist = false;
			foreach ($this->data['user_affis'] as $user_affi) {
				if ($affiliate['affiliate_id'] == $user_affi['affiliate_id']) {
					$inlist = true;
					break;
				}
			}
			if (!$inlist) {
				// not associated yet
				array_push($this->data['ua_affiliates'], $affiliate);
			}
		}
		// add for User as Affiliate end
		// add for Default Customer begin
		$this->load->model('localisation/country');
		$this->data['c_countries'] = $this->model_localisation_country->getCountries();
		$this->setDefaultCustomer($this->data);
		$this->load->model('sale/customer_group');
		$this->data['c_groups'] = $this->model_sale_customer_group->getCustomerGroups();
		// add for Default Customer end
		// add for Maximum Discount begin
		foreach ($this->data['user_groups'] as $key => $user_group) {
			$max_discount_fixed = 0;
			if ($this->config->get($user_group['user_group_id'].'_max_discount_fixed')) {
				$max_discount_fixed = $this->config->get($user_group['user_group_id'].'_max_discount_fixed');
			}
			$this->data['user_groups'][$key]['max_discount_fixed'] = $max_discount_fixed;
			$max_discount_percentage = 0;
			if ($this->config->get($user_group['user_group_id'].'_max_discount_percentage')) {
				$max_discount_percentage = $this->config->get($user_group['user_group_id'].'_max_discount_percentage');
			}
			$this->data['user_groups'][$key]['max_discount_percentage'] = $max_discount_percentage;
		}
		// add for Maximum Discount end
		// add for Quotation begin
		$this->data['quote_statuses'] = $this->model_pos_pos->getQuoteStatuses();
		// add for Quotation end
		// add for Cash type begin
		$this->data['cash_types'] = $this->config->get('cash_types');
		// add for Cash type end

		$this->template = 'pos/settings.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	// add for Default Customer begin
	private function setDefaultCustomer(&$data) {
		// add for Default Customer begin
		$default_country_id = $this->config->get('config_country_id');
		$default_zone_id = $this->config->get('config_zone_id');
		$data['c_id'] = $this->config->get('c_id') ? $this->config->get('c_id') : 0;
		$data['c_group_id'] = $this->config->get('c_group_id') ? $this->config->get('c_group_id') : 1;
		$use_default_general = true;
		$use_default_address = true;
		if ($this->config->get('c_type') == 2 || $this->config->get('c_type') == 3) {
			$data['c_type'] = $this->config->get('c_type');
			if ($this->config->get('c_type') == 2) {
				// use the configuration from settings table
				$data['a_country_id'] = $this->config->get('a_country_id') ? $this->config->get('a_country_id') : $default_country_id;
				$data['a_zone_id'] = $this->config->get('a_zone_id') ? $this->config->get('a_zone_id') : $default_zone_id;
				$data['c_firstname'] = $this->config->get('c_firstname') ? $this->config->get('c_firstname') : 'Instore';
				$data['c_lastname'] = $this->config->get('c_lastname') ? $this->config->get('c_lastname') : 'Dummy';
				$data['c_name'] = $data['c_firstname'] . ' ' . $data['c_lastname'];
				$data['c_email'] = $this->config->get('c_email') ? $this->config->get('c_email') : 'customer@instore.com';
				$data['c_telephone'] = $this->config->get('c_telephone') ? $this->config->get('c_telephone') : '1600';
				$data['c_fax'] = $this->config->get('c_fax') ? $this->config->get('c_fax') : '';
				$data['a_firstname'] = $this->config->get('a_firstname') ? $this->config->get('a_firstname') : 'Instore';
				$data['a_lastname'] = $this->config->get('a_lastname') ? $this->config->get('a_lastname') : 'Dummy';
				$data['a_address_1'] = $this->config->get('a_address_1') ? $this->config->get('a_address_1') : 'customer address';
				$data['a_address_2'] = $this->config->get('a_address_2') ? $this->config->get('a_address_2') : '';
				$data['a_city'] = $this->config->get('a_city') ? $this->config->get('a_city') : 'customer city';
				$data['a_postcode'] = $this->config->get('a_postcode') ? $this->config->get('a_postcode') : '1600';
				$use_default_general = false;
				$use_default_address = false;
			} else {
				// get the first address from customer address
				$this->load->model('sale/customer');
				$c_info = $this->model_sale_customer->getCustomer($data['c_id']);
				if ($c_info) {
					$use_default_general = false;
					$data['c_group_id'] = $c_info['customer_group_id'];
					$data['c_firstname'] = $c_info['firstname'];
					$data['c_lastname'] = $c_info['lastname'];
					$data['c_name'] = $data['c_firstname'] . ' ' . $data['c_lastname'];
					$data['c_email'] = $c_info['email'];
					$data['c_telephone'] = $c_info['telephone'];
					$data['c_fax'] = $c_info['fax'];
				}
				$c_addresses = $this->model_sale_customer->getAddresses($data['c_id']);
				ksort($c_addresses);
				if (count($c_addresses) > 0) {
					$use_default_address = false;
					foreach ($c_addresses as $c_address) {
						$data['a_country_id'] = $c_address['country_id'];
						$data['a_zone_id'] = $c_address['zone_id'];
						$data['a_firstname'] = $c_address['firstname'];
						$data['a_lastname'] = $c_address['lastname'];
						$data['a_address_1'] = $c_address['address_1'];
						$data['a_address_2'] = $c_address['address_2'];
						$data['a_city'] = $c_address['city'];
						$data['a_postcode'] = $c_address['postcode'];
						break;
					}
				}
			}
		} else {
			$data['c_type'] = 1;
		}
		
		$data['buildin'] = array();
		$data['buildin']['c_firstname'] = 'Instore';
		$data['buildin']['c_lastname'] = "Dummy";
		$data['buildin']['c_name'] = $data['buildin']['c_firstname'] . ' ' . $data['buildin']['c_lastname'];
		$data['buildin']['c_email'] = 'customer@instore.com';
		$data['buildin']['c_telephone'] = '1600';
		$data['buildin']['c_fax'] = '';
		$data['buildin']['a_country_id'] = $default_country_id;
		$data['buildin']['a_zone_id'] = $default_zone_id;
		$data['buildin']['a_firstname'] = 'Instore';
		$data['buildin']['a_lastname'] = "Dummy";
		$data['buildin']['a_address_1'] = 'customer address';
		$data['buildin']['a_address_2'] = '';
		$data['buildin']['a_city'] = 'customer city';
		$data['buildin']['a_postcode'] = '1600';

		if ($use_default_general) {
			$data['c_firstname'] = 'Instore';
			$data['c_lastname'] = "Dummy";
			$data['c_name'] = $data['c_firstname'] . ' ' . $data['c_lastname'];
			$data['c_email'] = 'customer@instore.com';
			$data['c_telephone'] = '1600';
			$data['c_fax'] = '';
		}
		if ($use_default_address) {
			$data['a_country_id'] = $default_country_id;
			$data['a_zone_id'] = $default_zone_id;
			$data['a_firstname'] = 'Instore';
			$data['a_lastname'] = "Dummy";
			$data['a_address_1'] = 'customer address';
			$data['a_address_2'] = '';
			$data['a_city'] = 'customer city';
			$data['a_postcode'] = '1600';
		}
	}
	// add for Default Customer end
	
	protected function validate() {
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}

	public function install() {
		/*
		if (!$this->checkVqmod()) {
			// not existing
			$this->language->load('module/pos');
			$this->session->data['error'] = $this->language->get('text_vqmod_not_installed');
			$this->load->model('setting/extension');
			// remove from the extension table
			$this->model_setting_extension->uninstall('module', 'pos');
			return false;
		}
		*/
		
		// create tables
		$this->load->model('pos/pos');
		$this->model_pos_pos->createModuleTables();

		// create vqmod files
		$this->createFile();
		
		// copy language file is English not set to default
		$this->copyLangFile();
		
		// add cash types
		$this->load->model('setting/setting');
		$pos_settings = $this->model_setting_setting->getSetting('POS');
		if (!isset($pos_settings['cash_types'])) {
			// no previous cash types defined, set Australia cash as default
			$this->language->load('module/pos');
			$text_note = $this->language->get('text_cash_type_note');
			$text_coin = $this->language->get('text_cash_type_coin');
			$cash_types = array(array('type'=>$text_note, 'image'=>'view/image/pos/aud_100_dollars.jpg', 'value'=>100), 
								array('type'=>$text_note, 'image'=>'view/image/pos/aud_50_dollars.jpg', 'value'=>50),
								array('type'=>$text_note, 'image'=>'view/image/pos/aud_20_dollars.jpg', 'value'=>20),
								array('type'=>$text_note, 'image'=>'view/image/pos/aud_10_dollars.jpg', 'value'=>10),
								array('type'=>$text_note, 'image'=>'view/image/pos/aud_5_dollars.jpg', 'value'=>5),
								array('type'=>$text_coin, 'image'=>'view/image/pos/aud_2_dollars.jpg', 'value'=>2),
								array('type'=>$text_coin, 'image'=>'view/image/pos/aud_1_dollar.jpg', 'value'=>1),
								array('type'=>$text_coin, 'image'=>'view/image/pos/aud_50_cents.jpg', 'value'=>0.5),
								array('type'=>$text_coin, 'image'=>'view/image/pos/aud_20_cents.jpg', 'value'=>0.2),
								array('type'=>$text_coin, 'image'=>'view/image/pos/aud_10_cents.jpg', 'value'=>0.1),
								array('type'=>$text_coin, 'image'=>'view/image/pos/aud_5_cents.jpg', 'value'=>0.05));
			$this->db->query("REPLACE INTO " . DB_PREFIX . "setting SET `value` = '" . $this->db->escape(serialize($cash_types)) . "', serialized = '1', `group` = 'POS', `key` = 'cash_types', store_id = '0'");
		}
		
		// add permission for report
		$this->load->model('user/user_group');
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'report/order_payment');
		$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'report/order_payment');
		// add for commission begin	
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'report/pos_commission');
		$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'report/pos_commission');
		// add for commission end		
		// install in store shipping and payment method
		$this->load->model('setting/extension');
		if ($this->user->hasPermission('modify', 'extension/shipping')) {
			$this->model_setting_extension->install('shipping', 'instore');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'shipping/instore');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'shipping/instore');
			$this->model_setting_setting->editSetting('instore', array('instore_geo_zone_id'=>'0', 'instore_status'=>'1', 'instore_sort_order'=>'1'));
		}
		if ($this->user->hasPermission('modify', 'extension/payment')) {
			$this->model_setting_extension->install('payment', 'in_store');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'payment/in_store');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'payment/in_store');
			$this->model_setting_setting->editSetting('in_store', array('in_store_geo_zone_id'=>'0', 'in_store_status'=>'1', 'in_store_sort_order'=>'1'));
		}
		// add for Discount begin
		if ($this->user->hasPermission('modify', 'extension/total')) {
			$this->model_setting_extension->install('total', 'pos_discount');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'total/pos_discount');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'total/pos_discount');
			$default_sort_order = '1';
			if ($this->config->get('total_sort_order')) {
				$default_sort_order = (int)$this->config->get('total_sort_order') - 2;
			}
			$this->model_setting_setting->editSetting('pos_discount', array('pos_discount_status'=>'1', 'pos_discount_sort_order'=>$default_sort_order));
		}
		// add for Discount end
		// add for Rounding begin
		if ($this->user->hasPermission('modify', 'extension/total')) {
			$this->model_setting_extension->install('total', 'pos_rounding');
			$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'total/pos_rounding');
			$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'total/pos_rounding');
			$default_sort_order = '1';
			if ($this->config->get('total_sort_order')) {
				$default_sort_order = (int)$this->config->get('total_sort_order') - 1;
			}
			$this->model_setting_setting->editSetting('pos_rounding', array('pos_rounding_status'=>'1', 'pos_rounding_sort_order'=>$default_sort_order));
		}
		// add for Rounding end
		
		// create a new point of sale group
		$ignore = array(
			'common/home',
			'common/startup',
			'common/login',
			'common/logout',
			'common/forgotten',
			'common/reset',			
			'error/not_found',
			'error/permission',
			'common/footer',
			'common/header'
		);

		$data['permission'] = array();
		$data['permission']['access'] = array();
		$data['permission']['modify'] = array();
		
		$files = glob(DIR_APPLICATION . 'controller/*/*.php');
		
		foreach ($files as $file) {
			$file_data = explode('/', dirname($file));
			
			$permission = end($file_data) . '/' . basename($file, '.php');
			
			if (!in_array($permission, $ignore)) {
				$data['permission']['access'][] = $permission;
				$data['permission']['modify'][] = $permission;
			}
		}
		$data['name'] = 'POS';
		$this->load->model('user/user_group');
		$groups = $this->model_user_user_group->getUserGroups();
		$pos_group_defined = false;
		if (!empty($groups)) {
			foreach ($groups as $group) {
				if ($group['name'] == 'POS') {
					$this->model_user_user_group->editUserGroup($group['user_group_id'], $data);
					$pos_group_defined = true;
					break;
				}
			}
		}
		if (!$pos_group_defined) {
			$this->model_user_user_group->addUserGroup($data);
		}
	}

	public function uninstall() {
		// $this->load->model('pos/pos');
		// $this->model_pos_pos->deleteModuleTables();

		// remove the files
		// $this->deleteFile();

		// $this->load->model('setting/setting');
		// $this->model_setting_setting->deleteSetting('POS');
		$this->load->model('setting/extension');
		$this->load->model('setting/setting');
		if ($this->user->hasPermission('modify', 'extension/shipping')) {
			$this->model_setting_extension->uninstall('shipping', 'instore');
			$this->model_setting_setting->deleteSetting('instore');
		}
		if ($this->user->hasPermission('modify', 'extension/payment')) {
			$this->model_setting_extension->uninstall('payment', 'in_store');
			$this->model_setting_setting->deleteSetting('in_store');
		}
		// add for Discount begin
		if ($this->user->hasPermission('modify', 'extension/total')) {
			$this->model_setting_extension->uninstall('total', 'pos_discount');
			$this->model_setting_setting->deleteSetting('pos_discount');
		}
		// add for Discount end
		// add for Rounding begin
		if ($this->user->hasPermission('modify', 'extension/total')) {
			$this->model_setting_extension->uninstall('total', 'pos_rounding');
			$this->model_setting_setting->deleteSetting('pos_rounding');
		}
		// add for Rounding end
		// remove pos group
		$this->load->model('user/user_group');
		$groups = $this->model_user_user_group->getUserGroups();
		if (!empty($groups)) {
			foreach ($groups as $group) {
				if ($group['name'] == 'POS') {
					$this->model_user_user_group->deleteUserGroup($group['user_group_id']);
					break;
				}
			}
		}
	}
	
	private function checkVqmod() {
		return file_exists(DIR_APPLICATION . '/../vqmod');
	}

	private function createFile() {
		if (version_compare(VERSION, '1.5.5.1', '>')) {
			// add for inplace pricing begin
			copy(DIR_APPLICATION . 'model/pos/pos_inplace_pricing_1.5.6.xml', DIR_APPLICATION . '../vqmod/xml/pos_inplace_pricing_1.5.6.xml');
			// add for inplace pricing end
			// add for weight based price begin
			copy(DIR_APPLICATION . 'model/pos/pos_product_weight_price_1.5.6.xml', DIR_APPLICATION . '../vqmod/xml/pos_product_weight_price_1.5.6.xml');
			// add for weight based price end
			// add for location based stock begin
			copy(DIR_APPLICATION . 'model/pos/pos_location_stock_1.5.6.xml', DIR_APPLICATION . '../vqmod/xml/pos_location_stock_1.5.6.xml');
			// add for location based stock end

			copy(DIR_APPLICATION . 'model/pos/pos_tax_calculate_1.5.6.xml', DIR_APPLICATION . '../vqmod/xml/pos_tax_calculate_1.5.6.xml');
		} else {
			// add for inplace pricing begin
			copy(DIR_APPLICATION . 'model/pos/pos_inplace_pricing.xml', DIR_APPLICATION . '../vqmod/xml/pos_inplace_pricing.xml');
			// add for inplace pricing end
			// add for weight based price begin
			copy(DIR_APPLICATION . 'model/pos/pos_product_weight_price.xml', DIR_APPLICATION . '../vqmod/xml/pos_product_weight_price.xml');
			// add for weight based price end
			// add for location based stock begin
			copy(DIR_APPLICATION . 'model/pos/pos_location_stock.xml', DIR_APPLICATION . '../vqmod/xml/pos_location_stock.xml');
			// add for location based stock end
			
			copy(DIR_APPLICATION . 'model/pos/pos_tax_calculate.xml', DIR_APPLICATION . '../vqmod/xml/pos_tax_calculate.xml');
		}
		// the hide_pos_orders.xml can prevent opencart from functioning if pos is not installed, now default not install this file, can be copied later
		// copy(DIR_APPLICATION . 'model/pos/pos_hide_pos_orders.xml', DIR_APPLICATION . '../vqmod/xml/pos_hide_pos_orders.xml');
	}

	private function deleteFile() {
		unlink(DIR_APPLICATION . '../vqmod/xml/pos.xml');
		unlink(DIR_APPLICATION . '../vqmod/xml/pos_redirect.xml');
	}
	
	private function copyLangFile() {
		$supported_languages = array();
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "language`"); 
		foreach ($query->rows as $result) {
			$supported_languages[$result['code']] = $result;
		}
		$directory = $supported_languages[$this->config->get('config_admin_language')]['directory'];
		if ($directory != 'english') {
			copy(DIR_LANGUAGE . 'english/pos/pos.php', DIR_LANGUAGE . $directory . '/pos/pos.php');
		}
	}

	public function addOrderPayment() {
		$this->load->model('pos/pos');
		if ($this->request->server['REQUEST_METHOD'] == 'GET') {
			$this->model_pos_pos->addOrderPayment($this->request->get);
			$json = array();
			// add for Print begin
			$json['p_payment'] = $this->config->get('p_payment') ? $this->config->get('p_payment') : 0;
			// add for Print end
			$this->response->setOutput(json_encode($json));
		}
	}

	public function deleteOrderPayment() {
		$this->load->model('pos/pos');
		if ($this->request->server['REQUEST_METHOD'] == 'GET') {
			$this->model_pos_pos->deleteOrderPayment($this->request->get);
			$this->response->setOutput(json_encode(array()));
		}
	}
	
	public function modifyOrderComment() {
		$this->load->model('pos/pos');
		if ($this->request->server['REQUEST_METHOD'] == 'GET') {
			$this->model_pos_pos->modifyOrderComment($this->request->get);
			$this->response->setOutput(json_encode(array()));
		}
	}
	
	public function main() {
		$this->language->load('module/pos');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (isset($this->request->post['selected'])) {
				// selected orders to be deleted
				$this->load->model('sale/order');
				foreach ($this->request->post['selected'] as $order_id) {
					$this->model_sale_order->deleteOrder($order_id);
				}
			}
		}
		
		if (isset($this->session->data['pos_user_login'])) {
			unset($this->session->data['pos_user_login']);
			$this->search_for_update();
		}
		
		if (isset($this->session->data['text_decimal_point']) && isset($this->session->data['text_thousand_point'])) {
			$this->data['text_decimal_point'] = $this->session->data['text_decimal_point'];
			$this->data['text_thousand_point'] = $this->session->data['text_thousand_point'];
		} else {
			// get the decimal point and thousand point from the front side language instead of the admin language
			$this->load->model('localisation/language');
			$languages = $this->model_localisation_language->getLanguages();
			$lang_dir = 'english';
			$lang_file = 'english';
			foreach ($languages as $language) {
				if ($language['code'] == $this->config->get('config_language')) {
					$lang_dir = $language['directory'];
					$lang_file = $language['filename'];
					break;
				}
			}
			include_once (DIR_CATALOG . 'language/' . $lang_dir . '/' . $lang_file . '.php');
			$this->data['text_decimal_point'] = $_['decimal_point'];
			$this->data['text_thousand_point'] = $_['thousand_point'];
			
			$this->session->data['text_decimal_point'] = $_['decimal_point'];
			$this->session->data['text_thousand_point'] = $_['thousand_point'];
		}

		$this->load->model('pos/pos');
		
		// add for Purchase Order Payment begin
		$pos_settings = $this->model_setting_setting->getSetting('POS');
		if (!isset($pos_settings['POS_payment_types'])) {
			// first time run the module, create the default payment types
			$pos_payments = array('cash'=>'Cash', 'credit_card'=>'Credit Card');
			$this->db->query("REPLACE INTO " . DB_PREFIX . "setting SET `value` = '" . $this->db->escape(serialize($pos_payments)) . "', serialized = '1', `group` = 'POS', `key` = 'POS_payment_types', store_id = '0'");
		} else {
			$pos_payments = $pos_settings['POS_payment_types'];
		}
		if (! array_key_exists('purchase_order', $pos_payments)) {
			$pos_payments['purchase_order'] = $this->language->get('purchase_order');
			// $this->model_setting_setting->editSettingValue('POS', 'POS_payment_types', $pos_payments);
			$store_id = 0;
			$this->db->query("UPDATE " . DB_PREFIX . "setting SET `value` = '" . $this->db->escape(serialize($pos_payments)) . "', serialized = '1' WHERE `group` = 'POS' AND `key` = 'POS_payment_types' AND store_id = '" . $store_id . "'");
		}
		$this->data['text_purchase_order_number'] = $this->language->get('text_purchase_order_number');
		// add for Purchase Order Payment end
		// add for Empty order control begin
		if (!$this->config->get('initial_status_id')) {
			$initial_status_id = $this->model_pos_pos->addAdditionalOrderStatus($this->language->get('text_status_initial'), $this->language->get('text_status_deleted'));
			$this->cache->delete('order_status');
			// $this->model_setting_setting->editSettingValue('POS', 'initial_status_id', $initial_status_id);
			$this->db->query("REPLACE INTO " . DB_PREFIX . "setting SET `value` = '" . $initial_status_id . "', `group` = 'POS', `key` = 'initial_status_id', store_id = '0'");
		}
		// add for Empty order control end
		
		// add for location based stock begin
		$location_id = $this->model_pos_pos->getLocationForUser($this->user->getId());
		if ($this->config->get('enable_location_stock') && $location_id) {
			$this->session->data['location_id'] = $location_id;
		}
		// add for location based stock end
		// add for table management begin
		$this->data['enable_table_management'] = $this->config->get('enable_table_management');
		$this->data['img_table_layout'] = $this->config->get('img_table_layout');
		$this->data['tables'] = $this->model_pos_pos->getTables(0);
		$this->data['table_orders'] = $this->model_pos_pos->getTables(1);
		// add for table management end
		
		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['text_terminal'] = $this->language->get('text_terminal');
		$this->data['text_register_mode'] = $this->language->get('text_register_mode');
		$this->data['text_date_added'] = $this->language->get('text_date_added');
		$this->data['text_date_modified'] = $this->language->get('text_date_modified');
		$this->data['text_customer'] = $this->language->get('text_customer');
		$this->data['text_product_quantity'] = $this->language->get('text_product_quantity');
		$this->data['text_items_in_cart']  = $this->language->get('text_items_in_cart');
		$this->data['text_amount_due']  = $this->language->get('text_amount_due');
		$this->data['text_change']  = $this->language->get('text_change');
		$this->data['text_payment_zero_amount']  = $this->language->get('text_payment_zero_amount');
		$this->data['text_quantity_zero']  = $this->language->get('text_quantity_zero');
		$this->data['text_comments'] = $this->language->get('text_comments');
		$this->data['text_order_sucess'] = $this->language->get('text_order_sucess');
		$this->data['text_load_order'] = $this->language->get('text_load_order');
		$this->data['text_filter_order_list'] = $this->language->get('text_filter_order_list');
		$this->data['text_load_order_list'] = $this->language->get('text_load_order_list');

		$this->data['text_product_name'] = $this->language->get('text_product_name');
		$this->data['text_product_upc'] = $this->language->get('text_product_upc');
		$this->data['text_no_order_selected'] = $this->language->get('text_no_order_selected');
		$this->data['text_confirm_delete_order'] = $this->language->get('text_confirm_delete_order');
		$this->data['text_not_available'] = $this->language->get('text_not_available');
		$this->data['text_del_payment_confirm'] = $this->language->get('text_del_payment_confirm');
		$this->data['text_autocomplete'] = $this->language->get('text_autocomplete');
		$this->data['text_customer_no_address'] = $this->language->get('text_customer_no_address');

		$this->data['column_payment_type']  = $this->language->get('column_payment_type');
		$this->data['column_payment_amount']  = $this->language->get('column_payment_amount');
		$this->data['column_payment_note']  = $this->language->get('column_payment_note');
		$this->data['column_payment_action']  = $this->language->get('column_payment_action');

		$this->data['button_add_payment']  = $this->language->get('button_add_payment');

		$this->data['button_existing_order'] = $this->language->get('button_existing_order'); 
		$this->data['button_new_order'] = $this->language->get('button_new_order'); 
		$this->data['button_complete_order'] = $this->language->get('button_complete_order');
		$this->data['button_print_invoice'] = $this->language->get('Print Invoice');
		$this->data['button_full_screen'] = $this->language->get('button_full_screen');
		$this->data['button_normal_screen'] = $this->language->get('button_normal_screen');
		$this->data['button_discount'] = $this->language->get('button_discount');
		$this->data['button_cut'] = $this->language->get('button_cut');
		$this->data['button_delete'] = $this->language->get('button_delete');
		$this->data['button_add_product'] = $this->language->get('button_add_product');
				
		$this->data['tab_product_search'] = $this->language->get('tab_product_search');
		$this->data['tab_product_browse'] = $this->language->get('tab_product_browse');
		$this->data['tab_product_details'] = $this->language->get('tab_product_details');
		$this->data['tab_order_shipping'] = $this->language->get('tab_order_shipping');
		$this->data['tab_order_payments'] = $this->language->get('tab_order_payments');
		$this->data['tab_order_customer'] = $this->language->get('tab_order_customer');
		
		// add for Report begin
		$this->data['report_heading_title'] = $this->language->get('report_heading_title');
		// add for Report end
		
		// add for Print begin
		$this->data['print_wait_title'] = $this->language->get('print_wait_title');
		$this->data['print_wait_message'] = $this->language->get('print_wait_message');
		$this->data['print_sign_message'] = $this->language->get('print_sign_message');
		$this->data['print_receipt_message'] = $this->language->get('print_receipt_message');
		// add for Print end
		// add for Invoice Print begin
		$this->data['print_invoice_message'] = $this->language->get('print_invoice_message');
		// add for Invoice Print end
		// add for Discount begin
		$this->data['tab_order_discount'] = $this->language->get('tab_order_discount');
		$this->data['text_discount_title'] = $this->language->get('text_discount_title');
		$this->data['text_discount_message'] = $this->language->get('text_discount_message');
		$this->data['text_discount_type_amount'] = $this->language->get('text_discount_type_amount');
		$this->data['text_discount_type_percentage'] = $this->language->get('text_discount_type_percentage');
		$this->data['text_discount_subtotal'] = $this->language->get('text_discount_subtotal');
		$this->data['text_discount_total'] = $this->language->get('text_discount_total');
		$this->data['text_discount'] = $this->language->get('text_discount');
		$this->data['text_discounted'] = $this->language->get('text_discounted');
		$this->data['text_discounted_title'] = $this->language->get('text_discounted_title');
		$this->data['button_discount'] = $this->language->get('button_discount');
		$this->data['text_apply_discount'] = $this->language->get('text_apply_discount');
		// add for Discount end
		// add for Inplace Pricing begin
		$this->data['enable_inplace_pricing'] = $this->config->get('enable_inplace_pricing');
		// add for Inplace Pricing end
		// add for Manufacturer Product begin
		$this->data['entry_manufacturer'] = $this->language->get('entry_manufacturer');
		// add for Manufacturer Product end
		// add for edit order address begin
		$this->data['text_order_shipping_address'] = $this->language->get('text_order_shipping_address');
		$this->data['text_order_payment_address'] = $this->language->get('text_order_payment_address');
		$this->data['entry_order_address'] = $this->language->get('entry_order_address');
		$this->data['button_edit_address'] = $this->language->get('button_edit_address');
		$this->data['entry_shipping_method'] = $this->language->get('entry_shipping_method');
		// add for edit order address end
		// add for Quotation begin
		$this->data['text_order_quote'] = $this->language->get('text_order_quote');
		$this->data['text_new_order'] = $this->language->get('text_new_order');
		$this->data['text_new_quote'] = $this->language->get('text_new_quote');
		$this->data['column_quote_id'] = $this->language->get('column_quote_id');
		$this->data['text_list_order'] = $this->language->get('text_list_order');
		$this->data['text_list_quote'] = $this->language->get('text_list_quote');
		$this->data['text_work_mode'] = '0';
		if (isset($this->request->get['work_mode'])) {
			$this->data['text_work_mode'] = $this->request->get['work_mode'];
		}
		$this->data['text_confirm_complete'] = $this->language->get('text_confirm_complete');
		$this->data['text_confirm_convert'] = $this->language->get('text_confirm_convert');
		$this->data['text_existing_quotes'] = $this->language->get('text_existing_quotes');
		$this->data['text_convert_to_order'] = $this->language->get('text_convert_to_order');
		// add for Quotation end
		// add for Quick sale begin
		$this->data['tab_product_quick_sale'] = $this->language->get('tab_product_quick_sale');
		$this->data['text_quick_sale'] = $this->language->get('text_quick_sale');
		$this->data['entry_quick_sale_name'] = $this->language->get('entry_quick_sale_name');
		$this->data['entry_quick_sale_model'] = $this->language->get('entry_quick_sale_model');
		$this->data['entry_quick_sale_price'] = $this->language->get('entry_quick_sale_price');
		$this->data['entry_quick_sale_tax'] = $this->language->get('entry_quick_sale_tax');
		$this->data['text_quick_sale_include_tax'] = $this->language->get('text_quick_sale_include_tax');
		$this->data['text_quick_sale_shipping'] = $this->language->get('text_quick_sale_shipping');
		
		$this->load->model('localisation/tax_class');
		$this->data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
		// add for Quick sale end
		// add for Browse begin
		$this->data['text_top_category_id'] = '0';
		$this->data['text_top_category_name'] = $this->language->get('text_top_category_name');
		// add for Browse end
		// add for serial no begin
		$this->data['entry_product_sn'] = $this->language->get('entry_product_sn');
		// add for serial no end
		
		$this->data['user'] = $this->user->getUserName();
		$text_week_0 = $this->language->get('text_week_0');
		$text_week_1 = $this->language->get('text_week_1');
		$text_week_2 = $this->language->get('text_week_2');
		$text_week_3 = $this->language->get('text_week_3');
		$text_week_4 = $this->language->get('text_week_4');
		$text_week_5 = $this->language->get('text_week_5');
		$text_week_6 = $this->language->get('text_week_6');
		$this->data['text_weeks'] = array($text_week_0, $text_week_1, $text_week_2, $text_week_3, $text_week_4, $text_week_5, $text_week_6);
		
		$text_month_1 = $this->language->get('text_month_1');
		$text_month_2 = $this->language->get('text_month_2');
		$text_month_3 = $this->language->get('text_month_3');
		$text_month_4 = $this->language->get('text_month_4');
		$text_month_5 = $this->language->get('text_month_5');
		$text_month_6 = $this->language->get('text_month_6');
		$text_month_7 = $this->language->get('text_month_7');
		$text_month_8 = $this->language->get('text_month_8');
		$text_month_9 = $this->language->get('text_month_9');
		$text_month_10 = $this->language->get('text_month_10');
		$text_month_11 = $this->language->get('text_month_11');
		$text_month_12 = $this->language->get('text_month_12');
		$this->data['text_months'] = array($text_month_1, $text_month_2, $text_month_3, $text_month_4, $text_month_5, $text_month_6, $text_month_7, $text_month_8, $text_month_9, $text_month_10, $text_month_11, $text_month_12);
		
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
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/pos/main', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/pos', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['payment_types'] = array();
		$this->data['text_select'] = $this->language->get('text_select');
		
		// add for Discount begin
		unset($this->session->data['pos_discount']);
		// add for Discount end
		// add for Hiding Delete begin
		$delete_excluded_groups = array();
		if ($this->config->get('enable_hide_delete') && $this->config->get('delete_excluded_groups')) {
			$delete_excluded_groups = $this->config->get('delete_excluded_groups');
		}
		$this->data['user_id'] = $this->user->getId();
		$this->load->model('user/user');
		$user = $this->model_user_user->getUser($this->user->getId());
		$user_group_id = 0;
		if ($user) {
			$user_group_id = $user['user_group_id'];
		}
		$this->data['display_delete'] = false;
		if ($this->config->get('enable_hide_delete') == null || in_array($user_group_id, $delete_excluded_groups)) {
			$this->data['display_delete'] = true;
		}
		// add for Hiding Delete end
		// add for Maximum Discount begin
		$max_discount_fixed = 0;
		if ($this->config->get($user_group_id.'_max_discount_fixed')) {
			$max_discount_fixed = $this->config->get($user_group_id.'_max_discount_fixed');
		}
		$this->data['max_discount_fixed'] = $max_discount_fixed;
		$max_discount_percentage = 0;
		if ($this->config->get($user_group_id.'_max_discount_percentage')) {
			$max_discount_percentage = $this->config->get($user_group_id.'_max_discount_percentage');
		}
		$this->data['max_discount_percentage'] = $max_discount_percentage;
		// add for Maximum Discount end
		
		if (isset($this->request->post['POS_payment_types'])) {
			$this->data['payment_types'] = $this->request->post['POS_payment_types'];
		} elseif ($this->config->get('POS_payment_types')) {
			$this->data['payment_types'] = $this->config->get('POS_payment_types');
		}
		
		$this->getOrderList();
		// add for Empty order control begin
		if (!isset($this->request->get['order_id']) && !isset($this->request->get['list'])) {
			$this->deleteEmptyOrders($this->config->get('delete_order_with_no_products'), $this->config->get('delete_order_with_inital_status'), $this->config->get('delete_order_with_deleted_status'));
		}
		// add for Empty order control end
		
		if (isset($this->request->get['order_id'])) {
			$this->getOrderProducts($this->request->get['order_id']);
			$this->data['display_order_content'] = 'block';
			$this->data['display_order_header'] = 'block';
			$this->data['display_orders'] = 'none';
		} elseif (isset($this->request->get['action']) || isset($this->request->get['page'])) {
			$this->data['display_order_content'] = 'none';
			$this->data['display_order_header'] = 'none';
			$this->data['display_orders'] = 'block';
		}
		// add for table management begin
		elseif ($this->data['enable_table_management']) {
			$this->data['display_order_content'] = 'none';
			$this->data['display_order_header'] = 'none';
			$this->data['display_orders'] = 'block';
		}
		// add for table management end
		elseif (!empty($this->data['orders'])) {
			$existingOrders = $this->data['orders'];
			$this->getOrderProducts($existingOrders[0]['order_id']);
			$this->data['display_order_content'] = 'block';
			$this->data['display_order_header'] = 'none';
			$this->data['display_orders'] = 'none';
			// add for Blank Page begin
			foreach ($this->data as $key => $value) {
				if (!(substr($key, 0, 5) == 'text_' || substr($key, 0, 7) == 'button_' ||
					substr($key, 0, 6) == 'entry_' || substr($key, 0, 7) == 'column_' ||
					$key == 'breadcrumbs' || substr($key, 0, 8) == 'display_' || substr($key, 0, 4) == 'tab_' ||
					$key == 'user' || $key == 'orders' || $key == 'token' || substr($key, 0, 6) == 'print_' ||
					$key == 'customer_countries' || $key == 'store_id' || $key == 'pagination' || $key == 'order_statuses' ||
					$key == 'quote_statuses' || $key == 'ccx_types')) {
					if (is_array($value)) {
						$this->data[$key] = array();
					} else {
						$this->data[$key] = '';
					}
				}
			}
			$this->data['text_order_ready'] = $this->language->get('text_order_ready');
			$this->data['text_order_blank'] = $this->language->get('text_order_blank');
			$this->data['totals'] = array(array('code'=>'total', 'title'=>'Total', 'text'=>$this->currency->formatFront(0, $this->config->get('config_currency')), 'value'=>0));
			// add for Blank Page end 
			// add for Quotation begin
			$this->data['text_quote_ready'] = $this->language->get('text_quote_ready');
			// add for Quotation end
		} else {
			$this->data['display_order_content'] = 'none';
			$this->data['display_order_header'] = 'none';
			$this->data['display_orders'] = 'block';
		}
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$this->data['store_url'] = HTTPS_CATALOG;
		} else {
			$this->data['store_url'] = HTTP_CATALOG;
		}

		$this->data['full_screen_mode'] = 1;
		// add for till control begin
		if ($this->config->get('enable_till_control')) {
			$this->data['enable_till_control'] = $this->config->get('enable_till_control');
		}
		if ($this->config->get('till_control_key')) {
			$this->data['till_control_key'] = $this->config->get('till_control_key');
		}
		if ($this->config->get('enable_till_full_payment')) {
			$this->data['enable_till_full_payment'] = $this->config->get('enable_till_full_payment');
		}
		// add for till control end
		
		$default_country_id = $this->config->get('config_country_id');
		$default_zone_id = $this->config->get('config_zone_id');
		$this->data['shipping_country_id'] = $default_country_id;
		$this->data['shipping_zone_id'] = $default_zone_id;
		$this->data['payment_country_id'] = $default_country_id;
		$this->data['payment_zone_id'] = $default_zone_id;
		$this->data['currency_code'] = $this->config->get('config_currency');
		$this->data['currency_value'] = '1.0';
		$this->data['store_id'] = $this->getStoreId();
		$this->data['customer_id'] = 0;
		$this->data['customer_group_id'] = 1;

		$this->template = 'pos/main.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}
	
	private function search_for_update() {
		$post_string = 'version=' . POS_VERSION . '&domain_name=' . $_SERVER['SERVER_NAME'];
		$url = 'http://www.pos4opencart.com/shop/pos_update.php';
		$parts = parse_url($url);

		$fp = @fsockopen($parts['host'],	isset($parts['port'])?$parts['port']:80, $errno, $errstr, 30);

		if ($fp) {
			$out = "POST " . $parts['path']. " HTTP/1.1\r\n";
			$out.= "Host: " . $parts['host']. "\r\n";
			$out.= "Content-Type: application/x-www-form-urlencoded\r\n";
			$out.= "Content-Length: ". strlen($post_string) . "\r\n";
			$out.= "Connection: Close\r\n\r\n";
			$out.= $post_string;

			fwrite($fp, $out);
			fclose($fp);
		}
	}
	
	public function getOrderList() {
		$this->language->load('sale/order');
		$this->load->model('sale/order');
		
		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = null;
		}

		if (isset($this->request->get['filter_customer'])) {
			$filter_customer = $this->request->get['filter_customer'];
		} else {
			$filter_customer = null;
		}

		if (isset($this->request->get['filter_order_status_id'])) {
			$filter_order_status_id = $this->request->get['filter_order_status_id'];
		} else {
			$filter_order_status_id = null;
		}
		
		// add for Quotation begin
		$filter_quote_status_id = null;
		if (isset($this->request->get['work_mode']) && $this->request->get['work_mode'] == '2') {
			$filter_quote = 1;
			if (isset($this->request->get['filter_quote_status_id'])) {
				$filter_quote_status_id = $this->request->get['filter_quote_status_id'];
			}
			// if filter by quote status id, the filter of order status id will not be used
			$filter_order_status_id = null;
		} else {
			$filter_quote = null;
		}
		// add for Quotation end
		
		// add for table management begin
		if (isset($this->request->get['filter_table_id'])) {
			$filter_table_id = $this->request->get['filter_table_id'];
		} else {
			$filter_table_id = null;
		}
		// add for table management end
		
		if (isset($this->request->get['filter_total'])) {
			$filter_total = $this->request->get['filter_total'];
		} else {
			$filter_total = null;
		}
		
		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = null;
		}
		
		if (isset($this->request->get['filter_date_modified'])) {
			$filter_date_modified = $this->request->get['filter_date_modified'];
		} else {
			$filter_date_modified = null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'o.order_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
				
		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}
		
		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		// add for Quotation begin
		if ($filter_quote) {
			if (isset($this->request->get['filter_quote_status_id'])) {
				$url .= '&filter_quote_status_id=' . $this->request->get['filter_quote_status_id'];
			}
		} else {
		// add for Quotation end
		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
		// add for Quotation begin
		}
		// add for Quotation end
		
		// add for table management begin
		if (isset($this->request->get['filter_table_id'])) {
			$url .= '&filter_table_id=' . $this->request->get['filter_table_id'];
		}
		// add for table management end
		
		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}
					
		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}
		
		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$this->data['orders'] = array();

		$data = array(
			// add for Quotation begin
			'filter_quote'           => $filter_quote,
			'filter_quote_status_id' => $filter_quote_status_id,
			// add for Quotation end
			// add for table management begin
			'filter_table_id'        => $filter_table_id,
			// add for table management end
			'filter_order_id'        => $filter_order_id,
			'filter_customer'	     => $filter_customer,
			'filter_order_status_id' => $filter_order_status_id,
			'filter_total'           => $filter_total,
			'filter_date_added'      => $filter_date_added,
			'filter_date_modified'   => $filter_date_modified,
			'sort'                   => $sort,
			'order'                  => $order,
			'start'                  => ($page - 1) * 14,
			'limit'                  => 14
		);

		$this->load->model('pos/pos');
		// add for Hiding Order Status begin
		$order_hiding_status = array();
		if ($this->config->get('order_hiding_status')) {
			$order_hiding_status = $this->config->get('order_hiding_status');
		}
		$data['order_hiding_status'] = $order_hiding_status;
		// add for user can only manage his/her own orders begin
		$data['filter_user_id'] = $this->user->getId();
		// add for user can only manage his/her own orders end
		if (!empty($order_hiding_status)) {
			$order_total = $this->model_pos_pos->getTotalOrders($data);

			$results = $this->model_pos_pos->getOrders($data);
		} else {
		// add for Hiding Order Status end
		// update for user can only manage his/her own orders begin, table management
		// $order_total = $this->model_sale_order->getTotalOrders($data);
		// $results = $this->model_sale_order->getOrders($data);
		$data['filter_user_id'] = $this->user->getId();
		$order_total = $this->model_pos_pos->getTotalOrders($data);
		$results = $this->model_pos_pos->getOrders($data);
		// update for user can only manage his/her own orders end, table management
		// add for Hiding Order Status begin
		}
		// add for Hiding Order Status end

    	foreach ($results as $result) {
			$action = array();
						
			$action[] = array(
				'text' => $this->language->get('text_select'),
				'href' => $this->url->link('module/pos/main', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'] . $url, 'SSL')
			);
			
			$this->data['orders'][] = array(
				'order_id'      => $result['order_id'],
				'customer'      => $result['customer'],
				'status'        => $result['status'],
				'total'         => $this->currency->formatFront($result['total'], $result['currency_code'], $result['currency_value']),
				'date_added'    => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_modified' => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
				'selected'      => isset($this->request->post['selected']) && in_array($result['order_id'], $this->request->post['selected']),
				'action'        => $action
			);
		}

		$this->data['text_no_results'] = $this->language->get('text_no_results');
		$this->data['text_missing'] = $this->language->get('text_missing');
		$this->data['text_wait'] = $this->language->get('text_wait');

		$this->data['column_order_id'] = $this->language->get('column_order_id');
    	$this->data['column_customer'] = $this->language->get('column_customer');
		$this->data['column_status'] = $this->language->get('column_status');
		$this->data['column_total'] = $this->language->get('column_total');
		$this->data['column_date_added'] = $this->language->get('column_date_added');
		$this->data['column_date_modified'] = $this->language->get('column_date_modified');
		$this->data['column_action'] = $this->language->get('column_action');

		$this->data['button_invoice'] = $this->language->get('button_invoice');
		$this->data['button_insert'] = $this->language->get('button_insert');
		$this->data['button_delete'] = $this->language->get('button_delete');
		$this->data['button_filter'] = $this->language->get('button_filter');
		$this->data['entry_option'] = $this->language->get('entry_option');

		$this->data['token'] = $this->session->data['token'];
		
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}
		
		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		// add for Quotation begin
		if ($filter_quote) {
			if (isset($this->request->get['filter_quote_status_id'])) {
				$url .= '&filter_quote_status_id=' . $this->request->get['filter_quote_status_id'];
			}
		} else {
		// add for Quotation end
		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
		// add for Quotation begin
		}
		// add for Quotation end

		// add for table management begin
		if (isset($this->request->get['filter_table_id'])) {
			$url .= '&filter_table_id=' . $this->request->get['filter_table_id'];
		}
		// add for table management end
		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}
					
		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}
		
		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$this->data['sort_order'] = $this->url->link('module/pos/main', 'token=' . $this->session->data['token'] . '&sort=o.order_id' . $url, 'SSL');
		$this->data['sort_customer'] = $this->url->link('module/pos/main', 'token=' . $this->session->data['token'] . '&sort=customer' . $url, 'SSL');
		$this->data['sort_status'] = $this->url->link('module/pos/main', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');
		$this->data['sort_total'] = $this->url->link('module/pos/main', 'token=' . $this->session->data['token'] . '&sort=o.total' . $url, 'SSL');
		$this->data['sort_date_added'] = $this->url->link('module/pos/main', 'token=' . $this->session->data['token'] . '&sort=o.date_added' . $url, 'SSL');
		$this->data['sort_date_modified'] = $this->url->link('module/pos/main', 'token=' . $this->session->data['token'] . '&sort=o.date_modified' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}
		
		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
		}

		// add for Quotation begin
		if ($filter_quote) {
			if (isset($this->request->get['filter_quote_status_id'])) {
				$url .= '&filter_quote_status_id=' . $this->request->get['filter_quote_status_id'];
			}
		} else {
		// add for Quotation end
		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
		// add for Quotation begin
		}
		// add for Quotation end
		
		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}
					
		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}
		
		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = 14;
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('module/pos/main', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$this->data['pagination'] = $pagination->render();

		$this->data['filter_order_id'] = $filter_order_id;
		$this->data['filter_customer'] = $filter_customer;
		$this->data['filter_order_status_id'] = $filter_order_status_id;
		// add for table management begin
		$this->data['filter_table_id'] = $filter_table_id;
		// add for table management end
		// add for Quotation begin
		if ($filter_quote) {
			$this->data['filter_quote_status_id'] = $filter_quote_status_id;
		}
		// add for Quotation end
		$this->data['filter_total'] = $filter_total;
		$this->data['filter_date_added'] = $filter_date_added;
		$this->data['filter_date_modified'] = $filter_date_modified;

		$this->load->model('localisation/order_status');

    	$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		// add for Hiding Order Status begin
		foreach ($this->data['order_statuses'] as $key => $value) {
			if (in_array($key, $order_hiding_status)) {
				unset($this->data['order_statuses'][$key]);
			}
		}
		// add for Hiding Order Status end
		// add for Complete Status begin
		$complete_status = $this->config->get('complete_status');
		if ($complete_status) {
			$this->cache->delete('order_status');
			$has_complete_status = false;
			$complete_status_id = $this->model_pos_pos->addCompleteStatus($complete_status);
			foreach ($this->data['order_statuses'] as $key => $value) {
				if ($value['order_status_id'] == $complete_status_id) {
					$has_complete_status = true;
					break;
				}
			}
			if (!$has_complete_status) {
				$this->data['order_statuses'][] = array('order_status_id'=>$complete_status_id, 'name'=>$complete_status);
			}
			$this->data['text_complete_status_id'] = $complete_status_id;
		}
		// add for Complete Status end
		// add for Quotation begin
		if ($filter_quote) {
			$this->data['quote_statuses'] = $this->model_pos_pos->getQuoteStatuses();
		}
		// add for Quotation end

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
  	}
	
	private function getOrderIdText($order_id) {
		$order_id_text = ''.$order_id;
		$order_id_len = strlen($order_id_text);
		if ($order_id_len < 7) {
			for ($i = 0; $i < 7-$order_id_len; $i++) {
				$order_id_text = '0'.$order_id_text;
			}
		}
		return $order_id_text;
	}
	
	private function getOrderProducts($order_id) {
		// unset the shipping method before load it again
		unset($this->session->data['shipping_method']);
		
		$this->load->model('sale/order');
		$this->load->model('pos/pos');

		$order_info = $this->model_sale_order->getOrder($order_id);

		$this->language->load('sale/order');

		$this->data['text_order_id'] = $this->language->get('text_order_id');
		$this->data['text_invoice_no'] = $this->language->get('text_invoice_no');
		$this->data['text_invoice_date'] = $this->language->get('text_invoice_date');
		$this->data['text_store_name'] = $this->language->get('text_store_name');
		$this->data['text_store_url'] = $this->language->get('text_store_url');		
		$this->data['text_default'] = $this->language->get('text_default');
		$this->data['text_customer'] = $this->language->get('text_customer');
		$this->data['text_customer_group'] = $this->language->get('text_customer_group');
		$this->data['text_total'] = $this->language->get('text_total');
		$this->data['text_reward'] = $this->language->get('text_reward');		
		$this->data['text_order_status'] = $this->language->get('text_order_status');
		$this->data['text_comment'] = $this->language->get('text_comment');
		$this->data['text_date_added'] = $this->language->get('text_date_added');
		$this->data['text_date_modified'] = $this->language->get('text_date_modified');			
    	$this->data['entry_firstname'] = $this->language->get('entry_firstname');
    	$this->data['entry_lastname'] = $this->language->get('entry_lastname');
    	$this->data['entry_email'] = $this->language->get('entry_email');
    	$this->data['entry_telephone'] = $this->language->get('entry_telephone');
    	$this->data['entry_fax'] = $this->language->get('entry_fax');
		$this->data['entry_company'] = $this->language->get('entry_company');
		$this->data['entry_company_id'] = $this->language->get('entry_company_id');
		$this->data['entry_tax_id'] = $this->language->get('entry_tax_id');
		$this->data['entry_address_1'] = $this->language->get('entry_address_1');
		$this->data['entry_address_2'] = $this->language->get('entry_address_2');
		$this->data['entry_city'] = $this->language->get('entry_city');
		$this->data['entry_postcode'] = $this->language->get('entry_postcode');
		$this->data['entry_zone'] = $this->language->get('entry_zone');
		$this->data['entry_country'] = $this->language->get('entry_country');
		$this->data['text_shipping_method'] = $this->language->get('text_shipping_method');
		$this->data['text_payment_method'] = $this->language->get('text_payment_method');	
		$this->data['text_download'] = $this->language->get('text_download');
		$this->data['text_wait'] = $this->language->get('text_wait');
		$this->data['text_generate'] = $this->language->get('text_generate');
		$this->data['text_voucher'] = $this->language->get('text_voucher');
		$this->data['text_add_product_prompt'] = $this->language->get('text_add_product_prompt');
		$this->data['text_select'] = $this->language->get('text_select');
		$this->data['text_no_product'] = $this->language->get('text_no_product');
		$this->data['text_order_ready'] = $this->language->get('text_order_ready');
		$this->data['text_none'] = $this->language->get('text_none');
						
		$this->data['column_product'] = $this->language->get('column_product');
		$this->data['column_model'] = $this->language->get('column_model');
		$this->data['column_qty'] = $this->language->get('column_qty');
		$this->data['column_price'] = $this->language->get('column_price');
		$this->data['column_total'] = $this->language->get('column_total');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_add_voucher'] = $this->language->get('button_add_voucher');
		$this->data['entry_to_name'] = $this->language->get('entry_to_name');
		$this->data['entry_to_email'] = $this->language->get('entry_to_email');
		$this->data['entry_from_name'] = $this->language->get('entry_from_name');
		$this->data['entry_from_email'] = $this->language->get('entry_from_email');
		$this->data['entry_theme'] = $this->language->get('entry_theme');	
		$this->data['entry_message'] = $this->language->get('entry_message');
		$this->data['entry_amount'] = $this->language->get('entry_amount');
		$this->data['text_product'] = $this->language->get('text_product');
		$this->data['entry_product'] = $this->language->get('entry_product');
		// add for SKU begin
		$this->data['entry_sku'] = $this->language->get('entry_sku');
		$this->data['text_no_product_for_sku'] = $this->language->get('text_no_product_for_sku');
		// add for SKU end
		// add for UPC begin
		$this->data['entry_upc'] = $this->language->get('entry_upc');
		$this->data['text_no_product_for_upc'] = $this->language->get('text_no_product_for_upc');
		// add for UPC end
		// add for MPN begin
		$this->data['entry_mpn'] = $this->language->get('entry_mpn');
		$this->data['text_no_product_for_mpn'] = $this->language->get('text_no_product_for_mpn');
		// add for MPN end
		// add for Model begin
		$this->data['entry_model'] = $this->language->get('entry_model');
		// add for Model end
		// add for UPC/SKU/MPN begin
		$this->data['config_scan_type'] = $this->config->get('config_scan_type');
		// add for UPC/SKU/MPN end
		$this->data['entry_option'] = $this->language->get('entry_option');
		$this->data['entry_quantity'] = $this->language->get('entry_quantity');
		$this->data['button_add_product'] = $this->language->get('button_add_product');
		$this->data['button_upload'] = $this->language->get('button_upload');
		// add for Quotation begin
		$this->data['column_quote_id'] = $this->language->get('column_quote_id');
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "'");
		if ($order_query->row && isset($order_query->row['quote_status_id']) && $order_query->row['quote_status_id'] > 0) {
			$this->data['column_order_id'] = $this->language->get('column_quote_id');
			$this->data['quote_status'] = $this->model_pos_pos->getQuoteStatus($order_query->row['quote_status_id']);
			$this->data['quote_statuses'] = $this->model_pos_pos->getQuoteStatuses();
		}
		// add for Quotation end
		// add for Browse begin
		$this->data['browse_items'] = $this->getCategoryItems(0, $order_info['currency_code'], $order_info['currency_value']);
		// add for Brose end
		// add for Cash type begin
		$cash_types = $this->config->get('cash_types');
		$cash_notes = array();
		$cash_coins = array();
		$text_note = $this->language->get('text_cash_type_note');
		if (!empty($cash_types)) {
			foreach ($cash_types as $cash_type) {
				if ($cash_type['type'] == $text_note) {
					array_push($cash_notes, $cash_type);
				} else {
					array_push($cash_coins, $cash_type);
				}
			}
			$sort_order = array();
			foreach ($cash_notes as $cash_note) {
				$sort_order[] = $cash_note['value'];
			}
			array_multisort($sort_order, SORT_DESC, $cash_notes);
			$sort_order = array();
			foreach ($cash_coins as $cash_coin) {
				$sort_order[] = $cash_coin['value'];
			}
			array_multisort($sort_order, SORT_DESC, $cash_coins);
			$cash_types = array($cash_notes, $cash_coins);
		}
		$this->data['cash_types'] = $cash_types;
		// add for Cash type end

		$this->data['token'] = $this->session->data['token'];

		$this->data['order_id'] = $order_id;
		$this->data['order_id_text'] = $this->getOrderIdText($order_id);
		
		$this->data['store_id'] = $order_info['store_id'];
		$this->data['invoice'] = $this->url->link('sale/order/invoice', 'token=' . $this->session->data['token'] . '&order_id=' . (int)$order_id, 'SSL');
		
		if ($order_info['invoice_no']) {
			$this->data['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];
		} else {
			$this->data['invoice_no'] = '';
		}
		
		$this->data['store_name'] = $order_info['store_name'];
		$this->data['store_url'] = $order_info['store_url'];
		$this->data['firstname'] = $order_info['firstname'];
		$this->data['lastname'] = $order_info['lastname'];
		
		if ($order_info['customer_id'] > 0) {
			$this->data['customer'] = $order_info['customer'];
			$this->data['customer_id'] = $order_info['customer_id'];
		} else {
			$this->data['customer'] = $order_info['firstname'].' '.$order_info['lastname'];
			$this->data['customer_id'] = 0;
		}
		$this->getCustomer($order_info['customer_id']);

		$this->load->model('sale/customer_group');

		$customer_group_info = $this->model_sale_customer_group->getCustomerGroup($order_info['customer_group_id']);
		$this->data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();
		$this->data['customer_group_id'] = $order_info['customer_group_id'];

		if ($customer_group_info) {
			$this->data['customer_group'] = $customer_group_info['name'];
		} else {
			$this->data['customer_group'] = '';
		}

		$this->data['email'] = $order_info['email'];
		$this->data['telephone'] = $order_info['telephone'];
		$this->data['fax'] = $order_info['fax'];
		$this->data['comment'] = $order_info['comment'];
		$this->data['total'] = $this->currency->formatFront($order_info['total'], $order_info['currency_code'], $order_info['currency_value']);
		
		if ($order_info['total'] < 0) {
			$this->data['credit'] = $order_info['total'];
		} else {
			$this->data['credit'] = 0;
		}
		// add for table management begin
		$this->data['order_table_id'] = $this->model_pos_pos->getOrderTableId($order_id);
		// add for table management end
		
		$this->load->model('sale/customer');
					
		$this->data['credit_total'] = $this->model_sale_customer->getTotalTransactionsByOrderId($order_id); 
		
		$this->data['reward'] = $order_info['reward'];
					
		$this->data['reward_total'] = $this->model_sale_customer->getTotalCustomerRewardsByOrderId($order_id);

		$this->data['affiliate_firstname'] = $order_info['affiliate_firstname'];
		$this->data['affiliate_lastname'] = $order_info['affiliate_lastname'];
		
		if ($order_info['affiliate_id']) {
			$this->data['affiliate'] = $this->url->link('sale/affiliate/update', 'token=' . $this->session->data['token'] . '&affiliate_id=' . $order_info['affiliate_id'], 'SSL');
			$this->data['affiliate_id'] = $order_info['affiliate_id'];
		} else {
			$this->data['affiliate'] = '';
			$this->data['affiliate_id'] = 0;
		}
		
		$this->data['commission'] = $this->currency->formatFront($order_info['commission'], $order_info['currency_code'], $order_info['currency_value']);
					
		$this->load->model('sale/affiliate');
		
		$this->data['commission_total'] = $this->model_sale_affiliate->getTotalTransactionsByOrderId($order_id); 

		$this->load->model('localisation/order_status');

		$order_status_info = $this->model_localisation_order_status->getOrderStatus($order_info['order_status_id']);

		if ($order_status_info) {
			$this->data['order_status'] = $order_status_info['name'];
		} else {
			$this->data['order_status'] = '';
		}
		
		$this->data['ip'] = $order_info['ip'];
		$this->data['forwarded_ip'] = $order_info['forwarded_ip'];
		$this->data['user_agent'] = $order_info['user_agent'];
		$this->data['accept_language'] = $order_info['accept_language'];
		$this->data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added'])) . ' '  .date($this->language->get('time_format'), strtotime($order_info['date_added']));
		$this->data['date_modified'] = date($this->language->get('date_format_short'), strtotime($order_info['date_modified'])) . ' '  .date($this->language->get('time_format'), strtotime($order_info['date_modified']));		
		$this->data['payment_firstname'] = $order_info['payment_firstname'];
		$this->data['payment_lastname'] = $order_info['payment_lastname'];
		$this->data['payment_company'] = $order_info['payment_company'];
		$this->data['payment_company_id'] = $order_info['payment_company_id'];
		$this->data['payment_tax_id'] = $order_info['payment_tax_id'];
		$this->data['payment_address_1'] = $order_info['payment_address_1'];
		$this->data['payment_address_2'] = $order_info['payment_address_2'];
		$this->data['payment_city'] = $order_info['payment_city'];
		$this->data['payment_postcode'] = $order_info['payment_postcode'];
		$this->data['payment_zone'] = $order_info['payment_zone'];
		$this->data['payment_zone_code'] = $order_info['payment_zone_code'];
		$this->data['payment_country'] = $order_info['payment_country'];			
		$this->data['payment_country_id'] = $order_info['payment_country_id'];			
		$this->data['payment_zone_id'] = $order_info['payment_zone_id'];			
		$this->data['shipping_firstname'] = $order_info['shipping_firstname'];
		$this->data['shipping_lastname'] = $order_info['shipping_lastname'];
		$this->data['shipping_company'] = $order_info['shipping_company'];
		$this->data['shipping_address_1'] = $order_info['shipping_address_1'];
		$this->data['shipping_address_2'] = $order_info['shipping_address_2'];
		$this->data['shipping_city'] = $order_info['shipping_city'];
		$this->data['shipping_postcode'] = $order_info['shipping_postcode'];
		$this->data['shipping_zone'] = $order_info['shipping_zone'];
		$this->data['shipping_zone_code'] = $order_info['shipping_zone_code'];
		$this->data['shipping_country'] = $order_info['shipping_country'];
		$this->data['shipping_country_id'] = $order_info['shipping_country_id'];
		$this->data['shipping_zone_id'] = $order_info['shipping_zone_id'];
		$this->data['shipping_code'] = $order_info['shipping_code'];
		$this->data['shipping_method'] = $order_info['shipping_method'];
		$this->data['payment_method'] = $order_info['payment_method'];
		$this->data['payment_code'] = $order_info['payment_code'];
		
		$this->data['products'] = array();

		$raw_products = $this->model_sale_order->getOrderProducts($order_id);
		// there is a bug in opencart code when write order back to db and read out, it maybe in different order
		// change the order in code to make constanct result by sorting by product id
		$products = array();
		while (count($raw_products) > 0) {
			$raw_index = 0;
			while (!isset($raw_products[$raw_index])) {
				$raw_index++;
			}
			$raw_product_min = $raw_products[$raw_index];

			$keys = array_keys($raw_products);
			foreach ($keys as $key) {
				if ($raw_product_min['order_product_id'] > $raw_products[$key]['order_product_id']) {
					$raw_index = $key;
					$raw_product_min = $raw_products[$key];
				}
			}
			array_push($products, $raw_product_min);
			unset($raw_products[$raw_index]);
		}

		$items_in_cart = 0;
		foreach ($products as $product) {
			$option_data = array();

			$options = $this->model_sale_order->getOrderOptions($order_id, $product['order_product_id']);
			$order_download = $this->model_sale_order->getOrderDownloads($order_id, $product['order_product_id']);
			// add for Weight based price begin
			$weight = 1;
			foreach ($options as $option) {
				if ((int)$option['product_option_id'] == -1) {
					$weight = (float)$option['value'];
					break;
				}
			}
			// add for Weight based price end
			// add for serial no begin
			$sns = $this->model_pos_pos->getSoldProductSN($product['order_product_id']);
			// add for serial no end

			$this->data['products'][] = array(
				'order_product_id' => $product['order_product_id'],
				'product_id'       => $product['product_id'],
				'name'    	 	   => html_entity_decode($product['name']),
				'model'    		   => $product['model'],
				'option'   		   => $options,
				'download'		   => $order_download,
				'quantity'		   => $product['quantity'],
				'price'			   => $product['price'],
				'price_text'	   => $this->currency->formatFront($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
				// add for (update) Weight based price begin
				// 'total'			   => $product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0),
				// 'total_text'       => $this->currency->formatFront($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']),
				'total'			   => $product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity'] * $weight) : 0),
				'total_text'       => $this->currency->formatFront($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity'] * $weight) : 0), $order_info['currency_code'], $order_info['currency_value']),
				// add for (update) Weight based price end
				// add for serail no begin
				'sns'              => $sns,
				// add for serial no end
				'tax'			   => $product['tax'],
				'reward'		   => $product['reward'],
				'href'     		   => $this->url->link('catalog/product/update', 'token=' . $this->session->data['token'] . '&product_id=' . $product['product_id'], 'SSL'),
				'selected'		   => isset($this->request->post['selected']) && in_array($product['product_id'], $this->request->post['selected'])
			);
			
			$items_in_cart += $product['quantity'];
		}
		$this->data['items_in_cart'] = $items_in_cart;
		$this->data['currency_code'] = $order_info['currency_code'];
		$this->data['currency_value'] = $order_info['currency_value'];
		$this->data['currency_symbol'] = $this->currency->getSymbolLeft($order_info['currency_code']);
		if ($this->data['currency_symbol'] == '') {
			$this->data['currency_symbol'] = $this->currency->getSymbolRight($order_info['currency_code']);
		}
	
		$this->data['order_vouchers'] = $this->model_sale_order->getOrderVouchers($order_id);
		$this->load->model('sale/voucher_theme');
		$this->data['voucher_themes'] = $this->model_sale_voucher_theme->getVoucherThemes();
		$this->load->model('setting/store');
		$this->data['stores'] = $this->model_setting_store->getStores();
	
		$this->data['totals'] = $this->model_sale_order->getOrderTotals($order_id);
		// add for Discount begin
		foreach ($this->data['totals'] as $order_total_data) {
			if ($order_total_data['code'] == 'pos_discount' || $order_total_data['code'] == 'pos_discount_subtotal' || $order_total_data['code'] == 'pos_discount_total') {
				// set the value into session
				$discount = array (
					'order_id' => $order_id,
					'code' => $order_total_data['code'],
					'title' => $order_total_data['title'],
					'text' => $order_total_data['text'],
					'value' => $order_total_data['value']
				);
				$this->session->data['pos_discount'] = $discount;
				$this->data['discount_type'] = 'amount';
				if ($order_total_data['code'] == 'pos_discount_subtotal') {
					$this->data['discount_type'] = 'percentage';
					$this->data['discount_total_type'] = 'subtotal';
				} else if ($order_total_data['code'] == 'pos_discount_total') {
					$this->data['discount_type'] = 'percentage';
					$this->data['discount_total_type'] = 'total';
				}
				$this->data['discount_value'] = $order_total_data['value'];
				if ($order_total_data['code'] == 'pos_discount_subtotal' || $order_total_data['code'] == 'pos_discount_total') {
					$index1 = strpos($order_total_data['title'], '(');
					$index2 = strpos($order_total_data['title'], ')');
					if ($index1 !== false && $index2 !== false && $index2 > $index1+2) {
						$this->data['discount_value'] = substr($order_total_data['title'], $index1+1, $index2-$index1-3);
					}
				}
			} elseif ($order_total_data['code'] == 'sub_total') {
				$this->data['total_subtotal_text'] = number_format($order_total_data['value'], 2);
				$this->data['total_subtotal_value'] = $order_total_data['value'];
			} elseif ($order_total_data['code'] == 'total') {
				$this->data['total_total_value'] = $order_total_data['value'];
			}
		}
		if (!isset($this->data['discount_type'])) {
			$this->data['discount_type'] = 'amount';
			$this->data['discount_value'] = 0;
		}
		if (isset($discount)) {
			// recalculate the total used for calculating the discount again
			$this->data['total_total_value'] = $this->data['total_total_value'] - $discount['value'];
		}
		if (isset($this->data['total_total_value'])) {
			$this->data['total_total_text'] = number_format($this->data['total_total_value'], 2);
		}
		// add for Discount end
		// instead of using the last object in the array, use the total code
		$totalPaymentAmount = 0;
		foreach ($this->data['totals'] as $order_total_data) {
			if ($order_total_data['code'] == 'total') {
				$totalPaymentAmount = $order_total_data['value'];
				if ($order_info['currency_value']) $totalPaymentAmount = (float)$totalPaymentAmount*$order_info['currency_value'];
				break;
			}
		}

		$totalPaid = 0;
		$order_payments = $this->model_pos_pos->retrieveOrderPayments($order_id);
		if ($order_payments) {
			// reverse the order
			$order_payments = array_reverse($order_payments);
			foreach ($order_payments as $order_payment) {
				$totalPaid += $order_payment['tendered_amount'];
				$this->data['order_payments'][] = array (
					'order_payment_id' => $order_payment['order_payment_id'],
					'payment_type'     => $order_payment['payment_type'],
					'tendered_amount'  => $this->currency->formatFront($order_payment['tendered_amount'], $order_info['currency_code'], 1),
					'payment_note'     => $order_payment['payment_note']
				);
			}
		}

		$this->data['payment_due_amount'] = $totalPaymentAmount - $totalPaid;
		$this->data['payment_change'] = 0;
		if ($this->data['payment_due_amount'] <  0) {
			$this->data['payment_change'] = 0 - $this->data['payment_due_amount'];
			$this->data['payment_due_amount'] = 0;
		}
		$this->data['payment_due_amount_text'] = $this->currency->formatFront($this->data['payment_due_amount'], $order_info['currency_code'], 1);
		$this->data['payment_change_text'] = $this->currency->formatFront($this->data['payment_change'], $order_info['currency_code'], 1);
		$this->data['downloads'] = array();

		foreach ($products as $product) {
			$results = $this->model_sale_order->getOrderDownloads($order_id, $product['order_product_id']);

			foreach ($results as $result) {
				$this->data['downloads'][] = array(
					'name'      => $result['name'],
					'filename'  => $result['mask'],
					'remaining' => $result['remaining']
				);
			}
		}
		$this->document->addScript('view/javascript/jquery/ajaxupload.js');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		// add for Empty order control begin
		if ($this->config->get('initial_status_id') && $order_info['order_status_id'] != (int)$this->config->get('initial_status_id')) {
			foreach ($this->data['order_statuses'] as $key => $value) {
				if ($value['order_status_id'] == $this->config->get('initial_status_id')) {
					unset($this->data['order_statuses'][$key]);
					break;
				}
			}
		}
		// add for Empty order control end

		$this->data['order_status_id'] = $order_info['order_status_id'];
	}
	
	public function getProductDetails() {
		$product_id = $this->request->get['product_id'];
		$this->language->load('catalog/product');

		if ('' == $product_id) {
			return;
		}
		
		$this->load->model('catalog/product');
		
    	$product_info = $this->model_catalog_product->getProduct($product_id);
		if (empty($product_info)) return;
 
    	$this->data['text_enabled'] = $this->language->get('text_enabled');
    	$this->data['text_disabled'] = $this->language->get('text_disabled');
    	$this->data['text_none'] = $this->language->get('text_none');
    	$this->data['text_yes'] = $this->language->get('text_yes');
    	$this->data['text_no'] = $this->language->get('text_no');
		$this->data['text_plus'] = $this->language->get('text_plus');
		$this->data['text_minus'] = $this->language->get('text_minus');
		$this->data['text_default'] = $this->language->get('text_default');
		$this->data['text_image_manager'] = $this->language->get('text_image_manager');
		$this->data['text_browse'] = $this->language->get('text_browse');
		$this->data['text_clear'] = $this->language->get('text_clear');
		$this->data['text_option'] = $this->language->get('text_option');
		$this->data['text_option_value'] = $this->language->get('text_option_value');
		$this->data['text_select'] = $this->language->get('text_select');
		$this->data['text_percent'] = $this->language->get('text_percent');
		$this->data['text_amount'] = $this->language->get('text_amount');

		$this->data['entry_name'] = $this->language->get('entry_name');
		$this->data['entry_meta_description'] = $this->language->get('entry_meta_description');
		$this->data['entry_meta_keyword'] = $this->language->get('entry_meta_keyword');
		$this->data['entry_description'] = $this->language->get('entry_description');
		$this->data['entry_store'] = $this->language->get('entry_store');
		$this->data['entry_keyword'] = $this->language->get('entry_keyword');
    	$this->data['entry_model'] = $this->language->get('entry_model');
		$this->data['entry_sku'] = $this->language->get('entry_sku');
		$this->data['entry_upc'] = $this->language->get('entry_upc');
		$this->data['entry_ean'] = $this->language->get('entry_ean');
		$this->data['entry_jan'] = $this->language->get('entry_jan');
		$this->data['entry_isbn'] = $this->language->get('entry_isbn');
		$this->data['entry_mpn'] = $this->language->get('entry_mpn');
		$this->data['entry_location'] = $this->language->get('entry_location');
		$this->data['entry_minimum'] = $this->language->get('entry_minimum');
		$this->data['entry_manufacturer'] = $this->language->get('entry_manufacturer');
    	$this->data['entry_shipping'] = $this->language->get('entry_shipping');
    	$this->data['entry_date_available'] = $this->language->get('entry_date_available');
    	$this->data['entry_quantity'] = $this->language->get('entry_quantity');
		$this->data['entry_stock_status'] = $this->language->get('entry_stock_status');
    	$this->data['entry_price'] = $this->language->get('entry_price');
		$this->data['entry_tax_class'] = $this->language->get('entry_tax_class');
		$this->data['entry_points'] = $this->language->get('entry_points');
		$this->data['entry_option_points'] = $this->language->get('entry_option_points');
		$this->data['entry_subtract'] = $this->language->get('entry_subtract');
    	$this->data['entry_weight_class'] = $this->language->get('entry_weight_class');
    	$this->data['entry_weight'] = $this->language->get('entry_weight');
		$this->data['entry_dimension'] = $this->language->get('entry_dimension');
		$this->data['entry_length'] = $this->language->get('entry_length');
    	$this->data['entry_image'] = $this->language->get('entry_image');
    	$this->data['entry_download'] = $this->language->get('entry_download');
    	$this->data['entry_category'] = $this->language->get('entry_category');
		$this->data['entry_filter'] = $this->language->get('entry_filter');
		$this->data['entry_related'] = $this->language->get('entry_related');
		$this->data['entry_attribute'] = $this->language->get('entry_attribute');
		$this->data['entry_text'] = $this->language->get('entry_text');
		$this->data['entry_option'] = $this->language->get('entry_option');
		$this->data['entry_option_value'] = $this->language->get('entry_option_value');
		$this->data['entry_required'] = $this->language->get('entry_required');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_customer_group'] = $this->language->get('entry_customer_group');
		$this->data['entry_date_start'] = $this->language->get('entry_date_start');
		$this->data['entry_date_end'] = $this->language->get('entry_date_end');
		$this->data['entry_priority'] = $this->language->get('entry_priority');
		$this->data['entry_tag'] = $this->language->get('entry_tag');
		$this->data['entry_customer_group'] = $this->language->get('entry_customer_group');
		$this->data['entry_reward'] = $this->language->get('entry_reward');
		$this->data['entry_layout'] = $this->language->get('entry_layout');
		
		$this->language->load('module/pos');
		$this->data['column_attr_name'] = $this->language->get('column_attr_name');
		$this->data['column_attr_value'] = $this->language->get('column_attr_value');
		$this->data['entry_thumb'] = $this->language->get('entry_thumb');
		
		$this->data['tab_option'] = $this->language->get('tab_option');
				
		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}

		$this->data['token'] = $this->session->data['token'];

		$this->data['name'] = '';
		$this->data['description'] = '';
		$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();
		$descriptions = $this->model_catalog_product->getProductDescriptions($product_id);
		foreach ($languages as $language) {
			if ($language['code'] == $this->language->get('code')) {
				$this->data['name'] = $descriptions[$language['language_id']]['name'];
				$this->data['description'] = $descriptions[$language['language_id']]['description'];
			}
		}
		$this->data['model'] = $product_info['model'];
		$this->data['sku'] = $product_info['sku'];
		$this->data['upc'] = $product_info['upc'];
		// the following attributes are not in the previous version (eariler than 1.5.5.1)
		// and the current page details do not require them,
		$this->data['ean'] = isset($product_info['ean']) ? $product_info['ean'] : '';
		$this->data['jan'] = isset($product_info['jan']) ? $product_info['jan'] : '';
		$this->data['isbn'] = isset($product_info['isbn']) ? $product_info['isbn'] : '';
		$this->data['mpn'] = isset($product_info['mpn']) ? $product_info['mpn'] : '';
		$this->data['location'] = $product_info['location'];

		$this->load->model('setting/store');
		$this->data['stores'] = $this->model_setting_store->getStores();
		$this->data['product_store'] = $this->model_catalog_product->getProductStores($product_id);
		$this->data['keyword'] = $product_info['keyword'];
		$this->data['image'] = $product_info['image'];

		$this->load->model('tool/image');
		if ($product_info['image'] && file_exists(DIR_IMAGE . $product_info['image'])) {
			$this->data['thumb'] = $this->model_tool_image->resize($product_info['image'], 100, 100);
		} else {
			$this->data['thumb'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		}
     	$this->data['shipping'] = $product_info['shipping'];
		$this->data['price'] = $product_info['price'];

		$this->load->model('localisation/tax_class');
		$this->data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
		$this->data['tax_class_id'] = $product_info['tax_class_id'];
		$this->data['date_available'] = date('Y-m-d', strtotime($product_info['date_available']));
    	$this->data['quantity'] = $product_info['quantity'];
      	$this->data['minimum'] = $product_info['minimum'];
      	$this->data['subtract'] = $product_info['subtract'];
      	$this->data['sort_order'] = $product_info['sort_order'];

		$this->load->model('localisation/stock_status');
		$stock_statuses = $this->model_localisation_stock_status->getStockStatuses();
		$this->data['stock_status'] = '';
		foreach ($stock_statuses as $stock_status) {
			if ($stock_status['stock_status_id'] == $product_info['stock_status_id']) {
				$this->data['stock_status'] = $stock_status['name'];
			}
		}

 		$this->data['status'] = $product_info['status'];
		$this->data['weight'] = $product_info['weight'];

		$this->load->model('localisation/weight_class');
		$this->data['weight_classes'] = $this->model_localisation_weight_class->getWeightClasses();
  		$this->data['weight_class_id'] = $product_info['weight_class_id'];
		$this->data['length'] = $product_info['length'];
		$this->data['width'] = $product_info['width'];
		$this->data['height'] = $product_info['height'];

		$this->load->model('localisation/length_class');
		$this->data['length_classes'] = $this->model_localisation_length_class->getLengthClasses();
  		$this->data['length_class_id'] = $product_info['length_class_id'];

		$this->load->model('catalog/manufacturer');
		$this->data['manufacturer_id'] = $product_info['manufacturer_id'];
		$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($product_info['manufacturer_id']);
		if ($manufacturer_info) {		
			$this->data['manufacturer'] = $manufacturer_info['name'];
		} else {
			$this->data['manufacturer'] = '';
		}	
		
		// Categories
		$this->load->model('catalog/category');
		$categories = $this->model_catalog_product->getProductCategories($product_id);
		$this->data['product_categories'] = array();
		
		foreach ($categories as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);
			
			if ($category_info) {
				$this->data['product_categories'][] = array(
					'category_id' => $category_info['category_id'],
					'name'        => isset($category_info['path']) ? (($category_info['path'] ? $category_info['path'] . ' &gt; ' : '') . $category_info['name']) : ''
				);
			}
		}
		
		// the following model is not in the previous version (eariler than 1.5.5.1)
		// and the current page details do not require it,
		// Filters
		/*
		$this->load->model('catalog/filter');
		$filters = $this->model_catalog_product->getProductFilters($product_id);
		$this->data['product_filters'] = array();
		foreach ($filters as $filter_id) {
			$filter_info = $this->model_catalog_filter->getFilter($filter_id);
			
			if ($filter_info) {
				$this->data['product_filters'][] = array(
					'filter_id' => $filter_info['filter_id'],
					'name'      => $filter_info['group'] . ' &gt; ' . $filter_info['name']
				);
			}
		}
		*/
		
		// Attributes
		$this->load->model('catalog/attribute');
		$product_attributes = $this->model_catalog_product->getProductAttributes($product_id);
		$this->data['product_attributes'] = array();
		foreach ($product_attributes as $product_attribute) {
			$attribute_info = $this->model_catalog_attribute->getAttribute($product_attribute['attribute_id']);
			
			if ($attribute_info) {
				$this->data['product_attributes'][] = array(
					'attribute_id'                  => $product_attribute['attribute_id'],
					'name'                          => isset($attribute_info['name']) ? $attribute_info['name'] : '',
					'product_attribute_description' => $product_attribute['product_attribute_description']
				);
			}
		}		
		
		// Options
		$this->load->model('catalog/option');
		$product_options = $this->model_catalog_product->getProductOptions($product_id);			

		$this->data['product_options'] = array();
		foreach ($product_options as $product_option) {
			if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
				$product_option_value_data = array();
				
				foreach ($product_option['product_option_value'] as $product_option_value) {
					$product_option_value_data[] = array(
						'product_option_value_id' => $product_option_value['product_option_value_id'],
						'option_value_id'         => $product_option_value['option_value_id'],
						'quantity'                => $product_option_value['quantity'],
						'subtract'                => $product_option_value['subtract'],
						'price'                   => $product_option_value['price'],
						'price_prefix'            => $product_option_value['price_prefix'],
						'points'                  => $product_option_value['points'],
						'points_prefix'           => $product_option_value['points_prefix'],						
						'weight'                  => $product_option_value['weight'],
						'weight_prefix'           => $product_option_value['weight_prefix']	
					);
				}
				
				$this->data['product_options'][] = array(
					'product_option_id'    => $product_option['product_option_id'],
					'product_option_value' => $product_option_value_data,
					'option_id'            => $product_option['option_id'],
					'name'                 => $product_option['name'],
					'type'                 => $product_option['type'],
					'required'             => $product_option['required']
				);				
			} else {
				$this->data['product_options'][] = array(
					'product_option_id' => $product_option['product_option_id'],
					'option_id'         => $product_option['option_id'],
					'name'              => $product_option['name'],
					'type'              => $product_option['type'],
					'option_value'      => $product_option['option_value'],
					'required'          => $product_option['required']
				);				
			}
		}
		
		$this->data['option_values'] = array();
		
		foreach ($this->data['product_options'] as $product_option) {
			if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
				if (!isset($this->data['option_values'][$product_option['option_id']])) {
					$this->data['option_values'][$product_option['option_id']] = $this->model_catalog_option->getOptionValues($product_option['option_id']);
				}
			}
		}
		
		$this->load->model('sale/customer_group');
		$this->data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();
		
		$this->data['product_discounts'] = $this->model_catalog_product->getProductDiscounts($product_id);
		$this->data['product_specials'] = $this->model_catalog_product->getProductSpecials($product_id);
		
		// Images
		$product_images = $this->model_catalog_product->getProductImages($product_id);
		$this->data['product_images'] = array();
		foreach ($product_images as $product_image) {
			if ($product_image['image'] && file_exists(DIR_IMAGE . $product_image['image'])) {
				$image = $product_image['image'];
			} else {
				$image = 'no_image.jpg';
			}
			
			$this->data['product_images'][] = array(
				'image'      => $image,
				'thumb'      => $this->model_tool_image->resize($image, 100, 100),
				'sort_order' => $product_image['sort_order']
			);
		}

		$this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);

		// Downloads
		$this->load->model('catalog/download');
		$product_downloads = $this->model_catalog_product->getProductDownloads($product_id);
		$this->data['product_downloads'] = array();
		foreach ($product_downloads as $download_id) {
			$download_info = $this->model_catalog_download->getDownload($download_id);
			
			if ($download_info) {
				$this->data['product_downloads'][] = array(
					'download_id' => $download_info['download_id'],
					'name'        => $download_info['name']
				);
			}
		}
		
		$products = $this->model_catalog_product->getProductRelated($product_id);
		$this->data['product_related'] = array();
		foreach ($products as $product_id) {
			$related_info = $this->model_catalog_product->getProduct($product_id);
			
			if ($related_info) {
				$this->data['product_related'][] = array(
					'product_id' => $related_info['product_id'],
					'name'       => $related_info['name']
				);
			}
		}

		$this->data['points'] = $product_info['points'];
		$this->data['product_reward'] = $this->model_catalog_product->getProductRewards($product_id);
		$this->data['product_layout'] = $this->model_catalog_product->getProductLayouts($product_id);

		$this->load->model('design/layout');
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
		
		// add for Cost Display begin
		if (isset($product_info['cost'])) {
			$this->data['cost'] = $product_info['cost'];
		}
		// add for Cost Display end

		$this->template = 'pos/product_details.tpl';
				
		$this->response->setOutput($this->render());
	}
	
	private function getCustomer($customer_id) {
		$this->language->load('sale/customer');
    	$this->data['text_enabled'] = $this->language->get('text_enabled');
    	$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_select'] = $this->language->get('text_select');
		
    	$this->data['entry_password'] = $this->language->get('entry_password');
    	$this->data['entry_confirm'] = $this->language->get('entry_confirm');
		$this->data['entry_newsletter'] = $this->language->get('entry_newsletter');
    	$this->data['entry_customer_group'] = $this->language->get('entry_customer_group');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_default'] = $this->language->get('entry_default');
 
    	$this->data['button_add_address'] = $this->language->get('button_add_address');
    	$this->data['button_remove'] = $this->language->get('button_remove');
	
		$this->data['tab_general'] = $this->language->get('tab_general');
		$this->data['tab_address'] = $this->language->get('tab_address');

		$this->load->model('sale/customer');
		$customer_info = $this->model_sale_customer->getCustomer($customer_id);
			
		if (!empty($customer_info)) { 
			$this->data['customer_firstname'] = $customer_info['firstname'];
			$this->data['customer_lastname'] = $customer_info['lastname'];
      		$this->data['customer_email'] = $customer_info['email'];
			$this->data['customer_telephone'] = $customer_info['telephone'];
			$this->data['customer_fax'] = $customer_info['fax'];
			$this->data['customer_newsletter'] = $customer_info['newsletter'];
			$this->data['customer_customer_group_id'] = $customer_info['customer_group_id'];
			$this->data['customer_status'] = $customer_info['status'];
			$this->data['customer_addresses'] = $this->model_sale_customer->getAddresses($customer_id);
			$this->data['customer_address_id'] = $customer_info['address_id'];
			$this->data['hasAddress'] = 1;
			foreach ($this->data['customer_addresses'] as $address) {
				if ($customer_info['address_id'] == $address['address_id']) {
					$this->data['hasAddress'] = 2;
					break;
				}
			}
			$this->data['customer_password'] = '';
			$this->data['customer_confirm'] = '';
		} else {
      		$this->data['customer_firstname'] = '';
      		$this->data['customer_lastname'] = '';
      		$this->data['customer_email'] = '';
      		$this->data['customer_telephone'] = '';
      		$this->data['customer_fax'] = '';
      		$this->data['customer_newsletter'] = '';
      		$this->data['customer_customer_group_id'] = $this->config->get('config_customer_group_id');
      		$this->data['customer_status'] = 1;
			$this->data['customer_password'] = '';
			$this->data['customer_confirm'] = '';
			$this->data['customer_addresses'] = array();
      		$this->data['customer_address_id'] = '';
    	}

		$this->load->model('sale/customer_group');
		$this->data['customer_customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();

		$this->load->model('localisation/country');
		$this->data['customer_countries'] = $this->model_localisation_country->getCountries();
	}
	
	public function save_order() {
		$json = array();
		$this->load->library('user');
		$this->user = new User($this->registry);
		if ($this->user->isLogged() && $this->user->hasPermission('modify', 'sale/order')) {
			$this->load->model('sale/order');
			$this->model_sale_order->editOrder($this->request->get['order_id'], $this->request->post);
		} else {
			$json['error']['warning'] = $this->language->get('error_permission');
		}
		$this->response->setOutput(json_encode($json));
	}
	
	public function save_customer() {
		$json = array();
		$json['hasAddress'] = 1;
		$this->load->library('user');
		$this->user = new User($this->registry);
		if ($this->user->isLogged() && $this->user->hasPermission('modify', 'sale/customer')) {
			$data = array();
			$keys = array_keys($this->request->post);
			foreach ($keys as $key) {
				$value = $this->request->post[$key];
				if ($key == 'customer_address') {
					foreach ($value as $address) {
						if (isset($address['default']) && $address['default']) {
							$json['hasAddress'] = 2;
							break;
						}
					}
				}
				if (strpos($key, 'customer_') === 0) {
					$dataKey = substr($key, 9);
					$data[$dataKey] = $value;
				}
			}
			
			if (isset($this->request->get['customer_id'])) {
				$data['customer_id'] = $this->request->get['customer_id'];
			}
			if (!empty($data) && isset($data['customer_id'])) {
				$this->load->model('sale/customer');
				$this->model_sale_customer->editCustomer($data['customer_id'], $data);
				$customer_addresses = $this->model_sale_customer->getAddresses((int)$data['customer_id']);
				// update email address (the rest of info can be modified from address edit page
				$this->db->query("UPDATE `" . DB_PREFIX . "order` SET email = '" . $this->db->escape($data['email']) . "' WHERE order_id = '" . (int)$this->request->get['order_id'] . "'");
				// add for Add Customer begin
				if ($this->user->hasPermission('modify', 'sale/order') && isset($this->request->get['order_id'])) {
					// update order with the given customer
					$order_id = $this->request->get['order_id'];
					$customer_info = $this->model_sale_customer->getCustomer((int)$data['customer_id']);
					$sql = "UPDATE `" . DB_PREFIX . "order` SET store_id = '" . $customer_info['store_id'] . "', customer_id = '" . $customer_info['customer_id'] . "', customer_group_id = '" . $customer_info['customer_group_id'] . "', firstname = '" . $this->db->escape($customer_info['firstname']) ."', lastname = '" . $this->db->escape($customer_info['lastname']) . "', email = '" . $this->db->escape($customer_info['email']) . "', telephone = '" . $this->db->escape($customer_info['telephone']) . "', fax = '" . $this->db->escape($customer_info['fax']) . "', date_modified = NOW()";

					$hasAddress = false;
					foreach ($customer_addresses as $address) {
						if ($customer_info['address_id'] == $address['address_id']) {
							// update the order shipping address and payment address
							$sql .= ", payment_firstname = '" . $this->db->escape($address['firstname']) . "', payment_lastname = '" . $this->db->escape($address['lastname']) . "', payment_company = '" . $this->db->escape($address['company']) . "', payment_company_id = '" . $this->db->escape($address['company_id']) . "', payment_tax_id = '" . $this->db->escape($address['tax_id']) . "', payment_address_1 = '" . $this->db->escape($address['address_1']) . "', payment_address_2 = '" . $this->db->escape($address['address_2']) . "', payment_city = '" . $this->db->escape($address['city']) . "', payment_postcode = '" . $this->db->escape($address['postcode']) . "', payment_country = '" . $this->db->escape($address['country']) . "', payment_country_id = '" . (int)$address['country_id'] . "', payment_zone = '" . $this->db->escape($address['zone']) . "', payment_zone_id = '" . (int)$address['zone_id'] . "', shipping_firstname = '" . $this->db->escape($address['firstname']) . "', shipping_lastname = '" . $this->db->escape($address['lastname']) . "',  shipping_company = '" . $this->db->escape($address['company']) . "', shipping_address_1 = '" . $this->db->escape($address['address_1']) . "', shipping_address_2 = '" . $this->db->escape($address['address_2']) . "', shipping_city = '" . $this->db->escape($address['city']) . "', shipping_postcode = '" . $this->db->escape($address['postcode']) . "', shipping_country = '" . $this->db->escape($address['country']) . "', shipping_country_id = '" . (int)$address['country_id'] . "', shipping_zone = '" . $this->db->escape($address['zone']) . "', shipping_zone_id = '" . (int)$address['zone_id'] . "'";
							$hasAddress = true;
							$json['order_address'] = $address;
							break;
						}
					}
					if (!$hasAddress) {
						$sql .= ", payment_firstname = '', payment_lastname = '', payment_company = '', payment_company_id = '', payment_tax_id = '', payment_address_1 = '', payment_address_2 = '', payment_city = '', payment_postcode = '', payment_country = '', payment_country_id = '', payment_zone = '', payment_zone_id = '', shipping_firstname = '', shipping_lastname = '',  shipping_company = '', shipping_address_1 = '', shipping_address_2 = '', shipping_city = '', shipping_postcode = '', shipping_country = '', shipping_country_id = '', shipping_zone = '', shipping_zone_id = ''";
					}
					$sql .= " WHERE order_id = '" . (int)$order_id . "'";
					$this->db->query($sql);
					try {
						$this->model_sale_customer->approve($data['customer_id']);
					} catch (Exception $e) {
					}
				} else {
					$customer_info = $this->model_sale_customer->getCustomer((int)$data['customer_id']);
					foreach ($customer_addresses as $address) {
						if ($customer_info['address_id'] == $address['address_id']) {
							$json['order_address'] = $address;
							break;
						}
					}
				}
				// add for Add Customer end
				// add for Edit order address begin
				$json['customer_addresses'] = $customer_addresses;
				// add for Edit order address end
				$this->language->load('module/pos');
				$json['success'] = $this->language->get('text_customer_success');
			}
		} else {
			$json['error']['warning'] = $this->language->get('error_permission');
		}
		$this->response->setOutput(json_encode($json));
	}
	
	private function getStoreId() {
		if (isset($this->request->get['store_id'])) {
			$store_id = $this->request->get['store_id'];
		} else {
			$store_id = 0;
			// get the default store id
			$this->load->model('setting/store');
			$stores = $this->model_setting_store->getStores();
			if (!empty($stores)) {
				$store_id = $stores[0]['store_id'];
			}
		}
		return $store_id;
	}
	
	public function createEmptyOrder() {
		// create an empty order with default / dummy customer data
		unset($this->session->data['shipping_method']);
		
		$data = array();
		
		$data['store_id'] = $this->getStoreId();
		
		$default_country_id = $this->config->get('config_country_id');
		$default_zone_id = $this->config->get('config_zone_id');
		$data['shipping_country_id'] = $default_country_id;
		$data['shipping_zone_id'] = $default_zone_id;
		$data['payment_country_id'] = $default_country_id;
		$data['payment_zone_id'] = $default_zone_id;
		$data['customer_id'] = 0;
		$data['customer_group_id'] = 1;
		$data['firstname'] = 'Instore';
		$data['lastname'] = "Dummy";
		$data['email'] = 'customer@instore.com';
		$data['telephone'] = '1600';
		$data['fax'] = '';
		$data['payment_firstname'] = 'Instore';
		$data['payment_lastname'] = "Dummy";
		$data['payment_company'] = '';
		$data['payment_company_id'] = '';
		$data['payment_tax_id'] = '';
		$data['payment_address_1'] = 'customer address';
		$data['payment_address_2'] = '';
		$data['payment_city'] = 'customer city';
		$data['payment_postcode'] = '1600';
		$data['payment_country_id'] = $default_country_id;
		$data['payment_zone_id'] = $default_zone_id;
		$data['payment_method'] = 'In Store';
		$data['payment_code'] = 'in_store';
		$data['shipping_firstname'] = 'Instore';
		$data['shipping_lastname'] = 'Dummy';
		$data['shipping_company'] = '';
		$data['shipping_address_1'] = 'customer address';
		$data['shipping_address_2'] = '';
		$data['shipping_city'] = 'customer city';
		$data['shipping_postcode'] = '1600';
		$data['shipping_country_id'] = $default_country_id;
		$data['shipping_zone_id'] = $default_zone_id;
		$data['shipping_method'] = 'instore';
		$data['shipping_code'] = 'instore.instore';
		$data['comment'] = '';
		$data['order_status_id'] = 1;
		// add for Empty order control begin
		if ($this->config->get('initial_status_id')) {
			$data['order_status_id'] = $this->config->get('initial_status_id');
		}
		// add for Empty order control end
		$data['affiliate_id'] = 0;
		$data['user_id'] = $this->user->getId();
		
		// add for Default Customer begin
		$c_data = array();
		$this->setDefaultCustomer($c_data);
		$data['customer_id'] = $c_data['c_id'];
		$data['customer_group_id'] = $c_data['c_group_id'];
		foreach ($c_data as $c_key => $c_value) {
			if (substr($c_key, 0, 2) == 'c_' && isset($data[substr($c_key, 2)])) {
				$data[substr($c_key, 2)] = $c_value;
			} elseif (substr($c_key, 0, 2) == 'a_') {
				if (isset($data['payment_'.substr($c_key, 2)])) {
					$data['payment_'.substr($c_key, 2)] = $c_value;
				}
				if (isset($data['shipping_'.substr($c_key, 2)])) {
					$data['shipping_'.substr($c_key, 2)] = $c_value;
				}
			}
		}
		// add for Default Customer end
		// add for Quotation begin
		if (isset($this->request->get['work_mode']) && $this->request->get['work_mode'] == '2') {
			$data['quote'] = 1;
		}
		// add for Quotation end
		// add for table management begin
		if (isset($this->request->get['table_id'])) {
			$data['table_id'] = $this->request->get['table_id'];
		}
		// add for table management end
		
		$this->load->model('pos/pos');
		$order_id = $this->model_pos_pos->addOrder($data);
		// add for table management begin
		if (isset($this->request->get['table_id'])) {
			$pos_table_new_order = array('table_id' => $this->request->get['table_id'], 'order_id' => $order_id);
			$this->session->data['pos_table_new_order'] = $pos_table_new_order;
		}
		// add for table management end
		$context = 'token=' . $this->session->data['token'] . '&order_id=' . $order_id;
		// add for Quotation begin
		if (isset($this->request->get['work_mode']) && $this->request->get['work_mode'] == '2') {
			$context .= '&work_mode=2';
		}
		// add for Quotation end
		$result = $this->url->link('module/pos/main', $context, 'SSL');
		$this->redirect($result);
	}

	public function detachCustomer() {
		$customer = array();
		
		$default_country_id = $this->config->get('config_country_id');
		$default_zone_id = $this->config->get('config_zone_id');
		$customer['customer_id'] = 0;
		$customer['customer_group_id'] = 1;
		$customer['firstname'] = 'Instore';
		$customer['lastname'] = "Dummy";
		$customer['email'] = 'customer@instore.com';
		$customer['telephone'] = '1600';
		$customer['fax'] = '';
		$customer['payment_firstname'] = 'Instore';
		$customer['payment_lastname'] = "Dummy";
		$customer['payment_company'] = '';
		$customer['payment_company_id'] = '';
		$customer['payment_tax_id'] = '';
		$customer['payment_address_1'] = 'customer address';
		$customer['payment_address_2'] = '';
		$customer['payment_city'] = 'customer city';
		$customer['payment_postcode'] = '1600';
		$customer['payment_country_id'] = $default_country_id;
		$customer['payment_zone_id'] = $default_zone_id;
		$customer['payment_method'] = 'In Store';
		$customer['payment_code'] = 'in_store';
		$customer['shipping_firstname'] = 'Instore';
		$customer['shipping_lastname'] = 'Dummy';
		$customer['shipping_company'] = '';
		$customer['shipping_address_1'] = 'customer address';
		$customer['shipping_address_2'] = '';
		$customer['shipping_city'] = 'customer city';
		$customer['shipping_postcode'] = '1600';
		$customer['shipping_country_id'] = $default_country_id;
		$customer['shipping_zone_id'] = $default_zone_id;
		
		$this->load->model('localisation/country');
		$this->load->model('localisation/zone');
		$country_info = $this->model_localisation_country->getCountry($default_country_id);
		if ($country_info) {
			$payment_country = $country_info['name'];
			$payment_address_format = $country_info['address_format'];			
		} else {
			$payment_country = '';	
			$payment_address_format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';					
		}
		$shipping_country = $payment_country;
		$shipping_address_format = $payment_address_format;
		$zone_info = $this->model_localisation_zone->getZone($default_zone_id);
		if ($zone_info) {
			$payment_zone = $zone_info['name'];
		} else {
			$payment_zone = '';			
		}			
		$shipping_zone = $payment_zone;
		
		$order_id = $this->request->get['order_id'];
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET customer_id = '0', customer_group_id = '1', firstname = '" . $this->db->escape($customer['firstname']) ."', lastname = '" . $this->db->escape($customer['lastname']) . "', email = '" . $this->db->escape($customer['email']) . "', telephone = '" . $this->db->escape($customer['telephone']) . "', fax = '" . $this->db->escape($customer['fax']) . "', payment_firstname = '" . $this->db->escape($customer['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($customer['payment_lastname']) . "', payment_company = '" . $this->db->escape($customer['payment_company']) . "', payment_company_id = '" . $this->db->escape($customer['payment_company_id']) . "', payment_tax_id = '" . $this->db->escape($customer['payment_tax_id']) . "', payment_address_1 = '" . $this->db->escape($customer['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($customer['payment_address_2']) . "', payment_city = '" . $this->db->escape($customer['payment_city']) . "', payment_postcode = '" . $this->db->escape($customer['payment_postcode']) . "', payment_country = '" . $this->db->escape($payment_country) . "', payment_country_id = '" . (int)$customer['payment_country_id'] . "', payment_zone = '" . $this->db->escape($payment_zone) . "', payment_zone_id = '" . (int)$customer['payment_zone_id'] . "', payment_address_format = '" . $this->db->escape($payment_address_format) . "', shipping_firstname = '" . $this->db->escape($customer['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($customer['shipping_lastname']) . "',  shipping_company = '" . $this->db->escape($customer['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($customer['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($customer['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($customer['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($customer['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($shipping_country) . "', shipping_country_id = '" . (int)$customer['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($shipping_zone) . "', shipping_zone_id = '" . (int)$customer['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($shipping_address_format) . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");
		$this->language->load('module/pos');
		$customer['success'] = $this->language->get('text_order_sucess');
		$this->response->setOutput(json_encode($customer));	
	}

	public function modifyOrder() {
		// add for location based stock begin
		$this->load->model('pos/pos');
		$location_id = $this->model_pos_pos->getLocationForUser($this->user->getId());
		$enable_location_stock = $this->config->get('enable_location_stock');
		// add for location based stock end
		$this->language->load('module/pos');
		
		$json = array();
		
		$order_id = $this->request->post['order_id'];
		
		$product_id = '';
		if (isset($this->request->post['order_product'])) {
			$order_product = $this->request->post['order_product'];
			$product_id = $order_product['product_id'];
			if ($order_product['action'] == 'insert') {
      			$this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$order_product['product_id'] . "', name = '" . $this->db->escape($order_product['name']) . "', model = '" . $this->db->escape($order_product['model']) . "', quantity = '" . (int)$order_product['quantity'] . "', price = '" . (float)$order_product['price'] . "', total = '" . (float)$order_product['total'] . "', tax = '" . (float)$order_product['tax'] . "', reward = '" . (int)$order_product['reward'] . "'");
			
				$order_product_id = $this->db->getLastId();
				// add for location based stock begin
				if ($enable_location_stock && $location_id) {
					$this->db->query("UPDATE " . DB_PREFIX . "order_product SET location_id = '" . (int)$location_id . "' WHERE order_product_id = '" . (int)$order_product_id . "'");
					$this->db->query("UPDATE " . DB_PREFIX . "location_stock SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE location_id = '" . (int)$location_id . "' AND product_id = '" . (int)$order_product['product_id'] . "' AND product_option_value_id = '0'");
				} else {
				// add for location based stock end
				
				$this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE product_id = '" . (int)$order_product['product_id'] . "' AND subtract = '1'");
				
				// add for location based stock begin
				}
				// add for location based stock end
				
				if (isset($order_product['option'])) {
					foreach ($order_product['option'] as $order_option) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "order_option SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', product_option_id = '" . (int)$order_option['product_option_id'] . "', product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "', name = '" . $this->db->escape($order_option['name']) . "', `value` = '" . $this->db->escape($order_option['option_value']) . "', `type` = '" . $this->db->escape($order_option['type']) . "'");
						
						// add for location based stock begin
						if ($enable_location_stock && $location_id) {
							$this->db->query("UPDATE " . DB_PREFIX . "location_stock SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE location_id = '" . (int)$location_id . "' AND product_id = '" . (int)$order_product['product_id'] . "' AND product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "'");
						} else {
						// add for location based stock end
						
						$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "' AND subtract = '1'");
						
						// add for location based stock begin
						}
						// add for location based stock end
					}
				}
				// add for serial no begin
				$json['product_sns'] = $this->model_pos_pos->sellProductSN($this->request->post['product_sn_id'], $this->request->post['product_sn'], $order_product_id, $product_id, $order_id);
				// add for serial no end
			} elseif ($order_product['action'] == 'modify') {
				$sqlQuery = "UPDATE " . DB_PREFIX . "order_product SET quantity = " . (int)$order_product['quantity'] . ", total = '" . (float)$order_product['total'] . "' WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product['order_product_id'] . "'";
      			$this->db->query($sqlQuery);
				
				// add for location based stock begin
				if ($enable_location_stock && $location_id) {
					$this->db->query("UPDATE " . DB_PREFIX . "order_product SET location_id = '" . (int)$location_id . "' WHERE order_product_id = '" . (int)$order_product['order_product_id'] . "'");
					$this->db->query("UPDATE " . DB_PREFIX . "location_stock SET quantity = (quantity - " . (int)$order_product['quantity_change'] . ") WHERE location_id = '" . (int)$location_id . "' AND product_id = '" . (int)$order_product['product_id'] . "' AND product_option_value_id = '0'");
				} else {
				// add for location based stock end
				
				$this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = (quantity - " . (int)$order_product['quantity_change'] . ") WHERE product_id = '" . (int)$order_product['product_id'] . "' AND subtract = '1'");
				
				// add for location based stock begin
				}
				// add for location based stock end
				
				if (isset($order_product['option'])) {
					foreach ($order_product['option'] as $order_option) {
						// add for location based stock begin
						if ($enable_location_stock && $location_id) {
							$this->db->query("UPDATE " . DB_PREFIX . "location_stock SET quantity = (quantity - " . (int)$order_product['quantity_change'] . ") WHERE location_id = '" . (int)$location_id . "' AND product_id = '" . (int)$order_product['product_id'] . "' AND product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "'");
						} else {
						// add for location based stock end
						
						$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity - " . (int)$order_product['quantity_change'] . ") WHERE product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "' AND subtract = '1'");
						
						// add for location based stock begin
						}
						// add for location based stock end
					}
				}
				// add for serial no begin
				$json['product_sns'] = $this->model_pos_pos->sellProductSN($this->request->post['product_sn_id'], $this->request->post['product_sn'], $order_product['order_product_id'], $order_product['product_id'], $order_id);
				// add for serial no end
			}
		} else {
			$action = $this->request->post['action'];
			if ($action != 'new' && $action != 'insert') {
				$product_id = $this->request->post['product_id'];
				$ex_order_product_id = $this->request->post['order_product_id'];
				if ($action == 'delete') {
					// add for location based stock begin
					if ($enable_location_stock && $location_id) {
						$this->db->query("UPDATE " . DB_PREFIX . "location_stock SET quantity = (quantity + " . (int)$this->request->post['quantity'] . ") WHERE location_id = '" . (int)$location_id . "' AND product_id = '" . (int)$this->request->post['product_id'] . "' AND product_option_value_id = '0'");
					} else {
					// add for location based stock end

					$this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = (quantity + " . (int)$this->request->post['quantity'] . ") WHERE product_id = '" . (int)$this->request->post['product_id'] . "' AND subtract = '1'");

					// add for location based stock begin
					}
					// add for location based stock end
					
					if (!empty($this->request->post['option'])) {
						foreach ($this->request->post['option'] as $order_option) {
							// add for location based stock begin
							if ($enable_location_stock && $location_id) {
								$this->db->query("UPDATE " . DB_PREFIX . "location_stock SET quantity = (quantity + " . (int)$this->request->post['quantity'] . ") WHERE location_id = '" . (int)$location_id . "' AND product_id = '" . (int)$this->request->post['product_id'] . "' AND product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "'");
							} else {
							// add for location based stock end
							
							$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity + " . (int)$this->request->post['quantity'] . ") WHERE product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "' AND subtract = '1'");

							// add for location based stock begin
							}
							// add for location based stock end
						}
					}
					$this->db->query("DELETE FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$ex_order_product_id . "'");
					$this->db->query("DELETE FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$ex_order_product_id . "'");
					$this->db->query("DELETE FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$ex_order_product_id . "'");
					// add for serial no begin
					$this->model_pos_pos->restoreSoldProductSN($ex_order_product_id);
					// add for serial no end
				} elseif ($action == 'modify') {
					$sqlQuery = "UPDATE " . DB_PREFIX . "order_product SET quantity = " . (int)$this->request->post['quantity'] . ", total = '" . (float)$this->request->post['total'] . "' WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$ex_order_product_id . "'";
					// add for Inplace Pricing begin
					if (isset($this->request->post['inplace_price'])) {
						$inplace_price = (float)$this->request->post['inplace_price'];
						$inplace_tax = (float)$this->request->post['inplace_tax'];
						$sqlQuery = "UPDATE " . DB_PREFIX . "order_product SET quantity = " . (int)$this->request->post['quantity'] . ", price = '" . $inplace_price . "', tax = '" . $inplace_tax . "', total = '" . (float)$this->request->post['total'] . "' WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$ex_order_product_id . "'";
					}
					// add for Inplace Pricing end
					$this->db->query($sqlQuery);
					// add for location based stock begin
					if ($enable_location_stock && $location_id) {
						$this->db->query("UPDATE " . DB_PREFIX . "order_product SET location_id = '" . (int)$location_id . "' WHERE order_product_id = '" . (int)$ex_order_product_id . "'");
						$this->db->query("UPDATE " . DB_PREFIX . "location_stock SET quantity = (quantity - " . (int)$this->request->post['quantity_change'] . ") WHERE location_id = '" . (int)$location_id . "' AND product_id = '" . (int)$this->request->post['product_id'] . "' AND product_option_value_id = '0'");
					} else {
					// add for location based stock end
					
					$this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = (quantity - " . (int)$this->request->post['quantity_change'] . ") WHERE product_id = '" . (int)$this->request->post['product_id'] . "' AND subtract = '1'");

					// add for location based stock begin
					}
					// add for location based stock end
					
					if (!empty($this->request->post['option'])) {
						foreach ($this->request->post['option'] as $order_option) {
							// add for location based stock begin
							if ($enable_location_stock && $location_id) {
								$this->db->query("UPDATE " . DB_PREFIX . "location_stock SET quantity = (quantity - " . (int)$this->request->post['quantity_change'] . ") WHERE location_id = '" . (int)$location_id . "' AND product_id = '" . (int)$this->request->post['product_id'] . "' AND product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "'");
							} else {
							// add for location based stock end
							
							$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity - " . (int)$this->request->post['quantity_change'] . ") WHERE product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "' AND subtract = '1'");
							
							// add for location based stock begin
							}
							// add for location based stock end
						}
					}
				}
			}
		}
		
		// Get the total
		$total = 0;
				
		$this->db->query("DELETE FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "'");
		
		if (isset($this->request->post['order_total'])) {		
      		foreach ($this->request->post['order_total'] as $order_total) {	
      			$this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int)$order_id . "', code = '" . $this->db->escape($order_total['code']) . "', title = '" . $this->db->escape($order_total['title']) . "', text = '" . $this->db->escape($order_total['text']) . "', `value` = '" . (float)$order_total['value'] . "', sort_order = '" . (int)$order_total['sort_order'] . "'");
			}
			
			$total += $order_total['value'];
		}
		$this->db->query("UPDATE `" . DB_PREFIX . "order` SET total = '" . (float)$total . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'"); 
		
		if (isset($order_product_id)) {
			$json['order_product_id'] = $order_product_id;
		}
		$json['success'] = $this->language->get('text_order_sucess');
		// add for Quotation begin
		if (isset($this->request->get['work_mode']) && $this->request->get['work_mode'] == '2') {
			$json['success'] = $this->language->get('text_quote_sucess');
		}
		// add for Quotation end
		if ($product_id != '') {
			// add for Openbay integration begin
			$json['enable_openbay'] = $this->config->get('enable_openbay');
			// add for Openbay integration bend
			$json['product_id'] = $product_id;
		}
		$this->response->setOutput(json_encode($json));	
	}
	
	public function saveOrderStatus() {
		$order_id = $this->request->post['order_id'];
		$order_status_id = $this->request->post['order_status_id'];
		$this->load->model('pos/pos');
		$this->model_pos_pos->saveOrderStatus($order_id, $order_status_id);

		$this->language->load('module/pos');
		$json['success'] = $this->language->get('text_order_sucess');
		// add for Print begin
		if ($order_status_id == 5) {
			$json['p_complete'] = $this->config->get('p_complete') ? $this->config->get('p_complete') : 0;
		}
		// add for Print end
		// add for Empty order control begin
		if ($this->config->get('initial_status_id')) {
			$json['initial_status_id'] = $this->config->get('initial_status_id');
		}
		// add for Empty order control end
		
		// add for status change notification begin
		if ($this->config->get('enable_notification')) {
			$this->sendNotification($order_id, $order_status_id);
		}
		// add for status change notification end
		// add for commission begin
		if ($order_status_id == 5 && $this->config->get('enable_commission')) {
			$this->model_pos_pos->addOrderCommission($order_id, $this->user->getId());
		}
		// add for commission end
		
		$this->response->setOutput(json_encode($json));	
	}
	
	// add for Quotation begin
	public function saveQuoteStatus() {
		$order_id = $this->request->post['order_id'];
		$quote_status_id = $this->request->post['quote_status_id'];
		$this->load->model('pos/pos');
		$this->model_pos_pos->saveQuoteStatus($order_id, $quote_status_id);

		$this->language->load('module/pos');
		$json['success'] = $this->language->get('text_quote_sucess');
		$this->response->setOutput(json_encode($json));	
	}
	// add for Quotation end
	
	public function saveOrderCustomer() {
		$json = array();
		$order_id = $this->request->get['order_id'];
		$sql = "UPDATE `" . DB_PREFIX . "order` SET store_id = '" . $this->request->post['store_id'] . "', customer_id = '" . $this->request->post['customer_id'] . "', customer_group_id = '" . $this->request->post['customer_group_id'] . "', firstname = '" . $this->db->escape($this->request->post['firstname']) ."', lastname = '" . $this->db->escape($this->request->post['lastname']) . "', email = '" . $this->db->escape($this->request->post['email']) . "', telephone = '" . $this->db->escape($this->request->post['telephone']) . "', fax = '" . $this->db->escape($this->request->post['fax']) . "', date_modified = NOW()";
		if (((int)$this->request->post['customer_id']) > 0) {
			$json['hasAddress'] = 1;
			// switch to a real customer
			$this->load->model('sale/customer');
			$customer_info = $this->model_sale_customer->getCustomer((int)$this->request->post['customer_id']);
			$json['customer_info'] = $customer_info;
			$json['customer_addresses'] = $this->model_sale_customer->getAddresses((int)$this->request->post['customer_id']);
			foreach ($json['customer_addresses'] as $address) {
				if ($customer_info['address_id'] == $address['address_id']) {
					// update the order shipping address and payment address
					$sql .= ", payment_firstname = '" . $this->db->escape($address['firstname']) . "', payment_lastname = '" . $this->db->escape($address['lastname']) . "', payment_company = '" . $this->db->escape($address['company']) . "', payment_company_id = '" . $this->db->escape($address['company_id']) . "', payment_tax_id = '" . $this->db->escape($address['tax_id']) . "', payment_address_1 = '" . $this->db->escape($address['address_1']) . "', payment_address_2 = '" . $this->db->escape($address['address_2']) . "', payment_city = '" . $this->db->escape($address['city']) . "', payment_postcode = '" . $this->db->escape($address['postcode']) . "', payment_country = '" . $this->db->escape($address['country']) . "', payment_country_id = '" . (int)$address['country_id'] . "', payment_zone = '" . $this->db->escape($address['zone']) . "', payment_zone_id = '" . (int)$address['zone_id'] . "', shipping_firstname = '" . $this->db->escape($address['firstname']) . "', shipping_lastname = '" . $this->db->escape($address['lastname']) . "',  shipping_company = '" . $this->db->escape($address['company']) . "', shipping_address_1 = '" . $this->db->escape($address['address_1']) . "', shipping_address_2 = '" . $this->db->escape($address['address_2']) . "', shipping_city = '" . $this->db->escape($address['city']) . "', shipping_postcode = '" . $this->db->escape($address['postcode']) . "', shipping_country = '" . $this->db->escape($address['country']) . "', shipping_country_id = '" . (int)$address['country_id'] . "', shipping_zone = '" . $this->db->escape($address['zone']) . "', shipping_zone_id = '" . (int)$address['zone_id'] . "'";
					$json['hasAddress'] = 2;
					$json['country_id'] = $address['country_id'];
					$json['zone_id'] = $address['zone_id'];
					// add for edit address begin
					$json['order_address'] = $address;
					// add for edit address end
					break;
				}
			}
			$this->load->model('localisation/country');
			$json['customer_countries'] = $this->model_localisation_country->getCountries();
		}
		$sql .= " WHERE order_id = '" . (int)$order_id . "'";
		$this->db->query($sql);
		$this->language->load('module/pos');
		$json['success'] = $this->language->get('text_order_sucess');
		$this->response->setOutput(json_encode($json));	
	}
	
	// add for SKU begin
	public function handleSKUEntry() {
		$json = array();
		
		if (isset($this->request->get['sku'])) {
			$this->load->model('catalog/product');
			$this->load->model('catalog/option');
			
			$result = $this->getProductBySKU($this->request->get['sku']);
			
			if ($result) {
				$json['product_id'] = $result['product_id'];
				$json['name']       = strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'));
				$json['model']      = $result['model'];
				$json['price']      = $result['price'];

				$product_options = $this->model_catalog_product->getProductOptions($result['product_id']);	
				
				if (!empty($product_options)) {
					$option_data = array();
					
					foreach ($product_options as $product_option) {
						$option_info = $this->model_catalog_option->getOption($product_option['option_id']);
						
						if ($option_info) {				
							if ($option_info['type'] == 'select' || $option_info['type'] == 'radio' || $option_info['type'] == 'checkbox' || $option_info['type'] == 'image') {
								$option_value_data = array();
								
								foreach ($product_option['product_option_value'] as $product_option_value) {
									$option_value_name = '';
									if (version_compare(VERSION, '1.5.5', '<')) {
										$option_value_name = $product_option_value['name'];
									} else {
										$option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);
										if ($option_value_info) {
											$option_value_name = $option_value_info['name'];
										}
									}
							
									$option_value_data[] = array(
										'product_option_value_id' => $product_option_value['product_option_value_id'],
										'option_value_id'         => $product_option_value['option_value_id'],
										'name'                    => $option_value_name,
										'price'                   => (float)$product_option_value['price'] ? $this->currency->formatFront($product_option_value['price'], $this->config->get('config_currency')) : false,
										'price_prefix'            => $product_option_value['price_prefix']
									);
								}
							
								$option_data[] = array(
									'product_option_id' => $product_option['product_option_id'],
									'option_id'         => $product_option['option_id'],
									'name'              => $option_info['name'],
									'type'              => $option_info['type'],
									'option_value'      => $option_value_data,
									'required'          => $product_option['required']
								);	
							} else {
								$option_data[] = array(
									'product_option_id' => $product_option['product_option_id'],
									'option_id'         => $product_option['option_id'],
									'name'              => $option_info['name'],
									'type'              => $option_info['type'],
									'option_value'      => $product_option['option_value'],
									'required'          => $product_option['required']
								);				
							}
						}
					}
						
					$json['option']     = $option_data;
				}
				// add for Weight based price begin
				$this->handleWeightPrice($json, $result['weight_price'], $result['weight_name']);
				// add for Weight based price end
				
			}
		}

		$this->response->setOutput(json_encode($json));
	}
	
	private function getProductBySKU($sku) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.sku = '" . $this->db->escape($sku) . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
				
		return $query->row;
	}
	// add for SKU end
	// add for UPC begin
	public function handleUPCEntry() {
		$json = array();
		
		if (isset($this->request->get['upc'])) {
			$this->load->model('catalog/product');
			$this->load->model('catalog/option');
			
			$result = $this->getProductByUPC($this->request->get['upc']);
			
			if ($result) {
				$json['product_id'] = $result['product_id'];
				$json['name']       = strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'));
				$json['model']      = $result['model'];
				$json['price']      = $result['price'];

				$product_options = $this->model_catalog_product->getProductOptions($result['product_id']);
				
				if (!empty($product_options)) {
					$option_data = array();
					
					foreach ($product_options as $product_option) {
						$option_info = $this->model_catalog_option->getOption($product_option['option_id']);
						
						if ($option_info) {				
							if ($option_info['type'] == 'select' || $option_info['type'] == 'radio' || $option_info['type'] == 'checkbox' || $option_info['type'] == 'image') {
								$option_value_data = array();
								
								foreach ($product_option['product_option_value'] as $product_option_value) {
									$option_value_name = '';
									if (version_compare(VERSION, '1.5.5', '<')) {
										$option_value_name = $product_option_value['name'];
									} else {
										$option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);
										if ($option_value_info) {
											$option_value_name = $option_value_info['name'];
										}
									}
							
									$option_value_data[] = array(
										'product_option_value_id' => $product_option_value['product_option_value_id'],
										'option_value_id'         => $product_option_value['option_value_id'],
										'name'                    => $option_value_name,
										'price'                   => (float)$product_option_value['price'] ? $this->currency->formatFront($product_option_value['price'], $this->config->get('config_currency')) : false,
										'price_prefix'            => $product_option_value['price_prefix']
									);
								}
							
								$option_data[] = array(
									'product_option_id' => $product_option['product_option_id'],
									'option_id'         => $product_option['option_id'],
									'name'              => $option_info['name'],
									'type'              => $option_info['type'],
									'option_value'      => $option_value_data,
									'required'          => $product_option['required']
								);	
							} else {
								$option_data[] = array(
									'product_option_id' => $product_option['product_option_id'],
									'option_id'         => $product_option['option_id'],
									'name'              => $option_info['name'],
									'type'              => $option_info['type'],
									'option_value'      => $product_option['option_value'],
									'required'          => $product_option['required']
								);				
							}
						}
					}
						
					$json['option']     = $option_data;
				}
				// add for Weight based price begin
				$this->handleWeightPrice($json, $result['weight_price'], $result['weight_name']);
				// add for Weight based price end
			}
		}

		$this->response->setOutput(json_encode($json));
	}
	
	// add for Weight based price begin
	private function handleWeightPrice(&$json, $weight_price, $weight_name) {
		$json['weight_price'] = $weight_price;
		$json['weight_name'] = $weight_name;
		if ($weight_price) {
			$option_data = array();
			if (!empty($json['option'])) {
				$option_data = $json['option'];
			}
			$weight_option_data = array(
				'product_option_id' => '-1',
				'option_id'         => '-1',
				'name'              => 'pos_weight',
				'type'              => 'text',
				'option_value'      => '1',
				'required'          => '0'
			);
			array_push($option_data, $weight_option_data);
			$json['option'] = $option_data;
		}
	}
	// add for Weight based price end
	
	private function getProductByUPC($upc) {
		// add for upc/ean support begin
		if (strlen($upc) == 13) {
			$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.ean = '" . $upc . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		} else {
		// add for upc/ean support end
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.upc = '" . $upc . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
		// add for upc/ean support begin
		}
		// add for upc/ean support end
				
		return $query->row;
	}
	// add for UPC end
	// add for MPN begin
	public function handleMPNEntry() {
		$json = array();
		
		if (isset($this->request->get['mpn'])) {
			$this->load->model('catalog/product');
			$this->load->model('catalog/option');
			
			$result = $this->getProductByMPN($this->request->get['mpn']);
			
			if ($result) {
				$json['product_id'] = $result['product_id'];
				$json['name']       = strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'));
				$json['model']      = $result['model'];
				$json['price']      = $result['price'];

				$product_options = $this->model_catalog_product->getProductOptions($result['product_id']);	
				
				if (!empty($product_options)) {
					$option_data = array();
					
					foreach ($product_options as $product_option) {
						$option_info = $this->model_catalog_option->getOption($product_option['option_id']);
						
						if ($option_info) {				
							if ($option_info['type'] == 'select' || $option_info['type'] == 'radio' || $option_info['type'] == 'checkbox' || $option_info['type'] == 'image') {
								$option_value_data = array();
								
								foreach ($product_option['product_option_value'] as $product_option_value) {
									$option_value_name = '';
									if (version_compare(VERSION, '1.5.5', '<')) {
										$option_value_name = $product_option_value['name'];
									} else {
										$option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);
										if ($option_value_info) {
											$option_value_name = $option_value_info['name'];
										}
									}
							
									$option_value_data[] = array(
										'product_option_value_id' => $product_option_value['product_option_value_id'],
										'option_value_id'         => $product_option_value['option_value_id'],
										'name'                    => $option_value_name,
										'price'                   => (float)$product_option_value['price'] ? $this->currency->formatFront($product_option_value['price'], $this->config->get('config_currency')) : false,
										'price_prefix'            => $product_option_value['price_prefix']
									);
								}
							
								$option_data[] = array(
									'product_option_id' => $product_option['product_option_id'],
									'option_id'         => $product_option['option_id'],
									'name'              => $option_info['name'],
									'type'              => $option_info['type'],
									'option_value'      => $option_value_data,
									'required'          => $product_option['required']
								);	
							} else {
								$option_data[] = array(
									'product_option_id' => $product_option['product_option_id'],
									'option_id'         => $product_option['option_id'],
									'name'              => $option_info['name'],
									'type'              => $option_info['type'],
									'option_value'      => $product_option['option_value'],
									'required'          => $product_option['required']
								);				
							}
						}
					}
						
					$json['option']     = $option_data;
				}
				// add for Weight based price begin
				$this->handleWeightPrice($json, $result['weight_price'], $result['weight_name']);
				// add for Weight based price end
			}
		}

		$this->response->setOutput(json_encode($json));
	}
	
	private function getProductByMPN($mpn) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.mpn = '" . $mpn . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
				
		return $query->row;
	}
	// add for MPN end

	// add for Print begin
	public function receipt() {
		$this->language->load('sale/order');
		$this->load->model('pos/pos');

		$this->data['direction'] = $this->language->get('direction');
		$this->data['language'] = $this->language->get('code');
		$this->data['column_price'] = $this->language->get('column_price');
		$this->data['column_total'] = $this->language->get('column_total');

		$this->language->load('module/pos');
		$this->data['title'] = $this->language->get('print_title');
		$this->data['column_desc'] = $this->language->get('column_desc');
		$this->data['column_qty'] = $this->language->get('column_qty');
		$this->data['column_type'] = $this->language->get('column_type');
		$this->data['column_amount'] = $this->language->get('column_amount');
		$this->data['column_note'] = $this->language->get('column_note');
		$user = $this->user->getUserName();
		$this->data['user_info'] = sprintf($this->language->get('user_info'), $user);
		$this->data['term_n_cond'] = $this->language->get('term_n_cond_default');
		if ($this->config->get('p_term_n_cond')) {
			$this->data['term_n_cond'] = $this->config->get('p_term_n_cond');
		}
		
		$this->data['p_logo'] = $this->config->get('p_logo');
		$this->data['p_width'] = $this->config->get('p_width');
		$this->data['date'] = date($this->language->get('date_format_short'));
		$this->data['time'] = date($this->language->get('time_format'));

		$this->load->model('sale/order');

		$this->load->model('setting/setting');

		$this->data['order'] = array();
		if ($this->request->post['change']) {
			$this->data['change'] = $this->request->post['change'];
		}

		$orders = array();

		$order_id = $this->request->get['order_id'];
		$order_info = $this->model_sale_order->getOrder($order_id);

		if ($order_info) {
			$store_info = $this->model_setting_setting->getSetting('config', $order_info['store_id']);
			
			if ($store_info) {
				$store_address = $store_info['config_address'];
				$store_email = $store_info['config_email'];
				$store_telephone = $store_info['config_telephone'];
				$store_fax = $store_info['config_fax'];
			} else {
				$store_address = $this->config->get('config_address');
				$store_email = $this->config->get('config_email');
				$store_telephone = $this->config->get('config_telephone');
				$store_fax = $this->config->get('config_fax');
			}
			
			$product_data = array();

			$products = $this->model_sale_order->getOrderProducts($order_id);

			foreach ($products as $product) {
				$option_data = array();

				$options = $this->model_sale_order->getOrderOptions($order_id, $product['order_product_id']);

				// add for Weight based price begin
				$weight = 1;
				// add for Weight based price end
				foreach ($options as $option) {
					if ($option['type'] != 'file') {
						$value = $option['value'];
					} else {
						$value = utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.'));
						// add for Weight based price begin
						if ((int)$option['product_option_id'] == -1) {
							$weight = (float)$option['value'];
						}
						// add for Weight based price end
					}
					
					$option_data[] = array(
						'name'  => $option['name'],
						'value' => $value
					);								
				}

				// add for Abbreviation begin
				$abbreviation = html_entity_decode($product['name']);
				/*
				$this->load->model('catalog/product');
				$product_info = $this->model_catalog_product->getProduct($product['product_id']);
				if ($product_info && !empty($product_info['abbreviation'])) {
					$abbreviation = $product_info['abbreviation'];
				} else if (strlen($abbreviation) > 10) {
					$abbreviation = substr($abbreviation, 0, 10);
				}
				*/
				// add for Abbreviation end
				// add for serial no begin
				$sns = $this->model_pos_pos->getSoldProductSN($product['order_product_id']);
				// add for serial no end
				$product_data[] = array(
					// add for (update) Abbreviation begin
					// 'name'     => $product['name'],
					'name'     => $abbreviation,
					// add for (update) Abbreviation end
					'model'    => $product['model'],
					'option'   => $option_data,
					'quantity' => $product['quantity'],
					// add for serial no begin
					'sns'      => $sns,
					// add for serial no end
					'price'    => $this->currency->formatFront($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
					// add for (update) Weight based price begin
					// 'total'    => $this->currency->formatFront($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
					'total'    => $this->currency->formatFront($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity'] * $weight) : 0), $order_info['currency_code'], $order_info['currency_value'])
					// add for (update) Weight based price end
				);
			}
			
			$voucher_data = array();
			
			$vouchers = $this->model_sale_order->getOrderVouchers($order_id);

			foreach ($vouchers as $voucher) {
				$voucher_data[] = array(
					'description' => $voucher['description'],
					'amount'      => $this->currency->formatFront($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])			
				);
			}
				
			$total_data = $this->model_sale_order->getOrderTotals($order_id);

			$this->data['order'] = array(
				'order_id'	         => $order_id,
				'store_name'         => $order_info['store_name'],
				'store_address'      => nl2br($store_address),
				'store_email'        => $store_email,
				'store_telephone'    => $store_telephone,
				'store_fax'          => $store_fax,
				'email'              => $order_info['email'],
				'telephone'          => $order_info['telephone'],
				'product'            => $product_data,
				'voucher'            => $voucher_data,
				'total'              => $total_data,
			);
			
			$this->data['payments'] = array();
			
			$this->load->model('pos/pos');
			$order_payments = $this->model_pos_pos->retrieveOrderPayments($order_id);
			if ($order_payments) {
				foreach ($order_payments as $order_payment) {
					$this->data['payments'][] = array (
						'type'   => $order_payment['payment_type'],
						'amount' => $this->currency->formatFront($order_payment['tendered_amount'], $order_info['currency_code'], $order_info['currency_value']),
						'note'   => $order_payment['payment_note']
					);
				}
			}
		}	
		
		// $today = date("YmdHis");
		// $today = date("Ymd");
		// $barcode_text = $today.$order_id;
		$barcode_text = $order_id;
		$img_file = DIR_APPLICATION . 'view/image/pos/' . $barcode_text . '.png';
		$this->createBarcode($barcode_text, false, $img_file);
		$this->data['img_file'] = 'view/image/pos/' . $barcode_text . '.png';
		$this->data['barcode_text'] = $barcode_text;
		
		$this->language->load('module/pos');
		$this->data['text_change'] = $this->language->get('text_change');

		$this->template = 'pos/receipt.tpl';

		$this->response->setOutput($this->render());
	}
	
	private function createBarcode($text='', $showText=false, $fileName=null, $fmode='png', $height=20, $thin=1, $thick=2, $fSize=2) {
		$bcHeight = $height;
		$bcThinWidth = $thin;
		$bcThickWidth = $bcThinWidth * $thick;
		$fontSize = $fSize;
		$mode = $fmode;
		$outMode = array('gif'=>'gif', 'png'=>'png', 'jpeg'=>'jpeg', 'wbmp'=>'vnd.wap.wbmp');
		$codeMap = array(
			'0'=>'010000101',	'1'=>'100100001',	'2'=>'001100001',	'3'=>'101100000',
			'4'=>'000110001',	'5'=>'100110000',	'6'=>'001110000',	'7'=>'000100101',	
			'8'=>'100100100',	'9'=>'001100100',	'A'=>'100001001',	'B'=>'001001001',
			'C'=>'101001000',	'D'=>'000011001',	'E'=>'100011000',	'F'=>'001011000',
			'G'=>'000001101',	'H'=>'100001100',	'I'=>'001001100',	'J'=>'000011100',
			'K'=>'100000011',	'L'=>'001000011',	'M'=>'101000010',	'N'=>'000010011',
			'O'=>'100010010',	'P'=>'001010010',	'Q'=>'000000111',	'R'=>'100000110',
			'S'=>'001000110',	'T'=>'000010110',	'U'=>'110000001',	'V'=>'011000001',
			'W'=>'111000000',	'X'=>'010010001',	'Y'=>'110010000',	'Z'=>'011010000',
			' '=>'011000100',	'$'=>'010101000',	'%'=>'000101010',	'*'=>'010010100',
			'+'=>'010001010',	'-'=>'000110100',	'.'=>'110000100',	'/'=>'010100010'
			);
		
		if (trim($text) <= ' ') {
			throw new exception('createBarcode - must be passed text to operate');
		}
		if (!$fileType = $outMode[$mode]) {
			throw new exception("createBarcode - unrecognized output format ({$mode})");
		}
		if (!function_exists("image{$mode}")) 
			throw new exception("createBarcode - unsupported output format ({$mode} - check phpinfo)");
		
		$text  =  strtoupper($text);
		$dispText = "* $text *";
		$text = "*$text*"; // adds start and stop chars
		$textLen  =  strlen($text); 
		$barcodeWidth  =  $textLen * (7 * $bcThinWidth + 3 * $bcThickWidth) - $bcThinWidth; 
		$im = imagecreate($barcodeWidth, $bcHeight); 
		$black = imagecolorallocate($im, 0, 0, 0); 
		$white = imagecolorallocate($im, 255, 255, 255); 
		imagefill($im, 0, 0, $white); 
		
		$xpos = 0;
		for ($idx=0; $idx<$textLen; $idx++) { 
			if (!$char = $text[$idx]) $char = '-';
			for ($ptr=0; $ptr<=8; $ptr++)
			{ 
				$elementWidth = ($codeMap[$char][$ptr]) ? $bcThickWidth : $bcThinWidth; 
				if (($ptr + 1) % 2) 
					imagefilledrectangle($im, $xpos, 0, $xpos + $elementWidth-1, $bcHeight, $black); 
				$xpos += $elementWidth; 
			}
			$xpos += $bcThinWidth; 
		}
		
		if ($showText)
		{
			$pxWid = imagefontwidth($fontSize) * strlen($dispText) + 10;
			$pxHt = imagefontheight($fontSize) + 2;
			$bigCenter = $barcodeWidth / 2;
			$textCenter = $pxWid / 2;
			imagefilledrectangle($im, $bigCenter - $textCenter, $bcHeight - $pxHt, $bigCenter + $textCenter, $bcHeight, $white);
			imagestring($im, $fontSize, ($bigCenter - $textCenter) + 5, ($bcHeight - $pxHt) + 1, $dispText, $black);
		}

		$badMode = false;
		if (!$fileName) header("Content-type:  image/{$fileType}");
		switch($mode)
		{
			case 'gif': 
				imagegif($im, $fileName);
				break;
			case 'png': 
				imagepng($im, $fileName);
				break;
			case 'jpeg': 
				imagejpeg($im, $fileName);
				break;
			case 'wbmp': 
				imagewbmp($im, $fileName);
				break;
			default:
				$badMode = true;
		}

		imagedestroy($im); 		
		if ($badMode) 
			throw new Exception("barCode: Unknown Graphics Type '{$mode}'");
	}
	// add for Print end
	// add for Discount begin
	public function applyDiscount() {
		$this->language->load('module/pos');
		$json = array();
		
		// check if the pos_discount is installed and enabled
		$installed = false;
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "extension WHERE type = 'total' AND code = 'pos_discount'");
		if ($query->row) {
			$installed = true;
		}
		if ($installed && $this->config->get('pos_discount_status')) {
		
			// update order to set the discount total
			$this->load->model('sale/order');
			$order_info = $this->model_sale_order->getOrder($this->request->post['order_id']);
			
			if ($order_info) {
				$text = $this->currency->formatFront($this->request->post['value'], $order_info['currency_code'], $order_info['currency_value']);
				$sort_order = $this->config->get('pos_discount_sort_order');
				// check if discount is already in place
				$sqlQuery = "SELECT order_total_id, value FROM `" . DB_PREFIX . "order_total` WHERE order_id='" . (int)$this->request->post['order_id'] . "' AND code in ('pos_discount', 'pos_discount_subtotal', 'pos_discount_total')";
				$query = $this->db->query($sqlQuery);
				if ($query->row) {
					// already there, update the record
					$order_total_id = $query->row['order_total_id'];
					$total = $this->request->post['total'] + $this->request->post['value'] - $query->row['value'];
					$this->db->query("UPDATE `" . DB_PREFIX . "order_total` SET code='" . $this->request->post['code'] . "', title='" . $this->db->escape($this->request->post['title']) . "', text='" . $this->db->escape($text) . "', value='" . $this->request->post['value'] . "', sort_order='" . $sort_order . "' WHERE order_total_id='" . $order_total_id . "'");
				} else {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "order_total` SET order_id='" . (int)$this->request->post['order_id'] . "', code='" . $this->request->post['code'] . "', title='" . $this->db->escape($this->request->post['title']) . "', text='" . $this->db->escape($text) . "', value='" . $this->request->post['value'] . "', sort_order='" . $sort_order . "'");
					$total = $this->request->post['total'] + $this->request->post['value'];
				}
				$text_total = $this->currency->formatFront($total, $order_info['currency_code'], $order_info['currency_value']);
				$this->db->query("UPDATE `" . DB_PREFIX . "order_total` SET value='" . (float)$total . "', text='" . $text_total . "', sort_order='" . ((int)$sort_order+1) . "' WHERE order_id='" . (int)$this->request->post['order_id'] . "' AND code='total'");
				// update total in order table as well
				$this->db->query("UPDATE `" . DB_PREFIX . "order` SET total='" . (float)$total . "' WHERE order_id='" . (int)$this->request->post['order_id'] . "'");
				// set the value into session
				$discount = array (
					'order_id' => $this->request->post['order_id'],
					'code' => $this->request->post['code'],
					'title' => $this->request->post['title'],
					'text' => $text,
					'value' => $this->request->post['value']
				);
				$this->session->data['pos_discount'] = $discount;
				$json['totals'] = $this->model_sale_order->getOrderTotals($this->request->post['order_id']);
				$json['success'] = $this->language->get('text_order_sucess');
			} else {
				$json['error'] = sprintf($this->language->get('error_discount_order_not_exist'), $this->request->post['order_id']);
			}
		} else {
			$json['error'] = $this->language->get('error_discount_not_installed');
		}
	
		$this->response->setOutput(json_encode($json));
	}
	
	public function getDiscount() {
		$this->language->load('module/pos');
		$json = array();
		
		// update order to set the discount total
		$this->load->model('sale/order');
		$order_info = $this->model_sale_order->getOrder($this->request->post['order_id']);
		
		if ($order_info) {
			// check if discount is already in place
			$sqlQuery = "SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id='" . (int)$this->request->post['order_id'] . "'";
			$rows = $this->db->query($sqlQuery)->rows;
			foreach ($rows as $row) {
				if ($row['code'] == 'total') {
					$json['total_value'] = $row['value'];
				} elseif ($row['code'] == 'sub_total') {
					$json['subtotal_value'] = $row['value'];
				} elseif ($row['code'] == 'pos_discount' || $row['code'] == 'pos_discount_subtotal' || $row['code'] == 'pos_discount_total') {
					$json['pos_discount'] = $row;
				}
			}
			$json['currency_symbol'] = $this->currency->getSymbolLeft($order_info['currency_code']);
			if ($json['currency_symbol'] == '') {
				$json['currency_symbol'] = $this->currency->getSymbolRight($order_info['currency_code']);
			}
			if (isset($json['pos_discount'])) {
				$json['total_value'] -= $json['pos_discount']['value'];
			}
		} else {
			$json['error'] = sprintf($this->language->get('error_discount_order_not_exist'), $this->request->post['order_id']);
		}
	
		$this->response->setOutput(json_encode($json));
	}
	// add for Discount end
	// add for User as Affiliate begin
	public function addUA() {
		// add a record to user_affiliate mappint table
		$this->load->model('pos/pos');
		$this->model_pos_pos->addUA($this->request->post);
	}
	public function deleteUA() {
		// add a record to user_affiliate mappint table
		$this->load->model('pos/pos');
		$this->model_pos_pos->deleteUA($this->request->post);
	}
	// add for User as Affiliate end
	// add for Add Customer begin
	public function createEmptyCustomer() {
		$this->db->query("INSERT INTO " . DB_PREFIX . "customer SET firstname = '', lastname = '', email = '', telephone = '', fax = '', newsletter = '0', customer_group_id = '1', salt = '" . $this->db->escape($salt = substr(md5(uniqid(rand(), true)), 0, 9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1('pa55w0rd')))) . "', status = '1', date_added = NOW()");
		$this->load->model('sale/customer');
		$customer_info = $this->model_sale_customer->getCustomer($this->db->getLastId());
		$json['customer_info'] = $customer_info;
		$this->load->model('localisation/country');
		$json['customer_countries'] = $this->model_localisation_country->getCountries();
		$this->response->setOutput(json_encode($json));
	}
	public function removeEmptyCustomer() {
		if (isset($this->request->get['customer_id'])) {
			$this->load->model('sale/customer');
			$this->model_sale_customer->deleteCustomer($this->request->get['customer_id']);
		}
	}
	// add for Add Customer end
	// add for edit order address begin
	public function saveOrderAddresses() {
		$this->load->model('pos/pos');
		$this->request->post['shipping_country_id'] = $this->request->post['shipping_']['country_id'];
		$this->request->post['payment_country_id'] = $this->request->post['payment_']['country_id'];
		$this->model_pos_pos->editOrderAddresses($this->request->get['order_id'], $this->request->post);
		$this->response->setOutput(json_encode(array()));
	}
	// add for edit order address end
	// add for Quotation begin
	public function addQuoteStatus() {
		$json = array();
		
		$this->load->model('pos/pos');
		$status_id = $this->model_pos_pos->addQuoteStatus($this->request->post['status']);
		if ($status_id) {
			$json['quote_status_id'] = $status_id;
		} else {
			$this->language->load('module/pos');
			$json['error'] = $this->language->get('text_quote_status_already_exist');
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function renameQuoteStatus() {
		$json = array();
		
		$this->load->model('pos/pos');
		$result = $this->model_pos_pos->renameQuoteStatus($this->request->post['status_id'], $this->request->post['status']);
		if (! $result) {
			$this->language->load('module/pos');
			$json['error'] = $this->language->get('text_quote_status_already_exist');
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function deleteQuoteStatus() {
		$json = array();
		
		$this->load->model('pos/pos');
		$order_ids = $this->model_pos_pos->deleteQuoteStatus($this->request->post['status_id']);
		if (! empty($order_ids)) {
			$order_id_s = implode(",", $order_ids);
			$this->language->load('module/pos');
			$json['error'] = sprintf($this->language->get('text_quote_status_in_use'), $order_id_s);
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function convertQuote2Order() {
		$json = array();
		
		$this->load->model('pos/pos');
		$new_order_id = $this->model_pos_pos->convertQuote2Order($this->request->post['order_id']);
		$this->language->load('module/pos');
		$json['success'] = $this->language->get('text_quote_converted');
		$json['order_id'] = $this->getOrderIdText($new_order_id);
		$this->language->load('sale/order');
		$json['order_text'] = $this->language->get('text_order_id');
		
		$this->response->setOutput(json_encode($json));
	}
	// add for Quotation end
	// add for Empty order control begin
	public function deleteEmptyOrdersPOST() {
		$json = array();
		$this->deleteEmptyOrders($this->request->post['no_product'], $this->request->post['initial_status'], $this->request->post['delete_status']);
		$json['success'] = 1;
		$this->response->setOutput(json_encode($json));
	}
	
	private function deleteEmptyOrders($no_product, $initial_status, $delete_status) {
		$this->language->load('module/pos');
		if ($initial_status) {
			$initial_status = $this->language->get('text_status_initial');
		}
		if ($delete_status) {
			$delete_status = $this->language->get('text_status_deleted');
		}
		$this->load->model('pos/pos');
		$this->model_pos_pos->deleteEmptyOrders($no_product, $initial_status, $delete_status);
	}
	// add for Empty order control end
	// add for Browse begin
	public function getCategoryTree() {
		// get the category tree in the catalog database
		$this->load->model('pos/pos');
		$categories = $this->model_pos_pos->getCategories();
		// convert the array to an tree-like array
		$category_tree = array();
		$parent_id_list = array();
		foreach ($categories as $category) {
			$parent_id_list[] = $category['parent_id'];
		}
		$this->convert2Tree($categories, $parent_id_list, $category_tree);
		
		$json = array();
		$json['category_tree'] = $category_tree;
		$this->response->setOutput(json_encode($json));
	}
	
	private function getCategoryItems($parent_category_id, $currency_code, $currency_value) {
		// get the direct sub-category and product in the given category
		$this->load->model('pos/pos');
		$sub_categories = $this->model_pos_pos->getSubCategories($parent_category_id);
		$products = $this->model_pos_pos->getProducts($parent_category_id);
		
		$this->language->load('module/pos');
		$browse_items = array();
		foreach ($sub_categories as $sub_category) {
			$browse_items[] = array('type' => 'C',
								'name' => $sub_category['name'],
								'image' => !empty($sub_category['image']) ? '../image/'.$sub_category['image'] : 'view/image/pos/no_image.jpg',
								'id' => $sub_category['category_id']);
		}
		foreach ($products as $product) {
			$browse_items[] = array('type' => 'P',
								'name' => $product['name'],
								'image' => !empty($product['image']) ? '../image/'.$product['image'] : 'view/image/pos/no_image.jpg',
								'price_text' => $this->currency->formatFront($product['price'], $currency_code, $currency_value),
								'stock_text' => $product['quantity'], // . ' ' . $this->language->get('text_remaining'),
								// add for (update) Weight based price begin
								// 'hasOptions' => $product['options'] ? '1' : '0',
								'hasOptions' => ($product['options'] || $product['weight_price']) ? '1' : '0',
								// add for (update) Weight based price end
								'id' => $product['product_id']);
		}
		
		return $browse_items;
	}
	
	public function getCategoryItemsAjax() {
		$parent_category_id = 0;
		if (isset($this->request->post['category_id'])) {
			$parent_category_id = $this->request->post['category_id'];
		}
		
		$json = array();
		$json['browse_items'] = $this->getCategoryItems($parent_category_id, $this->request->post['currency_code'], $this->request->post['currency_value']);
		// the above step already has model pos/pos include
		if (version_compare(VERSION, '1.5.5', '<')) {
			$category_path = $this->model_pos_pos->getCategoryFullPathOld($parent_category_id);
		} else {
			$category_path = $this->model_pos_pos->getCategoryFullPath($parent_category_id);
			if ($category_path) {
				$category_path = $category_path['name'];
			}
		}
		$json['path'] = array();
		if ($category_path) {
			$pathes = explode('!|||!', $category_path);
			$json['path'] = array();
			foreach ($pathes as $path) {
				$names = explode('|||', $path);
				$json['path'][] = array('id' => $names[0], 'name' => $names[1]);
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	private function convert2Tree($categories, $parent_id_list, &$parent_category, $parent_id = 0) {
		// find the sub categories under the given parent category with id $parent_id
		foreach ($categories as $category) {
			if ($category['parent_id'] == $parent_id) {
				// add it into the parent category array
				$category_names = explode(' &gt; ', $category['name']);
				$category_name = $category_names[sizeof($category_names)-1];
				$sub_category = array();
				if (in_array($category['category_id'], $parent_id_list)) {
					// the category still has sub categories
					$this->convert2Tree($categories, $parent_id_list, $sub_category, $category['category_id']);
				}
				array_push($parent_category, array('id' => $category['category_id'], 'name' => $category_name, 'subs' => $sub_category));
			}
		}
	}
	
	public function getProductOptions() {
		$json = array();
		$option_data = array();
		
		$this->load->model('catalog/product');
		$this->load->model('catalog/option');
		$product_options = $this->model_catalog_product->getProductOptions($this->request->get['product_id']);
		// add for Weight based price begin
		$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
		if ($product_info['weight_price']) {
			$json['weight_price'] = $product_info['weight_price'];
			$json['weight_name'] = $product_info['weight_name'];
			$option_data[] = array(
				'product_option_id' => '-1',
				'option_id'         => '-1',
				'name'              => 'pos_weight',
				'type'              => 'text',
				'option_value'      => '1',
				'required'          => '0'
			);				
		}
		// add for Weight based price end
		
		foreach ($product_options as $product_option) {
			$option_info = $this->model_catalog_option->getOption($product_option['option_id']);
			
			if ($option_info) {				
				if ($option_info['type'] == 'select' || $option_info['type'] == 'radio' || $option_info['type'] == 'checkbox' || $option_info['type'] == 'image') {
					$option_value_data = array();
					
					foreach ($product_option['product_option_value'] as $product_option_value) {
						$option_value_name = '';
						if (version_compare(VERSION, '1.5.5', '<')) {
							$option_value_name = $product_option_value['name'];
						} else {
							$option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);
							if ($option_value_info) {
								$option_value_name = $option_value_info['name'];
							}
						}
				
						$option_value_data[] = array(
							'product_option_value_id' => $product_option_value['product_option_value_id'],
							'option_value_id'         => $product_option_value['option_value_id'],
							'name'                    => $option_value_name,
							'price'                   => (float)$product_option_value['price'] ? $this->currency->formatFront($product_option_value['price'], $this->config->get('config_currency')) : false,
							'price_prefix'            => $product_option_value['price_prefix']
						);
					}
				
					$option_data[] = array(
						'product_option_id' => $product_option['product_option_id'],
						'option_id'         => $product_option['option_id'],
						'name'              => $option_info['name'],
						'type'              => $option_info['type'],
						'option_value'      => $option_value_data,
						'required'          => $product_option['required']
					);	
				} else {
					$option_data[] = array(
						'product_option_id' => $product_option['product_option_id'],
						'option_id'         => $product_option['option_id'],
						'name'              => $option_info['name'],
						'type'              => $option_info['type'],
						'option_value'      => $product_option['option_value'],
						'required'          => $product_option['required']
					);				
				}
			}
		}
		
		$json['option_data'] = $option_data;
		$this->response->setOutput(json_encode($json));
	}
	// add for Browse end
	// add for Quick sale begin
	public function updateQSProduct() {
		$data = array();
		$keys = array_keys($this->request->post);
		foreach ($keys as $key) {
			$value = $this->request->post[$key];
			if (strpos($key, 'quick_sale_') === 0) {
				$dataKey = substr($key, 11);
				$data[$dataKey] = $value;
			}
		}
		$this->load->model('pos/pos');
		$product_id = $this->model_pos_pos->updateQSProduct($data);
		
		$json = array();
		if ($product_id > 0) {
			$json['product_id'] = $product_id;
		}
		
		$this->response->setOutput(json_encode($json));
	}
	// add for Quick sale end
	
	// add for status change notification begin
	private function sendNotification($order_id, $order_status_id) {
		$this->load->model('sale/order');
		$order_info = $this->model_sale_order->getOrder($order_id);
		 
		if ($order_info) {
			$order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
			
			// Downloads
			$order_download_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int)$order_id . "'");
			
			// Order Totals			
			$order_total_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order ASC");
			
			// Send out order confirmation mail
			$order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$order_info['language_id'] . "'");
			
			if ($order_status_query->num_rows) {
				$order_status = $order_status_query->row['name'];	
			} else {
				$order_status = '';
			}
			
			$subject = sprintf($this->language->get('text_new_subject'), $order_info['store_name'], $order_id);
		
			// HTML Mail
			$template = new Template();
			
			$template->data['title'] = sprintf($this->language->get('text_new_subject'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'), $order_id);
			
			$template->data['text_greeting'] = sprintf($this->language->get('text_new_greeting'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
			$template->data['text_link'] = $this->language->get('text_new_link');
			$template->data['text_download'] = $this->language->get('text_new_download');
			$template->data['text_order_detail'] = $this->language->get('text_new_order_detail');
			$template->data['text_instruction'] = $this->language->get('text_new_instruction');
			$template->data['text_order_id'] = $this->language->get('text_new_order_id');
			$template->data['text_date_added'] = $this->language->get('text_new_date_added');
			$template->data['text_payment_method'] = $this->language->get('text_new_payment_method');	
			$template->data['text_shipping_method'] = $this->language->get('text_new_shipping_method');
			$template->data['text_email'] = $this->language->get('text_new_email');
			$template->data['text_telephone'] = $this->language->get('text_new_telephone');
			$template->data['text_ip'] = $this->language->get('text_new_ip');
			$template->data['text_payment_address'] = $this->language->get('text_new_payment_address');
			$template->data['text_shipping_address'] = $this->language->get('text_new_shipping_address');
			$template->data['text_product'] = $this->language->get('text_new_product');
			$template->data['text_model'] = $this->language->get('text_new_model');
			$template->data['text_quantity'] = $this->language->get('text_new_quantity');
			$template->data['text_price'] = $this->language->get('text_new_price');
			$template->data['text_total'] = $this->language->get('text_new_total');
			$template->data['text_footer'] = $this->language->get('text_new_footer');
			$template->data['text_powered'] = $this->language->get('text_new_powered');
			
			$template->data['logo'] = $this->config->get('config_url') . 'image/' . $this->config->get('config_logo');		
			$template->data['store_name'] = $order_info['store_name'];
			$template->data['store_url'] = $order_info['store_url'];
			$template->data['customer_id'] = $order_info['customer_id'];
			$template->data['link'] = $order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_id;
			
			if ($order_download_query->num_rows) {
				$template->data['download'] = $order_info['store_url'] . 'index.php?route=account/download';
			} else {
				$template->data['download'] = '';
			}
			
			$template->data['order_id'] = $order_id;
			$template->data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));    	
			$template->data['payment_method'] = $order_info['payment_method'];
			$template->data['shipping_method'] = $order_info['shipping_method'];
			$template->data['email'] = $order_info['email'];
			$template->data['telephone'] = $order_info['telephone'];
			$template->data['ip'] = $order_info['ip'];
			$template->data['comment'] = '';
						
			if ($order_info['payment_address_format']) {
				$format = $order_info['payment_address_format'];
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
			}
			
			$find = array(
				'{firstname}',
				'{lastname}',
				'{company}',
				'{address_1}',
				'{address_2}',
				'{city}',
				'{postcode}',
				'{zone}',
				'{zone_code}',
				'{country}'
			);
		
			$replace = array(
				'firstname' => $order_info['payment_firstname'],
				'lastname'  => $order_info['payment_lastname'],
				'company'   => $order_info['payment_company'],
				'address_1' => $order_info['payment_address_1'],
				'address_2' => $order_info['payment_address_2'],
				'city'      => $order_info['payment_city'],
				'postcode'  => $order_info['payment_postcode'],
				'zone'      => $order_info['payment_zone'],
				'zone_code' => $order_info['payment_zone_code'],
				'country'   => $order_info['payment_country']  
			);
		
			$template->data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));						
									
			if ($order_info['shipping_address_format']) {
				$format = $order_info['shipping_address_format'];
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
			}
			
			$find = array(
				'{firstname}',
				'{lastname}',
				'{company}',
				'{address_1}',
				'{address_2}',
				'{city}',
				'{postcode}',
				'{zone}',
				'{zone_code}',
				'{country}'
			);
		
			$replace = array(
				'firstname' => $order_info['shipping_firstname'],
				'lastname'  => $order_info['shipping_lastname'],
				'company'   => $order_info['shipping_company'],
				'address_1' => $order_info['shipping_address_1'],
				'address_2' => $order_info['shipping_address_2'],
				'city'      => $order_info['shipping_city'],
				'postcode'  => $order_info['shipping_postcode'],
				'zone'      => $order_info['shipping_zone'],
				'zone_code' => $order_info['shipping_zone_code'],
				'country'   => $order_info['shipping_country']  
			);
		
			$template->data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));
			
			// Products
			$template->data['products'] = array();
				
			foreach ($order_product_query->rows as $product) {
				$option_data = array();
				
				$order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$product['order_product_id'] . "'");
				
				foreach ($order_option_query->rows as $option) {
					if ($option['type'] != 'file') {
						$value = $option['value'];
					} else {
						$value = utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.'));
					}
					
					$option_data[] = array(
						'name'  => $option['name'],
						'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
					);					
				}
			  
				$template->data['products'][] = array(
					'name'     => $product['name'],
					'model'    => $product['model'],
					'option'   => $option_data,
					'quantity' => $product['quantity'],
					'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
					'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
				);
			}
		
			$template->data['totals'] = $order_total_query->rows;
			
			$html = $template->fetch('mail/order.tpl');
			
			// Text Mail
			$text  = sprintf($this->language->get('text_new_greeting'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8')) . "\n\n";
			$text .= $this->language->get('text_new_order_id') . ' ' . $order_id . "\n";
			$text .= $this->language->get('text_new_date_added') . ' ' . date($this->language->get('date_format_short'), strtotime($order_info['date_added'])) . "\n";
			$text .= $this->language->get('text_new_order_status') . ' ' . $order_status . "\n\n";
			
			// Products
			$text .= $this->language->get('text_new_products') . "\n";
			
			foreach ($order_product_query->rows as $product) {
				$text .= $product['quantity'] . 'x ' . $product['name'] . ' (' . $product['model'] . ') ' . html_entity_decode($this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']), ENT_NOQUOTES, 'UTF-8') . "\n";
				
				$order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . $product['order_product_id'] . "'");
				
				foreach ($order_option_query->rows as $option) {
					$text .= chr(9) . '-' . $option['name'] . ' ' . (utf8_strlen($option['value']) > 20 ? utf8_substr($option['value'], 0, 20) . '..' : $option['value']) . "\n";
				}
			}
			
			$text .= "\n";
			
			$text .= $this->language->get('text_new_order_total') . "\n";
			
			foreach ($order_total_query->rows as $total) {
				$text .= $total['title'] . ': ' . html_entity_decode($total['text'], ENT_NOQUOTES, 'UTF-8') . "\n";
			}			
			
			$text .= "\n";
			
			if ($order_info['customer_id']) {
				$text .= $this->language->get('text_new_link') . "\n";
				$text .= $order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_id . "\n\n";
			}
		
			if ($order_download_query->num_rows) {
				$text .= $this->language->get('text_new_download') . "\n";
				$text .= $order_info['store_url'] . 'index.php?route=account/download' . "\n\n";
			}
			
			// Comment
			if ($order_info['comment']) {
				$text .= $this->language->get('text_new_comment') . "\n\n";
				$text .= $order_info['comment'] . "\n\n";
			}

			$text .= $this->language->get('text_new_footer') . "\n\n";
		
			$mail = new Mail(); 
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->hostname = $this->config->get('config_smtp_host');
			$mail->username = $this->config->get('config_smtp_username');
			$mail->password = $this->config->get('config_smtp_password');
			$mail->port = $this->config->get('config_smtp_port');
			$mail->timeout = $this->config->get('config_smtp_timeout');
			$mail->setTo($order_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($order_info['store_name']);
			$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
			$mail->setHtml($html);
			$mail->setText(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
			$mail->send();
		}
	}
	// add for status change notification end
	// add for location based stock begin
	public function addLocation() {
		$json = array();
		
		$this->load->model('pos/pos');
		$location_id = $this->model_pos_pos->addLocation($this->request->post);
		if ($location_id) {
			$json['location_id'] = $location_id;
		} else {
			$this->language->load('module/pos');
			$json['error'] = $this->language->get('text_location_already_exist');
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function deleteLocation() {
		$json = array();
		
		$this->load->model('pos/pos');
		$this->model_pos_pos->deleteLocation($this->request->post['location_id']);
		
		$this->response->setOutput(json_encode($json));
	}
	// add for location based stock end
	// add for table management begin
	public function addTable() {
		$json = array();
		
		$this->load->model('pos/pos');
		$table_id = $this->model_pos_pos->addTable($this->request->post);
		if ($table_id) {
			$json['table_id'] = $table_id;
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function deleteTable() {
		$json = array();
		
		$this->load->model('pos/pos');
		$this->model_pos_pos->deleteTable($this->request->post);
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function addTableBatch() {
		$json = array();
		
		$this->load->model('pos/pos');
		$json['table_ids'] = $this->model_pos_pos->addTableBatch($this->request->post);
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function deleteTableBatch() {
		$json = array();
		
		$this->load->model('pos/pos');
		$this->model_pos_pos->deleteTableBatch($this->request->post);
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function saveOrderTableId() {
		$order_id = $this->request->post['order_id'];
		$table_id = $this->request->post['table_id'];
		$this->load->model('pos/pos');
		$this->model_pos_pos->saveOrderTableId($order_id, $table_id);

		$this->language->load('module/pos');
		$json['success'] = $this->language->get('text_order_sucess');
		
		$this->response->setOutput(json_encode($json));	
	}
	// add for table management end
	// add for serial no begin
	public function saveProductSN() {
		$json = array();
		$this->language->load('module/pos');
		if ($this->request->post['product_id'] && !empty($this->request->post['product_sn'])) {
			$this->load->model('pos/pos');
			$duplicates = $this->model_pos_pos->addProductSN($this->request->post['product_id'], $this->request->post['product_sn']);	
			if (!empty($duplicates)) {
				$json['error'] = sprintf($this->language->get('text_duplicated_sn'), implode($duplicates, "\n"));
			}
			$json['success'] = sprintf($this->language->get('text_add_sn_success'), sizeof($this->request->post['product_sn'])-sizeof($duplicates));
		} else {
			$json['success'] = sprintf($this->language->get('text_add_sn_success'), 0);
		}
		$this->response->setOutput(json_encode($json));
	}
	public function getProductSN() {
		$this->load->model('pos/pos');
		$this->language->load('module/pos');
		$json = $this->model_pos_pos->getProductSN($this->request->post);
		foreach ($json as $key => $value) {
			if ($value['status'] == 1) {
				$json[$key]['status'] = $this->language->get('text_sn_in_store');
			} elseif ($value['status'] == 2) {
				$json[$key]['status'] = $this->language->get('text_sn_sold') . sprintf($this->language->get('text_sold_info'), $value['order_id']);
			}
		}
		$this->response->setOutput(json_encode($json));
	}
	public function deleteProductSN() {
		$json = array();
		if ($this->request->post['product_sn_id']) {
			$this->load->model('pos/pos');
			$this->model_pos_pos->deleteProductSN($this->request->post['product_sn_id']);
		}
		$this->response->setOutput(json_encode($json));
	}
	public function sn_autocomplete() {
		$json = array();
		
		if (isset($this->request->get['filter_sn']) && isset($this->request->get['filter_product_id'])) {
			$this->load->model('pos/pos');
			
			$data = array(
				'product_id' => $this->request->get['filter_product_id'],
				'sn'       => $this->request->get['filter_sn'],
				'status' => '1'
			);
		
			$results = $this->model_pos_pos->getProductSN($data);
			
			foreach ($results as $result) {
				$json[] = array(
					'name'       => $result['sn'], 
					'product_sn_id' => $result['product_sn_id']
				);					
			}
		}

		$sort_order = array();
	  
		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->setOutput(json_encode($json));
	}
	// add for serial no end
	// add for commission begin
	public function commission_autocomplete() {
		$json = array();
		
		if (isset($this->request->get['filter_name'])) {
			$this->load->model('pos/pos');
			
			$data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 20
			);
		
			$results = $this->model_pos_pos->getCommissions($data);
			
			foreach ($results as $result) {
				$json[] = array(
					'product_id'        => $result['product_id'], 
					'product_name'      => $result['name'],
					'commission_type'   => $result['type'],
					'commission_value'  => $result['value'],
					'commission_base'   => $result['base']
				);					
			}
		}

		$sort_order = array();
	  
		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['product_name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->setOutput(json_encode($json));
	}
	public function saveCommission() {
		$json = array();
		$this->language->load('module/pos');
		if ($this->request->post['product_id'] && !empty($this->request->post['type'])) {
			$this->load->model('pos/pos');
			$this->model_pos_pos->addProductCommission($this->request->post['product_id'], $this->request->post['type'], $this->request->post['value'], $this->request->post['base']);	
		}
		$this->response->setOutput(json_encode($json));
	}
	public function getProductCommissions() {
		$this->load->model('pos/pos');
		$this->language->load('module/pos');
		$json = $this->model_pos_pos->getCommissions($this->request->post);
		foreach ($json as $key => $commission) {
			if ($commission['type']) {
				if ($commission['type'] == 1) {
					$json[$key]['commission'] = $this->language->get('entry_commission_fixed') . $commission['value'];
				} else {
					$json[$key]['commission'] = $this->language->get('entry_commission_percentage') . $commission['value'] . ' ' . $this->language->get('text_commission_percentage_base') . ' ' . $commission['base'];
				}
			} else {
				unset($json[$key]);
			}
		}
		$this->response->setOutput(json_encode($json));
	}
	public function deleteProductCommission() {
		$json = array();
		if ($this->request->post['product_id']) {
			$this->load->model('pos/pos');
			$this->model_pos_pos->deleteProductCommission($this->request->post['product_id']);
		}
		$this->response->setOutput(json_encode($json));
	}
	// add for commission end
	public function image() {
		if (isset($this->request->get['image'])) {
			$filename = $this->request->get['image'];
			if (!file_exists(DIR_IMAGE . $filename) || !is_file(DIR_IMAGE . $filename)) {
				return;
			} 
			
			$old_image = $filename;
			$new_image = 'cache/' . $filename;
			
			if (!file_exists(DIR_IMAGE . $new_image) || (filemtime(DIR_IMAGE . $old_image) > filemtime(DIR_IMAGE . $new_image))) {
				$path = '';
				
				$directories = explode('/', dirname(str_replace('../', '', $new_image)));
				
				foreach ($directories as $directory) {
					$path = $path . '/' . $directory;
					
					if (!file_exists(DIR_IMAGE . $path)) {
						@mkdir(DIR_IMAGE . $path, 0777);
					}		
				}
				
				copy(DIR_IMAGE . $old_image, DIR_IMAGE . $new_image);
			}
		
			if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
				$this->response->setOutput(HTTPS_CATALOG . 'image/' . $new_image);
			} else {
				$this->response->setOutput(HTTP_CATALOG . 'image/' . $new_image);
			}	
		}
	}

	////////////////////////////////////////////////////////

			 public function closeDrawer() {

            $pennies_count = $_POST['pennies_count'];
            print_r($Pennies_count);
            exit;


    }


	///////////////////////////////////////////////////////


}
?>