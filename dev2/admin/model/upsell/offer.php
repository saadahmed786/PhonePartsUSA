<?php
class ModelUpsellOffer extends Model {
	public function install() {
		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "upsell_offer` (
				  `upsell_offer_id` int(11) NOT NULL AUTO_INCREMENT,
				  `name` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
				  `total_price_min` decimal(15,4) DEFAULT NULL,
				  `total_price_max` decimal(15,4) DEFAULT NULL,
				  `date_start` date DEFAULT NULL,
				  `date_end` date DEFAULT NULL,
				  `upsell_products` text COLLATE utf8_bin NOT NULL,
				  `cart_products` text COLLATE utf8_bin DEFAULT NULL,
				  `stores` text COLLATE utf8_bin NOT NULL,
				  PRIMARY KEY (`upsell_offer_id`),
				  KEY `name` (`name`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";
		$this->db->query($sql);
		
		$sql = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "upsell_offer_description` (
				  `upsell_offer_id` int(11) NOT NULL,
				  `language_id` int(11) NOT NULL,
				  `title` varchar(255) COLLATE utf8_bin NOT NULL DEFAULT '',
				  `description` text COLLATE utf8_bin NOT NULL,
				  PRIMARY KEY (`upsell_offer_id`,`language_id`),
				  KEY `title` (`title`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;";
		$this->db->query($sql);
	}
	
	public function uninstall() {
		$this->db->query("DROP TABLE `" . DB_PREFIX . "upsell_offer`");
		$this->db->query("DROP TABLE `" . DB_PREFIX . "upsell_offer_description`");
	}
	
	public function addUpsellOffer($data){
		$this->db->query("INSERT INTO " . DB_PREFIX . "upsell_offer SET name	= '" . $this->db->escape($data['name']) . "', total_price_min = '" . (float)$data['total_price_min']. "', total_price_max = '" . (float)$data['total_price_max']. "', date_start = '" . $this->db->escape($data['date_start']) . "', date_end = '" . $this->db->escape($data['date_end']) . "', upsell_products = '" . $this->db->escape($data['upsell_offer_product']) . "', cart_products = '" . $this->db->escape($data['cart_product']) . "', stores = '" . $this->db->escape($data['stores']) . "'");
		
		$upsell_offer_id = $this->db->getLastId();
		
		foreach ($data['upsell_offer_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "upsell_offer_description SET upsell_offer_id = '" . (int)$upsell_offer_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}
	}
	
	public function editUpsellOffer($upsell_offer_id, $data){
		$this->db->query("UPDATE " . DB_PREFIX . "upsell_offer SET name = '" . $this->db->escape($data['name']) . "', total_price_min = '" . (float)$data['total_price_min']. "', total_price_max = '" . (float)$data['total_price_max']. "', date_start = '" . $this->db->escape($data['date_start']) . "', date_end = '" . $this->db->escape($data['date_end']) . "', upsell_products = '" . $this->db->escape($data['upsell_offer_product']) . "', cart_products = '" . $this->db->escape($data['cart_product']) . "', stores = '" . $this->db->escape($data['stores']) . "' WHERE upsell_offer_id='" . (int)$upsell_offer_id . "'");
		
		foreach ($data['upsell_offer_description'] as $language_id => $value) {
			$this->db->query("UPDATE " . DB_PREFIX . "upsell_offer_description SET upsell_offer_id = '" . (int)$upsell_offer_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "' WHERE upsell_offer_id='" . (int)$upsell_offer_id . "' AND language_id = '" . (int)$language_id . "'");
		}
	}

	public function getUpsellOffer($upsell_offer_id) {
		$query = $this->db->query("SELECT `upsell_offer_id`, `name`, `total_price_min`, `total_price_max`, `date_start`, `date_end`, `upsell_products`, `cart_products`, `stores` FROM " . DB_PREFIX . "upsell_offer WHERE upsell_offer_id = '" . (int)$upsell_offer_id . "' ");
				
		return $query->row;
	}
	
	public function getUpsellOfferDescriptions($upsell_offer_id) {
		$upsell_offer_description_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "upsell_offer_description WHERE upsell_offer_id = '" . (int)$upsell_offer_id . "'");
		
		foreach ($query->rows as $result) {
			$upsell_offer_description_data[$result['language_id']] = array(
				'title'            => $result['title'],
				'description'      => $result['description']
			);
		}
		
		return $upsell_offer_description_data;
	}
	
	public function getUpsellOffers($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "upsell_offer";
		
		$implode = array();
		
		if (!empty($data['filter_name'])) {
			$sql .= " WHERE LCASE(name) LIKE '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
		}
		
		$sort_data = array(
			'sort_name',
			'sort_total_price_min',
			'sort_total_price_max',
			'sort_date_start',
			'sort_date_stop',
			'sort_status',
			'sort_order'
		);	
			
		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];	
		} else {
			$sql .= " ORDER BY upsell_offer_id";
		}
			
		if (isset($data['order']) && ($data['order'] == 'ASC')) {
			$sql .= " ASC";
		} else {
			$sql .= " DESC";
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

	public function getTotalUpsellOffers($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "upsell_offer";
		
		if (!empty($data['filter_name'])) {
			$sql .= " WHERE LCASE(name) LIKE '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
		}
		
		$query = $this->db->query($sql);
		
		return $query->row['total'];
	}
	
	public function deleteUpsellOffer($upsell_offer_id){
		$this->db->query("DELETE FROM " . DB_PREFIX . "upsell_offer WHERE upsell_offer_id='" . (int)$upsell_offer_id . "'" );
		$this->db->query("DELETE FROM " . DB_PREFIX . "upsell_offer_description WHERE upsell_offer_id='" . (int)$upsell_offer_id . "'" );
	}
	
	public function getUpsellProducts($upsell_offer_id) {
		$query = $this->db->query("SELECT `upsell_products` FROM " . DB_PREFIX . "upsell_offer WHERE upsell_offer_id = '" . (int)$upsell_offer_id . "' ");
				
		return $query->row['upsell_products'];
	}
}
?>