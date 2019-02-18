<?php
class ControllerPaymentPPPayflowPro extends Controller {
	private $error = array();

	public function index() {

		if (version_compare('1.5.5',VERSION,'>')) {
			//Opencart version less than 1.5.5.0
			$this->load->language('payment/pp_payflow_pro');
		}else {
			$this->language->load('payment/pp_payflow_pro');
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->load->model('setting/setting');

			//if password didn't change, keep the current one
			if (isset($this->request->post['pp_payflow_pro_password']) && $this->request->post['pp_payflow_pro_password']=='password') {
				$this->request->post['pp_payflow_pro_password'] = $this->config->get('pp_payflow_pro_password');
			}

			$this->model_setting_setting->editSetting('pp_payflow_pro', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		$this->data['text_live'] = $this->language->get('text_live');
		$this->data['text_test'] = $this->language->get('text_test');
		$this->data['text_sale'] = $this->language->get('text_sale');
		$this->data['text_authorization'] = $this->language->get('text_authorization');

		$this->data['entry_partner'] = $this->language->get('entry_partner');
		$this->data['entry_vendor'] = $this->language->get('entry_vendor');
		$this->data['entry_username'] = $this->language->get('entry_username');
		$this->data['entry_password'] = $this->language->get('entry_password');
		$this->data['entry_server'] = $this->language->get('entry_server');
		$this->data['entry_transaction'] = $this->language->get('entry_transaction');
		$this->data['entry_timeout'] = $this->language->get('entry_timeout');
		$this->data['entry_timeout_order_status'] = $this->language->get('entry_timeout_order_status');
		$this->data['entry_fps_order_status'] = $this->language->get('entry_fps_order_status');
		$this->data['entry_order_status'] = $this->language->get('entry_order_status');
		$this->data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$this->data['entry_total'] = $this->language->get('entry_total');
		$this->data['entry_comment1'] = $this->language->get('entry_comment1');
		$this->data['entry_comment1_input'] = $this->language->get('entry_comment1_input');
		$this->data['entry_comment2'] = $this->language->get('entry_comment2');
		$this->data['entry_comment2_input'] = $this->language->get('entry_comment2_input');
		$this->data['entry_invnum'] = $this->language->get('entry_invnum');
		$this->data['entry_idprefix'] = $this->language->get('entry_idprefix');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		$this->data['tab_general'] = $this->language->get('tab_general');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['partner'])) {
			$this->data['error_partner'] = $this->error['partner'];
		} else {
			$this->data['error_partner'] = '';
		}

 		if (isset($this->error['vendor'])) {
			$this->data['error_vendor'] = $this->error['vendor'];
		} else {
			$this->data['error_vendor'] = '';
		}

 		if (isset($this->error['username'])) {
			$this->data['error_username'] = $this->error['username'];
		} else {
			$this->data['error_username'] = '';
		}

 		if (isset($this->error['password'])) {
			$this->data['error_password'] = $this->error['password'];
		} else {
			$this->data['error_password'] = '';
		}

 		if (isset($this->error['timeout'])) {
			$this->data['error_timeout'] = $this->error['timeout'];
		} else {
			$this->data['error_timeout'] = '';
		}

		//$this->document->breadcrumbs = array();
		$this->data['breadcrumbs'] = array();


   		//$this->document->breadcrumbs[] = array(
   		$this->data['breadcrumbs'][] = array(
       		//'href'      => $this->url->https('common/home'),
		'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);

   		//$this->document->breadcrumbs[] = array(
   		$this->data['breadcrumbs'][] = array(
       		//'href'      => $this->url->https('extension/payment'),
		'href'      => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
       		'text'      => $this->language->get('text_payment'),
      		'separator' => ' :: '
   		);

   		//$this->document->breadcrumbs[] = array(
   		$this->data['breadcrumbs'][] = array(
       		//'href'      => $this->url->https('payment/pp_payflow_pro'),
		'href'      => $this->url->link('payment/pp_payflow_pro', 'token=' . $this->session->data['token'], 'SSL'),
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);

		//$this->data['action'] = $this->url->https('payment/pp_payflow_pro');
		$this->data['action'] = $this->url->link('payment/pp_payflow_pro', 'token=' . $this->session->data['token'], 'SSL');

		//$this->data['cancel'] = $this->url->https('extension/payment');
		$this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

		if (isset($this->request->post['pp_payflow_pro_partner'])) {
			$this->data['pp_payflow_pro_partner'] = $this->request->post['pp_payflow_pro_partner'];
		} else {
			$this->data['pp_payflow_pro_partner'] = $this->config->get('pp_payflow_pro_partner');
		}

		if (isset($this->request->post['pp_payflow_pro_vendor'])) {
			$this->data['pp_payflow_pro_vendor'] = $this->request->post['pp_payflow_pro_vendor'];
		} else {
			$this->data['pp_payflow_pro_vendor'] = $this->config->get('pp_payflow_pro_vendor');
		}

		if (isset($this->request->post['pp_payflow_pro_username'])) {
			$this->data['pp_payflow_pro_username'] = $this->request->post['pp_payflow_pro_username'];
		} else {
			$this->data['pp_payflow_pro_username'] = $this->config->get('pp_payflow_pro_username');
		}

		$this->data['pp_payflow_pro_password'] = '';
		if ($this->config->get('pp_payflow_pro_password')){
			$this->data['pp_payflow_pro_password'] = 'password';//$this->config->get('pp_payflow_pro_password');
		}

		if (isset($this->request->post['pp_payflow_pro_server'])) {
			$this->data['pp_payflow_pro_server'] = $this->request->post['pp_payflow_pro_server'];
		} else {
			$this->data['pp_payflow_pro_server'] = $this->config->get('pp_payflow_pro_server');
		}

		if (isset($this->request->post['pp_payflow_pro_transaction'])) {
			$this->data['pp_payflow_pro_transaction'] = $this->request->post['pp_payflow_pro_transaction'];
		} else {
			$this->data['pp_payflow_pro_transaction'] = $this->config->get('pp_payflow_pro_transaction');
		}

		if (isset($this->request->post['pp_payflow_pro_comment1'])) {
			$this->data['pp_payflow_pro_comment1'] = $this->request->post['pp_payflow_pro_comment1'];
		} else {
			$this->data['pp_payflow_pro_comment1'] = $this->config->get('pp_payflow_pro_comment1');
		}

		if (isset($this->request->post['pp_payflow_pro_comment2'])) {
			$this->data['pp_payflow_pro_comment2'] = $this->request->post['pp_payflow_pro_comment2'];
		} else {
			$this->data['pp_payflow_pro_comment2'] = $this->config->get('pp_payflow_pro_comment2');
		}

		if (isset($this->request->post['pp_payflow_pro_invnum'])) {
			$this->data['pp_payflow_pro_invnum'] = $this->request->post['pp_payflow_pro_invnum'];
		} else {
			$this->data['pp_payflow_pro_invnum'] = $this->config->get('pp_payflow_pro_invnum');
		}

		if (isset($this->request->post['pp_payflow_pro_idprefix'])) {
			$this->data['pp_payflow_pro_idprefix'] = $this->request->post['pp_payflow_pro_idprefix'];
		} else {
			$this->data['pp_payflow_pro_idprefix'] = $this->config->get('pp_payflow_pro_idprefix');
		}

        //added in 1.5.2j
		if (isset($this->request->post['pp_payflow_pro_timeout'])) {
			$this->data['pp_payflow_pro_timeout'] = (int)$this->request->post['pp_payflow_pro_timeout'];
		} else {
			if ($this->config->get('pp_payflow_pro_timeout')) {
                $this->data['pp_payflow_pro_timeout'] = $this->config->get('pp_payflow_pro_timeout');
			}else {
				$this->data['pp_payflow_pro_timeout'] = 120;//120 seconds default
			}
		}

        //added in 1.5.2j
		if (isset($this->request->post['pp_payflow_pro_timeout_order_status_id'])) {
			$this->data['pp_payflow_pro_timeout_order_status_id'] = $this->request->post['pp_payflow_pro_timeout_order_status_id'];
		} else {
			if ($this->config->get('pp_payflow_pro_timeout_order_status_id')) {
				$this->data['pp_payflow_pro_timeout_order_status_id'] = $this->config->get('pp_payflow_pro_timeout_order_status_id');
			}else {
				$this->data['pp_payflow_pro_timeout_order_status_id'] = '1';//pending status default
			}
		}

		if (isset($this->request->post['pp_payflow_pro_fps_order_status_id'])) {
			$this->data['pp_payflow_pro_fps_order_status_id'] = $this->request->post['pp_payflow_pro_fps_order_status_id'];
		} else {
			if ($this->config->get('pp_payflow_pro_fps_order_status_id')) {
				$this->data['pp_payflow_pro_fps_order_status_id'] = $this->config->get('pp_payflow_pro_fps_order_status_id');
			}else {
				$this->data['pp_payflow_pro_fps_order_status_id'] = '10';
			}
		}

		if (isset($this->request->post['pp_payflow_pro_order_status_id'])) {
			$this->data['pp_payflow_pro_order_status_id'] = $this->request->post['pp_payflow_pro_order_status_id'];
		} else {
			if ($this->config->get('pp_payflow_pro_order_status_id')) {
				$this->data['pp_payflow_pro_order_status_id'] = $this->config->get('pp_payflow_pro_order_status_id');
			}else {
				$this->data['pp_payflow_pro_order_status_id'] = '2';
			}
		}

		$this->load->model('localisation/order_status');

		$this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['pp_payflow_pro_geo_zone_id'])) {
			$this->data['pp_payflow_pro_geo_zone_id'] = $this->request->post['pp_payflow_pro_geo_zone_id'];
		} else {
			$this->data['pp_payflow_pro_geo_zone_id'] = $this->config->get('pp_payflow_pro_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['pp_payflow_pro_total'])) {
			$this->data['pp_payflow_pro_total'] = $this->request->post['pp_payflow_pro_total'];
		} else {
			$this->data['pp_payflow_pro_total'] = $this->config->get('pp_payflow_pro_total');
		}

		if (isset($this->request->post['pp_payflow_pro_status'])) {
			$this->data['pp_payflow_pro_status'] = $this->request->post['pp_payflow_pro_status'];
		} else {
			$this->data['pp_payflow_pro_status'] = $this->config->get('pp_payflow_pro_status');
		}

		if (isset($this->request->post['pp_payflow_pro_sort_order'])) {
			$this->data['pp_payflow_pro_sort_order'] = $this->request->post['pp_payflow_pro_sort_order'];
		} else {
			$this->data['pp_payflow_pro_sort_order'] = $this->config->get('pp_payflow_pro_sort_order');
		}

		$this->template = 'payment/pp_payflow_pro.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		//$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
		$this->response->setOutput($this->render());
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'payment/pp_payflow_pro')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['pp_payflow_pro_partner']) {
			$this->error['partner'] = $this->language->get('error_partner');
		}

		if (!$this->request->post['pp_payflow_pro_vendor']) {
			$this->error['vendor'] = $this->language->get('error_vendor');
		}

		if (!$this->request->post['pp_payflow_pro_username']) {
			$this->error['username'] = $this->language->get('error_username');
		}

		if (!$this->request->post['pp_payflow_pro_password']) {
			$this->error['password'] = $this->language->get('error_password');
		}

        $timeout=(int)$this->request->post['pp_payflow_pro_timeout'];
		if ($timeout<1 || $timeout>450) {
			$this->error['timeout'] = $this->language->get('error_timeout');
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}
}
?>
