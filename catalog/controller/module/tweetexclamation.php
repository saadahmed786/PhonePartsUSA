<?php  
class ControllerModuleTweetexclamation extends Controller {
	static $config_id;
	public function twitter() {
		$config=array();
		$config['ckey'] = $this->config->get('tweetexclamation_ckey');
		$config['csecret'] = $this->config->get('tweetexclamation_csecret');
		$config['atoken'] = $this->config->get('tweetexclamation_atoken');
		$config['asecret'] = $this->config->get('tweetexclamation_asecret');
		$config['dir_lib']=DIR_APPLICATION.'view/theme/default/tweetexclamation/twitter/';
		$config['dir_cache']=DIR_SYSTEM.'cache/';
		ob_start();
		include DIR_APPLICATION.'view/theme/default/tweetexclamation/twitter/index.php';
		$output=ob_get_clean();
		$this->response->setOutput($output);		
	}
	protected function index($settings) {
		$this->language->load('module/tweetexclamation');
		$this->config_id++;

		//<script type="text/javascript" src="catalog/view/theme/default/tweetexclamation/twitter/jquery.tweet.min.js"></script>
		$this->document->addScript('catalog/view/theme/default/tweetexclamation/twitter/jquery.tweet.min.js');
		//<link rel="stylesheet" type="text/css" href="catalog/view/theme/default/tweetexclamation/jquery.tweet.css" />
		$this->document->addStyle('catalog/view/theme/default/tweetexclamation/jquery.tweet.css');

		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['text_loading'] = $this->language->get('text_loading');
		$this->data['text_join_text_default'] = $this->language->get('text_join_text_default');
		$this->data['text_join_text_ed'] = $this->language->get('text_join_text_ed');
		$this->data['text_join_text_ing'] = $this->language->get('text_join_text_ing');
		$this->data['text_join_text_reply'] = $this->language->get('text_join_text_reply');
		$this->data['text_join_text_url'] = $this->language->get('text_join_text_url');
			
		//$this->data['tweetexclamation_username'] = html_entity_decode($this->config->get('tweetexclamation_username'), ENT_QUOTES, 'UTF-8');
		$this->data['tweetexclamation_id'] = $this->config_id;
		$this->data['tweetexclamation_title'] = html_entity_decode($settings['config_title'], ENT_QUOTES, 'UTF-8');
		$this->data['tweetexclamation_username'] = html_entity_decode($settings['config_username'], ENT_QUOTES, 'UTF-8');
		$this->data['tweetexclamation_count'] = html_entity_decode($settings['config_count'], ENT_QUOTES, 'UTF-8');
		$this->data['tweetexclamation_avatar_size'] = html_entity_decode($settings['config_avatar_size'], ENT_QUOTES, 'UTF-8');
		$this->data['tweetexclamation_template'] = html_entity_decode($settings['config_template'], ENT_QUOTES, 'UTF-8');
		$this->data['tweetexclamation_readmore'] = html_entity_decode($settings['config_readmore'], ENT_QUOTES, 'UTF-8');

		$this->data['tweetexclamation_modpath'] = $this->url->link('module/tweetexclamation/twitter');

/*
		//set defaults
		if (empty($this->data['tweetexclamation_count']))
			$this->data['tweetexclamation_count']=3;
		$this->data['tweetexclamation_avatar_size'] = html_entity_decode($settings['config_avatar_size'], ENT_QUOTES, 'UTF-8');
		config_avatar_sizeif (empty($this->data['avatar_size']))
			$this->data['avatar_size']=32;
		$this->data['tweetexclamation_template'] = html_entity_decode($settings['config_avatar_size'], ENT_QUOTES, 'UTF-8');
		if (empty($this->data['tweetexclamation_template']))
			$this->data['tweetexclamation_template'] = "{avatar}{text}<br>{time}";
*/
		
		//$this->id = 'tweetexclamation';

		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/tweetexclamation.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/tweetexclamation.tpl';
		} else {
			$this->template = 'default/template/module/tweetexclamation.tpl';
		}
		
		$this->render();
	}
}
