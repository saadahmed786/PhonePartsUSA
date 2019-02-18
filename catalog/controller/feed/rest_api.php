<?php

class ControllerFeedRestApi extends Controller {

    private $debugIt = false;

    private function checkPlugin() {
        
        $this->response->addHeader('Content-Type: application/json');
        
        $json = array("success"=>false);

		/*check rest api is enabled*/
		if (!$this->config->get('rest_api_status')) {
			$json["error"] = 'API is disabled. Enable it!';
		}
	

		$headers = apache_request_headers();
		
		$key = "";

		if(isset($headers['X-Oc-Merchant-Id'])){
			$key = $headers['X-Oc-Merchant-Id'];
		}else if(isset($headers['X-OC-MERCHANT-ID'])) {
			$key = $headers['X-OC-MERCHANT-ID'];
		}
			
		/*validate api security key*/
		if ($this->config->get('rest_api_key') && ($key != $this->config->get('rest_api_key'))) {
			$json["error"] = 'Invalid secret key';
		}

		if(isset($json["error"])){			
			echo(json_encode($json));
			exit;
		}else {
			$this->response->setOutput(json_encode($json));			
		}	
	}
	
	/*check database modification*/
	public function getchecksum() {
		
		$this->checkPlugin();
		
		if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
	
			$this->load->model('catalog/product');
			
			$checksum = $this->model_catalog_product->getChecksum();
			
			$checksumArray = array();
			
			for ($i = 0; $i<count($checksum);$i++){
				$checksumArray[] = array('table' => $checksum[$i]['Table'], 'checksum' => $checksum[$i]['Checksum']);
			}
			
			$json = array('success' => true,'data' => $checksumArray);
			
			if ($this->debugIt) {
				echo '<pre>';
				print_r($json);
				echo '</pre>';
			} else {
				$this->response->setOutput(json_encode($json));
			}
		}
	}
	
	/*
	* PRODUCT FUNCTIONS
	*/	
	public function products() {

		$this->checkPlugin();

		if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
			//get product details
			if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
				$this->getProduct($this->request->get['id']);
			}else {
				//get products list

				/*check category id parameter*/
				if (isset($this->request->get['category']) && ctype_digit($this->request->get['category'])) {
					$category_id = $this->request->get['category'];
				} else {
					$category_id = 0;
				}

				$this->listProducts($category_id);
			}
		}else if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
			//insert product
			$requestjson = file_get_contents('php://input');
		
			$requestjson = json_decode($requestjson, true);           
			
			if (!empty($requestjson)) {
				$this->addProduct($requestjson);
			}else {
				$this->response->setOutput(json_encode(array('success' => false)));
			}    

		}else if ( $_SERVER['REQUEST_METHOD'] === 'PUT' ){
			//update product
			$requestjson = file_get_contents('php://input');
		
			$requestjson = json_decode($requestjson, true);           
			
			if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])
				&& !empty($requestjson)) {
				$this->updateProduct($this->request->get['id'], $requestjson);
			}else {
				$this->response->setOutput(json_encode(array('success' => false)));
			}
			
		}else if ( $_SERVER['REQUEST_METHOD'] === 'DELETE' ){
			if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
				$this->deleteProduct($this->request->get['id']);
			}else {
				$this->response->setOutput(json_encode(array('success' => false)));
			}
		}
    }
	
	/*
	* Get products list
	*/
	public function listProducts($category_id) {
		
		$json = array('success' => false);

		$this->load->model('catalog/product');
	    $this->load->model('tool/image');
		
		$products = $this->model_catalog_product->getProducts(array(
			'filter_category_id'        => $category_id
		));

		if(count($products) == 0){
			$json['success'] 	= false;
			$json['error'] 		= "No product found";
		}else {
			foreach ($products as $product) {

				if (isset($product['image']) && file_exists(DIR_IMAGE . $product['image'])) {
					$image = $this->model_tool_image->resize($product['image'], 500, 500);
				} else {
					$image = $this->model_tool_image->resize('no_image.jpg', 500, 500);
				}

				if ((float)$product['special']) {
					$special = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$special = "";
				}
				
				$json['success']	= true;
				
				$json['data'][] = array(
						'id'			=> $product['product_id'],
						'name'			=> $product['name'],
						'description'	=> $product['description'],
						'model'			=> (isset($product['model']) != "" ? $product['model'] : "") ,
						'sku'			=> (isset($product['sku']) != "" ? $product['sku'] : "") ,
						'quantity'		=> (isset($product['quantity']) != "" ? $product['quantity'] : "") ,
						'price'			=> $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'))),
						'href'			=> $this->url->link('product/product', 'product_id=' . $product['product_id']),
						'image'    		=> $image,
						'special'		=> $special,
						'rating'		=> $product['rating']
				);
			}
		}

		if ($this->debugIt) {
			echo '<pre>';
			print_r($json);
			echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}
	}
		
	/*
	* Get product details
	*/
	public function getProduct($id) {

		$json = array('success' => true);

		$this->load->model('catalog/product');
        $this->load->model('tool/image');
        
		$product = $this->model_catalog_product->getProduct($id);
		
		if(!empty($product)) {
		
			//product image
			if (isset($product['image']) && file_exists(DIR_IMAGE . $product['image'])) {
				$image = $this->model_tool_image->resize($product['image'], 500, 500);
			} else {
				$image = $this->model_tool_image->resize('no_image.jpg', 500, 500);
			}

			//additional images
			$additional_images = $this->model_catalog_product->getProductImages($product['product_id']);
			
            $images = array();

			foreach ($additional_images as $additional_image) {
                if (isset($additional_image['image']) && file_exists(DIR_IMAGE . $additional_image['image'])) {
        			$images[] = $this->model_tool_image->resize($additional_image['image'], 500, 500);
    			} else {
                    $images[] = $this->model_tool_image->resize('no_image.jpg', 500, 500);
    			}           
			}

			//special
			if ((float)$product['special']) {
					$special = $this->currency->format($this->tax->calculate($product['special'], $product['tax_class_id'], $this->config->get('config_tax')));
			} else {
					$special = "";
			}

			//discounts
			$discounts = array();
			$data_discounts = $this->model_catalog_product->getProductDiscounts($product['product_id']);

			foreach ($data_discounts as $discount) {
					$discounts[] = array(
							'quantity' => $discount['quantity'],
							'price' => $this->currency->format($this->tax->calculate($discount['price'], $product['tax_class_id'], $this->config->get('config_tax')))
					);
			}

			//options
			$options = array();

			foreach ($this->model_catalog_product->getProductOptions($product['product_id']) as $option) {
				if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') {
					$option_value_data = array();
					
					foreach ($option['option_value'] as $option_value) {
						if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
							if ((($this->customer->isLogged() && $this->config->get('config_customer_price')) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
								$price = $this->currency->format($this->tax->calculate($option_value['price'], $product['tax_class_id'], $this->config->get('config_tax')));
							} else {
								$price = false;
							}
							
							if (isset($option_value['image']) && file_exists(DIR_IMAGE . $option_value['image'])) {
								$option_image = $this->model_tool_image->resize($option_value['image'], 100, 100);
							} else {
								$option_image = $this->model_tool_image->resize('no_image.jpg', 100, 100);
							}

							$option_value_data[] = array(
								'image'					=> $option_image,
								'price'					=> $price,
								'price_prefix'			=> $option_value['price_prefix'],
								'product_option_value_id'=> $option_value['product_option_value_id'],
								'option_value_id'		=> $option_value['option_value_id'],
								'name'					=> $option_value['name'],
							);
						}
					}
					
					$options[] = array(
						'name'				=> $option['name'],
						'type'				=> $option['type'],
						'option_value'		=> $option_value_data,
						'required'			=> $option['required'],
						'product_option_id' => $option['product_option_id'],
						'option_id'			=> $option['option_id'],
						
					);                                        
				} elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
					$options[] = array(
						'name'				=> $option['name'],
						'type'				=> $option['type'],
						'option_value'		=> $option['option_value'],
						'required'			=> $option['required'],
						'product_option_id' => $option['product_option_id'],
						'option_id'			=> $option['option_id'],
					);                                                
				}
			}

			//minimum
			if ($product['minimum']) {
				$minimum = $product['minimum'];
			} else {
				$minimum = 1;
			}

			$json['data']	= array(
				'id'				=> $product['product_id'],
				'seo_h1'			=> (isset($product['seo_h1']) != "" ? $product['seo_h1'] : "") ,
				'name'				=> $product['name'],
				'manufacturer'			=> $product['manufacturer'],
				'sku'				=> (isset($product['sku']) != "" ? $product['sku'] : "") ,
				'model'				=> $product['model'],
				'image'				=> $image,
				'images'			=> $images,
				'price'				=> $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax'))),
				'rating'			=> (int)$product['rating'],
				'description'			=> html_entity_decode($product['description'], ENT_QUOTES, 'UTF-8'),
				'attribute_groups'		=> $this->model_catalog_product->getProductAttributes($product['product_id']),
				'special'			=> $special,
				'discounts'			=> $discounts,
				'options'			=> $options,
				'minimum'			=> $minimum,
				'reward'			=> $product['reward'],
				'points'			=> $product['points'],
				'quantity'			=> (isset($product['quantity']) != "" ? $product['quantity'] : "") ,
			);
		} else {
				$json['success']     = false;
		}
		if ($this->debugIt) {
			echo '<pre>';
			print_r($json);
			echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}
	}

    /*	Update product

    */
	private function updateProduct($id, $data) {
		
        $json = array('success' => false);       
              
		$this->load->model('catalog/product');
		
		if (ctype_digit($id)) {
			$product = $this->model_catalog_product->getProduct($id);

			$this->loadProductSavedData($data, $product);
            
			if(!empty($product)) {
				if ($this->validateProductForm($data)) {
					$json['success']     = true;
					$this->model_catalog_product->editProductById($id, $data);
				} else {
						$json['success']     = false;
				}
			}else {
				$json['success']     = false;
				$json['error']       = "The specified product does not exist.";
			}
		}else {
			$json['success']     = false;
            $json['error']       = "Invalid identifier.";
		}

		if ($this->debugIt) {
				echo '<pre>';
				print_r($json);
				echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}
	}

    /*
		Insert product
	*/
    public function addProduct($data) {
       
		$json = array('success' => true);

		$this->load->model('catalog/product');
        
        if ($this->validateProductForm($data)) {
            $productId = $this->model_catalog_product->addProduct($data);
			$json['product_id'] = $productId;
        } else {
                $json['success']	= false;
        }
       
        if ($this->debugIt) {
			echo '<pre>';
			print_r($json);
			echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}

    }

	/*
	* Delete product
	*/
    public function deleteProduct($id) {
		
		$json['success']     = false;

		$this->load->model('catalog/product');
       
		if (ctype_digit($id)) {

			$product = $this->model_catalog_product->getProduct($id);
			
			if(!empty($product)) {
				$json['success']     = true;
				$this->model_catalog_product->deleteProduct($id);
			}else {
				$json['success']     = false;
				$json['error']       = "The specified product does not exist.";
			}			
		}else {
			$json['success']     = false;
		}
	   
		if ($this->debugIt) {
				echo '<pre>';
				print_r($json);
				echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}
        
    }

	/*
	* BULK PRODUCT FUNCTIONS
	*/	
	public function bulkproducts() {

		$this->checkPlugin();

		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
			//insert products
			$requestjson = file_get_contents('php://input');
		
			$requestjson = json_decode($requestjson, true);           
			
			if (!empty($requestjson) && count($requestjson) > 0) {
                
				$this->addProducts($requestjson);
			}else {
				$this->response->setOutput(json_encode(array('success' => false)));
			}    

		}else if ( $_SERVER['REQUEST_METHOD'] === 'PUT' ){
			//update products
			$requestjson = file_get_contents('php://input');
			$requestjson = json_decode($requestjson, true);           
			
			if (!empty($requestjson) && count($requestjson) > 0) {
				$this->updateProducts($requestjson);
			}else {
				$this->response->setOutput(json_encode(array('success' => false)));
			}
			
		}
    }
	
    /*
		Insert products
	*/
    public function addProducts($products) {
       
		$json = array('success' => true);

		$this->load->model('catalog/product');
        
		foreach($products as $product) {
		
			if ($this->validateProductForm($product)) {
				$productId = $this->model_catalog_product->addProduct($product);				
			} else {
				$json['success']	= false;
			}
		} 
		
        if ($this->debugIt) {
			echo '<pre>';
			print_r($json);
			echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}

    }

    /*	Update products

    */
	private function updateProducts($products) {
		
        $json = array('success' => true);       
        
		$this->load->model('catalog/product');

		foreach($products as $productItem) {
			
			$id = $productItem['product_id'];

			if (ctype_digit($id)) {

				$product = $this->model_catalog_product->getProduct($id);

				$this->loadProductSavedData($productItem, $product);
				
				if(!empty($product)) {

					if ($this->validateProductForm($productItem)) {
						$this->model_catalog_product->editProductById($id, $productItem);
					} else {
						$json['success'] 	= false;
					}

				} else {
					$json['success']     = false;
					$json['error']       = "The specified product does not exist.";
				}

			} else {
					$json['success']     = false;
					$json['error']       = "Invalid identifier";
			}
		}

		if ($this->debugIt) {
				echo '<pre>';
				print_r($json);
				echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}
	}

	private function loadProductSavedData(&$data, $product) {
			
			if(!isset($data['sku'])){
				$data['sku'] = $product['sku'];
			}

			if(!isset($data['model'])){
				$data['model'] = $product['model'];
			}

			if(!isset($data['quantity'])){
				$data['quantity'] = $product['quantity'];
			}

			if(!isset($data['status'])){
				$data['status'] = $product['status'];
			}

			if(!isset($data['price'])){
				$data['price'] = $product['price'];
			}

			if(!isset($data['manufacturer_id'])){
				$data['manufacturer_id'] = $product['manufacturer_id'];
			}

			if(!isset($data['tax_class_id'])){
				$data['tax_class_id'] = $product['tax_class_id'];
			}

			if(!isset($data['sort_order'])){
				$data['sort_order'] = $product['sort_order'];
			}
	}


	private function validateProductForm(&$data) {
		
		$error = false;

		if ((utf8_strlen($data['sku']) < 2) || (utf8_strlen($data['sku']) > 255)) {
			$error  = true;
		}
		// Categories
		$this->load->model('catalog/category');

		if (isset($this->request->post['product_category'])) {
			$categories = $this->request->post['product_category'];
		} elseif (isset($this->request->get['product_id'])) {		
			$categories = $this->model_catalog_product->getProductCategories($this->request->get['product_id']);
		} else {
			$categories = array();
		}

		$data['product_categories'] = array();

		foreach ($categories as $category_id) {
			$category_info = $this->model_catalog_category->getCategory($category_id);

			if ($category_info) {
				$data['product_categories'][] = array(
					'category_id' => $category_info['category_id'],
					'name'        => ($category_info['path'] ? $category_info['path'] . ' &gt; ' : '') . $category_info['name']
				);
			}
		}
		if (!$error) {
			return true;
		} else {
			return false;
		}
	}

	/*
	* CATEGORY FUNCTIONS
	*/	
	public function categories() {

		$this->checkPlugin();

		if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
			//get category details
			if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
				$this->getCategory($this->request->get['id']);
			}else {
				//get category list

				/*check parent parameter*/
				if (isset($this->request->get['parent'])) {
					$parent = $this->request->get['parent'];
				} else {
					$parent = 0;
				}

				/*check level parameter*/
				if (isset($this->request->get['level'])) {
					$level = $this->request->get['level'];
				} else {
					$level = 1;
				}

				$this->listCategories($parent, $level);
			}
		}else if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
			//insert category data
			$requestjson = file_get_contents('php://input');
		
			$requestjson = json_decode($requestjson, true);           
		   
			if (!empty($requestjson)) {
				$this->addCategory($requestjson);
			}else {
				$this->response->setOutput(json_encode(array('success' => false)));
			}    

		}else if ( $_SERVER['REQUEST_METHOD'] === 'PUT' ){
			//update category data
			$requestjson = file_get_contents('php://input');
		
			$requestjson = json_decode($requestjson, true);           

			if (isset($this->request->get['id']) && ctype_digit($this->request->get['id']) 
				&& !empty($requestjson)) {
				$this->updateCategory($this->request->get['id'], $requestjson);
			}else {
				$this->response->setOutput(json_encode(array('success' => false)));
			}
		
		}else if ( $_SERVER['REQUEST_METHOD'] === 'DELETE' ){
			if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
				$this->deleteCategory($this->request->get['id']);
			}else {
				$this->response->setOutput(json_encode(array('success' => false)));
			}			
			
		}
		
    }

	
	/*
	* Get categories list
	*/	
	public function listCategories($parent,$level) {

		$json['success']	= true;
		
		$this->load->model('catalog/category');
		
		$data = $this->loadCatTree($parent, $level);

		if(count($data) == 0){
			$json['success'] 	= false;
			$json['error'] 		= "No category found";
		}else {
			$json['data'] = $data;
		}
		
		if ($this->debugIt) {
				echo '<pre>';
				print_r($json);
		} else {
				$this->response->setOutput(json_encode($json));
		}
	}

	/*
	* Get category details
	*/
	public function getCategory($id) {
		
		$json = array('success' => true);

		$this->load->model('catalog/category');
        $this->load->model('tool/image'); 
		
        if (ctype_digit($id)) {
			$category_id = $id;
		} else {
			$category_id = 0;
		}

		$category = $this->model_catalog_category->getCategory($category_id);

		if(isset($category['category_id'])){
			
			$json['success']	= true;
			
            if (isset($category['image']) && file_exists(DIR_IMAGE . $category['image'])) {
    			$image = $this->model_tool_image->resize($category['image'], 100, 100);
    		} else {
    			$image = $this->model_tool_image->resize('no_image.jpg', 100, 100);
    		}
            
            $json['data']	= array(
					'id'			=> $category['category_id'],
					'name'			=> $category['name'],
					'description'	=> $category['description'],
                    'image'         => $image,
					'href'			=> $this->url->link('product/category', 'category_id=' . $category['category_id'])
			);
		}else {
			$json['success']     = false;
			$json['error']       = "The specified category does not exist.";

		}

		if ($this->debugIt) {
			echo '<pre>';
			print_r($json);
			echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}
	}
	
	public function loadCatTree($parent = 0, $level = 1) {
		
		$this->load->model('catalog/category');
		$this->load->model('tool/image'); 
        
		$result = array();

		$categories = $this->model_catalog_category->getCategories($parent);
		
		if ($categories && $level > 0) {
			$level--;

			foreach ($categories as $category) {

				if (isset($category['image']) && file_exists(DIR_IMAGE . $category['image'])) {
        			$image = $this->model_tool_image->resize($category['image'], 100, 100);
        		} else {
        			$image = $this->model_tool_image->resize('no_image.jpg', 100, 100);
        		}

				$result[] = array(
					'category_id'   => $category['category_id'],
					'parent_id'     => $category['parent_id'],
					'name'          => $category['name'],
					'image'         => $image,
					'href'          => $this->url->link('product/category', 'category_id=' . $category['category_id']),
					'categories'    => $this->loadCatTree($category['category_id'], $level)
				);
			}
			return $result;
		}
	}

	/*
	Insert category
    */
    public function addCategory($data) {
       
		$json = array('success' => true);

		$this->load->model('catalog/category');
       
        if ($this->validateCategoryForm($data)) {
            $categoryId = $this->model_catalog_category->addCategory($data);
			$json['category_id'] = $categoryId;
        } else {
                $json['success']	= false;
        }
       
        if ($this->debugIt) {
			echo '<pre>';
			print_r($json);
			echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}

    }

	/*
	Uppdate category
	*/

    public function updateCategory($id, $data) {
       
        $json = array('success' => false); 

		$this->load->model('catalog/category');      
        
		if ($this->validateCategoryForm($data)) {
			if (ctype_digit($id)) {
				$category = $this->model_catalog_category->getCategory($id);
				
				if(!empty($category)) {
					$json['success']     = true;
					$this->model_catalog_category->editCategory($id, $data);
				}else{
					$json['success']     = false;
					$json['error']       = "The specified category does not exist.";				
				}
				
			} else {
				$json['success'] 	= false;
			}
		} else {
				$json['success']     = false;
		}
	   
		if ($this->debugIt) {
				echo '<pre>';
				print_r($json);
				echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}
    }

	/*
	* Delete category
	*/
    public function deleteCategory($id) {
		
		$json['success']     = false;

		$this->load->model('catalog/category');
       
		if (ctype_digit($id)) {
			
			$category = $this->model_catalog_category->getCategory($id);
			
			if(!empty($category)) {
				$json['success']     = true;
				$this->model_catalog_category->deleteCategory($id);
			}else {
				$json['success']     = false;
				$json['error']       = "The specified product does not exist.";
			}				
		}else {
			$json['success']     = false;
		}
	   
		if ($this->debugIt) {
				echo '<pre>';
				print_r($json);
				echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}
        
    }

    protected function validateCategoryForm($data) {
       
        $error = false;

        foreach ($data['category_description'] as $language_id => $value) {
            if ((utf8_strlen($value['name']) < 2) || (utf8_strlen($value['name']) > 255)) {
                $error  = true;
            }
        }
        if (!$error) {
            return true;
        } else {
            return false;
        }
    }

	/*
	* MANUFACTURER FUNCTIONS
	*/	
	public function manufacturers() {

		$this->checkPlugin();

		if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
			//get manufacturer details
			if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
				$this->getManufacturer($this->request->get['id']);
			}else {
				//get manufacturers list
				$this->listManufacturers();
			}
		}else if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
			//insert manufacturer
			$requestjson = file_get_contents('php://input');
		
			$requestjson = json_decode($requestjson, true);           

			if (!empty($requestjson)) {
				$this->addManufacturer($requestjson);
			}else {
				$this->response->setOutput(json_encode(array('success' => false)));
			}   

		}else if ( $_SERVER['REQUEST_METHOD'] === 'PUT' ){
			//update manufacturer
			$requestjson = file_get_contents('php://input');
		
			$requestjson = json_decode($requestjson, true);           
			
			if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])
				&& !empty($requestjson)) {
				$this->updateManufacturer($this->request->get['id'], $requestjson);
			}else {
				$this->response->setOutput(json_encode(array('success' => false)));
			}			
			
		}else if ( $_SERVER['REQUEST_METHOD'] === 'DELETE' ){
			//delete manufacturer
			if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
				$this->deleteManufacturer($this->request->get['id']);
			}else {
				$this->response->setOutput(json_encode(array('success' => false)));
			}
			
		}		
    }

	/*
	* Get manufacturers list
	*/	
	public function listManufacturers() {

		$this->load->model('catalog/manufacturer');
		$this->load->model('tool/image');
		$json = array('success' => true);
		
		$data['start'] = 0;
		$data['limit'] = 1000;

		$results = $this->model_catalog_manufacturer->getManufacturers($data);
				
		$manufacturers = array();


		
		foreach ($results as $result) {
			if (isset($result['image']) && file_exists(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 100, 100);
			} else {
				$image = $this->model_tool_image->resize('no_image.jpg', 100, 100);
			}

			$manufacturers[] = array(
					'manufacturer_id'	=> $result['manufacturer_id'],
					'name'			=> $result['name'],
					'image'			=> $image,
					'sort_order'		=> $result['sort_order']
			);
		}

		if(count($manufacturers) == 0){
			$json['success'] 	= false;
			$json['error'] 		= "No manufacturer found";
		}else {
			$json['data'] 	= $manufacturers;			
		}


		
		if ($this->debugIt) {
				echo '<pre>';
				print_r($json);
		} else {
				$this->response->setOutput(json_encode($json));
		}
    }

	/*
	* Get manufacturer details
	*/
	public function getManufacturer($id) {

		$json = array('success' => true);

		$this->load->model('catalog/manufacturer');	
		$this->load->model('tool/image');

		if (ctype_digit($id)) {
			$result = $this->model_catalog_manufacturer->getManufacturer($id);
		} else {
			$json['success'] 	= false;
		}
		
		if (isset($result['image']) && file_exists(DIR_IMAGE . $result['image'])) {
			$image = $this->model_tool_image->resize($result['image'], 100, 100);
		} else {
			$image = $this->model_tool_image->resize('no_image.jpg', 100, 100);
		}

		if(!empty($result)){		
			$json['data'] = array(
					'manufacturer_id'	=> $result['manufacturer_id'],
					'name'			=> $result['name'],
					'image'			=> $image,
					'sort_order'		=> $result['sort_order']
			);
		}else {
			$json['success']     = false;
			$json['error']       = "The specified manufacturer does not exist.";
		}

		if ($this->debugIt) {
			echo '<pre>';
			print_r($json);
			echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}
	}

	/*
		Insert manufacturer 
    */

	public function addManufacturer($data) {

		$json = array('success' => true);
		
		$this->load->model('catalog/manufacturer');			   
		
		if ($this->validateManufacturerForm($data)) {
			$manufacturerId = $this->model_catalog_manufacturer->addManufacturer($data);
			$json['manufacturer_id'] = $manufacturerId;
		} else {
				$json['success']     = false;
		}

		if ($this->debugIt) {
			echo '<pre>';
			print_r($json);
			echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}

	}

	/*
		Update manufacturer

    */
	public function updateManufacturer($id, $data) {
		
		$json = array('success' => false);

		$this->load->model('catalog/manufacturer');

		if ($this->validateManufacturerForm($data)) {
			if (ctype_digit($id)) {
				
				$result = $this->model_catalog_manufacturer->getManufacturer($id);
				
				if(!empty($result)) {
					$json['success']     = true;
					$this->model_catalog_manufacturer->editManufacturer($id, $data);
				}else{
					$json['success']     = false;
					$json['error']       = "The specified manufacturer does not exist.";
				}
				
			} else {
				$json['success'] 	= false;
			}
		} else {
				$json['success']     = false;
		}

		if ($this->debugIt) {
				echo '<pre>';
				print_r($json);
				echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}
	}

	/*Delete manufacturer*/
    public function deleteManufacturer($id) {
		
		$json['success']     = false;
    
		$this->load->model('catalog/manufacturer');
		
		if (ctype_digit($id)) {
			if($this->validateManufacturerDelete($id)){
							
				$result = $this->model_catalog_manufacturer->getManufacturer($id);
				
				if(!empty($result)) {
					$json['success']     = true;
					$this->model_catalog_manufacturer->deleteManufacturer($id);
				}else {
					$json['success']     = false;
					$json['error']       = "The specified manufacturer does not exist.";				
				}
				
			}else {
				$json['success']		= false;
				$json['error']			= "Some products belong to this manufacturer";
			}
		}else {
			$json['success']     = false;
		}
	   
		if ($this->debugIt) {
				echo '<pre>';
				print_r($json);
				echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}
    }
	

	protected function validateManufacturerForm($data) {
		
		$error = false;

		if(isset($data["name"])){
			if ((utf8_strlen($data["name"]) < 2) || (utf8_strlen($data["name"]) > 255)) {
				$error  = true;
			}
		}else{
			$error  = true;
		}
	
		if(isset($data["sort_order"])){
			if ((utf8_strlen($data["sort_order"]) < 1) || (utf8_strlen($data["sort_order"]) > 255)) {
				$error  = true;
			}
		}else{
			$error  = true;
		}
		   
		if (!$error) {
			return true;
		} else {
			return false;
		}
	}

	protected function validateManufacturerDelete($manufacturer_id) {
		
		$error = false;

		$this->load->model('catalog/product');

		$product_total = $this->model_catalog_product->getTotalProductsByManufacturerId($manufacturer_id);

		if ($product_total) {
			$error  = true;
		}	
		
		if (!$error) {
			return true;
		} else {
			return false;
		} 
	}

	/*
	* ORDER FUNCTIONS
	*/	
	public function orders() {

		$this->checkPlugin();

		if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
			//get order details
			if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
				$this->getOrder($this->request->get['id']);
			}else {
				//get orders list
				$this->listOrders();
			}
		}else if ( $_SERVER['REQUEST_METHOD'] === 'PUT' ){
			//update order data
			$requestjson = file_get_contents('php://input');
		
			$requestjson = json_decode($requestjson, true);           
			
			if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])
				&& !empty($requestjson)) {
				$this->updateOrder($this->request->get['id'], $requestjson);
			}else {
				$this->response->setOutput(json_encode(array('success' => false)));
			}				
			
			
		}else if ( $_SERVER['REQUEST_METHOD'] === 'DELETE' ){
			//delete order
			if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
				$this->deleteOrder($this->request->get['id']);
			}else {
				$this->response->setOutput(json_encode(array('success' => false)));
			}				
		}	
    }

	/*
	* List orders
	*/
	public function listOrders() {

		$json = array('success' => true);

		
		$this->load->model('account/order');

		/*check offset parameter*/
		if (isset($this->request->get['offset']) && $this->request->get['offset'] != "" && ctype_digit($this->request->get['offset'])) {
			$offset = $this->request->get['offset'];
		} else {
			$offset 	= 0;
		}

		/*check limit parameter*/
		if (isset($this->request->get['limit']) && $this->request->get['limit'] != "" && ctype_digit($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit 	= 10000;
		}
		
		/*get all orders of user*/
		$results = $this->model_account_order->getAllOrders($offset, $limit);
		
		$orders = array();

		if(count($results)){
			foreach ($results as $result) {

				$product_total = $this->model_account_order->getTotalOrderProductsByOrderId($result['order_id']);
				$voucher_total = $this->model_account_order->getTotalOrderVouchersByOrderId($result['order_id']);

				$orders[] = array(
						'order_id'		=> $result['order_id'],
						'name'			=> $result['firstname'] . ' ' . $result['lastname'],
						'status'		=> $result['status'],
						'date_added'	=> $result['date_added'],
						'products'		=> ($product_total + $voucher_total),
						'total'			=> $result['total'],
						'currency_code'	=> $result['currency_code'],
						'currency_value'=> $result['currency_value'],
				);
			}

			if(count($orders) == 0){
				$json['success'] 	= false;
				$json['error'] 		= "No orders found";
			}else {
				$json['data'] 	= $orders;	
			}
			
		}else {
			$json['error'] 		= "No orders found";
			$json['success'] 	= false;
		}

		if ($this->debugIt) {
			echo '<pre>';
			print_r($json);
			echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}
	}
	
	/*
	* List orders whith details
	*/
	public function listorderswithdetails() {

		$this->checkPlugin();

		if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){

			$json = array('success' => true);

			
			$this->load->model('account/order');

			/*check limit parameter*/
			if (isset($this->request->get['limit']) && $this->request->get['limit'] != "" && ctype_digit($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];
			} else {
				$limit 	= 100000;
			}

			if (isset($this->request->get['filter_date_added_from'])) {
					$date_added_from = date('Y-m-d H:i:s',strtotime($this->request->get['filter_date_added_from']));
					if($this->validateDate($date_added_from)) {
						$filter_date_added_from = $date_added_from;
					}
			} else {
				$filter_date_added_from = null;
			}

			if (isset($this->request->get['filter_date_added_on'])) {
					$date_added_on = date('Y-m-d',strtotime($this->request->get['filter_date_added_on']));
					if($this->validateDate($date_added_on, 'Y-m-d')) {
						$filter_date_added_on = $date_added_on;
					}
			} else {
				$filter_date_added_on = null;
			}


			if (isset($this->request->get['filter_date_added_to'])) {
					$date_added_to = date('Y-m-d H:i:s',strtotime($this->request->get['filter_date_added_to']));
					if($this->validateDate($date_added_to)) {
						$filter_date_added_to = $date_added_to;
					}
			} else {
				$filter_date_added_to = null;
			}
			
			if (isset($this->request->get['filter_date_modified_on'])) {
					$date_modified_on = date('Y-m-d',strtotime($this->request->get['filter_date_modified_on']));
					if($this->validateDate($date_modified_on, 'Y-m-d')) {
						$filter_date_modified_on = $date_modified_on;
					}
			} else {
				$filter_date_modified_on = null;
			}
			
			if (isset($this->request->get['filter_date_modified_from'])) {
					$date_modified_from = date('Y-m-d H:i:s',strtotime($this->request->get['filter_date_modified_from']));
					if($this->validateDate($date_modified_from)) {
						$filter_date_modified_from = $date_modified_from;
					}
			} else {
				$filter_date_modified_from = null;
			}
			
			if (isset($this->request->get['filter_date_modified_to'])) {
					$date_modified_to = date('Y-m-d H:i:s',strtotime($this->request->get['filter_date_modified_to']));
					if($this->validateDate($date_modified_to)) {
						$filter_date_modified_to = $date_modified_to;
					}
			} else {
				$filter_date_modified_to = null;
			}

			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
			} else {
				$page = 1;
			}

	
			$data = array(
				'filter_date_added_on'      => $filter_date_added_on,
				'filter_date_added_from'    => $filter_date_added_from,
				'filter_date_added_to'      => $filter_date_added_to,
				'filter_date_modified_on'   => $filter_date_modified_on,
				'filter_date_modified_from' => $filter_date_modified_from,
				'filter_date_modified_to'   => $filter_date_modified_to,
				'start'						=> ($page - 1) * $limit,
				'limit'						=> $limit
			);


			$results = $this->model_account_order->getOrdersByFilter($data);
			/*get all orders*/
			//$results = $this->model_account_order->getAllOrders($offset, $limit);
			
			$orders = array();

			if(count($results)){

				foreach ($results as $result) {
				
					$orderData = $this->getOrderDetailsToOrder($result);
		
					if (!empty($orderData)) {
						$orders[] = $orderData;
					}
				}

				if(count($orders) == 0){
					$json['success'] 	= false;
					$json['error'] 		= "No orders found";
				}else {
					$json['data'] 	= $orders;	
				}
				
			}else {
				$json['error'] 		= "No orders found";
				$json['success'] 	= false;
			}
		}else{
				$json['success'] 	= false;
		}

		if ($this->debugIt) {
			echo '<pre>';
			print_r($json);
			echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}
	}

	/*Get order details*/
	public function getOrder($id) {
		
		$this->load->model('checkout/order');
		$this->load->model('account/order');
		
		$json = array('success' => true);
			
		if (ctype_digit($id)) {
			$order_id = $id;
		} else {
			$order_id = 0;
		}

		$orderData = $this->getOrderDetails($order_id);

		if (!empty($orderData)) {
			$json['success'] 	= true;
			$json['data'] 		= $orderData;
			
		} else {
				$json['success']     = false;
				$json['error']       = "The specified order does not exist.";

		}
		
		if ($this->debugIt) {
			echo '<pre>';
			print_r($json);
			echo '</pre>';

		} else {
			$this->response->setOutput(json_encode($json));
		}
	}

	/*Get all orders of user */
	public function userorders(){
		
		$this->checkPlugin();

		if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
	
			$json = array('success' => true);				
	
			$user = null;
			
			/*check user parameter*/
			if (isset($this->request->get['user']) && $this->request->get['user'] != "" && ctype_digit($this->request->get['user'])) {
				$user = $this->request->get['user'];
			} else {
				$json['success'] 	= false;
			}
	
			if($json['success'] == true){
				$orderData['orders'] = array();
		
				$this->load->model('account/order');
				
				/*get all orders of user*/
				$results = $this->model_account_order->getOrdersByUser($user);
				
				$orders = array();
				
				foreach ($results as $result) {

					$product_total = $this->model_account_order->getTotalOrderProductsByOrderId($result['order_id']);
					$voucher_total = $this->model_account_order->getTotalOrderVouchersByOrderId($result['order_id']);

					$orders[] = array(
							'order_id'		=> $result['order_id'],
							'name'			=> $result['firstname'] . ' ' . $result['lastname'],
							'status'		=> $result['status'],
							'date_added'	=> $result['date_added'],
							'products'		=> ($product_total + $voucher_total),
							'total'			=> $result['total'],
							'currency_code'	=> $result['currency_code'],
							'currency_value'=> $result['currency_value'],
					);
				}

				if(count($orders) == 0){
					$json['success'] 	= false;
					$json['error'] 		= "No orders found";
				}else {
					$json['data'] 	= $orders;					
				}
			}else{
				$json['success'] 	= false;
			}
		}

		if ($this->debugIt) {
			echo '<pre>';
			print_r($json);
			echo '</pre>';

		} else {
			$this->response->setOutput(json_encode($json));
		}
	
	}
    private function getOrderDetailsToOrder($order_info) {

        $this->load->model('catalog/product');

        $orderData = array();

        if ($order_info) {

            $orderData['order_id']                = $order_info['order_id'];
            $orderData['invoice_no']              = $order_info['invoice_no'];
            $orderData['invoice_prefix']          = $order_info['invoice_prefix'];
            $orderData['store_id']                = $order_info['store_id'];
            $orderData['store_name']              = $order_info['store_name'];
            $orderData['store_url']               = $order_info['store_url'];
            $orderData['customer_id']             = $order_info['customer_id'];
            $orderData['firstname']               = $order_info['firstname'];
            $orderData['lastname']                = $order_info['lastname'];
            $orderData['telephone']               = $order_info['telephone'];
            $orderData['fax']                     = $order_info['fax'];
            $orderData['email']                   = $order_info['email'];
            $orderData['payment_firstname']       = $order_info['payment_firstname'];
            $orderData['payment_lastname']        = $order_info['payment_lastname'];
            $orderData['payment_company']         = $order_info['payment_company'];
            $orderData['payment_address_1']       = $order_info['payment_address_1'];
            $orderData['payment_address_2']       = $order_info['payment_address_2'];
            $orderData['payment_postcode']        = $order_info['payment_postcode'];
            $orderData['payment_city']            = $order_info['payment_city'];
            $orderData['payment_zone_id']         = $order_info['payment_zone_id'];
            $orderData['payment_zone']            = $order_info['payment_zone'];
            $orderData['payment_country_id']      = $order_info['payment_country_id'];
            $orderData['payment_country']         = $order_info['payment_country'];
            $orderData['payment_address_format']  = $order_info['payment_address_format'];
            $orderData['payment_method']          = $order_info['payment_method'];
            $orderData['shipping_firstname']      = $order_info['shipping_firstname'];
            $orderData['shipping_lastname']       = $order_info['shipping_lastname'];
            $orderData['shipping_company']        = $order_info['shipping_company'];
            $orderData['shipping_address_1']      = $order_info['shipping_address_1'];
            $orderData['shipping_address_2']      = $order_info['shipping_address_2'];
            $orderData['shipping_postcode']       = $order_info['shipping_postcode'];
            $orderData['shipping_city']           = $order_info['shipping_city'];
            $orderData['shipping_zone_id']        = $order_info['shipping_zone_id'];
            $orderData['shipping_zone']           = $order_info['shipping_zone'];
            $orderData['shipping_country_id']     = $order_info['shipping_country_id'];
            $orderData['shipping_country']        = $order_info['shipping_country'];
            $orderData['shipping_address_format'] = $order_info['shipping_address_format'];
            $orderData['shipping_method']         = $order_info['shipping_method'];
            $orderData['comment']                 = $order_info['comment'];
            $orderData['total']                   = $order_info['total'];
            $orderData['order_status_id']         = $order_info['order_status_id'];
            $orderData['language_id']             = $order_info['language_id'];
            $orderData['currency_id']             = $order_info['currency_id'];
            $orderData['currency_code']           = $order_info['currency_code'];
            $orderData['currency_value']          = $order_info['currency_value'];
            $orderData['date_modified']           = $order_info['date_modified'];
            $orderData['date_added']              = $order_info['date_added'];
            $orderData['ip']                      = $order_info['ip'];

            $orderData['products'] = array();

            $products = $this->model_account_order->getOrderProducts($orderData['order_id']);

            foreach ($products as $product) {
                $option_data = array();

                $options = $this->model_account_order->getOrderOptions($orderData['order_id'], $product['order_product_id']);

                foreach ($options as $option) {
                    if ($option['type'] != 'file') {
                        $option_data[] = array(
                            'name'  => $option['name'],
                            'value' => $option['value'],
                            'type'  => $option['type']
                        );
                    } else {
                        $option_data[] = array(
                            'name'  => $option['name'],
                            'value' => utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.')),
                            'type'  => $option['type']
                        );
                    }
                }

                $origProduct = $this->model_catalog_product->getProduct($product['product_id']);

                $orderData['products'][] = array(
                    'order_product_id' => $product['order_product_id'],
                    'product_id'       => $product['product_id'],
                    'name'    	 	   => $product['name'],
                    'model'    		   => $product['model'],
                    'sku'			   => (isset($origProduct['sku']) != "" ? $origProduct['sku'] : "") ,
                    'option'   		   => $option_data,
                    'quantity'		   => $product['quantity'],
                    'price'    		   => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
                    'total'    		   => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
                );
            }
        }

        return $orderData;
    }

	private function getOrderDetails($order_id) {
			$this->load->model('checkout/order');
			$this->load->model('account/order');
			$this->load->model('catalog/product');
	
			$order_info = $this->model_checkout_order->getOrder($order_id);
			
			$orderData = array();

			if ($order_info) {
						
				$orderData['order_id']                = $order_info['order_id'];
				$orderData['invoice_no']              = $order_info['invoice_no'];
				$orderData['invoice_prefix']          = $order_info['invoice_prefix'];
				$orderData['store_id']                = $order_info['store_id'];
				$orderData['store_name']              = $order_info['store_name'];
				$orderData['store_url']               = $order_info['store_url'];				
				$orderData['customer_id']             = $order_info['customer_id'];
				$orderData['firstname']               = $order_info['firstname'];
				$orderData['lastname']                = $order_info['lastname'];
				$orderData['telephone']               = $order_info['telephone'];
				$orderData['fax']                     = $order_info['fax'];
				$orderData['email']                   = $order_info['email'];
				$orderData['payment_firstname']       = $order_info['payment_firstname'];
				$orderData['payment_lastname']        = $order_info['payment_lastname'];				
				$orderData['payment_company']         = $order_info['payment_company'];
				$orderData['payment_address_1']       = $order_info['payment_address_1'];
				$orderData['payment_address_2']       = $order_info['payment_address_2'];
				$orderData['payment_postcode']        = $order_info['payment_postcode'];
				$orderData['payment_city']            = $order_info['payment_city'];
				$orderData['payment_zone_id']         = $order_info['payment_zone_id'];
				$orderData['payment_zone']            = $order_info['payment_zone'];
				$orderData['payment_country_id']      = $order_info['payment_country_id'];
				$orderData['payment_country']         = $order_info['payment_country'];	
				$orderData['payment_address_format']  = $order_info['payment_address_format'];
				$orderData['payment_method']          = $order_info['payment_method'];
				$orderData['shipping_firstname']      = $order_info['shipping_firstname'];
				$orderData['shipping_lastname']       = $order_info['shipping_lastname'];				
				$orderData['shipping_company']        = $order_info['shipping_company'];
				$orderData['shipping_address_1']      = $order_info['shipping_address_1'];
				$orderData['shipping_address_2']      = $order_info['shipping_address_2'];
				$orderData['shipping_postcode']       = $order_info['shipping_postcode'];
				$orderData['shipping_city']           = $order_info['shipping_city'];
				$orderData['shipping_zone_id']        = $order_info['shipping_zone_id'];
				$orderData['shipping_zone']           = $order_info['shipping_zone'];
				$orderData['shipping_country_id']     = $order_info['shipping_country_id'];
				$orderData['shipping_country']        = $order_info['shipping_country'];	
				$orderData['shipping_address_format'] = $order_info['shipping_address_format'];
				$orderData['shipping_method']         = $order_info['shipping_method'];
				$orderData['comment']                 = $order_info['comment'];
				$orderData['total']                   = $order_info['total'];
				$orderData['order_status_id']         = $order_info['order_status_id'];
				$orderData['language_id']             = $order_info['language_id'];
				$orderData['currency_id']             = $order_info['currency_id'];
				$orderData['currency_code']           = $order_info['currency_code'];
				$orderData['currency_value']          = $order_info['currency_value'];
				$orderData['date_modified']           = $order_info['date_modified'];
				$orderData['date_added']              = $order_info['date_added'];
				$orderData['ip']                      = $order_info['ip'];

				$orderData['products'] = array();

				$products = $this->model_account_order->getOrderProducts($orderData['order_id']);

				foreach ($products as $product) {
					$option_data = array();

					$options = $this->model_account_order->getOrderOptions($orderData['order_id'], $product['order_product_id']);

					foreach ($options as $option) {
						if ($option['type'] != 'file') {
							$option_data[] = array(
								'name'  => $option['name'],
								'value' => $option['value'],
								'type'  => $option['type']
							);
						} else {
							$option_data[] = array(
								'name'  => $option['name'],
								'value' => utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.')),
								'type'  => $option['type'],
								'href'  => $this->url->link('account/order/download', 'token=' . $this->session->data['token'] . '&order_id=' . $orderData['order_id'] . '&order_option_id=' . $option['order_option_id'], 'SSL')
							);						
						}
					}
			
					$origProduct = $this->model_catalog_product->getProduct($product['product_id']);
		
					$orderData['products'][] = array(
						'order_product_id' => $product['order_product_id'],
						'product_id'       => $product['product_id'],
						'name'    	 	   => $product['name'],
						'model'    		   => $product['model'],
						'sku'				=> (isset($origProduct['sku']) != "" ? $origProduct['sku'] : "") ,
						'option'   		   => $option_data,
						'quantity'		   => $product['quantity'],
						'price'    		   => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
						'total'    		   => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
					);
				}
			}

			return $orderData;
	}

	/*
		Update order status
	
	*/
    public function updateOrder($id, $data) {
       
      
		$json = array('success' => false);

		$this->load->model('checkout/order');       

		if (ctype_digit($id)) {
			
				if (isset($data['status']) && ctype_digit($data['status'])) {
			
					$result = $this->model_checkout_order->getOrder($id);
					if(!empty($result)) {
						$json['success']     = true;
						$this->model_checkout_order->update($id, $data['status']);
					}else {
						$json['success']     = false;
						$json['error']       = "The specified order does not exist.";
					}
					
				} else {
					$json['success'] 	= false;
				}
		} else {
				$json['success']     = false;
		}
		
		if ($this->debugIt) {
				echo '<pre>';
				print_r($json);
				echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}

    }
	
	/*Delete order*/
    public function deleteOrder($id) {

		$json['success']     = false;

	   $this->load->model('checkout/order');

		if (ctype_digit($id)) {
			$result = $this->model_checkout_order->getOrder($id);
			
			if(!empty($result)) {
				$json['success']     = true;
				$this->model_checkout_order->deleteOrder($id);
			}else{
				$json['success']     = false;
				$json['error']       = "The specified order does not exist.";
			}
			
		}else {
			$json['success']     = false;
		}
	   
		if ($this->debugIt) {
				echo '<pre>';
				print_r($json);
				echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}       
    }
	
	/*
	* CUSTOMER FUNCTIONS
	*/	
	public function customers() {

		$this->checkPlugin();

		if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
			//get customer details
			if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
				$this->getCustomer($this->request->get['id']);
			}else {
				//get customers list
				$this->listCustomers();
			}
		}else if ( $_SERVER['REQUEST_METHOD'] === 'PUT' ){
			//update customer
			$requestjson = file_get_contents('php://input');
		
			$requestjson = json_decode($requestjson, true);           
		   
			if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])
				&& !empty($requestjson)) {
				$this->updateCustomer($this->request->get['id'], $requestjson);
			}else {
				$this->response->setOutput(json_encode(array('success' => false)));
			}		

		}else if ( $_SERVER['REQUEST_METHOD'] === 'DELETE' ){
			//delete customer
			if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
				$this->deleteCustomer($this->request->get['id']);
			}else {
				$this->response->setOutput(json_encode(array('success' => false)));
			}	
		}		
    }

	/*
	* Get customers list
	*/	
	private function listCustomers() {

		$json = array('success' => true);
	
		$this->load->model('account/customer');
		
		$results = $this->model_account_customer->getCustomersMod();
				
		$customers = array();
		
		foreach ($results as $result) {
			$customers[] = array(
				'store_id'                => $result['store_id'],
				'customer_id'             => $result['customer_id'],
				'firstname'               => $result['firstname'],
				'lastname'                => $result['lastname'],
				'telephone'               => $result['telephone'],
				'fax'                     => $result['fax'],
				'email'                   => $result['email']
			);
		}

		if(count($customers) == 0){
			$json['success'] 	= false;
			$json['error'] 		= "No customers found";
		}else {
			$json['data'] 		= $customers;			
		}
		
		if ($this->debugIt) {
				echo '<pre>';
				print_r($json);
		} else {
				$this->response->setOutput(json_encode($json));
		}
    }

	/*
	* Get customer details
	*/
	private function getCustomer($id) {

		$json = array('success' => true);
	
		$this->load->model('account/customer');
		
		if (ctype_digit($id)) {
			$result = $this->model_account_customer->getCustomer($id);
		} else {
			$json['success'] 	= false;
		}

		if(isset($result['customer_id'])){		
			$json['data'] = array(
					'store_id'                => $result['store_id'],
					'customer_id'             => $result['customer_id'],
					'firstname'               => $result['firstname'],
					'lastname'                => $result['lastname'],
					'telephone'               => $result['telephone'],
					'fax'                     => $result['fax'],
					'email'                   => $result['email']
			);
		}else {
				$json['success']     = false;
				$json['error']       = "The specified customer does not exist.";
		}

		if ($this->debugIt) {
			echo '<pre>';
			print_r($json);
			echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}
	}

    /*
		Update customer
   */
	private function updateCustomer($id, $data) {
		
        $json = array('success' => false);       
              
		$this->load->model('account/customer');
	   
		if ($this->validateCustomerForm($data)) {
			if (ctype_digit($id)) {
				$result = $this->model_account_customer->getCustomer($id);
				if(!empty($result)) {
					$enableModificationy = true;
					
					//if user wanted to change current password, we need to check not in use
					if($result['email'] != strtolower($data['email'])){
						$email_query = $this->db->query("SELECT `email` FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(strtolower($data['email'])) . "'");
						/*check email not used*/
						if($email_query->num_rows > 0){
							$enableModificationy = false;
							$json['error'] 	= "The email is already used";
						}
					}
					if($enableModificationy){
						$json['success']     = true;
						$this->model_account_customer->editCustomerById($id, $data);
					}
				}else {
					$json['success']     = false;
					$json['error']       = "The specified customer does not exist.";
				}
			}else {
					$json['success'] 	= false;
			}
		} else {
				$json['success']     = false;
		}

		if ($this->debugIt) {
				echo '<pre>';
				print_r($json);
				echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}
	}
	
	/*Delete customer*/
    public function deleteCustomer($id) {

		$json['success']     = false;

	    $this->load->model('account/customer');

		if (ctype_digit($id)) {
			$result = $this->model_account_customer->getCustomer($id);
			if(!empty($result)) {
				$json['success']     = true;
				$this->model_account_customer->deleteCustomer($id);
			}else{
				$json['success']     = false;
				$json['error']       = "The specified customer does not exist.";			
			}
		}else {
				$json['success']     = false;
				$json['error']       = "Invalid id";
		}
	   
		if ($this->debugIt) {
				echo '<pre>';
				print_r($json);
				echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}       
    }

	private function validateCustomerForm($data) {
		
		$error = false;

		if ((utf8_strlen($data['firstname']) < 2) || (utf8_strlen($data['firstname']) > 255)) {
			$error  = true;
		}

		if ((utf8_strlen($data['lastname']) < 2) || (utf8_strlen($data['lastname']) > 255)) {
			$error  = true;
		}

		if ((utf8_strlen($data['email']) < 2) || (utf8_strlen($data['email']) > 255) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
			$error  = true;
		}
		   
		if (!$error) {
			return true;
		} else {
			return false;
		}
	}

	/*
	* LANGUAGE FUNCTIONS
	*/	
	public function languages() {

		$this->checkPlugin();

		if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
			//get language details
			if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
				$this->getLanguage($this->request->get['id']);
			}else {
				//get languages list
				$this->listLanguages();
			}
		}		
    }

    /*
* ORDER STATUSES FUNCTIONS
*/
    public function order_statuses() {

        $this->checkPlugin();

        if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
            //get order statuses list
            $this->listOrderStatuses();
        }
    }

    /*
    * Get order statuses list
    */
    private function listOrderStatuses() {

        $json = array('success' => true);

        $this->load->model('account/order');

        $statuses = $this->model_account_order->getOrderStatuses();

        if(count($statuses) == 0){
            $json['success'] 	= false;
            $json['error'] 		= "No order status found";
        }else {
            $json['data'] 		= $statuses;
        }

        if ($this->debugIt) {
            echo '<pre>';
            print_r($json);
        } else {
            $this->response->setOutput(json_encode($json));
        }
    }

	/*
	* Get languages list
	*/	
	private function listLanguages() {

		$json = array('success' => true);
	
		$this->load->model('localisation/language');
		
		$languages = $this->model_localisation_language->getLanguages();
				
		if(count($languages) == 0){
			$json['success'] 	= false;
			$json['error'] 		= "No language found";
		}else {
			$json['data'] 		= $languages;			
		}

		if ($this->debugIt) {
				echo '<pre>';
				print_r($json);
		} else {
				$this->response->setOutput(json_encode($json));
		}
    }

	/*
	* Get language details
	*/
	private function getLanguage($id) {

		$json = array('success' => true);
	
		$this->load->model('localisation/language');
		
		if (ctype_digit($id)) {
			$result = $this->model_localisation_language->getLanguage($id);
		} else {
			$json['success']     = false;
			$json['error']       = "Not valid id";
		}

		if(!empty($result)){			
			$json['data'] = array(
					'language_id' => $result['language_id'],
        			'name'        => $result['name'],
        			'code'        => $result['code'],
					'locale'      => $result['locale'],
					'image'       => $result['image'],
					'directory'   => $result['directory'],
					'filename'    => $result['filename'],
					'sort_order'  => $result['sort_order'],
					'status'      => $result['status']
			);
		}else {
			$json['success']     = false;
			$json['error']       = "The specified language does not exist.";
		}

		if ($this->debugIt) {
			echo '<pre>';
			print_r($json);
			echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}
	}

	/*
	* STORE FUNCTIONS
	*/	
	public function stores() {

		$this->checkPlugin();

		if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
			//get store details
			if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
				$this->getStore($this->request->get['id']);
			}else {
				//get stores list
				$this->listStores();
			}
		}		
    }

	/*
	* Get stores list
	*/	
	private function listStores() {

		$json = array('success' => true);
	
		$this->load->model('checkout/order');
		
		$results = $this->model_checkout_order->getStores();

		$stores = array();
		
		foreach ($results as $result) {
			$stores[] = array(
				'store_id'	=> $result['store_id'],
				'name'      => $result['name']
			);
		}
		
		$default_store = array(
				'store_id'	=> 0,
				'name'      => $this->config->get('config_name')
		);
		
		$data = array_merge($default_store, $stores);
		
		if(count($data) == 0){
			$json['success'] 	= false;
			$json['error'] 		= "No store found";
		}else {
			$json['data'] 		= $data;			
		}

		if ($this->debugIt) {
				echo '<pre>';
				print_r($json);
		} else {
				$this->response->setOutput(json_encode($json));
		}
    }

	/*
	* Get store details
	*/
	private function getStore($id) {

		$json = array('success' => true);
	
		$this->load->model('checkout/order');
		
		if (ctype_digit($id)) {
			$result = $this->model_checkout_order->getStore($id);
		} else {
			$json['success'] 	= false;
		}

		if(isset($result['store_id'])){		
			$json['data'] = array(
					'store_id'	  => $result['store_id'],
        			'name'        => $result['name']
			);
		}else {
			$json['success']     = false;
			$json['error']       = "The specified store does not exist.";
		}

		if ($this->debugIt) {
			echo '<pre>';
			print_r($json);
			echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}
	}


	/*
	* COUNTRY FUNCTIONS
	*/	
	public function countries() {

		$this->checkPlugin();

		if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
			//get country details
			if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
				$this->getCountry($this->request->get['id']);
			}else {
				$this->listCountries();
			}
		}		
    }

	/*
	* Get countries
	*/
	private function listCountries() {

		$json = array('success' => true);
	
		$this->load->model('localisation/country');
		
		$results = $this->model_localisation_country->getCountries();

		$data = array();
		
		foreach ($results as $result) {
			$data[] = array(
				'country_id'		=> $result['country_id'],
				'name'				=> $result['name'],
				'iso_code_2'		=> $result['iso_code_2'],
				'iso_code_3'		=> $result['iso_code_3'],
				'address_format'    => $result['address_format'],
				'postcode_required' => $result['postcode_required'],
				'status'			 => $result['status']
			);
		}
		
		if(count($results) == 0){
			$json['success'] 	= false;
			$json['error'] 		= "No country found";
		}else {
			$json['data'] 		= $data;			
		}

		if ($this->debugIt) {
				echo '<pre>';
				print_r($json);
		} else {
				$this->response->setOutput(json_encode($json));
		}
    }
	
	/*
	* Get country details
	*/
	public function getCountry($country_id) {
		
		$json = array('success' => true);
		
		$this->load->model('localisation/country');

    	$country_info = $this->model_localisation_country->getCountry($country_id);
		
		if(!empty($country_info)){
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($country_id),
				'status'            => $country_info['status']		
			);
		}else {
			$json['success']     = false;
			$json['error']       = "The specified country does not exist.";
		}

		if ($this->debugIt) {
			echo '<pre>';
			print_r($json);
			echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}
	}

	/*
	* SESSION FUNCTIONS
	*/	
	public function session() {

		$this->checkPlugin();

		if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
			//get session details			
				$this->getSessionId();
		}		
    }
    
	/*
	* Get current session id
	*/
	public function getSessionId() {
		
		$json = array('success' => true);
		
		$json['data'] = array('session' => session_id());		

		if ($this->debugIt) {
			echo '<pre>';
			print_r($json);
			echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}
	}
	

	/*
	* PRODUCT IMAGE MANAGEMENT FUNCTIONS
	*/	
	public function productimages() {

		$this->checkPlugin();

		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
			//upload and save image			
			$this->saveProductImage($this->request);
		}		
    }
    
	/*
	* Upload and save product image
	*/
	public function saveProductImage($request) {
		
		$json = array('success' => false);       
              
		$this->load->model('catalog/product');
					
		if (ctype_digit($request->get['id'])) {
			$product = $this->model_catalog_product->getProduct($request->get['id']);
			//check product exists
			if(!empty($product)) {
               if(isset($request->files['file'])){ 
					$uploadResult = $this->upload($request->files['file'], "products");
					if(!isset($uploadResult['error'])){
						$json['success']     = true;
						$this->model_catalog_product->setProductImage($request->get['id'], $uploadResult['file_path']);
					}else{
						$json['error']	= $uploadResult['error'];
					}
				} else {
					$json['error']	= "File is required!";
				}
			}else {
				$json['success']	= false;
				$json['error']      = "The specified product does not exist.";
			}
		} else {
				$json['success']    = false;
		}	
	
		if ($this->debugIt) {
			echo '<pre>';
			print_r($json);
			echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}
	}


	/*
	* CATEGORY IMAGE MANAGEMENT FUNCTIONS
	*/	
	public function categoryimages() {

		$this->checkPlugin();

		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
			//upload and save image			
			$this->saveCategoryImage($this->request);
		}		
    }
    
	/*
	* Upload and save category image
	*/
	public function saveCategoryImage($request) {
		$json = array('success' => false);       
              
		$this->load->model('catalog/category');
					
		if (ctype_digit($request->get['id'])) {
			$category = $this->model_catalog_category->getCategory($request->get['id']);
			//check category exists
			if(!empty($category)) {
				if(isset($request->files['file'])){
					$uploadResult = $this->upload($request->files['file'], "categories");
					if(!isset($uploadResult['error'])){
						$json['success']     = true;
						$this->model_catalog_category->setCategoryImage($request->get['id'], $uploadResult['file_path']);
					}else{
						$json['error']	= $uploadResult['error'];
					}
				} else {
					$json['error']	= "File is required!";
				}
			}else {
				$json['success']	= false;
				$json['error']      = "The specified category does not exist.";
			}
		} else {
				$json['success']    = false;
		}	
	
		if ($this->debugIt) {
			echo '<pre>';
			print_r($json);
			echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}
	}
    
    	/*
	* GET UTC AND LOCAL TIME DIFFERENCE
    	* returns offset in seconds
	*/	
	public function utc_offset() {

	$this->checkPlugin();
        
        $json = array('success' => false);
        
    	if ( $_SERVER['REQUEST_METHOD'] === 'GET' ){
		$serverTimeZone = date_default_timezone_get();
		$timezone = new DateTimeZone($serverTimeZone);
		$now = new DateTime("now", $timezone);
		$offset = $timezone->getOffset($now);

		$json['data'] = array('offset' => $offset);
		$json['success'] = true;
	}
        
        if ($this->debugIt) {
		echo '<pre>';
		print_r($json);
		echo '</pre>';
	} else {
		$this->response->setOutput(json_encode($json));
	}		
    }

	/*
	* MANUFACTURER IMAGE MANAGEMENT FUNCTIONS
	*/	
	public function manufacturerimages() {

		$this->checkPlugin();

		if ( $_SERVER['REQUEST_METHOD'] === 'POST' ){
			//upload and save manufacturer image			
			$this->saveManufacturerImage($this->request);
		}		
    }
    
	/*
	* Upload and save manufacturer image
	*/
	public function saveManufacturerImage($request) {
		
		$json = array('success' => false);       
              
		$this->load->model('catalog/manufacturer');
					
		if (ctype_digit($request->get['id'])) {
			$manufacturer = $this->model_catalog_manufacturer->getManufacturer($request->get['id']);
			//check manufacturer exists
			if(!empty($manufacturer)) {
				if(isset($request->files['file'])){
					$uploadResult = $this->upload($request->files['file'], "manufacturers");
					if(!isset($uploadResult['error'])){
						$json['success']     = true;
						$this->model_catalog_manufacturer->setManufacturerImage($request->get['id'], $uploadResult['file_path']);
					}else{
						$json['error']	= $uploadResult['error'];
					}
				} else {
					$json['error']	= "File is required!";
				}
			}else {
				$json['success']	= false;
				$json['error']      = "The specified manufacturer does not exist.";
			}
		} else {
				$json['success']    = false;
		}	
	
		if ($this->debugIt) {
			echo '<pre>';
			print_r($json);
			echo '</pre>';
		} else {
			$this->response->setOutput(json_encode($json));
		}
	}

	
	//Image upload
	public function upload($uploadedFile, $subdirectory) {
		$this->language->load('product/product');
		
		$result = array();
		
		if (!empty($uploadedFile['name'])) {
			$filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($uploadedFile['name'], ENT_QUOTES, 'UTF-8')));
			
			if ((utf8_strlen($filename) < 3) || (utf8_strlen($filename) > 64)) {
        		$result['error'] = $this->language->get('error_filename');
	  		}	  	

			// Allowed file extension types
			$allowed = array();
			
			$filetypes = explode("\n", $this->config->get('config_file_extension_allowed'));
			
			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}

			if (!in_array(substr(strrchr($filename, '.'), 1), $allowed)) {
				$result['error'] = $this->language->get('error_filetype');
       		}	
			
			// Allowed file mime types		
		    $allowed = array();
			
			$filetypes = explode("\n", $this->config->get('config_file_mime_allowed'));
			
			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}
	
			if (!in_array($uploadedFile['type'], $allowed)) {
				$result['error'] = $this->language->get('error_filetype');
			}
						
			if ($uploadedFile['error'] != UPLOAD_ERR_OK) {
				$result['error'] = $this->language->get('error_upload_' . $uploadedFile['error']);
			}
		} else {
			$result['error'] = $this->language->get('error_upload');
		}

		if (!$result && is_uploaded_file($uploadedFile['tmp_name']) && file_exists($uploadedFile['tmp_name'])) {
			$file = basename($filename) . '.' . md5(mt_rand());
			
			// Hide the uploaded file name so people can not link to it directly.
			$result['file'] = $this->encryption->encrypt($file);

			$result['file_path'] = "data/".$subdirectory."/".$filename;
			if($this->rmkdir(DIR_IMAGE."data/".$subdirectory)){
				move_uploaded_file($uploadedFile['tmp_name'], DIR_IMAGE .$result['file_path']);
			}else{
				$result['error'] = "Could not create directory or directory is not writeable: ".DIR_IMAGE ."data/".$subdirectory;
			}			
			$result['success'] = $this->language->get('text_upload');
		}	
		return $result;
	
	}
	
	/**
	 * Makes directory and returns BOOL(TRUE) if exists OR made.
	 *
	 * @param  $path Path name
	 * @return bool
	 */
	function rmkdir($path, $mode = 0777) {
		
		if (!file_exists($path)) {
			$path = rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", $path), "/");
			$e = explode("/", ltrim($path, "/"));
			if(substr($path, 0, 1) == "/") {
				$e[0] = "/".$e[0];
			}
			$c = count($e);
			$cp = $e[0];
			for($i = 1; $i < $c; $i++) {
				if(!is_dir($cp) && !@mkdir($cp, $mode)) {
					return false;
				}
				$cp .= "/".$e[$i];
			}
			return @mkdir($path, $mode);
		}

		if (is_writable($path)) {
			return true;
		}else {
			return false;
		}
	}

	//date format validator
	private function validateDate($date, $format = 'Y-m-d H:i:s')
	{
		$d = DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) == $date;
	}

}

if( !function_exists('apache_request_headers') ) {
    function apache_request_headers() {
        $arh = array();
        $rx_http = '/\AHTTP_/';

        foreach($_SERVER as $key => $val) {
            if( preg_match($rx_http, $key) ) {
                $arh_key = preg_replace($rx_http, '', $key);
                $rx_matches = array();
           // do some nasty string manipulations to restore the original letter case
           // this should work in most cases
                $rx_matches = explode('_', $arh_key);

                if( count($rx_matches) > 0 and strlen($arh_key) > 2 ) {
                    foreach($rx_matches as $ak_key => $ak_val) {
                        $rx_matches[$ak_key] = ucfirst($ak_val);
                    }

                    $arh_key = implode('-', $rx_matches);
                }

                $arh[$arh_key] = $val;
            }
        }
        
        return( $arh );
    }
}
