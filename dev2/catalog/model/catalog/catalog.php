<?php
class ModelCatalogCatalog extends Model {
	
	public function getSettings ($setting_name) {
		
		if ($setting_name) {

			$query = $this->db->query("SELECT setting_value FROM `catalog_setting` WHERE `setting_name` = '$setting_name'");

			return $query->row['setting_value'];

		}

		return false;
	}

	public function getManufacturer($sku)
	{
		$query = $this->db->query("SELECT b.manufacturer_id FROM inv_device_manufacturer b, inv_device_product a where a.device_product_id=b.device_product_id and a.sku='".$this->db->escape($sku)."'");
		return $query->row['manufacturer_id'];
	}
	public function getManufacturerName($manufacturer_id)
	{
		$query = $this->db->query("SELECT name FROM inv_manufacturer where manufacturer_id='".(int)$manufacturer_id."'");
		return $query->row['name'];
	}

	public function getShare ($share_id) {
		
		if ($share_id) {

			$query = $this->db->query("SELECT data FROM `imp_share` WHERE `id` = '". (int) $share_id ."'");

			return $query->row['data'];

		}

		return false;
	}

	public function loadManufacturers ($filter = array()) {

		$id = 'manufacturer_id';
		$name = 'name';
		$image = 'image';
		$description = 'description';

		$cache = md5(http_build_query($filter));
		
		$results = $this->cache->get('loadmanufacturers.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache);
		if(!$results)
		{
		$query = "SELECT * FROM `inv_manufacturer`";

		$where = array();
		foreach ($filter['where'] as $prop) {
			$where[] = $prop['column'] . ' ' . $prop['operator'] . ' "' . $prop['value'] . '"';
		}
		
		if ($where) {
			$query .= ' WHERE ' . implode(' AND ', $where);
		}

		if ($filter['order_by']) {
			$order_by = $filter['order_by'];
			$order_type = ($filter['order_type']) ? $filter['order_type']: 'ASC';
			$query .= " ORDER BY $order_by $order_type";
		}

		if ($filter['limit']) {
			$page = (!$filter['page'])? 1: (int) $filter['page'];
			$limit = (int) $filter['limit'];
			$start = ($page - 1) * $limit;
			$end = $page * $limit;

			$query .= " LIMIT $start, $end";
		}

		if ($filter['record'] == 1) {
			$row = $this->db->query($query)->row;
			$results = array(
				'id' => $row[$id],
				'name' => $row[$name],
				'image' => $row[$image],
				'description' => $row[$description],
				);
		} else {
			$rows = $this->db->query($query)->rows;
			
			$results = array();
			foreach ($rows as $row) {
				$results[] = array(
					'id' => $row[$id],
					'name' => $row[$name],
					'image' => $row[$image],
					'description' => $row[$description],
					);
			}
		}
		$this->cache->set('loadmanufacturers.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache, $results);
	}

		return $results;
	}

	public function loadClassification ($filter) {

		$id = 'id';
		$name = 'main_class';

		$query = "SELECT inv_classification.*, inv_main_classification.name as main_name FROM `inv_classification`, `inv_main_classification` ";

		$where = array();
		$where[] = 'inv_classification.main_class_id = inv_main_classification.id';
		foreach ($filter['where'] as $prop) {
			$where[] = $prop['column'] . ' ' . $prop['operator'] . ' "' . $prop['value'] . '"';
		}

		if ($where) {
			$query .= ' WHERE ' . implode(' AND ', $where);
		}

		$query .= " GROUP BY inv_classification.id";

		if ($filter['order_by']) {
			$order_by = $filter['order_by'];
			$order_type = ($filter['order_type']) ? $filter['order_type']: 'ASC';
			$query .= " ORDER BY $order_by $order_type";
		}

		if ($filter['limit']) {
			$page = (!$filter['page'])? 1: (int) $filter['page'];
			$limit = (int) $filter['limit'];
			$start = ($page - 1) * $limit;
			$end = $page * $limit;

			$query .= " LIMIT $start, $end";
		}


		if ($filter['record'] == 1) {
			$row = $this->db->query($query)->row;
			$results = array(
				'id' => $row[$id],
				'name' => $row[$name],
				);
		} else {
			$rows = $this->db->query($query)->rows;

			$results = array();

			foreach ($rows as $row) {
				$results[] = array(
					'id' => $row[$id],
					'name' => $row[$name],
					'main_name' => $row['main_name'],
					'main_class_id' => $row['main_class_id'],
					);
			}
		}

		return $results;
	}

	public function loadMainClassification ($filter = array()) {

		$id = 'id';
		$name = 'main_class';

		$query = "SELECT * FROM `inv_main_classification`";
		
		$where = array();
		foreach ($filter['where'] as $prop) {
			$where[] = $prop['column'] . ' ' . $prop['operator'] . ' "' . $prop['value'] . '"';
		}

		if ($where) {
			$query .= ' WHERE ' . implode(' AND ', $where);
		}

		if ($filter['order_by']) {
			$order_by = $filter['order_by'];
			$order_type = ($filter['order_type']) ? $filter['order_type']: 'ASC';
			$query .= " ORDER BY $order_by $order_type";
		}

		if ($filter['limit']) {
			$page = (!$filter['page'])? 1: (int) $filter['page'];
			$limit = (int) $filter['limit'];
			$start = ($page - 1) * $limit;
			$end = $page * $limit;

			$query .= " LIMIT $start, $end";
		}


		if ($filter['record'] == 1) {
			$row = $this->db->query($query)->row;
			$results = array(
				'id' => $row[$id],
				'name' => $row[$name],
				);
		} else {
			$rows = $this->db->query($query)->rows;

			$results = array();

			foreach ($rows as $row) {
				$results[] = array(
					'id' => $row[$id],
					'name' => $row[$name],
					);
			}
		}

		return $results;
	}

	public function loadModels ($filter) {
		
		
		$id = 'model_id';
		$name = 'device';
		$image = 'image';

		$cache = md5(http_build_query($filter));
		
		$results = $this->cache->get('loadmodels.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache);

		if(!$results)
		{
			$manufacturer_query='';
			if((bool)$filter['append_manufacturer']==true)
			{
				$manufacturer_query = ",(SELECT name from inv_manufacturer where inv_model_mt.manufacturer_id=inv_manufacturer.manufacturer_id) as manufacturer";
			}

		$query = "SELECT * $manufacturer_query FROM `inv_model_mt`";
		

		$where = array();
		foreach ($filter['where'] as $prop) {
			$where[] = $prop['column'] . ' ' . $prop['operator'] . ' "' . $prop['value'] . '"';
		}

		if ($where) {
			$query .= ' WHERE ' . implode(' AND ', $where);
		}

		if ($filter['order_by']) {
			$order_by = $filter['order_by'];
			$order_type = ($filter['order_type']) ? $filter['order_type']: 'ASC';
			$query .= " ORDER BY $order_by $order_type";
		}

		if ($filter['limit']) {
			$page = (!$filter['page'])? 1: (int) $filter['page'];
			$limit = (int) $filter['limit'];
			$start = ($page - 1) * $limit;
			$end = $page * $limit;

			$query .= " LIMIT $start, $end";
		}

		if ($filter['record'] == 1) {
			$row = $this->db->query($query)->row;
			$results = array(
				'id' => $row[$id],
				'name' => ($manufacturer_query?$row['manufacturer'].' ':'').$row[$name],
				'image' => $row[$image],
				);
		} else {
			$rows = $this->db->query($query)->rows; 

			$results = array();

			foreach ($rows as $row) {
				$results[] = array(
					'id' => $row[$id],
					'name' => ($manufacturer_query?$row['manufacturer'].' ':'').$row[$name],
					'image' => $row[$image],
					);
			}
		}

		$this->cache->set('loadmodels.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache, $results);
	}


		return $results;
	}


	public function loadSubModels ($device_id) {
		$query = "SELECT a.*, b.`carrier_id`, b.`id` FROM `inv_model_dt` a, `inv_model_carrier` b WHERE a.`sub_model_id` = b.`sub_model_id` AND model_id = '". $device_id ."' ORDER BY `sub_model`";
		$subModelsRecord = $this->db->query($query)->rows; 
		// echo $query;exit;

				// Aranging Sub Models
		$results = array();
		foreach ($subModelsRecord as $sMRow) {
			$carrier = '';
			if ($sMRow['carrier_id']) {
				$carrier = $this->db->query("SELECT name FROM `inv_carrier` WHERE `id` = '". $sMRow['carrier_id'] ."'")->row['name'];
			}
					// Array Sub Models
			$results[] = array(
				'id' => $sMRow['id'],
				'name' => $sMRow['sub_model'] . (($carrier)?' (' . $carrier . ')':''),
				);

		}

		return $results;
	}

	public function loadClassAttr ( $filter = array() ) {
		$query = "SELECT f.`id`, f.`name`, g.`name` main_name FROM `inv_device_model` `a`, `inv_device_device` b, `inv_device_manufacturer` `c`, `inv_device_class` d, `inv_device_attrib` e, `inv_attr` f, `inv_attribute_group` `g` WHERE a.`device_device_id` = b.`device_device_id` AND b.`device_manufacturer_id` = c.`device_manufacturer_id` AND c.`class_id` = d.`device_class_id` AND a.`device_model_id` = e.`device_model_id` AND e.`attrib_id` = f.`id` AND f.`attribute_group_id` = g.`id`";

		$main_class_id = (int)$filter['main_class_id'];
		if ($main_class_id) {
			$query .= " AND d.`class_id` IN (". $this->db->query('SELECT GROUP_CONCAT(id) as ids FROM `inv_classification` WHERE `main_class_id` = "'. $main_class_id .'"')->row['ids'] .")";
		}

		$manufacturer_id = (int)$filter['manufacturer_id'];
		if ($manufacturer_id) {
			$query .= " AND c.`manufacturer_id` = '$manufacturer_id'";
		}

		$device_id = (int)$filter['device_id'];
		if ($device_id) { 
			$query .= " AND b.`device_id` = '$device_id' ";
		}

		$model_id = trim($filter['model_id'], ',');
		if ($model_id) {
			$model_id = str_replace(',', "','", $model_id);
			$query .= " AND a.`model_id` in ('$model_id') ";
		}

		$class_id = (int)$filter['class_id'];
		if ($class_id) {
			$query .= " AND d.`class_id` = '$class_id'";
		}

		$productIds = $this->filterProducts($filter, true);
		if ($productIds) {
			$query .= " AND d.`device_product_id` IN ('". implode("', '", $productIds) ."')";
		}
		
		$query .= ' GROUP BY e.`attrib_id` ORDER BY g.`id`';

		$modelClassAttr = $this->db->query($query)->rows;

		return $modelClassAttr;

	}


	public function filterProducts( $filter = array() , $attr = false,$page = 1) {
		// print_r($filter);exit;
		$end_limit =40;
		$start_limit = ((int)$page - 1)*$end_limit;

		$cache = md5(http_build_query($filter));
		// $cache = md5(http_build_query($attr));
		
		$productIds = $this->cache->get('filterproducts.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache);
		
		if(!$productIds)
		{

		$query = "SELECT  d.* FROM `inv_device_model` a, `inv_device_device` b, `inv_device_manufacturer` c, `inv_device_class` d, `inv_device_attrib` e";

			if($filter['is_blowout'])
			{
				$query.=",`inv_device_product` f ";
			}


		$query.=" WHERE a.`device_device_id` = b.`device_device_id` AND b.`device_manufacturer_id` = c.`device_manufacturer_id` AND c.`class_id` = d.`device_class_id` AND a.`device_model_id` = e.`device_model_id` ";

			if($filter['is_blowout'])
			{
				$query.="AND f.`device_product_id`=c.`device_product_id`";
			}

		$main_class_id = (int)$filter['main_class_id'];
		if ($main_class_id && !$class_id) {
			$query .= " AND d.`class_id` IN (". $this->db->query('SELECT GROUP_CONCAT(id) as ids FROM `inv_classification` WHERE `main_class_id` = "'. $main_class_id .'"')->row['ids'] .")";
		}

		
	


		$manufacturer_id = (int)$filter['manufacturer_id'];
		if ($manufacturer_id) {
			$query .= " AND c.`manufacturer_id` = '$manufacturer_id'";
		}
	

		$device_id = (int)$filter['device_id'];
		if ($device_id) { 
			$query .= " AND b.`device_id` = '$device_id' ";
		}

		if($filter['is_blowout'])
		{
			$query.=" AND f.`is_blowout`=1 ";
		}

		$model_id = trim($filter['model_id'], ',');
		if ($model_id) {
			$model_id = str_replace(',', "','", $model_id);
			$query .= " AND a.`model_id` in ('$model_id') ";
		}

		if ($filter['manufacturers']) {

			// foreach ($filter['manufacturers'] as $manu) {
				$nQuery = '';
				if ($filter['manufacturers']) {
					$manuf = implode('\',\'', $filter['manufacturers']);
					$query .= " AND c.`manufacturer_id` IN ('$manuf')";
				}
				$sub_device_id = $filter['sub_device_id']['c'.$manu];
				if ($sub_device_id) { 
					// $sub_device_id = implode('\',\'', $sub_device_id);
					// $query .= " AND e.`device_model_id` IN ('$sub_device_id') ";
				}
				// $nQuery .= ' GROUP BY d.`device_product_id`';
				// $products = $this->db->query($query . $nQuery)->rows;
				// $productIdsreturn = $this->simplifyIds($products, 'device_product_id');
				// foreach ($productIdsreturn as $productid) {
				// 	$productIds[] = $productid;
				// }
			// }
		}


		$productIds = array();

		if ($filter['class_id']) {


			foreach ($filter['class_id'] as $class_id) {
				$nQuery = '';
				if ($class_id) {
					$nQuery .= " AND d.`class_id` = '$class_id'";
				}
				$attrib_id = $filter['attrib_id']['c'.$class_id];
				if ($attrib_id) { 
					$attrib_id = implode('\',\'', $attrib_id);
					$nQuery .= " AND e.`attrib_id` IN ('$attrib_id') ";
				}
				$nQuery .= ' GROUP BY d.`device_product_id` ';
				// echo $query. $nQuery;exit;
				$products = $this->db->query($query . $nQuery)->rows;
				$productIdsreturn = $this->simplifyIds($products, 'device_product_id');
				foreach ($productIdsreturn as $productid) {
					$productIds[] = $productid;
				}
			}
		} else {
			$query .= ' GROUP BY d.`device_product_id` ';
			$products = $this->db->query($query)->rows;
			$productIds = $this->simplifyIds($products, 'device_product_id');
		}
		// echo $query.$nQuery;;exit;
		// echo $page;exit;
		// echo "<pre>";
		// print_r($productIds);exit;
		$this->cache->set('filterproducts.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache, ($productIds));
	}
		if ($attr) {
			return $this->simplifyIds($this->loadProducts($productIds, '', $attr), 'device_product_id');
		}		
		return $productIds;

	}

	private function loadProducts ($productIds, $page, $attr = false) {
		
		$nRows = 50;
		$start = ($page - 1) * $nRows;
		$end = $page * $nRows;
		$products = array();
		if ($attr) {
			return $this->db->query("SELECT a.`device_product_id` FROM `inv_device_product` `a`, `oc_product` `b`, `oc_product_description` `c`, `inv_device_class` `d`, `inv_classification` `e` WHERE a.`sku` = b.`model` AND b.`product_id` = c.`product_id` AND a.`device_product_id` = d.`device_product_id` AND d.`class_id` = e.`id` AND b.`is_main_sku` = '1' AND b.`status` = '1' AND a.`device_product_id` IN ('". implode("', '", $productIds) ."') ORDER BY e.`sort` ASC")->rows;
		}
		if ($productIds) {
			$products = $this->db->query("SELECT b.`product_id`, b.`image`, b.`price`, b.`model`, c.`name`, b.`quantity` FROM `inv_device_product` `a`, `oc_product` `b`, `oc_product_description` `c`, `inv_device_class` `d`, `inv_classification` `e` WHERE a.`sku` = b.`model` AND b.`product_id` = c.`product_id` AND a.`device_product_id` = d.`device_product_id` AND d.`class_id` = e.`id` AND b.`is_main_sku` = '1' AND b.`status` = '1' AND a.`device_product_id` IN ('". implode("', '", $productIds) ."') ORDER BY e.`sort` ASC LIMIT $start,$end")->rows;
			$count = $this->db->query("SELECT COUNT(b.`product_id`) ids FROM `inv_device_product` a, `oc_product` b, `oc_product_description` c WHERE a.`sku` = b.`model` AND b.`product_id` = c.`product_id` AND a.`device_product_id` IN ('". implode("', '", $productIds) ."') and b.status=1")->row['ids'];
		}

		return array('products' => $products, 'total' => $count);
	}
	public function getProductClass($device_id)
	{


	}

	public function loadModelClasses ( $filter = array() ) {
		$query = "SELECT d.`class_id` id, f.`main_class` name, g.`name` as main_name, g.`id` as main_class_id FROM `inv_device_model` a, `inv_device_device` b, `inv_device_manufacturer` c, `inv_device_class` d, `inv_device_attrib` e, `inv_classification` f, `inv_main_classification` g,inv_device_product h WHERE c.device_product_id=h.device_product_id and a.`device_device_id` = b.`device_device_id` AND b.`device_manufacturer_id` = c.`device_manufacturer_id` AND c.`class_id` = d.`device_class_id` AND a.`device_model_id` = e.`device_model_id` AND d.`class_id` = f.`id` AND f.`main_class_id` = g.`id`";
		

		$device_product_id = (int)$filter['device_product_id'];
		if ($device_product_id) {
			$query .= " AND h.`device_product_id` ='".$device_product_id."'";
		}

		$device_id = (int)$filter['device_id'];
		if ($device_id) { 
			$query .= " AND b.`device_id` = '$device_id' ";
		}

		$model_id = (int)$filter['model_id'];
		if ($model_id) { 
			$query .= " AND a.`model_id` = '$model_id' ";
		}

		$main_class_id = (int)$filter['main_class_id'];
		if ($main_class_id) {
			$query .= " AND d.`class_id` IN (". $this->db->query('SELECT GROUP_CONCAT(id) as ids FROM `inv_classification` WHERE `main_class_id` = "'. $main_class_id .'"')->row['ids'] .")";
		}

		$class_name = $filter['class_name'];
		if ($class_name) {
			$query .= " AND (LCASE(g.`name`) = LCASE('Replacement Parts') OR LCASE(g.`name`) = LCASE('Accessories') OR LCASE(g.`name`) = LCASE('Refurbishing') OR LCASE(g.`name`) = LCASE('Repair Tools') OR LCASE(g.`name`) = LCASE('Screen Protectors'))";
			// $query .= " AND (LCASE(g.`name`) = LCASE('Replacement Parts') OR LCASE(g.`name`) = LCASE('Screen Protectors') OR LCASE(g.`name`) = LCASE('Accessories') OR LCASE(g.`name`) = LCASE('Repair Tools'))";
			//$query .= " AND LCASE(g.`name`) = LCASE('Screen Protectors')";
			//$query .= " AND LCASE(g.`name`) = LCASE('accessories')";
			//$query .= " AND LCASE(g.`name`) = LCASE('Repair Tools')";
			//$query .= " AND LCASE(g.`name`) = LCASE('Replacement Parts')";
		}
		


		$query .= ' GROUP BY d.`class_id` ORDER BY g.`sort`,f.`main_class` ASC';
		
		// echo $query;exit;
		$modelClasses = array();
		if ($model_id || $device_id) {
			$modelClasses = $this->db->query($query)->rows;
		}
		if($device_product_id)
		{
			$modelClasses = $this->db->query($query)->row;
		}

		return $modelClasses;

	}

	public function loadModelClassesSingle ( $filter = array() ) {
		$query = "SELECT d.`class_id` id, f.`main_class` name, g.`name` as main_name, g.`id` as main_class_id FROM `inv_device_model` a, `inv_device_device` b, `inv_device_manufacturer` c, `inv_device_class` d, `inv_device_attrib` e, `inv_classification` f, `inv_main_classification` g WHERE a.`device_device_id` = b.`device_device_id` AND b.`device_manufacturer_id` = c.`device_manufacturer_id` AND c.`class_id` = d.`device_class_id` AND a.`device_model_id` = e.`device_model_id` AND d.`class_id` = f.`id` AND f.`main_class_id` = g.`id`";
		
		$device_id = (int)$filter['device_id'];
		if ($device_id) { 
			$query .= " AND b.`device_id` = '$device_id' ";
		}

		$model_id = (int)$filter['model_id'];
		if ($model_id) { 
			$query .= " AND a.`model_id` = '$model_id' ";
		}
		$i = 0;
		if($filter['class_id'])
		{
		$query .=" AND ( ";	
		foreach ($filter['class_id'] as $class_id) {
			if ($i == 0) {
				$query .= " d.`class_id` = '$class_id'";
				$i = $i+1;
			} else {
				$query .=" OR ";
				$query .= " d.`class_id` = '$class_id'";
			}
		}
		$query .=" ) ";
	}

		/*$main_class_id = (int)$filter['main_class_id'];
		if ($main_class_id) {
			$query .= " AND d.`class_id` IN (". $this->db->query('SELECT GROUP_CONCAT(id) as ids FROM `inv_classification` WHERE `main_class_id` = "'. $main_class_id .'"')->row['ids'] .")";
		}*/

		/*$class_name = $filter['class_name'];
		if ($class_name) {
			$query .= " AND LCASE(g.`name`) = LCASE('$class_name')";
		}*/

		$query .= ' GROUP BY d.`class_id` ORDER BY g.`sort` ASC';
		// print_r($query);exit;

		$modelClasses = array();
		if ($model_id || $device_id) {
			$modelClasses = $this->db->query($query)->rows;
		}

		return $modelClasses;

	}

	public function getOCProductsIds ($productIds,$status=0,$is_blowout=0) {
		
		$query = "SELECT a.`product_id` FROM `oc_product` a, `inv_device_product` b WHERE a.`model` = b.`sku` AND b.`device_product_id` IN ('". implode("', '", $productIds) ."') ".($status==1?' AND a.status=1 AND a.visibility=1':' ').($is_blowout==1?' AND b.is_blowout=1 AND a.quantity>0':' ');
		$products = $this->db->query($query)->rows;
		$productIds = $this->simplifyIds($products, 'product_id');

		return $productIds;

	}
	public function getOCProductsIdsOfBlowout()
	{
		$query ="SELECT DISTINCT product_id FROM oc_product WHERE is_blowout=1 AND quantity>0 AND status=1 AND visibility=1 ";
		$products = $this->db->query($query)->rows;
		$productIds = $this->simplifyIds($products, 'product_id');

		return $productIds;
	}
	public function getSortedProductsByIds($product_ids)
	{
		// $productIds = '';
		$cache = md5(http_build_query($product_ids));
		
		$productIds = $this->cache->get('sortedproduct.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache);
		
		if($product_ids)
		{
			if(!$productIds)
			{
		$query = "SELECT a.product_id,(select sum(quantity) from oc_order_product b ,oc_order c WHERE a.product_id=b.product_id and b.order_id=c.order_id and c.date_added BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE()) as qty from oc_product a where a.product_id in(".implode(",", $product_ids).") order by a.show_on_top DESC, qty DESC";
		// echo $query;exit;
		$products = $this->db->query($query)->rows;
		$productIds = $this->simplifyIds($products, 'product_id');
		$this->cache->set('sortedproduct.' . (int)$this->config->get('config_language_id') . '.' . (int)$this->config->get('config_store_id') . '.' . $cache, $productIds);
		}
		}

		return $productIds;
	}

	public function getProductAttr( $sku ) {

		$query = "SELECT f.`id`, f.`name`, g.`name` main_name FROM `inv_device_model` `a`, `inv_device_device` b, `inv_device_manufacturer` `c`, `inv_device_class` d, `inv_device_attrib` e, `inv_attr` f, `inv_attribute_group` `g`, `inv_device_product` `h` WHERE a.`device_device_id` = b.`device_device_id` AND b.`device_manufacturer_id` = c.`device_manufacturer_id` AND c.`class_id` = d.`device_class_id` AND a.`device_model_id` = e.`device_model_id` AND e.`attrib_id` = f.`id` AND f.`attribute_group_id` = g.`id` AND d.`device_product_id` = h.`device_product_id`";

		$query .= " AND h.`sku` = '$sku'";
		
		$query .= ' GROUP BY e.`attrib_id` ORDER BY g.`id`';

		
		$modelClassAttr = $this->db->query($query)->rows;

		return $modelClassAttr;

	}

	public function getProductInfoManu($id) {

		$query = "SELECT h.`name`, i.`device` FROM `inv_device_device` b, `inv_device_manufacturer` c, `inv_device_class` d, `inv_device_product` f, `oc_product` g, `inv_manufacturer` h, `inv_model_mt` i WHERE b.`device_manufacturer_id` = c.`device_manufacturer_id` AND c.`class_id` = d.`device_class_id` AND d.`device_product_id` = f.`device_product_id` AND f.`sku` = g.`sku` AND c.`manufacturer_id` = h.`manufacturer_id` AND b.`device_id` = i.`model_id` AND g.`product_id` = '". (int) $id ."' limit 10";

		
		$rows = $this->db->query($query)->rows;		
		$string = '';
		$array= array();
		foreach ($rows as $k => $row) {
			// if ($row['name'] != $rows[($k - 1)]['name'] || $k == 0) {
			// 	$string  .= $row['name'] . ' ';
			// }
		//	$string .= $row['name'] . ' ';
		//	$string .= '>'. ' ';
		//	$string .= $row['device'];
			// if ($row['name'] != $rows[($k + 1)]['name'] && ($k + 1) != count($rows)) {
			// 	$string .= '&gt; ';
			// }
			$array[]= $row['name'] .' > '. $row['device'];
		  // $array[]=$string;
		}

		//print_r($array);
		//exit;
		return $array;


	}
	public function getDeviceID($sku)
	{
		$query ="SELECT a.device_product_id FROM inv_device_product a, oc_product b where a.sku=b.model AND a.sku='".$this->db->escape($sku)."'";
		$row = $this->db->query($query)->row;	
		return $row['device_product_id'];
	}
	public function filterProductsByDeviceIds( $filter = array() , $attr = false) {
		
		$query = "SELECT  d.* FROM `inv_device_model` a, `inv_device_device` b, `inv_device_manufacturer` c, `inv_device_class` d, `inv_device_attrib` e WHERE a.`device_device_id` = b.`device_device_id` AND b.`device_manufacturer_id` = c.`device_manufacturer_id` AND c.`class_id` = d.`device_class_id` AND a.`device_model_id` = e.`device_model_id`";

		$main_class_id = (int)$filter['main_class_id'];
		if ($main_class_id && !$class_id) {
			$query .= " AND d.`class_id` IN (". $this->db->query('SELECT GROUP_CONCAT(id) as ids FROM `inv_classification` WHERE `main_class_id` = "'. $main_class_id .'"')->row['ids'] .")";
		}

		$manufacturer_id = (int)$filter['manufacturer_id'];
		if ($manufacturer_id) {
			$query .= " AND c.`manufacturer_id` = '$manufacturer_id'";
		}

		$device_id = (int)$filter['device_id'];
		if ($device_id) { 
			$query .= " AND b.`device_id` = '$device_id' ";
		}

		$model_id = trim($filter['model_id'], ',');
		if ($model_id) {
			$model_id = str_replace(',', "','", $model_id);
			$query .= " AND a.`model_id` in ('$model_id') ";
		}

		$device_product_ids = trim($filter['device_product_ids'], ',');
		if ($device_product_ids) {
			$device_product_ids = str_replace(',', "','", $device_product_ids);
			$query .= " AND d.`device_product_id` in ('$device_product_ids') ";
		}

		$productIds = array();

		if ($filter['class_id']) {

			foreach ($filter['class_id'] as $class_id) {
				$nQuery = '';
				if ($class_id) {
					$nQuery .= " AND d.`class_id` = '$class_id'";
				}
				$attrib_id = $filter['attrib_id']['c'.$class_id];
				if ($attrib_id) { 
					$attrib_id = implode('\',\'', $attrib_id);
					$nQuery .= " AND e.`attrib_id` IN ('$attrib_id') ";
				}
				$nQuery .= ' GROUP BY d.`device_product_id`';
				
				$products = $this->db->query($query . $nQuery)->rows;
				$productIdsreturn = $this->simplifyIds($products, 'device_product_id');
				foreach ($productIdsreturn as $productid) {
					$productIds[] = $productid;
				}
			}
		} else {
			$query .= ' GROUP BY d.`device_product_id`';
			$products = $this->db->query($query)->rows;
			$productIds = $this->simplifyIds($products, 'device_product_id');
		}
		// echo $query;exit;
		if ($attr) {
			return $this->simplifyIds($this->loadProducts($productIds, '', $attr), 'device_product_id');
		}		
		return $productIds;

	}

	private function simplifyIds ($array, $key)	{
		$results = array();
		foreach ($array as $row) {
			$results[] = $row[$key];
		}
		// print_r($results);exit;
		return $results;
	}

}
?>