<?php
class ControllerModuleBossHomefilter extends Controller {
	protected function index($setting) {
		static $module = 0;

		$this->document->addScript('catalog/view/javascript/jquery/tabs.js');
		$this->document->addScript('catalog/view/javascript/bossthemes/jquery.easing.js');
		$this->document->addScript('catalog/view/javascript/bossthemes/jquery.elastislide.js');
		if (file_exists('catalog/view/theme/' . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/stylesheet/boss_homefilter.css')){
			$this->document->addStyle('catalog/view/theme/' . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/stylesheet/boss_homefilter.css');
		} else {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/boss_homefilter.css');
		}
		$this->language->load('module/boss_homefilter');
		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['use_scrolling_panel'] = $setting['use_scrolling_panel'];
		$this->data['button_cart'] = $this->language->get('button_cart');
		$this->data['template'] = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template'));
		$this->data['product_id'] = $this->config->get('product_id');
		
		// tab
		$this->load->model('catalog/product');
		$this->load->model('tool/image');
		
		$this->data['tabs'] = array();
		
		$tabs = array();
		$tabs = $this->config->get('boss_homefilter_tab');
		if (isset($tabs)) {
			foreach ($tabs as $tab) {
				$results = array();
				if ($tab['filter_type'] == "popular") {
					$results = $this->model_catalog_product->getPopularProducts($setting['limit']);
				}
				if ($tab['filter_type'] == "special") {
					$data = array(
						'sort'  => 'pd.name',
						'order' => 'ASC',
						'start' => 0,
						'limit' => $setting['limit']
					);
					$results = $this->model_catalog_product->getProductSpecials($data);
				}
				if ($tab['filter_type'] == "bestseller") {
					$results = $this->model_catalog_product->getBestSellerProducts($setting['limit']);
				}
				if ($tab['filter_type'] == "lastest") {
					$results = $this->model_catalog_product->getLatestProducts($setting['limit']);
				}
				if ($tab['filter_type'] == "category") {
					$data = array(
						'filter_category_id' => $tab['filter_type_category'],
						'sort'  => 'pd.name',
						'order' => 'ASC',
						'start' => 0,
						'limit' => $setting['limit']
					);
					$results = $this->model_catalog_product->getProducts($data);
				}
				
				$products = array();
				
				foreach ($results as $result) {
					if ($result['image']) {
						$image = $this->model_tool_image->resize($result['image'], $setting['image_width'], $setting['image_height']);
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
					
					if ($this->config->get('config_review_status')) {
						$rating = $result['rating'];
					} else {
						$rating = false;
					}
					
					$products[] = array(
						'product_id' => $result['product_id'],
						'thumb'   	 => $image,
						'name'    	 => $result['name'],
						'description'=> utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, 100) . '..',
						'price'   	 => $price,
						'special' 	 => $special,
						'rating'     => $rating,
						'reviews'    => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
						'href'    	 => $this->url->link('product/product', 'product_id=' . $result['product_id']),
					);
				}
				if ($tab['image']) {
					$image = $this->model_tool_image->resize($tab['image'], 20, 20);
				} else {
					$image = false;
				}
				$this->data['tabs'][] = array(
					'title'	 		 =>	$tab['title'][$this->config->get('config_language_id')],
					'image'			 =>	$image,
					'products'       => $products
				);
			}
		}
		// end tab
		
		$this->data['module'] = $module++;
				
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/boss_homefilter.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/boss_homefilter.tpl';
		} else {
			$this->template = 'default/template/module/boss_homefilter.tpl';
		}

		$this->render();
	}
}
?>