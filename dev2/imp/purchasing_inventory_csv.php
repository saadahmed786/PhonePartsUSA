<?php
include_once 'auth.php';
include_once 'inc/functions.php';
page_permission('purchasing_metrics');

function echoDate( $start, $end ){
	// echo $start;exit;
            $current = $start;
            $ret = array();
            $ret[] = $start;
            while( $current<$end ){
                
                $next = @date('Y-M-01', $current) . "+1 month";
                $current = @strtotime($next);
                $ret[] = $current;
            }
            array_pop($ret);

            return ($ret);
        }

function getOutOfStockData($sku,$date)
{
	global $db;
	// $data = $db->func_query_first("SELECT 	`date` FROM `inv_product_stock_record` where sku='".$sku."' and qty=0 and month(date)='".date('m',$date)."' and year(date)='".date('Y',$date)."' order by `date` limit 1");
	$data = $db->func_query_first_cell("SELECT 	count(*) FROM `inv_product_stock_record` where sku='".$sku."' and qty=0 and month(date)='".date('m',$date)."' and year(date)='".date('Y',$date)."' ");

	// $return = 0;
	// if($data)
	// {
	// 	$date1 = substr($data['date'], '-2');
	// 	$return = (int)date('t',$month) - (int)$date1;
	// }
	return $data;

}

        // $months = (echoDate(strtotime('2018-01'),strtotime('2018-09')));
        // foreach($months as $month)
        // {
        // 	echo date('Y-m-d',$month)."<br>";
        // }
        // exit;

        if(strlen($_GET['start_date'])!='7' || strlen($_GET['end_date'])!='7' )
        {
        	echo 'Invalid Dates';exit;
        }
        $date_start = $_GET['start_date'];
        $date_end = $_GET['end_date'];
        $date_start = date("Y-m-01", strtotime($date_start));
        $date_end = date("Y-m-t", strtotime($date_end));

        $months = (echoDate(strtotime($date_start),strtotime($date_end)));
if(count($months)>6)
{
	echo 'Too much data, please check your dates and try again';exit;
}


$filename = 'purchasing_inventory_'.$date_start.'_'.$date_end.'.csv';
$fp = fopen($filename, "w");
 


$headers = array('SKU','Product Name','Current Cost (USD)','Current Stock');


// print_r($months);exit;
        foreach($months as $month)
        {
        	$headers[] = date('M-Y',($month)).' Sold';
        	$headers[] = date('M-Y',($month)).' Ordered';
        	$headers[] = date('M-Y',($month)).' OOS';
        }

fputcsv($fp, $headers,',');

$page = $_GET['page'];
if(!$page)
{
	$page = 1;
}

$page_start = ($page-1)*700;
$page_end = 700;

$skus = $db->func_query("select distinct x.* from ( select distinct b.product_sku from inv_orders_items b, inv_orders a where a.order_id=b.order_id and lower(a.order_status)='shipped' and a.order_date between '".$date_start."' and '".$date_start."' union all select distinct b.product_sku from inv_shipment_items b, inv_shipments a where a.id=b.shipment_id and a.vendor_po_id<>'' and a.status='Completed' and a.date_completed between '".$date_start."' and '".$date_end."') x order by 1 limit $page_start,$page_end");

foreach($skus as $sku)
{
	$rowData = array();
	$rowData[] = $sku['product_sku'];
	$rowData[] = getItemName($sku['product_sku']);
	$rowData[] = getTrueCost($sku['product_sku']);
	$rowData[] = $db->func_query_first_cell("SELECT quantity FROM oc_product WHERE (model)='".($sku['product_sku'])."'");

	foreach($months as $month)
        {
        	$rowData[] = (int)$db->func_query_first_cell("SELECT SUM(b.product_qty) from inv_orders_items b,inv_orders a where a.order_id=b.order_id and lower(a.order_status)='shipped' and b.product_sku='".$sku['product_sku']."' and month(a.order_date)='".date('m',$month)."' and year(a.order_date)='".date('Y',$month)."'");
        	$rowData[] = (int)$db->func_query_first_cell("SELECT SUM(b.qty_received) from inv_shipment_items b,inv_shipments a where a.id=b.shipment_id and (a.status)='Completed' and b.product_sku='".$sku['product_sku']."'  and a.vendor_po_id<>'' and month(a.date_completed)='".date('m',$month)."' and year(a.date_completed)='".date('Y',$month)."'");

		$rowData[] = getOutOfStockData($sku['product_sku'],$month);    	

        }

        fputcsv($fp, $rowData,',');

}

fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
readfile($filename);
@unlink($filename);
?>