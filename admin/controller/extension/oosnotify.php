<?php
class ControllerExtensionOosNotify extends Controller{ 
    public function index(){
        $this->language->load('extension/oosnotify');

		$this->document->setTitle($this->language->get('heading_title')); 
		
		$this->data['heading_title'] = $this->language->get('heading_title');
		$this->data['button_customise']	= $this->language->get('button_customise');
        $template="extension/oosnotify.tpl"; 
		
		
        $this->load->model('setting/oosnotify');
		
		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/oosnotify', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);
		
		$this->data['notinstalled'] = '1';
		if ($this->model_setting_oosnotify->CheckInstall()==false){
			$this->data['notinstalled'] = 0;
			//$this->model_setting_oosnotify->install();
			$this->data['installed'] = '<div class="warning">This Extension is not installed. Go to settings page and install.<form action="'.$this->url->link('extension/oosnotify/setting', 'token=' . $this->session->data['token'], 'SSL').'" method="post"><input type="submit" class="button" value="SETTINGS"></form></div>';
		} else {
			
			$this->data['installed'] = '';
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}
				
		if(isset($this->request->get['delete'])){    
            $delete = $this->request->get['delete'];
			$this->model_setting_oosnotify->deleteRecords($delete);
			$this->data['success'] = $this->language->get('text_reset_success');
		}
		
		if(isset($this->request->post['sendemail'])){		
			$products = $this->model_setting_oosnotify->getUniqueId();
			foreach ($products as $product){
				$product_id = $product['pid'];
				$stockstatus = $this->model_setting_oosnotify->getStockStatus($product_id);
				$qty = $stockstatus['quantity'];
				$status = $stockstatus['stock_status_id'];
				
				//if (($qty > 0) and ($status == 5)){
				if ($qty > 0){
					$emaillists = $this->model_setting_oosnotify->getemail($product_id);
					
					foreach ($emaillists as $emaillist){
						$customer_email = $emaillist['email'];
						$oosn_id = $emaillist['oosn_id'];
						$customer_language_id = $emaillist['language_id'];
						
						$product_details = $this->model_setting_oosnotify->getProductDetails($product_id,$customer_language_id);
						$pname = $product_details['name'];
						$store_id = $this->model_setting_oosnotify->getProductStore($product_id);
						$store_url = $this->model_setting_oosnotify->getStoreUrl($store_id);
                        
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
		
						$this->model_setting_oosnotify->updatenotifieddate($oosn_id);
					}
					$this->data['success'] = $this->language->get('text_email_success');
				}
				
			}
			
		}

        $this->data['total_alert'] = $this->model_setting_oosnotify->getTotalAlert();
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
				'sku'	=> $this->model_setting_oosnotify->getProductSKU($result['product_id']),
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
		$this->data['column_sku'] = $this->language->get('column_sku');
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
		} // end of installation check
		
        $this->template = ''.$template.'';
        $this->children = array(
            'common/header',
            'common/footer'
        );      
        $this->response->setOutput($this->render());
    }
	
	public function setting(){
        $this->language->load('extension/oosnotify');

		$this->document->setTitle($this->language->get('heading_title_setting')); 
		
		$this->data['heading_title_setting'] = $this->language->get('heading_title_setting');
		
        $template="extension/oosnotify_settings.tpl"; // .tpl location and file

        $this->load->model('setting/oosnotify');
		
		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/oosnotify', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);
		
		if (isset($_POST['install'])){
			$this->model_setting_oosnotify->install();
			$this->redirect($this->url->link('extension/oosnotify/setting', 'token=' . $this->session->data['token'], 'SSL'));
		}
		if (isset($_POST['uninstall'])){
			$this->model_setting_oosnotify->uninstall();
			$this->redirect($this->url->link('extension/oosnotify/setting', 'token=' . $this->session->data['token'], 'SSL'));
		}
		
		if ($this->model_setting_oosnotify->CheckInstall()==false){
			$this->data['installed'] = '<div class="warning">This Extension is not installed. <form action="" method="post"><input type="submit" class="button" name="install" value="INSTALL"></form></div>';
		}else{
			$this->data['installed'] = '<div class="success">This Extension is installed.</div><br><input type="submit" class="button" name="uninstall" value="UNINSTALL">';
		}
		
		$this->data['counturl'] = $this->model_setting_oosnotify->countStoreUrl();
			if (isset($_POST['inserturl'])){
			$this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`store_id`, `group`, `key`, `value`, `serialized`) VALUES ('0', 'notify_out_stock', 'config_url_oosn', '".$_POST['url']."', '0'); ");
			$this->redirect($this->url->link('extension/oosnotify/setting', 'token=' . $this->session->data['token'], 'SSL'));
			}

		$this->data['success']	='';
		$this->data['href'] = $this->url->link('extension/oosnotify','token=' . $this->session->data['token'], 'SSL');
		$this->data['button_save']	= $this->language->get('button_save');
		$this->data['entry_text']	= $this->language->get('entry_text');
		$this->data['entry_success_msg']	= $this->language->get('entry_success_msg');
		$this->data['entry_store_subject']	= $this->language->get('entry_store_subject');
		$this->data['entry_store_body']	= $this->language->get('entry_store_body');
		
		$this->data['entry_customer_subject']	= $this->language->get('entry_customer_subject');
		$this->data['entry_customer_body']	= $this->language->get('entry_customer_body');
		
		$this->data['email_to_store']	= $this->language->get('email_to_store');
		$this->data['email_to_customer']	= $this->language->get('email_to_customer');
		$this->data['installation']	= $this->language->get('installation');
		
		$this->data['languages']	=	$this->model_setting_oosnotify->getLanguages();
		
		
		if (isset($_POST['save'])){
			$store_subject = $this->request->post['store_subject'];
			$store_body = $this->request->post['store_body'];
			
			$store_subject = addslashes($store_subject);
			$store_body = addslashes($store_body);
			
			$this->model_setting_oosnotify->updateSettings('oosn_store_mail_sub', $store_subject);
			$this->model_setting_oosnotify->updateSettings('oosn_store_mail_body', $store_body);
			
			$languages = $this->data['languages'];
			foreach ($languages as $language){
				$customer_subject = $this->request->post['customer_subject_'.$language['language_id']];
				$customer_body = $this->request->post['customer_body_'.$language['language_id']];
				$customer_subject = addslashes($customer_subject);
				$customer_body = addslashes($customer_body);
				
				$this->model_setting_oosnotify->updateSettings('oosn_customer_mail_sub'.$language['language_id'], $customer_subject);
				$this->model_setting_oosnotify->updateSettings('oosn_customer_mail_body'.$language['language_id'], $customer_body);		
			}
			
			$href = $this->url->link('extension/oosnotify/setting', 'token=' . $this->session->data['token'], 'SSL');
			$this->data['success']	='Saved Successfully. <a href="'.$href.'" class="button">Refresh this page</a> ';
		}
		
		$this->data['store_subject'] = $this->config->get('oosn_store_mail_sub');
		$this->data['store_body'] = $this->config->get('oosn_store_mail_body');
		
 		$this->template = ''.$template.'';
        $this->children = array(
            'common/header',
            'common/footer'
        );      
        $this->response->setOutput($this->render());
    }
	
}
?>