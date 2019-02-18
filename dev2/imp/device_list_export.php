<?php

include 'auth.php';
include_once 'inc/functions.php';
$table = "inv_devices";
$query_str = "";

$device_type_id = $_GET['device_type_id'];
$make = $_GET['make'];
$grade_id = $_GET['grade_id'];
$sku = $_GET['sku'];
$model_id = $_GET['model_id'];
$location_id = $_GET['location_id'];
$imei = $_GET['imei'];
$status_id = $_GET['status_id'];

if($device_type_id)
{
$query_str.=" AND b.device_type_id='".(int)$device_type_id."'"	;
}
if($make)
{
$query_str.=" AND b.manufacturer_id='".(int)$make."'"	;
}

if($grade_id)
{
$query_str.=" AND a.grade_id='".(int)$grade_id."'"	;
}
if($sku)
{
$query_str.=" AND a.sku LIKE '%".$sku."%'"	;
}
if($model_id)
{
$query_str.=" AND a.model_id='".(int)$model_id."'"	;
}

if($location_id)
{
$query_str.=" AND a.location_id='".(int)$location_id."'"	;
}
if($imei)
{
$query_str.=" AND a.imei LIKE '%".$imei."%'"	;
}
if($status_id)
{
$query_str.=" AND a.status_id='".(int)$status_id."'"	;
}

$_query = "SELECT a.* FROM inv_devices a INNER JOIN inv_d_model b ON (a.model_id=b.id) where 1 = 1 $query_str ";


$rows = $db->func_query($_query);

$filename = "devices-".date("Y-m-d").".csv";
$fp = fopen($filename,"w");

$headers = array("Type","ID","Manufacturer","Model","Carrier","IMEI","OS","Grade","Internal Storage","Location","P/F","Issues","Accessories","Images");
fputcsv($fp , $headers,',');

foreach($rows as $row){
	$model_info = $db->func_query_first("SELECT * FROM inv_d_model WHERE id='".$row['model_id']."'");
	
	$type = getResult("SELECT name FROM inv_d_type WHERE id='".$model_info['device_type_id']."'");
	$manufacturer = getResult("SELECT name FROM inv_d_manufacturer WHERE id='".$model_info['manufacturer_id']."'");
	$carrier = getResult("SELECT name FROM inv_d_carrier WHERE id='".$model_info['carrier_id']."'");
	$os = getResult("SELECT name FROM inv_d_os WHERE id='".$row['os_id']."'");
	$grade = getResult("SELECT name FROM inv_d_grade WHERE id='".$row['grade_id']."'");
	$storage = getResult("SELECT name FROM inv_d_storage WHERE id='".$row['storage_id']."'");
	$location = getResult("SELECT name FROM inv_d_location WHERE id='".$row['location_id']."'");
	$status = getResult("SELECT name FROM inv_d_status WHERE id='".$row['status_id']."'");
	
	$imei = $row['imei'];
	$id=$row['sku'];
	$model = $model_info['name'];
	
	$issues = array();
	$_rows = $db->func_query("SELECT * FROM inv_devices_issues WHERE device_id='".$row['id']."'");
	foreach($_rows as $_row)
	{
	$issues[]= getResult("SELECT name FROM inv_d_issue WHERE id='".$_row['issue_id']."'");	
	}
	
	$accessories = array();
	$_rows = $db->func_query("SELECT * FROM inv_devices_accessories WHERE device_id='".$row['id']."'");
	foreach($_rows as $_row)
	{
	$accessories[]= getResult("SELECT name FROM inv_d_accessories WHERE id='".$_row['accessories_id']."'");	
	}
	
	$images = array();
	$_rows = $db->func_query("SELECT image_path FROM inv_devices_images WHERE device_id='".$row['id']."'");
	foreach($_rows as $_row)
	{
	$images[]= $host_path.$_row['image_path'];
	}
	
	
	
	$rowData = array($type , $id , $manufacturer , $model , $carrier , $imei , $os , $grade , $storage , $location , $status , implode(",",$issues) , implode(",",$accessories),implode(",",$images));
	fputcsv($fp , $rowData,',');
}

fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($filename);
@unlink($filename);