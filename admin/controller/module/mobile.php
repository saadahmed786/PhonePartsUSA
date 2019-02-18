<?php

class ControllerModuleMobile extends Controller {
	
	private $error = array();

	public function index() {
		
		$this->load->language('module/mobile');

		$this->document->title = $this->language->get('heading_title');
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->model_setting_setting->editSetting('mobile', $this->request->post);		
					
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->redirect(HTTPS_SERVER . 'index.php?route=extension/module&token=' . $this->session->data['token']);
		}
				
		$this->data['heading_title'] = $this->language->get('heading_title');

		// common texts
		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_true'] = $this->language->get('text_true');
		$this->data['text_false'] = $this->language->get('text_false');
		
		// labels
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['autodetect'] = $this->language->get('autodetect'); 
		$this->data['generate_link'] = $this->language->get('generate_link'); 
		$this->data['template_name'] = $this->language->get('template_name'); 
		
		// buttons
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		$this->data['tab_general'] = $this->language->get('tab_general');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		
 		if (isset($this->error['template'])) {
			$this->data['error_template'] = $this->error['template'];
		} else {
			$this->data['error_template'] = '';
		}
		
  		$this->document->breadcrumbs = array();

   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=common/home&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);

   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=extension/module&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('text_module'),
      		'separator' => ' :: '
   		);
		
   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=module/mobile&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = HTTPS_SERVER . 'index.php?route=module/mobile&token=' . $this->session->data['token'];
		$this->data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/module&token=' . $this->session->data['token'];

		if (isset($this->request->post['autodetect'])) {
			$this->data['mobile_autodetect'] = $this->request->post['autodetect'];
		} else {
			$this->data['mobile_autodetect'] = $this->config->get('autodetect');
		}
		
		if (isset($this->request->post['generate_link'])) {
			$this->data['mobile_generate_link'] = $this->request->post['generate_link'];
		} else {
			$this->data['mobile_generate_link'] = $this->config->get('generate_link');
		}
	
		if (isset($this->request->post['mobile_status'])) {
			$this->data['mobile_status'] = $this->request->post['mobile_status'];
		} else {
			$this->data['mobile_status'] = $this->config->get('mobile_status');
		}		

		if (isset($this->request->post['mobile_template_name'])) {
			$this->data['mobile_template_name'] = $this->request->post['mobile_template_name'];
		} else {
			$this->data['mobile_template_name'] = $this->config->get('mobile_template_name');
		}			
		
		$this->id       = 'mobile';
		$this->template = 'module/mobile.tpl';
		$this->children = array(
			'common/header',	
			'common/footer'	
		);
		
		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
	}

	private function validate() {
		
		if (!$this->user->hasPermission('modify', 'module/mobile')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->request->post['mobile_template_name'] || $this->request->post['mobile_template_name'] == '') {
			$this->error['template'] = $this->language->get('error_template_name');
			
		} else {
			if (function_exists('is_dir') && !is_dir(DIR_CATALOG .'view/theme/'. $this->request->post['mobile_template_name']))
				$this->error['template'] = $this->language->get('error_template_name');
		}
		
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
?>