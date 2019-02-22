<?php
include_once("../config.php");

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("bonanza_keys.php");
include_once 'Bonanza.php';

$Bonanza = new Bonanza();
$Bonanza->setCredential($dev_key , $cert_key);

$last_cron_date = $db->func_query_first_cell("select last_cron_date from bonanza_credential");
if(!intval($last_cron_date)){
	$last_cron_date = date('Y-m-d\TH:i:s', strtotime('-1 day'));
}
else{
	$last_cron_date = date('Y-m-d\TH:i:s', (strtotime($last_cron_date) - (12*60*60)));
}

$end_date = date('Y-m-d H:i:s',(time() + (12*60*60)));
try{
	$result = $Bonanza->fetchStoreOrders($last_cron_date,$end_date,$auth_token);
}
catch(Exception $e){
	print $e->getMessage();
}

if($_REQUEST['m'] == 1){
	$_SESSION['message'] = "Order imported successfully";
	header("Location:".$host_path."order.php");
}
else{
	print_r($result);
}

echo "success";