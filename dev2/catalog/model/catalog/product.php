<?php
class ModelCatalogProduct extends Model {
	
	public function updateViewed($product_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "product SET viewed = (viewed + 1) WHERE product_id = '" . (int)$product_id . "'");
	}
	
	public function getProduct($product_id) {
		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getCustomerGroupId();
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		$product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p.status = '1' AND p.visibility='1'");

		$is_platinum = false;
		if(substr($product_query->row['model'],0,7)=='APL-001' || substr($product_query->row['model'],0,4)=='SRN-' || substr($product_query->row['model'],0,7)=='TAB-SRN'  )
		{
			$is_platinum = true;
		}
		if($is_platinum)
		{

		$customer_group_id = '1633'; // force assigning the customer group of platinum 1633
		}	
		
		
		$query = $this->db->query("SELECT DISTINCT *, pd.name AS name, p.is_main_sku, p.image, p.sale_price, m.name AS manufacturer, (SELECT price FROM " . DB_PREFIX . "product_discount pd2 WHERE pd2.product_id = p.product_id AND pd2.customer_group_id = '" . (int)$customer_group_id . "' AND pd2.quantity = '1' AND ((pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW())) ORDER BY pd2.priority ASC, pd2.price ASC LIMIT 1) AS discount, (SELECT price FROM " . DB_PREFIX . "product_special ps WHERE ps.product_id = p.product_id AND ps.customer_group_id = '" . (int)$customer_group_id . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) ORDER BY ps.priority ASC, ps.price ASC LIMIT 1) AS special, (SELECT points FROM " . DB_PREFIX . "product_reward pr WHERE pr.product_id = p.product_id AND customer_group_id = '" . (int)$customer_group_id . "') AS reward, (SELECT ss.name FROM " . DB_PREFIX . "stock_status ss WHERE ss.stock_status_id = p.stock_status_id AND ss.language_id = '" . (int)$this->config->get('config_language_id') . "') AS stock_status, (SELECT wcd.unit FROM " . DB_PREFIX . "weight_class_description wcd WHERE p.weight_class_id = wcd.weight_class_id AND wcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS weight_class, (SELECT lcd.unit FROM " . DB_PREFIX . "length_class_description lcd WHERE p.length_class_id = lcd.length_class_id AND lcd.language_id = '" . (int)$this->config->get('config_language_id') . "') AS length_class, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating, (SELECT COUNT(*) AS total FROM " . DB_PREFIX . "review r2 WHERE r2.product_id = p.product_id AND r2.status = '1' GROUP BY r2.product_id) AS reviews, p.sort_order FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) LEFT JOIN " . DB_PREFIX . "manufacturer m ON (p.manufacturer_id = m.manufacturer_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' and p.visibility=1 AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			$query->row['price'] = ($query->row['discount'] ? $query->row['discount'] : $query->row['price']);
			$query->row['rating'] = (int)$query->row['rating'];
			
			
			
			
			
			
			return $query->row;
		} else {
			return false;
		}
	}
	public function getProductGrades($main_sku) {
		$query = $this->db->query("SELECT product_id,price,item_grade FROM oc_product WHERE main_sku = '". $main_sku ."' AND quantity <> '0' AND status = '1' order by product_id");
		return $query->rows;
	}
	

	public function getProductQuality($sku) {

		$query = $this->db->query("SELECT f.* FROM inv_device_product AS a, inv_device_manufacturer AS b, inv_device_device AS c, inv_device_model AS d, inv_device_attrib AS e, inv_attr AS f, inv_attribute_group AS g WHERE a.device_product_id = b.device_product_id AND b.device_manufacturer_id = c.device_manufacturer_id AND c.device_device_id = d.device_device_id AND d.device_model_id = e.device_model_id AND e.attrib_id = f.id AND f.attribute_group_id = g.id AND g.name = 'Quality' AND a.sku = '$sku' GROUP BY e.attrib_id");
		
		if ($query->num_rows) {
			return $query->row['name'];
		} else {
			return false;
		}
	}

	public function getDeviceModels($product_id)
	{
		// error_reporting(E_ALL);
// echo "SELECT a.`device_product_id` FROM `inv_device_product` `a`, `oc_product` `b`, `oc_product_description` `c`, `inv_device_class` `d`, `inv_classification` `e` WHERE a.`sku` = b.`model` AND b.`product_id` = c.`product_id` AND a.`device_product_id` = d.`device_product_id` AND d.`class_id` = e.`id` AND b.`is_main_sku` = '1' AND b.`status` = '1' AND b.`product_id` ='".$product_id."' ";exit;		

		$device_product_id = $this->db->query("SELECT a.`device_product_id` FROM `inv_device_product` `a`, `oc_product` `b`, `oc_product_description` `c`, `inv_device_class` `d`, `inv_classification` `e` WHERE a.`sku` = b.`model` AND b.`product_id` = c.`product_id` AND a.`device_product_id` = d.`device_product_id` AND d.`class_id` = e.`id` AND b.`is_main_sku` = '1' AND b.`status` = '1' AND b.`product_id` ='".$product_id."' ");
		$device_product_id = $device_product_id->row;
		$final_result = array();
		if($device_product_id)
		{


			$manufacturer_id = $this->db->query("select * from inv_device_manufacturer where device_product_id='".$device_product_id['device_product_id']."'")->row;
			if($manufacturer_id)
			{
				$device_device = $this->db->query("SELECT group_concat( b.device_id ) as device_id FROM inv_device_manufacturer a,inv_device_device b WHERE a.`device_manufacturer_id`=b.`device_manufacturer_id` AND a.`manufacturer_id`='" .(int) $manufacturer_id['manufacturer_id'] . "' AND a.`device_product_id`='" . (int) $device_product_id['device_product_id'] . "'")->row;
		  		if($device_device['device_id']!='')
		  		{
		  			$selected_models = $this->db->query("SELECT group_concat(mo.model_id) as model_ids FROM `inv_device_manufacturer` m INNER JOIN `inv_device_device` d ON (m.`device_manufacturer_id` = d.`device_manufacturer_id`) INNER JOIN `inv_device_model` mo ON (d.`device_device_id` = mo.`device_device_id`) WHERE m.manufacturer_id='".$manufacturer_id['manufacturer_id']."' AND m.device_product_id='".$device_product_id['device_product_id']."' AND d.device_id in (".$device_device['device_id'].")")->row;
		  			if($selected_models['model_ids']!='')
		  			{
		  				// $this->log->write("SELECT mt.device, mc.`id`,d.`sub_model`,d.`model_id`,d.`sub_model_id`,mc.`carrier_id`,c.`name` FROM `inv_model_dt` d INNER JOIN inv_model_mt mt on (mt.model_id=d.model_id) LEFT JOIN `inv_model_carrier` mc ON (d.`sub_model_id` = mc.`sub_model_id`) LEFT JOIN `inv_carrier` c ON (mc.`carrier_id` = c.`id`) WHERE d.model_id  in  (".$device_device['device_id'].") and mc.id in (".$selected_models['model_ids'].") ");
						$final_result = $this->db->query("SELECT mt.device, mc.`id`,d.`sub_model`,d.`model_id`,d.`sub_model_id`,mc.`carrier_id`,c.`name` FROM `inv_model_dt` d INNER JOIN inv_model_mt mt on (mt.model_id=d.model_id) LEFT JOIN `inv_model_carrier` mc ON (d.`sub_model_id` = mc.`sub_model_id`) LEFT JOIN `inv_carrier` c ON (mc.`carrier_id` = c.`id`) WHERE d.model_id  in  (".$device_device['device_id'].") and mc.id in (".$selected_models['model_ids'].") ")->rows; 	
		  			}
		  		}
				
			}
			
		}

		  
		return $final_result;

	}

	public function getProducts($data = array()) {
		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getCustomerGroupId();
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}	
		
		$cache = md5(http_build_query($data));
		
		$product_data = $this->cache->get('product.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id . '.' . $cache);
		
		
		if (!$product_data) {
			$sql = "SELECT p.product_id,p.sale_price, (SELECT AVG(rating) AS total FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = p.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)"; 
			
			if (!empty($data['filter_tag'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_tag pt ON (p.product_id = pt.product_id)";			
			}
			
			if (!empty($data['filter_category_id'])) {
				$sql .= " LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)";			
			}
			
			$sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'"; 
			
			if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
				$sql .= " AND (";

					if (!empty($data['filter_name'])) {
						$implode = array();

						$words = explode(' ', trim(preg_replace('/\s\s+/', ' ', $data['filter_name'])));

						foreach ($words as $word) {
							if (!empty($data['filter_description'])) {
								$implode[] = "LCASE(pd.name) LIKE '%" . $this->db->escape(utf8_strtolower($word)) . "%' OR LCASE(pd.description) LIKE '%" . $this->db->escape(utf8_strtolower($word)) . "%'";
							} else {
								$implode[] = "LCASE(pd.name) LIKE '%" . $this->db->escape(utf8_strtolower($word)) . "%'";
							}				
						}

						if ($implode) {
							$sql .= " " . implode(" OR ", $implode) . "";
						}
					}

					if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
						$sql .= " OR ";
					}

					if (!empty($data['filter_tag'])) {
						$implode = array();

						$words = explode(' ', trim(preg_replace('/\s\s+/', ' ', $data['filter_tag'])));

						foreach ($words as $word) {
							$implode[] = "LCASE(pt.tag) LIKE '%" . $this->db->escape(utf8_strtolower($word)) . "%'";
						}

						if ($implode) {
							$sql .= " " . implode(" OR ", $implode) . " AND pt.language_id = '" . (int)$this->config->get('config_language_id') . "'";
						}
					}

					$sql .= ")";
}

if (!empty($data['filter_category_id'])) {
	if (!empty($data['filter_sub_category'])) {
		$implode_data = array();

		$implode_data[] = "p2c.category_id = '" . (int)$data['filter_category_id'] . "'";

		$this->load->model('catalog/category');

		$categories = $this->model_catalog_category->getCategoriesByParentId($data['filter_category_id']);

		foreach ($categories as $category_id) {
			$implode_data[] = "p2c.category_id = '" . (int)$category_id . "'";
		}

		$sql .= " AND (" . implode(' OR ', $implode_data) . ")";			
	} else {
		$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
	}
}

if (!empty($data['filter_model'])) {
	$sql .= " AND p.model = '" . $data['filter_model'] . "'";
}
if (!empty($data['filter_blowout'])) {
	$sql .= " AND p.is_blowout = '" . (int)$data['filter_blowout'] . "'";
}


if (!empty($data['filter_manufacturer_id'])) {
	$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
}

$sql .= " GROUP BY p.product_id";

$sort_data = array(
	'pd.name',
	'p.model',
	'p.quantity',
	'p.price',
	'rating',
	'p.sort_order',
	'p.date_added'
	);	

if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
	if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
		$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
	} else {
		$sql .= " ORDER BY " . $data['sort'];
	}
} else {
	$sql .= " ORDER BY p.sort_order";	
}

if (isset($data['order']) && ($data['order'] == 'DESC')) {
	$sql .= " DESC, LCASE(pd.name) DESC";
} else {
	$sql .= " ASC, LCASE(pd.name) ASC";
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

$product_data = array();

$query = $this->db->query($sql);

foreach ($query->rows as $result) {
	$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
}

$this->cache->set('product.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id . '.' . $cache, $product_data);
}

return $product_data;
}

public function getProductSpecials($data = array()) {
	if ($this->customer->isLogged()) {
		$customer_group_id = $this->customer->getCustomerGroupId();
	} else {
		$customer_group_id = $this->config->get('config_customer_group_id');
	}	

	$sql = "SELECT DISTINCT ps.product_id, (SELECT AVG(rating) FROM " . DB_PREFIX . "review r1 WHERE r1.product_id = ps.product_id AND r1.status = '1' GROUP BY r1.product_id) AS rating FROM " . DB_PREFIX . "product_special ps LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ps.customer_group_id = '" . (int)$customer_group_id . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW())) GROUP BY ps.product_id";

	$sort_data = array(
		'pd.name',
		'p.model',
		'ps.price',
		'rating',
		'p.sort_order'
		);

	if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
		if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
			$sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
		} else {
			$sql .= " ORDER BY " . $data['sort'];
		}
	} else {
		$sql .= " ORDER BY p.sort_order";	
	}

	if (isset($data['order']) && ($data['order'] == 'DESC')) {
		$sql .= " DESC, LCASE(pd.name) DESC";
	} else {
		$sql .= " ASC, LCASE(pd.name) ASC";
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

	$product_data = array();

	$query = $this->db->query($sql);

	foreach ($query->rows as $result) { 		
		$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
	}

	return $product_data;
}

public function getLatestProducts($limit) {
	if ($this->customer->isLogged()) {
		$customer_group_id = $this->customer->getCustomerGroupId();
	} else {
		$customer_group_id = $this->config->get('config_customer_group_id');
	}	

	$product_data = $this->cache->get('product.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $customer_group_id . '.' . (int)$limit);

	if (!$product_data) { 
		$query = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY p.date_added DESC LIMIT " . (int)$limit);

		foreach ($query->rows as $result) {
			$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
		}

		$this->cache->set('product.latest.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'). '.' . $customer_group_id . '.' . (int)$limit, $product_data);
	}

	return $product_data;
}

public function getPopularProducts($limit) {
	$product_data = array();

	$query = $this->db->query("SELECT p.product_id FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ORDER BY p.viewed, p.date_added DESC LIMIT " . (int)$limit);

	foreach ($query->rows as $result) { 		
		$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
	}

	return $product_data;
}

public function getBestSellerProducts($limit) {
	if ($this->customer->isLogged()) {
		$customer_group_id = $this->customer->getCustomerGroupId();
	} else {
		$customer_group_id = $this->config->get('config_customer_group_id');
	}	

	$product_data = $this->cache->get('product.bestseller.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'). '.' . $customer_group_id . '.' . (int)$limit);

	if (!$product_data) { 
		$product_data = array();

		$query = $this->db->query("SELECT op.product_id, COUNT(*) AS total FROM " . DB_PREFIX . "order_product op LEFT JOIN `" . DB_PREFIX . "order` o ON (op.order_id = o.order_id) LEFT JOIN `" . DB_PREFIX . "product` p ON (op.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE o.order_status_id > '0' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' GROUP BY op.product_id ORDER BY total DESC LIMIT " . (int)$limit);

		foreach ($query->rows as $result) { 		
			$product_data[$result['product_id']] = $this->getProduct($result['product_id']);
		}

		$this->cache->set('product.bestseller.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id'). '.' . $customer_group_id . '.' . (int)$limit, $product_data);
	}

	return $product_data;
}

public function getProductAttributes($product_id) {
	$product_attribute_group_data = array();

	$product_attribute_group_query = $this->db->query("SELECT ag.attribute_group_id, agd.name FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_group ag ON (a.attribute_group_id = ag.attribute_group_id) LEFT JOIN " . DB_PREFIX . "attribute_group_description agd ON (ag.attribute_group_id = agd.attribute_group_id) WHERE pa.product_id = '" . (int)$product_id . "' AND agd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY ag.attribute_group_id ORDER BY ag.sort_order, agd.name");

	foreach ($product_attribute_group_query->rows as $product_attribute_group) {
		$product_attribute_data = array();

		$product_attribute_query = $this->db->query("SELECT a.attribute_id, ad.name, pa.text FROM " . DB_PREFIX . "product_attribute pa LEFT JOIN " . DB_PREFIX . "attribute a ON (pa.attribute_id = a.attribute_id) LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) WHERE pa.product_id = '" . (int)$product_id . "' AND a.attribute_group_id = '" . (int)$product_attribute_group['attribute_group_id'] . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pa.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY a.sort_order, ad.name");

		foreach ($product_attribute_query->rows as $product_attribute) {
			$product_attribute_data[] = array(
				'attribute_id' => $product_attribute['attribute_id'],
				'name'         => $product_attribute['name'],
				'text'         => $product_attribute['text']		 	
				);
		}

		$product_attribute_group_data[] = array(
			'attribute_group_id' => $product_attribute_group['attribute_group_id'],
			'name'               => $product_attribute_group['name'],
			'attribute'          => $product_attribute_data
			);			
	}

	return $product_attribute_group_data;
}

public function getProductOptions($product_id) {
	$product_option_data = array();

	$product_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY o.sort_order");

	foreach ($product_option_query->rows as $product_option) {
		if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
			$product_option_value_data = array();

			$product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY ov.sort_order");

			foreach ($product_option_value_query->rows as $product_option_value) {
				$product_option_value_data[] = array(
					'product_option_value_id' => $product_option_value['product_option_value_id'],
					'option_value_id'         => $product_option_value['option_value_id'],
					'name'                    => $product_option_value['name'],
					'image'                   => $product_option_value['image'],
					'quantity'                => $product_option_value['quantity'],
					'subtract'                => $product_option_value['subtract'],
					'price'                   => $product_option_value['price'],
					'price_prefix'            => $product_option_value['price_prefix'],
					'weight'                  => $product_option_value['weight'],
					'weight_prefix'           => $product_option_value['weight_prefix']
					);
			}

			$product_option_data[] = array(
				'product_option_id' => $product_option['product_option_id'],
				'option_id'         => $product_option['option_id'],
				'name'              => $product_option['name'],
				'type'              => $product_option['type'],
				'option_value'      => $product_option_value_data,
				'required'          => $product_option['required']
				);
		} else {
			$product_option_data[] = array(
				'product_option_id' => $product_option['product_option_id'],
				'option_id'         => $product_option['option_id'],
				'name'              => $product_option['name'],
				'type'              => $product_option['type'],
				'option_value'      => $product_option['option_value'],
				'required'          => $product_option['required']
				);				
		}
	}

	return $product_option_data;
}

public function getProductDiscounts($product_id) {
	if ($this->customer->isLogged()) {
		$customer_group_id = $this->customer->getCustomerGroupId();
	} else {
		$customer_group_id = $this->config->get('config_customer_group_id');
	}
	
	$product = $this->getProduct($product_id);	

	$is_platinum = false;
		if(substr($product['model'],0,7)=='APL-001' || substr($product['model'],0,4)=='SRN-' || substr($product['model'],0,7)=='TAB-SRN'  )
		{
			$is_platinum = true;
		}
		if($is_platinum)
		{

		$customer_group_id = '1633'; // force assigning the customer group of platinum 1633
		}
	if($product['sale_price']==0.0000)
	{
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$customer_group_id . "' AND quantity > 1 AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity ASC, priority ASC, price ASC");
		$rows =  $query->rows;		
	}
	else
	{
		$rows = array();
		// $rows[] = array('quantity'=>1,'price'=>$product['sale_price']);
	}
	return $rows;
}

public function getProductImages($product_id) {
	$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC");

	return $query->rows;
}

public function getProductRelated($product_id) {
	$product_data = array();

	$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related pr LEFT JOIN " . DB_PREFIX . "product p ON (pr.related_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE pr.product_id = '" . (int)$product_id . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'");

	foreach ($query->rows as $result) { 
		$product_data[$result['related_id']] = $this->getProduct($result['related_id']);
	}

	return $product_data;
}

public function getProductTags($product_id) {
	$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_tag WHERE product_id = '" . (int)$product_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

	return $query->rows;
}

public function getProductLayoutId($product_id) {
	$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

	if ($query->num_rows) {
		return $query->row['layout_id'];
	} else {
		return  $this->config->get('config_layout_product');
	}
}

public function getCategories($product_id) {
	$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

	return $query->rows;
}	

public function getTotalProducts($data = array()) {
	$sql = "SELECT COUNT(DISTINCT p.product_id) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id)";

	if (!empty($data['filter_category_id'])) {
		$sql .= " LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)";			
	}

	if (!empty($data['filter_tag'])) {
		$sql .= " LEFT JOIN " . DB_PREFIX . "product_tag pt ON (p.product_id = pt.product_id)";			
	}

	$sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";

	if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
		$sql .= " AND (";
			
			if (!empty($data['filter_name'])) {
				$implode = array();
				
				$words = explode(' ', trim(preg_replace('/\s\s+/', ' ', $data['filter_name'])));
				
				foreach ($words as $word) {
					if (!empty($data['filter_description'])) {
						$implode[] = "LCASE(pd.name) LIKE '%" . $this->db->escape(utf8_strtolower($word)) . "%' OR LCASE(pd.description) LIKE '%" . $this->db->escape(utf8_strtolower($word)) . "%'";
					} else {
						$implode[] = "LCASE(pd.name) LIKE '%" . $this->db->escape(utf8_strtolower($word)) . "%'";
					}				
				}
				
				if ($implode) {
					$sql .= " " . implode(" OR ", $implode) . "";
				}
			}
			
			if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
				$sql .= " OR ";
			}
			
			if (!empty($data['filter_tag'])) {
				$implode = array();
				
				$words = explode(' ', trim(preg_replace('/\s\s+/', ' ', $data['filter_tag'])));
				
				foreach ($words as $word) {
					$implode[] = "LCASE(pt.tag) LIKE '%" . $this->db->escape(utf8_strtolower($word)) . "%'";
				}
				
				if ($implode) {
					$sql .= " " . implode(" OR ", $implode) . " AND pt.language_id = '" . (int)$this->config->get('config_language_id') . "'";
				}
			}
			
			$sql .= ")";
}

if (!empty($data['filter_category_id'])) {
	if (!empty($data['filter_sub_category'])) {
		$implode_data = array();

		$implode_data[] = "p2c.category_id = '" . (int)$data['filter_category_id'] . "'";

		$this->load->model('catalog/category');

		$categories = $this->model_catalog_category->getCategoriesByParentId($data['filter_category_id']);

		foreach ($categories as $category_id) {
			$implode_data[] = "p2c.category_id = '" . (int)$category_id . "'";
		}

		$sql .= " AND (" . implode(' OR ', $implode_data) . ")";			
	} else {
		$sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "'";
	}
}		

if (!empty($data['filter_manufacturer_id'])) {
	$sql .= " AND p.manufacturer_id = '" . (int)$data['filter_manufacturer_id'] . "'";
}

$query = $this->db->query($sql);

return $query->row['total'];
}

public function getTotalProductSpecials() {
	if ($this->customer->isLogged()) {
		$customer_group_id = $this->customer->getCustomerGroupId();
	} else {
		$customer_group_id = $this->config->get('config_customer_group_id');
	}		

	$query = $this->db->query("SELECT COUNT(DISTINCT ps.product_id) AS total FROM " . DB_PREFIX . "product_special ps LEFT JOIN " . DB_PREFIX . "product p ON (ps.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_to_store p2s ON (p.product_id = p2s.product_id) WHERE p.status = '1' AND p.date_available <= NOW() AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND ps.customer_group_id = '" . (int)$customer_group_id . "' AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))");

	if (isset($query->row['total'])) {
		return $query->row['total'];
	} else {
		return 0;	
	}
}	


public function getPromo($product_id,$data) {
	$query = $this->db->query("SELECT pt.promo_text, pt.promo_link, pt.image, pt.pimage FROM " . DB_PREFIX . "promo_tags pt, " . DB_PREFIX . "product p WHERE pt.promo_tags_id = '" . (int)$data . "' AND p.product_id = '" . (int)$product_id . "'");
	return $query->row;	
}
public function getProductClass($sku) {

	$query = $this->db->query("SELECT c.*, d.name as main_category FROM inv_device_product AS a, inv_device_class AS b, inv_classification AS c, inv_main_classification AS d WHERE a.device_product_id = b.device_product_id AND b.class_id = c.id AND c.main_class_id = d.id AND sku = '$sku'");

	if ($query->num_rows) {
		return $query->row;
	} else {
		return false;
	}
}

public function getProductMainClass($product_id) {

	$query = $this->db->query("SELECT ic.`name` FROM `inv_classification` ic INNER JOIN `oc_product` op ON (ic.`id` = op.`classification_id`) WHERE op.`product_id` = '". (int)$product_id ."'");
	return $query->row['name'];
}

public function getProductIDbySku($sku) {

	$query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE model = '". $sku ."'");
	return $query->row['product_id'];
}

public function getProductSteps($id)
{
	$query = $this->db->query("SELECT * FROM inv_product_repair_guide WHERE product_sku = '". $id ."'");
	return $query;
}

public function getProductQuestion($id)
{
	$query = $this->db->query("SELECT * FROM oc_product_question WHERE product_sku = '". $id ."'");
	return $query;
}

public function saveProductList($parameters)
{
	$this->db->query("INSERT INTO oc_customer_product_list SET name ='".$parameters['name']."',customer_id='".(int)$parameters['customer_id']."'");
}

public function getCustomerLists()
{
	$query = $this->db->query("SELECT * FROM oc_customer_product_list WHERE customer_id = '". (int)$this->customer->getId() ."'");
	return $query;
}

public function addProductTolist($parameters)
{
	$this->db->query("INSERT INTO oc_list_products SET product_id ='".$parameters['product_id']."',list_id='".(int)$parameters['list_id']."'");
}

public function getListProducts($id)
{
	$query = $this->db->query("SELECT * FROM oc_list_products WHERE list_id = '". (int)$id ."'");
	return $query;
}

public function getProductFromList($id)
{
	$query = $this->db->query("SELECT * FROM oc_list_products WHERE product_id = '". (int)$id ."'");
	return $query;
}

public function getTopSellingProducts($sku,$start=0,$total=4)
{
		$_sku = str_replace("%", "", $sku);
	
		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getCustomerGroupId();
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		$cache = md5(http_build_query($_sku));
		
		$product_data = $this->cache->get($_sku.'topproduct.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id .'.'.(int)$start.(int)$total. '.' . $cache);
		
		if(!$product_data)
		{
			$query = $this->db->query("SELECT id, product_sku, oc_product.image, oc_product.price,oc_product.sale_price, oc_product.product_id,oc_product_description.name, oc_product.quantity, count(product_sku) as order_d from inv_orders_items inner join oc_product on inv_orders_items.product_sku = oc_product.sku inner join oc_product_description on oc_product.product_id = oc_product_description.product_id where product_sku  like '".$sku."' and oc_product.status=1 and oc_product.visibility=1 GROUP by product_sku ORDER by order_d desc LIMIT ".(int)$start.",".(int)$total."");
			$product_data = $query->rows;
			$this->cache->set($_sku.'topproduct.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id.'.' .(int)$start.(int)$total. '.' . $cache, $product_data);
		}
	return $product_data;
}

public function getTopSellingProductsByProductIds($product_ids,$start=0,$total=5,$is_limit=true)
{
	
	
		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getCustomerGroupId();
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		$cache = md5(http_build_query($_sku));
		
		$product_data = $this->cache->get('topproduct.productids.'.$product_ids.'.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id .'.'.(int)$start.(int)$total. '.' . $cache);
		
		if(!$product_data)
		{
			if($product_ids)
			{



			$query = $this->db->query("SELECT id, product_sku, oc_product.image, oc_product.price,oc_product.sale_price, oc_product.product_id,oc_product_description.name, oc_product.quantity, count(product_sku) as order_d from inv_orders_items inner join oc_product on inv_orders_items.product_sku = oc_product.sku inner join oc_product_description on oc_product.product_id = oc_product_description.product_id where oc_product.product_id in (".$product_ids.")   and oc_product.status=1 and oc_product.visibility=1  ".($is_limit?" and inv_orders_items.dateofmodification BETWEEN CURDATE() - INTERVAL 60 DAY AND CURDATE()":'')." GROUP by product_sku ORDER by oc_product.show_on_top DESC, order_d DESC LIMIT ".(int)$start.",".(int)$total."");
			$product_data = $query->rows;
		}
		else
		{
			$product_data = array();
		}

			$this->cache->set('topproduct.productids.'.$product_ids.'.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id .'.'.(int)$start.(int)$total. '.' . $cache);
		}
		// print_r($product_data);exit;
	return $product_data;
}

public function getTopSellingProductsByQuery($que)
{
	$query = $this->db->query($que);
	return $query;
}

public function getTopSellingProductsByName($name,$start=0,$total=4,$not_like='',$not_like1='')
{
	$_temp = str_replace("%", "", $name);
	
		if ($this->customer->isLogged()) {
			$customer_group_id = $this->customer->getCustomerGroupId();
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

	$cache = md5(http_build_query($_temp));

	$product_data = $this->cache->get($_temp.'topproductbyname.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id.'.' .(int)$start.(int)$total.'.'.$not_like.'.'.$not_like1. '.' . $cache);
		
		if(!$product_data)
		{

			$query = $this->db->query("SELECT id, product_sku,oc_product.quantity,oc_product.sale_price, oc_product.image, oc_product.price, oc_product.product_id,oc_product_description.name, count(product_sku) as order_d from inv_orders_items inner join oc_product on inv_orders_items.product_sku = oc_product.sku inner join oc_product_description on oc_product.product_id = oc_product_description.product_id where oc_product_description.name like '".$name."' AND oc_product_description.name NOT like '".$not_like."' AND oc_product_description.name NOT like '".$not_like1."' and oc_product.status=1 and oc_product.visibility=1 GROUP by product_sku ORDER by order_d desc LIMIT ".(int)$start.",".(int)$total."");
			$product_data = $query->rows;
			$this->cache->set($_temp.'topproductbyname.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . (int)$customer_group_id .'.' .(int)$start.(int)$total.'.'.$not_like.'.'.$not_like1. '.' . $cache, $product_data);
		}
	return $product_data;
}
}
?>