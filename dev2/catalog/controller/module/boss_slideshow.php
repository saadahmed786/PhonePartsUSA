<?php
class ControllerModuleBossSlideshow extends Controller {
	protected function index($setting) {
		if ((($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) == 'ppu2') {
			$setting['image_width'] = '1266';
			$setting['image_height'] = '446';
		}
		static $module = 0;
		/*
		$this->document->addScript('catalog/view/javascript/bossthemes/jquery.easing.js');
		$this->document->addScript('catalog/view/javascript/bossthemes/jquery.flexslider.js');

		if (file_exists('catalog/view/theme/' . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/stylesheet/boss_slideshow.css')) {
			$this->document->addStyle('catalog/view/theme/' . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/stylesheet/boss_slideshow.css');
		} else {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/boss_slideshow.css');
		}
		*/
		$this->data['width'] = $setting['image_width'];
		$this->data['height'] = $setting['image_height'];
		
		// image		
		$this->load->model('tool/image');

		$this->data['images'] = array();

		$image_sliders = array();
		
		$image_sliders = $this->config->get('image_sliders');
		$sort_order = array();
foreach($image_sliders as $key => $sort_slider)
{
	
	$sort_order[$key] = $sort_slider['sort'];
}
array_multisort($sort_order, SORT_ASC, $image_sliders);
		foreach ($image_sliders as $image_slider) {		
			if ($image_slider['image'] && file_exists(DIR_IMAGE . $image_slider['image'])) {
				$image = $image_slider['image'];
				$this->data['images'][] = array(
					'description'	 => html_entity_decode($image_slider['description'][$this->config->get('config_language_id')], ENT_QUOTES, 'UTF-8'),
					'link'           => $image_slider['link'],			
					'image' 		 => $this->model_tool_image->resize($image, $setting['image_width'], $setting['image_height'])
				);	
			}
		} 
		
		$this->data['module'] = $module++;
				
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/boss_slideshow.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/boss_slideshow.tpl';
		} else {
			$this->template = 'default/template/module/boss_slideshow.tpl';
		}

		$this->render();
	}
}
?>