<?php
require_once ("config.php");
include_once 'inc/functions.php';

$returns = $db->func_query ( "select * from inv_returns where po_created = 0 and rma_status = 'Completed'" );
if (! $returns) {
	echo "No returns exist";
	exit ();
}

$returns_po_items = array ();
$return_shipment_box_items = array ();

foreach ( $returns as $return ) {
	$return_id = $return ['id'];

	$return_items = $db->func_query ( "select * from inv_return_items where return_id = '$return_id'" );
	if (! $return_items) {
		continue;
	}

	foreach ( $return_items as $return_item ) {
		$return_item ['order_id'] = $return ['order_id'];
		$return_item ['rma_number'] = $return ['rma_number'];

		if ($return_item ['item_condition'] == 'Good For Stock') {
			//$returns_po_items [] = $return_item;
			$return_shipment_box_items ['RTSBox'] [] = $return_item;
		}
		elseif ($return_item ['item_condition'] == 'Item Issue') {
			$return_shipment_box_items ['ItemIssueBox'] [] = $return_item;
		}
		elseif ($return_item ['item_condition'] == 'Not Tested') {
			$return_shipment_box_items ['NotTestedBox'] [] = $return_item;
		}
		elseif ($return_item ['item_condition'] == 'Customer Damage') {
			$return_shipment_box_items ['CustomerDamageBox'] [] = $return_item;
		}
	}

	$db->db_exec ( "update inv_returns SET po_created = 1 where id = '$return_id'" );
}

if ($returns_po_items) {
	$returns_po_insert = array ();
	$returns_po_insert ['box_number'] = getReturnBoxNumber ( 1 );
	$returns_po_insert ['date_added'] = date ( 'Y-m-d H:i:s' );

	$returns_po_id = $db->func_array2insert ( "inv_returns_po", $returns_po_insert );
	foreach ( $returns_po_items as $returns_po_item ) {
		$returns_po_item_insert = array ();
		$returns_po_item_insert ['product_sku'] = $returns_po_item ['sku'];
		$returns_po_item_insert ['quantity'] = $returns_po_item ['quantity'];
		$returns_po_item_insert ['price'] = $returns_po_item ['price'];
		$returns_po_item_insert ['returns_po_id'] = $returns_po_id;
		$returns_po_item_insert ['return_id'] = $returns_po_item ['return_id'];

		$db->func_array2insert ( "inv_returns_po_items", $returns_po_item_insert );
	}
}

if ($return_shipment_box_items) {
	foreach ( $return_shipment_box_items as $box_type => $return_shipment_box_item ) {
		//check if there is any box open
		$inv_return_shipment_box_id = $db->func_query_first_cell("select id from inv_return_shipment_boxes where box_number LIKE '%$box_type%' and status = 'Issued' order by date_added DESC");
		if(!$inv_return_shipment_box_id){
			$return_shipment_boxes_insert = array ();
			$return_shipment_boxes_insert ['box_number'] = getReturnBoxNumber ( 0, $box_type );
			$return_shipment_boxes_insert ['box_type']   = $box_type;
			$return_shipment_boxes_insert ['date_added'] = date ( 'Y-m-d H:i:s' );
			$inv_return_shipment_box_id = $db->func_array2insert ( "inv_return_shipment_boxes", $return_shipment_boxes_insert );
		}

		foreach ( $return_shipment_box_item as $returns_po_item ) {
			$returns_po_item_insert = array ();
			$returns_po_item_insert ['product_sku'] = $returns_po_item ['sku'];
			$returns_po_item_insert ['quantity'] = $returns_po_item ['quantity'];
			$returns_po_item_insert ['price'] = $returns_po_item ['price'];
			$returns_po_item_insert ['source'] = 'manual';
			$returns_po_item_insert ['order_id'] = $returns_po_item ['order_id'];
			$returns_po_item_insert ['rma_number'] = $returns_po_item ['rma_number'];
			$returns_po_item_insert ['return_item_id'] = getReturnItemId($returns_po_item ['rma_number'] , $returns_po_item ['sku']);
			$returns_po_item_insert ['date_added'] = date ( 'Y-m-d H:i:s' );
			$returns_po_item_insert ['return_shipment_box_id'] = $inv_return_shipment_box_id;
				
			$db->func_array2insert ( "inv_return_shipment_box_items", $returns_po_item_insert );
		}
	}
}

echo "success";