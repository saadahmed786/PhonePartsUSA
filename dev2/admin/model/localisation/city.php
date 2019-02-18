<?php
class ModelLocalisationCity extends Model {
	public function addCity($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "city SET city_name = '" . $this->db->escape($data['city_name']) . "', zone_id = '" . $this->db->escape($data['zone_id']) . "'");
			
		$this->cache->delete('city');
	}
	
	public function getCity($zone_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "city WHERE zone_id = '" . (int)$zone_id . "'");
		
		return $query->row;
	}
	
	public function getCities($data = array()) {
		$sql = "SELECT *, c.city_name, z.name AS Zone FROM " . DB_PREFIX . "city c LEFT JOIN " . DB_PREFIX . "zone z ON (c.zone_id = z.zone_id)";
			
		$sort_data = array(
			'z.name',
			'c.city_name',
			'c.zone_id'
		);	
			
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];	
		} else {
			$sql .= " ORDER BY z.name";	
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
	
	public function getCitiesByZoneId($zone_id) {
		$city_data = $this->cache->get('city.' . (int)$zone_id);
	
		if (!$city_data) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "city WHERE zone_id = '" . (int)$zone_id . "' ORDER BY zone_id");
	
			$city_data = $query->rows;
			
			$this->cache->set('city.' . (int)$zone_id, $city_data);
		}
	
		return $city_data;
	}
	
	public function getTotalCities() {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "city");
		
		return $query->row['total'];
	}
				
	public function getTotalCitiesByZoneId($zone_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "city WHERE zone_id = '" . (int)$zone_id . "'");
	
		return $query->row['total'];
	}
}
?>