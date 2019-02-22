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
		// include("../library/cache.php");
		// $cache = new Cache;;

		$query = "SELECT * FROM `inv_model_mt` WHERE `manufacturer_id` = '$manufacturer_id' ORDER BY `device`";
		$modelsRecord = $this->func_query($query);

			// Aranging Models
		// $results = $this->cache->get('catalog_manufacturers.' . (int)$manufacturer_id);
		// if(!$results)
		// {
		$results = array();
		foreach ($modelsRecord as $mRow) {

				// Array Models
			$results[] = array(
				'id' => $mRow['model_id'],
				'name' => $mRow['device']
				);
		}
	// 	$this->cache->set('catalog_manufacturers.' . (int)$manufacturer_id, $results);
	// }
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
				$products = $this->func_query($query . $nQuery);
				$productIdsreturn = $this->simplifyIds($products, 'device_product_id');
				foreach ($productIdsreturn as $productid) {
					$productIds[] = $productid;
				}
			}
		} else {
			$query .= ' GROUP BY d.`device_product_id`';
			$products = $this->func_query($query);
			$productIds = $this->simplifyIds($products, 'device_product_id');
		}

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
			return $this->func_query("SELECT a.`device_product_id` FROM `inv_device_product` `a`, `oc_product` `b`, `oc_product_description` `c`, `inv_device_class` `d`, `inv_classification` `e` WHERE a.`sku` = b.`model` AND b.`product_id` = c.`product_id` AND a.`device_product_id` = d.`device_product_id` AND d.`class_id` = e.`id` AND b.`is_main_sku` = '1' AND b.`status` = '1' AND a.`device_product_id` IN ('". implode("', '", $productIds) ."') ORDER BY e.`sort` ASC");
		}
		if ($productIds) {
			$products = $this->func_query("SELECT b.`product_id`, b.`image`, b.`price`,b.`sale_price`, b.`model`, c.`name`, b.`quantity` FROM `inv_device_product` `a`, `oc_product` `b`, `oc_product_description` `c`, `inv_device_class` `d`, `inv_classification` `e` WHERE a.`sku` = b.`model` AND b.`product_id` = c.`product_id` AND a.`device_product_id` = d.`device_product_id` AND d.`class_id` = e.`id` AND b.`is_main_sku` = '1' AND b.`status` = '1' AND a.`device_product_id` IN ('". implode("', '", $productIds) ."') ORDER BY e.`sort` ASC LIMIT $start,$end");
			$count = $this->func_query_first_cell("SELECT COUNT(b.`product_id`) FROM `inv_device_product` a, `oc_product` b, `oc_product_description` c WHERE a.`sku` = b.`model` AND b.`product_id` = c.`product_id` AND a.`device_product_id` IN ('". implode("', '", $productIds) ."') and b.status=1");
		}
		/*$results = array();
		foreach ($products as $value) {
			if ($value['sale_price'] != '0.0000') {
					$value['price'] = $value['sale_price'];
					$value['is_sale_item'] = 'yes';
					$results[] = $value;
			} else {
				$results[] = $value;
			}
		}*/

		return array('products' => $products, 'total' => $count);
	}

	public function productGrade ($sku)	{

		$products = array();
		if ($sku) {
			$products = $this->func_query("SELECT a.* FROM `oc_product` a  WHERE `main_sku` = '$sku' AND status = '1'");
		}

		return $products;
	}

	public function productPrice ($product_id) {
		
		$product_id = (int) $product_id;
		if ($product_id) {
			$price = $this->func_query("SELECT * FROM `oc_product_discount` WHERE product_id = '$product_id' ORDER BY `customer_group_id`, `quantity` asc");
			$sale_price = $this->func_query_first_cell("SELECT sale_price FROM `oc_product` WHERE product_id = '$product_id'");
		}
		$return = array();
		$i=0;
		if ($sale_price!='0.0000') {
			foreach($price as $_p) {
				$return[$i] = array(
					'product_id'=>$_p['product_id'],
					'customer_group_id'=>$_p['customer_group_id'],
					'quantity'=>$_p['quantity'],
					'price'=>$sale_price 
					);
				$i++;
			}
		} else {
			foreach($price as $_p) {
				$return[$i] = array(
					'product_id'=>$_p['product_id'],
					'customer_group_id'=>$_p['customer_group_id'],
					'quantity'=>$_p['quantity'],
					'price'=>$_p['price']
					);
				$i++;
			}
		}
		
		return $return;
	}
	public function isSaleItem ($product_id) {
		if ($product_id) {
			$sale_price = $this->func_query_first_cell("SELECT sale_price FROM `oc_product` WHERE product_id = '$product_id'");
		}
		if ($sale_price!='0.0000') {
			$return = array();
			$return['is_sale_item'] = true;
			$return['sale_price']= $sale_price;
			return $return;
		} else {
			$return = array();
			$return['is_sale_item']=false;
			$return['sale_price']= ' ';
			return $return;
		}
	}

	public function productQtyOnOrder ($sku) {
		
		if ($sku) {
			$qty = (int)$this->func_query_first_cell("SELECT SUM(a.`qty_shipped`) FROM `inv_shipment_items` a, `inv_shipments` b WHERE a.`shipment_id` = b.`id` AND b.`status` IN ('Pending', 'Issued', 'Received') AND a.`product_sku` = '$sku'");
		}

		return $qty;
	}
	public function getScrapper($sku){
		//print_r($sku);
		$scrapping_sites = array('mobile_sentrix', 'fixez', 'mengtor', 'mobile_defenders','etrade_supply','maya_cellular','lcd_loop','parts_4_cells','cell_parts_hub');
		$result = array();
		foreach ($scrapping_sites as $site) {

			$price = $this->func_query_first("select *, (SELECT price from inv_product_price_scrap_history where sku = ph.sku and type = ph.type order by added desc limit 1, 1) as old_price from inv_product_price_scrap_history ph where sku = '$sku' AND type = '$site' order by added DESC limit 1");
			$change = number_format($price['price'] / $price['old_price'] * 100, 2);
			if ($change < 100.00 && $change > 0.00) {
						$change = '&#8595;' . (number_format(100 - $change,2)).'%';
						$up_or_down ='down';
					} else if ($change == 0.00) {
						$change = '&#8593;'.(number_format(100 - $change,2)).'%';
						$up_or_down ='no_change';
					} else {
						$change = '&#8593;' . (number_format($change - 100,2)).'%';
						$up_or_down ='up';
					}
					if((float)$price['old_price']=='0.00')
					{
						$change = "";
						$up_or_down = "no_change";
					}
			if ($price['out_of_stock'] == '1') {
				$stock = 'no';
			} else {
				$stock = 'yes';
			}
			$url = $this->func_query_first_cell("SELECT url from inv_product_price_scrap where url<>'' and type='$site' AND sku = '$sku'");
			$result[] = array('site_name' => $site, 
				'site_url' => $url,
				'current_price' => (float)$price['price'],
				'old_price' => (float)$price['old_price'],
				'up_or_down' => $up_or_down,
				'stock' => $stock,
				'change' => $change);
		}
		return $result;
	}
	public function getTrueCostRow($sku){
		//print_r($sku);
		$product_cost = $this->func_query_first("Select pc.user_id , u.name , pc.current_cost, pc.raw_cost , pc.ex_rate, pc.cost_date , pc.shipping_fee, pc.vendor_code from oc_product p left join inv_product_costs pc on (p.sku = pc.sku) left join inv_users u on (u.id = pc.user_id) where p.sku = '$sku' order by pc.id DESC");
		$result = array();
		$result['cost_date'] =date("d/m/Y", strtotime($product_cost['cost_date']));
		if ($product_cost['raw_cost'] != '' ) {
			$result['raw_cost'] =$product_cost['raw_cost'];
		 } else {
		 	$result['raw_cost'] = number_format(0,2);
		 }
		 if ($product_cost['ex_rate'] != '' ) {
			$result['ex_rate'] =$product_cost['ex_rate'];
		 } else {
		 	$result['ex_rate'] = number_format(0,2);
		 }
		 if ($product_cost['shipping_fee'] != '' ) {
			$result['shipping_fee'] =$product_cost['shipping_fee'];
		 } else {
		 	$result['shipping_fee'] = number_format(0,2);
		 }
		$result['true_cost'] = number_format(($product_cost['raw_cost'] + $product_cost['shipping_fee']) / $product_cost['ex_rate'], 2);
		return $result;
	}

	public function productNeededQty ($sku) {
		
		if ($sku) {
			$qty = (int)$this->func_query_first_cell("SELECT SUM(a.`needed`) FROM `inv_vendor_po_items` a, `inv_vendor_po` b WHERE a.`vendor_po_id` = b.`vendor_po_id` AND b.`status` IN ('issued') AND a.`sku` = '$sku'");
		}

		return $qty;
	}

	public function getSetting ($setting_name) {

		if ($setting_name) {
			$value = $this->func_query_first_cell("SELECT setting_value FROM `catalog_setting` WHERE `setting_name` = '$setting_name'");
			if ($value) {
				return $value;
			}
		}

		return false;
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