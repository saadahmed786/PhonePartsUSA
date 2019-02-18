<?php
class ControllerReportOrderPayment extends Controller { 
	public function index() {  
		$this->language->load('pos/payment_report');

		$this->document->setTitle($this->language->get('details_heading_title'));

		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = null;
		}

		if (isset($this->request->get['filter_payment_type'])) {
			$filter_payment_type = $this->request->get['filter_payment_type'];
		} else {
			$filter_payment_type = null;
		}

		if (isset($this->request->get['filter_tendered_amount'])) {
			$filter_tendered_amount = $this->request->get['filter_tendered_amount'];
		} else {
			$filter_tendered_amount = null;
		}
		
		if (isset($this->request->get['filter_payment_date'])) {
			$filter_payment_date = $this->request->get['filter_payment_date'];
		} else {
			$filter_payment_date = null;
		}
		
		// add for admin payment details begin
		if (isset($this->request->get['filter_user_id'])) {
			$filter_user_id = $this->request->get['filter_user_id'];
		} else {
			$filter_user_id = null;
		}
		
		if (isset($this->request->get['filter_user_name'])) {
			$filter_user_name = $this->request->get['filter_user_name'];
		} else {
			$filter_user_name = null;
		}
	
		if (isset($this->request->get['filter_invoice_number'])) {
			$filter_invoice_number = $this->request->get['filter_invoice_number'];
		} else {
			$filter_invoice_number = null;
		}
		// add for admin payment details end
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'order_payment_id';
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
		
		if (isset($this->request->get['filter_payment_type'])) {
			$url .= '&filter_payment_type=' . urlencode(html_entity_decode($this->request->get['filter_payment_type'], ENT_QUOTES, 'UTF-8'));
		}
											
		if (isset($this->request->get['filter_tendered_amount'])) {
			$url .= '&filter_tendered_amount=' . $this->request->get['filter_tendered_amount'];
		}
		
		if (isset($this->request->get['filter_payment_date'])) {
			$url .= '&filter_payment_date=' . $this->request->get['filter_payment_date'];
		}
		
		// add for admin payment details begin
		if (isset($this->request->get['filter_user_id'])) {
			$url .= '&filter_user_id=' . $this->request->get['filter_user_id'];
		}
		if (isset($this->request->get['filter_user_name'])) {
			$url .= '&filter_user_name=' . $this->request->get['filter_user_name'];
		}
		
		if (isset($this->request->get['filter_invoice_number'])) {
			$url .= '&filter_invoice_number=' . $this->request->get['filter_invoice_number'];
		}
		// add for admin payment details end

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
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
       		'text'      => $this->language->get('details_heading_title'),
			'href'      => $this->url->link('report/order_payment', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);

		$this->data['order_payments'] = array();

		$data = array(
			'filter_order_id'        => $filter_order_id,
			'filter_payment_type'	 => $filter_payment_type,
			'filter_tendered_amount' => $filter_tendered_amount,
			'filter_payment_date'    => $filter_payment_date,
			// add for admin payment details begin
			'filter_user_id'         => $filter_user_id,
			'filter_invoice_number'  => $filter_invoice_number,
			// add for admin payment details end
			'sort'                   => $sort,
			'order'                  => $order,
			'start'                  => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit'                  => $this->config->get('config_admin_limit')
		);

		$this->load->model('pos/pos');
		
		$payment_total = $this->model_pos_pos->getTotalOrderPayments($data);
		$results = $this->model_pos_pos->getOrderPayments($data);

		// add for admin payment details begin
		$this->load->model('user/user');
		// add for admin payment details end

    	foreach ($results as $result) {
			$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$result['order_id'] . "'");
			$order_info = $order_query->row;
			if ($order_info) {
				// add for admin payment details begin
				$user_info = $this->model_user_user->getUser($order_info['user_id']);
				// add for admin payment details end
				$this->data['order_payments'][] = array(
					'order_payment_id' => $result['order_payment_id'],
					'order_id'         => $result['order_id'],
					'payment_type'     => $result['payment_type'],
					'tendered_amount'  => $this->currency->format($result['tendered_amount'], $order_info['currency_code'], $order_info['currency_value']),
					'payment_note'     => $result['payment_note'],
					// add for admin payment details begin
					'user_id'          => $order_info['user_id'],
					'user_name'        => $user_info ? $user_info['username'] : '',
					'invoice_number'   => $order_info['invoice_prefix'] . $order_info['invoice_no'],
					// add for admin payment details end
					'payment_time'     => date($this->language->get('date_format_short'), strtotime($result['payment_time']))
				);
			}
		}

		$this->data['heading_title'] = $this->language->get('details_heading_title');

		$this->data['text_no_results'] = $this->language->get('text_no_results');

		$this->data['column_order_payment_id'] = $this->language->get('column_order_payment_id');
    	$this->data['column_order_id'] = $this->language->get('column_order_id');
		$this->data['column_payment_type'] = $this->language->get('column_payment_type');
		$this->data['column_tendered_amount'] = $this->language->get('column_tendered_amount');
		$this->data['column_payment_note'] = $this->language->get('column_payment_note');
		$this->data['column_payment_time'] = $this->language->get('column_payment_time');
		// add for admin payment details begin
		$this->data['column_user_name'] = $this->language->get('column_user_name');
		$this->data['column_invoice_number'] = $this->language->get('column_invoice_number');
		// add for admin payment details end

		$this->data['button_filter'] = $this->language->get('button_filter');

		$this->data['token'] = $this->session->data['token'];
		
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}
		
		if (isset($this->request->get['filter_payment_type'])) {
			$url .= '&filter_payment_type=' . urlencode(html_entity_decode($this->request->get['filter_payment_type'], ENT_QUOTES, 'UTF-8'));
		}
											
		if (isset($this->request->get['filter_tendered_amount'])) {
			$url .= '&filter_tendered_amount=' . $this->request->get['filter_tendered_amount'];
		}
		
		if (isset($this->request->get['filter_payment_date'])) {
			$url .= '&filter_payment_date=' . $this->request->get['filter_payment_date'];
		}
		
		// add for admin payment details begin
		if (isset($this->request->get['filter_user_id'])) {
			$url .= '&filter_user_id=' . $this->request->get['filter_user_id'];
		}
		if (isset($this->request->get['filter_invoice_number'])) {
			$url .= '&filter_invoice_number=' . $this->request->get['filter_invoice_number'];
		}
		// add for admin payment details end

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$this->data['sort_order_payment'] = $this->url->link('report/order_payment', 'token=' . $this->session->data['token'] . '&sort=order_payment_id' . $url, 'SSL');
		$this->data['sort_order'] = $this->url->link('report/order_payment', 'token=' . $this->session->data['token'] . '&sort=order_id' . $url, 'SSL');
		$this->data['sort_payment_type'] = $this->url->link('report/order_payment', 'token=' . $this->session->data['token'] . '&sort=payment_type' . $url, 'SSL');
		$this->data['sort_tendered_amount'] = $this->url->link('report/order_payment', 'token=' . $this->session->data['token'] . '&sort=tendered_amount' . $url, 'SSL');
		$this->data['sort_payment_time'] = $this->url->link('report/order_payment', 'token=' . $this->session->data['token'] . '&sort=payment_time' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}
		
		if (isset($this->request->get['filter_payment_type'])) {
			$url .= '&filter_payment_type=' . urlencode(html_entity_decode($this->request->get['filter_payment_type'], ENT_QUOTES, 'UTF-8'));
		}
											
		if (isset($this->request->get['filter_tendered_amount'])) {
			$url .= '&filter_tendered_amount=' . $this->request->get['filter_tendered_amount'];
		}
		
		if (isset($this->request->get['filter_payment_date'])) {
			$url .= '&filter_payment_date=' . $this->request->get['filter_payment_date'];
		}
		
		// add for admin payment details begin
		if (isset($this->request->get['filter_user_id'])) {
			$url .= '&filter_user_id=' . $this->request->get['filter_user_id'];
		}
		if (isset($this->request->get['filter_invoice_number'])) {
			$url .= '&filter_invoice_number=' . $this->request->get['filter_invoice_number'];
		}
		// add for admin payment details end

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $payment_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('report/order_payment', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$this->data['pagination'] = $pagination->render();

		$this->data['filter_order_id'] = $filter_order_id;
		$this->data['filter_payment_type'] = $filter_payment_type;
		$this->data['filter_tendered_amount'] = $filter_tendered_amount;
		$this->data['filter_payment_date'] = $filter_payment_date;
		
		// add for admin payment details begin
		$this->data['filter_user_id'] = $filter_user_id;
		$this->data['filter_user_name'] = $filter_user_name;
		$this->data['filter_invoice_number'] = $filter_invoice_number;
		// add for admin payment details end

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->template = 'pos/report_payment_details.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}
	
	// add for admin payment details begin
	public function autocompleteByUserName() {
		$json = array();
		
		if (isset($this->request->get['filter_name'])) {
			$sql = "SELECT * FROM `" . DB_PREFIX . "user` WHERE username LIKE '%" . $this->db->escape($this->request->get['filter_name']) . "%' LIMIT 0, 20";
			
			$query = $this->db->query($sql);
			foreach ($query->rows as $result) {
				$json[] = array(
					'user_id'       => $result['user_id'], 
					'user_name'     => $result['username']
				);					
			}

			$sort_order = array();
		  
			foreach ($json as $key => $value) {
				$sort_order[$key] = $value['user_name'];
			}

			array_multisort($sort_order, SORT_ASC, $json);
		}

		$this->response->setOutput(json_encode($json));
	}
	// add for admin payment details end
	
	public function summary() {  
		$this->language->load('pos/payment_report');
		$this->language->load('report/sale_order');

		$this->document->setTitle($this->language->get('summary_heading_title'));

		if (isset($this->request->get['filter_date_start'])) {
			$filter_date_start = $this->request->get['filter_date_start'];
		} else {
			$filter_date_start = date('Y-m-d', strtotime(date('Y') . '-' . date('m') . '-01'));
		}

		if (isset($this->request->get['filter_date_end'])) {
			$filter_date_end = $this->request->get['filter_date_end'];
		} else {
			$filter_date_end = date('Y-m-d');
		}
		
		if (isset($this->request->get['filter_group'])) {
			$filter_group = $this->request->get['filter_group'];
		} else {
			$filter_group = 'week';
		}
		
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

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

   		$this->data['breadcrumbs'] = array();

		$heading_title = $this->language->get('summary_heading_title');

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),       		
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
			'text'      => $heading_title,
			'href'      => $this->url->link('report/payment_summary', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->load->model('setting/setting');
		$pos_settings = $this->model_setting_setting->getSetting('POS');
		
		$payment_types = $pos_settings['POS_payment_types'];

		$this->load->model('pos/report_payment');
		$query = $this->db->query("SELECT DISTINCT payment_type FROM `" . DB_PREFIX . "order_payment`");
		if (!empty($query->rows)) {
			$payment_types = array();
			foreach ($query->rows as $row) {
				if ($row['payment_type'] != 'pos_change') {
					$payment_types[] = $row['payment_type'];
				}
			}
		}
		$this->data['col_key'] = $payment_types;

		$this->data['orders'] = array();
		
		$data = array(
			'payment_types'          => $payment_types,
			'filter_date_start'	     => $filter_date_start, 
			'filter_date_end'	     => $filter_date_end, 
			'filter_group'           => $filter_group,
			'start'                  => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit'                  => $this->config->get('config_admin_limit')
		);
		
		$order_total = $this->model_pos_report_payment->getTotalPayments($data);
		
		$results = $this->model_pos_report_payment->getPayments($data);
		
		foreach ($results as $result) {
			foreach ($result['payments'] as $payment) {
				$payments = array();
				
				if ($result['username']) {
					$payments['username'] = $result['username'];
				} else {
					$payments['username'] = $this->language->get('text_front_order');
				}
				$pos_payment_total = 0;

				foreach (array_keys($payment) as $result_key) {
					if ($result_key == 'date_start' || $result_key == 'date_end') {
						$payments[$result_key] = date($this->language->get('date_format_short'), strtotime($payment[$result_key]));
					} else {
						$payments[$result_key] = $this->currency->format($payment[$result_key], $this->config->get('config_currency'));
						$pos_payment_total += $payment[$result_key];
					}
				}
				$payments['total'] = $this->currency->format($pos_payment_total, $this->config->get('config_currency'));

				$this->data['payments'][] = $payments;
			}
		}

		$this->data['heading_title'] = $this->language->get('summary_heading_title');
		
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		
		$this->data['column_user_name'] = $this->language->get('column_user_name');
		$this->data['column_date_start'] = $this->language->get('column_date_start');
		$this->data['column_date_end'] = $this->language->get('column_date_end');
		$this->data['column_total'] = $this->language->get('column_total');
		
		$this->data['entry_date_start'] = $this->language->get('entry_date_start');
		$this->data['entry_date_end'] = $this->language->get('entry_date_end');
		$this->data['entry_group'] = $this->language->get('entry_group');	

		$this->data['button_filter'] = $this->language->get('button_filter');
		
		$this->data['token'] = $this->session->data['token'];

		$this->data['groups'] = array();

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
				
		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('report/payment_summary', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
			
		$this->data['pagination'] = $pagination->render();		

		$this->data['filter_date_start'] = $filter_date_start;
		$this->data['filter_date_end'] = $filter_date_end;		
		$this->data['filter_group'] = $filter_group;
				 
		$this->template = 'pos/report_payment_summary.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}
}
?>