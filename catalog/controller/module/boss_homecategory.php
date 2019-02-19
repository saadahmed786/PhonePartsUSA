<?php
class ControllerModuleBossHomecategory extends Controller {
    protected function index($setting) {
        static $module = 0;
       
		$this->document->addScript('catalog/view/javascript/jquery/tabs.js');
		$this->document->addScript('catalog/view/javascript/bossthemes/jquery.easing.js');
		$this->document->addScript('catalog/view/javascript/bossthemes/jquery.elastislide.js');
		if (file_exists('catalog/view/theme/' . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/stylesheet/boss_homecategory.css'))
		{
			$this->document->addStyle('catalog/view/theme/' . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/stylesheet/boss_homecategory.css');
		} else {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/boss_homecategory.css');
		}

		$this->data['button_cart'] = $this->language->get('button_cart');
		$this->data['use_scrolling_panel'] = $setting['use_scrolling_panel']; 
		$this->data['template'] = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template'));
		$this->data['product_id'] = $this->config->get('product_id');
		
		$this->load->model('catalog/product');
		$this->load->model('catalog/category');
		$this->load->model('tool/image');
		
		$category_id = $setting['category_id'];
		if (isset($category_id)) {
			$this->data['tabs'] = array();
			$categories = array();
			$catagory_name = $this->model_catalog_category->getCategory($category_id);
			$results_category = $this->model_catalog_category->getCategories($category_id);
			foreach ($results_category as $category) {
				//print_r($results);die();
				$data = array(
					'filter_category_id' => $category['category_id'],
					'start' => 0,
					'limit' => $setting['limit']
				);
				
				$results = $this->model_catalog_product->getProducts($data);
				
				$products = array();
				foreach($results as $result){
					if ($result['image']) {
						$image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
					} else {
						$image = false;
					}

					if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
					} else {
						$price = false;
					}
							
					if ((float)$result['special']) { 
						$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
					} else {
						$special = false;
					}
					
					if ($this->config->get('config_review_status')) {
						$rating = $result['rating'];
					} else {
						$rating = false;
					}

					$products[] = array(
						'product_id' => $result['product_id'],
						'thumb'   	 => $image,
						'name'    	 => $result['name'],
						'description'=> utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..',
						'price'   	 => $price,
						'special' 	 => $special,
						'rating'     => $rating,
						'reviews'    => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
						'href'    	 => $this->url->link('product/product', 'product_id=' . $result['product_id']),
					);
				}
				
				
				$categories[] = array(
					'name' => $category['name'],
					'products' => $products
				);				
			}
			
			$this->data['tabs'] = array(
					'name'	 		 =>	$catagory_name['name'],
					'categories'       => $categories
			);
			
		}
		
		$this->data['module'] = $module++; 
       
	    if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/boss_homecategory.tpl')) {
            $this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/boss_homecategory.tpl';
        } else {
            $this->template = 'default/template/module/boss_homecategory.tpl';
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
								
			$children_data[] = array(
				'name'  	=> $child['name'],
				'children' 	=> $this->getChildrenCategory($child, $path . '_' . $child['category_id']),
				'column'   	=> 1,
				'href'  => $this->url->link('product/category', 'path=' . $path . '_' . $child['category_id'])	
			);
			
		}
		return $children_data;
	}
}

?>