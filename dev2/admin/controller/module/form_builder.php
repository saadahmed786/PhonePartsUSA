<?php
//==============================================================================
// Flexible Form v154.2
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
//==============================================================================

class ControllerModuleFormBuilder extends Controller {
	private $type = 'module';
	private $name = 'form_builder';
	
	//------------------------------------------------------------------------------
	// Form List
	//------------------------------------------------------------------------------
	public function index() {
		$this->loadtemplate('list');
		
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "form` (
			`form_id` int(11) NOT NULL AUTO_INCREMENT,
			`status` tinyint(1) NOT NULL DEFAULT '1',
			`name` text COLLATE utf8_bin NOT NULL,
			`password` mediumtext COLLATE utf8_bin NOT NULL,
			`fields` mediumtext COLLATE utf8_bin NOT NULL,
			`errors` mediumtext COLLATE utf8_bin NOT NULL,
			`email` mediumtext COLLATE utf8_bin NOT NULL,
			PRIMARY KEY (`form_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin
		");
		$this->db->query("
			CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "form_response` (
			`form_response_id` int(11) NOT NULL AUTO_INCREMENT,
			`form_id` int(11) NOT NULL,
			`answered` tinyint(1) NOT NULL DEFAULT '1',
			`date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			`customer_id` int(11) NOT NULL DEFAULT '0',
			`ip` varchar(40) COLLATE utf8_bin NOT NULL,
			`response` mediumtext COLLATE utf8_bin NOT NULL,
			PRIMARY KEY (`form_response_id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin
		");
		file_put_contents(DIR_LOGS.'clearthinking.txt',date('Y-m-d H:i:s')."\t".$this->request->server['REMOTE_ADDR']."\t".$this->name."\n",FILE_APPEND|LOCK_EX);
		$setting_table = $this->db->query("SHOW COLUMNS FROM " . DB_PREFIX . "setting WHERE Field = 'value'");
		if (strtolower($setting_table->row['Type']) == 'text') {
			$this->db->query("ALTER TABLE " . DB_PREFIX . "setting MODIFY `value` MEDIUMTEXT NOT NULL");
		}
		
		$forms = $this->db->query("SELECT * FROM " . DB_PREFIX . "form");
		$this->data['forms'] = array();
		foreach ($forms->rows as $form) {
			$name = unserialize($form['name']);
			$this->data['forms'][$name[$this->config->get('config_admin_language')]] = $form;
		}
		ksort($this->data['forms']);
		
		$this->response->setOutput($this->render());
	}
	
	//------------------------------------------------------------------------------
	// Form Edit
	//------------------------------------------------------------------------------
	public function edit() {
		$this->loadtemplate('form');
		
		$stores = $this->db->query("SELECT * FROM " . DB_PREFIX . "store ORDER BY name");
		$this->data['stores'] = $stores->rows;
		array_unshift($this->data['stores'], array('store_id' => 0, 'name' => $this->config->get('config_name')));
		
		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
		$this->load->model('design/layout');
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
		
		$this->data['positions'] = array(
			'content_top',
			'content_bottom',
			'column_left',
			'column_right'
		);
		
		if (empty($this->request->get['form_id'])) {
			$this->data['form'] = array('form_id' => 0);
		} else {
			$this->data['form'] = $this->getForm($this->request->get['form_id']);
			$this->data['heading_title'] = $this->data['form']['name'][$this->config->get('config_admin_language')];
		}
		$this->data['modules'] = ($this->config->get($this->name . '_module')) ? $this->config->get($this->name . '_module') : array(array('form_id' => 0));
		
		$this->document->addScript('view/javascript/jquery/ui/external/jquery.cookie.js');
		$this->document->addScript('view/javascript/jquery/ui/jquery-ui-timepicker-addon.js');
		$this->document->addScript('view/javascript/ckeditor/ckeditor.js');
		$this->response->setOutput($this->render());
	}
	
	public function save() {
		if ($this->user->hasPermission('modify', $this->type . '/' . $this->name)) {
			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting($this->name, array($this->name . '_module' => $this->request->post['module']));
			
			$this->db->query(
				(empty($this->request->post['form_id']) ? "INSERT INTO " : "UPDATE ") . DB_PREFIX . "form SET
				status = " . (int)$this->request->post['status'] . ",
				name = '" . $this->db->escape(serialize($this->request->post['name'])) . "',
				password = '" . $this->db->escape(serialize($this->request->post['password'])) . "',
				fields = '" . $this->db->escape(serialize(isset($this->request->post['fields']) ? $this->request->post['fields'] : array())) . "',
				errors = '" . $this->db->escape(serialize($this->request->post['errors'])) . "',
				email = '" . $this->db->escape(serialize($this->request->post['email'])) . "'
				" . (empty($this->request->post['form_id']) ? "" : "WHERE form_id = " . (int)$this->request->post['form_id'])
			);
			
			echo (empty($this->request->post['form_id'])) ? $this->db->getLastId() : $this->request->post['form_id'];
		}
	}
	
	public function createFormPage() {
		$response = '';
		
		if ($this->user->hasPermission('modify', $this->type . '/' . $this->name) &&
			$this->user->hasPermission('modify', 'catalog/information') &&
			$this->user->hasPermission('modify', 'design/layout')
		) {
			$layout_name = 'Form Layout: ' . $this->request->post['name'][0]['value'];
			$this->load->model('design/layout');
			$this->model_design_layout->addLayout(array('name' => $layout_name));
			$layout_id = $this->db->getLastId();
			
			$languages = array();
			$language_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "language ORDER BY sort_order, name");
			foreach ($language_query->rows as $language) {
				$languages[$language['code']] = $language['language_id'];
			}
			
			$info_description = array();
			foreach ($this->request->post['name'] as $name) {
				$code = substr($name['name'], -3, 2);
				$info_description[$languages[$code]] = array(
					'title'			=> $name['value'],
					'description'	=> ''
				);
			}
			
			$info_store = array(0);
			$info_layout = array(0 => array('layout_id' => $layout_id));
			$this->load->model('setting/store');
			$stores = $this->model_setting_store->getStores();
			foreach ($stores as $store) {
				$info_store[] = $store['store_id'];
				$info_layout[$store['store_id']] = array('layout_id' => $layout_id);
			}
			
			$data = array(
				'sort_order'				=> 1,
				'bottom'					=> $this->request->post['bottom'],
				'status'					=> 1,
				'information_description'	=> $info_description,
				'information_store'			=> $info_store,
				'information_layout'		=> $info_layout,
				'keyword'					=> $this->request->post['keyword']
			);
			$this->load->model('catalog/information');
			$this->model_catalog_information->addInformation($data);
			
			$response = array(
				'layout_id'		=> $layout_id,
				'layout_name'	=> $layout_name
			);
		}
		
		echo json_encode($response);
	}
	
	//------------------------------------------------------------------------------
	// Form Report
	//------------------------------------------------------------------------------
	public function report() {
		$this->loadtemplate('report');
		
		if (empty($this->request->get['form_id'])) {
			$this->redirect($this->url->link($this->type . '/' . $this->name, '', 'SSL'));
		} else {
			$this->data['form'] = $this->getForm($this->request->get['form_id']);
			
			foreach ($this->data['form']['fields'] as $field) {
				if (!isset($field['type']) || !isset($field['key'])) continue;
				$this->data[$field['type'] . 's'][] = $field['key'];
			}
			
			$page = (!empty($this->request->get['page'])) ? $this->request->get['page'] : 1;
			$this->data['responses'] = $this->getFormResponses($this->request->get['form_id'], $page);
			
			$all_responses = $this->getFormResponses($this->request->get['form_id']);
			$this->data['summary'] = array();
			foreach ($all_responses as $response) {
				foreach ($response['response'] as $key => $value) {
					if (!isset($this->data['summary'][$key])) $this->data['summary'][$key] = array();
					if (is_array($value)) {
						foreach($value as $answer) {
							if (!isset($this->data['summary'][$key][$answer])) $this->data['summary'][$key][$answer] = 0;
							$this->data['summary'][$key][$answer]++;
						}
					} else {
						if (in_array($key, $this->data['files']) && empty($value)) continue;
						if (in_array($key, $this->data['files'])) $value = $this->data['text_number_of_uploads'];
						if (!isset($this->data['summary'][$key][$value])) $this->data['summary'][$key][$value] = 0;
						$this->data['summary'][$key][$value]++;
					}
				}
			}
		}
		$this->load->model('sale/customer');
		
		$pagination = new Pagination();
		$pagination->total = count($all_responses);
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link($this->type . '/' . $this->name . '/report', '&form_id=' . $this->request->get['form_id'] . '&page={page}' . '&token=' . $this->data['token'], 'SSL');
		$this->data['pagination'] = $pagination->render();
		
		$this->data['heading_title'] = $this->data['form']['name'][$this->config->get('config_admin_language')];
		$this->document->addScript('view/javascript/jquery/ui/external/jquery.cookie.js');
		$this->response->setOutput($this->render());
	}
	
	public function download() {
		if (empty($this->request->get['filename'])) return;
		$file = DIR_DOWNLOAD . $this->request->get['filename'];
		header('Content-Type: application/octet-stream');
		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename="' . pathinfo($file, PATHINFO_FILENAME) . '"');
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . filesize($file));
		readfile($file, 'rb');
	}
	
	//------------------------------------------------------------------------------
	// Private General Use Functions
	//------------------------------------------------------------------------------
	private function getForm($form_id) {
		$form_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "form WHERE form_id = " . (int)$form_id);
		$form = $form_query->row;
		foreach ($form as &$data) if (is_string($data) && strpos($data, 'a:') === 0) $data = unserialize($data);
		return $form;
	}
	
	private function getFormResponses($form_id, $page = 0) {
		$form_response_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "form_response WHERE form_id = " . (int)$form_id . ($page ? " ORDER BY date_added DESC LIMIT " . ($page-1)*$this->config->get('config_admin_limit') . "," . $this->config->get('config_admin_limit') : ""));
		$form_responses = $form_response_query->rows;
		foreach ($form_responses as &$data) $data['response'] = unserialize($data['response']);
		return $form_responses;
	}
	
	private function loadtemplate($template = 'list') {
		$this->data['type'] = $this->type;
		$this->data['name'] = $this->name;
		$this->data['token'] = $token = (isset($this->session->data['token'])) ? $this->session->data['token'] : '';
		$this->data['exit'] = $this->url->link($this->type . '/' . $this->name, 'token=' . $token, 'SSL');
		
		$this->data = array_merge($this->data, $this->load->language($this->type . '/' . $this->name));
		
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
		
		$this->template = $this->type . '/' . $this->name . '_' . $template . '.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->document->setTitle($this->data['heading_title']);
	}
	
	//------------------------------------------------------------------------------
	// Public General Use Functions
	//------------------------------------------------------------------------------
	public function toggleEntry() {
		$response = '';
		if ($this->user->hasPermission('modify', $this->type . '/' . $this->name)) {
			if ($this->request->post['table'] == 'form') {
				$this->db->query("UPDATE " . DB_PREFIX . "form SET status = " . (int)$this->request->post['new_value'] . " WHERE form_id = " . (int)$this->request->post['id']);
				$response = 'success';
			} elseif ($this->request->post['table'] == 'form_response') {
				$this->db->query("UPDATE " . DB_PREFIX . "form_response SET answered = " . (int)$this->request->post['new_value'] . " WHERE form_response_id = " . (int)$this->request->post['id']);
				$response = 'success';
			}
		}
		echo $response;
	}
	
	public function copyRow() {
		$response = '';
		if ($this->user->hasPermission('modify', $this->type . '/' . $this->name)) {
			if ($this->request->post['table'] == 'form') {
				$form_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "form WHERE form_id = " . (int)$this->request->post['id']);
				$this->db->query("
					INSERT INTO " . DB_PREFIX . "form SET
					status = 0,
					name = '" . $this->db->escape($form_query->row['name']) . "',
					password = '" . $this->db->escape($form_query->row['password']) . "',
					fields = '" . $this->db->escape($form_query->row['fields']) . "',
					errors = '" . $this->db->escape($form_query->row['errors']) . "',
					email = '" . $this->db->escape($form_query->row['email']) . "'
				");
				$response = 'success';
			}
		}
		echo $response;
	}
	
	public function deleteRow() {
		$response = '';
		if ($this->user->hasPermission('modify', $this->type . '/' . $this->name)) {
			if ($this->request->post['table'] == 'form' || $this->request->post['table'] == 'form_response') {
				$this->db->query("DELETE FROM " . DB_PREFIX . $this->request->post['table'] . " WHERE " . $this->request->post['table'] . "_id = " . (int)$this->request->post['id']);
				$response = 'success';
			}
		}
		echo $response;
	}
	
	public function exportCSV() {
		// Not yet finished
		if ($this->user->hasPermission('access', $this->type . '/' . $this->name)) {
			$form_responses = $this->getFormResponses($this->request->get['form_id']);
			
			header('Pragma: public');
			header('Expires: 0');
			header('Content-Description: File Transfer');
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=' . $form['name'][$this->config->get('config_admin_language')] . '_' . date('Y-m-d_H-i-s', time()) . '.csv');
			header('Content-Transfer-Encoding: binary');
			
			echo '"answered","customer_id","date_added","ip"';
			$keys = array();
			foreach ($form_responses as $response) {
				
			}
			echo implode('","', array_keys($response)) . "\n";
			foreach ($form_responses as $response) {
				echo '"' . implode('","', str_replace('"', "''", $result)) . '"' . "\n";
			}
			
			exit();
		}
	}
}
?>