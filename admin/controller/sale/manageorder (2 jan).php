<?php
class ControllerSaleManageOrder extends Controller {
	private $error = array();

  	public function index() {
		$this->load->language('sale/order');
		$this->load->language('sale/manageorder');
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('sale/manageorder');

    	$this->getList();
  	}

  	public function delete() {
		$this->load->language('sale/order');
		$this->load->language('sale/manageorder');
		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/order');

    	if (isset($this->request->post['selected']) && ($this->validateDelete())) {
			foreach ($this->request->post['selected'] as $order_id) {
				$this->model_sale_order->deleteOrder($order_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['pre_page'])) {
				$url .= '&pre_page=' . $this->request->get['pre_page'];
			}
			
			if (isset($this->request->get['filter_order_id'])) {
				$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
			}
			
			if (isset($this->request->get['filter_invoice_id'])) {
				$url .= '&filter_invoice_id=' . $this->request->get['filter_invoice_id'];
			}
		
			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . $this->request->get['filter_email'];
			}
			
			if (isset($this->request->get['filter_customer'])) {
				$url .= '&filter_customer=' . $this->request->get['filter_customer'];
			}
												
			if (isset($this->request->get['filter_order_status_id'])) {
				$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
			}
			
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

			$this->redirect($this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . $url, 'SSL'));
    	}
		
    	$this->getList();
  	}
	
	public function export() {
		$this->load->language('sale/order');
		$this->load->language('sale/manageorder');
		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('sale/manageorder');

    	if (isset($this->request->post['selected']) && ($this->validateDelete())) {
		    $selectid='';
			foreach ($this->request->post['selected'] as $order_id) {
				$selectid.=$order_id.',';
			}
			$this->model_sale_manageorder->exportOrder(substr($selectid,0,strlen($selectid)-1));

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['pre_page'])) {
				$url .= '&pre_page=' . $this->request->get['pre_page'];
			}
			
			if (isset($this->request->get['filter_order_id'])) {
				$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
			}
			
			if (isset($this->request->get['filter_invoice_id'])) {
				$url .= '&filter_invoice_id=' . $this->request->get['filter_invoice_id'];
			}
		
			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . $this->request->get['filter_email'];
			}
			
			if (isset($this->request->get['filter_customer'])) {
				$url .= '&filter_customer=' . $this->request->get['filter_customer'];
			}
												
			if (isset($this->request->get['filter_order_status_id'])) {
				$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
			}
			
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

			$this->redirect($this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . $url, 'SSL'));
    	}

		if(!isset($this->error['warning']))$this->error['warning'] = $this->language->get('error_noselected');
		$this->getList();
  	}
	
	public function addhistory() {
		$this->load->language('sale/order');
		$this->load->language('sale/manageorder');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('sale/order');
		
    	if (isset($this->request->post['selected']) && ($this->validateDelete())) {
		    $this->request->post['notify'] = isset($this->request->post['notify'])? $this->request->post['notify'] : 0;
			$selectid=explode('/',$this->request->post['selected']);
			foreach ($selectid as $order_id) {
				$this->model_sale_order->addOrderHistory($order_id, $this->request->post);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['pre_page'])) {
				$url .= '&pre_page=' . $this->request->get['pre_page'];
			}
			
			if (isset($this->request->get['filter_order_id'])) {
				$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
			}
			
			if (isset($this->request->get['filter_customer'])) {
				$url .= '&filter_customer=' . $this->request->get['filter_customer'];
			}
												
			if (isset($this->request->get['filter_order_status_id'])) {
				$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
			}
			
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

			$this->redirect($this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . $url, 'SSL'));
    	}

		if(!isset($this->error['warning']))$this->error['warning'] = $this->language->get('error_noselected');
		$this->getList();
  	}

  	private function getList() {
		if (isset($this->request->get['filter_order_id'])) {
			$filter_order_id = $this->request->get['filter_order_id'];
		} else {
			$filter_order_id = null;
		}
		
		if (isset($this->request->get['filter_invoice_id'])) {
			$filter_invoice_id = $this->request->get['filter_invoice_id'];
		} else {
			$filter_invoice_id = null;
		}

		if (isset($this->request->get['filter_customer'])) {
			$filter_customer = $this->request->get['filter_customer'];
		} else {
			$filter_customer = null;
		}
		
		if (isset($this->request->get['filter_email'])) {
			$filter_email = $this->request->get['filter_email'];
		} else {
			$filter_email = null;
		}

		if (isset($this->request->get['filter_order_status_id'])) {
			$filter_order_status_id = $this->request->get['filter_order_status_id'];
		} else {
			$filter_order_status_id = null;
		}
		
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
		
		if (isset($this->request->get['pre_page'])) {
			$pre_page = $this->request->get['pre_page'];
		} else {
			$pre_page = $this->config->get('config_admin_limit');
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
		
		if (isset($this->request->get['filter_invoice_id'])) {
			$url .= '&filter_invoice_id=' . $this->request->get['filter_invoice_id'];
		}
		
		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . $this->request->get['filter_customer'];
		}
		
		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . $this->request->get['filter_email'];
		}
											
		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
		
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
		
		$this->data['show50'] = $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . '&pre_page=50' . $url, 'SSL');
		$this->data['show200'] = $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . '&pre_page=200' . $url, 'SSL');
		$this->data['show500'] = $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . '&pre_page=500' . $url, 'SSL');
		$this->data['show1000'] = $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . '&pre_page=1000' . $url, 'SSL');
		
		if (isset($this->request->get['pre_page'])) {
			$url .= '&pre_page=' . $this->request->get['pre_page'];
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);

		$this->data['export_select'] = $this->url->link('sale/manageorder/export', 'token=' . $this->session->data['token']. $url, 'SSL');
		$this->data['update_select'] = $this->url->link('sale/manageorder/updatehistory', 'token=' . $this->session->data['token']. $url, 'SSL');
		$this->data['invoice'] = $this->url->link('sale/order/invoice', 'token=' . $this->session->data['token'], 'SSL');
		//version
		$version = $this->model_sale_manageorder->versiontoint();
		if($version >= 1520){
			$this->data['insert'] = $this->url->link('sale/order/insert', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['button_insert'] = $this->language->get('button_insert');
		}
		$this->data['delete'] = $this->url->link('sale/manageorder/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');
		

		$this->data['orders'] = array();

		$data = array(
			'filter_order_id'        => $filter_order_id,
			'filter_invoice_id'      => $filter_invoice_id,
			'filter_customer'	     => $filter_customer,
			'filter_email'  	     => $filter_email,
			'filter_order_status_id' => $filter_order_status_id,
			'filter_total'           => $filter_total,
			'filter_date_added'      => $filter_date_added,
			'filter_date_modified'   => $filter_date_modified,
			'sort'                   => $sort,
			'order'                  => $order,
			'start'                  => ($page - 1) * $pre_page,
			'limit'                  => $pre_page
		);

		$order_total = $this->model_sale_manageorder->getTotalOrders($data);
		$orders_data = array();
		$results = $this->model_sale_manageorder->getOrders($data);
		$this->load->model('sale/order');
		$this->load->model('sale/affiliate');
		$this->load->model('sale/customer');
		
		foreach ($results as $result) {
			$action = array();
			$orderinfo = $this->model_sale_order->getOrder($result['order_id']);
			$orderproducts = $this->model_sale_order->getOrderProducts($result['order_id']);
			$orderproductstr = '';
			foreach($orderproducts as $orderproduct){
				$orderproductstr .= '<br />'.$orderproduct['model'].'*'.$orderproduct['quantity'];
			}
			
			$action[] = array(
				'text' => $this->language->get('text_view'),
				'href' => $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'] . $url, 'SSL')
			);
			
			if ($version >= 1520 && (strtotime($result['date_added']) > strtotime('-' . (int)$this->config->get('config_order_edit') . ' day'))) {
				$action[] = array(
					'text' => $this->language->get('text_edit'),
					'href' => $this->url->link('sale/order/update', 'token=' . $this->session->data['token'] . '&order_id=' . $result['order_id'] . $url, 'SSL')
				);
			}

			$orders_data[] = array(
				'order_id'      => $result['order_id'],
				'invoice_id'    => $result['invoice_id'],
				'customer'      => $result['customer'],
				'email'         => $result['email'].$orderproductstr,
				'status'        => $result['status'],
				'payment_method'=> $result['payment_method'],
				'sub_total'     => $this->currency->format($result['sub_total'], $result['currency_code'], $result['currency_value']),
				'store_credit'  => $this->currency->format($result['store_credit'], $result['currency_code'], $result['currency_value']),
				'reward'        => $orderinfo['customer_id'] ? $orderinfo['reward'] : 0,
				'reward_total'  => $this->model_sale_customer->getTotalCustomerRewardsByOrderId($result['order_id']),
				'affiliate'     => $orderinfo['affiliate_id'],
				'commission'  	=> $this->currency->format($orderinfo['commission'], $orderinfo['currency_code'], $orderinfo['currency_value']),
				'commission_total'  => $this->model_sale_affiliate->getTotalTransactionsByOrderId($result['order_id']),
				'total'         => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
				'date_added'    => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_modified' => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
				'selected'      => isset($this->request->post['selected']) && in_array($result['order_id'], $this->request->post['selected']),
				'action'        => $action
			);
			
		}
		
		$this->data['orders'] = $orders_data;

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_no_results'] = $this->language->get('text_no_results');
		$this->data['text_abandoned_orders'] = ($this->language->get('text_abandoned_orders')=='text_abandoned_orders')? $this->language->get('text_missing') : $this->language->get('text_abandoned_orders');

		$this->data['column_order_id'] = $this->language->get('column_order_id');
		$this->data['column_invoice_id'] = $this->language->get('column_invoice_id');
    	$this->data['column_customer'] = $this->language->get('column_customer');
		$this->data['column_email'] = $this->language->get('column_email');
		$this->data['column_status'] = $this->language->get('column_status');
		$this->data['column_payment_method'] = $this->language->get('column_payment_method');
		$this->data['column_sub_total'] = $this->language->get('column_sub_total');
		$this->data['column_store_credit'] = $this->language->get('column_store_credit');
		$this->data['column_total'] = $this->language->get('column_total');
		$this->data['column_date_added'] = $this->language->get('column_date_added');
		$this->data['column_date_modified'] = $this->language->get('column_date_modified');
		$this->data['column_action'] = $this->language->get('column_action');

		$this->data['button_export'] = $this->language->get('button_export');
		$this->data['button_updateorder'] = $this->language->get('button_updateorder');
		$this->data['button_invoice'] = $this->language->get('button_invoice');
		$this->data['button_delete'] = $this->language->get('button_delete');
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

		if (isset($this->request->get['pre_page'])) {
			$url .= '&pre_page=' . $this->request->get['pre_page'];
		}
		
		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}
		
		if (isset($this->request->get['filter_invoice_id'])) {
			$url .= '&filter_invoice_id=' . $this->request->get['filter_invoice_id'];
		}
		
		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . $this->request->get['filter_customer'];
		}
		
		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . $this->request->get['filter_email'];
		}
		
							
		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
		
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
			$url .= '&order=' .  'DESC';
		} else {
			$url .= '&order=' .  'ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$this->data['sort_order'] = $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . '&sort=o.order_id' . $url, 'SSL');
		$this->data['sort_invoice'] = $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . '&sort=invoice_id' . $url, 'SSL');
		$this->data['sort_customer'] = $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . '&sort=customer' . $url, 'SSL');
		$this->data['sort_email'] = $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . '&sort=o.email' . $url, 'SSL');
		$this->data['sort_status'] = $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');
		$this->data['sort_total'] = $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . '&sort=o.total' . $url, 'SSL');
		$this->data['sort_date_added'] = $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . '&sort=o.date_added' . $url, 'SSL');
		$this->data['sort_date_modified'] = $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . '&sort=o.date_modified' . $url, 'SSL');
		

		$url = '';

		if (isset($this->request->get['pre_page'])) {
			$url .= '&pre_page=' . $this->request->get['pre_page'];
		}
		
		if (isset($this->request->get['filter_order_id'])) {
			$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
		}
		
		if (isset($this->request->get['filter_invoice_id'])) {
			$url .= '&filter_invoice_id=' . $this->request->get['filter_invoice_id'];
		}
		
		if (isset($this->request->get['filter_customer'])) {
			$url .= '&filter_customer=' . $this->request->get['filter_customer'];
		}
		
		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . $this->request->get['filter_email'];
		}
											
		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
		
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
		$pagination->limit = $pre_page;
		$pagination->text = $this->language->get('text_showperpage');
		$pagination->url = $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$this->data['pagination'] = $pagination->render();		
		
		$this->data['filter_order_id'] = $filter_order_id;
		$this->data['filter_invoice_id'] = $filter_invoice_id;
		$this->data['filter_customer'] = $filter_customer;
		$this->data['filter_email'] = $filter_email;
		$this->data['filter_order_status_id'] = $filter_order_status_id;
		$this->data['filter_total'] = $filter_total;
		$this->data['filter_date_added'] = $filter_date_added;
		$this->data['filter_date_modified'] = $filter_date_modified;

		$this->load->model('localisation/order_status');

    	$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->template = 'sale/manageorder_list.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
  	}
	
	
   	private function validateDelete() {
    	if (!$this->user->hasPermission('modify', 'sale/manageorder')) {
			$this->error['warning'] = $this->language->get('error_permission');
    	}

		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}

	public function updatehistory() {
    	
		$this->language->load('sale/order');
		$this->language->load('sale/manageorder');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('sale/manageorder');
		$this->load->model('sale/order');

		if (isset($this->request->post['selected']) && ($this->validateDelete())) {

			$url = '';

			if (isset($this->request->get['pre_page'])) {
				$url .= '&pre_page=' . $this->request->get['pre_page'];
			}
			
			if (isset($this->request->get['filter_order_id'])) {
				$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
			}
			
			if (isset($this->request->get['filter_invoice_id'])) {
				$url .= '&filter_invoice_id=' . $this->request->get['filter_invoice_id'];
			}
		
			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . $this->request->get['filter_email'];
			}
			
			if (isset($this->request->get['filter_customer'])) {
				$url .= '&filter_customer=' . $this->request->get['filter_customer'];
			}
												
			if (isset($this->request->get['filter_order_status_id'])) {
				$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
			}
			
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

			$this->data['submit_url']=$this->url->link('sale/manageorder/addhistory', 'token=' . $this->session->data['token'] . $url, 'SSL');
			
			$selectid='';
			foreach ($this->request->post['selected'] as $order_id) {
				$selectid.=$order_id.'/';
			}
			
			$this->data['heading_title'] = $this->language->get('heading_title');
						
			$this->data['entry_order_status'] = $this->language->get('entry_order_status');
			$this->data['entry_notify'] = $this->language->get('entry_notify');
			$this->data['entry_comment'] = $this->language->get('entry_comment');
			
			$this->data['button_add_history'] = $this->language->get('button_add_history');
		
			$this->data['tab_order_history'] = $this->language->get('tab_order_history');
		
			$this->data['token'] = $this->session->data['token'];
			$this->data['order_selectid'] = substr($selectid,0,strlen($selectid)-1);
			$this->data['comments'] = $this->model_sale_manageorder->getOrderStatusComment();

			$this->data['breadcrumbs'] = array();

			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => false
			);

			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('heading_title'),
				'href'      => $this->url->link('sale/manageorder', 'token=' . $this->session->data['token'], 'SSL'),				
				'separator' => ' :: '
			);

			$this->load->model('localisation/order_status');

			$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		
			$this->template = 'sale/manageorder_history.tpl';
			$this->children = array(
				'common/header',
				'common/footer',
				);
			
			$this->response->setOutput($this->render());
		}else{		
		if(!isset($this->error['warning']))$this->error['warning'] = $this->language->get('error_noselected');
		$this->getList();
		
		}
  	}
	
	public function ini(){
		if(trim($this->request->get['route']) == 'sale/order'){
			$this->request->get['route'] = 'sale/manage';
			return $this->forward($this->request->get['route']);
		}
	}
}
?>