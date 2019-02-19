<?php
class ControllerReportSaleOrder extends Controller { 
	public function index() {  
		$this->load->language('report/sale_order');

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = '';
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = '';
		}
		
		if (isset($this->request->get['filter_group'])) {
			$filter_group = $this->request->get['filter_group'];
		} else {
			$filter_group = 'week';
		}

// Advanced Sales Report + Profit Reporting - START
		if (isset($this->request->get['filter_range'])) {
			$filter_range = $this->request->get['filter_range'];
		} else {
			$filter_range = 'month'; //show Month in Statistics Range by default
		}
		
		if (isset($this->request->get['filter_order_status_id'])) {
			$filter_order_status_id = $this->request->get['filter_order_status_id'];
		} else {
			$filter_order_status_id = '';
		}		

		if (isset($this->request->get['filter_store_id'])) {
			$filter_store_id = $this->request->get['filter_store_id'];
		} else {
			$filter_store_id = '';
		}	

		if (isset($this->request->get['filter_currency'])) {
			$filter_currency = $this->request->get['filter_currency'];
		} else {
			$filter_currency = '';
		}	

		if (isset($this->request->get['filter_taxes'])) {
			$filter_taxes = $this->request->get['filter_taxes'];
		} else {
			$filter_taxes = '';
		}	
		
		if (isset($this->request->get['filter_customer_group_id'])) {
			$filter_customer_group_id = $this->request->get['filter_customer_group_id'];
		} else {
			$filter_customer_group_id = '';
		}
		
		if (isset($this->request->get['filter_customer_id'])) {
			$filter_customer_id = $this->request->get['filter_customer_id'];
		} else {
			$filter_customer_id = '';
		}
		
		if (isset($this->request->get['filter_shipping'])) {
			$filter_shipping = $this->request->get['filter_shipping'];
		} else {
			$filter_shipping = '';
		}
		
		if (isset($this->request->get['filter_payment'])) {
			$filter_payment = $this->request->get['filter_payment'];
		} else {
			$filter_payment = '';
		}	
		
		if (isset($this->request->get['filter_shipping_country'])) {
			$filter_shipping_country = $this->request->get['filter_shipping_country'];
		} else {
			$filter_shipping_country = '';
		}
		
		if (isset($this->request->get['filter_payment_country'])) {
			$filter_payment_country = $this->request->get['filter_payment_country'];
		} else {
			$filter_payment_country = '';
		}			
// Advanced Sales Report + Profit Reporting - END

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$url = '';
						
		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}
		
		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}
		
		if (isset($this->request->get['filter_group'])) {
			$url .= '&filter_group=' . $this->request->get['filter_group'];
		}		
		
// Advanced Sales Report + Profit Reporting - START
		if (isset($this->request->get['filter_range'])) {
			$url .= '&filter_range=' . $this->request->get['filter_range'];
		}	
		
		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}

		if (isset($this->request->get['filter_store_id'])) {
			$url .= '&filter_store_id=' . $this->request->get['filter_store_id'];
		}		

		if (isset($this->request->get['filter_currency'])) {
			$url .= '&filter_currency=' . $this->request->get['filter_currency'];
		}

		if (isset($this->request->get['filter_taxes'])) {
			$url .= '&filter_taxes=' . $this->request->get['filter_taxes'];
		}
		
		if (isset($this->request->get['filter_customer_id'])) {
			$url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
		}	

		if (isset($this->request->get['filter_customer_group_id'])) {
			$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
		}
		
		if (isset($this->request->get['filter_shipping'])) {
			$url .= '&filter_shipping=' . $this->request->get['filter_shipping'];
		}		
		
		if (isset($this->request->get['filter_payment'])) {
			$url .= '&filter_payment=' . $this->request->get['filter_payment'];
		}

		if (isset($this->request->get['filter_shipping_country'])) {
			$url .= '&filter_shipping_country=' . $this->request->get['filter_shipping_country'];
		}		
		
		if (isset($this->request->get['filter_payment_country'])) {
			$url .= '&filter_payment_country=' . $this->request->get['filter_payment_country'];
		}
// Advanced Sales Report + Profit Reporting - END

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

   		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),       		
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('report/sale_order', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->load->model('report/sale');
		
		$this->data['orders'] = array();
		
		$data = array(
			'filter_date_start'	     => $filter_date_start, 
			'filter_date_end'	     => $filter_date_end, 
			'filter_group'           => $filter_group,
// Advanced Sales Report + Profit Reporting - START			
			'filter_range'           	=> $filter_range,
			'filter_order_status_id'	=> $filter_order_status_id,
			'filter_store_id' 	 	 	=> $filter_store_id,
			'filter_currency' 	 	 	=> $filter_currency,
			'filter_taxes' 	 	 		=> $filter_taxes,			
			'filter_customer_id' 	 	=> $filter_customer_id,
			'filter_customer_group_id'  => $filter_customer_group_id,
			'filter_shipping'  			=> $filter_shipping,
			'filter_payment'  			=> $filter_payment,
			'filter_shipping_country'  	=> $filter_shipping_country,
			'filter_payment_country'  	=> $filter_payment_country,			
// Advanced Sales Report + Profit Reporting - END				
			'start'                  => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit'                  => $this->config->get('config_admin_limit')
		);
		
		$order_total = $this->model_report_sale->getTotalOrders($data);
		
		$results = $this->model_report_sale->getOrders($data);

// Advanced Sales Report + Profit Reporting - START
		if (isset($this->request->get['option'])) {
			$option = $this->request->get['option'];
		} else {
			$option = 'filter';
		}

		if ($option == 'filter') {
			foreach ($results as $result) {
				
				$this->data['orders'][] = array(
					'temp'   			=> $result['temp'],
					'date_start' 		=> date($this->language->get('date_format_short'), strtotime($result['date_start'])),
					'date_end'   		=> date($this->language->get('date_format_short'), strtotime($result['date_end'])),					
					'order_id'   		=> $result['id'],
					'order_idc'     	=> $result['idc'],
					'order_date'   		=> $result['order_date'],
					'inv_prefix'    	=> $result['inv_prefix'],
					'inv_id'     		=> $result['inv_id'],
					'store'      		=> $result['store'],
					'cust_name'   		=> $result['cust_name'],
					'cust_email'   		=> $result['cust_email'],
					'cust_group'   		=> $result['cust_group'],
					'shipping_method'  	=> $result['shipping_method'],
					'payment_method'  	=> $result['payment_method'],					
					'os_name'  			=> $result['os_name'],
					'order_quantity' 	=> $result['order_quantity'],
					'order_currency' 	=> $result['order_currency'],
					'order_sub_total'  	=> $result['order_sub_total'],
					'order_shipping'  	=> $result['order_shipping'],
					'order_tax'  		=> $result['order_tax'],					
					'order_value'  		=> $result['order_value'],
					'order_profit'   	=> $result['order_profit'],					
					'orders'     		=> $result['orders'],
					'products'   		=> $result['products'],
					'customers'   		=> $result['customers'],
					'sub_total'        	=> $this->currency->format($result['sub_total'], $this->config->get('config_currency')),
					'reward'      		=> $this->currency->format($result['reward'], $this->config->get('config_currency')),
					'shipping'        	=> $this->currency->format($result['shipping'], $this->config->get('config_currency')),
					'coupon'      		=> $this->currency->format($result['coupon'], $this->config->get('config_currency')),
					'tax'        		=> $this->currency->format($result['tax'], $this->config->get('config_currency')),
					'credit'      		=> $this->currency->format($result['credit'], $this->config->get('config_currency')),
					'voucher'        	=> $this->currency->format($result['voucher'], $this->config->get('config_currency')),
					'total'      		=> $this->currency->format($result['total'], $this->config->get('config_currency')),
					'costs'      		=> $this->currency->format(-$result['costs'], $this->config->get('config_currency')),					
					'netprofit'      	=> $this->currency->format($result['grossprofit']+$result['reward']+$result['credit']+$result['coupon'], $this->config->get('config_currency'))
				);
			}

		$this->data['text_no_grouping'] = $this->language->get('text_no_grouping');
		$this->data['text_all_stores'] = $this->language->get('text_all_stores');
		$this->data['text_all_currencies'] = $this->language->get('text_all_currencies');
		$this->data['text_all_taxes'] = $this->language->get('text_all_taxes');		
		$this->data['text_all_customers'] = $this->language->get('text_all_customers');	
		$this->data['text_all_groups'] = $this->language->get('text_all_groups');
		$this->data['text_all_shippings'] = $this->language->get('text_all_shippings');
		$this->data['text_all_payments'] = $this->language->get('text_all_payments');
		$this->data['text_all_countries'] = $this->language->get('text_all_countries');			
		$this->data['text_detail'] = $this->language->get('text_detail');
		$this->data['text_export_xls'] = $this->language->get('text_export_xls');
		$this->data['text_export_xls_detail'] = $this->language->get('text_export_xls_detail');
		$this->data['text_export_html'] = $this->language->get('text_export_html');
		$this->data['text_export_html_detail'] = $this->language->get('text_export_html_detail');	
		
    	$this->data['column_customers'] = $this->language->get('column_customers');
		$this->data['column_sub_total'] = $this->language->get('column_sub_total');
		$this->data['column_points'] = $this->language->get('column_points');
		$this->data['column_shipping'] = $this->language->get('column_shipping');		
		$this->data['column_coupon'] = $this->language->get('column_coupon');
		$this->data['column_credit'] = $this->language->get('column_credit');	
		$this->data['column_voucher'] = $this->language->get('column_voucher');		
		$this->data['column_costs'] = $this->language->get('column_costs');
		$this->data['column_net_profit'] = $this->language->get('column_net_profit');
		$this->data['column_action'] = $this->language->get('column_action');
		$this->data['column_date_added'] = $this->language->get('column_date_added');
		$this->data['column_order_id'] = $this->language->get('column_order_id');
		$this->data['column_inv_date'] = $this->language->get('column_inv_date');
		$this->data['column_inv_id'] = $this->language->get('column_inv_id');		
		$this->data['column_name'] = $this->language->get('column_name');
		$this->data['column_email'] = $this->language->get('column_email');
		$this->data['column_customer_group'] = $this->language->get('column_customer_group');		
		$this->data['column_shipping_method'] = $this->language->get('column_shipping_method');
		$this->data['column_payment_method'] = $this->language->get('column_payment_method');		
		$this->data['column_order_status'] = $this->language->get('column_order_status');
		$this->data['column_store'] = $this->language->get('column_store');
		$this->data['column_order_quantity'] = $this->language->get('column_order_quantity');
		$this->data['column_order_currency'] = $this->language->get('column_order_currency');
		$this->data['column_order_total'] = $this->language->get('column_order_total');	
		$this->data['column_order_profit'] = $this->language->get('column_order_profit');	
		
		$this->data['entry_range'] = $this->language->get('entry_range');
		$this->data['entry_store'] = $this->language->get('entry_store');
		$this->data['entry_currency'] = $this->language->get('entry_currency');	
		$this->data['entry_tax'] = $this->language->get('entry_tax');		
		$this->data['entry_customer'] = $this->language->get('entry_customer');	
		$this->data['entry_customer_group'] = $this->language->get('entry_customer_group');
		$this->data['entry_shipping'] = $this->language->get('entry_shipping');
		$this->data['entry_payment'] = $this->language->get('entry_payment');
		$this->data['entry_shipping_country'] = $this->language->get('entry_shipping_country');
		$this->data['entry_payment_country'] = $this->language->get('entry_payment_country');			

		$this->data['button_export'] = $this->language->get('button_export');
// Advanced Sales Report + Profit Reporting - END

		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		$this->data['text_all_status'] = $this->language->get('text_all_status');
		
		$this->data['column_date_start'] = $this->language->get('column_date_start');
		$this->data['column_date_end'] = $this->language->get('column_date_end');
    	$this->data['column_orders'] = $this->language->get('column_orders');
		$this->data['column_products'] = $this->language->get('column_products');
		$this->data['column_tax'] = $this->language->get('column_tax');
		$this->data['column_total'] = $this->language->get('column_total');
		
		$this->data['entry_date_start'] = $this->language->get('entry_date_start');
		$this->data['entry_date_end'] = $this->language->get('entry_date_end');
		$this->data['entry_group'] = $this->language->get('entry_group');	
		$this->data['entry_status'] = $this->language->get('entry_status');

		$this->data['button_filter'] = $this->language->get('button_filter');
		
		$this->data['token'] = $this->session->data['token'];

// Advanced Sales Report + Profit Reporting - START
		$this->data['order_statuses'] = $this->model_report_sale->getOrderStatuses(); 	
		$this->data['stores'] = $this->model_report_sale->getOrderStores();			
		$this->data['currencies'] = $this->model_report_sale->getOrderCurrencies();
		$this->data['taxes'] = $this->model_report_sale->getOrderTaxes();		
		$this->data['customers'] = $this->model_report_sale->getOrderCustomers();
		$this->data['customer_groups'] = $this->model_report_sale->getOrderCustomerGroups();	
		$this->data['shippings'] = $this->model_report_sale->getOrderShipping();
		$this->data['payments'] = $this->model_report_sale->getOrderPayment();	
		$this->data['shipping_countries'] = $this->model_report_sale->getShippingCountries();
		$this->data['payment_countries'] = $this->model_report_sale->getPaymentCountries();			
		
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
			'text'  => $this->language->get('stat_all'),
			'value' => 'all',
		);		

		$this->data['groups'] = array();

		$this->data['groups'][] = array(
			'text'  => $this->language->get('text_no_grouping'),
			'value' => 'all',
		);
		
		$this->data['groups'][] = array(
			'text'  => $this->language->get('text_year'),
			'value' => 'year',
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
// Advanced Sales Report + Profit Reporting - END

		$url = '';
						
		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}
		
		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}
		
		if (isset($this->request->get['filter_group'])) {
			$url .= '&filter_group=' . $this->request->get['filter_group'];
		}		

// Advanced Sales Report + Profit Reporting - START
		if (isset($this->request->get['filter_range'])) {
			$url .= '&filter_range=' . $this->request->get['filter_range'];
		}
		
		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}

		if (isset($this->request->get['filter_store_id'])) {
			$url .= '&filter_store_id=' . $this->request->get['filter_store_id'];
		}	

		if (isset($this->request->get['filter_currency'])) {
			$url .= '&filter_currency=' . $this->request->get['filter_currency'];
		}	

		if (isset($this->request->get['filter_taxes'])) {
			$url .= '&filter_taxes=' . $this->request->get['filter_taxes'];
		}	
		
		if (isset($this->request->get['filter_customer_id'])) {
		    $url .= '&filter_customer_id=' . $this->request->get['filter_customer_id'];
		}

		if (isset($this->request->get['filter_customer_group_id'])) {
			$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
		}
		
		if (isset($this->request->get['filter_shipping'])) {
			$url .= '&filter_shipping=' . $this->request->get['filter_shipping'];
		}
		
		if (isset($this->request->get['filter_payment'])) {
			$url .= '&filter_payment=' . $this->request->get['filter_payment'];
		}
		
		if (isset($this->request->get['filter_shipping_country'])) {
			$url .= '&filter_shipping_country=' . $this->request->get['filter_shipping_country'];
		}
		
		if (isset($this->request->get['filter_payment_country'])) {
			$url .= '&filter_payment_country=' . $this->request->get['filter_payment_country'];
		}			
// Advanced Sales Report + Profit Reporting - END

		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
// Advanced Sales Report + Profit Reporting - START		
		$pagination->limit = 9999;
// Advanced Sales Report + Profit Reporting - END
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('report/sale_order', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
			
		$this->data['pagination'] = $pagination->render();		

		$this->data['filter_date_start'] = $filter_date_start;
		$this->data['filter_date_end'] = $filter_date_end;		
		$this->data['filter_group'] = $filter_group;
// Advanced Sales Report + Profit Reporting - START		
		$this->data['filter_range'] = $filter_range;
		$this->data['filter_order_status_id'] = $filter_order_status_id;
		$this->data['filter_store_id'] = $filter_store_id;
		$this->data['filter_currency'] = $filter_currency;
		$this->data['filter_taxes'] = $filter_taxes;		
		$this->data['filter_customer_id'] = $filter_customer_id; 
		$this->data['filter_customer_group_id'] = $filter_customer_group_id;
		$this->data['filter_shipping'] = $filter_shipping;
		$this->data['filter_payment'] = $filter_payment;
		$this->data['filter_shipping_country'] = $filter_shipping_country;
		$this->data['filter_payment_country'] = $filter_payment_country;		
// Advanced Sales Report + Profit Reporting - END	
				 
		$this->template = 'report/sale_order.tpl';
		$this->children = array(
			'common/header',
			'common/footer',
		);
				
		$this->response->setOutput($this->render());

// Advanced Sales Report + Profit Reporting - START
		} elseif ($option == 'xls') {
				$xls_output ="<html><head>";
				$xls_output .="<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
				$xls_output .="</head>";
				$xls_output .="<body>";				
				$xls_output .="<table border='1'>";	
				$xls_output .="<tr>";
				$xls_output .= "<td align='left' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_date_start')."</td>";
				$xls_output .= "<td align='left' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_date_end')."</td>";				
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_customers')."</td>";
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_orders')."</td>";
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_products')."</td>";
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_sub_total')."</td>";
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_points')."</td>";				
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_shipping')."</td>";
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_coupon')."</td>";				
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_tax')."</td>";
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_credit')."</td>";				
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_voucher')."</td>";				
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_total')."</td>";
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_costs')."</td>";
				$xls_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_net_profit')."</td>";				
				$xls_output .="</tr>";
				foreach ($results as $result) {						
					$xls_output .="<tr>";
					$xls_output .= "<td align='left'>".date($this->language->get('date_format_short'), strtotime($result['date_start']))."</td>";
					$xls_output .= "<td align='left'>".date($this->language->get('date_format_short'), strtotime($result['date_end']))."</td>";				
					$xls_output .= "<td align='right'>".$result['customers']."</td>";
					$xls_output .= "<td align='right'>".$result['orders']."</td>";
					$xls_output .= "<td align='right'>".$result['products']."</td>";
					$xls_output .= "<td align='right'>".$this->currency->format($result['sub_total'], $this->config->get('config_currency'))."</td>";
					$xls_output .= "<td align='right'>".$this->currency->format($result['reward'], $this->config->get('config_currency'))."</td>";					
					$xls_output .= "<td align='right'>".$this->currency->format($result['shipping'], $this->config->get('config_currency'))."</td>";
					$xls_output .= "<td align='right'>".$this->currency->format($result['coupon'], $this->config->get('config_currency'))."</td>";					
					$xls_output .= "<td align='right'>".$this->currency->format($result['tax'], $this->config->get('config_currency'))."</td>";
					$xls_output .= "<td align='right'>".$this->currency->format($result['credit'], $this->config->get('config_currency'))."</td>";					
					$xls_output .= "<td align='right'>".$this->currency->format($result['voucher'], $this->config->get('config_currency'))."</td>";				
					$xls_output .= "<td align='right'>".$this->currency->format($result['total'], $this->config->get('config_currency'))."</td>";
					$xls_output .= "<td align='right'>".$this->currency->format(-$result['costs'], $this->config->get('config_currency'))."</td>";
					$xls_output .= "<td align='right'>".$this->currency->format($result['grossprofit']+$result['reward']+$result['credit']+$result['coupon'], $this->config->get('config_currency'))."</td>";					
					$xls_output .="</tr>";				
				}
				$xls_output .="</body></html>";

			$filename = "sales_report_profit_simple_".date("Y-m-d",time());
			header('Expires: 0');
			header('Cache-control: private');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-Description: File Transfer');			
			header('Content-Type: application/vnd.ms-excel; charset=UTF-8; encoding=UTF-8');			
			header('Content-Disposition: attachment; filename='.$filename.".xls");
			header('Content-Transfer-Encoding: UTF-8');	
			print $xls_output;			
			exit;
			
		} elseif ($option == 'xls_detail') {
				$xls_detail_output ="<html><head>";
				$xls_detail_output .="<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
				$xls_detail_output .="</head>";
				$xls_detail_output .="<body>";
				foreach ($results as $result) {					
				$xls_detail_output .="<table border='1'>";		
				$xls_detail_output .="<tr>";
				$xls_detail_output .= "<td align='left' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_date_start')."</td>";
				$xls_detail_output .= "<td align='left' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_date_end')."</td>";				
				$xls_detail_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_customers')."</td>";
				$xls_detail_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_orders')."</td>";
				$xls_detail_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_products')."</td>";
				$xls_detail_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_sub_total')."</td>";
				$xls_detail_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_points')."</td>";				
				$xls_detail_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_shipping')."</td>";
				$xls_detail_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_coupon')."</td>";				
				$xls_detail_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_tax')."</td>";
				$xls_detail_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_credit')."</td>";				
				$xls_detail_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_voucher')."</td>";				
				$xls_detail_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_total')."</td>";
				$xls_detail_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_costs')."</td>";
				$xls_detail_output .= "<td align='right' style='background-color:#D8D8D8; font-weight:bold;'>".$this->language->get('column_net_profit')."</td>";					
				$xls_detail_output .="</tr>";				
					$xls_detail_output .="<tr>";
					$xls_detail_output .= "<td align='left'>".date($this->language->get('date_format_short'), strtotime($result['date_start']))."</td>";
					$xls_detail_output .= "<td align='left'>".date($this->language->get('date_format_short'), strtotime($result['date_end']))."</td>";				
					$xls_detail_output .= "<td align='right'>".$result['customers']."</td>";
					$xls_detail_output .= "<td align='right'>".$result['orders']."</td>";
					$xls_detail_output .= "<td align='right'>".$result['products']."</td>";
					$xls_detail_output .= "<td align='right'>".$this->currency->format($result['sub_total'], $this->config->get('config_currency'))."</td>";
					$xls_detail_output .= "<td align='right'>".$this->currency->format($result['reward'], $this->config->get('config_currency'))."</td>";					
					$xls_detail_output .= "<td align='right'>".$this->currency->format($result['shipping'], $this->config->get('config_currency'))."</td>";
					$xls_detail_output .= "<td align='right'>".$this->currency->format($result['coupon'], $this->config->get('config_currency'))."</td>";					
					$xls_detail_output .= "<td align='right'>".$this->currency->format($result['tax'], $this->config->get('config_currency'))."</td>";
					$xls_detail_output .= "<td align='right'>".$this->currency->format($result['credit'], $this->config->get('config_currency'))."</td>";					
					$xls_detail_output .= "<td align='right'>".$this->currency->format($result['voucher'], $this->config->get('config_currency'))."</td>";				
					$xls_detail_output .= "<td align='right'>".$this->currency->format($result['total'], $this->config->get('config_currency'))."</td>";
					$xls_detail_output .= "<td align='right'>".$this->currency->format(-$result['costs'], $this->config->get('config_currency'))."</td>";
					$xls_detail_output .= "<td align='right'>".$this->currency->format($result['grossprofit']+$result['reward']+$result['credit']+$result['coupon'], $this->config->get('config_currency'))."</td>";
					$xls_detail_output .="</tr>";
					$xls_detail_output .="<tr>";
					$xls_detail_output .= "<td colspan='2'></td>";
					$xls_detail_output .= "<td colspan='13' align='center'>";
						$xls_detail_output .="<table border='1'>";
						$xls_detail_output .="<tr>";
						$xls_detail_output .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".$this->language->get('column_order_id')."</td>";					
						$xls_detail_output .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".$this->language->get('column_date_added')."</td>";
						$xls_detail_output .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".$this->language->get('column_inv_id')."</td>";										
						$xls_detail_output .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".$this->language->get('column_name')."</td>";
						$xls_detail_output .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".$this->language->get('column_email')."</td>";
						$xls_detail_output .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".$this->language->get('column_customer_group')."</td>";
						$xls_detail_output .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".$this->language->get('column_shipping_method')."</td>";
						$xls_detail_output .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".$this->language->get('column_payment_method')."</td>";						
						$xls_detail_output .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".$this->language->get('column_order_status')."</td>";
						$xls_detail_output .= "<td align='left' style='background-color:#EFEFEF; font-weight:bold;'>".$this->language->get('column_store')."</td>";
						$xls_detail_output .= "<td align='right' style='background-color:#EFEFEF; font-weight:bold;'>".$this->language->get('column_order_quantity')."</td>";	
						$xls_detail_output .= "<td align='right' style='background-color:#EFEFEF; font-weight:bold;'>".$this->language->get('column_order_currency')."</td>";
						$xls_detail_output .= "<td align='right' style='background-color:#EFEFEF; font-weight:bold;'>".$this->language->get('column_sub_total')."</td>";
						$xls_detail_output .= "<td align='right' style='background-color:#EFEFEF; font-weight:bold;'>".$this->language->get('column_shipping')."</td>";
						$xls_detail_output .= "<td align='right' style='background-color:#EFEFEF; font-weight:bold;'>".$this->language->get('column_tax')."</td>";							
						$xls_detail_output .= "<td align='right' style='background-color:#EFEFEF; font-weight:bold;'>".$this->language->get('column_order_total')."</td>";
						$xls_detail_output .= "<td align='right' style='background-color:#EFEFEF; font-weight:bold;'>".$this->language->get('column_order_profit')."</td>";							
						$xls_detail_output .="</tr>";
						$xls_detail_output .="<tr>";
						$xls_detail_output .= "<td align='left'>".$result['idc']."</td>";					
						$xls_detail_output .= "<td align='left'>".$result['order_date']."</td>";
						$xls_detail_output .= "<td align='left'><table border='0'>";
						$xls_detail_output .="<tr>";
						$xls_detail_output .= "<td align='right'>".$result['inv_prefix']."</td>";
						$xls_detail_output .= "<td align='left'>".$result['inv_id']."</td>";
						$xls_detail_output .="</tr>";					
						$xls_detail_output .="</table></td>";					
						$xls_detail_output .= "<td align='left'>".$result['cust_name']."</td>";
						$xls_detail_output .= "<td align='left'>".$result['cust_email']."</td>";
						$xls_detail_output .= "<td align='left'>".$result['cust_group']."</td>";
						$xls_detail_output .= "<td align='left'>".$result['shipping_method']."</td>";
						$xls_detail_output .= "<td align='left'>".$result['payment_method']."</td>";							
						$xls_detail_output .= "<td align='left'>".$result['os_name']."</td>";
						$xls_detail_output .= "<td align='left'>".$result['store']."</td>";
						$xls_detail_output .= "<td align='right'>".$result['order_quantity']."</td>";
						$xls_detail_output .= "<td align='right'>".$result['order_currency']."</td>";
						$xls_detail_output .= "<td align='right'>".$result['order_sub_total']."</td>";
						$xls_detail_output .= "<td align='right'>".$result['order_shipping']."</td>";
						$xls_detail_output .= "<td align='right'>".$result['order_tax']."</td>";						
						$xls_detail_output .= "<td align='right'>".$result['order_value']."</td>";
						$xls_detail_output .= "<td align='right'>".$result['order_profit']."</td>";						
						$xls_detail_output .="</tr>";					
						$xls_detail_output .="</table>";
				$xls_detail_output .="</td>";
				$xls_detail_output .="</tr>";					
			}
				$xls_detail_output .="</body></html>";

			$filename = "sales_report_profit_advanced_".date("Y-m-d",time());
			header('Expires: 0');
			header('Cache-control: private');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-Description: File Transfer');			
			header('Content-Type: application/vnd.ms-excel; charset=UTF-8; encoding=UTF-8');			
			header('Content-Disposition: attachment; filename='.$filename.".xls");
			header('Content-Transfer-Encoding: UTF-8');
			print $xls_detail_output;			
			exit;
			
		} elseif ($option == 'html') {
			$html_output ="<html><head>";
			$html_output .="<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
			$html_output .="</head>";
			$html_output .="<body>";
			$html_output .="<style type='text/css'>
.list_main {
	border-collapse: collapse;
	width: 100%;
	border-top: 1px solid #DDDDDD;
	border-left: 1px solid #DDDDDD;	
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
.list_main td {
	border-right: 1px solid #DDDDDD;
	border-bottom: 1px solid #DDDDDD;	
}
.list_main thead td {
	background-color: #E5E5E5;
	padding: 3px;
	font-weight: bold;
}
.list_main tbody a {
	text-decoration: none;
}
.list_main tbody td {
	vertical-align: middle;
	padding: 3px;
}
.list_main .left {
	text-align: left;
	padding: 7px;
}
.list_main .right {
	text-align: right;
	padding: 7px;
}
.list_main .center {
	text-align: center;
	padding: 3px;
}
</style>";
				$html_output .="<table class='list_main'>";
				$html_output .="<thead>";
				$html_output .="<tr>";
				$html_output .= "<td align='left' width='80' nowrap='nowrap'>".$this->language->get('column_date_start')."</td>";
				$html_output .= "<td align='left' width='80' nowrap='nowrap'>".$this->language->get('column_date_end')."</td>";				
				$html_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_customers')."</td>";
				$html_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_orders')."</td>";
				$html_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_products')."</td>";
				$html_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_sub_total')."</td>";	
				$html_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_points')."</td>";				
				$html_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_shipping')."</td>";
				$html_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_coupon')."</td>";				
				$html_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_tax')."</td>";
				$html_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_credit')."</td>";					
				$html_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_voucher')."</td>";				
				$html_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_total')."</td>";
				$html_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_costs')."</td>";
				$html_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_net_profit')."</td>";			
				$html_output .="</tr>";
				$html_output .="</thead><tbody>";
				foreach ($results as $result) {
					$html_output .="<tr>";
					$html_output .= "<td align='left' nowrap='nowrap'>".date($this->language->get('date_format_short'), strtotime($result['date_start']))."</td>";
					$html_output .= "<td align='left' nowrap='nowrap'>".date($this->language->get('date_format_short'), strtotime($result['date_end']))."</td>";				
					$html_output .= "<td align='right' nowrap='nowrap'>".$result['customers']."</td>";
					$html_output .= "<td align='right' nowrap='nowrap'>".$result['orders']."</td>";
					$html_output .= "<td align='right' nowrap='nowrap'>".$result['products']."</td>";
					$html_output .= "<td align='right' nowrap='nowrap' style='background-color:#DCFFB9;'>".$this->currency->format($result['sub_total'], $this->config->get('config_currency'))."</td>";	
					$html_output .= "<td align='right' nowrap='nowrap' style='background-color:#ffd7d7;'>".$this->currency->format($result['reward'], $this->config->get('config_currency'))."</td>";					
					$html_output .= "<td align='right' nowrap='nowrap'>".$this->currency->format($result['shipping'], $this->config->get('config_currency'))."</td>";
					$html_output .= "<td align='right' nowrap='nowrap' style='background-color:#ffd7d7;'>".$this->currency->format($result['coupon'], $this->config->get('config_currency'))."</td>";					
					$html_output .= "<td align='right' nowrap='nowrap'>".$this->currency->format($result['tax'], $this->config->get('config_currency'))."</td>";
					$html_output .= "<td align='right' nowrap='nowrap' style='background-color:#ffd7d7;'>".$this->currency->format($result['credit'], $this->config->get('config_currency'))."</td>";					
					$html_output .= "<td align='right' nowrap='nowrap'>".$this->currency->format($result['voucher'], $this->config->get('config_currency'))."</td>";				
					$html_output .= "<td align='right' nowrap='nowrap'>".$this->currency->format($result['total'], $this->config->get('config_currency'))."</td>";
					$html_output .= "<td align='right' nowrap='nowrap' style='background-color:#ffd7d7;'>".$this->currency->format(-$result['costs'], $this->config->get('config_currency'))."</td>";
					$html_output .= "<td align='right' nowrap='nowrap' style='background-color:#DCFFB9; font-weight:bold;'>".$this->currency->format($result['grossprofit']+$result['reward']+$result['credit']+$result['coupon'], $this->config->get('config_currency'))."</td>";					
					$html_output .="</tr>";				
				}
				$html_output .="</tbody></table>";
				$html_output .="</body></html>";

			$filename = "sales_report_profit_simple_".date("Y-m-d",time());
			header('Expires: 0');
			header('Cache-control: private');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-Description: File Transfer');			
			header('Content-Disposition: attachment; filename='.$filename.".html");
			print $html_output;			
			exit;
			
		} elseif ($option == 'html_detail') {
			$html_detail_output ="<html><head>";
			$html_detail_output .="<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
			$html_detail_output .="</head>";
			$html_detail_output .="<body>";
			$html_detail_output .="<style type='text/css'>
.list_main {
	border-collapse: collapse;
	width: 100%;
	border-top: 1px solid #DDDDDD;
	border-left: 1px solid #DDDDDD;
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
}
.list_main td {
	border-right: 1px solid #DDDDDD;
	border-bottom: 1px solid #DDDDDD;	
}
.list_main thead td {
	background-color: #E5E5E5;
	padding: 3px;
}
.list_main thead td a, .list_main thead td {
	text-decoration: none;
	font-weight: bold;
}
.list_main tbody a {
	text-decoration: none;
}
.list_main tbody td {
	vertical-align: middle;
	padding: 3px;
}
.list_main .left {
	text-align: left;
	padding: 7px;
}
.list_main .right {
	text-align: right;
	padding: 7px;
}
.list_main .center {
	text-align: center;
	padding: 3px;
}

.list_detail {
	border-collapse: collapse;
	width: 100%;
	border-top: 1px solid #DDDDDD;
	border-left: 1px solid #DDDDDD;
	margin-top: 10px;
	margin-bottom: 10px;
}
.list_detail td {
	border-right: 1px solid #DDDDDD;
	border-bottom: 1px solid #DDDDDD;
}
.list_detail thead td {
	background-color: #F0F0F0;
	padding: 0px 3px;
	font-size: 11px;
}
.list_detail tbody td {
	padding: 0px 3px;
	font-size: 11px;	
}
.list_detail .left {
	text-align: left;
	padding: 3px;
}
.list_detail .right {
	text-align: right;
	padding: 3px;
}
.list_detail .center {
	text-align: center;
	padding: 3px;
}
</style>";
				foreach ($results as $result) {	
				$html_detail_output .="<table class='list_main'>";
				$html_detail_output .="<thead>";
				$html_detail_output .="<tr>";
				$html_detail_output .= "<td align='left' width='80' nowrap='nowrap'>".$this->language->get('column_date_start')."</td>";
				$html_detail_output .= "<td align='left' width='80' nowrap='nowrap'>".$this->language->get('column_date_end')."</td>";				
				$html_detail_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_customers')."</td>";
				$html_detail_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_orders')."</td>";
				$html_detail_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_products')."</td>";
				$html_detail_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_sub_total')."</td>";	
				$html_detail_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_points')."</td>";				
				$html_detail_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_shipping')."</td>";
				$html_detail_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_coupon')."</td>";				
				$html_detail_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_tax')."</td>";
				$html_detail_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_credit')."</td>";					
				$html_detail_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_voucher')."</td>";				
				$html_detail_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_total')."</td>";
				$html_detail_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_costs')."</td>";
				$html_detail_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_net_profit')."</td>";				
				$html_detail_output .="</tr>";
				$html_detail_output .="</thead><tbody>";				
					$html_detail_output .="<tr>";
					$html_detail_output .= "<td align='left' nowrap='nowrap'>".date($this->language->get('date_format_short'), strtotime($result['date_start']))."</td>";
					$html_detail_output .= "<td align='left' nowrap='nowrap'>".date($this->language->get('date_format_short'), strtotime($result['date_end']))."</td>";				
					$html_detail_output .= "<td align='right' nowrap='nowrap'>".$result['customers']."</td>";
					$html_detail_output .= "<td align='right' nowrap='nowrap'>".$result['orders']."</td>";
					$html_detail_output .= "<td align='right' nowrap='nowrap'>".$result['products']."</td>";
					$html_detail_output .= "<td align='right' nowrap='nowrap' style='background-color:#DCFFB9;'>".$this->currency->format($result['sub_total'], $this->config->get('config_currency'))."</td>";	
					$html_detail_output .= "<td align='right' nowrap='nowrap' style='background-color:#ffd7d7;'>".$this->currency->format($result['reward'], $this->config->get('config_currency'))."</td>";					
					$html_detail_output .= "<td align='right' nowrap='nowrap'>".$this->currency->format($result['shipping'], $this->config->get('config_currency'))."</td>";
					$html_detail_output .= "<td align='right' nowrap='nowrap' style='background-color:#ffd7d7;'>".$this->currency->format($result['coupon'], $this->config->get('config_currency'))."</td>";					
					$html_detail_output .= "<td align='right' nowrap='nowrap'>".$this->currency->format($result['tax'], $this->config->get('config_currency'))."</td>";
					$html_detail_output .= "<td align='right' nowrap='nowrap' style='background-color:#ffd7d7;'>".$this->currency->format($result['credit'], $this->config->get('config_currency'))."</td>";					
					$html_detail_output .= "<td align='right' nowrap='nowrap'>".$this->currency->format($result['voucher'], $this->config->get('config_currency'))."</td>";				
					$html_detail_output .= "<td align='right' nowrap='nowrap'>".$this->currency->format($result['total'], $this->config->get('config_currency'))."</td>";
					$html_detail_output .= "<td align='right' nowrap='nowrap' style='background-color:#ffd7d7;'>".$this->currency->format(-$result['costs'], $this->config->get('config_currency'))."</td>";
					$html_detail_output .= "<td align='right' nowrap='nowrap' style='background-color:#DCFFB9; font-weight:bold;'>".$this->currency->format($result['grossprofit']+$result['reward']+$result['credit']+$result['coupon'], $this->config->get('config_currency'))."</td>";						
					$html_detail_output .="</tr>";
					$html_detail_output .="<tr>";
					$html_detail_output .= "<td colspan='15' align='center'>";
						$html_detail_output .="<table class='list_detail'>";
						$html_detail_output .="<thead>";
						$html_detail_output .="<tr>";
						$html_detail_output .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_order_id')."</td>";					
						$html_detail_output .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_date_added')."</td>";
						$html_detail_output .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_inv_id')."</td>";	
						$html_detail_output .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_name')."</td>";
						$html_detail_output .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_email')."</td>";
						$html_detail_output .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_customer_group')."</td>";
						$html_detail_output .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_shipping_method')."</td>";
						$html_detail_output .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_payment_method')."</td>";							
						$html_detail_output .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_order_status')."</td>";
						$html_detail_output .= "<td align='left' nowrap='nowrap'>".$this->language->get('column_store')."</td>";
						$html_detail_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_order_quantity')."</td>";	
						$html_detail_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_order_currency')."</td>";
						$html_detail_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_sub_total')."</td>";	
						$html_detail_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_shipping')."</td>";	
						$html_detail_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_tax')."</td>";								
						$html_detail_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_order_total')."</td>";	
						$html_detail_output .= "<td align='right' nowrap='nowrap'>".$this->language->get('column_order_profit')."</td>";							
						$html_detail_output .="</tr>";
						$html_detail_output .="</thead><tbody>";
						$html_detail_output .="<tr>";
						$html_detail_output .= "<td align='left' nowrap='nowrap'>".$result['idc']."</td>";					
						$html_detail_output .= "<td align='left' nowrap='nowrap'>".$result['order_date']."</td>";
						$html_detail_output .= "<td align='left' nowrap='nowrap'><div style='display:inline-block; float:left;'>".$result['inv_prefix']."</div><div style='display:inline-block; float:left;'>".$result['inv_id']."</div></td>";						
						$html_detail_output .= "<td align='left' nowrap='nowrap'>".$result['cust_name']."</td>";
						$html_detail_output .= "<td align='left' nowrap='nowrap'>".$result['cust_email']."</td>";
						$html_detail_output .= "<td align='left' nowrap='nowrap'>".$result['cust_group']."</td>";
						$html_detail_output .= "<td align='left' nowrap='nowrap'>".$result['shipping_method']."</td>";
						$html_detail_output .= "<td align='left' nowrap='nowrap'>".$result['payment_method']."</td>";						
						$html_detail_output .= "<td align='left' nowrap='nowrap'>".$result['os_name']."</td>";
						$html_detail_output .= "<td align='left' nowrap='nowrap'>".$result['store']."</td>";
						$html_detail_output .= "<td align='right' nowrap='nowrap'>".$result['order_quantity']."</td>";
						$html_detail_output .= "<td align='right' nowrap='nowrap'>".$result['order_currency']."</td>";
						$html_detail_output .= "<td align='right' nowrap='nowrap'>".$result['order_sub_total']."</td>";
						$html_detail_output .= "<td align='right' nowrap='nowrap'>".$result['order_shipping']."</td>";
						$html_detail_output .= "<td align='right' nowrap='nowrap'>".$result['order_tax']."</td>";							
						$html_detail_output .= "<td align='right' nowrap='nowrap'>".$result['order_value']."</td>";
						$html_detail_output .= "<td align='right' nowrap='nowrap' style='background-color:#DCFFB9; font-weight:bold;'>".$result['order_profit']."</td>";						
						$html_detail_output .="</tr>";					
						$html_detail_output .="</tbody></table>";
						$html_detail_output .="</td>";
						$html_detail_output .="</tr>";								
				}
				$html_detail_output .="</tbody></table>";
				$html_detail_output .="</body></html>";

			$filename = "sales_report_profit_advanced_".date("Y-m-d",time());
			header('Expires: 0');
			header('Cache-control: private');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Content-Description: File Transfer');			
			header('Content-Disposition: attachment; filename='.$filename.".html");
			print $html_detail_output;			
			exit;			
		}		
// Advanced Sales Report + Profit Reporting - END

	}
}
?>