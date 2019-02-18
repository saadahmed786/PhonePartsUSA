<?php
class ControllerModuleBossSlideshow extends Controller {
	private $error = array(); 
	
	public function index() {   
		$this->load->language('module/boss_slideshow');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
				
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
		
			$this->model_setting_setting->editSetting('boss_slideshow', $this->request->post);		
			
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}
				
		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_header_bottom'] = $this->language->get('text_header_bottom');
		$this->data['text_content_top'] = $this->language->get('text_content_top');
		$this->data['text_content_bottom'] = $this->language->get('text_content_bottom');		
		$this->data['text_browse'] = $this->language->get('text_browse');
		$this->data['text_clear'] = $this->language->get('text_clear');			
		$this->data['text_image_manager'] = $this->language->get('text_image_manager');
		
		$this->data['entry_image'] = $this->language->get('entry_image');
		$this->data['entry_layout'] = $this->language->get('entry_layout');
		$this->data['entry_position'] = $this->language->get('entry_position');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_sort_order'] = $this->language->get('entry_sort_order');
		
		$this->data['nivo_caption'] = $this->language->get('nivo_caption');
		$this->data['nivo_URL'] = $this->language->get('nivo_URL');
		$this->data['nivo_image'] = $this->language->get('nivo_image');
		
		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');
		$this->data['button_add_module'] = $this->language->get('button_add_module');
		$this->data['button_remove'] = $this->language->get('button_remove');
		$this->data['button_add_image'] = $this->language->get('button_add_image');

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
			'href'      => $this->url->link('module/boss_slideshow', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['action'] = $this->url->link('module/boss_slideshow', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		$this->data['token'] = $this->session->data['token'];
		
		// image
		$this->load->model('tool/image');
		
		$image_sliders = array();
		
		if (isset($this->request->post['image_sliders'])) {
			$image_sliders = $this->request->post['image_sliders'];
		} elseif ($this->config->get('image_sliders')) { 
			$image_sliders = $this->config->get('image_sliders');
		}	
		
		$this->data['images'] = array();
		
		foreach ($image_sliders as $image_slider) {
			if ($image_slider['image'] && file_exists(DIR_IMAGE . $image_slider['image'])) {
				$image = $image_slider['image'];
			} else {
				$image = 'no_image.jpg';
			}			
			
			$this->data['images'][] = array(
				'description'	 			=> $image_slider['description'],
				'link'                     => $image_slider['link'],
				'image'                    => $image,
				'thumb'                    => $this->model_tool_image->resize($image, 100, 100),
				'sort'					=>$image_slider['sort']
			);	
		} 
		
		$this->data['no_image'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);		

		//module		
		$this->data['modules'] = array();
		
		if (isset($this->request->post['boss_slideshow_module'])) {
			$this->data['modules'] = $this->request->post['boss_slideshow_module'];
		} elseif ($this->config->get('boss_slideshow_module')) { 
			$this->data['modules'] = $this->config->get('boss_slideshow_module');
		}					
		$this->load->model('design/layout');
		
		$this->data['layouts'] = $this->model_design_layout->getLayouts();
		
		$this->load->model('localisation/language');
		$this->data['languages'] = $this->model_localisation_language->getLanguages();
		
		$this->template = 'module/boss_slideshow.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);		
		$this->response->setOutput($this->render());
	}
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/boss_slideshow')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (isset($this->request->post['boss_slideshow_module'])) {
			foreach ($this->request->post['boss_slideshow_module'] as $key => $value) {
				if (!$value['image_width'] || !$value['image_height']) {
					$this->error['image'][$key] = $this->language->get('error_image');
				}
			}
		}
				
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
	private function getIdLayout($layout_name) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "layout WHERE LOWER(name) = LOWER('".$layout_name."')");
		return (int)$query->row['layout_id'];
	}
	//boss_slideshow
	public function install() 
	{
		$this->load->model('setting/setting');
			
		$this->load->model('localisation/language');
			
		$languages = $this->model_localisation_language->getLanguages();
		
		$array_description0 = array();
		$array_description1 = array();
		$array_description2 = array();
		
		foreach($languages as $language){
			$array_description0{$language['language_id']} = '';
			$array_description1{$language['language_id']} = '';
			$array_description2{$language['language_id']} = '';
		}
		
		$boss_slideshow = array('image_sliders' => array ( 
			0 => array ('description' => $array_description0,'link' => '#', 'image' => 'data/'.$this->config->get('config_template').'/slide01.jpg'),
			1 => array ('description' => $array_description1,'link' => '#', 'image' => 'data/'.$this->config->get('config_template').'/slide02.jpg'),
			2 => array ('description' => $array_description2,'link' => '#', 'image' => 'data/'.$this->config->get('config_template').'/slide03.jpg'),
		),'boss_slideshow_module' => array ( 
			0 => array ('image_width' => 533, 'image_height' => 277, 'layout_id' => $this->getIdLayout("home"), 'position' => 'header_bottom', 'status' => 1, 'sort_order' => 1))
		);
		
		$this->model_setting_setting->editSetting('boss_slideshow', $boss_slideshow);		
	}
}
?>