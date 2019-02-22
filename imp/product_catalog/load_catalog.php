<?php

class load_catalog extends Database {
	
	public function loadClass () {
		$rows = $this->func_query("SELECT * FROM inv_classification WHERE status=1");
		return $rows;
	}

	public function loadClassAttr ($class_id) {
		$attrib_groups = $this->func_query_first_cell("SELECT attribute_group_id FROM inv_classification WHERE id=" . (int) $class_id);
		$attrib_groups = explode(',', $attrib_groups);

		$results = array();
		foreach ($attrib_groups as $i => $attrib_group) {
			$groupName = $this->func_query_first_cell("SELECT name FROM inv_attribute_group WHERE id='" . (int) $attrib_group . "'");
			$rows = $this->func_query("SELECT * FROM inv_attr WHERE attribute_group_id = '" . (int) $attrib_group . "'");

			$restuls[] = array(
				'id' => (int) $attrib_group,
				'name' => $groupName,
				'attr' => $rows,
				);
			
		}

		return $results;
	}

	public function loadManufacturers ($prop, $class_id) {

		// Getting Device Manufacturers related to Device Classes
		$query = "SELECT * FROM `inv_device_manufacturer` a , `inv_manufacturer` b, `inv_device_class` c WHERE a.`manufacturer_id` = b.`manufacturer_id` AND a.`class_id` = c.`device_class_id` AND b.`status` = '1' AND c.`class_id` = '". (int)$class_id ."' GROUP BY a.`manufacturer_id`";

		$manufacturers = $this->func_query($query); // Manufacturers

		// Aranging Manufacturers
		$results = array();
		foreach ($manufacturers as $row) {

			// Array Manufacturers
			$results[] = array(
				'device_manufacturer_id' => $row['device_manufacturer_id'],
				'manufacturer_id' => $row['manufacturer_id'],
				'id' => $row['manufacturer_id'],
				'name' => $row['name']
				);
		}

		return $results;
	}

	public function loadModels ($manufacturer_id, $class_id) {
		$query = "SELECT  a.*, b.* FROM `inv_device_device` a, `inv_model_mt` b, `inv_device_manufacturer` c, `inv_device_class` d  WHERE a.`device_id` = b.`model_id`  AND a.`device_manufacturer_id` = c.`device_manufacturer_id`  AND c.`class_id` = d.`device_class_id`  AND c.`manufacturer_id` = '$manufacturer_id'  AND d.`class_id` = '". (int) $class_id ."' GROUP BY a.`device_id`";
		$modelsRecord = $this->func_query($query);

			// Aranging Models
		$results = array();
		foreach ($modelsRecord as $mRow) {

				// Array Models
			$results[] = array(
				'device_device_id' => $mRow['device_device_id'],
				'device_id' => $mRow['device_id'],
				'id' => $mRow['device_id'],
				'name' => $mRow['device']
				);
		}

		return $results;
	}

	public function loadSubModels ($device_id, $class_id) {
		$query = "SELECT a.*, c.`sub_model`, b.`carrier_id` FROM `inv_device_model` a, `inv_model_carrier` b, `inv_model_dt` c, `inv_device_device` d, `inv_device_manufacturer` e, `inv_device_class` f WHERE b.`id` = a.`model_id` AND b.`sub_model_id` = c.`sub_model_id` AND a.`device_device_id` = d.`device_device_id` AND e.`device_manufacturer_id` = d.`device_manufacturer_id` AND e.`class_id` = f.`device_class_id` AND f.`class_id` = '". (int) $class_id ."' AND d.`device_id` = '". $device_id ."' GROUP BY a.`model_id`";
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
				'device_model_id' => $sMRow['device_model_id'],
				'model_id' => $sMRow['model_id'],
				'id' => $sMRow['model_id'],
				'name' => $sMRow['sub_model'] . (($carrier)?' (' . $carrier . ')':''),
				'carrier' => ''
				);

		}

		return $results;
	}

	public function filterProducts( $filter = array() ){
		$class_id = (int)$filter['class_id'];
		if ($class_id) {
			$query = "SELECT  d.* FROM `inv_device_model` a, `inv_device_device` b, `inv_device_manufacturer` c, `inv_device_class` d WHERE a.`device_device_id` = b.`device_device_id` AND b.`device_manufacturer_id` = c.`device_manufacturer_id` AND c.`class_id` = d.`device_class_id` AND d.`class_id` = '$class_id'";

			$manufacturer_id = (int)$filter['manufacturer_id'];
			if ($manufacturer_id) {
				$query .= " AND c.`manufacturer_id` = '$manufacturer_id'";
			}

			$device_id = (int)$filter['device_id'];
			if ($device_id) { 
				$query .= " AND b.`device_id` = '$device_id' ";
			}

			$model_id = (int)$filter['model_id'];
			if ($model_id) { 
				$query .= " AND a.`model_id` = '$model_id' ";
			}
			$query .= ' GROUP BY d.`device_product_id`';

			$products = $this->func_query($query);
		}
		$productIds = $this->simplifyIds($products, 'device_product_id');

		return $this->loadProducts($productIds);

	}


	private function loadProducts ($productIds) {
		
		$products = array();
		if ($productIds) {
			$products = $this->func_query("SELECT a.*, b.`image` FROM `inv_device_product` a, `oc_product` b WHERE a.`sku` = b.`model` AND a.`device_product_id` IN ('". implode("', '", $productIds) ."')");
		}

		return $products;
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