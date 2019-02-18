<?php
use Elasticsearch\ClientBuilder;
require_once(DIR_APPLICATION . '../wx/vendor/autoload.php');

class ModelWxSearch extends Model {

	public function getProducts($data = array()) {
		if(version_compare(VERSION, '1.5.4', '<')) {
			$sql = "SELECT p.product_id, p.status, p.sort_order, p.sku, p.model, p.status, p.date_added, p.price, p.manufacturer_id, pd.name, pd.description FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = 1";
		}
		
		if(version_compare(VERSION, '1.5.4', '>=')) {
			$sql = "SELECT p.product_id, p.status, p.sort_order, p.sku, p.model, p.status, p.date_added, p.price, p.manufacturer_id, pd.name, pd.description, pd.tag FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.status = 1";
		}
		
		$sql .= " AND p.sku NOT LIKE 'LBB%'";
		
		if (isset($data['start']) && isset($data['limit'])) {
			$sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
		}
		
		$query = $this->db->query($sql);
		
		return $query->rows;
	}
	
	public function getManufacturerById($manu_id) {
		$sql = "SELECT name FROM " . DB_PREFIX . "manufacturer WHERE manufacturer_id = '" . (int)$manu_id . "'";
		$query = $this->db->query($sql);
		
		$manufacturer = '';
		foreach ($query->rows as $result) {
			$manufacturer = $result['name'];
		}
		return $manufacturer;
	}
	
	public function getTotalProducts($data = array()) {

		$client = ClientBuilder::create()->build();
		$params = array('index' => DB_DATABASE, 'type' => 'product');
		
		if (isset($data['filter_manufacturer_id']) && !empty($data['filter_manufacturer_id'])) {
			$params['body']['query']['match']['manufacturer_id'] = $data['filter_manufacturer_id'];
		}

		if (isset($data['filter_category_id']) && !empty($data['filter_category_id'])) {
			$params['body']['query']['match']['category_id'] = $data['filter_category_id'];
		}

		if (isset($data['filter_name']) && !empty($data['filter_name'])) {
			$params['body']['query']['match']['combined'] = $data['filter_name'];
		}
		
		$result = $client->search($params);
		
		return $result['hits']['total'];
	}	
	
	public function getCardinality($data = array()) {
	
		$client = ClientBuilder::create()->build();
		$params = array('index' => DB_DATABASE, 'type' => 'product');
		$params['body']['aggs']['device_manufacturer']['terms']['field'] = 'device_manufacturer_id';
		$params['body']['aggs']['device_device_id']['terms']['field']    = 'device_device_id';
		$params['body']['aggs']['device_device_id']['terms']['size']     = '100';
		$params['body']['aggs']['device_model']['terms']['field']        = 'device_model';
		$params['body']['aggs']['device_model']['terms']['size']         = '100';
		
		if (isset($data['filter_manufacturer_id']) && !empty($data['filter_manufacturer_id'])) {
			$params['body']['query']['match']['manufacturer_id'] = $data['filter_manufacturer_id'];
		}

		if (isset($data['filter_device_id']) && !empty($data['filter_device_id'])) {
			$params['body']['query']['bool']['filter']['term']['device_device_id'] = $data['filter_device_id'];
		}
		
		if (isset($data['filter_name']) && !empty($data['filter_name'])) {
			$data['filter_name'] = trim($data['filter_name']);
			if (strpos($data['filter_name'], " ") !== false) {
				$filter_name = explode(" ", $data['filter_name']);
				$name_filters = array();
				foreach ($filter_name as $fn) {
					$name_filters[] = array('match' => array('combined' => $fn));
				}
				$params['body']['query']['bool']['must'] = $name_filters;
			} else {
				$params['body']['query']['bool']['must']['match']['combined']	= $data['filter_name'];
			}
		}
		
		if (isset($data['start'])) {
			$params['body']['from'] = $data['start'];
		}
		
		if (isset($data['limit'])) {
			$params['body']['size'] = $data['limit'];
		} else {
			$params['body']['size'] = 1000;
		}		
		$result = $client->search($params);
		
		$product_count = $result['hits']['total'];
		$products = array();
		foreach ($result['hits']['hits'] as $product) {			
			$p = $this->model_catalog_product->getProduct($product['_id']);
			if ($p) {
				$products[] = $p;
			} else {
				$product_count--; 
			}
		}		
		
		$phone_manufacturers = array();
		foreach ($result['aggregations']['device_manufacturer']['buckets'] as $agg) $phone_manufacturers[] = $agg['key'];
		if (!empty($phone_manufacturers)) {
			$phone_manufacturers = $this->db->query("SELECT * FROM `inv_manufacturer` WHERE manufacturer_id IN (" . implode($phone_manufacturers) . ")")->rows;
		}

		$phone_models = array();
		foreach ($result['aggregations']['device_device_id']['buckets'] as $agg) {
			$phone_models[] = $agg['key'];
		}
		
		if (!empty($phone_models)) {
			$phone_models = $this->db->query("SELECT * FROM inv_model_mt WHERE model_id IN (" . implode($phone_models, ",") . ") ORDER BY device")->rows;
		}
		
		$phone_parts = array();
		foreach ($result['aggregations']['device_device_id']['buckets'] as $agg) {
			$phone_parts[] = $agg['key'];
		}
		
		if (!empty($phone_parts)) {
			$phone_parts = $this->db->query("SELECT * FROM inv_model_dt WHERE model_id IN (" . implode($phone_parts, ",") . ")")->rows;
		}
		
		return array(
			'count'         => $product_count,
			'manufacturers' => $phone_manufacturers,
			'models'        => $phone_models,
			'parts'         => $phone_parts,
			'products'      => $products,
			'aggregations'  => $result['aggregations'],
		);
	
	}
	
	public function getSearchProducts($data = array()) {
	
		$this->load->model('catalog/product');
		$client = ClientBuilder::create()->build();
		$params = array('index' => DB_DATABASE, 'type' => 'product');

		
		// PRODUCT SORT
		$sort_order = (isset($data['order']) && ($data['order'] == 'DESC')) ? 'desc' : 'asc';
		if (isset($data['sort']) && !empty($data['sort'])) {
			if ($data['sort'] == 'pd.name') {
				$params['body']['sort'] = array(array('nsort' => $sort_order));
			} else if ($data['sort'] == 'p.model') {
				$params['body']['sort'] = array(array('model' => $sort_order));
			} else if ($data['sort'] == 'p.price') {
				$params['body']['sort'] = array(array('price' => $sort_order));
			} else if ($data['sort'] == 'p.date_added') {
				$params['body']['sort'] = array(array('date_added' => $sort_order));
			} else if ($data['sort'] == 'p.status') {
				$params['body']['sort'] = array(array('status' => $sort_order));
			} else {
				$params['body']['sort'] = array(array('sort_order' => $sort_order, 'nsort' => $sort_order));
			}
		} else {
			$params['body']['sort'] = array(array('sort_order' => $sort_order, 'nsort' => $sort_order));
		}
		
		
		// PRODUCT FILTERS
		if (isset($data['filter_manufacturer_id']) && !empty($data['filter_manufacturer_id'])) {
			$params['body']['query']['match']['manufacturer_id'] = $data['filter_manufacturer_id'];
		}

		//if (isset($data['filter_device_id']) && !empty($data['filter_device_id'])) {
		//	$params['body']['query']['match']['manufacturer_id'] = $data['filter_manufacturer_id'];
		//}
		
		if (isset($data['filter_category_id']) && !empty($data['filter_category_id'])) {
			$params['body']['query']['match']['category_id'] = $data['filter_category_id'];
		}
		
		if (isset($data['filter_name']) && !empty($data['filter_name'])) {
			$params['body']['query']['match']['combined'] = $data['filter_name'];
		}
		
		//$params['body']['query']['bool']['must']['match'] = array("status" => true);
		
		if (isset($data['start'])) {
			$params['body']['from'] = $data['start'];
		}
		
		if (isset($data['limit'])) {
			$params['body']['size'] = $data['limit'];
		} else {
			$params['body']['size'] = 1000;
		}
		
		$result = $client->search($params);
		var_dump($result);
		
		$products = array();
		foreach ($result['hits']['hits'] as $product) {
			$products[] = $this->model_catalog_product->getProduct($product['_id']);
		}
		return $products;
	}
}
?>