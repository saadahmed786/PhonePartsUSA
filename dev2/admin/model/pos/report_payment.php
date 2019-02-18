<?php
class ModelPosReportPayment extends Model {
	public function getPayments($data = array()) {
		$payments = array();
		
		// get the mapping between users and orders
		$user_orders = $this->db->query("SELECT u.username, oids.order_ids FROM `" . DB_PREFIX . "user` u RIGHT JOIN (SELECT user_id, GROUP_CONCAT(order_id) as order_ids FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0' GROUP BY user_id) oids ON u.user_id = oids.user_id");
		if ($user_orders->rows) {
			if (!empty($data['filter_group'])) {
				$group = $data['filter_group'];
			} else {
				$group = 'week';
			}
			
			switch($group) {
				case 'day';
					$sql_group = " GROUP BY DAY(tmp.date_added)";
					break;
				default:
				case 'week':
					$sql_group = " GROUP BY WEEK(tmp.date_added)";
					break;	
				case 'month':
					$sql_group = " GROUP BY MONTH(tmp.date_added)";
					break;
				case 'year':
					$sql_group = " GROUP BY YEAR(tmp.date_added)";
					break;									
			}
			
			foreach ($user_orders->rows as $row) {
				$pos_select_clause = "";
				$pos_from_clause = "";
			
				$order_ids = $row['order_ids'];
				if (substr($order_ids, -1) == ',') {
					$order_ids = substr($order_ids, 0, -1);
				}
				foreach ($data['payment_types'] as $payment_type) {
					$pos_select_clause .= "SUM(tmp.`" . $payment_type . "`) AS `" . $payment_type . "`, ";
					$pos_from_clause .= "(SELECT SUM(tendered_amount) FROM `" . DB_PREFIX . "order_payment` WHERE DATE(payment_time) = DATE(p.payment_time) AND order_id IN (" . $order_ids . ") AND payment_type = '" . $payment_type . "') AS `" . $payment_type . "`, ";
				}
				$sql = "SELECT " . $pos_select_clause . "MIN(tmp.date_added) AS date_start, MAX(tmp.date_added) AS date_end FROM (SELECT " . $pos_from_clause . "DATE(p.payment_time) AS date_added FROM `" . DB_PREFIX . "order_payment` p WHERE p.order_id IN (" . $order_ids . ")";

				if (!empty($data['filter_date_start'])) {
					$sql .= " AND DATE(p.payment_time) >= '" . $this->db->escape($data['filter_date_start']) . "'";
				}
				if (!empty($data['filter_date_end'])) {
					$sql .= " AND DATE(p.payment_time) <= '" . $this->db->escape($data['filter_date_end']) . "'";
				}
				$sql .= " GROUP BY DATE(p.payment_time)) tmp" . $sql_group . " ORDER BY tmp.date_added DESC";
				
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
				if ($query->rows) {
					$payments[] = array('username' => $row['username'], 'payments' => $query->rows);
				}
			}
		}
		
		return $payments;
	}	
	
	public function getTotalPayments($data = array()) {
		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}
		
		switch($group) {
			case 'day';
				$sql_select = "SELECT COUNT(DISTINCT DAY(payment_time)) AS total FROM `" . DB_PREFIX . "order_payment`";
				break;
			default:
			case 'week':
				$sql_select = "SELECT COUNT(DISTINCT WEEK(payment_time)) AS total FROM `" . DB_PREFIX . "order_payment`";
				break;	
			case 'month':
				$sql_select = "SELECT COUNT(DISTINCT MONTH(payment_time)) AS total FROM `" . DB_PREFIX . "order_payment`";
				break;
			case 'year':
				$sql_select = "SELECT COUNT(DISTINCT YEAR(payment_time)) AS total FROM `" . DB_PREFIX . "order_payment`";
				break;									
		}

		$total_payments = 0;
		
		// get the mapping between users and orders
		$user_orders = $this->db->query("SELECT user_id, GROUP_CONCAT(order_id) as order_ids FROM `" . DB_PREFIX . "order` WHERE order_status_id > '0' GROUP BY user_id");
		if ($user_orders->rows) {
			foreach ($user_orders->rows as $row) {
				$order_ids = $row['order_ids'];
				if (substr($order_ids, -1) == ',') {
					$order_ids = substr($order_ids, 0, -1);
				}
				$sql = $sql_select . " WHERE order_id in (" . $order_ids . ")";

				if (!empty($data['filter_date_start'])) {
					$sql .= " AND DATE(payment_time) >= '" . $this->db->escape($data['filter_date_start']) . "'";
				}

				if (!empty($data['filter_date_end'])) {
					$sql .= " AND DATE(payment_time) <= '" . $this->db->escape($data['filter_date_end']) . "'";
				}

				$query = $this->db->query($sql);
				
				$total_payments += $query->row['total'];
			}
		}
		
		return $total_payments;	
	}
}
?>