<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("../config.php");

$successIdsStr   = $_REQUEST['successIdsStr'];
$nomappingIdsStr = $_REQUEST['nomappingIdsStr'];
$errorMessage = json_decode($_REQUEST['errorMessage'],true);

if($successIdsStr){
	$db->db_exec("Update inv_return_shipment_boxes SET fb_added = 1 , ignored = 0 where id IN ($successIdsStr)");
}

if($nomappingIdsStr){
	$db->db_exec("Update inv_return_shipment_boxes SET ignored = 1 where id IN ($nomappingIdsStr)");
}


echo "success";