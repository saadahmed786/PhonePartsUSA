<?php
class ControllerModuleSuccessPage extends Controller {
	private $error = array();
	private $default_design = array(
		1 => array(
			'name'    => 'Lazure',
			'setting' => 'PGRpdiBzdHlsZT0icG9zaXRpb246IHJlbGF0aXZlOyI+CjxoMT48c3BhbiBzdHlsZT0iY29sb3I6IzY5Njk2OTsiPjxzdHJvbmc+WW91ciBvcmRlciBoYXMgYmVlbiBQcm9jY2Vzc2VkITwvc3Ryb25nPjwvc3Bhbj48L2gxPgoKPGRpdiBzdHlsZT0icG9zaXRpb246IGFic29sdXRlOyB0b3A6IDJweDsgcmlnaHQ6IDVweDsiPjxhIGhyZWY9IiI+PGltZyBhbHQ9Ikdvb2dsZSBwbHVzIiBzcmM9Ii4vaW1hZ2Uvc3VjY2Vzc19wYWdlL2dvb2dsZV9wbHVzLnBuZyIgdGl0bGU9Ikdvb2dsZSBwbHVzIiAvPjwvYT4gPGEgaHJlZj0iIj48aW1nIGFsdD0iRmxpY2tyIiBzcmM9Ii4vaW1hZ2Uvc3VjY2Vzc19wYWdlL2ZsaWNrci5wbmciIHRpdGxlPSJGbGlja3IiIC8+PC9hPiA8YSBocmVmPSIiPjxpbWcgYWx0PSJMaW5rZWRJbiIgc3JjPSIuL2ltYWdlL3N1Y2Nlc3NfcGFnZS9pbi5wbmciIHRpdGxlPSJMaW5rZWRJbiIgLz48L2E+PC9kaXY+CjwvZGl2PgoKPGRpdiBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgIzdBQzM2NDsgcGFkZGluZzogMTBweDsgLW1vei1ib3JkZXItcmFkaXVzOjVweDsgLXdlYmtpdC1ib3JkZXItcmFkaXVzOjVweDsgLWtodG1sLWJvcmRlci1yYWRpdXM6NXB4OyBib3JkZXItcmFkaXVzOjVweDsgYm94LXNoYWRvdzogM3B4IDNweCAzcHggIzk5OTsiPgo8dGFibGUgYm9yZGVyPSIwIiBjZWxscGFkZGluZz0iMCIgY2VsbHNwYWNpbmc9IjAiIHN0eWxlPSJ3aWR0aDoxMDAlOyI+Cgk8dGJvZHk+CgkJPHRyPgoJCQk8dGQgc3R5bGU9ImJvcmRlci1yaWdodDogMXB4IGRvdHRlZCAjODg4OyB2ZXJ0aWNhbC1hbGlnbjogdG9wOyI+PHNwYW4gc3R5bGU9ImNvbG9yOiM2M2I3MzU7IGZvbnQtc2l6ZToyMXB4OyI+WW91ciBvcmRlciBOdW1iZXIgaXM6IDxzdHJvbmc+e29yZGVyX2lkfTwvc3Ryb25nPjwvc3Bhbj4KCgkJCTxkaXYgc3R5bGU9InBhZGRpbmc6IDE1cHggMCA1cHggMDsgZm9udC13ZWlnaHQ6IGJvbGQ7Ij5Zb3UnbGwgcmVjZWl2ZSBhbiBlbWFpbCBjb25maXJtYXRpb24gc2hvcnRseSB0bzogPHNwYW4gc3R5bGU9ImNvbG9yOiAjMmQ1ZWUzOyI+e2VtYWlsfTwvc3Bhbj48L2Rpdj4KCgkJCTxkaXYgc3R5bGU9InBhZGRpbmc6IDEwcHggMCA1cHggMDsgY29sb3I6ICM2NjY7IGZvbnQtd2VpZ2h0OiBib2xkOyI+PGEgaHJlZj0iIj5XYXJyYW50eTwvYT4gfCA8YSBocmVmPSIiPlJldHVybnM8L2E+IHwgPGEgaHJlZj0iIj5Qcml2YWN5IFBvbGljeTwvYT4gfCA8YSBocmVmPSIiPkFjY291bnQ8L2E+IHwgPGEgaHJlZj0iIj5Db250YWN0PC9hPjwvZGl2PgoJCQk8c3BhbiBzdHlsZT0iY29sb3I6ICM5OTk7IGZvbnQtc2l6ZToxNHB4OyI+UGxlYXNlIGRpcmVjdCBhbnkgcXVlc3Rpb25zIHlvdSBoYXZlIHRvIHRoZSBzdG9yZSBvd25lci48L3NwYW4+PC90ZD4KCQkJPHRkIHN0eWxlPSJ2ZXJ0aWNhbC1hbGlnbjogdG9wOyBwYWRkaW5nLWxlZnQ6IDEwcHg7Ij48c3BhbiBzdHlsZT0iY29sb3I6IzgwODE4MjsgZm9udC1zaXplOjE2cHg7IGZvbnQtd2VpZ2h0OiA1MDAiPkRlbGl2ZXJ5IGFkZHJlc3M8L3NwYW4+PGJyIC8+CgkJCTxiciAvPgoJCQk8c3BhbiBzdHlsZT0iY29sb3I6IzgwODE4MjsiPntkZWxpdmVyeV9hZGRyZXNzfTwvc3Bhbj48L3RkPgoJCTwvdHI+Cgk8L3Rib2R5Pgo8L3RhYmxlPgo8L2Rpdj4KCjxkaXYgc3R5bGU9Im1hcmdpbjogMjBweCAwIDVweCAwOyBmb250LXNpemU6IDE0cHg7IGNvbG9yOiAjNTU1OyI+Rm9sbG93IFVzIG9uIEZhY2Vib29rPC9kaXY+Cgo8ZGl2IHN0eWxlPSJiYWNrZ3JvdW5kLWNvbG9yOiAjZjRmNGY0OyI+e2ZhY2Vib29rfTwvZGl2PgoKPHA+Jm5ic3A7PC9wPg=='
		), array(
			'name'    => 'Paciato',
			'setting' => 'PGgxPjxzcGFuIHN0eWxlPSJjb2xvcjojZGExYzVjOyI+PHN0cm9uZz5Zb3VyIG9yZGVyIGhhcyBiZWVuIFByb2NjZXNzZWQhPC9zdHJvbmc+PC9zcGFuPjwvaDE+Cgo8dGFibGUgYm9yZGVyPSIwIiBjZWxscGFkZGluZz0iMCIgY2VsbHNwYWNpbmc9IjAiIHN0eWxlPSJ3aWR0aDoxMDAlOyI+Cgk8dGJvZHk+CgkJPHRyPgoJCQk8dGQgc3R5bGU9InZlcnRpY2FsLWFsaWduOiB0b3A7Ij4KCQkJPGRpdiBzdHlsZT0iYm9yZGVyOiAxcHggc29saWQgI2RkZDsgYmFja2dyb3VuZC1jb2xvcjogI2VlZTsgcGFkZGluZzogMTBweDsgLW1vei1ib3JkZXItcmFkaXVzOjVweDsgLXdlYmtpdC1ib3JkZXItcmFkaXVzOjVweDsgLWtodG1sLWJvcmRlci1yYWRpdXM6NXB4OyBib3JkZXItcmFkaXVzOjVweDsgYm94LXNoYWRvdzogM3B4IDNweCAzcHggIzk5OTsgY29sb3I6IzJiMmIyYjsiPjxzcGFuIHN0eWxlPSJmb250LXNpemU6MjFweDsiPllvdXIgb3JkZXIgTnVtYmVyIGlzOiA8c3Ryb25nPjxzcGFuIHN0eWxlPSJjb2xvcjogIzFjOTliNTsiPntvcmRlcl9pZH08L3NwYW4+PC9zdHJvbmc+PC9zcGFuPgoKCQkJPGRpdiBzdHlsZT0icGFkZGluZzogMTVweCAwIDFweCAwOyBmb250LXdlaWdodDogYm9sZDsiPllvdSdsbCByZWNlaXZlIGFuIGVtYWlsIGNvbmZpcm1hdGlvbiBzaG9ydGx5IHRvOiA8c3BhbiBzdHlsZT0iY29sb3I6ICNkYTFjNWM7Ij57ZW1haWx9PC9zcGFuPjwvZGl2PgoKCQkJPGRpdiBzdHlsZT0icGFkZGluZzogMHB4IDAgNXB4IDA7IGZvbnQtd2VpZ2h0OiBib2xkOyI+U2hpcHBpbmcgbWV0aG9kOiA8c3BhbiBzdHlsZT0iY29sb3I6ICNkYTFjNWM7Ij57c2hpcHBpbmdfbWV0aG9kfTwvc3Bhbj48L2Rpdj4KCQkJPHNwYW4gc3R5bGU9ImNvbG9yOiAjOTk5OyBmb250LXNpemU6MTRweDsiPlBsZWFzZSBkaXJlY3QgYW55IHF1ZXN0aW9ucyB5b3UgaGF2ZSB0byB0aGUgc3RvcmUgb3duZXIuPC9zcGFuPjxiciAvPgoJCQk8YnIgLz4KCQkJPGJyIC8+CgkJCTx1PkRlbGl2ZXJ5IGFkZHJlc3M8L3U+PGJyIC8+CgkJCXtkZWxpdmVyeV9hZGRyZXNzfTwvZGl2PgoJCQk8L3RkPgoJCQk8dGQgc3R5bGU9InZlcnRpY2FsLWFsaWduOiB0b3A7IHBhZGRpbmctbGVmdDogMTBweDsgd2lkdGg6IDMwJTsiPjxzcGFuIHN0eWxlPSJjb2xvcjojNjNiNzM1OyBmb250LXNpemU6MTZweDsgZm9udC13ZWlnaHQ6IDUwMCI+Rm9sbG93IFVzIG9uIEZhY2Vib29rPC9zcGFuPjxiciAvPgoJCQl7ZmFjZWJvb2t9PC90ZD4KCQk8L3RyPgoJPC90Ym9keT4KPC90YWJsZT4KCjxwPiZuYnNwOzwvcD4='
		)
	);

	public function index() {
		$this->language->load('module/success_page');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			foreach (array_keys($this->request->post['success_page']) as $store_id) {
				$this->model_setting_setting->editSetting('success_page', $this->request->post['success_page'][$store_id], $store_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['heading_normal'] = $this->language->get('heading_normal');
		$this->data['heading_redirect'] = $this->language->get('heading_redirect');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_default'] = $this->language->get('text_default');
		$this->data['text_yes'] = $this->language->get('text_yes');
		$this->data['text_no'] = $this->language->get('text_no');
		$this->data['text_none'] = $this->language->get('text_none');
		$this->data['text_shortcode'] = $this->language->get('text_shortcode');

		$this->data['entry_redirect'] = $this->language->get('entry_redirect');
		$this->data['entry_facebook'] = $this->language->get('entry_facebook');
		$this->data['entry_dimension'] = $this->language->get('entry_dimension');
		$this->data['entry_facebook_profile'] = $this->language->get('entry_facebook_profile');
		$this->data['entry_facebook_border'] = $this->language->get('entry_facebook_border');
		$this->data['entry_layout'] = $this->language->get('entry_layout');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_to_default'] = $this->language->get('entry_to_default');
		$this->data['entry_body'] = $this->language->get('entry_body');
		$this->data['entry_logged'] = $this->language->get('entry_logged');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
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
			'href'      => $this->url->link('module/success_page', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);

		$this->data['token'] = $this->session->data['token'];

		$this->data['action'] = $this->url->link('module/success_page', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['module'] = array();

		if (isset($this->request->post['success_page'])) {
			$this->data['module'] = $this->request->post['success_page'];
		} else {
			$data = array();

			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "setting WHERE `group` = 'success_page'");

			foreach ($query->rows as $result) {
				if (!$result['serialized']) {
					$data[$result['store_id']][$result['key']] = $result['value'];
				} else {
					$data[$result['store_id']][$result['key']] = unserialize($result['value']);
				}
			}

			$this->data['module'] = $data;
		}

		$this->data['stores'] = array();

		$this->data['stores'][] = array('store_id' => '0', 'name' => $this->language->get('text_default'));

		$this->load->model('setting/store');

		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$this->data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}

		$this->load->model('localisation/language');

		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		$this->data['layouts'] = $this->default_design;

		$this->template = 'module/success_page.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	public function loadtemplate() {
		if (isset($this->request->get['id']) && isset($this->default_design[$this->request->get['id']])) {
			echo base64_decode($this->default_design[$this->request->get['id']]['setting']);
			exit();
		}

		echo'';
		exit();
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'module/success_page')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
}
?>