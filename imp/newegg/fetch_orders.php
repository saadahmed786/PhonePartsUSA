<?php

include_once '../config.php';
include_once 'Newegg.php';

$dataDir = $path."newegg/orders";

$orders_csv = array();
if($handle = opendir($dataDir)){
	while (false !== ($file = readdir($handle))){
		if(strlen($file) > 4 and file_exists($dataDir."/".$file) and stristr($file,"csv")){
			$csv_path = $dataDir."/".$file;
			$orders_csv[] = $csv_path;
		}
	}
}

if($orders_csv){
	$Newegg = new Newegg();

	foreach($orders_csv as $csv){
		$Newegg->readCSV($csv);
	}
}

echo "success";
exit;