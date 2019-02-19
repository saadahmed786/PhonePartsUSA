<?php

class ControllerLocalisationCannedMessages extends Controller {
	private $error = array();
	
	private function dbcheck() {
		if(!isset($this->session->data['canned_message_check'])) {
			$this->db->query(
				sprintf(
				'CREATE TABLE IF NOT EXISTS `%1$scanned_message` (
  `canned_message_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`canned_message_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;'
				,
				DB_PREFIX)
			);
			$this->session->data['canned_message_check'] = 1;
		}
	}  
 
	public function index() {
		$this->dbcheck();
		
		$this->load->language('localisation/canned_messages');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('localisation/canned_messages');
		
		$this->getList();
	}

	public function insert() {
		$this->load->language('localisation/canned_messages');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('localisation/canned_messages');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_localisation_canned_messages->addCannedMessage($this->request->post);
			
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
			
			$this->redirect($this->url->link('localisation/canned_messages', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function update() {
		$this->load->language('localisation/canned_messages');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('localisation/canned_messages');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_localisation_canned_messages->editCannedMessage($this->request->get['canned_message_id'], $this->request->post);
			
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
			
			$this->redirect($this->url->link('localisation/canned_messages', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('localisation/canned_messages');

		$this->document->setTitle($this->language->get('heading_title'));
 		
		$this->load->model('localisation/canned_messages');
		
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $canned_message_id) {
				$this->model_localisation_canned_messages->deleteCannedMessage($canned_message_id);
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
			
			$this->redirect($this->url->link('localisation/canned_messages', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	private function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'title';
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
			'href'      => $this->url->link('localisation/canned_messages', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);
		
		$this->data['insert'] = $this->url->link('localisation/canned_messages/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['delete'] = $this->url->link('localisation/canned_messages/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');
		 
		$this->data['canned_messages'] = array();
		
		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$length_class_total = $this->model_localisation_canned_messages->getTotalCannedMessages();
		
		$results = $this->model_localisation_canned_messages->getCannedMessages($data);
		
		foreach ($results as $result) {
			$action = array();
			
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('localisation/canned_messages/update', 'token=' . $this->session->data['token'] . '&canned_message_id=' . $result['canned_message_id'] . $url, 'SSL')
			);

			$this->data['canned_messages'][] = array(
				'canned_message_id' => $result['canned_message_id'],
				'title'           => $result['title'],
				'selected'        => isset($this->request->post['selected']) && in_array($result['canned_message_id'], $this->request->post['selected']),
				'action'          => $action
			);
		}

		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		
		$this->data['column_title'] = $this->language->get('column_title');
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
		
		$this->data['sort_title'] = $this->url->link('localisation/canned_messages', 'token=' . $this->session->data['token'] . '&sort=title' . $url, 'SSL');
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
												
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $length_class_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('localisation/canned_messages', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$this->data['pagination'] = $pagination->render();
		
		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->template = 'localisation/canned_messages_list.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function getForm() {
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['entry_title'] = $this->language->get('entry_title');
		$this->data['entry_message'] = $this->language->get('entry_message');
		$this->data['entry_tags'] = $this->language->get('entry_tags');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		$this->data['tab_general'] = $this->language->get('tab_general');
		
		
		$this->load->model('sale/order');
		$data = array(
			'start'	=> 0,
			'limit' => 1,
			'order'	=> 'DESC',
			'sort'	=> 'o.date_added',
		);
		
		$this->data['order'] = false;
		
		$result = $this->model_sale_order->getOrders($data);
		if(!empty($result[0])) {
			$order_id = $result[0]['order_id'];
			$this->data['order'] = $this->model_localisation_canned_messages->getCannedOrder($order_id);
			ksort($this->data['order']);
			
			$this->document->addScript('view/javascript/jquery/canned_messages.js');
			$this->document->addStyle('view/stylesheet/canned_messages.css');
		}

 		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

 		if (isset($this->error['title'])) {
			$this->data['error_title'] = $this->error['title'];
		} else {
			$this->data['error_title'] = array();
		}

 		if (isset($this->error['message'])) {
			$this->data['error_message'] = $this->error['message'];
		} else {
			$this->data['error_message'] = array();
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
			'href'      => $this->url->link('localisation/canned_messages', 'token=' . $this->session->data['token'] . $url, 'SSL'),      		
      		'separator' => ' :: '
   		);
		
		if (!isset($this->request->get['canned_message_id'])) {
			$this->data['action'] = $this->url->link('localisation/canned_messages/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$this->data['action'] = $this->url->link('localisation/canned_messages/update', 'token=' . $this->session->data['token'] . '&canned_message_id=' . $this->request->get['canned_message_id'] . $url, 'SSL');
		}

		$this->data['cancel'] = $this->url->link('localisation/canned_messages', 'token=' . $this->session->data['token'] . $url, 'SSL');

		if (isset($this->request->get['canned_message_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$canned_message_info = $this->model_localisation_canned_messages->getCannedMessage($this->request->get['canned_message_id']);
    	}	
		
		if (isset($this->request->post['title'])) {
			$this->data['title'] = $this->request->post['title'];
		} elseif (isset($canned_message_info)) {
			$this->data['title'] = $canned_message_info['title'];
		} else {
			$this->data['title'] = '';
		}		
		
		if (isset($this->request->post['message'])) {
			$this->data['message'] = $this->request->post['message'];
		} elseif (isset($canned_message_info)) {
			$this->data['message'] = $canned_message_info['message'];
		} else {
			$this->data['message'] = '';
		}
		
 		if (isset($this->error['title'])) {
			$this->data['error_title'] = $this->error['title'];
		} else {
			$this->data['error_title'] = '';
		}
		
 		if (isset($this->error['message'])) {
			$this->data['error_message'] = $this->error['message'];
		} else {
			$this->data['error_message'] = '';
		}

		$this->template = 'localisation/canned_messages_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
	}

	private function validateForm() {
		if (!$this->user->hasPermission('modify', 'localisation/canned_messages')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		$title_length = $this->strlen($this->request->post['title']);
		$message_length = $this->strlen($this->request->post['message']);
		
		if($title_length < 3 || $title_length > 128) {
			$this->error['title'] = $this->language->get('error_title');
		}
		
		if($message_length < 5) {
			$this->error['message'] = $this->language->get('error_message');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	private function validateDelete() {
		if (!$this->user->hasPermission('modify', 'localisation/canned_messages')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}
	
	private function strlen($text) {
		if(function_exists('utf8_strlen')) {
			return utf8_strlen($text);
		} else {
			return strlen(utf8_decode($text));
		}
	}
}