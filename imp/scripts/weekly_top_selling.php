<?php
include_once '../config.php';
include_once '../inc/functions.php';
$filename = 'repricing_top_sellers_'.date('WY').'.csv';
$fp = fopen($filename, "w");
$headers = array("SKU","Title","URL","Raw Cost","Our Cost", "Our Price","MS Price","In Stock","Sold Qty(30 Days)","Sold in Previous Week");
fputcsv($fp, $headers,',');

$skus = $_GET['skus'];
if(!$skus)
{
	$skus = array('APL-001-2152',
'APL-001-2144',
'APL-001-2089',
'APL-001-2080',
'APL-001-2090',
'APL-001-2092',
'APL-001-2091',
'APL-001-2093',
'APL-001-2094',
'APL-001-2210',
'APL-001-2211',
'APL-001-2203',
'APL-001-2204',
'APL-001-2365',
'APL-001-2367',
'APL-001-2366',
'APL-001-2368',
'APL-001-2378',
'APL-001-2377',
'APL-001-2381',
'APL-001-2376',
'APL-001-2403');
}
else
{
	$skus = explode(",", $skus);
}


foreach($skus as $sku)
{
	$product= $db->func_query_first("SELECT a.model,b.name,a.sale_price,a.quantity from oc_product a,oc_product_description b where a.product_id=b.product_id and trim(lower(a.model))='".trim(strtolower($sku))."'");
	if($product)
	{

		 $ms_price = $db->func_query_first("select *, (SELECT price from inv_product_price_scrap_history where sku = ph.sku and type = ph.type order by added desc limit 1, 1) as old_price from inv_product_price_scrap_history ph where trim(lower(sku))= '" . trim(strtolower($product['model'])) . "' AND type = 'mobile_sentrix' order by added DESC limit 1");
		 $sold_in_previous_week = $db->func_query_first_cell("SELECT SUM(b.product_qty) FROM inv_orders a,inv_orders_items b where a.order_id=b.order_id and lower(a.order_status)='shipped' and trim(lower(b.product_sku))='".trim(strtolower($product['model']))."' and yearweek(a.ship_date,3)='".date('YW',strtotime('previous week'))."'");

		 $sold_in_last_30 = $db->func_query_first_cell("SELECT SUM(b.product_qty) FROM inv_orders a,inv_orders_items b where a.order_id=b.order_id and lower(a.order_status)='shipped' and trim(lower(b.product_sku))='".trim(strtolower($product['model']))."' and date(a.ship_date)>='".date('Y-m-d',strtotime('-30 day'))."'");

		 $raw_cost = $db->func_query_first_cell("SELECT raw_cost from inv_product_costs where sku='".$sku."' order by id desc limit 1");


		$rowData = array($sku,$product['name'],'http://imp.phonepartsusa.com/product/'.$product['model'],$raw_cost,getTrueCost($product['model']),$product['sale_price'],(float)$ms_price['price'],$product['quantity'],(int)$sold_in_last_30,(int)$sold_in_previous_week);
		fputcsv($fp, $rowData,',');	
	}
}

fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($filename);
@unlink($filename);
?>