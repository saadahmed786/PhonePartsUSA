<?php
class ModelCatalogPromoTags extends Model {
	public function addPromoTags($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "promo_tags SET promo_text = '" . $this->db->escape($data['promo_text']) . "', promo_link = '" . $this->db->escape($data['promo_link']) . "', promo_direction = '" . (int)$data['promo_direction'] . "', sort_order = '" . (int)$data['sort_order'] . "'");

		$promo_tags_id = $this->db->getLastId();

		if (isset($data['image']) || isset($data['pimage'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "promo_tags SET image = '" . $this->db->escape($data['image']) . "', pimage = '" . $this->db->escape($data['pimage']) . "' WHERE promo_tags_id = '" . (int)$promo_tags_id . "'");
		}
		
		$this->cache->delete('promotags');
	}

	public function editPromoTags($promo_tags_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "promo_tags SET promo_text = '" . $this->db->escape($data['promo_text']) . "', promo_direction = '" . (int)$data['promo_direction'] . "', promo_link = '" . $this->db->escape($data['promo_link']) . "', sort_order = '" . (int)$data['sort_order'] . "' WHERE promo_tags_id = '" . (int)$promo_tags_id . "'");

		if (isset($data['image']) || isset($data['pimage'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "promo_tags SET image = '" . $this->db->escape($data['image']) . "', pimage = '" . $this->db->escape($data['pimage']) . "' WHERE promo_tags_id = '" . (int)$promo_tags_id . "'");
		}

		$this->cache->delete('promotags');
	}

	public function deletePromoTags($promo_tags_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "promo_tags WHERE promo_tags_id = '" . (int)$promo_tags_id . "'");
		$this->cache->delete('promotags');
	}
	
	public function getPromoTag($promo_tags_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "promo_tags WHERE promo_tags_id = '" . (int)$promo_tags_id . "'");
		
		return $query->row;
	}
	
	public function getPromoTags($data = array()) {
		if ($data) {
			
			$sql = "SELECT * FROM " . DB_PREFIX . "promo_tags";
			
			$sort_data = array(
				'promo_text',
				'sort_order'
			);	
			
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];	
			} else {
				$sql .= " ORDER BY promo_text";	
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
		} else {
			
			if (!$promotags_data) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "promo_tags ORDER BY promo_text");

				$promotags_data = $query->rows;

				$this->cache->set('promotags', $promotags_data);
			}

			return $promotags_data;
		}
	}
	public function getTotalProductsByPromoTagsTopRightId($promotags_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE promo_top_right = '" . (int)$promotags_id . "'");

		return $query->row['total'];
	}
	
	public function getTotalProductsByPromoTagsTopLeftId($promotags_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE promo_top_left = '" . (int)$promotags_id . "'");

		return $query->row['total'];
	}
	
	public function getTotalProductsByPromoTagsBottomRightId($promotags_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE promo_bottom_left = '" . (int)$promotags_id . "'");

		return $query->row['total'];
	}
	
	public function getTotalProductsByPromoTagsBottomLeftId($promotags_id) {
      	$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE promo_bottom_right = '" . (int)$promotags_id . "'");

		return $query->row['total'];
	}

	public function getTotalPromoTags($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "promo_tags";
		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	
}
?>