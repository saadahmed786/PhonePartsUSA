<?php
class ControllerModuleShipStation extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('module/shipstation');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->document->addStyle('view/stylesheet/shipstation.css');

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->uninstall(false);

			$this->install(false);

			$this->model_setting_setting->editSetting('shipstation', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['heading_general'] = $this->language->get('heading_general');
		$this->data['heading_export'] = $this->language->get('heading_export');
		$this->data['heading_update'] = $this->language->get('heading_update');
		$this->data['heading_error'] = $this->language->get('heading_error');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_select'] = $this->language->get('text_select');
		$this->data['text_confirm'] = $this->language->get('text_confirm');
		$this->data['text_not_enabled'] = $this->language->get('text_not_enabled');

		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_config_key'] = $this->language->get('entry_config_key');
		$this->data['entry_config_ver_key'] = $this->language->get('entry_config_ver_key');
		$this->data['entry_start_date'] = $this->language->get('entry_start_date');
		$this->data['entry_end_date'] = $this->language->get('entry_end_date');

		$this->data['tab_general'] = $this->language->get('tab_general');
		$this->data['tab_export'] = $this->language->get('tab_export');
		$this->data['tab_update'] = $this->language->get('tab_update');
		$this->data['tab_error'] = $this->language->get('tab_error');

		$this->data['button_keygen'] = $this->language->get('button_keygen');
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_export'] = $this->language->get('button_export');
		$this->data['button_clear'] = $this->language->get('button_clear');

		$this->data['token'] = $this->session->data['token'];

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		if (isset($this->error['warning'])){
			$this->data['error_warning'] = $this->error['warning'];
		} elseif (isset($this->session->data['date_error'])) {
			$this->data['error_warning'] = $this->session->data['date_error'];

			unset($this->session->data['date_error']);
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['config_key'])){
			$this->data['error_config_key'] = $this->error['config_key'];
		} else {
			$this->data['error_config_key'] = '';
		}

		if (isset($this->error['verify_key'])){
			$this->data['error_verify_key'] = $this->error['verify_key'];
		} else {
			$this->data['error_verify_key'] = '';
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
			'href'      => $this->url->link('module/shipstation', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$this->data['keygen'] = $this->url->link('module/shipstation/keygen', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['action'] = $this->url->link('module/shipstation', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['export'] = HTTPS_SERVER . '../shipstation/index.php?action=export';
		$this->data['clear'] = $this->url->link('module/shipstation/clear', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['shipstation_status'])) {
			$this->data['shipstation_status'] = $this->request->post['shipstation_status'];
		} else {
			$this->data['shipstation_status'] = $this->config->get('shipstation_status');
		}

		if (isset($this->request->post['shipstation_config_key'])) {
			$this->data['shipstation_config_key'] = $this->request->post['shipstation_config_key'];
		} elseif ($this->config->get('shipstation_config_key')) {
			$this->data['shipstation_config_key'] = $this->config->get('shipstation_config_key');
		} else {
			$this->data['shipstation_config_key'] = '';
		}

		if (isset($this->request->post['shipstation_verify_key'])) {
			$this->data['shipstation_verify_key'] = $this->request->post['shipstation_verify_key'];
		} elseif ($this->config->get('shipstation_verify_key')) {
			$this->data['shipstation_verify_key'] = $this->config->get('shipstation_verify_key');
		} else {
			$this->data['shipstation_verify_key'] = '';
		}

		$file = DIR_LOGS . 'shipstation/' . $this->config->get('config_error_filename');

		if (file_exists($file)) {
			$this->data['log'] = file_get_contents($file, FILE_USE_INCLUDE_PATH, null);
		} else {
			$this->data['log'] = '';
		}

		$this->template = 'module/shipstation.tpl';

		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/shipstation')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['shipstation_config_key']) {
			$this->error['config_key'] = $this->language->get('error_config_key');
		}

		if (!$this->request->post['shipstation_verify_key']) {
			$this->error['verify_key'] = $this->language->get('error_verify_key');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	public function install($install = true) {
		if (!$this->user->hasPermission('modify', 'module/shipstation')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!file_exists(DIR_LOGS . 'shipstation')) {
			mkdir(DIR_LOGS . 'shipstation');
		}

		if ($install) {
			$base_dir = str_replace('\'', '/', realpath(DIR_APPLICATION . '../')) . '/';

			$output  = '<?php' . "\n";
			$output .= '// Generated during install (' . date('F j, Y, g:i a') . ')' . "\n\n";

			$output .= '// HTTP' . "\n";
			$output .= 'define(\'HTTP_SERVER\', \'' . HTTP_SERVER . '\');' . "\n";
			$output .= 'define(\'HTTP_IMAGE\', \'http://' . $_SERVER['HTTP_HOST'] . '/image/\');' . "\n\n";

			$output .= '// HTTPS' . "\n";
			$output .= 'define(\'HTTPS_SERVER\', \'' . HTTPS_SERVER . '\');' . "\n";
			$output .= 'define(\'HTTPS_IMAGE\', \'http://' . $_SERVER['HTTP_HOST'] . '/image/\');' . "\n\n";

			$output .= '// DIR' . "\n";
			$output .= 'define(\'BASE_DIR\', \'' . $base_dir . '\');' . "\n\n";
			$output .= 'define(\'DIR_APPLICATION\', \'' . $base_dir . 'shipstation/' . '\');' . "\n";
			$output .= 'define(\'DIR_SYSTEM\', \'' . DIR_SYSTEM . '\');' . "\n";
			$output .= 'define(\'DIR_DATABASE\', \'' . DIR_DATABASE . '\');' . "\n";
			$output .= 'define(\'DIR_LANGUAGE\', \'' . DIR_LANGUAGE . '\');' . "\n";
			$output .= 'define(\'DIR_CONFIG\', \'' . DIR_CONFIG . '\');' . "\n";
			$output .= 'define(\'DIR_IMAGE\', \'' . DIR_IMAGE . '\');' . "\n";
			$output .= 'define(\'DIR_CACHE\', \'' . DIR_CACHE . '\');' . "\n";
			$output .= 'define(\'DIR_LOGS\', \'' . DIR_LOGS . 'shipstation/' . '\');' . "\n\n";

			$output .= '// DB' . "\n";
			$output .= 'define(\'DB_DRIVER\', \'' . DB_DRIVER . '\');' . "\n";
			$output .= 'define(\'DB_HOSTNAME\', \'' . DB_HOSTNAME . '\');' . "\n";
			$output .= 'define(\'DB_USERNAME\', \'' . DB_USERNAME . '\');' . "\n";
			$output .= 'define(\'DB_PASSWORD\', \'' . DB_PASSWORD . '\');' . "\n";
			$output .= 'define(\'DB_DATABASE\', \'' . DB_DATABASE . '\');' . "\n";
			$output .= 'define(\'DB_PREFIX\', \'' . DB_PREFIX . '\');' . "\n";
			$output .= '?>';

			$file = fopen('../shipstation/config.php', 'w');

			fwrite($file, $output);

			fclose($file);
		}
	}

	public function uninstall($uninstall = true) {
		if (!$this->user->hasPermission('modify', 'module/shipstation')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->model('setting/setting');

		$this->model_setting_setting->deleteSetting('shipstation');

		if ($uninstall) {
			$output = '';

			$file = fopen('../shipstation/config.php', 'w');

			fwrite($file, $output);

			fclose($file);
		}
	}

	public function keygen() {
		$this->load->model('setting/setting');

		$config_key = sha1('shipstation' . time() . HTTP_CATALOG);
		$verify_key = md5($config_key . DIR_APPLICATION);

		$data = array(
			'shipstation_status'     => $this->config->get('shipstation_status'),
			'shipstation_config_key' => $config_key,
			'shipstation_verify_key' => $verify_key
		);

		$this->model_setting_setting->editSetting('shipstation', $data);

		$this->redirect($this->url->link('module/shipstation', 'token=' . $this->session->data['token'], 'SSL'));
	}

	public function clear() {
		$this->load->language('module/shipstation');

		$file = DIR_LOGS . 'shipstation/' . $this->config->get('config_error_filename');

		$handle = fopen($file, 'w+'); 

		fclose($handle); 			

		$this->session->data['success'] = $this->language->get('text_cleared');

		$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));		
	}
}
?>
