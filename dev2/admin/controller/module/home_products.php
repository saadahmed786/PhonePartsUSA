<?php
class ControllerModuleHomeProducts extends Controller {
	private $error = array(); 
	
	public function index() {   
		$this->load->language('module/home_products');
		$this->load->model('tool/image');
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {			
			$this->model_setting_setting->editSetting('home_products', $this->request->post);		
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}
				
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_content_top'] = $this->language->get('text_content_top');
		$this->data['text_content_bottom'] = $this->language->get('text_content_bottom');		
		$this->data['text_column_left'] = $this->language->get('text_column_left');
		$this->data['text_column_right'] = $this->language->get('text_column_right');
		
		$this->data['entry_product1'] = $this->language->get('entry_product1');
		$this->data['entry_product2'] = $this->language->get('entry_product2');
		$this->data['entry_product3'] = $this->language->get('entry_product3');
		$this->data['entry_limit'] = $this->language->get('entry_limit');
		$this->data['entry_image'] = $this->language->get('entry_image');
		$this->data['entry_layout'] = $this->language->get('entry_layout');
		$this->data['entry_position'] = $this->language->get('entry_position');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_add_module'] = $this->language->get('button_add_module');
		$this->data['button_remove'] = $this->language->get('button_remove');
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->error['image'])) {
			$this->data['error_image'] = $this->error['image'];
		} else {
			$this->data['error_image'] = array();
		}
				
  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/home_products', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/home_products', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['token'] = $this->session->data['token'];

		if (isset($this->request->post['home_products1'])) {
			$this->data['home_products1'] = $this->request->post['home_products1'];
		} else {
			$this->data['home_products1'] = $this->config->get('home_products1');
		}
		
		
		if (isset($this->request->post['home_products2'])) {
			$this->data['home_products2'] = $this->request->post['home_products2'];
		} else {
			$this->data['home_products2'] = $this->config->get('home_products2');
		}	
		
		if (isset($this->request->post['home_products3'])) {
			$this->data['home_products3'] = $this->request->post['home_products3'];
		} else {
			$this->data['home_products3'] = $this->config->get('home_products3');
		}	
		
		if (isset($this->request->post['home_products4'])) {
			$this->data['home_products4'] = $this->request->post['home_products4'];
		} else {
			$this->data['home_products4'] = $this->config->get('home_products4');
		}		
				
		$this->load->model('catalog/product');
				
		if (isset($this->request->post['home_products1'])) {
			$products1 = explode(',', $this->request->post['home_products1']);
		} else {		
			$products1 = explode(',', $this->config->get('home_products1'));
		}
		
		if (isset($this->request->post['home_products2'])) {
			$products2 = explode(',', $this->request->post['home_products2']);
		} else {		
			$products2 = explode(',', $this->config->get('home_products2'));
		}
		
		if (isset($this->request->post['home_products3'])) {
			$products3 = explode(',', $this->request->post['home_products3']);
		} else {		
			$products3 = explode(',', $this->config->get('home_products3'));
		}
		
		if (isset($this->request->post['home_products4'])) {
			$products4 = explode(',', $this->request->post['home_products4']);
		} else {		
			$products4 = explode(',', $this->config->get('home_products4'));
		}
		
		
		$this->data['products1'] = array();
		
		foreach ($products1 as $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);
			if ($product_info['image'] && file_exists(DIR_IMAGE . $product_info['image'])) {
				$image = $this->model_tool_image->resize($product_info['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.jpg', 40, 40);
			}
			if ($product_info) {
				$this->data['products1'][] = array(
					'product_id' => $product_info['product_id'],
					'name'       => $product_info['name'],
					'image'		=>	$image
				);
			}
		}	
		
		$this->data['products2'] = array();
		
		foreach ($products2 as $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);
			if ($product_info['image'] && file_exists(DIR_IMAGE . $product_info['image'])) {
				$image = $this->model_tool_image->resize($product_info['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.jpg', 40, 40);
			}
			if ($product_info) {
				$this->data['products2'][] = array(
					'product_id' => $product_info['product_id'],
					'name'       => $product_info['name'],
					'image'		=>	$image
				);
			}
		}	
		
		
		$this->data['products3'] = array();
		
		foreach ($products3 as $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);
			
			if ($product_info['image'] && file_exists(DIR_IMAGE . $product_info['image'])) {
				$image = $this->model_tool_image->resize($product_info['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.jpg', 40, 40);
			}
			
			if ($product_info) {
				$this->data['products3'][] = array(
					'product_id' => $product_info['product_id'],
					'name'       => $product_info['name'],
					'image'		=>	$image
				);
			}
		}	
		
		
		$this->data['products4'] = array();
		
		foreach ($products4 as $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);
			if ($product_info['image'] && file_exists(DIR_IMAGE . $product_info['image'])) {
				$image = $this->model_tool_image->resize($product_info['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.jpg', 40, 40);
			}
			
			if ($product_info) {
				$this->data['products4'][] = array(
					'product_id' => $product_info['product_id'],
					'name'       => $product_info['name'],
					'image'		=>	$image
				);
			}
		}	
		
			
		$this->data['modules'] = array();
		
			
		
		$this->template = 'module/home_products.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/home_products')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		/*if (isset($this->request->post['featured_module'])) {
			foreach ($this->request->post['featured_module'] as $key => $value) {
				if (!$value['image_width'] || !$value['image_height']) {
					$this->error['image'][$key] = $this->language->get('error_image');
				}
			}
		}*/
				
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
}
?>