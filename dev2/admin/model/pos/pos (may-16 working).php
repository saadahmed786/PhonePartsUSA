<?php

class ModelPosPos extends Model {
	
	/*
	 * POS database table definition
	 * 
	 */
	
	// This function is how POS module creates it's tables to store order payment entries. You would call this function in your controller in a
	// function called install(). The install() function is called automatically by OC versions 1.4.9.x, and maybe 1.4.8.x when a module is
	// installed in admin.
	public function createModuleTables() {

		$query  = "ALTER TABLE `" . DB_PREFIX . "order` ADD `card_no` TINYINT( 4 ) NULL AFTER `payment_code`;";
		$query .= "ALTER TABLE `" . DB_PREFIX . "order` ADD `user_id` INT( 100 ) NULL AFTER `customer_id`;";
		$query .= "ALTER TABLE `" . DB_PREFIX . "user` ADD `cash` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0' AFTER `ip` ,
		ADD `card` DECIMAL( 10, 2 ) NOT NULL DEFAULT '0' AFTER `cash` ;";

		$query .= "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "pos_withdraw` (
			`pos_withdraw_id` int(100) NOT NULL AUTO_INCREMENT,
			`user_id` int(100) NOT NULL,
			`amount` decimal(10,2) NOT NULL,
			`date` datetime NOT NULL,
			PRIMARY KEY (`pos_withdraw_id`)
			) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";

            /*
            $query .= "INSERT INTO '" . DB_PREFIX . "user_group' values('','point of sale','a:2:{s:6:\"access\";a:2:{i:0;s:10:\"module/pos\";i:1;s:10:\"sale/order\";}s:6:\"modify\";a:2:{i:0;s:10:\"module/pos\";i:1;s:10:\"sale/order\";}}')";

            $this->db->query($query);

            $user_group_id = $this->db->getLastId();

            //add setting data
            $this->db->query("DELETE '" . DB_PREFIX . "setting' WHERE key= 'pos_user_group_id'");
            $this->db->query("INSERT INTO '" . DB_PREFIX . "setting' values('','0','POS',pos_user_group_id','".$user_group_id."',0)");
            */
        }


        public function createVoidTable() {


        	$query .= "
        	CREATE TABLE TABLE IF NOT EXISTS `".DB_PREFIX."void_product` (
        		`void_product_id` int(11) NOT NULL AUTO_INCREMENT,
        		`order_id` int(11) NOT NULL,
        		`product_id` int(11) NOT NULL,
        		`quantity` int(11) NOT NULL,
        		`amount` decimal(15,4) NOT NULL,
        		`user_id` int(11) NOT NULL,
        		`date_added` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ,
        		PRIMARY KEY (`void_product_id`)
        		) ENGINE=InnoDB DEFAULT CHARSET=latin1
";


}

public function deleteModuleTables() {
		// $query = $this->db->query("DROP TABLE " . DB_PREFIX . "order_payment");
}

public function addPayment($data) {            
	$sql = "update " . DB_PREFIX . "user set cash = cash + ".$data['cash'].", card= card + ".$data['card']." where user_id = ".$data['user_id'];
	$this->db->query($sql);
}

public function editPayment($order_id,$data){
	$query = $this->db->query("select user_id, total, payment_method from " . DB_PREFIX . "order where order_id = '".$order_id."'");
	$row = $query->row;

	$cash = $card = 0;

	if($row['total'] > $data['total'] || $data['payment_method'] != 'Card'){
		$cash = $data['total'] - $row['total'];
	}elseif($data['payment_method'] == 'Card'){
		$card = $data['total'] - $row['total'];
	}

	if($data['payment_method']=='Cash,Card')
	{
		$cash = $data['split_cash'];	
		$card = $data['split_card'];
	}

	$sql = "update " . DB_PREFIX . "user set cash = cash + ".$cash.", card= card + ".$card." where user_id = ".$data['user_id'];
	$this->db->query($sql);
}

public function addOrder($data) {
	$this->load->model('setting/store');

	$store_info = $this->model_setting_store->getStore($data['store_id']);

	if ($store_info) {
		$store_name = $store_info['name'];
		$store_url = $store_info['url'];
	} else {
		$store_name = $this->config->get('config_name');
		$store_url = HTTP_CATALOG;
	}

	$this->load->model('setting/setting');

	$setting_info = $this->model_setting_setting->getSetting('setting', $data['store_id']);

	if (isset($setting_info['invoice_prefix'])) {
		$invoice_prefix = $setting_info['invoice_prefix'];
	} else {
		$invoice_prefix = $this->config->get('config_invoice_prefix');
	}

	$this->load->model('localisation/country');

	$this->load->model('localisation/zone');

	$country_info = $this->model_localisation_country->getCountry($data['shipping_country_id']);

	if ($country_info) {
		$shipping_country = $country_info['name'];
		$shipping_address_format = $country_info['address_format'];
	} else {
		$shipping_country = '';	
		$shipping_address_format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
	}	

	$zone_info = $this->model_localisation_zone->getZone($data['shipping_zone_id']);

	if ($zone_info) {
		$shipping_zone = $zone_info['name'];
	} else {
		$shipping_zone = '';			
	}	

	$country_info = $this->model_localisation_country->getCountry($data['payment_country_id']);

	if ($country_info) {
		$payment_country = $country_info['name'];
		$payment_address_format = $country_info['address_format'];			
	} else {
		$payment_country = '';	
		$payment_address_format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';					
	}
	
	$zone_info = $this->model_localisation_zone->getZone($data['payment_zone_id']);

	if ($zone_info) {
		$payment_zone = $zone_info['name'];
	} else {
		$payment_zone = '';			
	}	

	$this->load->model('localisation/currency');

	$currency_info = $this->model_localisation_currency->getCurrencyByCode($this->config->get('config_currency'));

	if ($currency_info) {
		$currency_id = $currency_info['currency_id'];
		$currency_code = $currency_info['code'];
		$currency_value = $currency_info['value'];
	} else {
		$currency_id = 0;
		$currency_code = $this->config->get('config_currency');
		$currency_value = 1.00000;			
	}

	$this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET card_no ='" . $this->db->escape($data['card_no']) . "',  invoice_prefix = '" . $this->db->escape($invoice_prefix) . "', store_id = '" . (int)$data['store_id'] . "', store_name = '" . $this->db->escape($store_name) . "',store_url = '" . $this->db->escape($store_url) . "', customer_id = '" . (int)$data['customer_id'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', payment_company = '" . $this->db->escape($data['payment_company']) . "', payment_company_id = '" . $this->db->escape($data['payment_company_id']) . "', payment_tax_id = '" . $this->db->escape($data['payment_tax_id']) . "', payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', payment_city = '" . $this->db->escape($data['payment_city']) . "', payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', payment_country = '" . $this->db->escape($payment_country) . "', payment_country_id = '" . (int)$data['payment_country_id'] . "', payment_zone = '" . $this->db->escape($payment_zone) . "', payment_zone_id = '" . (int)$data['payment_zone_id'] . "', payment_address_format = '" . $this->db->escape($payment_address_format) . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "', shipping_company = '" . $this->db->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($shipping_country) . "', shipping_country_id = '" . (int)$data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($shipping_zone) . "', shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($shipping_address_format) . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "', comment = '" . $this->db->escape($data['comment']) . "', order_status_id = '" . (int)$data['order_status_id'] . "', affiliate_id  = '" . (int)$data['affiliate_id'] . "', language_id = '" . (int)$this->config->get('config_language_id') . "', currency_id = '" . (int)$currency_id . "', currency_code = '" . $this->db->escape($currency_code) . "', currency_value = '" . (float)$currency_value . "', date_added = NOW(), date_modified = NOW()");

	$order_id = $this->db->getLastId();

	if (isset($data['order_product'])) {		
		foreach ($data['order_product'] as $order_product) {	
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$order_product['product_id'] . "', name = '" . $this->db->escape($order_product['name']) . "', model = '" . $this->db->escape($order_product['model']) . "', quantity = '" . (int)$order_product['quantity'] . "', price = '" . (float)$order_product['price'] . "', total = '" . (float)$order_product['total'] . "', tax = '" . (float)$order_product['tax'] . "', reward = '" . (int)$order_product['reward'] . "'");
			
			$order_product_id = $this->db->getLastId();

			$this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE product_id = '" . (int)$order_product['product_id'] . "' AND subtract = '1'");

			if (isset($order_product['order_option'])) {
				foreach ($order_product['order_option'] as $order_option) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "order_option SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', product_option_id = '" . (int)$order_option['product_option_id'] . "', product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "', name = '" . $this->db->escape($order_option['name']) . "', `value` = '" . $this->db->escape($order_option['value']) . "', `type` = '" . $this->db->escape($order_option['type']) . "'");

					$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "' AND subtract = '1'");
				}
			}

			if (isset($order_product['order_download'])) {
				foreach ($order_product['order_download'] as $order_download) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "order_download SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', name = '" . $this->db->escape($order_download['name']) . "', filename = '" . $this->db->escape($order_download['filename']) . "', mask = '" . $this->db->escape($order_download['mask']) . "', remaining = '" . (int)$order_download['remaining'] . "'");
				}
			}
		}
	}

	if (isset($data['order_voucher'])) {	
		foreach ($data['order_voucher'] as $order_voucher) {	
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_voucher SET order_id = '" . (int)$order_id . "', voucher_id = '" . (int)$order_voucher['voucher_id'] . "', description = '" . $this->db->escape($order_voucher['description']) . "', code = '" . $this->db->escape($order_voucher['code']) . "', from_name = '" . $this->db->escape($order_voucher['from_name']) . "', from_email = '" . $this->db->escape($order_voucher['from_email']) . "', to_name = '" . $this->db->escape($order_voucher['to_name']) . "', to_email = '" . $this->db->escape($order_voucher['to_email']) . "', voucher_theme_id = '" . (int)$order_voucher['voucher_theme_id'] . "', message = '" . $this->db->escape($order_voucher['message']) . "', amount = '" . (float)$order_voucher['amount'] . "'");
			
			$this->db->query("UPDATE " . DB_PREFIX . "voucher SET order_id = '" . (int)$order_id . "' WHERE voucher_id = '" . (int)$order_voucher['voucher_id'] . "'");
		}
	}

		// Get the total
	$total = 0;

	if (isset($data['order_total'])) {		
		foreach ($data['order_total'] as $order_total) {	
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int)$order_id . "', code = '" . $this->db->escape($order_total['code']) . "', title = '" . $this->db->escape($order_total['title']) . "', text = '" . $this->db->escape($order_total['text']) . "', `value` = '" . (float)$order_total['value'] . "', sort_order = '" . (int)$order_total['sort_order'] . "'");
		}

		$total += $order_total['value'];
	}

		// Affiliate
	$affiliate_id = 0;
	$commission = 0;

	if (!empty($this->request->post['affiliate_id'])) {
		$affiliate_id = (int)$this->request->post['affiliate_id'];
	}

	if ($affiliate_id > 0 ) {
		$this->load->model('sale/affiliate');

		$affiliate_info = $this->model_sale_affiliate->getAffiliate($affiliate_id);

		if ($affiliate_info) {
			$commission = ($total / 100) * $affiliate_info['commission']; 
		}
	}

		// Update order total			 
	$this->db->query("UPDATE `" . DB_PREFIX . "order` SET user_id = '".$this->user->getId()."', total = '" . (float)$total . "', affiliate_id = '" . (int)$affiliate_id . "', commission = '" . (float)$commission . "' WHERE order_id = '" . (int)$order_id . "'"); 	

	return $order_id;
}

public function editOrder($order_id, $data) {

	$this->load->model('localisation/country');

	$this->load->model('localisation/zone');

	$country_info = $this->model_localisation_country->getCountry($data['shipping_country_id']);

	if ($country_info) {
		$shipping_country = $country_info['name'];
		$shipping_address_format = $country_info['address_format'];
	} else {
		$shipping_country = '';	
		$shipping_address_format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
	}	

	$zone_info = $this->model_localisation_zone->getZone($data['shipping_zone_id']);

	if ($zone_info) {
		$shipping_zone = $zone_info['name'];
	} else {
		$shipping_zone = '';			
	}	

	$country_info = $this->model_localisation_country->getCountry($data['payment_country_id']);

	if ($country_info) {
		$payment_country = $country_info['name'];
		$payment_address_format = $country_info['address_format'];			
	} else {
		$payment_country = '';	
		$payment_address_format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';					
	}

	$zone_info = $this->model_localisation_zone->getZone($data['payment_zone_id']);

	if ($zone_info) {
		$payment_zone = $zone_info['name'];
	} else {
		$payment_zone = '';			
	}			

		// Restock products before subtracting the stock later on
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

	$this->db->query("UPDATE `" . DB_PREFIX . "order` SET firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', fax = '" . $this->db->escape($data['fax']) . "', payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', payment_company = '" . $this->db->escape($data['payment_company']) . "', payment_company_id = '" . $this->db->escape($data['payment_company_id']) . "', payment_tax_id = '" . $this->db->escape($data['payment_tax_id']) . "', payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', payment_city = '" . $this->db->escape($data['payment_city']) . "', payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', payment_country = '" . $this->db->escape($payment_country) . "', payment_country_id = '" . (int)$data['payment_country_id'] . "', payment_zone = '" . $this->db->escape($payment_zone) . "', payment_zone_id = '" . (int)$data['payment_zone_id'] . "', payment_address_format = '" . $this->db->escape($payment_address_format) . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "',  shipping_company = '" . $this->db->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($shipping_country) . "', shipping_country_id = '" . (int)$data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($shipping_zone) . "', shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($shipping_address_format) . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "', comment = '" . $this->db->escape($data['comment']) . "', order_status_id = '" . (int)$data['order_status_id'] . "', affiliate_id  = '" . (int)$data['affiliate_id'] . "', date_modified = NOW(),pos_date=NOW(),cash_split='".(float)$data['cash_split']."',card_split='".(float)$data['card_split']."' WHERE order_id = '" . (int)$order_id . "'");

	if($data['order_status_id']==7)
	{
		$_order_status= 'Canceled';	

		$t_data = array( 'order_id' => $order_id,
			'type' => 'canceled the',
			'user_name' =>  $this->user->getUserName(),
			'url' => base64_encode(HTTP_CATALOG . 'imp/viewOrderDetail.php?order=' . $order_id));

		$_xdata = array();
		foreach ($t_data as $key => $value) {
			$_xdata[] = $key . '=' . $value;
		}

		$url = HTTP_CATALOG . 'imp/trello/hitTrello.php';

		$data_string = implode('&', $_xdata);
		
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);

		curl_setopt($ch,CURLOPT_POST, 1);
		curl_setopt($ch,CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 

		$output = curl_exec($ch);

		curl_close($ch);

		$result = json_decode($output, true);
		
	}
	else
	{
		$_order_status='Shipped';	

	}

	$this->db->query("UPDATE inv_orders SET order_status='".$_order_status."' WHERE order_id='".(int)$order_id."'");

	$this->db->query("DELETE FROM " . DB_PREFIX . "order_product WHERE order_id = '" . (int)$order_id . "'"); 
	$this->db->query("DELETE FROM " . DB_PREFIX . "order_option WHERE order_id = '" . (int)$order_id . "'");
	$this->db->query("DELETE FROM " . DB_PREFIX . "order_download WHERE order_id = '" . (int)$order_id . "'");
	
	

	if (isset($data['order_product'])) {
		
		foreach ($data['order_product'] as $order_product) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_product_id = '', order_id = '" . (int)$order_id . "', product_id = '" . (int)$order_product['product_id'] . "', name = '" . $this->db->escape($order_product['name']) . "', model = '" . $this->db->escape($order_product['model']) . "', quantity = '" . (int)$order_product['quantity'] . "', price = '" . (float)$order_product['price'] . "', total = '" . (float)$order_product['total'] . "', tax = '" . (float)$order_product['tax'] . "', reward = '" . (int)$order_product['reward'] . "'");

			$order_product_id = $this->db->getLastId();

			$this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE product_id = '" . (int)$order_product['product_id'] . "' AND subtract = '1'");

			if (isset($order_product['order_option'])) {
				foreach ($order_product['order_option'] as $order_option) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "order_option SET order_option_id = '', order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', product_option_id = '" . (int)$order_option['product_option_id'] . "', product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "', name = '" . $this->db->escape($order_option['name']) . "', `value` = '" . $this->db->escape($order_option['value']) . "', `type` = '" . $this->db->escape($order_option['type']) . "'");


					$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity - " . (int)$order_product['quantity'] . ") WHERE product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "' AND subtract = '1'");
				}
			}

			if (isset($order_product['order_download'])) {
				foreach ($order_product['order_download'] as $order_download) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "order_download SET order_download_id = '', order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', name = '" . $this->db->escape($order_download['name']) . "', filename = '" . $this->db->escape($order_download['filename']) . "', mask = '" . $this->db->escape($order_download['mask']) . "', remaining = '" . (int)$order_download['remaining'] . "'");
				}
			}
		}
	}
	

	$this->db->query("DELETE FROM " . DB_PREFIX . "order_voucher WHERE order_id = '" . (int)$order_id . "'"); 

	if (isset($data['order_voucher'])) {

		foreach ($data['order_voucher'] as $order_voucher) {	

			$this->db->query("INSERT INTO " . DB_PREFIX . "order_voucher SET order_voucher_id = '', order_id = '" . (int)$order_id . "', voucher_id = '" . (int)$order_voucher['voucher_id'] . "', description = '" . $this->db->escape($order_voucher['description']) . "', code = '" . $this->db->escape($order_voucher['code']) . "', from_name = '" . $this->db->escape($order_voucher['from_name']) . "', from_email = '" . $this->db->escape($order_voucher['from_email']) . "', to_name = '" . $this->db->escape($order_voucher['to_name']) . "', to_email = '" . $this->db->escape($order_voucher['to_email']) . "', voucher_theme_id = '" . (int)$order_voucher['voucher_theme_id'] . "', message = '" . $this->db->escape($order_voucher['message']) . "', amount = '" . (float)$order_voucher['amount'] . "'");

			$this->db->query("UPDATE " . DB_PREFIX . "voucher SET order_id = '" . (int)$order_id . "' WHERE voucher_id = '" . (int)$order_voucher['voucher_id'] . "'");
			$this->db->query("INSERT INTO `" . DB_PREFIX . "voucher_history` SET voucher_id = '" . (int)$order_voucher['voucher_id'] . "', order_id = '" . (int)$order_id . "', amount = '" . (float)($order_voucher['amount']*-1) . "', date_added = NOW()");
		}

	}

		// Get the total
	$total = 0;
	$this->db->query("DELETE FROM " . DB_PREFIX . "order_total WHERE order_id = '" . (int)$order_id . "'"); 

	if (isset($data['order_total'])) {		
		foreach ($data['order_total'] as $order_total) {	
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int)$order_id . "', code = '" . $this->db->escape($order_total['code']) . "', title = '" . $this->db->escape($order_total['title']) . "', text = '" . $this->db->escape($order_total['text']) . "', `value` = '" . (float)$order_total['value'] . "', sort_order = '" . (int)$order_total['sort_order'] . "'");
		}

		$total += $order_total['value'];
	}

		// Affiliate
	$affiliate_id = 0;
	$commission = 0;

	if (!empty($this->request->post['affiliate_id'])) {
		$affiliate_id = (int)$this->request->post['affiliate_id'];
	}

	if ($affiliate_id > 0 ) {
		$this->load->model('sale/affiliate');

		$affiliate_info = $this->model_sale_affiliate->getAffiliate($affiliate_id);

		if ($affiliate_info) {
			$commission = ($total / 100) * $affiliate_info['commission']; 
		}
	}

		// Update order total			 
	$this->db->query("UPDATE `" . DB_PREFIX . "order` SET user_id = '".$this->user->getId()."', total = '" . (float)$total . "', affiliate_id = '" . (int)$affiliate_id . "', commission = '" . (float)$commission . "',pos_total='".(float)$data['pos_total']."' WHERE order_id = '" . (int)$order_id . "'"); 	
	$this->db->query("UPDATE inv_orders SET order_price='".(float)$total."',paid_price='".(float)$data['pos_total']."' WHERE order_id = '" . $order_id . "'");
	return $order_id;
}

	// add for Browse begin
public function getTopCategories() {
		// get all categories
	$query = $this->db->query("SELECT c.image, c.category_id, c.parent_id, cd.name FROM `" . DB_PREFIX . "category` c LEFT JOIN `" . DB_PREFIX . "category_description` cd ON c.category_id = cd.category_id WHERE c.parent_id = 0 and cd.language_id = '". (int)$this->config->get('config_language_id') . "' ORDER BY c.sort_order, LCASE(cd.name)");
	return $query->rows;
}

public function getCategories() {
		// get all categories
	$query = $this->db->query("SELECT c.image, c.category_id, c.parent_id, cd.name FROM `" . DB_PREFIX . "category` c LEFT JOIN `" . DB_PREFIX . "category_description` cd ON c.category_id = cd.category_id WHERE cd.language_id = '". (int)$this->config->get('config_language_id') . "'");
	return $query->rows;
}

public function getSubCategories($category_id) {
		// get all sub categories under the given category
	$query = $this->db->query("SELECT c.category_id, c.image, cd.name FROM `" . DB_PREFIX . "category` c LEFT JOIN `" . DB_PREFIX . "category_description` cd ON c.category_id = cd.category_id WHERE cd.language_id = '". (int)$this->config->get('config_language_id') . "' AND c.parent_id = '" . $category_id . "'");
	return $query->rows;
}

public function getProductByBarcode($barcode) {
		// get all products in the given category
	$query = $this->db->query("SELECT p.product_id, GROUP_CONCAT(po.option_id) as options from `" . DB_PREFIX . "product` p LEFT JOIN `" . DB_PREFIX . "product_option` po ON p.product_id = po.product_id WHERE p.sku = '" . $barcode . "'");
	return $query->row;
}

public function total_products($category_id) {
            // get all products in the given category
	$query = $this->db->query("SELECT count(p.product_id) as total FROM `" . DB_PREFIX . "product_to_category` pc LEFT JOIN `" . DB_PREFIX . "product` p ON pc.product_id = p.product_id LEFT JOIN `" . DB_PREFIX . "product_description` pd ON p.product_id = pd.product_id WHERE pd.language_id = '". (int)$this->config->get('config_language_id') . "' AND pc.category_id = '" . $category_id . "'  AND p.status = '1' AND p.quantity > 0");
	$result = $query->row;
	return $result['total'];
}

public function getProducts($category_id, $limit = 20, $offset = 0) {
            // get all products in the given category
	$query = $this->db->query("SELECT p.product_id, p.price, p.quantity, p.image, pd.name, GROUP_CONCAT(po.option_id) as options FROM `" . DB_PREFIX . "product_to_category` pc LEFT JOIN `" . DB_PREFIX . "product` p ON pc.product_id = p.product_id LEFT JOIN `" . DB_PREFIX . "product_description` pd ON p.product_id = pd.product_id LEFT JOIN `" . DB_PREFIX . "product_option` po ON p.product_id = po.product_id WHERE pd.language_id = '". (int)$this->config->get('config_language_id') . "' AND pc.category_id = '" . $category_id . "'  AND p.status = '1' AND p.quantity > 0 GROUP BY p.product_id LIMIT ".$offset.", ".$limit);
	return $query->rows;
}

public function total_search_products($q){
            // get all products in the given category
	$query = $this->db->query("SELECT count( p.product_id ) AS total from `" . DB_PREFIX . "product` p LEFT JOIN `" . DB_PREFIX . "product_description` pd ON p.product_id = pd.product_id WHERE pd.language_id = '". (int)$this->config->get('config_language_id') . "'  AND pd.name like '%".$q."%' AND p.status = '1' AND p.quantity > 0");
	$result= $query->row;
	return $result['total'];
}

public function searchProducts($q, $limit = 20, $offset = 0){
            // get all products in the given category
	$query = $this->db->query("SELECT p.product_id, p.price, p.quantity, p.image, pd.name, GROUP_CONCAT(po.option_id) as options FROM `" . DB_PREFIX . "product_to_category` pc LEFT JOIN `" . DB_PREFIX . "product` p ON pc.product_id = p.product_id LEFT JOIN `" . DB_PREFIX . "product_description` pd ON p.product_id = pd.product_id LEFT JOIN `" . DB_PREFIX . "product_option` po ON p.product_id = po.product_id WHERE pd.language_id = '". (int)$this->config->get('config_language_id') . "'  AND pd.name like '%".$q."%' AND p.status = '1' AND p.quantity > 0 GROUP BY p.product_id LIMIT ".$offset.", ".$limit);
	return $query->rows;
}

public function getCustomer($customer_id){
            //search customer by name 
	$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer`  WHERE customer_id = '".$customer_id."'");
	return $query->row;
}

public function searchCustomer($q){
            //search customer by name 
	$query = $this->db->query("SELECT c.firstname, c.lastname, c.customer_id FROM `" . DB_PREFIX . "customer` c WHERE c.firstname like '%".$q."%' or c.lastname like '%".$q."%'");
	return $query->rows;
}

public function getStatistics(){
	$query = $this->db->query("select user_id, username, firstname, lastname, cash, card from " . DB_PREFIX . "user");
	return $query->rows;
}

public function withdraw($data){
            //user_id, amount 
            //1) insert into oc_pos_withdraw 
            //2) cash = cash - amount on user 
	$this->db->query("insert into `" . DB_PREFIX . "pos_withdraw` set pos_withdraw_id = '', user_id ='".$data['user_id']."', amount= '".$data['amount']."', date = NOW()");
	$this->db->query("update `" . DB_PREFIX . "user` set cash = cash - ".$data['amount']." where user_id = '".$data['user_id']."'");
}

public function total_history($user_id){
	$query = $this->db->query("select count(*) as total from `" . DB_PREFIX . "pos_withdraw` pw left join `" . DB_PREFIX . "user` u on pw.user_id = u.user_id where pw.user_id='".$user_id."'");
	$row = $query->row;
	return $row['total'];
}

public function history($user_id, $limit = 10, $offset = 0){
	$query = $this->db->query("select u.username, u.firstname, u.lastname, pw.* from `" . DB_PREFIX . "pos_withdraw` pw left join `" . DB_PREFIX . "user` u on pw.user_id = u.user_id where pw.user_id='".$user_id."' ORDER BY pw.date DESC LIMIT ".$offset.", ".$limit);
	return $query->rows;
}

public function hold_cart($data){
	$this->db->query("insert into `" . DB_PREFIX . "cart_holder` set cart_holder_id = '', user_id ='".$data['user_id']."', name= '".$data['name']."', cart = '".serialize($data['cart'])."', date_created = NOW()"); 
	return $this->db->getLastId();
}

public function get_hold_cart_list(){
	$query = $this->db->query('select cart_holder_id, name, date_created from `' . DB_PREFIX . 'cart_holder` where user_id = "'.$this->user->getId().'"'); 
	return $query->rows;
}

public function hold_cart_select($id){
	$query = $this->db->query('SELECT * FROM `' . DB_PREFIX . 'cart_holder` WHERE cart_holder_id="'.$id.'"');
	return $query->row;
}

public function hold_cart_delete($id){
	$this->db->query('DELETE FROM `' . DB_PREFIX . 'cart_holder` WHERE cart_holder_id="'.$id.'"');
}

public function get_today_card($user_id){
	$query = $this->db->query('SELECT sum(pos_total) as total FROM `' . DB_PREFIX . 'order` WHERE  payment_method ="Card" AND DATE(date_modified) ="'.date('Y-m-d').'"');
	$row = $query->row;

	$query = $this->db->query('SELECT sum(card_split) as total FROM `' . DB_PREFIX . 'order` WHERE  payment_method ="Cash,Card" AND DATE(date_modified) ="'.date('Y-m-d').'"');
	$row2 = $query->row;


	return $row['total']+$row2['total'];
}

public function get_today_paypal($user_id){
	$query = $this->db->query('SELECT sum(pos_total) as total FROM `' . DB_PREFIX . 'order` WHERE payment_method in("PayPal","Paypal Express") AND DATE(date_modified) ="'.date('Y-m-d').'" and order_status_id IN(3,11) AND user_id IS NOT NULL');
	$row = $query->row;
	return $row['total'];
}





public function get_today_cash($user_id){

	$query = $this->db->query('SELECT sum(pos_total) as total FROM `' . DB_PREFIX . 'order` WHERE payment_method="Cash" and order_status_id IN(3,11) AND DATE(date_modified) ="'.date('Y-m-d').'"');
	$row = $query->row;

	$query = $this->db->query('SELECT sum(cash_split) as total FROM `' . DB_PREFIX . 'order` WHERE payment_method="Cash,Card" and order_status_id IN(3,11) AND DATE(date_modified) ="'.date('Y-m-d').'"');
	$row2 = $query->row;

	return $row['total']+$row2['total'];
}

public function getPaypal($user_id,$date_start,$date_end){

	$query = $this->db->query('SELECT sum(pos_total) as total FROM `' . DB_PREFIX . 'order` WHERE user_id="'.$user_id.'" and order_status_id IN(3,11) AND payment_method in ("PayPal","Paypal Express") AND DATE(date_modified) BETWEEN "'.$date_start.'" AND "'.$date_end.'"');

	$row = $query->row;
	return $row['total'];
}


public function get_month_card($user_id,$date_start,$date_end){
	$query = $this->db->query('SELECT sum(pos_total) as total FROM `' . DB_PREFIX . 'order` WHERE user_id="'.$user_id.'" and order_status_id IN(3,11) AND payment_method="Card" AND DATE(date_modified) BETWEEN "'.$date_start.'" AND "'.$date_end.'"');
	$row = $query->row;


	$query = $this->db->query('SELECT sum(card_split) as total FROM `' . DB_PREFIX . 'order` WHERE user_id="'.$user_id.'" AND payment_method="Cash,Card" AND DATE(date_modified) BETWEEN "'.$date_start.'" AND "'.$date_end.'"');
	$row2 = $query->row;

	return $row['total']+$row2['total'];
}


public function get_month_cash($user_id,$date_start,$date_end){
	$query = $this->db->query('SELECT sum(pos_total) as total FROM `' . DB_PREFIX . 'order` WHERE user_id="'.$user_id.'" and order_status_id IN(3,11) AND payment_method="Cash" AND DATE(date_modified) BETWEEN "'.$date_start.'" AND "'.$date_end.'"');
	$row = $query->row;


	$query = $this->db->query('SELECT sum(cash_split) as total FROM `' . DB_PREFIX . 'order` WHERE user_id="'.$user_id.'" and order_status_id IN(3,11) AND payment_method="Cash,Card" AND DATE(date_modified) BETWEEN "'.$date_start.'" AND "'.$date_end.'"');
	$row2 = $query->row;

	return $row['total']+$row2['total'];
}


public function getTotalCash($date_start,$date_end){
	$query = $this->db->query('SELECT sum(pos_total) as total FROM `' . DB_PREFIX . 'order` WHERE payment_method="Cash" and order_status_id IN(3,11) AND DATE(date_modified) BETWEEN "'.$date_start.'" AND "'.$date_end.'" ');
	$row = $query->row;


	$query = $this->db->query('SELECT sum(cash_split) as total FROM `' . DB_PREFIX . 'order` WHERE payment_method="Cash,Card" and order_status_id IN(3,11) AND DATE(date_modified) BETWEEN "'.$date_start.'" AND "'.$date_end.'" ');
	$row2 = $query->row;

	return $row['total']+$row2['total'];
}
public function getTotalInStore($date_start,$date_end){
	$query = $this->db->query('SELECT sum(total) as total FROM `' . DB_PREFIX . 'order` WHERE payment_method="In Store" AND DATE(date_modified) BETWEEN "'.$date_start.'" AND "'.$date_end.'" ');
	$row = $query->row;
	return $row['total'];
}

public function getTotalPaypal($date_start,$date_end){

	$query = $this->db->query('SELECT sum(total) as total FROM `' . DB_PREFIX . 'order` WHERE payment_method in ("PayPal","Paypal Express") and order_status_id IN(3,11) AND DATE(date_modified) BETWEEN "'.$date_start.'" AND "'.$date_end.'" ');
	$row = $query->row;
	return $row['total'];
}

public function getTotalStoreCredit($date_start,$date_end){
	$query = $this->db->query('SELECT SUM(a.amount) AS total FROM `' . DB_PREFIX . 'voucher` a WHERE  DATE(a.date_added) BETWEEN "'.$date_start.'" AND "'.$date_end.'" ');
	$row = $query->row;
	return $row['total'];
}
public function getTotalReplacement($date_start,$date_end){
	$query = $this->db->query('SELECT
		SUM(amount) AS total
		FROM
		`'.DB_PREFIX.'return_program_mt` m
		INNER JOIN `'.DB_PREFIX.'return_program_dt` d
		ON (m.`return_id` = d.`return_id`)
		WHERE m.resolution="replacement"  AND DATE(date_added) BETWEEN "'.$date_start.'" AND "'.$date_end.'"');
	$row = $query->row;
	return $row['total'];
}

public function get_user_balance($user_id){
	$query = $this->db->query('SELECT cash, card FROM `' . DB_PREFIX . 'user` WHERE user_id="'.$user_id.'"');
	return $query->row;   
}
public function makeVoidReport($data)
{



	$this->db->query("INSERT INTO `".DB_PREFIX."void_product` SET order_id='".(int)$data['order_id']."',product_id='".(int)$data['product_id']."',quantity='".(int)$data['quantity']."',amount='".(float)$data['total']."',user_id='".$this->user->getId()."',reason_id='".(int)$data['reason_id']."',date_added=NOW() ");


	$datax['order_status_id'] = $this->config->get('config_complete_status_id');
	$datax['notify'] = 0;
	$datax['comment'] = 'Item Removed';


	$this->load->model('sale/order');
	$this->model_sale_order->addOrderHistory($data['order_id'],$datax);

}

public function totalReplacementOrders($user_id,$date_start,$date_end)
{

	$query = $this->db->query('SELECT COUNT(*) AS total FROM `' . DB_PREFIX . 'return_program_mt` WHERE user_id="'.$user_id.'" AND resolution="replacement" AND DATE(date_added) BETWEEN "'.$date_start.'" AND "'.$date_end.'"');
	$row = $query->row;
	return $row['total'];


}

public function replacementAmount($user_id,$date_start,$date_end)
{


	$query = $this->db->query('SELECT
		SUM(amount) AS total
		FROM
		`'.DB_PREFIX.'return_program_mt` m
		INNER JOIN `'.DB_PREFIX.'return_program_dt` d
		ON (m.`return_id` = d.`return_id`)
		WHERE m.`user_id`="'.$user_id.'" AND m.resolution="replacement" AND DATE(m.date_added) BETWEEN "'.$date_start.'" AND "'.$date_end.'"');
	$row = $query->row;
	return $row['total'];


}

public function voucherUsedAmount($user_id,$date_start,$date_end)
{
	$query = $this->db->query('SELECT SUM(a.amount) AS total FROM `' . DB_PREFIX . 'order_voucher` a,`'.DB_PREFIX.'voucher` b WHERE a.voucher_id=b.voucher_id AND b.user_id="'.$user_id.'" AND DATE(b.date_added) BETWEEN "'.$date_start.'" AND "'.$date_end.'"');
	$row = $query->row;
	return $row['total'];


}


public function voucherIssuedAmount($user_id,$date_start,$date_end)
{
	$query = $this->db->query('SELECT SUM(a.amount) AS total FROM `' . DB_PREFIX . 'voucher` a WHERE  a.user_id="'.$user_id.'" AND DATE(a.date_added) BETWEEN "'.$date_start.'" AND "'.$date_end.'"');
	$row = $query->row;
	return $row['total'];


}

public function getTotalReturns($user_id,$date_start,$date_end)
{
	$query = $this->db->query('SELECT COUNT(*) AS total FROM `' . DB_PREFIX . 'return_program_mt`  WHERE  user_id="'.$user_id.'" AND DATE(date_added) BETWEEN "'.$date_start.'" AND "'.$date_end.'"');
	$row = $query->row;
	return $row['total'];


}
}
?>