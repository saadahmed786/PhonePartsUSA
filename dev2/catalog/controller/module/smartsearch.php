<?php
//==============================================================================
// Smart Search v154.2
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
//==============================================================================

class ControllerModuleSmartsearch extends Controller {
	private $type = 'module';
	private $name = 'smartsearch';
	
	public function smartsearch() {
		$v14x = (!defined('VERSION') || VERSION < 1.5);
		$v150 = (defined('VERSION') && strpos(VERSION, '1.5.0') === 0);
		
		$settings = ($v14x || $v150) ? unserialize($this->config->get($this->name . '_data')) : $this->config->get($this->name . '_data');
		$data = array(
			'filter_name'	=> $this->request->post['filter_name'],
			'sort'			=> 'pd.name',
			'order'			=> 'ASC',
			'start'			=> 0,
			'limit'			=> $settings['ajax_limit'],
			'return_total'	=> false
		);
		
		$this->load->model('catalog/smartsearch');
		$product_ids = $this->model_catalog_smartsearch->smartsearch($data);
		
		$this->load->model('catalog/product');
		$this->load->model('catalog/review');
		$this->load->model('tool/image');
		
		$products = array();
		
		foreach ($product_ids as $result) {
			if (!$result) continue;
			
			if ($v14x) {
				$result['special'] = $this->model_catalog_product->getProductSpecial($result['product_id']);
				$result['rating'] = $this->model_catalog_review->getAverageRating($result['product_id']);
				$result['reviews'] = $this->model_catalog_review->getTotalReviewsByProductId($result['product_id']);
			}
			
			$image = $this->model_tool_image->resize(($result['image']) ? $result['image'] : 'no_image.jpg', $settings['ajax_image_width'], $settings['ajax_image_height']);
			$options = $this->model_catalog_product->getProductOptions($result['product_id']);
			$rating = ($this->config->get('config_review' . ($v14x ? '' : '_status'))) ? (int)$result['rating'] : false;
			
			$products[] = array(
				'add'			=> $this->makeURL(($options ? 'product/product' : 'checkout/cart'), 'product_id=' . $result['product_id']),
				'description'	=> join('', array_slice(preg_split('//u', strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), -1, PREG_SPLIT_NO_EMPTY), 0, $settings['ajax_description'])) . '...',				'href'			=> $this->makeURL('product/product', 'product_id=' . $result['product_id']),
				'image'			=> $image,
				'model'			=> $result['model'],
				'name'			=> $result['name'],
				'options'		=> $options,
				'price'			=> (!$this->config->get('config_customer_price') || $this->customer->isLogged()) ? $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax'))) : false,
				'product_id'	=> $result['product_id'],
				'rating'		=> $rating,
				'reviews'		=> sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
				'special'		=> ((float)$result['special']) ? $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax'))) : false,
				'stars'			=> sprintf($this->language->get('text_stars'), $rating),
				'tax'			=> ($this->config->get('config_tax')) ? $this->currency->format((float)$result['special'] ? $result['special'] : $result['price']) : false,
				'thumb'			=> $image
			);
		}
		
		$this->response->setOutput(json_encode($products));
	}
	
	private function makeURL($route, $args = '', $connection = 'NONSSL') {
		if (!defined('VERSION') || VERSION < 1.5) {
			$this->load->model('tool/seo_url');
			$url = ($connection == 'NONSSL') ? HTTP_SERVER : HTTPS_SERVER;
			$url .= 'index.php?route=' . $route;
			$url .= ($args) ? '&' . ltrim($args, '&') : '';
			return $this->model_tool_seo_url->rewrite($url);
		} else {
			return $this->url->link($route, $args, $connection);
		}
	}
}
?>