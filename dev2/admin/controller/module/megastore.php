<?php

class ControllerModulemegastore extends Controller {
	
	private $error = array(); 
	
	public function index() {   
		//Load the language file for this module
		$this->load->language('module/megastore');

		//Set the title from the language file $_['heading_title'] string
		$this->document->setTitle($this->language->get('heading_title'));
		
		

		
		
		//Load the settings model. You can also add any other models you want to load here.
		$this->load->model('setting/setting');
		
		//Save the settings if the user has submitted the admin form (ie if someone has pressed save).
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('megastore', $this->request->post);	

				
					
			$this->session->data['success'] = $this->language->get('text_success');

                $this->redirect(HTTPS_SERVER . 'index.php?route=module/megastore&token=' . $this->session->data['token']);
						
		}
		
			$this->data['text_image_manager'] = 'Image manager';
					$this->data['token'] = $this->session->data['token'];		
		
		$text_strings = array(
				'heading_title',
				'text_enabled',
				'text_disabled',
				'text_content_top',
				'text_content_bottom',
				'text_column_left',
				'text_column_right',
				'entry_layout',
				'entry_position',
				'entry_status',
				'entry_sort_order',
				'button_save',
				'button_cancel',
				'button_add_module',
				'button_remove',
				'entry_example' 
		);
		
		foreach ($text_strings as $text) {
			$this->data[$text] = $this->language->get($text);
		}
		

		// store config data
		
		$config_data = array(
			'general_status',
			'product_per_pow',
			'column_position',
			'refine_search_style',
			'default_list_grid',
			'slideshow_speed',
			'layout_type',
			'font_status',
			'body_font',
			'categories_bar',
			'categories_bar_px',
			'headlines',
			'headlines_px',
			'footer_headlines',
			'footer_headlines_px',
			'customfooter',
			'colors_status',
			'megastore_color',
			'background_status',
			'general_background',
			'general_background_background',
			'general_background_position',
			'general_background_repeat',
			'general_background_attachment',
			'footer_background',
			'footer_background_background',
			'footer_background_position',
			'footer_background_repeat',
			'payment_status',
			'payment_mastercard_status',
			'payment_mastercard',
			'payment_visa',
			'payment_visa_status',
			'payment_moneybookers',
			'payment_moneybookers_status',
			'payment_paypal',
			'payment_paypal_status',
			'ex_tax_price',
			'reward_points',
			'reviews',
			'product_social_share',
			'animation_hover_effect',
			'body_font_px',
			'body_font_smaller_px',
			'big_headlines',
			'big_headlines_px',
			'custom_price',
			'custom_price_px',
			'custom_price_on_product_page',
			'top_bar_breadcrumb_background',
			'top_bar_breadcrumb_body',
			'top_bar_breadcrumb_link',
			'top_bar_breadcrumb_headlines',
			'content_background',
			'content_body_and_old_price',
			'content_product_name',
			'content_price',
			'content_headlines',
			'content_links',
			'footer_backgrounds',
			'footer_body',
			'footer_headliness',
			'footer_links',
			'category_bar_top_gradient',
			'category_bar_bottom_gradient',
			'category_bar_font_color',
			'add_to_cart_button_top_gradient',
			'add_to_cart_button_bottom_gradient',
			'add_to_cart_button_font_color',
			'body_backgrounds',
			'standard_button_font_color',
			'standard_button_bottom_gradient',
			'standard_button_top_gradient'
		);
		
		foreach ($config_data as $conf) {
			if (isset($this->request->post[$conf])) {
				$this->data[$conf] = $this->request->post[$conf];
			} else {
				$this->data[$conf] = $this->config->get($conf);
			}
		}
		
		
		
	
		//This creates an error message. The error['warning'] variable is set by the call to function validate() in this controller (below)
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
            if (isset($this->session->data['success'])) {
                $this->data['success'] = $this->session->data['success'];

                unset($this->session->data['success']);
            } else {
                $this->data['success'] = '';
            }
		
		//SET UP BREADCRUMB TRAIL. YOU WILL NOT NEED TO MODIFY THIS UNLESS YOU CHANGE YOUR MODULE NAME.
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
			'href'      => $this->url->link('module/megastore', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/megastore', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

	
		//This code handles the situation where you have multiple instances of this module, for different layouts.
		if (isset($this->request->post['megastore_module'])) {
			$modules = explode(',', $this->request->post['megastore_module']);
		} elseif ($this->config->get('megastore_module') != '') {
			$modules = explode(',', $this->config->get('megastore_module'));
		} else {
			$modules = array();
		}			
				
		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
		
		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
				
		foreach ($modules as $module) {
			if (isset($this->request->post['megastore_' . $module . '_layout_id'])) {
				$this->data['megastore_' . $module . '_layout_id'] = $this->request->post['megastore_' . $module . '_layout_id'];
			} else {
				$this->data['megastore_' . $module . '_layout_id'] = $this->config->get('megastore_' . $module . '_layout_id');
			}	
			
			if (isset($this->request->post['megastore_' . $module . '_position'])) {
				$this->data['megastore_' . $module . '_position'] = $this->request->post['megastore_' . $module . '_position'];
			} else {
				$this->data['megastore_' . $module . '_position'] = $this->config->get('megastore_' . $module . '_position');
			}	
			
			if (isset($this->request->post['megastore_' . $module . '_status'])) {
				$this->data['megastore_' . $module . '_status'] = $this->request->post['megastore_' . $module . '_status'];
			} else {
				$this->data['megastore_' . $module . '_status'] = $this->config->get('megastore_' . $module . '_status');
			}	
						
			if (isset($this->request->post['megastore_' . $module . '_sort_order'])) {
				$this->data['megastore_' . $module . '_sort_order'] = $this->request->post['megastore_' . $module . '_sort_order'];
			} else {
				$this->data['megastore_' . $module . '_sort_order'] = $this->config->get('megastore_' . $module . '_sort_order');
			}				
		}
		

		
		$this->data['modules'] = $modules;
		
		if (isset($this->request->post['megastore_module'])) {
			$this->data['megastore_module'] = $this->request->post['megastore_module'];
		} else {
			$this->data['megastore_module'] = $this->config->get('megastore_module');
		}

		//Choose which template file will be used to display this request.
		$this->template = 'module/megastore.tpl';
		$this->children = array(
			'common/header',
			'common/footer',
		);
		
		//Send the output.
		$this->response->setOutput($this->render());
	}
	
	/*
	 * 
	 * This function is called to ensure that the settings chosen by the admin user are allowed/valid.
	 * You can add checks in here of your own.
	 * 
	 */
	
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/megastore')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}


}
?>
