<?php
class ModelCheckoutOrder extends Model {	

					public function deleteOrder($order_id) {
						$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0' AND order_id = '" . (int)$order_id . "'");

						if ($order_query->num_rows) {
							$product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

							foreach($product_query->rows as $product) {
								$this->db->query("UPDATE `" . DB_PREFIX . "product` SET quantity = (quantity + " . (int)$product['quantity'] . ") WHERE product_id = '" . (int)$product['product_id'] . "' AND subtract = '1'");

								$option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$product['order_product_id'] . "'");

								foreach ($option_query->rows as $option) {
									$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity + " . (int)$product['quantity'] . ") WHERE product_option_value_id = '" . (int)$option['product_option_value_id'] . "' AND subtract = '1'");
								}
							}
						}

						$this->db->query("DELETE FROM `" . DB_PREFIX . "order` WHERE order_id = '" . (int)$order_id . "'");
						$this->db->query("DELETE FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");
						$this->db->query("DELETE FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "'");
						$this->db->query("DELETE FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int)$order_id . "'");
						$this->db->query("DELETE FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");
						$this->db->query("DELETE FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "'");
						$this->db->query("DELETE FROM " . DB_PREFIX . "order_history WHERE order_id = '" . (int)$order_id . "'");
						$this->db->query("DELETE FROM " . DB_PREFIX . "order_fraud WHERE order_id = '" . (int)$order_id . "'");
						$this->db->query("DELETE FROM " . DB_PREFIX . "customer_transaction WHERE order_id = '" . (int)$order_id . "'");
						$this->db->query("DELETE FROM " . DB_PREFIX . "customer_reward WHERE order_id = '" . (int)$order_id . "'");
						$this->db->query("DELETE FROM " . DB_PREFIX . "affiliate_transaction WHERE order_id = '" . (int)$order_id . "'");
						
						if(VERSION != "1.5.4" && VERSION != "1.5.4.1" && VERSION != "1.5.3" && VERSION != "1.5.3.1" && VERSION != "1.5.5" && VERSION != "1.5.5.1"){
							$this->db->query("DELETE `or`, ort FROM " . DB_PREFIX . "order_recurring `or`, " . DB_PREFIX . "order_recurring_transaction ort WHERE order_id = '" . (int)$order_id . "' AND ort.order_recurring_id = `or`.order_recurring_id");
						}
					}
			
					public function getStore($store_id) {
						$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "store WHERE store_id = '" . (int)$store_id . "'");
						
						return $query->row;
					}
					
					public function getStores($data = array()) {
						$store_data = $this->cache->get('store');
					
						if (!$store_data) {
							$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "store ORDER BY url");

							$store_data = $query->rows;
						
							$this->cache->set('store', $store_data);
						}
					 
						return $store_data;
					}
			

            public function getOrderProduct($order_id) {
   				$query = $this->db->query("SELECT cd.name as category_name,op.* FROM " . DB_PREFIX . "category_description cd INNER JOIN " . DB_PREFIX . "product_to_category pc ON pc.category_id = cd.category_id INNER JOIN " . DB_PREFIX . "order_product op ON pc.product_id = op.product_id LEFT JOIN " . DB_PREFIX . "order_option oo ON (oo.order_product_id = op.order_product_id) WHERE op.order_id = '" . (int)$order_id . "' AND pc.product_id = op.product_id AND oo.order_id IS NULL GROUP BY op.order_product_id");

            	return $query->rows;
       		 }
               
            public function getOrderProductOptions($order_id) {
				$query = $this->db->query("SELECT cd.name as category_name,op.*,oo.name as option_name, oo.value,oo.order_product_id,GROUP_CONCAT(DISTINCT oo.name, ': ', oo.value SEPARATOR ' - ') as options_data FROM " . DB_PREFIX . "category_description cd INNER JOIN " . DB_PREFIX . "product_to_category pc ON pc.category_id = cd.category_id INNER JOIN " . DB_PREFIX . "order_product op ON pc.product_id = op.product_id INNER JOIN " . DB_PREFIX . "order_option oo ON op.order_product_id = oo.order_product_id WHERE op.order_id = '" . (int)$order_id . "' AND pc.product_id = op.product_id AND op.order_product_id = oo.order_product_id GROUP BY oo.order_product_id");

            	return $query->rows;
       		 } 
            
	public function addOrder($data) {
    // echo "INSERT INTO `" . DB_PREFIX . "order` SET invoice_prefix = '" . $this->db->escape($data['invoice_prefix']) . "', store_id = '" . (int)$data['store_id'] . "', store_name = '" . $this->db->escape($data['store_name']) . "', store_url = '" . $this->db->escape($data['store_url']) . "', customer_id = '" . (int)$data['customer_id'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', payment_company = '" . $this->db->escape($data['payment_company']) . "', payment_company_id = '" . $this->db->escape($data['payment_company_id']) . "', payment_tax_id = '" . $this->db->escape($data['payment_tax_id']) . "', payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', payment_city = '" . $this->db->escape($data['payment_city']) . "', payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', payment_country = '" . $this->db->escape($data['payment_country']) . "', payment_country_id = '" . (int)$data['payment_country_id'] . "', payment_zone = '" . $this->db->escape($data['payment_zone']) . "', payment_zone_id = '" . (int)$data['payment_zone_id'] . "', payment_address_format = '" . $this->db->escape($data['payment_address_format']) . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "', shipping_company = '" . $this->db->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($data['shipping_country']) . "', shipping_country_id = '" . (int)$data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($data['shipping_zone']) . "', shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($data['shipping_address_format']) . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "', comment = '" . $this->db->escape($data['comment']) . "', total = '" . (float)$data['total'] . "', affiliate_id = '" . (int)$data['affiliate_id'] . "', commission = '" . (float)$data['commission'] . "', language_id = '" . (int)$data['language_id'] . "', currency_id = '" . (int)$data['currency_id'] . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', currency_value = '" . (float)$data['currency_value'] . "', ip = '" . $this->db->escape($data['ip']) . "', forwarded_ip = '" .  $this->db->escape($data['forwarded_ip']) . "', user_agent = '" . $this->db->escape($data['user_agent']) . "', accept_language = '" . $this->db->escape($data['accept_language']) . "', date_added = NOW(), date_modified = NOW()";exit;
		
                        //$this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET invoice_prefix = '" . $this->db->escape($data['invoice_prefix']) . "', store_id = '" . (int)$data['store_id'] . "', store_name = '" . $this->db->escape($data['store_name']) . "', store_url = '" . $this->db->escape($data['store_url']) . "', customer_id = '" . (int)$data['customer_id'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', payment_company = '" . $this->db->escape($data['payment_company']) . "', payment_company_id = '" . $this->db->escape($data['payment_company_id']) . "', payment_tax_id = '" . $this->db->escape($data['payment_tax_id']) . "', payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', payment_city = '" . $this->db->escape($data['payment_city']) . "', payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', payment_country = '" . $this->db->escape($data['payment_country']) . "', payment_country_id = '" . (int)$data['payment_country_id'] . "', payment_zone = '" . $this->db->escape($data['payment_zone']) . "', payment_zone_id = '" . (int)$data['payment_zone_id'] . "', payment_address_format = '" . $this->db->escape($data['payment_address_format']) . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "', shipping_company = '" . $this->db->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($data['shipping_country']) . "', shipping_country_id = '" . (int)$data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($data['shipping_zone']) . "', shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($data['shipping_address_format']) . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "', comment = '" . $this->db->escape($data['comment']) . "', total = '" . (float)$data['total'] . "', affiliate_id = '" . (int)$data['affiliate_id'] . "', commission = '" . (float)$data['commission'] . "', language_id = '" . (int)$data['language_id'] . "', currency_id = '" . (int)$data['currency_id'] . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', currency_value = '" . (float)$data['currency_value'] . "', ip = '" . $this->db->escape($data['ip']) . "', forwarded_ip = '" .  $this->db->escape($data['forwarded_ip']) . "', user_agent = '" . $this->db->escape($data['user_agent']) . "', accept_language = '" . $this->db->escape($data['accept_language']) . "', date_added = NOW(), date_modified = NOW()");
                            if(isset($this->session->data['tmp_order_id'])){
                                    $order_id = $this->session->data['tmp_order_id'];
                                    if(isset($data['products']) && is_array($data['products']) && count($data['products'])>0){
                                        $this->db->query("DELETE from " . DB_PREFIX . "order_product WHERE order_id='" . (int)$order_id . "'");
                                        $this->db->query("DELETE from " . DB_PREFIX . "order_option WHERE order_id='" . (int)$order_id . "'");
                                        $this->db->query("DELETE from " . DB_PREFIX . "order_download WHERE order_id='" . (int)$order_id . "'");
                                    }
                                    if(isset($data['vouchers']) && is_array($data['vouchers']) && count($data['vouchers'])>0){
                                        $this->db->query("DELETE from " . DB_PREFIX . "order_voucher WHERE order_id='" . (int)$order_id . "'");
                                    }
                                    if(isset($data['totals']) && is_array($data['totals']) && count($data['totals'])>0){
                                        $this->db->query("DELETE from " . DB_PREFIX . "order_total WHERE order_id='" . (int)$order_id . "'");
                                    }
                                    $this->db->query("REPLACE `" . DB_PREFIX . "order` SET order_id='".(int)$order_id."', invoice_prefix = '" . $this->db->escape($data['invoice_prefix']) . "', store_id = '" . (int)$data['store_id'] . "', store_name = '" . $this->db->escape($data['store_name']) . "', store_url = '" . $this->db->escape($data['store_url']) . "', customer_id = '" . (int)$data['customer_id'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', payment_company = '" . $this->db->escape($data['payment_company']) . "', payment_company_id = '" . $this->db->escape($data['payment_company_id']) . "', payment_tax_id = '" . $this->db->escape($data['payment_tax_id']) . "', payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', payment_city = '" . $this->db->escape($data['payment_city']) . "', payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', payment_country = '" . $this->db->escape($data['payment_country']) . "', payment_country_id = '" . (int)$data['payment_country_id'] . "', payment_zone = '" . $this->db->escape($data['payment_zone']) . "', payment_zone_id = '" . (int)$data['payment_zone_id'] . "', payment_address_format = '" . $this->db->escape($data['payment_address_format']) . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "', shipping_company = '" . $this->db->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($data['shipping_country']) . "', shipping_country_id = '" . (int)$data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($data['shipping_zone']) . "', shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($data['shipping_address_format']) . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "', comment = '" . $this->db->escape($data['comment']) . "', total = '" . (float)$data['total'] . "', affiliate_id = '" . (int)$data['affiliate_id'] . "', commission = '" . (float)$data['commission'] . "', language_id = '" . (int)$data['language_id'] . "', currency_id = '" . (int)$data['currency_id'] . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', currency_value = '" . (float)$data['currency_value'] . "', ip = '" . $this->db->escape($data['ip']) . "', forwarded_ip = '" .  $this->db->escape($data['forwarded_ip']) . "', user_agent = '" . $this->db->escape($data['user_agent']) . "', accept_language = '" . $this->db->escape($data['accept_language']) . "', date_added = NOW(), date_modified = NOW()");
                            }else{
                                    $this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET invoice_prefix = '" . $this->db->escape($data['invoice_prefix']) . "', store_id = '" . (int)$data['store_id'] . "', store_name = '" . $this->db->escape($data['store_name']) . "', store_url = '" . $this->db->escape($data['store_url']) . "', customer_id = '" . (int)$data['customer_id'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', payment_company = '" . $this->db->escape($data['payment_company']) . "', payment_company_id = '" . $this->db->escape($data['payment_company_id']) . "', payment_tax_id = '" . $this->db->escape($data['payment_tax_id']) . "', payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', payment_city = '" . $this->db->escape($data['payment_city']) . "', payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', payment_country = '" . $this->db->escape($data['payment_country']) . "', payment_country_id = '" . (int)$data['payment_country_id'] . "', payment_zone = '" . $this->db->escape($data['payment_zone']) . "', payment_zone_id = '" . (int)$data['payment_zone_id'] . "', payment_address_format = '" . $this->db->escape($data['payment_address_format']) . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "', shipping_company = '" . $this->db->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($data['shipping_country']) . "', shipping_country_id = '" . (int)$data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($data['shipping_zone']) . "', shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($data['shipping_address_format']) . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "', comment = '" . $this->db->escape($data['comment']) . "', total = '" . (float)$data['total'] . "', affiliate_id = '" . (int)$data['affiliate_id'] . "', commission = '" . (float)$data['commission'] . "', language_id = '" . (int)$data['language_id'] . "', currency_id = '" . (int)$data['currency_id'] . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', currency_value = '" . (float)$data['currency_value'] . "', ip = '" . $this->db->escape($data['ip']) . "', forwarded_ip = '" .  $this->db->escape($data['forwarded_ip']) . "', user_agent = '" . $this->db->escape($data['user_agent']) . "', accept_language = '" . $this->db->escape($data['accept_language']) . "', date_added = NOW(), date_modified = NOW()");
                                    $order_id = $this->db->getLastId();
                                    $this->session->data['tmp_order_id'] = $order_id;
                            }
                        

		$order_id = $this->db->getLastId();

		foreach ($data['products'] as $product) { 
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$product['product_id'] . "', name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "', quantity = '" . (int)$product['quantity'] . "', price = '" . (float)$product['price'] . "', total = '" . (float)$product['total'] . "', tax = '" . (float)$product['tax'] . "', reward = '" . (int)$product['reward'] . "'");

			$order_product_id = $this->db->getLastId();

			foreach ($product['option'] as $option) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "order_option SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', product_option_id = '" . (int)$option['product_option_id'] . "', product_option_value_id = '" . (int)$option['product_option_value_id'] . "', name = '" . $this->db->escape($option['name']) . "', `value` = '" . $this->db->escape($option['value']) . "', `type` = '" . $this->db->escape($option['type']) . "'");
			}

			foreach ($product['download'] as $download) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "order_download SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', name = '" . $this->db->escape($download['name']) . "', filename = '" . $this->db->escape($download['filename']) . "', mask = '" . $this->db->escape($download['mask']) . "', remaining = '" . (int)($download['remaining'] * $product['quantity']) . "'");
			}	
		}
		
		foreach ($data['vouchers'] as $voucher) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_voucher SET order_id = '" . (int)$order_id . "', description = '" . $this->db->escape($voucher['description']) . "', code = '" . $this->db->escape($voucher['code']) . "', from_name = '" . $this->db->escape($voucher['from_name']) . "', from_email = '" . $this->db->escape($voucher['from_email']) . "', to_name = '" . $this->db->escape($voucher['to_name']) . "', to_email = '" . $this->db->escape($voucher['to_email']) . "', voucher_theme_id = '" . (int)$voucher['voucher_theme_id'] . "', message = '" . $this->db->escape($voucher['message']) . "', amount = '" . (float)$voucher['amount'] . "'");
		}

    $dis_tax = $this->db->query("SELECT dis_tax FROM " . DB_PREFIX . "customer WHERE email = '" . $data['email'] . "'")->row['dis_tax'];

		foreach ($data['totals'] as $total) {
      if ($total['code'] == 'tax' && $dis_tax) {
        continue;
      }
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int)$order_id . "', code = '" . $this->db->escape($total['code']) . "', title = '" . $this->db->escape($total['title']) . "', text = '" . $this->db->escape($total['text']) . "', `value` = '" . (float)$total['value'] . "', sort_order = '" . (int)$total['sort_order'] . "'");
		}	

		return $order_id;
	}

  public function getGTSOrderProduct($order_id) { // Google Trusted Store Code
  	$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "' GROUP BY order_product_id");
  	return $query->rows;
  }
  public function backUpOrder ($order_id) {
        $order_id = (int) $order_id;
        $tables = array( 'order', 'order_commission', 'order_download', 'order_emails', 'order_fraud', 'order_history', 'order_misc', 'order_mod_logs', 'order_option', 'order_payment', 'order_product', 'order_survey', 'order_total', 'order_voucher');

        if ($order_id) {
            foreach ($tables as $table) {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . $table ." WHERE order_id = '" . $order_id . "'");
                $query2 = $this->db->query("SELECT * FROM " . DB_PREFIX . "temp_" . $table ." WHERE order_id = '" . $order_id . "'");

                if ($query->row && !$query2->row) {
                    $this->db->query("INSERT INTO " . DB_PREFIX . "temp_" . $table ." SELECT * FROM " . DB_PREFIX . $table ." WHERE order_id = '" . $order_id . "'");
                }
            }
        }
    }
  public function getOrderByEmail($email){
    $orders = $this->db->query("SELECT *,(SELECT os.name FROM `" . DB_PREFIX . "order_status` os WHERE os.order_status_id = o.order_status_id AND os.language_id = o.language_id) AS order_status FROM `" . DB_PREFIX . "order` o WHERE o.order_status_id = '3' AND o.email = '" . $email . "'");
    if($orders){
return $orders->rows;

    } else {
      return false;
    }

   } 
  public function getOrder($order_id) {
  	$order_query = $this->db->query("SELECT *, (SELECT os.name FROM `" . DB_PREFIX . "order_status` os WHERE os.order_status_id = o.order_status_id AND os.language_id = o.language_id) AS order_status FROM `" . DB_PREFIX . "order` o WHERE o.order_id = '" . (int)$order_id . "'");

  	$TotalUnits = $this->db->query("SELECT SUM(quantity) AS quantity_order FROM ".DB_PREFIX."order_product WHERE order_id='".(int)$order_id."'");

  	$TotalUnits = $TotalUnits->row;

    $_query = $this->db->query("SELECT value FROM ".DB_PREFIX."order_total WHERE order_id='".(int)$order_id."' and code='sub_total'");
    $sub_total = $_query->row['value'];

    $_query = $this->db->query("SELECT value FROM ".DB_PREFIX."order_total WHERE order_id='".(int)$order_id."' and code='tax'");
    $tax = $_query->row['value'];

    $_query = $this->db->query("SELECT value FROM ".DB_PREFIX."order_total WHERE order_id='".(int)$order_id."' and code='shipping'");
    $shipping = $_query->row['value'];

    $_query = $this->db->query("SELECT sum(value) as value FROM ".DB_PREFIX."order_total WHERE order_id='".(int)$order_id."' and code='voucher'");
    $voucher_total = $_query->row['value'];

    $_query = $this->db->query("SELECT title FROM ".DB_PREFIX."order_total WHERE order_id='".(int)$order_id."' and code='voucher'");
    $vouchers = $_query->rows;

  	if ($order_query->num_rows) {
  		$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['payment_country_id'] . "'");

  		if ($country_query->num_rows) {
  			$payment_iso_code_2 = $country_query->row['iso_code_2'];
  			$payment_iso_code_3 = $country_query->row['iso_code_3'];
  		} else {
  			$payment_iso_code_2 = '';
  			$payment_iso_code_3 = '';				
  		}

  		$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['payment_zone_id'] . "'");

  		if ($zone_query->num_rows) {
  			$payment_zone_code = $zone_query->row['code'];
  		} else {
  			$payment_zone_code = '';
  		}			

  		$country_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "country` WHERE country_id = '" . (int)$order_query->row['shipping_country_id'] . "'");

  		if ($country_query->num_rows) {
  			$shipping_iso_code_2 = $country_query->row['iso_code_2'];
  			$shipping_iso_code_3 = $country_query->row['iso_code_3'];
  		} else {
  			$shipping_iso_code_2 = '';
  			$shipping_iso_code_3 = '';				
  		}

  		$zone_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` WHERE zone_id = '" . (int)$order_query->row['shipping_zone_id'] . "'");

  		if ($zone_query->num_rows) {
  			$shipping_zone_code = $zone_query->row['code'];
  		} else {
  			$shipping_zone_code = '';
  		}
			// Google Trusted Store Starts
  		$tax_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_query->row['order_id'] . "' AND code = 'tax'");
  		$order_tax = 0;
  		foreach ($tax_query->rows as $taxresult) {
  			$order_tax += $taxresult['value'];
  		}

			// Google Trusted Store Ends


            $uatracking_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "setting` s WHERE s.store_id = '" . (int)$order_query->row['store_id'] . "' AND s.key = 'config_ua_tracking'");
            
            $tax_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_query->row['order_id'] . "' AND code = 'tax'");
			
			if ($tax_query->num_rows) {
				$order_tax = $tax_query->row['value'];
			} else {
				$order_tax = '';
			}  
            
  		$this->load->model('localisation/language');

  		$language_info = $this->model_localisation_language->getLanguage($order_query->row['language_id']);

  		if ($language_info) {
  			$language_code = $language_info['code'];
  			$language_filename = $language_info['filename'];
  			$language_directory = $language_info['directory'];
  		} else {
  			$language_code = '';
  			$language_filename = '';
  			$language_directory = '';
  		}

  		return array(
  			'order_id'                => $order_query->row['order_id'],

            'ua_tracking'             => $uatracking_query->row['value'],
            
  			'invoice_no'              => $order_query->row['invoice_no'],
  			'invoice_prefix'          => $order_query->row['invoice_prefix'],
  			'store_id'                => $order_query->row['store_id'],
'store_address'           => nl2br($this->config->get('config_address')),

				'store_telephone'         => $this->config->get('config_telephone'),

				'store_fax'               => $this->config->get('config_fax'),

                'store_email'             => $this->config->get('config_email'),

				'store_logo'              => $this->config->get('config_logo'),
				
  			'store_name'              => $order_query->row['store_name'],
  			'store_url'               => $order_query->row['store_url'],				
  			'customer_id'             => $order_query->row['customer_id'],
  			'firstname'               => $order_query->row['firstname'],
  			'lastname'                => $order_query->row['lastname'],
  			'telephone'               => $order_query->row['telephone'],
  			'fax'                     => $order_query->row['fax'],
  			'email'                   => $order_query->row['email'],
  			'payment_firstname'       => $order_query->row['payment_firstname'],
  			'payment_lastname'        => $order_query->row['payment_lastname'],				
  			'payment_company'         => $order_query->row['payment_company'],
  			'payment_address_1'       => $order_query->row['payment_address_1'],
  			'payment_address_2'       => $order_query->row['payment_address_2'],
  			'payment_postcode'        => $order_query->row['payment_postcode'],
  			'payment_city'            => $order_query->row['payment_city'],
  			'payment_zone_id'         => $order_query->row['payment_zone_id'],
  			'payment_zone'            => $order_query->row['payment_zone'],
  			'payment_zone_code'       => $payment_zone_code,
  			'payment_country_id'      => $order_query->row['payment_country_id'],
  			'payment_country'         => $order_query->row['payment_country'],	
  			'payment_iso_code_2'      => $payment_iso_code_2,
  			'payment_iso_code_3'      => $payment_iso_code_3,
  			'payment_address_format'  => $order_query->row['payment_address_format'],
  			'payment_method'          => $order_query->row['payment_method'],
  			'payment_code'            => $order_query->row['payment_code'],
  			'shipping_firstname'      => $order_query->row['shipping_firstname'],
  			'shipping_lastname'       => $order_query->row['shipping_lastname'],				
  			'shipping_company'        => $order_query->row['shipping_company'],
  			'shipping_address_1'      => $order_query->row['shipping_address_1'],
  			'shipping_address_2'      => $order_query->row['shipping_address_2'],
  			'shipping_postcode'       => $order_query->row['shipping_postcode'],
  			'shipping_city'           => $order_query->row['shipping_city'],
  			'shipping_zone_id'        => $order_query->row['shipping_zone_id'],
  			'shipping_zone'           => $order_query->row['shipping_zone'],
  			'shipping_zone_code'      => $shipping_zone_code,
  			'shipping_country_id'     => $order_query->row['shipping_country_id'],
  			'shipping_country'        => $order_query->row['shipping_country'],	
  			'shipping_iso_code_2'     => $shipping_iso_code_2,
  			'shipping_iso_code_3'     => $shipping_iso_code_3,
  			'shipping_address_format' => $order_query->row['shipping_address_format'],
  			'shipping_method'         => $order_query->row['shipping_method'],
  			'shipping_code'           => $order_query->row['shipping_code'],
  			'comment'                 => $order_query->row['comment'],
  			'total'                   => $order_query->row['total'],

            'order_tax'       		  => $order_tax,  
            
  			'order_tax'       		  => $order_tax,  
  			'order_status_id'         => $order_query->row['order_status_id'],
  			'order_status'            => $order_query->row['order_status'],
  			'language_id'             => $order_query->row['language_id'],
  			'language_code'           => $language_code,
  			'language_filename'       => $language_filename,
  			'language_directory'      => $language_directory,
  			'currency_id'             => $order_query->row['currency_id'],
  			'currency_code'           => $order_query->row['currency_code'],
  			'currency_value'          => $order_query->row['currency_value'],
  			'ip'                      => $order_query->row['ip'],
  			'forwarded_ip'            => $order_query->row['forwarded_ip'], 
  			'user_agent'              => $order_query->row['user_agent'],	
  			'accept_language'         => $order_query->row['accept_language'],				
  			'date_modified'           => $order_query->row['date_modified'],
  			'date_added'              => $order_query->row['date_added'],
  			'total_units'			  => $TotalUnits['quantity_order'],
  			'ref_order_id'			  => $order_query->row['ref_order_id'],
        'sub_total'     => $sub_total,
        'tax_total'     => $tax,
        'shipping_total'     => $shipping,
        'voucher_total'     => $voucher_total,
        'total_vouchers'     => $vouchers,

  			);
} else {
	return false;	
}
}	
public function ifCustomerIsNew($customer_email)
{

	$CustomerStatus = $this->db->query("SELECT COUNT(*) as id FROM `".DB_PREFIX."order` WHERE email='".$customer_email."'");

	if($CustomerStatus->row['id'] > 1)
	{
    return  0;

  }
  else
  {
    return 1; 
  }

}

public function confirm($order_id, $order_status_id, $comment = '', $notify = false) {
  $order_info = $this->getOrder($order_id);

        $this->load->model('localisation/zone');
  if ($order_info && !$order_info['order_status_id']) {


    if($order_info['shipping_code']=='multiflatrate.multiflatrate_0')
    {
      $order_status_id = $this->config->get('config_order_status_id');
    }
    else
    {
      // Fraud Detection
      if ($this->config->get('config_fraud_detection')) {
        $this->load->model('checkout/fraud');
        
        $risk_score = $this->model_checkout_fraud->getFraudScore($order_info);
        
        if ($risk_score > $this->config->get('config_fraud_score')) {
          
          $order_status_id = $this->config->get('config_fraud_status_id');
        //  $this->trelloCard($order_info);
        }
      }
    }

      // Blacklist
    $status = false;

    $this->load->model('account/customer');

    if ($order_info['customer_id']) {
      $results = $this->model_account_customer->getIps($order_info['customer_id']);

      foreach ($results as $result) {
        if ($this->model_account_customer->isBlacklisted($result['ip'])) {
          $status = true;

          break;
        }
      }
    } else {
      $status = $this->model_account_customer->isBlacklisted($order_info['ip']);
    }

    if ($status) {
      $order_status_id = $this->config->get('config_order_status_id');
    }
    if($this->session->data['payment_method']['code']=='paypal_express_new')
    {
      $_payment_method = 'PayPal';
    }
    else
    {

    $_payment_method =   $this->db->escape($this->session->data['payment_methods'][$this->session->data['payment_method']['code']]['title']);
    }
    $comment = $this->db->escape($this->session->data['comment']);
    if ($comment=='undefined') {
      $comment = $this->db->escape($this->session->data['logged_comment']);
    }
    if ($comment=='undefined') {
        $comment = '';
    }
    if($this->session->data['shipping_method']['title']=='Combined Shipping')
    {
      $comment.=" ** Combined Shipping with Order #".$this->session->data['combined_order_id'].' **';
    }

    if($this->session->data['shipping_method']['title']!='Combined Shipping' && isset($this->session->data['combined_order_id']))
    {
      $comment.=" ** Shipping upgraded to ".$this->session->data['shipping_method']['title']." and combined shipping with #".$this->session->data['combined_order_id'].' **';
    }

    unset($this->session->data['combined_order_id']);
    $this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$order_status_id . "', date_modified = NOW(),payment_method='".$_payment_method."',payment_code='".$this->db->escape($this->session->data['payment_method']['code'])."',shipping_method='".$this->db->escape($this->session->data['shipping_method']['title'])."',shipping_code='".$this->session->data['shipping_method']['code']."',comment='".$comment."',repairdesk_po='".$this->db->escape($this->session->data['newcheckout']['repairdesk_po'])."' WHERE order_id = '" . (int)$order_id . "'");




    if ($this->customer->isLogged()) {
          $this->load->model('account/address');
          
          $shipping_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);  
        } elseif (isset($this->session->data['guest'])) {
          $shipping_address = $this->session->data['guest']['shipping'];
        }     
        
        $data['shipping_firstname'] = $shipping_address['firstname'];
        $data['shipping_lastname'] = $shipping_address['lastname']; 
        $data['shipping_company'] = $shipping_address['company']; 
        $data['shipping_address_1'] = $shipping_address['address_1'];
        $data['shipping_address_2'] = $shipping_address['address_2'];
        $data['shipping_city'] = $shipping_address['city'];
        $data['shipping_postcode'] = $shipping_address['postcode'];
        $data['shipping_zone'] = $shipping_address['zone'];
        $data['shipping_zone_id'] = $shipping_address['zone_id'];
        $data['shipping_country'] = $shipping_address['country'];
        $data['shipping_country_id'] = $shipping_address['country_id'];
        $data['shipping_address_format'] = $shipping_address['address_format'];


        if ($this->customer->isLogged()) {
      
        $this->load->model('account/address');
        
        $payment_address = $this->model_account_address->getAddress($this->session->data['shipping_address_id']);
      } elseif (isset($this->session->data['guest'])) {
        
        $payment_address = $this->session->data['newcheckout'];
      }
      
      $data['payment_firstname'] = $payment_address['firstname'];
      $data['payment_lastname'] = $payment_address['lastname']; 
      $data['payment_company_id'] = $payment_address['company_id']; 
      $data['payment_tax_id'] = $payment_address['tax_id']; 
      $data['payment_company'] = $this->session->data['newcheckout']['company'];  
      if($this->session->data['newcheckout']['address_1'])
    {
      $_zone = $this->model_localisation_zone->getZone($this->session->data['newcheckout']['zone_id']);

      $data['payment_address_1'] = $this->session->data['newcheckout']['address_1'];
      $data['payment_address_2'] = $this->session->data['newcheckout']['address_2'];
      $data['payment_city'] = $this->session->data['newcheckout']['city'];;
      $data['payment_postcode'] = $this->session->data['newcheckout']['postcode'];;
      $data['payment_zone'] = $_zone['name'];
      $data['payment_zone_id'] = $this->session->data['newcheckout']['zone_id'];;
    
    }
    else
    {
      $data['payment_address_1'] = $payment_address['address_1'];
      $data['payment_address_2'] = $payment_address['address_2'];
      $data['payment_city'] = $payment_address['city'];
      $data['payment_postcode'] = $payment_address['postcode'];
      $data['payment_zone'] = $payment_address['zone'];
      $data['payment_zone_id'] = $payment_address['zone_id'];
      
    }
    $data['payment_country'] = $payment_address['country'];


    $this->db->query("UPDATE `".DB_PREFIX."order` SET 
      shipping_firstname='".$this->db->escape($data['shipping_firstname'])."',
      shipping_lastname='".$this->db->escape($data['shipping_lastname'])."',
      shipping_company='".$this->db->escape($data['shipping_company'])."',
      payment_company='".$this->db->escape($data['payment_company'])."',

      shipping_address_1='".$this->db->escape($data['shipping_address_1'])."',
      shipping_address_2='".$this->db->escape($data['shipping_address_2'])."',
      shipping_city='".$this->db->escape($data['shipping_city'])."',
      shipping_postcode='".$this->db->escape($data['shipping_postcode'])."',
      shipping_country='".$this->db->escape($data['shipping_country'])."',
      shipping_country='".$this->db->escape($data['shipping_country'])."',
      shipping_country_id='".$this->db->escape($data['shipping_country_id'])."',
      shipping_zone='".$this->db->escape($data['shipping_zone'])."',
      shipping_zone_id='".$this->db->escape($data['shipping_zone_id'])."',
      payment_firstname='".$this->db->escape($data['payment_firstname'])."',
      payment_lastname='".$this->db->escape($data['payment_lastname'])."',
      payment_address_1='".$this->db->escape($data['payment_address_1'])."',
      payment_address_2='".$this->db->escape($data['payment_address_2'])."',
      payment_city='".$this->db->escape($data['payment_city'])."',
      payment_postcode='".$this->db->escape($data['payment_postcode'])."',
      payment_country='".$this->db->escape($data['payment_country'])."',
      payment_zone='".$this->db->escape($data['payment_zone'])."',
      payment_zone_id='".$this->db->escape($data['payment_zone_id'])."'
      where order_id='".(int)$order_id."'
      ");

     $this->db->query("DELETE FROM ".DB_PREFIX."order_product WHERE order_id='".(int)$order_id."'");
   
    foreach ($this->cart->getProducts() as $product) {
      
   
      


        $this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$product['product_id'] . "', name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "', quantity = '" . (int)$product['quantity'] . "', price = '" . (float)$product['price'] . "', total = '" . (float)$product['total'] . "', tax = '" . (float)$product['tax'] . "', reward = '" . (int)$product['reward'] . "'");

       
      }
    

		 $this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '".$notify."', comment = '" . $this->db->escape($comment) . "', date_added = NOW()"); 

		$order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");

		foreach ($order_product_query->rows as $order_product) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE product_id = '" . (int)$order_product['product_id'] . "' AND subtract = '1'");

			$order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$order_product['order_product_id'] . "'");
			
			foreach ($order_option_query->rows as $option) {
				$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE product_option_value_id = '" . (int)$option['product_option_value_id'] . "' AND subtract = '1'");
			}
		}


            if(!isset($passArray) || empty($passArray)){ $passArray = null; }
            $this->openbay->orderNew((int)$order_id);
            
		$this->cache->delete('product');

			// Downloads
		$order_download_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int)$order_id . "'");

			// Gift Voucher
		$this->load->model('checkout/voucher');

		$order_voucher_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'");

		foreach ($order_voucher_query->rows as $order_voucher) {
			$voucher_id = $this->model_checkout_voucher->addVoucher($order_id, $order_voucher);

			$this->db->query("UPDATE " . DB_PREFIX . "order_voucher SET voucher_id = '" . (int)$voucher_id . "' WHERE order_voucher_id = '" . (int)$order_voucher['order_voucher_id'] . "'");
		}			

			// Send out any gift voucher mails
		if ($this->config->get('config_complete_status_id') == $order_status_id) {
			$this->model_checkout_voucher->confirm($order_id);
		}

    $this->db->query("DELETE FROM ".DB_PREFIX."order_total where order_id='".(int)$order_id."'");

      $total_data = array();
      $total = 0;
      $taxes = $this->cart->getTaxes();
       
      $this->load->model('setting/extension');
      
      $sort_order = array(); 
      
      $results = $this->model_setting_extension->getExtensions('total');
      
      foreach ($results as $key => $value) {
        $sort_order[$key] = $this->config->get($value['code'] . '_sort_order');
      }
      
      array_multisort($sort_order, SORT_ASC, $results);
      
      foreach ($results as $result) {
        if ($this->config->get($result['code'] . '_status')) {
          $this->load->model('total/' . $result['code']);
    
          $this->{'model_total_' . $result['code']}->getTotal($total_data, $total, $taxes);
        }
      }
      
      $sort_order = array(); 
      
      foreach ($total_data as $key => $value) {
        $sort_order[$key] = $value['sort_order'];
      }
  
      array_multisort($sort_order, SORT_ASC, $total_data);

       $dis_tax = $this->db->query("SELECT dis_tax FROM " . DB_PREFIX . "customer WHERE email = '" . $data['email'] . "'")->row['dis_tax'];

    foreach ($total_data as $_total) {
      if ($_total['code'] == 'tax' && $dis_tax) {
        continue;
      }
      $this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int)$order_id . "', code = '" . $this->db->escape($_total['code']) . "', title = '" . $this->db->escape($_total['title']) . "', text = '" . $this->db->escape($_total['text']) . "', `value` = '" . (float)$_total['value'] . "', sort_order = '" . (int)$_total['sort_order'] . "'");
    } 
    

			// Order Totals			
		$order_total_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order ASC");

		foreach ($order_total_query->rows as $order_total) {
			$this->load->model('total/' . $order_total['code']);

			if (method_exists($this->{'model_total_' . $order_total['code']}, 'confirm')) {
				$this->{'model_total_' . $order_total['code']}->confirm($order_info, $order_total);
			}
		}

    $this->db->query("UPDATE ".DB_PREFIX."order SET total='".(float)$total."' where order_id='".(int)$order_id."'");

			// Send out order confirmation mail
		$language = new Language($order_info['language_directory']);
		$language->load($order_info['language_filename']);
		$language->load('mail/order');

		$order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$order_info['language_id'] . "'");

		if ($order_status_query->num_rows) {
			$order_status = $order_status_query->row['name'];	
		} else {
			$order_status = '';
		}

		$subject = sprintf($language->get('text_new_subject'), $order_info['store_name'], $order_id);
		
			// HTML Mail
		$template = new Template();
$this->load->model('tool/image');

		$template->data['title'] = sprintf($language->get('text_new_subject'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'), $order_id);

		$template->data['text_greeting'] = sprintf($language->get('text_new_greeting'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
		$template->data['text_link'] = $language->get('text_new_link');
		$template->data['text_download'] = $language->get('text_new_download');
		$template->data['text_order_detail'] = $language->get('text_new_order_detail');
		$template->data['text_instruction'] = $language->get('text_new_instruction');
		$template->data['text_order_id'] = $language->get('text_new_order_id');
		$template->data['text_date_added'] = $language->get('text_new_date_added');
		$template->data['text_payment_method'] = $language->get('text_new_payment_method');	
		$template->data['text_shipping_method'] = $language->get('text_new_shipping_method');
		$template->data['text_email'] = $language->get('text_new_email');
		$template->data['text_telephone'] = $language->get('text_new_telephone');
		$template->data['text_ip'] = $language->get('text_new_ip');
		$template->data['text_payment_address'] = $language->get('text_new_payment_address');
		$template->data['text_shipping_address'] = $language->get('text_new_shipping_address');
		$template->data['text_product'] = $language->get('text_new_product');
		$template->data['text_model'] = $language->get('text_new_model');
		$template->data['text_quantity'] = $language->get('text_new_quantity');
		$template->data['text_price'] = $language->get('text_new_price');
		$template->data['text_total'] = $language->get('text_new_total');
$template->data['text_update_comment'] = $language->get('text_update_comment');
		$template->data['text_footer'] = $language->get('text_new_footer');
		$template->data['text_powered'] = $language->get('text_new_powered');

		$template->data['logo'] = HTTP_IMAGE . $this->config->get('config_logo');		
		$template->data['store_name'] = $order_info['store_name'];
		$template->data['store_url'] = $order_info['store_url'];
		$template->data['customer_id'] = $order_info['customer_id'];
		$template->data['link'] = $order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_id;

		if ($order_download_query->num_rows) {
			$template->data['download'] = $order_info['store_url'] . 'index.php?route=account/download';
		} else {
			$template->data['download'] = '';
		}

		$template->data['order_id'] = $order_id;
		$template->data['date_added'] = date($language->get('date_format_short'), strtotime($order_info['date_added']));    	
$template->data['store_address'] = $order_info['store_address'];

			$template->data['store_telephone'] = $order_info['store_telephone'];

			$template->data['store_logo'] = $order_info['store_logo'];

			$template->data['store_email'] = $order_info['store_email'];

			$template->data['store_fax'] = $order_info['store_fax'];

			$template->data['text_invoice_no'] = $language->get('text_invoice_no');

			$template->data['text_fax'] = $language->get('text_fax');

			$template->data['text_invoice'] = $language->get('text_invoice');

			$template->data['text_company_id'] = $language->get('text_company_id');

			$template->data['text_tax_id'] = $language->get('text_tax_id');

			$template->data['order_comment'] = $order_info['comment'];

			$template->data['orderlogo'] = $this->config->get('config_url') . 'image/data/' . 'order.png';

			$template->data['stocklogo'] = $this->config->get('config_url') . 'image/data/' . 'stock.png';

			$template->data['zerostock'] = $this->config->get('zerostock');

			$template->data['zeroostock'] = $this->config->get('zeroostock');

			$confirm_email = $this->config->get('confirm_email');

			$confirm_email2 = $this->config->get('confirm_email2');

			$confirm_email3 = $this->config->get('confirm_email3');

			$confirm_email4 = $this->config->get('confirm_email4');
			
			$enablestockz = $this->config->get('enablestockz');
			
			$invnameav2 = $this->config->get('invnameav2');
			$template->data['invnameav2'] = $this->config->get('invnameav2');
			$template->data['invname2c2'] = $this->config->get('invname2c2');
			$template->data['custfoot221'] = $this->config->get('custfoot221');
			$template->data['custfoot2'] = $this->config->get('custfoot2');
			$template->data['invpicav2'] = $this->config->get('invpicav2');
			$template->data['inv_skuca2'] = $this->config->get('inv_skuca2');
			
			$template->data['logo2'] = DIR_IMAGE . $this->config->get('config_logo');
			
			
            $template->data['dirimage'] = DIR_IMAGE ; 
			

			$template->data['text_update_comment'] = $language->get('text_update_comment');
		$template->data['payment_method'] = $order_info['payment_method'];
		$template->data['shipping_method'] = $order_info['shipping_method'];
		$template->data['email'] = $order_info['email'];
		$template->data['telephone'] = $order_info['telephone'];
		$template->data['ip'] = $order_info['ip'];

$template->data['autotime'] = $this->config->get('autotime');
$template->data['auto_width'] = $this->config->get('auto_width');
$template->data['auto_tfont'] = $this->config->get('auto_tfont');
$template->data['auto_bfont'] = $this->config->get('auto_bfont');
$template->data['auto_cfont'] = $this->config->get('auto_cfont');
$template->data['auto_margin'] = $this->config->get('auto_margin');
$template->data['auto_flogo'] = $this->config->get('auto_flogo');
$template->data['auto_border'] = $this->config->get('auto_border');
$template->data['auto_pad'] = $this->config->get('auto_pad');
if ($this->config->get('savegoogle_drive2')==1) {
			$show_extra2 = '1';
		} else {
			$show_extra2 = '0'; }
		$template->data['extra6'] = $show_extra2;
		if ($this->config->get('savegoogle_drive')==1) {
			$show_extra = '1';
		} else {
			$show_extra = '0'; }
		$template->data['extra5'] = $show_extra;

$template->data['order_comment'] = $order_info['comment'];

		if ($comment && $notify) {
			$template->data['comment'] = nl2br($comment);
		} else {
			$template->data['comment'] = '';
		}

		if ($order_info['payment_address_format']) {
			$format = $order_info['payment_address_format'];
		} else {
			$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . 	'{payment_company_id}' . "\n" . '{payment_tax_id}' . "\n" .'{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
		}

		$find = array(
			'{firstname}',
			'{lastname}',
			'{company}',
	'{payment_company_id}',
				'{payment_tax_id}',
			'{address_1}',
			'{address_2}',
			'{city}',
			'{postcode}',
			'{zone}',
			'{zone_code}',
			'{country}'
			);
		
		$replace = array(
			'firstname' => $order_info['payment_firstname'],
			'lastname'  => $order_info['payment_lastname'],
			'company'   => $order_info['payment_company'],
	'payment_company_id' => $order_info['payment_company_id'],
				'payment_tax_id' => $order_info['payment_tax_id'],
			'address_1' => $order_info['payment_address_1'],
			'address_2' => $order_info['payment_address_2'],
			'city'      => $order_info['payment_city'],
			'postcode'  => $order_info['payment_postcode'],
			'zone'      => $order_info['payment_zone'],
			'zone_code' => $order_info['payment_zone_code'],
			'country'   => $order_info['payment_country']  
			);
		
		$template->data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));						

		if ($order_info['shipping_address_format']) {
			$format = $order_info['shipping_address_format'];
		} else {
			$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
		}

		$find = array(
			'{firstname}',
			'{lastname}',
			'{company}',
			'{address_1}',
			'{address_2}',
			'{city}',
			'{postcode}',
			'{zone}',
			'{zone_code}',
			'{country}'
			);
		
		$replace = array(
			'firstname' => $order_info['shipping_firstname'],
			'lastname'  => $order_info['shipping_lastname'],
			'company'   => $order_info['shipping_company'],
			'address_1' => $order_info['shipping_address_1'],
			'address_2' => $order_info['shipping_address_2'],
			'city'      => $order_info['shipping_city'],
			'postcode'  => $order_info['shipping_postcode'],
			'zone'      => $order_info['shipping_zone'],
			'zone_code' => $order_info['shipping_zone_code'],
			'country'   => $order_info['shipping_country']  
			);
		
		$template->data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

			// Products
		$template->data['products'] = array();

		foreach ($order_product_query->rows as $product) {
			$option_data = array();
$order_sku_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product['product_id'] . "'");
                          foreach ($order_sku_query->rows as $product2) {

				
$product_query = $this->db->query("SELECT image FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product['product_id'] . "'");

foreach ($product_query->rows as $prodquery) { 

$image = $prodquery['image']; 


}

$string = $this->model_tool_image->resize($image, 50, 50);

$thumb2 = substr($string, strpos($string, "image")+6);

$thumb = DIR_IMAGE . $thumb2;

$thumb4 = $string = $this->model_tool_image->resize($image, 50, 50);



			$order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$product['order_product_id'] . "'");

			foreach ($order_option_query->rows as $option) {
				if ($option['type'] != 'file') {
					$value = $option['value'];
				} else {
					$value = utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.'));
				}

				$option_data[] = array(
					'name'  => $option['name'],
					'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
					);					
			}

			$template->data['products'][] = array(
'thumb'     => $thumb,
							'thumb4'     => $thumb4,

			'href'    	 => $this->url->link('product/product', 'product_id=' . $product['product_id']),
				'name'     => $product['name'],
				'model'    => $product['model'],
 'sku'    => $product2['sku'],

				
				'option'   => $option_data,
				'quantity' => $product['quantity'],
				'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
				'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value'])
				);
		}

			// Vouchers
		$template->data['vouchers'] = array();
 }

				

		foreach ($order_voucher_query->rows as $voucher) {
			$template->data['vouchers'][] = array(
				'description' => $voucher['description'],
				'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']),
				);
		}

		$template->data['totals'] = $order_total_query->rows;
 if($this->config->get('custfoot23')==1){ $html2 = $template->fetch('default/template/mail/autop2.tpl');} else {
		$html2 = $template->fetch('default/template/mail/autop.tpl');}


		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mail/order.tpl')) {
			$html = $template->fetch($this->config->get('config_template') . '/template/mail/order.tpl');
		} else {
			$html = $template->fetch('default/template/mail/order.tpl');
		}
    
    if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mail/xorder.tpl')) {
    
      $xhtml = $template->fetch($this->config->get('config_template') . '/template/mail/xorder.tpl');
    
    } else {
     
      $xhtml = $template->fetch('default/template/mail/xorder.tpl');
      
    }

    
     // printnode work
    
    $this->createPdf(array('html'=>$xhtml),'drawer/',(($this->session->data['shipping_method']['code']=='multiflatrate.multiflatrate_0')?true:false),$order_id);
      
    
    //end printnode work

    // repairdesk update po
    if(isset($this->session->data['newcheckout']['repairdesk_po']))
    {
      $this->model_account_customer->update_repairdesk_po($order_info['email']);
      unset($this->session->data['newcheckout']['repairdesk_po']);
    }


    //web hook
      /*$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "http://imp.phonepartsusa.com/web/index.php",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "GET",
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);
*/


    //end webhook




			// Text Mail
		$text  = sprintf($language->get('text_new_greeting'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8')) . "\n\n";
		$text .= $language->get('text_new_order_id') . ' ' . $order_id . "\n";
		$text .= $language->get('text_new_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_info['date_added'])) . "\n";
		$text .= $language->get('text_new_order_status') . ' ' . $order_status . "\n\n";

		if ($comment && $notify) {
			$text .= $language->get('text_new_instruction') . "\n\n";
			$text .= $comment . "\n\n";
		}

			// Products
		$text .= $language->get('text_new_products') . "\n";

		foreach ($order_product_query->rows as $product) {
			$text .= $product['quantity'] . 'x ' . $product['name'] . ' (' . $product['model'] . ') ' . html_entity_decode($this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']), ENT_NOQUOTES, 'UTF-8') . "\n";

			$order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . $product['order_product_id'] . "'");

			foreach ($order_option_query->rows as $option) {
				$text .= chr(9) . '-' . $option['name'] . ' ' . (utf8_strlen($option['value']) > 20 ? utf8_substr($option['value'], 0, 20) . '..' : $option['value']) . "\n";
			}
		}

		foreach ($order_voucher_query->rows as $voucher) {
			$text .= '1x ' . $voucher['description'] . ' ' . $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']);
		}

		$text .= "\n";

		$text .= $language->get('text_new_order_total') . "\n";

		foreach ($order_total_query->rows as $total) {
			$text .= $total['title'] . ': ' . html_entity_decode($total['text'], ENT_NOQUOTES, 'UTF-8') . "\n";
		}			

		$text .= "\n";

		if ($order_info['customer_id']) {
			$text .= $language->get('text_new_link') . "\n";
			$text .= $order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_id . "\n\n";
		}
		
		if ($order_download_query->num_rows) {
			$text .= $language->get('text_new_download') . "\n";
			$text .= $order_info['store_url'] . 'index.php?route=account/download' . "\n\n";
		}

		if ($order_info['comment']) {
			$text .= $language->get('text_new_comment') . "\n\n";
			$text .= $order_info['comment'] . "\n\n";
		}

		$text .= $language->get('text_new_footer') . "\n\n";

	//$this->log->write('Order Logged 1');
	if ($order_info['payment_code'] == "cod" || (isset($order_info['shipping_code']) && ($order_info['shipping_code'] == 'multiflatrate.multiflatrate_0') || $order_info['shipping_code'] == 'multiflatrate.multiflatrate_21')) {
	//$this->log->write('Inside the function');
		$filesz = HTTP_SERVER ."/catalog/controller/icache/files/printmachineclient.php";
		//$this->log->write($filesz);
		function async_get($url) {
			
			$parts=parse_url($url);
			//$fp = fsockopen($parts['host'], isset($parts['port'])?$parts['port']:80, $errno, $errstr, 30);
			$fp = fsockopen('ssl://'.$parts['host'], isset($parts['port'])?$parts['port']:443, $errno, $errstr, 30);
			$out = "GET ".$parts['path']." HTTP/1.1\r\n";
			$out.= "Host: ".$parts['host']."\r\n";
			$out.= "Connection: Close\r\n\r\n";
			fwrite($fp, $out);
			//fclose($fp);
			
			
		}
		//async_get($filesz);
		//$this->log->write("Log written");
	}
	
		

    // $this->backUpOrder($order_info['order_id']);
		$mail = new Mail(); 
		$mail->protocol = $this->config->get('config_mail_protocol');
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->hostname = $this->config->get('config_smtp_host');
		$mail->username = $this->config->get('config_smtp_username');
		$mail->password = $this->config->get('config_smtp_password');
		$mail->port = $this->config->get('config_smtp_port');
		$mail->timeout = $this->config->get('config_smtp_timeout');			
		$mail->setTo($order_info['email']);
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender($order_info['store_name']);
		$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
		$mail->setHtml($html);
		$mail->setText(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
		$mail->send();

			// Admin Alert Mail
		if ($this->config->get('config_alert_mail')) {
			$subject = sprintf($language->get('text_new_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'), $order_id);

				// Text 
			$text  = $language->get('text_new_received') . "\n\n";
			$text .= $language->get('text_new_order_id') . ' ' . $order_id . "\n";
			$text .= $language->get('text_new_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_info['date_added'])) . "\n";
			$text .= $language->get('text_new_order_status') . ' ' . $order_status . "\n\n";
			$text .= $language->get('text_new_products') . "\n";

			foreach ($order_product_query->rows as $product) {
				$text .= $product['quantity'] . 'x ' . $product['name'] . ' (' . $product['model'] . ') ' . html_entity_decode($this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']), ENT_NOQUOTES, 'UTF-8') . "\n";

				$order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . $product['order_product_id'] . "'");

				foreach ($order_option_query->rows as $option) {
					if ($option['type'] != 'file') {
						$value = $option['value'];
					} else {
						$value = utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.'));
					}

					$text .= chr(9) . '-' . $option['name'] . ' ' . (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value) . "\n";
				}
			}

			foreach ($order_voucher_query->rows as $voucher) {
				$text .= '1x ' . $voucher['description'] . ' ' . $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']);
			}

			$text .= "\n";

			$text .= $language->get('text_new_order_total') . "\n";

			foreach ($order_total_query->rows as $total) {
				$text .= $total['title'] . ': ' . html_entity_decode($total['text'], ENT_NOQUOTES, 'UTF-8') . "\n";
			}			

			$text .= "\n";

			if ($order_info['comment']) {
				$text .= $language->get('text_new_comment') . "\n\n";
				$text .= $order_info['comment'] . "\n\n";
			}
			
			$mail = new Mail(); 
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->hostname = $this->config->get('config_smtp_host');
			$mail->username = $this->config->get('config_smtp_username');
			$mail->password = $this->config->get('config_smtp_password');
			$mail->port = $this->config->get('config_smtp_port');
			$mail->timeout = $this->config->get('config_smtp_timeout');
			$mail->setTo($this->config->get('config_email'));
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($order_info['store_name']);
			$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
			$mail->setText(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
			$mail->send();

				// Send to additional alert emails
			$emails = explode(',', $this->config->get('config_alert_emails'));

			foreach ($emails as $email) {
				if ($email && preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $email)) {
					$mail->setTo($email);
					$mail->send();
				}
			}				
		}		
	}
}


public function autoprint_confirm($order_id, $comment = '', $notify = false) {
	$order_info = $this->getOrder($order_id);

	$language = new Language($order_info['language_directory']);
	$language->load($order_info['language_filename']);
	$language->load('mail/order');
	if ($order_info) {
		

		$subject = sprintf($language->get('text_new_subject'), $order_info['store_name'], $order_id);
		
			// HTML Mail
		$template = new Template();
$this->load->model('tool/image');

		$template->data['title'] = sprintf($language->get('text_new_subject'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'), $order_id);

		$template->data['text_greeting'] = sprintf($language->get('text_new_greeting'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
		$template->data['text_link'] = $language->get('text_new_link');
		$template->data['text_download'] = $language->get('text_new_download');
		$template->data['text_order_detail'] = $language->get('text_new_order_detail');
		$template->data['text_instruction'] = $language->get('text_new_instruction');
		$template->data['text_order_id'] = $language->get('text_new_order_id');
		$template->data['text_date_added'] = $language->get('text_new_date_added');
		$template->data['text_payment_method'] = $language->get('text_new_payment_method');	
		$template->data['text_shipping_method'] = $language->get('text_new_shipping_method');
		$template->data['text_email'] = $language->get('text_new_email');
		$template->data['text_telephone'] = $language->get('text_new_telephone');
		$template->data['text_ip'] = $language->get('text_new_ip');
		$template->data['text_payment_address'] = $language->get('text_new_payment_address');
		$template->data['text_shipping_address'] = $language->get('text_new_shipping_address');
		$template->data['text_product'] = $language->get('text_new_product');
		$template->data['text_model'] = $language->get('text_new_model');
		$template->data['text_quantity'] = $language->get('text_new_quantity');
		$template->data['text_price'] = $language->get('text_new_price');
		$template->data['text_total'] = $language->get('text_new_total');
		$template->data['text_footer'] = 'Please reply to this email if you have any questions.';




		$template->data['text_powered'] = $language->get('text_new_powered');

		$template->data['logo'] = HTTP_IMAGE . $this->config->get('config_logo');		
		$template->data['store_name'] = $order_info['store_name'];
		$template->data['store_url'] = $order_info['store_url'];
		$template->data['customer_id'] = $order_info['customer_id'];
		$template->data['link'] = $order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_id;

		if ($order_download_query->num_rows) {
			$template->data['download'] = $order_info['store_url'] . 'index.php?route=account/download';
		} else {
			$template->data['download'] = '';
		}
		if($order_info['ref_order_id'])
		{
			$template->data['order_id'] = $order_info['ref_order_id'];
			
			
		}
		else
		{
			$template->data['order_id'] = $order_id;
			
		}

		$template->data['date_added'] = date($language->get('date_format_short'), strtotime($order_info['date_added']));    	
$template->data['store_address'] = $order_info['store_address'];

			$template->data['store_telephone'] = $order_info['store_telephone'];

			$template->data['store_logo'] = $order_info['store_logo'];

			$template->data['store_email'] = $order_info['store_email'];

			$template->data['store_fax'] = $order_info['store_fax'];

			$template->data['text_invoice_no'] = $language->get('text_invoice_no');

			$template->data['text_fax'] = $language->get('text_fax');

			$template->data['text_invoice'] = $language->get('text_invoice');

			$template->data['text_company_id'] = $language->get('text_company_id');

			$template->data['text_tax_id'] = $language->get('text_tax_id');

			$template->data['order_comment'] = $order_info['comment'];

			$template->data['orderlogo'] = $this->config->get('config_url') . 'image/data/' . 'order.png';

			$template->data['stocklogo'] = $this->config->get('config_url') . 'image/data/' . 'stock.png';

			$template->data['zerostock'] = $this->config->get('zerostock');

			$template->data['zeroostock'] = $this->config->get('zeroostock');

			$confirm_email = $this->config->get('confirm_email');

			$confirm_email2 = $this->config->get('confirm_email2');

			$confirm_email3 = $this->config->get('confirm_email3');

			$confirm_email4 = $this->config->get('confirm_email4');
			
			$enablestockz = $this->config->get('enablestockz');
			
			$invnameav2 = $this->config->get('invnameav2');
			$template->data['invnameav2'] = $this->config->get('invnameav2');
			$template->data['invname2c2'] = $this->config->get('invname2c2');
			$template->data['custfoot221'] = $this->config->get('custfoot221');
			$template->data['custfoot2'] = $this->config->get('custfoot2');
			$template->data['invpicav2'] = $this->config->get('invpicav2');
			$template->data['inv_skuca2'] = $this->config->get('inv_skuca2');
			
			$template->data['logo2'] = DIR_IMAGE . $this->config->get('config_logo');
			
			
            $template->data['dirimage'] = DIR_IMAGE ; 
			

			$template->data['text_update_comment'] = $language->get('text_update_comment');
		$template->data['payment_method'] = $order_info['payment_method'];
		$template->data['shipping_method'] = $order_info['shipping_method'];
		$template->data['email'] = $order_info['email'];
		$template->data['telephone'] = $order_info['telephone'];
		$template->data['ip'] = $order_info['ip'];

$template->data['autotime'] = $this->config->get('autotime');
$template->data['auto_width'] = $this->config->get('auto_width');
$template->data['auto_tfont'] = $this->config->get('auto_tfont');
$template->data['auto_bfont'] = $this->config->get('auto_bfont');
$template->data['auto_cfont'] = $this->config->get('auto_cfont');
$template->data['auto_margin'] = $this->config->get('auto_margin');
$template->data['auto_flogo'] = $this->config->get('auto_flogo');
$template->data['auto_border'] = $this->config->get('auto_border');
$template->data['auto_pad'] = $this->config->get('auto_pad');
if ($this->config->get('savegoogle_drive2')==1) {
			$show_extra2 = '1';
		} else {
			$show_extra2 = '0'; }
		$template->data['extra6'] = $show_extra2;
		if ($this->config->get('savegoogle_drive')==1) {
			$show_extra = '1';
		} else {
			$show_extra = '0'; }
		$template->data['extra5'] = $show_extra;

$template->data['order_comment'] = $order_info['comment'];

		if ($comment && $notify) {
			$template->data['comment'] = nl2br($comment);
		} else {
			$template->data['comment'] = '';
		}

		if ($order_info['payment_address_format']) {
			$format = $order_info['payment_address_format'];
		} else {
			$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
		}

		$find = array(
			'{firstname}',
			'{lastname}',
			'{company}',
			'{address_1}',
			'{address_2}',
			'{city}',
			'{postcode}',
			'{zone}',
			'{zone_code}',
			'{country}'
			);
		
		$replace = array(
			'firstname' => $order_info['payment_firstname'],
			'lastname'  => $order_info['payment_lastname'],
			'company'   => $order_info['payment_company'],
	'payment_company_id' => $order_info['payment_company_id'],
				'payment_tax_id' => $order_info['payment_tax_id'],
			'address_1' => $order_info['payment_address_1'],
			'address_2' => $order_info['payment_address_2'],
			'city'      => $order_info['payment_city'],
			'postcode'  => $order_info['payment_postcode'],
			'zone'      => $order_info['payment_zone'],
			'zone_code' => $order_info['payment_zone_code'],
			'country'   => $order_info['payment_country']  
			);
		
		$template->data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));						

		if ($order_info['shipping_address_format']) {
			$format = $order_info['shipping_address_format'];
		} else {
			$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
		}

		$find = array(
			'{firstname}',
			'{lastname}',
			'{company}',
			'{address_1}',
			'{address_2}',
			'{city}',
			'{postcode}',
			'{zone}',
			'{zone_code}',
			'{country}'
			);
		
		$replace = array(
			'firstname' => $order_info['shipping_firstname'],
			'lastname'  => $order_info['shipping_lastname'],
			'company'   => $order_info['shipping_company'],
			'address_1' => $order_info['shipping_address_1'],
			'address_2' => $order_info['shipping_address_2'],
			'city'      => $order_info['shipping_city'],
			'postcode'  => $order_info['shipping_postcode'],
			'zone'      => $order_info['shipping_zone'],
			'zone_code' => $order_info['shipping_zone_code'],
			'country'   => $order_info['shipping_country']  
			);
		
		$template->data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

			// Products
		$template->data['products'] = array();
		$order_product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'");		
		foreach ($order_product_query->rows as $product) {
			$option_data = array();
$order_sku_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product['product_id'] . "'");
                          foreach ($order_sku_query->rows as $product2) {

				
$product_query = $this->db->query("SELECT image FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product['product_id'] . "'");

foreach ($product_query->rows as $prodquery) { 

$image = $prodquery['image']; 


}

$string = $this->model_tool_image->resize($image, 50, 50);

$thumb2 = substr($string, strpos($string, "image")+6);

$thumb = DIR_IMAGE . $thumb2;

$thumb4 = $string = $this->model_tool_image->resize($image, 50, 50);



			$order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . (int)$product['order_product_id'] . "'");

			foreach ($order_option_query->rows as $option) {
				if ($option['type'] != 'file') {
					$value = $option['value'];
				} else {
					$value = utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.'));
				}

				$option_data[] = array(
					'name'  => $option['name'],
					'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
					);					
			}

			$template->data['products'][] = array(
'thumb'     => $thumb,
							'thumb4'     => $thumb4,

			'href'    	 => $this->url->link('product/product', 'product_id=' . $product['product_id']),
				'name'     => $product['name'],
				'model'    => $product['model'],
 'sku'    => $product2['sku'],

				
				'option'   => $option_data,
				'quantity' => $product['quantity'],
				'price'    => $this->currency->format(0.00 , $order_info['currency_code'], $order_info['currency_value']),
				'total'    => $this->currency->format(0.00, $order_info['currency_code'], $order_info['currency_value'])
				);
		}

			// Vouchers
		$template->data['vouchers'] = array();
 }

				

		foreach ($order_voucher_query->rows as $voucher) {
			$template->data['vouchers'][] = array(
				'description' => $voucher['description'],
				'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']),
				);
		}

		$template->data['totals'] = $order_total_query->rows;
 if($this->config->get('custfoot23')==1){ $html2 = $template->fetch('default/template/mail/autop2.tpl');} else {
		$html2 = $template->fetch('default/template/mail/autop.tpl');}

		/*	if($this->config->get('custfoot23')==1){ $html2 = $template->fetch('default/template/mail/autop2.tpl');} else {
			$html2 = $template->fetch('default/template/mail/autop.tpl');}*/
			
			if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mail/order.tpl')) {
				$html = $template->fetch($this->config->get('config_template') . '/template/mail/order.tpl');
			} else {
				$html = $template->fetch('default/template/mail/order.tpl');
			}

      if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/mail/xorder.tpl')) {
    
      $xhtml = $template->fetch($this->config->get('config_template') . '/template/mail/xorder.tpl');
    
    } else {
     
      $xhtml = $template->fetch('default/template/mail/xorder.tpl');
      
    }

    
     // printnode work
   
    $this->createPdf(array('html'=>$xhtml),'drawer/',(($this->session->data['shipping_method']['code']=='multiflatrate.multiflatrate_0')?true:false),$order_id);
      
    
    //end printnode work
			
			// Text Mail
			$text  = sprintf($language->get('text_new_greeting'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8')) . "\n\n";
			$text .= $language->get('text_new_order_id') . ' ' . $order_id . "\n";
			$text .= $language->get('text_new_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_info['date_added'])) . "\n";
			$text .= $language->get('text_new_order_status') . ' ' . $order_status . "\n\n";
			
			if ($comment && $notify) {
				$text .= $language->get('text_new_instruction') . "\n\n";
				$text .= $comment . "\n\n";
			}
			
			// Products
			$text .= $language->get('text_new_products') . "\n";
			
			foreach ($order_product_query->rows as $product) {
				$text .= $product['quantity'] . 'x ' . $product['name'] . ' (' . $product['model'] . ') ' . html_entity_decode($this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']), ENT_NOQUOTES, 'UTF-8') . "\n";
				
				$order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . $product['order_product_id'] . "'");
				
				foreach ($order_option_query->rows as $option) {
					$text .= chr(9) . '-' . $option['name'] . ' ' . (utf8_strlen($option['value']) > 20 ? utf8_substr($option['value'], 0, 20) . '..' : $option['value']) . "\n";
				}
			}
			
			foreach ($order_voucher_query->rows as $voucher) {
				$text .= '1x ' . $voucher['description'] . ' ' . $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']);
			}

			$text .= "\n";
			
			$text .= $language->get('text_new_order_total') . "\n";
			
			foreach ($order_total_query->rows as $total) {
				$text .= $total['title'] . ': ' . html_entity_decode($total['text'], ENT_NOQUOTES, 'UTF-8') . "\n";
			}			
			
			$text .= "\n";
			
			if ($order_info['customer_id']) {
				$text .= $language->get('text_new_link') . "\n";
				$text .= $order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_id . "\n\n";
			}

			if ($order_download_query->num_rows) {
				$text .= $language->get('text_new_download') . "\n";
				$text .= $order_info['store_url'] . 'index.php?route=account/download' . "\n\n";
			}
			
			if ($order_info['comment']) {
				$text .= $language->get('text_new_comment') . "\n\n";
				$text .= $order_info['comment'] . "\n\n";
			}
			
			$text .= "Please reply to this email if you have any questions." . "\n\n";
			
			// if (($order_info['shipping_code'] == 'multiflatrate.multiflatrate_0')) {
				
			// 	$filesz = HTTP_SERVER ."/catalog/controller/icache/files/printmachineclient.php";
			// 	function async_get1($url) {
			// // $parts=parse_url($url);
			// // $fp = fsockopen($parts['host'], isset($parts['port'])?$parts['port']:80, $errno, $errstr, 30);
			// // $out = "GET ".$parts['path']." HTTP/1.1\r\n";
			// // $out.= "Host: ".$parts['host']."\r\n";
			// // $out.= "Connection: Close\r\n\r\n";
			// // fwrite($fp, $out);
			// // fclose($fp);

			// 		$parts=parse_url($url);
			// //$fp = fsockopen($parts['host'], isset($parts['port'])?$parts['port']:80, $errno, $errstr, 30);
			// 		$fp = fsockopen('ssl://'.$parts['host'], isset($parts['port'])?$parts['port']:443, $errno, $errstr, 30);
			// 		$out = "GET ".$parts['path']." HTTP/1.1\r\n";
			// 		$out.= "Host: ".$parts['host']."\r\n";
			// 		$out.= "Connection: Close\r\n\r\n";
			// 		fwrite($fp, $out);
			// 		fclose($fp);
			// 	}
			// 	async_get1($filesz);
			// }

			$mail = new Mail(); 
			$mail->protocol = $this->config->get('config_mail_protocol');
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->hostname = $this->config->get('config_smtp_host');
			$mail->username = $this->config->get('config_smtp_username');
			$mail->password = $this->config->get('config_smtp_password');
			$mail->port = $this->config->get('config_smtp_port');
			$mail->timeout = $this->config->get('config_smtp_timeout');			
			$mail->setTo($order_info['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender($order_info['store_name']);
			$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
			$mail->setHtml($html);
			$mail->setText(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
			$mail->send();

			// Admin Alert Mail
			if ($this->config->get('config_alert_mail')) {
				$subject = sprintf($language->get('text_new_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'), $order_id);
				
				// Text 
				$text  = $language->get('text_new_received') . "\n\n";
				$text .= $language->get('text_new_order_id') . ' ' . $order_id . "\n";
				$text .= $language->get('text_new_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_info['date_added'])) . "\n";
				$text .= $language->get('text_new_order_status') . ' ' . $order_status . "\n\n";
				$text .= $language->get('text_new_products') . "\n";
				
				foreach ($order_product_query->rows as $product) {
					$text .= $product['quantity'] . 'x ' . $product['name'] . ' (' . $product['model'] . ') ' . html_entity_decode($this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']), ENT_NOQUOTES, 'UTF-8') . "\n";
					
					$order_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "' AND order_product_id = '" . $product['order_product_id'] . "'");
					
					foreach ($order_option_query->rows as $option) {
						if ($option['type'] != 'file') {
							$value = $option['value'];
						} else {
							$value = utf8_substr($option['value'], 0, utf8_strrpos($option['value'], '.'));
						}

						$text .= chr(9) . '-' . $option['name'] . ' ' . (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value) . "\n";
					}
				}
				
				foreach ($order_voucher_query->rows as $voucher) {
					$text .= '1x ' . $voucher['description'] . ' ' . $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']);
				}

				$text .= "\n";

				$text .= $language->get('text_new_order_total') . "\n";
				
				foreach ($order_total_query->rows as $total) {
					$text .= $total['title'] . ': ' . html_entity_decode($total['text'], ENT_NOQUOTES, 'UTF-8') . "\n";
				}			
				
				$text .= "\n";
				
				if ($order_info['comment']) {
					$text .= $language->get('text_new_comment') . "\n\n";
					$text .= $order_info['comment'] . "\n\n";
				}

				$mail = new Mail(); 
				$mail->protocol = $this->config->get('config_mail_protocol');
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->hostname = $this->config->get('config_smtp_host');
				$mail->username = $this->config->get('config_smtp_username');
				$mail->password = $this->config->get('config_smtp_password');
				$mail->port = $this->config->get('config_smtp_port');
				$mail->timeout = $this->config->get('config_smtp_timeout');
				$mail->setTo($this->config->get('config_email'));
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender($order_info['store_name']);
				$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
				$mail->setText(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
				$mail->send();
				
				// Send to additional alert emails
				$emails = explode(',', $this->config->get('config_alert_emails'));
				
				foreach ($emails as $email) {
					if ($email && preg_match('/^[^\@]+@.*\.[a-z]{2,6}$/i', $email)) {
						$mail->setTo($email);
						$mail->send();
					}
				}				
			}		
		}
	}
	
	
	
	public function update($order_id, $order_status_id, $comment = '', $notify = false) {
		$order_info = $this->getOrder($order_id);

		if ($order_info && $order_info['order_status_id']) {
			// Fraud Detection
			if ($this->config->get('config_fraud_detection')) {
				$this->load->model('checkout/fraud');
				
				$risk_score = $this->model_checkout_fraud->getFraudScore($order_info);
				
				if ($risk_score > $this->config->get('config_fraud_score')) {
					$order_status_id = $this->config->get('config_fraud_status_id');
				  $this->trelloCard($order_info);
        }
			}			

			// Blacklist
			$status = false;
			
			$this->load->model('account/customer');
			
			if ($order_info['customer_id']) {

				$results = $this->model_account_customer->getIps($order_info['customer_id']);
				
				foreach ($results as $result) {
					if ($this->model_account_customer->isBlacklisted($result['ip'])) {
						$status = true;
						
						break;
					}
				}
			} else {
				$status = $this->model_account_customer->isBlacklisted($order_info['ip']);
			}
			
			if ($status) {
				$order_status_id = $this->config->get('config_order_status_id');
			}		

			$this->db->query("UPDATE `" . DB_PREFIX . "order` SET order_status_id = '" . (int)$order_status_id . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");

			$this->db->query("INSERT INTO " . DB_PREFIX . "order_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");

			// Send out any gift voucher mails
			if ($this->config->get('config_complete_status_id') == $order_status_id) {
				$this->load->model('checkout/voucher');

				$this->model_checkout_voucher->confirm($order_id);
			}	

			if ($notify) {
				$language = new Language($order_info['language_directory']);
				$language->load($order_info['language_filename']);
				$language->load('mail/order');

				$subject = sprintf($language->get('text_update_subject'), html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'), $order_id);

				$message  = $language->get('text_update_order') . ' ' . $order_id . "\n";
				$message .= $language->get('text_update_date_added') . ' ' . date($language->get('date_format_short'), strtotime($order_info['date_added'])) . "\n\n";
				
				$order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE order_status_id = '" . (int)$order_status_id . "' AND language_id = '" . (int)$order_info['language_id'] . "'");
				
				if ($order_status_query->num_rows) {
					$message .= $language->get('text_update_order_status') . "\n\n";
					$message .= $order_status_query->row['name'] . "\n\n";					
				}
				
				if ($order_info['customer_id']) {
					$message .= $language->get('text_update_link') . "\n";
					$message .= $order_info['store_url'] . 'index.php?route=account/order/info&order_id=' . $order_id . "\n\n";
				}
				
				if ($comment) { 
					$message .= $language->get('text_update_comment') . "\n\n";
					$message .= $comment . "\n\n";
				}

				$message .= $language->get('text_update_footer');

				$mail = new Mail();
				$mail->protocol = $this->config->get('config_mail_protocol');
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->hostname = $this->config->get('config_smtp_host');
				$mail->username = $this->config->get('config_smtp_username');
				$mail->password = $this->config->get('config_smtp_password');
				$mail->port = $this->config->get('config_smtp_port');
				$mail->timeout = $this->config->get('config_smtp_timeout');				
				$mail->setTo($order_info['email']);
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender($order_info['store_name']);
				$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
				$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
				$mail->send();
			}
		}
	}
	public function getReplacementOrder ($order_id)	{
		$query = $this->db->query("SELECT order_id FROM " . DB_PREFIX . "order WHERE ref_order_id = '" . $order_id . "'");
		return $query->row;
	}

	public function getReplacementRef ($order_id)	{
		$query = $this->db->query("SELECT ref_order_id, order_id FROM " . DB_PREFIX . "order WHERE order_id = '" . (int)$order_id . "'");
		$order = $query->row;
		$order_id = ($order['ref_order_id'])? $order['ref_order_id']: $order_id;
		return $order_id;
	}
  private function trelloCard($order_info)
  {
    $transaction_check = $this->db->query("SELECT * FROM ".DB_PREFIX."payflow_admin_tools WHERE order_id='".(int)$order_info['order_id']."'");
    $transaction_row = $transaction_check->row;
    if(!$transaction_row)
    {
      $transaction_check = $this->db->query("SELECT * FROM ".DB_PREFIX."paypal_admin_tools WHERE order_id='".(int)$order_info['order_id']."'");
    $transaction_row = $transaction_check->row;
    }
    
    $avs_data='';
    $transaction_id = $transaction_row['transaction_id'];
    if($transaction_row['avsaddr'])
    {
    $avs_data = 'AVSADDR: '.$transaction_row['avsaddr'].', AVSZIP: '.$transaction_row['avszip'].',CVV2MATCH: '.$transaction_row['cvv2match'];
  
    }
    


    $t_data = array( 'order_id' => $order_info['order_id'],
                            'type' => 'put Hold On',
                            'user_name' => 'PPUSA System',
                            'customer_name' => $order_info['firstname'].' '.$order_info['lastname'],
                            'customer_telephone' => $order_info['telephone'],
                            'shipping_address'  => $order_info['shipping_address_1'],' , '.$order_info['shipping_city'].', '.$order_info['shipping_zone'].' '.$order_info['shipping_postcode'].'.',
                            'billing_address'  => $order_info['payment_address_1'],' , '.$order_info['payment_city'].', '.$order_info['payment_zone'].' '.$order_info['payment_postcode'].'.',
                            'transaction_id'  =>$transaction_id,
                            'avs_data'  =>  $avs_data,
                            'list2'=>1,
                            'url' => base64_encode(HTTP_SERVER . 'imp/viewOrderDetail.php?order=' . $order_info['order_id']));

                        $data_xt = array();
                        foreach ($t_data as $key => $value) {
                            $data_xt[] = $key . '=' . $value;
                        }

                        $url = HTTP_SERVER . 'imp/trello/hitTrello.php';

                        $data_string = implode('&', $data_xt);

                        $ch = curl_init();

                        curl_setopt($ch, CURLOPT_URL, $url);

                        curl_setopt($ch,CURLOPT_POST, 1);
                        curl_setopt($ch,CURLOPT_POSTFIELDS, $data_string);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

                        $output = curl_exec($ch);

                        curl_close($ch);

                        $result = json_decode($output, true);

  }
  public function createPdf ($data = array(), $dir = '',$is_print=false,$order_id='') {
    
    require_once(DIR_SYSTEM . 'html2_pdf_lib/html2pdf.class.php');
    $size = array ($data['pageWidth'], $data['pageHeight']);
    try {
      $html2pdf = new HTML2PDF('P', array(215.9,279.4), 'en');
      $html2pdf->setDefaultFont('courier');
      $html2pdf->writeHTML($data['html']);
     if($order_id!='')
     {
      $filename = $order_id;
     }
     else
     {
      $filename = time();
      
     }
      $file = $dir .  $filename . '.pdf';
     
      $filePath = DIR_IMAGE . $file;
       $this->session->data['checkout_order_pdf_file']  = HTTPS_IMAGE.$file;
      $html2pdf->Output($filePath, 'F');
    } catch (HTML2PDF_exception $e) {
      echo $e;
      exit;
    }
    if($is_print){
     $this->printNodeSlip($file);
      
    }

  }
  public function printNodeSlip ($pdf) {
    
      $this->printNodePDF($pdf, 'Beta Local Order Print');
    
  }
  public function printNodePDF($pdf, $title)  {
    if (strpos($pdf, DIR_IMAGE) === false) {
      $pdf = DIR_IMAGE . $pdf;
    }
    require_once(DIR_SYSTEM . 'PrintNode-PHP-master/vendor/autoload.php');
    //$credentials = 'f9305047bdf9a187cfc02de4780b8e0c7cb3261a'; /*Dev ID*/
    $credentials = '19982dc5978951c99f98cdcfe5feb4881cc5147b';
    $request = new PrintNode\Request($credentials);
    // $computers = $request->getComputers();
    $printers = $request->getPrinters();
      // print_r($printers);exit;
    // $printJobs = $request->getPrintJobs();
    $printJob = new PrintNode\PrintJob();
    // $printJob->printer = 130442; //$printers[1]; /*Dev id*/
    // $printJob->printer = 130444; //$printers[1]; /*Dev id*/
    $printJob->printer = 182841;
    $printJob->contentType = 'pdf_base64';
    $printJob->content = base64_encode(file_get_contents($pdf));
    $printJob->source = 'My App/1.0';
    $printJob->title = $title;
    $response = $request->post($printJob);
    $statusCode = $response->getStatusCode();
    $statusMessage = $response->getStatusMessage();
  }
}
?>