<?php  
class ControllerSaleVoucher extends Controller {
	private $error = array();
     
  	public function index() {
		$this->load->language('sale/voucher');
    	
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('sale/voucher');
		if($this->user->getUserGroupId()=='1')
		{
			$this->data['is_admin'] = true;	
			
		}
		else
		{
			$this->data['is_admin'] = false;	
		}
		
		$this->getList();
  	}
  
  	public function insert() {
    	$this->load->language('sale/voucher');
		$this->load->model('sale/order');
		
    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('sale/voucher');
		
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
		$order_info = $this->model_sale_order->getOrder($this->request->post['order_id']);
		
		$this->request->post['to_name'] = $order_info['firstname'].' '.$order_info['lastname'];
		$this->request->post['to_email'] = $order_info['email'];	
			
			$voucher_id = $this->model_sale_voucher->addVoucher($this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			if(isset($this->request->post['save_and_send']) and $this->request->post['save_and_send']=='send')
			{
				$this->send2($voucher_id);
				
			}
			
			$this->redirect($this->url->link('sale/voucher', 'token=' . $this->session->data['token'] . $url, 'SSL'));
			
    	}
    
    	$this->getForm();
  	}

  	public function update() {
    	$this->load->language('sale/voucher');

    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('sale/voucher');
		$this->load->model('sale/order');
				
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			
			
			//print_r($this->request->post);exit;
			$order_info = $this->model_sale_order->getOrder($this->request->post['order_id']);
		
				$this->request->post['to_name'] = $order_info['firstname'].' '.$order_info['lastname'];
				$this->request->post['to_email'] = $order_info['email'];
		
			$this->model_sale_voucher->editVoucher($this->request->get['voucher_id'], $this->request->post);
      		
			$this->session->data['success'] = $this->language->get('text_success');
	  
			$url = '';
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			if(isset($this->request->post['save_and_send']) and $this->request->post['save_and_send']=='send')
			{
				$this->send2($this->request->get['voucher_id']);
					
			}
			$this->redirect($this->url->link('sale/voucher', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
    
    	$this->getForm();
  	}

  	public function delete() {
    	$this->load->language('sale/voucher');

    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('sale/voucher');
		
    	if (isset($this->request->post['selected']) && $this->validateDelete()) { 
			foreach ($this->request->post['selected'] as $voucher_id) {
				$this->model_sale_voucher->deleteVoucher($voucher_id);
			}
      		
			$this->session->data['success'] = $this->language->get('text_success');
	  
			$url = '';
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$this->redirect($this->url->link('sale/voucher', 'token=' . $this->session->data['token'] . $url, 'SSL'));
    	}
	
    	$this->getList();
  	}

  	private function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'v.date_added';
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
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('sale/voucher', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);
							
		$this->data['insert'] = $this->url->link('sale/voucher/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['delete'] = $this->url->link('sale/voucher/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');
		
		$this->data['vouchers'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$voucher_total = $this->model_sale_voucher->getTotalVouchers();
	
		$results = $this->model_sale_voucher->getVouchers($data);
 $this->load->model('user/user');
    	foreach ($results as $result) {
			$action = array();
									
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('sale/voucher/update', 'token=' . $this->session->data['token'] . '&voucher_id=' . $result['voucher_id'] . $url, 'SSL')
			);
					$user_id = 	$this->model_user_user->getUser($result['user_id']);
					$user_name = $user_id['firstname'].' '.$user_id['lastname'];
				$balance = $this->model_sale_voucher->getVoucherBalance($result['voucher_id']);
			$this->data['vouchers'][] = array(
				'voucher_id' => $result['voucher_id'],
				'code'       => $result['code'],
				'balance'	=>  $this->currency->format($balance, $this->config->get('config_currency')),
				'from'       => $result['from_name'],
				'to'         => $result['to_name'],
				'user_id'    => ($result['user_id']!=''?$user_name:$result['from_name']),
				'theme'      => $result['theme'],
				'amount'     => $this->currency->format($result['amount'], $this->config->get('config_currency')),
				'status'     => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'selected'   => isset($this->request->post['selected']) && in_array($result['voucher_id'], $this->request->post['selected']),
				'action'     => $action
			);
		}
									
		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['text_send'] = $this->language->get('text_send');
		$this->data['text_wait'] = $this->language->get('text_wait');
		$this->data['text_no_results'] = $this->language->get('text_no_results');

		$this->data['column_code'] = $this->language->get('column_code');
		$this->data['column_from'] = $this->language->get('column_from');
		$this->data['column_to'] = $this->language->get('column_to');
		$this->data['column_theme'] = $this->language->get('column_theme');
		$this->data['column_amount'] = $this->language->get('column_amount');
		$this->data['column_status'] = $this->language->get('column_status');
		$this->data['column_date_added'] = $this->language->get('column_date_added');
		$this->data['column_action'] = $this->language->get('column_action');		
		
		$this->data['button_insert'] = $this->language->get('button_insert');
		$this->data['button_delete'] = $this->language->get('button_delete');
 
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

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$this->data['sort_code'] = $this->url->link('sale/voucher', 'token=' . $this->session->data['token'] . '&sort=v.code' . $url, 'SSL');
		$this->data['sort_from'] = $this->url->link('sale/voucher', 'token=' . $this->session->data['token'] . '&sort=v.from_name' . $url, 'SSL');
		$this->data['sort_to'] = $this->url->link('sale/voucher', 'token=' . $this->session->data['token'] . '&sort=v.to_name' . $url, 'SSL');
		$this->data['sort_theme'] = $this->url->link('sale/voucher', 'token=' . $this->session->data['token'] . '&sort=theme' . $url, 'SSL');
		$this->data['sort_amount'] = $this->url->link('sale/voucher', 'token=' . $this->session->data['token'] . '&sort=v.amount' . $url, 'SSL');
		$this->data['sort_status'] = $this->url->link('sale/voucher', 'token=' . $this->session->data['token'] . '&sort=v.date_end' . $url, 'SSL');
		$this->data['sort_date_added'] = $this->url->link('sale/voucher', 'token=' . $this->session->data['token'] . '&sort=v.date_added' . $url, 'SSL');
				
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
												
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $voucher_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('sale/voucher', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
			
		$this->data['pagination'] = $pagination->render();

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

			

		$this->template = 'sale/voucher_list.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
  	}

  	private function getForm() {
		$this->load->model('catalog/product');
		$this->load->model('sale/order');
		$this->load->model('sale/credit_reason');
		
    	$this->data['heading_title'] = $this->language->get('heading_title');

    	$this->data['text_enabled'] = $this->language->get('text_enabled');
    	$this->data['text_disabled'] = $this->language->get('text_disabled');
		
    	$this->data['entry_code'] = $this->language->get('entry_code');
		$this->data['entry_from_name'] = $this->language->get('entry_from_name');
		$this->data['entry_from_email'] = $this->language->get('entry_from_email');
		$this->data['entry_to_name'] = $this->language->get('entry_to_name');
		$this->data['entry_to_email'] = $this->language->get('entry_to_email');
		$this->data['entry_theme'] = $this->language->get('entry_theme');
		$this->data['entry_message'] = $this->language->get('entry_message');
		$this->data['entry_amount'] = $this->language->get('entry_amount');
		$this->data['entry_status'] = $this->language->get('entry_status');

    	$this->data['button_save'] = $this->language->get('button_save');
    	$this->data['button_cancel'] = $this->language->get('button_cancel');
		
		$this->data['tab_general'] = $this->language->get('tab_general');
		$this->data['tab_voucher_history'] = $this->language->get('tab_voucher_history');
		
		if (isset($this->request->get['voucher_id'])) {
			$this->data['voucher_id'] = $this->request->get['voucher_id'];
		} else {
			$this->data['voucher_id'] = 0;
		}
		 		
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->error['code'])) {
			$this->data['error_code'] = $this->error['code'];
		} else {
			$this->data['error_code'] = '';
		}	
		if (isset($this->error['reason'])) {
			$this->data['error_reason'] = $this->error['reason'];
		} else {
			$this->data['error_reason'] = '';
		}		
		
		if (isset($this->error['from_name'])) {
			$this->data['error_from_name'] = $this->error['from_name'];
		} else {
			$this->data['error_from_name'] = '';
		}	
		
		if (isset($this->error['from_email'])) {
			$this->data['error_from_email'] = $this->error['from_email'];
		} else {
			$this->data['error_from_email'] = '';
		}	
		
		if (isset($this->error['to_name'])) {
			$this->data['error_to_name'] = $this->error['to_name'];
		} else {
			$this->data['error_to_name'] = '';
		}	
		
		if (isset($this->error['to_email'])) {
			$this->data['error_to_email'] = $this->error['to_email'];
		} else {
			$this->data['error_to_email'] = '';
		}
			
		
		if (isset($this->error['order_id'])) {
			$this->data['error_order_id'] = 'Provide order id';
		} else {
			$this->data['error_order_id'] = '';
		}	
		
		if (isset($this->error['amount'])) {
			$this->data['error_amount'] = $this->error['amount'];
		} else {
			$this->data['error_amount'] = '';
		}
				
		$url = '';
			
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
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('sale/voucher', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);
									
		if (!isset($this->request->get['voucher_id'])) {
			$this->data['action'] = $this->url->link('sale/voucher/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$this->data['action'] = $this->url->link('sale/voucher/update', 'token=' . $this->session->data['token'] . '&voucher_id=' . $this->request->get['voucher_id'] . $url, 'SSL');
		}
		
		$this->data['cancel'] = $this->url->link('sale/voucher', 'token=' . $this->session->data['token'] . $url, 'SSL');
  		
		if (isset($this->request->get['voucher_id']) && (!$this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$voucher_info = $this->model_sale_voucher->getVoucher($this->request->get['voucher_id']);
    	}
		
		$this->data['token'] = $this->session->data['token'];

    	if (isset($this->request->post['code'])) {
      		$this->data['code'] = $this->request->post['code'];
    	} elseif (!empty($voucher_info)) {
			$this->data['code'] = $voucher_info['code'];
		} else {
      		$this->data['code'] = '';
    	}
		
    	if (isset($this->request->post['from_name'])) {
      		$this->data['from_name'] = $this->request->post['from_name'];
    	} elseif (!empty($voucher_info)) {
			$this->data['from_name'] = $voucher_info['from_name'];
		} else {
      		$this->data['from_name'] = '';
    	}
		
    	if (isset($this->request->post['from_email'])) {
      		$this->data['from_email'] = $this->request->post['from_email'];
    	} elseif (!empty($voucher_info)) {
			$this->data['from_email'] = $voucher_info['from_email'];
		} else {
      		$this->data['from_email'] = '';
    	}
		
		if (isset($this->request->post['credit_shipping'])) {
      		$this->data['credit_shipping'] = $this->request->post['credit_shipping'];
    	} elseif (!empty($voucher_info)) {
			$this->data['credit_shipping'] = $voucher_info['credit_shipping'];
		} else {
      		$this->data['credit_shipping'] = '';
    	}
		
		if (isset($this->request->post['order_id'])) {
      		$this->data['order_id'] = $this->request->post['order_id'];
    	} elseif (!empty($voucher_info)) {
			$this->data['order_id'] = $voucher_info['order_id'];
		} else {
      		$this->data['order_id'] = '';
    	}
		
		
		if (isset($this->request->post['reason'])) {
      		$this->data['reason_id'] = $this->request->post['reason'];
    	} elseif (!empty($voucher_info)) {
			$this->data['reason_id'] = $voucher_info['reason'];
		} else {
      		$this->data['reason_id'] = '';
    	}
		
		
		if (isset($this->request->post['product_items'])) {
			$product_list = array();
			foreach($this->request->post['product_items'] as $key => $item)
			{
				$item = explode("-",$item);
				
				$product = $this->model_catalog_product->getProduct($item[0]);
					$product_list[] = array(
					'product_id'=>$product['product_id'],
					'name' => $product['name'],
					'price'=> $item[1]
					
					);
				
			}
			
			
			$order_total = $this->model_sale_order->getOrderTotals($this->request->post['order_id']);
			
			foreach($order_total as $order_total)
			{
				
				if($order_total['code']=='shipping')
				{
				$this->data['shipping_method'] = $order_total['title'].' ('.$order_total['text'].')';
				$this->data['shipping_price'] = $order_total['value'];	
				}
				
			}
			
      	$order_total = $this->model_sale_order->getOrderTotals($this->request->post['order_id']);
			
			foreach($order_total as $order_total)
			{
				
				if($order_total['code']=='shipping')
				{
				$this->data['shipping_method'] = $order_total['title'].' ('.$order_total['text'].')';
				$this->data['shipping_price'] = $order_total['value'];	
				}
				
			}
			$this->data['product_related'] = $product_list;
			
			
    	} elseif (!empty($voucher_info)) {
			
			$product_list = array();
			foreach(explode(",",$voucher_info['product_ids']) as $key => $item)
			{
				$item = explode("-",$item);
				
				$product = $this->model_catalog_product->getProduct($item[0]);
					$product_list[] = array(
					'product_id'=>$product['product_id'],
					'name' => $product['name'],
					'price'=> $item[1]
					
					);
				
			}
			
			$order_total = $this->model_sale_order->getOrderTotals($voucher_info['order_id']);
			
			foreach($order_total as $order_total)
			{
				
				if($order_total['code']=='shipping')
				{
				$this->data['shipping_method'] = $order_total['title'].' ('.$order_total['text'].')';
				$this->data['shipping_price'] = $order_total['value'];	
				}
				
			}
			$this->data['product_related'] = $product_list;
			
		} else {
      		$this->data['product_related'] = '';
    	}

    	if (isset($this->request->post['to_name'])) {
      		$this->data['to_name'] = $this->request->post['to_name'];
    	} elseif (!empty($voucher_info)) {
			$this->data['to_name'] = $voucher_info['to_name'];
		} else {
      		$this->data['to_name'] = '';
    	}
		
		if (isset($this->request->post['order_id'])) {
      		$this->data['shipping'] = $this->request->post['to_name'];
    	} elseif (!empty($voucher_info)) {
			$this->data['to_name'] = $voucher_info['to_name'];
		} else {
      		$this->data['to_name'] = '';
    	}
		
		
    	if (isset($this->request->post['to_email'])) {
      		$this->data['to_email'] = $this->request->post['to_email'];
    	} elseif (!empty($voucher_info)) {
			$this->data['to_email'] = $voucher_info['to_email'];
		} else {
      		$this->data['to_email'] = '';
    	}
 
 		$this->load->model('sale/voucher_theme');
			
		$this->data['voucher_themes'] = $this->model_sale_voucher_theme->getVoucherThemes();

    	if (isset($this->request->post['voucher_theme_id'])) {
      		$this->data['voucher_theme_id'] = $this->request->post['voucher_theme_id'];
    	} elseif (!empty($voucher_info)) { 
			$this->data['voucher_theme_id'] = $voucher_info['voucher_theme_id'];
		} else {
      		$this->data['voucher_theme_id'] = '';
    	}	
		
    	if (isset($this->request->post['message'])) {
      		$this->data['message'] = $this->request->post['message'];
    	} elseif (!empty($voucher_info)) {
			$this->data['message'] = $voucher_info['message'];
		} else {
      		$this->data['message'] = '';
    	}
		
		
		
		$reasons = $this->model_sale_credit_reason->getReasons();
		
		foreach($reasons as $reason)
		{
			if($reason['status'] == 1)
			{
				$this->data['reasons'][] = array(
				'reason_id'=>$reason['reason_id'],
				'name'	=>	$reason['name'],
				'code'	=>	$reason['code']
						);
				
			$canned_message[] = array(
			'reason_id'=>$reason['reason_id'],
			'message'=>$reason['message']);	
			
			$reason_codes[] = array(
			'reason_id'=>$reason['reason_id'],
			'code'=>$reason['code']);	
			
			}
			
			
			
		}
		
		
		$this->data['canned_messages'] = $this->getJSON($canned_message);
		$this->data['reason_codes'] = $this->getJSON($reason_codes);
		
    	if (isset($this->request->post['amount'])) {
      		$this->data['amount'] = $this->request->post['amount'];
    	} elseif (!empty($voucher_info)) {
			$this->data['amount'] = $voucher_info['amount'];
		} else {
      		$this->data['amount'] = '';
    	}
	
    	if (isset($this->request->post['status'])) { 
      		$this->data['status'] = $this->request->post['status'];
    	} elseif (!empty($voucher_info)) {
			$this->data['status'] = $voucher_info['status'];
		} else {
      		$this->data['status'] = 1;
    	}

//$this->load->model('localisation/canned_messages');
	//		$canned_messages = $this->model_localisation_canned_messages->getCannedMessages();
			
			
			
			
			
			/*foreach($canned_messages as $k => $canned_message) {
				$canned_messages[$k]['message'] = $this->model_localisation_canned_messages->orderMergeMessage($canned_messages[$k]['message'], $order_info['order_id']);
			}*/
			
			
			//$this->data['canned_messages'] = $this->getJSON($canned_messages);
			
			if (!$this->user->hasPermission('modify', 'sale/voucher')) {
			$this->data['modify_permission'] = '0';	
			}
			else
			{
				$this->data['modify_permission'] = '1';	
				
			}

		$this->template = 'sale/voucher_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());		
  	}
	
	private function getJSON($data) {
		if(file_exists(DIR_SYSTEM . 'library/json.php')){
			$this->load->library('json');
			return JSON::encode($data);
		} else {
			return json_encode($data);
		}
	}
  	private function validateForm() {
    	if (!$this->user->hasPermission('modify', 'sale/voucher')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}
		
    	if ((utf8_strlen($this->request->post['code']) < 3) || (utf8_strlen($this->request->post['code']) > 10)) {
      		$this->error['code'] = $this->language->get('error_code');
    	}
		
		$voucher_info = $this->model_sale_voucher->getVoucherByCode($this->request->post['code']);
		
		if ($voucher_info) {
			if (!isset($this->request->get['voucher_id'])) {
				$this->error['warning'] = $this->language->get('error_exists');
			} elseif ($voucher_info['voucher_id'] != $this->request->get['voucher_id'])  {
				$this->error['warning'] = $this->language->get('error_exists');
			}
		}
		
		$voucher_order = $this->model_sale_voucher->getVoucherByOrderID($this->request->post['order_id']);
		if($this->request->post['order_id'])
		{
		if ($voucher_order) {
			if (!isset($this->request->get['voucher_id'])) {
				$this->error['warning'] = $this->language->get('error_exists');
			} elseif ($voucher_order['voucher_id'] != $this->request->get['voucher_id'])  {
				$this->error['warning'] = $this->language->get('error_exists');
			}
		}}
					      
    	/*if ((utf8_strlen($this->request->post['to_name']) < 1) || (utf8_strlen($this->request->post['to_name']) > 64)) {
      		$this->error['to_name'] = $this->language->get('error_to_name');
    	}    	
		
		if ((utf8_strlen($this->request->post['to_email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['to_email'])) {
      		$this->error['to_email'] = $this->language->get('error_email');
    	}
		
    	if ((utf8_strlen($this->request->post['from_name']) < 1) || (utf8_strlen($this->request->post['from_name']) > 64)) {
      		$this->error['from_name'] = $this->language->get('error_from_name');
    	}  
		
		if ((utf8_strlen($this->request->post['from_email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['from_email'])) {
      		$this->error['from_email'] = $this->language->get('error_email');
    	}*/
		
		/*if (!isset($this->request->post['order_id'])) {
      		$this->error['order_id'] = 'Please provide Order ID';
    	}    
		if (!isset($this->request->post['product_items'])) {
      		$this->error['order_id'] = 'Please provide Products';
    	} */
		
		if ($this->request->post['amount'] < 0.01) {
      		$this->error['amount'] = $this->language->get('error_amount');
    	}
		/*if ($this->request->post['reason'] == '') {
      		$this->error['reason'] = 'Please select a reason';;
    	}*/

    	if (!$this->error) {
      		return true;
    	} else {
      		return false;
    	}
  	}

  	private function validateDelete() {
    	if (!$this->user->hasPermission('modify', 'sale/voucher')) {
      		$this->error['warning'] = $this->language->get('error_permission');  
    	}
		
		$this->load->model('sale/order');
		
		foreach ($this->request->post['selected'] as $voucher_id) {
			$order_voucher_info = $this->model_sale_order->getOrderVoucherByVoucherId($voucher_id);
			
			/*if ($order_voucher_info) {
				$this->error['warning'] = sprintf($this->language->get('error_order'), $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $order_voucher_info['order_id'], 'SSL')); 
				
				break;       
			}*/
		}
		
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}	
	
	public function history() {
    	$this->language->load('sale/voucher');
		
		$this->load->model('sale/voucher');
				
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		
		$this->data['column_order_id'] = $this->language->get('column_order_id');
		$this->data['column_customer'] = $this->language->get('column_customer');
		$this->data['column_amount'] = $this->language->get('column_amount');
		$this->data['column_date_added'] = $this->language->get('column_date_added');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}  
		
		$this->data['histories'] = array();
			
		$results = $this->model_sale_voucher->getVoucherHistories($this->request->get['voucher_id'], ($page - 1) * 10, 10);
      		
		foreach ($results as $result) {
        	$this->data['histories'][] = array(
				'order_id'   => $result['order_id'],
				'customer'   => $result['customer'],
				'amount'     => $this->currency->format($result['amount'], $this->config->get('config_currency')),
        		'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
        	);
      	}			
		
		$history_total = $this->model_sale_voucher->getTotalVoucherHistories($this->request->get['voucher_id']);
			
		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page = $page;
		$pagination->limit = 10; 
		$pagination->url = $this->url->link('sale/voucher/history', 'token=' . $this->session->data['token'] . '&voucher_id=' . $this->request->get['voucher_id'] . '&page={page}', 'SSL');
			
		$this->data['pagination'] = $pagination->render();
		
		$this->template = 'sale/voucher_history.tpl';		
		
		$this->response->setOutput($this->render());
  	}
	
	
	public function voucher_payment()
	{
		
		$this->language->load('sale/voucher');
		$this->load->model('sale/voucher');
		$this->load->model('sale/order');
		$json = array();
		$order_id = $this->request->get['order_id'];
		
			$order_code_type= $this->request->get['order_code_type'];	
			
		
		
		
		$code = $order_id.$order_code_type;
		$amount = $this->request->post['generate_gv'];
		
    	$data = array();
		
		$order_info = $this->model_sale_order->getOrder($order_id);
		
		$order_products = $this->model_sale_order->getOrderProducts($order_id);
		
		$product_ids = array();
		if(!isset($this->request->post['product_items']))
		{
		foreach($order_products as $order_product)
		{
			for($i=1;$i<=$order_product['quantity'];$i++)
			{
				$product_ids[] =$order_product['product_id'].'-'.$order_product['price'];
			}
			
		}}
		else
		{
			foreach(	$this->request->post['product_items'] as $product_id)
			{
				$product_ids[] = $product_id;	
				
			}
			
		}
		
		// Validate Voucher
		
		$record_check = $this->model_sale_voucher->getVoucherByCode($code);
		if($order_id=='')
		{
			
			$json['error'] = 'Please Select Order to Proceed';
			echo json_encode($json);exit;
		}
		
		if((int)$amount==0)
		{
			
			$json['error'] = 'Please provide a valid amount';
			echo json_encode($json);exit;
		}
		
		if($record_check)
		{
			
			$json['error'] = 'Store Credit has already been issue with this Code: '.$code;
			echo json_encode($json);exit;		
		}
		
		// End Validate Voucehr
		if(!$json['error'])
		{
		$data['code'] = $code;
		$data['voucher_theme_id'] = 8;
		$data['message'] = $this->request->post['message'];
		$data['amount'] = $amount;
		$data['status'] = 1;
		$data['order_id'] = $order_id;
		$data['product_items'] = $product_ids;
		$data['to_name'] = $order_info['firstname'].' '.$order_info['lastname'];
		$data['to_email'] = $order_info['email'];
		$data['reason'] = $this->request->post['reason'];
		$data['credit_shipping'] = (isset($this->request->post['credit_shipping'])?1:0);
		
		$voucher_id = $this->model_sale_voucher->addVoucher($data);
		
			
				$this->model_sale_voucher->sendVoucher($voucher_id);
			
				$this->load->model('sale/order');
	$this->model_sale_order->updateOrderProductAndHistory($order_id);
				
				
				
				$json['success'] = 'Voucher # '.$code.' has been made of amount $'.$amount.' and sent to customer.';	
		}
				$this->response->setOutput(json_encode($json));		
				
				
			
		
	}
	public function send() {
    	$this->language->load('sale/voucher');
		
		$json = array();
    	
     	if (!$this->user->hasPermission('modify', 'sale/voucher')) {
      		$json['error'] = $this->language->get('error_permission'); 
    	} elseif (isset($this->request->get['voucher_id'])) {
			$this->load->model('sale/voucher');
			
			$this->model_sale_voucher->sendVoucher($this->request->get['voucher_id']);
			
			$json['success'] = $this->language->get('text_sent');
		}	
		
		$this->response->setOutput(json_encode($json));			
  	}	
	
	public function send2($voucher_id) {
    	$this->language->load('sale/voucher');
		
		$json = array();
    	
     	if (!$this->user->hasPermission('modify', 'sale/voucher')) {
      		return false;
    	} elseif (isset($voucher_id)) {
			$this->load->model('sale/voucher');
			
			$this->model_sale_voucher->sendVoucher($voucher_id);
			
			return true;
		}	
		
				
  	}	
}
?>