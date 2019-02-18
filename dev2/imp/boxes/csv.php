<?php

include_once '../auth.php';
include_once '../inc/functions.php';
include_once '../inc/split_page_results.php';

$filename = "rejected_shipments.csv";
$fp = fopen($filename, "w");

$headers = array("Package Number","Reject ID","Product SKU","Reason","Date Added");
fputcsv($fp, $headers,',');

$shipments = $db->func_query("select * from inv_rejected_shipments");
foreach($shipments as $shipment){
	$shipment_id = $shipment['id'];

	$inv_query   = "select si.* , s.package_number from inv_rejected_shipment_itemsbk si inner join inv_shipments s on (si.shipment_id = s.id) where rejected_shipment_id = '$shipment_id' order by shipment_id";
	$products = $db->func_query($inv_query);

	$count = 1;
	$shipment_id = $products[0]['shipment_id'];

	foreach($products as $product){
		if($shipment_id != $product['shipment_id']){
			$count = 1;
			$shipment_id = $product['shipment_id'];
		}
		
		$reason = explode("<br/>",$product['reject_reason']);
		for($j=0;$j<$product['qty_rejected'];$j++)
		{
			$reject_id = $product['package_number'] . "_". $count;
			$data = array($shipment['package_number'] , $reject_id , $product['product_sku'] , $reason[$j] , $shipment['date_added']);
			fputcsv($fp, $data,',');
			$count++;
			
			$rejected_shipment_item = array();
			$rejected_shipment_item['shipment_id'] = $shipment_id;
			$rejected_shipment_item['rejected_shipment_id'] = $product['rejected_shipment_id'];
			$rejected_shipment_item['product_sku']   = $product['product_sku'];
			$rejected_shipment_item['reject_reason'] = $db->func_escape_string($reason[$j]);
			$rejected_shipment_item['reject_item_id'] = $reject_id;
			$rejected_shipment_item['qty_rejected'] = 1;
			$rejected_shipment_item['date_added'] = date('Y-m-d H:i:s');
			$rejected_shipment_item['qc_app_uploaded'] = $product['qc_app_uploaded'];
			
			$db->func_array2insert("inv_rejected_shipment_items", $rejected_shipment_item);
		}
	}
}

fclose($fp);

echo "done";