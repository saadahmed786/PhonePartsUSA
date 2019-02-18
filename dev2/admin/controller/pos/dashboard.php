<?php

class ControllerPosDashboard extends Controller {
	private $error = array(); 
	
        private function getStoreId() {
		if (isset($this->request->get['store_id'])) {
			$store_id = $this->request->get['store_id'];
		} else {
			$store_id = 0;
			// get the default store id
			$this->load->model('setting/store');
			$stores = $this->model_setting_store->getStores();
			if (!empty($stores)) {
				$store_id = $stores[0]['store_id'];
			}
		}
		return $store_id;
	}
	
        public function index(){
			
            
            $this->currency->set($this->config->get('config_currency'));
            
            $this->load->model('pos/pos');
            $this->language->load('pos/pos');
            
            $this->document->setTitle($this->language->get('heading_title')); 
            
            //add metro css, js             
            $this->document->addScript('view/javascript/pos/fancybox/jquery.fancybox.pack.js');
            $this->document->addStyle('view/stylesheet/pos/style.css');
            $this->document->addStyle('view/javascript/pos/fancybox/jquery.fancybox.css');
                   
            $this->data['token'] = $this->session->data['token'];
            $data = array();
			if(isset($this->request->get['filter_date_start']) && $this->request->get['filter_date_start']!='' )
			{
			
			$this->data['filter_date_start'] = date('Y-m-d',strtotime($this->request->get['filter_date_start']));
			}
			else
			{
				
				$this->data['filter_date_start'] = date('Y-m-d');	
			}
			
			
			if(isset($this->request->get['filter_date_end']) && $this->request->get['filter_date_end']!='' )
			{
			
			$this->data['filter_date_end'] = date('Y-m-d',strtotime($this->request->get['filter_date_end']));
			}
			else
			{
				
				$this->data['filter_date_end'] = date('Y-m-d');	
			}
			
			$rows = $this->model_pos_pos->getStatistics($data);
            $this->data['heading_title'] = $this->language->get('heading_title');
            $this->data['column_username'] = $this->language->get('column_username');
            $this->data['column_name'] = $this->language->get('column_name');
            $this->data['column_cash'] = $this->language->get('column_cash');
            $this->data['column_card'] = $this->language->get('column_card');
            $this->data['column_action'] = $this->language->get('column_action');
            
            $this->data['rows'] = array();
             
            foreach ($rows as $row){                
                $row['total_cash'] = $this->model_pos_pos->get_today_cash($row['user_id'],$this->data['filter_date_start'],$this->data['filter_date_end']);
                $row['total_card'] = $this->model_pos_pos->get_today_card($row['user_id'],$this->data['filter_date_start'],$this->data['filter_date_end']);
				$row['card'] = $this->model_pos_pos->get_month_card($row['user_id'],$this->data['filter_date_start'],$this->data['filter_date_end']);
				$row['cash'] = $this->model_pos_pos->get_month_cash($row['user_id'],$this->data['filter_date_start'],$this->data['filter_date_end']);
				
				$row['paypal_total'] = $this->model_pos_pos->getPaypal($row['user_id'],$this->data['filter_date_start'],$this->data['filter_date_end']);
				
				$row['replacement_orders'] = $this->model_pos_pos->totalReplacementOrders($row['user_id'],$this->data['filter_date_start'],$this->data['filter_date_end']);
				
				$row['replacement_amount'] = ($this->model_pos_pos->replacementAmount($row['user_id'],$this->data['filter_date_start'],$this->data['filter_date_end']));
				
				$row['voucher_used_amount'] = ($this->model_pos_pos->voucherUsedAmount($row['user_id'],$this->data['filter_date_start'],$this->data['filter_date_end']));
				$row['voucher_issued_amount'] = ($this->model_pos_pos->voucherIssuedAmount($row['user_id'],$this->data['filter_date_start'],$this->data['filter_date_end']));
				$row['total_returns'] = $this->model_pos_pos->getTotalReturns($row['user_id'],$this->data['filter_date_start'],$this->data['filter_date_end']);
				
				
				
                $this->data['rows'][] = $row;
            }
			$this->data['total_cash']=$this->currency->format($this->model_pos_pos->getTotalCash($this->data['filter_date_start'],$this->data['filter_date_end']));
			$this->data['total_instore']=$this->currency->format($this->model_pos_pos->getTotalInStore($this->data['filter_date_start'],$this->data['filter_date_end']));
			$this->data['total_paypal']=$this->currency->format($this->model_pos_pos->getTotalPaypal($this->data['filter_date_start'],$this->data['filter_date_end']));
			$this->data['total_store_credit']=$this->currency->format($this->model_pos_pos->getTotalStoreCredit($this->data['filter_date_start'],$this->data['filter_date_end']));
			$this->data['total_replacement']=$this->currency->format($this->model_pos_pos->getTotalReplacement($this->data['filter_date_start'],$this->data['filter_date_end']));
            
            $this->template = 'pos/statistics.tpl';
            $this->children = array(
                'common/header',
                'common/footer'
            );            
            $this->response->setOutput($this->render());
        }
        
        public function withdraw(){        
            
            //validation 
            if(empty($this->request->post['amount']) or $this->request->post['amount']==''){
                $json['error'] = 'Error: Please, enter the amount!';            
                echo json_encode($json);
                die();
            }            
            
            $data = array(
              'user_id' => $this->request->post['user_id'],
              'amount'  => $this->request->post['amount'], 
            );
            
            $this->load->model('pos/pos');
            
            $this->model_pos_pos->withdraw($data);
            
            $json['success'] = 'Success: amount withdrawed from selected user!';
            
            $this->response->setOutput(json_encode($json));
        }
        
        public function history(){
            if(isset($this->request->get['user_id'])){
                $user_id = $this->request->get['user_id'];
            }else{
                $user_id = 0;
            }
            
            if(isset($this->request->get['page'])){
                $page = $this->request->get['page'];
            }else{
                $page = 1;
            }
            
            $limit  =  $this->config->get('config_catalog_limit');
            $offset = ($page-1)*$limit;
            
            $this->load->model('pos/pos');
            $this->language->load('pos/pos');
            
            $this->currency->set($this->config->get('config_currency'));
            
            $this->document->setTitle($this->language->get('heading_title')); 
            
            $this->data['heading_title'] = $this->language->get('heading_title');
            $this->data['column_username'] = $this->language->get('column_username');
            $this->data['column_name'] = $this->language->get('column_name');
            $this->data['column_withdraw'] = $this->language->get('column_withdraw');
            $this->data['column_time'] = $this->language->get('column_time');
            
            $this->data['rows'] = $this->model_pos_pos->history($user_id, $limit, $offset);
            $total = $this->model_pos_pos->total_history($user_id);
            
            $pagination = new Pagination();
            $pagination->total = $total;
            $pagination->page = $page;
            $pagination->limit = $limit;
            $pagination->text = $this->language->get('text_pagination');
            $pagination->url = $this->url->link('pos/dashboard/history', 'token=' . $this->session->data['token'] . '&user_id='.$user_id.'&page={page}', 'SSL');

            $this->data['pagination'] = $pagination->render();
            
            $this->template = 'pos/history.tpl';
            $this->children = array(
                'common/header',
                'common/footer'
            );            
            $this->response->setOutput($this->render());            
        }
        
        public function orderHistory(){
            
            $limit = 1000;
            $this->language->load('pos/pos');            
            $this->document->setTitle($this->language->get('heading_title')); 
            $this->data['heading_title'] = $this->language->get('heading_title');
            $this->data['token'] = $this->session->data['token'];
            
            if(isset($this->request->get['user_id'])){
                $filter_user_id = $this->request->get['user_id'];
            }else{
                $filter_user_id = 0;
            }
            
            if (isset($this->request->get['filter_order_id'])) {
                    $filter_order_id = $this->request->get['filter_order_id'];
            } else {
                    $filter_order_id = null;
            }

            if (isset($this->request->get['filter_customer'])) {
                    $filter_customer = $this->request->get['filter_customer'];
            } else {
                    $filter_customer = null;
            }
			
			 if (isset($this->request->get['filter_payment_method'])) {
                    $filter_payment_method = $this->request->get['filter_payment_method'];
            } else {
                    $filter_payment_method = null;
            }

            if (isset($this->request->get['filter_order_status_id'])) {
                    $filter_order_status_id = $this->request->get['filter_order_status_id'];
            } else {
                    $filter_order_status_id = null;
            }

            if (isset($this->request->get['filter_total'])) {
                    $filter_total = $this->request->get['filter_total'];
            } else {
                    $filter_total = null;
            }

            if (isset($this->request->get['filter_date_added'])) {
                    $filter_date_added = $this->request->get['filter_date_added'];
            } else {
                    $filter_date_added = null;
            }

            if (isset($this->request->get['filter_date_modified'])) {
                    $filter_date_modified = $this->request->get['filter_date_modified'];
            } else {
                    $filter_date_modified = date('Y-m-d');
            }

            if (isset($this->request->get['sort'])) {
                    $sort = $this->request->get['sort'];
            } else {
                    $sort = 'o.order_id';
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
            
            if (isset($this->request->get['filter_order_id'])) {
                    $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
            }

            
            if (isset($this->request->get['filter_order_id'])) {
                    $url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
            }

            if (isset($this->request->get['filter_customer'])) {
                    $url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
            }
			
			if (isset($this->request->get['filter_payment_method'])) {
                    $url .= '&filter_payment_method=' . urlencode(html_entity_decode($this->request->get['filter_payment_method'], ENT_QUOTES, 'UTF-8'));
            }

            if (isset($this->request->get['filter_order_status_id'])) {
                    $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
            }

            if (isset($this->request->get['filter_total'])) {
                    $url .= '&filter_total=' . $this->request->get['filter_total'];
            }

            if (isset($this->request->get['filter_date_added'])) {
                    $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
            }

            if (isset($this->request->get['filter_date_modified'])) {
                    $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
            }
			

            if (isset($this->request->get['sort'])) {
                    $url .= '&sort=' . $this->request->get['sort'];
            }

            if (isset($this->request->get['order'])) {
                    $url .= '&order=' . $this->request->get['order'];
            }

            if (isset($this->request->get['page'])) {
                    $url .= '&page=' . $this->request->get['page'];
            }
            
            $data = array(
                'filter_user_id'         => $filter_user_id,
                'filter_order_id'        => $filter_order_id,
                'filter_customer'	 => $filter_customer,
				'payment_method'=>$filter_payment_method,
                'filter_order_status_id' => $filter_order_status_id,
                'filter_total'           => $filter_total,
                'filter_date_added'      => $filter_date_added,
                'filter_date_modified'   => $filter_date_modified,
                'sort'                   => $sort,
                'order'                  => $order,
                'start'                  => ($page - 1) * $limit,
                'limit'                  => $limit,
				'shipping_code' => 'multiflatrate.multiflatrate_0',
				'picked_up_orders'=>true,
            );
            
            $this->load->model('sale/order');
            
            $order_total = $this->model_sale_order->getTotalOrders($data);
            
            $this->load->model('localisation/order_status');
            $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
            
            $rows = $this->model_sale_order->getOrders($data);
            
            $this->data['rows'] = array();
            
            foreach ($rows as $row){
				if($row['pos_total']!='0.0000') $row['total'] = $row['pos_total'];
				
                $row['total'] = $this->currency->format($row['total']);
				$checkForRefundInvoice = $this->db->query("SELECT order_id,ref_order_id FROM ".DB_PREFIX."order WHERE order_id='".($this->db->escape($row['order_id']))."' ");
				
			$checkForRefundInvoice = $checkForRefundInvoice->row;
			
			if($checkForRefundInvoice) {
			$row['ref_order_id'] = $checkForRefundInvoice['ref_order_id'];
			}
			else
			{
				$row['ref_order_id'] = '';	
			}
			
                $this->data['rows'][] = $row;
            }
            
            $this->data['text_missing'] = 'Missing Orders';
            $this->data['currency_code'] = $this->config->get('config_currency');
	    $this->data['currency_value'] = '1.0';
	    $this->data['store_id'] = $this->getStoreId();
	    $this->data['token'] = $this->session->data['token'];
                
            $pagination = new Pagination();
            $pagination->total = $order_total;
            $pagination->page = $page;
            $pagination->limit = $limit;
            $pagination->text = $this->language->get('text_pagination');
            $pagination->url = $this->url->link('pos/dashboard/orderHistory', 'token=' . $this->session->data['token'] . $url . '&user_id='.$filter_user_id.'&page={page}', 'SSL');

            $this->data['pagination'] = $pagination->render();

            $this->data['filter_order_id'] = $filter_order_id;
            $this->data['filter_customer'] = $filter_customer;
			$this->data['filter_payment_method'] = $filter_payment_method;
            $this->data['filter_order_status_id'] = $filter_order_status_id;
            $this->data['filter_total'] = $filter_total;
            $this->data['filter_date_added'] = $filter_date_added;
            $this->data['filter_date_modified'] = $filter_date_modified;
            
            $this->data['url_order_info'] = $this->url->link('sale/order/info', 'token=' . $this->session->data['token'], 'SSL');
            
            $this->template = 'pos/order_history.tpl';		
            $this->children = array(
                'common/header',
                'common/footer'
            );   
            $this->response->setOutput($this->render());
            
        }
      
        public function getOrder(){

            $this->load->model('sale/order');

            $json = array();

            $this->load->library('customer');
            $this->customer = new Customer($this->registry);

            $this->load->library('tax');//
            $this->tax = new Tax($this->registry);

            $this->load->library('pos_cart');//
            $this->cart = new Pos_cart($this->registry);

            $this->load->model('catalog/product');

            $order_products = $this->model_sale_order->getOrderProducts($this->request->get['order_id']);
            $this->cart->clear();
            foreach ($order_products as $order_product) {
                if (isset($order_product['order_option'])) {
                    $order_option = $order_product['order_option'];
                } elseif (isset($this->request->get['order_id'])) {
                    $order_option = $this->model_sale_order->getOrderOptions($this->request->get['order_id'], $order_product['order_product_id']);
                } else {
                    $order_option = array();
                }

                $this->cart->add($order_product['product_id'], $order_product['quantity'], $order_option);
            }

               //html for cart
            $json['products'] = array();
                      
            foreach ($this->cart->getProducts() as $product) {

                    $option_data = array();

                    foreach ($product['option'] as $option) {
                            if ($option['type'] != 'file') {
                                    $value = $option['option_value'];	
                            } else {
                                    $filename = $this->encryption->decrypt($option['option_value']);

                                    $value = utf8_substr($filename, 0, utf8_strrpos($filename, '.'));
                            }				

                            $option_data[] = array(								   
                                    'name'  => $option['name'],
                                    'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value),
                                    'type'  => $option['type']
                            );
                    }

                    // Display prices
                    if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                            $price = $this->currency->format($product['price']);
                    } else {
                            $price = false;
                    }

                    // Display prices
                    if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                            $total = $this->currency->format($this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity']);
                    } else {
                            $total = false;
                    }

                    $json['products'][] = array(
                            'key'       => $product['key'],
                            'name'      => $product['name'],
                            'model'     => $product['model'], 
                            'option'    => $option_data,
                            'quantity'  => $product['quantity'],
                            'price'     => $price,	
                            'total'     => $total,	
                            'href'      => $this->url->link('product/product', 'product_id=' . $product['product_id']),
                    );
            }//foreach product in cart generate html 

            // Totals
            $this->load->model('pos/extension');

            $total_data = array();					
            $total = 0;
            $taxes = $this->cart->getTaxes();

            // Display prices
            if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
                    $sort_order = array(); 

                    $results = $this->model_pos_extension->getExtensions('total');

                    foreach ($results as $key => $value) {
                            $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
                    }

                    array_multisort($sort_order, SORT_ASC, $results);

                    foreach ($results as $result) {
                            if ($this->config->get($result['code'] . '_status')) {
                                    $this->load->model('pos/' . $result['code']);

                                    $this->{'model_pos_' . $result['code']}->getTotal($total_data, $total, $taxes);
                            }

                            $sort_order = array(); 

                            foreach ($total_data as $key => $value) {
                                    $sort_order[$key] = $value['sort_order'];
                            }

                            array_multisort($sort_order, SORT_ASC, $total_data);			
                    }
            }

            $json['total_data'] = $total_data;
            $json['total'] = $this->currency->format($total);
            
            //customer info             
            $this->load->model('pos/pos');
            $json['customer'] = $this->model_pos_pos->getCustomer['customer_id'];
            $json['order_id'] = $this->request->get['order_id'];
            echo json_encode($json);
        }//get order         
}
?>