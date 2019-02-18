<?php  
class ControllerModuleOmf extends Controller {
	protected function index() {
		if (!$this->config->get('omf_installed')) {
			$this->redirect('omf_install/index.php');
		}
		$this->language->load('omf/common');

		if (isset($this->request->post['config_mobile_front_page_cat_list'])) {
			$this->data['config_mobile_front_page_cat_list'] = $this->request->post['config_mobile_front_page_cat_list'];
		} else {
			$this->data['config_mobile_front_page_cat_list'] = $this->config->get('config_mobile_front_page_cat_list');
		}

		$this->data['text_language'] = $this->language->get('text_language');
    	$this->data['text_currency'] = $this->language->get('text_currency');
		$this->data['text_home'] = $this->language->get('text_home');
		$this->data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
		$this->data['text_cart'] = $this->language->get('text_cart');
		$this->data['text_search'] = $this->language->get('text_search');
		$this->data['text_search_link'] = $this->language->get('text_search_link');
		$this->data['text_checkout'] = $this->language->get('text_checkout');
		$this->data['text_welcome'] = sprintf($this->language->get('text_welcome'), $this->url->link('account/login', '', 'SSL'), $this->url->link('account/register', '', 'SSL'));
		$this->data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', 'SSL'), $this->customer->getFirstName(), $this->url->link('account/logout', '', 'SSL'));
		$this->data['text_information'] = $this->language->get('text_information');
		$this->data['text_contact'] = $this->language->get('text_contact');
		$this->data['text_account'] = $this->language->get('text_account');
		$this->data['text_order'] = $this->language->get('text_order');

		//For the contact submenu
		$this->language->load('information/contact');
		$this->data['text_address'] = str_replace(':','',$this->language->get('text_address'));
		$this->data['text_enquiry'] = str_replace(':','',$this->language->get('entry_enquiry'));
		$this->data['text_call'] = $this->language->get('text_call');

		//OMF specific
		$this->data['text_all_categories'] = $this->language->get('text_all_categories');

		if (defined('VERSION') && (version_compare(VERSION, '1.5.3', '<') == true)) {
			$this->data['button_apply'] = $this->language->get('button_apply');
		}

		if (isset($this->request->get['filter_name'])) {
			$this->data['filter_name'] = $this->request->get['filter_name'];
		} else {
			$this->data['filter_name'] = '';
		}

		$this->load->model('catalog/information');
		
		$this->data['informations'] = array();

		foreach ($this->model_catalog_information->getInformations() as $result) {
			$this->data['informations'][] = array(
				'title' => $result['title'],
				'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
			);
    	}

		$this->data['home'] = $this->url->link('common/home');
		$this->data['wishlist'] = $this->url->link('account/wishlist');
		$this->data['logged'] = $this->customer->isLogged();
		$this->data['account'] = $this->url->link('account/account', '', 'SSL');
		$this->data['order'] = $this->url->link('account/order', '', 'SSL');
		$this->data['cart'] = $this->url->link('checkout/cart');
		$this->data['contact'] = $this->url->link('information/contact');
		$this->data['info'] = $this->url->link('information/info_page_list');
		$this->data['checkout'] = $this->url->link('checkout/login', '', 'SSL');

		$this->data['address'] = urlencode(str_replace('\n', '+', $this->config->get('config_address')));
    	$this->data['telephone'] = $this->config->get('config_telephone');

		$this->data['text_blog']    = $this->language->get('text_blog');

		$this->load->model('omf/omf');
		$this->data['blog'] = $this->model_omf_omf->isBlogManagerInstalled();

		$route = empty($this->request->get['route']) ? 'common/home' : $this->request->get['route'];
		$this->data['route'] = $route;

		if($route == 'common/home') {

			$this->load->model('catalog/category');
			$this->load->model('catalog/product');

			$this->data['categories'] = array();

			$categories = $this->model_catalog_category->getCategories(0);

			foreach ($categories as $category) {
				if($this->config->get('config_mobile_display_top_cats') == true) {
					if ($category['top']){
						$children_data = array();

						$children = $this->model_catalog_category->getCategories($category['category_id']);

						foreach ($children as $child) {
							$data = array(
								'filter_category_id'  => $child['category_id'],
								'filter_sub_category' => true
							);

							#$product_total = $this->model_catalog_product->getTotalProducts($data);

							$children_data[] = array(
								'name'  => $child['name'] /*. ' (' . $product_total . ')'*/,
								'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
							);
						}

						// Level 1
						$this->data['categories'][] = array(
							'name'     => $category['name'],
							'children' => $children_data,
							'column'   => $category['column'] ? $category['column'] : 1,
							'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
						);
					}
				} else {
					$children_data = array();

					$children = $this->model_catalog_category->getCategories($category['category_id']);

					foreach ($children as $child) {
						$data = array(
							'filter_category_id'  => $child['category_id'],
							'filter_sub_category' => true
						);

						#$product_total = $this->model_catalog_product->getTotalProducts($data);

						$children_data[] = array(
							'name'  => $child['name'] /*. ' (' . $product_total . ')'*/,
							'href'  => $this->url->link('product/category', 'path=' . $category['category_id'] . '_' . $child['category_id'])
						);
					}

					// Level 1
					$this->data['categories'][] = array(
						'name'     => $category['name'],
						'children' => $children_data,
						'column'   => $category['column'] ? $category['column'] : 1,
						'href'     => $this->url->link('product/category', 'path=' . $category['category_id'])
					);
				}

			}

			$this->data['all_categories'] = $this->url->link('common/all_categories');
		}

		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/omf.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/omf.tpl';
		} else {
			$this->template = 'omf2/template/module/omf.tpl';
		}

		$this->children = array(
			'module/language',
			'module/currency',
		);
		
		$this->render();
	}

	public function saveScreenShot() {
		if ($this->request->server['REQUEST_METHOD'] == "POST") {
			/*if (!file_exists("admin/view/image/omfa/"))	{
				@mkdir("admin/view/image/omfa/");
			}*/
			
			//if (!file_exists("admin/view/image/omfa/desktop-theme-screenshot.png")) {				
				$filteredData = substr($this->request->post['data'], strpos($this->request->post['data'], ",") + 1);
		        $unencodedData = base64_decode($filteredData);
				file_put_contents('admin/view/image/omfa/desktop-theme-screenshot.png', $unencodedData);
			//}
		}
	}
}
?>