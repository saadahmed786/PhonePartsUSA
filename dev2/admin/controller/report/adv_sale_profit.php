<?php
class ControllerReportAdvSaleProfit extends Controller { 
	public function index() {  
		// Insert DB columns
		$query = $this->db->query("DESC " . DB_PREFIX . "product cost_additional");
			if (!$query->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "product` ADD `cost_additional` decimal(15,4) NOT NULL DEFAULT '0.0000' AFTER `price`;");
			}

		$query = $this->db->query("DESC " . DB_PREFIX . "product cost_percentage");
			if (!$query->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "product` ADD `cost_percentage` decimal(15,2) NOT NULL DEFAULT '0.00' AFTER `price`;");
			}

		$query = $this->db->query("DESC " . DB_PREFIX . "product cost_amount");
			if (!$query->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "product` ADD `cost_amount` decimal(15,4) NOT NULL DEFAULT '0.0000' AFTER `price`;");
			}
			
		$query = $this->db->query("DESC " . DB_PREFIX . "product cost");
			if (!$query->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "product` ADD `cost` decimal(15,4) NOT NULL DEFAULT '0.0000' AFTER `price`;");
			}

		$query = $this->db->query("DESC " . DB_PREFIX . "product_option_value cost_prefix");
			if (!$query->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "product_option_value` ADD `cost_prefix` varchar(1) COLLATE utf8_bin NOT NULL AFTER `price`;");
			}	
			
		$query = $this->db->query("DESC " . DB_PREFIX . "product_option_value cost");
			if (!$query->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "product_option_value` ADD `cost` decimal(15,4) NOT NULL DEFAULT '0.0000' AFTER `price`;");
			}	
			
		$query = $this->db->query("DESC " . DB_PREFIX . "order_product cost");
			if (!$query->num_rows) {
				$this->db->query("ALTER TABLE `" . DB_PREFIX . "order_product` ADD `cost` decimal(15,4) NOT NULL DEFAULT '0.0000';");
			}

		$this->load->language('report/adv_sale_profit');

		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('report/adv_sale_profit');
		
		if (isset($this->request->post['filter_date_start'])) {
			$filter_date_start = $this->request->post['filter_date_start'];
		} else {
			$filter_date_start = '';
		}

		if (isset($this->request->post['filter_date_end'])) {
			$filter_date_end = $this->request->post['filter_date_end'];
		} else {
			$filter_date_end = '';
		}

		$this->data['ranges'] = array();
		
		$this->data['ranges'][] = array(
			'text'  => $this->language->get('stat_custom'),
			'value' => 'custom',
		);			
		$this->data['ranges'][] = array(
			'text'  => $this->language->get('stat_week'),
			'value' => 'week',
		);		
		$this->data['ranges'][] = array(
			'text'  => $this->language->get('stat_month'),
			'value' => 'month',
		);					
		$this->data['ranges'][] = array(
			'text'  => $this->language->get('stat_quarter'),
			'value' => 'quarter',
		);
		$this->data['ranges'][] = array(
			'text'  => $this->language->get('stat_year'),
			'value' => 'year',
		);
		$this->data['ranges'][] = array(
			'text'  => $this->language->get('stat_current_week'),
			'value' => 'current_week',
		);
		$this->data['ranges'][] = array(
			'text'  => $this->language->get('stat_current_month'),
			'value' => 'current_month',
		);	
		$this->data['ranges'][] = array(
			'text'  => $this->language->get('stat_current_quarter'),
			'value' => 'current_quarter',
		);			
		$this->data['ranges'][] = array(
			'text'  => $this->language->get('stat_current_year'),
			'value' => 'current_year',
		);			
		$this->data['ranges'][] = array(
			'text'  => $this->language->get('stat_last_week'),
			'value' => 'last_week',
		);
		$this->data['ranges'][] = array(
			'text'  => $this->language->get('stat_last_month'),
			'value' => 'last_month',
		);	
		$this->data['ranges'][] = array(
			'text'  => $this->language->get('stat_last_quarter'),
			'value' => 'last_quarter',
		);			
		$this->data['ranges'][] = array(
			'text'  => $this->language->get('stat_last_year'),
			'value' => 'last_year',
		);			
		$this->data['ranges'][] = array(
			'text'  => $this->language->get('stat_all_time'),
			'value' => 'all_time',
		);
		
		if (isset($this->request->post['filter_range'])) {
			$filter_range = $this->request->post['filter_range'];
		} else {
			$filter_range = 'current_year'; //show Current Year in Statistics Range by default
		}

		$this->data['groups'] = array();
		
		$this->data['groups'][] = array(
			'text'  => $this->language->get('text_year'),
			'value' => 'year',
		);

		$this->data['groups'][] = array(
			'text'  => $this->language->get('text_quarter'),
			'value' => 'quarter',
		);
		
		$this->data['groups'][] = array(
			'text'  => $this->language->get('text_month'),
			'value' => 'month',
		);
		
		$this->data['groups'][] = array(
			'text'  => $this->language->get('text_week'),
			'value' => 'week',
		);

		$this->data['groups'][] = array(
			'text'  => $this->language->get('text_day'),
			'value' => 'day',
		);
		
		$this->data['groups'][] = array(
			'text'  => $this->language->get('text_order'),
			'value' => 'order',
		);
		
		if (isset($this->request->post['filter_group'])) {
			$filter_group = $this->request->post['filter_group'];
		} else {
			$filter_group = 'month'; //show Month in Group Report by default
		}

		if (isset($this->request->post['filter_sort'])) {
			$filter_sort = $this->request->post['filter_sort'];
		} else {
			$filter_sort = 'date';
		}	

		if (isset($this->request->post['filter_details'])) {
			$filter_details = $this->request->post['filter_details'];
		} else {
			$filter_details = 0;
		}	

		if (isset($this->request->post['filter_limit'])) {
			$filter_limit = $this->request->post['filter_limit'];
		} else {
			$filter_limit = 25;
		}
		
		$this->data['order_statuses'] = $this->model_report_adv_sale_profit->getOrderStatuses(); 			
		if (isset($this->request->post['filter_order_status_id']) && is_array($this->request->post['filter_order_status_id'])) {
			$filter_order_status_id = array_flip($this->request->post['filter_order_status_id']);
		} else {
			$filter_order_status_id = '';
		}

		$this->data['stores'] = $this->model_report_adv_sale_profit->getOrderStores();						
		if (isset($this->request->post['filter_store_id']) && is_array($this->request->post['filter_store_id'])) {
			$filter_store_id = array_flip($this->request->post['filter_store_id']);
		} else {
			$filter_store_id = '';			
		}
		
		$this->data['currencies'] = $this->model_report_adv_sale_profit->getOrderCurrencies();	
		if (isset($this->request->post['filter_currency']) && is_array($this->request->post['filter_currency'])) {
			$filter_currency = array_flip($this->request->post['filter_currency']);
		} else {
			$filter_currency = '';		
		}

		$this->data['taxes'] = $this->model_report_adv_sale_profit->getOrderTaxes();					
		if (isset($this->request->post['filter_taxes']) && is_array($this->request->post['filter_taxes'])) {
			$filter_taxes = array_flip($this->request->post['filter_taxes']);
		} else {
			$filter_taxes = '';		
		}
		
		$this->data['customer_groups'] = $this->model_report_adv_sale_profit->getOrderCustomerGroups();		
		if (isset($this->request->post['filter_customer_group_id']) && is_array($this->request->post['filter_customer_group_id'])) {
			$filter_customer_group_id = array_flip($this->request->post['filter_customer_group_id']);
		} else {
			$filter_customer_group_id = '';
		}

		if (isset($this->request->post['filter_company'])) {
			$filter_company = $this->request->post['filter_company'];
		} else {
			$filter_company = '';
		}
		
		if (isset($this->request->post['filter_customer_id'])) {
			$filter_customer_id = $this->request->post['filter_customer_id'];
		} else {
			$filter_customer_id = '';
		}

		if (isset($this->request->post['filter_email'])) {
			$filter_email = $this->request->post['filter_email'];
		} else {
			$filter_email = '';
		}

		if (isset($this->request->post['filter_product_id'])) {
			$filter_product_id = $this->request->post['filter_product_id'];
		} else {
			$filter_product_id = '';
		}

		$this->data['product_options'] = $this->model_report_adv_sale_profit->getProductOptions();
		if (isset($this->request->post['filter_option']) && is_array($this->request->post['filter_option'])) {
			$filter_option = array_flip($this->request->post['filter_option']);
		} else {
			$filter_option = '';
		}
		
		$this->data['locations'] = $this->model_report_adv_sale_profit->getProductLocation();			
		if (isset($this->request->post['filter_location']) && is_array($this->request->post['filter_location'])) {
			$filter_location = array_flip($this->request->post['filter_location']);
		} else {
			$filter_location = '';		
		}
		
		$this->data['affiliates'] = $this->model_report_adv_sale_profit->getOrderAffiliate();
		if (isset($this->request->post['filter_affiliate']) && is_array($this->request->post['filter_affiliate'])) {
			$filter_affiliate = array_flip($this->request->post['filter_affiliate']);
		} else {
			$filter_affiliate = '';
		}
		
		$this->data['shippings'] = $this->model_report_adv_sale_profit->getOrderShipping();			
		if (isset($this->request->post['filter_shipping']) && is_array($this->request->post['filter_shipping'])) {
			$filter_shipping = array_flip($this->request->post['filter_shipping']);
		} else {
			$filter_shipping = '';		
		}

		$this->data['payments'] = $this->model_report_adv_sale_profit->getOrderPayment();	
		if (isset($this->request->post['filter_payment']) && is_array($this->request->post['filter_payment'])) {
			$filter_payment = array_flip($this->request->post['filter_payment']);
		} else {
			$filter_payment = '';		
		}

		$this->data['shipping_zones'] = $this->model_report_adv_sale_profit->getShippingZones();			
		if (isset($this->request->post['filter_shipping_zone']) && is_array($this->request->post['filter_shipping_zone'])) {
			$filter_shipping_zone = array_flip($this->request->post['filter_shipping_zone']);
		} else {
			$filter_shipping_zone = '';		
		}
		
		$this->data['shipping_countries'] = $this->model_report_adv_sale_profit->getShippingCountries();			
		if (isset($this->request->post['filter_shipping_country']) && is_array($this->request->post['filter_shipping_country'])) {
			$filter_shipping_country = array_flip($this->request->post['filter_shipping_country']);
		} else {
			$filter_shipping_country = '';		
		}
		
		$this->data['payment_countries'] = $this->model_report_adv_sale_profit->getPaymentCountries();	
		if (isset($this->request->post['filter_payment_country']) && is_array($this->request->post['filter_payment_country'])) {
			$filter_payment_country = array_flip($this->request->post['filter_payment_country']);
		} else {
			$filter_payment_country = '';		
		}
		
   		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),       		
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('report/adv_sale_profit', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

		$this->load->model('report/adv_sale_profit');
			
		$this->data['orders'] = array();
		
		$data = array(
			'filter_date_start'	     	=> $filter_date_start, 
			'filter_date_end'	     	=> $filter_date_end, 
			'filter_range'           	=> $filter_range,
			'filter_group'           	=> $filter_group,			
			'filter_order_status_id'	=> $filter_order_status_id,
			'filter_store_id' 	 	 	=> $filter_store_id,
			'filter_currency' 	 	 	=> $filter_currency,
			'filter_taxes' 	 	 		=> $filter_taxes,			
			'filter_customer_group_id'  => $filter_customer_group_id,
			'filter_company' 	 		=> $filter_company,
			'filter_customer_id' 	 	=> $filter_customer_id,			
			'filter_email' 	 			=> $filter_email,	
			'filter_product_id' 	 	=> $filter_product_id,			
			'filter_option'  			=> $filter_option,
			'filter_location'  			=> $filter_location,
			'filter_affiliate'  		=> $filter_affiliate,			
			'filter_shipping'  			=> $filter_shipping,			
			'filter_payment'  			=> $filter_payment,
			'filter_shipping_zone'  	=> $filter_shipping_zone,			
			'filter_shipping_country'  	=> $filter_shipping_country,
			'filter_payment_country'  	=> $filter_payment_country,	
			'filter_sort'  				=> $filter_sort,	
			'filter_details'  			=> $filter_details,				
			'filter_limit'  			=> $filter_limit
		);

		$results = $this->model_report_adv_sale_profit->getSaleOrders($data);

		foreach ($results as $result) {
				
			if ($result['prod_costs']) {
				$profit_margin_percent = ($result['sub_total']+$result['handling']+$result['low_order_fee']+$result['reward']+$result['coupon']+$result['credit']+$result['voucher']-$result['commission']) > 0 ? round(100 * ($result['sub_total']-$result['prod_costs']-$result['commission']+$result['handling']+$result['low_order_fee']+$result['reward']+$result['coupon']+$result['credit']+$result['voucher']) / ($result['sub_total']+$result['handling']+$result['low_order_fee']+$result['reward']+$result['coupon']+$result['credit']+$result['voucher']-$result['commission']), 2) . '%' : '0%';
				$profit_margin_total_percent = ($result['sub_total_total']+$result['handling_total']+$result['low_order_fee_total']+$result['reward_total']+$result['coupon_total']+$result['credit_total']+$result['voucher_total']-$result['commission_total']) > 0 ? round(100 * ($result['sub_total_total']-$result['prod_costs_total']-$result['commission_total']+$result['handling_total']+$result['low_order_fee_total']+$result['reward_total']+$result['coupon_total']+$result['credit_total']+$result['voucher_total']) / ($result['sub_total_total']+$result['handling_total']+$result['low_order_fee_total']+$result['reward_total']+$result['coupon_total']+$result['credit_total']+$result['voucher_total']-$result['commission_total']), 2) . '%' : '0%';						
			} else {
				$profit_margin_percent = '100%';
				$profit_margin_total_percent = '100%';				
			}
			
			$this->data['orders'][] = array(
				'year'		       				=> $result['year'],
				'quarter'		       			=> 'Q' . $result['quarter'],	
				'year_quarter'		       		=> 'Q' . $result['quarter']. ' ' . $result['year'],					
				'month'		       				=> $result['month'],
				'year_month'		       		=> substr($result['month'],0,3) . ' ' . $result['year'],			
				'date_start' 					=> date($this->language->get('date_format_short'), strtotime($result['date_start'])),
				'date_end'   					=> date($this->language->get('date_format_short'), strtotime($result['date_end'])),	
				'order_id'   					=> $result['order_id'],	
				'orders'     					=> $result['orders'],
				'customers'   					=> $result['customers'],				
				'products'   					=> $result['products'],	
				'sub_total'        				=> $this->currency->format($result['sub_total'], $this->config->get('config_currency')),
				'gsales'      					=> $result['sub_total']+$result['handling']+$result['low_order_fee'],	
				'handling'        				=> $this->currency->format($result['handling'], $this->config->get('config_currency')),
				'low_order_fee'        			=> $this->currency->format($result['low_order_fee'], $this->config->get('config_currency')),				
				'reward'      					=> $this->currency->format($result['reward'], $this->config->get('config_currency')),
				'shipping'        				=> $this->currency->format($result['shipping'], $this->config->get('config_currency')),
				'coupon'      					=> $this->currency->format($result['coupon'], $this->config->get('config_currency')),
				'tax'        					=> $this->currency->format($result['tax'], $this->config->get('config_currency')),
				'credit'      					=> $this->currency->format($result['credit'], $this->config->get('config_currency')),
				'voucher'        				=> $this->currency->format($result['voucher'], $this->config->get('config_currency')),
				'commission'      				=> $this->currency->format('-' . ($result['commission']), $this->config->get('config_currency')),				
				'total'      					=> $this->currency->format($result['total'], $this->config->get('config_currency')),
				'prod_costs'      				=> $this->currency->format('-' . ($result['prod_costs']), $this->config->get('config_currency')),
				'gcosts'      					=> $result['prod_costs']+$result['commission']-$result['reward']-$result['coupon']-$result['credit']-$result['voucher'],				
				'netprofit'      				=> $this->currency->format($result['sub_total']-$result['prod_costs']-$result['commission']+$result['handling']+$result['low_order_fee']+$result['reward']+$result['coupon']+$result['credit']+$result['voucher'], $this->config->get('config_currency')),
				'gnetprofit'      				=> $result['sub_total']-$result['prod_costs']-$result['commission']+$result['handling']+$result['low_order_fee']+$result['reward']+$result['coupon']+$result['credit']+$result['voucher'],				
				'profit_margin_percent' 		=> $profit_margin_percent,
				'order_ord_id'     				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 1 ? $result['order_ord_id'] : '',
				'order_ord_idc'     			=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 1 ? $result['order_ord_idc'] : '',					
				'order_order_date'    			=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 1 ? $result['order_order_date'] : '',
				'order_inv_no'     				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 1 ? $result['order_inv_no'] : '',
				'order_name'   					=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 1 ? $result['order_name'] : '',
				'order_email'   				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 1 ? $result['order_email'] : '',
				'order_group'   				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 1 ? $result['order_group'] : '',
				'order_shipping_method' 		=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 1 ? $result['order_shipping_method'] : '',
				'order_payment_method'  		=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 1 ? strip_tags($result['order_payment_method'], '<br>') : '',
				'order_status'  				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 1 ? $result['order_status'] : '',
				'order_store'      				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 1 ? $result['order_store'] : '',	
				'order_currency' 				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 1 ? $result['order_currency'] : '',				
				'order_products' 				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 1 ? $result['order_products'] : '',
				'order_sub_total'  				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 1 ? $result['order_sub_total'] : '',
				'order_hf'  					=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 1 ? $result['order_hf'] : '',
				'order_lof'  					=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 1 ? $result['order_lof'] : '',				
				'order_shipping'  				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 1 ? $result['order_shipping'] : '',
				'order_tax'  					=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 1 ? $result['order_tax'] : '',					
				'order_value'  					=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 1 ? $result['order_value'] : '',
				'order_costs'   				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 1 ? $result['order_costs'] : '',
				'order_profit'   				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 1 ? $result['order_profit'] : '',	
				'order_profit_margin_percent' 	=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 1 ? $result['order_profit_margin_percent'] : '',				
				'product_ord_id'  				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 2 ? $result['product_ord_id'] : '',
				'product_ord_idc'  				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 2 ? $result['product_ord_idc'] : '',
				'product_order_date'    		=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 2 ? $result['product_order_date'] : '',
				'product_inv_no'     			=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 2 ? $result['product_inv_no'] : '',					
				'product_pid'  					=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 2 ? $result['product_pid'] : '',	
				'product_pidc'  				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 2 ? $result['product_pidc'] : '',	
				'product_sku'  					=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 2 ? $result['product_sku'] : '',					
				'product_name'  				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 2 ? $result['product_name'] : '',	
				'product_option'  				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 2 ? $result['product_option'] : '',					
				'product_model'  				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 2 ? $result['product_model'] : '',					
				'product_manu'  				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 2 ? $result['product_manu'] : '',
				'product_currency'  			=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 2 ? $result['product_currency'] : '',
				'product_price'  				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 2 ? $result['product_price'] : '',
				'product_quantity'  			=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 2 ? $result['product_quantity'] : '',				
				'product_total'  				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 2 ? $result['product_total'] : '',
				'product_tax'  					=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 2 ? $result['product_tax'] : '',
				'product_costs'   				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 2 ? $result['product_costs'] : '',
				'product_profit'   				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 2 ? $result['product_profit'] : '',
				'product_profit_margin_percent' => isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 2 ? $result['product_profit_margin_percent'] : '',
				'customer_ord_id' 				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 3 ? $result['customer_ord_id'] : '',	
				'customer_order_date' 			=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 3 ? $result['customer_order_date'] : '',
				'customer_inv_no' 				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 3 ? $result['customer_inv_no'] : '',
				'customer_cust_id' 				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 3 ? $result['customer_cust_id'] : '',	
				'customer_cust_idc' 			=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 3 ? $result['customer_cust_idc'] : '',	
				'billing_name' 					=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 3 ? $result['billing_name'] : '',
				'billing_company' 				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 3 ? $result['billing_company'] : '',
				'billing_address_1' 			=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 3 ? $result['billing_address_1'] : '',
				'billing_address_2' 			=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 3 ? $result['billing_address_2'] : '',
				'billing_city' 					=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 3 ? $result['billing_city'] : '',
				'billing_zone' 					=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 3 ? $result['billing_zone'] : '',
				'billing_postcode' 				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 3 ? $result['billing_postcode'] : '',	
				'billing_country' 				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 3 ? $result['billing_country'] : '',
				'customer_telephone' 			=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 3 ? $result['customer_telephone'] : '',
				'shipping_name' 				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 3 ? $result['shipping_name'] : '',
				'shipping_company' 				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 3 ? $result['shipping_company'] : '',
				'shipping_address_1' 			=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 3 ? $result['shipping_address_1'] : '',
				'shipping_address_2' 			=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 3 ? $result['shipping_address_2'] : '',
				'shipping_city' 				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 3 ? $result['shipping_city'] : '',
				'shipping_zone' 				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 3 ? $result['shipping_zone'] : '',
				'shipping_postcode' 			=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 3 ? $result['shipping_postcode'] : '',	
				'shipping_country' 				=> isset($this->request->post['filter_details']) && $this->request->post['filter_details'] == 3 ? $result['shipping_country'] : '',				
				'orders_total'      			=> $result['orders_total'],	
				'customers_total'      			=> $result['customers_total'],
				'products_total'      			=> $result['products_total'],				
				'sub_total_total'      			=> $this->currency->format($result['sub_total_total'], $this->config->get('config_currency')),
				'handling_total'      			=> $this->currency->format($result['handling_total'], $this->config->get('config_currency')),
				'low_order_fee_total'      		=> $this->currency->format($result['low_order_fee_total'], $this->config->get('config_currency')),
				'reward_total'      			=> $this->currency->format($result['reward_total'], $this->config->get('config_currency')),
				'shipping_total'      			=> $this->currency->format($result['shipping_total'], $this->config->get('config_currency')),
				'coupon_total'      			=> $this->currency->format($result['coupon_total'], $this->config->get('config_currency')),
				'tax_total'      				=> $this->currency->format($result['tax_total'], $this->config->get('config_currency')),
				'credit_total'      			=> $this->currency->format($result['credit_total'], $this->config->get('config_currency')),
				'voucher_total'      			=> $this->currency->format($result['voucher_total'], $this->config->get('config_currency')),
				'commission_total'      		=> $this->currency->format('-' . ($result['commission_total']), $this->config->get('config_currency')),
				'total_total'      				=> $this->currency->format($result['total_total'], $this->config->get('config_currency')),	
				'prod_costs_total'      		=> $this->currency->format('-' . ($result['prod_costs_total']), $this->config->get('config_currency')),					
				'netprofit_total'      			=> $this->currency->format($result['sub_total_total']-$result['prod_costs_total']-$result['commission_total']+$result['handling_total']+$result['low_order_fee_total']+$result['reward_total']+$result['coupon_total']+$result['credit_total']+$result['voucher_total'], $this->config->get('config_currency')),
				'profit_margin_total_percent' 	=> $profit_margin_total_percent				
			);
		}

		$this->data['text_no_details'] = $this->language->get('text_no_details');
		$this->data['text_order_list'] = $this->language->get('text_order_list');
		$this->data['text_product_list'] = $this->language->get('text_product_list');
		$this->data['text_customer_list'] = $this->language->get('text_customer_list');			
		$this->data['text_no_results'] = $this->language->get('text_no_results');		
		$this->data['text_all_status'] = $this->language->get('text_all_status');		
		$this->data['text_all_stores'] = $this->language->get('text_all_stores');
		$this->data['text_all_currencies'] = $this->language->get('text_all_currencies');
		$this->data['text_all_taxes'] = $this->language->get('text_all_taxes');		
		$this->data['text_all_groups'] = $this->language->get('text_all_groups');
		$this->data['text_all_options'] = $this->language->get('text_all_options');		
		$this->data['text_all_locations'] = $this->language->get('text_all_locations');	
		$this->data['text_all_affiliates'] = $this->language->get('text_all_affiliates');		
		$this->data['text_all_shippings'] = $this->language->get('text_all_shippings');			
		$this->data['text_all_payments'] = $this->language->get('text_all_payments');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');			
		$this->data['text_all_countries'] = $this->language->get('text_all_countries');	
		$this->data['text_none_selected'] = $this->language->get('text_none_selected');
		$this->data['text_selected'] = $this->language->get('text_selected');		
		$this->data['text_detail'] = $this->language->get('text_detail');
		$this->data['text_export_no_details'] = $this->language->get('text_export_no_details');
		$this->data['text_export_order_list'] = $this->language->get('text_export_order_list');
		$this->data['text_export_product_list'] = $this->language->get('text_export_product_list');	
		$this->data['text_export_customer_list'] = $this->language->get('text_export_customer_list');
		$this->data['text_export_all_details'] = $this->language->get('text_export_all_details');				
		$this->data['text_filter_total'] = $this->language->get('text_filter_total');
		$this->data['text_profit_help'] = $this->language->get('text_profit_help');	
		$this->data['text_filtering_options'] = $this->language->get('text_filtering_options');
		$this->data['text_mv_columns'] = $this->language->get('text_mv_columns');		
		$this->data['text_ol_columns'] = $this->language->get('text_ol_columns');	
		$this->data['text_pl_columns'] = $this->language->get('text_pl_columns');	
		$this->data['text_cl_columns'] = $this->language->get('text_cl_columns');
		$this->data['text_pagin_page'] = $this->language->get('text_pagin_page');
		$this->data['text_pagin_of'] = $this->language->get('text_pagin_of');
		$this->data['text_pagin_results'] = $this->language->get('text_pagin_results');			
		
		$this->data['column_date'] = $this->language->get('column_date');
		$this->data['column_date_start'] = $this->language->get('column_date_start');
		$this->data['column_date_end'] = $this->language->get('column_date_end');
    	$this->data['column_orders'] = $this->language->get('column_orders');
    	$this->data['column_customers'] = $this->language->get('column_customers');		
		$this->data['column_products'] = $this->language->get('column_products');		
		$this->data['column_sub_total'] = $this->language->get('column_sub_total');
		$this->data['column_hf'] = $this->language->get('column_hf');
		$this->data['column_handling'] = $this->language->get('column_handling');
		$this->data['column_lof'] = $this->language->get('column_lof');		
		$this->data['column_loworder'] = $this->language->get('column_loworder');		
		$this->data['column_points'] = $this->language->get('column_points');
		$this->data['column_shipping'] = $this->language->get('column_shipping');		
		$this->data['column_coupon'] = $this->language->get('column_coupon');
		$this->data['column_tax'] = $this->language->get('column_tax');		
		$this->data['column_credit'] = $this->language->get('column_credit');	
		$this->data['column_voucher'] = $this->language->get('column_voucher');	
		$this->data['column_commission'] = $this->language->get('column_commission');		
		$this->data['column_total'] = $this->language->get('column_total');		
		$this->data['column_prod_costs'] = $this->language->get('column_prod_costs');
		$this->data['column_net_profit'] = $this->language->get('column_net_profit');
		$this->data['column_profit_margin'] = $this->language->get('column_profit_margin');		
		$this->data['column_action'] = $this->language->get('column_action');
		$this->data['column_order_date_added'] = $this->language->get('column_order_date_added');
		$this->data['column_order_order_id'] = $this->language->get('column_order_order_id');
		$this->data['column_order_inv_date'] = $this->language->get('column_order_inv_date');
		$this->data['column_order_inv_no'] = $this->language->get('column_order_inv_no');
		$this->data['column_order_customer'] = $this->language->get('column_order_customer');		
		$this->data['column_order_email'] = $this->language->get('column_order_email');		
		$this->data['column_order_customer_group'] = $this->language->get('column_order_customer_group');		
		$this->data['column_order_shipping_method'] = $this->language->get('column_order_shipping_method');
		$this->data['column_order_payment_method'] = $this->language->get('column_order_payment_method');		
		$this->data['column_order_status'] = $this->language->get('column_order_status');
		$this->data['column_order_store'] = $this->language->get('column_order_store');
		$this->data['column_order_currency'] = $this->language->get('column_order_currency');		
		$this->data['column_order_quantity'] = $this->language->get('column_order_quantity');	
		$this->data['column_order_sub_total'] = $this->language->get('column_order_sub_total');
		$this->data['column_order_hf'] = $this->language->get('column_order_hf');	
		$this->data['column_order_lof'] = $this->language->get('column_order_lof');		
		$this->data['column_order_shipping'] = $this->language->get('column_order_shipping');
		$this->data['column_order_tax'] = $this->language->get('column_order_tax');			
		$this->data['column_order_value'] = $this->language->get('column_order_value');	
		$this->data['column_order_costs'] = $this->language->get('column_order_costs');
		$this->data['column_order_profit'] = $this->language->get('column_order_profit');
		$this->data['column_prod_order_id'] = $this->language->get('column_prod_order_id');		
		$this->data['column_prod_date_added'] = $this->language->get('column_prod_date_added');	
		$this->data['column_prod_inv_no'] = $this->language->get('column_prod_inv_no');			
		$this->data['column_prod_id'] = $this->language->get('column_prod_id');
		$this->data['column_prod_sku'] = $this->language->get('column_prod_sku');		
		$this->data['column_prod_model'] = $this->language->get('column_prod_model');		
		$this->data['column_prod_name'] = $this->language->get('column_prod_name');	
		$this->data['column_prod_option'] = $this->language->get('column_prod_option');			
		$this->data['column_prod_manu'] = $this->language->get('column_prod_manu');
		$this->data['column_prod_currency'] = $this->language->get('column_prod_currency');
		$this->data['column_prod_price'] = $this->language->get('column_prod_price');
		$this->data['column_prod_quantity'] = $this->language->get('column_prod_quantity');
		$this->data['column_prod_total'] = $this->language->get('column_prod_total');
		$this->data['column_prod_tax'] = $this->language->get('column_prod_tax');
		$this->data['column_prod_costs'] = $this->language->get('column_prod_costs');
		$this->data['column_prod_profit'] = $this->language->get('column_prod_profit');	
		$this->data['column_customer_order_id'] = $this->language->get('column_customer_order_id');
		$this->data['column_customer_date_added'] = $this->language->get('column_customer_date_added');
		$this->data['column_customer_inv_no'] = $this->language->get('column_customer_inv_no');
		$this->data['column_customer_cust_id'] = $this->language->get('column_customer_cust_id');
		$this->data['column_billing_name'] = $this->language->get('column_billing_name');
		$this->data['column_billing_company'] = $this->language->get('column_billing_company');
		$this->data['column_billing_address_1'] = $this->language->get('column_billing_address_1');
		$this->data['column_billing_address_2'] = $this->language->get('column_billing_address_2');
		$this->data['column_billing_city'] = $this->language->get('column_billing_city');
		$this->data['column_billing_zone'] = $this->language->get('column_billing_zone');
		$this->data['column_billing_postcode'] = $this->language->get('column_billing_postcode');		
		$this->data['column_billing_country'] = $this->language->get('column_billing_country');
		$this->data['column_customer_telephone'] = $this->language->get('column_customer_telephone');
		$this->data['column_shipping_name'] = $this->language->get('column_shipping_name');
		$this->data['column_shipping_company'] = $this->language->get('column_shipping_company');
		$this->data['column_shipping_address_1'] = $this->language->get('column_shipping_address_1');
		$this->data['column_shipping_address_2'] = $this->language->get('column_shipping_address_2');
		$this->data['column_shipping_city'] = $this->language->get('column_shipping_city');
		$this->data['column_shipping_zone'] = $this->language->get('column_shipping_zone');
		$this->data['column_shipping_postcode'] = $this->language->get('column_shipping_postcode');		
		$this->data['column_shipping_country'] = $this->language->get('column_shipping_country');		
		
		$this->data['column_year'] = $this->language->get('column_year');
		$this->data['column_quarter'] = $this->language->get('column_quarter');
		$this->data['column_month'] = $this->language->get('column_month');
		$this->data['column_sales'] = $this->language->get('column_sales');		
		$this->data['column_total_costs'] = $this->language->get('column_total_costs');
		$this->data['column_total_profit'] = $this->language->get('column_total_profit');

		$this->data['entry_date_start'] = $this->language->get('entry_date_start');
		$this->data['entry_date_end'] = $this->language->get('entry_date_end');
		$this->data['entry_range'] = $this->language->get('entry_range');
		$this->data['entry_status'] = $this->language->get('entry_status');		
		$this->data['entry_store'] = $this->language->get('entry_store');
		$this->data['entry_currency'] = $this->language->get('entry_currency');	
		$this->data['entry_tax'] = $this->language->get('entry_tax');
		$this->data['entry_customer_group'] = $this->language->get('entry_customer_group');
		$this->data['entry_company'] = $this->language->get('entry_company');
		$this->data['entry_customer'] = $this->language->get('entry_customer');		
		$this->data['entry_email'] = $this->language->get('entry_email'); 
		$this->data['entry_product'] = $this->language->get('entry_product');
		$this->data['entry_option'] = $this->language->get('entry_option');		
		$this->data['entry_location'] = $this->language->get('entry_location');
		$this->data['entry_affiliate'] = $this->language->get('entry_affiliate');		
		$this->data['entry_shipping'] = $this->language->get('entry_shipping');	
		$this->data['entry_payment'] = $this->language->get('entry_payment');
		$this->data['entry_zone'] = $this->language->get('entry_zone');
		$this->data['entry_shipping_country'] = $this->language->get('entry_shipping_country');
		$this->data['entry_payment_country'] = $this->language->get('entry_payment_country');
		$this->data['entry_group'] = $this->language->get('entry_group');		
		$this->data['entry_sort_by'] = $this->language->get('entry_sort_by');
		$this->data['entry_show_details'] = $this->language->get('entry_show_details');	
		$this->data['entry_limit'] = $this->language->get('entry_limit');		

		$this->data['button_filter'] = $this->language->get('button_filter');
		$this->data['button_chart'] = $this->language->get('button_chart');		
		$this->data['button_export'] = $this->language->get('button_export');
		$this->data['button_settings'] = $this->language->get('button_settings');		
		
		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['heading_version'] = $this->language->get('heading_version');		
		
		$this->data['token'] = $this->session->data['token'];

		$this->data['filter_date_start'] = $filter_date_start;
		$this->data['filter_date_end'] = $filter_date_end;		
		$this->data['filter_group'] = $filter_group;
		$this->data['filter_range'] = $filter_range;
		$this->data['filter_order_status_id'] = $filter_order_status_id;
		$this->data['filter_store_id'] = $filter_store_id;
		$this->data['filter_currency'] = $filter_currency;
		$this->data['filter_taxes'] = $filter_taxes;		
		$this->data['filter_customer_group_id'] = $filter_customer_group_id;
		$this->data['filter_company'] = $filter_company; 
		$this->data['filter_customer_id'] = $filter_customer_id; 		
		$this->data['filter_email'] = $filter_email; 	
		$this->data['filter_product_id'] = $filter_product_id; 
		$this->data['filter_option'] = $filter_option; 		
		$this->data['filter_location'] = $filter_location;
		$this->data['filter_affiliate'] = $filter_affiliate; 		
		$this->data['filter_shipping'] = $filter_shipping;		
		$this->data['filter_payment'] = $filter_payment;
		$this->data['filter_shipping_zone'] = $filter_shipping_zone;		
		$this->data['filter_shipping_country'] = $filter_shipping_country;
		$this->data['filter_payment_country'] = $filter_payment_country;		
		$this->data['filter_sort'] = $filter_sort;	
		$this->data['filter_details'] = $filter_details;
		$this->data['filter_limit'] = $filter_limit;		
		
		$this->template = 'report/adv_sale_profit.tpl';
		$this->children = array(
			'common/header',
			'common/footer',
		);


	$this->response->setOutput($this->render());

    	if (isset($this->request->post['export']) && $this->request->post['export'] == 1) { // export_xls
			$this->load->model('report/adv_sale_profit_export');
    		$results = $this->model_report_adv_sale_profit_export->getSaleProfitExport($data);
			include(DIR_APPLICATION."controller/report/adv_reports/sop_export_xls.inc.php");
			
		} elseif (isset($this->request->post['export']) && $this->request->post['export'] == 2) { // export_xls_order_list
			$this->load->model('report/adv_sale_profit_export');
    		$results = $this->model_report_adv_sale_profit_export->getSaleProfitExport($data);
			include(DIR_APPLICATION."controller/report/adv_reports/sop_export_xls_order_list.inc.php");

		} elseif (isset($this->request->post['export']) && $this->request->post['export'] == 3) { // export_xls_product_list
			$this->load->model('report/adv_sale_profit_export');
    		$results = $this->model_report_adv_sale_profit_export->getSaleProfitExport($data);
			include(DIR_APPLICATION."controller/report/adv_reports/sop_export_xls_product_list.inc.php");

		} elseif (isset($this->request->post['export']) && $this->request->post['export'] == 4) { // export_xls_customer_list
			$this->load->model('report/adv_sale_profit_export');
    		$results = $this->model_report_adv_sale_profit_export->getSaleProfitExport($data);
			include(DIR_APPLICATION."controller/report/adv_reports/sop_export_xls_customer_list.inc.php");
				
		} elseif (isset($this->request->post['export']) && $this->request->post['export'] == 5) { // export_xls_all_details
			$this->load->model('report/adv_sale_profit_export_all');
    		$results = $this->model_report_adv_sale_profit_export_all->getSaleProfitExportAll($data);
			include(DIR_APPLICATION."controller/report/adv_reports/sop_export_xls_all_details.inc.php");
				
		} elseif (isset($this->request->post['export']) && $this->request->post['export'] == 6) { // export_html
			$this->load->model('report/adv_sale_profit_export');
    		$results = $this->model_report_adv_sale_profit_export->getSaleProfitExport($data);
			include(DIR_APPLICATION."controller/report/adv_reports/sop_export_html.inc.php");
			
		} elseif (isset($this->request->post['export']) && $this->request->post['export'] == 7) { // export_html_order_list
			$this->load->model('report/adv_sale_profit_export');
    		$results = $this->model_report_adv_sale_profit_export->getSaleProfitExport($data);
			include(DIR_APPLICATION."controller/report/adv_reports/sop_export_html_order_list.inc.php");
				
		} elseif (isset($this->request->post['export']) && $this->request->post['export'] == 8) { // export_html_product_list
			$this->load->model('report/adv_sale_profit_export');
    		$results = $this->model_report_adv_sale_profit_export->getSaleProfitExport($data);
			include(DIR_APPLICATION."controller/report/adv_reports/sop_export_html_product_list.inc.php");
							
		} elseif (isset($this->request->post['export']) && $this->request->post['export'] == 9) { // export_html_customer_list
			$this->load->model('report/adv_sale_profit_export');
    		$results = $this->model_report_adv_sale_profit_export->getSaleProfitExport($data);
			include(DIR_APPLICATION."controller/report/adv_reports/sop_export_html_customer_list.inc.php");

		} elseif (isset($this->request->post['export']) && $this->request->post['export'] == 10) { // export_html_all_details
			$this->load->model('report/adv_sale_profit_export_all');
    		$results = $this->model_report_adv_sale_profit_export_all->getSaleProfitExportAll($data);
			include(DIR_APPLICATION."controller/report/adv_reports/sop_export_html_all_details.inc.php");

		} elseif (isset($this->request->post['export']) && $this->request->post['export'] == 11) { // export_pdf
			$this->load->model('report/adv_sale_profit_export');
    		$results = $this->model_report_adv_sale_profit_export->getSaleProfitExport($data);
			require_once(DIR_SYSTEM . 'library/dompdf/dompdf_config.inc.php');
			include(DIR_APPLICATION."controller/report/adv_reports/sop_export_pdf.inc.php");
		
		} elseif (isset($this->request->post['export']) && $this->request->post['export'] == 12) { // export_pdf_order_list
			$this->load->model('report/adv_sale_profit_export');
    		$results = $this->model_report_adv_sale_profit_export->getSaleProfitExport($data);
			require_once(DIR_SYSTEM . 'library/dompdf/dompdf_config.inc.php');
			include(DIR_APPLICATION."controller/report/adv_reports/sop_export_pdf_order_list.inc.php");
			
		} elseif (isset($this->request->post['export']) && $this->request->post['export'] == 13) { // export_pdf_product_list
			$this->load->model('report/adv_sale_profit_export');
    		$results = $this->model_report_adv_sale_profit_export->getSaleProfitExport($data);
			require_once(DIR_SYSTEM . 'library/dompdf/dompdf_config.inc.php');
			include(DIR_APPLICATION."controller/report/adv_reports/sop_export_pdf_product_list.inc.php");
			
		} elseif (isset($this->request->post['export']) && $this->request->post['export'] == 14) { // export_pdf_customer_list
			$this->load->model('report/adv_sale_profit_export');
    		$results = $this->model_report_adv_sale_profit_export->getSaleProfitExport($data);
			require_once(DIR_SYSTEM . 'library/dompdf/dompdf_config.inc.php');
			include(DIR_APPLICATION."controller/report/adv_reports/sop_export_pdf_customer_list.inc.php");
			
		} elseif (isset($this->request->post['export']) && $this->request->post['export'] == 15) { // export_pdf_all_details
			$this->load->model('report/adv_sale_profit_export_all');
    		$results = $this->model_report_adv_sale_profit_export_all->getSaleProfitExportAll($data);
			require_once(DIR_SYSTEM . 'library/dompdf/dompdf_config.inc.php');
			include(DIR_APPLICATION."controller/report/adv_reports/sop_export_pdf_all_details.inc.php");			
		}			
	}
	
	public function customer_autocomplete() {
		$json = array();

		$this->data['token'] = $this->session->data['token'];
		
		if (isset($this->request->get['filter_customer_id']) || isset($this->request->get['filter_email']) || isset($this->request->get['filter_company'])) {
			$this->load->model('report/adv_sale_profit');
					
		if (isset($this->request->get['filter_company'])) {
			$filter_company = $this->request->get['filter_company'];
		} else {
			$filter_company = '';
		}
		
		if (isset($this->request->get['filter_customer_id'])) {
			$filter_customer_id = $this->request->get['filter_customer_id'];
		} else {
			$filter_customer_id = '';
		}

		if (isset($this->request->get['filter_email'])) {
			$filter_email = $this->request->get['filter_email'];
		} else {
			$filter_email = '';
		}	
		
		$data = array(		
			'filter_company' 	 		=> $filter_company,
			'filter_customer_id' 	 	=> $filter_customer_id,			
			'filter_email' 	 			=> $filter_email			
		);
						
		$results = $this->model_report_adv_sale_profit->getCustomerAutocomplete($data);
			
			foreach ($results as $result) {
				$json[] = array(
					'customer_id'     		=> $result['customer_id'],
					'cust_company'     		=> html_entity_decode($result['cust_company'], ENT_QUOTES, 'UTF-8'),					
					'cust_name'     		=> html_entity_decode($result['cust_name'], ENT_QUOTES, 'UTF-8'),
					'cust_email'     		=> $result['cust_email']					
				);
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}	
	
	public function product_autocomplete() {
		$json = array();

		$this->data['token'] = $this->session->data['token'];
		
		if (isset($this->request->get['filter_product_id'])) {
			$this->load->model('report/adv_sale_profit');
					
		if (isset($this->request->get['filter_product_id'])) {
			$filter_product_id = $this->request->get['filter_product_id'];
		} else {
			$filter_product_id = '';
		}
		
		$data = array(				
			'filter_product_id' 	 	=> $filter_product_id			
		);
						
		$results = $this->model_report_adv_sale_profit->getProductAutocomplete($data);
			
			foreach ($results as $result) {
				$json[] = array(
					'product_id'     		=> $result['product_id'],
					'prod_name'     		=> html_entity_decode($result['prod_name'], ENT_QUOTES, 'UTF-8')					
				);
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}	
}
?>