<?php
class ControllerModuleBossHomecategory extends Controller {
	private $error = array(); 
	
	public function index() {   
		$this->load->language('module/boss_homecategory');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('boss_homecategory', $this->request->post);		
					
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}
				
		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['entry_image'] = $this->language->get('entry_image');
		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_content_top'] = $this->language->get('text_content_top');
		$this->data['text_content_bottom'] = $this->language->get('text_content_bottom');		
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		$this->data['text_home'] = $this->language->get('text_home');
		
		$this->data['entry_category'] = $this->language->get('entry_category');
		$this->data['entry_limit'] = $this->language->get('entry_limit');
		$this->data['entry_use_scrolling_panel'] = $this->language->get('entry_use_scrolling_panel');	
	
		$this->data['entry_layout'] = $this->language->get('entry_layout');
		$this->data['entry_position'] = $this->language->get('entry_position');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_add_module'] = $this->language->get('button_add_module');
		$this->data['button_remove'] = $this->language->get('button_remove');
			
		$this->data['column_name'] = $this->language->get('column_name');
		//select
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		if (isset($this->error['image'])) {
			$this->data['error_image'] = $this->error['image'];
		} else {
			$this->data['error_image'] = array();
		}
		if (isset($this->error['limit'])) {
			$this->data['error_limit'] = $this->error['limit'];
		} else {
			$this->data['error_limit'] = array();
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
			'href'      => $this->url->link('module/boss_homecategory', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/boss_homecategory', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->load->model('catalog/category');
		
		$this->data['categories'] = array();
		
		$results = $this->model_catalog_category->getCategories(0);

		foreach ($results as $result) {
			$this->data['categories'][] = array(
				'category_id' => $result['category_id'],
				'name'        => $result['name']
			);
		}
		
		//modules
		$this->data['modules'] = array();
		
		if (isset($this->request->post['boss_homecategory_module'])) {
			$this->data['modules'] = $this->request->post['boss_homecategory_module'];
		} elseif ($this->config->get('boss_homecategory_module')) { 
			$this->data['modules'] = $this->config->get('boss_homecategory_module');
		}
						
		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
					
		$this->template = 'module/boss_homecategory.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		
		if (!$this->user->hasPermission('modify', 'module/boss_homecategory')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (isset($this->request->post['boss_homecategory_module'])) {
			foreach ($this->request->post['boss_homecategory_module'] as $key => $value) {				
				if (!$value['width'] || !$value['height']) {
					$this->error['image'][$key] = $this->language->get('error_image');
				}
				if(!$value['limit']){
					$this->error['limit'][$key] = $this->language->get('error_limit');
				}
			}
		}	
				
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
	
	private function getIdLayout($layout_name) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "layout WHERE LOWER(name) = LOWER('".$layout_name."')");
		return (int)$query->row['layout_id'];
	}
	
	public function install() 
	{
		$this->load->model('setting/setting');
		$boss_homecategory = array('boss_homecategory_module' => array ( 
			0 => array ('width' => 150, 'height' => 150, 'limit' => 20, 'use_scrolling_panel' => 1,  'category_id' =>  25 ,'layout_id' => $this->getIdLayout("home"), 'position' => 'content_bottom', 'status' => 1, 'sort_order' => 1),
			1 => array ('width' => 150, 'height' => 150, 'limit' => 20, 'use_scrolling_panel' => 1,  'category_id' =>  18 ,  'layout_id' => $this->getIdLayout("home"), 'position' => 'content_bottom', 'status' => 1, 'sort_order' => 3),
			2 => array ('width' => 150, 'height' => 150, 'limit' => 20, 'use_scrolling_panel' => 1,  'category_id' => 30  ,  'layout_id' => $this->getIdLayout("home"), 'position' => 'content_bottom', 'status' => 1, 'sort_order' => 5),
			3 => array ('width' => 150, 'height' => 150, 'limit' => 20, 'use_scrolling_panel' => 1,  'category_id' =>  28 ,  'layout_id' => $this->getIdLayout("home"), 'position' => 'content_bottom', 'status' => 1, 'sort_order' => 7),
			)
		);
		
		$this->model_setting_setting->editSetting('boss_homecategory', $boss_homecategory);		
	}	
}
?>