<?php
class ControllerModulePaypalExpressModule extends Controller {
	private $error = array();

	public function index() {
		$classname = str_replace('vq2-' . basename(DIR_APPLICATION) . '_' . strtolower(get_parent_class($this)) . '_module_', '', basename(__FILE__, '.php'));

		$this->data = array_merge($this->data, $this->load->language('module/' . $classname));

		$this->data['classname'] = $classname;

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting($classname, $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect((HTTPS_SERVER?HTTPS_SERVER:HTTP_SERVER) . 'index.php?route=extension/module&token=' . $this->session->data['token']);
		}

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => ((HTTPS_SERVER?HTTPS_SERVER:HTTP_SERVER) . 'index.php?route=common/home&token=' . $this->session->data['token']),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => ((HTTPS_SERVER?HTTPS_SERVER:HTTP_SERVER) . 'index.php?route=extension/module&token=' . $this->session->data['token']),
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => ((HTTPS_SERVER?HTTPS_SERVER:HTTP_SERVER) . 'index.php?route=module/' . $classname . '&token=' . $this->session->data['token']),
      		'separator' => ' :: '
   		);
		
		/* 14x backwards compatibility */
		if (method_exists($this->document, 'addBreadcrumb')) { //1.4.x
			$this->document->breadcrumbs = $this->data['breadcrumbs'];
			unset($this->data['breadcrumbs']);
		}//

		$this->data['action'] = ((HTTPS_SERVER?HTTPS_SERVER:HTTP_SERVER) . 'index.php?route=module/' . $classname . '&token=' . $this->session->data['token']);

		$this->data['cancel'] = ((HTTPS_SERVER?HTTPS_SERVER:HTTP_SERVER) . 'index.php?route=extension/module&token=' . $this->session->data['token']);

		if (method_exists($this->document, 'addBreadcrumb')) { // v14x
		
			if (isset($this->request->post[$classname . '_status'])) {
				$this->data[$classname . '_status'] = $this->request->post[$classname . '_status'];
			} else {
				$this->data[$classname . '_status'] = $this->config->get($classname . '_status');
			}
		
			if (isset($this->request->post[$classname . '_position'])) {
				$this->data[$classname . '_position'] = $this->request->post[$classname . '_position'];
			} else {
				$this->data[$classname . '_position'] = $this->config->get($classname . '_position');
			}
			
			if (isset($this->request->post[$classname . '_sort_order'])) {
				$this->data[$classname . '_sort_order'] = $this->request->post[$classname . '_sort_order'];
			} else {
				$this->data[$classname . '_sort_order'] = $this->config->get($classname . '_sort_order');
			}
			
		} else { //v151x
		
			$this->data['modules'] = array();

			if (isset($this->request->post[$classname . '_module'])) {
				$this->data['modules'] = $this->request->post[$classname . '_module'];
			} elseif ($this->config->get($classname . '_module')) {
				$this->data['modules'] = $this->config->get($classname . '_module');
			}
			
			$this->load->model('design/layout');

			$this->data['layouts'] = $this->model_design_layout->getLayouts();
		
		}
		
		$this->template = 'module/' . $classname . '.tpl';
		$this->children = array(
			'common/header',
			'common/footer',
		);

		$this->response->setOutput($this->render(TRUE));
		
	}

	private function validate() {
		$classname = str_replace('vq2-' . basename(DIR_APPLICATION) . '_' . strtolower(get_parent_class($this)) . '_module_', '', basename(__FILE__, '.php'));
		if (!$this->user->hasPermission('modify', 'module/' . $classname)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
?>