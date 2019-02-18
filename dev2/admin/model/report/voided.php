<?php
class ModelReportVoided extends Model {
	public function getOrdersOld($data = array()) {
		$sql = "SELECT vr.*,COUNT(vr.product_id) as total_products,SUM(vr.quantity) as total_quantity,u.username FROM `".DB_PREFIX."void_product` vr,`".DB_PREFIX."order` o,`".DB_PREFIX."user` u WHERE o.order_id=vr.order_id AND u.user_id=vr.user_id "; 

		
		
		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(vr.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(vr.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
		
		$sql .= " GROUP BY o.order_id,vr.user_id,vr.date_added";
		
		
		
		
		
		$sql .= " ORDER BY vr.date_added DESC";
		
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}			

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}	
			
			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}	
		
		//echo $sql;exit;
		
		$query = $this->db->query($sql);
		
		return $query->rows;
	}	
	
	
	public function getOrders($data = array()) {
		$sql = "SELECT v.*,r.name as reason FROM ".DB_PREFIX."void_product v,".DB_PREFIX."return_reason r WHERE v.reason_id=r.return_reason_id  " ; 

		
		
		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(v.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(v.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
		
		//$sql .= " GROUP BY o.order_id,vr.user_id,vr.date_added";
		
		
		
		
		
		$sql .= " ORDER BY r.return_reason_id ASC";
		
		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}			

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}	
			
			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}	
		
		//echo $sql;exit;
		
		$query = $this->db->query($sql);
		
		return $query->rows;
	}	
	
	public function getTotalOrders($data = array()) {
		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}
		
		switch($group) {
			case 'day';
				$sql = "SELECT COUNT(DISTINCT DAY(date_added)) AS total FROM `" . DB_PREFIX . "order`";
				break;
			default:
			case 'week':
				$sql = "SELECT COUNT(DISTINCT WEEK(date_added)) AS total FROM `" . DB_PREFIX . "order`";
				break;	
			case 'month':
				$sql = "SELECT COUNT(DISTINCT MONTH(date_added)) AS total FROM `" . DB_PREFIX . "order`";
				break;
			case 'year':
				$sql = "SELECT COUNT(DISTINCT YEAR(date_added)) AS total FROM `" . DB_PREFIX . "order`";
				break;									
		}
		
		if (!empty($data['filter_order_status_id'])) {
			$sql .= " WHERE order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " WHERE order_status_id > '0'";
		}
				
		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];	
	}
	
	public function getTaxes($data = array()) {
		$sql = "SELECT MIN(o.date_added) AS date_start, MAX(o.date_added) AS date_end, ot.title, SUM(ot.value) AS total, COUNT(o.order_id) AS `orders` FROM `" . DB_PREFIX . "order_total` ot LEFT JOIN `" . DB_PREFIX . "order` o ON (ot.order_id = o.order_id) WHERE ot.code = 'tax'"; 

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND o.order_status_id > '0'";
		}
		
		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
		
		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}
		
		switch($group) {
			case 'day';
				$sql .= " GROUP BY ot.title, DAY(o.date_added)";
				break;
			default:
			case 'week':
				$sql .= " GROUP BY ot.title, WEEK(o.date_added)";
				break;	
			case 'month':
				$sql .= " GROUP BY ot.title, MONTH(o.date_added)";
				break;
			case 'year':
				$sql .= " GROUP BY ot.title, YEAR(o.date_added)";
				break;									
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
		
		$query = $this->db->query($sql);
		
		return $query->rows;
	}	
	
	public function getTotalTaxes($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM (SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order_total` ot LEFT JOIN `" . DB_PREFIX . "order` o ON (ot.order_id = o.order_id) WHERE ot.code = 'tax'";
		
		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND order_status_id > '0'";
		}
				
		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
		
		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}
		
		switch($group) {
			case 'day';
				$sql .= " GROUP BY DAY(o.date_added), ot.title";
				break;
			default:
			case 'week':
				$sql .= " GROUP BY WEEK(o.date_added), ot.title";
				break;	
			case 'month':
				$sql .= " GROUP BY MONTH(o.date_added), ot.title";
				break;
			case 'year':
				$sql .= " GROUP BY YEAR(o.date_added), ot.title";
				break;									
		}
		
		$sql .= ") tmp";
		
		$query = $this->db->query($sql);

		return $query->row['total'];	
	}	
	
	public function getShipping($data = array()) {
		$sql = "SELECT MIN(o.date_added) AS date_start, MAX(o.date_added) AS date_end, ot.title, SUM(ot.value) AS total, COUNT(o.order_id) AS `orders` FROM `" . DB_PREFIX . "order_total` ot LEFT JOIN `" . DB_PREFIX . "order` o ON (ot.order_id = o.order_id) WHERE ot.code = 'shipping'"; 

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND o.order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND o.order_status_id > '0'";
		}
		
		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
		
		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}
		
		switch($group) {
			case 'day';
				$sql .= " GROUP BY ot.title, DAY(o.date_added)";
				break;
			default:
			case 'week':
				$sql .= " GROUP BY ot.title, WEEK(o.date_added)";
				break;	
			case 'month':
				$sql .= " GROUP BY ot.title, MONTH(o.date_added)";
				break;
			case 'year':
				$sql .= " GROUP BY ot.title, YEAR(o.date_added)";
				break;									
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
		
		$query = $this->db->query($sql);
		
		return $query->rows;
	}	
	
	public function getTotalShipping($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM (SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "order_total` ot LEFT JOIN `" . DB_PREFIX . "order` o ON (ot.order_id = o.order_id) WHERE ot.code = 'shipping'";
		
		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND order_status_id = '" . (int)$data['filter_order_status_id'] . "'";
		} else {
			$sql .= " AND order_status_id > '0'";
		}
				
		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
		
		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}
		
		switch($group) {
			case 'day';
				$sql .= " GROUP BY DAY(o.date_added), ot.title";
				break;
			default:
			case 'week':
				$sql .= " GROUP BY WEEK(o.date_added), ot.title";
				break;	
			case 'month':
				$sql .= " GROUP BY MONTH(o.date_added), ot.title";
				break;
			case 'year':
				$sql .= " GROUP BY YEAR(o.date_added), ot.title";
				break;									
		}
		
		$sql .= ") tmp";
		
		$query = $this->db->query($sql);

		return $query->row['total'];	
	}		
}
?>