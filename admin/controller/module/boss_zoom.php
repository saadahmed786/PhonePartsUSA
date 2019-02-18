<?php
class ControllerModuleBossZoom extends Controller {
	private $error = array(); 
	
	public function index() {   
		$this->load->language('module/boss_zoom');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {			
			$this->model_setting_setting->editSetting('boss_zoom', $this->request->post);		
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}
				
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_enable'] = $this->language->get('text_enable');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_disable'] = $this->language->get('text_disable');
		$this->data['text_enabled_disabled'] = $this->language->get('text_enabled_disabled');
		$this->data['text_information'] = $this->language->get('text_information');
		$this->data['text_image_Thumb'] = $this->language->get('text_image_Thumb');
		$this->data['text_image_Addition'] = $this->language->get('text_image_Addition');
		$this->data['text_image_Zoom'] = $this->language->get('text_image_Zoom');
		$this->data['text_area_Zoom'] = $this->language->get('text_area_Zoom');
		$this->data['text_auto_size_area'] = $this->language->get('text_auto_size_area');
		$this->data['text_area_position'] = $this->language->get('text_area_position');
		$this->data['text_distance'] = $this->language->get('text_distance');
		$this->data['text_Inner'] = $this->language->get('text_Inner');
		$this->data['text_AdjustX'] = $this->language->get('text_AdjustX');
		$this->data['text_AdjustY'] = $this->language->get('text_AdjustY');
		$this->data['text_title'] = $this->language->get('text_title');
		$this->data['text_title_opacity'] = $this->language->get('text_title_opacity');
		$this->data['text_Tint'] = $this->language->get('text_Tint');
		$this->data['text_Tint_opacity'] = $this->language->get('text_Tint_opacity');
		$this->data['text_softFocus'] = $this->language->get('text_softFocus');
		$this->data['text_Opacity_lens'] = $this->language->get('text_Opacity_lens');
		$this->data['text_Smooth'] = $this->language->get('text_Smooth');
	
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		
 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		if (isset($this->error['error_thumb_image'])) {
			$this->data['error_thumb_image'] = $this->error['error_thumb_image'];
		} else {
			$this->data['error_thumb_image'] = '';
		}
		if (isset($this->error['error_addition_image'])) {
			$this->data['error_addition_image'] = $this->error['error_addition_image'];
		} else {
			$this->data['error_addition_image'] = '';
		}
		if (isset($this->error['error_zoom_image'])) {
			$this->data['error_zoom_image'] = $this->error['error_zoom_image'];
		} else {
			$this->data['error_zoom_image'] = '';
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
			'href'      => $this->url->link('module/boss_zoom', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/boss_zoom', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['token'] = $this->session->data['token'];
				
		$this->data['modules'] = array();
		
		if (isset($this->request->post['boss_zoom_module'])) {
			$this->data['modules'] = $this->request->post['boss_zoom_module'];
		} elseif ($this->config->get('boss_zoom_module')) { 
			$this->data['modules'] = $this->config->get('boss_zoom_module');
		}		
	
		$this->template = 'module/boss_zoom.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/boss_zoom')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		if (isset($this->request->post['boss_zoom_module'])) {
			$value = array();
			$value = $this->request->post['boss_zoom_module'];
			if (!$value[0]['thumb_image_width'] || !$value[0]['thumb_image_heigth']) {
				$this->error['error_thumb_image'] = $this->language->get('error_image');
			}
			if (!$value[0]['addition_image_width'] || !$value[0]['addition_image_heigth']) {
				$this->error['error_addition_image'] = $this->language->get('error_image');
			}
			if (!$value[0]['zoom_image_width'] || !$value[0]['zoom_image_heigth']) {
				$this->error['error_zoom_image'] = $this->language->get('error_image');
			}
		}
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
	
//boss_zoom
	private function getIdLayout($layout_name) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "layout WHERE LOWER(name) = LOWER('".$layout_name."')");
		return (int)$query->row['layout_id'];
	}
	
	public function install() 
	{
		$this->load->model('setting/setting');
		
		$boss_zoom = array('boss_zoom_module' => array ( 
		0 => array ( 'layout_id' => $this->getIdLayout("product"), 'position' => 'content_bottom', 'status' => 1, 'sort_order' => 1, 'thumb_image_width' => 276,'thumb_image_heigth' => 287,'addition_image_width' => 65,'addition_image_heigth' => 72,'zoom_image_width' => 500,'zoom_image_heigth' => 500,'zoom_area_width' => 276,'zoom_area_heigth' => 287,'position_zoom_area' => 'right','adjustX' => 30,'adjustY' => 0,'title_image' => 'true', 'title_opacity' => 0.5, 'tint' => '#FFFFFF', 'tintOpacity' => 0.5, 'softfocus' => 'false', 'lensOpacity' => 0.7, 'smoothMove' => 3)
		));
		
		$this->model_setting_setting->editSetting('boss_zoom', $boss_zoom);		
	}
	
}
?>