<?php  
class ControllerSaleCreditReason extends Controller {
	private $error = array();
     
  	public function index() {
		$this->load->language('sale/credit_reason');
    	
		$this->document->setTitle($this->language->get('heading_title'));
		
		
		$this->load->model('sale/credit_reason');
		if($this->user->getUserGroupId()=='1')
		{
			$this->data['is_admin'] = true;	
			
		}
		else
		{
			$this->data['is_admin'] = false;	
		}
		
		$this->getList();
  	}
  
  	public function insert() {
    	$this->load->language('sale/credit_reason');
		
		
    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('sale/credit_reason');
		
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
		
			
			$reason_id = $this->model_sale_credit_reason->addReason($this->request->post);
			
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
			
			
			$this->redirect($this->url->link('sale/credit_reason', 'token=' . $this->session->data['token'] . $url, 'SSL'));
			
    	}
    
    	$this->getForm();
  	}

  	public function update() {
    	$this->load->language('sale/credit_reason');

    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('sale/credit_reason');
		
				
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			
			
			//print_r($this->request->post);exit;
			
		
			$this->model_sale_credit_reason->editReason($this->request->get['reason_id'], $this->request->post);
      		
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
			
			
			$this->redirect($this->url->link('sale/credit_reason', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
    
    	$this->getForm();
  	}

  	public function delete() {
    	$this->load->language('sale/credit_reason');

    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('sale/credit_reason');
		
    	if (isset($this->request->post['selected']) && $this->validateDelete()) { 
			foreach ($this->request->post['selected'] as $reason_id) {
				$this->model_sale_credit_reason->deleteReason($reason_id);
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
			
			$this->redirect($this->url->link('sale/credit_reason', 'token=' . $this->session->data['token'] . $url, 'SSL'));
    	}
	
    	$this->getList();
  	}

  	private function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'date_added';
		}
		
		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
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
			'href'      => $this->url->link('sale/credit_reason', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);
							
		$this->data['insert'] = $this->url->link('sale/credit_reason/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['delete'] = $this->url->link('sale/credit_reason/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');
		
		$this->data['vouchers'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);
		
		$reason_total = $this->model_sale_credit_reason->getTotalReasons();
	
		$results = $this->model_sale_credit_reason->getReasons($data);
 
    	foreach ($results as $result) {
			$action = array();
									
			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('sale/credit_reason/update', 'token=' . $this->session->data['token'] . '&reason_id=' . $result['reason_id'] . $url, 'SSL')
			);
						
			$this->data['reasons'][] = array(
				'reason_id' => $result['reason_id'],
				'name'       => $result['name'],
				'code'       => $result['code'],
				'message'    => $result['message'],
				'status'     => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'selected'   => isset($this->request->post['selected']) && in_array($result['reason_id'], $this->request->post['selected']),
				'action'     => $action
			);
		}
									
		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['text_send'] = $this->language->get('text_send');
		$this->data['text_wait'] = $this->language->get('text_wait');
		$this->data['text_no_results'] = $this->language->get('text_no_results');

		$this->data['column_code'] = $this->language->get('column_code');
		$this->data['column_name'] = $this->language->get('column_name');
		$this->data['column_message'] = $this->language->get('column_message');
		
		$this->data['column_status'] = $this->language->get('column_status');
		$this->data['column_date_added'] = $this->language->get('column_date_added');
		$this->data['column_action'] = $this->language->get('column_action');		
		
		$this->data['button_insert'] = $this->language->get('button_insert');
		$this->data['button_delete'] = $this->language->get('button_delete');
 
 		$this->data['token'] = $this->session->data['token'];
		
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
		
		$this->data['sort_code'] = $this->url->link('sale/credit_reason', 'token=' . $this->session->data['token'] . '&sort=code' . $url, 'SSL');
		$this->data['sort_name'] = $this->url->link('sale/credit_reason', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');
		
		$this->data['sort_status'] = $this->url->link('sale/credit_reason', 'token=' . $this->session->data['token'] . '&sort=status' . $url, 'SSL');
		$this->data['sort_date_added'] = $this->url->link('sale/credit_reason', 'token=' . $this->session->data['token'] . '&sort=date_added' . $url, 'SSL');
				
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
												
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $reason_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('sale/credit_reason', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');
			
		$this->data['pagination'] = $pagination->render();

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

			

		$this->template = 'sale/credit_reason_list.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
  	}

  	private function getForm() {
		$this->load->model('sale/credit_reason');
		
    	$this->data['heading_title'] = $this->language->get('heading_title');

    	$this->data['text_enabled'] = $this->language->get('text_enabled');
    	$this->data['text_disabled'] = $this->language->get('text_disabled');
		
    	$this->data['entry_code'] = $this->language->get('entry_code');
		$this->data['entry_name'] = $this->language->get('entry_name');
		$this->data['entry_message'] = $this->language->get('entry_message');
		$this->data['entry_status'] = $this->language->get('entry_status');
		$this->data['entry_template_shortcode'] = $this->language->get('entry_template_shortcode');

    	$this->data['button_save'] = $this->language->get('button_save');
    	$this->data['button_cancel'] = $this->language->get('button_cancel');
		
		
		
		if (isset($this->request->get['reason_id'])) {
			$this->data['reason_id'] = $this->request->get['reason_id'];
		} else {
			$this->data['reason_id'] = 0;
		}
		 		
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->error['code'])) {
			$this->data['error_code'] = $this->error['code'];
		} else {
			$this->data['error_code'] = '';
		}	
		if (isset($this->error['reason'])) {
			$this->data['error_reason'] = $this->error['reason'];
		} else {
			$this->data['error_reason'] = '';
		}		
		
		if (isset($this->error['name'])) {
			$this->data['error_name'] = $this->error['name'];
		} else {
			$this->data['error_name'] = '';
		}	
		
		if (isset($this->error['message'])) {
			$this->data['error_message'] = $this->error['message'];
		} else {
			$this->data['error_message'] = '';
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
			'href'      => $this->url->link('sale/credit_reason', 'token=' . $this->session->data['token'] . $url, 'SSL'),
      		'separator' => ' :: '
   		);
									
		if (!isset($this->request->get['reason_id'])) {
			$this->data['action'] = $this->url->link('sale/credit_reason/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$this->data['action'] = $this->url->link('sale/credit_reason/update', 'token=' . $this->session->data['token'] . '&reason_id=' . $this->request->get['reason_id'] . $url, 'SSL');
		}
		
		$this->data['cancel'] = $this->url->link('sale/credit_reason', 'token=' . $this->session->data['token'] . $url, 'SSL');
  		
		if (isset($this->request->get['reason_id']) && (!$this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$reason_info = $this->model_sale_credit_reason->getReason($this->request->get['reason_id']);
    	}
		
		$this->data['token'] = $this->session->data['token'];

    	if (isset($this->request->post['code'])) {
      		$this->data['code'] = $this->request->post['code'];
    	} elseif (!empty($reason_info)) {
			$this->data['code'] = $reason_info['code'];
		} else {
      		$this->data['code'] = '';
    	}
		
    	if (isset($this->request->post['name'])) {
      		$this->data['name'] = $this->request->post['name'];
    	} elseif (!empty($reason_info)) {
			$this->data['name'] = $reason_info['name'];
		} else {
      		$this->data['name'] = '';
    	}
		
    	if (isset($this->request->post['message'])) {
      		$this->data['message'] = $this->request->post['message'];
    	} elseif (!empty($reason_info)) {
			$this->data['message'] = $reason_info['message'];
		} else {
      		$this->data['message'] = '';
    	}
		
		
 
 		
    	if (isset($this->request->post['status'])) { 
      		$this->data['status'] = $this->request->post['status'];
    	} elseif (!empty($reason_info)) {
			$this->data['status'] = $reason_info['status'];
		} else {
      		$this->data['status'] = 1;
    	}


		$this->template = 'sale/credit_reason_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());		
  	}
	
	private function getJSON($data) {
		if(file_exists(DIR_SYSTEM . 'library/json.php')){
			$this->load->library('json');
			return JSON::encode($data);
		} else {
			return json_encode($data);
		}
	}
  	private function validateForm() {
    	if (!$this->user->hasPermission('modify', 'sale/credit_reason')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}
		
    	if ((utf8_strlen($this->request->post['code']) < 1) || (utf8_strlen($this->request->post['code']) > 3)) {
      		$this->error['code'] = $this->language->get('error_code');
    	}
		
		$reason_info = $this->model_sale_credit_reason->getReasonByCode($this->request->post['code']);
		
		if ($reason_info) {
			if (!isset($this->request->get['reason_id'])) {
				$this->error['warning'] = $this->language->get('error_exists');
			} elseif ($reason_info['reason_id'] != $this->request->get['reason_id'])  {
				$this->error['warning'] = $this->language->get('error_exists');
			}
		}
					      
    	/*if ((utf8_strlen($this->request->post['to_name']) < 1) || (utf8_strlen($this->request->post['to_name']) > 64)) {
      		$this->error['to_name'] = $this->language->get('error_to_name');
    	}    	
		
		if ((utf8_strlen($this->request->post['to_email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['to_email'])) {
      		$this->error['to_email'] = $this->language->get('error_email');
    	}
		
    	if ((utf8_strlen($this->request->post['from_name']) < 1) || (utf8_strlen($this->request->post['from_name']) > 64)) {
      		$this->error['from_name'] = $this->language->get('error_from_name');
    	}  
		
		if ((utf8_strlen($this->request->post['from_email']) > 96) || !preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $this->request->post['from_email'])) {
      		$this->error['from_email'] = $this->language->get('error_email');
    	}*/
		
		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 50)) {
      		$this->error['name'] = $this->language->get('error_name');
    	}
		if ((utf8_strlen($this->request->post['message']) < 10) ) {
      		$this->error['message'] = 'Please provide a detailed message description.';
    	}
		

    	if (!$this->error) {
      		return true;
    	} else {
      		return false;
    	}
  	}

  	private function validateDelete() {
    	if (!$this->user->hasPermission('modify', 'sale/credit_reason')) {
      		$this->error['warning'] = $this->language->get('error_permission');  
    	}
		
		$this->load->model('sale/credit_reason');
		
		
		
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}	
	
	
	
	
}
?>