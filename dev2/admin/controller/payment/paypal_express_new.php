<?php
//-----------------------------------------
// Author: Qphoria@gmail.com
// Web: http://www.OpenCartGuru.com/
//-----------------------------------------
class ControllerPaymentPaypalExpressNew extends Controller {
	private $error = array();
	

	public function index() {

		/* START ERRORS */
		$errors = array(
			'warning',
			'apiuser',
			'apipass',
			'apisig'
		);
		/* END ERRORS */



		/* START COMMON STUFF */
		$classname = str_replace('vq2-' . basename(DIR_APPLICATION) . '_' . strtolower(get_parent_class($this)) . '_payment_', '', basename(__FILE__, '.php'));

		if (!isset($this->session->data['token'])) { $this->session->data['token'] = 0; }
		$this->data['token'] = $this->session->data['token'];
		$this->data = array_merge($this->data, $this->load->language('payment/' . $classname));

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate($errors))) {

			//1.5.x uses column_left column_right
			if (!method_exists($this->document, 'addBreadcrumb')) { //1.5.x


			}

			// Install Paypal Express sidebox
            $this->load->model('setting/extension');
            $this->model_setting_extension->uninstall('module', $classname);
            $this->model_setting_extension->install('module', $classname);
			//

			foreach ($this->request->post as $key => $value) {
				if (is_array($value)) { $this->request->post[$key] = implode(',', $value); }
			}
			$this->load->model('setting/setting');

			$this->model_setting_setting->editSetting($classname, $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect((((HTTPS_SERVER) ? HTTPS_SERVER : HTTP_SERVER) . 'index.php?token=' . $this->session->data['token'] . '&route=extension/payment'));
		}

		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'href'      => (((HTTPS_SERVER) ? HTTPS_SERVER : HTTP_SERVER) . 'index.php?token=' . $this->session->data['token'] . '&route=common/home'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);

   		$this->data['breadcrumbs'][] = array(
       		'href'      => (((HTTPS_SERVER) ? HTTPS_SERVER : HTTP_SERVER) . 'index.php?token=' . $this->session->data['token'] . '&route=extension/payment'),
       		'text'      => $this->language->get('text_payment'),
      		'separator' => ' :: '
   		);

   		$this->data['breadcrumbs'][] = array(
       		'href'      => (((HTTPS_SERVER) ? HTTPS_SERVER : HTTP_SERVER) . 'index.php?token=' . $this->session->data['token'] . '&route=payment/' . $classname),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);

		$this->data['action'] = (((HTTPS_SERVER) ? HTTPS_SERVER : HTTP_SERVER) . 'index.php?token=' . $this->session->data['token'] . '&route=payment/' . $classname);

		$this->data['cancel'] = (((HTTPS_SERVER) ? HTTPS_SERVER : HTTP_SERVER) . 'index.php?token=' . $this->session->data['token'] . '&route=extension/payment');

		$this->id       = 'content';
		$this->template = 'payment/' . $classname . '.tpl';

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
		$this->data['extension_class'] = 'payment';
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

		$this->data['tabs'] = array();

		$this->data['tabs'][] = array(
			'id'		=> 'tab_general',
			'title'		=> $this->language->get('tab_general')
		);

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

		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_test'),
			'type'			=> 'select',
			'name' 			=> $classname . '_test',
			'value' 		=> (isset($this->request->post[$classname . '_test'])) ? $this->request->post[$classname . '_test'] : $this->config->get($classname . '_test'),
			'required' 		=> false,
			'options'		=> array(
				'0' => $this->language->get('text_no'),
				'1' => $this->language->get('text_yes')
			)
		);

		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_apiuser'),
			'type'			=> 'text',
			'size'			=> '80',
			'name' 			=> $classname . '_apiuser',
			'value' 		=> (isset($this->request->post[$classname . '_apiuser'])) ? $this->request->post[$classname . '_apiuser'] : $this->config->get($classname . '_apiuser'),
			'required' 		=> true,
			'error'			=> (isset($this->error['apiuser'])) ? $this->error['apiuser'] : ''
		);

		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_apipass'),
			'type'			=> 'text',
			'size'			=> '80',
			'name' 			=> $classname . '_apipass',
			'value' 		=> (isset($this->request->post[$classname . '_apipass'])) ? $this->request->post[$classname . '_apipass'] : $this->config->get($classname . '_apipass'),
			'required' 		=> true,
			'error'			=> (isset($this->error['apipass'])) ? $this->error['apipass'] : ''
		);

		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_apisig'),
			'type'			=> 'text',
			'size'			=> '80',
			'name' 			=> $classname . '_apisig',
			'value' 		=> (isset($this->request->post[$classname . '_apisig'])) ? $this->request->post[$classname . '_apisig'] : $this->config->get($classname . '_apisig'),
			'required' 		=> true,
			'error'			=> (isset($this->error['apisig'])) ? $this->error['apisig'] : ''
		);
/*
		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_module'),
			'type'			=> 'select',
			'name' 			=> $classname . '_module',
			'value' 		=> (isset($this->request->post[$classname . '_module'])) ? $this->request->post[$classname . '_module'] : $this->config->get($classname . '_module'),
			'required' 		=> false,
			'options'		=> array(
				'0' => $this->language->get('text_no'),
				'1' => $this->language->get('text_yes')
			)
		);

		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_checkout_cart'),
			'type'			=> 'select',
			'name' 			=> $classname . '_checkout_cart',
			'value' 		=> (isset($this->request->post[$classname . '_checkout_cart'])) ? $this->request->post[$classname . '_checkout_cart'] : $this->config->get($classname . '_checkout_cart'),
			'required' 		=> false,
			'options'		=> array(
				'0' => $this->language->get('text_no'),
				'1' => $this->language->get('text_yes')
			)
		);

		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_module_cart'),
			'type'			=> 'select',
			'name' 			=> $classname . '_module_cart',
			'value' 		=> (isset($this->request->post[$classname . '_module_cart'])) ? $this->request->post[$classname . '_module_cart'] : $this->config->get($classname . '_module_cart'),
			'required' 		=> false,
			'options'		=> array(
				'0' => $this->language->get('text_no'),
				'1' => $this->language->get('text_yes')
			)
		);

		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_login'),
			'type'			=> 'select',
			'name' 			=> $classname . '_login',
			'value' 		=> (isset($this->request->post[$classname . '_login'])) ? $this->request->post[$classname . '_login'] : $this->config->get($classname . '_login'),
			'required' 		=> false,
			'options'		=> array(
				'0' => $this->language->get('text_no'),
				'1' => $this->language->get('text_yes')
			)
		);

		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_module_position'),
			'type'			=> 'select',
			'name' 			=> $classname . '_login',
			'value' 		=> (isset($this->request->post[$classname . '_login'])) ? $this->request->post[$classname . '_login'] : $this->config->get($classname . '_login'),
			'required' 		=> false,
			'options'		=> array(
				'left'  => $this->language->get('text_left'),
				'right' => $this->language->get('text_right')
			)
		);

		// Change position types for 1.5.x
		if (!method_exists($this->document, 'addBreadcrumb')) { //1.5.x
			$this->data['fields'][count($this->data['fields'])-1]['options'] = array();
			$this->data['fields'][count($this->data['fields'])-1]['options']['content_top'] = $this->language->get('text_content_top');
			$this->data['fields'][count($this->data['fields'])-1]['options']['column_left'] = $this->language->get('text_column_left');
			$this->data['fields'][count($this->data['fields'])-1]['options']['column_right'] = $this->language->get('text_column_right');
			$this->data['fields'][count($this->data['fields'])-1]['options']['content_bottom'] = $this->language->get('text_content_bottom');
		}
*/

		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_logo'),
			'type'			=> 'text',
			'size'			=> '80',
			'name' 			=> $classname . '_logo',
			'value' 		=> (isset($this->request->post[$classname . '_logo'])) ? $this->request->post[$classname . '_logo'] : $this->config->get($classname . '_logo'),
			'required' 		=> false,
			'error'			=> (isset($this->error['logo'])) ? $this->error['logo'] : '',
			'help'			=> $this->language->get('help_logo')
		);
		
		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_payment_action'),
			'type'			=> 'select',
			'name' 			=> $classname . '_payment_action',
			'value' 		=> (isset($this->request->post[$classname . '_payment_action'])) ? $this->request->post[$classname . '_payment_action'] : $this->config->get($classname . '_payment_action'),
			'required' 		=> false,
			'options'		=> array(
				'Sale' 			=> $this->language->get('text_sale'),
				'Authorization' => $this->language->get('text_auth')
			)
		);
		
		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_account'),
			'type'			=> 'select',
			'name' 			=> $classname . '_account',
			'value' 		=> (isset($this->request->post[$classname . '_account'])) ? $this->request->post[$classname . '_account'] : $this->config->get($classname . '_account'),
			'required' 		=> false,
			'options'		=> array(
				'0' => $this->language->get('text_no'),
				'1' => $this->language->get('text_yes')
			)
		);

		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_landing'),
			'type'			=> 'select',
			'name' 			=> $classname . '_landing',
			'value' 		=> (isset($this->request->post[$classname . '_landing'])) ? $this->request->post[$classname . '_landing'] : $this->config->get($classname . '_landing'),
			'required' 		=> false,
			'options'		=> array(
				'0' => $this->language->get('text_login'),
				'1' => $this->language->get('text_billing')
			)
		);

		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_checkout'),
			'type'			=> 'select',
			'name' 			=> $classname . '_checkout',
			'value' 		=> (isset($this->request->post[$classname . '_checkout'])) ? $this->request->post[$classname . '_checkout'] : $this->config->get($classname . '_checkout'),
			'required' 		=> false,
			'options'		=> array(
				'0' => $this->language->get('text_no'),
				'1' => $this->language->get('text_yes')
			)
		);

/*
		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_ajax'),
			'type'			=> 'select',
			'name' 			=> $classname . '_ajax',
			'value' 		=> (isset($this->request->post[$classname . '_ajax'])) ? $this->request->post[$classname . '_ajax'] : $this->config->get($classname . '_ajax'),
			'required' 		=> false,
			'options'		=> array(
				'0' => $this->language->get('text_disabled'),
				'1' => $this->language->get('text_enabled')
			)
		);
*/
/*
		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_customer_group'),
			'type'			=> 'select',
			'multiple'		=> true,
			'name' 			=> $classname . '_customer_group[]',
			'value' 		=> (isset($this->request->post[$classname . '_customer_group'])) ? $this->request->post[$classname . '_customer_group'] : $this->config->get($classname . '_customer_group'),
			'required' 		=> false,
			'options'		=> $customer_groups
		);
*/

		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_debug'),
			'type'			=> 'select',
			'name' 			=> $classname . '_debug',
			'value' 		=> (isset($this->request->post[$classname . '_debug'])) ? $this->request->post[$classname . '_debug'] : $this->config->get($classname . '_debug'),
			'required' 		=> false,
			'options'		=> array(
				'0' => $this->language->get('text_disabled'),
				'1' => $this->language->get('text_enabled')
			),
			'help'			=> $this->language->get('help_debug')
		);

		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_geo_zone'),
			'type'			=> 'select',
			'name' 			=> $classname . '_geo_zone_id',
			'value' 		=> (isset($this->request->post[$classname . '_geo_zone_id'])) ? $this->request->post[$classname . '_geo_zone_id'] : $this->config->get($classname . '_geo_zone_id'),
			'required' 		=> false,
			'options'		=> $geo_zones
		);

		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_order_status'),
			'type'			=> 'select',
			'name' 			=> $classname . '_order_status_id',
			'value' 		=> (isset($this->request->post[$classname . '_order_status_id'])) ? $this->request->post[$classname . '_order_status_id'] : $this->config->get($classname . '_order_status_id'),
			'required' 		=> false,
			'options'		=> $order_statuses
		);
		
		$this->data['fields'][] = array(
			'entry' 		=> $this->language->get('entry_unverified_order_status'),
			'type'			=> 'select',
			'name' 			=> $classname . '_unverified_order_status_id',
			'value' 		=> (isset($this->request->post[$classname . '_unverified_order_status_id'])) ? $this->request->post[$classname . '_unverified_order_status_id'] : $this->config->get($classname . '_unverified_order_status_id'),
			'required' 		=> false,
			'options'		=> $order_statuses
		);

		$this->data['fields'][] = array(
			'entry'			=> $this->language->get('entry_sort_order'),
			'type'			=> 'text',
			'name'			=> $classname . '_sort_order',
			'value'			=> (isset($this->request->post[$classname . '_sort_order'])) ? $this->request->post[$classname . '_sort_order'] : $this->config->get($classname . '_sort_order'),
			'required'		=> false,
		);
		/* END FIELDS */

        $this->response->setOutput($this->render(TRUE));
	}

	private function validate($errors = array()) {
		$classname = str_replace('vq2-' . basename(DIR_APPLICATION) . '_' . strtolower(get_parent_class($this)) . '_payment_', '', basename(__FILE__, '.php'));
		if (!$this->user->hasPermission('modify', 'payment/' . $classname)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($errors as $error) {
			if (isset($this->request->post[$classname . '_' . $error]) && !$this->request->post[$classname . '_' . $error]) {
				$this->error[$error] = $this->language->get('error_' . $error);
			}
			if ($error == 'types') {
				if (!isset($this->request->post[$classname . '_types']) || !$this->request->post[$classname . '_types']) {
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