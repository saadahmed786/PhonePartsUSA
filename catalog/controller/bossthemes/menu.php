<?php   
class ControllerBossthemesMenu extends Controller {
    protected function index() {
		
		$this->load->model('catalog/category');
		
		$this->data['categories'] = array();
		$categories = $this->model_catalog_category->getCategories(0);
		
		foreach ($categories as $category) {
			if ($category['top']) {
				$this->data['categories'][] = array(
					'name'     => $category['name'],
					'children' => $this->getChildrenCategory($category, $category['category_id']),
					'column'   => $category['column'] ? $category['column'] : 1,
					'href'     => $this->url->link('product/category', 'path=' . $category['category_id']),
					'category_id' => $category['category_id']
				);
			}
		}
        
		if (isset($this->request->get['path'])) {
			$path = '';
		
			$parts = explode('_', (string)$this->request->get['path']);
		
			$category_id = array_pop($parts);
			
			$this->data['current_id'] = $category_id;
		}
        
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/bossthemes/menu.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/bossthemes/menu.tpl';
		} else {
			$this->template = 'default/template/bossthemes/menu.tpl';
		}
		
    	$this->render();
    }
	
	private function getChildrenCategory($category, $path)
	{
		$children_data = array();
		$children = $this->model_catalog_category->getCategories($category['category_id']);
		
		foreach ($children as $child) {
			$data = array(
				'filter_category_id'  => $child['category_id'],
				'filter_sub_category' => true	
			);		
      if(  $this->config->get('config_product_count')){
        $product_total = $this->model_catalog_product->getTotalProducts($data);
                
        $children_data[] = array(
          'name'  => $child['name'] . ' (' . $product_total . ')',
          'children' => $this->getChildrenCategory($child, $path . '_' . $child['category_id']),
          'column'   => 1,
          'href'  => $this->url->link('product/category', 'path=' . $path . '_' . $child['category_id'])	
        );
      }else{
        $children_data[] = array(
          'name'  => $child['name'],
          'children' => $this->getChildrenCategory($child, $path . '_' . $child['category_id']),
          'column'   => 1,
          'href'  => $this->url->link('product/category', 'path=' . $path . '_' . $child['category_id'])	
        );
      }
		}
		return $children_data;
	}
}
?>