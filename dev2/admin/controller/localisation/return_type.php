<?php

class ControllerLocalisationReturnType extends Controller {
  private $error = array();

  public function index() {
    $this->language->load('localisation/return_type');

    $this->document->setTitle($this->language->get('heading_title'));

    $this->load->model('localisation/return_type');

    $this->getList();
  }

  public function insert() {
    $this->language->load('localisation/return_type');

    $this->document->setTitle($this->language->get('heading_title'));

    $this->load->model('localisation/return_type');

    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
      $this->model_localisation_return_type->addReturnType($this->request->post);

      $this->session->data['success'] = $this->language->get('text_success');

      $url = '';

      if (isset($this->request->get['sort'])) {
        $url .= '&sort=' . $this->request->get['sort'];
      }

      if (isset($this->request->get['order'])) {
        $url .= '&order=' . $this->request->get['order'];
      }

      if (isset($this->request->get['page'])) {
        $url .= '&page=' . $this->request->get['page'];
      }

      $this->redirect($this->url->link('localisation/return_type', 'token=' . $this->session->data['token'] . $url, 'SSL'));
    }

    $this->getForm();
  }

  public function update() {
    $this->language->load('localisation/return_type');

    $this->document->setTitle($this->language->get('heading_title'));

    $this->load->model('localisation/return_type');

    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
      $this->model_localisation_return_type->editReturnType($this->request->get['return_type_id'], $this->request->post);

      $this->session->data['success'] = $this->language->get('text_success');

      $url = '';

      if (isset($this->request->get['sort'])) {
        $url .= '&sort=' . $this->request->get['sort'];
      }

      if (isset($this->request->get['order'])) {
        $url .= '&order=' . $this->request->get['order'];
      }

      if (isset($this->request->get['page'])) {
        $url .= '&page=' . $this->request->get['page'];
      }

      $this->redirect($this->url->link('localisation/return_type', 'token=' . $this->session->data['token'] . $url, 'SSL'));
    }

    $this->getForm();
  }

  public function delete() {
    $this->language->load('localisation/return_type');

    $this->document->setTitle($this->language->get('heading_title'));

    $this->load->model('localisation/return_type');

    if (isset($this->request->post['selected']) && $this->validateDelete()) {
      foreach ($this->request->post['selected'] as $return_type_id) {
        $this->model_localisation_return_type->deleteReturnType($return_type_id);
      }

      $this->session->data['success'] = $this->language->get('text_success');

      $url = '';

      if (isset($this->request->get['sort'])) {
        $url .= '&sort=' . $this->request->get['sort'];
      }

      if (isset($this->request->get['order'])) {
        $url .= '&order=' . $this->request->get['order'];
      }

      if (isset($this->request->get['page'])) {
        $url .= '&page=' . $this->request->get['page'];
      }

      $this->redirect($this->url->link('localisation/return_type', 'token=' . $this->session->data['token'] . $url, 'SSL'));
    }

    $this->getList();
  }

  protected function getList() {
    if (isset($this->request->get['sort'])) {
      $sort = $this->request->get['sort'];
    } else {
      $sort = 'name';
    }

    if (isset($this->request->get['order'])) {
      $order = $this->request->get['order'];
    } else {
      $order = 'ASC';
    }

    if (isset($this->request->get['page'])) {
      $page = $this->request->get['page'];
    } else {
      $page = 1;
    }

    $url = '';

    if (isset($this->request->get['sort'])) {
      $url .= '&sort=' . $this->request->get['sort'];
    }

    if (isset($this->request->get['order'])) {
      $url .= '&order=' . $this->request->get['order'];
    }

    if (isset($this->request->get['page'])) {
      $url .= '&page=' . $this->request->get['page'];
    }

    $this->data['breadcrumbs'] = array();

    $this->data['breadcrumbs'][] = array(
      'text'      => $this->language->get('text_home'),
      'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      'separator' => false
    );

    $this->data['breadcrumbs'][] = array(
      'text'      => $this->language->get('heading_title'),
      'href'      => $this->url->link('localisation/return_type', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      'separator' => ' :: '
    );

    $this->data['insert'] = $this->url->link('localisation/return_type/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
    $this->data['delete'] = $this->url->link('localisation/return_type/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');

    $this->data['return_types'] = array();

    $data = array(
      'sort'  => $sort,
      'order' => $order,
      'start' => ($page - 1) * $this->config->get('config_admin_limit'),
      'limit' => $this->config->get('config_admin_limit')
    );

    $return_type_total = $this->model_localisation_return_type->getTotalReturnTypes();

    $results = $this->model_localisation_return_type->getReturnTypes($data);

    foreach ($results as $result) {
      $action = array();

      $action[] = array(
        'text' => $this->language->get('text_edit'),
        'href' => $this->url->link('localisation/return_type/update', 'token=' . $this->session->data['token'] . '&return_type_id=' . $result['return_type_id'] . $url, 'SSL')
      );

      $this->data['return_types'][] = array(
        'return_type_id' => $result['return_type_id'],
        'name'          => $result['name'],
        'selected'      => isset($this->request->post['selected']) && in_array($result['return_type_id'], $this->request->post['selected']),
        'action'        => $action
      );
    }

    $this->data['heading_title'] = $this->language->get('heading_title');

    $this->data['text_no_results'] = $this->language->get('text_no_results');

    $this->data['column_name'] = $this->language->get('column_name');
    $this->data['column_action'] = $this->language->get('column_action');

    $this->data['button_insert'] = $this->language->get('button_insert');
    $this->data['button_delete'] = $this->language->get('button_delete');

    if (isset($this->error['warning'])) {
      $this->data['error_warning'] = $this->error['warning'];
    } else {
      $this->data['error_warning'] = '';
    }

    if (isset($this->session->data['success'])) {
      $this->data['success'] = $this->session->data['success'];

      unset($this->session->data['success']);
    } else {
      $this->data['success'] = '';
    }

    $url = '';

    if ($order == 'ASC') {
      $url .= '&order=DESC';
    } else {
      $url .= '&order=ASC';
    }

    if (isset($this->request->get['page'])) {
      $url .= '&page=' . $this->request->get['page'];
    }

    $this->data['sort_name'] = $this->url->link('localisation/return_type', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');

    $url = '';

    if (isset($this->request->get['sort'])) {
      $url .= '&sort=' . $this->request->get['sort'];
    }

    if (isset($this->request->get['order'])) {
      $url .= '&order=' . $this->request->get['order'];
    }

    $pagination = new Pagination();
    $pagination->total  = $return_type_total;
    $pagination->page   = $page;
    $pagination->limit  = $this->config->get('config_admin_limit');
    $pagination->text   = $this->language->get('text_pagination');
    $pagination->url    = $this->url->link('localisation/return_type', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

    $this->data['pagination'] = $pagination->render();

    $this->data['sort'] = $sort;
    $this->data['order'] = $order;

    $this->template = 'localisation/return_type_list.tpl';
    $this->children = array(
      'common/header',
      'common/footer'
    );

    $this->response->setOutput($this->render());
  }

  protected function getForm() {
    $this->data['heading_title'] = $this->language->get('heading_title');

    $this->data['entry_name'] = $this->language->get('entry_name');

    $this->data['button_save'] = $this->language->get('button_save');
    $this->data['button_cancel'] = $this->language->get('button_cancel');

    if (isset($this->error['warning'])) {
      $this->data['error_warning'] = $this->error['warning'];
    } else {
      $this->data['error_warning'] = '';
    }

    if (isset($this->error['name'])) {
      $this->data['error_name'] = $this->error['name'];
    } else {
      $this->data['error_name'] = array();
    }

    $url = '';

    if (isset($this->request->get['sort'])) {
      $url .= '&sort=' . $this->request->get['sort'];
    }

    if (isset($this->request->get['order'])) {
      $url .= '&order=' . $this->request->get['order'];
    }

    if (isset($this->request->get['page'])) {
      $url .= '&page=' . $this->request->get['page'];
    }

    $this->data['breadcrumbs'] = array();

    $this->data['breadcrumbs'][] = array(
      'text'      => $this->language->get('text_home'),
      'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      'separator' => false
    );

    $this->data['breadcrumbs'][] = array(
      'text'      => $this->language->get('heading_title'),
      'href'      => $this->url->link('localisation/return_type', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      'separator' => ' :: '
    );

    if (!isset($this->request->get['return_type_id'])) {
      $this->data['action'] = $this->url->link('localisation/return_type/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
    } else {
      $this->data['action'] = $this->url->link('localisation/return_type/update', 'token=' . $this->session->data['token'] . '&return_type_id=' . $this->request->get['return_type_id'] . $url, 'SSL');
    }

    $this->data['cancel'] = $this->url->link('localisation/return_type', 'token=' . $this->session->data['token'] . $url, 'SSL');

    $this->load->model('localisation/language');

    $this->data['languages'] = $this->model_localisation_language->getLanguages();

    if (isset($this->request->post['return_type'])) {
      $this->data['return_type'] = $this->request->post['return_type'];
    } elseif (isset($this->request->get['return_type_id'])) {
      $this->data['return_type'] = $this->model_localisation_return_type->getReturnTypeDescriptions($this->request->get['return_type_id']);
    } else {
      $this->data['return_type'] = array();
    }

    $this->template = 'localisation/return_type_form.tpl';
    $this->children = array(
      'common/header',
      'common/footer'
    );

    $this->response->setOutput($this->render());
  }

  protected function validateForm() {
    if (!$this->user->hasPermission('modify', 'localisation/return_type')) {
      $this->error['warning'] = $this->language->get('error_permission');
    }

    foreach ($this->request->post['return_type'] as $language_id => $value) {
      if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 32)) {
        $this->error['name'][$language_id] = $this->language->get('error_name');
      }
    }

    if (!$this->error) {
      return true;
    } else {
      return false;
    }
  }

  protected function validateDelete() {
    if (!$this->user->hasPermission('modify', 'localisation/return_type')) {
      $this->error['warning'] = $this->language->get('error_permission');
    }

    $this->load->model('sale/return');

    foreach ($this->request->post['selected'] as $return_type_id) {
      if ($this->config->get('config_return_type_id') == $return_type_id) {
        $this->error['warning'] = $this->language->get('error_default');
      }

      $return_total = $this->model_sale_return->getTotalReturnsByReturnTypeId($return_type_id);

      if ($return_total) {
        $this->error['warning'] = sprintf($this->language->get('error_return'), $return_total);
      }

      $return_total = $this->model_sale_return->getTotalReturnHistoriesByReturnTypeId($return_type_id);

      if ($return_total) {
        $this->error['warning'] = sprintf($this->language->get('error_return'), $return_total);
      }
    }

    if (!$this->error) {
      return true;
    } else {
      return false;
    }
  }
}

?>
