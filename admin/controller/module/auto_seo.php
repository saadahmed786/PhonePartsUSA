<?php
class ControllerModuleautoseo extends Controller {
    private $error = array();
    public function index () 
	{
        $this->load->language('module/auto_seo');
        $this->document->setTitle = $this->language->get('heading_title');
        $this->load->model('setting/setting');
        $this->load->model('module/auto_seo');
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {
            if (isset($this->request->post['categories'])) {
                $this->model_module_auto_seo->generateCategories();
            }
            if (isset($this->request->post['products'])) {
                $this->model_module_auto_seo->generateProducts();
            }
            $this->data['success'] = $this->language->get('text_success');
        }
        if (isset($this->error['warning'])) {
            $this->data['error_warning'] = $this->error['warning'];
        } else {
            $this->data['error_warning'] = '';
        }
        $this->data['warning_clear'] = $this->language->get('warning_clear');
        $this->data['back'] = $this->language->get('back');
        $this->data['categories'] = $this->language->get('categories');
        $this->data['products'] = $this->language->get('products');
        $this->data['generate'] = $this->language->get('generate');
        $this->document->breadcrumbs = array();
        $this->document->breadcrumbs[] = array('href' => HTTPS_SERVER . 'index.php?route=common/home&token=' . $this->session->data['token'], 'text' => $this->language->get('text_home'), 'separator' => FALSE);
        $this->document->breadcrumbs[] = array('href' => HTTPS_SERVER . 'index.php?route=extension/module&token=' . $this->session->data['token'], 'text' => $this->language->get('text_module'), 'separator' => ' :: ');
        $this->document->breadcrumbs[] = array('href' => HTTPS_SERVER . 'index.php?route=module/auto_seo&token=' . $this->session->data['token'], 'text' => $this->language->get('heading_title'), 'separator' => ' :: ');
        $this->data['action'] = HTTPS_SERVER . 'index.php?route=module/auto_seo&token=' . $this->session->data['token'];
        $this->data['cancel'] = HTTPS_SERVER . 'index.php?route=extension/module&token=' . $this->session->data['token'];
        $this->data['heading_title'] = $this->language->get('heading_title');
        if (isset($this->request->post['auto_seo_status'])) {
            $this->data['auto_seo_status'] = $this->request->post['auto_seo_status'];
        } else {
            $this->data['auto_seo_status'] = $this->config->get('auto_seo_status');
        }
        $this->template = 'module/auto_seo.tpl';
        $this->children = array('common/header', 'common/footer');
        $this->response->setOutput($this->render(TRUE), $this->config->get('config_compression'));
    }
    
	private function validate () 
	{
        if (! $this->user->hasPermission('modify', 'module/auto_seo')) 
		{
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if (! $this->error) 
		{
            return true;
        } else {
            return false;
        }
    }
} 