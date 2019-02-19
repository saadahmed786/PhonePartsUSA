<?php
class ModelLocalisationCity extends Model {
	public function getCity($zone_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "city WHERE zone_id = '" . (int)$zone_id . "'");
		
		return $query->row;
	}		
	
	public function getCitiesByZoneId($zone_id) {
		$city_data = $this->cache->get('city.' . (int)$zone_id);
	
		if (!$city_data) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "city WHERE zone_id = '" . (int)$zone_id . "' ORDER BY city_name");
	
			$city_data = $query->rows;
			
			$this->cache->set('city.' . (int)$zone_id, $city_data);
		}
	
		return $city_data;
	}
}
?>