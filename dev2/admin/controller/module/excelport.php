<?php
class ControllerModuleExcelport extends Controller {
	private $error = array(); 
	
	public function index() {
		header('Cache-Control: no-cache, no-store');
		$this->language->load('module/excelport');
		ini_set('memory_limit', '1024M');
		ini_set('max_execution_time', 900);
		ini_set('error_reporting', E_ALL);
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->document->addStyle('view/javascript/excelport/bootstrap/css/bootstrap.min.css');
		$this->document->addStyle('view/stylesheet/excelport.css');
		$this->document->addScript('view/javascript/excelport/bootstrap/js/bootstrap.min.js');
		
		$this->load->model('module/excelport');
		$this->load->model('setting/store');
		$this->load->model('localisation/language');
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			$this->session->data['flash_error'] = array();
			$this->session->data['flash_success'] = array();
			
			if (!$this->user->hasPermission('modify', 'module/excelport')) {
				$this->session->data['flash_error'][] = $this->language->get('error_permission');
				$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
			}
			
			$this->model_module_excelport->editSetting('excelport', $this->request->post);
			
			$submitAction = (empty($this->request->get['submitAction'])) ? '' : $this->request->get['submitAction'];
			
			if (empty($submitAction)) $this->session->data['flash_success'][] = $this->language->get('text_success');
			
			if (!empty($this->request->get['activate'])) {
				$this->session->data['flash_success'][] = $this->language->get('text_success_activation');
			}
			
			$submitAction = (empty($this->request->get['submitAction'])) ? '' : $this->request->get['submitAction'];
			
			try {
				switch ($submitAction) {
					case 'export' : {
						unset($this->session->data['generated_files']);
						unset($this->session->data['generated_file']);
						$this->session->data['generated_files'] = array();
						$this->model_module_excelport->deleteProgress();
						$this->session->data['ajaxgenerate'] = true;
						$this->model_module_excelport->cleanTemp(IMODULE_ROOT . IMODULE_TEMP_FOLDER);
					} break;
					case 'import' : {
						$this->model_module_excelport->deleteProgress();
						$this->session->data['ajaximport'] = true;
						
						$uploadedFile = $this->model_module_excelport->getStandardFile($this->request->files['ExcelPort'], 'Import', 'File');
						
						$this->session->data['uploaded_files'] = $this->model_module_excelport->prepareUploadedFile($uploadedFile);
						
						if (!empty($this->session->data['uploaded_files']) && !empty($this->request->post['ExcelPort']['Import']['Delete'])) {
							if ($this->request->post['ExcelPort']['Import']['DataType'] == 'Products') {
								$this->load->model('module/excelport_product');
								$this->model_module_excelport_product->deleteProducts();
							} else if ($this->request->post['ExcelPort']['Import']['DataType'] == 'Categories') {
								$this->load->model('module/excelport_category');
								$this->model_module_excelport_category->deleteCategories();	
							} else if ($this->request->post['ExcelPort']['Import']['DataType'] == 'Options') {
								$this->load->model('module/excelport_option');
								$this->model_module_excelport_option->deleteOptions();	
							} else if ($this->request->post['ExcelPort']['Import']['DataType'] == 'Attributes') {
								$this->load->model('module/excelport_attribute');
								$this->model_module_excelport_attribute->deleteAttributes();
								$this->model_module_excelport_attribute->deleteAttributeGroups();	
							} else if ($this->request->post['ExcelPort']['Import']['DataType'] == 'Customers') {
								$this->load->model('module/excelport_customer');
								$this->model_module_excelport_attribute->deleteCustomers();
							} else if ($this->request->post['ExcelPort']['Import']['DataType'] == 'CustomerGroups') {
								$this->load->model('module/excelport_customer_group');
								$this->model_module_excelport_customer_group->deleteCustomerGroups();
							}
						}
					} break;
				}
			} catch(Exception $e) {
				$this->session->data['flash_error'][] = $e->getMessage();	
			}
			
			$selectedTab = (empty($this->request->post['selectedTab'])) ? 0 : $this->request->post['selectedTab'];
			$this->redirect($this->url->link('module/excelport', 'token=' . $this->session->data['token'] . '&tab='.$selectedTab, 'SSL'));
		}

		// Set language data
		$variables = array(
			'heading_title',
			'text_enabled',
			'text_disabled',
			'text_content_top',
			'text_content_bottom',
			'text_column_left',
			'text_column_right',
			'text_activate',
			'text_not_activated',
			'text_click_activate',
			'entry_code',
			'button_save',
			'button_cancel',
			'entry_layouts_active',
			'text_question_data',
			'text_datatype_option_products',
			'text_question_store',
			'text_question_language',
			'button_export',
			'text_note',
			'text_learn_to_increase',
			'button_import',
			'text_question_data_import',
			'text_question_store_import',
			'text_question_language_import',
			'text_question_file_import',
			'text_file_generating',
			'text_file_downloading',
			'text_import_done',
			'text_preparing_data',
			'text_export_entries_number',
			'text_import_limit',
			'text_confirm_delete_other',
			'text_question_delete_other',
			'text_question_type_export',
			'text_question_add_as_new',
			'text_datatype_option_categories',
			'text_datatype_option_attributes',
			'text_toggle_filter',
			'button_add_condition',
			'button_discard_condition',
			'text_conjunction',
			'text_the_value',
			'help_conjunction',
			'text_datatype_option_customers',
			'text_datatype_option_customer_groups',
			'text_datatype_option_options'
		);
		foreach ($variables as $variable) $this->data[$variable] = $this->language->get($variable);
		
		$this->data['text_supported_in_oc1541'] = sprintf($this->language->get('text_supported_in_oc1541'), IMODULE_UPMOST_VERSION);
		
		$this->data['stores'] = array_values($this->model_setting_store->getStores());
		$this->data['languages'] = array_values($this->model_localisation_language->getLanguages());
		
		$this->data['error_code'] = isset($this->error['code']) ? $this->error['code'] : '';
  		$this->data['breadcrumbs'] = array(
			array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => false
			),
			array(
				'text'      => $this->language->get('text_module'),
				'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => ' :: '
			),
			array(
				'text'      => $this->language->get('heading_title'),
				'href'      => $this->url->link('module/excelport', 'token=' . $this->session->data['token'], 'SSL'),
				'separator' => ' :: '
			)
		);	
		$this->data['action'] = $this->url->link('module/excelport', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		
		if (isset($this->request->post['ExcelPort'])) {
			foreach ($this->request->post['ExcelPort'] as $key => $value) {
				$this->data['data']['ExcelPort'][$key] = $this->request->post['ExcelPort'][$key];
			}
		} else {
			$configValue = $this->model_module_excelport->getSetting('excelport');
			$this->data['data'] = $configValue;
			
		}
		
		$this->data['currenttemplate'] =  $this->config->get('config_template');
		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
		
		$this->data['conditions'] = $this->model_module_excelport->conditions;
		$this->data['operations'] = $this->model_module_excelport->operations;
		
		$this->template = 'module/excelport.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
		
		$this->response->setOutput($this->render());
	}
	
	public function ajaxgenerate() {
		header('Cache-Control: no-cache, no-store');
		$this->session->data['start_time'] = time();
		ini_set('memory_limit', '1024M');
		ini_set('max_execution_time', 900);
		ini_set('display_errors', 1);
		ini_set('error_reporting', E_ALL);
		$this->load->model('module/excelport');
		$error = false;
		//$this->model_module_excelport->deleteProgress();
		
		set_error_handler(
			create_function(
				'$severity, $message, $file, $line',
				'throw new Exception($message . " in file " . $file . " on line " . $line);'
			)
		);
		
		try {
			$this->session->data['success'] = array();
			if ($this->model_module_excelport->exportXLS(
				$this->request->post['ExcelPort']['Export']['DataType'], 
				$this->request->post['ExcelPort']['Export']['Language'], 
				$this->request->post['ExcelPort']['Export']['Store'], IMODULE_ROOT . IMODULE_TEMP_FOLDER, 
				$this->request->post['ExcelPort']['Settings'], 
				!empty($this->request->post['ExcelPort']['Export']['QuickExport']), 
				!empty($this->request->post['ExcelPort']['Export']['Filter']), 
				!empty($this->request->post['ExcelPort']['Export']['Filters']) ? $this->request->post['ExcelPort']['Export']['Filters'] : array()
			)) {
				//$this->session->data['success'][] = 'Success'; // TODO - AJAX
			} else {
				//$this->session->data['error_warning'][] = 'I\'m a Failure :(';
			}
		} catch (Exception $e) {
			$error = $e->getMessage();	
		}
		
		restore_error_handler();
		$progress = $this->model_module_excelport->getProgress($error);
		header('Content-Type: application/json');
		echo json_encode($progress);
		exit;
	}
	
	public function ajaximport() {
		header('Cache-Control: no-cache, no-store');
		$this->session->data['start_time'] = time();
		ini_set('memory_limit', '1024M');
		ini_set('max_execution_time', 900);
		ini_set('display_errors', 1);
		ini_set('error_reporting', E_ALL);
		$this->load->model('module/excelport');
		$error = false;
		
		//$this->model_module_excelport->deleteProgress();
		if (!empty($this->session->data['uploaded_files'])) {
			$file = $this->session->data['uploaded_files'][0];
			
			set_error_handler(
				create_function(
					'$severity, $message, $file, $line',
					'throw new Exception($message . " in file " . $file . " on line " . $line);'
				)
			);
			
			try {
				$this->session->data['success'] = array();
				
				if ($this->model_module_excelport->importXLS($this->request->post['ExcelPort']['Import']['DataType'], $this->request->post['ExcelPort']['Import']['Language'], $file, $this->request->post['ExcelPort']['Settings'], !empty($this->request->post['ExcelPort']['Import']['AddAsNew']))) {
					//$this->session->data['success'][] = 'Success'; // TODO - AJAX
				} else {
					//$this->session->data['error_warning'][] = 'I\'m a Failure :(';
				}
			} catch (Exception $e) {
				$error = $e->getMessage();	
			}
			
			restore_error_handler();
			
		} else {
			$this->language->load('module/excelport');
			$progress = $this->model_module_excelport->getProgress();
			$progress['finishedImport'] = true;
			$this->model_module_excelport->setProgress($progress);
		}
		
		$progress = $this->model_module_excelport->getProgress($error);
		header('Content-Type: application/json');
		echo json_encode($progress);
		exit;
	}
	
	public function download() {
		header('Cache-Control: no-cache, no-store');
		$files = $this->session->data['generated_files'];
		$this->load->model('module/excelport');
		
		if (!empty($files)) {
			$this->load->model('localisation/language');
			
			$name = 'excelport_' . str_replace('/', '_', substr(HTTP_CATALOG, 7, strlen(HTTP_CATALOG) - 8)) . '_' . date("Y-m-d_H-i-s") . ".zip";
			
			$file = $this->model_module_excelport->createZip($files, IMODULE_ROOT . IMODULE_TEMP_FOLDER . '/' . $name, true, IMODULE_ROOT . IMODULE_TEMP_FOLDER . '/');
			if (file_exists($file) && !empty($file)) {
				$this->model_module_excelport->createDownload($file, false);
			} else {
				$this->model_module_excelport->cleanTemp();	
			}
		} else {
			$this->model_module_excelport->cleanTemp();	
		}
	}
	
	public function install() {
		
	}
	
	public function uninstall() {
		$this->load->model('module/excelport');
		$this->model_module_excelport->deleteSetting('excelport');
	}
	
	
}
?>