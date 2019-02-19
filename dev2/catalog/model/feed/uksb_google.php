<?php
class ModelFeedUKSBGoogle extends Model {
	public function getProducts($data = array()) {
		$customer_group_id = $this->config->get('config_customer_group_id');
		$customer_group_id='1633';

		$sql = "SELECT p.product_id, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$customer_group_id . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$customer_group_id . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "uksb_google_merchant_products uksbp ON (p.product_id = uksbp.product_id) WHERE p.price > 0 AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND uksbp.g_on_google = 1 AND (uksbp.g_expiry_date > NOW() OR uksbp.g_expiry_date = '') AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' GROUP BY p.product_id ORDER BY p.product_id ASC";

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}				

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}	

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$product_data = array();

		$query = $this->db->query($sql);

		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
		}

		return $product_data;
	}

	public function getProduct($product_id) {
		$customer_group_id = $this->config->get('config_customer_group_id');
		$customer_group_id='1633';
		$query = $this->db->query("SELECT DISTINCT *, pd.name AS name, p.image, m.name AS manufacturer, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$customer_group_id . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$customer_group_id . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) LEFT JOIN " . DB_PREFIX . "uksb_google_merchant_products uksbp ON (p.product_id = uksbp.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return array(
				'product_id'       => $query->row['product_id'],
				'name'             => $query->row['name'],
				'description'      => $query->row['description'],
				'model'            => $query->row['model'],
				'sku'              => $query->row['sku'],
				'upc'              => $query->row['upc'],
				'location'         => $query->row['location'],
				'quantity'         => $query->row['quantity'],
				'image'            => $query->row['image'],
				'manufacturer'     => $query->row['manufacturer'],
				'price'            => ($query->row['discount'] ? $query->row['discount'] : $query->row['price']),
				'special'          => $query->row['special'],
				'tax_class_id'     => $query->row['tax_class_id'],
				'date_available'   => $query->row['date_available'],
				'weight'           => $query->row['weight'],
				'weight_class_id'  => $query->row['weight_class_id'],
				'rating'           => round($query->row['rating']),
				'reviews'          => $query->row['reviews'] ? $query->row['reviews'] : 0,
				'google_category_gb'  => $query->row['google_category_gb'],
				'google_category_us'  => $query->row['google_category_us'],
				'google_category_au'  => $query->row['google_category_au'],
				'google_category_fr'  => $query->row['google_category_fr'],
				'google_category_de'  => $query->row['google_category_de'],
				'google_category_it'  => $query->row['google_category_it'],
				'google_category_nl'  => $query->row['google_category_nl'],
				'google_category_es'  => $query->row['google_category_es'],
				'google_category_pt'  => $query->row['google_category_pt'],
				'google_category_cz'  => $query->row['google_category_cz'],
				'google_category_jp'  => $query->row['google_category_jp'],
				'google_category_dk'  => $query->row['google_category_dk'],
				'google_category_no'  => $query->row['google_category_no'],
				'google_category_pl'  => $query->row['google_category_pl'],
				'google_category_ru'  => $query->row['google_category_ru'],
				'google_category_sv'  => $query->row['google_category_sv'],
				'google_category_tr'  => $query->row['google_category_tr'],
				'g_condition'  => $query->row['g_condition'],
				'g_brand'  => $query->row['g_brand'],
				'g_gtin'  => $query->row['g_gtin'],
				'g_identifier_exists'  => $query->row['g_identifier_exists'],
				'g_gender'  => $query->row['g_gender'],
				'g_age_group'  => $query->row['g_age_group'],
				'g_colour'  => $query->row['g_colour'],
				'g_size'  => $query->row['g_size'],
				'g_size_type'  => $query->row['g_size_type'],
				'g_size_system'  => $query->row['g_size_system'],
				'g_material'  => $query->row['g_material'],
				'g_pattern'  => $query->row['g_pattern'],
				'g_mpn'              => $query->row['g_mpn'],
				'v_mpn'  => $query->row['v_mpn'],
				'v_gtin'  => $query->row['v_gtin'],
				'v_prices'  => $query->row['v_prices'],
				'v_images'  => $query->row['v_images'],
				'g_multipack'  => $query->row['g_multipack'],
				'g_is_bundle'  => $query->row['g_is_bundle'],
				'g_adult'  => $query->row['g_adult'],
				'g_adwords_redirect'  => $query->row['g_adwords_redirect'],
				'g_custom_label_0'  => $query->row['g_custom_label_0'],
				'g_custom_label_1'  => $query->row['g_custom_label_1'],
				'g_custom_label_2'  => $query->row['g_custom_label_2'],
				'g_custom_label_3'  => $query->row['g_custom_label_3'],
				'g_custom_label_4'  => $query->row['g_custom_label_4'],
				'g_expiry_date'  => $query->row['g_expiry_date'],
				'g_unit_pricing_measure'  => $query->row['g_unit_pricing_measure'],
				'g_unit_pricing_base_measure'  => $query->row['g_unit_pricing_base_measure'],
				'g_energy_efficiency_class'  => $query->row['g_energy_efficiency_class']
			);
		} else {
			return false;
		}
	}

	public function getProductImages($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");

		return $query->rows;
	}
	
	public function getCategory($category_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");
		
		return $query->row;
	}
	
	public function getCategories($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		return $query->rows;
	}	


	public function getCategoryGoogleCategories($category_id, $lang) {
		$col = 'google_category_'.$lang;
		
		$query = $this->db->query("SELECT " . $this->db->escape($col) . " as cat FROM " . DB_PREFIX . "uksb_google_merchant_categories WHERE category_id = '" . (int)$category_id . "'");
		
		if($query->num_rows){
			return $query->row['cat'];
		}else{
			return '';
		}
	}			

	public function getFeedSpecialStartDate($product_id) {
		$query = $this->db->query("SELECT ps.date_start AS start FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = '" . (int)$product_id . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) GROUP BY ps.date_start");
		
		if($query->row['start']=='0000-00-00'){
			return '2011-09-06';
		}else{
			return $query->row['start'];
		}
	}	

	public function getFeedSpecialEndDate($product_id) {
		$query = $this->db->query("SELECT ps.date_end AS end FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = '" . (int)$product_id . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) GROUP BY ps.date_end");
		
		if($query->row['end']=='0000-00-00'){
			return '2034-09-06';
		}else{
			return $query->row['end'];
		}
	}	

	public function getTotalProducts() {
		$customer_group_id = $this->config->get('config_customer_group_id');
		$customer_group_id='1633';
		$sql = "SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "uksb_google_merchant_products uksbp ON (p.product_id = uksbp.product_id) WHERE p.price > 0 AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pd.description != '' AND p.status = '1' AND p.date_available <= NOW() AND uksbp.g_on_google = 1 AND (uksbp.g_expiry_date > NOW() OR uksbp.g_expiry_date = '') AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' GROUP BY p.product_id";

		$query = $this->db->query($sql);

		return $query->num_rows;
	}
}