<?php 
class ControllerWgicronOosncron extends Controller {
	public function index(){
		
		$this->load->model('wgi/oosnotify');
		
			$products = $this->model_wgi_oosnotify->getUniqueId();
			foreach ($products as $product){ //2S
				$product_id = $product['pid'];
				$stockstatus = $this->model_wgi_oosnotify->getStockStatus($product_id);
				$qty = $stockstatus['quantity'];
				$status = $stockstatus['stock_status_id'];
				
				//if (($qty > 0) and ($status == 5)){
				if ($qty > 0){ //3S
					$emaillists = $this->model_wgi_oosnotify->getemail($product_id);
					foreach ($emaillists as $emaillist){ //4S
						$customer_email = $emaillist['email'];
						$oosn_id = $emaillist['oosn_id'];
						$customer_language_id = $emaillist['language_id'];
						
						$product_details = $this->model_wgi_oosnotify->getProductDetails($product_id,$customer_language_id);
						$pname = $product_details['name'];
						$store_id = $this->model_wgi_oosnotify->getProductStore($product_id);
						//$store_url = $this->model_setting_oosnotify->getStoreUrl($store_id);
						// if you have multi store enabled, uncomment the above line
						$store_url = '';
                        
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
						$message .= '    <title>' . $this->config->get('oosn_customer_mail_sub'.$customer_language_id) . '</title>' . "\n";
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
						$this->model_wgi_oosnotify->updatenotifieddate($oosn_id);
					} //4E
				} // 3E
				
			}// 2E
	
    }
}
?>