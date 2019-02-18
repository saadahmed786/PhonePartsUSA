<?php

date_default_timezone_set("America/Los_Angeles");
include_once '../config.php';

$marketplaces = $_GET['marketplaces'];

if($_GET['action'] == 'startImportNow'){
	$ch = curl_init();
	curl_setopt($ch , CURLOPT_URL , $host_path."crons/update_prices.php?marketplaces=$marketplaces");
	curl_setopt($ch , CURLOPT_TIMEOUT, 10);
	curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec($ch);
	
	print_r($result);
}
elseif($_GET['action'] == 'getProcessUpdate'){
	$result   = $db->func_query_first("select last_id , total, last_cron_date from inv_prices_cron");
	$last_id  = $result['last_id'] + 1;
	$total    = $result['total'];

	//if script started but stopped by FB then restart it
	$last_cron_date = $result['last_cron_date'];
	$time_delay = time() - strtotime($last_cron_date);
	
	//print $time_delay . "--" .$total ."--". ($total - $last_id);
	if($time_delay > 200 and $total > 5 and ($total - $last_id) > 10){
		$ch = curl_init();
		curl_setopt($ch , CURLOPT_URL , $host_path."crons/update_prices.php?marketplaces=$marketplaces");
		curl_setopt($ch , CURLOPT_TIMEOUT, 10);
		curl_setopt($ch , CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
	}

	$width   = number_format(4 * (($last_id * 100) / $total),2);
	echo $width;
}

?>