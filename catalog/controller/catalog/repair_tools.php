<?php 
class ControllerCatalogRepairTools extends Controller {
	public function index() {
		$this->language->load('catalog/repair_tools');

		$this->load->model('catalog/catalog');
		
		$this->load->model('tool/image');
		$this->document->setTitle("Repair Tools");
		$this->data['heading_title'] = "Repair Tools";

		$this->data['breadcrumbs'] = array ();

		$this->data['breadcrumbs'][] = array (
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
			);

		$this->data['breadcrumbs'][] = array (
			'text'      => $this->language->get('text_page'),
			'href'      => $this->url->link('catalog/repair_tools'),
			);

		$main_class_id = 0;

		if (isset($this->request->get['path'])) {
			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$main_class_id = ($parts[0])? $parts[0]: 0;

			$breadcrumbs = array ();

			if ($main_class_id) {
				$classFilter = array (

					'record' => 1,
					'where' => array (
						array (
							'column' => 'inv_classification.id',
							'operator' => '=',
							'value' => $main_class_id
							)
						)
					);

				$info = $this->model_catalog_catalog->loadClassification($classFilter);
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
					'href'      => $this->url->link('catalog/repair_tools', 'path=' . $path),
					);

			}

		}

		$this->data['module'] = ($this->request->get['route'])? explode('/', $this->request->get['route'])[1]: 'repair_tools';

		if ($main_class_id) {
			
			$this->load->model('catalog/product');

			$this->language->load('product/product');

			$classFilter = array (
				'order_by' => 'main_class',
				'order_type' => 'ASC',
				'where' => array (
					array (
						'column' => 'inv_classification.status',
						'operator' => '=',
						'value' => '1'
						),
					array (
						'column' => 'main_class_id',
						'operator' => '=',
						'value' => 5
						)
					)
				);

			$this->data['classes'] = $this->model_catalog_catalog->loadClassification($classFilter);

			$productFilter = array(
				'main_class_id' => ($main_class_id)
				);
			$productsIds = $this->model_catalog_catalog->getOCProductsIds($this->model_catalog_catalog->filterProducts($productFilter));


			$this->data['productsIds'] = $productsIds;

			
		$productsIds = $this->model_catalog_catalog->getSortedProductsByIds($productsIds);

			if(isset($this->request->get['class_id']))
			{

			$this->data['class_id'] = (int)$this->request->get['class_id'];
			}
			$this->data['products'] = array();
			$count = 1;
			foreach ($productsIds as $product_id) {
				if($count>40) break;
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
						'reviews'     => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
						'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
						);
				}
				$count++;
			}


		} else if (!isset($this->request->get['path'])) {

			$this->data['info'] = array (
				'id' => 0,
				'name' => $this->language->get('text_page'),
				'image' => $this->model_tool_image->resize('data/image-coming-soon.jpg', 150, 150)
				);

			$classFilter = array (
				'order_by' => 'main_class',
				'order_type' => 'DESC',
				'where' => array (
					array (
						'column' => 'inv_classification.status',
						'operator' => '=',
						'value' => '1'
						),
					array (
						'column' => 'main_class_id',
						'operator' => '=',
						'value' => 5
						)
					)
				);

			$rows = $this->model_catalog_catalog->loadClassification($classFilter);

			$products = array ();
			foreach ($rows as $key => $row) {
				$products[] = array (
					'name' => $row['name'],
					'id' => $row['id'],
					'image' => ($row['image'])? $this->model_tool_image->resize($row['image'], 150, 150, true): $this->model_tool_image->resize('data/image-coming-soon.jpg', 150, 150),
					'href' => $this->url->link('catalog/repair_tools', 'path=' . $row['id']),
					);
			}
			$this->data['products'] = $products;
		}

		if ($main_class_id) {
			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/catalog/products.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/catalog/products.tpl';
			} else {
				$this->template = 'default/template/catalog/products.tpl';
			}
		} else {			
			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/catalog/repair_tools.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/catalog/repair_tools.tpl';
			} else {
				$this->template = 'default/template/catalog/repair_tools.tpl';	
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
		$productFilter['class_name'] = $this->request->post['class_name'];

		$this->load->model('catalog/catalog');
		$this->load->model('tool/image');
		$this->load->model('catalog/product');

		$this->language->load('product/product');

		$this->data['classes'] = $this->model_catalog_catalog->loadModelClasses($productFilter);
		$productFilter['main_class_id'] = $this->data['classes'][0]['main_class_id'];
		if(is_array($productFilter['class_id']))
		{


		$productsIds = $this->model_catalog_catalog->getOCProductsIds($this->model_catalog_catalog->filterProducts($productFilter,false,$this->request->post['page']));
		$this->data['productsIds'] = $productsIds;
		$productsIds = $this->model_catalog_catalog->getSortedProductsByIds($productsIds);
		$this->data['products'] = array();

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
					'reviews'     => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
					);
			}
			$count++;
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