<?php

include_once '../auth.php';
include_once '../inc/functions.php';

$box_type = $_GET['type'];
if(!$box_type){
	$_SESSION['message'] = "Box type is not valid";
	header("Location:$host_path/boxes/customer_damage.php");
	exit;
}

$return_shipment_boxes_insert = array ();
$return_shipment_boxes_insert ['box_number'] = getReturnBoxNumber ( 0, $box_type );
$return_shipment_boxes_insert ['box_type']   = $box_type;
$return_shipment_boxes_insert ['date_added'] = date ( 'Y-m-d H:i:s' );
$inv_return_shipment_box_id = $db->func_array2insert ( "inv_return_shipment_boxes", $return_shipment_boxes_insert );

$return = $_GET['return'].".php";

$_SESSION['message'] = "Box is created successfully.";
header("Location:$host_path/boxes/$return");
exit;