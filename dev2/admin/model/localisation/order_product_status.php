<?php 
class ModelLocalisationOrderProductStatus extends Model {
	public function addOrderProductStatus($data) {
		foreach ($data['order_product_status'] as $language_id => $value) {
			if (isset($order_product_status_id)) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "order_product_status SET " 
									. "   order_product_status_id = '" . (int)$order_product_status_id 
									. "', language_id             = '" . (int)$language_id 
									. "', days_delay              = '" . (int)$data['days_delay']
									. "', order_status_id         = '" . (int)$data['order_status_id']
									. "', int_order_status_id     = '" . (int)$data['int_order_status_id']
									. "', name = '" . $this->db->escape($value['name']) . "'");
			} else {
				$this->db->query("INSERT INTO " . DB_PREFIX . "order_product_status SET "
									. "   language_id             = '" . (int)$language_id 
									. "', days_delay              = '" . (int)$data['days_delay']
									. "', order_status_id         = '" . (int)$data['order_status_id']
									. "', int_order_status_id     = '" . (int)$data['int_order_status_id']
									. "', name = '" . $this->db->escape($value['name']) . "'");
				
				$order_product_status_id = $this->db->getLastId();
			}
		}
		
		$this->cache->delete('order_product_status');
	}

	public function editOrderProductStatus($order_product_status_id, $data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "order_product_status WHERE order_product_status_id = '" . (int)$order_product_status_id . "'");

		foreach ($data['order_product_status'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_product_status SET "
									. "   order_product_status_id = '" . (int)$order_product_status_id 
									. "', language_id             = '" . (int)$language_id 
									. "', days_delay              = '" . (int)$data['days_delay']
									. "', order_status_id         = '" . (int)$data['order_status_id']
									. "', int_order_status_id     = '" . (int)$data['int_order_status_id']
									. "', name = '" . $this->db->escape($value['name']) . "'");
		}
				
		$this->cache->delete('order_product_status');
	}
	
	public function deleteOrderProductStatus($order_product_status_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "order_product_status WHERE order_product_status_id = '" . (int)$order_product_status_id . "'");
	
		$this->cache->delete('order_product_status');
	}
		
	public function getOrderProductStatus($order_product_status_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product_status WHERE order_product_status_id = '" . (int)$order_product_status_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
		return $query->row;
	}
		
	public function getOrderProductStatuses($data = array()) {
      	if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "order_product_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";
			
			$sql .= " ORDER BY name";	
			
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
		} else {
			$order_product_status_data = $this->cache->get('order_product_status.' . (int)$this->config->get('config_language_id'));
		
			if (!$order_product_status_data) {
				$query = $this->db->query("SELECT order_product_status_id, name FROM " . DB_PREFIX . "order_product_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");
	
				$order_product_status_data = $query->rows;
			
				$this->cache->set('order_product_status.' . (int)$this->config->get('config_language_id'), $order_product_status_data);
			}	
	
			return $order_product_status_data;				
		}
	}
	
	public function getOrderProductStatusDescriptions($order_product_status_id) {
		$order_product_status_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_product_status WHERE order_product_status_id = '" . (int)$order_product_status_id . "'");
		
		foreach ($query->rows as $result) {
			$order_product_status_data[$result['language_id']] = array('name' => $result['name']);
		}
		
		return $order_product_status_data;
	}
	
	public function getTotalOrderProductStatuses() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_product_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
		return $query->row['total'];
	}	
}
?>