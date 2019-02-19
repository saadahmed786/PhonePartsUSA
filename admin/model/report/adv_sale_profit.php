<?php
class ModelReportAdvSaleProfit extends Model {
	public function getSaleOrders($data = array()) {
		$query = 'SET SESSION group_concat_max_len=500000'; 
 
		mysql_query($query);
		$token = $this->session->data['token'];

		if (isset($data['filter_date_start']) && $data['filter_date_start']) {
			$date_start = $data['filter_date_start'];
		} else {
			$date_start = '';
		}

		if (isset($data['filter_date_end']) && $data['filter_date_end']) {
			$date_end = $data['filter_date_end'];
		} else {
			$date_end = '';
		}

		if (isset($data['filter_range'])) {
			$range = $data['filter_range'];
		} else {
			$range = 'current_year'; //show Current Year in Statistics Range by default
		}

		switch($range) 
		{
			case 'custom';
				$date_start = "DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
				$date_end = " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";				
				break;			
			case 'week';
				$date_start = "DATE(o.date_added) >= '" . $this->db->escape(date('Y-m-d', strtotime('-7 day'))) . "'";
				$date_end = " AND DATE(o.date_added) <= DATE (NOW())";	
				break;
			case 'month';
				$date_start = "DATE(o.date_added) >= '" . $this->db->escape(date('Y-m-d', strtotime('-30 day'))) . "'";
				$date_end = " AND DATE(o.date_added) <= DATE (NOW())";					
				break;			
			case 'quarter';
				$date_start = "DATE(o.date_added) >= '" . $this->db->escape(date('Y-m-d', strtotime('-91 day'))) . "'";
				$date_end = " AND DATE(o.date_added) <= DATE (NOW())";						
				break;
			case 'year';
				$date_start = "DATE(o.date_added) >= '" . $this->db->escape(date('Y-m-d', strtotime('-365 day'))) . "'";
				$date_end = " AND DATE(o.date_added) <= DATE (NOW())";					
				break;
			case 'current_week';
				$date_start = "DATE(o.date_added) >= CURDATE() - WEEKDAY(CURDATE())";
				$date_end = " AND DATE(o.date_added) <= DATE (NOW())";			
				break;	
			case 'current_month';
				$date_start = "YEAR(o.date_added) = YEAR(CURDATE())";
				$date_end = " AND MONTH(o.date_added) = MONTH(CURDATE())";			
				break;
			case 'current_quarter';
				$date_start = "QUARTER(o.date_added) = QUARTER(CURDATE())";
				$date_end = " AND YEAR(o.date_added) = YEAR(CURDATE())";					
				break;					
			case 'current_year';
				$date_start = "DATE(o.date_added) >= CURDATE() - YEAR(CURDATE())";
				$date_end = " AND DATE(o.date_added) <= DATE (NOW())";				
				break;					
			case 'last_week';
				$date_start = "DATE(o.date_added) >= CURDATE() - INTERVAL DAYOFWEEK(CURDATE())+5 DAY";
				$date_end = " AND DATE(o.date_added) < CURDATE() - INTERVAL DAYOFWEEK(CURDATE())-2 DAY";				
				break;	
			case 'last_month';
				$date_start = "DATE(o.date_added) >= DATE_FORMAT(CURRENT_DATE - INTERVAL 1 MONTH, '%Y/%m/01')";
				$date_end = " AND DATE(o.date_added) < DATE_FORMAT(CURRENT_DATE, '%Y/%m/01')";				
				break;
			case 'last_quarter';
				$date_start = "QUARTER(o.date_added) = QUARTER(DATE_ADD(NOW(), INTERVAL -3 MONTH))";
				$date_end = " AND YEAR(o.date_added) = YEAR(CURDATE())";				
				break;					
			case 'last_year';
				$date_start = "DATE(o.date_added) >= DATE_FORMAT(CURRENT_DATE - INTERVAL 1 YEAR, '%Y/01/01')";
				$date_end = " AND DATE(o.date_added) < DATE_FORMAT(CURRENT_DATE, '%Y/01/01')";				
				break;					
			case 'all_time';
				$date_start = "DATE(o.date_added) >= '" . $this->db->escape(date('Y-m-d','0')) . "'";
				$date_end = " AND DATE(o.date_added) <= DATE (NOW())";						
				break;	
		}
		
		$date = ' WHERE (' . $date_start . $date_end . ')';
				
		$osi = '';
    	if (isset($data['filter_order_status_id']) && is_array($data['filter_order_status_id'])) {
      		foreach($data['filter_order_status_id'] as $key => $val)
		{
        if (!empty($osi)) $osi .= ' OR ';
        $osi .= 'o.order_status_id = ' . (int)$this->db->escape($key);
      	}
		$osi = ' AND (' . $osi . ') ';
		} else {
		$osi = ' AND o.order_status_id > 0';
		}

		$store = '';
    	if (isset($data['filter_store_id']) && is_array($data['filter_store_id'])) {
      		foreach($data['filter_store_id'] as $key => $val)
		{
        if (!empty($store)) $store .= ' OR ';
        $store .= 'o.store_id = ' . (int)$this->db->escape($key);
      	}
		$store = ' AND (' . $store . ') ';
	    }
		
		$cur = '';
    	if (isset($data['filter_currency']) && is_array($data['filter_currency'])) {
      		foreach($data['filter_currency'] as $key => $val)
		{
        if (!empty($cur)) $cur .= ' OR ';
        $cur .= 'o.currency_id = ' . (int)$this->db->escape($key);
      	}
		$cur = ' AND (' . $cur . ') ';
	    }
		
		$tax = '';
    	if (isset($data['filter_taxes']) && is_array($data['filter_taxes'])) {
      		foreach($data['filter_taxes'] as $key => $val)
		{
        if (!empty($tax)) $tax .= ' OR ';
        $tax .= " (SELECT HEX(ot.title) FROM `" . DB_PREFIX . "order_total` ot WHERE o.order_id = ot.order_id AND ot.code = 'tax' AND HEX(ot.title) = '" . $this->db->escape($key) . "')";		
      	}
		$tax = ' AND (' . $tax . ') ';
	    }

		$cgrp = '';
    	if (isset($data['filter_customer_group_id']) && is_array($data['filter_customer_group_id'])) {
      		foreach($data['filter_customer_group_id'] as $key => $val)
		{
        if (!empty($cgrp)) $cgrp .= ' OR ';
        $cgrp .= " ((SELECT c.customer_group_id FROM `" . DB_PREFIX . "customer` c WHERE c.customer_id = o.customer_id AND c.customer_group_id = '" . (int)$this->db->escape($key) . "') OR (o.customer_group_id = '" . (int)$this->db->escape($key) . "' AND o.customer_id = 0))";
      	}
		$cgrp = ' AND (' . $cgrp . ') ';
	    }

		$comp = '';
		if (!empty($data['filter_company'])) {
			$comp = " AND LCASE(o.payment_company) LIKE '%" . $this->db->escape(mb_strtolower($data['filter_company'], 'UTF-8')) . "%'";
		} else {
			$comp = '';
		}

		$cust = '';
		if (!empty($data['filter_customer_id'])) {
			$cust = " AND LCASE(CONCAT(o.firstname, ' ', o.lastname)) LIKE '%" . $this->db->escape(mb_strtolower($data['filter_customer_id'], 'UTF-8')) . "%'";
		} else {
			$cust = '';
		}

		$email = '';
		if (!empty($data['filter_email'])) {
			$email = " AND LCASE(o.email) LIKE '%" . $this->db->escape(mb_strtolower($data['filter_email'], 'UTF-8')) . "%'";			
		} else {
			$email = '';
		}

		$prod = '';
		if (!empty($data['filter_product_id'])) {
        	$prod = " AND (SELECT DISTINCT op.order_id FROM `" . DB_PREFIX . "order_product` op WHERE o.order_id = op.order_id AND LCASE(op.name) LIKE '%" . $this->db->escape(mb_strtolower($data['filter_product_id'], 'UTF-8')) . "%')";				
		} else {
			$prod = '';
		}

		$opt = '';
    	if (isset($data['filter_option']) && is_array($data['filter_option'])) {	
      		foreach($data['filter_option'] as $key => $val)
		{
        if (!empty($opt)) $opt .= ' AND ';
        $opt .= " (SELECT DISTINCT op.order_product_id FROM `" . DB_PREFIX . "order_option` oo, `" . DB_PREFIX . "order_product` op WHERE o.order_id = op.order_id AND oo.order_product_id = op.order_product_id AND HEX(CONCAT(oo.name, oo.value, oo.type)) = '" . $this->db->escape($key) . "' AND LCASE(op.name) LIKE '%" . $this->db->escape(mb_strtolower($data['filter_product_id'], 'UTF-8')) . "%')";
      	}
		$opt = ' AND ' . $opt;	
		}
		
		$loc = '';
    	if (isset($data['filter_location']) && is_array($data['filter_location'])) {
      		foreach($data['filter_location'] as $key => $val)
		{
        if (!empty($loc)) $loc .= ' OR ';
        $loc .= " (SELECT DISTINCT HEX(p.location) FROM `" . DB_PREFIX . "product` p, `" . DB_PREFIX . "order_product` op WHERE p.product_id = op.product_id AND o.order_id = op.order_id AND HEX(p.location) = '" . $this->db->escape($key) . "')";
      	}
		$loc = ' AND (' . $loc . ') ';
	    }

		$aff = '';
    	if (isset($data['filter_affiliate']) && is_array($data['filter_affiliate'])) {
      		foreach($data['filter_affiliate'] as $key => $val)
		{
        if (!empty($aff)) $aff .= ' OR ';
        $aff .= " (SELECT at.affiliate_id FROM `" . DB_PREFIX . "affiliate_transaction` at WHERE at.order_id = o.order_id AND at.affiliate_id = '" . (int)$this->db->escape($key) . "')";
      	}
		$aff = ' AND (' . $aff . ') ';
	    }
		
		$shipp = '';
    	if (isset($data['filter_shipping']) && is_array($data['filter_shipping'])) {
      		foreach($data['filter_shipping'] as $key => $val)
		{
        if (!empty($shipp)) $shipp .= ' OR ';
        $shipp .= " HEX(o.shipping_method) = '" . $this->db->escape($key) . "'";
      	}
		$shipp = ' AND (' . $shipp . ') ';
	    }
		
		$pay = '';
    	if (isset($data['filter_payment']) && is_array($data['filter_payment'])) {
      		foreach($data['filter_payment'] as $key => $val)
		{
        if (!empty($pay)) $pay .= ' OR ';
        $pay .= " HEX(o.payment_method) = '" . $this->db->escape($key) . "'";
      	}
		$pay = ' AND (' . $pay . ') ';
	    }

		$zone = '';
    	if (isset($data['filter_shipping_zone']) && is_array($data['filter_shipping_zone'])) {
      		foreach($data['filter_shipping_zone'] as $key => $val)
		{
        if (!empty($zone)) $zone .= ' OR ';
        $zone .= " o.shipping_zone_id = '" . (int)$this->db->escape($key) . "'";
      	}
		$zone = ' AND (' . $zone . ') ';
	    }
		
		$shippc = '';
    	if (isset($data['filter_shipping_country']) && is_array($data['filter_shipping_country'])) {
      		foreach($data['filter_shipping_country'] as $key => $val)
		{
        if (!empty($shippc)) $shippc .= ' OR ';
        $shippc .= " o.shipping_country_id = '" . (int)$this->db->escape($key) . "'";
      	}
		$shippc = ' AND (' . $shippc . ') ';
	    }

		$payc = '';
    	if (isset($data['filter_payment_country']) && is_array($data['filter_payment_country'])) {
      		foreach($data['filter_payment_country'] as $key => $val)
		{
        if (!empty($payc)) $payc .= ' OR ';
        $payc .= " o.payment_country_id = '" . (int)$this->db->escape($key) . "'";
      	}
		$payc = ' AND (' . $payc . ') ';
	    }

		$sql = "SELECT 
		YEAR(o.date_added) AS year, 
		QUARTER(o.date_added) AS quarter, 		
		MONTHNAME(o.date_added) AS month, 
		MIN(o.date_added) AS date_start, 
		MAX(o.date_added) AS date_end, 			  
		o.date_added, 
		o.order_id,
		o.invoice_prefix, 
		o.invoice_no, 			  
		o.customer_id, 
		o.firstname, 
		o.lastname, 
		o.email AS cust_email, 
		o.customer_group_id, 
		o.shipping_method, 
		o.payment_method, 
		o.order_status_id, 
		o.store_name, 
		o.currency_code,
		o.currency_value, 
		COUNT(o.order_id) AS orders, 
		COUNT(DISTINCT CONCAT(o.lastname, ', ', o.firstname)) AS customers, 
		SUM((SELECT SUM(op.quantity) FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id GROUP BY op.order_id)) AS products, 
		SUM((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'sub_total' GROUP BY ot.order_id)) AS sub_total, 
		SUM((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'handling' GROUP BY ot.order_id)) AS handling, 
		SUM((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'low_order_fee' GROUP BY ot.order_id)) AS low_order_fee, 		
		SUM((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'reward' GROUP BY ot.order_id)) AS reward, 
		SUM((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'shipping' GROUP BY ot.order_id)) AS shipping, 
		SUM((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'coupon' GROUP BY ot.order_id)) AS coupon, 
		SUM((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'tax' GROUP BY ot.order_id)) AS tax, 
		SUM((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'credit' GROUP BY ot.order_id)) AS credit, 
		SUM((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'voucher' GROUP BY ot.order_id)) AS voucher, 
		SUM(o.commission) AS commission, 
		SUM(o.total) AS total, 
		SUM((SELECT SUM(op.cost*op.quantity) FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id GROUP BY op.order_id)) AS prod_costs, ";

		if (isset($data['filter_details']) && $data['filter_details'] == 1) {
			$sql .= " GROUP_CONCAT('<a href=\"index.php?route=sale/order/info&token=$token&order_id=',o.order_id,'\">',o.order_id,'</a>' ORDER BY o.order_id SEPARATOR '<br>') AS order_ord_id, 
					GROUP_CONCAT(o.order_id ORDER BY o.order_id SEPARATOR '<br>') AS order_ord_idc, 		
					GROUP_CONCAT(DATE_FORMAT(o.date_added, '%e/%m/%Y') ORDER BY o.order_id SEPARATOR '<br>') AS order_order_date, 
					GROUP_CONCAT(IFNULL(o.invoice_prefix,'&nbsp;'),IFNULL(o.invoice_no,'&nbsp;') ORDER BY o.order_id SEPARATOR '<br>') AS order_inv_no, 
					GROUP_CONCAT(CONCAT(o.firstname,' ',o.lastname) ORDER BY o.order_id SEPARATOR '<br>') AS order_name, 
					GROUP_CONCAT(o.email ORDER BY o.order_id SEPARATOR '<br>') AS order_email, 
					GROUP_CONCAT(IFNULL((SELECT cgd.name FROM `" . DB_PREFIX . "customer_group_description` cgd WHERE cgd.customer_group_id = o.customer_group_id AND cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'),'&nbsp;') ORDER BY o.order_id SEPARATOR '<br>') AS order_group, 	
					GROUP_CONCAT(IF (o.shipping_method = '','&nbsp;',o.shipping_method) ORDER BY o.order_id SEPARATOR '<br>') AS order_shipping_method, 
					GROUP_CONCAT(IF (o.payment_method = '','&nbsp;',o.payment_method) ORDER BY o.order_id SEPARATOR '<br>') AS order_payment_method, 
					GROUP_CONCAT(IFNULL((SELECT os.name FROM `" . DB_PREFIX . "order_status` os WHERE os.order_status_id = o.order_status_id AND os.language_id = '" . (int)$this->config->get('config_language_id') . "'),'&nbsp;') ORDER BY o.order_id SEPARATOR '<br>') AS order_status, 
 					GROUP_CONCAT(o.store_name ORDER BY o.order_id SEPARATOR '<br>') AS order_store, 
					GROUP_CONCAT(o.currency_code ORDER BY o.order_id SEPARATOR '<br>') AS order_currency, 
					GROUP_CONCAT(IFNULL((SELECT SUM(op.quantity) FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id GROUP BY op.order_id),'&nbsp;') ORDER BY o.order_id SEPARATOR '<br>') AS order_products, 
					GROUP_CONCAT(IFNULL((SELECT FORMAT(o.currency_value*SUM(ot.value), 2) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'sub_total' GROUP BY ot.order_id),'0') ORDER BY o.order_id SEPARATOR '<br>') AS order_sub_total, 
					GROUP_CONCAT(IFNULL((SELECT FORMAT(o.currency_value*SUM(ot.value), 2) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'handling' GROUP BY ot.order_id),'0') ORDER BY o.order_id SEPARATOR '<br>') AS order_hf, 
					GROUP_CONCAT(IFNULL((SELECT FORMAT(o.currency_value*SUM(ot.value), 2) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'low_order_fee' GROUP BY ot.order_id),'0') ORDER BY o.order_id SEPARATOR '<br>') AS order_lof, 	
					GROUP_CONCAT(IFNULL((SELECT FORMAT(o.currency_value*SUM(ot.value), 2) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'shipping' GROUP BY ot.order_id),'0') ORDER BY o.order_id SEPARATOR '<br>') AS order_shipping, 
					GROUP_CONCAT(IFNULL((SELECT FORMAT(o.currency_value*SUM(ot.value), 2) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'tax' GROUP BY ot.order_id),'0') ORDER BY o.order_id SEPARATOR '<br>') AS order_tax, 
					GROUP_CONCAT(FORMAT(o.currency_value*o.total, 2) ORDER BY o.order_id SEPARATOR '<br>') AS order_value,  
					GROUP_CONCAT(FORMAT(o.currency_value*(IFNULL((SELECT SUM((op.cost*op.quantity) + o.commission) FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id),0)-IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'reward' GROUP BY ot.order_id),0)-IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'coupon' GROUP BY ot.order_id),0)-IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'credit' GROUP BY ot.order_id),0)-IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'voucher' GROUP BY ot.order_id),0)), 2) ORDER BY o.order_id SEPARATOR '<br>-') AS order_costs, 
					GROUP_CONCAT(FORMAT(o.currency_value*(IFNULL((SELECT SUM(op.total - (op.cost*op.quantity) - o.commission) FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id),o.total)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'handling' GROUP BY ot.order_id),0)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'low_order_fee' GROUP BY ot.order_id),0)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'reward' GROUP BY ot.order_id),0)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'coupon' GROUP BY ot.order_id),0)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'credit' GROUP BY ot.order_id),0)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'voucher' GROUP BY ot.order_id),0)), 2) ORDER BY o.order_id SEPARATOR '<br>') AS order_profit, 
					GROUP_CONCAT(IFNULL(FORMAT(100*(((IFNULL((SELECT SUM(op.total - (op.cost*op.quantity) - o.commission) FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id),100)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'handling' GROUP BY ot.order_id),0)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'low_order_fee' GROUP BY ot.order_id),0)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'reward' GROUP BY ot.order_id),0)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'coupon' GROUP BY ot.order_id),0)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'credit' GROUP BY ot.order_id),0)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'voucher' GROUP BY ot.order_id),0)))) / ((IFNULL((SELECT SUM(op.total - o.commission) FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id),100)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'handling' GROUP BY ot.order_id),0)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'low_order_fee' GROUP BY ot.order_id),0)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'reward' GROUP BY ot.order_id),0)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'coupon' GROUP BY ot.order_id),0)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'credit' GROUP BY ot.order_id),0)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'voucher' GROUP BY ot.order_id),0))), 2),'0') ORDER BY o.order_id SEPARATOR '%<br>') AS order_profit_margin_percent, ";
					
		} elseif (isset($data['filter_details']) && $data['filter_details'] == 2) {
			$sql .= " GROUP_CONCAT((SELECT GROUP_CONCAT('<a href=\"index.php?route=sale/order/info&token=$token&order_id=',op.order_id,'\">',op.order_id,'</a>' SEPARATOR '<br>') FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id ORDER BY op.order_product_id) ORDER BY o.order_id SEPARATOR '<br>') AS product_ord_id, 
					GROUP_CONCAT((SELECT GROUP_CONCAT(op.order_id SEPARATOR '<br>') FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id ORDER BY op.order_product_id) ORDER BY o.order_id SEPARATOR '<br>') AS product_ord_idc, 
					GROUP_CONCAT((SELECT GROUP_CONCAT((SELECT DATE_FORMAT(o.date_added, '%e/%m/%Y') FROM `" . DB_PREFIX . "order` o WHERE op.order_id = o.order_id) SEPARATOR '<br>') FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id ORDER BY op.order_product_id) ORDER BY o.order_id SEPARATOR '<br>') AS product_order_date,  
					GROUP_CONCAT((SELECT GROUP_CONCAT(IFNULL((SELECT o.invoice_prefix FROM `" . DB_PREFIX . "order` o WHERE op.order_id = o.order_id),'&nbsp;'),IFNULL((SELECT o.invoice_no FROM `" . DB_PREFIX . "order` o WHERE op.order_id = o.order_id),'&nbsp;') SEPARATOR '<br>') FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id ORDER BY op.order_product_id) ORDER BY o.order_id SEPARATOR '<br>') AS product_inv_no, 
					GROUP_CONCAT((SELECT GROUP_CONCAT(IFNULL((SELECT CONCAT('<a href=\"index.php?route=catalog/product/update&token=$token&product_id=',op.product_id,'\">',op.product_id,'</a>') FROM `" . DB_PREFIX . "product` p WHERE op.product_id = p.product_id),op.product_id) SEPARATOR '<br>') FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id ORDER BY op.order_product_id) ORDER BY o.order_id SEPARATOR '<br>') AS product_pid, 
					GROUP_CONCAT((SELECT GROUP_CONCAT(op.product_id SEPARATOR '<br>') FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id ORDER BY op.order_product_id) ORDER BY o.order_id SEPARATOR '<br>') AS product_pidc, 
					GROUP_CONCAT((SELECT GROUP_CONCAT(IFNULL((SELECT p.sku FROM `" . DB_PREFIX . "product` p WHERE op.product_id = p.product_id),'&nbsp;') SEPARATOR '<br>') FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id ORDER BY op.order_product_id) ORDER BY o.order_id SEPARATOR '<br>') AS product_sku, 
					GROUP_CONCAT((SELECT GROUP_CONCAT(op.name SEPARATOR '<br>') FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id ORDER BY op.order_product_id) ORDER BY o.order_id SEPARATOR '<br>') AS product_name, 
					GROUP_CONCAT((SELECT GROUP_CONCAT(IFNULL((SELECT GROUP_CONCAT(CONCAT(oo.name,': ',oo.value) SEPARATOR '; ') FROM `" . DB_PREFIX . "order_option` oo WHERE op.order_product_id = oo.order_product_id AND (oo.type = 'radio' OR oo.type = 'checkbox' OR oo.type = 'select') ORDER BY op.order_product_id),'&nbsp;') SEPARATOR '<br>') FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id ORDER BY op.order_product_id) ORDER BY o.order_id SEPARATOR '<br>') AS product_option, 
					GROUP_CONCAT((SELECT GROUP_CONCAT(op.model SEPARATOR '<br>') FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id ORDER BY op.order_product_id) ORDER BY o.order_id SEPARATOR '<br>') AS product_model, 
					GROUP_CONCAT((SELECT GROUP_CONCAT(IFNULL((SELECT m.name FROM `" . DB_PREFIX . "product` p, `" . DB_PREFIX . "manufacturer` m WHERE op.product_id = p.product_id AND p.manufacturer_id = m.manufacturer_id),'&nbsp;') SEPARATOR '<br>') FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id ORDER BY op.order_product_id) ORDER BY o.order_id SEPARATOR '<br>') AS product_manu, 				
					GROUP_CONCAT((SELECT GROUP_CONCAT((SELECT o.currency_code FROM `" . DB_PREFIX . "order` o WHERE op.order_id = o.order_id) SEPARATOR '<br>') FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id ORDER BY op.order_product_id) ORDER BY o.order_id SEPARATOR '<br>') AS product_currency,  
					GROUP_CONCAT((SELECT GROUP_CONCAT(FORMAT(o.currency_value*op.price, 2) SEPARATOR '<br>') FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id ORDER BY op.order_product_id) ORDER BY o.order_id SEPARATOR '<br>') AS product_price, 
					GROUP_CONCAT((SELECT GROUP_CONCAT(op.quantity SEPARATOR '<br>') FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id ORDER BY op.order_product_id) ORDER BY o.order_id SEPARATOR '<br>') AS product_quantity, 
					GROUP_CONCAT((SELECT GROUP_CONCAT(FORMAT(o.currency_value*op.total, 2) SEPARATOR '<br>') FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id ORDER BY op.order_product_id) ORDER BY o.order_id SEPARATOR '<br>') AS product_total, 
					GROUP_CONCAT((SELECT GROUP_CONCAT(FORMAT(o.currency_value*op.tax, 2) SEPARATOR '<br>') FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id ORDER BY op.order_product_id) ORDER BY o.order_id SEPARATOR '<br>') AS product_tax, 
					GROUP_CONCAT((SELECT GROUP_CONCAT(FORMAT(o.currency_value*op.cost*op.quantity, 2) SEPARATOR '<br>-') FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id ORDER BY op.order_product_id) ORDER BY o.order_id SEPARATOR '<br>-') AS product_costs, 
					GROUP_CONCAT((SELECT GROUP_CONCAT(FORMAT(o.currency_value*op.total - o.currency_value*op.cost*op.quantity, 2) SEPARATOR '<br>') FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id ORDER BY op.order_product_id) ORDER BY o.order_id SEPARATOR '<br>') AS product_profit, 
					GROUP_CONCAT((SELECT GROUP_CONCAT(IFNULL(FORMAT(100*(op.total - op.cost*op.quantity) / op.total, 2),'0') SEPARATOR '%<br>') FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id ORDER BY op.order_product_id) ORDER BY o.order_id SEPARATOR '%<br>') AS product_profit_margin_percent, ";
					
		} elseif (isset($data['filter_details']) && $data['filter_details'] == 3) {
			$sql .= " GROUP_CONCAT('<a href=\"index.php?route=sale/order/info&token=$token&order_id=',o.order_id,'\">',o.order_id,'</a>' ORDER BY o.order_id SEPARATOR '<br>') AS customer_ord_id, 
					GROUP_CONCAT(o.order_id ORDER BY o.order_id SEPARATOR '<br>') AS customer_ord_idc, 	
					GROUP_CONCAT(DATE_FORMAT(o.date_added, '%e/%m/%Y') ORDER BY o.order_id SEPARATOR '<br>') AS customer_order_date, 
					GROUP_CONCAT(IFNULL(o.invoice_prefix,'&nbsp;'),IFNULL(o.invoice_no,'&nbsp;') ORDER BY o.order_id SEPARATOR '<br>') AS customer_inv_no, 			
					GROUP_CONCAT(IF (o.customer_id = 0,'&nbsp;',CONCAT('<a href=\"index.php?route=sale/customer/update&token=$token&customer_id=',o.customer_id,'\">',o.customer_id,'</a>')) ORDER BY o.order_id SEPARATOR '<br>') AS customer_cust_id, 
					GROUP_CONCAT(IF (o.customer_id = 0,'&nbsp;',o.customer_id) ORDER BY o.order_id SEPARATOR '<br>') AS customer_cust_idc, 
					GROUP_CONCAT(CONCAT(o.payment_firstname,' ',o.payment_lastname) ORDER BY o.order_id SEPARATOR '<br>') AS billing_name, 
					GROUP_CONCAT(o.payment_company ORDER BY o.order_id SEPARATOR '<br>') AS billing_company, 
					GROUP_CONCAT(o.payment_address_1 ORDER BY o.order_id SEPARATOR '<br>') AS billing_address_1, 
					GROUP_CONCAT(o.payment_address_2 ORDER BY o.order_id SEPARATOR '<br>') AS billing_address_2, 
					GROUP_CONCAT(o.payment_city ORDER BY o.order_id SEPARATOR '<br>') AS billing_city, 
					GROUP_CONCAT(o.payment_zone ORDER BY o.order_id SEPARATOR '<br>') AS billing_zone, 
					GROUP_CONCAT(o.payment_postcode ORDER BY o.order_id SEPARATOR '<br>') AS billing_postcode, 
					GROUP_CONCAT(o.payment_country ORDER BY o.order_id SEPARATOR '<br>') AS billing_country, 
					GROUP_CONCAT(o.telephone ORDER BY o.order_id SEPARATOR '<br>') AS customer_telephone, 
					GROUP_CONCAT(CONCAT(o.shipping_firstname,' ',o.shipping_lastname) ORDER BY o.order_id SEPARATOR '<br>') AS shipping_name, 
					GROUP_CONCAT(o.shipping_company ORDER BY o.order_id SEPARATOR '<br>') AS shipping_company, 
					GROUP_CONCAT(o.shipping_address_1 ORDER BY o.order_id SEPARATOR '<br>') AS shipping_address_1, 
					GROUP_CONCAT(o.shipping_address_2 ORDER BY o.order_id SEPARATOR '<br>') AS shipping_address_2, 
					GROUP_CONCAT(o.shipping_city ORDER BY o.order_id SEPARATOR '<br>') AS shipping_city, 
					GROUP_CONCAT(o.shipping_zone ORDER BY o.order_id SEPARATOR '<br>') AS shipping_zone, 			
					GROUP_CONCAT(o.shipping_postcode ORDER BY o.order_id SEPARATOR '<br>') AS shipping_postcode, 
					GROUP_CONCAT(o.shipping_country ORDER BY o.order_id SEPARATOR '<br>') AS shipping_country, ";			
		}

		$sql .= " (SELECT COUNT(o.order_id) FROM `" . DB_PREFIX . "order` o" . $date . $osi . $store . $cur . $tax . $cgrp . $comp . $cust . $email . $prod . $opt . $loc . $aff . $shipp . $pay . $zone . $shippc . $payc . ") AS orders_total, 
		(SELECT COUNT(DISTINCT CONCAT(o.lastname, ', ', o.firstname)) FROM `" . DB_PREFIX . "order` o" . $date . $osi . $store . $cur . $tax . $cgrp . $comp . $cust . $email . $prod . $opt . $loc . $aff . $shipp . $pay . $zone . $shippc . $payc . ") AS customers_total, 
		(SELECT SUM(op.quantity) FROM `" . DB_PREFIX . "order_product` op, `" . DB_PREFIX . "order` o" . $date . $osi . $store . $cur . $tax . $cgrp . $comp . $cust . $email . $prod . $opt . $loc . $aff . $shipp . $pay . $zone . $shippc . $payc . " AND op.order_id = o.order_id) AS products_total, 
		(SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot, `" . DB_PREFIX . "order` o" . $date . $osi . $store . $cur . $tax . $cgrp . $comp . $cust . $email . $prod . $opt . $loc . $aff . $shipp . $pay . $zone . $shippc . $payc . " AND ot.order_id = o.order_id AND ot.code = 'sub_total') AS sub_total_total, 
		(SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot, `" . DB_PREFIX . "order` o" . $date . $osi . $store . $cur . $tax . $cgrp . $comp . $cust . $email . $prod . $opt . $loc . $aff . $shipp . $pay . $zone . $shippc . $payc . " AND ot.order_id = o.order_id AND ot.code = 'handling') AS handling_total, 	
		(SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot, `" . DB_PREFIX . "order` o" . $date . $osi . $store . $cur . $tax . $cgrp . $comp . $cust . $email . $prod . $opt . $loc . $aff . $shipp . $pay . $zone . $shippc . $payc . " AND ot.order_id = o.order_id AND ot.code = 'low_order_fee') AS low_order_fee_total, 			
		(SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot, `" . DB_PREFIX . "order` o" . $date . $osi . $store . $cur . $tax . $cgrp . $comp . $cust . $email . $prod . $opt . $loc . $aff . $shipp . $pay . $zone . $shippc . $payc . " AND ot.order_id = o.order_id AND ot.code = 'reward') AS reward_total, 			
		(SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot, `" . DB_PREFIX . "order` o" . $date . $osi . $store . $cur . $tax . $cgrp . $comp . $cust . $email . $prod . $opt . $loc . $aff . $shipp . $pay . $zone . $shippc . $payc . " AND ot.order_id = o.order_id AND ot.code = 'shipping') AS shipping_total, 	
		(SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot, `" . DB_PREFIX . "order` o" . $date . $osi . $store . $cur . $tax . $cgrp . $comp . $cust . $email . $prod . $opt . $loc . $aff . $shipp . $pay . $zone . $shippc . $payc . " AND ot.order_id = o.order_id AND ot.code = 'coupon') AS coupon_total, 
		(SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot, `" . DB_PREFIX . "order` o" . $date . $osi . $store . $cur . $tax . $cgrp . $comp . $cust . $email . $prod . $opt . $loc . $aff . $shipp . $pay . $zone . $shippc . $payc . " AND ot.order_id = o.order_id AND ot.code = 'tax') AS tax_total, 
		(SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot, `" . DB_PREFIX . "order` o" . $date . $osi . $store . $cur . $tax . $cgrp . $comp . $cust . $email . $prod . $opt . $loc . $aff . $shipp . $pay . $zone . $shippc . $payc . " AND ot.order_id = o.order_id AND ot.code = 'credit') AS credit_total, 	
		(SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot, `" . DB_PREFIX . "order` o" . $date . $osi . $store . $cur . $tax . $cgrp . $comp . $cust . $email . $prod . $opt . $loc . $aff . $shipp . $pay . $zone . $shippc . $payc . " AND ot.order_id = o.order_id AND ot.code = 'voucher') AS voucher_total, 	
		(SELECT SUM(o.commission) FROM `" . DB_PREFIX . "order` o" . $date . $osi . $store . $cur . $tax . $cgrp . $comp . $cust . $email . $prod . $opt . $loc . $aff . $shipp . $pay . $zone . $shippc . $payc . ") AS commission_total, 
		(SELECT SUM(o.total) FROM `" . DB_PREFIX . "order` o" . $date . $osi . $store . $cur . $tax . $cgrp . $comp . $cust . $email . $prod . $opt . $loc . $aff . $shipp . $pay . $zone . $shippc . $payc . ") AS total_total, 	
		(SELECT SUM(op.cost*op.quantity) FROM `" . DB_PREFIX . "order_product` op, `" . DB_PREFIX . "order` o" . $date . $osi . $store . $cur . $tax . $cgrp . $comp . $cust . $email . $prod . $opt . $loc . $aff . $shipp . $pay . $zone . $shippc . $payc . " AND op.order_id = o.order_id) AS prod_costs_total
		
		FROM `" . DB_PREFIX . "order` o" . $date . $osi . $store . $cur . $tax . $cgrp . $comp . $cust . $email . $prod . $opt . $loc . $aff . $shipp . $pay . $zone . $shippc . $payc;
		
		if (isset($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'month'; //show Month in Group Report by default
		}
		
		switch($group) {
			case 'order';
				$sql .= " GROUP BY o.order_id";
				break;				
			case 'day';
				$sql .= " GROUP BY YEAR(o.date_added), DAY(o.date_added)";
				break;
			case 'week':
				$sql .= " GROUP BY YEAR(o.date_added), WEEK(o.date_added)";
				break;			
			case 'month':
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added)";
				break;
			case 'quarter':
				$sql .= " GROUP BY YEAR(o.date_added), QUARTER(o.date_added)";
				break;				
			case 'year':
				$sql .= " GROUP BY YEAR(o.date_added)";
				break;			
		}
		
		if (isset($data['filter_sort']) && $data['filter_sort'] == 'date') {
			$sql .= " ORDER BY date_added DESC ";
		} elseif (isset($data['filter_sort']) && $data['filter_sort'] == 'customers') {
			$sql .= " ORDER BY customers DESC, total DESC ";
		} elseif (isset($data['filter_sort']) && $data['filter_sort'] == 'orders') {
			$sql .= " ORDER BY orders DESC, total DESC ";			
		} elseif (isset($data['filter_sort']) && $data['filter_sort'] == 'products') {
			$sql .= " ORDER BY products DESC, total DESC ";
		} elseif (isset($data['filter_sort']) && $data['filter_sort'] == 'sub_total') {
			$sql .= " ORDER BY sub_total DESC ";
		} elseif (isset($data['filter_sort']) && $data['filter_sort'] == 'reward') {
			$sql .= " ORDER BY reward DESC ";
		} elseif (isset($data['filter_sort']) && $data['filter_sort'] == 'shipping') {
			$sql .= " ORDER BY shipping DESC ";
		} elseif (isset($data['filter_sort']) && $data['filter_sort'] == 'coupon') {
			$sql .= " ORDER BY coupon DESC ";
		} elseif (isset($data['filter_sort']) && $data['filter_sort'] == 'tax') {
			$sql .= " ORDER BY tax DESC ";	
		} elseif (isset($data['filter_sort']) && $data['filter_sort'] == 'credit') {
			$sql .= " ORDER BY credit DESC ";	
		} elseif (isset($data['filter_sort']) && $data['filter_sort'] == 'voucher') {
			$sql .= " ORDER BY voucher DESC ";	
		} elseif (isset($data['filter_sort']) && $data['filter_sort'] == 'commission') {
			$sql .= " ORDER BY commission DESC ";				
		} elseif (isset($data['filter_sort']) && $data['filter_sort'] == 'total') {
			$sql .= " ORDER BY total DESC ";	
		} elseif (isset($data['filter_sort']) && $data['filter_sort'] == 'prod_costs') {
			$sql .= " ORDER BY prod_costs DESC ";	
		} elseif (isset($data['filter_sort']) && $data['filter_sort'] == 'profit') {
			$sql .= " ORDER BY (SUM((SELECT SUM(op.total - (op.cost*op.quantity) - o.commission) FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id))+IFNULL(SUM((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'handling' GROUP BY ot.order_id)),0)+IFNULL(SUM((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'low_order_fee' GROUP BY ot.order_id)),0)+IFNULL(SUM((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'reward' GROUP BY ot.order_id)),0)+IFNULL(SUM((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'coupon' GROUP BY ot.order_id)),0)+IFNULL(SUM((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'credit' GROUP BY ot.order_id)),0)+IFNULL(SUM((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'voucher' GROUP BY ot.order_id)),0)) DESC ";				
		} else {
			$sql .= " ORDER BY date_added DESC ";
		}		
						
		$query = $this->db->query($sql);
		
		return $query->rows;
	}	

	public function getOrderStatuses($data = array()) {
		$query = $this->db->query("SELECT DISTINCT os.name, os.order_status_id FROM `" . DB_PREFIX . "order_status` os WHERE os.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY LCASE(os.name) ASC");
		
		return $query->rows;	
	}
	
	public function getOrderStores($data = array()) {
		$query = $this->db->query("SELECT DISTINCT o.store_name, o.store_id FROM `" . DB_PREFIX . "order` o ORDER BY o.store_id ASC");
		
		return $query->rows;	
	}
	
	public function getOrderCurrencies($data = array()) {
		$query = $this->db->query("SELECT DISTINCT cur.currency_id, cur.code, cur.title FROM `" . DB_PREFIX . "currency` cur ORDER BY LCASE(cur.title) ASC");
		
		return $query->rows;	
	}

	public function getOrderTaxes($data = array()) {
		$query = $this->db->query("SELECT DISTINCT ot.title AS tax_title, HEX(ot.title) AS tax FROM `" . DB_PREFIX . "order_total` ot WHERE ot.code = 'tax' ORDER BY LCASE(ot.title) ASC");
		
		return $query->rows;	
	}
	
	public function getOrderCustomerGroups($data = array()) {
		$query = $this->db->query("SELECT DISTINCT cgd.name, cgd.customer_group_id FROM `" . DB_PREFIX . "customer_group_description` cgd WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY (cgd.name) ASC");
		
		return $query->rows;	
	}

	public function getProductOptions($data = array()) {
		$query = $this->db->query("SELECT DISTINCT HEX(CONCAT(oo.name, oo.value, oo.type)) AS options, oo.name AS option_name, oo.value AS option_value FROM `" . DB_PREFIX . "order_option` oo WHERE (oo.type = 'radio' OR oo.type = 'checkbox' OR oo.type = 'select') GROUP BY oo.name, oo.value, oo.type ORDER BY oo.name, oo.value, oo.type ASC");		

		return $query->rows;
	}
	
	public function getProductLocation($data = array()) {
		$query = $this->db->query("SELECT DISTINCT p.location AS location_name, HEX(p.location) AS location_title FROM `" . DB_PREFIX . "product` p ORDER BY LCASE(p.location) ASC");
		
		return $query->rows;	
	}	

	public function getOrderAffiliate($data = array()) {
		$query = $this->db->query("SELECT DISTINCT a.affiliate_id, CONCAT(a.firstname, ' ', a.lastname) AS affiliate_name FROM `" . DB_PREFIX . "affiliate` a ORDER BY LCASE(a.lastname) ASC");
		
		return $query->rows;	
	}	
	
	public function getOrderShipping($data = array()) {
		$query = $this->db->query("SELECT DISTINCT o.shipping_method AS shipping_name, HEX(o.shipping_method) AS shipping_title FROM `" . DB_PREFIX . "order` o WHERE HEX(o.shipping_method) != 0 AND o.order_status_id > 0 ORDER BY LCASE(o.shipping_method) ASC");
		
		return $query->rows;	
	}	

	public function getOrderPayment($data = array()) {
		$query = $this->db->query("SELECT DISTINCT o.payment_method AS payment_name, HEX(o.payment_method) AS payment_title FROM `" . DB_PREFIX . "order` o WHERE HEX(o.payment_method) != 0 AND o.order_status_id > 0 ORDER BY LCASE(o.payment_method) ASC");
		
		return $query->rows;	
	}

	public function getShippingZones($data = array()) {
		$query = $this->db->query("SELECT DISTINCT o.shipping_zone AS zone_name, o.shipping_zone_id AS shipping_zone FROM `" . DB_PREFIX . "order` o WHERE o.shipping_zone_id != 0 AND o.order_status_id > 0 ORDER BY LCASE(o.shipping_zone) ASC");
		
		return $query->rows;	
	}
	
	public function getShippingCountries($data = array()) {
		$query = $this->db->query("SELECT DISTINCT o.shipping_country AS country_name, o.shipping_country_id AS shipping_country FROM `" . DB_PREFIX . "order` o WHERE o.shipping_country_id != 0 AND o.order_status_id > 0 ORDER BY LCASE(o.shipping_country) ASC");
		
		return $query->rows;	
	}

	public function getPaymentCountries($data = array()) {
		$query = $this->db->query("SELECT DISTINCT o.payment_country AS country_name, o.payment_country_id AS payment_country FROM `" . DB_PREFIX . "order` o WHERE o.payment_country_id != 0 AND o.order_status_id > 0 ORDER BY LCASE(o.payment_country) ASC");
		
		return $query->rows;	
	}

	public function getCustomerAutocomplete($data = array()) {
		
		$comp = '';
		if (!empty($data['filter_company'])) {
			$comp = " AND LCASE(o.payment_company) LIKE '%" . $this->db->escape(mb_strtolower($data['filter_company'], 'UTF-8')) . "%'";
		} else {
			$comp = '';
		}

		$cust = '';
		if (!empty($data['filter_customer_id'])) {
			$cust = " AND LCASE(CONCAT(o.firstname, ' ', o.lastname)) LIKE '%" . $this->db->escape(mb_strtolower($data['filter_customer_id'], 'UTF-8')) . "%'";
		} else {
			$cust = '';
		}

		$email = '';
		if (!empty($data['filter_email'])) {
			$email = " AND LCASE(o.email) LIKE '%" . $this->db->escape(mb_strtolower($data['filter_email'], 'UTF-8')) . "%'";			
		} else {
			$email = '';
		}
		
		$sql = "SELECT DISTINCT o.customer_id, o.payment_company AS cust_company, CONCAT(o.firstname, ' ', o.lastname) AS cust_name, o.email AS cust_email FROM `" . DB_PREFIX . "order` o WHERE o.order_status_id > 0" . $comp . $cust . $email;
						
		$query = $this->db->query($sql);
		
		return $query->rows;
	}

	public function getProductAutocomplete($data = array()) {
		
		$prod = '';
		if (!empty($data['filter_product_id'])) {
        	$prod = " WHERE LCASE(op.name) LIKE '%" . $this->db->escape(mb_strtolower($data['filter_product_id'], 'UTF-8')) . "%'";				
		} else {
			$prod = '';
		}

		$sql = "SELECT DISTINCT op.product_id, op.name AS prod_name FROM " . DB_PREFIX . "order_product op" . $prod;
						
		$query = $this->db->query($sql);
		
		return $query->rows;
	}
}
?>