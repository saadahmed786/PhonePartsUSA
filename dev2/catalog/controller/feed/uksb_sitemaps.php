<?php
class ControllerFeedUksbSitemaps extends Controller {
	public function all() {
		
		if ($this->config->get('uksb_sitemaps_status')) {
			$output  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
			$output .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
			
			if($this->config->get('uksb_sitemap_products_on')){
				$output .= $this->product_sitemaps($this->request->get['store']);
			}
			
			if($this->config->get('uksb_sitemap_categories_on')){
				$output .= $this->category_sitemaps($this->request->get['store']);
			}
			
			if($this->config->get('uksb_sitemap_manufacturers_on')){
				$output .= $this->manufacturer_sitemaps($this->request->get['store']);
			}
			
			if($this->config->get('uksb_sitemap_pages_on')){
				$output .= $this->page_sitemaps($this->request->get['store']);
			}
			
			$output .= '</sitemapindex>';
			
			$this->response->addHeader('Content-Type: text/xml; charset=utf-8');
			$this->response->setCompression(0);
			$this->response->setOutput($output);
		}
	}
	public function google() {
		
		if ($this->config->get('uksb_sitemaps_status')) {
			$output  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
			$output .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
			
			if($this->config->get('uksb_sitemap_products_on')){
				$output .= $this->product_sitemaps($this->request->get['store']);
			}
			
			if($this->config->get('uksb_sitemap_categories_on')){
				$output .= $this->category_sitemaps($this->request->get['store']);
			}
			
			if($this->config->get('uksb_sitemap_manufacturers_on')){
				$output .= $this->manufacturer_sitemaps($this->request->get['store']);
			}
			
			if($this->config->get('uksb_sitemap_pages_on')){
				$output .= $this->page_sitemaps($this->request->get['store']);
			}
			
			if($this->config->get('uksb_image_sitemap')){
				$output .= $this->image_sitemap($this->request->get['store']);
			}
			
			$output .= '</sitemapindex>';
			
			$this->response->addHeader('Content-Type: text/xml; charset=utf-8');
			$this->response->setCompression(0);
			$this->response->setOutput($output);
		}
	}
	
	protected function product_sitemaps($store_id){
		if($this->config->get('uksb_sitemaps_status')&&$this->config->get('uksb_sitemap_products_on')){

			$this->load->model('feed/uksb_sitemaps');
	
			$output = '';
			
			if($this->config->get('uksb_sitemaps_split')>0){
				$split = $this->config->get('uksb_sitemaps_split');
				$totalproducts = $this->model_feed_uksb_sitemaps->getTotalProductsByStore($store_id);
				if($totalproducts>$split){
					$j = floor($totalproducts/$split);
					$rem = $totalproducts-($j*$split);
					for($i=1; $i<=$j; $i++){
						$from = (($i-1)*$split)+1;
						$to = $i*$split;
						$output .= '<sitemap>' . "\n";
						$output .= '<loc><![CDATA[' . $this->url->link('feed/uksb_sitemaps/products&send=' . $from . '-' . $to . '&store=' . $store_id) . ']]></loc>' . "\n";
						$output .= '</sitemap>' . "\n";
					}
					if($rem>0){
						$output .= '<sitemap>' . "\n";
						$output .= '<loc><![CDATA[' . $this->url->link('feed/uksb_sitemaps/products&send=' . ($to+1) . '-' . ($to+$rem) . '&store=' . $store_id) . ']]></loc>' . "\n";
						$output .= '</sitemap>' . "\n";
					}
				}else{
					$output .= '<sitemap>' . "\n";
					$output .= '<loc><![CDATA[' . $this->url->link('feed/uksb_sitemaps/products&store=' . $store_id) . ']]></loc>' . "\n";
					$output .= '</sitemap>' . "\n";
				}
			}else{
				$output .= '<sitemap>' . "\n";
				$output .= '<loc><![CDATA[' . $this->url->link('feed/uksb_sitemaps/products&store=' . $store_id) . ']]></loc>' . "\n";
				$output .= '</sitemap>' . "\n";
			}
			
			return $output;
		}else{
			return '';
		}
	}
   
	protected function category_sitemaps($store_id){
		if($this->config->get('uksb_sitemaps_status')&&$this->config->get('uksb_sitemap_categories_on')){
			$output = '';
			
			$output .= '<sitemap>' . "\n";
			$output .= '<loc><![CDATA[' . $this->url->link('feed/uksb_sitemaps/categories&store=' . $store_id) . ']]></loc>' . "\n";
			$output .= '</sitemap>' . "\n";
			
			return $output;
		}else{
			return '';
		}
	}
   
	protected function manufacturer_sitemaps($store_id){
		if($this->config->get('uksb_sitemaps_status')&&$this->config->get('uksb_sitemap_manufacturers_on')){
			$output = '';
			
			$output .= '<sitemap>' . "\n";
			$output .= '<loc><![CDATA[' . $this->url->link('feed/uksb_sitemaps/manufacturers&store=' . $store_id) . ']]></loc>' . "\n";
			$output .= '</sitemap>' . "\n";
			
			return $output;
		}else{
			return '';
		}
	}
   
	protected function page_sitemaps($store_id){
		if($this->config->get('uksb_sitemaps_status')&&$this->config->get('uksb_sitemap_pages_on')){
			$output = '';
			
			$output .= '<sitemap>' . "\n";
			$output .= '<loc><![CDATA[' . $this->url->link('feed/uksb_sitemaps/pages&store=' . $store_id) . ']]></loc>' . "\n";
			$output .= '</sitemap>' . "\n";
			
			return $output;
		}else{
			return '';
		}
	}
   
	protected function image_sitemap($store_id){
		if($this->config->get('uksb_sitemaps_status')&&$this->config->get('uksb_image_sitemap')){
			$this->load->model('feed/uksb_sitemaps');

			$output = '';
			
			if($this->config->get('uksb_sitemaps_split')>0){
				$split = $this->config->get('uksb_sitemaps_split');
				$totalproducts = $this->model_feed_uksb_sitemaps->getTotalProductsByStore($store_id);
				if($totalproducts>$split){
					$j = floor($totalproducts/$split);
					$rem = $totalproducts-($j*$split);
					for($i=1; $i<=$j; $i++){
						$from = (($i-1)*$split)+1;
						$to = $i*$split;
						$output .= '<sitemap>' . "\n";
						$output .= '<loc><![CDATA[' . $this->url->link('feed/uksb_sitemaps/images&send=' . $from . '-' . $to . '&store=' . $store_id) . ']]></loc>' . "\n";
						$output .= '</sitemap>' . "\n";
					}
					if($rem>0){
						$output .= '<sitemap>' . "\n";
						$output .= '<loc><![CDATA[' . $this->url->link('feed/uksb_sitemaps/images&send=' . ($to+1) . '-' . ($to+$rem) . '&store=' . $store_id) . ']]></loc>' . "\n";
						$output .= '</sitemap>' . "\n";
					}
				}else{
					$output .= '<sitemap>' . "\n";
					$output .= '<loc><![CDATA[' . $this->url->link('feed/uksb_sitemaps/images' . '&store=' . $store_id) . ']]></loc>' . "\n";
					$output .= '</sitemap>' . "\n";
				}
			}else{
				$output .= '<sitemap>' . "\n";
				$output .= '<loc><![CDATA[' . $this->url->link('feed/uksb_sitemaps/images' . '&store=' . $store_id) . ']]></loc>' . "\n";
				$output .= '</sitemap>' . "\n";
			}
			
			return $output;
		}else{
			return '';
		}
	}
   
	public function products() {
		if($this->config->get('uksb_sitemaps_status')&&$this->config->get('uksb_sitemap_products_on')){
			$this->load->model('catalog/product');

			$output  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
			$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
			
			if(isset($this->request->get['send'])){
				$split = explode("-", $this->request->get['send']);
				$data = array('start' => ($split[0]-1), 'limit' => ($split[1]-$split[0]+1));
				$products = $this->model_catalog_product->getProducts($data);
			}else{
				$products = $this->model_catalog_product->getProducts();
			}
			
			foreach ($products as $product) {
				$output .= '<url>' . "\n";
				$output .= '<loc><![CDATA[' . str_replace("&amp;", "&", $this->url->link('product/product', 'product_id=' . $product['product_id'])) . ']]></loc>' . "\n";
				$output .= '<changefreq>' . $this->config->get('uksb_sitemap_products_fr') . '</changefreq>' . "\n";
				$output .= '<priority>' . $this->config->get('uksb_sitemap_products_pr') . '</priority>' . "\n";
				$output .= '</url>' . "\n";  
			}
			
			$output .= '</urlset>' . "\n";
			
			$this->response->addHeader('Content-Type: text/xml; charset=utf-8');
			$this->response->setCompression(0);
			$this->response->setOutput($output);
		}
	}
	
	public function categories() {
		if($this->config->get('uksb_sitemaps_status')&&$this->config->get('uksb_sitemap_categories_on')){
			$this->load->model('catalog/category');

			$output  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
			$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
			
			$categories = $this->model_catalog_category->getCategories();
			
			$output .= $this->getCategories(0);
			
			$output .= '</urlset>' . "\n";
			
			$this->response->addHeader('Content-Type: text/xml; charset=utf-8');
			$this->response->setCompression(0);
			$this->response->setOutput($output);
		}
	}
   
	protected function getCategories($parent_id, $current_path = '') {
		$output = '';
		
		$results = $this->model_catalog_category->getCategories($parent_id);
		
		foreach ($results as $result) {
			if (!$current_path) {
				$new_path = $result['category_id'];
			} else {
				$new_path = $current_path . '_' . $result['category_id'];
			}
			
			$output .= '<url>';
			$output .= '<loc><![CDATA[' . str_replace("&amp;", "&", $this->url->link('product/category', 'path=' . $new_path)) . ']]></loc>';
			$output .= '<changefreq>' . $this->config->get('uksb_sitemap_categories_fr') . '</changefreq>';
			$output .= '<priority>' . $this->config->get('uksb_sitemap_categories_pr') . '</priority>';
			$output .= '</url>';         
			
			$output .= $this->getCategories($result['category_id'], $new_path);
		}
		
		return $output;
	}      

	public function manufacturers() {
		if($this->config->get('uksb_sitemaps_status')&&$this->config->get('uksb_sitemap_manufacturers_on')){
			$this->load->model('catalog/manufacturer');
			
			$output  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
			$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
			
			$manufacturers = $this->model_catalog_manufacturer->getManufacturers();
			
			foreach ($manufacturers as $manufacturer) {
				$output .= '<url>' . "\n";
				$output .= '<loc><![CDATA[' . str_replace("&amp;", "&", $this->url->link('product/manufacturer/product', 'manufacturer_id=' . $manufacturer['manufacturer_id'])) . ']]></loc>' . "\n";
				$output .= '<changefreq>' . $this->config->get('uksb_sitemap_manufacturers_fr') . '</changefreq>' . "\n";
				$output .= '<priority>' . $this->config->get('uksb_sitemap_manufacturers_pr') . '</priority>' . "\n";
				$output .= '</url>' . "\n";   
			}
			
			$output .= '</urlset>' . "\n";
			
			$this->response->addHeader('Content-Type: text/xml; charset=utf-8');
			$this->response->setCompression(0);
			$this->response->setOutput($output);
		}
	}

	public function pages() {
		if($this->config->get('uksb_sitemaps_status')&&$this->config->get('uksb_sitemap_pages_on')){
			$this->load->model('catalog/information');
			
			$output  = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
			$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
			
			$informations = $this->model_catalog_information->getInformations();
			
			if(!$this->config->get('uksb_pages_omit_a')){
				$output .= '<url>' . "\n";
				$output .= '<loc><![CDATA[' . str_replace("&amp;", "&", $this->url->link('common/home')) . ']]></loc>' . "\n";
				$output .= '<changefreq>' . $this->config->get('uksb_sitemap_pages_fr') . '</changefreq>' . "\n";
				$output .= '<priority>' . $this->config->get('uksb_sitemap_pages_pr') . '</priority>' . "\n";
				$output .= '</url>' . "\n";
			}

			if(!$this->config->get('uksb_pages_omit_b')){
				$output .= '<url>' . "\n";
				$output .= '<loc><![CDATA[' . str_replace("&amp;", "&", $this->url->link('product/special')) . ']]></loc>' . "\n";
				$output .= '<changefreq>' . $this->config->get('uksb_sitemap_pages_fr') . '</changefreq>' . "\n";
				$output .= '<priority>' . $this->config->get('uksb_sitemap_pages_pr') . '</priority>' . "\n";
				$output .= '</url>' . "\n";   
			}

			foreach ($informations as $information) {
				if(!$this->config->get('uksb_pages_omit_'.$information['information_id'])){
					$output .= '<url>' . "\n";
					$output .= '<loc><![CDATA[' . str_replace("&amp;", "&", $this->url->link('information/information', 'information_id=' . $information['information_id'])) . ']]></loc>' . "\n";
					$output .= '<changefreq>' . $this->config->get('uksb_sitemap_pages_fr') . '</changefreq>' . "\n";
					$output .= '<priority>' . $this->config->get('uksb_sitemap_pages_pr') . '</priority>' . "\n";
					$output .= '</url>' . "\n";
				}
			}
			
			$output .= '</urlset>' . "\n";
			
			$this->response->addHeader('Content-Type: text/xml; charset=utf-8');
			$this->response->setCompression(0);
			$this->response->setOutput($output);
		}
	}
   
	public function images() {
		if($this->config->get('uksb_sitemaps_status')&&$this->config->get('uksb_image_sitemap')){
			if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
				$server = $this->config->get('config_ssl');
			} else {
				$server = $this->config->get('config_url');
			}


			$this->load->model('catalog/category');
			$this->load->model('catalog/product');
			$this->load->model('tool/image');

   			$output  = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
			$output .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'."\n".'   xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">'."\n";
			
			if(isset($this->request->get['send'])){
				$split = explode("-", $this->request->get['send']);
				$data = array('start' => ($split[0]-1), 'limit' => ($split[1]-$split[0]+1));
				$products = $this->model_catalog_product->getProducts($data);
			}else{
				$products = $this->model_catalog_product->getProducts();
			}
			
			foreach ($products as $product) {
				
				if ($product['image']) {
					$output .= '<url>'."\n";
					$output .= '<loc><![CDATA[' . str_replace("&amp;", "&", $this->url->link('product/product', 'product_id=' . $product['product_id'])) . ']]></loc>' . "\n";
					$output .= '<image:image>'."\n";

					$output .= '<image:loc><![CDATA[' . $server . 'image/cache/' . $this->imagepop($product['image']) . ']]></image:loc>'."\n";
					$output .= '<image:title><![CDATA[' . $product['name'] . ']]></image:title>'."\n";
					$output .= '<image:caption><![CDATA[' . $this->plainText($product['description']) . ']]></image:caption>'."\n";

					$addimages = $this->model_catalog_product->getProductImages($product['product_id']);
					
					$addimnum = 0;
					foreach($addimages as $addimage){
						$output .= '</image:image>'."\n";
						$output .= '<image:image>'."\n";
						$output .= '<image:loc><![CDATA[' . $server . 'image/cache/' . $this->imagepop($addimage['image']) . ']]></image:loc>'."\n";
						$output .= '<image:title><![CDATA[' . $product['name'] . ']]></image:title>'."\n";
						$output .= '<image:caption><![CDATA[' . $this->plainText($product['description']) . ']]></image:caption>'."\n";
					}
					$output .= '</image:image>'."\n";
					$output .= '</url>'."\n";
				}
			}

			$output .= '</urlset>' . "\n";
			
			$this->response->addHeader('Content-Type: text/xml; charset=utf-8');
			$this->response->setCompression(0);
			$this->response->setOutput($output);
		}
	}

	protected function imagepop($image){
		return substr_replace($image,'-'.$this->config->get('config_image_popup_width').'x'.$this->config->get('config_image_popup_height'),-4,0);
	}

	protected function plainText($string) {
	    $table = array(
		'“'=>'&#39;', '”'=>'&#39;', '‘'=>"&#34;", '’'=>"&#34;", '•'=>'*', '—'=>'-', '–'=>'-', '¿'=>'?', '¡'=>'!', '°'=>' deg. ',
		'÷'=>' / ', '×'=>'X', '±'=>'+/-',
		'&nbsp;'=> ' ', '"'=> '&#34;', "'"=> '&#39;', '<'=> '&lt;', '>'=> '&gt;', "\n"=> ' ', "\r"=> ' '
	    );

	    $string = strip_tags(html_entity_decode($string));
	    $string = strtr($string, $table);
	    $string = preg_replace('/&#?[a-z0-9]+;/i',' ',$string);	
	    $string = preg_replace('/\s{2,}/i', ' ', $string );	

		$table2 = array(
				'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 
				'Þ'=>'B', 'þ'=>'b', 'ß'=>'Ss',
				'ç'=>'c',
				'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 
				'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i',
				'ñ'=>'n',
				'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'œ'=>'o', 'ð'=>'o',
				'š'=>'s',
				'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u',
				'ý'=>'y', 'ÿ'=>'y', 
				'ž'=>'z', 'ž'=>'z',
				'©'=>'(c)', '®'=>'(R)'
		);

		$string = strtr($string, $table2);
		$string = preg_replace('/[^(\x20-\x7F)]*/','', $string ); 

	    return substr($string, 0, 5000 );	
	}
}