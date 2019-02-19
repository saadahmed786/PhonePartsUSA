<?php

require_once ("config.php");

include_once 'inc/functions.php';


$returns = $db->func_query ( "select * from inv_returns where po_created = 0 and rma_status in ('In QC', 'Completed') limit 2" );

if (! $returns) {

	echo "No returns exist";

	exit ();

}



$returns_po_items = array ();

$return_shipment_box_items = array ();



foreach ( $returns as $return ) {

	$return_id = $return ['id'];



	$return_items = $db->func_query ( "select * from inv_return_items where return_id = '$return_id'" );
	//testObject($return_items);exit;

	if (! $return_items) {

		continue;

	}



	foreach ( $return_items as $return_item ) {

		if (!$return_item['add_to_box']) {
			continue;
		}
		
		$return_item ['order_id'] = $return ['order_id'];

		$return_item ['rma_number'] = $return ['rma_number'];
		$return_item ['source'] = $return ['source'];
		$return_item ['rma_creation_date'] = $return ['date_added'];


		if ($return_item ['item_condition'] == 'Good For Stock') {

			//$returns_po_items [] = $return_item;

			$return_shipment_box_items ['GFSBox'] [] = $return_item;

		}

		elseif ($return_item ['item_condition'] == 'Item Issue') {

			$return_shipment_box_items ['ItemIssueBox'] [] = $return_item;

		}

		elseif ($return_item ['item_condition'] == 'Item Issue - RTV') {
			$return_shipment_box_items ['RTV'] [] = $return_item;

		}

		elseif ($return_item ['item_condition'] == 'Not Tested') {

			$return_shipment_box_items ['NotTestedBox'] [] = $return_item;

		}

		elseif ($return_item ['item_condition'] == 'Customer Damage' || $return_item ['item_condition'] == 'Over 60 days' || $return_item ['item_condition'] == 'Not PPUSA Part') {

			$return_shipment_box_items ['Customer Damage'] [] = $return_item;

		}

		/*elseif ($return_item ['item_condition'] == 'Over 60 days') {

			$return_shipment_box_items ['O60DBox'] [] = $return_item;

		}*/

		elseif ($return_item ['item_condition'] == 'Shipping Damage') {

			$return_shipment_box_items ['SDBox'] [] = $return_item;

		}
		elseif ($return_item ['item_condition'] == 'Need To Repair') {

			$return_shipment_box_items ['NTRBox'] [] = $return_item;

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
		if ($box_type != 'RTV') {
		$inv_return_shipment_box_id = $db->func_query_first_cell("select id from inv_return_shipment_boxes where box_number LIKE '%$box_type%' and status = 'Issued' order by date_added DESC");
		$inv_return_shipment_box_number = $db->func_query_first_cell("select box_number from inv_return_shipment_boxes where box_number LIKE '%$box_type%' and status = 'Issued' order by date_added DESC");
		}
		if(!$inv_return_shipment_box_id && $box_type!=='RTV'){

			$return_shipment_boxes_insert = array ();

			$return_shipment_boxes_insert ['box_number'] = getReturnBoxNumber ( 0, $box_type );

			$return_shipment_boxes_insert ['box_type']   = $box_type;


			$return_shipment_boxes_insert ['date_added'] = date ( 'Y-m-d H:i:s' );

			$inv_return_shipment_box_number = $return_shipment_boxes_insert ['box_number'];

			$inv_return_shipment_box_id = $db->func_array2insert ( "inv_return_shipment_boxes", $return_shipment_boxes_insert );

		}


		$rtv_ship_num = 0;
		foreach ( $return_shipment_box_item as $returns_po_item ) {
			if ($box_type == 'RTV'){
				if (!$returns_po_item ['rtv_shipment_id']) {
					$rejcetedShipment = array();
					$rejcetedShipment ['package_number'] = 'RTV-' . rand();
					$rejcetedShipment ['vendor'] = $returns_po_item ['rtv_vendor_id'];
					$rejcetedShipment ['status'] = 'Pending';
					$rejcetedShipment ['date_added'] = date ( 'Y-m-d H:i:s' );
					$rejcetedShipment ['user_id'] = $_SESSION['user_id'];
					$rtv_ship_num = $db->func_array2insert ( 'inv_rejected_shipments', $rejcetedShipment );
				} else if ($returns_po_item ['rtv_shipment_id']){
					$rtv_ship_num = $db->func_query_first_cell('SELECT id FROM inv_rejected_shipments WHERE package_number = "'. $returns_po_item ['rtv_shipment_id'] .'"');
				}
				$returns_po_item_insert = array ();
				$returns_po_item_insert ['rejected_shipment_id'] = $rtv_ship_num;
				$returns_po_item_insert ['product_sku'] = $returns_po_item ['sku'];
				$returns_po_item_insert ['qty_rejected'] = $returns_po_item ['quantity'];
				// $returns_po_item_insert ['cost'] = $db->func_query_first_cell('SELECT product_true_cost FROM inv_orders_items WHERE order_id = "'. $returns_po_item ['order_id'] .'" AND product_sku = "'. $returns_po_item ['sku'] .'"');
				$returns_po_item_insert ['reject_reason'] = $db->func_query_first_cell("select id from inv_rj_reasons where name='".$returns_po_item['item_issue']."' limit 1");
				// $returns_po_item_insert ['cost'] = $returns_po_item['price'];
				$returns_po_item_insert ['cost'] = getTrueCost($returns_po_item ['sku']);
				$returns_po_item_insert ['reject_item_id'] = getReturnItemId($returns_po_item ['rma_number'] , $returns_po_item ['sku'],1);
				$returns_po_item_insert ['order_id'] = $returns_po_item ['order_id'];
				$returns_po_item_insert ['production_date'] = $returns_po_item ['production_date'];
				if ($returns_po_item['source'] == 'storefront') {
								$returns_po_item_insert ['date_added'] = $returns_po_item['rma_creation_date'];
								$returns_po_item_insert ['original_box_added'] = date ( 'Y-m-d H:i:s' );
							} else {
								$returns_po_item_insert ['date_added'] = date ( 'Y-m-d H:i:s' );
								$returns_po_item_insert ['original_box_added'] = date ( 'Y-m-d H:i:s' );
							}
				$returns_po_item_insert ['rma_number'] = $returns_po_item ['rma_number'];
				
				$source = ($returns_po_item['source'] == 'storefront')? 'SF': 'RC';
			if ($returns_po_item['printer']) {
				$returns_po_item_insert['label_pdf'] = printLabel($returns_po_item_insert['reject_item_id'], $returns_po_item_insert['product_sku'], $rtv_ship_num, $returns_po_item['item_issue'], $returns_po_item_insert['order_id'], $returns_po_item['printer'], $source);
			}
				$db->db_exec("UPDATE inv_return_items SET return_item_id='" . $returns_po_item_insert ['reject_item_id'] . "' WHERE id='" . (int) $returns_po_item ['id']. "'");
				
				$db->func_array2insert ( "inv_rejected_shipment_items", $returns_po_item_insert);

			} else {

			$returns_po_item_insert = array ();

			$returns_po_item_insert ['product_sku'] = $returns_po_item ['sku'];

			$returns_po_item_insert ['quantity'] = $returns_po_item ['quantity'];

			$returns_po_item_insert ['price'] = $returns_po_item ['price'];

			$returns_po_item_insert ['source'] = $returns_po_item['source'];

			$returns_po_item_insert ['order_id'] = $returns_po_item ['order_id'];
			
			$returns_po_item_insert ['cost'] = $db->func_query_first_cell('SELECT product_true_cost FROM inv_orders_items WHERE order_id = "'. $returns_po_item ['order_id'] .'" AND product_sku = "'. $returns_po_item ['sku'] .'"');

			$returns_po_item_insert ['rma_number'] = $returns_po_item ['rma_number'];
			$returns_po_item_insert ['reason'] = $returns_po_item ['qc_comment'];

			$returns_po_item_insert ['return_item_id'] = getReturnItemId($returns_po_item ['rma_number'] , $returns_po_item ['sku']);

			if ($returns_po_item['source'] == 'storefront') {
								$returns_po_item_insert ['date_added'] = $returns_po_item['rma_creation_date'];
								$returns_po_item_insert ['original_box_added'] = date ( 'Y-m-d H:i:s' );
							} else {
								$returns_po_item_insert ['date_added'] = date ( 'Y-m-d H:i:s' );
								$returns_po_item_insert ['original_box_added'] = date ( 'Y-m-d H:i:s' );
							}

			$returns_po_item_insert ['return_shipment_box_id'] = $inv_return_shipment_box_id;
			$source = ($returns_po_item['source'] == 'storefront')? 'SF': 'RC';
			if ($returns_po_item['printer']) {
				$returns_po_item_insert['label_pdf'] = printLabel($returns_po_item_insert['return_item_id'], $returns_po_item_insert['product_sku'], $inv_return_shipment_box_number, $returns_po_item_insert['reason'], $returns_po_item_insert['order_id'], $returns_po_item['printer'], $source);
			}
				$db->db_exec("UPDATE inv_return_items SET return_item_id='" . $returns_po_item_insert ['return_item_id'] . "', box_id = '".$inv_return_shipment_box_number."' WHERE id='" . (int) $returns_po_item ['id']. "'");
			
			$db->func_array2insert ( "inv_return_shipment_box_items", $returns_po_item_insert );
			}

		}

	}

}



echo "success";