<?php

class load_catalog extends Database {
	
	public function loadSubClass ($main_id = 0) {
		$query = "SELECT * FROM inv_classification WHERE status='1' ";
		if ((int)$main_id) {
			$query .= " AND main_class_id = '". (int) $main_id ."'";
		}
		$rows = $this->func_query($query);
		return $rows;
	}

	public function userGroup () {
		$query = "SELECT b.`customer_group_id` id, b.`name` FROM `oc_customer_group` a, `oc_customer_group_description` b WHERE a.`customer_group_id` = b.`customer_group_id` and a.customer_group_id in(8,10,6,1631,1632,1633,1634) order by b.`name`";

		$rows = $this->func_query($query);

		return $rows;
	}

	public function getUserGroup ($email) {
		
		$query = "SELECT `customer_group_id` FROM `oc_customer` WHERE email = '$email'";

		$rows = $this->func_query_first_cell($query);

		$rows = ($rows)? $rows: 8;

		return $rows;

	}

	public function loadManufacturers () {

		// Getting Device Manufacturers related to Device Classes
		$query = "SELECT * FROM `inv_manufacturer` WHERE `status` = '1' ORDER BY name";

		$manufacturers = $this->func_query($query); // Manufacturers

		// Aranging Manufacturers
		$results = array();
		foreach ($manufacturers as $row) {

			// Array Manufacturers
			$results[] = array(
				'id' => $row['manufacturer_id'],
				'name' => $row['name']
				);
		}

		return $results;
	}

	public function loadModels ($manufacturer_id) { 
		$query = "SELECT * FROM `inv_model_mt` WHERE `manufacturer_id` = '$manufacturer_id' ORDER BY `device`";
		$modelsRecord = $this->func_query($query);

			// Aranging Models
		$results = array();
		foreach ($modelsRecord as $mRow) {

				// Array Models
			$results[] = array(
				'id' => $mRow['model_id'],
				'name' => $mRow['device']
				);
		}

		return $results;
	}

	public function loadSubModels ($device_id) {
		$query = "SELECT a.*, b.`carrier_id`, b.`id` FROM `inv_model_dt` a, `inv_model_carrier` b WHERE a.`sub_model_id` = b.`sub_model_id` AND model_id = '". $device_id ."' ORDER BY `sub_model`";
		$subModelsRecord = $this->func_query($query);

				// Aranging Sub Models
		$results = array();
		foreach ($subModelsRecord as $sMRow) {
			$carrier = '';
			if ($sMRow['carrier_id']) {
				$carrier = $this->func_query_first_cell("SELECT name FROM `inv_carrier` WHERE `id` = '". $sMRow['carrier_id'] ."'");
			}
					// Array Sub Models
			$results[] = array(
				'id' => $sMRow['id'],
				'name' => $sMRow['sub_model'] . (($carrier)?' (' . $carrier . ')':''),
				);

		}

		return $results;
	}

	public function loadMainClass ($prop) {
		$query = "SELECT * FROM `inv_main_classification` WHERE status = 1";
		$mainClasses = $this->func_query($query);

		// Aranging Attr
		$results = array();
		foreach ($mainClasses as $sMRow) {
			// Array ATTR
			$results[] = array(
				'id' => $sMRow['id'],
				'name' => $sMRow['name'],
				);

		}

		return $results;
	}

	public function loadAttr ($model_id) {
		$query = "SELECT a.*, c.`name` FROM `inv_device_attrib` a, `inv_device_model` b, `inv_attr` c WHERE a.`device_model_id` = b.`device_model_id` AND a.`attrib_id` = c.`id` AND b.`model_id` = '" . $model_id . "'  GROUP BY a.`attrib_id`";
		$subModelsRecord = $this->func_query($query);

		// Aranging Attr
		$results = array();
		foreach ($subModelsRecord as $sMRow) {
			// Array ATTR
			$results[] = array(
				'id' => $sMRow['attrib_id'],
				'name' => $sMRow['name'],
				);

		}

		return $results;
	}

	public function loadModelClasses ( $filter = array() ) {
		$query = "SELECT d.`class_id` id, f.`main_class` name, g.`name` as main_name FROM `inv_device_model` a, `inv_device_device` b, `inv_device_manufacturer` c, `inv_device_class` d, `inv_device_attrib` e, `inv_classification` f, `inv_main_classification` g WHERE a.`device_device_id` = b.`device_device_id` AND b.`device_manufacturer_id` = c.`device_manufacturer_id` AND c.`class_id` = d.`device_class_id` AND a.`device_model_id` = e.`device_model_id` AND d.`class_id` = f.`id` AND f.`main_class_id` = g.`id`";
		
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
			$query .= " AND d.`class_id` IN (". $this->func_query_first_cell('SELECT GROUP_CONCAT(id) FROM `inv_classification` WHERE `main_class_id` = "'. $main_class_id .'"') .")";
		}

		$query .= ' GROUP BY d.`class_id` ORDER BY f.`sort` ASC';

		$modelClasses = array();
		if ($model_id || $device_id) {
			$modelClasses = $this->func_query($query);
		}
		
		return $modelClasses;

	}

	public function loadClassAttr ( $filter = array() ) {
		$query = "SELECT f.`id`, f.`name`, g.`name` main_name FROM `inv_device_model` `a`, `inv_device_device` b, `inv_device_manufacturer` `c`, `inv_device_class` d, `inv_device_attrib` e, `inv_attr` f, `inv_attribute_group` `g` WHERE a.`device_device_id` = b.`device_device_id` AND b.`device_manufacturer_id` = c.`device_manufacturer_id` AND c.`class_id` = d.`device_class_id` AND a.`device_model_id` = e.`device_model_id` AND e.`attrib_id` = f.`id` AND f.`attribute_group_id` = g.`id`";
		
		$model_id = (int)$filter['model_id'];
		
		if ($model_id) { 
			$query .= " AND a.`model_id` = '$model_id' ";
		}

		$main_class_id = (int)$filter['main_class_id'];
		if ($main_class_id) {
			$query .= " AND d.`class_id` IN (". $this->func_query_first_cell('SELECT GROUP_CONCAT(id) FROM `inv_classification` WHERE `main_class_id` = "'. $main_class_id .'"') .")";
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

		$modelClassAttr = $this->func_query($query);

		return $modelClassAttr;
		
	}

	public function filterProducts( $filter = array() , $attr = false) {
		
		$query = "SELECT  d.* FROM `inv_device_model` a, `inv_device_device` b, `inv_device_manufacturer` c, `inv_device_class` d, `inv_device_attrib` e WHERE a.`device_device_id` = b.`device_device_id` AND b.`device_manufacturer_id` = c.`device_manufacturer_id` AND c.`class_id` = d.`device_class_id` AND a.`device_model_id` = e.`device_model_id`";

		//$class_id = (int)$filter['class_id'];
		$class_id = implode("','", $filter['class_id']);
		if ($class_id) {
			$query .= " AND d.`class_id` IN ('$class_id')";
		}

		$main_class_id = (int)$filter['main_class_id'];
		if ($main_class_id && !$class_id) {
			$query .= " AND d.`class_id` IN (". $this->func_query_first_cell('SELECT GROUP_CONCAT(id) FROM `inv_classification` WHERE `main_class_id` = "'. $main_class_id .'"') .")";
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

		$attrib_id = $filter['attrib_id'];
		if ($attrib_id) { 
			$query .= " AND e.`attrib_id` IN $attrib_id ";
		}
		$query .= ' GROUP BY d.`device_product_id`';		
		$products = $this->func_query($query);
		$productIds = $this->simplifyIds($products, 'device_product_id');
		$page = (int)$filter['page'];
		if ($attr) {
			return $this->simplifyIds($this->loadProducts($productIds, '', $attr), 'device_product_id');
		}
		return $this->loadProducts($productIds, $page);

	}

	private function loadProducts ($productIds, $page, $attr = false) {
		
		$nRows = 50;
		$start = ($page - 1) * $nRows;
		$end = $page * $nRows;
		$products = array();
		if ($attr) {
			return $this->func_query("SELECT a.`device_product_id` FROM `inv_device_product` a, `oc_product` b, `oc_product_description` c WHERE a.`sku` = b.`model` AND b.`product_id` = c.`product_id` AND b.`is_main_sku` = '1' AND b.`status` = '1' AND a.`device_product_id` IN ('". implode("', '", $productIds) ."')");
		}
		if ($productIds) {
			$products = $this->func_query("SELECT b.`product_id`, b.`image`, b.`price`, b.`model`, c.`name`, b.`quantity` FROM `inv_device_product` a, `oc_product` b, `oc_product_description` c WHERE a.`sku` = b.`model` AND b.`product_id` = c.`product_id` AND b.`is_main_sku` = '1' AND b.`status` = '1' AND a.`device_product_id` IN ('". implode("', '", $productIds) ."')  LIMIT $start,$end");
			$count = $this->func_query_first_cell("SELECT COUNT(b.`product_id`) FROM `inv_device_product` a, `oc_product` b, `oc_product_description` c WHERE a.`sku` = b.`model` AND b.`product_id` = c.`product_id` AND a.`device_product_id` IN ('". implode("', '", $productIds) ."') and b.status=1");
		}

		return array('products' => $products, 'total' => $count);
	}

	public function productGrade ($sku)	{

		$products = array();
		if ($sku) {
			$products = $this->func_query("SELECT * FROM `oc_product` WHERE `main_sku` = '$sku'");
		}

		return $products;
	}

	public function productPrice ($product_id) {
		
		$product_id = (int) $product_id;
		if ($product_id) {
			$price = $this->func_query("SELECT * FROM `oc_product_discount` WHERE product_id = '$product_id' ORDER BY `customer_group_id`, `quantity` asc");
		}
		$return = array();
		$i=0;
		foreach($price as $_p) {
			$return[$i] = array(
				'product_id'=>$_p['product_id'],
				'customer_group_id'=>$_p['customer_group_id'],
				'quantity'=>$_p['quantity'],
				'price'=>$_p['price']
				);
			$i++;
		}
		//echo "<Pre>";
		//print_r($return);exit;
		return $return;
	}

	public function productQtyOnOrder ($sku) {
		
		if ($sku) {
			$qty = (int)$this->func_query_first_cell("SELECT SUM(a.`qty_shipped`) FROM `inv_shipment_items` a, `inv_shipments` b WHERE a.`shipment_id` = b.`id` AND b.`status` IN ('Pending', 'Issued', 'Received') AND a.`product_sku` = '$sku'");
		}

		return $qty;
	}

	private function simplifyIds ($array, $key)	{
		$results = array();
		foreach ($array as $row) {
			$results[] = $row[$key];
		}
		return $results;
	}

	public function nextAction($action) {
		$array = array(
			'loadManufacturers',
			'loadModels',
			'loadSubModels'
			//'loadMainClass'
			);
		$key = array_search($action, $array);

		$result = '';
		if ($array[($key + 1)]) {
			$result = $array[($key + 1)];
		}

		return $result; 
	}
	
}

$catalog = new load_catalog;

?>