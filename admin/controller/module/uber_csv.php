<?php
//-----------------------------------------
// Author: 	Qphoria@gmail.com
// Web: 	http://www.OpenCartGuru.com/
//-----------------------------------------
class ControllerModuleUberCSV extends Controller {
	private $error = array();

	public function index() {

		// Init
		$classname = str_replace('vq2-admin_controller_module_', '', basename(__FILE__, '.php'));
		$this->data['classname'] = $classname;
		$this->data = array_merge($this->data, $this->load->language('module/' . $classname));
		if (!isset($this->session->data['token'])) { $this->session->data['token'] = 0; }
		$this->data['token'] = $this->session->data['token'];

		$this->load->language('module/' . $classname);

		$this->load->model('module/' . $classname);

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate()) {
			if (is_uploaded_file($this->request->files['csv_import']['tmp_name'])) {
				$content = file_get_contents($this->request->files['csv_import']['tmp_name']);
				$filename = $this->request->files['csv_import']['tmp_name'];
			} else {
				$content = false;
			}

			if ($content) {
				$truncate = false;
				if (isset($this->request->post['truncate'])) {
					$truncate = true;
				}
				$result = $this->model_module_uber_csv->csvImport($filename, $truncate);

				if (isset($result['error'])) {
					$this->error['warning'] = $this->language->get($result['error']);
				} else {
					$this->session->data['success'] = str_replace("{updated}", $result['updated'], $this->language->get('text_success'));
					$this->session->data['success'] = str_replace("{inserted}", $result['inserted'], $this->session->data['success']);
				}

				$this->redirect(HTTPS_SERVER . 'index.php?route=module/'.$classname.'&token=' . $this->session->data['token']);
			} else {
				$this->error['warning'] = $this->language->get('error_empty');
			}
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$this->data['heading_title'] = $this->language->get('heading_title');

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'href'      => (((HTTPS_SERVER) ? HTTPS_SERVER : HTTP_SERVER) . 'index.php?token=' . $this->session->data['token'] . '&route=common/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);

   		$this->data['breadcrumbs'][] = array(
       		'href'      => (((HTTPS_SERVER) ? HTTPS_SERVER : HTTP_SERVER) . 'index.php?token=' . $this->session->data['token'] . '&route=extension/module'),
       		'text'      => $this->language->get('text_module'),
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
       		'href'      => (((HTTPS_SERVER) ? HTTPS_SERVER : HTTP_SERVER) . 'index.php?token=' . $this->session->data['token'] . '&route=module/' . $classname),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);

   		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		}

		if (isset($this->session->data['error'])) {
			$this->data['error_warning'] = $this->session->data['error'];
			unset($this->session->data['error']);
		} elseif (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		$this->data['restore'] = (((HTTPS_SERVER) ? HTTPS_SERVER : HTTP_SERVER) . 'index.php?route=module/'.$classname.'&token=' . $this->session->data['token']);
		$this->data['csv'] = (((HTTPS_SERVER) ? HTTPS_SERVER : HTTP_SERVER) . 'index.php?route=module/'.$classname.'/csv&token=' . $this->session->data['token']);
		$this->data['csv_import'] = (((HTTPS_SERVER) ? HTTPS_SERVER : HTTP_SERVER) . 'index.php?route=module/'.$classname.'&token=' . $this->session->data['token']);
		$this->data['csv_export'] = (((HTTPS_SERVER) ? HTTPS_SERVER : HTTP_SERVER) . 'index.php?route=module/'.$classname.'/csvExport&token=' . $this->session->data['token']);

		//$this->load->model('model/uber_csv');

		$this->data['tables'] = $this->model_module_uber_csv->getTables();

		$this->data['cancel'] = (((HTTPS_SERVER) ? HTTPS_SERVER : HTTP_SERVER) . 'index.php?token=' . $this->session->data['token'] . '&route=extension/module');

		$this->id       = 'content';
		$this->template = 'module/' . $classname . '.tpl';

		$this->children = array(
			'common/header',
			'common/footer'
		);

        if (method_exists($this->document, 'addBreadcrumb')) {
			$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression')); //v14x
		} else {
			$this->response->setOutput($this->render()); //v15x
		}
	}

	public function csvExport() {
		$classname = str_replace('vq2-admin_controller_module_', '', basename(__FILE__, '.php'));
		$this->data = array_merge($this->data, $this->load->language('module/' . $classname));

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validate() && isset($this->request->post['csv_export_table'])) {

			$ReflectionResponse = new ReflectionClass($this->response);
			if ($ReflectionResponse->getMethod('addheader')->getNumberOfParameters() == 2) {
				$this->response->addheader('Pragma', 'public');
				$this->response->addheader('Expires', '0');
				$this->response->addheader('Content-Description', 'File Transfer');
				$this->response->addheader("Content-type', 'text/octect-stream");
				$this->response->addheader("Content-Disposition', 'attachment;filename=csv_export_" . $this->request->post['csv_export_table'] . ".csv");
				$this->response->addheader('Content-Transfer-Encoding', 'binary');
				$this->response->addheader('Cache-Control', 'must-revalidate, post-check=0,pre-check=0');
			} else {
				$this->response->addheader('Pragma: public');
				$this->response->addheader('Expires: 0');
				$this->response->addheader('Content-Description: File Transfer');
				$this->response->addheader("Content-type:text/octect-stream");
				$this->response->addheader("Content-Disposition:attachment;filename=csv_export_" . $this->request->post['csv_export_table'] . ".csv");
				$this->response->addheader('Content-Transfer-Encoding: binary');
				$this->response->addheader('Cache-Control: must-revalidate, post-check=0,pre-check=0');
			}

			$this->load->model('module/uber_csv');

			$results = $this->model_module_uber_csv->csvExport($this->request->post);

			if (is_array($results) && !empty($results['error'])) {
				$this->session->data['error'] = $this->language->get($results['error']);
				$this->redirect(HTTPS_SERVER . 'index.php?route=module/'.$classname.'&token=' . $this->session->data['token']);
			} else {
				$this->response->setOutput($results);
			}
		} else {
			return $this->forward('error/permission');
		}
	}

	public function getColumns() {
		$json = array();

		$this->load->model('module/uber_csv');

    	$columns = $this->model_module_uber_csv->getColumns($this->request->get['table']);

		if ($columns) {
			$json['columns'] = $columns;
		}

		$this->response->setOutput(json_encode($json));
	}

	private function validate() {
		$classname = str_replace('vq2-admin_controller_module_', '', basename(__FILE__, '.php'));
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