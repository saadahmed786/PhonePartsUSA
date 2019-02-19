<?php
class ControllerModuleProductbundles extends Controller {
	private $error = array(); 
	public function index() {   
		$this->language->load('module/productbundles');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');
		$this->data['error_warning'] = '';
	
		$this->document->addScript('view/javascript/productbundles/bootstrap/js/bootstrap.min.js');
		$this->document->addScript('view/javascript/productbundles/productbundles.js');
		$this->document->addStyle('view/javascript/productbundles/bootstrap/css/bootstrap.min.css');
		$this->document->addStyle('view/javascript/productbundles/font-awesome/css/font-awesome.min.css');
		$this->document->addStyle('view/stylesheet/productbundles.css');		
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			if (!$this->user->hasPermission('modify', 'module/productbundles')) {
				$this->validate();
				$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
			}
			if (!empty($_POST['OaXRyb1BhY2sgLSBDb21'])) {
				$this->request->post['ProductBundles']['LicensedOn'] = $_POST['OaXRyb1BhY2sgLSBDb21'];
			}
			if (!empty($_POST['cHRpbWl6YXRpb24ef4fe'])) {
				$this->request->post['ProductBundles']['License'] = json_decode(base64_decode($_POST['cHRpbWl6YXRpb24ef4fe']),true);
			}
		
			if (isset($this->request->post['productbundles_custom'])) {
				foreach ($this->request->post['productbundles_custom'] as $bundle) {
					if (!isset($bundle['products'])) {
						unset($this->request->post['productbundles_custom'][$bundle['id']]);	
					}
				}
			}
			
			$this->model_setting_setting->editSetting('ProductBundles', $this->request->post);		
				
			$this->session->data['success'] = $this->language->get('text_success');
			
			if (!empty($_GET['activate'])) {
				$this->session->data['success'] = $this->language->get('text_success_activation');
			}
			
			$selectedTab = (empty($this->request->post['selectedTab'])) ? 0 : $this->request->post['selectedTab'];
			$this->redirect($this->url->link('module/productbundles', 'token=' . $this->session->data['token'] . '&tab='.$selectedTab, 'SSL'));
		}
		
		if (isset($this->request->post['store_id'])) {
			$this->data['store_id'] = $this->request->post['store_id'];
		} elseif (!empty($order_info)) {
			$this->data['store_id'] = $order_info['store_id'];
		} else {
			$this->data['store_id'] = '';
		}

		$this->load->model('setting/store');

		$this->data['stores'] = $this->model_setting_store->getStores();

		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$this->data['store_url'] = HTTPS_CATALOG;
		} else {
			$this->data['store_url'] = HTTP_CATALOG;
		}
	
		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_content_top'] = $this->language->get('text_content_top');
		$this->data['text_content_bottom'] = $this->language->get('text_content_bottom');		
		$this->data['text_column_left'] = $this->language->get('text_column_left');
		$this->data['text_column_right'] = $this->language->get('text_column_right');
		$this->data['text_load_in_selector'] = $this->language->get('text_load_in_selector');
		$this->data['text_default'] = $this->language->get('text_default');
		$this->data['entry_code'] = $this->language->get('entry_code');
		$this->data['entry_layout'] = $this->language->get('entry_layout');
		$this->data['entry_position'] = $this->language->get('entry_position');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$this->data['entry_layout_options'] = $this->language->get('entry_layout_options');
		$this->data['entry_position_options'] = $this->language->get('entry_position_options');
		$this->data['entry_enable_disable']	= $this->language->get('entry_enable_disable');
		$this->data['entry_yes'] = $this->language->get('entry_yes');
		$this->data['entry_no'] = $this->language->get('entry_no');
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_add_module'] = $this->language->get('button_add_module');
		$this->data['button_remove'] = $this->language->get('button_remove');
		$this->load->model('localisation/language');
		$languages = $this->model_localisation_language->getLanguages();;
		$this->data['languages'] = $languages;
		$firstLanguage = array_shift($languages);
		$this->data['firstLanguageCode'] = $firstLanguage['code'];
		
		$price = $this->currency->getSymbolRight($this->config->get('config_currency'));
		if (!empty($price)) {
			$this->data['currencyAlignment'] = "R";
			$this->data['currency'] = $this->currency->getSymbolRight($this->config->get('config_currency'));
		} else {
			$this->data['currencyAlignment'] = "L";
			$this->data['currency'] = $this->currency->getSymbolLeft($this->config->get('config_currency'));
		}
		
 		if (isset($this->error['code'])) {
			$this->data['error_code'] = $this->error['code'];
		} else {
			$this->data['error_code'] = '';
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
			'href'      => $this->url->link('module/productbundles', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		$this->data['action'] = $this->url->link('module/productbundles', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		if (isset($this->request->post['ProductBundles'])) {
			foreach ($this->request->post['ProductBundles'] as $key => $value) {
				$this->data['data']['ProductBundles'][$key] = $this->request->post['ProductBundles'][$key];
			}
		} else {
			$configValue = $this->config->get('ProductBundles');
			$this->data['data']['ProductBundles'] = $configValue;		
		}
		
		$this->data['CustomBundles'] = array();
		if (isset($this->request->post['productbundles_custom'])) {
			$this->data['CustomBundles'] = $this->request->post['productbundles_custom'];
			exit;
		} elseif ($this->config->get('productbundles_custom')) { 
			$this->data['CustomBundles'] = $this->config->get('productbundles_custom');
		}
		
		$this->data['currenttemplate'] =  $this->config->get('config_template');
		$this->data['modules'] = array();
		if (isset($this->request->post['productbundles_module'])) {
			$this->data['modules'] = $this->request->post['productbundles_module'];
			exit;
		} elseif ($this->config->get('productbundles_module')) { 
			$this->data['modules'] = $this->config->get('productbundles_module');
		}	
		$this->load->model('design/layout');
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
		$this->template = 'module/productbundles.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);		
		$this->load->model('catalog/product');
		$this->load->model('catalog/category');
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/productbundles')) {
			$this->error = true;
		}
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}	
}
?>