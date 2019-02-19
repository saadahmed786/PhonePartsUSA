<?php  
class ControllerModuleBossMegaMenu extends Controller {
	protected function index() {
		// menu
		$this->data['menus'] = array();
		
		$menus = array();
		$menus = $this->config->get('boss_megamenu_menu');
		
		if (isset($menus)) {
			$sort_order = array(); 
			foreach ($menus as $key => $value) {
				$sort_order[$key] = $value['order'];
			}
			array_multisort($sort_order, SORT_ASC, $menus);
			
			$this->load->model('catalog/manufacturer');
			$this->load->model('tool/image');
			$this->load->model('catalog/information');
			$this->load->model('catalog/category');
			$this->load->model('catalog/product');
			
			foreach ($menus as $menu) {
				if ($menu['status']){
					$href = "#";
					$options = array(); 
					if (isset($menu['options'])) {
						foreach ($menu['options'] as $option) {
							// manufacturer
							if ($option['opt'] == 'manufacturer') {
								if ($href == "#") {
									$href = $this->url->link('product/manufacturer');
								}
								
								$manufacturers = array();
								if (isset($option['opt_manufacturer_ids'])) {
									foreach($option['opt_manufacturer_ids'] as $manufacturer_id){
										$result = $this->model_catalog_manufacturer->getManufacturer($manufacturer_id);
										$manufacturers[] = array(
											'name'       	  	=> $result['name'],
											'image'				=> $this->model_tool_image->resize($result['image'], isset($option['opt_manufacturer_img_w']) ? $option['opt_manufacturer_img_w'] : 45, isset($option['opt_manufacturer_img_h']) ? $option['opt_manufacturer_img_h'] : 45),
											'href'				=> $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id'])
											);
									}
								}
								
								$options[] = array(
									'type'				=> 'manufacturer',
									'width'				=> $menu['dropdown_width']/$menu['dropdown_column']*$option['fill_column'],
									'column'			=> $option['fill_column'],
									'manufacturers'		=> $manufacturers,
									'show_image'		=> $option['opt_manufacturer_img'],
									'show_name'			=> $option['opt_manufacturer_name']
									);
							}
							// category
							if ($option['opt'] == 'category') {
								$categories = array();
								if (isset($option['opt_category_id'])) {
									if ($href == "#") {
										$href = $this->url->link('product/category', 'path=20');
									}
									
									$results = $this->model_catalog_category->getCategories($option['opt_category_id']);
									foreach ($results as $category) {
										$categories[] = array(
											'name'     		=> $category['name'],
											'children'		=> $this->getChildrenCategory($category, $category['category_id']),
											'image'			=> $category['image'] ? $this->model_tool_image->resize($category['image'], isset($option['opt_category_img_w']) ? $option['opt_category_img_w'] : 45, isset($option['opt_category_img_h']) ? $option['opt_category_img_h'] : 45) : '',
											'href'     		=> $this->url->link('product/category', 'path=' . $category['category_id'])
											);
									}
									if ($option['opt_category_id'] != 0) {
										$result = $this->model_catalog_category->getCategory($option['opt_category_id']);
										$parent = array(
											'name'     		=> $result['name'],
											'image'			=> $result['image'] ? $this->model_tool_image->resize($result['image'], isset($option['opt_category_img_w']) ? $option['opt_category_img_w'] : 45, isset($option['opt_category_img_h']) ? $option['opt_category_img_h'] : 45) : '',
											'href'     		=> $this->url->link('product/category', 'path=' . $result['category_id'])
											);
									}
									
									$options[] = array(
										'type'				=> 'category',
										'width'				=> $menu['dropdown_width']/$menu['dropdown_column']*$option['fill_column'],
										'column'			=> $option['fill_column'],
										'parent'			=> (isset($parent) ? $parent : null),
										'categories'		=> $categories,
										'show_image'		=> $option['opt_category_show_img'],
										'show_parent'		=> ($option['opt_category_id'] == 0 ? 0 : $option['opt_category_show_parent']),
										'show_submenu'		=> $option['opt_category_show_sub']
										);
								}
							}
							// information
							if ($option['opt'] == 'information') {
								$informations = array();
								if (isset($option['opt_information_ids'])) {
									foreach($option['opt_information_ids'] as $information_id){
										$result = $this->model_catalog_information->getInformation($information_id);
										$informations[] = array(
											'title' => $result['title'],
											'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
											);
									}
								}
								
								$options[] = array(
									'type'				=> 'information',
									'width'				=> $menu['dropdown_width']/$menu['dropdown_column']*$option['fill_column'],
									'column'			=> $option['fill_column'],
									'informations'		=> $informations
									);
							}
							// static block
							if ($option['opt'] == 'static_block') {
								$options[] = array(
									'type'				=> 'static_block',
									'width'				=> $menu['dropdown_width']/$menu['dropdown_column']*$option['fill_column'],
									'column'			=> $option['fill_column'],
									'description'		=> html_entity_decode($option['opt_static_block_des'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8')
									);
							}
							// product
							if ($option['opt'] == 'product') {
								$products = array();
								if (isset($option['opt_product_ids'])) {
									
									foreach ($option['opt_product_ids'] as $product_id) {
										$product_info = $this->model_catalog_product->getProduct($product_id);

										if ($product_info) {
											if ($option['opt_product_show_img'] && $product_info['image']) {
												$image = $this->model_tool_image->resize($product_info['image'], isset($option['opt_product_img_w']) ? $option['opt_product_img_w'] : 45, isset($option['opt_product_img_h']) ? $option['opt_product_img_h'] : 45);
											} else {
												$image = false;
											}

											if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
												$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
											} else {
												$price = false;
											}

											$products[] = array(
												'thumb'   	 => $image,
												'name'    	 => $product_info['name'],
												'price'   	 => $price,
												'href'    	 => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
												);
										}
									}
									
									$options[] = array(
										'type'				=> 'product',
										'width'				=> $menu['dropdown_width']/$menu['dropdown_column']*$option['fill_column'],
										'column'			=> $option['fill_column'],
										'products'			=> $products,
										);
								}
							}
							// link to
							if ($option['opt'] == 'linkto') {
								if (isset($option['opt_linkto_link'])) {
									$href = $option['opt_linkto_link'];
								}
							}
						}
					}
					
					$this->data['menus'][] = array(
						'title'	 			=> $menu['title'][$this->config->get('config_language_id')],
						'href'				=> $href,
						'dropdown_width'	=> $menu['dropdown_width'],
						'column_width'		=> $menu['dropdown_width']/ ($menu['dropdown_column'] ? $menu['dropdown_column'] : 1),
						'options'			=> $options
						);
				}
			}
		}
		// end menu
		
		if (in_array('product', explode('/', $this->request->get['route'])) && (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) == 'ppu2') {

			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/boss_megamenu_acordian.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/boss_megamenu_acordian.tpl';
			} else {
				$this->template = 'default/template/module/boss_megamenu_acordian.tpl';
			}
			
		} else {
			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/boss_megamenu.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/boss_megamenu.tpl';
			} else {
				$this->template = 'default/template/module/boss_megamenu.tpl';
			}
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