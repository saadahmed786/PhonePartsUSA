<?php
class ModelReportAdvSaleProfitExport extends Model {
	public function getSaleProfitExport($data = array()) { 	
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

		if (isset($this->request->post['export']) && ($this->request->post['export'] == 2 or $this->request->post['export'] == 7 or $this->request->post['export'] == 12)) {
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
			GROUP_CONCAT(IFNULL(FORMAT(100*(((IFNULL((SELECT SUM(op.total - (op.cost*op.quantity) - o.commission) FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id),100)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'handling' GROUP BY ot.order_id),0)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'low_order_fee' GROUP BY ot.order_id),0)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'reward' GROUP BY ot.order_id),0)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'coupon' GROUP BY ot.order_id),0)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'credit' GROUP BY ot.order_id),0)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'voucher' GROUP BY ot.order_id),0)))) / ((IFNULL((SELECT SUM(op.total - o.commission) FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id),100)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'handling' GROUP BY ot.order_id),0)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'low_order_fee' GROUP BY ot.order_id),0)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'reward' GROUP BY ot.order_id),0)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'coupon' GROUP BY ot.order_id),0)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'credit' GROUP BY ot.order_id),0)+IFNULL((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'voucher' GROUP BY ot.order_id),0))), 2),'0') ORDER BY o.order_id SEPARATOR '%<br>') AS order_profit_margin_percent, 

			'' AS product_ord_id, '' AS product_ord_idc, '' AS product_order_date, '' AS product_inv_no, '' AS product_pid, '' AS product_pidc, '' AS product_sku, '' AS product_model, '' AS product_name, '' AS product_option, '' AS product_manu, '' AS product_currency, '' AS product_price, '' AS product_quantity, '' AS product_total, '' AS product_tax, '' AS product_costs, '' AS product_profit, '' AS product_profit_margin_percent, '' AS customer_ord_id, '' AS customer_ord_idc, '' AS customer_order_date, '' AS customer_inv_no, '' AS customer_cust_id, '' AS customer_cust_idc, '' AS billing_name, '' AS billing_company, '' AS billing_address_1, '' AS billing_address_2, '' AS billing_city, '' AS billing_zone, '' AS billing_postcode, '' AS billing_country, '' AS customer_email, '' AS customer_telephone, '' AS shipping_name, '' AS shipping_company, '' AS shipping_address_1, '' AS shipping_address_2, '' AS shipping_city, '' AS shipping_zone, '' AS shipping_postcode, '' AS shipping_country ";
					
		} elseif (isset($this->request->post['export']) && ($this->request->post['export'] == 3 or $this->request->post['export'] == 8 or $this->request->post['export'] == 13)) {
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
			GROUP_CONCAT((SELECT GROUP_CONCAT(IFNULL(FORMAT(100*(op.total - op.cost*op.quantity) / op.total, 2),'0') SEPARATOR '%<br>') FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id ORDER BY op.order_product_id) ORDER BY o.order_id SEPARATOR '%<br>') AS product_profit_margin_percent, 

			'' AS order_ord_id, '' AS order_ord_idc, '' AS order_order_date, '' AS order_inv_no, '' AS order_name, '' AS order_email, '' AS order_group, '' AS order_shipping_method, '' AS order_payment_method, '' AS order_status, '' AS order_store, '' AS order_products, '' AS order_currency, '' AS order_sub_total, '' AS order_hf, '' AS order_lof, '' AS order_shipping, '' AS order_tax, '' AS order_value, '' AS order_costs, '' AS order_profit, '' AS order_profit_margin_percent, '' AS customer_ord_id, '' AS customer_ord_idc, '' AS customer_order_date, '' AS customer_inv_no, '' AS customer_cust_id, '' AS customer_cust_idc, '' AS billing_name, '' AS billing_company, '' AS billing_address_1, '' AS billing_address_2, '' AS billing_city, '' AS billing_zone, '' AS billing_postcode, '' AS billing_country, '' AS customer_email, '' AS customer_telephone, '' AS shipping_name, '' AS shipping_company, '' AS shipping_address_1, '' AS shipping_address_2, '' AS shipping_city, '' AS shipping_zone, '' AS shipping_postcode, '' AS shipping_country ";
					
		} elseif (isset($this->request->post['export']) && ($this->request->post['export'] == 4 or $this->request->post['export'] == 9 or $this->request->post['export'] == 14)) {
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
			GROUP_CONCAT(o.shipping_country ORDER BY o.order_id SEPARATOR '<br>') AS shipping_country, 

			'' AS order_ord_id, '' AS order_ord_idc, '' AS order_order_date, '' AS order_inv_no, '' AS order_name, '' AS order_email, '' AS order_group, '' AS order_shipping_method, '' AS order_payment_method, '' AS order_status, '' AS order_store, '' AS order_products, '' AS order_currency, '' AS order_sub_total, '' AS order_hf, '' AS order_lof, '' AS order_shipping, '' AS order_tax, '' AS order_value, '' AS order_costs, '' AS order_profit, '' AS order_profit_margin_percent, '' AS product_ord_id, '' AS product_ord_idc, '' AS product_order_date, '' AS product_inv_no, '' AS product_pid, '' AS product_pidc, '' AS product_sku, '' AS product_model, '' AS product_name, '' AS product_option, '' AS product_manu, '' AS product_currency, '' AS product_price, '' AS product_quantity, '' AS product_total, '' AS product_tax, '' AS product_costs, '' AS product_profit, '' AS product_profit_margin_percent ";
		
		} elseif (isset($this->request->post['export']) && ($this->request->post['export'] == 1 or $this->request->post['export'] == 6 or $this->request->post['export'] == 11)) {
		$sql .= " '' AS order_ord_id, '' AS order_ord_idc, '' AS order_order_date, '' AS order_inv_no, '' AS order_name, '' AS order_email, '' AS order_group, '' AS order_shipping_method, '' AS order_payment_method, '' AS order_status, '' AS order_store, '' AS order_products, '' AS order_currency, '' AS order_sub_total, '' AS order_hf, '' AS order_lof, '' AS order_shipping, '' AS order_tax, '' AS order_value, '' AS order_costs, '' AS order_profit, '' AS order_profit_margin_percent, '' AS product_ord_id, '' AS product_ord_idc, '' AS product_order_date, '' AS product_inv_no, '' AS product_pid, '' AS product_pidc, '' AS product_sku, '' AS product_model, '' AS product_name, '' AS product_option, '' AS product_manu, '' AS product_currency, '' AS product_price, '' AS product_quantity, '' AS product_total, '' AS product_tax, '' AS product_costs, '' AS product_profit, '' AS product_profit_margin_percent,  '' AS customer_ord_id, '' AS customer_ord_idc, '' AS customer_order_date, '' AS customer_inv_no, '' AS customer_cust_id, '' AS customer_cust_idc, '' AS billing_name, '' AS billing_company, '' AS billing_address_1, '' AS billing_address_2, '' AS billing_city, '' AS billing_zone, '' AS billing_postcode, '' AS billing_country, '' AS customer_email, '' AS customer_telephone, '' AS shipping_name, '' AS shipping_company, '' AS shipping_address_1, '' AS shipping_address_2, '' AS shipping_city, '' AS shipping_zone, '' AS shipping_postcode, '' AS shipping_country ";
		
		}
		$sql .= " FROM `" . DB_PREFIX . "order` o" . $date . $osi . $store . $cur . $tax . $cgrp . $comp . $cust . $email . $prod . $opt . $loc . $aff . $shipp . $pay . $zone . $shippc . $payc;
		
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
}	
?>