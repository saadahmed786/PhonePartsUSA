<?php
require_once("config.php");
require_once("inc/functions.php");
page_permission('inventory_report');
$keyword = @trim($_GET['keyword']);
$date = @trim($_GET['date']);
$show_all = @$_GET['show_all'];
$bit = (int)$_GET['bit'];
if($keyword){
    $keyword = $db->func_escape_string($keyword);
    $where = " Where Lower(pd.name) like Lower('%$keyword%') OR Lower(p.sku) like Lower('%$keyword%') ";
    $where2 = "Lower(sku) like Lower('%$keyword%')";
    $parameters[] = "keyword=$keyword";
}
else{
    $where = " Where p.sku != '' and p.is_main_sku = 1 and p.sku<>'SIGN'";
    $where2 = "sku<>'' and sku<>'SIGN'";
    $parameters[] = "";
}

if($show_all){
    $show_all = $db->func_escape_string($show_all);
    $where .= " and 1=1 ";
    $where2 .= 'and 1=1';
    $parameters[] = "show_all=$show_all";
}
else{
    $where .= " and p.quantity>0";
    $where2 .= ' and qty>0';
    $parameters[] = "";
}


$dir = @$_GET['dir'];
if(!$dir || !in_array($dir,array("asc","desc"))){
    $dir = 'asc';
}

$parameters[] = "dir=$dir";

$sort = @$_GET['sort'];
if(!$sort || !in_array($sort,array("name","sku","quantity"))){
    $sort = 'sku';
}

$parameters[] = "sort=$sort";

if(in_array($sort,array("sku","quantity"))){
    $dsort = "p.$sort";
}
elseif(in_array($sort,array("name"))){
    $dsort = "pd.$sort";
}
else{
    $dsort = "p.$sort";
}
$where.=' AND p.sku NOT IN (SELECT kit_sku FROM inv_kit_skus) ';
$_query = "Select p.sku, p.weight, p.quantity, p.price ,  pd.name
from oc_product p inner join oc_product_description pd on (p.product_id = pd.product_id)

$where group by p.sku order by $dsort $dir";

if($date and $date!=date('Y-m-d'))
{
    $_query = "SELECT sku,0.00 as weight,qty as quantity, '-' as name from inv_product_stock_record where $where2 and `date`='".date('Y-m-d',strtotime($date))."' order by sku asc";
}


// echo $_query;exit;
//$splitPage = new splitPageResults($db , $_query , 100 , $xpage,$page);
$results = $db->func_query($_query);

$filename = 'reports/Inventory/' . date("Y-m-d") . "-InventoryReport.csv";
$fp = fopen($filename, "w");
if (!$bit) {
$headers = array("SKU", "ItemName", "Qty", "Avg. Product Cost","Total Item Cost", "W1 Avg. Sale", "Total W1 Price");    
}else {
$headers = array("SKU", "ItemName", "Qty", "Avg. Product Cost","Total Item Cost");   
}


fputcsv($fp, $headers,',');
$k=0;
$total_avg_cost = 0.00;
$total_avg_total = 0.00;

$total_w1_cost = 0.00;
$total_w1_total = 0.00;

$total_qty = 0;

foreach($results as $i => $result){
    $avg_cost = getAvgProductCost($result['sku'], $date);
    $w1_avg = getWholeSaleAvgCost($result['sku'],1);

    if($result['quantity']<0) {
        $qty=0; 
    }
    else
    {
        $qty=$result['quantity'];   
    }

    $total_avg_cost+=$avg_cost;
    $total_w1_cost+=$w1_avg;

    $total_avg_total+=$qty * $avg_cost;
    $total_w1_total+=$qty * $w1_avg;

    if($result['name']=='-')
    {
        $result['name'] = getItemName($result['sku']);
    }


    $total_qty+=$result['quantity'];
                    // $qty = ($date)? $db->func_query_first_cell("SELECT qty FROM inv_product_stock_record WHERE sku = '". $result['sku'] ."' AND date = '$date'") : $result['quantity'];
    $qty = $result['quantity'];
    $rowData=array();
    if (!$bit) {
    $rowData = array($result['sku'],
    $result['name'],
    (int)$qty,
    round($avg_cost,2),
    round($qty * $avg_cost,2),
    round($w1_avg,2),
    round($qty * $w1_avg,2));
    } else {
    $rowData = array($result['sku'],
    $result['name'],
    (int)$qty,
    round($avg_cost,2),
    round($qty * $avg_cost,2));
    }
 fputcsv($fp, $rowData,',');
}
fclose($fp);
if(!$bit)
{
header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
readfile($filename);
@unlink($filename);
    
}

?>