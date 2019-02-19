<?php  
class ControllerSaleReturnProgram extends Controller {
	private $error = array();
     
  	public function index() {
		$this->load->language('sale/return_program');
    	
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('sale/return_program');
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
    	$this->load->language('sale/return_program');
		$this->load->model('sale/order');
		
    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('sale/return_program');
		
    	if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
					
						
			$voucher_id = $this->model_sale_return_program->addReturn($this->request->post);
			
			$this->session->data['success'] = $this->language->get('text_success');

			if($this->request->get['xorder_id'])
			{
			$this->redirect($this->url->link('sale/return_program', 'token=' . $this->session->data['token'].'&xorder_id='.$this->request->get['order_id'], 'SSL'));
			}
			else
			{
				$this->redirect($this->url->link('sale/return_program', 'token=' . $this->session->data['token'], 'SSL'));
			}
			
    	}
    
    	$this->getForm();
  	}

  	public function update() {
    	$this->load->language('sale/voucher');

    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('sale/voucher');
		$this->load->model('sale/order');
				
    	if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			
			
			//print_r($this->request->post);exit;
			$order_info = $this->model_sale_order->getOrder($this->request->post['order_id']);
		
				$this->request->post['to_name'] = $order_info['firstname'].' '.$order_info['lastname'];
				$this->request->post['to_email'] = $order_info['email'];
		
			$this->model_sale_voucher->editVoucher($this->request->get['voucher_id'], $this->request->post);
      		
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
			
			if(isset($this->request->post['save_and_send']) and $this->request->post['save_and_send']=='send')
			{
				$this->send2($this->request->get['voucher_id']);
					
			}
			$this->redirect($this->url->link('sale/voucher', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}
    
    	$this->getForm();
  	}

  	public function delete() {
    	$this->load->language('sale/voucher');

    	$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('sale/voucher');
		
    	if (isset($this->request->post['selected']) && $this->validateDelete()) { 
			foreach ($this->request->post['selected'] as $voucher_id) {
				$this->model_sale_voucher->deleteVoucher($voucher_id);
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
			
			$this->redirect($this->url->link('sale/voucher', 'token=' . $this->session->data['token'] . $url, 'SSL'));
    	}
	
    	$this->getList();
  	}

  	private function getList() {
		

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('sale/return_program', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
							
		$this->data['insert'] = $this->url->link('sale/return_program/insert', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['vouchers'] = array();

		
		
		
	
		$results = $this->model_sale_return_program->getReturns();
		
 $this->load->model('user/user');
 $this->load->model('sale/customer');
  $this->load->model('sale/order');
  $this->load->model('localisation/return_reason');
    
		
		
		foreach ($results as $result) {
			
					$user_id = 	$this->model_user_user->getUser($result['user_id']);
					$user_name = $user_id['firstname'].' '.$user_id['lastname'];
				
				
				$order_infox = $this->model_sale_order->getOrder($result['order_id']);
				
				$reason_info = $this->model_localisation_return_reason->getReturnReasonDescriptions($result['reason_id']);
				
				
			$this->data['returns'][] = array(
				'return_id' => $result['return_id'],
				'order_id'       => $result['order_id'],
				
				'customer'       => ($order_infox['shipping_firstname'].' '.$order_infox['shipping_lastname']),
				'items_returned'         => $this->model_sale_return_program->getReturnItems($result['return_id']),
				'user'    => ($user_name),
				'reason'      => $reason_info[1]['name'],
				'resolution'     => $result['resolution'],
				
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
				
			);
		}

		$this->data['heading_title'] = $this->language->get('heading_title');
		
		$this->data['text_send'] = $this->language->get('text_send');
		$this->data['text_wait'] = $this->language->get('text_wait');
		$this->data['text_no_results'] = $this->language->get('text_no_results');

		$this->data['column_date'] = 'Date';
		$this->data['column_order_id'] = 'Order ID';
		$this->data['column_customer'] = 'Customer Name';
		$this->data['column_item_returned'] = 'Items Returned - Reason';
		$this->data['column_resolution'] = 'Resolution';
		$this->data['column_user'] = 'Completed By';
		
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

			

		$this->template = 'sale/return_program_list.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);
				
		$this->response->setOutput($this->render());
  	}

  	private function getForm() {
		$this->load->model('catalog/product');
		$this->load->model('sale/order');
		$this->load->model('sale/credit_reason');
		$this->load->model('sale/return_program');
		$this->load->model('localisation/return_reason');
		
    	$this->data['heading_title'] = $this->language->get('heading_title');

    	$this->data['text_enabled'] = $this->language->get('text_enabled');
    	$this->data['text_disabled'] = $this->language->get('text_disabled');
		
    	

    	$this->data['button_save'] = $this->language->get('button_save');
    	$this->data['button_cancel'] = $this->language->get('button_cancel');
		
		
		 		
		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}
		
		if (isset($this->error['order_id'])) {
			$this->data['error_order_id'] = $this->error['order_id'];
		} else {
			$this->data['error_order_id'] = '';
		}	
			
		
		if (isset($this->error['amount'])) {
			$this->data['error_amount'] = $this->error['amount'];
		} else {
			$this->data['error_amount'] = '';
		}
				
		

  		$this->data['breadcrumbs'] = array();

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => false
   		);

   		$this->data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('sale/return_program', 'token=' . $this->session->data['token'], 'SSL'),
      		'separator' => ' :: '
   		);
									
		
			$this->data['action'] = $this->url->link('sale/return_program/insert', 'token=' . $this->session->data['token'], 'SSL');
	
		
		$this->data['cancel'] = $this->url->link('sale/return_program', 'token=' . $this->session->data['token'], 'SSL');
  		
		if (isset($this->request->get['order_id']) && (!$this->request->server['REQUEST_METHOD'] != 'POST')) {
      		$return_info = $this->model_sale_return_program->getReturn($this->request->get['return_id']);
    	}
		
		$this->data['token'] = $this->session->data['token'];

    	if (isset($this->request->post['order_id'])) {
      		$this->data['order_id'] = $this->request->post['order_id'];
    	
		} else {
      		$this->data['order_id'] = '';
    	}
		
    	if (isset($this->request->post['resolution'])) {
      		$this->data['resolution'] = $this->request->post['resolution'];
    	
		} else {
      		$this->data['resolution'] = '';
    	}
		
		if (isset($this->request->post['store_credit_code'])) {
      		$this->data['store_credit_code'] = $this->request->post['store_credit_code'];
    	
		} else {
      		$this->data['store_credit_code'] = '';
    	}
		
		
		if (isset($this->request->post['refund_shipping'])) {
      		$this->data['refund_shipping'] = $this->request->post['refund_shipping'];
    	
		} else {
      		$this->data['refund_shipping'] = '';
    	}
		
    	
		
		
		if (isset($this->request->post['reason_id'])) {
      		$this->data['reason_id'] = $this->request->post['reason_id'];
    	
		} else {
      		$this->data['reason_id'] = '';
    	}
		
		
		if (isset($this->request->post['product_items'])) {
			$product_list = array();
			foreach($this->request->post['product_items'] as $key => $item)
			{
				$item = explode("-",$item);
				
				$product = $this->model_catalog_product->getProduct($item[0]);
					$product_list[] = array(
					'product_id'=>$product['product_id'],
					'name' => $product['name'],
					'price'=> $item[1]
					
					);
				
			}
			
			
			$order_total = $this->model_sale_order->getOrderTotals($this->request->post['order_id']);
			
			foreach($order_total as $order_total)
			{
				
				if($order_total['code']=='shipping')
				{
				$this->data['shipping_method'] = $order_total['title'].' ('.$order_total['text'].')';
				$this->data['shipping_price'] = $order_total['value'];	
				}
				
			}
			
      	$order_total = $this->model_sale_order->getOrderTotals($this->request->post['order_id']);
			
			foreach($order_total as $order_total)
			{
				
				if($order_total['code']=='shipping')
				{
				$this->data['shipping_method'] = $order_total['title'].' ('.$order_total['text'].')';
				$this->data['shipping_price'] = $order_total['value'];	
				}
				
			}
			$this->data['product_related'] = $product_list;
			
			
    	}  else {
      		$this->data['product_related'] = array();
			
			$this->data['shipping_method'] = '';
				$this->data['shipping_price'] = '';
    	}

    	
		
    	
 
 		
		
		
		
		$reasons = $this->model_localisation_return_reason->getReturnReasons();
		
		foreach($reasons as $reason)
		{
			
				$this->data['reasons'][] = array(
				'reason_id'=>$reason['return_reason_id'],
				'name'	=>	$reason['name']
						);
			
			
			
		}
		
		
		
		
    	if (isset($this->request->post['amount'])) {
      		$this->data['amount'] = $this->request->post['amount'];
    	
		} else {
      		$this->data['amount'] = '';
    	}
	
    	if (isset($this->request->post['status'])) { 
      		$this->data['status'] = $this->request->post['status'];
    	
		} else {
      		$this->data['status'] = 1;
    	}
		if (isset($this->request->post['message'])) { 
      		$this->data['message'] = $this->request->post['message'];
    	
		} else {
      		$this->data['message'] = '';
    	}
		if($this->request->get['xorder_id'])
		{
			$this->data['xorder_id']=$this->request->get['xorder_id'];	
		}
		else
		{
			$this->data['xorder_id']='';	
		}

		$this->template = 'sale/return_program.tpl';
		
		
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
    	if (!$this->user->hasPermission('modify', 'sale/voucher')) {
      		$this->error['warning'] = $this->language->get('error_permission');
    	}
		
    	if ((utf8_strlen($this->request->post['code']) < 3) || (utf8_strlen($this->request->post['code']) > 10)) {
      		$this->error['code'] = $this->language->get('error_code');
    	}
		
		$voucher_info = $this->model_sale_voucher->getVoucherByCode($this->request->post['code']);
		
		if ($voucher_info) {
			if (!isset($this->request->get['voucher_id'])) {
				$this->error['warning'] = $this->language->get('error_exists');
			} elseif ($voucher_info['voucher_id'] != $this->request->get['voucher_id'])  {
				$this->error['warning'] = $this->language->get('error_exists');
			}
		}
		
		$voucher_order = $this->model_sale_voucher->getVoucherByOrderID($this->request->post['order_id']);
		
		if ($voucher_order) {
			if (!isset($this->request->get['voucher_id'])) {
				$this->error['warning'] = $this->language->get('error_exists');
			} elseif ($voucher_order['voucher_id'] != $this->request->get['voucher_id'])  {
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
		
		if (!isset($this->request->post['order_id'])) {
      		$this->error['order_id'] = 'Please provide Order ID';
    	}    
		if (!isset($this->request->post['product_items'])) {
      		$this->error['order_id'] = 'Please provide Products';
    	} 
		
		if ($this->request->post['amount'] < 0.01) {
      		$this->error['amount'] = $this->language->get('error_amount');
    	}
		if ($this->request->post['reason'] == '') {
      		$this->error['reason'] = 'Please select a reason';;
    	}

    	if (!$this->error) {
      		return true;
    	} else {
      		return false;
    	}
  	}

  	private function validateDelete() {
    	if (!$this->user->hasPermission('modify', 'sale/voucher')) {
      		$this->error['warning'] = $this->language->get('error_permission');  
    	}
		
		$this->load->model('sale/order');
		
		foreach ($this->request->post['selected'] as $voucher_id) {
			$order_voucher_info = $this->model_sale_order->getOrderVoucherByVoucherId($voucher_id);
			
			if ($order_voucher_info) {
				$this->error['warning'] = sprintf($this->language->get('error_order'), $this->url->link('sale/order/info', 'token=' . $this->session->data['token'] . '&order_id=' . $order_voucher_info['order_id'], 'SSL')); 
				
				break;       
			}
		}
		
		if (!$this->error) {
	  		return true;
		} else {
	  		return false;
		}
  	}	
	
	public function getResolution() {
		$this->load->language('common/filemanager');
		$this->load->model('xhelper/order');
		$this->load->model('sale/order');
		$this->load->model('localisation/zone');
		$this->load->model('sale/credit_reason');
		
		$this->data['title'] = 'Details';
		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$this->data['base'] = HTTPS_SERVER;
		} else {
			$this->data['base'] = HTTP_SERVER;
		}
		
		$order_id = $this->request->get['order_id'];
		
		$amount = $this->request->get['amount'];
		$this->data['token'] = $this->session->data['token'];
		
		$order_info = $this->model_sale_order->getOrder($order_id);
		
		$zones = $this->model_localisation_zone->getZones();
		
		
		
		$this->data['order_id']=$order_id;
		$this->data['order_info'] = $order_info;
		
		$this->data['payment_address_1'] = $order_info['payment_address_1'];
		$this->data['payment_city'] = $order_info['payment_city'];
		$this->data['payment_zone'] = $order_info['payment_zone'];
		$this->data['payment_postcode'] = $order_info['payment_postcode'];
		$this->data['amount_owned'] = $this->request->get['amount'];
		$this->data['zones']	= $zones;
		$this->data['months'] = array();
		
		for ($i = 1; $i <= 12; $i++) {
			$this->data['months'][] = array(
				'text'  => strftime('%B', mktime(0, 0, 0, $i, 1, 2000)), 
				'value' => sprintf('%02d', $i)
			);
		}
		
		$today = getdate();

		$this->data['year_expire'] = array();

		for ($i = $today['year']; $i < $today['year'] + 11; $i++) {
			$this->data['year_expire'][] = array(
				'text'  => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)),
				'value' => strftime('%Y', mktime(0, 0, 0, 1, 1, $i)) 
			);
		}
		if (isset($order_info['payment_code'])) {
				$this->data['payment_code'] = $order_info['payment_code'];
			} elseif (strpos($order_info['payment_method'], 'Authorize') !== false) {
				$this->data['payment_code'] = 'authorizenet_aim';
			} else {
				$this->data['payment_code'] = 'xxx';
			}
			$this->data['aat_env'] = $this->config->get('aat_env') ? $this->config->get('aat_env') : '';
			$this->data['aat_merchant_id'] = $this->config->get('authorizenet_aim_login') ? $this->config->get('authorizenet_aim_login') : '';
			$this->data['aat_transaction_key'] = $this->config->get('authorizenet_aim_key') ? $this->config->get('authorizenet_aim_key') : '';
		
		
		$this->data['ppat_env'] = $this->config->get('paypal_express_test')==0 ? 'live' : 'sandbox';
				$this->data['ppat_api_user'] = $this->config->get('paypal_express_apiuser') ? $this->config->get('paypal_express_apiuser') : '';
			$this->data['ppat_api_pass'] = $this->config->get('paypal_express_apipass') ? $this->config->get('paypal_express_apipass') : '';
			$this->data['ppat_api_sig']  = $this->config->get('paypal_express_apisig')  ? $this->config->get('paypal_express_apisig')  : '';
		
		
		$this->load->model('sale/credit_reason');
		
		$reasons = $this->model_sale_credit_reason->getReasons();
		
		foreach($reasons as $reason)
		{
			if($reason['status'] == 1)
			{
				$this->data['reasons'][] = array(
				'reason_id'=>$reason['reason_id'],
				'name'	=>	$reason['name'],
				'code'	=>	$reason['code']
						);
				
			$canned_message[] = array(
			'reason_id'=>$reason['reason_id'],
			'message'=>$reason['message']);	
			
			$reason_codes[] = array(
			'reason_id'=>$reason['reason_id'],
			'code'=>$reason['code']);	
			
			}
			
			
			
		}
		
		$this->data['canned_messages'] = $this->getJSON($canned_message);
		$this->data['reason_codes'] = $this->getJSON($reason_codes);
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			$this->data['store_url'] = HTTPS_CATALOG;
		} else {
			$this->data['store_url'] = HTTP_CATALOG;
		}
		
		
		
		$order_total = $this->model_sale_order->getOrderTotals($order_id);
			
			foreach($order_total as $order_total)
			{
				
				if($order_total['code']=='shipping')
				{
				
				$this->data['shipping_price'] = $order_total['value'];	
				}
				
			}
		
		
		$this->template = 'sale/get_resolution.tpl';
		
				
		$this->response->setOutput($this->render());
		
		}
	
	
	public function voucher_payment()
	{
		
		$this->language->load('sale/voucher');
		$this->load->model('sale/voucher');
		$this->load->model('sale/order');
		
		$order_id = $this->request->get['order_id'];
		if(!isset($this->request->get['order_code_type']))
		{
			$order_code_type= 'S';	
		}
		else
		{
			$order_code_type= $this->request->get['order_code_type'];	
			
		}
		$code = $order_id.$order_code_type;
		$amount = $this->request->post['generate_gv'];
		
    	$data = array();
		
		$order_info = $this->model_sale_order->getOrder($order_id);
		
		$order_products = $this->model_sale_order->getOrderProducts($order_id);
		
		$product_ids = array();
		foreach($order_products as $order_product)
		{
			for($i=1;$i<=$order_product['quantity'];$i++)
			{
				$product_ids[] =$order_product['product_id'].'-'.$order_product['price'];
			}
			
		}
		
		
		$data['code'] = $code;
		$data['voucher_theme_id'] = 8;
		$data['message'] = $this->request->post['message'];
		$data['amount'] = $amount;
		$data['status'] = 1;
		$data['order_id'] = $order_id;
		$data['product_items'] = $product_ids;
		$data['to_name'] = $order_info['firstname'].' '.$order_info['lastname'];
		$data['to_email'] = $order_info['email'];
		$data['reason'] = 'S';
		$data['credit_shipping'] = 1;
		
		$voucher_id = $this->model_sale_voucher->addVoucher($data);
		
			
				$this->model_sale_voucher->sendVoucher($voucher_id);
				
				$this->load->model('sale/order');
	$this->model_sale_order->updateOrderProductAndHistory($order_id);
				
				$json = array();
				
				$json['success'] = 'Voucher # '.$code.' has been made of amount $'.$amount.' and sent to customer.';	
				
				$this->response->setOutput(json_encode($json));		
				
				
			
		
	}
	public function send() {
    	$this->language->load('sale/voucher');
		
		$json = array();
    	
     	if (!$this->user->hasPermission('modify', 'sale/voucher')) {
      		$json['error'] = $this->language->get('error_permission'); 
    	} elseif (isset($this->request->get['voucher_id'])) {
			$this->load->model('sale/voucher');
			
			$this->model_sale_voucher->sendVoucher($this->request->get['voucher_id']);
			
			$json['success'] = $this->language->get('text_sent');
		}	
		
		$this->response->setOutput(json_encode($json));			
  	}	
	
	public function send2($voucher_id) {
    	$this->language->load('sale/voucher');
		
		$json = array();
    	
     	if (!$this->user->hasPermission('modify', 'sale/voucher')) {
      		return false;
    	} elseif (isset($voucher_id)) {
			$this->load->model('sale/voucher');
			
			$this->model_sale_voucher->sendVoucher($voucher_id);
			
			return true;
		}	
		
				
  	}	
}
?>