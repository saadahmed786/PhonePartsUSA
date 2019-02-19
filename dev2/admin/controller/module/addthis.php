<?php
class ControllerModuleAddthis extends Controller {
	private $error = array(); 
	
	public function index() {   
		$this->load->language('module/addthis');

		$this->document->title = $this->language->get('heading_title');
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->model_setting_setting->editSetting('addthis', $this->request->post);		
					
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->redirect(HTTPS_SERVER . 'index.php?route=extension/module');
		}
				
		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['heading_follow'] = $this->language->get('heading_follow');		

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_left'] = $this->language->get('text_left');
		$this->data['text_right'] = $this->language->get('text_right');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		
		$this->data['entry_username'] = $this->language->get('entry_username');		
		$this->data['entry_twitter_username'] = $this->language->get('entry_twitter_username');
		$this->data['entry_facebook_username'] = $this->language->get('entry_facebook_username');							
		$this->data['entry_position'] = $this->language->get('entry_position');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		$this->data['tab_general'] = $this->language->get('tab_general');

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

  		$this->document->breadcrumbs = array();

   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=common/home',
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);

   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=extension/module',
       		'text'      => $this->language->get('text_module'),
      		'separator' => ' :: '
   		);
		
   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=module/addthis',
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = HTTPS_SERVER . 'index.php?route=module/addthis';
		
		$this->data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/module';
		
		if (isset($this->request->post['addthis_username'])) {
			$this->data['addthis_username'] = $this->request->post['addthis_username'];
		} else {
			$this->data['addthis_username'] = $this->config->get('addthis_username');
		}	
		if (isset($this->request->post['addthis_twitter_username'])) {
			$this->data['addthis_twitter_username'] = $this->request->post['addthis_twitter_username'];
		} else {
			$this->data['addthis_twitter_username'] = $this->config->get('addthis_twitter_username');
		}	
		if (isset($this->request->post['addthis_facebook_username'])) {
			$this->data['addthis_facebook_username'] = $this->request->post['addthis_facebook_username'];
		} else {
			$this->data['addthis_facebook_username'] = $this->config->get('addthis_facebook_username');
		}			
		if (isset($this->request->post['addthis_position'])) {
			$this->data['addthis_position'] = $this->request->post['addthis_position'];
		} else {
			$this->data['addthis_position'] = $this->config->get('addthis_position');
		}
		
		if (isset($this->request->post['addthis_status'])) {
			$this->data['addthis_status'] = $this->request->post['addthis_status'];
		} else {
			$this->data['addthis_status'] = $this->config->get('addthis_status');
		}
		
		if (isset($this->request->post['addthis_sort_order'])) {
			$this->data['addthis_sort_order'] = $this->request->post['addthis_sort_order'];
		} else {
			$this->data['addthis_sort_order'] = $this->config->get('addthis_sort_order');
		}				
		
		$this->template = 'module/addthis.tpl';
		$this->children = array(
			'common/header',	
			'common/footer'	
		);
		
		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/addthis')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
}
?>