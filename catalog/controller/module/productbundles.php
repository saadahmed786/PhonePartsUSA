<?php  
class ControllerModuleProductbundles extends Controller {
	protected function index() {
		$this->language->load('module/productbundles');
		$this->load->model('tool/image');
      	$this->data['heading_title'] = $this->language->get('heading_title');
      	$this->data['button_cart'] = $this->language->get('button_cart');
		
		$this->data['ProductBundles_BundlePrice'] = $this->language->get('ProductBundles_BundlePrice');
		$this->data['ProductBundles_YouSave'] = $this->language->get('ProductBundles_YouSave');
		$this->data['ProductBundles_AddBundleToCart'] = $this->language->get('ProductBundles_AddBundleToCart');

		$this->data['currenttemplate'] =  (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template'));
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$this->data['data']['ProductBundles'] = str_replace('http', 'https', $this->config->get('ProductBundles'));
		} else {
			$this->data['data']['ProductBundles'] = $this->config->get('ProductBundles');
		}
		$this->document->addScript('catalog/view/javascript/productbundles/pb_timepicker.js');
		$this->document->addScript('catalog/view/javascript/productbundles/pb_ajaxupload.js');
		$this->document->addScript('catalog/view/javascript/productbundles/fancybox/jquery.fancybox.pack.js');
		$this->document->addStyle('catalog/view/javascript/productbundles/fancybox/jquery.fancybox.css');

		
		if(!isset($this->data['data']['ProductBundles']['WidgetTitle'][$this->config->get('config_language')])){
			$this->data['data']['ProductBundles']['WidgetTitle'] = '';
		} else {
			$this->data['data']['ProductBundles']['WidgetTitle'] = $this->data['data']['ProductBundles']['WidgetTitle'][$this->config->get('config_language')];
		}
			$this->data['data']['ProductBundlesConfig'] = $this->config->get('productbundles_module');
			
		if (isset($this->data['data']['ProductBundles']['ShowCloseButton']) && ($this->data['data']['ProductBundles']['ShowCloseButton']=='yes')) {
			$this->data['CloseButton'] = 'true';
		} else {
			$this->data['CloseButton'] = 'false';
		}
		
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/productbundles.tpl')) {
			$this->document->addStyle('catalog/view/theme/'.(($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/stylesheet/productbundles.css');
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/productbundles.tpl';
		} else {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/productbundles.css');
			$this->template = 'default/template/module/productbundles.tpl';
		}
		
		$this->data['CustomBundles'] = $this->config->get('productbundles_custom');
		$this->data['ShowTheModule'] = false;
		$bundles = array();
		
		if ((isset($this->request->get['product_id'])) && isset($this->data['CustomBundles'])) {
			$productID = $this->request->get['product_id'];
			
			foreach ($this->data['CustomBundles'] as $CustomBundles) {
				
				if (isset($CustomBundles['productsShow'])) {
					if (in_array($productID, $CustomBundles['productsShow'])) {
						$bundles[] = $CustomBundles['id'];
						$this->data['ShowTheModule'] = true;
					}
				}
			}
			
			if ($this->data['ShowTheModule'] == true) {
				$rand_bundle = array_rand($bundles, 1);
				$this->data['BundleNumber'] = $bundles[$rand_bundle];
			
				$this->data['BundleProducts'] = "";
				$this->data['products'] = array();
				
				if (empty($this->data['data']['ProductBundles']['PictureWidth'])) 
					$picture_width=80; else $picture_width=$this->data['data']['ProductBundles']['PictureWidth'];
				if (empty($this->data['data']['ProductBundles']['PictureHeight'])) 
					$picture_height=80; else $picture_height=$this->data['data']['ProductBundles']['PictureHeight'];
					
				$TotalPrice = 0;
				$i=0;	
				$this->data['productOptions']=false;
				
				foreach ($this->data['CustomBundles'][$this->data['BundleNumber']]['products'] as $result) {
					$product_info = $this->model_catalog_product->getProduct($result);
					
					if ($i!=0) {
						$this->data['BundleProducts'] .= "_";
					}
					$this->data['BundleProducts'] .= $result;
						if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], $picture_width, $picture_height);
						} else {
							$image = false;
						}
						  
						if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
							$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
						} else {
							$price = false;
						}
						
								
						if ((float)$product_info['special']) {
							$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')));
							$TotalPrice+=$this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax'));					
						} else {
							$TotalPrice+=$this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'));
							$special = false;
						}
						
						if ($this->config->get('config_review_status')) {
							$rating = (int)$product_info['rating'];
						} else {
							$rating = false;
						}
						
						$product_options = $this->model_catalog_product->getProductOptions($product_info['product_id']);
						if (!empty($product_options)) $this->data['productOptions'] = true;
						$this->data['products'][] = array(
							'product_id' => $product_info['product_id'],
							'thumb'   	 => $image,
							'name'    	 => $product_info['name'],
							'price'   	 => $price,
							'special' 	 => $special,
							'rating'     => $rating,
							'reviews'    => sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']),
							'href'    	 => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
						);
						$i++;
				}
				
				$VoucherPrice = $this->data['CustomBundles'][$this->data['BundleNumber']]['voucherprice'];
				$FinalPrice = $TotalPrice-$VoucherPrice;
				$this->data['VoucherData'] = $VoucherPrice;
				$this->data['TotalPrice'] = $this->currency->format($TotalPrice);
				$this->data['VoucherPrice'] = $this->currency->format($VoucherPrice);
				$this->data['FinalPrice'] = $this->currency->format($FinalPrice);
				
			}
		}

		if ((isset($this->request->get['path'])) && isset($this->data['CustomBundles']) && (!isset($this->request->get['product_id']))) {
			$category = (explode("_", $this->request->get['path']));
			if (isset($category[1]))
				$categoryID = end($category);
			else
				$categoryID = $this->request->get['path'];

			foreach ($this->data['CustomBundles'] as $CustomBundles) {
				if (isset($CustomBundles['categoriesShow'])) {
					if (in_array($categoryID, $CustomBundles['categoriesShow'])) {
					$bundles[] = $CustomBundles['id'];
					$this->data['ShowTheModule'] = true;
					}
				}
			}
			
			if ($this->data['ShowTheModule'] == true) {
				$rand_bundle = array_rand($bundles, 1);
				$this->data['BundleNumber'] = $bundles[$rand_bundle];
			
				$this->data['BundleProducts'] = "";
				$this->data['products'] = array();
				
				if (empty($this->data['data']['ProductBundles']['PictureWidth'])) 
					$picture_width=80; else $picture_width=$this->data['data']['ProductBundles']['PictureWidth'];
				if (empty($this->data['data']['ProductBundles']['PictureHeight'])) 
					$picture_height=80; else $picture_height=$this->data['data']['ProductBundles']['PictureHeight'];
					
				$TotalPrice = 0;
				$i=0;	
				$this->data['productOptions']=false;
				
				foreach ($this->data['CustomBundles'][$this->data['BundleNumber']]['products'] as $result) {
					$product_info = $this->model_catalog_product->getProduct($result);
					
					if ($i!=0) {
						$this->data['BundleProducts'] .= "_";
					}
					$this->data['BundleProducts'] .= $result;
						if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], $picture_width, $picture_height);
						} else {
							$image = false;
						}
						  
						if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
							$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
						} else {
							$price = false;
						}
						
								
						if ((float)$product_info['special']) {
							$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')));
							$TotalPrice+=$this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax'));					
						} else {
							$TotalPrice+=$this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'));
							$special = false;
						}
						
						if ($this->config->get('config_review_status')) {
							$rating = (int)$product_info['rating'];
						} else {
							$rating = false;
						}
						
						$product_options = $this->model_catalog_product->getProductOptions($product_info['product_id']);
						if (!empty($product_options)) $this->data['productOptions'] = true;
						$this->data['products'][] = array(
							'product_id' => $product_info['product_id'],
							'thumb'   	 => $image,
							'name'    	 => $product_info['name'],
							'price'   	 => $price,
							'special' 	 => $special,
							'rating'     => $rating,
							'reviews'    => sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']),
							'href'    	 => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
						);
						$i++;
				}
				
				$VoucherPrice = $this->data['CustomBundles'][$this->data['BundleNumber']]['voucherprice'];
				$FinalPrice = $TotalPrice-$VoucherPrice;
				$this->data['VoucherData'] = $VoucherPrice;
				$this->data['TotalPrice'] = $this->currency->format($TotalPrice);
				$this->data['VoucherPrice'] = $this->currency->format($VoucherPrice);
				$this->data['FinalPrice'] = $this->currency->format($FinalPrice);
				
			}
		}
		
		if (($this->data['ShowTheModule'] == false) && isset($this->data['data']['ProductBundles']['ShowRandomBundles']) && ($this->data['data']['ProductBundles']['ShowRandomBundles'] == 'yes')) {
			if (sizeof($this->data['CustomBundles'])>0) {
				$rand_bundle = array_rand($this->data['CustomBundles'], 1);
				$this->data['ShowTheModule'] = true;
				$this->data['BundleProducts'] = "";
				$this->data['products'] = array();
				$this->data['BundleNumber'] = $rand_bundle;
				if (empty($this->data['data']['ProductBundles']['PictureWidth'])) 
					$picture_width=80; else $picture_width=$this->data['data']['ProductBundles']['PictureWidth'];
				if (empty($this->data['data']['ProductBundles']['PictureHeight'])) 
					$picture_height=80; else $picture_height=$this->data['data']['ProductBundles']['PictureHeight'];
					
				$TotalPrice = 0;
				$i=0;	
				$this->data['productOptions']=false;
				
				foreach ($this->data['CustomBundles'][$rand_bundle]['products'] as $result) {
					$product_info = $this->model_catalog_product->getProduct($result);
					
					if ($i!=0) {
						$this->data['BundleProducts'] .= "_";
					}
					$this->data['BundleProducts'] .= $result;
						if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], $picture_width, $picture_height);
						} else {
							$image = false;
						}
						  
						if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
							$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
						} else {
							$price = false;
						}
						
								
						if ((float)$product_info['special']) {
							$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')));
							$TotalPrice+=$this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax'));					
						} else {
							$TotalPrice+=$this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'));
							$special = false;
						}
						
						if ($this->config->get('config_review_status')) {
							$rating = (int)$product_info['rating'];
						} else {
							$rating = false;
						}
						
						$product_options = $this->model_catalog_product->getProductOptions($product_info['product_id']);
						if (!empty($product_options)) $this->data['productOptions'] = true;
						$this->data['products'][] = array(
							'product_id' => $product_info['product_id'],
							'thumb'   	 => $image,
							'name'    	 => $product_info['name'],
							'price'   	 => $price,
							'special' 	 => $special,
							'rating'     => $rating,
							'reviews'    => sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']),
							'href'    	 => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
						);
						$i++;
				}
				
				$VoucherPrice = $this->data['CustomBundles'][$rand_bundle]['voucherprice'];
				$FinalPrice = $TotalPrice-$VoucherPrice;
				$this->data['VoucherData'] = $VoucherPrice;
				$this->data['TotalPrice'] = $this->currency->format($TotalPrice);
				$this->data['VoucherPrice'] = $this->currency->format($VoucherPrice);
				$this->data['FinalPrice'] = $this->currency->format($FinalPrice);
			}
		}
		
		$this->render();
	}
	
	public function bundleproductoptions() {
		$this->language->load('module/productbundles');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		$this->load->model('setting/setting');
		
		$this->data['text_select'] = $this->language->get('text_select');
		$this->data['text_option'] = $this->language->get('text_option');
		$this->data['text_option_heading'] = $this->language->get('text_option_heading');
		$this->data['heading_title'] = $this->language->get('heading_title');
      	$this->data['button_cart'] = $this->language->get('button_cart');
		$this->data['button_upload'] = $this->language->get('button_upload');
		$this->data['AdditionalFees'] = $this->language->get('AdditionalFees');
		$this->data['Continue'] = $this->language->get('Continue');
		
		$this->data['currenttemplate'] =  (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template'));
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$this->data['data']['ProductBundles'] = str_replace('http', 'https', $this->config->get('ProductBundles'));
		} else {
			$this->data['data']['ProductBundles'] = $this->config->get('ProductBundles');
		}
		
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/productbundles_options.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/productbundles_options.tpl';
		} else {
			$this->template = 'default/template/module/productbundles_options.tpl';
		}

		$this->data['CustomBundles'] = $this->config->get('productbundles_custom');
		$this->data['ShowPage'] = false;

		if (isset($_GET['bundle'])) {
		
			$this->data['BundleNumber'] = $_GET['bundle'];
			$this->data['ShowPage'] = true;

			$CurrentBundle = $this->data['CustomBundles'][$this->data['BundleNumber']];
			$i=0;
			$TotalPrice = 0;
			$this->data['BundleProducts'] = "";

				$picture_width=128;
				$picture_height=128;
			
			foreach ($CurrentBundle['products'] as $result) {
				$product_info = $this->model_catalog_product->getProduct($result);

				if ($i!=0) {
					$this->data['BundleProducts'] .= "_";
				}

				$this->data['BundleProducts'] .= $result;
					if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], $picture_width, $picture_height);
					} else {
						$image = false;
					}
					  
					if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
					} else {
						$price = false;
					}
							
					if ((float)$product_info['special']) {
						$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')));
						$TotalPrice+=$this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax'));					
					} else {
						$TotalPrice+=$this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'));
						$special = false;
					}
					
					if ($this->config->get('config_review_status')) {
						$rating = (int)$product_info['rating'];
					} else {
						$rating = false;
					}
					
					$product_options = $this->model_catalog_product->getProductOptions($product_info['product_id']);
					$this->data['options'] = array();
					
					foreach ($this->model_catalog_product->getProductOptions($product_info['product_id']) as $option) { 
						if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') { 
							$option_value_data = array();
		
							foreach ($option['option_value'] as $option_value) {
								if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
									if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
										$option_price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
									} else {
										$option_price = 0;
									}
		
									$option_value_data[] = array(
										'product_option_value_id' => $option_value['product_option_value_id'],
										'option_value_id'         => $option_value['option_value_id'],
										'name'                    => $option_value['name'],
										'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
										'price'                   => $option_price,
										'price_prefix'            => $option_value['price_prefix']
									);
								}
							}
		
							$this->data['options'][] = array(
								'product_option_id' => $option['product_option_id'],
								'option_id'         => $option['option_id'],
								'name'              => $option['name'],
								'type'              => $option['type'],
								'option_value'      => $option_value_data,
								'required'          => $option['required']
							);					
						} elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
							$this->data['options'][] = array(
								'product_option_id' => $option['product_option_id'],
								'option_id'         => $option['option_id'],
								'name'              => $option['name'],
								'type'              => $option['type'],
								'option_value'      => $option['option_value'],
								'required'          => $option['required']
							);						
						}
					}
					$this->data['products'][] = array(
						'product_id' => $product_info['product_id'],
						'thumb'   	 => $image,
						'name'    	 => $product_info['name'],
						'price'   	 => $price,
						'special' 	 => $special,
						'rating'     => $rating,
						'reviews'    => sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']),
						'href'    	 => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
						'options' => $this->data['options']
					);
					$i++;
			}
			$VoucherPrice = $this->tax->calculate($CurrentBundle['voucherprice'], $product_info['tax_class_id'], $this->config->get('config_tax'));
			$FinalPrice = $TotalPrice-$VoucherPrice;
			$this->data['VoucherData'] = $VoucherPrice;
			$this->data['TotalPrice'] = $this->currency->format($TotalPrice);
			$this->data['VoucherPrice'] = $this->currency->format($VoucherPrice);
			$this->data['FinalPrice'] = $this->currency->format($FinalPrice);
		
		}
		$this->response->setOutput($this->render());
	}
	
	public function bundletocartoptions() {
		if ((isset($_POST)) && (($_POST['products']) && ($_POST['discount']))) {
			
		$this->load->model("catalog/product");	
		$this->language->load('module/productbundles');

		$products = explode("_", $_POST['products']); // Explode products
		
		if (isset($this->request->post['option'])) {  // Product Options
			$option = $this->request->post['option'];
		} else {
			$option = array();	
		}
	
		$json = array();

		foreach ($products as $key=>$p) { // Check for empty but required product options
			$product_options = $this->model_catalog_product->getProductOptions($p);
				foreach ($product_options as $product_option) {
					if ($product_option['required'] && empty($option[$key][$product_option['product_option_id']])) {
						if (empty($json['error']['option'][$product_option['product_option_id']])) {
						 	$json['error']['option'][$product_option['product_option_id']] = array();
						}
						$json['error']['option'][$product_option['product_option_id']][] = array(
							'message' => sprintf($this->language->get('error_required'), $product_option['name']),
							'key' 	 => $key 
						);
				}
			}
		}
		$config = $this->config->get('productbundles_custom');
	
			if (($config[$_POST['bundle']]['products'] == $products) && (isset($_POST['discount'])) && (isset($_POST['bundle']))) {
				
				if (!$json) {
					foreach ($products as $key=>$p) {
						$p_option = $p_option = !empty($option[$key]) ? $option[$key] : '';
						$this->cart->add($p, 1, $p_option, "");
					}
					$json['bundle_code'] = $_POST['bundle'];
					$json['success'] = "1";
				}
			} else {
				//echo "ERROR 1!";	
			}
		} else {
			//echo "ERROR 2!";	
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function bundletocart() {
		$json = array();
		if ((isset($_POST)) && (($_POST['products']) && ($_POST['discount']))) {
			$products = explode("_", $_POST['products']);
			$this->load->model("catalog/product");
			$config = $this->config->get('productbundles_custom');

			if ( ($config[$_POST['bundle']]['products'] == $products) && (isset($_POST['discount'])) && (isset($_POST['bundle'])) ) {
				
				if (!$json) {
					foreach ($products as $p) {
						$this->cart->add($p, 1, "", "");
					}
					$json['bundle_code'] = $_POST['bundle'];
					$json['success'] = 1;
				}	
			} else {
				$json['error'] = 'error';
			}
		} else {
			$json['error'] = 'error';	
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function listing() {
		$this->language->load('module/productbundles');
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		$this->load->model('setting/setting');
		
		$this->data['ProductBundles_BundlePrice'] = $this->language->get('ProductBundles_BundlePrice');
		$this->data['ProductBundles_YouSave'] = $this->language->get('ProductBundles_YouSave');
		$this->data['ProductBundles_AddBundleToCart'] = $this->language->get('ProductBundles_AddBundleToCart');
		
		$this->data['heading_title'] = $this->language->get('listing_heading_title');
		
		$this->data['breadcrumbs'] = array();
		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),			
			'separator' => false
		);
		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_breadcrumb'),
			'href'      => $this->url->link('module/productbundles/listing'),
			'separator' => $this->language->get('text_separator')
		);
		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$this->data['data']['ProductBundles'] = str_replace('http', 'https', $this->config->get('ProductBundles'));
		} else {
			$this->data['data']['ProductBundles'] = $this->config->get('ProductBundles');
		}
		$this->data['CustomBundles'] = $this->config->get('productbundles_custom');	
		
		$this->document->setTitle($this->data['data']['ProductBundles']['PageTitle'][$this->config->get('config_language_id')]);
		$this->document->setDescription($this->data['data']['ProductBundles']['MetaDescription'][$this->config->get('config_language_id')]);
		$this->document->setKeywords($this->data['data']['ProductBundles']['MetaKeywords'][$this->config->get('config_language_id')]);

		$this->document->addScript('catalog/view/javascript/productbundles/fancybox/jquery.fancybox.pack.js');
		$this->document->addStyle('catalog/view/javascript/productbundles/fancybox/jquery.fancybox.css');
		$this->document->addScript('catalog/view/javascript/productbundles/pb_timepicker.js');
		$this->document->addScript('catalog/view/javascript/productbundles/pb_ajaxupload.js');
		
		$picture_width		= isset($this->data['data']['ProductBundles']['ListingPictureWidth']) ? $this->data['data']['ProductBundles']['ListingPictureWidth'] : '100';
		$picture_height		= isset($this->data['data']['ProductBundles']['ListingPictureHeight']) ? $this->data['data']['ProductBundles']['ListingPictureHeight'] : '100';
		
		$Bundles			= array();
		$n					= 0;
		
		foreach ($this->data['CustomBundles'] as $CustomBundle) {
			$Bundles[$n] = $CustomBundle;
			$n++;
		}

		$limit = isset($this->data['data']['ProductBundles']['ListingLimit']) ? $this->data['data']['ProductBundles']['ListingLimit'] : '10';
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else { 
			$page = 1;
		}
		
		$start = ($page - 1) * $limit;
		
		for ($n=$start; $n < ($limit+$start); $n++) {
			if (!isset($Bundles[$n]['id'])) 
				break;
			$this->data['Bundles'][$n]['BundleNumber'] = $Bundles[$n]['id'];
			$CurrentBundle = $Bundles[$n];
			$i=0;
			$TotalPrice = 0;
			$this->data['Bundles'][$n]['BundleProducts'] = "";

			foreach ($CurrentBundle['products'] as $result) {
				$product_info = $this->model_catalog_product->getProduct($result);

				if ($i!=0) {
					$this->data['Bundles'][$n]['BundleProducts'] .= "_";
				}
				$this->data['Bundles'][$n]['BundleProducts'] .= $result;
				
				if ($product_info['image']) {
					$image = $this->model_tool_image->resize($product_info['image'], $picture_width, $picture_height);
				} else {
					$image = false;
				}
				  
				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$price = false;
				}
						
				if ((float)$product_info['special']) {
					$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')));
					$TotalPrice+=$this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax'));					
				} else {
					$TotalPrice+=$this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax'));
					$special = false;
				}
					
				$product_options = $this->model_catalog_product->getProductOptions($product_info['product_id']);
				if (!empty($product_options))
					$this->data['Bundles'][$n]['productOptions'] = true;
				else
					$this->data['Bundles'][$n]['productOptions'] = false;
					
				$this->data['options'] = array();
				foreach ($this->model_catalog_product->getProductOptions($product_info['product_id']) as $option) { 
					if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') { 
						$option_value_data = array();
	
						foreach ($option['option_value'] as $option_value) {
							if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
								if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
									$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
								}
	
								$option_value_data[] = array(
									'product_option_value_id' => $option_value['product_option_value_id'],
									'option_value_id'         => $option_value['option_value_id'],
									'name'                    => $option_value['name'],
									'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
									'price'                   => $price,
									'price_prefix'            => $option_value['price_prefix']
								);
							}
						}
	
						$this->data['options'][] = array(
							'product_option_id' => $option['product_option_id'],
							'option_id'         => $option['option_id'],
							'name'              => $option['name'],
							'type'              => $option['type'],
							'option_value'      => $option_value_data,
							'required'          => $option['required']
						);					
					} elseif ($option['type'] == 'text' || $option['type'] == 'textarea' || $option['type'] == 'file' || $option['type'] == 'date' || $option['type'] == 'datetime' || $option['type'] == 'time') {
						$this->data['options'][] = array(
							'product_option_id' => $option['product_option_id'],
							'option_id'         => $option['option_id'],
							'name'              => $option['name'],
							'type'              => $option['type'],
							'option_value'      => $option['option_value'],
							'required'          => $option['required']
						);						
					}
				}
				
				$this->data['Bundles'][$n]['products'][] = array(
					'product_id' => $product_info['product_id'],
					'thumb'   	 => $image,
					'name'    	 => $product_info['name'],
					'price'   	 => $price,
					'special' 	 => $special,
					'href'    	 => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
					'options' => $this->data['options']
				);
				$i++;
			}
							
			$VoucherPrice = $this->tax->calculate($CurrentBundle['voucherprice'], $product_info['tax_class_id'], $this->config->get('config_tax'));
			$FinalPrice = $TotalPrice-$VoucherPrice;
			$this->data['Bundles'][$n]['VoucherData'] = $VoucherPrice;
			$this->data['Bundles'][$n]['TotalPrice'] = $this->currency->format($TotalPrice);
			$this->data['Bundles'][$n]['VoucherPrice'] = $this->currency->format($VoucherPrice);
			$this->data['Bundles'][$n]['FinalPrice'] = $this->currency->format($FinalPrice);
		}
		
	
		$url = '';
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}	
		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}
		
		$pagination = new Pagination();
		$pagination->total = sizeof($this->data['CustomBundles']);
		$pagination->page = $page;
		$pagination->limit = $limit;
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('module/productbundles/listing', $url.'&page={page}');
		
		$this->data['pagination']		 = $pagination->render();
		
		$this->data['currenttemplate']	 =  (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template'));
		$this->children = array(
			'common/column_left',
			'common/column_right',
			'common/content_top',
			'common/content_bottom',
			'common/footer',
			'common/header'
		);
		
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/productbundles_listing.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/productbundles_listing.tpl';
			$this->document->addStyle('catalog/view/theme/'.(($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/stylesheet/productbundles.css');
		} else {
			$this->template = 'default/template/module/productbundles_listing.tpl';
			$this->document->addStyle('catalog/view/theme/default/stylesheet/productbundles.css');
		}

		$this->response->setOutput($this->render());
	}
}
?>