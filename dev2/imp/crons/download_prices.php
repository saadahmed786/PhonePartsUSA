<?php

include_once '../config.php';
$prices = $db->func_query("select * from inv_product_prices");

$filename = "prices.csv";
$fp = fopen($filename, "w");

$header = array("SKU","ChannelAdvisor MM","ChannelAdvisor 1US","ChannelAdvisor 2US","Bigcommerce","Bigcommerce Retail","Bonanza","Wish");
fputcsv($fp, $header,',');

foreach($prices as $price){
	$data = array($price['product_sku'],
				  $price['channel_advisor_new'],
				  $price['channel_advisor1_new'],
				  $price['channel_advisor2_new'],
				  $price['bigcommerce_new'],
				  $price['bigcommerce_retail_new'],
				  $price['bonanza_new'],
				  $price['wish_new']
	);
	fputcsv($fp, $data,',');
}

fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($filename);
@unlink($filename);