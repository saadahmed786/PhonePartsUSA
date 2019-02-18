<?php
class ControllerReportVoidedItem extends Controller { 
	public function index() {  
	
		$this->load->language('report/voided_item');

		$this->document->setTitle($this->language->get('heading_title'));

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
		
		$this->load->model('report/voided');
		
		$this->data['orders'] = array();
		
		$data = array(
			'filter_date_start'	     => $filter_date_start, 
			'filter_date_end'	     => $filter_date_end, 
			
			
			'start'                  => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit'                  => $this->config->get('config_admin_limit')
		);
		
		//$order_total = $this->model_report_sale->getTotalOrders($data);
		
		$results = $this->model_report_voided->getOrders($data);
	$this->load->model('catalog/product');
	$this->load->model('sale/order');
	
		foreach ($results as $result) {
			$product_info = $this->model_catalog_product->getProduct($result['product_id']);
			$order_history = $this->model_sale_order->getOrderHistory($result['order_id']);
			$this->data['orders'][] = array(
				'date' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'order_id'     => $result['order_id'],
				'void_type'   => $result['reason'],
				'quantity'   => $result['quantity'],
				'sku'   => $product_info['sku'],
				'reason'   => $order_history['comment']
			);
		}

		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		
		$this->data['column_date'] = $this->language->get('column_date');
		$this->data['column_order_id'] = $this->language->get('column_order_id');
    	$this->data['column_products'] = $this->language->get('column_products');
		$this->data['column_quantity'] = $this->language->get('column_quantity');
		$this->data['column_username'] = $this->language->get('column_username');
		$this->data['column_total'] = $this->language->get('column_total');
		
		$this->data['entry_date_start'] = $this->language->get('entry_date_start');
		$this->data['entry_date_end'] = $this->language->get('entry_date_end');
		

		$this->data['button_filter'] = $this->language->get('button_filter');
		
		$this->data['token'] = $this->session->data['token'];
		
		/*$this->load->model('localisation/order_status');
		
		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

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
*/


$this->data['sort_date_added'] = $this->url->link('report/voided_item', 'token=' . $this->session->data['token'] . '&sort=v.date_added' . $url, 'SSL');

$this->data['sort_reason'] = $this->url->link('report/voided_item', 'token=' . $this->session->data['token'] . '&sort=r.return_reason_id' . $url, 'SSL');
		$url = '';
						
		if (isset($this->request->get['filter_date_start'])) {
			$url .= '&filter_date_start=' . $this->request->get['filter_date_start'];
		}
		
		if (isset($this->request->get['filter_date_end'])) {
			$url .= '&filter_date_end=' . $this->request->get['filter_date_end'];
		}
		
		
				
		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('report/voided_item', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
			
		//$this->data['pagination'] = $pagination->render();		

		$this->data['filter_date_start'] = $filter_date_start;
		$this->data['filter_date_end'] = $filter_date_end;		
		
				 
		$this->template = 'report/voided_item.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
}
?>