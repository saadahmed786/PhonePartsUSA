<?php
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 0); //300 seconds = 5 minutes
include_once '../config.php';
include_once '../inc/functions.php';

$date = $_GET['date'];
if(!$date)
{
	$date = date('Y-m-d');
}

$filename = $date.'_export.csv';
$fp = fopen($filename, "w");
$headers = array("Source","Sales Person","Order ID","Order Status", "Email","Item Name","Closeout", "SKU","Qty","True Cost","Total Cost","TXN Fee","Total Sold Price","Total Sold Price Profit","Margin Value","Margin Percentage"
	);
fputcsv($fp, $headers,',');

$rows = $db->func_query("SELECT DISTINCT a.sales_user,a.transaction_fee,a.store_type,a.order_status,a.prefix,a.order_id, a.email, a.order_price, b.product_sku,b.product_true_cost,  sum(b.product_qty) as product_qty ,sum(b.product_unit) as product_unit
FROM inv_orders_items b, inv_orders a
WHERE b.order_id = a.order_id
AND LOWER( a.order_status ) 
IN (
 'processed',  'shipped',  'completed',  'issued',  'unshipped'
)
AND DATE( a.order_date ) =  '$date'
AND LOWER( a.email ) NOT 
IN (
 'fba@amazon.com',  'ecafferty@encompass.com'
)
GROUP BY a.order_id,b.product_sku");
foreach($rows as $row)
{

	// $closeout_check = $db->func_query_first_cell("SELECT COUNT(*) FROM oc_product a,oc_product_to_category b where a.product_id=b.product_id and trim(lower(a.model))='".trim(strtolower($row['product_sku']))."' and b.category_id in (1391,1392,1393,1394,1395)");
	$closeout_check = $db->func_query_first_cell("SELECT discontinue FROM oc_product WHERE TRIM(LOWER(model))='".trim(strtolower($row['product_sku']))."'");
	$order_fees = $db->func_query_first_cell("select sum(fee) from inv_order_fees where order_id = '".$row['order_id']."'");
	$is_clousout='';
	if($closeout_check==1)
	{
		$is_clousout='closeout';
	}
	$txn_fee = 0.00;
	if($order_fees)
	{
		$txn_fee = $order_fees * (-1);

	}
	else
	{
		if($row['store_type']=='ebay')
		{
			$txn_fee = $row['transaction_fee'];
		}
		else
		{

		$txn_fee = $db->func_query_first_cell("SELECT transaction_fee FROM inv_transactions WHERE order_id='".$row['order_id']."' ");
		}
	}
	$item_txn_fee = ((float)$row['product_unit']/(float)$row['order_price']) * $txn_fee;

	$total_cost = (int)$row['product_qty'] * (float)$row['product_true_cost'];
	$total_sold_price = $row['product_unit'] * (int)$row['product_qty'];
	$total_sold_price_profit = ($total_sold_price - $total_cost) - $item_txn_fee;
	$margin = $total_sold_price_profit;
	$margin_percent = ($total_sold_price_profit/$total_cost) * 100;

	$sale_agent_id = $db->func_query_first_cell("SELECT user_id from inv_customers where email='".$row['email']."'");

	$sales_user = get_username($sale_agent_id);
	if (!$sales_user) {
		$sales_user = get_username($sale_agent_id,true);
	}

	$rowData = array();
	$rowData = array(mapStoreType($row['store_type']),$sales_user,$row['prefix'].$row['order_id'],$row['order_status'],$row['email'],getItemName($row['product_sku']),$is_clousout,$row['product_sku'],(int)$row['product_qty'],round($row['product_true_cost'],2),round($total_cost,2),round($item_txn_fee,2),round($total_sold_price,2),round($total_sold_price_profit,2),round($margin,2),round($margin_percent).'%');
	
		
	// print_r($rowData);exit;
	 fputcsv($fp, $rowData,',');
}
fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($filename);
@unlink($filename);

// echo 1;
?>