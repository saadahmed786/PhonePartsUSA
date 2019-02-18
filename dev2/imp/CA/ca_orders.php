<?php
include_once("../config.php");

date_default_timezone_set("America/Los_Angeles");
set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once("ca_keys.php");
include_once 'ChannelAdvisor.php';

global $db,$DEV_KEY,$Password,$AccountID;

foreach($accounts as $account){
	$last_cron_date = $db->func_query_first_cell("select last_cron_date from ca_credential where account_id = '".$account['AccountID']."'");
	if(!intval($last_cron_date)){
		$last_cron_date = date('Y-m-d\TH:i:s', strtotime('-1 day'));
	}
	else{
		$last_cron_date = date('Y-m-d\TH:i:s', (strtotime($last_cron_date) - (6*60*60)));
	}

	$ChannelAdvisor = new ChannelAdvisor($DEV_KEY , $Password , $account['AccountID'] , $account['Prefix']);
	$result[$account['AccountID']] = $ChannelAdvisor->fetchStoreOrders($last_cron_date);
}

if($_REQUEST['m'] == 1){
	$_SESSION['message'] = "Order imported successfully";
	header("Location:".$host_path."order.php");
}
else{
	print_r($result);
}

echo "success";