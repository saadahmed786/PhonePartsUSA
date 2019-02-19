<?php
class ControllerReportPosCommission extends Controller { 
	public function index() {  
		$this->language->load('pos/commission');

		$this->document->setTitle($this->language->get('details_heading_title'));

		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = null;
		}

		if (isset($this->request->get['filter_commission'])) {
			$filter_commission = $this->request->get['filter_commission'];
		} else {
			$filter_commission = null;
		}

		if (isset($this->request->get['filter_commission_date'])) {
			$filter_commission_date = $this->request->get['filter_commission_date'];
		} else {
			$filter_commission_date = null;
		}
		
		if (isset($this->request->get['filter_user_id'])) {
			$filter_user_id = $this->request->get['filter_user_id'];
		} else {
			$filter_user_id = 0;
		}
		
		if (isset($this->request->get['filter_user_name'])) {
			$filter_user_name = $this->request->get['filter_user_name'];
		} else {
			$filter_user_name = null;
		}
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'order_id';
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

		if (isset($this->request->get['filter_commission_date'])) {
			$url .= '&filter_commission_date=' . $this->request->get['filter_commission_date'];
		}
		
		if (isset($this->request->get['filter_user_id'])) {
			$url .= '&filter_user_id=' . $this->request->get['filter_user_id'];
		}
		if (isset($this->request->get['filter_user_name'])) {
			$url .= '&filter_user_name=' . $this->request->get['filter_user_name'];
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

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('details_heading_title'),
			'href'      => $this->url->link('report/pos_commission', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);

		$data = array(
			'filter_order_id'        => $filter_order_id,
			'filter_commission'      => $filter_commission,
			'filter_commission_date' => $filter_commission_date,
			'filter_user_id'         => $filter_user_id,
			'sort'                   => $sort,
			'order'                  => $order,
			'start'                  => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit'                  => $this->config->get('config_admin_limit')
		);

		$this->load->model('pos/report_commission');
		
		$commissions_total = $this->model_pos_report_commission->getTotalOrderCommissions($data);
		$this->data['order_commissions'] = $this->model_pos_report_commission->getOrderCommissions($data);

		$this->data['heading_title'] = $this->language->get('details_heading_title');

		$this->data['text_no_results'] = $this->language->get('text_no_results');

    	$this->data['column_order_id'] = $this->language->get('column_order_id');
		$this->data['column_commission'] = $this->language->get('column_commission');
		$this->data['column_commission_date'] = $this->language->get('column_commission_date');
		$this->data['column_time'] = $this->language->get('column_time');
		$this->data['column_user_name'] = $this->language->get('column_user_name');

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
		
		if (isset($this->request->get['filter_commission_date'])) {
			$url .= '&filter_commission_date=' . $this->request->get['filter_commission_date'];
		}
		
		if (isset($this->request->get['filter_user_id'])) {
			$url .= '&filter_user_id=' . $this->request->get['filter_user_id'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$this->data['sort_order'] = $this->url->link('report/pos_commission', 'token=' . $this->session->data['token'] . '&sort=order_id' . $url, 'SSL');
		$this->data['sort_commission'] = $this->url->link('report/pos_commission', 'token=' . $this->session->data['token'] . '&sort=commission' . $url, 'SSL');
		$this->data['sort_commission_time'] = $this->url->link('report/pos_commission', 'token=' . $this->session->data['token'] . '&sort=date_modified' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}
		
		if (isset($this->request->get['filter_commission_date'])) {
			$url .= '&filter_commission_date=' . $this->request->get['filter_commission_date'];
		}
		
		if (isset($this->request->get['filter_user_id'])) {
			$url .= '&filter_user_id=' . $this->request->get['filter_user_id'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $commissions_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('report/pos_commission', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$this->data['pagination'] = $pagination->render();

		$this->data['filter_order_id'] = $filter_order_id;
		$this->data['filter_commission'] = $filter_commission;
		$this->data['filter_commission_date'] = $filter_commission_date;
		$this->data['filter_user_id'] = $filter_user_id;
		$this->data['filter_user_name'] = $filter_user_name;

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->template = 'pos/report_commission_details.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}
	
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

	public function summary() {  
		$this->language->load('pos/commission');
		$this->language->load('report/sale_order');

		$this->document->setTitle($this->language->get('summary_heading_title'));
		
		$this->load->model('user/user');
		$this->data['users'] = $this->model_user_user->getUsers();
		
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

		if (isset($this->request->get['filter_user_id'])) {
			$filter_user_id = $this->request->get['filter_user_id'];
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
		
		if (isset($this->request->get['filter_user_id'])) {
			$url .= '&filter_user_id=' . $this->request->get['filter_user_id'];
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
			'href'      => $this->url->link('report/pos_commission/summary', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);
		
		$data = array(
			'filter_date_start'	     => $filter_date_start, 
			'filter_date_end'	     => $filter_date_end, 
			'filter_group'           => $filter_group,
			'start'                  => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit'                  => $this->config->get('config_admin_limit')
		);
		if (isset($filter_user_id)) {
			$data['filter_user_id'] = $filter_user_id;
		}
		
		$this->load->model('pos/report_commission');
		
		$order_total = $this->model_pos_report_commission->getTotalOrderCommissionSummary($data);
		
		$this->data['commissions'] = $this->model_pos_report_commission->getOrderCommissionSummary($data);
		
		$this->data['heading_title'] = $this->language->get('summary_heading_title');
		
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		
		$this->data['column_user_name'] = $this->language->get('column_user_name');
		$this->data['column_date_start'] = $this->language->get('column_date_start');
		$this->data['column_date_end'] = $this->language->get('column_date_end');
		$this->data['column_commission'] = $this->language->get('column_commission');
		
		$this->data['entry_date_start'] = $this->language->get('entry_date_start');
		$this->data['entry_date_end'] = $this->language->get('entry_date_end');
		$this->data['entry_group'] = $this->language->get('entry_group');	
		$this->data['entry_user_id'] = $this->language->get('entry_user_id');

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
		
		if (isset($this->request->get['filter_user_id'])) {
			$url .= '&filter_user_id=' . $this->request->get['filter_user_id'];
		}
		
		if (isset($this->request->get['filter_group'])) {
			$url .= '&filter_group=' . $this->request->get['filter_group'];
		}		

		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('report/pos_commission/summary', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
			
		$this->data['pagination'] = $pagination->render();		

		$this->data['filter_date_start'] = $filter_date_start;
		$this->data['filter_date_end'] = $filter_date_end;		
		$this->data['filter_group'] = $filter_group;
		if (isset($filter_user_id)) {
			$this->data['filter_user_id'] = $filter_user_id;
		}

		$this->template = 'pos/report_commission_summary.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}
}
?>