<?php
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 0); //300 seconds = 5 minutes
include_once '../config.php';
$fp = fopen('main_export.csv', "w");
$headers = array("SKU Type", "Qty Purchased","Avg Price Purchased", "Qty Sold","Avg Price Sold","Total Cost","Total Price Sold","# Returns","Return Amount"
	);
fputcsv($fp, $headers,',');

$rows = $db->func_query("select sku from inv_product_skus order by sku asc");
foreach($rows as $row)
{
	$len = strlen($row['sku']);
	$qty_purchased = $db->func_query_first_cell("SELECT SUM(b.qty_received) FROM inv_shipment_items b,inv_shipments a where b.shipment_id=a.id and a.status='Completed' and left(b.product_sku,$len)='".$row['sku']."' and month(a.date_completed)='12' and year(a.date_completed)='2016'");
	$avg_purchased = $db->func_query_first_cell("SELECT AVG(b.unit_price/a.ex_rate) FROM inv_shipment_items b,inv_shipments a where b.shipment_id=a.id and a.status='Completed' and left(b.product_sku,$len)='".$row['sku']."' and month(a.date_completed)='12' and year(a.date_completed)='2016'");
	
	$qty_sold = $db->func_query_first_cell("SELECT SUM(b.product_qty) FROM inv_orders_items b,inv_orders a where b.order_id=a.order_id and lower(a.order_status) in ('processed','shipped','completed','issued','unshipped') and left(b.product_sku,$len)='".$row['sku']."' and month(a.order_date)='12' and year(a.order_date)='2016' and lower(a.email) not in ('fba@amazon.com','ecafferty@encompass.com')");
	$avg_sold = $db->func_query_first_cell("SELECT avg(b.product_unit) FROM inv_orders_items b,inv_orders a where b.order_id=a.order_id and lower(a.order_status) in ('processed','shipped','completed','issued','unshipped') and left(b.product_sku,$len)='".$row['sku']."' and month(a.order_date)='12' and year(a.order_date)='2016' and lower(a.email) not in ('fba@amazon.com','ecafferty@encompass.com')");

	$total_price_sold = $db->func_query_first_cell("SELECT sum(b.product_unit * b.product_qty) FROM inv_orders_items b,inv_orders a where b.order_id=a.order_id and lower(a.order_status) in ('processed','shipped','completed','issued','unshipped') and left(b.product_sku,$len)='".$row['sku']."' and month(a.order_date)='12' and year(a.order_date)='2016' and lower(a.email) not in ('fba@amazon.com','ecafferty@encompass.com')");
	$total_cost = $db->func_query_first_cell("SELECT sum(b.product_true_cost * b.product_qty) FROM inv_orders_items b,inv_orders a where b.order_id=a.order_id and lower(a.order_status) in ('processed','shipped','completed','issued','unshipped') and left(b.product_sku,$len)='".$row['sku']."' and month(a.order_date)='12' and year(a.order_date)='2016' and lower(a.email) not in ('fba@amazon.com','ecafferty@encompass.com')");
	$no_of_returns = $db->func_query_first_cell("SELECT sum(b.quantity) from inv_returns a,inv_return_items b where a.id=b.return_id and a.rma_status in ('In QC','Completed') and b.item_condition<>'Customer Damaage' and b.item_condition<>'' and left(b.sku,$len)='".$row['sku']."' and month(a.date_qc)='12' and year(a.date_qc)='2016' ");
	$return_amount = $db->func_query_first_cell("SELECT sum(b.price) from inv_returns a,inv_return_items b where a.id=b.return_id and a.rma_status in ('In QC','Completed') and b.item_condition<>'Customer Damaage' and b.item_condition<>'' and left(b.sku,$len)='".$row['sku']."' and month(a.date_qc)='12' and year(a.date_qc)='2016'");
	$rowData = array();
	if($avg_sold>0 && $avg_purchased>0)
	{
		$rowData = array($row['sku'],(int)$qty_purchased,(float)round($avg_purchased,4),(int)$qty_sold,(float)round($avg_sold,4),round($total_cost,4),round($total_price_sold,4),(int)$no_of_returns,round($return_amount,4));
		
	 	fputcsv($fp, $rowData,',');
	}
		
	// print_r($rowData);exit;
}
fclose($fp);

echo 1;
?>