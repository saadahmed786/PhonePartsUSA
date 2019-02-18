<?php
class ControllerModuleCategoryQuantity extends Controller {
	private $error = array(); 
	 
	public function index() {   
		$this->load->language('module/category_quantity');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			$this->request->post['category_quantity_module'][1]['layout_id']=0;
			$this->request->post['category_quantity_module'][1]['position']=0;
			$this->request->post['category_quantity_module'][1]['sort_order']=0;
			
			$this->model_setting_setting->editSetting('category_quantity', $this->request->post);		
			
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}
		
		
		
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_modules'] = $this->language->get('text_modules');
		$this->data['text_success'] = $this->language->get('text_success');
		
		$this->data['entry_enable_on_views'] = $this->language->get('entry_enable_on_views');
		$this->data['entry_enable_on_modules'] = $this->language->get('entry_enable_on_modules');

		$this->data['error_permission']    = $this->language->get('error_permission');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		
		//views
		$views[]=array(
					'name' => $this->language->get('view_category'),
					'key' => 'category'
				);
		$views[]=array(
					'name' => $this->language->get('view_product'),
					'key' => 'product'
				);
		$views[]=array(
					'name' => $this->language->get('view_cart'),
					'key' => 'cart'
				);
		$views[]=array(
				'name' => $this->language->get('view_search'),
				'key' => 'search'
		);
		
		$this -> data['views']=$views;
		
		//modules
		$modules[]=array(
					'name' => $this->language->get('module_bestseller'),
					'key' => 'bestseller'
				);
		$modules[]=array(
				'name' => $this->language->get('module_latest'),
				'key' => 'latest'
		);
		$modules[]=array(
				'name' => $this->language->get('module_special'),
				'key' => 'special'
		);
		$modules[]=array(
				'name' => $this->language->get('module_featured'),
				'key' => 'featured'
		);
		$this -> data['show_on_modules'] = $modules;

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
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
			'href'      => $this->url->link('module/category_quantity', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/category_quantity', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['token'] = $this->session->data['token'];

		$this->data['modules'] = array();
		
		if (isset($this->request->post['category_quantity_module'])) {
			$this->data['modules'] = $this->request->post['category_quantity_module'];
		} elseif ($this->config->get('category_quantity_module')) { 
			$this->data['modules'] = $this->config->get('category_quantity_module');
		}	
				
		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
		
		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		$this->template = 'module/category_quantity.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/category_quantity')) {
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