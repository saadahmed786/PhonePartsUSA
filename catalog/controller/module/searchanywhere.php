<?php 
class ControllerModuleSearchAnywhere extends Controller { 	
	public function index($setting) { 
		$this->language->load('module/searchanywhere');
		
    	$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['setting'] = $setting; 
		
		$this->data['text_content_explain'] = $this->language->get('text_content_explain');
		$this->data['text_sidebar_explain'] = $this->language->get('text_sidebar_explain');
		$this->data['button_search'] = $this->language->get('button_search');
		
		
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/searchanywhere.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/searchanywhere.tpl';
		} else {
			$this->template = 'default/template/module/searchanywhere.tpl';
		}

		$this->render();
	}
}
?>