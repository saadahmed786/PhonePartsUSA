<?php   
class ControllerCommonHeader extends Controller {
	protected function index() {

		if (preg_match('/MSIE\s(?P<v>\d+)/i', @$_SERVER['HTTP_USER_AGENT'], $B) && $B['v'] <= 8) {
			$this->redirect('https://phonepartsusa.com/old');
   
}
// if($this->request->get['route']!='common/maintenance/info')
// {
// 	// $this->redirect('https://phonepartsusa.com/index.php?route=common/maintenance/info');
// }
		


		
		if (isset($this->session->data['order_id'])) {

			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$this->session->data['order_id'] . "' AND order_status_id > 0");

			if ($query->num_rows) {
				$this->cart->clear();

				unset($this->session->data['shipping_method']);
				unset($this->session->data['shipping_methods']);
				unset($this->session->data['payment_method']);
				unset($this->session->data['payment_methods']);
				unset($this->session->data['guest']);
				unset($this->session->data['comment']);
				unset($this->session->data['order_id']);	
				unset($this->session->data['coupon']);
				unset($this->session->data['reward']);
				unset($this->session->data['voucher']);
				unset($this->session->data['vouchers']);
			}
		}
		
		$this->data['title'] = $this->document->getTitle();
		$notice = $this->db->query("SELECT * FROM `" . DB_PREFIX . "setting` WHERE `key` = 'oc_notification' ");
		$this->data['notice'] = unserialize($notice->row['value']);
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$this->data['base'] = $this->config->get('config_ssl');
		} else {
			$this->data['base'] = $this->config->get('config_url');
		}
		
		$this->data['description'] = $this->document->getDescription();
		$this->data['keywords'] = $this->document->getKeywords();
		$this->data['links'] = $this->document->getLinks();	 
		$this->data['styles'] = $this->document->getStyles();
		$this->data['scripts'] = $this->document->getScripts();
		
		$this->data['lang'] = $this->language->get('code');
		$this->data['direction'] = $this->language->get('direction');
		
		if($this->request->get['route']=='product/product')
		{
			$this->data['google_analytics'] = "";
		}
		elseif($this->request->get['route']=='wholesale/wholesale/thanks')
		{
			$this->data['google_analytics'] = "<script async src='https://www.googletagmanager.com/gtag/js?id=AW-1020579853'></script> <script> window.dataLayer = window.dataLayer || []; function gtag(){dataLayer.push(arguments);} gtag('js', new Date()); gtag('config', 'AW-1020579853'); </script>";
		}
		else
		{

		// $this->data['google_analytics'] = html_entity_decode($this->config->get('config_google_analytics'), ENT_QUOTES, 'UTF-8');
			$google_analytics ="<script>
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
				(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
				m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
			";
			if($this->customer->getId())
			{
				$google_analytics.="ga('create', 'UA-24721193-1', 'auto', {
  userId: ".$this->customer->getId()."
});
			";
			}
			else
			{
				$google_analytics.="ga('create', 'UA-24721193-1', 'auto');
			";
			}
			$google_analytics.="ga('send', 'pageview');";
			if($this->customer->getId())
			{
				//$google_analytics.="ga('set', 'userId', ".$this->customer->getId().");";
			}

			$google_analytics.="</script>";

			$google_analytics.='<script type="text/javascript"> if (!window.mstag) mstag = {loadTag : function(){},time : (new Date()).getTime()};</script> <script id="mstag_tops" type="text/javascript" src="//flex.msn.com/mstag/site/094b815f-e894-4bb6-b24d-bea6328f5c4e/mstag.js"></script> <script type="text/javascript"> mstag.loadTag("analytics", {dedup:"1",domainId:"1325622",type:"1",revenue:"",actionid:"249384"})</script> <noscript> <iframe src="//flex.msn.com/mstag/tag/094b815f-e894-4bb6-b24d-bea6328f5c4e/analytics.html?dedup=1&domainId=1325622&type=1&revenue=&actionid=249384" frameborder="0" scrolling="no" width="1" height="1" style="visibility:hidden;display:none"> </iframe> </noscript>';
			
			$this->data['google_analytics'] = $google_analytics;
		}
		
		$this->language->load('common/header');
		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$server = HTTPS_IMAGE;
		} else {
			$server = HTTP_IMAGE;
		}

		if ($this->config->get('config_icon') && file_exists(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->data['icon'] = $server . $this->config->get('config_icon');
		} else {
			$this->data['icon'] = '';
		}
		
		$this->data['name'] = $this->config->get('config_name');

		if ($this->config->get('config_logo') && file_exists(DIR_IMAGE . $this->config->get('config_logo'))) {
			$this->data['logo'] = $server . $this->config->get('config_logo');
		} else {
			$this->data['logo'] = '';
		}
		
		$this->data['page_class'] = str_replace('/', '_', $this->request->get['route']);
		$this->data['text_home'] = $this->language->get('text_home');
		$this->data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		$this->data['text_shopping_cart'] = $this->language->get('text_shopping_cart');
		$this->data['text_search'] = $this->language->get('text_search');
		$this->data['text_welcome'] = sprintf($this->language->get('text_welcome'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '', 'SSL'));
		$this->data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', 'SSL'), $this->customer->getFirstName(), $this->url->link('account/logout', '', 'SSL'));
		$this->data['text_account'] = $this->language->get('text_account');
		$this->data['text_checkout'] = $this->language->get('text_checkout');
		$this->data['text_faq'] = $this->language->get('text_faq');
		$this->data['text_catalog'] = $this->language->get('text_catalog');
		$this->data['text_reparing'] = $this->language->get('text_reparing');
		$this->data['text_lbb'] = $this->language->get('text_lbb');
		$this->data['text_whole'] = $this->language->get('text_whole');


		$this->data['home'] = $this->url->link('common/home');
		$this->data['wishlist'] = $this->url->link('account/wishlist');
		$this->data['logged'] = $this->customer->isLogged();
		$this->data['account'] = $this->url->link('account/account', '', 'SSL');
		$this->data['shopping_cart'] = $this->url->link('checkout/cart', '', 'SSL');
		$this->data['checkout'] = $this->url->link('checkout/checkout', '', 'SSL');
		$this->data['contact'] = $this->url->link('information/contact', '', 'SSL');
		$this->data['faq'] = $this->url->link('account/faq', '', 'SSL');
		$this->data['catalog'] = $this->url->link('catalog/catalog', '', 'SSL');
		$this->data['repaing'] = $this->url->link('account/faq', '', 'SSL');
		$this->data['lbb'] = $this->url->link('buyback/buyback', '', 'SSL');
		$this->data['whole'] = $this->url->link('wholesale/wholesale', '', 'SSL');
		$this->data['comingsoon'] = $this->url->link('misc/comingsoon', '', 'SSL');
		$this->data['total_cart_items'] = $this->cart->countProducts();
		
		if (isset($this->request->get['filter_name'])) {
			$this->data['filter_name'] = $this->request->get['filter_name'];
		} else {
			$this->data['filter_name'] = '';
		}

			if (isset($this->request->get['product_id'])) {
				$this->data['product_id'] = $this->request->get['product_id'];
				$this->load->model('catalog/product');
				$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
				$this->data['product_name'] = $product_info['name'];
				$this->data['product_url'] = $this->url->link('product/product', 'product_id=' . $product_info['product_id']);
				$this->load->model('tool/image');
				if ($product_info['image']) {
					$this->data['product_thumb'] = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
				} else {
					$this->data['product_thumb'] = '';
				}
			} else {
				$this->data['product_id'] = 0;
			}
		
		// Menu
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		
		$this->data['categories'] = array();

		$categories = $this->model_catalog_category->getCategories(0);
		
		foreach ($categories as $category) {
			if ($category['top']) {
				$children_data = array();
				
				$children = $this->model_catalog_category->getCategories($category['category_id']);
				
				foreach ($children as $child) {
					$data = array(
						'filter_category_id'  => $child['category_id'],
						'filter_sub_category' => true	
						);		

					if ($this->config->get('config_product_count')) {
						$product_total = $this->model_catalog_product->getTotalProducts($data);
						
						$child['name'] .= ' (' . $product_total . ')';
					}

					$children_data[] = array(
						'name'  => $child['name'],
						'category_id' => $child['category_id'],
						'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])	
						);					
				}
				
				// Level 1
				$this->data['categories'][] = array(
					'name'     => $category['name'],
					'children' => $children_data,
					'category_id' => $category['category_id'],
					'column'   => $category['column'] ? $category['column'] : 1,
					'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
					);
			}
		}

		// Menu PPU2.0

		$this->data['menus'] = array();
		
		$menus = array();
		$menus = $this->config->get('boss_megamenu_menu');
		
		if (isset($menus)) {
			$sort_order = array(); 
			foreach ($menus as $key => $value) {
				$sort_order[$key] = $value['order'];
			}
			array_multisort($sort_order, SORT_ASC, $menus);
			
			$this->load->model('catalog/manufacturer');
			$this->load->model('tool/image');
			$this->load->model('catalog/information');
			$this->load->model('catalog/category');
			$this->load->model('catalog/product');


			
			foreach ($menus as $menu) {
				if ($menu['status']){
					$href = "#";
					$options = array(); 
					if (isset($menu['options'])) {
						foreach ($menu['options'] as $option) {
							// manufacturer
							if ($option['opt'] == 'manufacturer') {
								if ($href == "#") {
									$href = $this->url->link('product/manufacturer');
								}
								
								$manufacturers = array();
								if (isset($option['opt_manufacturer_ids'])) {
									foreach($option['opt_manufacturer_ids'] as $manufacturer_id){
										$result = $this->model_catalog_manufacturer->getManufacturer($manufacturer_id);
										$manufacturers[] = array(
											'name'       	  	=> $result['name'],
											'image'				=> $this->model_tool_image->resize($result['image'], isset($option['opt_manufacturer_img_w']) ? $option['opt_manufacturer_img_w'] : 45, isset($option['opt_manufacturer_img_h']) ? $option['opt_manufacturer_img_h'] : 45),
											'href'				=> $this->url->link('product/manufacturer/info', 'manufacturer_id=' . $result['manufacturer_id'])
										);
									}
								}
								
								$options[] = array(
									'type'				=> 'manufacturer',
									'width'				=> $menu['dropdown_width']/$menu['dropdown_column']*$option['fill_column'],
									'column'			=> $option['fill_column'],
									'manufacturers'		=> $manufacturers,
									'show_image'		=> $option['opt_manufacturer_img'],
									'show_name'			=> $option['opt_manufacturer_name']
								);
							}
							// category
							if ($option['opt'] == 'category') {
								$categories = array();
								if (isset($option['opt_category_id'])) {
									if ($href == "#") {
										$href = $this->url->link('product/category', 'path=20');
									}
									
									$results = $this->model_catalog_category->getCategories($option['opt_category_id']);
									foreach ($results as $category) {
										$categories[] = array(
											'name'     		=> $category['name'],
											'children'		=> $this->getChildrenCategory($category, $category['category_id']),
											'image'			=> $category['image'] ? $this->model_tool_image->resize($category['image'], isset($option['opt_category_img_w']) ? $option['opt_category_img_w'] : 45, isset($option['opt_category_img_h']) ? $option['opt_category_img_h'] : 45) : '',
											'href'     		=> $this->url->link('product/category', 'path=' . $category['category_id'])
										);
									}
									if ($option['opt_category_id'] != 0) {
										$result = $this->model_catalog_category->getCategory($option['opt_category_id']);
										$parent = array(
											'name'     		=> $result['name'],
											'image'			=> $result['image'] ? $this->model_tool_image->resize($result['image'], isset($option['opt_category_img_w']) ? $option['opt_category_img_w'] : 45, isset($option['opt_category_img_h']) ? $option['opt_category_img_h'] : 45) : '',
											'href'     		=> $this->url->link('product/category', 'path=' . $result['category_id'])
										);
									}
									
									$options[] = array(
										'type'				=> 'category',
										'width'				=> $menu['dropdown_width']/$menu['dropdown_column']*$option['fill_column'],
										'column'			=> $option['fill_column'],
										'parent'			=> (isset($parent) ? $parent : null),
										'categories'		=> $categories,
										'show_image'		=> $option['opt_category_show_img'],
										'show_parent'		=> ($option['opt_category_id'] == 0 ? 0 : $option['opt_category_show_parent']),
										'show_submenu'		=> $option['opt_category_show_sub']
									);
								}
							}
							// information
							if ($option['opt'] == 'information') {
								$informations = array();
								if (isset($option['opt_information_ids'])) {
									foreach($option['opt_information_ids'] as $information_id){
										$result = $this->model_catalog_information->getInformation($information_id);
										$informations[] = array(
											'title' => $result['title'],
											'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
										);
									}
								}
								
								$options[] = array(
									'type'				=> 'information',
									'width'				=> $menu['dropdown_width']/$menu['dropdown_column']*$option['fill_column'],
									'column'			=> $option['fill_column'],
									'informations'		=> $informations
								);
							}
							// static block
							if ($option['opt'] == 'static_block') {
								$options[] = array(
									'type'				=> 'static_block',
									'width'				=> $menu['dropdown_width']/$menu['dropdown_column']*$option['fill_column'],
									'column'			=> $option['fill_column'],
									'description'		=> html_entity_decode($option['opt_static_block_des'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8')
								);
							}
							// product
							if ($option['opt'] == 'product') {
								$products = array();
								if (isset($option['opt_product_ids'])) {
									
									foreach ($option['opt_product_ids'] as $product_id) {
										$product_info = $this->model_catalog_product->getProduct($product_id);
			
										if ($product_info) {
											if ($option['opt_product_show_img'] && $product_info['image']) {
												$image = $this->model_tool_image->resize($product_info['image'], isset($option['opt_product_img_w']) ? $option['opt_product_img_w'] : 45, isset($option['opt_product_img_h']) ? $option['opt_product_img_h'] : 45);
											} else {
												$image = false;
											}

											if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
												$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')));
											} else {
												$price = false;
											}
													
											$products[] = array(
												'thumb'   	 => $image,
												'name'    	 => $product_info['name'],
												'price'   	 => $price,
												'href'    	 => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
											);
										}
									}
									
									$options[] = array(
										'type'				=> 'product',
										'width'				=> $menu['dropdown_width']/$menu['dropdown_column']*$option['fill_column'],
										'column'			=> $option['fill_column'],
										'products'			=> $products,
									);
								}
							}
							// link to
							if ($option['opt'] == 'linkto') {
								if (isset($option['opt_linkto_link'])) {
									$href = $option['opt_linkto_link'];
								}
							}
						}
					}
					
					$this->data['menus'][] = array(
						'title'	 			=> $menu['title'][$this->config->get('config_language_id')],
						'href'				=> $href,
						'dropdown_width'	=> $menu['dropdown_width'],
						'column_width'		=> $menu['dropdown_width']/ ($menu['dropdown_column'] ? $menu['dropdown_column'] : 1),
						'options'			=> $options
					);
				}
			}
		}
		// end menu

		
		$this->children = array(
			'module/language',
			'module/currency',
			'module/cart',
			'module/catalog_menu'
			// 'module/side_cart',
			// 'module/xcart'
			);
		
				//echo DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/common/header.tpl';
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/common/header.tpl')) {
		$this->data['data'] = $this->data;

			// $this->children[] = 'module/side_cart';
			$this->children[] = 'module/toggle_menu';
			// print_r($this->children);exit;
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/common/header.tpl';
		} else {
			$this->template = 'default/template/common/header.tpl';
		}


		$this->render();
	}

	public function updateCartNew() {
		echo '('.$this->cart->countProducts().' Items)';
	}

	private function getChildrenCategory($category, $path)
	{
		$children_data = array();
		$children = $this->model_catalog_category->getCategories($category['category_id']);
		
		foreach ($children as $child) {
			$data = array(
				'filter_category_id'  => $child['category_id'],
				'filter_sub_category' => true	
			);		
								
			$children_data[] = array(
				'name'  	=> $child['name'],
				'children' 	=> $this->getChildrenCategory($child, $path . '_' . $child['category_id']),
				'column'   	=> 1,
				'href'  => $this->url->link('product/category', 'path=' . $path . '_' . $child['category_id'])	
			);
			
		}
		return $children_data;
	}
}
?>
