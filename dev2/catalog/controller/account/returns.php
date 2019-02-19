<?php 
class ControllerAccountReturns extends Controller {
	private $error = array();
		
	public function index() {
    	if (!$this->customer->isLogged()) {
      		$this->session->data['redirect'] = $this->url->link('account/returns', '', 'SSL');

	  		$this->redirect($this->url->link('account/login', '', 'SSL'));
    	}
		
		$this->language->load('account/returns');
		
		$this->load->model('account/returns');
		$this->load->model('account/order');

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
			'href'      => $this->url->link('account/returns', $url, 'SSL'),        	
        	'separator' => $this->language->get('text_separator')
      	);

		$this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['text_return_id'] = $this->language->get('text_return_id');
        $this->data['text_rma_number'] = $this->language->get('text_rma_number');
        $this->data['text_order_id'] = $this->language->get('text_order_id');
        $this->data['text_status'] = $this->language->get('text_status');
        $this->data['text_date_added'] = $this->language->get('text_date_added');
        $this->data['text_customer'] = $this->language->get('text_customer');
        $this->data['text_empty'] = $this->language->get('text_empty');

        $this->data['button_view'] = $this->language->get('button_view');
        $this->data['button_continue'] = $this->language->get('button_continue');
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$this->data['returns'] = array();
		
		$returns_total = $this->model_account_returns->getTotalReturns();
		
		$results = $this->model_account_returns->getReturns(($page - 1) * 60, 10);

		
		foreach ($results as $result) {
			//$orderTotal = $this->model_account_order->getOrderSubTotal($result['order_id']);
			$orderTotal = 0;
			$rma_products = $this->model_account_returns->getReturnProducts($result['id']);
			foreach ($rma_products as $product) {
				$orderTotal = $product['price'] + $orderTotal;
			}
			$this->data['returns'][] = array(
				'return_id'   => $result['id'],
				'rma_number'   => $result['rma_number'],
				'order_id'   => $result['order_id'],
				'rma_status'     => $result['rma_status'],
				'total' => $orderTotal,
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'href'       => $this->url->link('account/returns/info', 'return_id=' . $result['id'], 'SSL'),
				'order_href'       => $this->url->link('account/order/info', 'order_id=' . $result['order_id'], 'SSL')
			);
		}

		$pagination = new Pagination();
		$pagination->total = $returns_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('account/returns', 'page={page}', 'SSL');
		
		$this->data['pagination'] = $pagination->render();
		if (isset($this->session->data['viewreturn'])) {
			$this->data['ischild'] = $this->session->data['viewreturn'];
		}
		$this->data['continue'] = $this->url->link('account/account', '', 'SSL');

		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/returns_list.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/returns_list.tpl';
		} else {
			$this->template = 'default/template/account/returns_list.tpl';
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
		$this->language->load('account/returns');
		
		if (isset($this->request->get['return_id'])) {
			$return_id = $this->request->get['return_id'];
		} else {
			$return_id = 0;
		}	

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/returns', 'return_id=' . $return_id, 'SSL');
			
			$this->redirect($this->url->link('account/login', '', 'SSL'));
    	}
						
		$this->load->model('account/returns');
			
		$return_info = $this->model_account_returns->getReturn($return_id);
		
		if ($return_info) {
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
			
			$url = '';
			
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
						
			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('heading_title'),
				'href'      => $this->url->link('account/returns', $url, 'SSL'),      	
				'separator' => $this->language->get('text_separator')
			);
			
			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_return'),
				'href'      => $this->url->link('account/returns/info', 'return_id=' . $this->request->get['return_id'] . $url, 'SSL'),
				'separator' => $this->language->get('text_separator')
			);
					
      		$this->data['heading_title'] = $this->language->get('text_return');

            $this->data['text_return_detail'] = $this->language->get('text_return_detail');
            $this->data['text_return_id'] = $this->language->get('text_return_id');
            $this->data['text_rma_number'] = $this->language->get('text_rma_number');
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
            $this->data['column_price'] = $this->language->get('column_price');
            $this->data['column_date_added'] = $this->language->get('column_date_added');
            $this->data['column_status'] = $this->language->get('column_status');
            $this->data['column_comment'] = $this->language->get('column_comment');

            $this->data['button_continue'] = $this->language->get('button_continue');
		
      		
            $return_info['date_added'] = date($this->language->get('date_format_short'), strtotime($return_info['date_added']));
            $return_info['pdf_link'] = 'imp/pdf_reports/rma_report.php?return_id=' . $return_id;
            
            
			$this->data['return_info'] = $return_info;
			
      		$this->data['products'] = $this->model_account_returns->getReturnProducts($return_info['id']);
      		foreach ($this->data['products'] as $key => $product) {
      			if (!$product['decision']) {
      				$decision = $this->model_account_returns->getReturnItemDecision($product['id']);
      				$this->data['products'][$key]['decision'] = $decision['action'];
      			}
      		}


      		$this->data['replacements'] = $this->model_account_returns->getReturnsReplacements($return_info['id']);
      		$this->data['credits'] = $this->model_account_returns->getReturnsCredits($return_info['id']);
      		$this->data['refunds'] = $this->model_account_returns->getReturnsRefunds($return_info['id']);

      		$this->data['continue'] = $this->url->link('account/returns', '', 'SSL');
		
			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/returns_info.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/returns_info.tpl';
			} else {
				$this->template = 'default/template/account/returns_info.tpl';
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
			
      		$this->data['heading_title'] = $this->language->get('text_return');

      		$this->data['text_error'] = $this->language->get('text_error');

      		$this->data['button_continue'] = $this->language->get('button_continue');
			
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
				'href'      => $this->url->link('account/returns', '', 'SSL'),
				'separator' => $this->language->get('text_separator')
			);
			
			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_return'),
				'href'      => $this->url->link('account/returns/info', 'return_id=' . $return_id, 'SSL'),
				'separator' => $this->language->get('text_separator')
			);
												
      		$this->data['continue'] = $this->url->link('account/returns', '', 'SSL');
			 			
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
}
?>