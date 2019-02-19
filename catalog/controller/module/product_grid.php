<?php
class ControllerModuleProductGrid extends Controller {
	protected function index($setting) {
		$this->language->load('module/product_grid'); 

		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['button_cart'] = $this->language->get('button_cart');
		$this->data['button_compare'] = $this->language->get('button_compare');
		$this->data['button_wishlist'] = $this->language->get('button_wishlist');
		$this->data['product_id'] = $this->config->get('product_id');
		
		$this->load->model('catalog/product'); 
		
		$this->load->model('tool/image');

		$this->data['products'] = array();

		$products = explode(',', $this->config->get('product_grid')['products']);		

		$cats = explode(',', $this->config->get('product_grid')['cat']);		

		if (empty($setting['limit'])) {
			$setting['limit'] = 2;
		}
		
		$products = array_slice($products, 0, ((int)$setting['limit'] * 2));
		$cats = array_slice($cats, 0, (int)$setting['limit']);
		$i = 0;
		foreach ($products as $key => $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);

			if ($product_info) {
				if ($product_info['image']) {
					$image = $this->model_tool_image->resize($product_info['image'], 63, 123);
				} else {
					$image = $this->model_tool_image->resize('data/image-coming-soon.jpg', 63, 123);
				}

				if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$price = false;
				}

				if ((float)$product_info['special']) {
					$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')));
				} else {
					$special = false;
				}
				
				if ($this->config->get('config_review_status')) {
					$rating = $product_info['rating'];
				} else {
					$rating = false;
				}

				$this->data['products'][$i][($key % 2)] = array(
					'product_id' => $product_info['product_id'],
					'thumb'   	 => $image,
					'name'    	 => $product_info['name'],
					'short_description'    	 => $product_info['meta_description'],
					'price'   	 => $price,
					'special' 	 => $special,
					'rating'     => $rating,
					'reviews'    => sprintf($this->language->get('text_reviews'), (int)$product_info['reviews']),
					'href'    	 => $this->url->link('product/product', 'product_id=' . $product_info['product_id']),
					);
				if ($key % 2) {
					$i++;
				}
			}
		}

		$this->data['categories'] = array();
		foreach ($cats as $key => $category) {
			$result = $this->model_catalog_category->getCategory($category);

			$result['image'] = ($result['image'])? $result['image']: 'data/image-coming-soon.jpg';
			
				$this->data['categories'][] = array(
					'name'     		=> $result['name'],
					'description'	=> $result['description'],
					'image'			=> $this->model_tool_image->resize($result['image'], $setting['image_width'], $setting['image_height']),
					'href'     		=> $this->url->link('product/category', 'path=' . $category),
					'products'		=> $this->data['products'][$key]
					);
		}


		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/product_grid.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/product_grid.tpl';
		} else {
			$this->template = 'default/template/module/product_grid.tpl';
		}

		$this->render();
	}
}
?>
