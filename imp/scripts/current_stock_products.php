<?php

include '../auth.php';
include '../inc/functions.php';


$filename = "current_stock-" . date("Y-m-d") . ".csv";
$fp = fopen($filename, "w");
$headers = array('SKU',
    'Product Name',
    
    'Qty');
    
fputcsv($fp, $headers);


    $rows = $db->func_query("SELECT a.sku,(select b.name from oc_product_description b where a.product_id=b.product_id) as name,a.quantity FROM oc_product a where a.status='1' and a.sku<>'' and a.image='' GROUP BY a.sku");
    
    foreach ($rows as $product) {
        // $name = $db->func_query_first_cell('SELECT opd.name FROM oc_product op inner join oc_product_description opd on op.product_id = opd.product_id WHERE sku = "'. $product['sku'] .'"');
        $rowData = array($product['sku'],$product['name'],$product['quantity']);
        
        fputcsv($fp, $rowData);
    }


fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
readfile($filename);
@unlink($filename);