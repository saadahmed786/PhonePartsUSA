<?php 
class ControllerAccountAccount extends Controller { 
	public function index() {
		if (!$this->customer->isLogged()) {
	  		$this->session->data['redirect'] = $this->url->link('account/account', '', 'SSL');
	  
	  		$this->redirect($this->url->link('account/login', '', 'SSL'));
    	} 
    	$this->document->addScript('catalog/view/javascript/ppusa2.0/labelholder.js');
		$this->document->addStyle('catalog/view/theme/ppusa2.0/stylesheet/labelholder.css');
    	// echo $this->customer->getFirstName();exit;
	
		$this->language->load('account/account');



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
		
		if (isset($this->session->data['success'])) {
    		$this->data['success'] = $this->session->data['success'];
			
			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
		
    	$this->data['heading_title'] = $this->language->get('heading_title');

    	$this->data['text_my_account'] = $this->language->get('text_my_account');
		$this->data['text_my_orders'] = $this->language->get('text_my_orders');
		$this->data['text_my_lbb'] = $this->language->get('text_my_lbb');
		$this->data['text_my_returns'] = $this->language->get('text_my_returns');
		$this->data['text_my_newsletter'] = $this->language->get('text_my_newsletter');
    	$this->data['text_edit'] = $this->language->get('text_edit');
    	$this->data['text_password'] = $this->language->get('text_password');
    	$this->data['text_address'] = $this->language->get('text_address');
		$this->data['text_wishlist'] = $this->language->get('text_wishlist');
    	$this->data['text_order'] = $this->language->get('text_order');
		$this->data['text_lbb'] = $this->language->get('text_lbb');
		$this->data['text_returns'] = $this->language->get('text_returns');
    	$this->data['text_download'] = $this->language->get('text_download');
		$this->data['text_reward'] = $this->language->get('text_reward');
		$this->data['text_return'] = $this->language->get('text_return');
		$this->data['text_transaction'] = $this->language->get('text_transaction');
		$this->data['text_newsletter'] = $this->language->get('text_newsletter');
		$this->data['text_viewvouchers'] = 'View Store Credit Vouchers';

    	$this->data['edit'] = $this->url->link('account/edit', '', 'SSL');
    	$this->data['password'] = $this->url->link('account/password', '', 'SSL');
		$this->data['address'] = $this->url->link('account/address', '', 'SSL');
		$this->data['wishlist'] = $this->url->link('account/wishlist');
    	$this->data['order'] = $this->url->link('account/order', '', 'SSL');
    	$this->data['lbb'] = $this->url->link('account/lbb', '', 'SSL');
    	$this->data['returns'] = $this->url->link('account/returns', '', 'SSL');
    	$this->data['download'] = $this->url->link('account/download', '', 'SSL');
		$this->data['return'] = $this->url->link('account/return', '', 'SSL');
		$this->data['transaction'] = $this->url->link('account/transaction', '', 'SSL');
		$this->data['newsletter'] = $this->url->link('account/newsletter', '', 'SSL');
		$this->data['viewvouchers'] = $this->url->link('account/viewvouchers', '', 'SSL');
		
		if ($this->config->get('reward_status')) {
			$this->data['reward'] = $this->url->link('account/reward', '', 'SSL');
		} else {
			$this->data['reward'] = '';
		}
		
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/account.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/account.tpl';
		} else {
			$this->template = 'default/template/account/account.tpl';
		}
		
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'		
		);
		// $this->session->data['viewvouchers'] = 'child';
		// $this->session->data['vieworder'] = 'child';
		// $this->session->data['viewlbb'] = 'child';
		$this->session->data['vieworder'] = 'child';
		$this->data['customer_name'] = $this->customer->getFirstName();
		$this->data['customer_email'] = $this->customer->getEmail();
		$this->data['customer_business'] = $this->customer->getBusinessName();
		$this->data['telephone'] = $this->customer->getTelephone();
		// $this->data['orders'] = $this->getChild('account/order');
		// $this->data['vouchers'] = $this->getChild('account/viewvouchers');
		// $this->data['buyback'] = $this->getChild('account/lbb');
		// $this->data['template_returns'] = $this->getChild('account/returns');
		// $this->data['settings'] = $this->getChild('account/edit');
		// $this->data['communications'] = $this->getChild('account/newsletter');
		// $this->data['lists'] = $this->getChild('account/wishlist');

				$this->document->setTitle('Account Control Center');
		unset($this->session->data['viewvouchers']);
		unset($this->session->data['vieworder']);
		unset($this->session->data['viewlbb']);
		unset($this->session->data['vieworder']);
		$this->response->setOutput($this->render());
  	}

  	public function addNewList()
  	{
  		$this->load->model('catalog/product');
  		$data['name'] = $_POST['list_name'];
  		$data['customer_id'] = $this->customer->getId();
  		$this->model_catalog_product->saveProductList($data);
  		echo "true";
  	}

  	public function addProductTolist()
  	{
  		$this->load->model('catalog/product');
  		$data['product_id'] = $_POST['product_id'];
  		$data['list_id'] = $_POST['list_id'];
  		$this->model_catalog_product->addProductTolist($data);
  		echo "true";
  	}

  	public function getModule()
  	{
  		$type = $this->request->get['type'];
  		switch($type)
  		{
  			case 'Settings':
  			$html = $this->getChild('account/edit');
  			break;

  			case 'Dashboard':
  			$html = $this->getChild('account/dashboard');
  			break;

  			case 'Communication':
  			$html = $this->getChild('account/newsletter');
  			break;

  			case 'List':
  			$html = $this->getChild('account/wishlist');
  			break;

  			default:
  			$html='';
  			break;




  		}
  		echo $html;
  		exit;
  	}
  	
}
?>