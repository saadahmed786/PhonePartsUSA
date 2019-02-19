<?php
class ModelFeedUksbSitemaps extends Model {
	public function getTotalProductsByStore($store_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_store p2s LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$store_id . "' Order By p2s.store_id ASC");

		return $query->row['total'];
	}			
}
?>