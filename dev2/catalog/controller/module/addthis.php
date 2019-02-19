<?php  
class ControllerModuleAddthis extends Controller {
	protected function index() {
		$this->language->load('module/addthis');
		
    $this->data['heading_title'] = $this->language->get('heading_title');
    $this->data['text_follow_us'] = $this->language->get('text_follow_us');		
 		
		$this->data['addthis_username'] = $this->config->get('addthis_username');
		$this->data['addthis_twitter_username'] = $this->config->get('addthis_twitter_username');
		$this->data['addthis_facebook_username'] = $this->config->get('addthis_facebook_username');		
				
		$this->id       = 'addthis';
		
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/addthis.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/addthis.tpl';
		} else {
			$this->template = 'default/template/module/addthis.tpl';
		}
				
		$this->render();
	}
}
?>