<?php

include_once '../config.php';

$newegg_credential = $db->func_query_first("select * from inv_newegg_credential");

//connect to vender1 ftp server
$ftp_host = $newegg_credential['ftp_host'];
$ftp_user = $newegg_credential['username'];
$ftp_pwd  = $newegg_credential['password'];

$last_cron_date = strtotime($newegg_credential['last_cron_date']);

$conn = ftp_connect($ftp_host) or die("Can not connect to ftp server");
ftp_login($conn , $ftp_user , $ftp_pwd) or die("Can not login to ftp server");

$dirlist = ftp_nlist($conn , '/Outbound/OrderList/');
foreach($dirlist as $file){
	$file_name  = basename($file);
	$file_parts = explode("_", $file_name);
	$file_date  = substr($file_parts[1],0, 4)."-".substr($file_parts[1],4, 2)."-".substr($file_parts[1],6, 2);
	$file_date  = strtotime($file_date);
	
	//print $last_cron_date . " - " . $file_date . "<br />";
	if($last_cron_date < $file_date){
		ftp_get($conn , $path.'/newegg/orders/'.$file_name , $file , FTP_ASCII);
	}
}

ftp_close($conn);

echo "success";