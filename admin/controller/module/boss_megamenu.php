<?php
class ControllerModuleBossMegaMenu extends Controller {
	private $error = array(); 
	
	public function index() {   
		$this->language->load('module/boss_megamenu');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('boss_megamenu', $this->request->post);		
					
			$this->session->data['success'] = $this->language->get('text_success');
						
			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}
				
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['menu_title'] = $this->language->get('menu_title');
		$this->data['menu_options'] = $this->language->get('menu_options');
		$this->data['menu_dropdown'] = $this->language->get('menu_dropdown');
		$this->data['menu_order'] = $this->language->get('menu_order');
		$this->data['menu_status'] = $this->language->get('menu_status');
		
		$this->data['option_linkto'] = $this->language->get('option_linkto');
		$this->data['option_category'] = $this->language->get('option_category');
		$this->data['option_product'] = $this->language->get('option_product');
		$this->data['option_static_block'] = $this->language->get('option_static_block');
		$this->data['option_manufacturer'] = $this->language->get('option_manufacturer');
		$this->data['option_information'] = $this->language->get('option_information');
		
		$this->data['text_option'] = $this->language->get('text_option');
		$this->data['text_options'] = $this->language->get('text_options');
		$this->data['text_fill_the_column'] = $this->language->get('text_fill_the_column');
		$this->data['text_products_autocomplete'] = $this->language->get('text_products_autocomplete');
		$this->data['text_width'] = $this->language->get('text_width');
		$this->data['text_number_of_columns'] = $this->language->get('text_number_of_columns');
		$this->data['text_show_image'] = $this->language->get('text_show_image');
		$this->data['text_w_x_h'] = $this->language->get('text_w_x_h');
		$this->data['text_homepage_or_other_link'] = $this->language->get('text_homepage_or_other_link');
		$this->data['text_show_sub_category'] = $this->language->get('text_show_sub_category');
		$this->data['text_show_parent_category'] = $this->language->get('text_show_parent_category');
		$this->data['text_choose_parent_category'] = $this->language->get('text_choose_parent_category');
		$this->data['text_content'] = $this->language->get('text_content');
		$this->data['text_show_name'] = $this->language->get('text_show_name');
		$this->data['text_choose_manufacturers'] = $this->language->get('text_choose_manufacturers');
		$this->data['text_choose_informations'] = $this->language->get('text_choose_informations');
		$this->data['text_hide'] = $this->language->get('text_hide');
		$this->data['text_show'] = $this->language->get('text_show');
		$this->data['text_root_category'] = $this->language->get('text_root_category');
		
		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_remove'] = $this->language->get('button_remove');
		$this->data['button_add_menu'] = $this->language->get('button_add_menu');
		$this->data['button_add_option'] = $this->language->get('button_add_option');
		$this->data['button_remove_option'] = $this->language->get('button_remove_option');
		
		$this->data['entry_product'] = $this->language->get('entry_product');
		
		
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
			'href'      => $this->url->link('module/boss_megamenu', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/boss_megamenu', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['token'] = $this->session->data['token'];
		
		$this->data['menus'] = array();
		
		if (isset($this->request->post['boss_megamenu_menu'])) {
			$this->data['menus'] = $this->request->post['boss_megamenu_menu'];
		} elseif ($this->config->get('boss_megamenu_menu')) { 
			$this->data['menus'] = $this->config->get('boss_megamenu_menu');
		}	
		
		$this->load->model('catalog/product');
		$this->data['products_name'] = array();
		
		if (isset($this->data['menus'])) {
			foreach ($this->data['menus'] as $menu) {
				if (isset($menu['options'])) {
					foreach ($menu['options'] as $opt) {
						if (isset($opt['opt_product_ids'])) {
							foreach ($opt['opt_product_ids'] as $product_id) {
								$product_info = $this->model_catalog_product->getProduct($product_id);
								if ($product_info) {
									$this->data['products_name'][$product_id] = $product_info['name'];
								}
							}
						}
					}
				}
			}
		}
		
		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
		
		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
		$this->load->model('catalog/category');
		$this->data['categories'] = $this->model_catalog_category->getCategories(0);
		
		$this->load->model('catalog/manufacturer');
		$results = $this->model_catalog_manufacturer->getManufacturers();

        foreach ($results as $result) {
			$this->data['manufacturers'][] = array(
                'manufacturer_id' => $result['manufacturer_id'],
                'name'       	  => $result['name']
            );
        }
		
		$this->load->model('catalog/information');
		$results_info = $this->model_catalog_information->getInformations();
 
    	foreach ($results_info as $result) {
			$this->data['informations'][] = array(
				'information_id' => $result['information_id'],
				'title'          => $result['title']
			);
		}
		
		$this->template = 'module/boss_megamenu.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/boss_megamenu')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}

	public function install() 
	{
		$this->load->model('setting/setting');
			
		$this->load->model('localisation/language');
			
		$languages = $this->model_localisation_language->getLanguages();
		
		$array_title = array();
						
		foreach($languages as $language){
			$array_title{$language['language_id']} = 'home';
		}
		
		$array_option = array(0 => array('opt' => 'linkto', 'fill_column' => 6, 'opt_linkto_link' => 'index.php?route=common/home')
		);
		
		$boss_megamenu = array('boss_megamenu_menu' => array ( 
		0 => array ( 'title' => $array_title, 'dropdown_width' => 960, 'dropdown_column' => 6, 'options' => $array_option, 'order' => 1, 'status' => 1)	));
		
		$this->model_setting_setting->editSetting('boss_megamenu', $boss_megamenu);		
	}
}
?>