<?php
class ControllerModuleUpsellOffer extends Controller {
	private $error = array(); 
	
	public function install(){
		$this->load->model('upsell/offer');
		$this->model_upsell_offer->install();
	}
	
	public function uninstall(){
		$this->load->model('upsell/offer');
		$this->model_upsell_offer->uninstall();
	}
	
	public function insert() {
		$this->load->language('module/upsell_offer');

		$this->load->model('upsell/offer');
		
		$json = array();
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			if (isset($this->request->post['upsell_offer_store'])) {
				$this->request->post['stores'] = '^' . implode("^,^", $this->request->post['upsell_offer_store']) . '^';
			}
			
			$this->model_upsell_offer->addUpsellOffer($this->request->post);
					
			$json['success'] = $this->language->get('text_success');
			
			$this->response->setOutput(json_encode($json));
		} else {
			if ($this->error) {
				$json['error'] = $this->error;
				$json['error']['warning'] = $this->language->get('error_warning');
				
				$this->response->setOutput(json_encode($json));
			} else {	
				$this->getForm();
			}
		}
  	}

  	public function update() {
		$this->load->language('module/upsell_offer');

		$this->load->model('upsell/offer');
		
		$json = array();
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			if (isset($this->request->post['upsell_offer_store'])) {
				$this->request->post['stores'] = '^' . implode("^,^", $this->request->post['upsell_offer_store']) . '^';
			}
				
			$this->model_upsell_offer->editUpsellOffer($this->request->get['upsell_offer_id'], $this->request->post);
					
			$json['success'] = $this->language->get('text_success');
			
			$this->response->setOutput(json_encode($json));
		} else {
			if ($this->error) {
				$json['error'] = $this->error;
				$json['error']['warning'] = $this->language->get('error_warning');
				
				$this->response->setOutput(json_encode($json));
			} else {	
				$this->getForm();
			}
		}
  	}
	
	public function index() {
		$this->load->language('module/upsell_offer');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		$this->load->model('tool/image');
		$this->load->model('upsell/offer');
				
		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['text_heading_title'] = $this->language->get('text_heading_title');
		$this->data['form_title'] = $this->language->get('form_title');
		$this->data['text_image_manager'] = $this->language->get('text_image_manager');
		$this->data['text_yes'] = $this->language->get('text_yes');
		$this->data['text_no'] = $this->language->get('text_no');
		$this->data['text_browse'] = $this->language->get('text_browse');
		$this->data['text_clear'] = $this->language->get('text_clear');
		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_behaviour_nothing'] = $this->language->get('text_behaviour_nothing');
		$this->data['text_behaviour_last'] = $this->language->get('text_behaviour_last');
		$this->data['text_behaviour_auto'] = $this->language->get('text_behaviour_auto');
		$this->data['text_content_top'] = $this->language->get('text_content_top');
		$this->data['text_content_bottom'] = $this->language->get('text_content_bottom');		
		$this->data['text_column_left'] = $this->language->get('text_column_left');
		$this->data['text_column_right'] = $this->language->get('text_column_right');
		$this->data['text_choose_store'] = $this->language->get('text_choose_store');
		
		$this->data['tab_statistics'] = $this->language->get('tab_statistics');
		$this->data['tab_upsell_offers'] = $this->language->get('tab_upsell_offers');
		$this->data['tab_settings'] = $this->language->get('tab_settings');
		$this->data['tab_help'] = $this->language->get('tab_help');
		
		$this->data['entry_image'] = $this->language->get('entry_image');
		$this->data['entry_layout'] = $this->language->get('entry_layout');
		$this->data['entry_position'] = $this->language->get('entry_position');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_selector'] = $this->language->get('entry_selector');
		
		$this->data['entry_import_from'] = $this->language->get('entry_import_from');
		$this->data['entry_product_sort'] = $this->language->get('entry_product_sort');
		$this->data['entry_limit'] = $this->language->get('entry_limit');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$this->data['entry_product_image_size'] = $this->language->get('entry_product_image_size');
		$this->data['entry_product_list_image_size'] = $this->language->get('entry_product_list_image_size');
		$this->data['entry_general_title'] = $this->language->get('entry_general_title');
		$this->data['entry_general_description'] = $this->language->get('entry_general_description');
		
		$this->data['button_save_settings'] = $this->language->get('button_save_settings');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_insert_upsell_offer'] = $this->language->get('button_insert_upsell_offer');
		$this->data['button_delete'] = $this->language->get('button_delete');
		$this->data['button_add_module'] = $this->language->get('button_add_module');
		$this->data['button_remove'] = $this->language->get('button_remove');
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = array();
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
			'href'      => $this->url->link('module/upsell_offer', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/upsell_offer', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['upsell_offer_product_image_width'] = $this->config->get('upsell_offer_product_image_width');
		$this->data['upsell_offer_product_image_height'] = $this->config->get('upsell_offer_product_image_height');
		$this->data['upsell_offer_product_list_image_width'] = $this->config->get('upsell_offer_product_list_image_width');
		$this->data['upsell_offer_product_list_image_height'] = $this->config->get('upsell_offer_product_list_image_height');
		
		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
		$this->data['upsell_offer_description'] = $this->config->get('upsell_offer_description');
		
		$this->data['modules'] = array();
		
		$this->data['modules'] = $this->config->get('upsell_offer_module');
		
		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
		
		$this->data['token'] = $this->session->data['token'];
				
		$this->template = 'module/upsell_offer.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	public function getList(){
		$this->language->load('module/upsell_offer');
		
		$this->load->model('upsell/offer');

		if (isset($this->request->get['filter_name'])){
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'upsell_offer_id'; 
		}
		
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		$url = '';
		
		
		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
		
		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$this->data['upsell_offers'] = array();
		
		$data = array(
			'filter_name'	=> $filter_name,
			'sort'          => $sort,
			'order'         => $order,
			'start'         => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit'         => $this->config->get('config_admin_limit')
		);
		
		$upsell_offer_total = $this->model_upsell_offer->getTotalUpsellOffers($data);
		
		$results = $this->model_upsell_offer->getUpsellOffers($data);
		
		foreach($results as $result){
			$action = array();
		
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => '',
				'onclick'=> 'updateOffer(' . $result['upsell_offer_id'] . ');'
			);
			
			$action[] = array(
				'text' => $this->language->get('text_preview'),
				'href' => '',
				'onclick'=> 'previewOffer(' . $result['upsell_offer_id'] . ')'
			);
			
			$this->data['upsell_offers'][] = array(
				'upsell_offer_id' => $result['upsell_offer_id'],
				'name'	          => html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'),
				'total_price_min' => ($result['total_price_min'] > 0)?$result['total_price_min']:'',
				'total_price_max' => ($result['total_price_max'] > 0)?$result['total_price_max']:'',
				'date_start'      => ($result['date_start'] != '0000-00-00')?date($this->language->get('date_format_short'), strtotime($result['date_start'])):'',
				'date_end'        => ($result['date_end'] != '0000-00-00')?date($this->language->get('date_format_short'), strtotime($result['date_end'])):'',
				'selected'        => isset($this->request->post['selected']) && in_array($result['upsell_offer_id'], $this->request->post['selected']),
				'action'          => $action
			);
		}
		
		$this->data['column_name'] = $this->language->get('column_name');
		$this->data['column_total_price_min'] = $this->language->get('column_total_price_min');
		$this->data['column_total_price_max'] = $this->language->get('column_total_price_max');
		$this->data['column_date_start'] = $this->language->get('column_date_start');
		$this->data['column_date_end'] = $this->language->get('column_date_end');
		$this->data['column_action'] = $this->language->get('column_action');
		
		$this->data['button_filter'] = $this->language->get('button_filter');
		
		$this->data['text_yes'] = $this->language->get('text_yes');
		$this->data['text_no'] = $this->language->get('text_no');
		$this->data['text_all_customers'] = $this->language->get('text_all_customers');
		$this->data['text_newsletter_subscribers'] = $this->language->get('text_newsletter_subscribers');
		
		$this->data['token'] = $this->session->data['token'];
		
		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}
								
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
			
		$this->data['url'] = $url;
		
		$url = '';
		
		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
												
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $upsell_offer_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('module/upsell_offer/getList', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
			
		$this->data['pagination'] = $pagination->render();
		
		$this->data['filter_name'] = $filter_name;
		
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;
		
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		
		$this->template = 'module/upsell_offer_list.tpl';
		
		$this->response->setOutput($this->render());	
	}
	
	private function getForm() {
		$this->load->model('catalog/product');
		
		$json = array();
		
		$this->data['tab_general'] = $this->language->get('tab_general');
		$this->data['tab_conditions'] = $this->language->get('tab_conditions');
		$this->data['tab_products'] = $this->language->get('tab_products');
			
		$this->data['text_all_customers'] = $this->language->get('text_all_customers');
		$this->data['text_newsletter_subscribers'] = $this->language->get('text_newsletter_subscribers');
		$this->data['text_sort_date_added'] = $this->language->get('text_sort_date_added');
		$this->data['text_sort_price_asc'] = $this->language->get('text_sort_price_asc');
		$this->data['text_sort_price_desc'] = $this->language->get('text_sort_price_desc');
		$this->data['text_sort_most_viewed'] = $this->language->get('text_sort_most_viewed');
		$this->data['text_sort_less_viewed'] = $this->language->get('text_sort_less_viewed');
		$this->data['text_browse'] = $this->language->get('text_browse');
		$this->data['text_clear'] = $this->language->get('text_clear');
		$this->data['text_image_manager'] = $this->language->get('text_image_manager');
		$this->data['text_link'] = $this->language->get('text_link');
		$this->data['text_max_width'] = $this->language->get('text_max_width');
		$this->data['text_default'] = $this->language->get('text_default');
		$this->data['text_upsell_products_help'] = $this->language->get('text_upsell_products_help');
		$this->data['text_cart_products_help'] = $this->language->get('text_cart_products_help');
			
		$this->data['entry_name'] = $this->language->get('entry_name');
		$this->data['entry_title'] = $this->language->get('entry_title');
		$this->data['entry_description'] = $this->language->get('entry_description');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$this->data['entry_store'] = $this->language->get('entry_store');
		$this->data['entry_product'] = $this->language->get('entry_product');
		$this->data['entry_upsell_products'] = $this->language->get('entry_upsell_products');
		$this->data['entry_date_start'] = $this->language->get('entry_date_start');
		$this->data['entry_date_end'] = $this->language->get('entry_date_end');
		$this->data['entry_total_price_min'] = $this->language->get('entry_total_price_min');
		$this->data['entry_total_price_max'] = $this->language->get('entry_total_price_max');
			
		$this->data['button_save_upsell_offer'] = $this->language->get('button_save_upsell_offer');
		
		$this->data['upsell_offer_description'] = null;
		
		if (!isset($this->request->get['upsell_offer_id'])) {
			$this->data['action'] = 'index.php?route=module/upsell_offer/insert&token=' . $this->session->data['token'];
		} else {
			$this->data['action'] = 'index.php?route=module/upsell_offer/update&token=' . $this->session->data['token'] . '&upsell_offer_id=' . $this->request->get['upsell_offer_id'];
			$upsell_offer_info = $this->model_upsell_offer->getUpsellOffer($this->request->get['upsell_offer_id']);
			$this->data['upsell_offer_description'] = $this->model_upsell_offer->getUpsellOfferDescriptions($upsell_offer_info['upsell_offer_id']);
		}
			
		$this->data['token'] = $this->session->data['token'];
			
		$this->load->model('localisation/language');
		
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
			
		if (isset($upsell_offer_info)){
			$this->data['name'] 			= $upsell_offer_info['name'];
			$this->data['upsell_offer_product']	= $upsell_offer_info['upsell_products'];
			$upsell_products 			= explode(',', $upsell_offer_info['upsell_products']);
			$this->data['cart_product'] 		= $upsell_offer_info['cart_products'];
			$cart_products 				= explode(',', $upsell_offer_info['cart_products']);
			
			$this->data['upsell_offer_store']	= substr($upsell_offer_info['stores'], 1, -1);
			$this->data['upsell_offer_store'] 	= explode('^,^', $upsell_offer_info['stores']);
			
			$this->data['date_start'] 		= ($upsell_offer_info['date_start'] == '0000-00-00')?'':date('Y-m-d', strtotime($upsell_offer_info['date_start']));
			$this->data['date_end'] 		= ($upsell_offer_info['date_end'] == '0000-00-00')?'':date('Y-m-d', strtotime($upsell_offer_info['date_end']));
			$this->data['total_price_min'] 		= ($upsell_offer_info['total_price_min'] > 0)?$upsell_offer_info['total_price_min']:'';
			$this->data['total_price_max'] 		= ($upsell_offer_info['total_price_max'] > 0)?$upsell_offer_info['total_price_max']:'';
		} else {
			$this->data['name'] 			= '';
			$this->data['upsell_offer_product'] 	= '';
			$upsell_products 			= array();
			$this->data['cart_product'] 		= '';
			$cart_products 				= array();
			$this->data['upsell_offer_store'] 	= array(0);
			$this->data['date_start'] 		= '';
			$this->data['date_end'] 		= '';
			$this->data['total_price_min'] 		= '';
			$this->data['total_price_max'] 		= '';
		}
			
		$this->data['upsell_products'] = array();
			
		foreach ($upsell_products as $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);
				
			if ($product_info) {
				$this->data['upsell_products'][] = array(
					'product_id' => $product_info['product_id'],
					'name'       => $product_info['name']
				);
			}
		}
			
		$this->data['cart_products'] = array();
			
		foreach ($cart_products as $product_id) {
			$product_info = $this->model_catalog_product->getProduct($product_id);
				
			if ($product_info) {
				$this->data['cart_products'][] = array(
					'product_id' => $product_info['product_id'],
					'name'       => $product_info['name']
				);
			}
		}
			
		$this->load->model('setting/store');
		
		$this->data['stores'] = $this->model_setting_store->getStores();
			
		$this->template = 'module/upsell_offer_form.tpl';
					
		$json['output'] = $this->render();		
		
		$this->response->setOutput(json_encode($json));	
	}	
	
	public function delete() {
		$this->load->language('module/upsell_offer');
		
		$this->load->model('upsell/offer');
		
		$json = array();
		
		if (isset($this->request->post['selected']) && $this->validate()) {			
			foreach($this->request->post['selected'] as $upsell_offer_id){
				$this->model_upsell_offer->deleteUpsellOffer($upsell_offer_id);
			}
			
			$json['success'] = $this->language->get('text_success_delete');
		}
		
		
		$this->response->setOutput(json_encode($json));
	}	
	
	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'module/upsell_offer')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->request->post['name']) {
			$this->error['name'] = $this->language->get('error_name');
		}
		
		if (!$this->request->post['upsell_offer_product']) {
			$this->error['upsell_offer_product'] = $this->language->get('error_upsell_offer_product');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/upsell_offer')) {
			$this->error['warning'] = $this->language->get('error_permission');  
		}
		
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}
	
	public function autocomplete() {
		$json = array();
		
		if (isset($this->request->get['filter_name'])) {
			$this->load->model('upsell/offer');
			
			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}
			
			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];	
			} else {
				$limit = 20;	
			}			
						
			$data = array(
				'filter_name'         => $filter_name,
				'start'               => 0,
				'limit'               => $limit
			);
			
			$results = $this->model_upsell_offer->getUpsellOffers($data);
			
			foreach ($results as $result) {
				$json[] = array(
					'id' 	=> $result['upsell_offer_id'],
					'name'  => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);	
			}
		}

		$this->response->setOutput(json_encode($json));
	}
	
	public function preview() {
		$this->load->language('module/upsell_offer');

		$this->load->model('upsell/offer');
		
		$json = array();
		
		$upsell_products = $this->model_upsell_offer->getUpsellProducts($this->request->get['upsell_offer_id']);
		
		if ($upsell_products) {
			$upsell_products = explode(',', $upsell_products);
			
			$this->data['upsell_products'] = array();
			
			$this->load->model('catalog/product');
			
			$this->load->model('tool/image');
			
			$product_nr = count($upsell_products);
			
			foreach ($upsell_products as $product_id) {
				$product_info = $this->model_catalog_product->getProduct($product_id);

				if ($product_info) {
					if ($product_info['image']) {
						if ( $product_nr == 1) {
							$image = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_thumb_width'), $this->config->get('config_image_thumb_height'));
						} else {
							$image = $this->model_tool_image->resize($product_info['image'], $this->config->get('config_image_product_width'), $this->config->get('config_image_product_height'));
						}						
					} else {
						$image = false;
					}
					
					$product_specials = $this->model_catalog_product->getProductSpecials($product_id);
					
					if ( isset($product_specials[0]['price']) && (float)$product_specials[0]['price'] ) {
						$special = $product_specials[0]['price'];
					} else {
						$special = false;
					}
					
					if ( isset($product_specials[0]['price']) && $this->config->get('config_tax') ) {
						$tax = $this->currency->format((float)$product_specials[0]['price'] ? $product_specials[0]['price'] : $product_info['price']);
					} else {
						$tax = false;
					}
					
					$this->data['upsell_products'][] = array(
						'product_id' 	=> $product_info['product_id'],
						'name'       	=> $product_info['name'],
						'thumb'      	=> $image,
						'special'	=> $special,
						'price'	     	=> $product_info['price'],
						'minimum'    	=> ($product_info['minimum'])?$product_info['minimum']:'1',
						'tax'	     	=> $tax,
						'text_minimum'  => sprintf($this->language->get('text_minimum'), $product_info['minimum'])
					);
				}
			}
			
			$upsell_offer_description = $this->model_upsell_offer->getUpsellOfferDescriptions($this->request->get['upsell_offer_id']);
			
			$this->data['title'] = $upsell_offer_description[$this->config->get('config_language_id')]['title'];
			$this->data['description'] = $upsell_offer_description[$this->config->get('config_language_id')]['description'];
			
			$this->data['text_qty'] = $this->language->get('text_qty');
			$this->data['text_price'] = $this->language->get('text_price');
			$this->data['text_tax'] = $this->language->get('text_tax');
			
			$this->data['button_cart'] = $this->language->get('button_cart');
			
			if ( $product_nr == 1) {
				$this->template = 'module/upsell_offer_preview.tpl';
			} else {
				$this->template = 'module/upsell_offer_list_preview.tpl';
			}
			
			$json['output'] = $this->render();		
		}
		
		$this->response->setOutput(json_encode($json));
	}
	
	public function saveSettings() {
		$this->load->language('module/upsell_offer');
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$json = array();
			
			$this->model_setting_setting->editSetting('upsell_offer', $this->request->post);		
					
			$json['success'] = $this->language->get('text_success');
						
			$this->response->setOutput(json_encode($json));
		}
  	}
	
	public function autocompleteProduct() {
		$json = array();
		
		if (isset($this->request->get['filter_name'])) {
			$this->load->model('catalog/product');
			
			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}
			
			if (isset($this->request->get['limit'])) {
				$limit = $this->request->get['limit'];	
			} else {
				$limit = 20;	
			}			
						
			$data = array(
				'filter_name'         => $filter_name,
				'start'               => 0,
				'limit'               => $limit
			);
			
			$results = $this->model_catalog_product->getProducts($data);
			
			foreach ($results as $result) {
				$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);	
			}
		}

		$this->response->setOutput(json_encode($json));
	}
}
?>