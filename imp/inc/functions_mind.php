<?php

function getMPSBySku($sku) {
	global $db;
	
	// get last 90 days orders
	$start_date = date ( "Y-m-d H:i:s", (time () - (90 * 60 * 60 * 24)) );
	$end_date = date ( "Y-m-d H:i:s", (time () + (60 * 60 * 12)) );
	
	$_query = "select sum(product_qty) as total from inv_orders_items ot inner join inv_orders o on (ot.order_id = o.order_id)
			   where ot.product_sku = '$sku' and order_date >= '$start_date' AND order_date <= '$end_date'";
	
	$product_qty = $db->func_query_first_cell ( $_query );
	
	if ($product_qty > 0) {
		$product_qty = $product_qty / 3;
	}
	
	return number_format ( $product_qty, 2 );
}

function getDPSBySku($sku) {
	global $db;
	
	// get last 30 days orders
	$start_date = date ( "Y-m-d H:i:s", (time () - (30 * 60 * 60 * 24)) );
	$end_date = date ( "Y-m-d H:i:s", (time () + (60 * 60 * 12)) );
	
	$days = 30;
	$outofstock_days = $db->func_query_first_cell ( "select count(id) from inv_product_outofstock_days where product_sku = '$sku'" );
	$days = $days - $outofstock_days;
	if ($days <= 0) {
		$days = 1;
	}
	
	$_query = "select sum(product_qty) as total from inv_orders_items ot inner join inv_orders o on (ot.order_id = o.order_id)
			   where ot.product_sku = '$sku' and order_date >= '$start_date' AND order_date <= '$end_date'";
	
	$product_qty = $db->func_query_first_cell ( $_query );
	if ($product_qty > 0) {
		$product_qty = $product_qty / $days;
	}
	
	return number_format ( $product_qty, 2 );
}

function getRop($mps, $lead_time, $qc_time, $safety_stock) {
	$rop = $mps * ($lead_time + $qc_time + $safety_stock);
	return $rop;
}

function getQtyToBeShipped($rop, $qty, $mps, $lead_time, $qc_time, $addtional_days = 4) {
	$qtyToShipped = ($rop - $qty) + ($addtional_days * $mps) + ($mps * ($lead_time + $qc_time));
	
	if ($qtyToShipped > 0) {
		return $qtyToShipped;
	}
	else {
		return 0;
	}
}

function getShipmentDetail($shipments, $sku, $qty_shipped) {
	if (! $shipments) {
		return;
	}
	
	$output = '';
	$qty = 0;
	foreach ( $shipments as $shipment ) {
		if ($shipment ['product_sku'] == $sku) {
			$output .= $shipment ['package_number'] . " --- " . $shipment ['qty_shipped'] . " qty <br />";
			$qty += $shipment ['qty_shipped'];
		}
	}
	
	return array(
			$output,
			$qty_shipped - $qty );
}

function getProductSkuLastID($part_type) {
	global $db;
	
	$part_sku = $db->func_query_first_cell ( "select max(abs(replace(sku,'$part_type-',''))) as part_sku from oc_product where sku LIKE '%$part_type-%'" );
	return $part_sku;
}

function getSKUFromLastId($part_type, $last_id) {
	global $db;
	
	$last_sku = $db->func_query_first_cell ( "select sku from oc_product where sku LIKE '%$part_type-%' order by (abs(replace(sku,'$part_type-',''))) desc limit 1" );
	$parts = explode ( "-", $last_sku );
	
	if (strlen ( $parts [2] ) > 3) {
		if ($last_id >= 99 && $last_id < 999) {
			$new_sku = $part_type . "-0" . ($last_id + 1);
		}
		elseif ($last_id >= 9 && $last_id < 99) {
			$new_sku = $part_type . "-00" . ($last_id + 1);
		}
		elseif ($last_id < 9) {
			$new_sku = $part_type . "-000" . ($last_id + 1);
		}
		else {
			$new_sku = $part_type . "-" . ($last_id + 1);
		}
	}
	else {
		if ($last_id >= 9 && $last_id < 99) {
			$new_sku = $part_type . "-0" . ($last_id + 1);
		}
		elseif ($last_id < 9) {
			$new_sku = $part_type . "-00" . ($last_id + 1);
		}
		else {
			$new_sku = $part_type . "-" . ($last_id + 1);
		}
	}
	
	return $new_sku;
}

function addToRejectedShipment($product_sku, $qty, $shipment_id) {
	global $db;
	
	$last_id = $db->func_query_first_cell ( "select id from inv_rejected_shipments where status != 'Completed'" );
	if (! $last_id) {
		$rejcetedShipment = array();
		$rejcetedShipment ['status'] = 'Pending';
		$rejcetedShipment ['date_added'] = date ( 'Y-m-d H:i:s' );
		$rejcetedShipment ['user_id'] = $_SESSION ['user_id'];
		
		$last_id = $db->func_array2insert ( 'inv_rejected_shipments', $rejcetedShipment );
	}
	
	$rejcetedShipmentItem = array();
	$rejcetedShipmentItem ['qty_rejected'] = $qty;
	
	$checkExist = $db->func_query_first_cell ( "select id from inv_rejected_shipment_items where product_sku = '$product_sku' and
						rejected_shipment_id = '$last_id' and shipment_id = '$shipment_id'" );
	
	if (! $checkExist) {
		$rejcetedShipmentItem ['shipment_id'] = $shipment_id;
		$rejcetedShipmentItem ['product_sku'] = $product_sku;
		$rejcetedShipmentItem ['rejected_shipment_id'] = $last_id;
		
		$db->func_array2insert ( 'inv_rejected_shipment_items', $rejcetedShipmentItem );
	}
	else {
		$db->func_array2update ( 'inv_rejected_shipment_items', $rejcetedShipmentItem, "product_sku = '$product_sku' and
						rejected_shipment_id = '$last_id' and shipment_id = '$shipment_id'" );
	}
	
	return 1;
}

function removeFromRejectedShipment($product_sku, $shipment_id) {
	global $db;
	
	$last_id = $db->func_query_first_cell ( "select id from inv_rejected_shipments where status != 'Completed'" );
	if (! $last_id) {
		return 0;
	}
	
	$db->db_exec ( "delete from inv_rejected_shipment_items where product_sku = '$product_sku' and rejected_shipment_id = '$last_id' and shipment_id = '$shipment_id'" );
	return 1;
}

// update only if raw_cost > 0
function addUpdateProductCost($SKU, $raw_cost, $ex_rate, $shipping_fee = 0, $vendor_code = 'China Office') {
	global $db;
	
	$date = date ( 'Y-m-d' );
	if ($ex_rate > 0) {
		$current_cost = $raw_cost / $ex_rate;
	}
	else {
		$current_cost = $raw_cost;
	}
	
	if (strlen ( $SKU ) > 0 and $raw_cost > 0) {
		$prev_cost = 0;
		$lastCostExist = $db->func_query_first_cell ( "select raw_cost from inv_product_costs where vendor_code = '$vendor_code' AND sku = '$SKU' order by cost_date DESC limit 1" );
		if ($lastCostExist) {
			$prev_cost = $lastCostExist;
		}
		
		$productCost = array();
		$productCost ['raw_cost'] = $raw_cost;
		$productCost ['current_cost'] = $current_cost;
		$productCost ['ex_rate'] = $ex_rate;
		$productCost ['prev_cost'] = $prev_cost;
		$productCost ['user_id'] = $_SESSION ['user_id'];
		$productCost ['shipping_fee'] = $shipping_fee;
		$productCost ['dateofmodification'] = date ( 'Y-m-d H:i:s' );
		
		$isExist = $db->func_query_first_cell ( "select id from inv_product_costs where vendor_code = '$vendor_code' AND sku = '$SKU' AND cost_date = '$date'" );
		if ($isExist) {
			$db->func_array2update ( "inv_product_costs", $productCost, " vendor_code = '$vendor_code' AND sku = '$SKU' AND cost_date = '$date' " );
		}
		else {
			$productCost ['sku'] = $SKU;
			$productCost ['cost_date'] = $date;
			$productCost ['vendor_code'] = $vendor_code;
			$db->func_array2insert ( "inv_product_costs", $productCost );
		}
	}
	
	return 1;
}

function getItemName($sku) {
	if (! $sku) {
		return;
	}
	
	global $db;
	return $db->func_query_first_cell ( "select name from oc_product_description where product_id = ( select product_id from oc_product where sku = '$sku' limit 1)" );
}

function getItemImage($sku) {
	if (! $sku) {
		return;
	}
	
	global $db;
	return $db->func_query_first_cell ( "select image from oc_product where sku = '$sku'" );
}

function createSKU($new_sku, $name, $desc, $price, $main_sku = '', $is_main_sku = 0, $grade = '', $image = '', $status = 0) {
	global $db;
	
	// insert into products table if not exist
	$productExist = $db->func_query_first_cell ( "select product_id from oc_product where model = '$new_sku'" );
	if (! $productExist) {
		$product = array();
		$product ['sku'] = $new_sku;
		$product ['model'] = $new_sku;
		if (! is_null ( $price )) {
			$product ['price'] = $price;
		}
		$product ['main_sku'] = $main_sku;
		$product ['is_main_sku'] = $is_main_sku;
		$product ['stock_status_id'] = 5;
		$product ['weight_class_id'] = 5;
		$product ['length_class_id'] = 3;
		$product ['is_imp_sku'] = 1;
		$product ['status'] = $status;
		$product ['date_available'] = date ( 'Y-m-d' );
		if ($grade) {
			$product ['item_grade'] = "Grade " . $grade;
		}
		
		$product ['image'] = $image;
		$product ['date_added'] = date ( 'Y-m-d H:i:s' );
		
		$product_id = $db->func_array2insert ( "oc_product", $product );
		if ($product_id) {
			$product_desc = array();
			$product_desc ['product_id'] = $product_id;
			if ($grade) {
				$product_desc ['name'] = $name . " - Grade $grade";
				$product_desc ['description'] = $desc . "<br /> This is $grade Grade Product.";
			}
			else {
				$product_desc ['name'] = $name;
				$product_desc ['description'] = $desc;
			}
			
			$product_desc ['language_id'] = 1;
			$db->func_array2insert ( "oc_product_description", $product_desc );
			
			$product_addtl = array();
			$product_addtl ['product_id'] = $product_id;
			$product_addtl ['additional_product_id'] = 5;
			$product_addtl ['name'] = "New Product";
			$product_addtl ['language_id'] = 1;
			$db->func_array2insert ( "oc_product_to_field", $product_addtl );
			
			$db->db_exec ( "insert ignore into oc_product_to_store SET product_id = '$product_id' , store_id = 0" );
		}
	}
	else {
		$product_id = $productExist;
	}
	
	return $product_id;
}

function createGradeSku($product_sku, $grade) {
	global $db;
	
	$main_sku = mysql_real_escape_string ( $product_sku );
	$parts = explode ( "-", $main_sku );
	$part_type = $parts [0] . "-" . $parts [1];
	
	$product_details = $db->func_query_first ( "select p.product_id , name , image, price , description, manufacturer_id  from oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id) where p.model = '$main_sku'" );
	
	$last_id = getProductSkuLastID ( $part_type );
	$last_id = ( int ) $last_id;
	$new_sku = getSKUFromLastId ( $part_type, $last_id );
	
	$product_id = createSKU ( $new_sku, $product_details ['name'], $product_details ['description'], $product_details ['price'], $main_sku, 0, $grade, $product_details ['image'] );
	
	$db->db_exec ( "update oc_product SET location = 1 , manufacturer_id = '" . $product_details ['manufacturer_id'] . "' where sku = '$new_sku'" );
	$db->db_exec ( "insert into oc_product_to_category (product_id , category_id) select $product_id , category_id from oc_product_to_category where product_id = '" . $product_details ['product_id'] . "'" );
	
	return $product_id;
}

function getProduct($main_sku, $fields = array()) {
	global $db;
	
	if ($fields) {
		$fields_str = implode ( ",", $fields );
	}
	else {
		$fields_str = "product_id , sku";
	}
	
	return $db->func_query_first ( "select $fields_str from oc_product where sku = '$main_sku'" );
}

function createField($field_name, $field_id, $field_type = 'text', $default = false, $values = array(), $extra = false) {
	switch ($field_type) {
		case 'select' :
			$field = "<select name='$field_name' id='$field_id' $extra>";
			$field .= "<option value=''>Select One</option>";
			foreach ( $values as $value ) {
				if ($default == $value ['id']) {
					$field .= "<option value='" . $value ['id'] . "' selected='selected'>" . $value ['value'] . "</option>";
				}
				else {
					$field .= "<option value='" . $value ['id'] . "'>" . $value ['value'] . "</option>";
				}
			}
			$field .= "</select>";
			break;
		
		case 'checkbox' :
			$checked = ($default == 1) ? "checked='checked'" : '';
			$field = "<input type='checkbox' name='$field_name' id='$field_id' value='1' $checked $extra />";
			break;
		
		case 'file' :
			$field = "<input type='file' name='$field_name' id='$field_id' $extra />";
			break;
		
		case 'multiselect' :
			$field = "<select name='$field_name' id='$field_id' multiple='true' size='4' $extra>";
			$field .= "<option value=''>Select One(" . count ( $values ) . ")</option>";
			foreach ( $values as $value ) {
				if (in_array ( $value ['id'], $default )) {
					$field .= "<option value='" . $value ['id'] . "' selected='selected'>" . $value ['value'] . "</option>";
				}
				else {
					$field .= "<option value='" . $value ['id'] . "'>" . $value ['value'] . "</option>";
				}
			}
			$field .= "</select>";
			break;
		
		case 'text' :
		default :
			$field = "<input type='text' name='$field_name' id='$field_id' value='$default' $extra />";
			break;
	}
	
	return $field;
}

function getRMANumber($store_type) {
	global $db;
	
	if ($store_type == 'bigcommerce') {
		$prefix = "RL";
	}
	elseif ($store_type == 'bonanza') {
		$prefix = "BO";
	}
	elseif ($store_type == 'web') {
		$prefix = "PP";
	}
	elseif ($store_type == 'channel_advisor') {
		$prefix = "MM";
	}
	elseif ($store_type == 'wish') {
		$prefix = "WL";
	}
	elseif ($store_type == 'amazon') {
		$prefix = "AM";
	}
	else {
		$prefix = "PP";
	}
	
	$last_number = $db->func_query_first_cell ( "select max(replace(rma_number,'$prefix','')) as rma_number from inv_returns where rma_number LIKE '%$prefix%'" );
	
	if ($last_number >= 999 && $last_number < 9999) {
		$rma_number = $prefix . "0" . ($last_number + 1);
	}
	elseif ($last_number >= 99 && $last_number < 999) {
		$rma_number = $prefix . "00" . ($last_number + 1);
	}
	elseif ($last_number >= 9) {
		$rma_number = $prefix . "000" . ($last_number + 1);
	}
	elseif ($last_number < 9) {
		$rma_number = $prefix . "0000" . ($last_number + 1);
	}
	else {
		$rma_number = $prefix . "" . ($last_number + 1);
	}
	
	return $rma_number;
}

function RMAReturns($order_id) {
	global $db;
	$returns = $db->func_query ( "select rma_number from inv_returns where order_id = '$order_id'" );
	
	$output = '';
	if ($returns) {
		foreach ( $returns as $return ) {
			$output .= "<a href='return_detail.php?rma_number=" . $return ['rma_number'] . "'>RMA# " . $return ['rma_number'] . "</a> <br />";
		}
		
		$output .= "<hr />";
	}
	
	return $output;
}

function getReturnBoxNumber($is_po = 0, $box_type = false) {
	global $db;
	
	if ($is_po) {
		$prefix = 'RMAP0-';
		$last_number = $db->func_query_first_cell ( "select max(replace(box_number,'$prefix','')) as box_number from inv_returns_po" );
		return $prefix . ($last_number + 1);
	}
	else {
		if ($box_type == 'returnable') {
			$prefix = 'RMARBox';
		}
		else {
			$prefix = 'RMANRBox';
		}
		
		$last_number = $db->func_query_first_cell ( "select max(replace(box_number,'$prefix','')) as box_number from inv_return_shipment_boxes where box_number LIKE '%$prefix%'" );
		return $prefix . ($last_number + 1);
	}
}
// Defining the basic cURL function
function curl($url) {
	$ch = curl_init (); // Initialising cURL
	$options = Array(
			CURLOPT_RETURNTRANSFER => TRUE, // Setting cURL's option to return the webpage data
			CURLOPT_FOLLOWLOCATION => TRUE, // Setting cURL to follow 'location' HTTP headers
			CURLOPT_AUTOREFERER => TRUE, // Automatically set the referer where following 'location' HTTP headers
			CURLOPT_CONNECTTIMEOUT => 120, // Setting the amount of time (in seconds) before the request times out
			CURLOPT_TIMEOUT => 120, // Setting the maximum amount of time for cURL to execute queries
			CURLOPT_MAXREDIRS => 10, // Setting the maximum number of redirections to follow
			CURLOPT_USERAGENT => "Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1a2pre) Gecko/2008073000 Shredder/3.0a2pre ThunderBrowse/3.2.1.8", // Setting the useragent
			CURLOPT_URL => $url ) // Setting cURL's URL option with the $url variable passed into the function
;
	// curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt_array ( $ch, $options ); // Setting cURL's options using the previously assigned array data in $options
	$data = curl_exec ( $ch ); // Executing the cURL request and assigning the returned data to the $data variable
	curl_close ( $ch ); // Closing cURL
	return $data; // Returning the data from the function
}

function scrape_between($data, $start, $end) {
	$data = stristr ( $data, $start ); // Stripping all data from before $start
	$data = substr ( $data, strlen ( $start ) ); // Stripping $start
	$stop = stripos ( $data, $end ); // Getting the position of the $end of the data to scrape
	$data = substr ( $data, 0, $stop ); // Stripping all data from after and including the $end of the data to scrape
	return $data; // Returning the scraped data from the function
}