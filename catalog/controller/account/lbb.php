<?php 
class ControllerAccountLbb extends Controller {
	private $error = array();
		
	public function index() {
    	if (!$this->customer->isLogged()) {
      		$this->session->data['redirect'] = $this->url->link('account/lbb', '', 'SSL');

	  		$this->redirect($this->url->link('account/login', '', 'SSL'));
    	}
		
		$this->language->load('account/lbb');
		
		$this->load->model('account/lbb');

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
			'href'      => $this->url->link('account/lbb', $url, 'SSL'),        	
        	'separator' => $this->language->get('text_separator')
      	);

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_lbb_id'] = $this->language->get('text_lbb_id');
		$this->data['text_shipment_no'] = $this->language->get('text_shipment_no');
		$this->data['text_status'] = $this->language->get('text_status');
		$this->data['text_date_added'] = $this->language->get('text_date_added');
		$this->data['text_customer'] = $this->language->get('text_customer');
		$this->data['text_products'] = $this->language->get('text_products');
		$this->data['text_total'] = $this->language->get('text_total');
		$this->data['text_empty'] = $this->language->get('text_empty');

		$this->data['button_view'] = $this->language->get('button_view');
		$this->data['button_continue'] = $this->language->get('button_continue');
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$lbb_total = $this->model_account_lbb->getTotalLbb();
		
		$results = $this->model_account_lbb->getLbbs(($page - 1) * 10, 10);
		
		foreach ($results as $result) {

			$this->data['lbbs'][] = array(
				'buyback_id'   => $result['buyback_id'],
				'shipment_number'   => $result['shipment_number'],
				'pdf_link' => 'imp/buyback/pdf_report.php?authcode=1&shipment_number=' . $result['shipment_number'],
				'status'     => $result['status'],
				'total'      => $result['total_received'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'href'       => $this->url->link('account/lbb/info', 'buyback_id=' . $result['buyback_id'], 'SSL')
			);
		}

		$pagination = new Pagination();
		$pagination->total = $lbb_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('account/lbb', 'page={page}', 'SSL');
		
		$this->data['pagination'] = $pagination->render();

		$this->data['continue'] = $this->url->link('account/account', '', 'SSL');
		if (isset($this->session->data['viewlbb'])) {
			$this->data['ischild'] = $this->session->data['viewlbb'];
		}
		$this->data['dashboard'] = $this->url->link('account/account', '', 'SSL');
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/lbb_list.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/lbb_list.tpl';
		} else {
			$this->template = 'default/template/account/lbb_list.tpl';
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
		$this->language->load('account/lbb');
		
		if (isset($this->request->get['buyback_id'])) {
			$buyback_id = $this->request->get['buyback_id'];
		} else {
			$buyback_id = 0;
		}

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/lbb', 'buyback_id=' . $buyback_id, 'SSL');
			
			$this->redirect($this->url->link('account/login', '', 'SSL'));
    	}
						
		$this->load->model('account/lbb');
			
		$lbb_info = $this->model_account_lbb->getLbb($buyback_id);
		
		if ($lbb_info) {
			$this->document->setTitle($this->language->get('text_lbb'));
			
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
				'href'      => $this->url->link('account/lbb', $url, 'SSL'),      	
				'separator' => $this->language->get('text_separator')
			);
			
			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_lbb'),
				'href'      => $this->url->link('account/lbb/info', 'buyback_id=' . $this->request->get['buyback_id'] . $url, 'SSL'),
				'separator' => $this->language->get('text_separator')
			);
					
      		$this->data['heading_title'] = $this->language->get('text_lbb');
			
			$this->data['text_lbb_detail'] = $this->language->get('text_lbb_detail');
			$this->data['text_invoice_no'] = $this->language->get('text_invoice_no');
    		$this->data['text_lbb_id'] = $this->language->get('text_lbb_id');
			$this->data['text_date_added'] = $this->language->get('text_date_added');
      		$this->data['text_shipping_method'] = $this->language->get('text_shipping_method');
			$this->data['text_shipping_address'] = $this->language->get('text_shipping_address');
      		$this->data['text_payment_method'] = $this->language->get('text_payment_method');
      		$this->data['text_payment_address'] = $this->language->get('text_payment_address');
      		$this->data['text_history'] = $this->language->get('text_history');
			$this->data['text_comment'] = $this->language->get('text_comment');

      		$this->data['column_name'] = $this->language->get('column_name');
      		$this->data['column_model'] = $this->language->get('column_model');
      		$this->data['column_quantity_oem'] = $this->language->get('column_quantity_oem');
      		$this->data['column_quantity_non_oem'] = $this->language->get('column_quantity_non_oem');
      		$this->data['column_oem_price'] = $this->language->get('column_oem_price');
      		$this->data['column_non_oem_price'] = $this->language->get('column_non_oem_price');
      		$this->data['column_total'] = $this->language->get('column_total');
			$this->data['column_action'] = $this->language->get('column_action');
			$this->data['column_date_added'] = $this->language->get('column_date_added');
      		$this->data['column_status'] = $this->language->get('column_status');
      		$this->data['column_comment'] = $this->language->get('column_comment');
			
			$this->data['button_return'] = $this->language->get('button_return');
      		$this->data['button_continue'] = $this->language->get('button_continue');
		
      		$lbb_data = $lbb_info;
      		if ($lbb_info['address_id'] != '-1') {
      			$aDetails = $this->model_account_lbb->getAddressDetail($lbb_info['address_id']);
      			$lbb_data['firstname'] = $aDetails['firstname'];
      			$lbb_data['lastname'] = $aDetails['lastname'];
      			$lbb_data['address_1'] = $aDetails['address_1'];
      			$lbb_data['city'] = $aDetails['city'];
      			$lbb_data['postcode'] = $aDetails['postcode'];
      			$lbb_data['date_added'] = date($this->language->get('date_format_short'), strtotime($lbb_info['date_added']));
      		}
      		
      		$lbb_data['pdf_link'] = 'imp/buyback/pdf_report.php?authcode=1&shipment_number=' . $lbb_data['shipment_number'];

			$this->data['lbb_data'] = $lbb_data;


      		$payment = $this->model_account_lbb->getLbbPayment($lbb_info['buyback_id']);

      		$this->data['payment_details'] = array();

      		if ($payment) {
      			$this->data['payment_details'] = $payment;
      		}

      		$this->data['products'] = $this->model_account_lbb->getLbbProducts($lbb_info['buyback_id']);

      		$this->data['continue'] = $this->url->link('account/lbb', '', 'SSL');
		
			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/lbb_info.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/lbb_info.tpl';
			} else {
				$this->template = 'default/template/account/lbb_info.tpl';
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
			$this->document->setTitle($this->language->get('text_lbb'));
			
      		$this->data['heading_title'] = $this->language->get('text_lbb');

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
				'href'      => $this->url->link('account/lbb', '', 'SSL'),
				'separator' => $this->language->get('text_separator')
			);
			
			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_lbb'),
				'href'      => $this->url->link('account/lbb/info', 'buyback_id=' . $buyback_id, 'SSL'),
				'separator' => $this->language->get('text_separator')
			);
												
      		$this->data['continue'] = $this->url->link('account/lbb', '', 'SSL');
			 			
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