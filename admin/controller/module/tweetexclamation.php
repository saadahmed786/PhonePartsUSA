<?php
class ControllerModuleTweetExclamation extends Controller {
	private $error = array(); 
	
	public function index() {   
		$this->load->language('module/tweetexclamation');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->model_setting_setting->editSetting('tweetexclamation', $this->request->post);		
					
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}
				
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_content_top'] = $this->language->get('text_content_top');
		$this->data['text_content_bottom'] = $this->language->get('text_content_bottom');		
		$this->data['text_column_left'] = $this->language->get('text_column_left');
		$this->data['text_column_right'] = $this->language->get('text_column_right');
		$this->data['text_instructions'] = $this->language->get('text_instructions');

		$this->data['entry_username'] = $this->language->get('entry_username');
		$this->data['entry_config'] = $this->language->get('entry_config');
		$this->data['entry_config_title'] = $this->language->get('entry_config_title');
		$this->data['entry_config_title_default'] = $this->language->get('entry_config_title_default');
		$this->data['entry_config_username'] = $this->language->get('entry_config_username');
		$this->data['entry_config_count'] = $this->language->get('entry_config_count');
		$this->data['entry_config_count_default'] = $this->language->get('entry_config_count_default');
		$this->data['entry_config_avatar_size'] = $this->language->get('entry_config_avatar_size');
		$this->data['entry_config_avatar_size_default'] = $this->language->get('entry_config_avatar_size_default');
		$this->data['entry_config_template'] = $this->language->get('entry_config_template');
		$this->data['entry_config_template_default'] = $this->language->get('entry_config_template_default');
		$this->data['entry_config_readmore'] = $this->language->get('entry_config_readmore');
		$this->data['entry_config_readmore_default'] = $this->language->get('entry_config_readmore_default');
		$this->data['entry_config_ckey'] = $this->language->get('entry_config_ckey');
		$this->data['entry_config_csecret'] = $this->language->get('entry_config_csecret');
		$this->data['entry_config_atoken'] = $this->language->get('entry_config_atoken');
		$this->data['entry_config_asecret'] = $this->language->get('entry_config_asecret');
		$this->data['entry_layout'] = $this->language->get('entry_layout');
		$this->data['entry_position'] = $this->language->get('entry_position');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$this->data['entry_skrip'] = $this->language->get('entry_skrip');
        
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_add_module'] = $this->language->get('button_add_module');
		$this->data['button_remove'] = $this->language->get('button_remove');

		//$this->data['tab_module'] = $this->language->get('tab_module');
		
		$this->data['token'] = $this->session->data['token'];

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
 		if (isset($this->error['modules'])) {
			$this->data['error_modules'] = $this->error['modules'];
		} else {
			$this->data['error_modules'] = array();
		}
		
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/tweetexclamation', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/tweetexclamation', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
/*
		if (isset($this->request->post['tweetexclamation_username'])) {
			$this->data['tweetexclamation_username'] = $this->request->post['tweetexclamation_username'];
		} else {
			$this->data['tweetexclamation_username'] = $this->config->get('tweetexclamation_username');
		}
*/
		
		if (isset($this->request->post['tweetexclamation_ckey'])) {
			$this->data['tweetexclamation_ckey'] = $this->request->post['tweetexclamation_ckey'];
		} else {
			$this->data['tweetexclamation_ckey'] = $this->config->get('tweetexclamation_ckey');
		}	
		if (isset($this->request->post['tweetexclamation_csecret'])) {
			$this->data['tweetexclamation_csecret'] = $this->request->post['tweetexclamation_csecret'];
		} else {
			$this->data['tweetexclamation_csecret'] = $this->config->get('tweetexclamation_csecret');
		}	
		if (isset($this->request->post['tweetexclamation_atoken'])) {
			$this->data['tweetexclamation_atoken'] = $this->request->post['tweetexclamation_atoken'];
		} else {
			$this->data['tweetexclamation_atoken'] = $this->config->get('tweetexclamation_atoken');
		}	
		if (isset($this->request->post['tweetexclamation_asecret'])) {
			$this->data['tweetexclamation_asecret'] = $this->request->post['tweetexclamation_asecret'];
		} else {
			$this->data['tweetexclamation_asecret'] = $this->config->get('tweetexclamation_asecret');
		}	

		$this->data['modules'] = array();
		
		if (isset($this->request->post['tweetexclamation_module'])) {
			$this->data['modules'] = $this->request->post['tweetexclamation_module'];
		} elseif ($this->config->get('tweetexclamation_module')) { 
			$this->data['modules'] = $this->config->get('tweetexclamation_module');
		}	
				
		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
		
		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		$this->template = 'module/tweetexclamation.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/tweetexclamation')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
/*
		if (!$this->request->post['tweetexclamation_username']) {
			$this->error['username'] = $this->language->get('error_code_username');
		}
*/
		$modules_error=array();
		//print_r($this->request->post['tweetexclamation_module']);
		if (isset($this->request->post['tweetexclamation_module'])) {
			foreach($this->request->post['tweetexclamation_module'] as $index=>$module){

				if (!$module['config_username'])
					$modules_error[$index]['config_username']=$this->language->get('error_code_username');
			}

			$this->data['modules'] = $this->request->post['tweetexclamation_module'];
		}
		if($modules_error){
			$this->error['modules']=$modules_error;
		}
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}
}
