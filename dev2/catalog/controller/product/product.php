<?php  
class ControllerProductProduct extends Controller {
	private $error = array(); 
	public function gradeDiscounts() {
		$this->language->load('product/product');
		$this->load->model('catalog/product');
		$discounts = $this->model_catalog_product->getProductDiscounts($this->request->get['product_id']);
		// var_dump($this->request->get['product_id']);exit;
		$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
			$json['discounts'] = array(); 
			if (!$discounts){
			//$price = getUpdatedPrice($this->request->get['product_id']);
			$json['error'] = 'error';
			$json['discounts'][] = array(
				'quantity' => '1',
				'price'    => $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')))
				);
			} else {
				
				$json['success'] = 'success';
				$json['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
					foreach ($discounts as $key=> $discount) {
					$json['discounts'][] = array(
						'quantity' => $discount['quantity']. ($discount === end($discounts) ? '+' : ' - ' . ( $discounts[$key+1]['quantity'] - 1 )),
						'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')))
						);
					}
			}
			
			
			echo json_encode($json);
			exit;
	} 
	public function index() { 
		$this->load->model('catalog/product');
		$this->load->model('catalog/catalog');
		// $this->document->addStyle('catalog/view/theme/' . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')). '/stylesheet/products.css');
		if($this->session->data['temp_theme']=='ppusa2.0')
		{
			  // $this->document->addScript('catalog/view/javascript/js/elevatezoom.js');
		}

		$this->language->load('product/product');

		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home'),			
			'separator' => false
			);
		
		$this->load->model('catalog/category');	
		
		if (isset($this->request->get['path'])) {
			$path = '';

			foreach (explode('_', $this->request->get['path']) as $path_id) {
				if (!$path) {
					$path = $path_id;
				} else {
					$path .= '_' . $path_id;
				}
				
				$category_info = $this->model_catalog_category->getCategory($path_id);
				
				if ($category_info) {
					$this->data['breadcrumbs'][] = array(
						'text'      => $category_info['name'],
						'href'      => $this->url->link('product/category', 'path=' . $path),
						'separator' => $this->language->get('text_separator')
						);
				}
			}
		}
		
		$this->load->model('catalog/manufacturer');	
		
		if (isset($this->request->get['manufacturer_id'])) {
			$this->data['breadcrumbs'][] = array( 
				'text'      => $this->language->get('text_brand'),
				'href'      => $this->url->link('product/manufacturer'),
				'separator' => $this->language->get('text_separator')
				);	

			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($this->request->get['manufacturer_id']);

			if ($manufacturer_info) {	
				$this->data['breadcrumbs'][] = array(
					'text'	    => $manufacturer_info['name'],
					'href'	    => $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $this->request->get['manufacturer_id']),					
					'separator' => $this->language->get('text_separator')
					);
			}
		}
		
		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_tag'])) {
			$url = '';
			
			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . $this->request->get['filter_name'];
			}

			if (isset($this->request->get['filter_tag'])) {
				$url .= '&filter_tag=' . $this->request->get['filter_tag'];
			}

			if (isset($this->request->get['filter_description'])) {
				$url .= '&filter_description=' . $this->request->get['filter_description'];
			}
			
			if (isset($this->request->get['filter_category_id'])) {
				$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
			}	

			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_search'),
				'href'      => $this->url->link('product/search', $url),
				'separator' => $this->language->get('text_separator')
				);	
		}
		
		if (isset($this->request->get['product_id'])) {
			$product_id = $this->request->get['product_id'];
		} else {
			$product_id = 0;
		}
		
		
		$product_info = $this->model_catalog_product->getProduct($product_id);
		// if($product_id=='12237')
		// {
		// 	echo '<pre>';
		// print_r($product_info);
		// echo '</pre>';exit;	
		// }
		
		//$this->data['submodels'] = $this->model_catalog_catalog->loadSubModels();
		// echo 'here';exit;
$this->data['compatibles'] = $this->model_catalog_product->getDeviceModels($product_info['product_id']);
if (!$this->data['compatibles'] && $product_info['main_sku']) {
		 	$main_product_id = $this->model_catalog_product->getProductIDbySku($product_info['main_sku']);
		 	$this->data['compatibles'] = $this->model_catalog_product->getDeviceModels($main_product_id);
		 }
// print_r($this->data['compatibles']);exit;
		$class_details = $this->model_catalog_product->getProductClass($product_info['model']);
		$product_info['class'] = $class_details;
		$product_info['item_grade'] = (trim($product_info['item_grade'])) ? $product_info['item_grade'] : 'New';
		$this->data['qualities'] = array('Used' => 'Used / Refurbished', 'New' => 'Like new condition', 'Grade A' => 'Minor cosmetic issues', 'Grade B' => 'Moderate cosmetic issues', 'Grade C' => 'Major cosmetic issues', 'Grade D' => 'Severe cosmetic issues');
		$product_info['quality'] = $this->model_catalog_product->getProductQuality($product_info['model']);
		
		/*code start*/
		if((strtotime(date('Y-m-d')) >= strtotime($product_info['promo_date_start'])) && (strtotime(date('Y-m-d')) <= strtotime($product_info['promo_date_end'])) || (($product_info['promo_date_start'] == '0000-00-00') && ($product_info['promo_date_end'] == '0000-00-00'))) {
			$promo_on = TRUE;
		} else {
			$promo_on = FALSE;
		}
		/*code end*/
		if ($product_info['is_main_sku']){
			$this->data['sub_models'] = $this->model_catalog_product->getProductGrades($product_info['model']);
		}else{	
		$this->data['sub_models'] = '';
		}
		$this->data['product_info'] = $product_info;
		
		if ($product_info) {
			$url = '';
			
			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}
			
			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}			

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . $this->request->get['filter_name'];
			}

			if (isset($this->request->get['filter_tag'])) {
				$url .= '&filter_tag=' . $this->request->get['filter_tag'];
			}
			
			if (isset($this->request->get['filter_description'])) {
				$url .= '&filter_description=' . $this->request->get['filter_description'];
			}	

			if (isset($this->request->get['filter_category_id'])) {
				$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
			}

			$this->data['breadcrumbs'][] = array(
				'text'      => $product_info['name'],
				'href'      => $this->url->link('product/product', $url . '&product_id=' . $this->request->get['product_id']),
				'separator' => $this->language->get('text_separator')
				);			
			
			$this->document->setTitle($product_info['name']);
			$this->document->setDescription($product_info['meta_description']);
			$this->document->setKeywords($product_info['meta_keyword']);
			$this->document->addLink($this->url->link('product/product', 'product_id=' . $this->request->get['product_id']), 'canonical');
			
			$this->data['heading_title'] = $product_info['name'];
			
			
			$this->data['text_select'] = $this->language->get('text_select');
			$this->data['text_manufacturer'] = $this->language->get('text_manufacturer');
			$this->data['text_model'] = $this->language->get('text_model');
			$this->data['text_reward'] = $this->language->get('text_reward');
			$this->data['text_points'] = $this->language->get('text_points');	
			$this->data['text_discount'] = $this->language->get('text_discount');
			$this->data['text_stock'] = $this->language->get('text_stock');
			$this->data['text_price'] = $this->language->get('text_price');
			$this->data['text_compare'] = $this->language->get('text_compare');
			$this->data['text_tax'] = $this->language->get('text_tax');
            // Add 
			$this->data['youtubeproduct'] = $product_info['video'];
			$this->data['youtube_extension'] = $this->language->get('youtube_extension');
            // End add
			$this->data['text_discount'] = $this->language->get('text_discount');
			$this->data['text_option'] = $this->language->get('text_option');
			$this->data['text_qty'] = $this->language->get('text_qty');
			$this->data['text_minimum'] = sprintf($this->language->get('text_minimum'), $product_info['minimum']);
			$this->data['text_or'] = $this->language->get('text_or');
			$this->data['text_write'] = $this->language->get('text_write');
			$this->data['text_note'] = $this->language->get('text_note');
			$this->data['text_share'] = $this->language->get('text_share');
			$this->data['text_wait'] = $this->language->get('text_wait');
			$this->data['text_tags'] = $this->language->get('text_tags');
			
			$this->data['entry_name'] = $this->language->get('entry_name');
			$this->data['entry_review'] = $this->language->get('entry_review');
			$this->data['entry_rating'] = $this->language->get('entry_rating');
			$this->data['entry_good'] = $this->language->get('entry_good');
			$this->data['entry_bad'] = $this->language->get('entry_bad');
			$this->data['entry_captcha'] = $this->language->get('entry_captcha');
			
			$this->data['button_cart'] = $this->language->get('button_cart');
			$this->data['button_wishlist'] = $this->language->get('button_wishlist');
			$this->data['button_compare'] = $this->language->get('button_compare');			
			$this->data['button_upload'] = $this->language->get('button_upload');
			$this->data['button_continue'] = $this->language->get('button_continue');
			
			$this->load->model('catalog/review');

			$this->data['tab_description'] = $this->language->get('tab_description');
			$this->data['tab_attribute'] = $this->language->get('tab_attribute');
			$this->data['tab_review'] = sprintf($this->language->get('tab_review'), $this->model_catalog_review->getTotalReviewsByProductId($this->request->get['product_id']));
			$this->data['tab_related'] = $this->language->get('tab_related');
			
			$this->data['product_id'] = $this->request->get['product_id'];
			$this->data['manufacturer'] = $product_info['manufacturer'];
			$this->data['manufacturers'] = $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $product_info['manufacturer_id']);
			$this->data['model'] = $product_info['model'];
			$this->data['reward'] = $product_info['reward'];
			$this->data['points'] = $product_info['points'];
			$steps = $this->model_catalog_product->getProductSteps($product_info['model']);
			if($steps->rows)
			{
				$this->data['steps'] = $steps->rows;
			}

			$question = $this->model_catalog_product->getProductQuestion($product_info['model']);
			if($question->rows)
			{
				$this->data['question'] = $question->rows;
			}
			if ($product_info['quantity'] <= 0) {
				$this->data['stock'] = $product_info['stock_status'];
			} elseif ($this->config->get('config_stock_display')) {
				$this->data['stock'] = $product_info['quantity'];
			} else {
				$this->data['stock'] = $this->language->get('text_instock');
			}
			
			$this->load->model('tool/image');

			if ($product_info['image']) {
				$this->data['popup'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'));
			} else {
				$this->data['popup'] = $this->model_tool_image->resize('data/image-coming-soon.jpg', $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'));
			}
			
			if ($product_info['image']) {
				$this->data['thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
			} else {
				$this->data['thumb'] = $this->model_tool_image->resize('data/image-coming-soon.jpg', $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
			}
			
			$this->data['images'] = array();
			
			$results = $this->model_catalog_product->getProductImages($this->request->get['product_id']);
			
			foreach ($results as $result) {
				$this->data['images'][] = array(
					'popup' => ($result['image']?$this->model_tool_image->resize($result['image'], $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height')):$this->model_tool_image->resize('data/image-coming-soon.jpg', $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'))),
					'thumb' => ($result['image']?$this->model_tool_image->resize($result['image'], $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height')):$this->model_tool_image->resize('data/image-coming-soon.jpg', $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height')))
					);
			}
			
			if(!$results)
			{
				$this->data['images'][0] = array(
					'popup' => ($this->model_tool_image->resize('data/image-coming-soon.jpg', $this->config->get('config_image_popup_width'), $this->config->get('config_image_popup_height'))),
					'thumb' => ($this->model_tool_image->resize('data/image-coming-soon.jpg', $this->config->get('config_image_additional_width'), $this->config->get('config_image_additional_height')))
					);
				
			}
			


			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$this->data['price'] = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$this->data['price'] = false;
			}


			if ((float)$product_info['sale_price']) {
				$this->data['sale_price'] = $this->currency->format($this->tax->calculate($product_info['sale_price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$this->data['sale_price'] = false;
			}			

			if ((float)$product_info['special']) {
				$this->data['special'] = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$this->data['special'] = false;
			}
			
			if ($this->config->get('config_tax')) {
				$this->data['tax'] = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price']);
			} else {
				$this->data['tax'] = false;
			}
			
			$discounts = $this->model_catalog_product->getProductDiscounts($this->request->get['product_id']);
			
			$this->data['discounts'] = array(); 
			
			foreach ($discounts as $discount) {
				$this->data['discounts'][] = array(
					'quantity' => $discount['quantity'],
					'price'    => $this->currency->format($this->tax->calculate($discount['price'], $product_info['tax_class_id'], $this->config->get('config_tax')))
					);
			}
			
			$this->data['options'] = array();
			
			foreach ($this->model_catalog_product->getProductOptions($this->request->get['product_id']) as $option) { 
				if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox' || $option['type'] == 'image') { 
					$option_value_data = array();
					
					foreach ($option['option_value'] as $option_value) {
						if (!$option_value['subtract'] || ($option_value['quantity'] > 0)) {
							if ((($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) && (float)$option_value['price']) {
								$price = $this->currency->format($this->tax->calculate($option_value['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
							} else {
								$price = false;
							}
							
							$option_value_data[] = array(
								'product_option_value_id' => $option_value['product_option_value_id'],
								'option_value_id'         => $option_value['option_value_id'],
								'name'                    => $option_value['name'],
								'image'                   => $this->model_tool_image->resize($option_value['image'], 50, 50),
								'price'                   => $price,
								'compare'				  => $compare,
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

			if ($product_info['minimum']) {
				$this->data['minimum'] = $product_info['minimum'];
			} else {
				$this->data['minimum'] = 1;
			}
			
			/*code start*/
			$promo_top_right = $this->model_catalog_product->getPromo($product_info['product_id'],$product_info['promo_top_right']);

			if (!empty($promo_top_right['promo_text']) && $promo_on) {
				if (!empty($promo_top_right['promo_link'])){
					$promo_tags = '<span>Notes: </span><span style="color:red"><a href="' . $promo_top_right['promo_link'] . '" Title="Click Me">' . $promo_top_right['promo_text'] . '</a></span>';
					if (!empty($promo_top_right['pimage'])){
						$promo_tags = $promo_tags . '<br /><tr><td colspan="2"><a href="' . $promo_top_right['promo_link'] . '" Title="Click Me"><img src="image/' . $promo_top_right['pimage'] . '" /></a></td></tr>';
					}
				} else {
					$promo_tags = '<span>Notes: </span><span style="color: red; font-weight:bold;">' . $promo_top_right['promo_text'] . '</span>';
					if (!empty($promo_top_right['pimage'])){
						$promo_tags = $promo_tags . '<br /><tr><td colspan="2"><img src="image/' . $promo_top_right['pimage'] . '" /></td></tr>';
					}
				}
			} else {
				$promo_tags = '';
			}
			
			if (!empty($promo_top_right['promo_text']) && $promo_on) {
				$promo_tag_product_top_right = '<div class="promotags" style="width:70px;height:70px;top:0px;right:0px;background: url(\'' . 'image/' . $promo_top_right['image'] . '\') no-repeat;background-position:top right"></div>';
			} else {
				$promo_tag_product_top_right = '';
			}
			
			$promo_top_left = $this->model_catalog_product->getPromo($product_info['product_id'],$product_info['promo_top_left']);
			if (!empty($promo_top_left['promo_text']) && $promo_on) {
				$promo_tag_product_top_left = '<div class="promotags" style="width:70px;height:70px;top:0px;left:0px;background: url(\'' . 'image/' . $promo_top_left['image'] . '\') no-repeat;background-position:top left"></div>';
			} else {
				$promo_tag_product_top_left = '';
			}
			
			$promo_bottom_left = $this->model_catalog_product->getPromo($product_info['product_id'],$product_info['promo_bottom_left']);
			if (!empty($promo_bottom_left['promo_text']) && $promo_on) {
				$promo_tag_product_bottom_left = '<div class="promotags" style="width:70px;height:70px;bottom:0px;left:0px;background: url(\'' . 'image/' . $promo_bottom_left['image'] . '\') no-repeat;background-position:bottom left"></div>';
			} else {
				$promo_tag_product_bottom_left = '';
			}
			
			$promo_bottom_right = $this->model_catalog_product->getPromo($product_info['product_id'],$product_info['promo_bottom_right']);
			if (!empty($promo_bottom_right['promo_text']) && $promo_on) {
				$promo_tag_product_bottom_right = '<div class="promotags" style="width:70px;height:70px;bottom:0px;right:0px;background: url(\'' . 'image/' . $promo_bottom_right['image'] . '\') no-repeat;background-position:bottom right"></div>';
			} else {
				$promo_tag_product_bottom_right = '';
			}			
			/*code end*/
			
			$this->data['review_status'] = $this->config->get('config_review_status');
			$this->data['reviews'] = sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']);
			$this->data['rating'] = (int)$product_info['rating'];
			$this->data['description'] = stripslashes(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8'));
			$this->data['attribute_groups'] = $this->model_catalog_product->getProductAttributes($this->request->get['product_id']);
			/*code start*/
			$this->data['promo_tags'] = $promo_tags;
			$this->data['promo_tag_product_top_right'] = $promo_tag_product_top_right;
			$this->data['promo_tag_product_top_left'] = $promo_tag_product_top_left;
			$this->data['promo_tag_product_bottom_left'] = $promo_tag_product_bottom_left;
			$this->data['promo_tag_product_bottom_right'] = $promo_tag_product_bottom_right;
			/*code end*/
			
				
			// print_r($this->data['products']);exit;
			$this->data['tags'] = array();

			$results = $this->model_catalog_product->getProductTags($this->request->get['product_id']);
			
			foreach ($results as $result) {
				$this->data['tags'][] = array(
					'tag'  => $result['tag'],
					'href' => $this->url->link('product/search', 'filter_tag=' . $result['tag'])
					);
			}
			
			$this->model_catalog_product->updateViewed($this->request->get['product_id']);
			if ($this->data['model'] == "SIGN") {
				$this->redirect($this->url->link('product/search', '', 'SSL'));
			}

			$this->load->model('setting/setting');
		$latest_products = $this->model_setting_setting->getSetting('home_products');
		$results = explode(",",$latest_products['home_products4']);

		foreach ($results as $resultx) {
			if($resultx=='') continue;
			$result = $this->model_catalog_product->getProduct($resultx);
		
			if ($result['image']) {
				$image = $this->model_tool_image->resize($result['image'], 199, 132);
			} else {
				$image = $this->model_tool_image->resize('data/image-coming-soon.jpg', 199, 132);
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
			
			$this->data['latest_products'][] = array(
				'product_id' => $result['product_id'],
				'thumb'   	 => $image,
				'name'    	 => $result['name'],
				'price'   	 => $price,
				'special' 	 => $special,
				'rating'     => $rating,
				'reviews'    => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
				'href'    	 => $this->url->link('product/product', 'product_id=' . $result['product_id']),
			);
		}

			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/product/product.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/product/product.tpl';
			} else {
				$this->template = 'default/template/product/product.tpl';
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
			
		} else {
			$url = '';
			
			if (isset($this->request->get['path'])) {
				$url .= '&path=' . $this->request->get['path'];
			}
			
			if (isset($this->request->get['manufacturer_id'])) {
				$url .= '&manufacturer_id=' . $this->request->get['manufacturer_id'];
			}			

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . $this->request->get['filter_name'];
			}	

			if (isset($this->request->get['filter_tag'])) {
				$url .= '&filter_tag=' . $this->request->get['filter_tag'];
			}

			if (isset($this->request->get['filter_description'])) {
				$url .= '&filter_description=' . $this->request->get['filter_description'];
			}

			if (isset($this->request->get['filter_category_id'])) {
				$url .= '&filter_category_id=' . $this->request->get['filter_category_id'];
			}

			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_error'),
				'href'      => $this->url->link('product/product', $url . '&product_id=' . $product_id),
				'separator' => $this->language->get('text_separator')
				);			

			$this->document->setTitle($this->language->get('text_error'));

			$this->data['heading_title'] = $this->language->get('text_error');

			$this->data['text_error'] = $this->language->get('text_error');

			$this->data['button_continue'] = $this->language->get('button_continue');

			$this->data['continue'] = $this->url->link('common/home');

			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/error/not_found.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/error/not_found.tpl';
			} else {
				$this->template = 'default/template/error/not_found.tpl';
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
	}
	public function getUpdatedPrice() {
		$this->load->model('catalog/product');
		$product_id = (int)$this->request->post['product_id'];
		$quantity = (int)$this->request->post['quantity'];
		$product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p.status = '1'");

		if ($product_query->num_rows) {


			// Bulk Price Module

			$product_limit_qty=0;

			$_limit_qty = false;

			foreach ($this->session->data['voucher'] as $_voucher ) {

				$coupon_query =  $this->db->query("SELECT product_limit_qty FROM ".DB_PREFIX."coupon WHERE code='".$this->db->escape($_voucher)."' AND has_product_limit=1 ");
				if($coupon_query->num_rows)
				{
					
					foreach ($this->cart->getProducts() as $_product) {
						
						
				
											
						$product_class =	$this->model_catalog_product->getProductClass($product_query->row['model']);
						$product_quality = $this->model_catalog_product->getProductQuality($product_query->row['model']);
						// echo $product_quality;
						
						if ((strtolower($product_class['name']) == 'screen-lcdtouchscreenassembly' || strtolower($product_class['name']) == 'screen-touchscreen') && strtolower($product_quality)=='economy plus') {
			
							$product_limit_qty+=(int)$_product['quantity'];

						}
					
					}
					
					if($product_limit_qty>=$coupon_query->row['product_limit_qty'])
					{
						$_limit_qty = true;
					}
				}
			}
			// echo $product_limit_qty;exit;

			// Bulk Price Module Ends


			if ($this->customer->isLogged()) {
				$customer_group_id = $this->customer->getCustomerGroupId();
			} else {
				$customer_group_id = $this->config->get('config_customer_group_id');
			}
			$is_platinum = false;
					if(substr($product_query->row['model'],0,7)=='APL-001' || substr($product_query->row['model'],0,4)=='SRN-' || substr($product_query->row['model'],0,7)=='TAB-SRN'  )
					{
						$is_platinum = true;
					}
					if($is_platinum)
					{

					$customer_group_id = '1633'; // force assigning the customer group of platinum 1633
					}
			if ((strtolower($product_class['name']) == 'screen-lcdtouchscreenassembly' || strtolower($product_class['name']) == 'screen-touchscreen') && strtolower($product_quality)=='economy plus' && $_limit_qty==true && $product_query->row['bulk_price']>0.00 ) 
				{
					$price = $product_query->row['bulk_price'];
				}
				elseif ($product_query->row['sale_price'] != '0.0000') {
				$price = $product_query->row['sale_price'];
				$old_price = 	$product_query->row['price'];

			} else {


				$price = $product_query->row['price'];
				$old_price = 0.00;


				$product_discount_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$customer_group_id . "' AND quantity <= '" . (int)$quantity . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");

				if ($product_discount_query->num_rows) {
					$price = $product_discount_query->row['price'];
				}

					// Product Specials
				$product_special_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$customer_group_id . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");

				if ($product_special_query->num_rows) {
					$price = $product_special_query->row['price'];
				}						
			}
					// Reward Points
			$product_reward_query = $this->db->query("SELECT points FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$customer_group_id . "'");

			if ($product_reward_query->num_rows) {	
				$reward = $product_reward_query->row['points'];
			} else {
				$reward = 0;
			}



			$json = array();
			$json['success']=$this->currency->format($price*$quantity);
			$json['unit_price'] = $this->currency->format($price);
			$json['old_price'] = $this->currency->format($old_price);
			echo json_encode($json);exit;
		}


	}
	public function review() {
		$this->language->load('product/product');
		
		$this->load->model('catalog/review');

		$this->data['text_on'] = $this->language->get('text_on');
		$this->data['text_no_reviews'] = $this->language->get('text_no_reviews');

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}  
		
		$this->data['reviews'] = array();
		
		$review_total = $this->model_catalog_review->getTotalReviewsByProductId($this->request->get['product_id']);
		$results = $this->model_catalog_review->getReviewsByProductId($this->request->get['product_id'], ($page - 1) * 5, 5);

		foreach ($results as $result) {
			$this->data['reviews'][] = array(
				'author'     => $result['author'],
				'text'       => $result['text'],
				'rating'     => (int)$result['rating'],
				'reviews'    => sprintf($this->language->get('text_reviews'), (int)$review_total),
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
				);
		}			

		$pagination = new Pagination();
		$pagination->total = $review_total;
		$pagination->page = $page;
		$pagination->limit = 5; 
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('product/product/review', 'product_id=' . $this->request->get['product_id'] . '&page={page}');

		$this->data['pagination'] = $pagination->render();
		
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/product/review.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/product/review.tpl';
		} else {
			$this->template = 'default/template/product/review.tpl';
		}
		
		
		$this->response->setOutput($this->render());
	}
	
	public function write() {
		$this->language->load('product/product');
		
		$this->load->model('catalog/review');
		
		$json = array();
		
		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 25)) {
				$json['error'] = $this->language->get('error_name');
			}
			
			if ((utf8_strlen($this->request->post['text']) < 25) || (utf8_strlen($this->request->post['text']) > 1000)) {
				$json['error'] = $this->language->get('error_text');
			}

			if (empty($this->request->post['rating'])) {
				$json['error'] = $this->language->get('error_rating');
			}

			if (empty($this->session->data['captcha']) || ($this->session->data['captcha'] != $this->request->post['captcha'])) {
				$json['error'] = $this->language->get('error_captcha');
			}

			if (!isset($json['error'])) {
				$this->model_catalog_review->addReview($this->request->get['product_id'], $this->request->post);
				
				$json['success'] = $this->language->get('text_success');
			}
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function captcha() {
		$this->load->library('captcha');
		
		$captcha = new Captcha();
		
		$this->session->data['captcha'] = $captcha->getCode();
		
		$captcha->showImage();
	}
	
	public function upload() {
		$this->language->load('product/product');
		
		$json = array();
		
		if (!empty($this->request->files['file']['name'])) {
			$filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8')));
			
			if ((strlen($filename) < 3) || (strlen($filename) > 64)) {
				$json['error'] = $this->language->get('error_filename');
			}	  	
			
			$allowed = array();
			
			$filetypes = explode(',', $this->config->get('config_upload_allowed'));
			
			foreach ($filetypes as $filetype) {
				$allowed[] = trim($filetype);
			}
			
			if (!in_array(substr(strrchr($filename, '.'), 1), $allowed)) {
				$json['error'] = $this->language->get('error_filetype');
			}	

			if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
				$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
			}
		} else {
			$json['error'] = $this->language->get('error_upload');
		}
		
		if (!$json) {
			if (is_uploaded_file($this->request->files['file']['tmp_name']) && file_exists($this->request->files['file']['tmp_name'])) {
				$file = basename($filename) . '.' . md5(mt_rand());
				
				// Hide the uploaded file name so people can not link to it directly.
				$json['file'] = $this->encryption->encrypt($file);
				
				move_uploaded_file($this->request->files['file']['tmp_name'], DIR_DOWNLOAD . $file);
			}

			$json['success'] = $this->language->get('text_upload');
		}	
		
		$this->response->setOutput(json_encode($json));		
	}

	public function saveQuestion()
	{
		// $this->db->query("INSERT INTO oc_product_question SET product_id='".$_POST['product_id']."',product_sku='".$_POST['product_sku']."',question='".$_POST['question']."',product_title='".$_POST['product_title']."',question_date=NOW()");

		// $html = '<div class="query-box"><h5>Question:</h5><p>';
		// $html .= $_POST['question'];
		// $html .= '</p></div>';

		// echo json_encode($html);

		// <h5>Answer</h5>
		// <p>It is a full color display, but there are iPhone settings that allow for black and white only operation</p>
		// <a href="#ans-pop" class="btn btn-primary fancybox">answer this question</a>
	}
	public function getRelatedProducts()
	{
		$this->load->model('catalog/product');
		$this->load->model('catalog/catalog');
		$this->load->model('tool/image');
		// echo 
		$product_info = $this->model_catalog_product->getProduct($this->request->post['product_id']);
			
			
		

			$data = array();
			$_manufacturer_id = $this->model_catalog_catalog->getManufacturer($product_info['model']);
			$manufacturer_name = $this->model_catalog_catalog->getManufacturerName($_manufacturer_id);
			// $results = $this->model_catalog_product->getProductRelated($this->request->get['product_id']);
			$model_ids = ($this->model_catalog_product->getDeviceModels($product_info['product_id']));
			//New code injection to load parents related products Gohar
			if (!$model_ids) {
			 	$product_id = $this->model_catalog_product->getProductIDbySku($this->request->post['main_sku']);
			 	$product_info = $this->model_catalog_product->getProduct($product_id);
			 	$_manufacturer_id = $this->model_catalog_catalog->getManufacturer($product_info['model']);
				$manufacturer_name = $this->model_catalog_catalog->getManufacturerName($_manufacturer_id);
				$model_ids = ($this->model_catalog_product->getDeviceModels($product_info['product_id']));			 	
			 }
			// print_r($model_ids);exit;
			$model_id = $model_ids[0]['model_id'];
			$href_name = $manufacturer_name.' '.$model_ids[0]['device'];
			$href = $this->url->link('catalog/repair_parts','path='.$_manufacturer_id.'_'.$model_id);
			$results = $this->cache->get('related_products.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $href_name);
			if(!$results)
		{
			$_test =  $this->model_catalog_catalog->loadModelClasses(array('device_id' => $model_id, 'class_name' => 'Replacement Parts'));
			// $device_id = $this->model_catalog_catalog->getDeviceID($product_info['model']);
			$mc_ids = array();
			// print_r($_test);exit;
			foreach ($_test as $key => $class) {
				if($class['main_name']!='Replacement Parts' ) continue;
				$mc_ids[]= $class['main_class_id'];
			}	
			$unique_mc_ids = array_unique($mc_ids);
			// print_r($unique_mc_ids);exit;
			foreach ($unique_mc_ids as $key => $value) {
			$main_class_id = $value;	
			$productFilter = array(
				'manufacturer_id' => $manufacturer_id,
				'device_id' => $model_id,
				'main_class_id' => $main_class_id,
				);
			// print_r($this->model_catalog_catalog->filterProducts($productFilter));exit;
			$productsIds[] = $this->model_catalog_catalog->getOCProductsIds($this->model_catalog_catalog->filterProducts($productFilter));

			}
			// print_r($productsIds);exit;
			$_results = array();
			foreach($productsIds as $_product_ids)
			{
				// print_r($_product_ids);exit;
				foreach($_product_ids as $_product_id)
				{
					if($_product_id==$product_info['product_id']) continue;

					$_results[] = $_product_id;
				}
			}
			
			$_results = implode(",", $_results);
			// print_r($_results);exit;
			$results = $this->model_catalog_product->getTopSellingProductsByProductIds($_results,0,20);
			// print_r($results);exit;
			$results2=array();
			if(count($results)<20)
			{

				foreach ($_test as $key => $class) {
				if($class['main_name']!='Repair Tools' ) continue;
				$mc_ids[]= $class['main_class_id'];
			}	
			$unique_mc_ids = array_unique($mc_ids);
			// print_r($unique_mc_ids);exit;
			foreach ($unique_mc_ids as $key => $value) {
			$main_class_id = $value;	
			$productFilter = array(
				'manufacturer_id' => $manufacturer_id,
				'device_id' => $model_id,
				'main_class_id' => $main_class_id,
				);
			// print_r($this->model_catalog_catalog->filterProducts($productFilter));exit;
			$productsIds[] = $this->model_catalog_catalog->getOCProductsIds($this->model_catalog_catalog->filterProducts($productFilter));

			}
			$_results = array();
			foreach($productsIds as $_product_ids)
			{
				// print_r($_product_ids);exit;
				foreach($_product_ids as $_product_id)
				{
					if($_product_id==$product_info['product_id']) continue;

					$_results[] = $_product_id;
				}
			}
			
			$_results = implode(",", $_results);
			// print_r($_results);
			$results2 = $this->model_catalog_product->getTopSellingProductsByProductIds($_results,0,20,false);
			// echo $_results;exit;

			}
			$results = array_merge($results,$results2);
			$this->cache->set('related_products.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $href_name,$results);
		}

			$k=1;
			// print_r($results);exit;
	

			foreach ($results as $_result) {
				// if($k<$start) continue;
				if($k==21) break;
				$result = $this->model_catalog_product->getProduct($_result['product_id']);
				if(!$result) continue;
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
				$k++;
			}
			// print_r($this->data['products']);exit;

			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/catalog/products_ajax.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/catalog/products_ajax.tpl';
		} else {
			$this->template = 'default/template/catalog/products_ajax.tpl';
		}

		$json['products'] = $this->render();
		$json['href'] = array('name'=>$href_name,'href'=>$href);
		// $json['products'] .='<div class="col-sm-1">
		// 	<img src="image/slider-arrow.png" onclick=""  style="transform: rotate(180deg);position: absolute;top: 85px;width: 45px;cursor: pointer;">
		// </div>';

		echo json_encode($json);
		exit;
	}
}
?>