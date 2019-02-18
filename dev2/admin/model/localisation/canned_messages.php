<?php
class ModelLocalisationCannedMessages extends Model {
	private $_orderMaps = array();
	private $_orderStatuses = array();
	
	public function addCannedMessage($data) {
		$this->db->query("
INSERT INTO		`" . DB_PREFIX . "canned_message`
SET				`title` = '" . $this->db->escape($data['title']) . "',
				`message` = '" . $this->db->escape($data['message']) . "'");
	}

	public function editCannedMessage($canned_message_id, $data) {
		$this->db->query("
UPDATE			`" . DB_PREFIX . "canned_message`
SET				`title` = '" . $this->db->escape($data['title']) . "',
				`message` = '" . $this->db->escape($data['message']) . "'
WHERE			`canned_message_id` = '" . (int) $canned_message_id . "'");
	}

	public function deleteCannedMessage($canned_message_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "canned_message` WHERE `canned_message_id` = '" . (int)$canned_message_id . "'");
	}

	public function getCannedMessages($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "canned_message`";

		$sort_data = array(
			'title'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY `title`";
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

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getCannedMessage($canned_message_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "canned_message` WHERE `canned_message_id` = '" . (int) $canned_message_id . "'");

		return $query->row;
	}
	
	public function orderMergeMessage($message, $order_id) {
		$message = html_entity_decode($message, ENT_QUOTES, 'UTF-8');
		if(!preg_match('~\{\{[^\}]+\}\}~i', $message)) return $message;
		
		$order_id = (int) $order_id;
		
		$order = array();
		$orderMap = array();
		
		if(!empty($this->_orderMaps[$order_id])) {
			$orderMap = $this->_orderMaps[$order_id];
		} else {
			$order = $this->getCannedOrder($order_id);
			
			
			foreach($order as $key => $value) {
				$orderMap['search'][] = '{{' . $key .'}}';
				$orderMap['replace'][] = $value;
			}
			$this->_orderMaps[$order_id] = $orderMap; 
		}
		
		if($orderMap) {
			$message = str_ireplace($orderMap['search'], $orderMap['replace'], $message);
		}
		
		return $message;
	}

	public function getTotalCannedMessages() {
		$query = $this->db->query("SELECT COUNT(`canned_message_id`) AS `total` FROM `" .
			DB_PREFIX . "canned_message`");

		return $query->row['total'];
	}
	
	public function getCannedOrder($order_id) {
		$this->load->model('sale/order');
		$order = $this->model_sale_order->getOrder($order_id);
		if($order) {
			if(empty($this->_orderStatuses)) {
				
				$this->load->model('localisation/order_status');
				$statuses = $this->model_localisation_order_status->getOrderStatuses();
				
				foreach($statuses as $status) {
					$this->_orderStatuses[$status['order_status_id']] = $status['name'];
				}
			}
			
			if(!empty($this->_orderStatuses[$order['order_status_id']])) {
				$order['order_status_formatted'] = $this->_orderStatuses[$order['order_status_id']];
			}
			$order['total_formatted'] = $this->currency->format($order['total'], $order['currency_code'], $order['currency_value']);
		}
		
		return $order;
	}
}
?>