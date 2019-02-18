<?php
set_time_limit(0);
include "../config.php";
include "../inc/functions.php";
require_once("../easypost/lib/easypost.php");
\EasyPost\EasyPost::setApiKey(EASYPOST_API);
$tracking_number = $_POST['tracking_number'];

$carrier = $_POST['carrier'];
$shipment_number = $_POST['shipment_number'];
// UnComment when funds available
if(strlen($tracking_number)<=5 or $carrier=='') {
	exit;
} try {

	$tracker_obj = \EasyPost\Tracker::create(array('tracking_code' => $tracking_number,'carrier'=>$carrier));
}

catch (\EasyPost\Error $e) {
	echo '('.$carrier.')'. $e->ecode;
	continue;
}

$tracker_info = \EasyPost\Tracker::retrieve($tracker_obj->id);

if($tracker_info->id) {
	$_array = array();
	$_array['tracker_id'] = $tracker_info->id;
	$_array['tracking_code'] = $tracker_info->tracking_code;
	$_array['status'] = $tracker_info->status;
	if ($shipment_number) {
		$_array['shipment_id'] = $shipment_number;
	}
	$_array['created_at'] = $tracker_info->created_at;
	$_array['updated_at'] = $tracker_info->updated_at;
	$_array['weight'] = (float)$tracker_info->weight;
	$_array['est_delivery_date'] = $tracker_info->est_delivery_date;
	$_array['carrier'] = $tracker_info->carrier;


	$check = $db->func_query_first_cell("SELECT tracker_id FROM inv_tracker WHERE tracker_id='".$tracker_info->id."'");
	if($check) {
		$db->func_array2update("inv_tracker",$_array," tracker_id = '".$tracker_info->id."' ");
	} else {
		$_array['datetime'] = date('Y-m-d H:i:s');
		$db->func_array2insert("inv_tracker", $_array);
	}
	$db->db_exec("DELETE FROM inv_tracker_status WHERE tracker_id='".$tracker_info->id."'");
	foreach($tracker_info->tracking_details as $detail) {
		$_array = array();
		$_array['tracker_id'] = $tracker_info->id;
		$_array['message'] = $db->func_escape_string($detail->message);
		$_array['status'] = $db->func_escape_string($detail->status);
		$_array['datetime'] = $db->func_escape_string($detail->datetime);
		$_array['tracking_location'] = $db->func_escape_string($detail->tracking_location);
		$db->func_array2insert("inv_tracker_status", $_array);
	}
	echo json_encode(array('success' => true));
}
?>