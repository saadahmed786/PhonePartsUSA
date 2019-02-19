<?php
class ModelSaleVoucher extends Model {
	public $privateSql;
	public function addVoucher($data) {
      	/*$this->db->query("INSERT INTO " . DB_PREFIX . "voucher SET code = '" . $this->db->escape($data['code']) . "', from_name = '" . $this->db->escape($data['from_name']) . "', from_email = '" . $this->db->escape($data['from_email']) . "', to_name = '" . $this->db->escape($data['to_name']) . "', to_email = '" . $this->db->escape($data['to_email']) . "', voucher_theme_id = '" . (int)$data['voucher_theme_id'] . "', message = '" . $this->db->escape($data['message']) . "', amount = '" . (float)$data['amount'] . "', status = '" . (int)$data['status'] . "', date_added = NOW()");*/
		
		if(!isset($data['credit_shipping'])) $data['credit_shipping']=0;
		
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "voucher SET code = '" . $this->db->escape($data['code']) . "', voucher_theme_id = '8', message = '" . $this->db->escape($data['message']) . "', amount = '" . (float)$data['amount'] . "', status = '" . (int)$data['status'] . "',product_ids='".implode(",",$data['product_items'])."',order_id='".$data['order_id']."', date_added = NOW(),from_name='".$this->config->get('config_name')."',from_email='".$this->config->get('config_email')."',to_name='".$data['to_name']."',to_email='".$data['to_email']."',reason='".$data['reason']."',voucher_items_reasons='".$data['voucher_items_reasons']."',credit_shipping='".(int)$data['credit_shipping']."',user_id='".(int)$this->user->getId()."'");
		
		$voucher_id = $this->db->getLastId();
		
		
		$this->db->query("INSERT INTO `".DB_PREFIX."order_voucher` SET order_id='".$data['order_id']."',voucher_id='".$voucher_id."',description='$".number_format($data['amount'],2)." Store Credit for ".$data['to_name']."',code='".$this->db->escape($data['code'])."', from_name='".$this->db->escape($this->config->get('config_name'))."',from_email='".$this->db->escape($this->config->get('config_email'))."',to_name='".$this->db->escape($data['to_name'])."',to_email='".$this->db->escape($data['to_email'])."',voucher_theme_id=8,message='".$this->db->escape($data['message'])."',amount='".(float)$data['amount']."'");
		
		
		$this->load->model('sale/order');
		
		$previous_status_query = $this->db->query("SELECT order_status_id FROM ".DB_PREFIX."order WHERE order_id='".(int)$data['order_id']."'");
		$previous_status = $previous_status_query->row;
		
		
		$xData = array();
		$xData['order_status_id']=$previous_status['order_status_id'];
		$xData['notify']=0;
		$xData['comment'] = $this->db->escape($data['message']);
		$xData['store_credit'] = 1;
		$xData['code'] =$this->db->escape($data['code']);
		$xData['amount'] = (float)$data['amount'];
		
		
		
		$this->model_sale_order->addOrderHistory($data['order_id'],$xData);
		
		return $voucher_id;
		
		
	}
	public function addVoucherProduct($data) {
		$this->db->query("INSERT INTO inv_voucher_products SET voucher_id='".$data['voucher_id']."',order_id='".$data['order_id']."',rma_number='".$data['rma_number']."',sku='".$data['sku']."',price='".$data['price']."',reason='".$data['reason']."'");
		return "Success";
	}
	
	public function editVoucher($voucher_id, $data) {
      /*	$this->db->query("UPDATE " . DB_PREFIX . "voucher SET code = '" . $this->db->escape($data['code']) . "', from_name = '" . $this->db->escape($data['from_name']) . "', from_email = '" . $this->db->escape($data['from_email']) . "', to_name = '" . $this->db->escape($data['to_name']) . "', to_email = '" . $this->db->escape($data['to_email']) . "', voucher_theme_id = '" . (int)$data['voucher_theme_id'] . "', message = '" . $this->db->escape($data['message']) . "', amount = '" . (float)$data['amount'] . "', status = '" . (int)$data['status'] . "' WHERE voucher_id = '" . (int)$voucher_id . "'");*/
	  
	  
	  if(!isset($data['credit_shipping'])) $data['credit_shipping']=0;
	  
	  
	  
	  $this->db->query("UPDATE " . DB_PREFIX . "voucher  SET code = '" . $this->db->escape($data['code']) . "', voucher_theme_id = '8', message = '" . $this->db->escape($data['message']) . "', amount = '" . (float)$data['amount'] . "', status = '" . (int)$data['status'] . "',product_ids='".implode(",",$data['product_items'])."',order_id='".$data['order_id']."', date_added = NOW(),from_name='".$this->config->get('config_name')."',from_email='".$this->config->get('config_email')."',to_name='".$data['to_name']."',to_email='".$data['to_email']."',reason='".$data['reason']."',credit_shipping='".(int)$data['credit_shipping']."' WHERE voucher_id = '" . (int)$voucher_id . "'");
	  
	  
	  
	  $this->db->query("UPDATE `".DB_PREFIX."order_voucher` SET order_id='".$data['order_id']."',voucher_id='".$voucher_id."',description='$".number_format($data['amount'],2)." Store Credit for ".$data['to_name']."',code='".$this->db->escape($data['code'])."', from_name='".$this->db->escape($this->config->get('config_name'))."',from_email='".$this->db->escape($this->config->get('config_email'))."',to_name='".$this->db->escape($data['to_name'])."',to_email='".$this->db->escape($data['to_email'])."',voucher_theme_id=8,message='".$this->db->escape($data['message'])."',amount='".(float)$data['amount']."' WHERE voucher_id='".(int)$voucher_id."'");
	  
	  
	}
	
	public function deleteVoucher($voucher_id) {
      	$this->db->query("DELETE FROM " . DB_PREFIX . "voucher WHERE voucher_id = '" . (int)$voucher_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "voucher_history WHERE voucher_id = '" . (int)$voucher_id . "'");
	}
	
	public function getVoucher($voucher_id) {
      	$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "voucher WHERE voucher_id = '" . (int)$voucher_id . "'");
		
		return $query->row;
	}

	public function getVoucherByCode($code) {
      	$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "voucher WHERE code = '" . $this->db->escape($code) . "'");
		
		
		return $query->row;
	}
	
	public function getVoucherByOrderID($order_id) {
	
      	$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "voucher WHERE order_id = '" . $this->db->escape($order_id) . "'");
		
		return $query->row;
	}
		
	public function getVouchers($data = array()) {
		$sql = "SELECT v.voucher_id, v.code, v.from_name,v.user_id, v.from_email, v.to_name, v.to_email, (SELECT vtd.name FROM " . DB_PREFIX . "voucher_theme_description vtd WHERE vtd.voucher_theme_id = v.voucher_theme_id AND vtd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS theme, v.amount, v.status, v.date_added FROM " . DB_PREFIX . "voucher v";
		$sqlPrivate = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "voucher";

		$implode = array();
		
		if (!empty($data['filter_code'])) {
			$implode[] = "LCASE(v.code) LIKE '" . $this->db->escape(utf8_strtolower($data['filter_code'])) . "%'";
		}
		
		if (!empty($data['filter_email'])) {
			$implode[] = "LCASE(v.to_email) LIKE '" . $this->db->escape(utf8_strtolower($data['filter_email'])) . "%'";
		}
	
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "v.status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_date']) && !is_null($data['filter_date'])) {
			$implode[] = "v.date_added > DATE_SUB(now(), INTERVAL " . $data['filter_date'] . ")";
		}
				
		// if (!empty($data['filter_date_added'])) {
		// 	$implode[] = "DATE(v.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		// }
		
		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
			$sqlPrivate .= " WHERE " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'v.code',
			'v.from_name',
			'v.from_email',
			'v.to_name',
			'v.to_email',
			'v.theme',
			'v.amount',
			'v.status',
			'v.date_added'
		);	
			
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];	
		} else {
			$sql .= " ORDER BY v.date_added";
		}
			
		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}			

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}	
			
			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}		
		// echo $sql; exit;
		$query = $this->db->query($sql);
		
		return $query->rows;
	}
		
	public function sendVoucher($voucher_id) {
		$voucher_info = $this->getVoucher($voucher_id);
		
		if ($voucher_info) {
			if ($voucher_info['order_id']) {
				$order_id = $voucher_info['order_id'];
			} else {
				$order_id = 0;
			}
			
			$this->load->model('sale/order');
			
			$order_info = $this->model_sale_order->getOrder($order_id);
			
			// If voucher belongs to an order
			if ($order_info) {
				$this->load->model('localisation/language');
				
				$language = new Language($order_info['language_directory']);
				$language->load($order_info['language_filename']);	
				$language->load('mail/voucher');
				
				// HTML Mail
				$template = new Template();				
				
				$template->data['title'] = sprintf($language->get('text_subject'), $voucher_info['from_name']);
				
				$template->data['text_greeting'] = sprintf($language->get('text_greeting'), $this->currency->format($voucher_info['amount'], $order_info['currency_code'], $order_info['currency_value']));
				$template->data['text_from'] = sprintf($language->get('text_from'), $voucher_info['from_name']);
				$template->data['text_message'] = $language->get('text_message');
				$template->data['text_redeem'] = sprintf($language->get('text_redeem'), $voucher_info['code']);
				$template->data['text_footer'] = sprintf($language->get('text_footer'),$order_info['firstname'],$this->currency->format($voucher_info['amount'], $order_info['currency_code'], $order_info['currency_value']));	
				$template->data['text_footer2'] = $language->get('text_footer2');	
				$template->data['text_main'] = $language->get('text_main');
				$template->data['text_secret_code'] = sprintf($language->get('text_secret_code'), $voucher_info['code']);
				
				$this->load->model('sale/voucher_theme');
					
				$voucher_theme_info = $this->model_sale_voucher_theme->getVoucherTheme($voucher_info['voucher_theme_id']);
				
				/*if ($voucher_info && file_exists(DIR_IMAGE . $voucher_theme_info['image'])) {
					$template->data['image'] = HTTP_IMAGE . $voucher_theme_info['image'];
				} else {
					$template->data['image'] = '';
				}*/
				if ($this->config->get('config_logo') && file_exists(DIR_IMAGE . $this->config->get('config_logo'))) {
			$template->data['image'] = HTTP_IMAGE . $this->config->get('config_logo');
		} else {
			$template->data['image'] = '';
		}
		$var = HTTP_SERVER."view/image/store_credit.jpg";
		$template->data['store_credit_image'] = $var;
				
				
				
				$this->load->model('localisation/canned_messages');
				$voucher_info['message']=$this->model_localisation_canned_messages->orderMergeMessage($voucher_info['message'], $order_info['order_id']);
				
				
				
				$template->data['store_name'] = $order_info['store_name'];
				$template->data['store_url'] = $order_info['store_url'];
				$template->data['message'] = nl2br($voucher_info['message']);
	
	
	
	
				$mail = new Mail(); 
				$mail->protocol = $this->config->get('config_mail_protocol');
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->hostname = $this->config->get('config_smtp_host');
				$mail->username = $this->config->get('config_smtp_username');
				$mail->password = $this->config->get('config_smtp_password');
				$mail->port = $this->config->get('config_smtp_port');
				$mail->timeout = $this->config->get('config_smtp_timeout');			
				$mail->setTo($voucher_info['to_email']);
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender($order_info['store_name']);
				$mail->setSubject(html_entity_decode(sprintf($language->get('text_subject'),$order_info['firstname'], $voucher_info['from_name']), ENT_QUOTES, 'UTF-8'));
				$mail->setHtml($template->fetch('mail/voucher.tpl'));				
				$mail->send();
			
			// If voucher does not belong to an order				
			}  else {
				$this->language->load('mail/voucher');
				
				$template = new Template();		
				
				$template->data['title'] = sprintf($this->language->get('text_subject'), $voucher_info['from_name']);
				
				$template->data['text_greeting'] = sprintf($this->language->get('text_greeting'), $this->currency->format($voucher_info['amount'], $order_info['currency_code'], $order_info['currency_value']));
				$template->data['text_from'] = sprintf($this->language->get('text_from'), $voucher_info['from_name']);
				$template->data['text_message'] = $this->language->get('text_message');
				$template->data['text_redeem'] = sprintf($this->language->get('text_redeem'), $voucher_info['code']);
				$template->data['text_footer'] = $this->language->get('text_footer');					
			
				$this->load->model('sale/voucher_theme');
					
				$voucher_theme_info = $this->model_sale_voucher_theme->getVoucherTheme($voucher_info['voucher_theme_id']);
				
				if ($voucher_info && file_exists(DIR_IMAGE . $voucher_theme_info['image'])) {
					$template->data['image'] = HTTP_IMAGE . $voucher_theme_info['image'];
				} else {
					$template->data['image'] = '';
				}
				
				$template->data['store_name'] = $this->config->get('config_name');
				$template->data['store_url'] = HTTP_CATALOG;
				$template->data['message'] = nl2br($voucher_info['message']);
	
				$mail = new Mail(); 
				$mail->protocol = $this->config->get('config_mail_protocol');
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->hostname = $this->config->get('config_smtp_host');
				$mail->username = $this->config->get('config_smtp_username');
				$mail->password = $this->config->get('config_smtp_password');
				$mail->port = $this->config->get('config_smtp_port');
				$mail->timeout = $this->config->get('config_smtp_timeout');			
				$mail->setTo($voucher_info['to_email']);
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender($this->config->get('config_name'));
				$mail->setSubject(html_entity_decode(sprintf($this->language->get('text_subject'), $voucher_info['from_name']), ENT_QUOTES, 'UTF-8'));
				$mail->setHtml($template->fetch('mail/voucher.tpl'));
				$mail->send();				
			}
		}
	}
			
	public function getTotalVouchers($data) {
		$sqlPrivate = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "voucher";

		$implode = array();
		
		if (!empty($data['filter_code'])) {
			$implode[] = "LCASE(code) LIKE '" . $this->db->escape(utf8_strtolower($data['filter_code'])) . "%'";
		}
		
		if (!empty($data['filter_email'])) {
			$implode[] = "LCASE(to_email) LIKE '" . $this->db->escape(utf8_strtolower($data['filter_email'])) . "%'";
		}
	
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$implode[] = "status = '" . (int)$data['filter_status'] . "'";
		}
				
		// if (!empty($data['filter_date_added'])) {
		// 	$implode[] = "DATE(v.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		// }
		
		if ($implode) {
			$sqlPrivate .= " WHERE " . implode(" AND ", $implode);
		}
		
		$query = $this->db->query($sqlPrivate);
      	//$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "voucher");
		
		return $query->row['total'];
	}	
	
	public function getTotalVouchersByVoucherThemeId($voucher_theme_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "voucher WHERE voucher_theme_id = '" . (int)$voucher_theme_id . "'");
		
		return $query->row['total'];
	}	
	
	public function getVoucherHistories($voucher_id, $start = 0, $limit = 10) {
		$query = $this->db->query("SELECT vh.order_id, CONCAT(o.firstname, ' ', o.lastname) AS customer, vh.amount, vh.date_added FROM " . DB_PREFIX . "voucher_history vh LEFT JOIN `" . DB_PREFIX . "order` o ON (vh.order_id = o.order_id) WHERE vh.voucher_id = '" . (int)$voucher_id . "' ORDER BY vh.date_added ASC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}
	
	public function getTotalVoucherHistories($voucher_id) {
	  	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "voucher_history WHERE voucher_id = '" . (int)$voucher_id . "'");

		return $query->row['total'];
	}	
	
	
	public function getVoucherBalance($voucher_id) {
	  	$query = $this->db->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "voucher_history WHERE voucher_id = '" . (int)$voucher_id . "'");

		$total_history= $query->row['total'];
		
		$voucher = $this->getVoucher($voucher_id);
		
		$balance = (float)$voucher['amount'] + (float)$total_history;
		
		return $balance;
		
		
	}			
}
?>