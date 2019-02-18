<?php
class ControllerMiscComingsoon extends Controller {
	public function index() {

		$this->children = array(
			'common/header',
			'common/footer'
		);

		 if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/misc/comingsoon.tpl')) {
            $this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/misc/comingsoon.tpl';
        } else {
            $this->template = 'default/template/misc/comingsoon.tpl';
        }
				
		$this->response->setOutput($this->render());
  	}
}
 ?>