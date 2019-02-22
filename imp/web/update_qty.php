<?php

set_time_limit(0);
ini_set("memory_limit", "20000M");

include_once 'config.php';

function updatewebQty($webArray = null){
	global $db;

	if(!$webArray){
		return false;
	}

	foreach($webArray as $web){
		$db->db_exec("update oc_product set quantity = '".$web['qty']."' where model = '".$web['sku']."'");
	}
	
	return 1;
}

?>