<?php 
class ControllerProductSearch extends Controller { 	
	public function index() { 
		$this->language->load('product/search');
		
		$this->load->model('catalog/category');
		
		$this->load->model('catalog/product');
		
		$this->load->model('tool/image'); 
		
		$this->language->load('product/product');
		$this->load->model('catalog/catalog');
		$search_tags = array('note'=>'Note','battery'=>'Battery','grand prime'=>'Grand Prime');
		
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];

			

		} else {
			$filter_name = '';
		} 
		// echo $filter_name;
		
		if (isset($this->request->get['filter_tag'])) {
			$filter_tag = $this->request->get['filter_tag'];
		} elseif (isset($this->request->get['filter_name'])) {
			$filter_tag = $filter_name;
		} else {
			$filter_tag = '';
		} 
		
		if (isset($this->request->get['filter_description'])) {
			$filter_description = $this->request->get['filter_description'];
		} else {
			$filter_description = '';
		} 
		
		if (isset($this->request->get['filter_category_id'])) {
			$filter_category_id = $this->request->get['filter_category_id'];
		} else {
			$filter_category_id = 0;
		} 
		
		if (isset($this->request->get['filter_sub_category'])) {
			$filter_sub_category = $this->request->get['filter_sub_category'];
		} else {
			$filter_sub_category = '';
		} 
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.sort_order';
		} 
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = 500;
		}
		
		if (isset($this->request->get['filter_name'])) {
			$this->document->setTitle($this->language->get('heading_title') .  ' - ' . $this->request->get['filter_name']);
		} else {
			$this->document->setTitle($this->language->get('heading_title'));
		}
		$this->data['breadcrumbs'] = array();
		$this->data['breadcrumbs'][] = array( 
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
			'separator' => false
			);
		
		$url = '';
		$this->document->addStyle('catalog/view/theme/' . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')). '/stylesheet/products.css');
		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_tag'])) {
			$url .= '&filter_tag=' . urlencode(html_entity_decode($this->request->get['filter_tag'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_description'])) {
			$url .= '&filter_description=' . $this->request->get['filter_description'];
		}
		
		if (isset($this->request->get['filter_category_id'])) {
			$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
		}
		
		if (isset($this->request->get['filter_sub_category'])) {
			$url .= '&filter_sub_category=' . $this->request->get['filter_sub_category'];
		}
		
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}	
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}	
		
		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}
		
		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('product/search', $url),
			'separator' => $this->language->get('text_separator')
			);
		
		if (isset($this->request->get['filter_name'])) {
			$this->data['heading_title'] = $this->language->get('heading_title') .  ' - ' . $this->request->get['filter_name'];
		} else {
			$this->data['heading_title'] = $this->language->get('heading_title');
		}
		
		$this->data['text_empty'] = $this->language->get('text_empty');
		$this->data['text_critea'] = $this->language->get('text_critea');
		$this->data['text_search'] = $this->language->get('text_search');
		$this->data['text_keyword'] = $this->language->get('text_keyword');
		$this->data['text_category'] = $this->language->get('text_category');
		$this->data['text_sub_category'] = $this->language->get('text_sub_category');
		$this->data['text_quantity'] = $this->language->get('text_quantity');
		$this->data['text_manufacturer'] = $this->language->get('text_manufacturer');
		$this->data['text_model'] = $this->language->get('text_model');
		$this->data['text_price'] = $this->language->get('text_price');
		$this->data['text_tax'] = $this->language->get('text_tax');
		$this->data['text_points'] = $this->language->get('text_points');
		$this->data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));
		$this->data['text_display'] = $this->language->get('text_display');
		$this->data['text_list'] = $this->language->get('text_list');
		$this->data['text_grid'] = $this->language->get('text_grid');		
		$this->data['text_sort'] = $this->language->get('text_sort');
		$this->data['text_limit'] = $this->language->get('text_limit');
		
		$this->data['entry_search'] = $this->language->get('entry_search');
		$this->data['entry_description'] = $this->language->get('entry_description');
		
		$this->data['button_search'] = $this->language->get('button_search');
		$this->data['button_cart'] = $this->language->get('button_cart');
		$this->data['button_wishlist'] = $this->language->get('button_wishlist');
		$this->data['button_compare'] = $this->language->get('button_compare');
		$this->data['compare'] = $this->url->link('product/compare');
		
		$this->load->model('catalog/category');
		
		// 3 Level Category Search
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
						'category_id' => $category_3['category_id'],
						'name'        => $category_3['name'],
						);
				}
				
				$level_2_data[] = array(
					'category_id' => $category_2['category_id'],	
					'name'        => $category_2['name'],
					'children'    => $level_3_data
					);					
			}
			
			$this->data['categories'][] = array(
				'category_id' => $category_1['category_id'],
				'name'        => $category_1['name'],
				'children'    => $level_2_data
				);
		}
		
		$this->data['products'] = array();
		
		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_tag'])) {
			$data = array(
				'filter_name'         => $filter_name, 
				'filter_tag'          => $filter_tag, 
				'filter_description'  => $filter_description,
				'filter_category_id'  => $filter_category_id, 
				'filter_sub_category' => $filter_sub_category, 
				'sort'                => $sort,
				'order'               => $order,
				'start'               => ($page - 1) * $limit,
				'limit'               => $limit
				);
			// print_r($data);
// Clear Thinking: Smart Search
				if ($this->config->get('smartsearch_status')) {
					$this->load->model('catalog/smartsearch');
					
					$data['return_total'] = true;
					$product_total = $this->model_catalog_smartsearch->smartsearch($data);
					
					$data['return_total'] = false;
					$results = $this->model_catalog_smartsearch->smartsearch($data);
				}
				// end Smart Search
				// Clear Thinking: Smart Search
			if ($this->config->get('smartsearch_status')) {
				$this->load->model('catalog/smartsearch');
				// $data['return_total'] = true;
// Clear Thinking: Smart Search
				if (!$this->config->get('smartsearch_status')) {
				// $product_total = $this->model_catalog_smartsearch->smartsearch($data);
}
				// end Smart Search
				$data['return_total'] = false;
				$results = $this->model_catalog_smartsearch->smartsearch($data);
			}
				// end Smart Search
				// print_r($results);exit;
			// Clear Thinking: Smart Search
			if (!$this->config->get('smartsearch_status')) {
// Clear Thinking: Smart Search
				if (!$this->config->get('smartsearch_status')) {
				$product_total = $this->model_catalog_product->getTotalProducts($data);
}
				// end Smart Search
			}
			// end Smart Search
			$manufacturer_id = (int)$this->request->get['brand_id'];		
			
			
			if (!$this->config->get('smartsearch_status')) {
// Clear Thinking: Smart Search
				if (!$this->config->get('smartsearch_status')) {
				$results = $this->model_catalog_product->getProducts($data);
}
				// end Smart Search
			}
			$model_ids = array();
// Clear Thinking: Smart Search
				if (!$this->config->get('smartsearch_status')) {
			$product_total = 0;					
}
				// end Smart Search
			foreach($results as $result)
			{
				if($manufacturer_id)
				{
					// echo "SELECT device_product_id FROM inv_device_product WHERE sku='".$result['model']."'";exit;
					$device_id = $this->db->query("SELECT device_product_id FROM inv_device_product WHERE sku='".$result['model']."'");
					$device_product_id = $device_id->row;
					$device_product_id = $device_product_id['device_product_id'];
					if(!$device_product_id)
					{
						continue;
					}
					else
					{
						// echo "SELECT device_manufacturer_id as id FROM inv_device_manufacturer WHERE manufacturer_id='".(int)$manufacturer_id."' and device_product_id='".(int)$device_product_id."'";exit;
						$check = $this->db->query("SELECT device_manufacturer_id as id FROM inv_device_manufacturer WHERE manufacturer_id='".(int)$manufacturer_id."' and device_product_id='".(int)$device_product_id."'");
						$check = $check->row;
						if(!$check)
						{
							continue;
						}
					}
				}
				// echo $result['product_id'];exit;
				$data = $this->model_catalog_product->getDeviceModels($result['product_id']);
				
				if($data)
				{
					foreach($data as $d)
					{
						$model_ids[$d['model_id']] = $d['model_id'];
					}
				}
				$product_total++;
			}
			$this->data['submodels'] = array();
			$this->data['devices'] = array();
			// print_r($model_ids);exit;
			foreach($model_ids as $model_id => $data)
			{
				

				// print_r($this->model_catalog_catalog->loadMOdels($modFilter));

				// Zaman Code starts to search from catalog
				if ($model_id) {
					
					if($product_total>20)
					{
					$this->data['submodels'][] = $this->model_catalog_catalog->loadSubModels($model_id);
						$modFilter = array (
					'limit' => 4,
					'order_by' => 'device',
					'order_type' => 'ASC',
					'append_manufacturer'=>true,
					'where' => array (
						array (
							'column' => 'model_id',
							'operator' => '=',
							'value' => $model_id
							)
						)
					);
						$this->data['devices'][] = $this->model_catalog_catalog->loadModels($modFilter);
					}


					$this->data['productsIds'] = $productsIds;
					$this->data['manufacturer_id'] = $manufacturer_id;
					// $this->data['main_class_id'] = $main_class_id;
					// $this->data['device_id'] = $model_id;
					$this->data['products'] = array();

				}

				
				// Zaman catalog code ends here
			}
			$_tmp =array();
			 foreach ($this->data['devices'] as $__submodels) { 
                   foreach ($__submodels as $xsubmodel) { 
                   	$_tmp[] = array('id'=>$xsubmodel['id'],'name'=>$xsubmodel['name']);
                   }
               }
               // print_r($_tmp);exit;
			usort($_tmp, array('ControllerProductSearch','compareByName2')); // sorting it
			$this->data['devices'] = $_tmp;
			// print_r($_tmp);exit;
			// print_r($this->data['devices']);exit;
			// exit;
			$classes = array();
			foreach ($results as $result) {
				// echo "SELECT device_product_id FROM inv_device_product WHERE sku='".$result['model']."'";exit;
				$device_id = $this->db->query("SELECT device_product_id FROM inv_device_product WHERE sku='".$result['model']."'");
				$device_product_id = $device_id->row;
				$device_product_id = $device_product_id['device_product_id'];	
				if($manufacturer_id)
				{
					// echo "SELECT device_product_id FROM inv_device_product WHERE sku='".$result['model']."'";exit;
					
					if(!$device_product_id)
					{
						continue;
					}
					else
					{
						// echo "SELECT device_manufacturer_id as id FROM inv_device_manufacturer WHERE manufacturer_id='".(int)$manufacturer_id."' and device_product_id='".(int)$device_product_id."'";exit;
						$check = $this->db->query("SELECT device_manufacturer_id as id FROM inv_device_manufacturer WHERE manufacturer_id='".(int)$manufacturer_id."' and device_product_id='".(int)$device_product_id."'");
						$check = $check->row;
						if(!$check)
						{
							continue;
						}
					}
				}
				// echo $device_product_id.'-'.$result['model'];exit;
				if($product_total>20)
				{

				$classes[] = $this->model_catalog_catalog->loadModelClasses(array('device_product_id' => $device_product_id, 'class_name' => 'Replacement Parts'));
				}
				// print_r($this->data['classes']);exit;		
				if ($result) {

					if($result['visibility'] == '1')
					{
						if ($result['image']) {
							$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
						} else {
							$image = $this->model_tool_image->resize('data/image-coming-soon.jpg', $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
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
						// echo $result['sale_price'];exit;	
						if ((float)$result['sale_price']) {
				$sale_price = $this->currency->format($this->tax->calculate($result['sale_price'], $result['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$sale_price = false;
			}

						if ($this->config->get('config_tax')) {
							$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price']);
						} else {
							$tax = false;
						}				

						if ($this->config->get('config_review_status')) {
							$rating = (int)$result['rating'];
						} else {
							$rating = false;
						}
						$this->data['products'][] = array(
							'product_id'  => $result['product_id'],
							'thumb'       => $image,
							'name'        => $result['name'],
							'quantity' 	  => $result['quantity'],
							'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..',
							'price'       => $price,
							'sale_price'  => $sale_price,
							'special'     => $special,
							'tax'         => $tax,
							'rating'      => $result['rating'],
							'attr'        => $this->model_catalog_catalog->getProductAttr($result['model']),
							'quality'        => $this->model_catalog_product->getProductQuality($result['model']),
							'class'        => $this->model_catalog_product->getProductClass($result['model']),
							'reviews'     => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
							'href'        => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'])
							);
					}

				}
			}	
			$classes = array_map("unserialize", array_unique(array_map("serialize", $classes)));
					// echo "<pre>";
					// print_r($classes);exit;
			$my_class = array();
			foreach($classes as $class)
			{
				if($class)
				{

					$my_class[] = $class;
				}
			}
			// print_r($my_class);exit;
			
				usort($my_class, array('ControllerProductSearch','compareByName')); // sorting it
				$this->data['classes'] = $my_class;


				


					// print_r($this->data['classes']);exit;
			// echo 'here';exit;
			// array_unique($model_ids);
			
			// foreach ($results as $result) {
			
			
			// 	if ($result['image']) {
			// 		$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
			// 	} else {
			// 		$image = $this->model_tool_image->resize('data/image-coming-soon.jpg', $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
			// 	}
			
			// 	if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
			// 		$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
			// 	} else {
			// 		$price = false;
			// 	}
			
			// 	if ((float)$result['special']) {
			// 		$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
			// 	} else {
			// 		$special = false;
			// 	}	
			
			// 	if ($this->config->get('config_tax')) {
			// 		$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price']);
			// 	} else {
			// 		$tax = false;
			// 	}				
			
			// 	if ($this->config->get('config_review_status')) {
			// 		$rating = (int)$result['rating'];
			// 	} else {
			// 		$rating = false;
			// 	}
			
			// 	$this->data['products'][] = array(
			// 		'product_id'  => $result['product_id'],
			// 		'thumb'       => $image,
			// 		'name'        => $result['name'],
			// 		'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..',
			// 		'price'       => $price,
			// 		'special'     => $special,
			// 		'tax'         => $tax,
			// 		'rating'      => $result['rating'],
			// 		'reviews'     => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
			// 		'href'        => $this->url->link('product/product', $url . '&product_id=' . $result['product_id'])
			// 	);
			// }
			
			// print_r($this->data['classes']);exit;
			$url = '';
			
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_tag'])) {
				$url .= '&filter_tag=' . urlencode(html_entity_decode($this->request->get['filter_tag'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_description'])) {
				$url .= '&filter_description=' . $this->request->get['filter_description'];
			}
			
			if (isset($this->request->get['filter_category_id'])) {
				$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
			}
			
			if (isset($this->request->get['filter_sub_category'])) {
				$url .= '&filter_sub_category=' . $this->request->get['filter_sub_category'];
			}
			
			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}
			

			if (count($this->data['products']) == 1) {
				foreach ($this->data['products'] as $product) {
					$this->redirect($product['href']);
				}
			}
			
			$this->data['sorts'] = array();
			
			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_default'),
				'value' => 'p.sort_order-ASC',
				'href'  => $this->url->link('product/search', 'sort=p.sort_order&order=ASC' . $url)
				);
			
			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_name_asc'),
				'value' => 'pd.name-ASC',
				'href'  => $this->url->link('product/search', 'sort=pd.name&order=ASC' . $url)
				); 
			
			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_name_desc'),
				'value' => 'pd.name-DESC',
				'href'  => $this->url->link('product/search', 'sort=pd.name&order=DESC' . $url)
				);
			
			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_price_asc'),
				'value' => 'p.price-ASC',
				'href'  => $this->url->link('product/search', 'sort=p.price&order=ASC' . $url)
				); 
			
			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_price_desc'),
				'value' => 'p.price-DESC',
				'href'  => $this->url->link('product/search', 'sort=p.price&order=DESC' . $url)
				); 
			
			if ($this->config->get('config_review_status')) {
				$this->data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_desc'),
					'value' => 'rating-DESC',
					'href'  => $this->url->link('product/search', 'sort=rating&order=DESC' . $url)
					); 
				
				$this->data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_asc'),
					'value' => 'rating-ASC',
					'href'  => $this->url->link('product/search', 'sort=rating&order=ASC' . $url)
					);
			}
			
			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_model_asc'),
				'value' => 'p.model-ASC',
				'href'  => $this->url->link('product/search', 'sort=p.model&order=ASC' . $url)
				); 
			
			$this->data['sorts'][] = array(
				'text'  => $this->language->get('text_model_desc'),
				'value' => 'p.model-DESC',
				'href'  => $this->url->link('product/search', 'sort=p.model&order=DESC' . $url)
				);
			
			$url = '';
			
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_tag'])) {
				$url .= '&filter_tag=' . urlencode(html_entity_decode($this->request->get['filter_tag'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_description'])) {
				$url .= '&filter_description=' . $this->request->get['filter_description'];
			}
			
			if (isset($this->request->get['filter_category_id'])) {
				$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
			}
			
			if (isset($this->request->get['filter_sub_category'])) {
				$url .= '&filter_sub_category=' . $this->request->get['filter_sub_category'];
			}
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}	
			
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			
			$this->data['limits'] = array();
			
			$this->data['limits'][] = array(
				'text'  => $this->config->get('config_catalog_limit'),
				'value' => $this->config->get('config_catalog_limit'),
				'href'  => $this->url->link('product/search', $url . '&limit=' . $this->config->get('config_catalog_limit'))
				);
			
			$this->data['limits'][] = array(
				'text'  => 25,
				'value' => 25,
				'href'  => $this->url->link('product/search', $url . '&limit=25')
				);
			
			$this->data['limits'][] = array(
				'text'  => 50,
				'value' => 50,
				'href'  => $this->url->link('product/search', $url . '&limit=50')
				);
			
			$this->data['limits'][] = array(
				'text'  => 75,
				'value' => 75,
				'href'  => $this->url->link('product/search', $url . '&limit=75')
				);
			
			$this->data['limits'][] = array(
				'text'  => 100,
				'value' => 100,
				'href'  => $this->url->link('product/search', $url . '&limit=100')
				);
			
			$url = '';
			
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_tag'])) {
				$url .= '&filter_tag=' . urlencode(html_entity_decode($this->request->get['filter_tag'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_description'])) {
				$url .= '&filter_description=' . $this->request->get['filter_description'];
			}
			
			if (isset($this->request->get['filter_category_id'])) {
				$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
			}
			
			if (isset($this->request->get['filter_sub_category'])) {
				$url .= '&filter_sub_category=' . $this->request->get['filter_sub_category'];
			}
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}	
			
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			
			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}
			
			$pagination = new Pagination();
			$pagination->total = $product_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->text = $this->language->get('text_pagination');
			$pagination->url = $this->url->link('product/search', $url . '&page={page}');
			
			$this->data['pagination'] = $pagination->render();
		}	
		
		$this->data['filter_name'] = $filter_name;
		$this->data['filter_description'] = $filter_description;
		$this->data['filter_category_id'] = $filter_category_id;
		$this->data['filter_sub_category'] = $filter_sub_category;
		
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['limit'] = $limit;
		
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/product/search.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/product/search.tpl';
		} else {
			$this->template = 'default/template/product/search.tpl';
		}
		
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
			);
		
		$this->response->setOutput($this->render());
	}
	public function loadFilterProducts() {

		$this->load->model('catalog/catalog');
		// $this->load->model('catalog/smartsearch');
		$productFilter = $this->request->post['filter'];
		$productFilter['class_name'] = $this->request->post['class_name'];
		$filter_name = urldecode($this->request->post['filter_name']);

		// echo urldecode($filter_name);exit;
		$manufacturer_id = $productFilter['manufacturer_id'];
		$data = array(
				'filter_name'         => $filter_name, 
				'filter_tag'          => '', 
				'filter_description'  => '',
				'filter_category_id'  => '', 
				'filter_sub_category' => '', 
				'sort'                => 'p.sort_order',
				'order'               => 'ASC',
				'start'               => 0,
				'limit'               => 500
				);
				// Clear Thinking: Smart Search
			if ($this->config->get('smartsearch_status')) {
				$this->load->model('catalog/smartsearch');
				$data['return_total'] = true;
// Clear Thinking: Smart Search
				if (!$this->config->get('smartsearch_status')) {
				$product_total = $this->model_catalog_smartsearch->smartsearch($data);
}
				// end Smart Search
				$data['return_total'] = false;
				$results = $this->model_catalog_smartsearch->smartsearch($data);
			}
			$device_product_ids = array();

			foreach($results as $result)
			{
				$device_product_id = $this->model_catalog_catalog->getDeviceID($result['model']);
				if($manufacturer_id)
				{
					if(!$device_product_id)
					{
						continue;
					}
					else
					{
						// echo "SELECT device_manufacturer_id as id FROM inv_device_manufacturer WHERE manufacturer_id='".(int)$manufacturer_id."' and device_product_id='".(int)$device_product_id."'";exit;
						$check = $this->db->query("SELECT device_manufacturer_id as id FROM inv_device_manufacturer WHERE manufacturer_id='".(int)$manufacturer_id."' and device_product_id='".(int)$device_product_id."'");
						$check = $check->row;
						if(!$check)
						{
							continue;
						}
					}
				}

				$device_product_ids[] = $device_product_id;
			}
			$model_ids = explode(",", $productFilter['model_id']);
			$sub_models = array();
			foreach($model_ids as $model_id)
			{
					
					$sub_models[] = $this->model_catalog_catalog->loadSubModels($model_id);
				

			}
			foreach($sub_models as $_temp)
			{
				foreach($_temp as $sub)
				{
					$sub_models[] = $sub['id'];
				}
			}
			$sub_models = implode(",", $sub_models);
			$productFilter['model_id'] = $sub_models;
			// print_r($productFilter);exit;
			// print_r($device_product_ids);exit;

		$this->load->model('catalog/catalog');
		$this->load->model('tool/image');
		$this->load->model('catalog/product');

		$this->language->load('product/product');

		//$this->data['classes'] = $this->model_catalog_catalog->loadModelClasses($productFilter);

		$this->data['classes'] = array();
		$productFilter['main_class_id'] = 0;
		$productFilter['device_product_ids'] = implode(",", $device_product_ids);
		if(is_array($productFilter['class_id']))
		{
		$productsIds = $this->model_catalog_catalog->getOCProductsIds($this->model_catalog_catalog->filterProductsByDeviceIds($productFilter));
		// echo "<pre>";
		// print_r($productsIds);exit;
		$this->data['productsIds'] = $productsIds;
		$this->data['products'] = array();
		foreach ($productsIds as $product_id) {
			$result = $this->model_catalog_product->getProduct($product_id);
			if ($result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
				} else {
					$image = $this->model_tool_image->resize('data/image-coming-soon.jpg', $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
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

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price']);
				} else {
					$tax = false;
				}				

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}
				if ((float)$result['sale_price']) {
				$sale_price = $this->currency->format($this->tax->calculate($result['sale_price'], $result['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$sale_price = false;
			}

				$this->data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'quantity'        => $result['quantity'],
					'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..',
					'price'       => $price,
					'sale_price'  => $sale_price,
					'special'     => $special,
					'tax'         => $tax,
					'rating'      => $result['rating'],
					'attr'        => $this->model_catalog_catalog->getProductAttr($result['model']),
					'quality'        => $this->model_catalog_product->getProductQuality($result['model']),
					'class'        => $this->model_catalog_product->getProductClass($result['model']),
					'reviews'     => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
					'href'        => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'])
					);
			}
		}
	}
	else
	{
		$this->data['products'] = array();
	}

		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/catalog/products_ajax.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/catalog/products_ajax.tpl';
		} else {
			$this->template = 'default/template/catalog/products_ajax.tpl';
		}

		$json['products'] = $this->render();
		$json['classes'] = $this->data['classes'];
		$json['main_class_id'] = $this->data['classes'][0]['main_class_id'];

		echo json_encode($json);
		exit;

	}
	private static function compareByName($a, $b) {
		return strcmp($a["main_name"], $b["main_name"]);
	}
	private static function compareByName2($a, $b) {
		return strcmp($a["name"], $b["name"]);
	}
}
?>