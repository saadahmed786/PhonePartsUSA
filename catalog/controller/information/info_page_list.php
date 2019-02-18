<?php 
class ControllerInformationInfoPageList extends Controller { 
	public function index() {		
		if(!$this->isVisitorMobile()) {				
			$this->redirect($this->url->link('common/home'));
		} else {		
			$this->language->load('common/footer');
			/* $this->language->load('omf/common'); */

			$this->document->setTitle($this->language->get('heading_title'));

			$this->data['breadcrumbs'] = array();

			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_home'),
				'href'      => $this->url->link('common/home'),
				'separator' => false
			); 

			$this->data['breadcrumbs'][] = array(
				'text'      => $this->language->get('text_information'),
				'href'      => $this->url->link('information/info_page_list'),      		
				'separator' => $this->language->get('text_separator')
			);				
			
			$this->load->model('catalog/information');
			
			$this->data['informations'] = array();

			foreach ($this->model_catalog_information->getInformations() as $result) {
				$this->data['informations'][] = array(
					'title' => $result['title'],
					'href'  => $this->url->link('information/information', 'information_id=' . $result['information_id'])
				);
			}
			
			$this->data['heading_title'] = $this->language->get('text_information');
					
			if (file_exists(DIR_TEMPLATE . (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/information/info_page_list.tpl')) {
				$this->template = (($this->session->data['temp_theme'])? $this->session->data['temp_theme'] : $this->config->get('config_template')) . '/template/information/info_page_list.tpl';
			} else {
				$this->template = 'default/template/information/info_page_list.tpl';
			}
			
			$this->children = array(
				'common/column_left',
				'common/column_right',
				'common/content_top',
				'common/content_bottom',
				'common/footer',
				'common/header'		
			);
					
		}
		$this->response->setOutput($this->render());
  	}
}
?>