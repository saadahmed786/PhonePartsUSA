<?php
include_once("config.php");
include_once("inc/functions.php");
if(isset($_GET['reset']) && $_GET['reset']==1){
	$db->db_exec("UPDATE inv_returns SET return_id_sync='0'");
	echo "Reset Done";
	exit;
}

$ret_id = $db->func_query_first_cell('SELECT ri.return_id FROM inv_return_items ri inner join inv_returns r on (ri.return_id = r.id) WHERE ri.return_item_id = "" AND r.return_id_sync = "0" order by ri.id DESC ');


$return_items_details = $db->func_query("SELECT r.rma_number,ri.* FROM inv_returns r inner join inv_return_items ri on (r.id = ri.return_id) WHERE ri.return_id = '".$ret_id."' ");

foreach ($return_items_details as $item) {
	if ($item['rtv_shipment_id']) {

		$return_item_id = $db->func_query_first("SELECT id,reject_item_id FROM inv_rejected_shipment_items WHERE reject_item_id LIKE '%".$item['rma_number'].'-'.$item['sku']."%' AND return_id_moved = '0' ");
		if ($return_item_id['reject_item_id']) {
			$db->db_exec("UPDATE inv_return_items SET return_item_id='" . $return_item_id['reject_item_id'] . "' WHERE id='" . $item['id']. "'");
			$db->db_exec("UPDATE inv_rejected_shipment_items SET return_id_moved='1' WHERE id='" . (int)$return_item_id['id']. "'");
		}
	} else {

		$return_item_id = $db->func_query_first("SELECT id,return_item_id,return_shipment_box_id FROM inv_return_shipment_box_items WHERE return_item_id LIKE '%".$item['rma_number'].'-'.$item['sku']."%' AND return_id_moved = '0' ");
		if ($return_item_id['return_item_id']) {
			$box_number = $db->func_query_first_cell("SELECT box_number from inv_return_shipment_boxes where id = '".$return_item_id['return_shipment_box_id']."'");
			$db->db_exec("UPDATE inv_return_items SET return_item_id='" . $return_item_id['return_item_id'] . "', box_id = '".$box_number."' WHERE id='" .$item['id']. "'");
			$db->db_exec("UPDATE inv_return_shipment_box_items SET return_id_moved='1' WHERE id='" . (int)$return_item_id['id']. "'");
		}
	}
}


$db->db_exec("UPDATE inv_returns SET return_id_sync='1' WHERE id='" . (int)$ret_id. "'");
echo "Return Item Id's added for RMA id ".$ret_id;


?>