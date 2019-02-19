<?php  
class ControllerModuleBossAlphabet extends Controller {
	protected function index() {
		if (file_exists('catalog/view/theme/' . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/stylesheet/boss_alphabet.css')) {
			$this->document->addStyle('catalog/view/theme/' . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/stylesheet/boss_alphabet.css');
		} else {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/boss_alphabet.css');
		}
		
		$this->language->load('module/boss_alphabet');
		
    	$this->data['heading_title'] = $this->language->get('heading_title');
		
		$alphabet = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z");
		foreach($alphabet as $char)
		{
			$this->data['alphabet'][] = array(
				'char' 		=> $char,
				'href'      => $this->url->link('bossthemes/product_by_alphabet', 'c=' . $char)
			);
		}
		$this->data['alphabet'][] = array(
			'char'		=> "#",
			'href'     	=> $this->url->link('bossthemes/product_by_alphabet', 'c=0')
		);

		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/boss_alphabet.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/boss_alphabet.tpl';
		} else {
			$this->template = 'default/template/module/boss_alphabet.tpl';
		}
		
		$this->render();
  	}
}
?>