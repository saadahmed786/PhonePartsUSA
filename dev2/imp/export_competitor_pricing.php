<?php

include 'auth.php';
include 'inc/functions.php';
include 'shipstation/shipstation.php';

$ship = new ShipStation;

$filename = "scraps-" . date("Y-m-d") . ".csv";
$fp = fopen($filename, "w");
$headers = array('SKU',
    'Product Name',
    'Cost',
    'Price',
    'Mobile Sentrix Old Price',
    'Mobile Sentrix Price',
    'Mobile Sentrix Change',
    'Mobile Sentrix Stock',
    'Mobile Sentrix Url',
    'Fixez Old Price',
    'Fixez Price',
    'Fixez Change',
    'Fixez Stock',
    'Fixez Url',
    'Mengtor Old Price',
    'Mengtor Price',
    'Mengtor Change',
    'Mengtor Stock',
    'Mengtor Url',
    'Mobile Defenders Old Price',
    'Mobile Defenders Price',
    'Mobile Defenders Change',
    'Mobile Defenders Stock',
    'Mobile Defenders Url',
    'eTrade Supply Old Price',
    'eTrade Supply Price',
    'eTrade Supply Change',
    'eTrade Supply Stock',
    'eTrade Supply Url',
    'Maya Cellular Old Price',
    'Maya Cellular Price',
    'Maya Cellular Change',
    'Maya Cellular Stock',
    'Maya Cellular Url',
    'LCD Loop Old Price',
    'LCD Loop Price',
    'LCD Loop Change',
    'LCD Loop Stock',
    'LCD Loop Url',

    'Parts 4 Cells Old Price',
    'Parts 4 Cells Price',
    'Parts 4 Cells Change',
    'Parts 4 Cells Stock',
    'Parts 4 Cells Url',

    'LCD Parts Hub Old Price',
    'LCD Parts Hub Price',
    'LCD Parts Hub Change',
    'LCD Parts Hub Stock',
    'LCD Parts Hub Url',

    );

fputcsv($fp, $headers,',');

if ($_GET['m']) {
    $rows = $db->func_query("SELECT * FROM inv_product_price_scrap where url<>'' GROUP BY sku");
    // $rows = $db->func_query("SELECT distinct a.* FROM `inv_product_price_scrap` a left outer join inv_product_price_scrap_history b on (a.sku=b.sku and a.type=b.type) WHERE a.url='' and b.sku is null group by a.sku");
    $scrapping_sites = array('mobile_sentrix', 'fixez', 'mengtor', 'mobile_defenders','etrade_supply','maya_cellular','lcd_loop','parts_4_cells','cell_parts_hub');
    // $_scrapping_sites = implode("'", pieces)
    // $_scrapping_sites = "'" . implode("','", $scrapping_sites) . "'";
    // $query = ("SELECT * from inv_product_price_scrap where trim(url)='' 
    // and type='mobile_sentrix'
    // and type='fixez'
    // and type='mengtor'
    // and type='mobile_defenders'
    // and type='etrade_supply'
    // and type='maya_cellular'
    // and type='lcd_loop'
    // and type='parts_4_cells'
    // and type='cell_parts_hub'
    
    //   group by sku ");
    // echo $query;exit;
    // $rows = $db->func_query($query);
    foreach ($rows as $product) {
        $name = $db->func_query_first_cell('SELECT opd.name FROM oc_product op inner join oc_product_description opd on op.product_id = opd.product_id WHERE sku = "'. $product['sku'] .'"');
        $skuprice = $db->func_query_first_cell('SELECT opd.price FROM oc_product op inner join oc_product_discount opd on op.product_id = opd.product_id WHERE customer_group_id = "1633" AND opd.quantity = "1" AND sku = "'. $product['sku'] .'"');
        $rowData = array(
            $product['sku'],
            $name,
            getTrueCost($product['sku']),
            $skuprice
            );
        foreach ($scrapping_sites as $site) {
            $price = $db->func_query_first("select *, (SELECT price from inv_product_price_scrap_history where sku = ph.sku and type = ph.type order by added desc limit 1, 1) as old_price from inv_product_price_scrap_history ph where sku = '" . $product['sku'] . "' AND type = '$site' order by added DESC limit 1");
            $url = $db->func_query_first_cell("SELECT url FROM inv_product_price_scrap where sku = '" . $product['sku'] . "' AND type = '$site'");
            $change = number_format($price['price'] / $price['old_price'] * 100, 2);
            if ($change < 100.00 && $change > 0.00) {
                $change = '-' . (100 - $change);
            } else if ($change == 0.00) {
                $change = 100 - $change;
            } else {
                $change = '+' . ($change - 100);
            }
            $rowData[] = number_format($price['old_price'], 2);
            $rowData[] = number_format($price['price'], 2);
            $rowData[] = $change;
            $rowData[] = ($price['out_of_stock'])? 'No': 'Yes';
            $rowData[] = $url;
        }
        fputcsv($fp, $rowData,',');
    }
}

fclose($fp);

header('Content-type: application/csv');
header('Content-Disposition: attachment; filename="' . $filename . '"');
readfile($filename);
@unlink($filename);