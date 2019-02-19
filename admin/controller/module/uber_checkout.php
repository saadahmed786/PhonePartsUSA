<?php
class ControllerModuleUberCheckout extends Controller {
	private $error = array();

	public function index() {

		/* START ERRORS */
		$errors = array(
			'warning',
		);
		/* END ERRORS */



		/* START COMMON STUFF */
		$classname = str_replace('vq2-admin_controller_module_', '', basename(__FILE__, '.php'));

		if (!isset($this->session->data['token'])) { $this->session->data['token'] = 0; }
		$this->data['token'] = $this->session->data['token'];
		$this->data = array_merge($this->data, $this->load->language('module/' . $classname));
		$this->data['classname'] = $classname;
		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate($errors))) {
			foreach ($this->request->post as $key => $value) {
				if (is_array($value)) { $this->request->post[$key] = implode(',', $value); }
			}

			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting($classname, $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect((((HTTPS_SERVER) ? HTTPS_SERVER : HTTP_SERVER) . 'index.php?token=' . $this->session->data['token'] . '&route=extension/module'));
		}

		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'href'      => (((HTTPS_SERVER) ? HTTPS_SERVER : HTTP_SERVER) . 'index.php?token=' . $this->session->data['token'] . '&route=common/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);

   		$this->data['breadcrumbs'][] = array(
       		'href'      => (((HTTPS_SERVER) ? HTTPS_SERVER : HTTP_SERVER) . 'index.php?token=' . $this->session->data['token'] . '&route=extension/module'),
       		'text'      => $this->language->get('text_module'),
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
       		'href'      => (((HTTPS_SERVER) ? HTTPS_SERVER : HTTP_SERVER) . 'index.php?token=' . $this->session->data['token'] . '&route=module/' . $classname),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);

		$this->data['action'] = (((HTTPS_SERVER) ? HTTPS_SERVER : HTTP_SERVER) . 'index.php?token=' . $this->session->data['token'] . '&route=module/' . $classname);

		$this->data['cancel'] = (((HTTPS_SERVER) ? HTTPS_SERVER : HTTP_SERVER) . 'index.php?token=' . $this->session->data['token'] . '&route=extension/module');

		$this->id       = 'content';
		$this->template = 'module/' . $classname . '.tpl';

		/* 14x backwards compatibility */
		if (method_exists($this->document, 'addBreadcrumb')) { //1.4.x
			$this->document->breadcrumbs = $this->data['breadcrumbs'];
			unset($this->data['breadcrumbs']);
		}//

		$this->children = array(
            'common/header',
            'common/footer'
        );

        foreach ($errors as $error) {
			if (isset($this->error[$error])) {
				$this->data['error_' . $error] = $this->error[$error];
			} else {
				$this->data['error_' . $error] = '';
			}
		}
		/* END COMMON STUFF */




		/* START FIELDS */
		$this->data['extension_class'] = 'module';
		$this->data['tab_class'] = 'htabs';

		$geo_zones = array();

		$this->load->model('localisation/geo_zone');

		$geo_zones[0] = $this->language->get('text_all_zones');
		foreach ($this->model_localisation_geo_zone->getGeoZones() as $geozone) {
			$geo_zones[$geozone['geo_zone_id']] = $geozone['name'];
		}

		$order_statuses = array();

		$this->load->model('localisation/order_status');

		foreach ($this->model_localisation_order_status->getOrderStatuses() as $order_status) {
			$order_statuses[$order_status['order_status_id']] = $order_status['name'];
		}

		$customer_groups = array();

		$this->load->model('sale/customer_group');

		foreach ($this->model_sale_customer_group->getCustomerGroups() as $customer_group) {
			$customer_groups[$customer_group['customer_group_id']] = $customer_group['name'];
		}

		$tax_classes = array();

		$this->load->model('localisation/tax_class');

		$tax_classes[0] = $this->language->get('text_none');
		foreach ($this->model_localisation_tax_class->getTaxClasses() as $tax_class) {
			$tax_classes[$tax_class['tax_class_id']] = $tax_class['title'];
		}

		$this->data['tabs'] = array();

		$this->data['tabs'][] = array(
			'id'		=> 'tab_general',
			'title'		=> $this->language->get('tab_general')
		);
/*
		$this->data['tabs'][] = array(
			'id'		=> 'tab_address',
			'title'		=> $this->language->get('tab_address')
		);
*/
		$this->data['fields'] = array();

		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_status'),
			'type'			=> 'select',
			'name' 			=> $classname . '_status',
			'value' 		=> (isset($this->request->post[$classname . '_status'])) ? $this->request->post[$classname . '_status'] : $this->config->get($classname . '_status'),
			'required' 		=> false,
			'options'		=> array(
				'0' => $this->language->get('text_disabled'),
				'1' => $this->language->get('text_enabled')
			)
		);
/*
		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_style'),
			'type'			=> 'select',
			'name' 			=> $classname . '_style',
			'value' 		=> (isset($this->request->post[$classname . '_style'])) ? $this->request->post[$classname . '_style'] : $this->config->get($classname . '_style'),
			'required' 		=> false,
			'options'		=> array(
				'normal' => $this->language->get('text_normal'),
				'popup'  => $this->language->get('text_popup')
			)
		);
*/
		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_shipping_style'),
			'type'			=> 'select',
			'name' 			=> $classname . '_shipping_style',
			'value' 		=> (isset($this->request->post[$classname . '_shipping_style'])) ? $this->request->post[$classname . '_shipping_style'] : $this->config->get($classname . '_shipping_style'),
			'required' 		=> false,
			'options'		=> array(
				'radio'  => $this->language->get('text_radio'),
				'select' => $this->language->get('text_select')
			)
		);

		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_payment_style'),
			'type'			=> 'select',
			'name' 			=> $classname . '_payment_style',
			'value' 		=> (isset($this->request->post[$classname . '_payment_style'])) ? $this->request->post[$classname . '_payment_style'] : $this->config->get($classname . '_payment_style'),
			'required' 		=> false,
			'options'		=> array(
				'radio'  => $this->language->get('text_radio'),
				'select' => $this->language->get('text_select')
			)
		);

		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_payment_update_total'),
			'type'			=> 'select',
			'name' 			=> $classname . '_payment_update_total',
			'value' 		=> (isset($this->request->post[$classname . '_payment_update_total'])) ? $this->request->post[$classname . '_payment_update_total'] : $this->config->get($classname . '_payment_update_total'),
			'required' 		=> false,
			'options'		=> array(
				'0' 	=> $this->language->get('text_disabled'),
				'1' 	=> $this->language->get('text_enabled')
			)
		);


		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_comment_update_total'),
			'type'			=> 'select',
			'name' 			=> $classname . '_comment_update_total',
			'value' 		=> (isset($this->request->post[$classname . '_comment_update_total'])) ? $this->request->post[$classname . '_comment_update_total'] : $this->config->get($classname . '_comment_update_total'),
			'required' 		=> false,
			'options'		=> array(
				'0' 	=> $this->language->get('text_disabled'),
				'1' 	=> $this->language->get('text_enabled')
			)
		);
		
		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_shipping_update_payment'),
			'type'			=> 'select',
			'name' 			=> $classname . '_shipping_update_payment',
			'value' 		=> (isset($this->request->post[$classname . '_shipping_update_payment'])) ? $this->request->post[$classname . '_shipping_update_payment'] : $this->config->get($classname . '_shipping_update_payment'),
			'required' 		=> false,
			'options'		=> array(
				'0' 	=> $this->language->get('text_disabled'),
				'1' 	=> $this->language->get('text_enabled')
			)
		);
		
		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_no_ship_address'),
			'type'			=> 'select',
			'name' 			=> $classname . '_no_ship_address',
			'value' 		=> (isset($this->request->post[$classname . '_no_ship_address'])) ? $this->request->post[$classname . '_no_ship_address'] : $this->config->get($classname . '_no_ship_address'),
			'required' 		=> false,
			'options'		=> array(
				'0' 	=> $this->language->get('text_no'),
				'1' 	=> $this->language->get('text_yes')
			)
		);
		
		/*
		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_login'),
			'type'			=> 'select',
			'name' 			=> $classname . '_login',
			'value' 		=> (isset($this->request->post[$classname . '_login'])) ? $this->request->post[$classname . '_login'] : $this->config->get($classname . '_login'),
			'required' 		=> false,
			'options'		=> array(
				'1' 	=> $this->language->get('text_enabled'),
				'0' 	=> $this->language->get('text_disabled')
			)
		);
*/
		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_captcha'),
			'type'			=> 'select',
			'name' 			=> $classname . '_captcha',
			'value' 		=> (isset($this->request->post[$classname . '_captcha'])) ? $this->request->post[$classname . '_captcha'] : $this->config->get($classname . '_captcha'),
			'required' 		=> false,
			'options'		=> array(
				'0' 	=> $this->language->get('text_disabled'),
				'1' 	=> $this->language->get('text_enabled')
			)
		);

		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_newsletter_default'),
			'type'			=> 'select',
			'name' 			=> $classname . '_newsletter_default',
			'value' 		=> (isset($this->request->post[$classname . '_newsletter_default'])) ? $this->request->post[$classname . '_newsletter_default'] : $this->config->get($classname . '_newsletter_default'),
			'required' 		=> false,
			'options'		=> array(
				'1' 	=> $this->language->get('text_yes'),
				'0' 	=> $this->language->get('text_no')
			)
		);
		/*
		## Address Tab
		$this->data['fields'][] = array(
			'tab'			=> 'tab_address',
			'entry' 		=> $this->language->get('entry_address_firstname'),
			'type'			=> 'radio',
			'name' 			=> $classname . '_address_firstname',
			'value' 		=> (isset($this->request->post[$classname . '_address_firstname'])) ? $this->request->post[$classname . '_address_firstname'] : $this->config->get($classname . '_address_firstname') ? $this->config->get($classname . '_address_firstname') : 'r',
			'required' 		=> false,
			'options'		=> array(
				'r'		=> $this->language->get('text_required'),
				'o' 	=> $this->language->get('text_optional'),
				'h' 	=> $this->language->get('text_hidden')
			)
		);

		$this->data['fields'][] = array(
			'tab'			=> 'tab_address',
			'entry' 		=> $this->language->get('entry_address_lastname'),
			'type'			=> 'radio',
			'name' 			=> $classname . '_address_lastname',
			'value' 		=> (isset($this->request->post[$classname . '_address_lastname'])) ? $this->request->post[$classname . '_address_lastname'] : $this->config->get($classname . '_address_lastname') ? $this->config->get($classname . '_address_lastname') : 'r',
			'required' 		=> false,
			'options'		=> array(
				'r'		=> $this->language->get('text_required'),
				'o' 	=> $this->language->get('text_optional'),
				'h' 	=> $this->language->get('text_hidden')
			)
		);

		$this->data['fields'][] = array(
			'tab'			=> 'tab_address',
			'entry' 		=> $this->language->get('entry_address_company'),
			'type'			=> 'radio',
			'name' 			=> $classname . '_address_company',
			'value' 		=> (isset($this->request->post[$classname . '_address_company'])) ? $this->request->post[$classname . '_address_company'] : $this->config->get($classname . '_address_company') ? $this->config->get($classname . '_address_company') : 'o',
			'required' 		=> false,
			'options'		=> array(
				'r'		=> $this->language->get('text_required'),
				'o' 	=> $this->language->get('text_optional'),
				'h' 	=> $this->language->get('text_hidden')
			),
			'help' 			=> $this->language->get('help_company')
		);

		$this->data['fields'][] = array(
			'tab'			=> 'tab_address',
			'entry' 		=> $this->language->get('entry_address_address_1'),
			'type'			=> 'radio',
			'name' 			=> $classname . '_address_address_1',
			'value' 		=> (isset($this->request->post[$classname . '_address_address_1'])) ? $this->request->post[$classname . '_address_address_1'] : $this->config->get($classname . '_address_address_1') ? $this->config->get($classname . '_address_address_1') : 'r',
			'required' 		=> false,
			'options'		=> array(
				'r'		=> $this->language->get('text_required'),
				'o' 	=> $this->language->get('text_optional'),
				'h' 	=> $this->language->get('text_hidden')
			)
		);

		$this->data['fields'][] = array(
			'tab'			=> 'tab_address',
			'entry' 		=> $this->language->get('entry_address_address_2'),
			'type'			=> 'radio',
			'name' 			=> $classname . '_address_address_2',
			'value' 		=> (isset($this->request->post[$classname . '_address_address_2'])) ? $this->request->post[$classname . '_address_address_2'] : $this->config->get($classname . '_address_address_2') ? $this->config->get($classname . '_address_address_2') : 'o',
			'required' 		=> false,
			'options'		=> array(
				'r'		=> $this->language->get('text_required'),
				'o' 	=> $this->language->get('text_optional'),
				'h' 	=> $this->language->get('text_hidden')
			)
		);

		$this->data['fields'][] = array(
			'tab'			=> 'tab_address',
			'entry' 		=> $this->language->get('entry_address_city'),
			'type'			=> 'radio',
			'name' 			=> $classname . '_address_city',
			'value' 		=> (isset($this->request->post[$classname . '_address_city'])) ? $this->request->post[$classname . '_address_city'] : $this->config->get($classname . '_address_city') ? $this->config->get($classname . '_address_city') : 'r',
			'required' 		=> false,
			'options'		=> array(
				'r'		=> $this->language->get('text_required'),
				'o' 	=> $this->language->get('text_optional'),
				'h' 	=> $this->language->get('text_hidden')
			)
		);

		$this->data['fields'][] = array(
			'tab'			=> 'tab_address',
			'entry' 		=> $this->language->get('entry_address_telephone'),
			'type'			=> 'radio',
			'name' 			=> $classname . '_address_telephone',
			'value' 		=> (isset($this->request->post[$classname . '_address_telephone'])) ? $this->request->post[$classname . '_address_telephone'] : $this->config->get($classname . '_address_telephone') ? $this->config->get($classname . '_address_telephone') : 'r',
			'required' 		=> false,
			'options'		=> array(
				'r'		=> $this->language->get('text_required'),
				'o' 	=> $this->language->get('text_optional'),
				'h' 	=> $this->language->get('text_hidden')
			)
		);

		$this->data['fields'][] = array(
			'tab'			=> 'tab_address',
			'entry' 		=> $this->language->get('entry_address_fax'),
			'type'			=> 'radio',
			'name' 			=> $classname . '_address_fax',
			'value' 		=> (isset($this->request->post[$classname . '_address_fax'])) ? $this->request->post[$classname . '_address_fax'] : $this->config->get($classname . '_address_fax') ? $this->config->get($classname . '_address_fax') : 'o',
			'required' 		=> false,
			'options'		=> array(
				'r'		=> $this->language->get('text_required'),
				'o' 	=> $this->language->get('text_optional'),
				'h' 	=> $this->language->get('text_hidden')
			)
		);
		*/
		/* END FIELDS */

        $this->response->setOutput($this->render(TRUE));
	}

	private function validate($errors = array()) {
		$classname = str_replace('vq2-admin_controller_module_', '', basename(__FILE__, '.php'));
		if (!$this->user->hasPermission('modify', 'module/' . $classname)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($errors as $error) {
			if (isset($this->request->post[$classname . '_' . $error]) && !$this->request->post[$classname . '_' . $error]) {
				$this->error[$error] = $this->language->get('error_' . $error);
			}
			if ($error == 'service') {
				if (!isset($this->request->post[$classname . '_service']) || !$this->request->post[$classname . '_service']) {
					$this->error[$error] = $this->language->get('error_' . $error);
				}
			}
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
?>