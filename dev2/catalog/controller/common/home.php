<?php  
class ControllerCommonHome extends Controller {

	const ALL_PRODUCTS_V2 = true;

	public function next_page() {
		$this->language->load('product/category');

		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$allProducts = 1;

		if (isset($this->request->get['filter'])) {
			$filter = $this->request->get['filter'];
		} else {
			$filter = '';
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
			$limit = $this->config->get('config_catalog_limit');
		}

		if (isset($this->request->get['path'])) {
			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = array_pop($parts);
		} else {
			$category_id = 0;
		}
//echo $category_id;
		$category_info = $this->model_catalog_category->getCategory($category_id);

		if ($category_info ||  $allProducts ) {
			if ($category_info) {
				$this->document->setTitle($category_info['name']);
				$this->data['heading_title'] = $category_info['name'];
			} else {
				$this->document->setTitle('Dinkum Deals');
				$this->data['heading_title'] = 'Dinkum Deals';
			}

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
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

			$this->data['text_tax'] = $this->language->get('text_tax');

			$this->data['button_cart'] = $this->language->get('button_cart');
			$this->data['button_wishlist'] = $this->language->get('button_wishlist');
			$this->data['button_compare'] = $this->language->get('button_compare');

			$this->data['cit_status'] = $this->config->get('custom_image_titles_status');

			$this->data['products'] = array();

			$data = array(
				'filter_category_id' => $category_id,
				'filter_filter'      => $filter,
				'sort'               => $sort,
				'order'              => $order,
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit
				);

			$results = $this->model_catalog_product->getProducts($data);

			foreach ($results as $result) {
//echo "<pre>";	print_r($result);		echo "</pre>";	echo "<br/>";
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
				} else {
					$image = false;
				}


				$images = $this->model_catalog_product->getProductImages($result['product_id']);

				if(isset($images[0]['image']) && !empty($images[0]['image'])){
					$images =$images[0]['image'];
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

				if($result['price'] == 0) {
					$result['price'] = 0.1;
				}

				$this->data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'rating'      => $result['rating'],
					'reviews'     => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id']),
					'in_cart'	  => (isset($this->session->data['cart'][$result['product_id']])?true:false),
					// for swap image
					'thumb_swap'  => $this->model_tool_image->resize($images, $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height')), 
					//
					// for saving percentage
					'saving'	=> round((($result['price'] - $result['special'])/$result['price'])*100, 0),
					//
					);

			}
			$this->data['es_status'] = $this->config->get('es_status');
			$this->data['use_more'] = $this->config->get('es_use_more');
			$this->data['use_more_after'] = $this->config->get('es_use_more_after');
			$this->data['use_back_top'] = $this->config->get('es_use_back_to_top');
			$this->data['use_sticky_footer'] = $this->config->get('es_use_sticky_footer');

			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/product/category_products.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/product/category_products.tpl';
			} else {
				$this->template = 'default/template/product/category_products.tpl';
			}

			$resp = array("success" => 1, "data" => $this->render());
//echo "<pre>";	print_r($resp);		echo "</pre>";		die();
			$this->response->setOutput(json_encode($resp));
		}
	}

	public function index() {

	// echo "<pre>";
	// print_r($_SESSION);exit;		
		if (isset($this->request->get['temp_theme'])) {
			$this->session->data['temp_theme'] = $this->request->get['temp_theme'];
		} else if ($this->request->get['temp_theme'] == '0') {
			unset($this->session->data['temp_theme']);
		}
		$this->document->setTitle($this->config->get('config_title'));
		$this->document->setDescription($this->config->get('config_meta_description'));
		$this->load->model('setting/setting');
		$this->data['heading_title'] = $this->config->get('config_title');

		$this->session->data['is_home_page'] = '1';
		$this->language->load('product/category');

		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$this->load->model('tool/image'); 
		$this->data['main_banner'] = $this->model_tool_image->resize('new_site/home/home-bg.jpg', 960, 391); // main banner

		$sub_banners = array('new_site/home/home-slide.jpg');
		foreach($sub_banners as $sub_banner)
		{
			$this->data['sub_banners'][] = $this->model_tool_image->resize('new_site/home/home-slide.jpg', 600, 308); // main banner
		}
		// echo $this->data['main_banner'];exit;
		//$allProducts = (array_key_exists('shop', $this->request->get) && ($this->request->get['shop'] == 'shop'));
		

	

			$this->data['text_refine'] = $this->language->get('text_refine');
			$this->data['text_empty'] = $this->language->get('text_empty');			
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

			$this->data['button_cart'] = $this->language->get('button_cart');
			$this->data['button_wishlist'] = $this->language->get('button_wishlist');
			$this->data['button_compare'] = $this->language->get('button_compare');
			$this->data['button_continue'] = $this->language->get('button_continue');

			



		


		
			

			$this->data['continue'] = $this->url->link('common/home');

			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/common/home.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/common/home.tpl';
			} else {
				$this->template = 'default/template/common/home.tpl';
			}

			$this->document->addStyle('catalog/view/theme/' . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')). '/stylesheet/products.css');
			// $home_products = $this->model_setting_setting->getSetting('home_products');
			// $hproducts1 = explode(",",$home_products['home_products1']);
			// $hproducts2 = explode(",",$home_products['home_products2']);
			// $hproducts3 = explode(",",$home_products['home_products3']);
			// // print_r($hproducts3);exit;
			// $products_home2 = array();
			// $products_home3 = array();
			
			// $https = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) ? 1 : 0;
			
			
			// $ph1_cache_name = 'ph1.' . $https . (int)$this->currency->getCode() . '.' . $this->customer->getCustomerGroupId() . '.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id');
			// if ($this->cache->get($ph1_cache_name)) { 
			// 	$products_home1 = $this->cache->get($ph1_cache_name);
			// } else { 
			// 	$products_home1 = array();			
			// 	$z=0;
			
			// foreach($hproducts1 as $hproduct) {
			// 	if($hproduct=='') continue;
			// 	$result = $this->model_catalog_product->getProduct($hproduct);
				
			// 	if ($result['image']) {
			// 		$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
			// 	} else {
			// 		$image = false;
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
			// 	if ((float)$result['sale_price']) {
			// 		$sale_price = $this->currency->format($this->tax->calculate($result['sale_price'], $result['tax_class_id'], $this->config->get('config_tax')));
			// 	} else {
			// 		$sale_price = false;
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
								
			// 	$products_home1[$z] = array(
			// 		'product_id'  => $result['product_id'],
			// 		'thumb'       => $image,
			// 		'name'        => $result['name'],
			// 		'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..',
			// 		'price'       => $price,
			// 		'sale_price'  => $sale_price,
			// 		'quantity'		=> $result['quantity'],
			// 		'special'     => $special,
			// 		'tax'         => $tax,
			// 		'rating'      => $result['rating'],
			// 		'reviews'     => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
			// 		'href'        => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'])
			// 	);
			
				
			// 	$z++;
			// }
			
			// $this->cache->set($ph1_cache_name, $products_home1);
			// }
			
			// $ph2_cache_name = 'ph2.' . $https . (int)$this->currency->getCode() . '.' . $this->customer->getCustomerGroupId() . '.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id');
			// if ($this->cache->get($ph2_cache_name)) { 
			// 	$products_home2 = $this->cache->get($ph2_cache_name);
			// } else { 
			// 	$products_home2 = array();			
			// 	$z=0;			

			// foreach($hproducts2 as $hproduct) {
			// 	if($hproduct=='') continue;
			// 	$result = $this->model_catalog_product->getProduct($hproduct);
				
			// 	if(!$result) continue;

			// 	if ($result['image']) {
			// 		$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
			// 	} else {
			// 		$image = false;
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
			// 	if ((float)$result['sale_price']) {
			// 		$sale_price = $this->currency->format($this->tax->calculate($result['sale_price'], $result['tax_class_id'], $this->config->get('config_tax')));
			// 	} else {
			// 		$sale_price = false;
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
								
			// 	$products_home2[$z] = array(
			// 		'product_id'  => $result['product_id'],
			// 		'thumb'       => $image,
			// 		'in_cart'	  => (isset($this->session->data['cart'][$result['product_id']])?true:false),
			// 		'name'        => $result['name'],
			// 		'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..',
			// 		'price'       => $price,
			// 		'sale_price'  => $sale_price,
			// 		'quantity'		=> $result['quantity'],
			// 		'special'     => $special,
			// 		'tax'         => $tax,
			// 		'rating'      => $result['rating'],
			// 		'reviews'     => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
			// 		'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
			// 	);
			
				
			// 	$z++;
			// }
			
			// $this->cache->set($ph2_cache_name, $products_home2);
			// }
			
			// $ph3_cache_name = 'ph3.' . $https . (int)$this->currency->getCode() . '.' . $this->customer->getCustomerGroupId() . '.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id');
			// if ($this->cache->get($ph3_cache_name)) { 
			// 	$products_home3 = $this->cache->get($ph3_cache_name);
			// } else { 
			// 	$products_home3 = array();			
			// 	$z=0;
			
			// foreach($hproducts3 as $hproduct)
			// {
			// 	if($hproduct=='') continue;
			// 	$result = $this->model_catalog_product->getProduct($hproduct);
			// 	// print_r($result);exit;
			// 	if(!$result) continue;

			// 	if ($result['image']) {
			// 		$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
			// 	} else {
			// 		$image = false;
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
			// 	if ((float)$result['sale_price']) {
			// 		$sale_price = $this->currency->format($this->tax->calculate($result['sale_price'], $result['tax_class_id'], $this->config->get('config_tax')));
			// 	} else {
			// 		$sale_price = false;
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
								
			// 	$products_home3[$z] = array(
			// 		'product_id'  => $result['product_id'],
			// 		'thumb'       => $image,
			// 		'in_cart'	  => (isset($this->session->data['cart'][$result['product_id']])?true:false),
			// 		'name'        => $result['name'],
			// 		'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..',
			// 		'price'       => $price,
			// 		'sale_price'  => $sale_price,
			// 		'quantity'		=> $result['quantity'],
			// 		'special'     => $special,
			// 		'tax'         => $tax,
			// 		'rating'      => $result['rating'],
			// 		'reviews'     => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
			// 		'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
			// 	);
			
				
			// 	$z++;
			// }
			// 	$this->cache->set($ph3_cache_name, $products_home3);
			// }	
			// 2.0 our selection box code
			//if($this->session->data['temp_theme']=='ppusa2.0')
			//{
			$https = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) ? 1 : 0;
			$customer_group_id  = method_exists($this->customer, "getCustomerGroupId") ? $this->customer->getCustomerGroupId() : $this->customer->getGroupId();
			$currency_id        = method_exists($this->currency, "getCode") ? $this->currency->getCode() : $this->config->get('config_currency');
			$cache_name         = 'home.manufacturers.nav' . '.' . $https . $currency_id . '.' . $customer_group_id . '.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id');
			$this->data['manufacturers'] = $this->cache->get($cache_name);
			
			if ($this->data['manufacturers']) { } else {
				
				$this->load->model('catalog/catalog');
				$manufacturers = array(
					'Apple'=>array('id'=>2,'image'=>'catalog/view/theme/ppusa2.0/images/home/services/apple.jpg'),
					'Samsung'=>array('id'=>10,'image'=>'catalog/view/theme/ppusa2.0/images/home/services/samsung.jpg'),
					'LG'=>array('id'=>6,'image'=>'catalog/view/theme/ppusa2.0/images/home/services/lg.jpg'),
					'HTC'=>array('id'=>4,'image'=>'catalog/view/theme/ppusa2.0/images/home/services/htc.jpg'),
					'Blackberry'=>array('id'=>3,'image'=>'catalog/view/theme/ppusa2.0/images/home/services/blackberry.jpg'),
					'Motorola'=>array('id'=>7,'image'=>'catalog/view/theme/ppusa2.0/images/home/services/nexus.jpg'),
					'Huawei'=>array('id'=>5,'image'=>'catalog/view/theme/ppusa2.0/images/home/services/huawei.jpg'),
					'Sony'=>array('id'=>11,'image'=>'catalog/view/theme/ppusa2.0/images/home/services/sony.jpg'),
					'Nokia'=>array('id'=>8,'image'=>'catalog/view/theme/ppusa2.0/images/home/services/nokia.jpg'),
					'Pantech'=>array('id'=>9,'image'=>'catalog/view/theme/ppusa2.0/images/home/services/pantech.jpg'),

					);

				/*Apple Mbile and Tabs*/

				$modFilter = array (
					'limit' => 4,
					'order_by' => 'model_id',
					'order_type' => 'DESC',
					'where' => array (
						array (
							'column' => 'manufacturer_id',
							'operator' => '=',
							'value' => $manufacturers['Apple']['id']
							),
						array (
							'column' => 'device',
							'operator' => 'like',
							'value' => 'ipho%'
							)
						)
					);

				$manufacturers['Apple']['href'] = $this->url->link('catalog/repair_parts', 'path=' . $manufacturers['Apple']['id']) ;
				$mobileRows = $this->model_catalog_catalog->loadModels($modFilter);
				$mobile = array();
				foreach ($mobileRows as $row) {
					$mobile[] = array (
						'href' => $this->url->link('catalog/repair_parts', 'path=' . $manufacturers['Apple']['id'] . '_' . $row['id']) ,
						'name' => $row['name']
						);
				}

				$mobile[] = array (
					'href' => $this->url->link('catalog/repair_parts', 'path=' . $manufacturers['Apple']['id']) ,
					'name' => 'More'
					);

				$manufacturers['Apple']['phones'] = $mobile;

				$modFilter = array (
					'limit' => 4,
					'order_by' => 'model_id',
					'order_type' => 'DESC',
					'where' => array (
						array (
							'column' => 'manufacturer_id',
							'operator' => '=',
							'value' => $manufacturers['Apple']['id']
							),
						array (
							'column' => 'device',
							'operator' => 'like',
							'value' => 'ipa%'
							)
						)
					);
				$tabRows = $this->model_catalog_catalog->loadModels($modFilter);
				$tab = array();
				foreach ($tabRows as $row) {
					$tab[] = array (
						'href' => $this->url->link('catalog/repair_parts', 'path=' . $manufacturers['Apple']['id'] . '_' . $row['id']) ,
						'name' => $row['name']
						);
				}

				$tab[] = array (
					'href' => $this->url->link('catalog/repair_parts', 'path=' . $manufacturers['Apple']['id']) ,
					'name' => 'More'
					);

				$manufacturers['Apple']['tabs'] = $tab;

				$modFilter = array (
					'limit' => 2,
					'order_by' => 'model_id',
					'order_type' => 'DESC',
					'where' => array (
						array (
							'column' => 'manufacturer_id',
							'operator' => '=',
							'value' => $manufacturers['Apple']['id']
							),
						array (
							'column' => 'device',
							'operator' => 'like',
							'value' => 'ipod%'
							)


						)
					);
				$tabRows = $this->model_catalog_catalog->loadModels($modFilter);
				$others = array();
				foreach ($tabRows as $row) {
					$others[] = array (
						'href' => $this->url->link('catalog/repair_parts', 'path=' . $manufacturers['Apple']['id'] . '_' . $row['id']) ,
						'name' => $row['name']
						);
				}

				$modFilter = array (
					'limit' => 2,
					'order_by' => 'model_id',
					'order_type' => 'DESC',
					'where' => array (
						array (
							'column' => 'manufacturer_id',
							'operator' => '=',
							'value' => $manufacturers['Apple']['id']
							),
						array (
							'column' => 'device',
							'operator' => 'like',
							'value' => 'watch%'
							)


						)
					);
				$tabRows = $this->model_catalog_catalog->loadModels($modFilter);
				$others2 = array();
				foreach ($tabRows as $row) {
					$others2[] = array (
						'href' => $this->url->link('catalog/repair_parts', 'path=' . $manufacturers['Apple']['id'] . '_' . $row['id']) ,
						'name' => $row['name']
						);
				}

				$others = array_merge($others,$others2);

				$others[] = array (
					'href' => $this->url->link('catalog/repair_parts', 'path=' . $manufacturers['Apple']['id']) ,
					'name' => 'More'
					);

				$manufacturers['Apple']['others'] = $others;
				

				/*Samsung Mobile and Tabs*/

				$modFilter = array (
					'limit' => 4,
					'order_by' => 'model_id',
					'order_type' => 'DESC',
					'where' => array (
						array (
							'column' => 'manufacturer_id',
							'operator' => '=',
							'value' => $manufacturers['Samsung']['id']
							),
						array (
							'column' => 'device',
							'operator' => 'Not like',
							'value' => '%tab%'
							)
						)
					);
				$manufacturers['Samsung']['href'] = $this->url->link('catalog/repair_parts', 'path=' . $manufacturers['Samsung']['id']) ;
				$mobileRows = $this->model_catalog_catalog->loadModels($modFilter);
				$mobile = array();
				foreach ($mobileRows as $row) {
					$mobile[] = array (
						'href' => $this->url->link('catalog/repair_parts', 'path=' . $manufacturers['Samsung']['id'] . '_' . $row['id']) ,
						'name' => $row['name']
						);
				}

				$mobile[] = array (
					'href' => $this->url->link('catalog/repair_parts', 'path=' . $manufacturers['Samsung']['id']) ,
					'name' => 'More'
					);

				$manufacturers['Samsung']['phones'] = $mobile;

				$modFilter = array (
					'limit' => 4,
					'order_by' => 'model_id',
					'order_type' => 'DESC',
					'where' => array (
						array (
							'column' => 'manufacturer_id',
							'operator' => '=',
							'value' => $manufacturers['Samsung']['id']
							),
						array (
							'column' => 'device',
							'operator' => 'like',
							'value' => '%tab%'
							)
						)
					);
				$tabRows = $this->model_catalog_catalog->loadModels($modFilter);
				$tab = array();
				foreach ($tabRows as $row) {
					$tab[] = array (
						'href' => $this->url->link('catalog/repair_parts', 'path=' . $manufacturers['Samsung']['id'] . '_' . $row['id']) ,
						'name' => $row['name']
						);
				}

				$tab[] = array (
					'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['Samsung']['id']) ,
					'name' => 'More'
					);

				$manufacturers['Samsung']['tabs'] = $tab;

				$modFilter = array (
					'limit' => 4,
					'order_by' => 'model_id',
					'order_type' => 'DESC',
					'where' => array (
						array (
							'column' => 'manufacturer_id',
							'operator' => '=',
							'value' => $manufacturers['Samsung']['id']
							),
						array (
							'column' => 'device',
							'operator' => 'like',
							'value' => 'gear%'
							)


						)
					);
				$tabRows = $this->model_catalog_catalog->loadModels($modFilter);
				$others = array();
				foreach ($tabRows as $row) {
					$others[] = array (
						'href' => $this->url->link('catalog/repair_parts', 'path=' . $manufacturers['Samsung']['id'] . '_' . $row['id']) ,
						'name' => $row['name']
						);
				}

					$others[] = array (
					'href' => $this->url->link('catalog/repair_parts', 'path=' . $manufacturers['Samsung']['id']) ,
					'name' => 'More'
					);

				$manufacturers['Samsung']['others'] = $others;

				/*LG Mobile and Tabs*/

				$modFilter = array (
					'limit' => 4,
					'order_by' => 'model_id',
					'order_type' => 'DESC',
					'where' => array (
						array (
							'column' => 'manufacturer_id',
							'operator' => '=',
							'value' => $manufacturers['LG']['id']
							),
						array (
							'column' => 'device',
							'operator' => 'Not like',
							'value' => '%pad%'
							)
						)
					);

				


				$manufacturers['LG']['href'] = $this->url->link('catalog/repair_parts','path=' . $manufacturers['LG']['id']) ;
				$mobileRows = $this->model_catalog_catalog->loadModels($modFilter);
				$mobile = array();
				foreach ($mobileRows as $row) {
					$mobile[] = array (
						'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['LG']['id'] . '_' . $row['id']) ,
						'name' => $row['name']
						);
				}

				$mobile[] = array (
					'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['LG']['id']) ,
					'name' => 'More'
					);

				$manufacturers['LG']['phones'] = $mobile;

				$modFilter = array (
					'limit' => 4,
					'order_by' => 'model_id',
					'order_type' => 'DESC',
					'where' => array (
						array (
							'column' => 'manufacturer_id',
							'operator' => '=',
							'value' => $manufacturers['LG']['id']
							),
						array (
							'column' => 'device',
							'operator' => 'like',
							'value' => '%pad%'
							)
						)
					);
				$tabRows = $this->model_catalog_catalog->loadModels($modFilter);
				$tab = array();
				foreach ($tabRows as $row) {
					$tab[] = array (
						'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['LG']['id'] . '_' . $row['id']) ,
						'name' => $row['name']
						);
				}

				$tab[] = array (
					'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['LG']['id']) ,
					'name' => 'More'
					);

				$manufacturers['LG']['tabs'] = $tab;

				/*HTC Mobile and Tabs*/

				$modFilter = array (
					'limit' => 4,
					'order_by' => 'model_id',
					'order_type' => 'DESC',
					'where' => array (
						array (
							'column' => 'manufacturer_id',
							'operator' => '=',
							'value' => $manufacturers['HTC']['id']
							),
						array (
							'column' => 'device',
							'operator' => 'Not REGEXP',
							'value' => 'Evo|Flyer|jetstream'
							)
						)
					);
				$manufacturers['HTC']['href'] = $this->url->link('catalog/repair_parts','path=' . $manufacturers['HTC']['id']) ;
				$mobileRows = $this->model_catalog_catalog->loadModels($modFilter);
				$mobile = array();
				foreach ($mobileRows as $row) {
					$mobile[] = array (
						'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['HTC']['id'] . '_' . $row['id']) ,
						'name' => $row['name']
						);
				}

				$mobile[] = array (
					'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['HTC']['id']) ,
					'name' => 'More'
					);

				$manufacturers['HTC']['phones'] = $mobile;

				$modFilter = array (
					'limit' => 4,
					'order_by' => 'model_id',
					'order_type' => 'DESC',
					'where' => array (
						array (
							'column' => 'manufacturer_id',
							'operator' => '=',
							'value' => $manufacturers['HTC']['id']
							),
						array (
							'column' => 'device',
							'operator' => 'REGEXP',
							'value' => 'Evo|Flyer|jetstream'
							)
						)
					);
				$tabRows = $this->model_catalog_catalog->loadModels($modFilter);
				$tab = array();
				foreach ($tabRows as $row) {
					$tab[] = array (
						'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['HTC']['id'] . '_' . $row['id']) ,
						'name' => $row['name']
						);
				}

				$tab[] = array (
					'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['HTC']['id']) ,
					'name' => 'More'
					);

				$manufacturers['HTC']['tabs'] = $tab;

				/*Blackberry Mobile and Tabs*/

				$modFilter = array (
					'limit' => 4,
					'order_by' => 'model_id',
					'order_type' => 'DESC',
					'where' => array (
						array (
							'column' => 'manufacturer_id',
							'operator' => '=',
							'value' => $manufacturers['Blackberry']['id']
							),
						array (
							'column' => 'device',
							'operator' => 'Not LIKE',
							'value' => '%Playbook%'
							)
						)
					);
				$manufacturers['Blackberry']['href'] = $this->url->link('catalog/repair_parts','path=' . $manufacturers['Blackberry']['id']) ;
				$mobileRows = $this->model_catalog_catalog->loadModels($modFilter);
				$mobile = array();
				foreach ($mobileRows as $row) {
					$mobile[] = array (
						'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['Blackberry']['id'] . '_' . $row['id']) ,
						'name' => $row['name']
						);
				}

				$mobile[] = array (
					'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['Blackberry']['id']) ,
					'name' => 'More'
					);

				$manufacturers['Blackberry']['phones'] = $mobile;

				$modFilter = array (
					'limit' => 4,
					'order_by' => 'model_id',
					'order_type' => 'DESC',
					'where' => array (
						array (
							'column' => 'manufacturer_id',
							'operator' => '=',
							'value' => $manufacturers['Blackberry']['id']
							),
						array (
							'column' => 'device',
							'operator' => 'LIKE',
							'value' => '%Playbook%'
							)
						)
					);
				$tabRows = $this->model_catalog_catalog->loadModels($modFilter);
				$tab = array();
				foreach ($tabRows as $row) {
					$tab[] = array (
						'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['Blackberry']['id'] . '_' . $row['id']) ,
						'name' => $row['name']
						);
				}

				$tab[] = array (
					'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['Blackberry']['id']) ,
					'name' => 'More'
					);

				$manufacturers['Blackberry']['tabs'] = $tab;

				/*Motorola Mobile and Tabs*/

				$modFilter = array (
					'limit' => 4,
					'order_by' => 'model_id',
					'order_type' => 'DESC',
					'where' => array (
						array (
							'column' => 'manufacturer_id',
							'operator' => '=',
							'value' => $manufacturers['Motorola']['id']
							),
						array (
							'column' => 'device',
							'operator' => 'Not LIKE',
							'value' => '%Xoom%'
							)
						)
					);
				$manufacturers['Motorola']['href'] = $this->url->link('catalog/repair_parts','path=' . $manufacturers['Motorola']['id']) ;
				$mobileRows = $this->model_catalog_catalog->loadModels($modFilter);
				$mobile = array();
				foreach ($mobileRows as $row) {
					$mobile[] = array (
						'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['Motorola']['id'] . '_' . $row['id']) ,
						'name' => $row['name']
						);
				}

				$mobile[] = array (
					'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['Motorola']['id']) ,
					'name' => 'More'
					);

				$manufacturers['Motorola']['phones'] = $mobile;

				$modFilter = array (
					'limit' => 4,
					'order_by' => 'model_id',
					'order_type' => 'DESC',
					'where' => array (
						array (
							'column' => 'manufacturer_id',
							'operator' => '=',
							'value' => $manufacturers['Motorola']['id']
							),
						array (
							'column' => 'device',
							'operator' => 'LIKE',
							'value' => '%Xoom%'
							)
						)
					);
				$tabRows = $this->model_catalog_catalog->loadModels($modFilter);
				$tab = array();
				foreach ($tabRows as $row) {
					$tab[] = array (
						'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['Motorola']['id'] . '_' . $row['id']) ,
						'name' => $row['name']
						);
				}

				$tab[] = array (
					'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['Motorola']['id']) ,
					'name' => 'More'
					);

				$manufacturers['Motorola']['tabs'] = $tab;

				/*Huawei Mobile and Tabs*/

				$modFilter = array (
					'limit' => 4,
					'order_by' => 'model_id',
					'order_type' => 'DESC',
					'where' => array (
						array (
							'column' => 'manufacturer_id',
							'operator' => '=',
							'value' => $manufacturers['Huawei']['id']
							)
						)
					);
				$manufacturers['Huawei']['href'] = $this->url->link('catalog/repair_parts','path=' . $manufacturers['Huawei']['id']) ;
				$mobileRows = $this->model_catalog_catalog->loadModels($modFilter);
				$mobile = array();
				foreach ($mobileRows as $row) {
					$mobile[] = array (
						'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['Huawei']['id'] . '_' . $row['id']) ,
						'name' => $row['name']
						);
				}

				$mobile[] = array (
					'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['Huawei']['id']) ,
					'name' => 'More'
					);

				$manufacturers['Huawei']['phones'] = $mobile;

				$tab = array();

				$tab[] = array (
					'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['Huawei']['id']) ,
					'name' => 'More'
					);

				$manufacturers['Huawei']['tabs'] = $tab;

				/*Sony Mobile and Tabs*/

				$modFilter = array (
					'limit' => 4,
					'order_by' => 'model_id',
					'order_type' => 'DESC',
					'where' => array (
						array (
							'column' => 'manufacturer_id',
							'operator' => '=',
							'value' => $manufacturers['Sony']['id']
							),
						array (
							'column' => 'device',
							'operator' => 'Not LIKE',
							'value' => '%Tablet%'
							)
						)
					);
				$manufacturers['Sony']['href'] = $this->url->link('catalog/repair_parts','path=' . $manufacturers['Sony']['id']) ;
				$mobileRows = $this->model_catalog_catalog->loadModels($modFilter);
				$mobile = array();
				foreach ($mobileRows as $row) {
					$mobile[] = array (
						'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['Sony']['id'] . '_' . $row['id']) ,
						'name' => $row['name']
						);
				}

				$mobile[] = array (
					'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['Sony']['id']) ,
					'name' => 'More'
					);

				$manufacturers['Sony']['phones'] = $mobile;

				$modFilter = array (
					'limit' => 4,
					'order_by' => 'model_id',
					'order_type' => 'DESC',
					'where' => array (
						array (
							'column' => 'manufacturer_id',
							'operator' => '=',
							'value' => $manufacturers['Sony']['id']
							),
						array (
							'column' => 'device',
							'operator' => 'LIKE',
							'value' => '%Tablet%'
							)
						)
					);
				$tabRows = $this->model_catalog_catalog->loadModels($modFilter);
				$tab = array();
				foreach ($tabRows as $row) {
					$tab[] = array (
						'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['Sony']['id'] . '_' . $row['id']) ,
						'name' => $row['name']
						);
				}

				$tab[] = array (
					'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['Sony']['id']) ,
					'name' => 'More'
					);

				$manufacturers['Sony']['tabs'] = $tab;

				/*Nokia Mobile and Tabs*/

				$modFilter = array (
					'limit' => 4,
					'order_by' => 'model_id',
					'order_type' => 'DESC',
					'where' => array (
						array (
							'column' => 'manufacturer_id',
							'operator' => '=',
							'value' => $manufacturers['Nokia']['id']
							)
						)
					);
				$manufacturers['Nokia']['href'] = $this->url->link('catalog/repair_parts','path=' . $manufacturers['Nokia']['id']) ;
				$mobileRows = $this->model_catalog_catalog->loadModels($modFilter);
				$mobile = array();
				foreach ($mobileRows as $row) {
					$mobile[] = array (
						'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['Nokia']['id'] . '_' . $row['id']) ,
						'name' => $row['name']
						);
				}

				$mobile[] = array (
					'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['Nokia']['id']) ,
					'name' => 'More'
					);

				$manufacturers['Nokia']['phones'] = $mobile;

				$tab = array();
				
				$tab[] = array (
					'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['Nokia']['id']) ,
					'name' => 'More'
					);

				$manufacturers['Nokia']['tabs'] = $tab;

				/*Pantech Mobile and Tabs*/

				$modFilter = array (
					'limit' => 4,
					'order_by' => 'model_id',
					'order_type' => 'DESC',
					'where' => array (
						array (
							'column' => 'manufacturer_id',
							'operator' => '=',
							'value' => $manufacturers['Pantech']['id']
							)
						)
					);
				$manufacturers['Pantech']['href'] = $this->url->link('catalog/repair_parts','path=' . $manufacturers['Pantech']['id']) ;
				$mobileRows = $this->model_catalog_catalog->loadModels($modFilter);
				$mobile = array();
				foreach ($mobileRows as $row) {
					$mobile[] = array (
						'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['Pantech']['id'] . '_' . $row['id']) ,
						'name' => $row['name']
						);
				}

				$mobile[] = array (
					'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['Pantech']['id']) ,
					'name' => 'More'
					);

				$manufacturers['Pantech']['phones'] = $mobile;

				$tab = array();

				$tab[] = array (
					'href' => $this->url->link('catalog/repair_parts','path=' . $manufacturers['Pantech']['id']) ,
					'name' => 'More'
					);

				$manufacturers['Pantech']['tabs'] = $tab;

				$this->data['manufacturers'] = $manufacturers;
				$this->cache->set($cache_name, $this->data['manufacturers']);
			}


			//}

			//end 2.0 select box code
			
			// 2.0 popular products
			$color_class = array(
				'#000'=>'Black',
				'#000000'=>'Black',
				'#fff'=>'White',
				'#ffffff'=>'White',
				'#08086C'=>'Dark Blue',
				'#C8C9C3'=>'Chrome Mirror',
				'#C79E6A'=>'Gold Mirror',
				'#609C46'=>'Green',
				'#E7983B'=>'Orange',
				'#DF7090'=>'Pink'
				);
			$this->data['colors']=$color_class;


			// end 2.0 popular products
			// print_r($products_home3);exit;
			
			$this->data['home_products1'] = $products_home1;
			$this->data['home_products2'] = $products_home2;
			$this->data['home_products3'] = $products_home3;
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

	// public function test()
	// {
	// 	$this->load->model('catalog/product');
	// 	$this->load->model('tool/image');
	// 	switch ($_POST['key']) {
	// 		case 'APPLE':
	// 			$sku = 'acc-apl%';
	// 			break;

	// 		case 'SAMSUNG':
	// 			$sku = 'acc-sam%';
	// 			break;

	// 		case 'MOTOROLA':
	// 			$sku = '%MOT%';
	// 			break;

	// 		case 'LG':
	// 			$sku = '%LG%';
	// 			break;
	// 		case 'TOOLS':
	// 			$sku = 'acc-zrt%';
	// 			break;
			
	// 		default:
	// 			$sku = 'acc-apl%';
	// 			break;
	// 	}
	// 	$html = '';
	// 	//get the top selling products
	// 	if($_POST['key'] == 'IPAD_SCREENS')
	// 	{
	// 		$products = $this->model_catalog_product->getTopSellingProductsByQuery("SELECT oc_product.quantity,id, product_sku, oc_product.image, oc_product.price,oc_product.sale_price, oc_product.product_id,oc_product_description.name, count(product_sku) as order_d from inv_orders_items inner join oc_product on inv_orders_items.product_sku = oc_product.sku inner join oc_product_description on oc_product.product_id = oc_product_description.product_id where product_sku  like 'apl%' AND oc_product_description.name like 'Touch%Screen%iPad%' AND oc_product_description.name NOT LIKE 'Adhesive' GROUP by product_sku ORDER by order_d desc LIMIT ".(int)$_POST['limit'].",4");
			
	// 		$products = $products->rows;
	// 	}
	// 	elseif($_POST['key'] == 'ADHESIVES' || $_POST['key'] == 'CHARGERS' ||  $_POST['key'] == 'BATTERIES' ||  $_POST['key'] == 'TEMPERED_GLASS' )
	// 	{
	// 		$notLike = '';
	// 		$notLike1 = '';

	// 		if($_POST['key'] == 'ADHESIVES' )
	// 		{
	// 			$name = '%Adhesive%';
	// 		}
	// 		elseif($_POST['key'] == 'CHARGERS')
	// 		{
	// 			$name = '%Charger%';
	// 		}
	// 		elseif($_POST['key'] == 'BATTERIES')
	// 		{
	// 			$name = '%Battery%';
	// 			$notLike = '%Cover%';
	// 			$notLike = '%Door%';
	// 		}
	// 		else
	// 		{
	// 			$name = '%Tempered%';
	// 		}
	// 		$products = $this->model_catalog_product->getTopSellingProductsByName($name,(int)$_POST['limit'],4,$notLike,$notLike1);
	// 	}
	// 	else
	// 	{
	// 		$products = $this->model_catalog_product->getTopSellingProducts($sku,(int)$_POST['limit']);
	// 	}
	// 	if($products )
	// 	{
	// 		foreach ($products as $key => $value) {
	// 			if($value['image'])
	// 				{
	// 					$_image = $this->model_tool_image->resize($value['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
	// 				}
	// 				else
	// 				{
	// 					$_image = $this->model_tool_image->resize('data/image-coming-soon.jpg', 150, 150);
	// 				}

	// 			$data= array('quantity'=>$value['quantity'],'description' => $value['name'],'product_id'=>$value['product_id'],'img'=>$_image,'price'=>$value['price'],'href'=> $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id='.$value['product_id'].''));

	// 			$html .= "<div class='col-sm-3 product_".$data['product_id']."'>
	// 				<img class='lazy'  height='150' width='150'  src='".$data['img']."' / >
					
	// 				<div style='min-height: 75px'>
	// 				<p style='margin-top:10px;margin-bottom: 10px;font-weight:400;color:#3e3e3e;font-family: \"Montserrat\";'><a href='".$data['href']."'>".$data['description']."</a> </p>
	// 				</div>";
	// 				if($data['quantity']>0)
	// 				{


	// 				$html.="<div class='qtyt-box'>
	// 										<div class='input-group inlineSpinner1 spinner'>
	// 											<span class='txt'>QTY</span>
	// 											<input type='text' class='form-control qty' value='1' style='color:#303030' id='homeqty-".$_POST['key']."-".((int)$_POST['limit']+(int)$key)."' data-i='".$_POST['key']."' data-j='".((int)$_POST['limit']+(int)$key)."'>
	// 											<div class='input-group-btn-vertical'>
	// 												<button class='btn' type='button'><i class='fa fa-plus'></i></button>
	// 												<button class='btn' type='button'><i class='fa fa-minus'></i></button>
	// 											</div>
	// 										</div>
	// 									</div>";
	// 				}
	// 				else
	// 				{
	// 					$html.="";
	// 				}

	// 				$html.="<input type='hidden' class='color-default-price' id='color-".$_POST['key']."-".((int)$_POST['limit']+(int)$key)."' value='".$data['product_id']."'>";
	// 				if($data['quantity']>0)
	// 				{
	// 					if ($value['sale_price']==0.0000) {


	// 						$html.="<div class='price' id='price-".$_POST['key']."-".((int)$_POST['limit']+(int)$key)."' style='margin-bottom: 10px;'><span style='font-size: 30px;font-weight: 400;color:#191919;'>
	// 					".$this->currency->format($data['price'])."</span>
	// 				</div>";
	// 			}
	// 			else
	// 			{
	// 				$html.="<div class='price' id='price-".$_POST['key']."-".((int)$_POST['limit']+(int)$key)."' style='margin-bottom: 10px;'><span style='font-size:17px; color:#808080; text-decoration:line-through;'>".$this->currency->format($data['price'])."</span><span style='font-size: 30px;font-weight: 400;color:red;'>".$this->currency->format($value['sale_price'])."
	// 				</span></div>";
	// 			}
	// 					}
	// 					else
	// 					{
	// 							$html.="<div >
	// 							<span class='oos_qty_error_".$data['product_id']."' style='font-size:11px;color:red'></span>
	// 		<input type='text' class='form-control customer_email_".$data['product_id']."' style='margin-bottom:48px' placeholder='Enter your Email' value='".$this->customer->getEmail()."'>

	// 							</div>";
	// 					}
	// 					if($data['quantity']>0)
	// 				{

	// 				$html.="<button class='btn btn-success2' onclick='addToCartpp2(\"".$data['product_id']."\",$(\"#homeqty-".$_POST['key']."-".((int)$_POST['limit']+(int)$key)."\").val())'>".(isset($this->session->data['cart'][$data['product_id']])?"IN CART":"ADD TO CART")."</button>";
	// 			}
	// 			else
	// 			{
	// 					$html.="<button class='btn btn-info' id='notify_btn_".$data['product_id']."' onclick='notifyMe(\"".$data['product_id']."\")'>NOTIFY WHEN AVAILABLE</button>";
	// 			}
	// 			$html.="</div>";
	// 		}
	// 		echo json_encode($html);
	// 		return;
	// 	}
	// 	echo json_encode("false");
	// 	return;


		
	// }
	public function getModules()
	{
		$this->load->model('setting/setting');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		$module = $this->request->post['module'];
		$home_products = $this->model_setting_setting->getSetting('home_products');
			
		$hproducts2 = explode(",",$home_products[$module]);
		// print_r($hproducts2);exit;
		if(!$hproducts2)
			{
				echo 'No Product(s) Found';exit;
			}

		$ph2_cache_name = 'ph.'.'.'.$module . $https . (int)$this->currency->getCode() .  '.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id');
			if ($this->cache->get($ph2_cache_name)) { 
				$products_home2 = $this->cache->get($ph2_cache_name);
			} else { 
				$products_home2 = array();			
				$z=0;			

			foreach($hproducts2 as $hproduct) {
				if($hproduct=='') continue;
				$result = $this->model_catalog_product->getProduct($hproduct);
				
				if(!$result) continue;

				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
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
								
				$products_home2[$z] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'in_cart'	  => (isset($this->session->data['cart'][$result['product_id']])?true:false),
					'name'        => $result['name'],
					'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..',
					'price'       => $price,
					'sale_price'  => $sale_price,
					'quantity'		=> $result['quantity'],
					'special'     => $special,
					'tax'         => $tax,
					'quality'        => $this->model_catalog_product->getProductQuality($result['model']),
					'class'        => $this->model_catalog_product->getProductClass($result['model']),
					'rating'      => $result['rating'],
					'reviews'     => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			
				
				$z++;
			}
			
			$this->cache->set($ph2_cache_name, $products_home2);
			}
			// print_r($products_home2);exit;
			$this->data['home_products2'] = $products_home2;
			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/home_products.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/home_products.tpl';
			} else {
				$this->template = 'default/template/module/home_products.tpl';
			}
			$this->response->setOutput($this->render());
	}
}
?>
