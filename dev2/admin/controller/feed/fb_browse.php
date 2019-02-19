<?php 
class ControllerFeedfbbrowse  extends Controller {
	public function install(){
		$key =  $this->config->get('BSS_PLUGIN_KEY');
		if (!isset($key) ||  $key='') {
			$key = $this->genRandomString();
			$value =array("BSS_PLUGIN_KEY" => $key);
			$this->load->model('setting/setting');
			$this->model_setting_setting->editSetting('fb_browse',$value);	
		}
	} 

	public function index() {
		$this->load->language('feed/fb_browse');

		//$this->document->title = $this->language->get('heading_title');
		
		$this->load->model('setting/setting');
			
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
			$this->load->model('setting/setting');
			
			$this->model_setting_setting->editSetting('fb_browse', $this->request->post);				
			
			$this->session->data['success'] = $this->language->get('text_success');

			$this->redirect(HTTPS_SERVER . 'index.php?route=extension/feed&token=' . $this->session->data['token']);
		}

		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['text_success'] = $this->language->get('text_success');
	
		$this->data['text_enabled'] = $this->language->get('text_enabled');
		$this->data['text_disabled'] = $this->language->get('text_disabled');
		$this->data['text_setting_section'] = $this->language->get('text_setting_section');
		$this->data['text_preview_section'] = $this->language->get('text_preview_section');
		$this->data['text_preview_link'] = $this->language->get('text_preview_link');
		
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['field_pluginkey'] = $this->language->get('field_pluginkey');
		$this->data['field_pluginkey_description'] = $this->language->get('field_pluginkey_description');
		$this->data['copyright'] = $this->language->get('copyright');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		$this->data['tab_general'] = $this->language->get('tab_general');

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
  		$this->document->breadcrumbs = array();

   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=common/home&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('text_home'),
      		'separator' => FALSE
   		);

   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=extension/feed&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('text_feed'),
      		'separator' => ' :: '
   		);

   		$this->document->breadcrumbs[] = array(
       		'href'      => HTTPS_SERVER . 'index.php?route=feed/fb_browse&token=' . $this->session->data['token'],
       		'text'      => $this->language->get('heading_title'),
      		'separator' => ' :: '
   		);
				
		$this->data['action'] = HTTPS_SERVER . 'index.php?route=feed/fb_browse&token=' . $this->session->data['token'];
		
		$this->data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/feed&token=' . $this->session->data['token'];
		
		if (isset($this->request->post['fb_browse_status'])) {
			$this->data['fb_browse_status'] = $this->request->post['fb_browse_status'];
		} else {
			$this->data['fb_browse_status'] = $this->config->get('fb_browse_status');
		}
		
		if (isset($this->request->post['BSS_PLUGIN_KEY'])) {
			$this->data['BSS_PLUGIN_KEY'] = $this->request->post['BSS_PLUGIN_KEY'];
		} else {
			$this->data['BSS_PLUGIN_KEY'] = $this->config->get('BSS_PLUGIN_KEY');
		}
		
		$this->data['preview_link'] = HTTP_CATALOG . 'index.php?route=feed/fb_browse&n=zc_browse&p=' . $this->data['BSS_PLUGIN_KEY'];
		
		$this->template = 'feed/fb_browse.tpl';
		$this->children = array(
			'common/header',	
			'common/footer'	
		);
		
		$this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
	} 
	
	private function validate() {
		if (!$this->user->hasPermission('modify', 'feed/fb_browse')) {
			$this->error['warning'] = $this->language->get('invalid_permission');
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}	
	}	
	
	private function genRandomString() {
		 $length = 10;
		 $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		 $string = '';    
	 
		for ($p = 0; $p < $length; $p++) {
			 $string .= $characters[mt_rand(0, strlen($characters))];
		 }
	 
		return $string;
	}

}
?>