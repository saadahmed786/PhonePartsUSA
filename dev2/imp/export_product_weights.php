<?php

include 'auth.php';

$_query = "select model , weight , pd.name from oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id)";

$products = $db->func_query($_query);

$filename = "products-".date("Y-m-d").".csv";
$fp = fopen($filename,"w");

$headers = array("SKU","Name","Weight");
fputcsv($fp , $headers,',');

foreach($products as $product){
	$rowData = array($product['model'] , $product['name'] , $product['weight']);
	fputcsv($fp , $rowData,',');
}

fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($filename);
@unlink($filename);