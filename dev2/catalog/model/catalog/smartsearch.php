<?php
//==============================================================================
// Smart Search v154.2
// 
// Author: Clear Thinking, LLC
// E-mail: johnathan@getclearthinking.com
// Website: http://www.getclearthinking.com
//==============================================================================

class ModelCatalogSmartsearch extends Model {
	private $type = 'catalog';
	private $name = 'smartsearch';
	private $return_total;
	private $record_search;
	private $tolerance;
	
	public function smartsearch($data = array()) {
		// Uncomment the following line if you get errors about exceeding MAX_JOIN_SIZE rows
		//$this->db->query("SET SQL_BIG_SELECTS=1");
		
		$v14x = (!defined('VERSION') || VERSION < 1.5);
		$v150 = (defined('VERSION') && strpos(VERSION, '1.5.0') === 0);
		
		$settings = ($v14x || $v150) ? unserialize($this->config->get($this->name . '_data')) : $this->config->get($this->name . '_data');
		$customer_group_id = ($this->customer->isLogged()) ? (int)$this->customer->getCustomerGroupId() : (int)$this->config->get('config_customer_group_id');
		$language_id = (int)$this->config->get('config_language_id');
		$store_id = (int)$this->config->get('config_store_id');
		
		$this->return_total = !empty($data['return_total']);
		$table_query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . $this->name . "'");
		$this->record_search = ($table_query->num_rows && !empty($data['filter_name']) && (empty($data['sort']) || $data['sort'] == 'p.sort_order') && empty($data['start'])) ? $data['filter_name'] : '';
		$this->tolerance = $settings['tolerance'];
		
		// Perform pre-search replacements
		if (!empty($settings['replace_array'])) {
			if (!empty($data['filter_name'])) {
				$data['filter_name'] = trim(str_replace($settings['replace_array'], $settings['with_array'], ' ' . $data['filter_name'] . ' '));
			}
			if (!empty($data['filter_tag'])) {
				$data['filter_tag'] = trim(str_replace($settings['replace_array'], $settings['with_array'], ' ' . $data['filter_tag'] . ' '));
			}
		}
		
		// Set search boundaries
		$_ = ($settings['partials']) ? '' : ' ';
		
		// Determine search fields
		$meta_keyword = ($v14x) ? 'meta_keywords' : 'meta_keyword';
		$this->load->model('catalog/product');
		if (method_exists($this->model_catalog_product, 'getProductTags')) {
			$product_tag = ($v14x) ? 'product_tags' : 'product_tag';
		} else {
			$product_tag = '';
		}
		
		$fields = array();
		if (!empty($settings['tag']) && $product_tag)									$fields['pt.tag'] = 'tag';
		if (!empty($settings['tag']) && !$product_tag)									$fields['pd.tag'] = 'tag';
		if (!empty($settings['model']) || !empty($data['filter_model']))				$fields['p.model'] = 'model';
		if (!empty($settings['description']) || !empty($data['filter_description']))	$fields['pd.description'] = 'description';
		if (!empty($settings['meta_description']))										$fields['pd.meta_description'] = 'meta_description';
		if (!empty($settings[$meta_keyword]))											$fields['pd.' . $meta_keyword] = $meta_keyword;
		if (!empty($settings['sku']))													$fields['p.sku'] = 'sku';
		if (!empty($settings['upc']) && !$v14x)											$fields['p.upc'] = 'upc';
		if (!empty($settings['location']))												$fields['p.location'] = 'location';
		if (!empty($settings['manufacturer']))											$fields['m.name'] = 'manufacturer';
		if (!empty($settings['attribute_group']) && !$v14x)								$fields['agd.name'] = 'attribute_group';
		if (!empty($settings['attribute_name']) && !$v14x)								$fields['ad.name'] = 'attribute_name';
		if (!empty($settings['attribute_value']) && !$v14x)								$fields['pa.text'] = 'attribute_value';
		if (!empty($settings['option_name']))											$fields[($v14x) ? 'pod.name' : 'od.name'] = 'option_name';
		if (!empty($settings['option_value']))											$fields[($v14x) ? 'povd.name' : 'ovd.name'] = 'option_value';
		if (!empty($settings['option_value']) && !$v14x)								$fields['po.option_value'] = 'product_option_value';
		
		$search_attributes = in_array('attribute_group', $fields) || in_array('attribute_name', $fields) || in_array('attribute_value', $fields);
		$search_options = in_array('option_name', $fields) || in_array('option_value', $fields);
		$search_tags = in_array('tag', $fields) || (!empty($data['filter_tag']) && $data['filter_tag'] != $data['filter_name']);
		
		// Select SQL
		$select_sql = "SELECT p.product_id, p.price, p.model,p.sale_price, pd.name,";
		foreach ($fields as $column => $alias) {
			$select_sql .= " " . $column . " AS " . $alias . ",";
		}
		$select_sql .= " (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . $customer_group_id . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special,";
		$select_sql .= " (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating";
		$select_sql .= " FROM " . DB_PREFIX . "product p";
		$select_sql .= " LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id AND pd.language_id = '" . $language_id . "')";
		$select_sql .= " LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)";
		$select_sql .= (in_array('manufacturer', $fields)) ? " LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id)" : "";
		if ($search_attributes) {
			$select_sql .= " LEFT JOIN " . DB_PREFIX . "product_attribute pa ON (p.product_id = pa.product_id)";
			$select_sql .= " LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id)";
			$select_sql .= " LEFT JOIN " . DB_PREFIX . "attribute_group_description agd ON (a.attribute_group_id = agd.attribute_group_id)";
			$select_sql .= " LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (pa.attribute_id = ad.attribute_id)";
		}
		if ($search_options) {
			$select_sql .= ($v14x) ? "" : " LEFT JOIN " . DB_PREFIX . "product_option po ON (p.product_id = po.product_id)";
			$select_sql .= ($v14x) ? " LEFT JOIN " . DB_PREFIX . "product_option_description pod ON (p.product_id = pod.product_id)" : " LEFT JOIN " . DB_PREFIX . "option_description od ON (po.option_id = od.option_id)";
			$select_sql .= ($v14x) ? " LEFT JOIN " . DB_PREFIX . "product_option_value_description povd ON (p.product_id = povd.product_id)" : " LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (po.option_id = ovd.option_id)";
		}
		if ($search_tags && $product_tag) {
			$select_sql .= " LEFT JOIN " . DB_PREFIX . $product_tag . " pt ON (p.product_id = pt.product_id AND pt.language_id = '" . $language_id . "')";
		}
		$select_sql .= " WHERE p.date_available <= NOW() AND p.status = '1' AND p2s.store_id = '" . $store_id . "'";
		
		$cache_sql = $select_sql;
		
		if (!empty($data['filter_tag']) && $data['filter_tag'] != $data['filter_name']) {
			$select_sql .= " AND LCASE(CONCAT('" . $_ . "', REPLACE(" . ($product_tag ? 'pt' : 'pd') . ".tag, ',', '" . $_ . "," . $_ . "'), '" . $_ . "')) LIKE '%" . $_ . $this->db->escape(strtolower($data['filter_tag'])) . $_ . "%'";
		}
		
		if (!empty($data['filter_category_id'])) {
			$select_sql .= " AND p.product_id IN (SELECT p2c.product_id FROM " . DB_PREFIX . "product_to_category p2c WHERE";
			if (!empty($settings['subcategories']) || !empty($data['filter_sub_category'])) {
				$implode_data = array();
				$categories = $this->getCategoriesByParentId($data['filter_category_id']);
				foreach ($categories as $category_id) {
					$implode_data[] = "p2c.category_id = '" . (int)$category_id . "'";
				}
				$select_sql .= " " . implode(' OR ', $implode_data) . ")";			
			} else {
				$select_sql .= " p2c.category_id = '" . (int)$data['filter_category_id'] . "')";
			}
		}
		
		// Group By SQL
		$group_sql = " GROUP BY p.product_id";
		
		// Order By SQL
		if (isset($data['sort']) && $data['sort'] == 'pd.name')	$order_sql = " ORDER BY LCASE(pd.name)";
		if (isset($data['sort']) && $data['sort'] == 'p.price')	$order_sql = " ORDER BY IFNULL(special, p.price)";
		if (isset($data['sort']) && $data['sort'] == 'rating')	$order_sql = " ORDER BY rating";
		if (isset($data['sort']) && $data['sort'] == 'p.model')	$order_sql = " ORDER BY LCASE(p.model)";
		if (empty($data['sort']) || !isset($order_sql))			$order_sql = " ORDER BY p.sort_order";
		
		$order_sql .= (isset($data['order']) && $data['order'] == 'DESC') ? " DESC" : " ASC";
		
		// Limit SQL
		$limit_sql = "";
		if (!$this->return_total && (isset($data['start']) || isset($data['limit']))) {
			$start = ($data['start'] < 0) ? 0 : $data['start'];
			$limit = ($data['limit'] < 1) ? 20 : $data['limit'];
			$limit_sql .= " LIMIT " . (int)$start . "," . (int)$limit;
		}
		
		// Phase 1: keywords as exact phrase
		if ($settings['phase1'] != 'skip') {
			$phase1_sql = "";
			if (!empty($data['filter_name'])) {
				$kw = strtolower($data['filter_name']);
				$keyword = $this->db->escape($kw);
				$phase1_sql .= " AND (";
				$phase1_sql .= "LCASE(CONCAT('" . $_ . "', pd.name, '" . $_ . "')) LIKE '%" . $_ . $keyword . $_ . "%'";
				$phase1_sql .= (!empty($settings['plurals']) && substr($kw, -1) == 's') ? " OR LCASE(CONCAT('" . $_ . "', pd.name, '" . $_ . "')) LIKE '%" . $_ . $this->db->escape(substr($kw, 0, -1)) . $_ . "%'" : "";
				$phase1_sql .= (!empty($settings['plurals']) && substr($kw, -2) == 'es') ? " OR LCASE(CONCAT('" . $_ . "', pd.name, '" . $_ . "')) LIKE '%" . $_ . $this->db->escape(substr($kw, 0, -2)) . $_ . "%'" : "";
				foreach ($fields as $column => $alias) {
					$col = ($alias == 'tag' && !$product_tag) ? "REPLACE(" . $column . ", ',', '" . $_ . "," . $_ . "')" : $column;
					$phase1_sql .= " OR LCASE(CONCAT('" . $_ . "', " . $col . ", '" . $_ . "')) LIKE '%" . $_ . $keyword . $_ . "%'";
				}
				$phase1_sql .= ")";
			}
			if ($settings['phase1'] == 'default') {
				$results = $this->runQuery($select_sql . $phase1_sql . $group_sql . $order_sql . $limit_sql, 1);
				if ($results) return $results;
			}
		}
		
		// Phase 2: all keywords, properly spelled
		$keywords = (!empty($data['filter_name'])) ? explode(' ', strtolower($data['filter_name'])) : array();
		if (count($keywords) > 1 || $settings['phase1'] != 'default') {
			$phase2_sql = "";
			foreach ($keywords as $kw) {
				$keyword = $this->db->escape($kw);
				$phase2_sql .= " AND (";
				$phase2_sql .= "LCASE(CONCAT('" . $_ . "', pd.name, '" . $_ . "')) LIKE '%" . $_ . $keyword . $_ . "%'";
				$phase2_sql .= (!empty($settings['plurals']) && substr($kw, -1) == 's') ? " OR LCASE(CONCAT('" . $_ . "', pd.name, '" . $_ . "')) LIKE '%" . $_ . $this->db->escape(substr($kw, 0, -1)) . $_ . "%'" : "";
				$phase2_sql .= (!empty($settings['plurals']) && substr($kw, -2) == 'es') ? " OR LCASE(CONCAT('" . $_ . "', pd.name, '" . $_ . "')) LIKE '%" . $_ . $this->db->escape(substr($kw, 0, -2)) . $_ . "%'" : "";
				foreach ($fields as $column => $alias) {
					$col = ($alias == 'tag' && !$product_tag) ? "REPLACE(" . $column . ", ',', '" . $_ . "," . $_ . "')" : $column;
					$phase2_sql .= " OR LCASE(CONCAT('" . $_ . "', " . $col . ", '" . $_ . "')) LIKE '%" . $_ . $keyword . $_ . "%'";
				}
				$phase2_sql .= ")";
			}
			if ($settings['phase1'] == 'combine') {
				if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
					$union_sql = "(" . $select_sql . $phase1_sql . $group_sql . ") UNION (" . $select_sql . $phase2_sql . $group_sql . ") " . str_replace(array('p.','pd.'), '', $order_sql) . $limit_sql;
				} else {
					$union_sql = "(" . $select_sql . $phase1_sql . $group_sql . $order_sql . ") UNION (" . $select_sql . $phase2_sql . $group_sql . $order_sql . ") " . $limit_sql;
				}
				$results = $this->runQuery($union_sql, 2);
			} else {
				$results = $this->runQuery($select_sql . $phase2_sql . $group_sql . $order_sql . $limit_sql, 2);
			}
			if ($results) return $results;
		}
		
		// Prioritization: speed vs. results
		if (!$settings['usecache']) {
			
			// Phase 3: all keywords, misspelled
			$phase3_phase4_sql = array();
			if (!empty($data['filter_name'])) {
				foreach (explode(' ', strtolower($data['filter_name'])) as $kw) {
					$underscored = $this->generateVariations($kw, 'underscore');
					$removed = $this->generateVariations($kw, 'remove');
					$transposed = $this->generateVariations($kw, 'transpose');
					$keywords = array_merge($underscored, $removed, $transposed);
					
					if (empty($keywords)) continue;
					$keywords = array_map('mysql_real_escape_string', $keywords);
					
					$keyword_sql = "";
					$keyword_sql .= "LCASE(CONCAT('" . $_ . "', pd.name, '" . $_ . "')) LIKE '%" . $_ . implode($_ . "%' OR LCASE(CONCAT('" . $_ . "', pd.name, '" . $_ . "')) LIKE '%" . $_, $keywords) . $_ . "%'";
					foreach ($fields as $column => $alias) {
						$col = ($alias == 'tag' && !$product_tag) ? "REPLACE(" . $column . ", ',', '" . $_ . "," . $_ . "')" : $column;
						$keyword_sql .= " OR LCASE(CONCAT('" . $_ . "', " . $col . ", '" . $_ . "')) LIKE '%" . $_ . implode($_ . "%' OR LCASE(CONCAT('" . $_ . "', " . $col . ", '" . $_ . "')) LIKE '%" . $_, $keywords) . $_ . "%'";
					}
					
					$phase3_phase4_sql[] = $keyword_sql;
				}
			}
			
			if (!$phase3_phase4_sql) {
				return ($this->return_total) ? 0 : array();
			}
			
			$results = $this->runQuery($select_sql . " AND ((" . implode(") AND (", $phase3_phase4_sql) . "))" . $group_sql . $order_sql . $limit_sql, 3);
			if ($results) return $results;
			
			// Phase 4: any keywords, misspelled
			$results = $this->runQuery($select_sql . " AND ((" . implode(") OR (", $phase3_phase4_sql) . "))" . $group_sql . $order_sql . $limit_sql, 4);
			return $results;
			
		} else {
			
			// Generate cache files if necessary
			$cache_files = glob(DIR_CACHE . $this->name . '.*.' . $store_id . '.' . $language_id . '.*');
			
			if ($cache_files) {
				foreach ($cache_files as $cache_file) {
					if (substr(strrchr($cache_file, '.'), 1) < time() && file_exists($cache_file)) {
						unlink($cache_file);
					}
				}
			}
			
			if (!$cache_files || !file_exists($cache_files[0])) {
				$time = time() + (int)$settings['refresh_cache'];
				$loop_interval = 1000;
				
				for ($i = 0; true; $i += $loop_interval) {
					$cache = array();
					
					$product_query = $this->db->query($cache_sql . $group_sql . " ORDER BY LCASE(pd.name) LIMIT " . $i . "," . $loop_interval);
					foreach ($product_query->rows as $result) {
						$words = explode(' ', strtolower(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')));
						if (!empty($settings['description_misspelled'])) {
							$sanitized_description = preg_replace('/[\x00-\x1F]*/u', '', strip_tags(html_entity_decode(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'), ENT_QUOTES, 'UTF-8')));
							$words = array_merge($words, explode(' ', strtolower($sanitized_description)));
						}
						foreach ($fields as $column => $alias) {
							if ($alias == 'description') continue;
							if ($alias == 'tag') {
								if ($product_tag) {
									$tag_query = $this->db->query("SELECT tag FROM " . DB_PREFIX . $product_tag . " WHERE product_id = '" . $result['product_id'] . "' AND language_id = '" . $language_id . "'");
									$fields = array();
									foreach ($tag_query->rows as $tag) {
										$tags[] = trim(strtolower(html_entity_decode($tag['tag'], ENT_QUOTES, 'UTF-8')));
									}
									$words = array_merge($words, $tags);
								} else {
									$words = array_merge($words, array_map('trim', explode(',', strtolower(html_entity_decode($result[$alias], ENT_QUOTES, 'UTF-8')))));
								}
							} else {
								$words = array_merge($words, explode(' ', strtolower(html_entity_decode($result[$alias], ENT_QUOTES, 'UTF-8'))));
							}
						}
						foreach ($words as $word) {
							if (strlen($word) >= (int)$settings['word_length'] && (!isset($cache[$word]) || !in_array($result['product_id'], $cache[$word]))) {
								$cache[$word][] = $result['product_id'];
							}
						}
					}
					
					if (!$product_query->num_rows) break;
					
					file_put_contents(DIR_CACHE . $this->name . '.' . ($i/$loop_interval) . '.' . $store_id . '.' . $language_id . '.' . $time, serialize($cache));
				}
			}
			
			// Phase 3: all keywords, misspelled
			$matches = array();
			$cache_files = glob(DIR_CACHE . $this->name . '.*.' . $store_id . '.' . $language_id . '.*');
			foreach ($cache_files as $cache_file) {
				if (!file_exists($cache_file)) continue;
				$cache = unserialize(file_get_contents($cache_file));
				foreach (array_merge(array(strtolower($data['filter_name'])), explode(' ', strtolower($data['filter_name']))) as $keyword) {
					if (!isset($matches[$keyword])) $matches[$keyword] = array();
					foreach ($cache as $word => $product_ids) {
						similar_text($word, $keyword, $percentage);
						if ($percentage >= $this->tolerance) {
							$matches[$keyword] = array_merge($matches[$keyword], $product_ids);
						}
					}
				}
			}
			
			$matches_sql = array();
			foreach ($matches as $match_list) {
				$matches_sql[] = (empty($match_list)) ? "FALSE" : "(p.product_id = '" . implode("' OR p.product_id = '", $match_list) . "')";
			}
			
			$phase3_sql = "(" . implode(" AND ", $matches_sql) . ")";
			$results = $this->runQuery($select_sql . " AND " . $phase3_sql . $group_sql . $order_sql . $limit_sql, 3);
			if ($results) return $results;
			
			// Phase 4: any keywords, misspelled
			$phase4_sql = "(" . implode(" OR ", $matches_sql) . ")";
			$results = $this->runQuery($select_sql . " AND " . $phase4_sql . $group_sql . $order_sql . $limit_sql, 4);
			return $results;
		}
	}
	
	private function runQuery($sql, $phase) {
		$query = $this->db->query($sql);
		if ($this->return_total) {
			if ($this->record_search && ($query->num_rows || $phase == 4)) {
				$this->db->query("INSERT INTO " . DB_PREFIX . $this->name . " SET date_added = NOW(), search = '" . $this->db->escape($this->record_search) . "', phase = '" . (int)$phase . "', results = '" . (int)$query->num_rows . "', customer_id = '" . (int)$this->customer->getId() . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'");
			}
			return $query->num_rows;
		} else {
			$this->load->model('catalog/product');
			$product_data = array();
			foreach ($query->rows as $result) {
				$product_data[$result['product_id']] = $this->model_catalog_product->getProduct($result['product_id']);
			}
			return $product_data;
		}
	}
	
	private function getCategoriesByParentId($category_id) {
		$category_data = array($category_id) ;
		$category_query = $this->db->query("SELECT category_id FROM " . DB_PREFIX . "category WHERE parent_id = '" . (int)$category_id . "'");
		foreach ($category_query->rows as $category) {
			$children = $this->getCategoriesByParentId($category['category_id']);
			if ($children) {
				$category_data = array_merge($children, $category_data);
			}			
		}
		return $category_data;
	}
	
	private function generateVariations($word, $type, $level = 1) {
		$words = array();
		$length = strlen($word);
		if (!$length) return array();
		if ((1 - $level / $length) >= ($this->tolerance / 100)) {
			for ($j = 0; $j < $length; $j++) {
				if ($type == 'underscore') {
					$new_word = substr_replace($word, '_', $j, 1);
				} elseif ($type == 'remove') {
					$new_word = substr_replace($word, '', $j, 1);
				} elseif ($type == 'transpose') {
					if ($j == $length - 1) continue;
					$new_word = $word;
					$new_word[$j] = $word[$j+1];
					$new_word[$j+1] = $word[$j];
				}
				$words[] = $new_word;
				$words = array_merge($words, $this->generateVariations($new_word, $type, $level + 1));
			}
		}
		return array_unique($words);
	}
}
?>