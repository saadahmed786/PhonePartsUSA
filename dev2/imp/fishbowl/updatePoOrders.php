<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("../config.php");

$successIdsStr   = $_REQUEST['successIdsStr'];
$nomappingIdsStr = $_REQUEST['nomappingIdsStr'];
$successPOIdsStr   = $_REQUEST['successPOIdsStr'];
$nomappingPOIdsStr = $_REQUEST['nomappingPOIdsStr'];
$errorMessage = json_decode($_REQUEST['errorMessage'],true);

if($successIdsStr){
	$db->db_exec("Update inv_shipments SET fb_added = 1 , ignored = 0 where id IN ($successIdsStr)");
}

if($nomappingIdsStr){
	$db->db_exec("Update inv_shipments SET ignored = 1 where id IN ($nomappingIdsStr)");
}

if($successPOIdsStr){
	$db->db_exec("Update inv_returns_po SET fb_added = 1 , ignored = 0 where id IN ($successPOIdsStr)");
}

if($nomappingPOIdsStr){
	$db->db_exec("Update inv_returns_po SET ignored = 1 where id IN ($nomappingPOIdsStr)");
}

echo "success";