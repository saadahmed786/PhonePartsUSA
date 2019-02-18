<?php   
class ControllerModuleBossStaticblock extends Controller {
	protected function index($setting) {
		
		$store_id = $this->config->get('config_store_id');
		if (isset($setting['store_id']) && in_array($store_id, $setting['store_id'])) {
			$this->data['content'] = html_entity_decode($setting['description'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8');

			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/boss_staticblock.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/boss_staticblock.tpl';
			} else {
				$this->template = 'default/template/module/boss_staticblock.tpl';
			}
			if (!(($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) == 'ppu2') {
				$this->render();
			}
		}

	}
}
?>