<?php
//==============================================================================
// New Returns E-mail v155.2
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
//==============================================================================

class ControllerModuleNewReturnsEmail extends Controller {
	private $error = array();
	private $type = 'module';
	private $name = 'new_returns_email';
	
	public function index() {
		$this->data['type'] = $this->type;
		$this->data['name'] = $this->name;
		
		$this->data = array_merge($this->data, $this->load->language($this->type . '/' . $this->name));
		
		$this->data['token'] = $token = (isset($this->session->data['token'])) ? $this->session->data['token'] : '';
		$this->data['exit'] = $this->url->link('extension/' . $this->type, 'token=' . $token, 'SSL');
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
			$this->save(false);
			$this->session->data['success'] = $this->data['standard_success'];
			$this->redirect($this->data['exit']);
		}
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `group` = '" . $this->db->escape($this->name) . "' ORDER BY `key` ASC");
		foreach ($query->rows as $setting) {
			$value = isset($this->request->post[$setting['key']]) ? $this->request->post[$setting['key']] : $setting['value'];
			$this->data[$setting['key']] = (is_string($value) && strpos($value, 'a:') === 0) ? unserialize($value) : $value;
		}
		
		// non-standard
		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		// end
		
		$this->data['breadcrumbs'] = array();
		$this->data['breadcrumbs'][] = array(
			'href'		=> $this->url->link('common/home', 'token=' . $token, 'SSL'),
			'text'		=> $this->data['text_home'],
			'separator' => false
		);
		$this->data['breadcrumbs'][] = array(
			'href'		=> $this->url->link('extension/' . $this->type, 'token=' . $token, 'SSL'),
			'text'		=> $this->data['standard_' . $this->type],
			'separator' => ' :: '
		);
		$this->data['breadcrumbs'][] = array(
			'href'		=> $this->url->link($this->type . '/' . $this->name, 'token=' . $token, 'SSL'),
			'text'		=> $this->data['heading_title'],
			'separator' => ' :: '
		);
		
		$this->data['error_warning'] = isset($this->error['warning']) ? $this->error['warning'] : '';
		$this->data['success'] = (isset($this->session->data['success'])) ? $this->session->data['success'] : '';
		unset($this->session->data['success']);
		
		$this->document->setTitle($this->data['heading_title']);
		$this->template = $this->type . '/' . $this->name . '.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}
	
	public function save($ajax = true) {
		if ($this->validate()) {
			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting($this->name, $this->request->post);
			if ($ajax) echo 'success';
		}
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', $this->type . '/' . $this->name)) {
			$this->error['warning'] = $this->data['standard_error'];
		}
		return ($this->error) ? false : true;
	}
}
?>