<?php

class ControllerKodecrmChat extends Controller {

    private $error = array();

    public function index() {

        $this->load->language('kodecrm/chat');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('setting/setting');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('kodecrm_chat', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');

            $this->redirect($this->url->link('kodecrm/chat', 'token=' . $this->session->data['token'], 'SSL'));
        }
        $this->data['heading_title'] = $this->language->get('heading_title');

        $this->data['entry_app_id'] = $this->language->get('entry_app_id');
        $this->data['entry_status'] = $this->language->get('entry_status');
        
        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');

        $this->data['tab_general'] = $this->language->get('tab_general');

        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');
        
        if (isset($this->session->data['success'])) {
            $this->data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        } else {
            $this->data['success'] = '';
        }

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
            'text'      => $this->language->get('heading_title'),
            'href'      => $this->url->link('kodecrm/chat', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['action'] = $this->url->link('kodecrm/chat', 'token=' . $this->session->data['token'], 'SSL');

        $this->data['cancel'] = $this->url->link('kodecrm/chat', 'token=' . $this->session->data['token'], 'SSL');

        if (isset($this->request->post['kodecrm_app_id'])) {
            $this->data['kodecrm_app_id'] = $this->request->post['kodecrm_app_id'];
        } else {
            $this->data['kodecrm_app_id'] = $this->config->get('kodecrm_app_id');
        }

        if (isset($this->request->post['kodecrm_widget_status'])) {
            $this->data['kodecrm_widget_status'] = $this->request->post['kodecrm_widget_status'];
        } else {
            $this->data['kodecrm_widget_status'] = $this->config->get('kodecrm_widget_status');
        }

        if (isset($this->request->post['kodecrm_widget_status'])) {
            $this->data['kodecrm_widget_status'] = $this->request->post['kodecrm_widget_status'];
        } else {
            $this->data['kodecrm_widget_status'] = $this->config->get('kodecrm_widget_status');
        }

        $this->template = 'kodecrm/chat.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );

        $this->response->setOutput($this->render());
    }

    private function validate() {
        if (!$this->user->hasPermission('modify', 'kodecrm/chat')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }
}
?>
