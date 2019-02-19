<?php 
class ControllerModuleBosslogin extends Controller {
	public function index() {
		$this->language->load('module/boss_login');
				
		$this->data['text_welcome_1'] = $this->language->get('text_welcome_1');
		$this->data['text_login'] = sprintf($this->language->get('text_login'), $this->url->link('account/login', '', 'SSL'));
		$this->data['text_welcome_2'] = sprintf($this->language->get('text_welcome_2') , $this->url->link('account/register', '', 'SSL'));
		$this->data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', 'SSL'), $this->customer->getFirstName(), $this->url->link('account/logout', '', 'SSL'));

		$this->data['text_forgotten'] 		= $this->language->get('text_forgotten');	
		$this->data['text_username'] 		= $this->language->get('text_username');	
		$this->data['entry_password'] 		= $this->language->get('entry_password');	
		$this->data['logged'] = $this->customer->isLogged();
		$this->data['action_login'] = $this->url->link('account/login', '', 'SSL');	
		$this->data['forgotten'] =  $this->url->link('account/forgotten');
		$this->data['button_login'] =  $this->language->get('button_login');
		
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/boss_login.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/boss_login.tpl';
		} else {
			$this->template = 'default/template/module/boss_login.tpl';
		}
		
		$this->response->setOutput($this->render());		
	}
}
?>