<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");
include_once("config.php");

$start = (int)$_REQUEST['start'];
if(!$start){
	$start = 0;
}

$count = (int)$_REQUEST['count'];
if($count){
	//where status = 1
	$total = $db->func_query_first_cell("select count(model) from oc_product");
	echo $total;
}
else{
	$all = (int)$_REQUEST['all'];
	if(!$all){
		//status = 1 and
		$_query = "select model from oc_product where date_modified < '".date('Y-m-d H:00:00')."' limit $start , 200";
		//$_query = "select model from oc_product where quantity = 1000";
		$products = $db->func_query($_query);
	}
	else{
		//where status = 1
		$products = $db->func_query("select model from oc_product limit $start , 200");
	}

	if(count($products) == 0){
		echo "NO";
		exit;
	}

	print_r(json_encode($products));
}