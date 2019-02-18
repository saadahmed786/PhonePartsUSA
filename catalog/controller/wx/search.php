<?php
use Elasticsearch\ClientBuilder;
require_once(DIR_APPLICATION . '../wx/vendor/autoload.php');

class ControllerWxSearch extends Controller {
	
	public function reindex() {
		
		echo "RE-INDEX OPERATION BEGINS\n";

		$client = ClientBuilder::create()->build();

		$params = array('index' => DB_DATABASE);
		if ($client->indices()->exists($params)) {
			$response = $client->indices()->delete($params);
		}
		
		$params = array(
			'index' => DB_DATABASE,
			'body'	=> array(
				'settings' => array(
					'number_of_shards'   => 1,
					'number_of_replicas' => 0,
					'analysis' => array(
						'filter' => array(
							'shingle' => array(
								'type' => 'shingle'
							)
						),
						'char_filter' => array(
							'pre_negs'    => array(
								'type'        => 'pattern_replace',
								'pattern'     => '(\\w+)\\s+((?i:never|no|nothing|nowhere|noone|none|not|havent|hasnt|hadnt|cant|couldnt|shouldnt|wont|wouldnt|dont|doesnt|didnt|isnt|arent|aint))\\b',
								'replacement' => '~$1 $2'
								),
							'post_negs' => array(
								'type' => 'pattern_replace',
								'pattern' => '\\b((?i:never|no|nothing|nowhere|noone|none|not|havent|hasnt|hadnt|cant|couldnt|shouldnt|wont|wouldnt|dont|doesnt|didnt|isnt|arent|aint))\\s+(\\w+)',
								'replacement' => '$1 ~$2'
								)
							),
						'analyzer' => array(
							'reuters' => array(
								'type' => 'custom',
								'tokenizer' => 'standard',
								'filter' => array('lowercase', 'stop', 'kstem')
								)
							)
						)
					),
				'mappings' => array(
					'product' => array(
						'properties' => array(
							'product_id'			 => array('type' => 'integer'),
							'manufacturer_id'		 => array('type' => 'integer'),
							'category_id'			 => array('type' => 'integer'),
							'sort_order'			 => array('type' => 'integer'),
							'status'				 => array('type' => 'boolean'),
							'date_added'			 => array('type' => 'date'),
							'device_manufacturer_id' => array('type' => 'integer'),
							'device_device_id'		 => array('type' => 'integer'),
							'device_manufacturer'    => array('type' => 'keyword', 'copy_to' => 'combined'),
							'device_device'			 => array('type' => 'keyword'),
							//'device_devices'		 => array('type' => 'nested'),
							'device_model'			 => array('type' => 'keyword'),
							'nsort'					 => array('type' => 'keyword'),
							'device_model_text'		 => array('type' => 'text', 'analyzer' => 'reuters', 'term_vector' => 'yes', 'copy_to' => 'combined'),
							'name'					 => array('type' => 'text', 'analyzer' => 'reuters', 'term_vector' => 'yes', 'copy_to' => 'combined'),
							'manufacturer'			 => array('type' => 'text', 'analyzer' => 'reuters', 'term_vector' => 'yes', 'copy_to' => 'combined'),
							'model'					 => array('type' => 'text', 'analyzer' => 'reuters', 'term_vector' => 'yes', 'copy_to' => 'combined'),
							'sku'					 => array('type' => 'text', 'analyzer' => 'reuters', 'term_vector' => 'yes', 'copy_to' => 'combined'),
							'tags'				=> array(
								'type'				=> 'text',
								'index'				=> false
								),
							'price'			=> array(
								'type'				=> 'scaled_float',
								'scaling_factor'	=> 100
								),
							'combined' => array(
								'type'        => 'text',
								'analyzer'    => 'reuters',
								'term_vector' => 'yes'
								),
							)
						)
					)
				)
			);

		$response = $client->indices()->create($params); // Create the index
		
		$this->load->model('catalog/product');
		$this->load->model('wx/search');

		$manu_hash = array();
		$i = 0;
		$params = array();
		
		$result = $this->db->query("SELECT count(product_id) as product_count FROM " . DB_PREFIX . "product");
		$product_count = $result->row['product_count'];
		
		for ($current = 0; $current < $product_count; $current+=2000) {

			$products = $this->model_wx_search->getProducts(array('start' => $current, 'limit' => 2000));
			
			foreach ($products as $product) {
				$i++;
			    
				if ($i % 1000 == 2) {
					echo "Current : " . $current . "Product : " . $product['product_id'] . "\n";
				}
				
				$params['body'][] = array(
						'index'	=> array(
							'_index'	=> DB_DATABASE,
							'_type'		=> 'product',
							'_id'		=> $product['product_id']
						)
					);

				
				// LOAD CATEGORY DATA
				if (method_exists($this->model_catalog_product, 'getProductCategories')) {
					$categories = $this->model_catalog_product->getProductCategories($product['product_id']);
				} else {
					$categories = $this->model_catalog_product->getCategories($product['product_id']);
				}

				$cats = array();
				foreach($categories as $cat) {
					$cats[] = $cat['category_id'];
				}
				
				
				$params['body'][] = array(
							'product_id'		=> $product['product_id'],
							'manufacturer_id'	=> $product['manufacturer_id'],
							'sort_order'		=> $product['sort_order'],
							'status'			=> ($product['status']) ? true : false,
							'date_added'		=> date("Y-m-d", strtotime($product['date_added'])),
							'category_id'		=> $cats,
							'nsort'				=> substr(strtolower($product['name']), 0, 15),
							'name'				=> $product['name'],
							'manufacturer'		=> '',
							'model'				=> $product['model'],
							'sku'				=> $product['sku'],
							'tags'				=> '',
							'price'				=> $product['price'],
					);

				$device_product_id = $this->db->query("SELECT a.`device_product_id` 
					FROM `inv_device_product` `a`, `oc_product` `b`, `oc_product_description` `c`, `inv_device_class` `d`, `inv_classification` `e` WHERE a.`sku` = b.`model` 
					AND b.`product_id` = c.`product_id` AND a.`device_product_id` = d.`device_product_id` AND d.`class_id` = e.`id` AND b.`is_main_sku` = '1' AND b.`status` = '1' AND b.`product_id` ='" . $product['product_id'] . "' ");
				$device_product_id = ($device_product_id->row) ? $device_product_id->row['device_product_id'] : false;
				
				if($device_product_id) {
					$manufacturer_id = $this->db->query("SELECT * FROM inv_device_manufacturer WHERE device_product_id='" . $device_product_id . "'")->row;
					$params['body'][count($params['body']) - 1]['device_manufacturer'] = '';
					$params['body'][count($params['body']) - 1]['device_manufacturer_id'] = 0;
					if ($manufacturer_id) {
						$params['body'][count($params['body']) - 1]['device_manufacturer_id'] = $manufacturer_id['manufacturer_id'];
						$manufacturer = $this->db->query("SELECT * FROM inv_manufacturer WHERE manufacturer_id = " . $manufacturer_id['manufacturer_id'])->row;
						if ($manufacturer) {
							$params['body'][count($params['body']) - 1]['device_manufacturer'] = $manufacturer['name'];
						}
					}
				}
				
				echo "PID: " . $product['product_id'] . "\n";
				
				$compatibles = $this->model_catalog_product->getDeviceModels($product['product_id']);
				$devices = array();
				$deviceids = array();
				$devicess = array();
				$models  = array();
				foreach ($compatibles as $compatible_model) {
				
					$devices[] = $compatible_model['device'];
					$deviceids[] = $compatible_model['model_id'];
					$devicess[$compatible_model['model_id']] = array('id' => $compatible_model['model_id'], 'name' => $compatible_model['device']);
					
					if (!empty($compatible_model['sub_model'])) {
						if (!empty($compatible_model['name'])) {
							$models[]  = $compatible_model['sub_model'] . ' (' . $compatible_model['name'] . ')';
						} else {
							$models[]  = $compatible_model['sub_model'];
						}
					}
				}
				
				$params['body'][count($params['body']) - 1]['device_device_id'] = array_unique($deviceids);
				$params['body'][count($params['body']) - 1]['device_device'] = array_unique($devices);
				//$params['body'][count($params['body']) - 1]['device_devices'] = $devicess;
				$params['body'][count($params['body']) - 1]['device_model']      = array_unique($models);
				if (!empty(array_unique($models))) {
					$params['body'][count($params['body']) - 1]['device_model_text'] = implode(" ",array_unique($models));
				}
				
				if ($i % 1000 == 0) { // Every 1000 documents stop and send the bulk request
					echo "Total Processed Records : " . $i . " / " . $product_count . "\n";
					$responses = $client->bulk($params);
					//var_dump($responses);
					$params = array();
				}
			}

			// Send the last batch if it exists
			if (!empty($params['body'])) {
				$responses = $client->bulk($params);
			}
		}
				
		exit();
		
	}
	
	public function index() { 
		error_reporting(E_ALL);
		
		$this->language->load('product/search');

		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$this->load->model('wx/search');
		
		$this->load->model('tool/image'); 

		$search      = isset($this->request->get['search']) ? $this->request->get['search'] : '';
		$filter_name = isset($this->request->get['filter_name']) ? $this->request->get['filter_name'] : '';
		
		if (isset($this->request->get['tag'])) {
			$tag = $this->request->get['tag'];
		} elseif (isset($this->request->get['search'])) {
			$tag = $this->request->get['search'];
		} else {
			$tag = '';
		} 

		if (isset($this->request->get['description'])) {
			$description = $this->request->get['description'];
		} else {
			$description = '';
		} 

		if (isset($this->request->get['category_id'])) {
			$category_id = $this->request->get['category_id'];
		} else {
			$category_id = 0;
		} 

		if (isset($this->request->get['filter_manufacturer_id'])) {
			$manufacturer_id = (int)$this->request->get['filter_manufacturer_id'];
		} else {
			$manufacturer_id = 0;
		}

		if (isset($this->request->get['filter_device_id'])) {
			$filter_device_id = (int)$this->request->get['filter_device_id'];
		} else {
			$filter_device_id = 0;
		}
		
		if (isset($this->request->get['sub_category'])) {
			$sub_category = $this->request->get['sub_category'];
		} else {
			$sub_category = '';
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

		if (isset($this->request->get['search'])) {
			$this->document->setTitle($this->language->get('heading_title') .  ' - ' . $this->request->get['search']);
		} else if (isset($this->request->get['filter_name'])) {
			$this->document->setTitle($this->language->get('heading_title') .  ' - ' . $this->request->get['filter_name']);
		} else {
			$this->document->setTitle($this->language->get('heading_title'));
		}

		$this->document->addScript('catalog/view/javascript/jquery/jquery.total-storage.min.js');

		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),
			'separator' => false
		);

		$url = '';

		if (isset($this->request->get['search'])) {
			$url .= '&search=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8'));
		}

		$url .= isset($this->request->get['filter_name']) ? '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8')) : '';
		
		if (isset($this->request->get['tag'])) {
			$url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['category_id'])) {
			$url .= '&category_id=' . $this->request->get['category_id'];
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

		if (isset($this->request->get['search'])) {
			$this->data['heading_title'] = $this->language->get('heading_title') .  ' - ' . $this->request->get['search'];
		} else if (isset($this->request->get['filter_name'])) {
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

		$this->data['categories'] = array();

		
		$this->data['products'] = array();

		if (isset($this->request->get['search']) || isset($this->request->get['filter_name']) || isset($this->request->get['tag'])) {
			$data = array(
				'filter_name'         => !empty($filter_name) ? $filter_name : $search, 
				'filter_tag'          => $tag, 
				'filter_description'  => $description,
				'filter_manufacturer_id'	=> $manufacturer_id,
				'filter_device_id'			=> $filter_device_id,
				'filter_category_id'		=> $category_id, 
				'filter_sub_category'		=> $sub_category, 
				'sort'						=> $sort,
				'order'               => $order,
				'start'               => ($page - 1) * $limit,
				'limit'               => $limit
			);

			$product_data  = $this->model_wx_search->getCardinality($data);
			
			$product_total = $product_data['count'];
			$this->data['product_total'] = $product_total;
			$this->data['manufacturers'] = $product_data['manufacturers'];
			$this->data['models'] = $product_data['models'];
			$this->data['url']    = $url;
			//$product_total = $this->model_wx_search->getTotalProducts($data);

			//$results = $this->model_wx_search->getSearchProducts($data);
			
			$results = $product_data['products'];

			
			foreach ($results as $result) {
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

				$this->data['products'][] = array(
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..',
					'quantity'    => $result['quantity'],
					'price'       => $price,
					'special'     => $special,
					'sale_price'  => $sale_price,
					'tax'         => $tax,
					'rating'      => $result['rating'],
					'reviews'     => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'] . $url)
				);
			}

			$url = '';

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
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

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			if (isset($this->request->get['sub_category'])) {
				$url .= '&sub_category=' . $this->request->get['sub_category'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}	

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			$this->data['limits'] = array();

			$limits = array_unique(array($this->config->get('config_catalog_limit'), 25, 50, 75, 100));

			sort($limits);

			foreach($limits as $value){
				$this->data['limits'][] = array(
					'text'  => $value,
					'value' => $value,
					'href'  => $this->url->link('product/search', $url . '&limit=' . $value)
				);
			}

			$url = '';

			if (isset($this->request->get['search'])) {
				$url .= '&search=' . urlencode(html_entity_decode($this->request->get['search'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['tag'])) {
				$url .= '&tag=' . urlencode(html_entity_decode($this->request->get['tag'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['description'])) {
				$url .= '&description=' . $this->request->get['description'];
			}

			if (isset($this->request->get['category_id'])) {
				$url .= '&category_id=' . $this->request->get['category_id'];
			}

			$url .= isset($this->request->get['sub_category']) ? '&sub_category=' . $this->request->get['sub_category'] : '';
			$url .= isset($this->request->get['sort']) ? '&sort=' . $this->request->get['sort'] : '';
			$url .= isset($this->request->get['order']) ? '&order=' . $this->request->get['order'] : '';
			$url .= isset($this->request->get['limit']) ? '&limit=' . $this->request->get['limit'] : '';

			$pagination = new Pagination();
			$pagination->total = $product_total;
			$pagination->page  = $page;
			$pagination->limit = $limit;
			$pagination->text  = $this->language->get('text_pagination');
			$pagination->url   = $this->url->link('product/search', $url . '&page={page}');

			$this->data['pagination'] = $pagination->render();
		}	

		$this->data['search'] = $search;
		$this->data['description'] = $description;
		$this->data['category_id'] = $category_id;
		$this->data['sub_category'] = $sub_category;

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		$this->data['limit'] = $limit;

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/product/search.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/product/wx_search.tpl';
		} else {
			$this->template = 'default/template/product/wx_search.tpl';
		}

		$this->children = array('common/column_left','common/column_right','common/content_top','common/content_bottom','common/footer','common/header');
		$this->response->setOutput($this->render());
	}
}