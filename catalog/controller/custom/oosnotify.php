<?php

class ControllerCustomOosnotify extends Controller {
	public function index() {
            
            $this->load->model('tool/oosnotify');
            $this->load->model('setting/setting');
			
					
		
			$products = $this->model_tool_oosnotify->getUniqueId();
			
			foreach ($products as $product){
				$product_id = $product['pid'];
				$stockstatus = $this->model_tool_oosnotify->getStockStatus($product_id);
				$qty = $stockstatus['quantity'];
				$status = $stockstatus['stock_status_id'];
				
				//if (($qty > 0) and ($status == 5)){
				if ($qty > 0){
					$emaillists = $this->model_tool_oosnotify->getemail($product_id);
					foreach ($emaillists as $emaillist){
						$customer_email = $emaillist['email'];
						$oosn_id = $emaillist['oosn_id'];
						$customer_language_id = $emaillist['language_id'];
						
						$product_details = $this->model_tool_oosnotify->getProductDetails($product_id,$customer_language_id);
						$pname = $product_details['name'];
						$store_id = $this->model_tool_oosnotify->getProductStore($product_id);
						$store_url = $this->model_tool_oosnotify->getStoreUrl($store_id);
                        
                        if(empty($store_url)){
                            $query = $this->db->query("SELECT `value` FROM " . DB_PREFIX . "setting WHERE store_id = '".$store_id."' and `key`='config_url_oosn'");
    	                    $store_url = $query->row['value'];
                        }
						$link = $store_url.'index.php?route=product/product&product_id='.$product_id;
						$pmodel = $product_details['model'];
						$pimage = $product_details['image'];
						
						if (!empty($pimage)){
							$pimagelink = $store_url.'/image/'.$pimage;
						} else{
							$pimagelink = $store_url.'/image/no_image.jpeg';
						}
						$text = $this->config->get('oosn_customer_success_msg');
						
		$mail_body = $this->config->get('oosn_customer_mail_body'.$customer_language_id);
		$mail_body = str_replace("{product_name}",$pname,$mail_body);
		$mail_body = str_replace("{model}",$pmodel,$mail_body);
		$mail_body = str_replace("{image}",$pimagelink,$mail_body);
		$mail_body = str_replace("{link}",$link,$mail_body);
		
		$message  = '<html dir="ltr" lang="en">' . "\n";
		$message .= '  <head>' . "\n";
		$message .= '    <title>' . $this->request->post['subject'] . '</title>' . "\n";
		$message .= '    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">' . "\n";
		$message .= '  </head>' . "\n";
		$message .= '  <body>' . html_entity_decode($mail_body, ENT_QUOTES, 'UTF-8') . '</body>' . "\n";
		$message .= '</html>' . "\n";

					
		$mail = new Mail();	
		$mail->protocol = $this->config->get('config_mail_protocol');
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->hostname = $this->config->get('config_smtp_host');
		$mail->username = $this->config->get('config_smtp_username');
		$mail->password = $this->config->get('config_smtp_password');
		$mail->port = $this->config->get('config_smtp_port');
		$mail->timeout = $this->config->get('config_smtp_timeout');				
		$mail->setTo($customer_email);
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender($this->config->get('config_name'));//Sender
		$mail->setSubject(html_entity_decode($this->config->get('oosn_customer_mail_sub'.$customer_language_id), ENT_QUOTES, 'UTF-8'));					
		$mail->setHtml($message);
		$mail->send();
		
						$this->model_tool_oosnotify->updatenotifieddate($oosn_id);
					}
		//			$this->data['success'] = $this->language->get('text_email_success');
				}
				
			}
			

       /* $this->data['total_alert'] = $this->model_setting_oosnotify->getTotalAlert();
        $this->data['total_responded'] = $this->model_setting_oosnotify->getTotalResponded();
        $this->data['customer_notified'] = $this->model_setting_oosnotify->getCustomerNotified();
        $this->data['awaiting_notification'] = $this->model_setting_oosnotify->getAwaitingNotification();
        $this->data['product_requested'] = $this->model_setting_oosnotify->getTotalRequested();

		$data = array(
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);

		if (isset($this->request->get['filteroption'])) {
    		$filteroption = $this->request->get['filteroption'];
            $this->data['current_report'] = strtoupper($filteroption); 
		}else {
			$filteroption = array();
    	    $this->data['current_report'] = strtoupper('ALL'); 
		}
		$reports_total = $this->model_setting_oosnotify->getTotalReports($filteroption); 
		
		$this->data['products'] = array();

		$results = $this->model_setting_oosnotify->getReports($data,$filteroption);

		foreach ($results as $result) {
			
			$this->data['products'][] = array(
				'product_id'    => $result['product_id'],
				'name'   => $result['name'],
				'email'   => $result['email'],
				'language_code'   => $result['language_code'],
				'enquiry_date'  => $result['enquiry_date'],
				'notify_date'  => $result['notified_date'],
                'product_link' => $this->url->link('catalog/product/update&product_id='.$result['product_id'], 'token=' . $this->session->data['token'], 'SSL')
			);
		}
				
		$this->data['text_no_results'] = $this->language->get('text_no_results');
		$this->data['link_to_setting'] = $this->url->link('extension/oosnotify/setting', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['column_product_id'] = $this->language->get('column_product_id');
		$this->data['column_product_name'] = $this->language->get('column_product_name');
		$this->data['column_email'] = $this->language->get('column_email');
		$this->data['column_language'] = $this->language->get('column_language');
		$this->data['column_enquiry_date'] = $this->language->get('column_enquiry_date');
		$this->data['column_notify_date'] = $this->language->get('column_notify_date');
		$this->data['current_page'] = $this->url->link('extension/oosnotify', 'token=' . $this->session->data['token'], 'SSL');
		
		$this->data['text_notify_customers']			= $this->language->get('text_notify_customers');
		$this->data['text_total_alert']					= $this->language->get('text_total_alert');
		$this->data['text_total_responded']				= $this->language->get('text_total_responded');
		$this->data['text_show_all_reports']			= $this->language->get('text_show_all_reports');
		$this->data['text_reset_all']					= $this->language->get('text_reset_all');
		$this->data['text_customers_awaiting_notification']		= $this->language->get('text_customers_awaiting_notification');
		$this->data['text_number_of_products_demanded']			= $this->language->get('text_number_of_products_demanded');
		$this->data['text_show_awaiting_reports']				= $this->language->get('text_show_awaiting_reports');
		$this->data['text_reset_awaiting']						= $this->language->get('text_reset_awaiting');
		$this->data['text_archive_records']						= $this->language->get('text_archive_records');
		$this->data['text_customers_notified']					= $this->language->get('text_customers_notified');
		$this->data['text_show_archive_reports']				= $this->language->get('text_show_archive_reports');
		$this->data['text_reset_archive']						= $this->language->get('text_reset_archive');
		$this->data['text_reports']								= $this->language->get('text_reports');
		$this->data['text_current_report_all']					= $this->language->get('text_current_report_all');
		$this->data['text_current_report_awaiting']				= $this->language->get('text_current_report_awaiting');
		$this->data['text_current_report_archive']				= $this->language->get('text_current_report_archive');
		$this->data['text_product_in_demand']					= $this->language->get('text_product_in_demand');
		$this->data['column_count']   							= $this->language->get('column_count');

		$url = '';		

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$pagination = new Pagination();
		$pagination->total = $reports_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('extension/oosnotify', 'token=' . $this->session->data['token'] . '&page={page}', 'SSL');

		$this->data['pagination'] = $pagination->render();
		$this->data['demands'] = $this->model_setting_oosnotify->getDemandedList();
		*/
            
	}
}
?>