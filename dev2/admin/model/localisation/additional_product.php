<?php 
class ModelLocalisationAdditionalProduct extends Model {
	public function addAdditionalProduct($data) {
		foreach ($data['additional_product'] as $language_id => $value) {
			if (isset($additional_product_id)) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "additional_product SET additional_product_id = '" . (int)$additional_product_id . "', sort = '" . (int)$value['sort'] . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', dropdown = '" . (isset($data['dropdown']) ? (int)$data['dropdown'] : 0) . "', display = '" . (isset($data['display']) ? (int)$data['display'] : 0) . "'");
			} else {
				$this->db->query("INSERT INTO " . DB_PREFIX . "additional_product SET language_id = '" . (int)$language_id . "', sort = '" . (int)$value['sort'] . "', name = '" . $this->db->escape($value['name']) . "', dropdown = '" . (isset($data['dropdown']) ? (int)$data['dropdown'] : 0) . "', display = '" . (isset($data['display']) ? (int)$data['display'] : 0) . "'");
				
				$additional_product_id = $this->db->getLastId();
			}
		}
		if (isset($data['values'])) {
		foreach ($data['values'] as $language_id => $values) {
			foreach ($values as $additional_product_value_id => $value) {
		
				$this->db->query("INSERT INTO " . DB_PREFIX . "additional_product_value SET additional_product_value_id = '" . (int)$additional_product_value_id . "', additional_product_id = '" . (int)$additional_product_id . "', language_id = '" . (int)$language_id . "', value = '" . $this->db->escape($value['value']) . "'");
			} 
		}
		}
				
		$this->cache->delete('additional_product');
	}

	public function editAdditionalProduct($additional_product_id, $data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "additional_product WHERE additional_product_id = '" . (int)$additional_product_id . "'");
		//print_r($data['additional_product']); die();
		foreach ($data['additional_product'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "additional_product SET additional_product_id = '" . (int)$additional_product_id . "', sort = '" . (int)$value['sort'] . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', dropdown = '" . (isset($data['dropdown']) ? (int)$data['dropdown'] : 0) . "', display = '" . (isset($data['display']) ? (int)$data['display'] : 0) . "'");
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "additional_product_value WHERE additional_product_id = '" . (int)$additional_product_id . "'");
		
		if (isset($data['values'])) {
		foreach ($data['values'] as $language_id => $values) {
			foreach ($values as $additional_product_value_id => $value) {
		
				$this->db->query("INSERT INTO " . DB_PREFIX . "additional_product_value SET additional_product_value_id = '" . (int)$additional_product_value_id . "', additional_product_id = '" . (int)$additional_product_id . "', language_id = '" . (int)$language_id . "', value = '" . $this->db->escape($value['value']) . "'");
			} 
		}}
				
		$this->cache->delete('additional_product');
	}
	
	public function deleteAdditionalProduct($additional_product_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "additional_product WHERE additional_product_id = '" . (int)$additional_product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_field WHERE additional_product_id = '" . (int)$additional_product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "additional_product_value WHERE additional_product_id = '" . (int)$additional_product_id . "'");
			
		$this->cache->delete('additional_product');
	}
		
	public function getAdditionalProduct($additional_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "additional_product WHERE additional_product_id = '" . (int)$additional_product_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
		return $query->row;
	}
	
	public function getAdditionalProductes($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "additional_product WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";
      		
			$sql .= " ORDER BY sort";	
			
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
			$additional_product_data = $this->cache->get('additional_product.' . (int)$this->config->get('config_language_id'));
		
			if (!$additional_product_data) {
				$query = $this->db->query("SELECT additional_product_id, name, dropdown, display, sort FROM " . DB_PREFIX . "additional_product WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY sort");
	
				$additional_product_data = $query->rows;
			
				$this->cache->set('additional_product.' . (int)$this->config->get('config_language_id'), $additional_product_data);
			}	
	
			return $additional_product_data;			
		}
	}
	
	public function getAdditionalProductDescriptions($additional_product_id) {
		$additional_product_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "additional_product WHERE additional_product_id = '" . (int)$additional_product_id . "'");
		
		foreach ($query->rows as $result) {
			$additional_product_data[$result['language_id']] = array('name' => $result['name'], 'sort' => $result['sort']);					
		}
		
		return $additional_product_data;
	}
	
	public function isDropDown($additional_product_id) {
				
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "additional_product WHERE additional_product_id = '" . (int)$additional_product_id . "'");
		
		foreach ($query->rows as $result) {
			$isdropdown = $result['dropdown'];					
		}
		
		return $isdropdown;
	}
	
	public function isDisplay($additional_product_id) {
				
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "additional_product WHERE additional_product_id = '" . (int)$additional_product_id . "'");
		
		foreach ($query->rows as $result) {
			$isdisplay = $result['display'];					
		}
		
		return $isdisplay;
	}
	
	public function getAdditionalProductValues($additional_product_id) {
		$additional_product_value = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "additional_product_value WHERE additional_product_id = '" . (int)$additional_product_id . "'");
		
		foreach ($query->rows as $result) {
			$additional_product_value[$result['additional_product_value_id']][$result['language_id']] =  array('value' => $result['value']);
		}
		
		return $additional_product_value;
	}
	
	public function getAdditionalProductDescriptionsbyProductId($product_id) {
		$product_to_field = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_field WHERE product_id = '" . (int)$product_id . "'");
		
		foreach ($query->rows as $result) {
			$product_to_field[$result['language_id']][$result['additional_product_id']] = array('name' => $result['name']);
		}
		return $product_to_field;
	}
	
	public function getTotalAdditionalProductes() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "additional_product WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");
		
		return $query->row['total'];
	}	
}
?>