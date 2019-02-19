<?php 
class ControllerCatalogRepairParts extends Controller {
	public function index() {
		$this->language->load('catalog/repair_parts');
		
		$this->load->model('catalog/catalog');
		
		$this->load->model('tool/image');

		$this->data['breadcrumbs'] = array ();

		$this->data['breadcrumbs'][] = array (
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
			);

		$this->data['breadcrumbs'][] = array (
			'text'      => $this->language->get('text_page'),
			'href'      => $this->url->link('catalog/repair_parts'),
			);

		$manufacturer_id = 0;
		$model_id = 0;

		if (isset($this->request->get['path'])) {
			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$manufacturer_id = ($parts[0])? $parts[0]: 0;
			
			$model_id = ($parts[1])? $parts[1]: 0;
			// echo $model_id;exit;

			$breadcrumbs = array ();
			// echo $manufacturer_id;exit;
			if ($manufacturer_id) {
				$manFilter = array (
					'record' => 1,
					'where' => array (
						array (
							'column' => 'manufacturer_id',
							'operator' => '=',
							'value' => $manufacturer_id
							)
						)
					);
				$info = $this->model_catalog_catalog->loadManufacturers($manFilter);
				// print_r($info);exit;
				$breadcrumbs[] = $info;
			}

			// echo $model_id;exit;
			if ($model_id) {
				$modFilter = array (
					'record' => 1,
					'where' => array (
						array (
							'column' => 'model_id',
							'operator' => '=',
							'value' => $model_id
							)
						)
					);
				$info = $this->model_catalog_catalog->loadModels($modFilter);
				// print_r($info);exit;
				$this->document->setTitle("Repair Parts for ".$info['name']);
				$this->data['heading_title'] = $info['name'];
				$breadcrumbs[] = $info;

			}


			foreach ($breadcrumbs as $breadcrumb) {

				if (!$path) {
					$path = $breadcrumb['id'];
				} else {
					$path .= '_' . $breadcrumb['id'];
				}

				$this->data['breadcrumbs'][] = array (
					'text'      => $breadcrumb['name'],
					'href'      => $this->url->link('catalog/repair_parts') . '&path=' . $path,
					);
				
			}

		}

		$this->data['module'] = ($this->request->get['route'])? explode('/', $this->request->get['route'])[1]: 'repair_parts';

		if ($model_id) {
			
			$this->load->model('catalog/product');

			$this->language->load('product/product');


			$this->data['submodels'] = $this->model_catalog_catalog->loadSubModels($model_id);
			$this->data['classes'] = $this->model_catalog_catalog->loadModelClasses(array('device_id' => $model_id, 'class_name' => 'Replacement Parts'));
			$mc_ids = array();

			foreach ($this->data['classes'] as $key => $class) {
				$mc_ids[]= $class['main_class_id'];
			}	

			$unique_mc_ids = array_unique($mc_ids);
			foreach ($unique_mc_ids as $key => $value) {
			$main_class_id = $value;	
			$productFilter = array(
				'manufacturer_id' => $manufacturer_id,
				'device_id' => $model_id,
				'main_class_id' => $main_class_id,
				);
			// print_r($this->model_catalog_catalog->filterProducts($productFilter));exit;
			$productsIds[] = $this->model_catalog_catalog->getOCProductsIds($this->model_catalog_catalog->filterProducts($productFilter),true);

			}

			// print_r($productsIds);exit;
			$this->data['productsIds'] = $productsIds;

			$_temp_productsIds = array();
			foreach ($productsIds as $p_id) {
				$to_sort_id = array();
		foreach ($p_id as $product_id) {
			$to_sort_id[] = $product_id;
		}
		$_temp_productsIds[] = $this->model_catalog_catalog->getSortedProductsByIds($to_sort_id);
	}
	$productsIds = array();
	foreach ($_temp_productsIds as $p_id) {
				
		foreach ($p_id as $product_id) {
			$productsIds[] = $product_id;
		}
	}
	// print_r($productsIds);exit;
	// print_r($productsIds);exit;
	// print_r($_temp_productsIds);exit;
		// $productsIds = $this->model_catalog_catalog->getSortedProductsByIds($to_sort_id);
		// $productsIds = $_temp_productsIds;
		// print_r($productsIds);exit;
	// $ids = array_column($communications, 'id');


			$this->data['manufacturer_id'] = $manufacturer_id;
			$this->data['main_class_id'] = $main_class_id;
			$this->data['device_id'] = $model_id;
			$this->data['products'] = array();
			$count = 1;

		
				
				foreach ($productsIds as $pid){
					if($count>40) break;

			
				$result = $this->model_catalog_product->getProduct($pid);
				if ($result) {
					if(!$result['visibility']  || $result['visibility'] == '1')
					{
						if($result['visibility'] != '0')
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
								'quantity'	  => (int)$result['quantity'],
								'thumb'       => $image,
								'name'        => $result['name'],
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
								'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
								);
						}
					}
				}
			$count++;
			}	
			

		} else if ($manufacturer_id) {

			$manFilter = array (
				'record' => 1,
				'where' => array (
					array (
						'column' => 'manufacturer_id',
						'operator' => '=',
						'value' => $manufacturer_id
						)
					)
				);
			$info = $this->model_catalog_catalog->loadManufacturers($manFilter);
			$this->data['info'] = array (
				'id' => $info['id'],
				'name' => $info['name'],
				'image' => ($info['image'])? $this->model_tool_image->resize($info['image'], 150, 150, true): $this->model_tool_image->resize('data/image-coming-soon.jpg', 150, 150),
				'description' => $info['description'],
				);
			$modFilter = array (
				'where' => array (
					array (
						'column' => 'manufacturer_id',
						'operator' => '=',
						'value' => $manufacturer_id
						)
					),
				'order_by'=>'device'
				);

			$rows = $this->model_catalog_catalog->loadModels($modFilter);

			$products = array ();
			foreach ($rows as $key => $row) {
				$products[] = array (
					'name' => $row['name'],
					'id' => $row['id'],
					'image' => ($row['image'])? $this->model_tool_image->resize($row['image'], 150, 150, true): $this->model_tool_image->resize('data/image-coming-soon.jpg', 150, 150),
					'href' => $this->url->link('catalog/repair_parts', 'path=' . $manufacturer_id . '_' . $row['id']),
					);
			}
			$this->data['products'] = $products;
			$this->children = array(
				'module/featured',
				);

			$this->document->setTitle("Repair Parts for ".$info['name']." Products");
		$this->data['heading_title'] = "Repair Parts for ".$info['name']." Products";

		} else if (!isset($this->request->get['path'])) {

			$this->data['info'] = array (
				'id' => 0,
				'name' => $this->language->get('text_page'),
				'image' => $this->model_tool_image->resize('data/image-coming-soon.jpg', 150, 150)
				);

			$manFilter = array (
				'order_by' => 'name',
				'order_type' => 'ASC',
				'where' => array (
					array (
						'column' => 'status',
						'operator' => '=',
						'value' => 1
						)
					)
				);

			$rows = $this->model_catalog_catalog->loadManufacturers($manFilter);

			$products = array ();
			foreach ($rows as $key => $row) {
				$products[] = array (
					'name' => $row['name'],
					'id' => $row['id'],
					'image' => ($row['image'])? $this->model_tool_image->resize($row['image'], 150, 150, true): $this->model_tool_image->resize('data/image-coming-soon.jpg', 150, 150),
					'href' => $this->url->link('catalog/repair_parts', 'path=' . $row['id']),
					);
			}
			$this->data['products'] = $products;

			$this->document->setTitle("Repair Parts for All Makes");
		$this->data['heading_title'] = "Repair Parts for All Makes";
		}

		if ($model_id) {
			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/catalog/products.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/catalog/products.tpl';
			} else {
				$this->template = 'default/template/catalog/products.tpl';
			}
		} else {
		


			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/catalog/repair_parts.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/catalog/repair_parts.tpl';
			} else {
				$this->template = 'default/template/catalog/repair_parts.tpl';
			}
		}
		
		$this->children = array (
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'module/featured',
			'common/header'
			);
		
		$this->response->setOutput($this->render());
		
	}


	public function loadFilterProducts() {

		$productFilter = $this->request->post['filter'];
		$manufacturer_id = $this->request->post['filter']['manufacturer_id'];
		$device_id = $this->request->post['filter']['device_id'];
		$productFilter['class_name'] = $this->request->post['class_name'];

		$this->load->model('catalog/catalog');
		$this->load->model('tool/image');
		$this->load->model('catalog/product');

		$this->language->load('product/product');
		// print_r($productFilter);exit;
		$this->data['classes'] = $this->model_catalog_catalog->loadModelClassesSingle($productFilter);
		$class_id = $productFilter['class_id'];
		$attrib_id = $productFilter['attrib_id'];
		// print_r($this->data['classes']);exit;
		$mc_ids = array();
			foreach ($this->data['classes'] as $key => $class) {
				$mc_ids[]= $class['main_class_id'];
			}	
			$unique_mc_ids = array_unique($mc_ids);
			foreach ($unique_mc_ids as $key => $value) {
			$main_class_id = $value;	
			$productFilter = array(
				'manufacturer_id' => $manufacturer_id,
				'device_id' => $productFilter['device_id'],
				'main_class_id' => $main_class_id,
				'class_id'		=> $class_id,
				'attrib_id'		=> $attrib_id,
				'model_id'		=> $productFilter['model_id']
				);
			$productsIds[] = $this->model_catalog_catalog->getOCProductsIds($this->model_catalog_catalog->filterProducts($productFilter,false,$this->request->post['page']),true);
			}
			$_temp_productsIds = array();
			foreach ($productsIds as $p_id) {
				$to_sort_id = array();
		foreach ($p_id as $product_id) {
			$to_sort_id[] = $product_id;
		}
		$_temp_productsIds[] = $this->model_catalog_catalog->getSortedProductsByIds($to_sort_id);
	}
	$productsIds = array();
	foreach ($_temp_productsIds as $p_id) {
				
		foreach ($p_id as $product_id) {
			$productsIds[] = $product_id;
		}
	}

		// $productsIds = $this->model_catalog_catalog->getSortedProductsByIds($to_sort_id);

		//$productFilter['main_class_id'] = $this->data['classes'][0]['main_class_id'];
		//$productsIds = $this->model_catalog_catalog->getOCProductsIds($this->model_catalog_catalog->filterProducts($productFilter));
		$this->data['productsIds'] = $productsIds;
		$this->data['products'] = array();
		// print_r(implode(",",$productsIds));exit;
		$end_limit =40;
		$start_limit = ((int)$this->request->post['page'] - 1)*$end_limit;
		$last_limit = $start_limit + $end_limit;
		$count = 0;

		foreach ($productsIds as $product_id) {
			if($count<$start_limit or  $count>$last_limit )
			{
				// echo $start_limit.'-'.$last_limit;exit;
			$count++;
			 continue;
			}
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
					'quantity'	  => $result['quantity'],
					'name'        => $result['name'],
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
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
					);
			}
			$count++;
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

	public function loadAttributes() {

		$this->load->model('catalog/catalog');

		$filter = $this->request->post['filter'];
		$filter['class_id'] = $this->request->post['class'];

		$json['attributes'] = $this->model_catalog_catalog->loadClassAttr($filter);

		echo json_encode($json);
		exit;

	}
}
?>