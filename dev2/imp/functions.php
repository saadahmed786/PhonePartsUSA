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

	removeFromRejectedShipment($product_sku, $shipment_id);

	$rejcetedShipmentItem = array();
	$rejcetedShipmentItem ['qty_rejected'] = 1;

	for($i=1;$i<=sizeof($qty);$i++){
		$rejcetedShipmentItem = array();
		$rejcetedShipmentItem ['shipment_id'] = $shipment_id;
		$rejcetedShipmentItem ['product_sku'] = $product_sku;
		$rejcetedShipmentItem ['cost'] = getTrueCost($product_sku);
		$rejcetedShipmentItem ['rejected_shipment_id'] = $last_id;
		$rejcetedShipmentItem['reject_item_id'] = getRejectId();
		$db->func_array2insert ( 'inv_rejected_shipment_items', $rejcetedShipmentItem );
	}

	return 1;
}

function needToRepairShipment($product_sku, $qty,$shipment_id,$reason) {
	global $db;

	$box_number = getReturnBoxNumber(0,'NTRBox');
	$array = array();
	$array['box_number'] = $box_number;
	$array['date_added'] = date('Y-m-d H:i:s'); 
	$array['status'] = 'Issued';
	$array['box_type'] = 'NTRBox';

	$last_id = $db->func_array2insert ( 'inv_return_shipment_boxes', $array );
	

	removeFromNeedToRepairShipment($product_sku, $shipment_id);

	

	for($i=1;$i<=sizeof($qty);$i++){
		$row = array();
		$row['return_shipment_box_id'] = $last_id;
		$row['product_sku'] = $product_sku;
		$row['quantity'] = 1;
		$row['price'] = 0.00;
		$row['source'] = 'manual';
		$row['reason'] = $reason;
		$row['shipment_id'] = $shipment_id;
		$row['return_item_id'] = getRejectId('NTR');
		$row['date_added'] = date('Y-m-d H:i:s');
		$db->func_array2insert ( 'inv_return_shipment_box_items', $row );
	}

	return 1;
}

function removeFromNeedToRepairShipment($sku,$shipment_id)
{
	global $db;
	$db->db_exec ( "delete from inv_return_shipment_box_items where product_sku = '$sku' and shipment_id = '$shipment_id' " );
	return 1;	
	
}

function addItemToBox($reject_id , $product_sku , $shipment_id = 0, $box_type = 'NTRBox', $reason = false, $order_id = false , $rma_number = false){
	global $db;

	//check if there is any box open
	$inv_return_shipment_box_id = $db->func_query_first_cell("select id from inv_return_shipment_boxes where box_number LIKE '%$box_type%' and status = 'Issued'");
	if(!$inv_return_shipment_box_id){
		$return_shipment_boxes_insert = array ();
		$return_shipment_boxes_insert ['box_number'] = getReturnBoxNumber ( 0, $box_type );
		$return_shipment_boxes_insert ['box_type']   = $box_type;
		$return_shipment_boxes_insert ['date_added'] = date ( 'Y-m-d H:i:s' );
		$inv_return_shipment_box_id = $db->func_array2insert ( "inv_return_shipment_boxes", $return_shipment_boxes_insert );
	}

	$returns_po_item_insert = array ();
	$returns_po_item_insert ['product_sku'] = $product_sku;
	$returns_po_item_insert ['quantity'] = 1;
	$returns_po_item_insert ['price']  = 0;
	$returns_po_item_insert ['source'] = 'manual';
	$returns_po_item_insert ['return_item_id'] = $reject_id;
	$returns_po_item_insert ['shipment_id'] = $shipment_id;
	$returns_po_item_insert ['order_id']   = $order_id;
	$returns_po_item_insert ['reason']     = $reason;
	$returns_po_item_insert ['rma_number'] = $rma_number;
	$returns_po_item_insert ['date_added'] = date ( 'Y-m-d H:i:s' );
	$returns_po_item_insert ['return_shipment_box_id'] = $inv_return_shipment_box_id;

	$db->func_array2insert ( "inv_return_shipment_box_items", $returns_po_item_insert );
	return 1;
}

function moveItemToBox($reject_id , $product_sku , $box_type = 'NTRBox'){
	global $db;

	//check if there is any box open
	$inv_return_shipment_box_id = $db->func_query_first_cell("select id from inv_return_shipment_boxes where box_number LIKE '%$box_type%' and status = 'Issued'");
	if(!$inv_return_shipment_box_id){
		$return_shipment_boxes_insert = array ();
		$return_shipment_boxes_insert ['box_number'] = getReturnBoxNumber ( 0, $box_type );
		$return_shipment_boxes_insert ['box_type']   = $box_type;
		$return_shipment_boxes_insert ['date_added'] = date ( 'Y-m-d H:i:s' );
		$inv_return_shipment_box_id = $db->func_array2insert ( "inv_return_shipment_boxes", $return_shipment_boxes_insert );
	}

	$returns_po_item_insert = array ();
	$returns_po_item_insert ['date_added']  = date ( 'Y-m-d H:i:s' );
	$returns_po_item_insert ['return_shipment_box_id'] = $inv_return_shipment_box_id;

	$db->func_array2update( "inv_return_shipment_box_items", $returns_po_item_insert, "return_item_id = '$reject_id'");
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
function addUpdateProductCost($SKU, $raw_cost, $ex_rate, $shipping_fee = 0, $date = false , $vendor_code = 'China Office') {
	global $db;

	if(!$date){
		$date = date ( 'Y-m-d' );
	}

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

		//$isExist = $db->func_query_first_cell ( "select id from inv_product_costs where vendor_code = '$vendor_code' AND sku = '$SKU' AND cost_date = '$date'" );
		/*if ($isExist) {
			$db->func_array2update ( "inv_product_costs", $productCost, " vendor_code = '$vendor_code' AND sku = '$SKU' AND cost_date = '$date' " );
			}
			else {*/
				$productCost ['sku'] = $SKU;
				$productCost ['cost_date'] = date('Y-m-d H:i:s');
				$productCost ['vendor_code'] = $vendor_code;
				$db->func_array2insert ( "inv_product_costs", $productCost );
		//}
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
		function getLeastPrice($sku)
		{
			global $db;
			$price = 0.00;
			$general_price  = 0.00;
			$discount_price = 0.00;

			$product_id = getProduct($sku,array('product_id'));
			$product_id = $product_id['product_id'];
			$general_price = getProduct($sku,array('price'));
			$general_price = 	(float)$general_price['price'];

			$discount_price = (float)$db->func_query_first_cell("SELECT MIN(price) FROM oc_product_discount WHERE product_id='".(int)$product_id."'");


			if($discount_price<$general_price)
			{
				$price = $discount_price;	
			}
			else
			{
				$price = $general_price;
			}
			return '$'.number_format($price,2);

		}

		function getResult($query) {
			global $db;
	// echo $query;
			return $db->func_query_first_cell ( $query );
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
				$product['vendor'] = 'abc';
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

					$product_desc ['description'] = $db->func_escape_string ( $product_desc ['description'] );
					$product_desc ['language_id'] = 1;
					$db->func_array2insert ( "oc_product_description", $product_desc );

					$product_addtl = array();
					$product_addtl ['product_id'] = $product_id;
					$product_addtl ['additional_product_id'] = 5;
					$product_addtl ['name'] = "New Product";
					$product_addtl ['language_id'] = 1;
					$db->func_array2insert ( "oc_product_to_field", $product_addtl );

					$db->db_exec ( "delete from oc_product_to_store where product_id = '$product_id' and store_id = 0" );
					$db->db_exec ( "insert into oc_product_to_store SET product_id = '$product_id' , store_id = 0" );
				}
			}
			else {
				$product_id = $productExist;
			}

			return $product_id;
		}

		function createGradeSku($product_sku, $grade) {
			global $db;

			$main_sku = $db->func_escape_string ( $product_sku );
			$parts = explode ( "-", $main_sku );
			$part_type = $parts [0] . "-" . $parts [1];

			$product_details = $db->func_query_first ( "select p.product_id , name , image, price , description, manufacturer_id  from oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id) where p.model = '$main_sku'" );

			$last_id = getProductSkuLastID ( $part_type );
			$last_id = ( int ) $last_id;
			$new_sku = getSKUFromLastId ( $part_type, $last_id );
			$price = $db->func_query_first('SELECT `raw_cost`, `shipping_fee`, `ex_rate` FROM `inv_product_costs` where `sku` = "'. $product_sku .'" order by `id` DESC');
			if (!empty($price)) {
				$product_details ['price'] = ($price['raw_cost'] + $price['shipping_fee']) / $price['ex_rate'];
				$grade_markup = $db->func_query_first_cell('select grade_'. strtolower($grade) .' from inv_product_pricing where range_from <= "'. $product_details['price'] .'" AND range_to >= "'. $product_details['price'] .'"');
				$product_details ['price'] = $grade_markup * $product_details ['price'];
			}

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
				$field = "<select class='decision' name='$field_name' id='$field_id' $extra>";
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

			$last_number = $db->func_query_first_cell ( "select max(abs(replace(rma_number,'$prefix',''))) as rma_number from inv_returns where rma_number LIKE '%$prefix%'" );

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
				$last_number = $db->func_query_first_cell ( "select max(abs(replace(box_number,'$prefix',''))) as box_number from inv_returns_po" );
				return $prefix . ($last_number + 1);
			}
			else {
				$prefix = $box_type;

				$last_number = $db->func_query_first_cell ( "select max(abs(replace(box_number,'$prefix',''))) as box_number from inv_return_shipment_boxes where box_number LIKE '%$prefix%'" );
				return $prefix . ($last_number + 1);
			}
		}

		function getRejectId($prefix = 'RJ'){
			global $db;

			$last_number = $db->func_query_first_cell ( "select max(abs(replace(current_reject_id,'$prefix',''))) as reject_item_id from inv_reject_id where current_reject_id LIKE '%$prefix%'" );

			if($last_number < 201){
				$reject_id = $prefix . "0000201";
			}
			elseif($last_number >= 201 && $last_number < 999){
				$reject_id = $prefix . "0000" . ($last_number + 1);
			}
			elseif($last_number >= 999 && $last_number < 9999){
				$reject_id = $prefix . "000" . ($last_number + 1);
			}
			elseif($last_number >= 9999 && $last_number < 99999){
				$reject_id = $prefix . "00" . ($last_number + 1);
			}
			elseif($last_number >= 99999 && $last_number < 999999){
				$reject_id = $prefix . "0" . ($last_number + 1);
			}
			else {
				$reject_id = $prefix . "" . ($last_number + 1);
			}

			$db->db_exec("update inv_reject_id SET current_reject_id = '$reject_id' , last_updated = '".date('Y-m-d H:i:s')."'");
			return $reject_id;
		}

		function getReturnItemId($rma_number , $product_sku){
			global $db;

			$prefix = $rma_number."-".$product_sku."-";

			$last_number = $db->func_query_first_cell ( "select max(abs(replace(return_item_id,'$prefix',''))) as return_item_id from inv_return_shipment_box_items where return_item_id LIKE '%$prefix%'" );
			$return_item_id = $prefix . "" . ($last_number + 1);

			return $return_item_id;
		}

		function resizeImage($filename, $dest, $max_width, $max_height) {
			list ( $orig_width, $orig_height ) = getimagesize ( $filename );

			$width = $orig_width;
			$height = $orig_height;

	// taller
			if ($height > $max_height) {
				$width = ($max_height / $height) * $width;
				$height = $max_height;
			}

	// wider
			if ($width > $max_width) {
				$height = ($max_width / $width) * $height;
				$width = $max_width;
			}

			$image_p = imagecreatetruecolor ( $width, $height );
			$image = imagecreatefromjpeg ( $filename );

			imagecopyresampled ( $image_p, $image, 0, 0, 0, 0, $width, $height, $orig_width, $orig_height );
			imagejpeg ( $image_p, $dest );

			return 1;
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
	CURLOPT_URL => $url ); // Setting cURL's URL option with the $url variable passed into the function
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

function getOrderStatus($id) {
	switch ($id) {
		case 3 :
		$order_status = 'Shipped';
		break;
		case 15 :
		$order_status = 'Processed';
		break;
		case 21 :
		$order_status = 'On Hold';
		break;
		case 24 :
		$order_status = 'Store Pick-up';
		break;
		case 16 :
		$order_status = 'Voided';
		break;
		case 11 :
		$order_status = 'Refunded';
		break;
		case 13 :
		$order_status = 'Chargeback';
		break;
		case 9 :
		$order_status = 'Canceled Reversal';
		break;
		case 12 :
		$order_status = 'Reversed';
		break;
		default :
		$order_status = 'NA';
		break;
	}

	return $order_status;
}

function getAttachments($order_id) {
	global $db;

	$result = "";
	$attachments = $db->func_query ( "select attachment_path from inv_order_docs where order_id = '$order_id'" );
	foreach ( $attachments as $attachment ) {
		$result .= "<a target='_blank' href='{$attachment['attachment_path']}'>{$attachment['attachment_path']}</a><br/>";
	}

	return $result;
}

function getComments($order_id) {
	global $db;
	$usps_link = 'USPS Tracking:<a href="https://tools.usps.com/go/TrackConfirmAction?tLabels=%s">%s</a>';
	$ups_link = 'UPS Tracking:<a href="http://wwwapps.ups.com/WebTracking/processRequest?HTMLVersion=5.0&Requester=NES&AgreeToTermsAndConditions=yes&loc=en_US&tracknum=%s">%s</a>';
	$fedex_link = 'Fedex Tracking:<a href="https://www.fedex.com/apps/fedextrack/?action=track&language=english&tracknumbers=%s">%s</a>';

	$result = "";
	$comments = $db->func_query ( "select oh.comment from oc_order_history oh where oh.order_id = '$order_id'" );
	foreach ( $comments as $comment ) {
		// parse usps , ups or fedex tracking number and make them as link
		preg_match ( "/Tracking No\\s*([a-zA-Z0-9]+)\\s*/is", $comment ['comment'], $matches );
		if ($matches) {
			if (stristr ( $comment ['comment'], "USPS" )) {
				$comment ['comment'] = preg_replace ( "/Tracking No\\s*([a-zA-Z0-9]+)\\s*/is", sprintf ( $usps_link, $matches [1], $matches [1] ), $comment ['comment'] );
			}
			elseif (stristr ( $comment ['comment'], "UPS" )) {
				$comment ['comment'] = preg_replace ( "/Tracking No\\s*([a-zA-Z0-9]+)\\s*/is", sprintf ( $ups_link, $matches [1], $matches [1] ), $comment ['comment'] );
			}
			else {
				$comment ['comment'] = preg_replace ( "/Tracking No\\s*([a-zA-Z0-9]+)\\s*/is", sprintf ( $fedex_link, $matches [1], $matches [1] ), $comment ['comment'] );
			}
		}

		$result .= "{$comment['comment']}<br/>";
	}

	return $result;
}

function replaceSpecial($str) {
	$str = trim ( $str );
	$chunked = str_split ( $str, 1 );
	$str = "";
	foreach ( $chunked as $chunk ) {
		$num = ord ( $chunk );
		// Remove non-ascii & non html characters
		if ($num >= 32 && $num <= 123) {
			$str .= $chunk;
		}
	}

	return $str;
}

function getMatchStatus($match_status , $payment_source , $avs_code , $is_address_verified) {
	global $db;

	if ($payment_source == 'Auth.net') {
		if ($avs_code == 'A') {
			$match1 = "<img src='images/check.png' alt='Match' />";
			$match2 = "<img src='images/cross.png' alt='No Match' />";
		}
		elseif ($avs_code == 'Z' || $avs_code == 'W') {
			$match1 = "<img src='images/cross.png' alt='No Match' />";
			$match2 = "<img src='images/check.png' alt='Match' />";
		}
		elseif ($avs_code == 'N') {
			$match1 = "<img src='images/cross.png' alt='No Match' />";
			$match2 = "<img src='images/cross.png' alt='No Match' />";
		}
		elseif ($avs_code == 'Y' || $avs_code == 'X') {
			$match1 = "<img src='images/check.png' alt='Match' />";
			$match2 = "<img src='images/check.png' alt='Match' />";
		}
		elseif ($avs_code == 'U' || $avs_code == 'R') {
			return $avs_code;
		}
		elseif ($avs_code == 'E') {
			return "Invalid";
		}
		elseif ($avs_code == 'S') {
			return "No AVS";
		}
		elseif ($avs_code == '') {
			return "No AVS";
		}
		else {
			$match1 = "<img src='images/cross.png' alt='No Match' />";
			$match2 = "<img src='images/cross.png' alt='No Match' />";
		}

		return "$match1,$match2";
	}
	elseif ($payment_source == 'PayPal') {
		return getPayPalMatchStatus ( $match_status , $is_address_verified );
	}
	elseif($payment_source=='Payflow')
	{
		return getPayflowMatchStatus($is_address_verified,$avs_code);
	}

	return 'NA';
}

function getPayflowMatchStatus($is_address_verified,$avs_code){
	$match1='';
	$match2='';
	if($is_address_verified=='Confirmed')
	{
		$match1="<img src='images/check.png' alt='Match' />";
	}
	else{
		$match1="<img src='images/cross.png' alt='No Match' />";
	}
	
	if($avs_code=='Y'){
		$match2="<img src='images/check.png' alt='Match' />";
	}
	else{
		$match2="<img src='images/cross.png' alt='No Match' />";
	}

	return $match1.",".$match2;
}

function getPayPalMatchStatus($match_status , $is_address_verified) {
	//if ($match_status) {
	if ($is_address_verified == 'Confirmed') {
		$match1 = "<img src='images/check.png' alt='Match Confirmed' />";
		$match2 = "<img src='images/check.png' alt='Match Confirmed' />";
	}
	elseif ($is_address_verified == 'None') {
		$match1 = "<img src='images/check.png' alt='None' />";
		$match2 = "<img src='images/check.png' alt='None' />";
	}
	else {
		$match1 = "<img src='images/cross.png' alt='Match UnConfirmed' />";
		$match2 = "<img src='images/cross.png' alt='Match UnConfirmed' />";
	}

	if($match_status == 2){
		$match2 = "<img src='images/cross.png' alt='No Match' />";
	}
	elseif($match_status == 3){
		$match1 = "<img src='images/cross.png' alt='No Match' />";
	}
	elseif($match_status == 4){
		$match1 = "<img src='images/cross.png' alt='No Match' />";
		$match2 = "<img src='images/cross.png' alt='No Match' />";
	}

	return $match1 ;
	/*}
	else {
		return 'NA';
	}*/
}

function getBSStatus($order_id) {
	global $db;
	$order = $db->func_query_first ( "select shipping_address_1 , payment_address_1 , shipping_postcode , payment_postcode from oc_order where order_id = '$order_id'" );

	$match = "<img src='images/cross.png' alt='No Match' />";

	$order ['shipping_address_1'] = strtolower ( trim ( $order ['shipping_address_1'] ) );
	$order ['payment_address_1'] = strtolower ( trim ( $order ['payment_address_1'] ) );

	$shippingArr = explode ( " ", trim ( $order ['shipping_address_1'] ) );
	$paymentArr = explode ( " ", $order ['payment_address_1'] );

	// print_r($shippingArr); exit;
	$order ['shipping_postcode'] = strtolower ( trim ( $order ['shipping_postcode'] ) );
	$order ['payment_postcode'] = strtolower ( trim ( $order ['payment_postcode'] ) );

	if (stristr ( $shippingArr [0], $paymentArr [0] ) && stristr ( $order ['shipping_postcode'], $order ['payment_postcode'] )) {
		$match = "<img src='images/check.png' alt='Match' />";
	}

	return $match;
}

function getOrderColor($email) {
	global $db;

	$is_banned = $db->func_query_first_cell ( "select is_banned from oc_customer where lower(email) = '$email'" );
	$is_banned = ( int ) $is_banned;
	if ($is_banned) {
		return "red";
	}
}

function getCountOrders($email) {
	global $db;

	$email = $db->func_escape_string ( $email );
	$email = strtolower ( $email );

	$count = $db->func_query_first_cell ( "select count(order_id) as total from oc_order where lower(email) = '$email'" );
	return $count;
}

function getWeeklyAverageOfReturnsBySKU($sku,$parameter='')
{
	global $db;
	$yearweek = date('YW');
	$avg = 0;
	if($parameter=='')
	{
		$parameter = ' 1 = 1 ';
	}
	for($i=10;$i>=1;$i--)
	{
		$avg += $db->func_query_first_cell("SELECT COUNT(b.sku) as count_sku FROM inv_returns a,inv_return_items b WHERE a.id=b.return_id AND b.sku='".$sku."' AND $parameter AND YEARWEEK(a.date_added)='".($yearweek-$i)."'");
	}
	return round($avg / 9);
}

function getWeeklyReturnsBySKU($sku,$parameter='')
{
	global $db;
	$year = date('Y');
	$week = date('W');
	$yearweek = date('YW');
	$avg = 0;

	if($parameter=='')
	{
		$parameter = ' 1 = 1 ';
	}

	echo '<table width="30%" cellspacing="0" cellpadding="5px" border="1" align="left" style="margin-bottom:7px">';
	for($i=10;$i>=1;$i--)
	{
		$return_week = getStartAndEndDate($week-$i,$year);
		echo '<tr>';
		echo ' <td width="50%" style="background-color:#e5e5e5;font-weight:bold">'.americanDate($return_week[0]).' - '.americanDate($return_week[1]).'</td>';
		echo '<td width="50%">'. $db->func_query_first_cell("SELECT COUNT(b.sku) as count_sku FROM inv_returns a,inv_return_items b WHERE a.id=b.return_id AND $parameter AND b.sku='".$sku."' AND YEARWEEK(a.date_added)='".($yearweek-$i)."'").'</td>';
		echo '</tr>';
	}
	echo "</table>";
	//return round($avg / 9);
}

function getStartAndEndDate($week, $year)
{

	$time = strtotime("1 January $year", time());
	$day = date('w', $time);
	$time += ((7*$week)+1-$day)*24*3600;
	$return[0] = date('d M Y', $time);
	$time += 6*24*3600;
	$return[1] = date('d M Y', $time);
	return $return;
}

function page_permission($key)
{
	if($_SESSION['user_id']!=0)
	{
		if(!$_SESSION[$key])
		{
			echo 'Permission Denied! You are not allowed to visit this module / page';exit;
		}
	}
}

function getOrder($order_id)
{
	global $db;
	$order_info = $db->func_query_first("SELECT o.*,od.* FROM inv_order o,inv_order_details od WHERE o.order_id=od.order_id AND  o.order_id='".$db->func_escape_string($order_id)."'");

	if($order_info)
	{
		$order_items = $db->func_query("SELECT * FROM inv_orders_items WHERE order_id='".$db->func_escape_string($order_info['order_id'])."'");

		$order_products = array();
		foreach($order_items as $temp)
		{
			$order_products[] = array(
				'order_item_id'	=>$temp['order_item_id'],
				'sku'	=>$temp['product_sku'],
				'price'	=>$temp['product_price']
				);
		}
	}

	return $data = array(
		'order_id'		=> 		$order_info['order_id'],
		'date_added'	=>		$order_info['order_date'],
		'total'			=>		$order_info['order_price'],
		'status'		=>		$order_info['order_status'],
		'email'			=>		$order_info['email'],
		'store_type'	=>		$order_info['store_type'],
		'firstname'		=>		$order_info['first_name'],
		'lastname'		=>		$order_info['last_name'],
		'phone'			=>		$order_info['phone_number'],
		'address1'		=>		$order_info['address1'],
		'address2'		=>		$order_info['address2'],
		'city'			=>		$order_info['city'],
		'state'			=>		$order_info['state'],
		'country'		=>		$order_info['country'],
		'zip'			=>		$order_info['zip'],
		'payment_address1'	=>		$order_info['bill_address1'],
		'payment_address2'	=>		$order_info['bill_address2'],
		'payment_city'		=>		$order_info['bill_city'],
		'payment_state'		=>		$order_info['bill_state'],
		'payment_country'	=>		$order_info['bill_country'],
		'payment_zip'		=>		$order_info['bill_zip'],
		'payment_method'	=>		$order_info['payment_method'],
		'shipping_method'	=>		$order_info['shipping_method'],
		'shipping_cost'		=>		$order_info['shipping_cost'],
		'products'			=>		$order_products


		);
}

function oc_config($key)
{
	global $db;
	return $db->func_query_first_cell("SELECT `value` FROM oc_setting WHERE `key`='".$key."' ");
}

function get_username($user_id='')
{
	global $db;
	$username = '';
	if($user_id=='') $user_id = $_SESSION['user_id'];

	if($user_id==0)
	{
		$username='Admin';
	}

	else if($user_id=='-1')
	{
		$username='Employee';
	}
	else
	{
		$username = $db->func_query_first_cell("SELECT name FROM inv_users WHERE id='".(int)$user_id."'");
	}

	return $username;
}

function get_issue_status_tag($statusx)
{
	switch($statusx)
	{
		case 'Created':
		$status = "<span class='tag blue-bg'>Created</span>";
		break;

		case 'Resolving':
		$status = "<span class='tag orange-bg'>Resolving</span>";
		break;


		case 'Fixed':
		$status = "<span class='tag green-bg'>Fixed</span>";
		break;
		case 'Completed':
		$status = "<span class='tag purple-bg'>Completed</span>";
		break;

		case 'Not Completed':
		$status = "<span class='tag black-bg'>Not Completed</span>";
		break;

		case 'Rejected':
		$status = "<span class='tag red-bg'>Rejected</span>";
		break;

		case 'Restarted':
		$status = "<span class='tag grey-bg'>Restarted</span>";
		break;
	}

	return $status;
}

function add_issue_history($issue_id,$description)
{
	global $db;
	$array = array();
	$array['issue_id'] = (int)$issue_id;
	$array['description'] = $db->func_escape_string($description);
	$array['user_id'] = $_SESSION['user_id'];
	$array['date_added'] = date("Y-m-d H:i:s");

	$db->func_array2insert("inv_issue_history",$array);
}

function addToPriceChangeReport($shipment_id,$sku,$raw_cost,$ex_rate,$shipping_fee){
	global $db;
	$array = array();
	$array['shipment_id'] = $shipment_id;
	$array['sku'] = $db->func_escape_string($sku);
	$array['raw_cost'] = $db->func_escape_string($raw_cost);
	$array['ex_rate'] = $db->func_escape_string($ex_rate);
	$array['shipping_fee'] = $db->func_escape_string($shipping_fee);
	$array['user_id'] = $_SESSION['user_id'];
	$array['date_added'] = date("Y-m-d H:i:s");
	$array['is_updated'] = 0;
	$isExist = $db->func_query_first("SELECT id FROM inv_price_change_history WHERE sku='$sku'");

	if($raw_cost>0)
	{
		if(!$isExist)
		{
			$db->func_array2insert("inv_price_change_history",$array);
		}
		else
		{
			$db->func_array2update("inv_price_change_history",$array,"sku='$sku'");
		}
	}
}

function getAvgProductCost($sku)
{
	global $db;
	$check_grade_sku = $db->func_query_first_cell("select main_sku FROM oc_product WHERE model='".$sku."' AND item_grade IN('Grade A','Grade B','Grade C')");
	if($check_grade_sku)
	{
		$sku = $check_grade_sku;	
	}
	
	$rows = $db->func_query("SELECT DISTINCT  DATE(cost_date) as cost_date,raw_cost,ex_rate,shipping_fee FROM inv_product_costs WHERE sku='".$sku."' ORDER BY id DESC limit 3");
	$avg = 0.00;
	foreach($rows as $row)
	{
		$avg=$avg + (($row['raw_cost']+$row['shipping_fee'])/$row['ex_rate']);

	}
	return $avg;
}

function getWholeSaleAvgCost($sku,$quantity)
{
	global $db;
	
	$check_grade_sku = $db->func_query_first_cell("select main_sku FROM oc_product WHERE model='".$sku."' AND item_grade IN('Grade A','Grade B','Grade C')");
	$is_grade_sku = false;
	if($check_grade_sku)
	{
		$sku = $check_grade_sku;
		$is_grade_sku = true;
	}
	
	$product_id = $db->func_query_first_cell ( "select product_id from oc_product where model = '$sku'" );
	$avg = $db->func_query_first_cell("SELECT AVG(price) FROM oc_product_discount WHERE customer_group_id=6 AND quantity=1 AND product_id='".(int)$product_id."'");
	if($is_grade_sku)
	{
		$avg = $db->func_query_first_cell("SELECT AVG(price) FROM oc_product WHERE product_id='".(int)$product_id."'");	
	}
	return $avg;
}

function getScore($mps)
{
	$score = 0;
	if($mps>=1 and $mps<=2)
	{
		$score = 1;
	}
	else if($mps>=3 and $mps<=5)
	{
		$score = 2;
	}
	else if($mps>=6 and $mps<=10)
	{
		$score = 3;
	}
	else if($mps>=11 and $mps<=20)
	{
		$score = 4;
	}
	else if($mps>=21 and $mps<=30)
	{
		$score = 5;
	}
	else if($mps>=31 and $mps<=40)
	{
		$score = 6;
	}
	else if($mps>=41 and $mps<=50)
	{
		$score = 7;
	}
	else if($mps>=51 and $mps<=60)
	{
		$score = 8;
	}
	else if($mps>=61 and $mps<=70)
	{
		$score = 9;
	}
	else if($mps>=71)
	{
		$score = 10;
	}
	else
	{
		$score = 0;
	}

	return $score;
}

function getTrueCost($sku){
	global $db;
	$true_cost = 0.00;
	$main_sku = $db->func_query_first_cell("SELECT main_sku FROM oc_product WHERE model='".$sku."'");
	if($main_sku)
	{
		$sku =$main_sku;
	}
	$cost = $db->func_query_first("SELECT  cost_date,raw_cost,ex_rate,shipping_fee FROM inv_product_costs WHERE sku='" . $sku . "' ORDER BY id DESC limit 1");
	if($cost)
	{
		$true_cost = ($cost['raw_cost'] + $cost['shipping_fee']) / $cost['ex_rate'];
		$true_cost = round($true_cost, 2);
	}
	return $true_cost;
}

function getInvoiceCost($sku, $order_id){
	global $db;

	$cost = $db->func_query_first_cell('SELECT  `product_true_cost` FROM `inv_orders_items` WHERE `order_id` = "' . $order_id . '" AND `product_sku` = "'. $sku .'"');

	if ($cost) {
		return $cost;
	}

	return FALSE;
}

function americanDate ($date) {
	return ($date != '0000-00-00 00:00:00' || $date != '')? date('m/d/y h:i A', strtotime($date)): 'N/A';
}

function linkToProduct($sku, $host_path) {
	return '<a href="' . $host_path . 'product/' . $sku . '">' . $sku . '</a>';
}

function linkToRma($rma_number, $host_path) {
	return '<a href="' . $host_path . 'return_detail.php?rma_number=' . $rma_number . '">' . $rma_number . '</a>';
}

function linkToProfile($email, $host_path) {
	global $db;
        /*$order_count = $db->func_query_first_cell("SELECT 
	count(`order_id`) as xtotal
	
	FROM
	`inv_orders` 
	WHERE `email` = '$email' 
	AND `order_status` IN (
		'Shipped',
		'On Hold',
		'Processed',
		'Store Pick Up'
		) 
");*/
$rec = $db->func_query_first("SELECT * FROM inv_customers WHERE email='".$email."'");
if ($email) {
	return '<a target="_parent" data-tooltip="'.(int)$rec['no_of_orders'].' / $'.number_format($rec['total_amount'],2).' ('.$rec['customer_group'].')'.'" href="'. $host_path . 'customer_profile.php?id=' . base64_encode($email) .'">'.$email.'</a>';
}
return FALSE;
}
function shortCodeReplace ($data, $message) {
	foreach ($data as $key => $value) {
		$message = str_replace('{{'.$key.'}}', $value, $message);
	}
	return $message;
}
function sendEmailDetails ($data = array(), $email, $smtp = array()) {
	if ($email) {
		if (!$email['image']) {
			$email['image'] = "http://phonepartsusa.com/image/data/0000png/notification.png";
		}

		$email['message'] = '<body style="background:#CCC;">
		<div style="width:700px; height:auto;margin:0 auto;  background:#35BDB2;">
			<div style="width:100%; height:100px; position:relative; background:#FFF; text-align:center">
				<a href="https://phonepartsusa.com" title="PhonePartsUSA"><img style="margin:25px 0 0 0;" src="http://phonepartsusa.com/image/data/0000png/phonepartsusalogo1-1.png" alt="PhonePartsUSA" /></a>
			</div>
			<div style="width:100%; height:auto; position:relative; margin:0; padding:20px 0 40px 0; text-align:center;">
				<h2 style="font-family:Arial, Helvetica, sans-serif; font-size:24px; color:#fff; text-align:center; text-shadow:1px 2px 0px rgba(150, 150, 150, 1);">'.$email['title'].'</h2>
				<img style="margin:20px 0 30px" src="'. $email['image'] .'" />
				<div style="text-align:center;">
					<label style="display:block; width:150px; text-align:center; color:#fff; font-size:22px; font-family:Arial, Helvetica, sans-serif; margin:0 auto; text-shadow:1px 2px 0px rgba(150, 150, 150, 1);">Order Id:</label>
					<input disabled="disabled" style="width:150px; text-align:center; color:#2b2b2b; background:#fff; border:1px solid #ccc; font-size:28px;" type="text" value="'.$data['order_id'].'" />
				</div>
			</div>
			<div style="width:100%; height:auto; position:relative; margin:0; padding:40px 0 0 0; background:#E6E4E5; text-align:center;">
				<p style="text-align:center; color:#2b2b2b; font-size:16px; font-family:Arial, Helvetica, sans-serif; width:70%; margin:0 auto;">'.str_replace('\\', '', str_replace(PHP_EOL, '<br />', $email['message'])).'</p>
				<a style="min-width:200px; border-radius:5px; text-align:center; color:#fff; background:#35BDB2; border:none; font-size:18px; margin:30px 0 0 0; cursor:pointer; padding: 18px 30px; text-decoration: none; display: inline-block;" href="http://phonepartsusa.com" target="_blank">Visit PhonePartsUSA.com</a>
				<p style="width:100%; background:#5E5E5E; color:#fff; line-height:16px; text-align:center; margin:30px 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; padding:8px 0;">
					&copy; '.date('Y').' PhonePartsUSA.com. All Rights Reserved. <br />
					5145 South Arvile St. Ste A • Las Vegas • NV 89118 USA</p>
				</div>
			</div>
		</body>';

		global $db;
		$mail = new PHPMailer();
		$mail->IsSMTP();
		$mail->CharSet = 'UTF-8';
		if ($smtp) {
			$mail->Host = $smtp['MAIL_HOST']; 
    	// SMTP server example
			$mail->SMTPDebug = 0;                     
	    // enables SMTP debug information (for testing)
			$mail->SMTPAuth = true;                  
	    // enable SMTP authentication
			$mail->Port = ($smtp['PORT'])? $smtp['port']: 25;                    
	    // set the SMTP port for the GMAIL server
			$mail->Username = $smtp['MAIL_USER']; 
	    // SMTP account username example
			$mail->Password = $smtp['MAIL_PASSWORD'];        
	    // SMTP account password example
			$mail->SetFrom($smtp['MAIL_USER'], 'PhonePartsUSA');
		} else {
			$mail->Host = MAIL_HOST; 
    	// SMTP server example
			$mail->SMTPDebug = 0;                     
	    // enables SMTP debug information (for testing)
			$mail->SMTPAuth = true;                  
	    // enable SMTP authentication
			$mail->Port = 25;                    
	    // set the SMTP port for the GMAIL server
			$mail->Username = MAIL_USER; 
	    // SMTP account username example
			$mail->Password = MAIL_PASSWORD;        
	    // SMTP account password example
			$mail->SetFrom(MAIL_USER, 'PhonePartsUSA');
		}

		$mail->addAddress($data['email'], $data['first_name']);
		$mail->Subject = $email['subject'];
		$mail->Body = $email['message'];
		$mail->IsHTML(true);

		$email = array();
		$email['customer_name'] = $data['first_name'];
		$email['customer_email'] = $data['email'];
		$email['order_id'] = $data['order_id'];
		$email['return_id'] = $data['order_id'] . 'R';
		$email['email_subject'] = $email['subject'];
		$email['email_body'] = $email['message'];
		$email['resolution'] = $email['title'];
		$email['notes'] = '';
		$email['date_sent'] = date('Y-m-d h:i:s');
		$email['sent_by'] = $_SESSION['user_id'];
		if ($mail->send()) {
			$email['is_sent'] = 1;
			$db->func_array2insert('inv_email_report', $email);
			$_SESSION['message'] = "Email sent";
		} else {
			$_SESSION['message'] = "Email Not Sent Please Try Some Other Time";
		}
	} else {
		$_SESSION['message'] = "Email Not Sent Email Templete Not Found";
	}
}

function updateProductPrice($sku,$raw_cost='',$ex_rate='',$shipping_fee='')
{
	global $db;
	$product = $db->func_query_first("SELECT product_id,sku FROM oc_product WHERE model='".$sku."'");

	if($raw_cost)
	{
		$cost['raw_cost'] =$raw_cost;
		$cost['ex_rate'] =$ex_rate;
		$cost['shipping_fee'] =$shipping_fee;

	}
	else
	{
		$cost = $db->func_query_first("SELECT  cost_date,raw_cost,ex_rate,shipping_fee FROM inv_product_costs WHERE sku='" . $product['sku'] . "' ORDER BY id DESC limit 1");		
	}
	$true_cost = ($cost['raw_cost'] + $cost['shipping_fee']) / $cost['ex_rate'];	


	$true_cost = round($true_cost, 2);
	$markup = $db->func_query_first("SELECT * FROM  inv_product_pricing WHERE  $true_cost BETWEEN COALESCE(`range_from`,$true_cost) AND COALESCE(`range_to`,$true_cost)");




	$sql = 'SELECT 
	iks.`kit_sku`, op.`price`
	FROM
	`inv_kit_skus` AS `iks`
	INNER JOIN
	`oc_product` AS `op` ON op.`sku` = iks.`kit_sku`
	WHERE
	iks.`kit_sku` = "' . $product['sku'] . 'K"
	';

	$kitSku = $db->func_query_first($sql);
                            // Setting kit sku Price if it exist;
	$kitSkuPrice = 0;
	if ($kitSku) {


		$kitSkuPrice = ($true_cost * $markup['markup_d1'])+$markup['kit_price'];

		$_temp_kit_sku = explode('.',(float)$kitSkuPrice);

		if((int)$_temp_kit_sku[1]==0)
		{
			$kitSkuPrice = $_temp_kit_sku[0].'.0000';	

		}
		else
		{

			$kitSkuPrice = $_temp_kit_sku[0].'.9500';	
		}
	}
	else
	{

		$kitSkuPrice = 0;


	}

	$markup_general = round($true_cost * $markup['markup_general'],4);
	$markup_d1 = round($true_cost * $markup['markup_d1'],4);
	$markup_d3 = round($true_cost * $markup['markup_d3'],4);
	$markup_d10 = round($true_cost * $markup['markup_d10'],4);

	$markup_l1 = round($true_cost * $markup['markup_l1'],4);
	$markup_l3 = round($true_cost * $markup['markup_l3'],4);
	$markup_l10 = round($true_cost * $markup['markup_l10'],4);

	$markup_w1 = round($true_cost * $markup['markup_w1'],4);
	$markup_w3 = round($true_cost * $markup['markup_w3'],4);
	$markup_w10 = round($true_cost * $markup['markup_w10'],4);

	$grade_a = round($true_cost * $markup['grade_a'],4);
	$grade_b = round($true_cost * $markup['grade_b'],4);
	$grade_c = round($true_cost * $markup['grade_c'],4);


	$db->db_exec("UPDATE oc_product SET price='" . (float) $markup_general . "' WHERE product_id='" . (int) $product['product_id'] . "'");

	$db->db_exec("DELETE FROM oc_product_discount WHERE product_id='" . (int) $product['product_id'] . "'");

			// Default markups
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '8' , quantity = '1' , price = '" . (float) $markup_d1 . "'");
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '8' , quantity = '3' , price = '" . (float) $markup_d3 . "'");
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '8' , quantity = '10' , price = '" . (float) $markup_d10 . "'");

			   // Local Markups
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '10' , quantity = '1' , price = '" . (float) $markup_l1 . "'");
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '10' , quantity = '3' , price = '" . (float) $markup_l3 . "'");
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '10' , quantity = '10' , price = '" . (float) $markup_l10 . "'");

			   // Wholesale markups
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '6' , quantity = '1' , price = '" . (float) $markup_w1 . "'");
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '6' , quantity = '3' , price = '" . (float) $markup_w3 . "'");
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '6' , quantity = '10' , price = '" . (float) $markup_w10 . "'");

		// Grade A markup
	$db->db_exec('UPDATE oc_product SET  `price` = "' . (float)$grade_a . '" WHERE `main_sku` = "' . $product['sku'] . '" AND `item_grade` = "Grade A"');

		 // Grade B markup
	$db->db_exec('UPDATE oc_product SET  `price` = "' . (float)$grade_b . '" WHERE `main_sku` = "' . $product['sku'] . '" AND `item_grade` = "Grade B"');

		 // Grade C markup
	$db->db_exec('UPDATE oc_product SET  `price` = "' . (float)$grade_c . '" WHERE `main_sku` = "' . $product['sku'] . '" AND `item_grade` = "Grade C"');

	if($kitSkuPrice)
	{
		 // Kit Sku markup
		$db->db_exec('UPDATE oc_product SET  `price` = "' . (float)$kitSkuPrice . '" WHERE `sku` = "' . $product['sku'] . 'K"');
	}

	
	
}