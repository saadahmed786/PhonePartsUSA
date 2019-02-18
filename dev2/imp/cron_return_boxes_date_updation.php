<?php
include_once("config.php");
include_once("inc/functions.php");

/*$_query = "SELECT * from inv_rejected_shipment_items WHERE rma_number <> '' and is_date_updated = '0' order by id desc  ";
$items = $db->func_query($_query);

if ($items) {
	foreach ($items as $item ) {
		//$date = $db->func_query_first_cell("SELECT date_added from inv_returns WHERE rma_number = '".$item['rma_number']."' ");
		$original_box_added = $item['original_box_added'];

		$source = $db->func_query_first_cell("SELECT source from inv_returns WHERE rma_number = '".$item['rma_number']."' ");
		if ($source != 'storefront') {			
		$db->db_exec("UPDATE inv_rejected_shipment_items SET date_added = '".$original_box_added."', is_date_updated = '1' WHERE id='" . (int) $item ['id']. "'");
		} else {
			$db->db_exec("UPDATE inv_rejected_shipment_items SET is_date_updated = '1' WHERE id='" . (int) $item ['id']. "'");
		}
	}
	echo '<pre>';
	echo "1000 Rejected Shipment Items dates Have been updated. (1)";
	echo '</pre>';
} else {
	echo '<pre>';
	echo "All Rejected Shipment Items dates Have been updated. (1) ";
	echo '</pre>';

}*/

/*$_query = "SELECT * from inv_return_shipment_box_items WHERE rma_number <> '' and is_date_updated = '0' order by id desc  ";
$items = $db->func_query($_query);

if ($items) {
	foreach ($items as $item ) {
		//$date = $db->func_query_first_cell("SELECT date_added from inv_returns WHERE rma_number = '".$item['rma_number']."' ");
		$original_box_added = $item['original_box_added'];

		$source = $db->func_query_first_cell("SELECT source from inv_returns WHERE rma_number = '".$item['rma_number']."' ");
		if ($source != 'storefront') {			
		$db->db_exec("UPDATE inv_return_shipment_box_items SET date_added = '".$original_box_added."', is_date_updated = '1' WHERE id='" . (int) $item ['id']. "'");
		}else {
			$db->db_exec("UPDATE inv_rejected_shipment_items SET is_date_updated = '1' WHERE id='" . (int) $item ['id']. "'");
		}
	}
	echo '<pre>';
	echo "1000 RejectedBox Items dates Have been updated. (2)";
	echo '</pre>';
} else {
	echo '<pre>';
	echo "All RejectedBox Items dates Have been updated. (2) ";
	echo '</pre>';

}*/

?>