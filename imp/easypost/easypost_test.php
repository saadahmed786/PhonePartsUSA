<?php
set_time_limit(0);
include "../config.php";
include "../inc/functions.php";
require_once("../easypost/lib/easypost.php");
\EasyPost\EasyPost::setApiKey(EASYPOST_API);

 $carrier = 'DHL Express';

try {
  $tracker_obj = \EasyPost\Tracker::create(array('tracking_code' => '6072014373','carrier'=>$carrier));
}
catch (\EasyPost\Error $e) {
  echo $e->description;
  exit;
}
  
 $tracker_info = \EasyPost\Tracker::retrieve($tracker_obj->id);
print_r($tracker_info);exit;
 if($tracker_info->id)
 {
 	$_array = array();
 	$_array['tracker_id'] = $tracker_info->id;
 	$_array['tracking_code'] = $tracker_info->tracking_code;
 	$_array['status'] = $tracker_info->status;
 	$_array['created_at'] = $tracker_info->created_at;
 	$_array['updated_at'] = $tracker_info->updated_at;
 	$_array['weight'] = (float)$tracker_info->weight;
 	$_array['est_delivery_date'] = $tracker_info->est_delivery_date;
 	$_array['shipment_id'] = $shipment['id'];
 	$_array['carrier'] = $tracker_info->carrier;
 	
 	
 	$check = $db->func_query_first_cell("SELECT tracker_id FROM inv_tracker WHERE tracker_id='".$tracker_info->id."'");
 	if($check)
 	{
	$db->func_array2update("inv_tracker",$_array," tracker_id = '".$tracker_info->id."' ");
 	}
 	else
 	{
 		$_array['datetime'] = date('Y-m-d H:i:s');
 		$db->func_array2insert("inv_tracker", $_array);
 	}
 	$db->db_exec("DELETE FROM inv_tracker_status WHERE tracker_id='".$tracker_info->id."'");
 	foreach($tracker_info->tracking_details as $detail)
 	{
 		$_array = array();
 	$_array['tracker_id'] = $tracker_info->id;
 	$_array['message'] = $db->func_escape_string($detail->message);
 	$_array['status'] = $db->func_escape_string($detail->status);
 	$_array['datetime'] = $db->func_escape_string($detail->datetime);
 	$_array['tracking_location'] = $db->func_escape_string($detail->tracking_location);
	$db->func_array2insert("inv_tracker_status", $_array);

 	}
 	$db->db_exec("UPDATE inv_shipments SET is_tracker_updated=1 where id='".$shipment['id']."'");
 	echo $shipment['package_number']."<br>";
 }

?>