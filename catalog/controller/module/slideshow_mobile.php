<?php
class ControllerModuleSlideshowMobile extends Controller {
	protected function index($setting) {
		static $module = 0;

		$this->load->model('design/banner');
		$this->load->model('tool/image');

		//$this->document->addScript('catalog/view/javascript/jquery/nivo-slider/jquery.nivo.slider.pack.js');
		//$this->document->addStyle('catalog/view/theme/' . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/stylesheet/slideshow.css');

		$this->data['width'] = $setting['width'];
		$this->data['height'] = $setting['height'];

		$this->data['banners'] = array();

		$results = $this->model_design_banner->getBanner($setting['banner_id']);

		if (!$this->config->get("config_mobile_disable_omf")) {
			$resolution = getResolution();

			//echo "<h1>Screen width: ". $resolution . '\\n';

			foreach ($results as $result) {
				if (file_exists(DIR_IMAGE . $result['image'])) {				
					$this->data['banners'][] = array(
						'title' => $result['title'],
						'link'  => $result['link'],
						'image' => $this->model_tool_image->resize($result['image'], $resolution, $resolution, 'r')
					);
				}
			}

			$this->data['module'] = $module++;

			if ($this->isVisitorMobile()) {
				if (file_exists(DIR_TEMPLATE . $this->config->get('config_mobile_theme') . '/template/module/slideshow_mobile.tpl')) {
					$this->template = $this->config->get('config_mobile_theme') . '/template/module/slideshow_mobile.tpl';
				} else {
					$this->template = 'omf2/template/module/slideshow_mobile.tpl';
				}

				$this->render();
			}

		}
	}
}
?>