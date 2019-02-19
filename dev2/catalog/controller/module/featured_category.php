<?php  
class ControllerModuleFeaturedCategory extends Controller {
	protected function index($setting) {
		static $module = 0;
		$this->language->load('module/featured_category');
		$this->load->model('tool/image');
		
		$this->data['heading_title'] = $this->language->get('heading_title');
		
							
		// Category
		$this->load->model('catalog/category');
				
		$this->data['categories'] = array();
					
		$categories_1 = $this->model_catalog_category->getCategories(0);
		
		foreach ($categories_1 as $category_1) {
			$level_2_data = array();
			
			
			
			$categories_2 = $this->model_catalog_category->getCategories($category_1['category_id']);
			
			foreach ($categories_2 as $category_2) {
				$level_3_data = array();
				
				$categories_3 = $this->model_catalog_category->getCategories($category_2['category_id']);
				
				foreach ($categories_3 as $category_3) {
					$level_3_data[] = array(
						'id' => $category_3['category_id'],
						'name' => $category_3['name'],
						'href' => $this->url->link('product/category', 'path=' . $category_1['category_id'] . '_' . $category_2['category_id'] . '_' . $category_3['category_id'])
					);
				}
				
				//level 2
				$level_2_data[] = array(
					'id' => $category_2['category_id'],
					'name'     => $category_2['name'],
					'children' => $level_3_data,
					'href'     => $this->url->link('product/category', 'path=' . $category_1['category_id'] . '_' . $category_2['category_id'])	
				);					
			}
			
			//level 1
			$this->data['categories'][] = array(
			    'id' => $category_1['category_id'],
				'name'     => $category_1['name'],
				'children' => $level_2_data,
				'href'     => $this->url->link('product/category', 'path=' . $category_1['category_id'])
			);
		}
		
		$this->data['featured_category_cat'] = array();
		
		$results = $this->config->get('featured_category_cat');
		
		foreach($results as $result){			
			$this->data['featured_category_cat'][] = array(
				'catId' => $result['catId'],
				'catImg'  => $this->model_tool_image->resize($result['catImage'], $setting['image_width'], $setting['image_height']),
				'catDesc' => $result['catDesc']
			);
		}
	
				
	    $this->data['module'] = $module++;
		
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/featured_category.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/featured_category.tpl';
		} else {
			$this->template = 'default/template/module/featured_category.tpl';
		}
		
		$this->render();
	}
}
?>