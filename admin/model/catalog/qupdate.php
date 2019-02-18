<?php
class ModelCatalogQupdate extends Model {
	public function editQupdate($product_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "product SET model = '" . $this->db->escape($data['model']) . "', price = '" . (float)$data['price'] . "', quantity = '" . (int)$data['quantity'] . "', weight = '" . (float)$data['weight'] . "', status = '" . (int)$data['status'] . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");
		$this->cache->delete('product');
	}

	public function deleteProduct($product_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_tag WHERE product_id='" . (int)$product_id. "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "review WHERE product_id = '" . (int)$product_id . "'");
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id. "'");
		
		$this->cache->delete('product');
	}

	public function getProduct($product_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "') AS keyword FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");
				
		return $query->row;
	}

	public function getProducts($data = array()) {
		if ($data) {
			$sql = "SELECT p.*,pd.*,p2c.product_id product_id2 FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
			
			if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
				$sql .= " AND LCASE(pd.name) LIKE '%" . $this->db->escape(strtolower($data['filter_name'])) . "%'";
			}
			
			if (isset($data['filter_category']) && !is_null($data['filter_category'])) {
                $lGroup = False ;
                $sql .= " AND p2c.category_id = '" . $this->db->escape($data['filter_category']) . "'";
            }else{
                $lGroup = True ;
            }

			if (isset($data['filter_model']) && !is_null($data['filter_model'])) {
				$sql .= " AND LCASE(p.model) LIKE '%" . $this->db->escape(strtolower($data['filter_model'])) . "%'";
			}

			if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
				$sql .= " AND p.price = '" . $this->db->escape($data['filter_price']) . "'";
			}
			
			if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
				$sql .= " AND p.quantity = '" . $this->db->escape($data['filter_quantity']) . "'";
			}
			
			if (isset($data['filter_weight']) && !is_null($data['filter_weight'])) {
				$sql .= " AND p.weight = '" . $this->db->escape($data['filter_weight']) . "'";
			}

			if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
				$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
			}

			$sort_data = array(
				'pd.name',
				'p2c.category_id',
				'p.model',
				'p.price',
				'p.quantity',
				'p.weight',
				'p.status',
				'p.sort_order'
			);
			
			if ($lGroup == True) {
                $sql .= " group by p.product_id ";
            }

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY pd.name";
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
			$product_data = $this->cache->get('product.' . $this->config->get('config_language_id'));

			if (!$product_data) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY pd.name ASC");

				$product_data = $query->rows;

				$this->cache->set('product.' . $this->config->get('config_language_id'), $product_data);
			}

			return $product_data;
		}
	}
 
	public function getTotalProducts($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		
		if (isset($data['filter_name']) && !is_null($data['filter_name'])) {
			$sql .= " AND LCASE(pd.name) LIKE '%" . $this->db->escape(strtolower($data['filter_name'])) . "%'";
		}

		if (isset($data['filter_model']) && !is_null($data['filter_model'])) {
			$sql .= " AND LCASE(p.model) LIKE '%" . $this->db->escape(strtolower($data['filter_model'])) . "%'";
		}

		if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
			$sql .= " AND LCASE(p.price) LIKE '" . $this->db->escape(strtolower($data['filter_price'])) . "%'";
		}
		
		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
				$sql .= " AND LCASE(p.quantity) LIKE '%" . $this->db->escape(strtolower($data['filter_quantity'])) . "%'";
		}
		
		if (isset($data['filter_weight']) && !is_null($data['filter_weight'])) {
			$sql .= " AND LCASE(p.weight) LIKE '" . $this->db->escape(strtolower($data['filter_weight'])) . "%'";
		}
		
		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

}
?>