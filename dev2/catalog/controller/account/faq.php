<?php 
class ControllerAccountFaq extends Controller { 
	private $error = array();
	
	public function index() {
    	
 if ($this->customer->isLogged()) {
      		$this->data['customer_id'] = $this->customer->getId();
			$this->data['firstname'] = $this->customer->getFirstName();
			$this->data['lastname'] = $this->customer->getLastName();
			$this->data['email'] = $this->customer->getEmail();

	  		
    	}
		else
		{
			$this->data['customer_id'] = 0;
			$this->data['firstname'] = '';
			$this->data['lastname'] = '';
			$this->data['email'] = '';
			
		}
		if($this->session->data['success_save'])
		{
			$this->data['success'] = $this->session->data['success_save'];	
			unset($this->session->data['success_save']);	
			
		}
		
		if($this->session->data['error_save'])
		{
			$this->data['error'] = $this->session->data['error_save'];	
			unset($this->session->data['error_save']);	
			
		}
		
   $this->document->addStyle('http://fonts.googleapis.com/css?family=Ubuntu:400,300,500,700');

    	$this->document->setTitle('Frequently Asked Questions');
			
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
			'href'      => $this->url->link('account/faq', $url, 'SSL'),        	
        	'separator' => $this->language->get('text_separator')
      	);

		$this->data['heading_title'] = 'Frequently Asked Questions';
		
		
		
		$this->load->model('account/faq');
		
	
		$this->data['faqs'] = array();
		
		$this->data['action'] = $this->url->link('account/faq/insert', '', 'SSL');
		
		$results = $this->model_account_faq->getFaqs();
		
		$this->data['faqs'] = $results;
		
		$categories = $this->model_account_faq->getFaqCategories();

		$this->data['categories'] = $categories;
		
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/faq.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/account/faq.tpl';
		} else {
			$this->template = 'default/template/account/faq.tpl';
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
	
		public function insert()
		{
			$this->load->model('account/faq');
			if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) 
			{
					$this->model_account_faq->saveFaq($this->request->post);
				
				//$this->session->data['redirect'] = $this->url->link('account/return', '', 'SSL');

	  		$this->session->data['success_save'] = 'Your Question has been sucessfully submitted. PhonePartsUSA will get back to you soon.';
			}
			else
			{
				
				$this->session->data['error_save'] = 'Something went wrong, please try again or contact support.';
			}
			$this->redirect($this->url->link('account/faq', '', 'SSL'));
			
		}
		private function validate()
		{
			
			if ((utf8_strlen($this->request->post['firstname']) < 1) || (utf8_strlen($this->request->post['firstname']) > 32)) {
      			$this->error['firstname'] = 'Please provide Firstname';
    	}
		if ((utf8_strlen($this->request->post['lastname']) < 1) || (utf8_strlen($this->request->post['lastname']) > 32)) {
      			$this->error['lastname'] = 'Please provide Lastname';
    	}
		if ((utf8_strlen($this->request->post['email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['email'])) {
      		$this->error['email'] = 'Invalid email address';
    	}
			if (!$this->error) {
      		return true;
    	} else {
      		return false;
    	}
			
		}
		
		
	}
?>
