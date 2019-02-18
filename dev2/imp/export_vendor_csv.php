<?php
require_once("auth.php");
require_once("inc/functions.php");
include_once 'inc/split_page_results.php';
$perission = 'vendor_po';
$pageName = 'Vendor PO';
$pageLink = 'vendor_po.php';
$pageCreateLink = 'vendor_po_create.php';
$pageViewLink = 'vendor_po_view.php?vpo_id=' . $_GET['vpo_id'];
$pageSetting = false;
$table = '`inv_vendor_po`';
//VPO Details
$vpo_id = (int) $_GET['vpo_id'];
$details = $db->func_query_first("SELECT * FROM $table WHERE id = '$vpo_id'");
$vendor_po_id = $details['vendor_po_id'];
$items = $db->func_query("SELECT a.*, b.`image`, b.`product_id` FROM `inv_vendor_po_items` a, `oc_product` b WHERE a.`sku` = b.`model` AND vendor_po_id = '$vendor_po_id' ORDER BY a.`sku` ASC");
$shipments = $db->func_query('SELECT * FROM `inv_shipments` WHERE `vendor_po_id` = "'. $vendor_po_id .'"');

foreach ($shipments as $i => $shipment) {
    $shipments[$i]['items'] = $db->func_query('SELECT * FROM `inv_shipment_items` WHERE `shipment_id` = "'. $shipment['id'] .'"');
    $totalItems = $db->func_query_first_cell('SELECT SUM(qty_shipped) FROM `inv_shipment_items` WHERE `shipment_id` = "'. $shipment['id'] .'"');
    
    foreach ($items as $key => $item) {

        foreach ($shipments[$i]['items'] as $sKey => $sItem) {
            $sItem['shipmentCost'] = $shipment['shipping_cost'] / $totalItems;
            
            $shipments[$i]['items'][$sKey]['image'] = $db->func_query_first_cell("SELECT `image` FROM `oc_product` WHERE `model` = '". $sItem['product_sku'] ."'");
            $shipments[$i]['items'][$sKey]['package_number'] = $shipment['package_number'];
            $shipments[$i]['items'][$sKey]['shipmentCost'] = $sItem['shipmentCost'];

            if ($sItem['product_sku'] == $item['sku']) {

                $items[$key]['shipments'][] = array(
                    'shipment_id' => $shipment['id'],
                    'qty_shipped' => $sItem['qty_shipped'],
                    'package_number' => $shipment['package_number'],
                    'shipmentCost' => $sItem['shipmentCost'],
                    'price' => $sItem['unit_price'] / $shipment['ex_rate']
                    );
                $shipments[$i]['items'][$sKey]['exist'] = 1;
                $items[$key]['qty_S'] += $sItem['qty_shipped'];

                if ($items[$key]['qty_S'] > $item['req_qty']) {
                    //$shipments[$i]['items'][$sKey]['exist'] = 0;
                    $items[$key]['extra'] = $items[$key]['qty_S'] - $item['req_qty'];
                }
            }

        }

    }

}

foreach ($items as $key => $item) {
    $items[$key]['cost'] = $item['cost'] / $details['ex_rate'];
    $tShiped = 0;
    foreach ($item['shipments'] as $i => $shipment) {
        $tShiped += $shipment['qty_shipped'];
    }
    if ($tShiped) {
        $items[$key]['qty_shipped'] = $tShiped;
        $items[$key]['needed'] = $item['req_qty'] - $tShiped;
    }
    else
    {
        $items[$key]['needed'] = $item['req_qty'];
    }

}

$extraItems = array();
foreach ($shipments as $i => $shipment) {
    foreach ($shipments[$i]['items'] as $sKey => $sItem) {
        if (!$sItem['exist']) {
            $extraItems[] = $sItem;
        }

    }
}


$filename = $details['vendor_po_id']."-".get_username($details['vendor']).'-' . date("Y-m-d") . ".csv";
$fp = fopen($filename, "w");

    $headers = array("SKU", "ItemName", "Requested", "Previous Cost"," ", "Shipped", "Package", "New Cost"," ", "Needed", "Extra");


fputcsv($fp, $headers,',');
$rowData=array();
foreach ($items as $key => $row){
$total_lineCost += $row['cost'] * $row['req_qty'];
$_shipment='';
$unit_price = 0;
if ($row['shipments'])
{
    foreach ($row['shipments'] as $shipment) {
$_shipment.=$shipment['qty_shipped'] . '-' . $shipment['package_number'].' /';
$total_shippingCost += $shipment['shipmentCost'] * $shipment['qty_shipped'];
$unit_price += $shipment['price'];
    }
}
else
{
    $_shipment = 'N/A';
}
$_shipment = rtrim($_shipment,'/');

$rowData = array($row['sku'],
    $row['name'],
    $row['req_qty'],
    '$'.round($row['cost'],2),
    '$'.round($row['cost'] * $row['req_qty'],2),
    $row['qty_shipped'],
    ($_shipment),
    ''.round($row['new_cost'], 2),
    '$'.round($unit_price / count($row['shipments']), 2),
    // '$'.round(($unit_price / count($row['shipments'])) * $row['req_qty'], 2),
    ($row['needed'] > 0? $row['needed']:0),
    (int)$row['extra']);
 fputcsv($fp, $rowData,',');
}

fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
readfile($filename);
@unlink($filename);
