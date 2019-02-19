<?php

include_once '../auth.php';
include_once '../inc/functions.php';
include_once '../inc/split_page_results.php';

$filename = "rejected_boxes.csv";
$fp = fopen($filename, "w");

$headers = array("Box Number","Reject ID","Product SKU","Reason","Date Added","Order ID","RMA Number");
fputcsv($fp, $headers,',');

$shipments = $db->func_query("select * from inv_return_shipment_boxes");
foreach($shipments as $shipment){
	$box_id = $shipment['id'];

	$inv_query   = "select si.* , s.box_number from inv_return_shipment_box_items si inner join inv_return_shipment_boxes s on (si.return_shipment_box_id  = s.id) where s.id = '$box_id' order by s.id";
	$products = $db->func_query($inv_query);

	$count = 1;
	$shipment_id = $products[0]['return_shipment_box_id'];

	foreach($products as $product){
		if($shipment_id != $product['return_shipment_box_id']){
			$count = 1;
			$shipment_id = $product['return_shipment_box_id'];
		}
		
		$reason = explode("<br/>",$product['reason']);
		for($j=0;$j<$product['quantity'];$j++)
		{
			$reject_id = $product['rma_number'] . "-". $product['product_sku']. "-".$count;
			$data = array($shipment['box_number'] , $reject_id , $product['product_sku'] , $reason[$j] , $shipment['date_added'],$product['order_id'],$product['rma_number']);
			fputcsv($fp, $data,',');
			$count++;
			
			$rejected_shipment_item = array();
			$rejected_shipment_item['reject_item_id'] = $reject_id;
			$db->func_array2update("inv_return_shipment_box_items", $rejected_shipment_item," id = '".$product['id']."'");
		}
	}
}

fclose($fp);

echo "done";