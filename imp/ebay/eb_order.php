<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");

date_default_timezone_set("America/Los_Angeles");

include_once("../config.php");

include_once("session.php");
include_once("eb_config.php");
include_once("ebAPI.php");

$totalSales = new ebAPI();

$token = $db->func_query_first_cell("Select config_value from configuration where config_key = 'USER_TOKEN' ");
if(!$token){
    exit;
}
// echo $token;exit;

$last_cron_date = $db->func_query_first_cell("select last_cron_date from ebay_credential");
if(!intval($last_cron_date)){
    $last_cron_date = date('Y-m-d H:i:s', strtotime('-1 day'));
}

$startDate = $last_cron_date;
$endDate   = $totalSales->geteBayTime();
$endDate   = substr($endDate,0,19);
$endDate   = str_ireplace(array("T")," ",$endDate);

$majorLastDate = '2013-09-14 00:00:00';
if(strtotime($startDate) < strtotime($majorLastDate)){
    $startDate = $majorLastDate;
}

print "$startDate ---  $endDate";

//mail("vipin.garg12@gmail.com","ebay cron run","$startDate ---  $endDate");

$result = $totalSales->fetchOrders($startDate,$endDate,$token);
//print_r($result);

if($_REQUEST['m'] == 1){
    $_SESSION['message'] = "Order imported successfully";
    header("Location:$host_path/order.php");
}

echo "success";

?>