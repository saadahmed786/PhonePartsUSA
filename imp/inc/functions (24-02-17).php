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

	$_query = "select ot.product_qty,o.po_business_id from inv_orders_items ot inner join inv_orders o on (ot.order_id = o.order_id)
	where ot.product_sku = '$sku' and order_date >= '$start_date' AND order_date <= '$end_date'";
	$product_qty = 0;
	$rows = $db->func_query($_query);
	foreach($rows as $row)
	{
		if($row['po_business_id'])
		{
			$is_fba_customer = (int)$db->func_query_first_cell("SELECT is_fbb FROM inv_po_customers WHERE id='".(int)$row['po_business_id']."'");
			if($is_fba_customer)
			{
				continue;
			}
		}
		
		$product_qty+=$row['product_qty'];
	}
	//$product_qty = $db->func_query_first_cell ( $_query );
	if ($product_qty > 0) {
		$product_qty = $product_qty / $days;
	}

	return  ( $product_qty );
}

function getRop($mps, $lead_time, $qc_time, $safety_stock) {
	$rop = $mps * ($lead_time  + $safety_stock);
	return $rop;
}

function getQtyToBeShipped($rop, $qty, $mps, $lead_time, $qc_time, $addtional_days = 4,$safety_stock=0) {
	//$qtyToShipped = ($rop - $qty) + ($addtional_days * $mps) + ($mps * ($lead_time + $qc_time));
	//$qtyToShipped = ($rop - $qty)  + ($mps * ($lead_time ));
	//$qtyToShipped = ($rop - $qty)  + ($mps * ($lead_time+$safety_stock ));
	//$qtyToShipped = $qty  -  ($rop + $lead_time  );
	
	/*Order Level = (Ave Sale * Lead Time) + ( ROP - Current QTY) if ROP > Current QTY*/

	// $a1 = ($rop - ($mps*$lead_time));
	// if($a1<=0)
	// {
	// 	$a1 = 0;
	// }
	// $b1 = ($qty - $rop);
	// if($b1<=0){
	// 	$b1 = 0;
	// }

	// $qtyToShipped = ($mps*$lead_time)+($a1)-($b1);
	
	if($rop>$qty)
	{
		$qtyToShipped = ($mps * $lead_time) + ($rop-$qty);
	}
	else
	{
		$qtyToShipped = 0;
	}
	
	if ($qtyToShipped > 0) {
		return $qtyToShipped;
	}
	else {
		return 0;
	}
}
function getQtytoBeNeeded($rop,$qty,$to_be_shipped)
{
	return $rop-$qty-$to_be_shipped;
}

function getShipmentDetail($shipments, $sku, $qty_shipped) {
	if (! $shipments) {
		return;
	}


	$output = '';
	$qty = 0;
	foreach ( $shipments as $shipment ) {
		if ($shipment ['product_sku'] == $sku) {
			$output .= '<br>' . linkToShipment($shipment ['shipment_id'],'',$shipment['package_number']) . " (" . $shipment ['qty_shipped'] . ") ";
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

function logRejectItem($reject_id, $log, $from, $to) {
	global $db;
	$data['`reject_item_id`'] = $reject_id;
	$data['`log`'] = $db->func_escape_string($log);
	$data['`date_added`'] = date ( 'Y-m-d H:i:s' );
	$data['`user_id`'] = $_SESSION['user_id'];
	$data['`from`'] = $from;
	$data['`to`'] = $to;

	$db->func_array2insert ( 'inv_rj_shipment_items_log', $data );
}
function logLbbItem($sku, $log, $from, $to) {
	global $db;
	$data['`item_sku`'] = $sku;
	$data['`log`'] = $db->func_escape_string($log);
	$data['`date_added`'] = date ( 'Y-m-d H:i:s' );
	$data['`user_id`'] = $_SESSION['user_id'];
	$data['`from`'] = $from;
	$data['`to`'] = $to;

	$db->func_array2insert ( 'inv_lbb_items_log', $data );
}

function transferRJInhouse ($rejected_shipment_id, $reject_id, $shipment_id) {
	global $db;
	global $host_path;
	$inv_return_shipment_box_items = array();
	$inv_return_shipment_box_items['rejected_shipment_id'] = $rejected_shipment_id;
	$inv_return_shipment_box_items['date_updated'] = date ( 'Y-m-d H:i:s' );
	$db->func_array2update("inv_rejected_shipment_items",$inv_return_shipment_box_items,"reject_item_id = '$reject_id'");
	$from = $db->func_query_first_cell( "select package_number from inv_rejected_shipments where id = '$shipment_id'" );
	$to = $db->func_query_first_cell( "select package_number from inv_rejected_shipments where id = '$rejected_shipment_id'" );
	logRejectItem($reject_id, 'Moved to <a href="'.$host_path.'addedit_rejectedshipment.php?shipment_id='.$rejected_shipment_id.'">' . $to . '</a> from <a href="'.$host_path.'addedit_rejectedshipment.php?shipment_id='.$shipment_id.'">' . $from . '</a> by ', $from, $to);
}

function addToRejectedShipment($product_sku, $qty, $shipment_id,$reason,$prod_price=0) {
	global $db;
	$shipment_detail = $db->func_query_first("SELECT vendor, vendor_po_id FROM inv_shipments WHERE id = '$shipment_id'");
	$vendor_id = $shipment_detail['vendor'];
	$vendor_po_id = $shipment_detail['vendor_po_id'];
	$last_id = $db->func_query_first_cell ( "select id from inv_rejected_shipments where vendor = '$vendor_id' AND status = 'Pending'" );
	if (! $last_id) {
		$rejcetedShipment = array();
		$rejcetedShipment ['package_number'] = 'RTV-' . rand();
		$rejcetedShipment ['status'] = 'Pending';
		$rejcetedShipment ['date_added'] = date ( 'Y-m-d H:i:s' );
		$rejcetedShipment ['user_id'] = $_SESSION['user_id'];
		$rejcetedShipment ['vendor'] = $vendor_id;
		$last_id = $db->func_array2insert ( 'inv_rejected_shipments', $rejcetedShipment );
	}

	removeFromRejectedShipment($product_sku, $shipment_id);

	$rejcetedShipmentItem = array();
	$rejcetedShipmentItem ['qty_rejected'] = 1;

	for($i=1;$i<= (int)$qty ;$i++){
		$rejcetedShipmentItem = array();
		$rejcetedShipmentItem ['shipment_id'] = $shipment_id;
		$rejcetedShipmentItem ['product_sku'] = $product_sku;
		$rejcetedShipmentItem ['reject_reason'] = $reason;
		$rejcetedShipmentItem ['vendor_po_id'] = $vendor_po_id;
		$rejcetedShipmentItem ['qty_rejected'] = 1;
		if ($prod_price!=0) {	
		$rejcetedShipmentItem ['cost'] = $prod_price;
		}else {
		$rejcetedShipmentItem ['cost'] = getTrueCost($product_sku);	
		}
		$rejcetedShipmentItem ['rejected_shipment_id'] = $last_id;
		$rejcetedShipmentItem['reject_item_id'] = getRejectId('RJ-' . $product_sku . '-');
		$rejcetedShipmentItem['date_added'] = date ( 'Y-m-d H:i:s' );
		$rejcetedShipmentItem['date_updated'] = date ( 'Y-m-d H:i:s' );
		$db->func_array2insert ( 'inv_rejected_shipment_items', $rejcetedShipmentItem );
		logRejectItem($rejcetedShipmentItem['reject_item_id'], 'RJ ID Created');
		// logRejectItem($rejcetedShipmentItem['reject_item_id'], 'Moved to ' . $db->func_query_first_cell( "select package_number from inv_rejected_shipments where id = '$last_id'" ));
	}
	// $from = $db->func_query_first_cell( "select package_number from inv_shipments where id = '$shipment_id'" );
	// $to = $db->func_query_first_cell( "select package_number from inv_rejected_shipments where id = '$last_id'" );
	// logRejectItem($qty,' Items Added to <a href="'.$host_path.'addedit_rejectedshipment.php?shipment_id='.$last_id.'">' . $to . '</a> by ',$from,$to);
	return 1;
}

function returnMoveToRJ($reject_id, $reason_id, $last_id,$box_type='NTR') {
	global $db;	
	global $host_path;
	//$last_id = $db->func_query_first_cell ( "select id from inv_rejected_shipments where status = 'Pending'" );
	if (! $last_id) {
		$rejcetedShipment = array();
		$rejcetedShipment ['package_number'] = 'RTV-' . rand();
		$rejcetedShipment ['status'] = 'Pending';
		$rejcetedShipment ['date_added'] = date ( 'Y-m-d H:i:s' );
		$rejcetedShipment ['user_id'] = $_SESSION['user_id'];
		$last_id = $db->func_array2insert ( 'inv_rejected_shipments', $rejcetedShipment );
	}

	$item = $db->func_query_first("SELECT * FROM inv_return_shipment_box_items WHERE return_item_id = '$reject_id'");
	$from = $db->func_query_first_cell("select box_number from inv_return_shipment_boxes where id = '". $item['return_shipment_box_id'] ."'");
	$to = $db->func_query_first_cell( "select package_number from inv_rejected_shipments where id = '$last_id'" );
	//testObject
	$rejcetedShipmentItem = array();
	$rejcetedShipmentItem ['shipment_id'] = $item['shipment_id'];
	$rejcetedShipmentItem ['product_sku'] = $item['product_sku'];
	$rejcetedShipmentItem ['reject_reason'] = $reason_id;
	$rejcetedShipmentItem ['qty_rejected'] = $item['quantity'];
	$rejcetedShipmentItem ['cost'] = $item['cost'];
	$rejcetedShipmentItem ['vendor_po_id'] = $item['vendor_po_id'];
	$rejcetedShipmentItem ['rejected_shipment_id'] = $last_id;
	$rejcetedShipmentItem['reject_item_id'] = $item['return_item_id'];
	$rejcetedShipmentItem['date_added'] = date ( 'Y-m-d H:i:s' );
	$rejcetedShipmentItem['date_updated'] = date ( 'Y-m-d H:i:s' );
	$db->func_array2insert ( 'inv_rejected_shipment_items', $rejcetedShipmentItem );
	$db->db_exec ("DELETE FROM inv_return_shipment_box_items WHERE return_item_id = '$reject_id'");

	if($box_type=='NTR')
	{
		$_link1 = '<a href="'.$host_path.'boxes/need_to_repair.php">'.$from.'</a>';
		$_link2 = '<a href="'.$host_path.'addedit_rejectedshipment.php?shipment_id='.$last_id.'">'.$to.'</a>';
	}
	else if ($box_type=='RTS')
	{
		$_link2 = '<a href="'.$host_path.'addedit_rejectedshipment.php?shipment_id='.$last_id.'">'.$to.'</a>';
		$_link1 = '<a href="'.$host_path.'boxes/boxes_edit.php?box_id='.$item['return_shipment_box_id'].'&return=return_to_stock">'.$from.'</a>';
	}

	logRejectItem($rejcetedShipmentItem['reject_item_id'], 'Moved to '.$_link2.' from '. $_link1 . ' by ', $from, $to);
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

	$shipment_detail = $db->func_query_first("SELECT vendor, vendor_po_id FROM inv_shipments WHERE id = '$shipment_id'");
	$vendor_id = $shipment_detail['vendor'];
	$vendor_po_id = $shipment_detail['vendor_po_id'];

	for($i=1;$i<= (int)$qty ;$i++){
		$row = array();
		$row['return_shipment_box_id'] = $last_id;
		$row['product_sku'] = $product_sku;
		$row['quantity'] = 1;
		$row['price'] = 0.00;
		$row['cost'] = getTrueCost($product_sku);
		$row['source'] = 'manual';
		$row['reason'] = $reason;
		$row['shipment_id'] = $shipment_id;
		$row['vendor_po_id'] = $vendor_po_id;
		$row['return_item_id'] = getRejectId('NTR-'. $shipment_id . '-' . $product_sku . '-');

		$row['date_added'] = date('Y-m-d H:i:s');
		$id[] = $db->func_array2insert ( 'inv_return_shipment_box_items', $row );
		logRejectItem($rejcetedShipmentItem['return_item_id'], 'Moved to ' . $db->func_query_first_cell( "select box_number from inv_return_shipment_boxes where id = '$last_id'" ));
	}

	return implode(',', $id);
}

function removeFromNeedToRepairShipment($sku,$shipment_id)
{
	global $db;
	$db->db_exec ( "delete from inv_return_shipment_box_items where product_sku = '$sku' and shipment_id = '$shipment_id' " );
	return 1;	
	
}

function addItemToBox($reject_id , $product_sku , $shipment_id = 0, $box_type = 'NTRBox', $reason = false, $order_id = false , $rma_number = false,$cost=0.00, $from){
	global $db;
	global $host_path;

	//check if there is any box open
	$inv_return_shipment_box = $db->func_query_first("select id, box_number from inv_return_shipment_boxes where box_number LIKE '%$box_type%' and status = 'Issued'");
	$inv_return_shipment_box_id = $inv_return_shipment_box['id'];
	$inv_return_shipment_box_number = $inv_return_shipment_box['box_number'];
	if(!$inv_return_shipment_box_id){
		$return_shipment_boxes_insert = array ();
		$return_shipment_boxes_insert ['box_number'] = getReturnBoxNumber ( 0, $box_type );
		$return_shipment_boxes_insert ['box_type']   = $box_type;
		$return_shipment_boxes_insert ['date_added'] = date ( 'Y-m-d H:i:s' );
		$inv_return_shipment_box_number = $return_shipment_boxes_insert ['box_number'];
		$inv_return_shipment_box_id = $db->func_array2insert ( "inv_return_shipment_boxes", $return_shipment_boxes_insert );
	}
	
	$shipment_detail = $db->func_query_first("SELECT vendor, vendor_po_id FROM inv_shipments WHERE id = '$shipment_id'");
	$vendor_id = $shipment_detail['vendor'];
	$vendor_po_id = $shipment_detail['vendor_po_id'];

	$returns_po_item_insert = array ();
	$returns_po_item_insert ['product_sku'] = $product_sku;
	$returns_po_item_insert ['quantity'] = 1;
	$returns_po_item_insert ['price']  = 0;
	$returns_po_item_insert ['source'] = 'manual';
	$returns_po_item_insert ['return_item_id'] = $reject_id;
	$returns_po_item_insert ['shipment_id'] = $shipment_id;
	$returns_po_item_insert ['order_id']   = $order_id;
	$returns_po_item_insert ['vendor_po_id']   = $vendor_po_id;
	$returns_po_item_insert ['cost']   = (float)$cost;
	$returns_po_item_insert ['reason']     = $reason;
	$returns_po_item_insert ['rma_number'] = $rma_number;
	$returns_po_item_insert ['date_added'] = date ( 'Y-m-d H:i:s' );
	$returns_po_item_insert ['return_shipment_box_id'] = $inv_return_shipment_box_id;

	$db->func_array2insert ( "inv_return_shipment_box_items", $returns_po_item_insert );
	if($box_type=='NTRBox')
	{
		$_link = '<a href="'.$host_path.'boxes/need_to_repair.php">'.$inv_return_shipment_box_number.'</a>';
	}
	else if ($box_type=='RTSBox')
	{
		$_link = '<a href="'.$host_path.'boxes/boxes_edit.php?box_id='.$inv_return_shipment_box_id.'&return=return_to_stock">'.$inv_return_shipment_box_number.'</a>'; 
	}
	logRejectItem($reject_id, 'moved to ' .$_link.' from '. $from .' by ',$from, $inv_return_shipment_box_number);
	// logRejectItem($reject_id, 'moved to '.$box_type.,$from, $inv_return_shipment_box_number);
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
	return $inv_return_shipment_box_id;
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
			return utf8_encode($db->func_query_first_cell ( "select name from oc_product_description where product_id = ( select product_id from oc_product where sku = '$sku' limit 1)" ));
		}
		function getLeastPrice($sku,$customer_group=0)
		{
			global $db;
			$price = 0.00;
			$general_price  = 0.00;
			$discount_price = 0.00;

			$product_id = getProduct($sku,array('product_id'));
			$product_id = $product_id['product_id'];
			$general_price = getProduct($sku,array('price'));
			$general_price = 	(float)$general_price['price'];
			if($customer_group==0)
			{
				$discount_price = (float)$db->func_query_first_cell("SELECT MIN(price) FROM oc_product_discount WHERE product_id='".(int)$product_id."'");
			}
			else
			{
				$discount_price = (float)$db->func_query_first_cell("SELECT MIN(price) FROM oc_product_discount WHERE product_id='".(int)$product_id."' AND customer_group_id='".(int)$customer_group."'");	
			}
			
			if($customer_group==0)
			{
				if($discount_price<$general_price)
				{
					$price = $discount_price;	
				}
				else
				{
					$price = $general_price;
				}
			}
			else
			{
				if($discount_price)
				{
					$price = $discount_price;
				}
				else
				{
					$price = $general_price;
				}
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

		function createSKU($new_sku, $name, $desc, $price, $main_sku = '', $is_main_sku = 0, $grade = '', $image = '', $status = 0,$weight=0.0000) {
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
				$product['weight'] = (float)$weight;
				$product ['weight_class_id'] = 5;
				$product ['length_class_id'] = 3;
				$product ['is_imp_sku'] = 1;
				$product['vendor'] = 'abc';
				$product['tax_class_id'] = 11;
				$product ['status'] = $status;
				$product ['date_available'] = date ( 'Y-m-d' );
				if ($grade) {
					$product ['item_grade'] = "Grade " . $grade;
				}

				$product ['image'] = $image;
				$product ['user_id'] = $_SESSION['user_id'];
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
			elseif ($store_type == 'amazon_ca') {
				$prefix = "AMCA";
			}
			elseif ($store_type == 'amazon_mx') {
				$prefix = "AMMX";
			}
			elseif ($store_type == 'amazon_pg') {
				$prefix = "AMPG";
			}
			elseif ($store_type == 'amazon_pgca') {
				$prefix = "AMPGCA";
			}
			elseif ($store_type == 'amazon_pgmx') {
				$prefix = "AMPGMX";
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
			$prefix1 = explode('-', $prefix)[0].'-';
			// $last_number = $db->func_query_first_cell ( "select max(abs(replace(reject_item_id,'$prefix',''))) as reject_item_id from inv_rejected_shipment_items where reject_item_id LIKE '%$prefix1%'" );
			//print_r($prefix1);exit;
			if ($prefix1 == 'RJ-') {
				$column = 'reject_item_id';
				$table = 'inv_rejected_shipment_items';
			} else {
				$column = 'return_item_id';
				$table = 'inv_return_shipment_box_items';
			}
			
			$last_number = $db->func_query_first_cell ( "select $column as reject_item_id from $table where $column LIKE '%$prefix1%' ORDER BY id DESC" );
			//echo( "select $column as reject_item_id from $table where $column LIKE '%$prefix1%' ORDER BY id DESC");exit;
			//print_r($last_number);exit;
			$last_number = explode('-', $last_number);
			
			$last_number = (int) $last_number[(count($last_number) - 1)];
			//print_r($last_number);exit;			
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

			// userAgents
			$useragents = array(
				'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2228.0 Safari/537.36',
				'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.1 Safari/537.36',
				'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/41.0.2227.0 Safari/537.36',
				'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1',
				'Mozilla/5.0 (Windows NT 6.3; rv:36.0) Gecko/20100101 Firefox/36.0',
				'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10; rv:33.0) Gecko/20100101 Firefox/33.0',
				'Opera/9.80 (X11; Linux i686; Ubuntu/14.10) Presto/2.12.388 Version/12.16',
				'Opera/9.80 (Windows NT 6.0) Presto/2.12.388 Version/12.14',
				'Opera/12.80 (Windows NT 5.1; U; en) Presto/2.10.289 Version/12.02',
				'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/7046A194A',
				'Mozilla/5.0 (iPad; CPU OS 6_0 like Mac OS X) AppleWebKit/536.26 (KHTML, like Gecko) Version/6.0 Mobile/10A5355d Safari/8536.25',
				'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_6_8) AppleWebKit/537.13+ (KHTML, like Gecko) Version/5.1.7 Safari/534.57.2',
				'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.9.1a2pre) Gecko/2008073000 Shredder/3.0a2pre ThunderBrowse/3.2.1.8'
				);
			
	$ch = curl_init (); // Initialising cURL
	$options = Array(
	CURLOPT_RETURNTRANSFER => TRUE, // Setting cURL's option to return the webpage data
	CURLOPT_FOLLOWLOCATION => TRUE, // Setting cURL to follow 'location' HTTP headers
	CURLOPT_AUTOREFERER => TRUE, // Automatically set the referer where following 'location' HTTP headers
	CURLOPT_CONNECTTIMEOUT => 120, // Setting the amount of time (in seconds) before the request times out
	CURLOPT_TIMEOUT => 120, // Setting the maximum amount of time for cURL to execute queries
	CURLOPT_MAXREDIRS => 10, // Setting the maximum number of redirections to follow
	CURLOPT_COOKIEJAR => COOKIE_FILE,
	CURLOPT_COOKIEFILE=> COOKIE_FILE,
	CURLOPT_USERAGENT => $useragents[array_rand($useragents, 1)], // Setting the useragent
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
function getTrackingNo($order_id)
{
	global $db;
	$trackingNo = $db->func_query_first_cell("SELECT tracking_number FROM inv_order_shipments WHERE order_id='".$order_id."'");

	return $trackingNo;
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

	echo '<table width="100%" cellspacing="0" cellpadding="5px" border="1" align="left" style="margin-bottom:7px">';
	echo '<tr>';
	echo '<td>Date</td><td>Amt Ordered</td><td>Returned</td><td>Item Issue</td><td>C Damaged</td><td>Replacement</td><td>Credit</td><td>Refund</td>';
	echo '</tr>';
	for($i=1;$i<=10;$i++)
	{
		

		$return_week = getStartAndEndDate($week-$i,$year);
		$amtOrdered = $db->func_query_first_cell("SELECT SUM(b.product_qty) AS count_sku FROM `inv_orders` a, `inv_orders_items` b WHERE a.`order_id` = b.`order_id` AND 1 = 1 AND b.product_sku = '$sku' AND YEARWEEK(a.order_date)='".date('YW',strtotime($return_week[0]))."' ");

		$issue_replacement = $db->func_query_first_cell("SELECT COUNT(b.sku) as count_sku FROM inv_returns a,inv_return_items b, inv_orders c WHERE a.id=b.return_id AND a.order_id = c.order_id AND $parameter AND b.sku='".$sku."' AND b.`decision` = 'Issue Replacement' AND YEARWEEK(c.order_date)='".date('YW',strtotime($return_week[0]))."' ");
		$issue_credit =$db->func_query_first_cell("SELECT COUNT(b.sku) as count_sku FROM inv_returns a,inv_return_items b, inv_orders c WHERE a.id=b.return_id AND a.order_id = c.order_id AND $parameter AND b.sku='".$sku."' AND b.`decision` = 'Issue Credit' AND YEARWEEK(c.order_date)='".date('YW',strtotime($return_week[0]))."' ");
		$issue_refund = $db->func_query_first_cell("SELECT COUNT(b.sku) as count_sku FROM inv_returns a,inv_return_items b, inv_orders c WHERE a.id=b.return_id AND a.order_id = c.order_id AND $parameter AND b.sku='".$sku."' AND b.`decision` = 'Issue Refund' AND YEARWEEK(c.order_date)='".date('YW',strtotime($return_week[0]))."' ");

		echo '<tr>';
		echo ' <td width="30%" style="background-color:#e5e5e5;font-weight:bold">'.americanDate($return_week[0]).' - '.americanDate($return_week[1]).'</td>';
		echo '<td width="10%">'. (($amtOrdered == '')? '0': $amtOrdered) .'</td>';
		echo '<td width="10%">'. $db->func_query_first_cell("SELECT COUNT(b.sku) as count_sku FROM inv_returns a,inv_return_items b, inv_orders c WHERE a.id=b.return_id AND a.order_id = c.order_id AND $parameter AND b.sku='".$sku."' AND YEARWEEK(c.order_date)='".date('YW',strtotime($return_week[0]))."' ").'</td>';
		echo '<td width="10%">'. $db->func_query_first_cell("SELECT COUNT(b.sku) as count_sku FROM inv_returns a,inv_return_items b, inv_orders c WHERE a.id=b.return_id AND a.order_id = c.order_id AND $parameter AND b.sku='".$sku."' AND b.`item_condition` = 'Item Issue' AND YEARWEEK(c.order_date)='".date('YW',strtotime($return_week[0]))."' ").'</td>';
		echo '<td width="10%">'. $db->func_query_first_cell("SELECT COUNT(b.sku) as count_sku FROM inv_returns a,inv_return_items b, inv_orders c WHERE a.id=b.return_id AND a.order_id = c.order_id AND $parameter AND b.sku='".$sku."' AND b.`item_condition` = 'Customer Damage' AND YEARWEEK(c.order_date)='".date('YW',strtotime($return_week[0]))."' ").'</td>';
		echo '<td width="10%">'. ($db->func_query_first_cell("SELECT COUNT(d.sku) as count_sku FROM inv_returns a, inv_orders c,inv_return_decision d WHERE  a.order_id = c.order_id AND a.order_id=d.order_id and a.id = d.return_id and $parameter AND d.sku='".$sku."' AND d.`action` = 'Issue Replacement' AND YEARWEEK(c.order_date)='".date('YW',strtotime($return_week[0]))."'  ") + $issue_replacement).'</td>';
		echo '<td width="10%">'. ($db->func_query_first_cell("SELECT COUNT(d.sku) as count_sku FROM inv_returns a, inv_orders c,inv_return_decision d WHERE  a.order_id = c.order_id AND a.order_id=d.order_id and a.id = d.return_id and $parameter AND d.sku='".$sku."' AND d.`action` = 'Issue Credit' AND YEARWEEK(c.order_date)='".date('YW',strtotime($return_week[0]))."'  ")+ $issue_credit).'</td>';
		echo '<td width="10%">'. ($db->func_query_first_cell("SELECT COUNT(d.sku) as count_sku FROM inv_returns a, inv_orders c,inv_return_decision d WHERE  a.order_id = c.order_id AND a.order_id=d.order_id and a.id = d.return_id and $parameter AND d.sku='".$sku."' AND d.`action` = 'Issue Refund' AND YEARWEEK(c.order_date)='".date('YW',strtotime($return_week[0]))."'  ")+$issue_refund).'</td>';

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
	if(!$_SESSION[$key] || $key == false)
	{
		echo 'Permission Denied! You are not allowed to visit this module / page';exit;
	}
}

function getOrder($order_id)
{
	global $db;
	$order_info = $db->func_query_first("SELECT o.*,od.* FROM inv_orders o,inv_orders_details od WHERE o.order_id=od.order_id AND  o.order_id='".$db->func_escape_string($order_id)."'");

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

function get_username($user_id, $ppusa = false)
{
	global $db;
	if ($ppusa) {
		$username = $db->func_query_first_cell("SELECT CONCAT(firstname, ' ', lastname) FROM oc_user WHERE user_id='".(int)$user_id."'");
	} else {
		$username = $db->func_query_first_cell("SELECT name FROM inv_users WHERE id='".(int)$user_id."'");
	}

	return $username;
}

function get_userdetail($user_id, $detail, $ppusa = false)
{
	global $db;
	if ($ppusa) {
		$username = $db->func_query_first_cell("SELECT $detail FROM oc_user WHERE user_id='".(int)$user_id."'");
	} else {
		$username = $db->func_query_first_cell("SELECT $detail FROM inv_users WHERE id='".(int)$user_id."'");
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

	if ($raw_cost > 0) {
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

function getAvgProductCost($sku, $date = '')
{
	global $db;
	$date = ($date) ? "AND cost_date < '$date' OR cost_date = '$date'": '';
	$check_grade_sku = $db->func_query_first_cell("select main_sku FROM oc_product WHERE model='".$sku."' AND item_grade IN('Grade A','Grade B','Grade C')");
	if($check_grade_sku)
	{
		$sku = $check_grade_sku;	
	}
	// echo "SELECT DISTINCT  DATE(cost_date) as cost_date,raw_cost,ex_rate,shipping_fee FROM inv_product_costs WHERE sku='".$sku."' $date ORDER BY id DESC limit 3";exit;
	$rows = $db->func_query("SELECT DISTINCT  DATE(cost_date) as cost_date,raw_cost,ex_rate,shipping_fee FROM inv_product_costs WHERE sku='".$sku."' $date ORDER BY id DESC limit 1");
	$avg = 0.00;
	$i=0;
	foreach($rows as $row)
	{
		$avg=$avg + (($row['raw_cost']+$row['shipping_fee'])/$row['ex_rate']);

		$i++;
	}
	if($i==0) $i = 1;

	return ($avg/$i);
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
	return ($date != '0000-00-00 00:00:00' && $date != '')? date('m/d/y h:i A', strtotime($date)): 'N/A';
}

function linkToProduct($sku, $host_path = '', $extra = '', $name) {
	return '<a href="' . $host_path . 'product/' . $sku . '" '. $extra .'>' . (($name)?$name:$sku) . '</a>';
}
function linkToProductPPUSA($sku, $id, $host_path = '', $extra = '', $name) {
	$href = ($host_path)? 'href="' . str_replace('imp/', '', $host_path) . 'index.php?route=product/product&product_id=' . $id . '"' : '';
	if ($href) {
		return '<a '. $href .' '. $extra .'>' . (($name)?$name:$sku) . '</a>';
	} else {
		return '<span '. $extra .'>' . (($name)?$name:$sku) . '</span>';
	}
}
function linkToShipment($shipment_id, $host_path = '', $tracking = '', $extra = '') {
	if ($shipment_id) {
		$r = '<a ' . $extra .' href="' . $host_path . 'view_shipment.php?shipment_id=' . $shipment_id . '">' . (($tracking)? $tracking: $shipment_id) . '</a>';
	} else {
		$r = 'N/A';
	}
	return $r;
}

function linkToVPO($vpo_id, $host_path = '', $vendor_po_id, $extra = '') {
	if ($vpo_id) {
		$r = '<a ' . $extra .' href="' . $host_path . 'vendor_po_view.php?vpo_id=' . $vpo_id . '">' . (($vendor_po_id)? $vendor_po_id: $vpo_id) . '</a>';
	} else {
		$r = 'N/A';
	}
	return $r;
}

function linkToLbbShipment($shipment_id, $host_path = '', $extra = '') {
	return '<a href="' . $host_path . 'buyback/shipment_detail.php?shipment=' . $shipment_id . '">' . $shipment_id . '</a>';
}

function linkToComplaint($complaint_id, $host_path = '', $extra = '') {
	return '<a href="' . $host_path . 'issues_complaint_view.php?id=' . $complaint_id . '">' . $complaint_id . '</a>';
}

function linkToOrder($order_id, $host_path = '', $extra = '') {
	global $db;
	$return  = 'N/A';
	if ($order_id) {
		$prefix = $db->func_query_first_cell("SELECT prefix FROM inv_orders WHERE order_id='".$order_id."'");
		$return = '<a href="' . $host_path . 'viewOrderDetail.php?order=' . $order_id . '" '.$extra.'>' . $prefix.$order_id . '</a>';
	}
	return $return;
}

function linkToRma($rma_number, $host_path = '', $extra = '') {
	$return = 'N/A';
	if ($rma_number) {
		$return = '<a href="' . $host_path . 'return_detail.php?rma_number=' . $rma_number . '">' . $rma_number . '</a>';
	}
	return $return;
}

function linkToVoucher($voucher_number, $host_path = '', $code = '', $extra = '') {
	return '<a href="' . $host_path . 'vouchers_create.php?edit=' . $voucher_number . '">' . (($code)? $code: $voucher_number) . '</a>';
}

function linkToProfile($email, $host_path = '', $extra = '') {
	global $db;

	$email = trim($email); 
	$customer_email ='';

	if (strpos($email, '@marketplace.amazon.')) {
		return $email;
	} else {
		$rec = $db->func_query_first("SELECT id,no_of_orders,total_amount,customer_group FROM inv_customers WHERE TRIM(email)='".$email."'");
		if($rec)
		{
			$customer_id ='PPC-'.$rec['id'];
			$no_of_orders = $rec['no_of_orders'];
			$total_amount = $rec['total_amount'];
			$customer_group = $rec['customer_group'];
		}



		if(!$rec)
		{
			$rec = $db->func_query_first("SELECT id FROM inv_po_customers WHERE TRIM(email)='".$email."'");

			if($rec)
			{
				$customer_id ='POC-'.$rec['id'];
				$no_of_orders = 0;
				$total_amount = 0;
				$customer_group = 'PO Customer';
			}
		}

		if(!$rec)
		{
			$rec = $db->func_query_first("SELECT buyback_id,COUNT(*) as no_of_orders,SUM(total) as total_amount FROM oc_buyback WHERE TRIM(email)='".$email."' GROUP BY email");

			if($rec)
			{
				$customer_id ='BBC-'.$rec['buyback_id'];
				$no_of_orders = $rec['no_of_orders'];
				$total_amount = $rec['total_amount'];
				$customer_group = 'LBB Customer';
			}
		}

		if(!$rec)
		{
			$rec = $db->func_query_first("SELECT buyback_id,COUNT(*) as no_of_orders,SUM(total) as total_amount FROM oc_buyback WHERE TRIM(email)='".$email."' GROUP BY email");

			if($rec)
			{
				$customer_id ='BBC-'.$rec['buyback_id'];
				$no_of_orders = $rec['no_of_orders'];
				$total_amount = $rec['total_amount'];
				$customer_group = 'LBB Customer';
			}
		}

		if(!$rec)
		{
			$rec = $db->func_query_first("SELECT 0,COUNT(*) as no_of_orders,SUM(total) as total_amount,customer_group_id FROM oc_order WHERE TRIM(email)='".$email."' GROUP BY email");

			if($rec)
			{
				$customer_id ='0';
				$no_of_orders = $rec['no_of_orders'];
				$total_amount = $rec['total_amount'];
				$customer_group = $db->func_query_first_cell("SELECT name FROM oc_customer_group WHERE customer_group_id='".$rec['customer_group_id']."'");
				$customer_email = $email;
			}
		}

		if(!$rec)
		{
			$rec = $db->func_query_first("SELECT 0,COUNT(*) as no_of_orders,SUM(order_price) as total_amount FROM inv_orders WHERE TRIM(email)='".$email."' GROUP BY email");

			if($rec)
			{
				$customer_id ='0';
				$no_of_orders = $rec['no_of_orders'];
				$total_amount = $rec['total_amount'];
				$customer_group = 'Guest';
				$customer_email = $email;
			}
		}


		$link =  $host_path . 'customer_profile.php?id=' . $customer_id ;
		if($customer_email)
		{
			$link.='&email='.base64_encode($customer_email);
		}




		return '<a target="_parent" data-tooltip="'.(int)$no_of_orders.' / $'.number_format($total_amount,2).' ('.$customer_group.')'.'" href="'.$link.'">'.$email.'</a>';

	}
	return FALSE;
}
function shortCodeReplace ($data, $message) {
	foreach ($data as $key => $value) {
		$message = str_replace('{{'.$key.'}}', $value, $message);
	}
	return $message;
}

function sendEmail($name, $email, $subject, $message, $sentfrom = array()) {
	$mail = new PHPMailer();
	$mail->IsSMTP();
	$mail->CharSet = 'UTF-8';
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
	$mail->SetFrom((($sentfrom['email'])?$sentfrom['email']: MAIL_USER), (($sentfrom['name'])?$sentfrom['name']: 'PhonePartsUSA'));

	$mail->addAddress($email, $name);
	$mail->Subject = $subject;
	$mail->Body = $message;

	$mail->IsHTML(true);
	if ($mail->send()) {
		$_SESSION['message'] = "Email sent";
	} else {
		$_SESSION['message'] = "Email Not Sent Please Try Some Other Time";
	}
}

function sendEmailDetails ($data = array(), $email, $smtp = array(),$attachment='', $attachmentname = '') {
	
	if ($email) {
		if (!$email['image']) {
			//$email['image'] = "http://phonepartsusa.com/image/data/0000png/notification.png";
			$email['image'] = 'https://phonepartsusa.com/admin/view/image/return-received.jpg';
		}
		if(!strpos($email['image'], 'phonepartsusa.com'))
		{
			$email['image'] = 'https://phonepartsusa.com/admin/view/image/return-received.jpg';
		}
		$numberTitle = ($email['number'])? $email['number']['title']: 'Order ID';
		$numberValue = ($email['number'])? $email['number']['value']: $data['order_id'];
	/*	$email['message'] = '<body style="background:#CCC;">
		<div style="width:700px; height:auto;margin:0 auto;  background:#35BDB2;">
			<div style="width:100%; height:100px; position:relative; background:#FFF; text-align:center">
				<a href="https://phonepartsusa.com" title="PhonePartsUSA"><img style="margin:25px 0 0 0;" src="http://phonepartsusa.com/image/data/0000png/phonepartsusalogo1-1.png" alt="PhonePartsUSA" /></a>
			</div>
			<div style="width:100%; height:auto; position:relative; margin:0; padding:20px 0 40px 0; text-align:center;">
				<h2 style="font-family:Arial, Helvetica, sans-serif; font-size:24px; color:#fff; text-align:center; text-shadow:1px 2px 0px rgba(150, 150, 150, 1);">'.$email['title'].'</h2>
				<img style="margin:20px 0 30px" src="'. $email['image'] .'" />
				<div style="text-align:center;">
					<label style="display:block; width:150px; text-align:center; color:#fff; font-size:22px; font-family:Arial, Helvetica, sans-serif; margin:0 auto; text-shadow:1px 2px 0px rgba(150, 150, 150, 1);">'. $numberTitle .':</label>
					<input disabled="disabled" style="width:150px; text-align:center; color:#2b2b2b; background:#fff; border:1px solid #ccc; font-size:28px;" type="text" value="'. $numberValue .'" />
				</div>
			</div>
			<div style="width:100%; height:auto; position:relative; margin:0; padding:40px 0 0 0; background:#E6E4E5; text-align:center;">
				<p style="text-align:center; color:#2b2b2b; font-size:16px; font-family:Arial, Helvetica, sans-serif; width:70%; margin:0 auto;">'.str_replace('\\', '', str_replace(PHP_EOL, '', $email['message'])).'</p>
				<a style="min-width:200px; border-radius:5px; text-align:center; color:#fff; background:#35BDB2; border:none; font-size:18px; margin:30px 0 0 0; cursor:pointer; padding: 18px 30px; text-decoration: none; display: inline-block;" href="http://phonepartsusa.com" target="_blank">Visit PhonePartsUSA.com</a>
				<p style="width:100%; background:#5E5E5E; color:#fff; line-height:16px; text-align:center; margin:30px 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:12px; padding:8px 0;">
					&copy; '.date('Y').' PhonePartsUSA.com. All Rights Reserved. <br />
					5145 South Arville St. Suite A - Las Vegas - NV 89118 USA</p>
				</div>
			</div>
		</body>';*/
		$email['message'] = str_replace('<p>','',$email['message']);
		$email['message'] = str_replace('</p>','<br><br>',$email['message']);
		
		$email['message'] = '<body style="background:#ebebeb;font-family: Arial, Helvetica, sans-serif;">
		<div class="container" style="width:544px; margin:0 auto;">

			<!-- Start header -->
			<div class="header">
				<div class="hading-holder" style="margin:0; background:#24241e; padding:23px 0;">
					<h1 style="margin:0 0 0 22px;"><a href="https://phonepartsusa.com" title="PhonePartsUSA"><img src="http://phonepartsusa.com/image/data/0000png/phonepartsusalogo1-1.png" alt="PhonePartsUSA" /></a></h1>
				</div>
			</div>
			<!-- End header -->

			<!-- Start Centerbody -->
			<div class="center-body" style="background:#f6f6f7; padding:28px 67px 31px 77px; text-align:center;">
				<h2 style="font-size:25px; color:#5d5d51; margin:0 0 27px;">'.$email['title'].'</h2>
				<span class="msg-img" style="display:block; margin:0 0 40px;"><img src="'.$email['image'].'" alt="Order Status"></span>
				<strong class="code-x" style="font-size:18px; color:#5d5d51; font-weight:500"><strong style="font-weight:700">'. $numberTitle .': ' . $numberValue . '</strong></strong>
			</div>
			<!-- End Centerbody -->

			<!-- Start Footer -->
			<div class="footer" style="padding:37px 97px 57px 97px; background:#fff; text-align:center;">
				<p style="font-size:12px; color:#5d5d51; margin:0 0 74px;text-align:left">'.str_replace('\\', '', str_replace(PHP_EOL, '', $email['message'])).'
					<a class="card-btn" style=" margin: 0 auto;padding:9px 0; width:236px; text-align:center; font-size:24px; background:#7fbe56; display:inline-block; color:#fff;text-decoration:none" href="https://phonepartsusa.com">Visit PPUSA</a>
				</div>
				<!-- End Footer -->
				<div class="footer-p" style=" padding:33px 115px; text-align:center;"><p style="margin:0; font-size:12px; color:#b7b7b7;">&copy; ' . date('Y') . ' PhonePartsUSA.com. All Rights Reserved. <br />5145 South Arville St. Suite A &bull; Las Vegas &bull; NV 89118 USA</p></div>
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

		$mail->addAddress($data['email'], $data['customer_name']);
		$mail->Subject = $email['subject'];
		$mail->Body = $email['message'];

		$mail->IsHTML(true);
		if($attachment){
			$mail->addAttachment($attachment, ($attachmentname)? $attachmentname: 'PDF Attachment');
		}

		$dataEmail = array();
		$dataEmail['customer_name'] = $data['customer_name'];
		$dataEmail['customer_email'] = $data['email'];
		$dataEmail['order_id'] = $data['order_id'];
		$dataEmail['return_id'] = $data['order_id'] . 'R';
		$dataEmail['email_subject'] = $email['subject'];
		$dataEmail['email_body'] = $db->func_escape_string($email['message']);
		$dataEmail['resolution'] = $email['title'];
		$dataEmail['notes'] = '';
		$dataEmail['date_sent'] = date('Y-m-d h:i:s');
		$dataEmail['sent_by'] = $_SESSION['user_id'];
		$dataEmail['comment'] = $email['message'];
		$dataEmail['title'] = $email['title'];

		if ($mail->send()) {
			$dataEmail['is_sent'] = 1;
			$db->func_array2insert('inv_email_report', $dataEmail);
			$_SESSION['message'] = "Email sent";
			return true;
		} else {
			$_SESSION['message'] = "Email Not Sent Please Try Some Other Time";
			return false;
		}
	} else {
		$_SESSION['message'] = "Email Not Sent Email Templete Not Found";
		return false;
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
	$markup_special = round($true_cost * $markup['markup_special'],4);
	$markup_d1 = round($true_cost * $markup['markup_d1'],4);
	$markup_d3 = round($true_cost * $markup['markup_d3'],4);
	$markup_d10 = round($true_cost * $markup['markup_d10'],4);

	$markup_l1 = round($true_cost * $markup['markup_l1'],4);
	$markup_l3 = round($true_cost * $markup['markup_l3'],4);
	$markup_l10 = round($true_cost * $markup['markup_l10'],4);

	$markup_w1 = round($true_cost * $markup['markup_w1'],4);
	$markup_w3 = round($true_cost * $markup['markup_w3'],4);
	$markup_w10 = round($true_cost * $markup['markup_w10'],4);
	
	
	$markup_silver1 = round($true_cost * $markup['markup_silver1'],4);
	$markup_silver3 = round($true_cost * $markup['markup_silver3'],4);
	$markup_silver10 = round($true_cost * $markup['markup_silver10'],4);
	
	$markup_gold1 = round($true_cost * $markup['markup_gold1'],4);
	$markup_gold3 = round($true_cost * $markup['markup_gold3'],4);
	$markup_gold10 = round($true_cost * $markup['markup_gold10'],4);
	
	$markup_platinum1 = round($true_cost * $markup['markup_platinum1'],4);
	$markup_platinum3 = round($true_cost * $markup['markup_platinum3'],4);
	$markup_platinum10 = round($true_cost * $markup['markup_platinum10'],4);
	
	$markup_diamond1 = round($true_cost * $markup['markup_diamond1'],4);
	$markup_diamond3 = round($true_cost * $markup['markup_diamond3'],4);
	$markup_diamond10 = round($true_cost * $markup['markup_diamond10'],4);
	
	

	$grade_a = round($true_cost * $markup['grade_a'],4);
	$grade_b = round($true_cost * $markup['grade_b'],4);
	$grade_c = round($true_cost * $markup['grade_c'],4);


	$db->db_exec("UPDATE oc_product SET price='" . (float) $markup_general . "',special_price='".(float)$markup_special."' WHERE product_id='" . (int) $product['product_id'] . "'");

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
	
	
	   // Silver markups
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '1631' , quantity = '1' , price = '" . (float) $markup_silver1 . "'");
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '1631' , quantity = '3' , price = '" . (float) $markup_silver3 . "'");
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '1631' , quantity = '10' , price = '" . (float) $markup_silver10 . "'");
	
	
	
	   // Gold markups
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '1632' , quantity = '1' , price = '" . (float) $markup_gold1 . "'");
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '1632' , quantity = '3' , price = '" . (float) $markup_gold3 . "'");
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '1632' , quantity = '10' , price = '" . (float) $markup_gold10 . "'");
	
	
	   // Platinum markups
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '1633' , quantity = '1' , price = '" . (float) $markup_platinum1 . "'");
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '1633' , quantity = '3' , price = '" . (float) $markup_platinum3 . "'");
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '1633' , quantity = '10' , price = '" . (float) $markup_platinum10 . "'");
	
	   // Diamond markups
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '1634' , quantity = '1' , price = '" . (float) $markup_diamond1 . "'");
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '1634' , quantity = '3' , price = '" . (float) $markup_diamond3 . "'");
	$db->db_exec("insert into oc_product_discount SET priority = 0 , product_id = '" . (int) $product['product_id'] . "' , customer_group_id = '1634' , quantity = '10' , price = '" . (float) $markup_diamond10 . "'");
	
	
	
	
	
	
	

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
function getCategories($parent_id = 0) {
	
	global $db;

	$category_data = array();

	$query = $db->func_query("SELECT * FROM oc_category c LEFT JOIN oc_category_description cd ON (c.category_id = cd.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '1' ORDER BY c.sort_order, cd.name ASC");

	foreach ($query as $result) {
		$category_data[] = array(
			'category_id' => $result['category_id'],
			'name'        => getPath($result['category_id'], 1),
			'status'  	  => $result['status'],
			'sort_order'  => $result['sort_order']
			);

		$category_data = array_merge($category_data, getCategories($result['category_id']));
	}	
	


	return $category_data;
}

function getPath($category_id) {
	global $db;
	$query = $db->func_query_first("SELECT name, parent_id FROM oc_category c LEFT JOIN oc_category_description cd ON (c.category_id = cd.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '1' ORDER BY c.sort_order, cd.name ASC");

	if ($query['parent_id']) {
		return getPath($query['parent_id'], 1) . ' > ' . $query['name'];
	} else {
		return $query['name'];
	}
}
function whiteList($order, $hold = 0, $array = 0) {
	global $db;
	global $host_path;
	global $path;
	$check = 0;
	$checkArr = array();
	$customer = 0;
	$blackListCustomer = 0;
	$address = getfristNumaricChar($order['address1']);
	$bill_address = getfristNumaricChar($order['bill_address1']);

	if ($db->func_query_first_cell('SELECT `white_list` FROM inv_customers WHERE LOWER(email) = "'. trim(strtolower($order['email'])) .'"')) {
		$customer = 1;
	} else if ($db->func_query_first_cell('SELECT `id` FROM inv_po_customers WHERE LOWER(email) = "'. trim(strtolower($order['email'])) .'"')) {
		$customer = 1;
	}

	if ($customer == 0) {
		$blackListCustomer = (int)$db->func_query_first_cell('SELECT COUNT(email) FROM inv_chargeback WHERE lower(email) = "'. trim(strtolower($order['email'])) .'" OR (street_name = "' . $address . '" AND zipcode = "'. substr($order['zip'], 0, 5) .'" AND street_name <> "" AND zipcode <> "")');
	}
	$total_orders = (int)$db->func_query_first_cell('SELECT COUNT(order_id) FROM inv_orders WHERE lower(email) = "'. trim(strtolower($order['email'])) .'" AND lower(order_status) = "shipped"');
	#Disable Big Commerce Bydefault Whitlist;
	if ($customer == 1) {
		$check = 4;
		$checkArr[0] = 'White listed';
		$checkArr[1] = 'White listed';
		$checkArr[2] = 'White listed';
		// $checkArr[3] = 'White listed';
	}

	if ($total_orders>=10) {
		$check = 4;
		$checkArr[0] = '10+ Orders';
		$checkArr[1] = '10+ Orders';
		$checkArr[2] = '10+ Orders';
		// $checkArr[3] = '10+ Orders';
	}

	if ($order['store_type'] == 'ebay' || $order['store_type'] == 'rakuten' || $order['store_type'] == 'newegg' || $order['store_type'] == 'newsears' || $order['store_type'] == 'opensky' || $order['store_type'] == 'wish' || $order['store_type'] == 'po_business' || strpos(trim(strtolower($order['email'])), '@marketplace.amazon.')) {
		$check = 4;
		$checkArr[0] = 'Authentic Store ' . $order['store_type'];
		$checkArr[1] = 'Authentic Store ' . $order['store_type'];
		$checkArr[2] = 'Authentic Store ' . $order['store_type'];
		// $checkArr[3] = 'Authentic Store ' . $order['store_type'];
	}

	if (strpos(trim(strtolower($order['payment_method'])), 'in store') || strpos(trim(strtolower($order['payment_method'])), 'cash') || strtolower($order['payment_method']) ==  'free checkout') {
		$check = 4;
		$checkArr[0] = 'Authentic Payment ' . $order['payment_method'];
		$checkArr[1] = 'Authentic Payment ' . $order['payment_method'];
		$checkArr[2] = 'Authentic Payment ' . $order['payment_method'];
		// $checkArr[3] = 'Authentic Payment ' . $order['payment_method'];
	}

	if (trim(strtolower($order['shipping_method']))=='local las vegas store pickup - 9:30am-4:30pm') {
		$check = 4;
		$checkArr[0] = 'Authentic Shipping Source ' . $order['shipping_method'];
		$checkArr[1] = 'Authentic Shipping Source ' . $order['shipping_method'];
		$checkArr[2] = 'Authentic Shipping Source ' . $order['shipping_method'];
		// $checkArr[3] = 'Authentic Shipping Source ' . $order['shipping_method'];
	}

	if ($order['ss_valid'] == 1) {
		$check = 4;
		$checkArr[0] = 'Order allowed Once';
		$checkArr[1] = 'Order allowed Once';
		$checkArr[2] = 'Order allowed Once';
		// $checkArr[3] = 'Order allowed Once';
	}

	if ($order['payment_method'] == 'Replacement') {
		$check = 4;
		$checkArr[0] = 'Replacement Order';
		$checkArr[1] = 'Replacement Order';
		$checkArr[2] = 'Replacement Order';
		// $checkArr[3] = 'Replacement Order';
	}

	if ($blackListCustomer && !$order['ss_valid']) {
		$check = 0;
		$checkArr[0] = 'Black List Customer';
		$checkArr[1] = 'Black List Customer';
		$checkArr[2] = 'Black List Customer';
		// $checkArr[3] = 'Black List Customer';
	}

	if (!$blackListCustomer && $check == 0) {

		if ($address==$bill_address && $address != '' && substr($order['zip'], 0, 5)==substr($order['bill_zip'], 0, 5) ) {
			$check += 1;
			$checkArr[0] = 'Address Confirmed';
		} else {
			$checkArr[0] = 'Address Not Confirmed';
		}
		// if ($order['payment_source'] == 'Auth.net' && ($order['avs_code'] == 'Y' || $order['avs_code'] == 'X')) {
		// 	$check += 2;
		// 	$checkArr[1] = 'Auth.net Code Confirmed';
		// 	$checkArr[2] = 'Auth.net Code Confirmed';
		// } else if ($check != 3) {
		// 	$checkArr[1] = 'Auth.net Code Not Confirmed';
		// 	$checkArr[2] = 'Auth.net Code Not Confirmed';
		// }
		if ($order['payment_source'] == 'PayPal' || $order['payment_method'] == 'Paypal Express' || $order['payment_method'] == 'PayPal') {
			if (($order['is_address_verified'] == 'None' || $order['is_address_verified'] == 'Confirmed') && ($order['match_status'] != 4 || $order['match_status'] != 3 || $order['match_status'] != 2)) {
				$check += 2;
				$checkArr[1] = 'PP Code Confirmed';
				$checkArr[2] = 'PP Address Confirmed';
			} else if (!$order['paypal_updated']) {
				$checkArr[1] = 'PP Verification Pending';
				$checkArr[2] = 'PP Verification Pending';
			} else if ($check != 4) {
				$checkArr[1] = 'PP Code Not Confirmed';
				$checkArr[2] = 'PP Address Not Confirmed';
			}
		}
		if($order['payment_source'] == 'Payflow') {
			if ($order['is_address_verified'] == 'Confirmed' && $order['avs_code'] == 'Y') {
				$check += 2;
				$checkArr[1] = 'PF Code Confirmed';
				$checkArr[2] = 'PF Address Confirmed';
			} else if ($check != 4) {
				$checkArr[1] = 'PF Code Not Confirmed';
				$checkArr[2] = 'PF Address Not Confirmed';
			}
		}

		$ship_meth = trim(strtolower($order['shipping_method']));
		if (($ship_meth == 'usps first class' || $ship_meth == 'usps priority mail' || $ship_meth == 'ups/fedex ground' || $ship_meth == 'per item shipping rate') && $order['order_price'] < 30.00 && $order['store_type']!='bigcommerce') {
			$check += 1;
			$checkArr[3] = 'Economy Shipment With Under $30';
		} else if ($order['order_price'] > 30.00) {
			$check += 1;
			$checkArr[3] = 'Shipment For Above $30';
		} else {
			$checkArr[3] = 'Un-Matched Shipment Methd with Order Total - ' . $ship_meth;
		}
	}

	if ($check >= 4 && $hold == 1) {
		$db->db_exec("UPDATE inv_orders SET is_order_verified='1' WHERE order_id='".$order['order_id']."'");
	}

	if ($check >= 4 && $hold == 1 && $order['first_order_verification'] == 1 && $order['order_status'] == 'On Hold') {
		$db->db_exec("UPDATE inv_orders SET order_status='Processed' WHERE order_id='".$order['order_id']."'");
		$db->db_exec("UPDATE oc_order SET order_status_id='15' WHERE cast(`order_id` as char(50))='".$order['order_id']."' OR ref_order_id='".$order['order_id']."'");
		$addcomment = array();
		$addcomment['date_added'] = date('Y-m-d H:i:s');
		$addcomment['user_id'] = '43';
		$addcomment['comment'] = 'Order is Set to Processed VIA Verification Process';
		$addcomment['order_id'] = $order['order_id'];
		$order_history_id = $db->func_array2insert("oc_order_history", $addcomment);
	}
	if ($check < 4 && $hold == 1 && $order['first_order_verification'] == 0) {

		require_once $path . 'phpmailer/class.smtp.php';
		require_once $path . 'phpmailer/class.phpmailer.php';
		
		if(strtolower($order['order_status'])!='shipped' && strtolower($order['order_status'])!='canceled' && strtolower($order['order_status'])!='cancelled' && strtolower($order['order_status'])!='voided' && strtolower($order['order_status'])!='completed' && strtolower($order['order_status'])!='declined'  )
		{



			$post = [
			"description" => 'Order on Hold',
			"subject" => 'Order #'. $order['order_id'] .' is set to On Hold',
			"email" => $order['email'],
			"name" => $order['customer_name'],
			"priority" => 1,
			"status" => 2,
			"action"=>'create'
			];

			$ch = curl_init($host_path . 'freshdesk/create_ticket.php?config=1');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post);

		// execute!
		// Commented for Card 134 Requirement by gohar
		//$response = curl_exec($ch);

		// close the connection, release resources used
			curl_close($ch);
			$db->db_exec("UPDATE inv_orders SET order_status='On Hold', first_order_verification = '1' WHERE order_id='".$order['order_id']."'");
			$db->db_exec("UPDATE oc_order SET order_status_id='21' WHERE cast(`order_id` as char(50))='".$order['order_id']."' OR ref_order_id='".$order['order_id']."'");
			$canned_message =$db->func_query_first('SELECT * FROM inv_canned_message WHERE `type` = "On Hold";');
			$rep_message = shortCodeReplace ($order, $canned_message['message']);
			$rep_subject = shortCodeReplace ($order, $canned_message['subject']);
			$dataEmail = array();
			$dataEmail['customer_name'] = $order['customer_name'];
		// $dataEmail['email'] = $order['email'];
			$dataEmail['email'] = 'xaman.riaz@gmail.com';
			$dataEmail['order_id'] = $order['order_id'];
			$emailer = array();
			$emailer['subject'] = $rep_subject;
			$emailer['message'] = $rep_message;
			$emailer['image'] = 'https://phonepartsusa.com/imp/files/canned_' . $canned_message['canned_message_id'] . '.png';
			$emailer['title'] = shortCodeReplace ($order, $canned_message['title']);;
			//sendEmailDetails($dataEmail,$emailer);
			$addcomment = array();
			$addcomment['date_added'] = date('Y-m-d H:i:s');
			$addcomment['user_id'] = '43';
			$addcomment['comment'] = 'Order is Set to On Hold VIA Verification Process';
			foreach ($checkArr as $key => $value) {
				$addcomment['comment'] .= '<br> ' . $value;
			}
			$addcomment['order_id'] = $order['order_id'];
			$order_history_id = $db->func_array2insert("oc_order_history", $addcomment);
		}


	}
	$checkArr['check'] = $check;
	if ($array) {
		return $checkArr;
	} else {
		return $check;
	}

}

function getAdmin($id) {
	global $db;
	if ($id != 0) {
		$user = ucfirst($db->func_query_first_cell('SELECT `name` FROM `inv_users` WHERE `id` = "'. $id .'";'));
	} else {
		$user = 'Admin';
	}
	return $user;
}
function logIP($user_id)
{
	global $db;
	$data = array();
	$data['user_id'] = $user_id;
	$data['ip_address'] = $_SERVER['REMOTE_ADDR'];
	$data['extra_details'] = $_SERVER['HTTP_USER_AGENT'];
	$data['login_time'] = date('Y-m-d H:i:s');
	$db->func_array2insert ( 'inv_login_logs', $data );


}

function changeImageName($data) {

   $string = str_replace(' ', '-', $data); // Replaces all spaces with hyphens.

	return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

}

function changeNameCatalog($data) {

   $string = str_replace(' ', '-', $data); // Replaces all spaces with hyphens.
   $string = preg_replace('/[^A-Za-z0-9\-\/&,\"\'.]/', '', $string); // Removes special chars.
   return str_replace("-", " ", $string);

}

function makeImgDir($sku, $path) {
	$asku = explode('-', $sku);
	$path = str_replace('/imp', '', $path) .'image/data/';
	$dir = $path;
	if (count($asku) == 3) {
		$dir = $asku[0] . '-' . $asku[1];
		if (!file_exists($path . $dir)) {
			mkdir($path . $dir, 0777, true);
		}
		$dir = $asku[0] . '-' . $asku[1] . '/' . $sku;
		if (!file_exists($path . $dir)) {
			mkdir($path . $dir, 0777, true);
		}
	} else {
		$dir = $sku;
		if (!file_exists($path . $dir)) {
			mkdir($path . $dir, 0777, true);
		}
	}
	return $dir . '/';
}

function noImage($name, $host_path, $path) {
	$path = str_replace('/imp', '', $path);
	if (file_exists($path . 'image/' . $name)) {
		return str_replace('/imp', '', $host_path) . 'image/' . $name;
	} else {
		return str_replace('/imp', '', $host_path) . 'image/no_image.jpg';
	}

}

function actionLog($log) {
	global $db;
	$data = array(
		'user_id' => $_SESSION['user_id'],
		'log' => $db->func_escape_string($log),
		'date_added' => date('Y-m-d H:i:s')
		);
	$db->func_array2insert ( 'inv_users_log', $data );
}
function mapStoreType($store_type)
{
	switch($store_type)
	{

		case 'web':
		$store_type='PPUSA';
		break;
		case 'bigcommerce':
		$store_type='RLCDs';
		break;
		default:
		$store_type = $store_type;
		break;
	}
	return $store_type;

}

function getVoucherCode($order_id, $ext) {
	global $db;
	$ext = '-' . $ext;
	$done = false;
	$code = '';
	for ($i=1; $done === false ; $i++) { 
		$code = $order_id . $ext . $i;
		if (!$db->func_query_first_cell('SELECT * FROM oc_voucher WHERE code = "'. $code .'"')) {
			$done = true;
		}
	}

	return $code;

}
function orderTotal($order_id, $update = false) {
	global $db;
	$order_id = $db->func_escape_string($order_id);
	$order =  $db->func_query_first("SELECT * FROM inv_orders as o, inv_orders_details as od WHERE od.order_id = o.order_id AND o.order_id = '$order_id'");
	$order['bill_state'] = (rtrim($order['bill_state']))? $order['bill_state']: $order['state'];

	$sub_total = $db->func_query_first("SELECT SUM(product_price) AS amount, SUM(product_true_cost * product_qty) AS truecost FROM inv_orders_items WHERE order_id='".$order_id."'");

	$tax = 0;
	if($order['bill_state'] == 'Nevada' && !$db->func_query_first_cell("SELECT dis_tax FROM inv_customers WHERE email='" . $order['email'] . "'")) {
		$tax_detail = $db->func_query_first("SELECT * FROM oc_tax_rate WHERE geo_zone_id=10");
		$tax = ($sub_total['amount']*(float)$tax_detail['rate'])/100;
	}


	$shipping_cost = $order['shipping_cost'];

	$vouchers = $db->func_query_first_cell('SELECT SUM(`a`.`amount`) AS `used` FROM `oc_voucher_history` as a, `oc_voucher` as b WHERE a.`voucher_id` = b.`voucher_id` AND a.`' . (($order['store_type'] == 'po_business')? 'inv_order_id': 'order_id') . '` = "'. $order_id .'"');

	$vouchers_u = $db->func_query('SELECT `a`.`amount`, b.code FROM `oc_voucher_history` as a, `oc_voucher` as b WHERE a.`voucher_id` = b.`voucher_id` AND a.`' . (($order['store_type'] == 'po_business')? 'inv_order_id': 'order_id') . '` = "'. $order_id .'"');

	$coupons = $db->func_query_first_cell('SELECT SUM(`a`.`amount`) as `used` FROM `oc_coupon_history` as a, `oc_coupon` as b WHERE a.`coupon_id` = b.`coupon_id` AND a.`order_id` = "'. $order_id .'"');

	$order_total = $sub_total['amount'] + $shipping_cost + $tax + $coupons + $vouchers;

	$profit = $order_total - $sub_total['truecost'];

	if ($update) {	
		$db->db_exec('UPDATE inv_orders SET `order_price` = "' . round($order_total, 2) . '", `profit` = "' . round($profit, 2) . '" WHERE order_id = "' . $order_id . '"');
		if ($order['store_type'] == 'web') {
			$db->func_query("UPDATE oc_order_total set value = '" . $sub_total['amount'] . "', text = '$" . $sub_total['amount'] . "' WHERE order_id = '$order_id' AND code = 'Sub-Total'");
			$db->func_query("UPDATE oc_order_total set value = '" . $order_total . "', text = '$" . $order_total . "' WHERE order_id = '$order_id' AND code = 'total'");
			$db->func_query("UPDATE oc_order set total = '" . $order_total . "' WHERE order_id = '$order_id'");
			if ($vouchers_u) {
				$db->func_query("DELETE FROM oc_order_total WHERE order_id = '$order_id' AND code = 'voucher'");
				foreach ($vouchers_u as $voucher) {
					$db->func_query("INSERT INTO oc_order_total set code = 'voucher', order_id = '$order_id', sort_order = '8', title = 'Voucher(". $voucher['code'] .")', value = '" . $voucher['amount'] . "', text = '$" . $voucher['amount'] . "'");
				}
			}

			if ($tax) {
				$db->func_query("DELETE FROM oc_order_total WHERE order_id = '$order_id' AND code = 'tax'");
				$db->func_query("INSERT INTO oc_order_total set code = 'tax', order_id = '$order_id', sort_order = '5', title = 'Tax " . $tax_detail['rate'] . "', value = '" . $tax . "', text = '$" . $tax . "'");
			}
		}
	}

	$total = array( 'sub_total' => round($sub_total['amount'], 2), 'shipping_fee' => round($shipping_cost, 2), 'tax' => round($tax, 2), 'vouchers' => round($vouchers, 2), 'coupons' => round($coupons, 2), 'order_total' => round($order_total, 2) );

	return $total;

}

function addComment($for, $data = array()) {
	global $db;
	// if (strlen(rtrim($data['comment'])) < 15) {
	// 	$msg = 'Comment can\'t be shorter than 15 characters';
	// } else {
	if ($data['comment']) {
		$comment = array (
			$for. '_id' => $data['id'],
			'comment' => $db->func_escape_string($data['comment']),
			'user_id' => $_SESSION['user_id'],
			'date_added' => date('Y-m-d H:i:s')
			);
		$db->func_array2insert ( 'inv_'. $for .'_comments', $comment );
		$msg = 'Comment Added';
	} else {
		$msg = 'Please Enter Comment';
	}
	// }

	return $msg;
}
function stripDashes($string)
{
	$to_be_capital = array('Usps '=>'USPS ','Ups '=>'UPS ');

	$string = strtolower($string);
	$string = str_replace("_", " ", $string);
	$string = ucwords($string);
	$string = str_replace(array_keys($to_be_capital), array_values($to_be_capital), $string);

	return $string;


}

function checkYoutubeId($id) {
	$data = @file_get_contents("https://www.googleapis.com/youtube/v3/videos?part=id&id=". $id ."&key=AIzaSyASMKyrkkw9VPvIrZ0p4m8nSKGXb-Uopg0");
	$data = json_decode($data);
	if ($data->pageInfo->totalResults != '1') {
		return false;
	}
	return true;
}
function addVoucherDetail($data)
{
	global $db;
	$db->db_exec("INSERT INTO inv_voucher_details SET 
		voucher_id='".(int)$data['voucher_id']."',
		order_id='".$db->func_escape_string($data['order_id'])."',
		rma_number='".$db->func_escape_string($data['rma_number'])."',
		item_detail = '".$db->func_escape_string($data['detail'])."',
		is_lbb='".(int)$data['is_lbb']."',
		is_rma='".(int)$data['is_rma']."',
		is_order_cancellation='".(int)$data['is_cancellation']."',
		is_pos='".(int)$data['is_pos']."',
		user_id='".(int)$data['user_id']."'
		");
}
function addBoxMoveLog ($cID, $nID, $iID) {
	global $db;
	if ($cID && $nID) {
		$cName = $db->func_query_first_cell('SELECT box_number FROM `inv_return_shipment_boxes` WHERE id = "'. $cID .'"');
		$nName = $db->func_query_first_cell('SELECT box_number FROM `inv_return_shipment_boxes` WHERE id = "'. $nID .'"');
		$item = $db->func_query_first('SELECT * FROM `inv_return_shipment_box_items` WHERE return_item_id = "'. $iID .'"');
		$log = $item['product_sku'] . ' is moved from ' . $cName . ' to ' . $nName;
		$data = array(
			'user_id' => $_SESSION['user_id'],
			'log' => $db->func_escape_string($log),
			'date_added' => date('Y-m-d H:i:s')
			);
		$db->func_array2insert ( 'inv_box_logs', $data );
	}

}

 function moveItemToLBB ($reject_id, $lbbBoxId,$colName, $qty, $nonOemQty, $nonOemPrice , $box_type , $lbbSku) {
 	global $db;
	//check if there is any box open
 	//$lbbBoxId = $db->func_query_first("SELECT * FROM inv_buyback_boxes WHERE id = 'Pending' ORDER BY id DESC");
 	if (!$lbbBoxId) {
 		$newLbbBox = array(
 			'package_number' => 'LBB-' . date('Ymdhis'),
 			'status' => 'Pending',
 			'date_added' => date('Y-m-d h:i:s'),
 			'date_issued' => date('Y-m-d h:i:s'),
 			'user_id' => $_SESSION['user_id']			
 			);
 		$lbbBoxId = $db->func_array2insert ( "inv_buyback_boxes", $newLbbBox );
 	}
 	$mapped_lbb_sku = $db->func_query_first('SELECT sku FROM `inv_buy_back` WHERE id = "'. $lbbSku .'"');
 	$itemData = $db->func_query_first('SELECT * FROM `inv_return_shipment_box_items` WHERE return_item_id = "'. $reject_id .'"');

 		 $id = $check['shipment_id'];
 		 $insertProduct = array(
 			'buyback_id' => '0',
 			'sku' => $mapped_lbb_sku['sku'],
 			'oem_a_price' => $oemPrice,
 			'non_oem_a_price' => $nonOemPrice
 			);

 		$buybackProductId = $db->func_array2insert( "oc_buyback_products", $insertProduct);
 		$insertLbbSku = array(
 			'lbb_sku' => $mapped_lbb_sku['sku'],
 			'product_sku' => $itemData['product_sku']
 			);

 		$mapped_sku_id = $db->func_array2insert( "inv_lbb_sku_mapping", $insertLbbSku);

 		$insertData = array(
 			'shipment_id' => $lbbBoxId,
 			'buyback_product_id' => $buybackProductId,
 			$colName => $qty
 			);
 		$id = $db->func_array2insert ( "inv_buyback_box_items", $insertData );
 		$item = $db->func_query_first("SELECT return_shipment_box_id FROM inv_return_shipment_box_items WHERE return_item_id = '$reject_id'");
		$from = $db->func_query_first_cell("select box_number from inv_return_shipment_boxes where id = '". $item['return_shipment_box_id'] ."'");
		$to = $db->func_query_first_cell( "select package_number from inv_buyback_boxes where id = '$lbbBoxId'" );
		if($box_type=='NTR')
		{
			$_link1 = '<a href="'.$host_path.'boxes/need_to_repair.php">'.$from.'</a>';
			$_link2 = '<a href="'.$host_path.'addedit_boxes.php?shipment_id='.$lbbBoxId.'">'.$to.'</a>';
		}
		else if ($box_type=='RTS')
		{
			$_link2 = '<a href="'.$host_path.'addedit_boxes.php?shipment_id='.$lbbBoxId.'">'.$to.'</a>';
			$_link1 = '<a href="'.$host_path.'boxes/boxes_edit.php?box_id='.$item['return_shipment_box_id'].'&return=return_to_stock">'.$from.'</a>';
		}
		logLbbItem(linkToProduct($itemData['product_sku'],'../'), 'Moved to '.$_link2.' from '. $_link1 . ' by ', $from, $to);
		// for LCD origin in boxes page
		$insert_buyback_id = array(
 			'description' => $_link1
 			); 		
 		$db->func_array2update( "oc_buyback_products", $insert_buyback_id ,"buyback_product_id = '$buybackProductId'");

 	if ($id) {
 		$db->db_exec('DELETE FROM inv_return_shipment_box_items WHERE id = "' . $itemData['id'] . '"');
 	}
 	return $id;
 }

 function escapeArrayDB ($value) {

 	global $db;

 	$return = array();
 	if (is_array($value)) {
 		foreach ($value as $k => $v) {
 			if (is_array($v)) {
 				$return[$k] = escapeArrayDB($v);
 			} else {
 				$return[$k] = $db->func_escape_string($v);
 			}
 		}
 	}

 	return $return;

 }

 function testObject($array, $exit = false) {
 	echo '<pre>'; 
 	print_r($array);
 	echo '</pre>'; 
 	if (!$exit) {
 		exit;
 	}
 }

 function saveInventory ($sku, $qty) {
 	global $db;

 	$oldQty = $db->func_query_first_cell("SELECT quantity FROM oc_product WHERE model = '$sku'");
 	$data = array (
 		'sku' => $sku,
 		'qty' => ((int) $oldQty - (int) $qty),
 		'date_added' => date('Y-m-d')
 		);
 	$id = $db->func_array2insert("inv_product_stock", $data);
 }
 function getOrderProfit($orders)
 {
 	global $db;
	// $order_data = $db->func_query_first("SELECT store_type, order_price FROM inv_orders where order_id='".$orders['order_id']."'");
	if($orders['store_type']=='po_business')
	{
		$order_total = $db->func_query_first_cell("SELECT SUM(product_price) from inv_orders_items WHERE order_id='".$orders['order_id']."'");
		$shipping_cost = $db->func_query_first_cell("SELECT shipping_cost FROM inv_orders_details WHERE order_id='".$orders['order_id']."'");
		$order_total = $order_total + $shipping_cost;

		$_shipping_cost = 0.00;
	}
	else{
		$order_total = $orders['order_price'];
		if($orders['payment_source']=='Replacement')
		{
			$order_total = 0.00;
		}
		$order_shipments = $db->func_query_first("select shipping_cost,insurance_cost,voided from inv_shipstation_transactions where order_id = '".$orders['order_id']."' ORDER BY voided DESC");
		$_shipping_cost = 0.00;	
		if(isset($order_shipments['voided']) and $order_shipments['voided']==0)
		{
			$_shipping_cost = $order_shipments['shipping_cost']+$order_shipments['insurance_cost'];
		}
	}	


	// echo $order_total;exit;
	$order_true_cost = 0.00;
	$order_true_cost = $db->func_query_first_cell("SELECT sum(product_true_cost * product_qty) FROM inv_orders_items WHERE order_id='".$orders['order_id']."'");

	$transaction_fee = (float)$db->func_query_first_cell("SELECT transaction_fee FROM inv_transactions WHERE order_id='".$orders['order_id']."'");

	$order_fee = (float)$db->func_query_first_cell("SELECT SUM(fee) as fee from inv_order_fees where order_id = '".$orders['order_id']."' ");

	$profit = $order_total-$order_true_cost-$transaction_fee-$_shipping_cost+$order_fee;
	return $profit;
}
function createPdf ($data = array(), $dir = '', $imp = false) {
	global $path;
	require_once(str_replace('imp/', 'system/', $path) . 'html2_pdf_lib/html2pdf.class.php');
	$size = array ($data['pageWidth'], $data['pageHeight']);
	try {
		$html2pdf = new HTML2PDF(($data['orientation'])? $data['orientation'] : 'P', (($size)? $size : array ('210', '297')), 'en', true, 'UTF-8', ($data['margin'])? $data['margin']: array('0','0','0','0'));
		$html2pdf->setDefaultFont(($data['font'])? $data['font'] : 'courier');
		$html2pdf->writeHTML($data['html']);
		$filename = uniqid() . time();
		$file = $dir .  $filename . '.pdf';
		$filePath = (($imp)? $path : str_replace('imp/', 'image/', $path)) . $file;
		$html2pdf->Output($filePath, 'F');
	} catch (HTML2PDF_exception $e) {
		echo $e;
		exit;
	}
	return $file;
}
//here
function printNodePDF($pdf, $title, $printer)	{
	global $path;
	if (strpos($pdf, str_replace('imp/', 'image/', $path)) === false) {
		$pdf = str_replace('imp/', 'image/', $path) . $pdf;
	}
	require_once(str_replace('imp/', 'system/', $path) . 'PrintNode-PHP-master/vendor/autoload.php');
		// $credentials = 'f9305047bdf9a187cfc02de4780b8e0c7cb3261a'; /*Dev ID*/
	$credentials = new PrintNode\Credentials();
	$credentials->setApiKey('19982dc5978951c99f98cdcfe5feb4881cc5147b');
	$request = new PrintNode\Request($credentials);
		// $computers = $request->getComputers();
	$printers = $request->getPrinters();
    	// print_r($printers);exit;
		// $printJobs = $request->getPrintJobs();
	$printJob = new PrintNode\PrintJob();
		// $printJob->printer = 130442; //$printers[1]; /*Dev id*/
		// $printJob->printer = 130444; //$printers[1]; /*Dev id*/
	$printJob->printer = ($printer)? $printer: 136106;
	$printJob->contentType = 'pdf_base64';
	$printJob->content = base64_encode(file_get_contents($pdf));
	$printJob->source = 'My App/1.0';
	$printJob->title = $title;
	$response = $request->post($printJob);
	$statusCode = $response->getStatusCode();
	$statusMessage = $response->getStatusMessage();
}


function printLabel ($id, $sku, $box_number, $reason, $order_id, $printer, $source, $font = 12,$img='', $rejLabel='') {
	global $host_path;
	$fontid = 12;
	if (strlen($id) > 25) {
		$fontid = 10;
	}
	$style .= '.heading {margin: 0; font-family: arial; font-size: 12px; width: 192px;}';
	$style .= '.idheading {margin: 0; font-family: arial; font-size: '. $fontid .'px; width: 192px;}';
	$style .= '.container {text-align: center; width: 192px;}';
	$style .= '.headingMain {margin: 0; font-family: arial; font-size: '. $font .'px; width: 192px;}';
	$header = '<page backtop="0mm" backleft="0mm" backbottom="0mm">';
	$header .= '<html>';
	$header .= '<head>';
	$header .= '<style>';
	$header .= $style;
	$header .= '</style>';
	$header .= '</head>';
	$header .= '<body>';

	$body = '<div class="container">';
	$body .= '<table width="100%">';
	$body .= ($id)?'<tr><td class="idheading">'. $id .'</td></tr>': '';
	$body .= ($box_number)?'<tr><td class="headingMain">'. $box_number .'</td></tr>': '';
	$body .= ($sku)?'<tr><td class="'. (($img)? 'headingMain': 'heading') .'">'. $sku .'<sup>'. $source .'</sup></td></tr>': '';
	$body .= ($reason)?'<tr><td class="heading">'. $reason .'</td></tr>': '';
	$body .= ($order_id)?'<tr><td class="heading">'. $order_id .'</td></tr>': '';
	$body .= ($img)?'<tr><td class="heading"><img style="width:165px" src="'. str_replace('imp/', '', $host_path) .'barcode.php?text='. $sku .'&size=50" alt="barcode"></td></tr>': '';
	$body .= ($rejLabel)?'<tr><td class="heading"><img src="'. str_replace('imp/', '', $host_path) .'barcode.php?text='. $sku .'&size=30" alt="barcode"></td></tr>': '';
	$body .= '</table>';
	$body .= '</div>';

	$footer = '</body>';
	$footer .= '</html>';
	$footer .= '</page>';

	$html = $header . $body . $footer;
	//print_r($html);exit;
	$pdf = createPdf(array('html' => $html, 'font' => 'arial', 'orientation' => 'L', 'pageWidth' => 2*25.4, 'pageHeight' => 25.4), 'returns/');
	$printer = ($printer)? $printer: '136097';
	printNodePDF($pdf, 'Lable print for return', $printer);
	return $pdf;
}

function getfristNumaricChar($str) {
	$array = str_split($str);
	$check = '1234567890';
	$no = '';
	foreach ($array as $value) {
		if ($no != '' && strpos($check, $value) === false) {
			break;
		}
		if (strpos($check, $value) !== false) {
			$no .= $value;
		}
	}
	return $no;
}

function scrapPrice ($scraped_data, $to_be_replaced = array('$',' ','or','US')) {
	return number_format((float)str_replace($to_be_replaced,'',$scraped_data), 2);
}

function addOrderComment ($comment, $order_id, $user_id = 0) {
		global $db;
		$user_id = ($user_id) ? $user_id: $_SESSION['user_id'];
		$addcomment = array();
		$addcomment['date_added'] = date('Y-m-d H:i:s');
		$addcomment['user_id'] = $user_id;
		$addcomment['comment'] = $db->func_escape_string($comment);
		$addcomment['order_id'] = $order_id;
		$order_history_id = $db->func_array2insert("oc_order_history", $addcomment);
}