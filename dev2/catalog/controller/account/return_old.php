<?php 
class ControllerAccountReturn extends Controller { 
	private $error = array();
	
	public function index() {
    	if (!$this->customer->isLogged()) {
      		$this->session->data['redirect'] = $this->url->link('account/return', '', 'SSL');

	  		$this->redirect($this->url->link('account/login', '', 'SSL'));
    	}
 
    	$this->language->load('account/return');

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
		
		$url = '';
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
				
      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('account/return', $url, 'SSL'),        	
        	'separator' => $this->language->get('text_separator')
      	);

		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['text_return_id'] = $this->language->get('text_return_id');
		$this->data['text_order_id'] = $this->language->get('text_order_id');
		$this->data['text_status'] = $this->language->get('text_status');
		$this->data['text_date_added'] = $this->language->get('text_date_added');
		$this->data['text_customer'] = $this->language->get('text_customer');
		$this->data['text_empty'] = $this->language->get('text_empty');

		$this->data['button_view'] = $this->language->get('button_view');
		$this->data['button_continue'] = $this->language->get('button_continue');
		
		$this->load->model('account/return');
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$this->data['returns'] = array();
		
		$return_total = $this->model_account_return->getTotalReturns();
		
		$results = $this->model_account_return->getReturns(($page - 1) * 10, 10);
		
		foreach ($results as $result) {
			$this->data['returns'][] = array(
				'return_id'  => $result['return_id'],
				'order_id'   => $result['order_id'],
				'name'       => $result['firstname'] . ' ' . $result['lastname'],
				'status'     => $result['status'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'href'       => $this->url->link('account/return/info', 'return_id=' . $result['return_id'] . $url, 'SSL')
			);
		}

		$pagination = new Pagination();
		$pagination->total = $return_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_catalog_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('account/history', 'page={page}', 'SSL');
		
		$this->data['pagination'] = $pagination->render();

		$this->data['continue'] = $this->url->link('account/account', '', 'SSL');
		
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/return_list.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/return_list.tpl';
		} else {
			$this->template = 'default/template/account/return_list.tpl';
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
	
	public function info() {
		$this->load->language('account/return');
		
		if (isset($this->request->get['return_id'])) {
			$return_id = $this->request->get['return_id'];
		} else {
			$return_id = 0;
		}
    	
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/return/info', 'return_id=' . $return_id, 'SSL');
			
			$this->redirect($this->url->link('account/login', '', 'SSL'));
    	}
		
		$this->load->model('account/return');
						
		$return_info = $this->model_account_return->getReturn($return_id);
		
		if ($return_info) {
			$this->document->setTitle($this->language->get('text_return'));

			$this->data['breadcrumbs'] = array();
	
			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home', '', 'SSL'),
				'separator' => false
			);
	
			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_account'),
				'href'      => $this->url->link('account/account', '', 'SSL'),
				'separator' => $this->language->get('text_separator')
			);
			
			$url = '';
			
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}	
					
			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('heading_title'),
				'href'      => $this->url->link('account/return', $url, 'SSL'),
				'separator' => $this->language->get('text_separator')
			);
						
			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_return'),
				'href'      => $this->url->link('account/return/info', 'return_id=' . $this->request->get['return_id'] . $url, 'SSL'),
				'separator' => $this->language->get('text_separator')
			);			
			
			$this->data['heading_title'] = $this->language->get('text_return');
			
			$this->data['text_return_detail'] = $this->language->get('text_return_detail');
			$this->data['text_return_id'] = $this->language->get('text_return_id');
			$this->data['text_order_id'] = $this->language->get('text_order_id');
			$this->data['text_date_ordered'] = $this->language->get('text_date_ordered');
			$this->data['text_customer'] = $this->language->get('text_customer');
			$this->data['text_email'] = $this->language->get('text_email');
			$this->data['text_telephone'] = $this->language->get('text_telephone');			
			$this->data['text_status'] = $this->language->get('text_status');
			$this->data['text_date_added'] = $this->language->get('text_date_added');
			$this->data['text_product'] = $this->language->get('text_product');
			$this->data['text_comment'] = $this->language->get('text_comment');
      		$this->data['text_history'] = $this->language->get('text_history');
			
      		$this->data['column_product'] = $this->language->get('column_product');
      		$this->data['column_model'] = $this->language->get('column_model');
      		$this->data['column_quantity'] = $this->language->get('column_quantity');
      		$this->data['column_opened'] = $this->language->get('column_opened');
			$this->data['column_reason'] = $this->language->get('column_reason');
			$this->data['column_action'] = $this->language->get('column_action');
			$this->data['column_date_added'] = $this->language->get('column_date_added');
      		$this->data['column_status'] = $this->language->get('column_status');
      		$this->data['column_comment'] = $this->language->get('column_comment');
							
			$this->data['button_continue'] = $this->language->get('button_continue');
			
			$this->data['return_id'] = $return_info['return_id'];
			$this->data['order_id'] = $return_info['order_id'];
			$this->data['date_ordered'] = date($this->language->get('date_format_short'), strtotime($return_info['date_ordered']));
			$this->data['date_added'] = date($this->language->get('date_format_short'), strtotime($return_info['date_added']));
			$this->data['firstname'] = $return_info['firstname'];
			$this->data['lastname'] = $return_info['lastname'];
			$this->data['email'] = $return_info['email'];
			$this->data['telephone'] = $return_info['telephone'];						
			$this->data['product'] = $return_info['product'];
			$this->data['model'] = $return_info['model'];
			$this->data['quantity'] = $return_info['quantity'];
			$this->data['reason'] = $return_info['reason'];
			$this->data['opened'] = $return_info['opened'] ? $this->language->get('text_yes') : $this->language->get('text_no');
			$this->data['comment'] = nl2br($return_info['comment']);
			$this->data['action'] = $return_info['action'];
						
			$this->data['histories'] = array();
			
			$results = $this->model_account_return->getReturnHistories($this->request->get['return_id']);
			
      		foreach ($results as $result) {
        		$this->data['histories'][] = array(
          			'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
          			'status'     => $result['status'],
          			'comment'    => nl2br($result['comment'])
        		);
      		}
			
			$this->data['continue'] = $this->url->link('account/return', $url, 'SSL');

			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/return_info.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/return_info.tpl';
			} else {
				$this->template = 'default/template/account/return_info.tpl';
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
		} else {
			$this->document->setTitle($this->language->get('text_return'));
						
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
				'text'      => $this->language->get('heading_title'),
				'href'      => $this->url->link('account/return', '', 'SSL'),
				'separator' => $this->language->get('text_separator')
			);
			
			$url = '';
			
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
									
			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_return'),
				'href'      => $this->url->link('account/return/info', 'return_id=' . $return_id . $url, 'SSL'),
				'separator' => $this->language->get('text_separator')
			);
			
			$this->data['heading_title'] = $this->language->get('text_return');

			$this->data['text_error'] = $this->language->get('text_error');

			$this->data['button_continue'] = $this->language->get('button_continue');

			$this->data['continue'] = $this->url->link('account/return', '', 'SSL');

			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/error/not_found.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/error/not_found.tpl';
			} else {
				$this->template = 'default/template/error/not_found.tpl';
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
		
	public function insert() {
		$this->language->load('account/return');

		$this->load->model('account/return');
		

    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_account_return->addReturn($this->request->post);
	  		
			$this->redirect($this->url->link('account/return/success', '', 'SSL'));
    	} 
							
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
        	'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('account/return/insert', '', 'SSL'),
        	'separator' => $this->language->get('text_separator')
      	);
		
    	$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_description'] = $this->language->get('text_description');
		$this->data['text_order'] = $this->language->get('text_order');
		$this->data['text_product'] = $this->language->get('text_product');
		$this->data['text_yes'] = $this->language->get('text_yes');
		$this->data['text_no'] = $this->language->get('text_no');
		
		$this->data['entry_order_id'] = $this->language->get('entry_order_id');	
		$this->data['entry_date_ordered'] = $this->language->get('entry_date_ordered');	    	
		$this->data['entry_firstname'] = $this->language->get('entry_firstname');
    	$this->data['entry_lastname'] = $this->language->get('entry_lastname');
    	$this->data['entry_email'] = $this->language->get('entry_email');
    	$this->data['entry_telephone'] = $this->language->get('entry_telephone');
		$this->data['entry_product'] = $this->language->get('entry_product');	
		$this->data['entry_model'] = $this->language->get('entry_model');			
		$this->data['entry_quantity'] = $this->language->get('entry_quantity');				
		$this->data['entry_reason'] = $this->language->get('entry_reason');	
		$this->data['entry_opened'] = $this->language->get('entry_opened');	
		$this->data['entry_fault_detail'] = $this->language->get('entry_fault_detail');	
		$this->data['entry_captcha'] = $this->language->get('entry_captcha');
				
		$this->data['button_continue'] = $this->language->get('button_continue');
		$this->data['button_back'] = $this->language->get('button_back');
		    
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->error['order_id'])) {
			$this->data['error_order_id'] = $this->error['order_id'];
		} else {
			$this->data['error_order_id'] = '';
		}
				
		if (isset($this->error['firstname'])) {
			$this->data['error_firstname'] = $this->error['firstname'];
		} else {
			$this->data['error_firstname'] = '';
		}	
		
		if (isset($this->error['lastname'])) {
			$this->data['error_lastname'] = $this->error['lastname'];
		} else {
			$this->data['error_lastname'] = '';
		}		
	
		if (isset($this->error['email'])) {
			$this->data['error_email'] = $this->error['email'];
		} else {
			$this->data['error_email'] = '';
		}
		
		if (isset($this->error['telephone'])) {
			$this->data['error_telephone'] = $this->error['telephone'];
		} else {
			$this->data['error_telephone'] = '';
		}
				
		if (isset($this->error['product'])) {
			$this->data['error_product'] = $this->error['product'];
		} else {
			$this->data['error_product'] = '';
		}
		
		if (isset($this->error['model'])) {
			$this->data['error_model'] = $this->error['model'];
		} else {
			$this->data['error_model'] = '';
		}
						
		if (isset($this->error['reason'])) {
			$this->data['error_reason'] = $this->error['reason'];
		} else {
			$this->data['error_reason'] = '';
		}
		
 		if (isset($this->error['captcha'])) {
			$this->data['error_captcha'] = $this->error['captcha'];
		} else {
			$this->data['error_captcha'] = '';
		}	

		$this->data['action'] = $this->url->link('account/return/insert', '', 'SSL');
	
		$this->load->model('account/order');
		
		if (isset($this->request->get['order_id'])) {
			$order_info = $this->model_account_order->getOrder($this->request->get['order_id']);
		}
		
		$this->load->model('catalog/product');
		
		if (isset($this->request->get['product_id'])) {
			$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
		}
		
    	if (isset($this->request->post['order_id'])) {
      		$this->data['order_id'] = $this->request->post['order_id']; 	
		} elseif (!empty($order_info)) {
			$this->data['order_id'] = $order_info['order_id'];
		} else {
      		$this->data['order_id'] = ''; 
    	}
				
    	if (isset($this->request->post['date_ordered'])) {
      		$this->data['date_ordered'] = $this->request->post['date_ordered']; 	
		} elseif (!empty($order_info)) {
			$this->data['date_ordered'] = date('Y-m-d', strtotime($order_info['date_added']));
		} else {
      		$this->data['date_ordered'] = '';
    	}
				
		if (isset($this->request->post['firstname'])) {
    		$this->data['firstname'] = $this->request->post['firstname'];
		} elseif (!empty($order_info)) {
			$this->data['firstname'] = $order_info['firstname'];	
		} else {
			$this->data['firstname'] = $this->customer->getFirstName();
		}

		if (isset($this->request->post['lastname'])) {
    		$this->data['lastname'] = $this->request->post['lastname'];
		} elseif (!empty($order_info)) {
			$this->data['lastname'] = $order_info['lastname'];			
		} else {
			$this->data['lastname'] = $this->customer->getLastName();
		}
		
		if (isset($this->request->post['email'])) {
    		$this->data['email'] = $this->request->post['email'];
		} elseif (!empty($order_info)) {
			$this->data['email'] = $order_info['email'];				
		} else {
			$this->data['email'] = $this->customer->getEmail();
		}
		
		if (isset($this->request->post['telephone'])) {
    		$this->data['telephone'] = $this->request->post['telephone'];
		} elseif (!empty($order_info)) {
			$this->data['telephone'] = $order_info['telephone'];				
		} else {
			$this->data['telephone'] = $this->customer->getTelephone();
		}
		
		if (isset($this->request->post['product'])) {
    		$this->data['product'] = $this->request->post['product'];
		} elseif (!empty($product_info)) {
			$this->data['product'] = $product_info['name'];				
		} else {
			$this->data['product'] = '';
		}
		
		if (isset($this->request->post['model'])) {
    		$this->data['model'] = $this->request->post['model'];
		} elseif (!empty($product_info)) {
			$this->data['model'] = $product_info['model'];				
		} else {
			$this->data['model'] = '';
		}
			
		if (isset($this->request->post['quantity'])) {
    		$this->data['quantity'] = $this->request->post['quantity'];
		} else {
			$this->data['quantity'] = 1;
		}	
				
		if (isset($this->request->post['opened'])) {
    		$this->data['opened'] = $this->request->post['opened'];
		} else {
			$this->data['opened'] = false;
		}	
		
		if (isset($this->request->post['return_reason_id'])) {
    		$this->data['return_reason_id'] = $this->request->post['return_reason_id'];
		} else {
			$this->data['return_reason_id'] = '';
		}	
														
		$this->load->model('localisation/return_reason');
		
    	$this->data['return_reasons'] = $this->model_localisation_return_reason->getReturnReasonsRMA();
		
		if (isset($this->request->post['comment'])) {
    		$this->data['comment'] = $this->request->post['comment'];
		} else {
			$this->data['comment'] = '';
		}	
		
		if (isset($this->request->post['captcha'])) {
			$this->data['captcha'] = $this->request->post['captcha'];
		} else {
			$this->data['captcha'] = '';
		}		

		$this->data['back'] = $this->url->link('account/account', '', 'SSL');
				
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/return_form.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/return_form.tpl';
		} else {
			$this->template = 'default/template/account/return_form.tpl';
		}
		
		$this->children = array(
		//	'common/column_left',
			//'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'	
		);
				
		$this->response->setOutput($this->render());		
  	}
	
  	public function success() {
		$this->language->load('account/return');
		$this->load->model('account/return');
		$this->load->model('checkout/order');

		$this->document->setTitle($this->language->get('heading_title')); 

	  	$this->data['breadcrumbs'] = array();

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
        	'separator' => false
      	);

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('account/return', '', 'SSL'),
        	'separator' => $this->language->get('text_separator')
      	);	
				
    	$this->data['heading_title'] = $this->language->get('heading_title');

    	$this->data['text_message'] = $this->language->get('text_message');

    	$this->data['button_continue'] = $this->language->get('button_continue');
	
    	$this->data['continue'] = $this->url->link('common/home');

		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/common/success_rma.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/common/success_rma.tpl';
		} else {
			$this->template = 'default/template/common/success_rma.tpl';
		}
		
		
		require_once(DIR_SYSTEM.'html2_pdf_lib/html2pdf.class.php');
		
		
		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$server = HTTPS_IMAGE;
		} else {
			$server = HTTP_IMAGE;
		}

		
				
		if ($this->config->get('config_logo') && file_exists(DIR_IMAGE . $this->config->get('config_logo'))) {
			$logo = $server . $this->config->get('config_logo');
		} else {
			$logo = '';
		}
		
		$rma_detail = $this->model_account_return->getRMAReturn($this->session->data['return_id']);
		$rma_items = $this->model_account_return->getRMAReturnItems($this->session->data['return_id']);
		$order_info = $this->model_checkout_order->getOrder($rma_detail['order_id']);
		
		$barcode_image = '<barcode type="C128A" value="RMA:'.$rma_detail['rma_number'].'" label="label" style="width:50mm; height:8mm; color: #000; font-size: 3mm"></barcode>';
		$html = '
		<link rel="stylesheet" type="text/css" href="catalog/view/theme/bt_optronics/stylesheet/stylesheet.css" />
		<div style="text-align:left">';
		$html.='<img src="'.$logo.'" /><br /><br>';
		$html.='<h4>RMA # '.$rma_detail['rma_number'].'</h4><br />';
		$html.='Date: '.date($this->language->get('date_format_short'), strtotime($rma_detail['date_added'])).'<br />';
		$html.='Original Order: '.$rma_detail['order_id'].'<br /><br>';
		$html.='<div style="border-top:1px dotted black">
		<br />
		<table>
		<tr>
			<td style="width:250px">
			<strong>Return Address</strong><br />
			'.$order_info['shipping_address_1'].' <br>
			'.$order_info['shipping_city'].', '.$order_info['shipping_zone'].' <br />
			'.$order_info['shipping_postcode'].'.
			 </td>
			<td style="width:300px"><strong>Customer Information</strong><br />
			'.$order_info['firstname'].' '.$order_info['lastname'].' <br>
			'.$order_info['email'].' <br />
			'.$order_info['telephone'].'</td>
		
		</tr>
		
		</table>
		<br>
		<table class="list">
		
		<thead>
		<tr>
		<td style="width:120px">SKU * Qty</td>
		<td style="width:280px">Item Name</td>
		<td style="width:170px">Return Reason</td>
		<td style="width:100px">How to Process</td>
		</tr>
		
		</thead>
		<tbody>
		';
		foreach($rma_items as $item)
		{
		$html.='<tr>';	
		$html.='<td style="width:120px">'.$item['sku'].' * 1</td>';	
		$html.='<td style="width:280px">'.$item['title'].'</td>';	
		$html.='<td style="width:170px">'.$item['return_code'].'</td>';	
		$html.='<td style="width:100px">'.$item['how_to_process'].'</td>';	
		$html.='</tr>';	
			
		}
		
		$html.='
		</tbody>
		</table>
		<br><br>
		';
		
		$html.='
		<div style="text-align:center;border-top:1px dotted black;margin-bottom:5px">
		<br />
		<span style="color:blue">Print this mailing label</span> and affix to your return packages<br>
		<small style="font-size:12px;margin-top:5px" ><img src="'.HTTP_IMAGE.'content-cutout.png"> Cut or fold the label along this line and affix to the outside of the return package.</small>
		
		</div>
		<div style="border: 2px dashed grey;width:530px;margin-left:85px;padding:8px">
		<div style="border:1px solid black">
		<table style="padding:20px" cellspacing="5">
		<tr>
		<td style="border-bottom:0.7px solid black;width:320px">'.$order_info['firstname'].' '.$order_info['lastname'].'</td>
		<td style="width:100px">&nbsp;</td>
		<td rowspan="4" style="vertical-align:middle;border:1px solid black;padding:5px;font-size:10px;width:60px;text-align:center">POSTAGE <br> REQUIRED</td>
		</tr>
		<tr>
		<td style="border-bottom:0.7px solid black;width:320px">'.$order_info['shipping_address_1'].'</td>
		<td >&nbsp;</td>
		
		</tr>
		<tr>
		<td style="border-bottom:0.7px solid black;width:320px">'.$order_info['shipping_city'].', '.$order_info['shipping_zone'].', '.$order_info['shipping_postcode'].'</td>
		<td >&nbsp;</td>
		
		</tr>
		<tr>
		<td >&nbsp;</td>
		<td >&nbsp;</td>
		
		</tr>
		
		<tr>
		<td  >&nbsp;</td>
		<td >&nbsp;</td>
		<td style="color:#FFF;background-color:black;font-size:42px;height:40px;text-align:center;vertical-align:middle;font-family:Arial;font-weight:bold">CP</td>
		</tr>
		<tr>
		<td  style="text-align:center">
		<div style="text-align:left">
		PPUSA Returns<br/>
		5145 South Arville Street<br>Suite A<br>Las Vegas, NV 89118
		</div>
		</td>
		</tr>
		<tr >
		<td colspan="3" style="text-align:center;">
		<br><br>
		'.$barcode_image.'
		</td>
		</tr>
		</table>
		</div>
		</div>
		';
		
		
		$html.='</div>';
      
    $html.='</div>';
		
		 try
    {
		
		
		
        $html2pdf = new HTML2PDF('P', 'A4', 'en');
      
$html2pdf->setDefaultFont('courier');
$html2pdf->writeHTML($html);

$filename = time();
$file = $html2pdf->Output(DIR_IMAGE.'returns/'.$filename.'.pdf','F');

//pdf creation

//now magic starts
/*$im = new imagick();


$im->setImageFormat( "jpg" );


	
$im->setSize(1000,800);

	$im->writeImage($img_name);
$im->clear();
$im->destroy(); */

// instantiate Imagick 

$img_name = $filename.'.jpg';
$im = new Imagick();

$im->setResolution(500,500);
$im->readimage(DIR_IMAGE.'returns/'.$filename.'.pdf'); 
$im->setImageFormat('jpeg');    
$im->writeImage(DIR_IMAGE."returns/".$img_name); 
$im->clear(); 
$im->destroy();

//remove temp pdf
//unlink('temp.pdf');

    }
	catch(HTML2PDF_exception $e) {
        echo $e;
        exit;
    }
		
		$this->data['return_image'] = $img_name;
		
		
		$html2='Thank you for submitting your return request. Your RMA # is '.$rma_detail['rma_number']."<br>";
		$html2.='Please read our returns policy (<a href="http://phonepartsusa.com/returns-or-exchanges">http://phonepartsusa.com/returns-or-exchanges</a>) and note the following:';
		
		$html2.='<ol>';
		$html2.='<li>Print and Affix RMA Label on the exterior of the Package</li>';
		$html2.='<li>Returns <span style="color:red"><strong>must be postmarked within 30 days after</strong> '.date($this->language->get('date_format_short'), strtotime($rma_detail['date_added'])).'</span> . If the returned package is sent after this time, we reserve the right to refuse refunds and exchanges.</li>';
		$html2.='<li>Exchanges and refunds will only be offered for items in their original unused condition. Damaged items will <strong>NOT</strong> be refunded.</li>';
		$html2.='<li>Please allow 24-48 business hours for return processing.</li>';
		
		
		$html2.='</ol>';
		
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
		$mail->setSender($this->config->get('config_name'));
		$mail->setSubject(html_entity_decode("RMA Return", ENT_QUOTES, 'UTF-8'));
		$mail->addAttachment(DIR_IMAGE.'returns/'.$filename.'.pdf');
		$mail->setHtml($html2);
		$mail->send();
		
		
		$this->children = array(
			//'common/column_left',
			//'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'	
		);
				
 		$this->response->setOutput($this->render()); 
	}
		
  	private function validate() {
    	if (!$this->request->post['order_id']) {
      		$this->error['order_id'] = $this->language->get('error_order_id');
    	}
		
		if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32)) {
      		$this->error['firstname'] = $this->language->get('error_firstname');
    	}

    	if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32)) {
      		$this->error['lastname'] = $this->language->get('error_lastname');
    	}

    	if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
      		$this->error['email'] = $this->language->get('error_email');
    	}
		
    	if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
      		$this->error['telephone'] = $this->language->get('error_telephone');
    	}		
		
		if ((utf8_strlen($this->request->post['product']) < 1) || (utf8_strlen($this->request->post['product']) > 255)) {
			$this->error['product'] = $this->language->get('error_product');
		}	
		
		if ((utf8_strlen($this->request->post['model']) < 1) || (utf8_strlen($this->request->post['model']) > 64)) {
			$this->error['model'] = $this->language->get('error_model');
		}							

		if (empty($this->request->post['return_reason_id'])) {
			$this->error['reason'] = $this->language->get('error_reason');
		}	
				
    	if (empty($this->session->data['captcha']) || ($this->session->data['captcha'] != $this->request->post['captcha'])) {
      		$this->error['captcha'] = $this->language->get('error_captcha');
    	}

		if (!$this->error) {
      		return true;
    	} else {
      		return false;
    	}
  	}
	
	public function validateNewReturn()
	{
		$order_id = $this->request->get['order_id'];
		$order_id2 = $this->request->get['order_id2'];
		
		$email = $this->request->get['email'];
		$postcode = $this->request->get['postcode'];
		$json = array();
		$error = false;
		if($order_id=='' and $order_id2=='')
		{
			$error = true;
			
			
		}
		
		if($email=='' and $postcode=='')
		{
			$error = true;
			
		}
		
		
		if($order_id!='' and $email=='')
		{
			$error = true;
			
		}
		if($order_id2!='' and $postcode=='')
		{
			$error = true;
			
		}
		
		if($email!='' and $order_id=='')
		{
			$error = true;
			
		}
		if($postcode!='' and $order_id2=='')
		{
			
			$error = true;
		}
		
				$this->load->model('checkout/order');
				$final_order_id;
				if($order_id!='')
				{
					$final_order_id = $order_id;
					$order_info = $this->model_checkout_order->getOrder($order_id);
					
					if($order_info)
					{
						if($order_info['email']!=$email)
						{
							$error = true;	
						}
						
					}
					else
					{
						$error = true;	
					}
					
				}
				else if($order_id2!='')
				{
					$final_order_id = $order_id;
					$order_info = $this->model_checkout_order->getOrder($order_id2);
					
					if($order_info)
					{
						if($order_info['payment_postcode']!=$postcode)
						{
							$error = true;	
						}
						
					}
					else
					{
						$error = true;	
					}
					
				}
			
			
		
		if($error)
		{
		
		$json['error'] = 'The Order ID and Detail provided do not match. The information can be located on the Invoice included in the package or the Order Confirmation Email';	
		}
		else
		{
			$json['success'] = $final_order_id;	
		}
		echo json_encode($json);
		
	}
	
	public function showProducts()
	{
		
		$order_id = $this->request->get['order_id'];
		$this->load->model('checkout/order');
		$order_info = $this->model_checkout_order->getOrder($order_id);
		if(!$order_info)
		{
		echo "<h1>Unable to locate the Order ID</h1>";exit;	
			
		}
		
		/*$returnExist = $this->db->query("select rma_number from inv_returns where order_id = '$order_id'");
	if($returnExist->row){	
		echo "<h1>A return is already exist for this orderID. RMA# is ".$returnExist->row['rma_number'].".</h1>";exit;
	}*/
		
		
		$products = $this->model_checkout_order->getGTSOrderProduct($order_id);
		$html = '<div style="text-align:left">';
		
		$html .= 'Hi, '.$order_info['firstname'].' '.$order_info['lastname'].', lets quickly generate your Return RMA<br /><br />';
		$html.='<h2>Order ID: '.$order_id.'</h2>';
		$html.='<h2>Order Date: '.date($this->language->get('date_format_short'), strtotime($order_info['date_added'])).'</h2><br/>';
			$html.='<table class="list">
    <thead>
      <tr>
        <td ></td>
        <td >SKU * Qty</td>
        <td >Item Name</td>
		<td >Return Reason</td>
		
		<td>How would you like your return to be processed?</td>
		
      </tr>
    </thead>
    <tbody>';
	$this->load->model('localisation/return_reason');
     $return_reasons = $this->model_localisation_return_reason->getReturnReasonsRMA();
	 $i=0;
     foreach($products as $product)
	 {
		 
		 for($k=1;$k<=$product['quantity'];$k++)
		 {
			$html.='<tr>';
			$html.='<td class="left"><input onChange="returnSelect();if(this.checked==true){$(\'#select_return'.$i.'\').fadeIn();$(\'#return_processing'.$i.'\').fadeIn()}else{$(\'#select_return'.$i.'\').fadeOut();$(\'#return_processing'.$i.'\').fadeOut()}" type="checkbox" id="return_select'.$i.'" value="'.$product['product_id'].'"></td>';
			$html.='<td>'.$product['model'].' * 1</td>';
			$html.='<td>'.$product['name'].'</td>';
			$html.='<td><select id="select_return'.$i.'" onChange="returnSelect()" style="display:none">';
			foreach($return_reasons as $reason)
			{
				$html.='<option value="'.$reason['return_reason_id'].'">'.$reason['name'].'</option>';	
				
			}
			
			$html.='</select></td>';
			$html.='<td><select id="return_processing'.$i.'" onChange="returnSelect()" style="width:280px;display:none;">
			<option value="Exchange">Exchange</option>
			<option value="Refund">Refund</option>
			</select>
			</td>';
			
			$html.='</tr>'; 
	$i++;	 
		 }
	 
	 }
      
      
    $html.='</tbody>
  </table>
  <input type="hidden" id="total_records" value="'.$i.'">
  <input type="hidden" name="order_id" value="'.$order_id.'">
  <input type="hidden" id="checked_records" name="checked_records">
  <div style="text-align:center;margin-top:10px">Pressing submit will generate a printer-friendly RMA confirmation page. Please print and enclose this page with the return shipment.<br /><br /><span class="button_pink"><input type="button" class="button" id="generate_rma" value="Generate RMA" ></span></div>
  
  
  </div>
  
  <script>
  $("#generate_rma").click(function(e) {
generateRMA();
});
function returnSelect()
{
var total_records = $("#total_records").val();	
	var str ="";
	for(var i=0;i<=total_records;i++)
	{
		if($("#return_select"+i).is(":checked"))
		{
			if(i>0)
			{
			str+="~";	
			}
			str+=$("#return_select"+i).val()+","+$("#select_return"+i).val()+","+$("#return_processing"+i).val();
			
			
		}
		
	}
	$("#checked_records").val(str);
}

function generateRMA()
{
	$.ajax({
		url: "index.php?route=account/return/generateRMA",
		data:$("#form_div input ,#form_div select"),
		type:"post",
		dataType: "json",
		beforeSend: function() {
			$(".warning").remove();
			
			$("#generate_rma").attr("disabled","disabled");
		},
		complete: function() {
			$(".wait").remove();
		},			
		success: function(json) {
			
				
				if(json["success"])
				{
					
				window.location=json["success"];	
					
				}
				else{
					
				alert(json["error"]);
				$("#generate_rma").removeAttr("disabled");
				return false;	
				}
				
				
			
			
		
		},
		error: function(xhr, ajaxOptions, thrownError) {
			
		}
	});
	
}

  
  </script>
  
  ';
  
  
  echo $html;
		
	}
	public function captcha() {
		$this->load->library('captcha');
		
		$captcha = new Captcha();
		
		$this->session->data['captcha'] = $captcha->getCode();
		
		$captcha->showImage();
	}
	
	public function generateRMA()
	{
		$this->load->model('checkout/order');
		
		$this->load->model('catalog/product');
		$this->load->model('account/return');
		
		
		
		$order_id = $this->request->post['order_id'];
		$checked_records = $this->request->post['checked_records'];
		if($checked_records=='')
		{
		$json['error'] = 'Please select minimum 1 product for return';
		
		echo json_encode($json);
		exit;	
		}
		$checked_records = explode("~",$checked_records);
		
		$order_info = $this->model_checkout_order->getOrder($order_id);
		
		$data = array();
		$data['order_id'] = $order_info['order_id'];
		$data['email'] = $order_info['email'];
		
		$return_id = $this->model_account_return->addReturn($data);
		
		foreach($checked_records as $records)
		{
			$record = explode(",",$records);
			
			$product_info = $this->model_catalog_product->getProduct($record[0]);
			
		$data = array();
		$data['order_id'] = $order_info['order_id'];
		$data['email'] = $order_info['email'];
		$data['price'] = $product_info['price'];
		
		$data['sku']=$product_info['model'];
		$data['title'] = $product_info['name'];
		$data['quantity'] = 1;
		$data['return_code'] = $record[1];
		
		
		$data['return_id'] = $return_id;
		$data['return_processing'] = $record[2];
		$data['source']='mail';
		
		//print_r($product_info);exit;
		if($product_info)
		{
			
			$CheckQuery = $this->db->query("SELECT
a.*
FROM
    `inv_returns` a
    INNER JOIN `inv_return_items` b
        ON (a.`id` = b.`return_id`)
        WHERE a.`order_id`='".$order_info['order_id']."' AND b.`sku`='".$product_info['model']."' AND b.return_id<>'".$return_id."'");
			
			
			if($CheckQuery->row)
			{
				$this->db->query("DELETE FROM inv_returns WHERE id='".$return_id."'");	
				
				$json['error'] = 'Item return already processed, please try again.';
		
		echo json_encode($json);
		exit;
			}
			
		$this->model_account_return->addReturnProduct($data);
		
		}
		
		
		
		}
		
		$this->session->data['return_id']=$return_id;
		
		$json['success'] = $this->url->link('account/return/success', '', 'SSL');
		//$json['success'] = 'here';
		
		echo json_encode($json);
		exit;
	}
}
?>
