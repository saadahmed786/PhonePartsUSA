<?php  
class ControllerModuleClickdesk extends Controller {
	protected function index($setting) {
		$this->language->load('module/clickdesk');
		
		$widgetid = $this->config->get('clickdesk_module');
 
 

		if(isset($widgetid[0]["clickdeskiddgetid"]))
		{
			$widgetid = 	$widgetid[0]["clickdeskiddgetid"];
		}else
		{
			$widgetid = "";
		}
 
		$this->data["widgetid"] = $widgetid;
		if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/clickdesk.tpl')) {
			$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/module/clickdesk.tpl';
		} else {
			$this->template = 'default/template/module/clickdesk.tpl';
		}
		
		$this->render();
	}
}
?>