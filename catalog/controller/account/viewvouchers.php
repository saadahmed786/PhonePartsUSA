<?php 
class ControllerAccountViewvouchers extends Controller { 
	public function index() {
		if (!$this->customer->isLogged()) {
	  		$this->session->data['redirect'] = $this->url->link('account/viewvouchers', '', 'SSL');
	  
	  		$this->redirect($this->url->link('account/login', '', 'SSL'));
    	}
		$this->load->model('account/viewvouchers'); 
	
		$this->language->load('account/viewvouchers');

		$this->document->setTitle($this->language->get('heading_title'));

      	$this->data['breadcrumbs'] = array();

      	$this->data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
        	'separator' => false
      	); 

      	$this->data['breadcrumbs'][] = array(       	
        	'text'      => $this->language->get('text_viewvouchers'),
			'href'      => $this->url->link('account/viewvouchers', '', 'SSL'),
        	'separator' => $this->language->get('text_separator')
      	);
		
		if (isset($this->session->data['success'])) {
    		$this->data['success'] = $this->session->data['success'];
			
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
			if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$total_vouchers = $this->model_account_viewvouchers->getTotalVouchers($this->customer->getEmail(), 90);
		$vouchers = $this->model_account_viewvouchers->getVouchers($this->customer->getEmail(), ($page - 1) * 10,10, 90);
		
		$this->data['vouchers'] = $vouchers;
    	$this->data['heading_title'] = $this->language->get('heading_title');
		
    	if (isset($this->session->data['viewvouchers'])) {
    		$this->data['vouchers'] = array();
    		foreach($vouchers as $voucher)
    		{
    			if($voucher['balance']>0.00)
    			{
    				$this->data['vouchers'][] = $voucher;
    			}
    		}

    	}
		
		
		$pagination = new Pagination();
		$pagination->total = $total_vouchers;
		$pagination->page = $page;
		$pagination->limit = 10; 
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('account/viewvouchers', 'page={page}', 'SSL');
			
		$this->data['pagination'] = $pagination->render();
		if (isset($this->session->data['viewvouchers'])) {
			$this->data['ischild'] = $this->session->data['viewvouchers'];
		}

		$this->data['dashboard'] = $this->url->link('account/account', '', 'SSL');
		
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/viewvouchers.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/viewvouchers.tpl';
		} else {
			$this->template = 'default/template/account/viewvouchers.tpl';
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
?>