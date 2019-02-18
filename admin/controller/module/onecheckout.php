<?php

class ControllerModuleOneCheckOut extends Controller {

	private $error = array(); 

	public function index() {  		
		$this->load->language('total/insurance_fee');
		$this->load->language('total/onecheckout_payment_fee');
		$this->load->language('module/onecheckout');
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if(substr(VERSION,0,5) == '1.5.0'){
				if (isset($this->request->post['onecheckout_fields'])) {
					$this->request->post['onecheckout_fields'] = serialize($this->request->post['onecheckout_fields']);
				}
				if (isset($this->request->post['onecheckout_payment_fee_types'])) {
					$this->request->post['onecheckout_payment_fee_types'] = serialize($this->request->post['onecheckout_payment_fee_types']);
				}
				if (isset($this->request->post['onecheckout_survey_option'])) {
					$this->request->post['onecheckout_survey_option'] = serialize($this->request->post['onecheckout_survey_option']);
				}
			}
			$this->model_setting_setting->editSetting('onecheckout', $this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		
		$this->data['tab_general'] = $this->language->get('tab_general');
		$this->data['tab_insurance'] = $this->language->get('tab_insurance');
		$this->data['tab_paymentfee'] = $this->language->get('tab_paymentfee');
		$this->data['tab_survey'] = $this->language->get('tab_survey');
		$this->data['tab_report'] = $this->language->get('tab_report');

		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_shipping'] = $this->language->get('entry_shipping');
		$this->data['entry_payment'] = $this->language->get('entry_payment');
		$this->data['entry_fields'] = $this->language->get('entry_fields');
		$this->data['entry_survey'] = $this->language->get('entry_survey');	
		$this->data['entry_date_start'] = $this->language->get('entry_date_start');
		$this->data['entry_date_end'] = $this->language->get('entry_date_end');
		$this->data['button_filter'] = $this->language->get('button_filter');
		
		$this->data['field_firstname'] = $this->language->get('field_firstname');
		$this->data['field_lastname'] = $this->language->get('field_lastname');
		$this->data['field_company'] = $this->language->get('field_company');
		$this->data['field_address1'] = $this->language->get('field_address1');
		$this->data['field_address2'] = $this->language->get('field_address2');
		$this->data['field_telephone'] = $this->language->get('field_telephone');
		$this->data['field_fax'] = $this->language->get('field_fax');
		$this->data['field_city'] = $this->language->get('field_city');
		$this->data['field_postcode'] = $this->language->get('field_postcode');
		$this->data['field_country'] = $this->language->get('field_country');
		$this->data['field_zone'] = $this->language->get('field_zone');
		
		$this->data['text_none'] = $this->language->get('text_none');
		$this->data['text_all_zones'] = $this->language->get('text_all_zones');
		$this->data['text_select'] = $this->language->get('text_select');
		$this->data['text_insurance_fee_offer'] = $this->language->get('text_insurance_fee_offer');
		$this->data['text_insurance_fee_opt'] = $this->language->get('text_insurance_fee_opt');
		$this->data['text_insurance_fee_required'] = $this->language->get('text_insurance_fee_required');
		$this->data['text_position_before'] = $this->language->get('text_position_before');
		$this->data['text_position_after'] = $this->language->get('text_position_after');
		$this->data['entry_onecheckout_payment_fee_total'] = $this->language->get('entry_onecheckout_payment_fee_total');
		$this->data['entry_onecheckout_payment_fee_percentage'] = $this->language->get('entry_onecheckout_payment_fee_percentage');
		$this->data['entry_onecheckout_payment_fee_fixed_fee'] = $this->language->get('entry_onecheckout_payment_fee_fixed_fee');
		$this->data['entry_onecheckout_payment_fee_payment_type'] = $this->language->get('entry_onecheckout_payment_fee_payment_type');
		$this->data['entry_onecheckout_payment_fee_geo_zone'] = $this->language->get('entry_onecheckout_payment_fee_geo_zone');		
		$this->data['entry_onecheckout_payment_fee_tax'] = $this->language->get('entry_onecheckout_payment_fee_tax');
		$this->data['entry_onecheckout_payment_fee_status'] = $this->language->get('entry_onecheckout_payment_fee_status');
		$this->data['entry_onecheckout_payment_fee_sort_order'] = $this->language->get('entry_onecheckout_payment_fee_sort_order');
		
		$this->data['entry_insurance_fee_percentage'] = $this->language->get('entry_insurance_fee_percentage');
		$this->data['entry_insurance_fee_fixed_fee'] = $this->language->get('entry_insurance_fee_fixed_fee');
		$this->data['entry_insurance_fee_selection'] = $this->language->get('entry_insurance_fee_selection');
		$this->data['entry_insurance_fee_geo_zone'] = $this->language->get('entry_insurance_fee_geo_zone');		
		$this->data['entry_insurance_fee_status'] = $this->language->get('entry_insurance_fee_status');
		$this->data['entry_insurance_fee_sort_order'] = $this->language->get('entry_insurance_fee_sort_order');
		$this->data['entry_onecheckout_survey_status'] = $this->language->get('entry_onecheckout_survey_status');
		$this->data['entry_onecheckout_survey_position'] = $this->language->get('entry_onecheckout_survey_position');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_add'] = $this->language->get('button_add');		
		
		$this->data['token'] = $this->session->data['token'];
		
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
			'href'      => $this->url->link('module/onecheckout', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['shippings'] = array();
		$this->data['payments'] = array();
		$this->load->model('setting/extension');
		$extensions = $this->model_setting_extension->getInstalled('shipping');		
		foreach ($extensions as $key => $value) {
			if (file_exists(DIR_APPLICATION . 'controller/shipping/' . $value . '.php') && $this->config->get($value . '_status')) {		
				$this->load->language('shipping/' . $value);
				$this->data['shippings'][] = array(
					'code'	 => $value,
					'title'	 => $this->language->get('heading_title'),
				);
			}
		}
		$extensions = $this->model_setting_extension->getInstalled('payment');		
		foreach ($extensions as $key => $value) {
			if (file_exists(DIR_APPLICATION . 'controller/payment/' . $value . '.php') && $this->config->get($value . '_status')) {		
				$this->load->language('payment/' . $value);
				$this->data['payments'][] = array(
					'code'	 => $value,
					'title'	 => $this->language->get('heading_title'),
				);
			}
		}
		
		if (isset($this->request->post['onecheckout_status'])) {
			$this->data['onecheckout_status'] = $this->request->post['onecheckout_status'];
		} else {
			$this->data['onecheckout_status'] = $this->config->get('onecheckout_status');
		}
		
		if (isset($this->request->post['onecheckout_fields'])) {
			$this->data['onecheckout_fields'] = $this->request->post['onecheckout_fields'];
		} elseif($this->config->has('onecheckout_fields')) {
			if(substr(VERSION,0,5) == '1.5.0'){
				$this->data['onecheckout_fields'] = unserialize($this->config->get('onecheckout_fields'));
			} else {
				$this->data['onecheckout_fields'] = $this->config->get('onecheckout_fields');
			}
		} else {
			$this->data['onecheckout_fields'] = array();
		}
		
		if (isset($this->request->post['insurance_fee_percentage'])) {
			$this->data['insurance_fee_percentage'] = $this->request->post['insurance_fee_percentage'];
		} else {
			$this->data['insurance_fee_percentage'] = $this->config->get('insurance_fee_percentage');
		}
		
		if (isset($this->request->post['insurance_fee_fixed_fee'])) {
			$this->data['insurance_fee_fixed_fee'] = $this->request->post['insurance_fee_fixed_fee'];
		} else {
			$this->data['insurance_fee_fixed_fee'] = $this->config->get('insurance_fee_fixed_fee');
		}
		
		if (isset($this->request->post['insurance_fee_selection'])) {
			$this->data['insurance_fee_selection'] = $this->request->post['insurance_fee_selection'];
		} else {
			$this->data['insurance_fee_selection'] = $this->config->get('insurance_fee_selection');
		}
							
		$this->load->model('localisation/geo_zone');
		$this->data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();
		
		if (isset($this->request->post['insurance_fee_geo_zone_id'])) {
			$this->data['insurance_fee_geo_zone_id'] = $this->request->post['insurance_fee_geo_zone_id'];
		} else {
			$this->data['insurance_fee_geo_zone_id'] = $this->config->get('insurance_fee_geo_zone_id');
		}
		
		
		if (isset($this->request->post['insurance_fee_status'])) {
			$this->data['insurance_fee_status'] = $this->request->post['insurance_fee_status'];
		} else {
			$this->data['insurance_fee_status'] = $this->config->get('insurance_fee_status');
		}
		
		if (isset($this->request->post['insurance_fee_sort_order'])) {
			$this->data['insurance_fee_sort_order'] = $this->request->post['insurance_fee_sort_order'];
		} else {
			$this->data['insurance_fee_sort_order'] = $this->config->get('insurance_fee_sort_order');
		}
		
		if (isset($this->request->post['onecheckout_payment_fee_total'])) {
			$this->data['onecheckout_payment_fee_total'] = $this->request->post['onecheckout_payment_fee_total'];
		} else {
			$this->data['onecheckout_payment_fee_total'] = $this->config->get('onecheckout_payment_fee_total');
		}
		
		if (isset($this->request->post['onecheckout_payment_fee_percentage'])) {
			$this->data['onecheckout_payment_fee_percentage'] = $this->request->post['onecheckout_payment_fee_percentage'];
		} else {
			$this->data['onecheckout_payment_fee_percentage'] = $this->config->get('onecheckout_payment_fee_percentage');
		}
		
		if (isset($this->request->post['onecheckout_payment_fee_fixed_fee'])) {
			$this->data['onecheckout_payment_fee_fixed_fee'] = $this->request->post['onecheckout_payment_fee_fixed_fee'];
		} else {
			$this->data['onecheckout_payment_fee_fixed_fee'] = $this->config->get('onecheckout_payment_fee_fixed_fee');
		}
		
		if (isset($this->request->post['onecheckout_payment_fee_types'])) {
			$this->data['onecheckout_payment_fee_types'] = $this->request->post['onecheckout_payment_fee_types'];
		} elseif ($this->config->has('onecheckout_payment_fee_types')) {
			if(substr(VERSION,0,5) == '1.5.0'){
				$this->data['onecheckout_payment_fee_types'] = unserialize($this->config->get('onecheckout_payment_fee_types'));
			} else {
				$this->data['onecheckout_payment_fee_types'] = $this->config->get('onecheckout_payment_fee_types');
			}
		} else { 	
			$this->data['onecheckout_payment_fee_types'] = array();
		}
		
		if (isset($this->request->post['onecheckout_payment_fee_geo_zone_id'])) {
			$this->data['onecheckout_payment_fee_geo_zone_id'] = $this->request->post['onecheckout_payment_fee_geo_zone_id'];
		} else {
			$this->data['onecheckout_payment_fee_geo_zone_id'] = $this->config->get('onecheckout_payment_fee_geo_zone_id');
		}
		
		$this->load->model('localisation/tax_class');
		$this->data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
		
		if (isset($this->request->post['onecheckout_payment_fee_tax_class_id'])) {
			$this->data['onecheckout_payment_fee_tax_class_id'] = $this->request->post['onecheckout_payment_fee_tax_class_id'];
		} else {
			$this->data['onecheckout_payment_fee_tax_class_id'] = $this->config->get('onecheckout_payment_fee_tax_class_id');
		}
		
		if (isset($this->request->post['onecheckout_payment_fee_status'])) {
			$this->data['onecheckout_payment_fee_status'] = $this->request->post['onecheckout_payment_fee_status'];
		} else {
			$this->data['onecheckout_payment_fee_status'] = $this->config->get('onecheckout_payment_fee_status');
		}
		
		if (isset($this->request->post['onecheckout_payment_fee_sort_order'])) {
			$this->data['onecheckout_payment_fee_sort_order'] = $this->request->post['onecheckout_payment_fee_sort_order'];
		} else {
			$this->data['onecheckout_payment_fee_sort_order'] = $this->config->get('onecheckout_payment_fee_sort_order');
		}
		
		$this->load->model('setting/extension');
		$payment_types = $this->model_setting_extension->getInstalled('payment');

		foreach ($payment_types as $key => $value) {
			$this->load->language('payment/' . $value);
				$this->data['payment_types'][] = array(
				'name'       => $this->language->get('heading_title'),
				'paymentkey' => $value
				);
		}
		
		if (isset($this->request->post['onecheckout_survey_status'])) {
			$this->data['onecheckout_survey_status'] = $this->request->post['onecheckout_survey_status'];
		} else {
			$this->data['onecheckout_survey_status'] = $this->config->get('onecheckout_survey_status');
		}
		
		if (isset($this->request->post['onecheckout_survey_position'])) {
			$this->data['onecheckout_survey_position'] = $this->request->post['onecheckout_survey_position'];
		} else {
			$this->data['onecheckout_survey_position'] = $this->config->get('onecheckout_survey_position');
		}
		
		if (isset($this->request->post['onecheckout_survey_option'])) {
			$this->data['survey_options'] = $this->request->post['onecheckout_survey_option'];
		} elseif($this->config->has('onecheckout_survey_option')) {
			if(substr(VERSION,0,5) == '1.5.0'){
				$this->data['survey_options'] = unserialize($this->config->get('onecheckout_survey_option'));
			} else {
				$this->data['survey_options'] = $this->config->get('onecheckout_survey_option');
			}
		} else {
			$this->data['survey_options'] = array();
		}
		$this->data['filter_date_start'] = date('Y-m-d', strtotime(date('Y-m-d')) - (7 * 86400));
		$this->data['filter_date_end'] = date('Y-m-d');

		$this->data['action'] = $this->url->link('module/onecheckout', 'token=' . $this->session->data['token'], 'SSL');
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		$this->template = 'module/onecheckout.tpl';

		$this->children = array(
			'common/header',
			'common/footer',
		);	
		$this->response->setOutput($this->render());

	}
	
	private function validate() {

		if (!$this->user->hasPermission('modify', 'module/onecheckout')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
	
	public function chart() {
		$this->load->language('module/onecheckout');	
		$data = array();
				
		$data['xaxis'] = array();
		$data['order']['label'] = $this->language->get('entry_laber');
		$tmpop = array();
		
		if($this->config->has('onecheckout_survey_option')) {
			if(substr(VERSION,0,5) == '1.5.0'){
				$survey_options = unserialize($this->config->get('onecheckout_survey_option'));
			} else {
				$survey_options = $this->config->get('onecheckout_survey_option');
			}
		} else {
			$survey_options = array();
		}
			
		
		$filter_date_end = $this->request->get['enddate'];
		$filter_date_start = $this->request->get['startdate'];
		$i=1;
		
		$query = $this->db->query("SELECT COUNT(*) AS total, SUM(total) AS ordertotal, os.option_value FROM `" . DB_PREFIX . "order` o INNER JOIN `" . DB_PREFIX . "order_survey` os ON o.order_id=os.order_id WHERE o.order_status_id > '0' AND (DATE(o.date_added) >= '" . $this->db->escape($filter_date_start) . "') AND (DATE(o.date_added) <= '" . $this->db->escape($filter_date_end) . "') GROUP BY os.option_value ORDER BY total desc");
					
		foreach ($query->rows as $row) {
			$data['order']['data'][] = array($i, (int)$row['total']);
			$tmpop[] = $row['option_value'];				
			$data['xaxis'][] = array($i, $row['option_value'].'<br />'.$this->currency->format($row['ordertotal']));
			$i++;
		}
		
		foreach($survey_options as $survey_option){
			if(!in_array($survey_option,$tmpop)){
				$data['order']['data'][] = array($i, 0);			
				$data['xaxis'][] = array($i, $survey_option.'<br />'.$this->currency->format(0));
				$i++;
			}
		}
		$data['count'] = ($i-1)*100;
					
		if (function_exists('json_encode')){
			$this->response->setOutput(json_encode($data));
		} else {
			$this->load->library('json');
			$this->response->setOutput(Json::encode($data));
		}
	}
	
	private function writefile($filename, $pattern, $replace, $repeat) {
		
		$real_filename = realpath($filename);		
		$content = $original_content = file_get_contents($real_filename);

		if($content === FALSE) {
			$this->error['warning'] = sprintf($this->language->get('error_notopen'),$filename);
			return;
		}
		
		if($repeat){
			$content = str_replace('// OneCheckOut' , '', $content);//preg_replace
		
			if($content !== $original_content){
				return;
			}
		}
			
		$content = str_replace($pattern, $replace, $content);//preg_replace
				
		if($content === NULL) {
			$this->error['warning'] = sprintf($this->language->get('error_regex'),$pattern);
			return;
		}
				
		if($content !== $original_content) {
				
			if(!is_writeable($real_filename)) {
				$this->error['warning'] = sprintf($this->language->get('error_notwrite'),$filename);
				return;
			} else {					
				$result = file_put_contents($real_filename, $content);
				if($result) {
					return;
				} else {
					$this->error['warning'] = sprintf($this->language->get('error_writefail'),$filename);
					return;
				}
			}
		}
	}
	
	public function getlayoutid(){
		$this->load->model('design/layout');
		$layouts = $this->model_design_layout->getLayouts();
		foreach($layouts as $layout){
			if($layout['name']=='OneCheckOut'){
				return $layout['layout_id'];
			}
		}
		
		return false;
	}
	
	public function install(){
		$this->load->model('setting/extension');		
		$this->model_setting_extension->install('total', 'insurance_fee');
		$this->model_setting_extension->install('total', 'onecheckout_payment_fee');

		$this->load->model('user/user_group');		
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'total/insurance_fee');
		$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'total/insurance_fee');	
		$this->model_user_user_group->addPermission($this->user->getId(), 'access', 'total/onecheckout_payment_fee');
		$this->model_user_user_group->addPermission($this->user->getId(), 'modify', 'total/onecheckout_payment_fee');		
				
		$layoutid = $this->getlayoutid();
		if(!$layoutid){
			$layoutdata = array();
			$layoutdata['name'] = 'OneCheckOut';
			$layoutdata['layout_route'][1] = array(
				'store_id'	=> '0',
				'route'		=> 'onecheckout/'
			);
			$this->load->model('design/layout');
			$this->model_design_layout->addLayout($layoutdata);
		}
		
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "order_survey` (`order_id` int(11) NOT NULL AUTO_INCREMENT,`option_value` varchar(100) COLLATE utf8_bin NOT NULL,PRIMARY KEY (`order_id`) ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;");
		
		$this->writefile('../index.php', '// SEO URL\'s', '// OneCheckOut
$controller->addPreAction(new Action(\'onecheckout/checkout/ini\'));

// SEO URL\'s', 1);
		$this->writefile('../catalog/controller/account/logout.php', 'unset($this->session->data[\'coupon\']);', 'unset($this->session->data[\'coupon\']);
			// OneCheckOut fix tax.php bug
			unset($this->session->data[\'guest\']);', 1);
	}
	
	public function uninstall(){
		$this->load->model('setting/extension');
		$this->load->model('setting/setting');
					
		$this->model_setting_extension->uninstall('total', 'insurance_fee');		
		$this->model_setting_setting->deleteSetting('insurance_fee');
		
		$this->model_setting_extension->uninstall('total', 'onecheckout_payment_fee');		
		$this->model_setting_setting->deleteSetting('onecheckout_payment_fee');
		
		$layoutid = $this->getlayoutid();
		if($layoutid){
			$this->load->model('design/layout');
			$this->model_design_layout->deleteLayout($layoutid);
		}
		
		$this->writefile('../index.php', '// OneCheckOut
$controller->addPreAction(new Action(\'onecheckout/checkout/ini\'));

// SEO URL\'s', '// SEO URL\'s', 0);
	}

}

?>